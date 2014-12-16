<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 10:54
 */
namespace ISTI\Image\Helper;
use ISTI\Image\Model\ImageInfoInterface;

class ImageInfoHasher {
    public function hash(ImageInfoInterface $info, $format)
    {
        $crop = $info->getCropForFormat($format);
        $path = $info->getPathForFormat($format);
        $source = $info->getSource()->getId();
        $str = $crop->toJSON()."-".$path."-".$source;

        return md5($str);

    }
} 