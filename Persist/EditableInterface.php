<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 22/11/14
 * Time: 18:06
 */

namespace ISTI\Image\Persist;


interface EditableInterface extends UneditableInterface{
    public function getTitle();
    public function getAlt();
    public function getLongDescription();
    public function getGeolocation();
    public function getCrops();
    public function getPaths();
    public function getFilters();
    public function setFilters($filters);
    public function setTitle($title);
    public function setAlt($alt);
    public function setLongDescription($description);
    public function setGeolocation($geolocation);
    public function setCrops($crops);
    public function setPaths($paths);
} 