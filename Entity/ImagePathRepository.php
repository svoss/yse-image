<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 10:50
 */

namespace ISTI\Image\Entity;

use Doctrine\ORM\EntityManager;
use ISTI\Image\Helper\ImageInfoHasher;
use ISTI\Image\Model\Format;
use ISTI\Image\Model\ImageInfoInterface;
use ISTI\Image\Persist\ImageinfoRepositoryInterface;

class ImagePathRepository implements ImageinfoRepositoryInterface{

    /**
     * @var ImageInfoHasher
     */
    protected $hasher;

    /**
     * @var EntityManager
     */
    protected $em;

    protected $cache;

    /**
     * @param ImageInfoHasher $hasher
     */
    public function __construct(ImageInfoHasher $hasher, EntityManager $entityManager)
    {
        $this->cache = array();
        $this->hasher = $hasher;
        $this->em = $entityManager;
    }

    /**
     * Returns all the paths of the imageinfo objects that have a path that are $path*$extension this makes sure the path is unique
     * Will ig
     * @return string[]
     */
    public function similarPaths($path, $extension)
    {
        $query = $this->em->createQuery("SELECT p.actualPath FROM ISTI\Image\Entity\ImagePath p WHERE p.actualPath LIKE :query ")->setParameter("query", $path."%".$extension);

        return array_map('current',$query->getScalarResult());
    }

    public function setActualPathUsed($path, ImageInfoInterface $info, Format $format)
    {
        $hash = $this->hasher->hash($info, $format);
        $query = $this->em->createQuery("SELECT p FROM ISTI\Image\Entity\ImagePath p WHERE p.hash = :hash ")->setParameter('hash',$hash);
        $r = $query->getOneOrNullResult();
        if($r === null) {
            $r = new ImagePath();
            $r->setHash($hash);
            $r->setActualPath($path);
        }
        $r->setActualPath($path);
        $this->em->persist($r);
        $this->em->flush();
        $this->cache[$r->getHash()] = $r;
    }

    public function getActualPathUsed(ImageInfoInterface $info, Format $format)
    {
        $hash = $this->hasher->hash($info, $format);
        if(isset($this->cache[$hash])){
            $r = $this->cache[$hash];
        }else{
            $query = $this->em->createQuery("SELECT p FROM ISTI\Image\Entity\ImagePath p WHERE p.hash = :hash ")->setParameter('hash',$hash);
            $r = $query->getOneOrNullResult();
        }
        if ($r!== null) {
            $this->cache[$r->getHash()] = $r;
        }

        return $r === null ? null : $r->getActualPath();
    }

    public function removeActualPathUsed(ImageInfoInterface $info, Format $format)
    {
        $hash = $this->hasher->hash($info, $format);
        if(isset($this->cache[$hash])){
            unset($this->cache[$hash]);
        }
        $query = $this->em->createQuery("DELETE FROM ISTI\Image\Entity\ImagePath p WHERE p.hash = :hash ")->setParameter('hash',$hash);
        $query->execute();

    }

    public function getClass()
    {
        return 'ISTI\Image\Model\ImageInfo';
    }

    public function loadInCache($items)
    {
        $hashes = [];

        foreach ($items as $x) {
            $info = $x['image'];
            $format = $x['format'];

            $hash = $this->hasher->hash($info, $format);

            if(!isset($this->cache[$hash])){
                $hashes[] = $hash;
            }

        }

        $query = $this->em->createQuery("SELECT p FROM ISTI\Image\Entity\ImagePath p WHERE p.hash IN (:hash) ");
        $query->setParameter('hash',$hashes);
        foreach($query->getResult() as $r) {
            $this->cache[$r->getHash()] = $r;
        }
    }


}