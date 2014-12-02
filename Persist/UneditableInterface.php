<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 22:40
 */

namespace ISTI\Image\Persist;

use ISTI\Image\Model\SourceInterface;

interface UneditableInterface{

    public function getSource();
    public function setSource();
    public function getParentObject();

} 