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

        $this->loadDefaults($parent, $attribute, $image,$index);

        $source = $this->createSource($image->getSourceClass(), $file);
        $image->setSource($source);

    }

    public function loadDefaults($parent, $attribute, $image,$index = null)
    {
        $relationInfo = $this->createRelationInfo($parent,$attribute,$index);

        if(in_array('ISTI\Image\Persist\EditableInterface', class_implements($image)))
        {

            $this->persistenceManager->loadDefaultToPersistent($image, $relationInfo);
        }
    }

    public function createSource($class, File $file)
    {
        $saver = $this->saverManager->getSaver($class);
        return $saver->createSource($file);

    }

    public function clearCache($parent, $attribute, $index = null)
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
        $image = $this->persistenceManager->loadFromPersistent($persistence, $this->imageInfoClass, $relation);
        //gets the saver for this source
        $saver = $this->saverManager->getSaver($image->getSource());
        //format for this image
        $rp = $this->relationProviderManager->getRelationProvider($parent);
        //load the repo of the image info persistence object:
        $repo = $this->persistenceManager->getRepository($image);

        $formats = $rp->getFormats($relation);
        foreach($formats as $format)
        {
            //retrieve image info object from persist image info and the image class
            $image = $this->persistenceManager->loadFromPersistent($persistence, $this->imageInfoClass, $relation);
            if($repo === null || $repo->getActualPathUsed($image,$format) === null)
                $path = $image->getPathForFormat($format).".".$saver->getExtension($image->getSource());
            else
                $path = $repo->getActualPathUsed($image,$format);
            if($saver->cached($path))
            {
                $saver->emptyCache($image->getSource(),$path);
            }
        }






    }

    public function removeImage(UneditableInterface $image)
    {

    }

    /**
     * @param $parent
     * @param $attribute
     * @param $index
     * @return RelationInfo
     *
     */
    protected function createRelationInfo($parent, $attribute, $index)
    {
        if(is_object($parent)) {
            $persistence = call_user_func(array($parent, 'get' . ucfirst($attribute)));
            if (is_array($persistence) || in_array('Traversable', class_implements($persistence))) {
                $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::ManyToOne);
            } else {
                $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::OneToOne);

            }
        } else {
            //@todo
            $relation = new RelationInfo($attribute, $index, $parent, RelationInfo::OneToOne);
        }
        return $relation;
    }

    public function thumb($parent, $attribute, $width, $height, $index = null)
    {
        $persistence = call_user_func(array($parent,'get'.ucfirst($attribute)));
        if($index != null)
            $persistence = $persistence[$index];

        $path = $this->saverManager->getSaver($persistence->getSource())->getFilePathToSource($persistence->getSource());
        return $this->resizer->createAdminThumb($path, $width,$height);
    }

    /**
     * @param $parent
     * @param $attribute
     * @param null $index
     * @return Relation\Format[]
     * @throws SEOImageException
     *
     */
    public function getImageFormats($parent, $attribute, $index = null)
    {
        $ri = $this->createRelationInfo($parent, $attribute,$index);
        $pm = $this->relationProviderManager->getRelationProvider($parent);
        $formats = $pm->getFormats($ri);
        return $formats;
    }

    public function getMinRes($parent, $attribute, $index = null)
    {
        $formats = $this->getImageFormats($parent,$attribute,$index);
        $max_h = 0;
        $max_w = 0;
        foreach($formats as $f)
        {
            if($max_h < $f->getHeight())
                $max_h = $f->getHeight();
            if($max_w < $f->getWidth())
                $max_w = $f->getWidth();
        }

        return array($max_w, $max_h);
    }

    public function getLinkToSource($source)
    {
        return $this->saverManager->getSaver($source)->linkToSource($source);
    }
}