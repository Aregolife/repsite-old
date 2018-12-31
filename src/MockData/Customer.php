<?php 
namespace AregoLife\Repsite\MockData;
require_once(realpath(dirname(__FILE__) . '/../../') . '/vendor/autoload.php');
use AregoLife\Repsite\Config as Conf;
use joshmoody\Mock\Generator as Gen;

class Customer {
	public function __construct($state='UT'){
		$generator = $this->generator = new Gen();
		$person = $generator->getPerson($state);
		$name = $person->name;
		$this->billing_first_name = $name->first;
		$this->billing_last_name = $name->last;
		$this->billing_company = $person->company;
		$this->billing_address_1 = $person->address->line_1;
		$this->billing_address_2 = $person->address->line_2;
		$this->billing_city = $person->address->city;
		$this->billing_state = $state;
		$this->billing_postcode = $person->address->zip;
		$this->billing_phone = $person->phone->home;
		$this->billing_email = $person->internet->email;
	}

	public function dump(){
		return json_encode($this,1);
	}
	public function generator(){
		return $this->generator;
	}
}
