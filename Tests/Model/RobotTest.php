<?php

require_once("Model/Robot.php");

class Tests_Unit_RobotTest extends PHPUnit_Framework_TestCase
{
    public function testMapDirections()
    {
        $stub = new Robot;
        $this->assertSame($stub->map_directions, array("N" => 1, "E" => 2, "S" => 3, "W" => 4));
    }
    
    public function testSetGetGrid()
    {
        $grid  = new Grid;
        $robot = new Robot;
        
        $robot->setGrid($grid);
        $this->assertSame($grid, $robot->getGrid());
    }
    
    public function testSetParams()
    {
        $grid     = new Grid;
        $position = "1 1 E";
        $instructions = "RFRFRFRF";
        $mock     = $this->getMock("Robot", array('setPosition', 'setInstructions', 'setGrid'));
        $result   = array("position" => array(1), "instructions" => array(2));
        
        $mock->expects($this->once())
             ->method("setGrid")
             ->with($this->equalTo($grid));
        
        $mock->expects($this->once())
             ->method("setPosition")
             ->with($this->equalTo($position))
             ->will($this->returnValue(array(1)));
        
        $mock->expects($this->once())
             ->method("setInstructions")
             ->with($this->equalTo($instructions))
             ->will($this->returnValue(array(2)));
        
        $this->assertSame($result, $mock->setParams($position, $instructions, $grid));
    }
    
    public function testSetPosition()
    {
        $position = "1 4 E";
        $position_array = array(0 => 1, 1 => 4, 2 => "E");
        $mock     = $this->getMock("Robot", array('validatePosition', 'getPositionArray', 'getNumericDirection'));
        
        $mock->expects($this->once())
             ->method('validatePosition')
             ->with($this->equalTo($position))
             ->will($this->returnValue(array()));
                
        $mock->expects($this->once())
             ->method("getPositionArray")
             ->with($this->equalTo($position))
             ->will($this->returnValue($position_array));
             
        $mock->expects($this->once())
             ->method("getNumericDirection")
             ->with($this->equalTo("E"))
             ->will($this->returnValue(2));
        
        $this->assertSame(array(), $mock->setPosition($position));     
        
        $this->assertSame(1, $mock->x);
        $this->assertSame(4, $mock->y);
        $this->assertSame(2, $mock->direction);
    }
    
    public function testSetPositionDoesntValidate()
    {
        $position = "1 4 E";
        $position_array = array(0 => 1, 1 => 4, 2 => "E");
        $mock     = $this->getMock("Robot", array('validatePosition', 'getPositionArray', 'getNumericDirection'));
        
        $mock->expects($this->once())
             ->method('validatePosition')
             ->with($this->equalTo($position))
             ->will($this->returnValue(array(0 => "error")));
                
        $mock->expects($this->never())
             ->method("getPositionArray");
             
        $mock->expects($this->never())
             ->method("getNumericDirection");
        
        $this->assertSame(array(0 => "error"), $mock->setPosition($position));     
        
        $this->assertNull($mock->x);
        $this->assertNull($mock->y);
        $this->assertNull($mock->direction);
    }
    
    public function testSetInstructions()
    {
        $instructions = "RFRFRFRF";
        $mock         = $this->getMock("Robot", array('validateInstructions'));
        
        $mock->expects($this->once())
             ->method("validateInstructions")
             ->with($this->equalTo($instructions))
             ->will($this->returnValue(array()));
        
        $this->assertSame(array(), $mock->setInstructions($instructions));
        $this->assertSame($mock->instructions, $instructions);
    }
    
    public function testSetInstructionsDoesntValidate()
    {
        $instructions = "RFRFRFRF";
        $mock         = $this->getMock("Robot", array('validateInstructions'));
        
        $mock->expects($this->once())
             ->method("validateInstructions")
             ->with($this->equalTo($instructions))
             ->will($this->returnValue(array(0 => "error")));
        
        $this->assertSame(array(0 => "error"), $mock->setInstructions($instructions));
        $this->assertNull($mock->instructions);
    }
    
    public function testValidateInstructions()
    {
        $stub = new Robot;
        $long_instructions = "XWRXIJNQAJLGMDDLJTEALJPDQRFGQFWBHFZLOSWAVQATUXQJJXULBZLZWAZCVNKAOWEXXKUFUKVNDDEDEBKZRDRNYCOUEWKJLLNUP";
        $incorrect_instructions = "RET";
        $correct_instructions   = "RFRFRFRF";
        
        $this->assertSame(array("Instructions are too long, only 100 steps are allowed at one time") , $stub->validateInstructions($long_instructions));
        $this->assertSame(array("Unknown instructions for the robot, please use only R, L, F") , $stub->validateInstructions($incorrect_instructions));
        $this->assertSame(array() , $stub->validateInstructions($correct_instructions));
    }
    
    public function testDrive()
    {
        $grid = new Grid;
        $mock = $this->getMock("Robot", array('forward', 'turn'));
        $mock->instructions = "RFLF";
        
        $mock->expects($this->at(0))
             ->method("turn")
             ->with("R");
        
        $mock->expects($this->at(1))
             ->method("forward")
             ->with($grid);
        
        $mock->expects($this->at(2))
             ->method("turn")
             ->with("L");
        
        $mock->expects($this->at(3))
             ->method("forward")
             ->with($grid);
        
        $this->assertNull($mock->drive($grid));
    }
    
    public function testForwardEast()
    {
        $mock = $this->getMock("Robot", array("moveForward"));
        $mock->x = 1;
        $mock->y = 2;
        
        $grid = new Grid;
        $grid->x = 3;
        $grid->y = 4;
        
        $mock->direction = 2;
        
        $mock->expects($this->once())
             ->method("moveForward")
             ->with($this->equalTo(1), $this->equalTo(3));
        
        $this->assertNull($mock->forward($grid));
    }
    
    public function testForwardWest()
    {
        $mock = $this->getMock("Robot", array("moveForward"));
        $mock->x = 1;
        $mock->y = 2;
        
        $grid = new Grid;
        $grid->x = 3;
        $grid->y = 4;
        
        $mock->direction = 4;
        
        $mock->expects($this->once())
             ->method("moveForward")
             ->with($this->equalTo(1), $this->equalTo(3));
        
        $this->assertNull($mock->forward($grid));
    }
    
    public function testForwardNorth()
    {
        $mock = $this->getMock("Robot", array("moveForward"));
        $mock->x = 1;
        $mock->y = 2;
        
        $grid = new Grid;
        $grid->x = 3;
        $grid->y = 4;
        
        $mock->direction = 1;
        
        $mock->expects($this->once())
             ->method("moveForward")
             ->with($this->equalTo(2), $this->equalTo(4));
        
        $this->assertNull($mock->forward($grid));
    }
    
    public function testForwardSouth()
    {
        $mock = $this->getMock("Robot", array("moveForward"));
        $mock->x = 1;
        $mock->y = 2;
        
        $grid = new Grid;
        $grid->x = 3;
        $grid->y = 4;
        
        $mock->direction = 3;
        
        $mock->expects($this->once())
             ->method("moveForward")
             ->with($this->equalTo(2), $this->equalTo(4));
        
        $this->assertNull($mock->forward($grid));
    }
    
    public function testMoveForward()
    {
        $current = 1;
        $mock    = $this->getMock("Robot", array("checkIfLost", "getStep"));
        
        $mock->expects($this->once())
             ->method("getStep")
             ->will($this->returnValue(1));
        
        $this->assertSame(2, $mock->moveForward($current, 3));
        
    }
    
    public function testMoveForwardOutOfGrid()
    {
        $current = 0;
        $mock    = $this->getMock("Robot", array("checkIfLost", "getStep"));
        
        $mock->expects($this->once())
             ->method("getStep")
             ->will($this->returnValue(-1));
        
        $mock->expects($this->once())
             ->method("checkIfLost");
        
        $this->assertSame($current, $mock->moveForward($current, 3));
    }
    
    public function testCheckIfLostHasScent()
    {
        $mock    = $this->getMock("Robot", array("hasScent", "addScent"));
        $mock->x = 1;
        $mock->y = 2;
        
        $mock->expects($this->once())
             ->method("hasScent")
             ->with($this->equalTo(1), $this->equalTo(2))
             ->will($this->returnValue(true));
        
        $mock->expects($this->never())
             ->method("addScent");
        
        $this->assertNull($mock->checkIfLost());
    }
    
    public function testCheckIfLostNoScent()
    {
        $mock    = $this->getMock("Robot", array("hasScent", "addScent"));
        $mock->x = 1;
        $mock->y = 2;
        
        $mock->expects($this->once())
             ->method("hasScent")
             ->with($this->equalTo(1), $this->equalTo(2))
             ->will($this->returnValue(false));
        
        $mock->expects($this->once())
             ->method("addScent")
             ->with($this->equalTo(1), $this->equalTo(2));
        
        $this->assertNull($mock->checkIfLost());
        $this->assertTrue($mock->lost);
    }
    
    public function testGetStep()
    {
        $stub = new Robot;
        
        $stub->direction = 1;
        $this->assertSame(1, $stub->getStep());
        
        $stub->direction = 2;
        $this->assertSame(1, $stub->getStep());
        
        $stub->direction = 3;
        $this->assertSame(-1, $stub->getStep());
        
        $stub->direction = 4;
        $this->assertSame(-1, $stub->getStep());
    }
    
    public function testTurn()
    {
        $stub = new Robot;
        
        $stub->direction = 1;
        $stub->turn("L");
        $this->assertSame(4, $stub->direction);
        
        $stub->direction = 2;
        $stub->turn("L");
        $this->assertSame(1, $stub->direction);
        
        $stub->direction = 2;
        $stub->turn("R");
        $this->assertSame(3, $stub->direction);
        
        $stub->direction = 4;
        $stub->turn("R");
        $this->assertSame(1, $stub->direction);
    }
    
    public function testValidatePositionNotEnoughParams()
    {
        $mock     = $this->getMock("Robot", array('getPositionArray', 'getGrid'));
        $position = "1 E";
        
        $mock->expects($this->once())
             ->method("getPositionArray")
             ->with($this->equalTo($position))
             ->will($this->returnValue(array(1, "E")));
        
        $result = $mock->validatePosition($position);
        $this->assertSame(1, count($result));
    }
    
    public function testValidatePositionIncorrectLessThan0()
    {
        $grid     = new Grid;
        $grid->x  = 5;
        $grid->y  = 7;
        $mock     = $this->getMock("Robot", array('getPositionArray', 'getGrid'));
        $position = "-1 -1 E";
        
        $mock->expects($this->once())
             ->method("getPositionArray")
             ->with($this->equalTo($position))
             ->will($this->returnValue(array(-1, -1, "R")));
        
        $mock->expects($this->any())
             ->method("getGrid")
             ->will($this->returnValue($grid));
             
        $this->assertSame(3, count($mock->validatePosition($position)));
    }
    
    public function testValidatePositionCorrect()
    {
        $grid     = new Grid;
        $grid->x  = 5;
        $grid->y  = 7;
        $mock     = $this->getMock("Robot", array('getPositionArray', 'getGrid'));
        $position = "1 2 E";
        
        $mock->expects($this->once())
             ->method("getPositionArray")
             ->with($this->equalTo($position))
             ->will($this->returnValue(array(1, 2, "E")));
        
        $mock->expects($this->any())
             ->method("getGrid")
             ->will($this->returnValue($grid));
             
        $this->assertSame(array(), $mock->validatePosition($position));
    }
    
    public function testValidatePositionIncorrectOutsideGrid()
    {
        $grid     = new Grid;
        $grid->x  = 1;
        $grid->y  = 1;
        $mock     = $this->getMock("Robot", array('getPositionArray', 'getGrid'));
        $position = "4 4 E";
        
        $mock->expects($this->once())
             ->method("getPositionArray")
             ->with($this->equalTo($position))
             ->will($this->returnValue(array(4, 4, "E")));
        
        $mock->expects($this->any())
             ->method("getGrid")
             ->will($this->returnValue($grid));
             
        $this->assertSame(2, count($mock->validatePosition($position)));
    }
    
    public function testGetPositionArray()
    {
        $stub   = new Robot;
        $result = array('1', '1', "E");
        $this->assertSame($result, $stub->getPositionArray("1 1 E"));
    }
    
    public function testToString()
    {
        $mock = $this->getMock("Robot", array("getDirectionLetter", "getLostString"));
        $mock->x = 1;
        $mock->y = 2;
        
        $mock->expects($this->once())
             ->method("getDirectionLetter")
             ->will($this->returnValue("W"));
        
        $mock->expects($this->once())
             ->method("getLostString")
             ->will($this->returnValue("LOST"));
        
        $string = "1 2 W LOST";
        
        $this->expectOutputString($string);
        echo $mock;
    }
    
    public function testGetLostString()
    {
        $stub = new Robot;
        $this->assertSame("", $stub->getLostString());
        
        $stub->lost = true;
        $this->assertSame("LOST", $stub->getLostString());
    }
    
    public function testGetNumericDirection()
    {
        $stub = new Robot;
        $this->assertFalse($stub->getNumericDirection("R"));
        $this->assertSame(1, $stub->getNumericDirection("N"));
    }
    
    public function testGetDirectionLetter()
    {
        $stub = new Robot;
        $stub->direction = "9";
        
        $this->assertFalse($stub->getDirectionLetter());
        
        $stub->direction = "4";
        $this->assertSame(W, $stub->getDirectionLetter());
    }
    
    public function testAddAnotherScent()
    {
        $_SESSION['scent'] = array(0 => array(1, 3));
        $stub = new Robot;
        $stub->addScent(4,5);
        
        $this->assertSame(array(0 => array(1,3), 1 => array(4,5)), $_SESSION['scent']);
        unset($_SESSION['scent']);
    }
    
    public function testAddScent()
    {
        $x = 1;
        $y = 2;
        $stub = new Robot;
        
        $stub->addScent($x, $y);
        $this->assertTrue(in_array(array(1, 2), $_SESSION['scent']));
    }
    
    public function testHasScent()
    {
        $stub = new Robot;
        
        $this->assertFalse($stub->hasScent(4,5));
        $this->assertTrue($stub->hasScent(1,2));
    }
}

?>
