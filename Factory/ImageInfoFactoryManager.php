<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 19:41
 */

namespace ISTI\Image\Factory;

use ISTI\Image\SEOImageException;
class ImageInfoFactoryManager {
    /**
     * @var ImageInfoFactory []
     */
    protected $factories;

    /**
     * If you provide an object
     * @param Object\string $class either a object or a class name
     * @throws SEOImageException
     *
     * @return ImageInfoFactoryInterface
     */
    public function getFactory($class)
    {
        //@todo Handle inheritance?
        if(is_object($class))
        {
            $class = get_class($class);
        }
        if(isset($this->factories[$class])) {
            return $this->factories[$class];
        } else {
            throw new SEOImageException("No factory for ".$class." found");

        }
    }

    /**
     *
     * @param ImageInfoFactoryInterface $factory
     * @throws SEOImageException
     */
    public function addFactory($factory)
    {
        if(!isset($this->relationProviders[$factory->getClass()])) {
            $this->factories[$factory->getClass()] = $factory;
        } else {
            throw new SEOImageException("An factory for the class:".$factory->getClass()."  was already found..");

        }

    }
} 