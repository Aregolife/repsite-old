<?php 
namespace AregoLife\Repsite;
require_once(realpath(dirname(__FILE__) . '/../') . '/vendor/autoload.php');
include_once(dirname(__FILE__) . '/config.php');
use SoapConsumer;
use \AregoLife\Repsite\Structures\EnrollMemberExtended;
use \AregoLife\Repsite\Structures\EnrollCustomer;
use \AregoLife\Repsite\Structures\EnrollMemberAsOrphan;
use \AregoLife\Repsite\Structures\GetCatalogueList;
class ExampleStubs {
public static function enroll_member_example(){
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
public static function handle_return_status($result,$result_member){
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

public static function enroll_customer_example(){
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

public static function enroll_member_as_orphan_example(){
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

public static function get_catalogue_list_example(){
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

};

ExampleStubs::enroll_member_example();
