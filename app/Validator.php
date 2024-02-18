<?php
/**
 _    __      ___     __      __            
| |  / /___ _/ (_)___/ /___ _/ /_____  _____
| | / / __ `/ / / __  / __ `/ __/ __ \/ ___/
| |/ / /_/ / / / /_/ / /_/ / /_/ /_/ / /    
|___/\__,_/_/_/\__,_/\__,_/\__/\____/_/     
                                            

A helper class for data validation used from within controllers.

 */
class Validator {
    
    /**
     * Returns whether the value is in the specific range
     * 
     * @param mixed $value
     * @param string $range
     * @return bool
     */
    public static function range($value,$range) : bool {
        $result = false;
        $parts  = explode('..',$range);
        if (($parts[0] ?? false) && ($parts[1] ?? false)) {
            if (!(is_numeric($value) && is_numeric($parts[0]) && is_numeric($parts[1]))) {
                $parts[0] = ord($parts[0]);
                $parts[1] = ord($parts[1]);
                $value    = ord($value);
            }
            $result = ($value >= $parts[0]) && ($value <= $parts[1]);
        }
        return $result;
    }
    
    /**
     * Returns whether a value is greater than or equal to another value
     * 
     * @param mixed $value
     * @param number $min
     * @return bool
     */
    public static function min($value,$min) : bool {
        $result = false;
        if (is_numeric($value) && is_numeric($min)) {
            $result = $value >= $min;
        }
        return $result;
    }
    
    /**
     * Returns whether a value is less than or equal to another value
     * 
     * @param mixed $value
     * @param number $max
     * @return bool
     */
    public static function max($value,$max) : bool {
        $result = false;
        if (is_numeric($value) && is_numeric($max)) {
            $result = $value <= $max;
        }
        return $result;
    }
    
}
