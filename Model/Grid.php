<?php

/**
 * Grid Model
 *
 * @name     Grid Model
 * @category Model
 * @package  Mars Expedition
 * @author   Kasia Gogolek <kasia@gogolek.co.uk>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://gogolek.co.uk/mars
 */

/**
 * Grid Model
 *
 * @name     Grid Model
 * @category Model
 * @package  Mars Expedition
 * @author   Kasia Gogolek <kasia@gogolek.co.uk>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     http://gogolek.co.uk/mars
 */
class Grid {
    
    public $x;
    public $y;
    
    /**
     * Set Grid parameters
     * 
     * @param int $x Grid right (X) coordinate
     * @param int $y Grid top (Y) coordinate
     * @return type
     */
    public function setParams($x, $y)
    {
        $errors = $this->validate($x, $y);
        if (empty($errors)) {
            $this->x = (int) $x;
            $this->y = (int) $y;
        }
        return $errors;
    }
    
    /**
     * Validating Grid parameters.
     * All of the grid coordinates have to be greater to 0 and less or equal to 50
     * 
     * @param int $x Grid right (X) coordinate
     * @param int $y Grid top (Y) coordinate
     * 
     * @return array
     */
    public function validate($x, $y) {
        $errors = array();
        if ($x < 1) {
            $errors['grid-x'][] = "Grid right coordinate has to be greater than 0";
        }
        if ($x > 50) {
            $errors['grid-x'][] = "Maximum grid coordinate can be 50";
        }
        
        if ($y < 1) {
            $errors['grid-y'][] = "Grid upper coordinate has to be greater than 0";
        }
        if ($y > 50) {
            $errors['grid-y'][] = "Maximum grid coordinate can be 50";
        }
        return $errors;
    }
}

