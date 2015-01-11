<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 19:38
 */

namespace ISTI\Image\Factory;

use ISTI\Image\Model\SourceInterface;

interface ImageInfoFactoryInterface {
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
     * @param $filters
     * @return mixed
     */
    public  function createInstance($title, $alt, $long, $geolocation, $parent, $crops, $paths, SourceInterface $source,$filters);

    public function getClass();
} 