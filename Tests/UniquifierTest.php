<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 02/12/14
 * Time: 22:21
 */

namespace ISTI\Image\Tests;

use ISTI\Image\Uniquifier;

class UniquifierTest extends \PHPUnit_Framework_TestCase{
    public function testUniquify()
    {
        $persistor = $this->getMock('ISTI\Image\Persist\EditableInterface');
        $pm = $this->persistenceMock("folder/foo",array(), "jpg",$persistor);
        $un = new Uniquifier($pm);
        $this->assertEquals("folder/foo.jpg",$un->uniquify("folder/foo.jpg",$persistor));

        $persistor = $this->getMock('ISTI\Image\Persist\EditableInterface');
        $pm = $this->persistenceMock("folder/foo",array('folder/foo.jpg'), "jpg",$persistor);
        $un = new Uniquifier($pm);
        $this->assertEquals("folder/foo-1.jpg",$un->uniquify("folder/foo.jpg",$persistor));

        $persistor = $this->getMock('ISTI\Image\Persist\EditableInterface');
        $pm = $this->persistenceMock("folder/foo",array('folder/foo.jpg'), "png",$persistor);
        $un = new Uniquifier($pm);
        $this->assertEquals("folder/foo.png",$un->uniquify("folder/foo.png",$persistor));
    }

    protected function persistenceMock($path, $pathsReturned, $extension,$persistor)
    {
        $repo = $this->getMockForAbstractClass("ISTI\Image\Persist\ImageinfoRepositoryInterface",array("getClass","similarPaths"));
        $repo->expects($this->once())->method("similarPaths")->with($path,$extension)->will($this->returnValue($pathsReturned));
        $pm =  $this->getMockBuilder("ISTI\Image\Persist\PersistenceManager",array("getRepository"))->disableOriginalConstructor()->getMock();
        $pm->expects($this->once())->method('getRepository')->with($persistor)->will($this->returnValue($repo));

        return $pm;

    }
} 