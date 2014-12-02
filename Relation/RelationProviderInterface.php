<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 12:09
 */

namespace ISTI\Image\Relation;
use ISTI\Image\Model\ImageInfoInterface;
use ISTI\Image\Persist\UneditableInterface;

/**
 * This interface declares how the communication between a Relation Provider and the rest of the system is done
 * An RelationProvider should provide in information of all the images an object has a relation to
 * Interface RelationProviderInterface
 * @package ISTI\Image\Relation
 */
interface RelationProviderInterface {
    /**
     * Gets the classes that this interface provides information of
     * @return string
     */
    public function getParentClass();

    /**
     * Get the attributes of the object that a relation with the image entity
     * @return string[]
     */
    public function getAttributes(UneditableInterface $ui, RelationInfo $relationInfo);

    /**
     * @param RelationInfo $relationInfo
     * @param ImageInfoInterface $image
     * @return mixed
     */
    public function loadMeta(UneditableInterface $ui,RelationInfo $relationInfo);

    /**
     * @param RelationInfo $relationInfo
     * @param ImageInfoInterface $image
     * @return mixed
     */
    public function loadDefaultPaths(UneditableInterface $ui, RelationInfo $relationInfo);

    /**
     * @param RelationInfo $relationInfo
     * @param ImageInfoInterface $image
     * @return mixed
     */
    public function loadDefaultCrops(UneditableInterface $ui, RelationInfo $relationInfo);

    /**
     * Gets formats
     * @return Format[]
     */
    public function getFormats(RelationInfo $relationInfo);
} 