<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 21:45
 */

namespace ISTI\Image\Tests\Saver;

use ISTI\Image\Saver\OpenstackSaver;
use Symfony\Component\HttpFoundation\File\File;

class OpenstackSaverTest extends \PHPUnit_Framework_TestCase{
    const USERNAME =  'wm-staging';
    const PASSWORD = 'd6krLD2Vt57fuu';
    const TENANTID = 'be2f9c9ac9bb48f7b50016ebdc746ca0';
    const IDURL = 'https://identity.stack.cloudvps.com/v2.0';

    const privateC = 'source-images';
    const publicC =  'images';
    public function testPreconditions()
    {
        $source = __DIR__."/images/nightwatch.jpg";
        $this->assertFileExists($source);
    }


    public function testSource()
    {

        $saver = new OpenstackSaver(SELF::IDURL, self::PASSWORD, self::privateC, self::publicC, self::TENANTID, self::USERNAME);
        $file = new File(__DIR__."/images/nightwatch-wrong.jpg");
        $filecorrected = new File(__DIR__."/images/nightwatch.jpg");
        $source  = $saver->createSource($file);
        $this->assertNotNull($source->getFilename());
        $saver->updateSource($filecorrected, $source);
        $saver->delete($source);
    }

    public function testResize()
    {
        $saver = new OpenstackSaver(SELF::IDURL, self::PASSWORD, self::privateC, self::publicC, self::TENANTID, self::USERNAME);
        $file = new File(__DIR__."/images/nightwatch.jpg");
        $source  = $saver->createSource($file);
        $file = new File(__DIR__."/images/nightwatch-zoom-100x100.jpg");
        $saver->saveResized($source, 'rijksmuseum/thumbs/nightwatch.jpg', $file);
        $this->assertTrue($saver->cached('rijksmuseum/thumbs/nightwatch.jpg'));
        $saver->emptyCache($source, 'rijksmuseum/thumbs/nightwatch.jpg');
        $this->assertFalse($saver->cached('rijksmuseum/thumbs/nightwatch.jpg'));
    }
} 