<?php

require_once("Controllers/Mission.php");

class Tests_Unit_MissionTest extends PHPUnit_Framework_TestCase
{
   
    
    public function testGetError()
    {
        $stub = new Mission;
        $stub->errors['foo'][] = "fooBar";
        
        $this->assertSame('', $stub->getError('bar'));
        $this->assertGreaterThan(1, strlen($stub->getError("foo")));
    }
    
    public function testHasErrors()
    {
        $stub = new Mission;
        $this->assertFalse($stub->hasErrors());
        
        $stub->errors['foo'][] = "fooBar";
        $this->assertTrue($stub->hasErrors());
    }
    
    public function testHasError()
    {
        $stub = new Mission;
        $stub->errors['foo'][] = "fooBar";
        $this->assertFalse($stub->hasError("bar"));
        $this->assertTrue($stub->hasError("foo"));
    }
    
    public function testAddErrors()
    {
        $stub = new Mission;
        $stub->errors['foo'][] = "fooBar";
        $new_error = array("bar" => array("foo bar error"));
        $stub->addErrors($new_error);
        
        $result = array("foo" => array("fooBar"), "bar" => array("foo bar error"));
        
        $this->assertSame($result, $stub->errors);
    }
    
    public function testStartWithoutErrors()
    {
        $grid       = new Grid;
        $params     ="foo bar";
        $robot_mock = $this->getMock("Robot", array("drive"));
        $robot_mock->expects($this->once())
                   ->method("drive")
                   ->with($grid);
        
        $mock = $this->getMock("Mission", array("init", "getRobot", "getGrid"));
        
        $mock->expects($this->once())
             ->method("getGrid")
             ->will($this->returnValue($grid));
        
        $mock->expects($this->once())
             ->method("init")
             ->with($params);
        
        $mock->expects($this->once())
             ->method("getRobot")
             ->will($this->returnValue($robot_mock));
        
        $this->assertNull($mock->start($params));
    }
    
    public function testInit()
    {
        $params = array('grid-x' => 1, 'grid-y' => 2, 'position' => 'foo', 'instructions' => 'bar');
                
        
        $grid_mock = $this->getMock("Grid", array("setParams"));
        $grid_mock->expects($this->once())
                  ->method("setParams")
                  ->with(1, 2)
                  ->will($this->returnValue(array('foo')));
        
        $robot_mock = $this->getMock("Robot", array("setParams"));
        $robot_mock->expects($this->once())
                   ->method("setParams")
                   ->with("foo", "bar", $grid_mock)
                ->will($this->returnValue(array('bar')));
        
        $mock = $this->getMock("Mission", array("getGrid", "getRobot", "addErrors"));
        $mock->expects($this->any())
             ->method("getGrid")
             ->will($this->returnValue($grid_mock));
        
        $mock->expects($this->any())
             ->method("getRobot")
             ->will($this->returnValue($robot_mock));
        
        $mock->expects($this->at(1))
             ->method("addErrors")
             ->with($this->equalTo(array('foo')));
        
        $mock->expects($this->at(4))
             ->method("addErrors")
             ->with($this->equalTo(array('bar')));
        
        $this->assertNull($mock->init($params));
    }
    
    public function testGetRobot()
    {
        $stub  = new Mission;
        $this->assertInstanceOf('Robot', $stub->getRobot());
    }
    
    public function testGetGrid()
    {
        $stub  = new Mission;
        $this->assertInstanceOf('Grid', $stub->getGrid());
    }
    
    public function testClear()
    {
        $mission = new Mission();
        $mission->clear();
        $this->assertEmpty($_SESSION['scent']);
    }
}

?>
