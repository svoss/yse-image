<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 12/04/15
 * Time: 20:49
 */

namespace ISTI\Image\Form\Type;

use ISTI\Image\Form\DataTransformer\FileToSourceTransformer;
use ISTI\Image\Form\DataTransformer\ImageTransformer;
use ISTI\Image\Form\DataTransformer\ResizeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use ISTI\Image\Saver\FilesystemSaver;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;
use Symfony\Component\Validator\Constraints\ImageValidator;
use Symfony\Component\Validator\ValidatorInterface;
class ImageEntityType extends AbstractType {
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    protected $defaultEntity;
    /**
     * @param FilesystemSaver $saver
     */
    public function __construct(FilesystemSaver $saver, $validator, $defaultEntity)
    {
        $this->saver = $saver;
        $this->validator = $validator;
        $this->defaultEntity = $defaultEntity;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $ic = new ImageConstraint(array("minWidth" => $options['minWidth'], "minHeight" => $options["minHeight"]));
        $builder
            ->add($builder->create('source', 'file',array("required" => false))
                ->addModelTransformer(new FileToSourceTransformer($this->saver )))
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use ($ic){
                $file = $event->getData()['source'];
                if($file != null && $file->guessExtension() != 'svg')
                {
                    $errorList = $this->validator->validate(
                        $file,
                        $ic
                    );
                    if(count($errorList) > 0) {
                        $event->setData(null);
                    }
                    foreach($errorList as $error) {
                        $ferror = new FormError($error->getMessage(), $error->getMessageTemplate(), $error->getMessageParameters());
                        $event->getForm()->addError($ferror);
                    }
                }

            })
            ->addModelTransformer(new ImageTransformer());

        $builder->add('alt','hidden');
        $builder->add('title','hidden');
        $builder->add($builder->create('crops','collection',array('type' => 'hidden'))->addModelTransformer(new ResizeTransformer()));

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options); // TODO: Change the autogenerated stub
        $view->vars["parentClass"] = get_class($form->getParent()->getData());
    }


    public function getName()
    {
        return 'image_entity';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->defaultEntity,
                "minWidth" => 0,
                "minHeight" => 0,

            ));

        // ...
    }
}