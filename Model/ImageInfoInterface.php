<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 10:59
 */
namespace ISTI\Image\Model;
/**
 *
 * Class Format
 * @package ISTI\Image\Model
 * @author Stijn Voss<svoss@i-sti.nl>
 */
interface ImageInfoInterface {
    public function getSource();
    public function getTitle();
    public function getAlt();
    public function getLongDescription();
    public function getGeolocation();
    public function getParentClass();
    public function getCropForFormat(Format $format, $sub = null);
    public function getPathForFormat(Format $format, $sub = null);


} 