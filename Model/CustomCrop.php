<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 11:03
 */

namespace ISTI\Image\Model;


class CustomCrop {
    /**
     * Where to start cropping in the x dimension
     * @var int
     */
    protected $startX;
    /**
     * Where to start cropping in the y dimension
     * @var int
     */
    protected $startY;

    /**
     * The width of the cropping area
     * @var int
     */
    protected $width;

    /**
     * The height of the cropping area
     * @var int
     */
    protected $height;

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getStartX()
    {
        return $this->startX;
    }

    /**
     * @return int
     */
    public function getStartY()
    {
        return $this->startY;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @param int $startX
     */
    public function setStartX($startX)
    {
        $this->startX = $startX;
    }

    /**
     * @param int $startY
     */
    public function setStartY($startY)
    {
        $this->startY = $startY;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function toJSON()
    {
        return array($this->getWidth(),$this->getHeight(),$this->getStartX(),$this->getStartY());
    }

    public function toArray()
    {

        return array(
            "width" => $this->getWidth(),
            "height" => $this->getHeight(),
            "startx" => $this->getStartX(),
            "starty" => $this->getStartY()
         );
    }

    public static function fromArray($array)
    {
        if($array === null) {
            return null;
        }
        $instance = new self();
        if(!is_array($array))
        {
            $instance->setHeight($array->height);
            $instance->setWidth($array->width);
            $instance->setStartX($array->startx);
            $instance->setStartY($array->starty);
        }
        else {
            $instance->setHeight($array['height']);
            $instance->setWidth($array['width']);
            $instance->setStartX($array['startx']);
            $instance->setStartY($array['starty']);
        }

        return $instance;
    }


} 