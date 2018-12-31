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
		$this->_response_data[] = [
			'time' => time(null),
			'response' => $this->_client->__getLastResponse(),
			'response_headers' => $this->_client->__getLastResponseHeaders(),
			'request' => $this->_client->__getLastRequest()
		];
		return $ret_val;
	}
	public function __destruct(){
		file_put_contents(dirname(__FILE__) . '/log', $this->_log,FILE_APPEND);
	}

}

