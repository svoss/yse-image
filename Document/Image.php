<?php

namespace ISTI\Image\Document;

use ISTI\Image\Saver\FilesystemSource;
use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCR;
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 18/12/14
 * Time: 16:57
 *
 * @PHPCR\Document(referenceable=true)
 */
class Image implements \ISTI\Image\Persist\EditableInterface{


    /**
     * @PHPCR\Id()
     */
    protected $id;

    /**
     * @PHPCR\ParentDocument()
     */
    protected $parent;

    /**
     * @PHPCR\String()
     */
    protected $filename;

    /**
     * @PHPCR\String()
     */
    protected $title;

    /**
     * @PHPCR\String()
     */
    protected $alt;

    /**
     * @PHPCR\String()
     */
    protected $longDescription;

    /**
     * @PHPCR\String()
     */
    protected $geoLocation;

    /**
     * @PHPCR\String()
     */
    protected $crops;

    /**
     * @PHPCR\String()
     */
    protected $paths;

    /**
     * @PHPCR\String()
     */
    protected $filters;

    protected $sourceChanged;

    function __construct()
    {
        $this->sourceChanged = false;
    }


    public function getCrops()
    {
        return unserialize(base64_decode($this->crops));
    }

    public function getPaths()
    {
        return unserialize(base64_decode($this->paths));
    }


    public function setCrops($crops)
    {

        $this->crops = base64_encode(serialize($crops));
    }

    public function setPaths($paths)
    {
        $this->paths = base64_encode(serialize($paths));
    }

    public function getSource()
    {
        if($this->filename == null)
            return null;
        else
            return new FilesystemSource($this->filename);
    }

    public function setSource($source)
    {

        if($source !== null) {
            $this->sourceChanged = true;
            $this->filename = $source->getFilename();
        }
    }

    /**
     * @return mixed
     */
    public function getSourceChanged()
    {
        return $this->sourceChanged;
    }



    public function getSourceClass()
    {
        return 'ISTI\Image\Saver\FilesystemSource';
    }

    /**
     * @return mixed
     */
    public function getParentDocument()
    {
        return $this->parent;
    }

    /**
     * @param mixed $parent
     */
    public function setParentDocument($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param mixed $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param mixed $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * @return mixed
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @param mixed $longDescription
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;
    }

    /**
     * @return mixed
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @param mixed $geoLocation
     */
    public function setGeoLocation($geoLocation)
    {
        $this->geoLocation = $geoLocation;
    }
    public function getFilters()
    {
        return unserialize(base64_decode($this->filters));
    }

    public function setFilters($filters)
    {
       $this->filters = base64_encode(serialize($filters));
    }

    public function getBgColor()
    {
        // TODO: Implement getBgColor() method.
    }

    public function setBgColor($bgColor)
    {
        // TODO: Implement setBgColor() method.
    }

    public function isCropOutside()
    {
        // TODO: Implement isCropOutside() method.
    }

    public function setCropOutside($cropOutside)
    {
        // TODO: Implement setCropOutside() method.
    }


}