<?php 
namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
use AregoLife\Repsite\Config as Conf;

class HookController {
	public function __construct(){
	}
	public static function __callStatic($method,$arguments){
		switch($method){
		case 'woocommerce_after_checkout_form':
			if(Conf::is_dev()){
				wp_register_script( 
					'repsite_autofill_script', 
					plugins_url('/../../repsite/public/js/repsite-aregolife-autofill-helper.js', __FILE__) ,
					array( 'jquery' )
				);
				wp_enqueue_script('repsite_autofill_script');
			}
			break;
		}
	}
}
