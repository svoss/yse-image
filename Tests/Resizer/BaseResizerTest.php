<?php
/**
 * Its recommended to install Imagick before running this test
 * Imagick enables image comparison
 */


namespace ISTI\Image\Tests\Resizer;
use ISTI\Image\Model\CustomCrop;
use ISTI\Image\Model\Format;
use ISTI\Image\Model\Resize;
use ISTI\Image\Resizer\ResizerInterface;

abstract class BaseResizerTest extends \PHPUnit_Framework_TestCase {
    public function testPreconditions()
    {
        $source = __DIR__."/images/nightwatch.jpg";
        $zoom = __DIR__."/images/nightwatch-zoom-100x100.jpg";
        $custom = __DIR__."/images/nightwatch-custom-100x100.jpg";

        $this->assertFileExists($source);
        $this->assertFileExists($zoom);
        $this->assertFileExists($custom);
    }
    public function forAllTypes(ResizerInterface $resizer){
        $source = __DIR__."/images/nightwatch.jpg";
        $zoom = __DIR__."/images/nightwatch-zoom-100x100.jpg";
        $custom = __DIR__."/images/nightwatch-custom-100x100.jpg";
        $cropTo = __DIR__."/images/100x100.jpg";
        //zoom crop
        $format = new Format();
        $format->setHeight(100);
        $format->setWidth(100);
        $format->setName('foo');

        $resize = new Resize();
        $resize->setType('zoom');
        $mock = $this->createImageInfoMock($resize, $format);
        $resizer->resize($mock, $format,$source, $cropTo);
        $this->assertImages($zoom,$cropTo);

        //custom:
        $resize = new Resize();
        $crop = new CustomCrop();
        $crop->setStartX(500);
        $crop->setStartY(500);
        $crop->setWidth(200);
        $crop->setHeight(200);
        $resize->setType('custom');
        $resize->setCustumCrop($crop);

        $mock = $this->createImageInfoMock($resize,$format);
        $resizer->resize($mock, $format, $source, $cropTo);
        $this->assertImages($custom, $cropTo);

        unlink($cropTo);

    }

    public function assertImages($image1, $image2)
    {
        $size1 = getimagesize($image1);
        $size2 = getimagesize($image2);
        $this->assertEquals($size1[0], $size2[0],"widths of images are not equal");
        $this->assertEquals($size1[1], $size2[1],"heights of images are not equal");
        if(class_exists('\Imagick'))
        {
            $image1 = new \Imagick($image1);
            $image2 = new \Imagick($image2);
            $this->assertTrue($image1->compareImages($image2,\Imagick::METRIC_ROOTMEANSQUAREDERROR)[1] < 0.1,"images not the same");
        }
    }

    protected function createImageInfoMock(Resize $resize, Format $format)
    {
        $imagemock = $this->getMock("ISTI\Image\Model\ImageInfoInterface");
        $imagemock
            ->expects($this->atLeastOnce())
            ->method('getCropForFormat')
            ->with($format)
            ->will($this->returnValue($resize));
        return $imagemock;
    }
} 