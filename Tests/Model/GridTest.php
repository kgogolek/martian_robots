<?php

require_once("Model/Grid.php");

class Tests_Unit_GridTest extends PHPUnit_Framework_TestCase
{
    public function testSetParams()
    {
        $x    = 1;
        $y    = 2;
        $mock = $this->getMock("Grid", array("validate"));
        
        $mock->expects($this->once())
             ->method("validate")
             ->with($this->equalTo($x),
                    $this->equalTo($y))
             ->will($this->returnValue(array()));
        
        $this->assertSame(array(), $mock->setParams($x, $y));
    }
    
    public function testValidateCorrect()
    {
        $stub = new Grid;
        $this->assertSame($stub->validate(3, 4), array());
    }

    
    public function testValidateIncorrect()
    {
        $error1 = array('grid-x' => array('Grid right coordinate has to be greater than 0'));
        $error2 = array('grid-x' => array('Maximum grid coordinate can be 50'));
        $error3 = array('grid-y' => array('Grid upper coordinate has to be greater than 0'));
        $error4 = array('grid-y' => array('Maximum grid coordinate can be 50'));
        
        $stub = new Grid;
        $this->assertSame($stub->validate(0, 4), $error1);
        $this->assertSame($stub->validate(51, 4), $error2);
        $this->assertSame($stub->validate(3, 0), $error3);
        $this->assertSame($stub->validate(3, 51), $error4);
    }
}

?>
