<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 21/11/14
 * Time: 12:04
 */

namespace ISTI\Image\Relation;


class RelationInfo {
    const ManyToOne = 1;
    const OneToOne = 2;
    static public $relationTypes = array(self::OneToOne, self::ManyToOne);
    /**
     * Types
     * @var string
     */
    protected $type;

    /**
     * @var integer
     */
    protected $index;

    /**
     * The parent object
     * @var Object
     */
    protected $parentObject;

    /**
     * Attribute name in parentObject that refers to this object
     * @var string
     */
    protected $attribute;

    function __construct($attribute, $index, $parentObject, $type)
    {
        $this->attribute = $attribute;
        $this->index = $index;
        $this->parentObject = $parentObject;
        $this->type = $type;
    }


    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }


    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }


    /**
     * @return Object
     */
    public function getParentObject()
    {
        return $this->parentObject;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function getPersistence()
    {
        $persistence = call_user_func(array($this->parentObject, 'get'.ucfirst($this->attribute)));
        if($this->getType() === self::ManyToOne) {
            return $persistence[$this->index];
        } else {
            return $persistence;
        }
    }



} 