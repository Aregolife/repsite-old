<?php
namespace AregoLife\Repsite;

class Config {
	public function __construct(){
		ini_set('error_log',dirname(__FILE__) . '/error_log');
		define('AREGOLIFE_DEBUG_MODE','1');
		if(defined('AREGOLIFE_DEBUG_MODE')){
			error_reporting(-1);
			ini_set('display_errors','1');
		}
		if(!defined('AREGOLIFE_API_KEY')){
			define('AREGOLIFE_API_KEY',trim(file_get_contents(dirname(__FILE__) . '/api.key')));
			define('AREGOLIFE_CONTEXT','AREGOLIFE');
		}
	}
	public static function is_dev(){
		return file_exists(dirname(__FILE__) . '/is_dev');
	}
	public static function stripe(){
		if(self::is_dev()){
			return ['pk' => 'pk_test_v6Oj5rkZcP0Sq2rWh4nqw8Gg',
				'sk' => 'sk_test_aVPhggVtiypN5G0rJzHNSPmE'];
		}else{
			throw 'No live credentials supplied yet';
		}
	}
}
(new Config());
