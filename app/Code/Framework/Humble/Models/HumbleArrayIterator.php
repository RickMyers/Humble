<?php
namespace Code\Framework\Humble\Models;
/**
   ______           __                     ___                         
  / ____/_  _______/ /_____  ____ ___     /   |  ______________ ___  __
 / /   / / / / ___/ __/ __ \/ __ `__ \   / /| | / ___/ ___/ __ `/ / / /
/ /___/ /_/ (__  ) /_/ /_/ / / / / / /  / ___ |/ /  / /  / /_/ / /_/ / 
\____/\__,_/____/\__/\____/_/ /_/ /_/  /_/_ |_/_/  /_/   \__,_/\__, /  
                /  _/ /____  _________ _/ /_____  _____       /____/   
                / // __/ _ \/ ___/ __ `/ __/ __ \/ ___/                
              _/ // /_/  __/ /  / /_/ / /_/ /_/ / /                    
             /___/\__/\___/_/   \__,_/\__/\____/_/                     
                                                                       
 * We are extending the internal ArrayIterator class to add a toString method
 * so that when you attempt to print a Unity load result, you get a nice JSON
 * string instead of the PHP array...
 */
class HumbleArrayIterator extends \ArrayIterator {
	
        use \Code\Framework\Humble\Traits\Base;
        
        /**
         * Because OOP
         * 
         * @param mixed $arr <-- don't care
         */
        public function __construct($arr=[]) {
            
	}
        
        /**
         * Actually sets the array that will be managed by ArrayIterator
         * 
         * @param mixed $arr
         * @return $this
         */
        public function set($arr=[]) {
            parent::__construct($arr);
            return $this;
        }
        
        /**
         * Will take the array being managed and convert it to JSON, since in 2017 a law was passed that stated "HENCEFORTH, JSON IT IS!"
         * 
         * @return string
         */
	public function __toString() {
            return json_encode($this->getArrayCopy());
	}
}
