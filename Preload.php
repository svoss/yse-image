<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 6/18/17
 * Time: 21:31
 */

namespace ISTI\Image;


class Preload
{
    protected $preloads;

    /**
     * Preload constructor.
     * @param $preloads
     */
    public function __construct()
    {
        $this->preloads = [];
    }


    public function add($entity, $attr, $formats)
    {
        if(!is_array($entity) &&  !in_array('Traversable', class_implements($entity))) {
            $entity = [$entity];
        }

        if(!is_array($formats)) {
            $formats = [$formats];
        }

        foreach($entity as $e) {
            foreach($formats as $f) {
                $this->preloads[] = [$e, $attr, $f];
            }
        }
    }

    /**
     * @return array
     */
    public function getPreloads()
    {

        return $this->preloads;
    }


}