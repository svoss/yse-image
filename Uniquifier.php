<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 02/12/14
 * Time: 20:50
 */

namespace ISTI\Image;


use ISTI\Image\Model\SourceInterface;
use ISTI\Image\Persist\EditableInterface;
use ISTI\Image\Persist\PersistenceManager;
use ISTI\Image\Model\ImageInfoInterface;
class Uniquifier {

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;
    /**
     * @param SaverManager $saveManager
     */
    public function __construct($persistenceManager )
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Unquifies the path, so make sures it only occurs once
     * @param string          $candidatePath
     * @param EditableInterface $persistor
     *
     * @return string
     */
    public function uniquify($candidatePath, ImageInfoInterface $image)
    {
        $info = explode('.', $candidatePath);
        $extension = end($info);
        $path = implode(".",array_splice($info, 0,-1));

        $repo = $this->persistenceManager->getRepository($image);


        $candidateConflicts = $repo->similarPaths($path, $extension);


        for($i=1; $i < pow(2,20);$i++)
        {
            if(!in_array($candidatePath, $candidateConflicts)) {
                return $candidatePath;
            }

            $candidatePath  = $path."-".$i.".".$extension;
        }

        throw new SEOImageException("More than ".pow(2,20)." paths in the same path name space found.");


    }
} 