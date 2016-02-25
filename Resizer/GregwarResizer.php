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

        if(!file_exists($fromPath))
        {
            throw new SEOImageException("File ".$fromPath." does not exist");
        }

        $image = Image::open($fromPath);
        $resize = $imageinfo->getCropForFormat($format);
        if ($resize->getType() === 'zoom' || $resize->getType() === null){
            $image->zoomCrop($format->getWidth(), $format->getHeight());
        } else if($resize->getType() === 'custom') {
            $crop = $resize->getCostumCrop();
            if($imageinfo->getCropOutside())
            {
                $target = $this->enlarge($image->width(),$image->height(),$format->getWidth()/$format->getHeight());
                $image->resize($target[0],$target[1],$imageinfo->getBgColor());
            }
            $image
                ->crop($crop->getStartX(),$crop->getStartY(),$crop->getWidth(), $crop->getHeight())
                ->resize( $format->getWidth(),$format->getHeight());
        } else {
            throw new SEOImageException("Can't handle resize type: ".$resize->getType());
        }
        if($imageinfo->getFiltersForFormat($format)!== null){
            foreach($imageinfo->getFiltersForFormat($format) as $filter){
                $this->applyFilter($image,$filter->getName(), $filter->getValue());
            }

        }

        $image->save($writePath,'guess',90);


    }

    protected function applyFilter(Image $image, $filterName, $filterValue)
    {
        switch($filterName){
            case 'brightness':
                $image->brightness($filterValue);
                break;
            case 'contrast':
                $image->contrast($filterValue);
                break;
            default:
                throw new SEOImageException("No filter found for ".$filterName);
                break;

        }


    }

    public function createAdminThumb($path,$width,$height)
    {
        $image = Image::open($path);
        return "/".$image->zoomCrop($width, $height)->jpeg();
    }

    protected function enlarge($width, $height, $targetRatio)
    {
        $currentRatio = $width/$height;
        $factor = $targetRatio/$currentRatio;
        //real width relativly lower than actual width
        if($factor > 1) {
            $targetWidth = $width * $factor;
            $targetHeight = $height;
        } elseif($factor < 1) {
            $targetHeight = $height/$factor;
            $targetWidth = $width;
        } else {
            $targetWidth = $width;
            $targetHeight = $height;
        }
        return [$targetWidth, $targetHeight];
    }
} 