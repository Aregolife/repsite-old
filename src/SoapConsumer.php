<?php 
namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
include_once(dirname(__FILE__) . '/config.php');
use \AregoLife\Repsite\Structures\EnrollMemberExtended;
use \AregoLife\Repsite\Structures\EnrollCustomer;
use \AregoLife\Repsite\Structures\EnrollMemberAsOrphan;
use \AregoLife\Repsite\Structures\GetCatalogueList;
$wsdl_locations = WSDL::$wsdl_locations; 

class SoapConsumer {
	protected $_api_key;
	protected $_client;
	protected $_wsdl;
	protected $_log;
	protected $_context;
	protected $_response_data;
	public function __construct($wsdl,$location){
		ini_set('soap.wsdl_cache_enabled','0');
		$this->_api_key = AREGOLIFE_API_KEY;
		$this->_context = AREGOLIFE_CONTEXT;
		$this->_client = new \SoapClient($wsdl,$conf =[
			'location' => $location,
			'exceptions'   => true,
			'cache_wsdl' => WSDL_CACHE_NONE,
			'soap_version' => SOAP_1_2 ,
			'trace' => 1,
			'uri' => 'http://www.trinitysoft.net',
			'ssl_method' => SOAP_SSL_METHOD_SSLv23,
		]);
		$this->log($conf);
		$this->_url_data = ['wsdl' => $wsdl,'location' => $location];
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
	public function post($func,$xml,$post_uri){
$xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body>' . "<${func} xmlns=\"http://trinitysoft.net/\">$xml</${func}></soap:Body></soap:Envelope>";

		$headers = [
			"Host: www.firestormwebservices.com",
			"Content-Type: text/xml; charset=utf-8",
			"SOAPAction: \"http://trinitysoft.net/${func}\"",
			"Content-length: ".strlen($xml_post_string),
		];


		// PHP cURL  for https connection with auth
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_URL, $post_uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		// converting
		$response = curl_exec($ch); 
		curl_close($ch);

		return $response;
		// converting
		//$response1 = str_replace("<soap:Body>","",$response);
		//$response2 = str_replace("</soap:Body>","",$response1);

		// convertingc to XML
		//$parser = simplexml_load_string($response2);
		// user $parser to get your data out of XML response and to display it.
		//var_dump($response);
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
	public function manual_call_func($func,$struct_object){
		$body = "";
		$struct_object->Token = $this->_api_key;
		$struct_object->Context = $this->_context;
		foreach((array)$struct_object as $key => $value){
			$body .= "<${key}>";
			if(strlen($value) == 0){
				$body .= "0";
			}else{
				$body .= $value;
			}
			$body .= "</${key}>";
		}
		return ($this->post($func,$body,$this->_url_data['location']));
	}
	public function call_func($func,$struct_object){
		/**
		 * IMPORTANT!!!
		 * -- Firestorm's Token and Context keys are CASE SENSITIVE!
		 */
		$struct_object->Token =  $this->_api_key;
		$struct_object->Context = $this->_context;
		$this->log('called:' . $func,$struct_object);
		$output_headers = [];
		$ret_val = null;
		try{

			$ret_val = $this->_client->__call($func, [(array)$struct_object]);
		}catch(\Exception $e){
		$this->_response_data[] = [
			'time' => time(null),
			'response' => $this->_client->__getLastResponse(),
			'response_headers' => $this->_client->__getLastResponseHeaders(),
			'request' => $this->_client->__getLastRequest(),
			'ret_val' => $ret_val
		];
			$this->error('exception: ' . $e->getMessage());
			$this->error('response_data:' . var_export($this->_response_data,1));
			return false;
		}
		$this->_response_data[] = [
			'time' => time(null),
			'response' => $this->_client->__getLastResponse(),
			'response_headers' => $this->_client->__getLastResponseHeaders(),
			'request' => $this->_client->__getLastRequest(),
			'ret_val' => $ret_val
		];
		$this->log('last response:' . var_export($this->_client->__getLastResponse(),1));
		$this->log('last response headers:' . var_export($this->_client->__getLastResponseHeaders(),1));
		$this->log('last request:' . var_export($this->_client->__getLastRequest(),1));
		$this->log('output headrs:' . var_export($output_headers,1));
		return $ret_val;
	}
	public function response_data(){ return $this->_response_data; }
	public function __destruct(){
		file_put_contents(dirname(__FILE__) . '/log', $this->_log,FILE_APPEND);
	}

}

