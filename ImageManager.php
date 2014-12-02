<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 22:15
 */

namespace ISTI\Image;


use ISTI\Image\Model\Format;
use ISTI\Image\Persist\PersistenceManager;
use ISTI\Image\Relation\RelationInfo;
use ISTI\Resizer\ResizerInterface;
use ISTI\Image\Relation\RelationProviderManager;
use ISTI\ImageBundle\Saver\SaverManager;
class ImageManager {

    public function __construct($persistenceManager, $imageInfoClass, $resizer, $relationProviderManager)
    {
        $this->persistenceManager = $persistenceManager;
        $this->imageInfoClass = $imageInfoClass;
        $this->resizer = $resizer;
        $this->relationProviderManager = $relationProviderManager;
    }
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
     * @var SaveManager
     */
    protected $saveManager;

    /**
     * Will get the formatted version of a certain image.
     * @param string $formatName        the format to convert to
     * @param Object $parent
     * @param string $attribute     the attribute
     * @param null $index
     *
     * @return string
     */
    public function format($formatName, $parent, $attribute, $index = null)
    {
        $image = call_user_func(array($parent,'get'.ucfirst($attribute)));
        if(is_array($image) && in_array('Traversable', class_implements($image))){
            if($index === null)
            {
                throw new SEOImageException("No index defined when in a one to many relation");
            }
            $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::ManyToOne);
            $image = $image[$index];
        } else {
            $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::OneToOne);

        }

        //retrieve image info object from persist image info and the image class
        $image = $this->persistenceManager->loadFromPersistent($image, $this->imageInfoClass, $relation);

        //format for this image
        $format = $image->getFormatFor($relation,$parent,$formatName);
        //path that this image should be available on
        $path = $image->getPathFor($relation,$parent,$formatName);

        //gets the saver for this source
        $saver = $this->saveManager->getSaver($image->getSource());

        if(!$saver->cached($image->getSource()))
        {
            //where is the source image
            $pathToSourceImage = $saver->getFilePathToSource($image->getSource());

            //create temporary file to save te file t
            $tmp = tempnam(sys_get_temp_dir(),'image');
            $this->resizer->resize($image, $format, $pathToSourceImage, $tmp);

            //now save image to right location
            $saver->saveResized($image->getSource(), $path, $tmp);
        }



    }
} 