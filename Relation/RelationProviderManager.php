<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 23/11/14
 * Time: 16:46
 */

namespace ISTI\Image\Relation;

use ISTI\Image\SEOImageException;
class RelationProviderManager {
    /**
     * @var RelationProviderInterface []
     */
    protected $relationProviders;

    /**
     * If you provide an object
     * @param Object\string $class either a object or a class name
     * @throws SEOImageException
     *
     * @return RelationProviderInterface
     */
    public function getRelationProvider($class)
    {
        //@todo Handle inheritance?
        if(is_object($class))
        {
            $class = get_class($class);

        }
        if(isset($this->relationProviders[$class])) {
           return $this->relationProviders[$class];
        } else {
            foreach(class_parents($class) as $class) {
                if(isset($this->relationProviders[$class])) {
                    return $this->relationProviders[$class];
                }
            }
            throw new SEOImageException("No relation provider for ".$class." found");

        }
    }

    /**
     *
     * @param RelationProviderInterface $relationProviderInterface
     * @throws SEOImageException
     */
    public function addRelationProvider($relationProviderInterface)
    {
        $class = $relationProviderInterface->getParentClass();
        if(is_array($class)){
            foreach($class as $c) {
                $this->addProvider($c, $relationProviderInterface);
            }
        }
        else {
            $this->addProvider($class, $relationProviderInterface);
        }


    }

    private function addProvider($name, $provider)
    {
        if(!isset($this->relationProviders[$name])) {
            $this->relationProviders[$name] = $provider;
        } else {
            throw new SEOImageException("An relation provider for the class:".$name."  was already found..");

        }
    }


    /**
     * @param $class
     * @param $formatName
     *
     * @return Format
     */
    public function getFormatFor($relationInfo, $class, $formatName)
    {

        $provider = $this->getRelationProvider($class);
        $formats = $provider->getFormats($relationInfo);

        foreach($formats as $format)
        {

            if($format->getName() === $formatName)
            {
                return $format;
            }
        }
        return null;
    }
} 