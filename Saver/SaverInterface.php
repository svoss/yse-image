<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 20:51
 */

namespace ISTI\Image\Saver;

use Symfony\Component\HttpFoundation\File\File ;
use ISTI\Image\Model\SourceInterface;

interface SaverInterface {

    /**
     * @param UploadedFile $uploadedFile
     * @return SourceInterface
     */
    public function createSource(File $uploadedFile);

    public function updateSource(File $uploadedFile, SourceInterface $source);

    public function getFilePathToSource(SourceInterface $source);
    /**
     * @param SourceInterface $sourceInterface
     * @param string          $toPath
     * @return mixed
     */
    public function saveResized(SourceInterface $source, $targetPath, $localPath,$copy = false);

    /**
     * Is this file in cache
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return bool
     */
    public function cached($path);

    /**
     * Empties the cache of a certain path
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return mixed
     */
    public function emptyCache(SourceInterface $source, $toPath);

    /**
     * Delete file
     * @param SourceInterface $sourceInterface
     * @return mixed
     */
    public function delete(SourceInterface $source);

    /**
     * Gets the link to an image path(used in src=) tag of an image
     * @return string
     */
    public function linkTo($path);

    /**
     * Gets the link to an image path(used in src=) tag of an image
     * @return string
     */
    public function linkToSource(SourceInterface $source);


    /**
     * gets source class that this interface provides in
     * @return string
     */
    public function getClass();

    public function getExtension(SourceInterface $source);

    public function cropable(SourceInterface $source);
} 