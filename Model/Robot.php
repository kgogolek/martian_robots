<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Robot
{
    private $_grid;
    
    public $x;
    public $y;
    public $direction;
    public $instructions;
    public $lost = false;
    
    public $map_directions = array("N" => 1, "E" => 2, "S" => 3, "W" => 4);
    
    /**
     * Setting the Grid object
     * 
     * @param Grid $grid Grid
     * 
     * @return void
     */
    public function setGrid(Grid $grid) {
        $this->_grid = $grid;
    }
    
    /**
     * Get the grid object
     * 
     * @return Grid
     */
    public function getGrid() {
        return $this->_grid;
    }
    
    /**
     * Set the params for robot
     * 
     * @param string $position     Robot's starting position
     * @param string $instructions Drive instructions
     * @param Grid   $grid         Grid used
     * 
     * @return array
     */
    public function setParams($position, $instructions, $grid) {
        $this->setGrid($grid);
        $errors['position']     = $this->setPosition($position);
        $errors['instructions'] = $this->setInstructions($instructions);
        return $errors;
    }
    
    /**
     * Set Robot's initial position
     * Validates the position, and assigns it to the robot
     * 
     * @param string $position Position input string
     * 
     * @return array
     */
    public function setPosition($position) {
        $errors = $this->validatePosition($position);
        if (empty($errors)) {
            
            $position = $this->getPositionArray($position);
            $this->x  = (int) $position[0];
            $this->y  = (int) $position[1];
            $this->direction = $this->getNumericDirection($position[2]);
        }
        return $errors;
    }
    
    /**
     * Set Instructions for Robot's drive
     * 
     * @param string $instructions Instructions string
     * 
     * @return array
     */
    public function setInstructions($instructions) {
        $errors = $this->validateInstructions($instructions);
        if (empty($errors)) {
            $this->instructions = $instructions;
        }
        return $errors;
    }
    
    /**
     * Validate Robot's instructions
     * The string should only contain following letters:
     * R - for right turn
     * L - for left turn
     * F - for step forward
     * 
     * @return array Errors
     */
    public function validateInstructions($instructions) {
        $instructions = strtoupper($instructions);
        if (strlen($instructions) > 100)
        {
            return array("Instructions are too long, only 100 steps are allowed at one time");
        }
        if (preg_match("/^[RLF]+$/i", $instructions) == 0) {
            return array("Unknown instructions for the robot, please use only R, L, F");
        }
        return array();
    }
    
    /**
     * Drive the robot on Grid
     * 
     * @param Grid $grid
     * 
     * @return void
     */
    public function drive($grid) {
        $count = strlen($this->instructions);
        for ($i = 0; $i < $count; $i++) {
            if($this->lost === false) {
                $position = substr($this->instructions, $i, 1);
                if ($position === "F") {
                    $this->forward($grid);
                } else {
                    $this->turn($position);
                }
            }
        }
    }
    
    /**
     * Move robot forward
     * Instruction moves the robot forward. We're going to check if the robot is
     * lost, and move it.
     * 
     * @param Grid $grid
     */
    public function forward($grid) {
        if(in_array($this->direction, array(2,4))) {
            $this->x = $this->moveForward($this->x, $grid->x);
        } else {
            $this->y = $this->moveForward($this->y, $grid->y);
        }
    }
    
    /**
     * Move the robot forward
     * 
     * @param int $current_position Current position on the grid (either X or Y)
     * @param int $grid_max         Max Grid position on X or Y (depending on direction the robot is facing)
     * 
     * @return void
     */
    public function moveForward($current_position, $grid_max)
    {
        $new = $current_position + $this->getStep();
        if ($new >= 0 && $new <= $grid_max) {
            return $new;
        } else {
            $this->checkIfLost();
            return $current_position;
        }
    }
    
    /**
     * Check if the robot is lost
     * We'll check if there is a scent already on this field, and if not, add new
     * one, and mark robot as lost
     * 
     * @return void
     */
    public function checkIfLost() {
        if ($this->hasScent($this->x, $this->y) === false) {
            $this->lost = true;
            $this->addScent($this->x, $this->y);
        }
    }
    
    /**
     * Get step depending on direction in which the robot is facing.
     * North, and East are +1 directions, where as South and West are a -1 step
     * 
     * @return int
     */
    public function getStep()
    {
        // N, E - goes forward
        if (in_array($this->direction, array(1,2))) {
            return 1;
        }
        // S, W goes towards zeros
        return -1;
    }
    
    /**
     * Turn the robot either left or right, assign correct direction to the
     * robot. Directions have numbers assigned to themthat allow iteration
     * 
     * @param string $turn_direction Direction of turn, either L or R
     * 
     * @return void
     */
    public function turn($turn_direction)
    {
        switch ($turn_direction) {
            case "L":
                if($this->direction > 1) {
                    $this->direction--;
                } else {
                    $this->direction = 4;
                }
                break;
            case "R":
                // checking if we haven't turned around
                if($this->direction < count($this->map_directions)) {
                    $this->direction++;
                } else {
                    $this->direction = 1;
                }
                break;
        }
    }
    
    /**
     * Get number corresponding to the direction
     * 
     * @param string $direction Direction of movement N,S,W,E
     * 
     * @return int
     */
    public function getNumericDirection($direction) {
        if (isset($this->map_directions[$direction])) {
            return $this->map_directions[$direction];
        }
        return false;
    }
    
    /**
     * Validate the position passed
     * 
     * @param string $position
     * 
     * @return string
     */
    public function validatePosition($position) {
        $errors = array();
        $position = $this->getPositionArray($position);
        if (count($position) != 3) {
            return array("Position needs to contain two integers, and the direction the robot will be facing.");
        }
        if ($position[0] < 0) {
            $errors[] = "The x-coordinate of the robot has to be greater than 0";
        }
        if ($position[0] > $this->getGrid()->y) {
            $errors[] = "The x-coordinate of the robot is beyond the grid specified, please rectify";
        }
        if ($position[1] < 0) {
            $errors[] = "The y-coordinate of the robot has to be greater than 0";
        }
        if ($position[1] > $this->getGrid()->x) {
            $errors[] = "The y-coordinate of the robot is beyond the grid specified, please rectify";
        }
        if (!in_array(strtoupper($position[2]), array_keys($this->map_directions))) {
            $errors[] = "Unknown robot direction";
        }
        return $errors;
    }
    
    /**
     * Will transform the position string inputted by the user, into array of values
     * 
     * @param string $position Position string
     * 
     * @return array
     */
    public function getPositionArray($position)
    {
        $position = explode(" ", trim($position));
        return $position;
    }
    
    /**
     * Returns the string representation of the end position of the robot
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->x . " ". $this->y . " " . $this->getDirectionLetter() . " " . $this->getLostString();
    }
    
    /**
     * Returns "LOST" if the robot fell off the grid
     * 
     * @return string
     */
    public function getLostString()
    {
        if ($this->lost === true) {
            return "LOST";
        }
        return "";
    }
            
    /**
     * Get letter symbolizing the direction of the robot
     * 
     * @return string
     */
    public function getDirectionLetter()
    {
        return array_search($this->direction, $this->map_directions);
    }
    
    /**
     * Add scent of position from which the robot fell off already
     * 
     * @param int $x X position
     * @param int $y Y position
     * 
     * @return void
     */
    public function addScent($x, $y)
    {
        $scent = array($x, $y);
        if(isset($_SESSION['scent'])) {
            $array   = $_SESSION['scent'];
        }
        if(!isset($_SESSION['scent']) || !in_array($scent, $_SESSION['scent'])) {
            $array[] = $scent;
            $_SESSION['scent'] = $array;
        }
    }
    
    /**
     * Can robot find any scents in this position
     * 
     * @param int $x
     * @param int $y
     * 
     * @return boolean
     */
    public function hasScent($x, $y)
    {
        if (isset($_SESSION['scent']) && in_array(array($x, $y), $_SESSION['scent'])) {
            return true;
        }
        return false;
    }
}

