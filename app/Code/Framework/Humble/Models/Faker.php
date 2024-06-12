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
        print("Primed\n");
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
        $gender = $gender ? $gender : $this->genders[rand(0,1)];
        return $this->data['names']['first'][$gender][rand(0,count($this->data['names']['first'][$gender]))];
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
        return $this->data['names']['last'][rand(0,count($this->data['names']['last']))];
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
    
    public function city($state=false) {
        if (!$this->primed) {
            $this->prime();
        }
        $state = ($state) ? $state : $this->data['states'][rand(0,count($this->data['states']))];
        $this->city = $this->data['locations'][$state][rand(0,count($this->data['locations'][$state]))];
        return $this->city['city'];
    }
    
    public function streetAddress() {
        if (!$this->primed) {
            $this->prime();
        }
        return $this->data['streets'][rand(0,count($this->data['streets']))]['street'];
    }
    
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
    
    public function zipCode($state=false) {
        if (!$this->primed) {
            $this->prime();
        }        
        $state = ($state) ? $state : $this->data['states'][rand(0,count($this->data['states']))];
        if (!$this->city) {
            $this->city($state);
        }
        return $this->city['zip_code'];
    }
    
    public function fullAddress($state=false) {
        if (!$this->primed) {
            $this->prime();
        }
        $state = ($state) ? $state : $this->data['states'][rand(0,count($this->data['states']))];
        return $this->number(4,100,4000).' '.$this->streetAddress().', '.$this->city($state).', '.$state.', '.$this->city['zip_code'];;
    }
    
    public function phoneNumber($state=false,$formatted=false) {
        if (!$this->primed) {
            $this->prime();
        }
        return ($formatted) ? ( isset($this->city['area-code']) ? $this->city['area-code'] : $this->number(3,100,999))."-".$this->number(3,100,999).'-'.$this->number(4,0,9999,'0') : $this->number(5,10000,99999).$this->number(5,10000,99999);
    }
}