<?php

/**
 * Mission Controller
 * Manages all of the mission actions, allows the mission directives to be cleared
 * Set up a new grid, and drive the robot
 *
 * @name     Mission Controller
 * @category Controller
 * @package  Mars Expedition
 * @author   Kasia Gogolek <kasia@gogolek.co.uk>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://gogolek.co.uk/mars
 */

require_once("Model/Grid.php");
require_once("Model/Robot.php");

/**
 * Mission Controller
 * Manages all of the mission actions, allows the mission directives to be cleared
 * Set up a new grid, and drive the robot
 *
 * @name     Mission Controller
 * @category Controller
 * @package  Mars Expedition
 * @author   Kasia Gogolek <kasia@gogolek.co.uk>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://gogolek.co.uk/mars
 */

class Mission {
 
    /**
     * Grid object
     * @var Grid
     */
    private $_grid;
    /**
     * Robot object
     * @var Robot
     */
    private $_robot;
    public $errors = array();
    
    /**
     * Get the Grid object
     * 
     * @return Grid
     */
    public function getGrid() {
        if ($this->_grid === null) {
            $this->_grid = new Grid;
        }
        return $this->_grid;
    }
    
    /**
     * Get the Robot Object
     * 
     * @return Robot
     */
    public function getRobot() {
        if ($this->_robot === null) {
            $this->_robot = new Robot;
        }
        return $this->_robot;
    }
    
    /**
     * Initialize the Grid and the Robot
     * 
     * @param array $params Params passed through POST
     * 
     * @return void
     */
    public function init($params) {
        $errors = $this->getGrid()->setParams((int) $params['grid-x'], (int) $params['grid-y']);
        $this->addErrors($errors);
        $errors = $this->getRobot()->setParams($params['position'], $params['instructions'], $this->getGrid());
        $this->addErrors($errors);
    }
    
    /**
     * Start the robot's mission
     * 
     * @param array $params Params passed through POST
     * 
     * @return void
     */
    public function start($params) {
        $this->init($params);
        if(empty($this->errors)) {
            $this->getRobot()->drive($this->getGrid());
        }
    }
    
    /**
     * Adding errors from validation
     * 
     * @param array $errors Array of errors returned by validation
     * 
     * @return void
     */
    public function addErrors($errors) {
        $this->errors = array_merge($this->errors, $errors);
        $this->errors = array_filter($this->errors);
    }
    
    /**
     * Checking if an error has been returned for the input with a certain name
     * 
     * @param string $name Name of the input field for which we're checking the errors
     * 
     * @return boolean
     */
    public function hasError($name) {
        return isset($this->errors[$name]);
    }
    
    /**
     * Has any of the validations returned errors?
     * 
     * @return boolean
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Get error string for the view display
     * 
     * @param string $name Name of the input field for which we're checking the errors
     * 
     * @return string
     */
    public function getError($name) {
        if(isset($this->errors[$name])) {
            $error = implode("<br/>", $this->errors[$name]);
            return '<span class="help-inline">' . $error ."</span>";
        }
        return '';
    }
    
    /**
     * Clear the scent session and redirect to the GET request
     * 
     * @return void;
     */
    public function clear()
    {
        unset($_SESSION['scent']);
        header("Location: /");
    }
   
    
}
