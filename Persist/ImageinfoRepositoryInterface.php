<?php
/**
 * Enables communication between the storage of the image information and the rest of the application
 */

namespace ISTI\Image\Persist;

use ISTI\Image\Model\Format;

interface ImageinfoRepositoryInterface {
    /**
     * Returns all the paths of the imageinfo objects that have a path that are $path*$extension this makes sure the path is unique
     * Will ig
     * @return string[]
     */
    public function similarPaths($path, $extension);

    public function setActualPathUsed($path, UneditableInterface $persist, Format $format);

    public function getActualPathUsed(UneditableInterface $persist, Format $format);

    public function getClass();
} 