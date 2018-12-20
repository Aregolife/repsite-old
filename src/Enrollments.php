<?php namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
require_once(dirname(__FILE__) . '/config.php');
class Enrollments extends SoapConsumer {
	public function __construct(){
		$wsdl_locations = WSDL::$wsdl_locations;
		$wsdl = $wsdl_locations['Enrollments'];
		parent::__construct($wsdl,'https://www.firestormwebservices.com/FirestormWebServices/FirestormEnrollmentWS.asmx');
	}
}

