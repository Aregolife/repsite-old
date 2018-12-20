<?php 
namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
include_once(dirname(__FILE__) . '/config.php');
use Structures\EnrollMemberExtended;
use Structures\EnrollCustomer;
use Structures\EnrollMemberAsOrphan;
use Structures\GetCatalogueList;
if(defined('AREGOLIFE_DEBUG_MODE')){
	error_reporting(-1);
	ini_set('display_errors','1');
}
$wsdl_locations = WSDL::$wsdl_locations; 

class SoapConsumer {
	protected $_api_key;
	protected $_client;
	protected $_wsdl;
	protected $_log;
	protected $_context;
	public function __construct($wsdl,$location){
		ini_set('soap.wsdl_cache_enabled','0');
		$this->_api_key = AREGOLIFE_API_KEY;
		$this->_context = AREGOLIFE_CONTEXT;
		$this->_client = new \SoapClient($wsdl,$conf =[
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
	public function process_struct_description_to_php($desc){
		$lines = explode("\n",$desc);
		$file_contents = '<?php namespace AregoLife\Repsite\Structures;
		use AregoLife\Repsite\TrivialLoader as Loader;
		class ';
		foreach($lines as $index => $ln){
			if(preg_match('|^\s*struct ([^\s]+)\s*{|',$ln,$matches)){
				$file_contents .= $matches[1] . ' extends Loader {
				';
				continue;
			}else{
				$ln_str = preg_replace('/^[\s]*(string|int|<anyXML>|double|boolean)\s*/','public \$',$ln,1);
				$ln_str = preg_replace('/;/',";\n",$ln_str);
				$file_contents .= $ln_str;
			}
		}
		$file_contents .= "\n\n";
		return $file_contents;
	}

	public function create_object_files(){
		@mkdir(dirname(__FILE__) . '/Structures/');
		$type_export  = $this->_client->__getTypes();
		foreach($type_export as $index => $struct_description){
			$matches = [];
			if(preg_match('|^struct\s+([^ ]+)\s*{|',$struct_description,$matches)){
				file_put_contents(dirname(__FILE__) . '/Structures/' . $matches[1] . '.php',
					$this->process_struct_description_to_php($struct_description));
			}else{
				$this->error('Couldn\'t properly parse struct description: ' . var_export($struct_description,1));
			}
		}
	}
	private function error(){
		$this->_log .= 'ERROR: ' . date('YYYY-MM-DD H:i:s') . '::' .  var_export(func_get_args(),1);
	}
	private function log(){
		$this->_log .= date('YYYY-MM-DD H:i:s') . '::' .  var_export(func_get_args(),1);
	}
	public function call_func($func,$struct_object){
		/**
		 * IMPORTANT!!!
		 * -- Firestorm's Token and Context keys are CASE SENSITIVE!
		 */
		$struct_object->Token =  $this->_api_key;
		$struct_object->Context = $this->_context;
		$this->log('called:' . $func,$struct_object);
		$this->log('types: ' . var_export($this->_client->__getTypes(),1));
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

function enroll_member_example(){
	try {
		$enrollments = new Enrollments();
		$ret = $enrollments->call_func('EnrollMemberExtended',new EnrollMemberExtended(
			['CatalogueID' => 0,
			'FirstName' => 'John',
			'MiddleInitial' => 'D',
			'LastName' => 'Doe',
			'CompanyName' => 'Test incorporated',
			'TaxPayerNumber' => '111-11-1111',
			'MailingAddress1' => '1234 Foobar Street',
			'MailingAddress2' => '',
			'MailingCity' => 'Taylorsville',
			'MailingState' => 'UT',
			'MailingZip' => 84123,
			'MailingCountry' => 'USA',
			'BillingAddress1' => '123 Foobar Street',
			'BillingAddress2' => '',
			'BillingCity' => 'Taylorsville',
			'BillingState' => 'UT',
			'BillingZip' => 84123,
			'BillingCountry' => 'USA',
			'DayPhone' => '801-123-1234',
			'EveningPhone' => '801-123-1222',
			'MobilePhone' => '801-333-1234',
			'Email' => 'aregotest@bnull.net',
			'ProductNumber' => '1',
			'Price' => 0.00,
			'DealershipTypeCode' => '',
			'MemberEnrollerID' => 0,
			'MemberEnrollerPosition' => '',
			'SponsorMemberID' => 0,
			'SponsorPosition' => '',
			'BinaryPlacementMemberID' => 0,
			'BinaryPlacementPosition' => '',
			'BinaryPlacementLineage' => 'L',	//L,R, or W (left,right,weak)
			'UniPlacementMemberID' =>0,
			'UniPlacementPosition' => 0,
			'PaymentTypeCode' => 0,
			'CardAccountNumber' => '1000-2000-3000-4000',
			'CVV2Code' => '123',
			'CardHolderName' => 'John Doe',
			'CardExpirationMonth' => 10,
			'CardExpirationYear' => 2021,
			'ShippingCode' => 0,
			'ReplicatedWebsiteURL' => 'foobar',
			'ReplicatedWebsitePassword' => 'foobar',
			'AddlPaymentInfo' => '',
			'OrderTotalAmt' => 0.00
		]));
		var_dump($ret);
	}catch(\Exception $e){
		return ['error' => $e];
	}
	return ['status' => 'ok','return' => $ret];
}
function handle_return_status($result,$result_member){
		if(isset($result->$result_member)){
			$dom = new \DOMDocument();
			if($dom->loadXML($result->$result_member) === false){
				return ['error' => 'Couldn\'t parse result XML','result' => $result];
			}
			$status = $dom->getElementsByTagName('STATUS');
			foreach ($status as $only_status){
				if($only_status->nodeValue === 'FAIL'){
					return ['error' => 'Failed to enroll customer','result' => $result,'ERRORMSG' => $dom->getElementsByTagName('ERRORMSG')[0]->nodeValue];
				}else{
					return ['status' => 'success','result' => $result];
				}
				break;
			}
		}else{
			return ['error' => 'Result didn\'t have result_member in it','result' => $result];
		}
		return ['error' => 'Unreachable code segment hit','result' => $result];
}

function enroll_customer_example(){
	$enrollments = new Enrollments();
	try{
		srand(time());
		return handle_return_status($enrollments->call_func('EnrollCustomer',new EnrollCustomer([
			'FirstName' => 'John',
			'MiddleInitial' => 'L',
			'LastName' => 'Jefferson',
			'CompanyName' => 'Jefferson Incorprated',
			'MailingAddress1' => '1234 Foobar Street',
			'MailingAddress2' => '',
			'MailingCity' => 'Taylorsville',
			'MailingState' => 'UT',
			'MailingZip' => '84123',
			'MailingCountry' => 'USA',
			'BillingAddress1' => '1234 Foobar Street',
			'BillingAddress2' => '',
			'BillingCity' => 'Taylorsville',
			'BillingState' => 'UT',
			'BillingZip' => '84123',
			'BillingCountry' => 'USA',
			'DayPhone' => '801-123-1234',
			'EveningPhone' => '801-123-1234',
			'MobilePhone' => '801-123-1234',
			'Email' => 'aregocustomer@bnull.net',
			'SponsorMemberID' => '',
			'Username' => 'aregobnull' . (rand() * 10),
			'WebsitePassword' => 'foobar',
			'CustomerType' => '',
		])),'EnrollMemberExtendedResult');
	}catch(\Exception $e){
		return ['error' => 'Exception occurred','exception' => $e->getMessage()];
	}
}

function enroll_member_as_orphan_example(){
	$enrollments = new Enrollments();
	try{
		srand(time());
		return handle_return_status($enrollments->call_func('EnrollMemberAsOrphan',new EnrollMemberAsOrphan([
'TaxPayerNumber' => '123-11-1234',
'CatalogueID' => '', /** "Any active enrollment catalog in Firestorm" */
'ProductNumber' => '', /** "Must exist in specified catalog" */
'Price' => 0.00,	/*
"LEGACY – NOT USED. The actual price
of the enrollment will be calculated
using the specified product’s pricing in
the catalog. All tax, shipping and other
fees will be calculated based on system
configurations
Must be >= 0 " --the docs
*/
'DealershipTypeCode' => '', //"Must exist in tblDealershipType.Code" --the docs
'MemberEnrollerID' => '1',
'MemberEnrollerPosition' => 1,	//Almost always 1 in most cases (as stated in docs)
'SponsorMemberID' => '1',	//Dealr ID
'SponsorPosition' => 1,	//Almost always 1 in most cases (as stated in docs) 
'BinaryPlacementMemberID' => '',
'BinaryPlacementPosition' => '',
'BinaryPlacementLineage' => '',
'UniPlacementMemberID' => '',
'UniPlacementPosition' => '',
'PaymentTypeCode' => '',
'CardAccountNumber' => '1000-2000-3000-4000',
'CVV2Code' => '123',
'CardHolderName' => 'John L Doe',
'CardExpirationMonth' => 10,
'CardExpirationYear' => 2021,
'ShippingCode' => '1234',
'ReplicatedWebsiteURL' => 'aregobnull' . (rand() * 10),
'ReplicatedWebsitePassword' => 'foobar',
'AddlPaymentInfo' => '',
			'FirstName' => 'John',
			'MiddleInitial' => 'L',
			'LastName' => 'Jefferson',
			'CompanyName' => 'Jefferson Incorprated',
			'MailingAddress1' => '1234 Foobar Street',
			'MailingAddress2' => '',
			'MailingCity' => 'Taylorsville',
			'MailingState' => 'UT',
			'MailingZip' => '84123',
			'MailingCountry' => 'USA',
			'BillingAddress1' => '1234 Foobar Street',
			'BillingAddress2' => '',
			'BillingCity' => 'Taylorsville',
			'BillingState' => 'UT',
			'BillingZip' => '84123',
			'BillingCountry' => 'USA',
			'DayPhone' => '801-123-1234',
			'EveningPhone' => '801-123-1234',
			'MobilePhone' => '801-123-1234',
			'Email' => 'aregocustomer@bnull.net',
			'SponsorMemberID' => 0,
			'Username' => 'aregobnull' . (rand() * 10),
			'WebsitePassword' => 'foobar',
			'CustomerType' => '',
		])),'EnrollMemberAsOrphanResult');
	}catch(\Exception $e){
		return ['error' => 'Exception occurred','exception' => $e->getMessage()];
	}
}

function get_catalogue_list_example(){
	$inventory = new Inventory();
	try{
		srand(time());
		$result = $inventory->call_func('GetCatalogueList',new GetCatalogueList([
'CatalogueID' => '',
'CountryID' => 'USA',
'DealershipTypeID' => '',
'RankCode' => '',
'ActiveOnly' => '',
		]));
		if(!isset($result->GetCatalogueListResult)){
			return ['error' => 'Malformed or missing result','result' => $result];
		}
		$dom = new \DOMDocument();
		$ret = $dom->loadXML($result->GetCatalogueListResult->any);
		if(!$ret){
			return ['error' => 'Malformed XML caught by loadXML','result' => $result];
		}
	}catch(\Exception $e){
		return ['error' => 'Exception occurred','exception' => $e->getMessage()];
	}
}

/*
$result = enroll_member_as_orphan_example();
	var_dump($result);
die;
 */
