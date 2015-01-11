<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 11/01/15
 * Time: 14:56
 */

namespace ISTI\Image\Model;


class Filter {
    protected $name;
    protected $value;

    function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


}