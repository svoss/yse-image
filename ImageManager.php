<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 22:15
 */

namespace ISTI\Image;


use ISTI\Image\Model\Format;
use ISTI\Image\Persist\EditableInterface;
use ISTI\Image\Persist\PersistenceManager;
use ISTI\Image\Persist\UneditableInterface;
use ISTI\Image\Relation\RelationInfo;
use ISTI\Image\Resizer\ResizerInterface;
use ISTI\Image\Relation\RelationProviderManager;
use ISTI\Image\Saver\SaverManager;
use ISTI\Image\SEOImageException;
use Symfony\Component\HttpFoundation\File\File;
class ImageManager {

    public function __construct($persistenceManager, $imageInfoClass, $resizer, $saveManager, Uniquifier $uniquifier, RelationProviderManager $relationProviderManager)
    {

        $this->persistenceManager = $persistenceManager;
        $this->imageInfoClass = $imageInfoClass;
        $this->resizer = $resizer;
        $this->uniquifier = $uniquifier;
        $this->saverManager = $saveManager;
        $this->relationProviderManager = $relationProviderManager;
    }
    /**
     * @var Uniquifier
     */
    protected $uniquifier;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * @var string
     */
    protected $imageInfoClass;

    /**
     * @var ResizerInterface
     */
    protected $resizer;

    /**
     * @var RelationProviderManager
     */
    protected $relationProviderManager;

    /**
     * @var SaverManager
     */
    protected $saverManager;

    /**
     * Will get the formatted version of a certain image.
     * @param string $formatName        the format to convert to
     * @param Object $parent
     * @param string $attribute     the attribute
     * @param null $index
     *
     * @return string src to file
     */
    public function format($formatName, $parent, $attribute, $index = null)
    {
        $persistence = call_user_func(array($parent,'get'.ucfirst($attribute)));

        if(is_array($persistence) || in_array('Traversable', class_implements($persistence))){
            if($index === null)
            {
                throw new SEOImageException("No index defined when in a one to many relation");
            }
            $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::ManyToOne);
            $persistence = $persistence[$index];
        } else {
            $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::OneToOne);

        }

        //retrieve image info object from persist image info and the image class
        $image = $this->persistenceManager->loadFromPersistent($persistence, $this->imageInfoClass, $relation);

        //format for this image
        $format = $this->relationProviderManager->getFormatFor($relation, $parent, $formatName);
        //load the repo of the image info persistence object:
        $repo = $this->persistenceManager->getRepository($image);

        //gets the saver for this source
        $saver = $this->saverManager->getSaver($image->getSource());
        //Path can either be uniqufied earlier, in that case we have to get it from the repo if get default path
        //from imageinfo object
        if($repo === null || $repo->getActualPathUsed($image,$format) === null)
            $path = $image->getPathForFormat($format).".".$saver->getExtension($image->getSource());
        else
            $path = $repo->getActualPathUsed($image,$format);


        //if path is new to and repo is defined make sure it's unqiue
        if($repo !== null && $repo->getActualPathUsed($image,$format) === null) {
            //make sure the path is unique:
            $path = $this->uniquifier->uniquify($path, $image);
            $repo->setActualPathUsed($path, $image,$format);
        }



        if(!$saver->cached($path))
        {

            //where is the source image
            $pathToSourceImage = $saver->getFilePathToSource($image->getSource());

            //create temporary file to save the file to it
            $tmp = tempnam(sys_get_temp_dir(),'image');
            $this->resizer->resize($image, $format, $pathToSourceImage, $tmp);

            //now save image to right location
            $saver->saveResized($image->getSource(), $path, $tmp);
        }

        return $saver->linkTo($path);

    }

    public function newImage($parent, $attribute, UneditableInterface $image, File $file, $index = null)
    {

        $saver = $this->saverManager->getSaver($image->getSourceClass());
        $relationInfo = $this->createRelationInfo($parent,$attribute,$index);

        if(in_array('ISTI\Image\Persist\EditableInterface', class_implements($image)))
        {

            $this->persistenceManager->loadDefaultToPersistent($image, $relationInfo);
        }
        $image->setSource($saver->createSource($file));

    }

    public function editImage(UneditableInterface $image)
    {

    }

    public function removeImage(UneditableInterface $image)
    {

    }

    protected function createRelationInfo($parent, $attribute, $index)
    {
        $persistence = call_user_func(array($parent,'get'.ucfirst($attribute)));
        if(is_array($persistence) || in_array('Traversable', class_implements($persistence))){
            $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::ManyToOne);
        } else {
            $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::OneToOne);

        }
        return $relation;
    }
}