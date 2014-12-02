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


} 