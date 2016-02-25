<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 11:27
 */

namespace ISTI\Image\Model;


class ImageInfo implements ImageInfoInterface{


    /**
     * @var  SourceInterface
     */
    protected $source;

    /**
     * @var string
     */
    protected $title;


    /**
     * @ORM\Column(type='string')
     * @var string
     */
    protected $alt;

    /**
     * @var string
     */
    protected $longDescription;

    /**
     * @var string
     */
    protected $geoLocation;

    /**
     * @var array
     */
    protected $crops;

    /**
     * @var array
     */
    protected $paths;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     */
    protected $bgColor;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    protected $cropOutside;

    function __construct($alt, $crops, $geoLocation, $longDescription, $paths, $source, $title, $filters, $bgColor, $cropOutside)
    {
        $this->alt = $alt;
        $this->crops = $crops;
        $this->geoLocation = $geoLocation;
        $this->longDescription = $longDescription;
        $this->paths = $paths;
        $this->source = $source;
        $this->title = $title;
        $this->filters = $filters;
        $this->bgColor = $bgColor;
        $this->cropOutside = $cropOutside;
    }

    /**
     * @return string
     */
    public function getBgColor()
    {
        return $this->bgColor;
    }

    /**
     * @param string $bgColor
     * @return self
     */
    public function setBgColor($bgColor)
    {
        $this->bgColor = $bgColor;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getCropOutside()
    {
        return $this->cropOutside;
    }

    /**
     * @param boolean $cropOutside
     * @return self
     */
    public function setCropOutside($cropOutside)
    {
        $this->cropOutside = $cropOutside;
        return $this;
    }


    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @return string
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @return SourceInterface
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }


    /**
     * @param Format $format
     * @return Resize
     */
    public function getCropForFormat(Format $format)
    {
        return isset($this->crops[$format->getName()]) ? $this->crops[$format->getName()] : null;
    }

    public function getPathForFormat(Format $format)
    {
        return isset($this->paths[$format->getName()]) ? $this->paths[$format->getName()] : null;
    }

    /**
     * @param Format $format
     * @return Filter[]
     */
    public function getFiltersForFormat(Format $format)
    {
        return isset($this->filters[$format->getName()]) ? $this->filters[$format->getName()] : null;
    }


} 