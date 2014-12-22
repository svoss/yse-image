<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 22:40
 */

namespace ISTI\Image\Persist;

use ISTI\Image\Model\SourceInterface;

/**
 * Implement this interface if you want all the information to come directly from the RelationProvider and customizing this information is not provided
 * Interface UneditableInterface
 * @package ISTI\Image\Persist
 */
interface UneditableInterface{

    public function getSource();
    public function setSource($source);
    public function getSourceClass();

} 