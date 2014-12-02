<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 11:10
 */

namespace ISTI\Image\Model;


/**
 * Describes an image Format
 * Class Format
 * @package ISTI\Image\Model
 */
class Format {

    /**
     * widt of main format
     * @var int
     */
    protected $width;

    /**
     * height of main format
     * @var int
     */
    protected $height;


    /**
     * Identifier name of format
     * @var string
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }



    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }
} 