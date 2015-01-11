<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 22/11/14
 * Time: 18:13
 */
namespace ISTI\Image\Persist;


use ISTI\Image\Model\ImageInfoInterface;
use ISTI\Image\Relation\RelationInfo;
use ISTI\Image\Relation\RelationProviderManager;
use ISTI\Image\Model\Format;
use ISTI\Image\Factory\ImageInfoFactoryManager;
use ISTI\Image\Persist\UneditableInterface;
/**
 * Handles persistence related tasks. Translates between image info object which provides the rest of the system in information
 * Will load the default data from the relation provider into an editable object using loadToPersistent
 * And will make sure that when an uneditable persist image is provided the data is loaded from the relationProvider instead
 * Class PersistenceManager
 * @package ISTI\Image\Persist
 */
class PersistenceManager {

    public function __construct($relationProviderManager, $factoryManager)
    {
        $this->relationProviderManager = $relationProviderManager;
        $this->factoryManager = $factoryManager;
        $this->repos = array();
    }

    /**
     * @var RelationProviderManager
     */
    protected $relationProviderManager;

    /**
     * @var ImageInfoFactoryManager
     */
    protected $factoryManager;


    /**
     * @var ImageInfoRepositoryInterface[]
     */
    protected $repos;
    /**
     * Creates a new
     * @param UneditableInterface $iii
     *
     * @return ImageInfoInterface
     */
    public function loadFromPersistent(UneditableInterface $ui, $imageInfoclass, RelationInfo $relationInfo)
    {

        if (in_array('ISTI\Image\Persist\EditableInterface',class_implements($ui))) {
            $title = $ui->getTitle();
            $alt = $ui->getAlt();
            $long = $ui->getLongDescription();
            $geo = $ui->getGeolocation();
            $filters = $ui->getFilters();
            $crops = $ui->getCrops();
            $paths = $ui->getPaths();
        } else {
            $relation = $this->relationProviderManager->getRelationProvider($relationInfo->getParentObject());
            $attrs = $relation->getAttributes($ui,$relationInfo);
            $title = $attrs['title'];
            $alt = $attrs['alt'];
            $long =  $attrs['longdescription'];
            $geo = $attrs['geo'];
            $filters = $relation->loadDefaultFilters($ui,$relationInfo);
            $crops = $relation->loadDefaultCrops($ui,$relationInfo);
            $paths = $relation->loadDefaultPaths($ui,$relationInfo);
        }
        $parent = get_class($relationInfo->getParentObject());
        $source = $ui->getSource();
        return $this->factoryManager->getFactory($imageInfoclass)->createInstance($title, $alt, $long, $geo, $parent, $crops, $paths, $source,$filters);
    }

    public function loadDefaultToPersistent(UneditableInterface $ui,  $relationInfo )
    {
        if ($ui instanceof EditableInterface) {
            $relation = $this->relationProviderManager->getRelationProvider($relationInfo->getParentObject());
            $attrs = $relation->getAttributes($ui,$relationInfo);
            $ui->setTitle($attrs['title']);
            $ui->setAlt($attrs['alt']);
            $ui->setLongDescription($attrs['longdescription']);
            $ui->setGeolocation($attrs['geo']);
            $ui->setFilters($relation->loadDefaultFilters($ui,$relationInfo));
            $ui->setCrops($relation->loadDefaultCrops($ui, $relationInfo));


            $ui->setPaths($relation->loadDefaultPaths($ui, $relationInfo));


        }
    }

    /**
     * If you provide an object
     * @param Object\string $class either a object or a class name
     * @throws SEOImageException
     *
     * @return ImageinfoRepositoryInterface
     */
    public function getRepository($class)
    {
        //@todo Handle inheritance?
        if(is_object($class))
        {
            $class = get_class($class);
        }
        if(isset($this->repos[$class])) {
            return $this->repos[$class];
        } else {
            //@todo add to unit test
            return null;

        }
    }

    /**
     *
     * @param ImageinfoRepositoryInterface $repository
     * @ret
     * @throws SEOImageException
     */
    public function addRepository($repository)
    {
        if(!isset($this->repos[$repository->getClass()])) {
            $this->repos[$repository->getClass()] = $repository;
        } else {
            throw new SEOImageException("An repository for the class:".$repository->getClass()."  was already found..");

        }

    }
} 