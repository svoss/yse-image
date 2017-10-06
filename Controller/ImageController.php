<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 1/31/16
 * Time: 9:45 PM
 */

namespace ISTI\Image\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class ImageController extends Controller
{
    public function cropableImageAction()
    {
        $request = $this->get('request');
        $folder = $this->container->getParameter('fs.private_folder');

        $image = $request->query->get('image');
        $borderize = $request->query->get('border',false);
        $full_path = $folder.$image;
        $img = new \Imagick($full_path);

        $bg = $request->query->get('bg');
        $width = $request->query->get('width');
        $height = $request->query->get('height');
        if ($bg === null) {
            if($img->getImageFormat() ===  'PNG') {
                $bg = new \ImagickPixel('transparent');
            } else {
                $bg = $img->getImageBorderColor();
            }

        }
        if($borderize){
            $geo = $img->getImageGeometry();
            $txt = var_export($geo, true);
            $dims = $this->borderDimensions($geo['width'], $geo['height'], $width/$height);
            $img->borderImage($bg,$dims[0], $dims[1]);
        }

        $r= new Response($img->getImageBlob(),200);
        $r->headers->set('Content-Type',$img->getImageMimeType());
        return $r;
    }

    public function colorAction()
    {
        $request = $this->get('request');
        $folder = $this->container->getParameter('fs.private_folder');

        $image = $request->query->get('image');
        $full_path = $folder.$image;
        $img = new \Imagick($full_path);

        $bg = $request->query->get('color');
        $name = null;
        try{
            if ($bg === null || $bg === '') {
                if($img->getImageFormat() ===  'PNG') {
                    $bg = new \ImagickPixel('transparent');

                } else {
                    $bg = $img->getImageBorderColor();
                }

            } else {
                $name = $bg;
                $bg = new \ImagickPixel($bg);
            }
        } catch(\ImagickPixelException $e)
        {
            return new Response(json_encode(['error' => true]),200);
        }

        if($name === null){
            $c = $bg->getColor();
            if(isset($c['a']) && $c['a'] === 0){
                $name =  'transparent';
            } else {
                $name = $this->rgb2hex($c);
            }
        }



        $r = new Response(json_encode(['error' => false, 'color' => $name]),200);
        return $r;
    }
    protected function rgb2hex($rgb)
    {
        return '#' . sprintf('%02x', $rgb['r']) . sprintf('%02x', $rgb['g']) . sprintf('%02x', $rgb['b']);
    }
    protected function borderDimensions($width, $height, $targetRatio)
    {
        $currentRatio = $width/$height;
        $factor = $targetRatio/$currentRatio;
        //real width relativly lower than actual width
        if($factor > 1) {
            $targetWidth = $width * $factor;
            return [(int) ceil(($targetWidth - $width)/2),1];
        } elseif($factor < 1) {
            $targetHeight = $height/$factor;
            return [1,(int) ceil(($targetHeight - $height)/2)];
        }
        return [0,0];
    }
}