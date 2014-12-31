<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 16:38
 */

namespace ISTI\Image\Saver;


use ISTI\Image\Model\SourceInterface;
use ISTI\Image\SEOImageException;
use Symfony\Component\HttpFoundation\File\File;
use OpenCloud\OpenStack;
use OpenCloud\ObjectStore\Service;
use OpenCloud\ObjectStore\Resource\Container;
use OpenCloud\ObjectStore\Exception\ObjectNotFoundException;
class OpenstackSaver implements SaverInterface{

    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $password;
    /**
     * @var string
     */
    protected $tenantId;
    /**
     * @var string
     */
    protected $idURL;
    /**
     * @var string
     */
    protected $privateContainerName;
    /**
     * @var string
     */
    protected $publicContainerName;

    /**
     * @var Container
     */
    protected $privateContainer;
    /**
     * @var Container
     */
    protected $publicContainer;

    /**
     * @var Container
     */
    protected $urlPrefix;
    /**
     * @var Service
     */
    protected $connection;

    function __construct($idURL, $password, $privateContainerName, $publicContainerName, $tenantId, $username, $urlPrefix, $privateUrlPrefix)
    {
        $this->idURL = $idURL;
        $this->password = $password;
        $this->privateContainerName = $privateContainerName;
        $this->publicContainerName = $publicContainerName;
        $this->tenantId = $tenantId;
        $this->username = $username;
        $this->connection = null;
        $this->privateContainer = null;
        $this->publicContainer = null;
        $this->urlPrefix = $urlPrefix;
        $this->privateUrlPrefix = $privateUrlPrefix;
    }

    /**
     * @return Service
     */
    protected function getConnection()
    {
        if($this->connection === null)
        {
            $os = new OpenStack(
                $this->idURL,
                array('username' => $this->username,
                    'password' => $this->password,
                    'tenantName' => $this->tenantId)
            );
            $this->connection = $os->objectStoreService("swift","NL","publicURL");
        }
        return $this->connection;
    }

    /**
     * @return Container
     */
    protected function getPrivateContainer()
    {
        if($this->privateContainer === null)
        {
            $this->privateContainer = $this->getConnection()->getContainer($this->privateContainerName);

        }
        return $this->privateContainer;
    }

    /**
     * @var Container
     */
    protected function getPublicContainer()
    {
        if($this->publicContainer === null)
        {
            $this->publicContainer = $this->getConnection()->getContainer($this->publicContainerName);
        }
        return $this->publicContainer;
    }
    /**
     * @param UploadedFile $uploadedFile
     * @return SourceInterface
     */
    public function createSource(File $uploadedFile)
    {

        $name = uniqid().".".$uploadedFile->guessExtension();
        $container = $this->getPrivateContainer();

        $resource = fopen($uploadedFile->getRealPath(),'r');
        $container->uploadObject($name,$resource);

        return new OpenstackSource($name);
    }

    public function updateSource(File $uploadedFile, SourceInterface $source)
    {

        if(!($source instanceof OpenstackSource)) {
            throw new SEOImageException("Openstack expected source of class: ISTI\Image\Saver\OpenstackSource");
        }
        else {
            $container = $this->getPrivateContainer();
            $resource = fopen($uploadedFile->getRealPath(),'r');
            $container->uploadObject($source->getFilename(), $resource);

        }

    }

    public function getFilePathToSource(SourceInterface $source)
    {
        $tmp = tempnam(sys_get_temp_dir(),'image');

        if(get_class($source) !== 'ISTI\Image\Saver\OpenstackSource' && !in_array('ISTI\Image\Saver\OpenstackSource', class_parents($source)) ) {
            throw new SEOImageException("Openstack expected source of class: ISTI\Image\Saver\OpenstackSource");
        }
        $container = $this->getPrivateContainer();
        $object = $container->getObject($source->getFilename());
        file_put_contents($tmp,$object->getContent());
        return $tmp;
    }

    /**
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return mixed
     */
    public function saveResized(SourceInterface $source, $targetPath, $localPath, $copy=false)
    {
        if(!$copy) {
            $container = $this->getPublicContainer();
            $container->uploadObject($targetPath, fopen($localPath,'r'));
        } else {
            throw new \Exception("Copy is not supported for now");
        }

    }

    /**
     * Is this file in cache
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return bool
     */
    public function cached($path)
    {
        try {
            $this->getPublicContainer()->getObject($path);
        }
        catch(ObjectNotFoundException $ex)
        {
            return false;
        }

        return true;
    }

    /**
     * Empties the cache of a certain path
     * @param SourceInterface $sourceInterface
     * @param string $toPath
     * @return mixed
     */
    public function emptyCache(SourceInterface $source, $toPath)
    {
        $this->getPublicContainer()->getObject($toPath)->delete();
    }

    /**
     * Delete file
     * @param SourceInterface $sourceInterface
     * @return mixed
     */
    public function delete(SourceInterface $source)
    {
        if(!($source instanceof OpenstackSource)) {
            throw new SEOImageException("Openstack expected source of class: ISTI\Image\Saver\OpenstackSource");
        }
        $this->getPrivateContainer()->getObject($source->getFilename())->delete();

    }

    /**
     * gets source class that this interface provides in
     * @return string
     */
    public function getClass()
    {
        return 'ISTI\Image\Saver\OpenstackSource';
    }

    /**
     * Gets the link to an image path(used in src=) tag of an image
     * @return string
     */
    public function linkTo($path)
    {
        return $this->urlPrefix.$path;
    }

    public function getExtension(SourceInterface $source)
    {
        if(get_class($source) !== 'ISTI\Image\Saver\OpenstackSource' && !in_array('ISTI\Image\Saver\OpenstackSource', class_parents($source)) ) {
            throw new SEOImageException("Openstack expected source of class: ISTI\Image\Saver\OpenstackSource");
        }
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
        return $this->privateUrlPrefix.$source->getFilename();
    }

    public function cropable(SourceInterface $source)
    {

        return $source->getExtension() !== 'svg';
    }
} 