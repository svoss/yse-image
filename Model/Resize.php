<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 11:02
 */

namespace ISTI\Image\Model;

/**
 * Describes how to resize a certain image. It either can be customized or a resizing strategy can be chosen
 *
 * @package ISTI\Image\Model
 * @author Stijn Voss<svoss@i-sti.nl>
 */
class Resize {
    /**
     * How to crop? custom or zoom
     * @var string
     */
    protected $type;

    /**
     * Should custom crop the image, if not null wil use customCrop
     * @var null|CustomCrop
     */
    protected $costumCrop;

    /**
     * @return CustomCrop|null
     */
    public function getCustumCrop()
    {
        return $this->custumCrop;
    }

    /**
     * @param CustomCrop|null $custumCrop
     */
    public function setCustumCrop($custumCrop)
    {
        $this->custumCrop = $custumCrop;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        if($type !== 'custom'){
            $this->custumCrop = null;
        }
        $this->type = $type;
    }


} 