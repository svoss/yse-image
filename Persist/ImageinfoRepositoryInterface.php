<?php
/**
 * Enables communication between the storage of the image information and the rest of the application
 */

namespace ISTI\Image\Persist;


interface ImageinfoRepositoryInterface {
    /**
     * Returns all the paths of the imageinfo objects that have a path that are $path* this makes sure the path is unique
     * Will ig
     * @return string[]
     */
    public function similarPaths($path, $extension);

    public function getClass();
} 