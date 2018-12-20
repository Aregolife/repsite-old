<?php namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
class TrivialLoader {
	public function __construct($values){
		foreach($values as $key => $value){
			$this->$key = $value;
		}
	}
}

