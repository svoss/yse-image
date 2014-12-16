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
        return $this->im->format($format, $parent, $attribute, $index);
    }

} 