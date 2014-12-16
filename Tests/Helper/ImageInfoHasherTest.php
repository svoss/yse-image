<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 07/12/14
 * Time: 10:55
 */
namespace ISTI\Image\Tests\Helper;

use ISTI\Image\Model\CustomCrop;
use ISTI\Image\Model\Resize;
use ISTI\Image\Helper\ImageInfoHasher;
class ImageInfoHasherTest extends \PHPUnit_Framework_TestCase {
    public function testHash()
    {
        $resize = new Resize();
        $resize->setType('customCrop');

        $crop = new CustomCrop();
        $crop->setHeight(100);
        $crop->setWidth(100);
        $crop->setStartX('4');
        $crop->setStartY('5');
        $resize->setCustumCrop($crop);
        $path = 'foo/image.jpg';

        $source = '123';
        $imageinfo  = $this->getMock("ISTI\Image\Model\ImageInfoInterface");
        $formatMock = $this->getMock("ISTI\Image\Model\Format");
        $sourceMock = $this->getMock("ISTI\Image\Model\SourceInterface");
        $sourceMock->expects($this->once())->method('getId')->will($this->returnValue($source));
        $imageinfo->expects($this->once())->method('getPathForFormat')->with($formatMock)->will($this->returnValue($path));
        $imageinfo->expects($this->once())->method('getCropForFormat')->with($formatMock)->will($this->returnValue($resize));
        $imageinfo->expects($this->once())->method('getSource')->with()->will($this->returnValue($sourceMock));

        $hasher = new ImageInfoHasher();

        $this->assertEquals($hasher->hash($imageinfo, $formatMock),md5($resize->toJSON()."-".$path."-".$source));
    }

} 