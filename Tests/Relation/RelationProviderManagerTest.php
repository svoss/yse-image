<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 23/11/14
 * Time: 17:00
 */
namespace ISTI\Image\Tests\Relation;
use ISTI\Image\Relation\RelationProviderManager;

class RelationProviderManagerTest extends \PHPUnit_Framework_TestCase {
    public function testAddRelationProvider()
    {
        $manager = new RelationProviderManager();
        $mock = $this->getMockForAbstractClass("ISTI\Image\Relation\RelationProviderInterface",
            array("getParentClass","getAttributes","loadMeta","loadDefaultPaths","loadDefaultCrops","getFormats"));
        $mock->expects($this->atLeastOnce())
            ->method('getParentClass')
            ->will($this->returnValue("FooClass"));

        $manager->addRelationProvider($mock);

        $this->assertEquals($mock,$manager->getRelationProvider("FooClass") );
    }

    public function testGetRelationProvider()
    {
        $manager = new RelationProviderManager();
        $object = $this->getMockForAbstractClass("stdClass");

        $mock = $this->getMock("ISTI\Image\Relation\RelationProviderInterface",
            array("getParentClass","getAttributes","loadMeta","loadDefaultPaths","loadDefaultCrops","getFormats"));
        $mock->expects($this->atLeastOnce())
            ->method('getParentClass')
            ->will($this->returnValue(get_class($object)));
        $manager->addRelationProvider($mock);
        $this->assertEquals($manager->getRelationProvider($object), $mock);
    }
} 