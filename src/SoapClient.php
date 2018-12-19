<?php 
namespace AregoLife\Repsite;
if(defined('AREGOLIFE_DEBUG_MODE')){
	error_reporting(-1);
	ini_set('display_errors','1');
}
define('AREGOLIFE_API_KEY',trim(file_get_contents(dirname(__FILE__) . '/api.key')));
$wsdl_locations = [
	'Enrollments' => 'https://www.firestormwebservices.com/FirestormWebServices/FirestormEnrollmentWS.asmx?WSDL',
	'Inventory' => 'https://www.firestormwebservices.com/FirestormWebServices/FirestormInventoryWS.asmx?WSDL',
];

class EnrollMemberExtended {
 public $Token;
 public $Context;
 public $FirstName;
 public $MiddleInitial;
 public $LastName;
 public $CompanyName;
 public $TaxPayerNumber;
 public $MailingAddress1;
 public $MailingAddress2;
 public $MailingCity;
 public $MailingState;
 public $MailingZip;
 public $MailingCountry;
 public $BillingAddress1;
 public $BillingAddress2;
 public $BillingCity;
 public $BillingState;
 public $BillingZip;
 public $BillingCountry;
 public $DayPhone;
 public $EveningPhone;
 public $MobilePhone;
 public $Email;
 public $CatalogueID;
 public $OrderDetail;
 public $OrderTotalAmt;
 public $DealershipTypeCode;
 public $MemberEnrollerID;
 public $MemberEnrollerPosition;
 public $SponsorMemberID;
 public $SponsorPosition;
 public $BinaryPlacementMemberID;
 public $BinaryPlacementPosition;
 public $BinaryPlacementLineage;
 public $UniPlacementMemberID;
 public $UniPlacementPosition;
 public $PaymentTypeCode;
 public $CardAccountNumber;
 public $CVV2Code;
 public $CardHolderName;
 public $CardExpirationMonth;
 public $CardExpirationYear;
 public $ShippingCode;
 public $ReplicatedWebsiteURL;
 public $ReplicatedWebsitePassword;
 public $AddlPaymentInfo;
};

/*
class MSSoapClient extends \SoapClient {
	public function __construct($wsdl,$options){
		parent::__construct($wsdl,$options);
	}
    function unused__doRequest($request, $location, $action, $version, $one_way = null) {
        $ns = "http://trinitysoft.net/";
        $request = preg_replace('/<ns1:(\w+)/', '<$1 xmlns="'.$ns.'"', $request, 1);
        $request = preg_replace('/<ns1:(\w+)/', '<$1', $request);
        $request = str_replace(array('/ns1:', 'xmlns:ns1="'.$ns.'"'), array('/', ''), $request);
        // parent call
        return parent::__doRequest($request, $location, $action, $version,$one_way);
    }
}
 */
class SoapConsumer {
	protected $_api_key;
	protected $_client;
	protected $_wsdl;
	protected $_log;
	public function __construct($wsdl,$location){
		ini_set('soap.wsdl_cache_enabled','0');
		$this->_api_key = AREGOLIFE_API_KEY;
		$this->_client = new \SoapClient($wsdl,$conf =[
			//'location' =>  preg_replace('|\?WSDL$|','',$wsdl),
			'location' => $location,
			"exceptions"   => true,
			'cache_wsdl' => WSDL_CACHE_NONE,
			'soap_version' => SOAP_1_2 ,
			'trace' => 1,
			'uri' => 'http://www.trinitysoft.net',
			'ssl_method' => SOAP_SSL_METHOD_SSLv23,
		]);
		$this->log($conf);
	}
	private function log(){
		$this->_log .= date('YYYY-MM-DD H:i:s') . '::' .  var_export(func_get_args(),1);
	}
	public function call_func($func,$object_name,$args){
		/**
		 * IMPORTANT!!!
		 * -- Firestorm's Token and Context keys are CASE SENSITIVE!
		 */
		$args['Token'] =  $this->_api_key;
		$args['Context'] = $this->_api_key;
		$struct_object = new $object_name();
		foreach($args as $key => $value){
			$struct_object->$key = $value;
		}
		$this->log('called:' . $func,$struct_object);
		//$this->log('types: ' . var_export($this->_client->__getTypes(),1));
		$output_headers = [];
		$ret_val = null;
		try{
			$ret_val = $this->_client->$func($struct_object);
		}catch(\Exception $e){
			$this->log('exception: ' . $e->getMessage());
			$this->log('output headrs:' . var_export($output_headers,1));
		}
		$this->log('last response:' . var_export($this->_client->__getLastResponse(),1));
		$this->log('last response headers:' . var_export($this->_client->__getLastResponseHeaders(),1));
		$this->log('last request:' . var_export($this->_client->__getLastRequest(),1));
		$this->log('output headrs:' . var_export($output_headers,1));
		return $ret_val;
	}
	public function __destruct(){
		file_put_contents(dirname(__FILE__) . '/log', $this->_log,FILE_APPEND);
	}

}

class Enrollments extends SoapConsumer {
	public function __construct(){
		global $wsdl_locations;
		$wsdl = $wsdl_locations['Enrollments'];
		parent::__construct($wsdl,'https://www.firestormwebservices.com/FirestormWebServices/FirestormEnrollmentWS.asmx');
	}
}
class Inventory extends SoapConsumer {
	public function __construct(){
		global $wsdl_locations;
		$wsdl = $wsdl_locations['Inventory'];
		parent::__construct($wsdl,'https://www.firestormwebservices.com/FirestormWebServices/FirestormInventoryWS.asmx');
	}
}

try {
	$enrollments = new Enrollments();
	$enrollments->call_func('EnrollMemberExtended',EnrollMemberExtended::class,
		['CatalogueID' => 0,
'FirstName' => 'John',
'MiddleInitial' => 'D',
'LastName' => 'Doe',
'CompanyName' => 'Test incorporated',
'TaxPayerNumber' => '111-11-1111',
'MailingAddress1' => '',
'MailingAddress2' => '',
'MailingCity' => 'Taylorsville',
'MailingState' => 'UT',
'MailingZip' => 84123,
'MailingCountry' => 'United States',
'BillingAddress1' => '123 Foobar Street',
'BillingAddress2' => '',
'BillingCity' => 'Taylorsville',
'BillingState' => 'UT',
'BillingZip' => 84123,
'BillingCountry' => 'United States',
'DayPhone' => '801-123-1234',
'EveningPhone' => '801-123-1222',
'MobilePhone' => '801-333-1234',
'Email' => 'johndoe@gmail.com',
'ProductNumber' => '',
'Price' => 20.00,
'DealershipTypeCode' => '',
'MemberEnrollerID' => 0,
'MemberEnrollerPosition' => '',
'SponsorMemberID' => 0,
'SponsorPosition' => '',
'BinaryPlacementMemberID' => 0,
'BinaryPlacementPosition' => '',
'BinaryPlacementLineage' => '',
'UniPlacementMemberID' =>0 ,
'UniPlacementPosition' => 0,
'PaymentTypeCode' => 0,
'CardAccountNumber' => '1111-2222-3333-4444',
'CVV2Code' => '123',
'CardHolderName' => 'John Doe',
'CardExpirationMonth' => 10,
'CardExpirationYear' => 2021,
'ShippingCode' => 0,
'ReplicatedWebsiteURL' => 'https://arego.bnull.net',
'ReplicatedWebsitePassword' => 'foobar',
'AddlPaymentInfo' => '',
'OrderTotalAmt' => 24.99
	]);
}catch(\Exception $e){
	print("Exception: " . $e->getMessage());
}

