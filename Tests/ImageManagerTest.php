<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 02/12/14
 * Time: 20:28
 */

namespace ISTI\Image\Tests;

use ISTI\Image\ImageManager;
use ISTI\Image\SEOImageException;
use ISTI\Image\Relation\RelationInfo;
use ISTI\Resizer\ResizerInterface;
class ImageManagerTest extends \PHPUnit_Framework_TestCase{

    /**
     * @expectedException        ISTI\Image\SEOImageException
     * @expectedExceptionMessage No index defined when in a one to many relation
     */
    public function testFormatNoIndex()
    {
        $pm = $this->getMockBuilder('ISTI\Image\Persist\PersistenceManager')->disableOriginalConstructor()->getMock();
        $ri = $this->getMock('ResizerInterface');

        $u = $this->getMockBuilder("ISTI\Image\Uniquifier")->disableOriginalConstructor()->getMock();
        $saveManager = $this->getMock('ISTI\Image\Saver\SaverManager');
        $rpm = $this->getMock("ISTI\Image\Relation\RelationProviderManager");
        $im = new ImageManager($pm,'FooClass',$ri,$saveManager, $u, $rpm);

        $parent = $this->getMock("StdClass",array("getImages"));
        $parent->expects($this->once())->method("getImages")->will($this->returnValue(array()));

        $im->format("boe",$parent,'images');
    }

    public function testFormatOneToOneRelation()
    {
        $path = 'foo/image.jpg';
        $format = 'foo';
        $sourcePath = '/tmp/image.jpg';
        $cached = false;

        $persistence = $this->getMock("ISTI\Image\Persist\EditableInterface");
        $parent = $this->getMock("StdClass",array("getImage"));
        $parent->expects($this->once())->method('getImage')->will($this->returnValue($persistence));

        $ri = new RelationInfo('image',null,$parent,RelationInfo::OneToOne);
        $formatMock = $this->getMock('ISTI\Image\Model\Format');
        $source = $this->getMock("ISTI\Image\Model\SourceInterface");

        $imageinfo = $this->getMock("ISTI\Image\Model\ImageInfoInterface");
        $imageinfo->expects($this->never())->method('getPathFor')->with($ri, $parent, $format)->will($this->returnValue($path));
        $imageinfo->expects($this->atLeastOnce())->method('getSource')->with()->will($this->returnValue($source));

        $repo = $this->createRepoMock('foo/image.jpg', $persistence, $formatMock);


        $pm = $this->getMockBuilder('ISTI\Image\Persist\PersistenceManager',array('loadFromPersistent','getRepository'))->disableOriginalConstructor()->getMock();
        $pm->expects($this->once())->method('loadFromPersistent')->with($persistence,'StdClass',$ri)->will($this->returnValue($imageinfo));
        $pm->expects($this->once())->method('getRepository')->with($persistence)->will($this->returnValue($repo));

        $u = $this->getMockBuilder("ISTI\Image\Uniquifier",array('uniquify'))->disableOriginalConstructor()->getMock();
        $u->expects($this->never())->method('uniquify');

        $saverManager = $this->createSaverManager($path, $source, $cached , $sourcePath);
        $resizer = $this->createResizer($imageinfo,$formatMock,$sourcePath, $cached);

        $rpm = $this->getMock("ISTI\Image\Relation\RelationProviderManager", array("getFormatFor"));
        $rpm->expects($this->once())->method("getFormatFor")->with($ri, $persistence, $format)->will($this->returnValue($formatMock));
        $im = new ImageManager($pm, 'StdClass', $resizer, $saverManager, $u,$rpm);


        $this->assertEquals($im->format($format,$parent,'image'), $path);

    }

    public function testFormatNonUnqiuenessForcing()
    {
        $path = 'foo/image.jpg';
        $format = 'foo';
        $sourcePath = '/tmp/image.jpg';
        $cached = false;

        $persistence = $this->getMock("ISTI\Image\Persist\EditableInterface");
        $parent = $this->getMock("StdClass",array("getImage"));
        $parent->expects($this->once())->method('getImage')->will($this->returnValue($persistence));

        $ri = new RelationInfo('image',null,$parent,RelationInfo::OneToOne);
        $formatMock = $this->getMock('ISTI\Image\Model\Format');
        $source = $this->getMock("ISTI\Image\Model\SourceInterface");

        $imageinfo = $this->getMock("ISTI\Image\Model\ImageInfoInterface");
        $imageinfo->expects($this->once())->method('getPathForFormat')->with($formatMock)->will($this->returnValue($path));
        $imageinfo->expects($this->atLeastOnce())->method('getSource')->with()->will($this->returnValue($source));



        $pm = $this->getMockBuilder('ISTI\Image\Persist\PersistenceManager',array('loadFromPersistent','getRepository'))->disableOriginalConstructor()->getMock();
        $pm->expects($this->once())->method('loadFromPersistent')->with($persistence,'StdClass',$ri)->will($this->returnValue($imageinfo));
        $pm->expects($this->once())->method('getRepository')->with($persistence)->will($this->returnValue(null));

        $u = $this->getMockBuilder("ISTI\Image\Uniquifier",array('uniquify'))->disableOriginalConstructor()->getMock();
        $u->expects($this->never())->method('uniquify');

        $saverManager = $this->createSaverManager($path, $source, $cached , $sourcePath);
        $resizer = $this->createResizer($imageinfo,$formatMock,$sourcePath, $cached);

        $rpm = $this->getMock("ISTI\Image\Relation\RelationProviderManager", array("getFormatFor"));
        $rpm->expects($this->once())->method("getFormatFor")->with($ri, $persistence, $format)->will($this->returnValue($formatMock));
        $im = new ImageManager($pm, 'StdClass', $resizer, $saverManager, $u,$rpm);


        $this->assertEquals($im->format($format,$parent,'image'), $path);
    }

    public function testFormatInCache()
    {
        $path = 'foo/image.jpg';
        $format = 'foo';
        $sourcePath = '/tmp/image.jpg';
        $cached = true;

        $persistence = $this->getMock("ISTI\Image\Persist\EditableInterface");
        $parent = $this->getMock("StdClass",array("getImage"));
        $parent->expects($this->once())->method('getImage')->will($this->returnValue($persistence));

        $ri = new RelationInfo('image',null,$parent,RelationInfo::OneToOne);
        $formatMock = $this->getMock('ISTI\Image\Model\Format');
        $source = $this->getMock("ISTI\Image\Model\SourceInterface");

        $imageinfo = $this->getMock("ISTI\Image\Model\ImageInfoInterface");
        $imageinfo->expects($this->once())->method('getPathForFormat')->with($formatMock)->will($this->returnValue($path));
        $imageinfo->expects($this->atLeastOnce())->method('getSource')->with()->will($this->returnValue($source));



        $pm = $this->getMockBuilder('ISTI\Image\Persist\PersistenceManager',array('loadFromPersistent','getRepository'))->disableOriginalConstructor()->getMock();
        $pm->expects($this->once())->method('loadFromPersistent')->with($persistence,'StdClass',$ri)->will($this->returnValue($imageinfo));
        $pm->expects($this->once())->method('getRepository')->with($persistence)->will($this->returnValue(null));
        $u = $this->getMockBuilder("ISTI\Image\Uniquifier",array('uniquify'))->disableOriginalConstructor()->getMock();
        $u->expects($this->never())->method('uniquify');

        $saverManager = $this->createSaverManager($path, $source, $cached , $sourcePath);
        $resizer = $this->createResizer($imageinfo,$formatMock,$sourcePath, $cached);

        $rpm = $this->getMock("ISTI\Image\Relation\RelationProviderManager", array("getFormatFor"));
        $rpm->expects($this->once())->method("getFormatFor")->with($ri, $persistence, $format)->will($this->returnValue($formatMock));
        $im = new ImageManager($pm, 'StdClass', $resizer, $saverManager, $u,$rpm);


        $this->assertEquals($im->format($format,$parent,'image'), $path);
    }

    public function testFormatNewForceUnique()
    {
        $path = 'foo/image.jpg';
        $format = 'foo';
        $sourcePath = '/tmp/image.jpg';
        $cached = false;

        $persistence = $this->getMock("ISTI\Image\Persist\EditableInterface");
        $parent = $this->getMock("StdClass",array("getImage"));
        $parent->expects($this->once())->method('getImage')->will($this->returnValue($persistence));

        $ri = new RelationInfo('image',null,$parent,RelationInfo::OneToOne);
        $formatMock = $this->getMock('ISTI\Image\Model\Format');
        $source = $this->getMock("ISTI\Image\Model\SourceInterface");

        $repo = $this->createRepoMock(null, $persistence, $formatMock,"foo/image-1.jpg");
        $imageinfo = $this->getMock("ISTI\Image\Model\ImageInfoInterface");
        $imageinfo->expects($this->once())->method('getPathForFormat')->with($formatMock)->will($this->returnValue($path));
        $imageinfo->expects($this->atLeastOnce())->method('getSource')->with()->will($this->returnValue($source));



        $pm = $this->getMockBuilder('ISTI\Image\Persist\PersistenceManager',array('loadFromPersistent','getRepository'))->disableOriginalConstructor()->getMock();
        $pm->expects($this->once())->method('loadFromPersistent')->with($persistence,'StdClass',$ri)->will($this->returnValue($imageinfo));
        $pm->expects($this->once())->method('getRepository')->with($persistence)->will($this->returnValue($repo));

        $u = $this->getMockBuilder("ISTI\Image\Uniquifier",array('uniquify'))->disableOriginalConstructor()->getMock();
        $u->expects($this->once())->method('uniquify')->with($path,$persistence)->will($this->returnValue('foo/image-1.jpg'));

        $saverManager = $this->createSaverManager('foo/image-1.jpg', $source, $cached , $sourcePath);
        $resizer = $this->createResizer($imageinfo,$formatMock,$sourcePath, $cached);

        $rpm = $this->getMock("ISTI\Image\Relation\RelationProviderManager", array("getFormatFor"));
        $rpm->expects($this->once())->method("getFormatFor")->with($ri, $persistence, $format)->will($this->returnValue($formatMock));
        $im = new ImageManager($pm, 'StdClass', $resizer, $saverManager, $u,$rpm);


        $this->assertEquals($im->format($format,$parent,'image'), 'foo/image-1.jpg');
    }


    public function testFormatManyToOne()
    {
        $path = 'foo/image.jpg';
        $format = 'foo';
        $sourcePath = '/tmp/image.jpg';
        $cached = true;

        $persistence = $this->getMock("ISTI\Image\Persist\EditableInterface");
        $parent = $this->getMock("StdClass",array("getImages"));
        $parent->expects($this->once())->method('getImages')->will($this->returnValue(array(null,$persistence)));

        $ri = new RelationInfo('images',1,$parent,RelationInfo::ManyToOne);
        $formatMock = $this->getMock('ISTI\Image\Model\Format');
        $source = $this->getMock("ISTI\Image\Model\SourceInterface");

        $repo = $this->createRepoMock("foo/image-1.jpg", $persistence, $formatMock);
        $imageinfo = $this->getMock("ISTI\Image\Model\ImageInfoInterface");
        $imageinfo->expects($this->never())->method('getPathForFormat');
        $imageinfo->expects($this->atLeastOnce())->method('getSource')->with()->will($this->returnValue($source));



        $pm = $this->getMockBuilder('ISTI\Image\Persist\PersistenceManager',array('loadFromPersistent','getRepository'))->disableOriginalConstructor()->getMock();
        $pm->expects($this->once())->method('loadFromPersistent')->with($persistence,'StdClass',$ri)->will($this->returnValue($imageinfo));
        $pm->expects($this->once())->method('getRepository')->with($persistence)->will($this->returnValue($repo));

        $u = $this->getMockBuilder("ISTI\Image\Uniquifier",array('uniquify'))->disableOriginalConstructor()->getMock();
        $u->expects($this->never())->method('uniquify')->with($path,$persistence)->will($this->returnValue('foo/image-1.jpg'));

        $saverManager = $this->createSaverManager('foo/image-1.jpg', $source, $cached , $sourcePath);
        $resizer = $this->createResizer($imageinfo,$formatMock,$sourcePath, $cached);

        $rpm = $this->getMock("ISTI\Image\Relation\RelationProviderManager", array("getFormatFor"));
        $rpm->expects($this->once())->method("getFormatFor")->with($ri, $persistence, $format)->will($this->returnValue($formatMock));
        $im = new ImageManager($pm, 'StdClass', $resizer, $saverManager, $u,$rpm);


        $this->assertEquals($im->format($format,$parent,'images',1), 'foo/image-1.jpg');
    }
    protected function createRepoMock($get,$persistence, $format, $set = null )
    {
         $methods = array("getActualPathUsed");
        if($set !== null ){
            $methods[] = 'setActualPathUsed';
        }

        $repo = $this->getMock('ISTI\Image\Persist\ImageInfoRepositoryInterface', $methods);

        $repo->expects($this->atLeastOnce())->method('getActualPathUsed')->with($persistence, $format)->will($this->returnValue($get));

        if($set !== null)
        {

            $repo->expects($this->once())->method('setActualPathUsed')->with($set, $persistence, $format);
        }

        return $repo;
    }

    protected function createSaverManager($path, $source, $cached, $filePath)
    {
        $saver = $this->getMockForAbstractClass("ISTI\Image\Saver\SaverInterface",array('cached', 'getFilePathToSource', 'saveResized'));
        $saver->expects($this->once())->method('cached')->with($path)->will($this->returnValue($cached));
        if(!$cached) {
            $saver->expects($this->once())->method('getFilePathToSource')->with($source)->will($this->returnValue($filePath));
            $saver->expects($this->once())->method('saveResized')->with($source, $path, $this->anything());
        } else {
            $saver->expects($this->never())->method('getFilePathToSource');
            $saver->expects($this->never())->method('saveResized');
        }

        $saveManager = $this->getMock('ISTI\Image\Saver\SaverManager',array('getSaver'));
        $saveManager->expects($this->atLeastOnce())->method('getSaver')->with($source)->will($this->returnValue($saver));

        return $saveManager;
    }

    protected function createResizer($image, $format, $fromPath, $cached)
    {
        $resizer = $this->getMock("ISTI\Image\Resizer\ResizerInterface",array('resize'));
        if($cached)
        {
            $resizer->expects($this->never())->method('resize');
        }
        else {
            $resizer->expects($this->once())->method('resize')->with($image,$format,$fromPath,$this->anything() );
        }
        return $resizer;
    }
}