<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 06/12/14
 * Time: 15:02
 */

namespace ISTI\Image\Resizer;

use Gregwar\Image\Image;
use ISTI\Image\Model\Format;
use ISTI\Image\Model\ImageInfoInterface;
use ISTI\Image\SEOImageException;

class GregwarResizer implements ResizerInterface {
    public function resize(ImageInfoInterface $imageinfo, Format $format, $fromPath, $writePath)
    {

        if(!file_exists($fromPath)) {
            throw new SEOImageException("File ".$fromPath." does not exist");
        }

        $image = Image::open($fromPath);
        $resize = $imageinfo->getCropForFormat($format);
        if ($resize->getType() === 'zoom'){
            $image->zoomCrop($format->getWidth(), $format->getHeight());
        } else if($resize->getType() === 'custom') {
            $crop = $resize->getCustumCrop();
            $image
                ->crop($crop->getStartX(),$crop->getStartY(),$crop->getWidth(), $crop->getHeight())
                ->resize($format->getHeight(), $format->getWidth());
        } else {
            throw new SEOImageException("Can't handle resize type: ".$resize->getType());
        }

        $image->save($writePath);


    }

} 