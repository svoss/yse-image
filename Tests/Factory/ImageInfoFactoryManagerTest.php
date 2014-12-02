<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 02/12/14
 * Time: 20:06
 */
namespace ISTI\Image\Tests\Factory;

use ISTI\Image\Factory\ImageInfoFactoryInterface;
use ISTI\Image\Factory\ImageInfoFactoryManager;
class ImageInfoFactoryManagerTest  extends \PHPUnit_Framework_TestCase {
    public function testAddInfoFactory()
    {
        $manager = new ImageInfoFactoryManager();
        $mock = $this->getMockForAbstractClass("ISTI\Image\Factory\ImageInfoFactoryInterface",
            array("createInstance","getClass"));
        $mock->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue("FooClass"));

        $manager->addFactory($mock);

        $this->assertEquals($mock,$manager->getFactory("FooClass") );
    }

    public function testGetFactory()
    {
        $manager = new ImageInfoFactoryManager();
        $object = $this->getMockForAbstractClass("stdClass");

        $mock = $this->getMock("ISTI\Image\Factory\ImageInfoFactoryInterface",
            array("getClass","createInstance"));
        $mock->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($object)));
        $manager->addFactory($mock);
        $this->assertEquals($manager->getFactory($object), $mock);
    }
}