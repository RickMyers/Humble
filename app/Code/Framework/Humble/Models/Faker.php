<?php
namespace Code\Framework\Humble\Models;
use Humble;
use Log;
use Environment;
/**
 *
 * Fake Data Generator
 *
 * A class that will be used to generate fake addresses, names, SSn,
 * etc...
 *
 * PHP version 7.0+
 *
 * @category   Logical Model
 * @package    Framework
 * @author     Rick <rick@humbleprogramming.com>
 */
class Faker extends Model
{
    private $primed  = false;
    private $city    = false;
    private $genders = [
        'M','F'
    ];
    private $data    = [
        "names" => [
            "first" => [],
            "last"  => []
        ],
        "locations" => [],
        "states"    => [],
        "streets"   => []
    ];
    private $first_name = '';
    private $last_name  = '';
    private $gender     = '';
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Required for Helpers, Models, and Events, but not Entities
     *
     * @return system
     */
    public function getClassName() {
        return __CLASS__;
    }

    protected function prime() {
        $source     = Environment::getRoot('humble').'/lib/fake/data';
        $cities     = $source.'/addresses/city_list.json';
        $streets    = $source.'/addresses/streets.json';
        $names      = [
            'F'     => $source.'/names/F.json',
            'M'     => $source.'/names/M.json'
        ];
        $surnames   = $source.'/names/S.json';
        $this->data['streets'] = json_decode(file_get_contents($streets),true);
        foreach (json_decode(file_get_contents($cities),true) as $city) {
            $this->data['locations'][$city['state']] = isset($this->data['locations'][$city['state']]) ? $this->data['locations'][$city['state']] : [];
            $this->data['locations'][$city['state']][] = $city;
        }
        $xref = [];
        foreach ($this->data['locations'] as $state => $list) {
            if (!isset($xref[$state])) {
                $this->data['states'][] = $state;
            }
        }
        foreach ($names as $gender => $source) {
            $this->data['names']['first'][$gender] = [];
            foreach (json_decode(file_get_contents($source),true) as $name) {
                $this->data['names']['first'][$gender][] = $name;
            }
        }
        $this->data['names']['last'] = json_decode(file_get_contents($surnames),true);
        $this->primed = true;
        return $this;
    }
    
    /**
     * Returns a random first name based on gender passed in (will pick a random gender if none are passed)
     * 
     * @param char $gender
     * @return string
     */
    public function firstName($gender=false) {
        if (!$this->primed) {
            $this->prime();
        }
        $this->gender = $gender ? $gender : $this->genders[rand(0,1)];
        return $this->first_name = $this->data['names']['first'][$this->gender][rand(0,count($this->data['names']['first'][$this->gender])-1)];
    }
    
    /**
     * Returns a random last name based on gender passed in (will pick a random gender if none are passed)
     * 
     * @param char $gender
     * @return string
     */    
    public function lastName() {
        if (!$this->primed) {
            $this->prime();
        }
        return $this->last_name = $this->data['names']['last'][rand(0,(int)count($this->data['names']['last'])-1)];
    }
    
    /**
     * Creates a simple user name, but firstName() and lastName() must be called first
     * 
     * @return string
     */
    public function userName() {
        return strtolower(substr($this->first_name,0,1).$this->last_name);
    }
    
    /**
     * Returns the gender from the last name call
     * 
     * @return string
     */
    public function gender() {
        return $this->gender;
    }
    /**
     * Creates a sample email, but firstName() and lastName() must be called first
     * 
     * @param boolean $full
     * @return string
     */
    public function email($full=false) {
        return strtolower((($full) ? $this->first_name.'.'.$this->last_name : substr($this->first_name,0,1).$this->last_name).'@example.com');
    }
    
    /**
     * Returns a random full name based on gender passed in (will pick a random gender if none are passed)
     * 
     * @param char $gender
     * @return string
     */
    public function fullName($gender=false) {
        if (!$this->primed) {
            $this->prime();
        }
        $gender = $gender ? $gender : $this->genders[rand(0,1)];
        return $this->firstName($gender).' '.$this->lastName();
    }
    
    /**
     * Returns an actual city name, and sets the global city object, optionally within a state that was passed as a parameter
     * 
     * @param string $state
     * @return string
     */
    public function city($state=false) {
        if (!$this->primed) {
            $this->prime();
        }
        $state = ($state) ? $state : $this->data['states'][rand(0,count($this->data['states'])-1)];
        $this->city = $this->data['locations'][$state][rand(0,count($this->data['locations'][$state])-1)];
        return $this->city['city'];
    }
    
    /**
     * Returns the state of the global city object or a random state if that isn't set
     * 
     * @return string
     */
    public function state() {
        return (isset($this->city['state'])) ? $this->city['state'] : $this->data['states'][rand(0,count($this->data['states'])-1)];
    }
    
    /**
     * Returns a random street name
     * 
     * @return string
     */
    public function streetAddress() {
        if (!$this->primed) {
            $this->prime();
        }
        return $this->data['streets'][rand(0,count($this->data['streets'])-1)]['street'];
    }
    
    /**
     * Returns a random number of a certain length, optionally between a min and max number, and padded a certain length
     * 
     * @param int $length
     * @param int $min
     * @param int $max
     * @param char $pad
     * @return number
     */
    public function number($length=8,$min=false,$max=false,$pad=false) {
        $min    = ($min!==false) ? $min : false;
        $max    = ($max) ? $max : false;
        $number = '';
        if ($min && $max) {
            $number = rand($min,$max);
        } else if (($min || ($min!==false)) && ($min>(int)$number)) {
            $number = rand($min,getrandmax());
        } else if (($max) && ((int)$number > (int)$max)) {
            $number = rand(0,$max);
        } else {
            for ($i=0; $i<$length; $i++) {
                $number.=rand(0,9);
            }                
        }
        if ($pad !== false) {
            if (strlen((string)$number) < $length) {
                $number = str_pad($number,$length,$pad,STR_PAD_LEFT);           //maybe?
            }
        }
        return $number;
    }
    
    /**
     * Returns the zip code of the current city object or sets that object and then returns the zip code
     * 
     * @param string $state
     * @return string
     */
    public function zipCode($state=false) {
        if (!$this->primed) {
            $this->prime();
        }        
        $state = ($state) ? $state : $this->data['states'][rand(0,count($this->data['states'])-1)];
        if (!$this->city) {
            $this->city($state);
        }
        return $this->city['zip_code'];
    }
    
    /**
     * Returns a random full address optionally one within a state and actual city
     * 
     * @param string $state
     * @return type
     */
    public function fullAddress($state=false) {
        if (!$this->primed) {
            $this->prime();
        }
        $state = ($state) ? $state : $this->data['states'][rand(0,count($this->data['states'])-1)];
        return $this->number(4,100,4000).' '.$this->streetAddress().', '.$this->city($state).', '.$state.', '.$this->city['zip_code'];;
    }
    
    /**
     * Returns a phone number, optionally formatted, but the area code should actually be the area code of the city object
     * 
     * @param string $state
     * @param boolean $formatted
     * @return string
     */
    public function phoneNumber($formatted=false) {
        if (!$this->primed) {
            $this->prime();
        }
        return ($formatted) ? (isset($this->city['area-code']) ? $this->city['area-code'] : $this->number(3,100,999))."-".$this->number(3,100,999).'-'.$this->number(4,0,9999,'0') : $this->number(5,10000,99999).$this->number(5,10000,99999);
    }
    
    /**
     * Returns a random date, optionally between one or two date limits
     * 
     * @param date $min_date
     * @param date $max_date
     * @return date
     */
    public function date($min_date=false,$max_date=false) {
        $date = false;
        if ($min_date && $max_date) {
            $date = date('m/d/Y',rand(strtotime($min_date),strtotime($max_date)));
        } else if ($min_date) {
            $date = date('m/d/Y',rand(date('Ymd',strtotime($min_date)),20301231));
        } else if ($max_date) {
            $date = date('m/d/Y',rand(19010101,date('Ymd',strtotime($max_date))));
        } else {
            $date = date('m/d/y',strtotime(rand(strtotime('19010101'),strtotime('20301231'))));
        }
        return $date;
    }
}