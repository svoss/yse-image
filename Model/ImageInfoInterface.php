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
    /**
     * @return SourceInterface
     */
    public function getSource();
    public function getTitle();
    public function getAlt();
    public function getLongDescription();
    public function getGeolocation();

    /**
     * @param Format $format
     * @return Resize
     */
    public function getCropForFormat(Format $format);
    public function getPathForFormat(Format $format);


} 