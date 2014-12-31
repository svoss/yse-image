<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 18/12/14
 * Time: 19:45
 */

namespace ISTI\Image\Saver;


use ISTI\Image\Model\SourceInterface;

class FilesystemSource implements SourceInterface {
    /**
     * @var string
     */
    protected $filename;

    function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }


    public function getId()
    {

        return $this->filename;
    }

    public function getExtension()
    {
        $filename = $this->getFilename();
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if($ext === 'jpeg')
            $ext = 'jpg';
        return $ext;
    }
}