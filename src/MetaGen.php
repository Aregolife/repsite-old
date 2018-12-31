<?php 
namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
include_once(dirname(__FILE__) . '/config.php');
$wsdl_locations = WSDL::$wsdl_locations; 

foreach([Inventory::class,
	Enrollments::class,Dealer::class,Dealership::class] as $class_name){
	$obj = new $class_name();
	$obj->create_object_files();
}
