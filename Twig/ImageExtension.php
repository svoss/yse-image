<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 14/12/14
 * Time: 10:08
 */

namespace ISTI\Image\Twig;


use ISTI\Image\ImageManager;

class ImageExtension extends \Twig_Extension {

    /**
     * @var ImageManager
     */
    protected $im;

    /**
     * @param ImageManager $im
     */
    public function __construct(ImageManager $im)
    {
        $this->im = $im;
    }

    public function getName()
    {
        return 'isti_image';
    }

    public function getFunctions(){
        return array(
            'resize' => new \Twig_Function_Function(array($this,'resizeImage')),

            'image_thumb' => new \Twig_Function_Function(array($this,'thumb')),
            'image_formats' => new \Twig_Function_Function(array($this,'imageFormats')),
            'source_link' => new \Twig_Function_Function(array($this,'sourceLink')),
            "min_res" => new \Twig_Function_Function(array($this,'minRes')),
            "croppable" => new \Twig_Function_Function(array($this,"croppable"))
        );
    }

    /**
     *
     * @param $format
     * @param $parent
     * @param $attribute
     * @param null $index
     *
     * @return string src to file
     */
    public function resizeImage($format, $parent, $attribute, $index = null)
    {
        return $this->im->path($format, $parent, $attribute, $index);
    }

    /**
     *
     * @param $format
     * @param $parent
     * @param $attribute
     * @param null $index
     *
     * @return string src to file
     */
    public function thumb( $parent, $attribute, $width, $height, $index = null)
    {
        return $this->im->thumb( $parent, $attribute, $width, $height);
    }

    public function imageFormats($parent, $attribute, $index = null)
    {
        $formats = $this->im->getImageFormats($parent, $attribute, $index);
        $format_array = array();
        foreach($formats as $k => $f) {
            $format_array[$k] = $f->toArray();
        }
        return json_encode($format_array);
    }
    public function minRes($parent, $attribute, $index = null)
    {
        return  $this->im->getMinRes($parent, $attribute, $index);

    }
    public function sourceLink($source)
    {
        return $this->im->getLinkToSource($source);
    }

    public function croppable($source)
    {
        return $this->im->isCroppable($source);
    }

} 