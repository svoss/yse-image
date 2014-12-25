<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 18/12/14
 * Time: 19:47
 */

namespace ISTI\Image\Saver;


use ISTI\Image\Model\SourceInterface;
use Symfony\Component\HttpFoundation\File\File;

class FilesystemSaver implements SaverInterface {

    /**
     * @var String
     */
    protected $pathPrefix;

    /**
     * @var String
     */
    protected $privateFolder;

    /**
     * @var String
     */
    protected $publicFolder;

    /**
     * @var String
     */
    protected $privatePathPrefix;
    public function __construct($pathPrefix, $privatePathPrefix, $privateFolder, $publicFolder)
    {
        $this->pathPrefix = $pathPrefix;
        $this->privateFolder = $privateFolder;
        $this->publicFolder = $publicFolder;
        $this->privatePathPrefix = $privatePathPrefix;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @return SourceInterface
     */
    public function createSource(File $uploadedFile)
    {
        $ext = $uploadedFile->guessExtension();
        $filename = uniqid()."-".$uploadedFile->getFilename().$ext;

        $uploadedFile->move($this->privateFolder, $filename);

        return new FilesystemSource($filename);
    }

    public function updateSource(File $uploadedFile, SourceInterface $source)
    {
        $filename = $source->getFilename();
        $uploadedFile->move($this->privateFolder, $filename);

    }

    public function getFilePathToSource(SourceInterface $source)
    {
        return $this->privateFolder.$source->getFilename();
    }

    /**
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return mixed
     */
    public function saveResized(SourceInterface $source, $targetPath, $localPath)
    {
        $target = $this->publicFolder.$targetPath;
        $dir = dirname($target);
        if(!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        rename($localPath,$target );
    }

    /**
     * Is this file in cache
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return bool
     */
    public function cached($path)
    {
        return file_exists($this->publicFolder.$path);
    }

    /**
     * Empties the cache of a certain path
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return mixed
     */
    public function emptyCache(SourceInterface $source, $toPath)
    {
        unlink($this->publicFolder.$toPath);
    }

    /**
     * Delete file
     * @param SourceInterface $sourceInterface
     * @return mixed
     */
    public function delete(SourceInterface $source)
    {
        unlink($this->privateFolder.$source->getFilename());
    }

    /**
     * Gets the link to an image path(used in src=) tag of an image
     * @return string
     */
    public function linkTo($path)
    {
        return $this->pathPrefix."".$path;
    }

    /**
     * gets source class that this interface provides in
     * @return string
     */
    public function getClass()
    {
        return 'ISTI\Image\Saver\FilesystemSource';
    }

    public function getExtension(SourceInterface $source)
    {
        $filename = $source->getFilename();
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if($ext === 'jpeg')
            $ext = 'jpg';
        return $ext;
    }

    /**
     * Gets the link to an image path(used in src=) tag of an image
     * @return string
     */
    public function linkToSource(SourceInterface $source)
    {
        return $this->privatePathPrefix.$source->getFilename();
    }


}