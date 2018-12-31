<?php namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/config.php');
class Dealer extends SoapConsumer {
	public function __construct(){
		$wsdl_locations = WSDL::$wsdl_locations;
		$wsdl = $wsdl_locations['Dealer'];
		parent::__construct($wsdl,'https://www.firestormwebservices.com/FirestormWebServices/FirestormDealerWS.asmx');
	}
}
