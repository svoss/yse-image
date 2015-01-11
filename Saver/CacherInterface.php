<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 11/01/15
 * Time: 13:28
 */

namespace ISTI\Image\Saver;

/**
 * Can be used to cache the availability of certain resized images. F.e. when using openstack
 * Interface CacherInterface
 * @package ISTI\Image\Saver
 */
Interface CacherInterface {
    /**
     * @param $path
     * @return boolean
     */
    public function isCached($path);
    public function removeCached($path);
    public function setCached($path);
    public function flush();


}