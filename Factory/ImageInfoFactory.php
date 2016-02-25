<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 14:14
 */

namespace ISTI\Image\Factory;



use ISTI\Image\Model\ImageInfo;
use ISTI\Image\Model\SourceInterface;

class ImageInfoFactory implements ImageInfoFactoryInterface{
    /**
     * Creates an new instance with this variables
     * @param $title
     * @param $alt
     * @param $long
     * @param $geolocation
     * @param $parent
     * @param $crops
     * @param $paths
     * @param SourceInterface $source
     * @return mixed
     */
    public function createInstance($title, $alt, $long, $geolocation, $parent, $crops, $paths, SourceInterface $source,$filters, $cropOutside=null,$bgColor = null )
    {
        return new ImageInfo($alt,$crops,$geolocation,$long, $paths,$source,$title,$filters,$bgColor, $cropOutside);
    }

    public function getClass()
    {
       return 'ISTI\Image\Model\ImageInfo';
    }

} 