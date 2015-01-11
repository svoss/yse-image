<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 11/01/15
 * Time: 13:34
 */

namespace ISTI\Image\Saver;

use Predis\Client;

class RedisCacher implements CacherInterface {


    /**
     * @var Client
     */
    protected $connection;

    /**
     * @var String
     */
    protected $setKey;

    function __construct($connection, $setKey)
    {
        $this->connection = $connection;
        $this->setKey = $setKey;
    }

    /**
     * @param $path
     * @return boolean
     */
    public function isCached($path)
    {
        return $this->connection->sismember($this->setKey, $path);
    }

    public function removeCached($path)
    {
        $this->connection->srem($this->setKey, $path);
    }

    public function setCached($path)
    {
        $this->connection->sadd($this->setKey, $path);
    }

    public function flush()
    {
        // TODO: Implement flush() method.
    }

}