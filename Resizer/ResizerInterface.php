<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 22:11
 */

namespace ISTI\Image\Resizer;

use ISTI\Image\Model\Format;
use ISTI\Image\Model\ImageInfoInterface;

interface ResizerInterface {
    public function resize(ImageInfoInterface $imageinfo, Format $format, $fromPath ,$writePath);
} 