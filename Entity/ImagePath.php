<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 10:18
 */

namespace ISTI\Image\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * An entity that keeps track of the image path, can be used to make sure all image paths are unique
 *
 * Class ImagePaths
 * @package ISTI\Image\Entity\Image
 * @ORM\Entity()
 * @ORM\Table(indexes={@ORM\Index(name="search_actual_path", columns={"hash"})})
 */
class ImagePath {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="string",length=32)
     */
    protected $hash;

    /**
     * @ORM\Column(type="string",unique=true)
     */
    protected $actualPath;

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getActualPath()
    {
        return $this->actualPath;
    }

    /**
     * @param mixed $actualPath
     */
    public function setActualPath($actualPath)
    {
        $this->actualPath = $actualPath;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }


}