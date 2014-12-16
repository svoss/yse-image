<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 02/12/14
 * Time: 19:54
 */
namespace ISTI\Image\Tests\Saver;
use ISTI\Image\Saver\SaverManager;
class SaverManagerTest extends \PHPUnit_Framework_TestCase {
    public function testAddSaver()
    {
        $manager = new SaverManager();
        $mock = $this->getMockForAbstractClass("ISTI\Image\Saver\SaverInterface",
            array("createSource","updateSource","getFilePathToSource","saveResized","cached","emptyCache","delete","getClass"));
        $mock->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue("FooClass"));

        $manager->addSaver($mock);

        $this->assertEquals($mock,$manager->getSaver("FooClass") );
    }

    public function testGetRelationProvider()
    {
        $manager = new SaverManager();
        $object = $this->getMockForAbstractClass("stdClass");

        $mock = $this->getMock("ISTI\Image\Saver\SaverInterface",
            array("createSource","updateSource","getFilePathToSource","saveResized","cached","emptyCache","delete","getClass"));
        $mock->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($object)));
        $manager->addSaver($mock);
        $this->assertEquals($manager->getSaver($object), $mock);
    }
} 