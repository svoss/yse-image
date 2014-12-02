<?php
/**
 * Created by PhpStorm.
 * User: stijnvoss
 * Date: 27/11/14
 * Time: 17:05
 */

namespace ISTI\Image\Tests\Persist;

use ISTI\Image\Persist\PersistenceManager;
use ISTI\Image\Persist\EditableInterface;
use ISTI\Image\Relation\RelationProviderManager;
class PersistenceManagerTest  extends \PHPUnit_Framework_TestCase {
    public function  testLoadDefaultToPersistent()
    {
        $mock = $this->getMockForAbstractClass("ISTI\Image\Persist\EditableInterface",
                array('setTitle','setAlt','setLongDescription','setGeolocation','setCrops','setPaths', 'getParentObject')
            );

        $parentObject = $this->getMock('StdClass');

        $methods = array(
            'title' => 'foo_title',
            'alt' => 'foo_alt',
            'longDescription' => 'foo_longdescription',
            'geolocation' => "foo_geolocation",
            'crops' => array("foo_format" => null , "foo_format2" => array(0,0,400,400)),
            'paths' => array("foo_format" => 'bla/bla.jpg', "foo_format2" => 'bla/bla2.jpg'),

        );
        $mock->expects($this->atLeastOnce())->method('getParentObject')->will($this->returnValue($parentObject));


        foreach($methods as $property => $value) {
            $mock->expects($this->once())->method('set'.ucfirst($property))->with($value);

        }
        $relationinfomock = $this->getMockBuilder('ISTI\Image\Relation\RelationInfo')->disableOriginalConstructor()->getMock();
        $mockRelationProvider = $this->getMockForAbstractClass("ISTI\Image\Relation\RelationProviderInterface",
            array('getAttributes','loadDefaultPaths','loadDefaultCrops')
        );
        $mockRelationProvider->expects($this->once())->method('getAttributes')->with($mock, $relationinfomock)->will($this->returnValue(
            array(
                'title' => 'foo_title',
                'alt' => 'foo_alt',
                'longdescription' => 'foo_longdescription',
                'geo' => "foo_geolocation",
            )
        ));
        $mockRelationProvider->expects($this->once())->method('loadDefaultCrops')->with($mock, $relationinfomock)->will($this->returnValue(
           array("foo_format" => null , "foo_format2" => array(0,0,400,400))
        ));

        $mockRelationProvider->expects($this->once())->method('loadDefaultPaths')->with($mock, $relationinfomock)->will($this->returnValue(
            array("foo_format" => 'bla/bla.jpg', "foo_format2" => 'bla/bla2.jpg')));

        $manager = $this->getMock('ISTI\Image\Factory\ImageInfoFactoryManager');
        $rm = $this->relationManagerMock($mockRelationProvider, $parentObject);
        $pm = new PersistenceManager($rm,$manager);
        $pm->loadDefaultToPersistent($mock, $relationinfomock);
    }

    public function testLoadFromPersistent(){

        $parentObject = $this->getMock('StdClass');
        $source = $this->getMock('ISTI\Image\Model\SourceInterface');
        $relationinfomock = $this->getMockBuilder('ISTI\Image\Relation\RelationInfo')->disableOriginalConstructor()->getMock();
        $mock = $this->getMockForAbstractClass("ISTI\Image\Persist\UnEditableInterface",
            array('getSource','getParentObject')
        );

        $mock->expects($this->once())->method('getSource')->will($this->returnValue($source));
        $mock->expects($this->atLeastOnce())->method('getParentObject')->will($this->returnValue($parentObject));
        //Uneditable:
        $mockRelationProvider = $this->getMockForAbstractClass("ISTI\Image\Relation\RelationProviderInterface",
            array('getAttributes','loadDefaultPaths','loadDefaultCrops')
        );
        $mockRelationProvider->expects($this->once())->method('getAttributes')->with($mock, $relationinfomock)->will($this->returnValue(
            array(
                'title' => 'foo_title',
                'alt' => 'foo_alt',
                'longdescription' => 'foo_longdescription',
                'geo' => "foo_geolocation",
            )
        ));
        $mockRelationProvider->expects($this->once())->method('loadDefaultCrops')->with($mock, $relationinfomock)->will($this->returnValue(
            array("foo_format" => null , "foo_format2" => array(0,0,400,400))
        ));

        $mockRelationProvider->expects($this->once())->method('loadDefaultPaths')->with($mock, $relationinfomock)->will($this->returnValue(
            array("foo_format" => 'bla/bla.jpg', "foo_format2" => 'bla/bla2.jpg')));


        $rm = $this->relationManagerMock($mockRelationProvider, $parentObject);


        $manager = $this->imageinfoCreate('foo_title','foo_alt','foo_longdescription','foo_geolocation',$parentObject, array("foo_format" => null , "foo_format2" => array(0,0,400,400)), array("foo_format" => 'bla/bla.jpg', "foo_format2" => 'bla/bla2.jpg'),$source,'ImageInfoFoo');
        $pm = new PersistenceManager($rm,$manager);

        $pm->loadFromPersistent($mock,'ImageInfoFoo',$relationinfomock);

    }

    public function testLoadFromPersistent2()
    {
        $parentObject = $this->getMock('StdClass');
        $source = $this->getMock('ISTI\Image\Model\SourceInterface');
        $relationinfomock = $this->getMockBuilder('ISTI\Image\Relation\RelationInfo')->disableOriginalConstructor()->getMock();
        $mock = $this->getMockForAbstractClass("ISTI\Image\Persist\EditableInterface",
            array('getSource','getParentObject','getTitle','getAlt','getLongDescription','getGeolocation','getCrops','getPaths')
        );


        $methods = array(
            'title' => 'foo_title',
            'alt' => 'foo_alt',
            'longDescription' => 'foo_longdescription',
            'geolocation' => "foo_geolocation",
            'crops' => array("foo_format" => null , "foo_format2" => array(0,0,400,400)),
            'paths' => array("foo_format" => 'bla/bla.jpg', "foo_format2" => 'bla/bla2.jpg'),

        );
        foreach($methods as $property => $value) {
            $mock->expects($this->once())->method('get'.ucfirst($property))->will($this->returnValue($value));

        }


        $mock->expects($this->once())->method('getSource')->will($this->returnValue($source));
        $mock->expects($this->atLeastOnce())->method('getParentObject')->will($this->returnValue($parentObject));
        //Uneditable:
        $mockRelationProvider = $this->getMockForAbstractClass("ISTI\Image\Relation\RelationProviderInterface"
        );

        $rm = $this->getMock('ISTI\Image\Relation\RelationProviderManager', array('getRelationProvider'));
        $rm->expects($this->never())->method('getRelationProvider');



        $manager = $this->imageinfoCreate('foo_title','foo_alt','foo_longdescription','foo_geolocation',$parentObject, array("foo_format" => null , "foo_format2" => array(0,0,400,400)), array("foo_format" => 'bla/bla.jpg', "foo_format2" => 'bla/bla2.jpg'),$source,'ImageInfoFoo');
        $pm = new PersistenceManager($rm,$manager);

        $pm->loadFromPersistent($mock,'ImageInfoFoo',$relationinfomock);
    }
    protected function imageinfoCreate($title, $alt, $long, $geo, $parent, $crops, $paths, $source, $class)
    {
        $imageinfofactory = $this->getMockForAbstractClass('ISTI\Image\Factory\ImageInfoFactoryInterface',array('createInstance'));
        $imageinfofactory->expects($this->once())->method('createInstance')->with($title, $alt, $long, $geo, get_class($parent), $crops, $paths, $source);

        $manager = $this->getMock('ISTI\Image\Factory\ImageInfoFactoryManager');
        $manager->expects($this->once())->method('getFactory')->with($class)->will($this->returnValue($imageinfofactory));
        return $manager;
    }

    protected function relationManagerMock($mock, $parentObject)
    {
        $rmm = $this->getMock('ISTI\Image\Relation\RelationProviderManager', array('getRelationProvider'));
        $rmm->expects($this->once())->method('getRelationProvider')->with($parentObject)->will($this->returnValue($mock));
        return $rmm;
    }

    public function testAddRepository()
    {
        $this->getMockForAbstractClass('ISTI\Image\Factory\ImageInfoFactoryInterface',array('createInstance'));
        $manager = $this->getMock('ISTI\Image\Factory\ImageInfoFactoryManager');
        $rmm = $this->getMock('ISTI\Image\Relation\RelationProviderManager');
        $pm = new PersistenceManager($rmm,$manager);

        $mock = $this->getMockForAbstractClass("ISTI\Image\Persist\ImageinfoRepositoryInterface",
            array("getClass", "similarPaths"));
        $mock->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue("FooClass"));

        $pm->addRepository($mock);

        $this->assertEquals($mock,$pm->getRepository("FooClass") );
    }

    public function testGetRepository()
    {
        $this->getMockForAbstractClass('ISTI\Image\Factory\ImageInfoFactoryInterface',array('createInstance'));
        $manager = $this->getMock('ISTI\Image\Factory\ImageInfoFactoryManager');
        $rmm = $this->getMock('ISTI\Image\Relation\RelationProviderManager');
        $pm = new PersistenceManager($rmm,$manager);

        $object = $this->getMockForAbstractClass("stdClass");

        $mock = $this->getMockForAbstractClass("ISTI\Image\Persist\ImageinfoRepositoryInterface",
            array("getClass", "similarPaths"));
        $mock->expects($this->atLeastOnce())
            ->method('getClass')
            ->will($this->returnValue(get_class($object)));
        $pm->addRepository($mock);
        $this->assertEquals($pm->getRepository($object), $mock);
    }
} 