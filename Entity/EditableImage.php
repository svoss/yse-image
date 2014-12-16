<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 06/12/14
 * Time: 20:34
 */

namespace ISTI\Image\Entity;


use ISTI\Image\Persist\EditableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Implements and editable persistence object in an doctrine entity
 * Extend and implement  getSource to make it all work
 * @ORM\MappedSuperclass()
 * Class EditableImage
 * @package ISTI\Image\Entity\Image
 */
abstract class EditableImage implements EditableInterface{
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $title;
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $alt;
    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $longDescription;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $geoLocation;

    /**
     * @ORM\Column(type="object")
     * @var array
     */
    protected $crops;

    /**
     * @ORM\Column(type="object")
     * @var array
     */
    protected $paths;

    /**
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * @param string $alt
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;
    }

    /**
     * @return string
     */
    public function getGeoLocation()
    {
        return $this->geoLocation;
    }

    /**
     * @param string $geoLocation
     */
    public function setGeoLocation($geoLocation)
    {
        $this->geoLocation = $geoLocation;
    }

    /**
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @param string $longDescription
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }



    public function getCrops()
    {
        return $this->crops;
    }

    public function getPaths()
    {
        return $this->paths;
    }



    public function setCrops($crops)
    {
        $this->crops = $crops;
    }

    public function setPaths($paths)
    {
        $this->paths = $paths;
    }


} 