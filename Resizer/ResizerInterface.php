<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 22:11
 */

namespace ISTI\Resizer;

use ISTI\Image\Model\Format;
use ISTI\Image\Model\ImageInfoInterface;

interface ResizerInterface {
    public function resize(ImageInfoInterface $image, Format $format, $fromPath ,$writePath);
} 