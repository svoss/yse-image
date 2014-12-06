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
use ISTI\Image\Relation\RelationInfo;
use ISTI\Resizer\ResizerInterface;
use ISTI\Image\Relation\RelationProviderManager;
use ISTI\ImageBundle\Saver\SaverManager;
use ISTI\Image\SEOImageException;
class ImageManager {

    public function __construct($persistenceManager, $imageInfoClass, $resizer, $saveManager, Uniquifier $uniquifier, RelationProviderManager $relationProviderManager)
    {
        $this->persistenceManager = $persistenceManager;
        $this->imageInfoClass = $imageInfoClass;
        $this->resizer = $resizer;
        $this->uniquifier = $uniquifier;
        $this->saveManager = $saveManager;
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
        $format = $this->relationProviderManager->getFormatFor($relation, $persistence, $formatName);

        //load the repo of the image info persistence object:
        $repo = $this->persistenceManager->getRepository($persistence);

        //Path can either be uniqufied earlier, in that case we have to get it from the repo if get default path
        //from imageinfo object
        if($repo === null || $repo->getActualPathUsed($persistence,$format) === null)
            $path = $image->getPathForFormat($format);
        else
            $path = $repo->getActualPathUsed($persistence,$format);

        //if path is new to and repo is defined make sure it's unqiue
        if($repo !== null && $repo->getActualPathUsed($persistence,$format) === null) {
            //make sure the path is unique:
            $path = $this->uniquifier->uniquify($path, $persistence);
            $repo->setActualPathUsed($path, $persistence,$format);
        }

        //gets the saver for this source
        $saver = $this->saveManager->getSaver($image->getSource());

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

        return $path;

    }
} 