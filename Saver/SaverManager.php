<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 21:10
 */

namespace ISTI\Image\Saver;


class SaverManager {
    /**
     * @var SaverInterface []
     */
    protected $savers;

    /**
     * If you provide an object
     * @param Object\string $class either a object or a class name
     * @throws SEOImageException
     *
     * @return SaverInterface
     */
    public function getSaver($class)
    {
        //@todo Handle inheritance?
        if(is_object($class))
        {
            $class = get_class($class);
        }
        if(isset($this->savers[$class])) {
            return $this->savers[$class];
        } else {
            throw new SEOImageException("No saver for ".$class." found");

        }
    }

    /**
     *
     * @param SaverInterface $saver
     * @throws SEOImageException
     */
    public function addSaver($saver)
    {
        if(!isset($this->savers[$saver->getClass()])) {
            $this->savers[$saver->getClass()] = $saver;
        } else {
            throw new SEOImageException("An saver for the class:".$saver->getClass()."  was already found..");

        }

    }
} 