<?php
require dirname(__FILE__) . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use AregoLife\Repsite\Structures\DealerLocator;
use AregoLife\Repsite\Structures\GetDealershipTypes;
use AregoLife\Repsite\Structures\EnrollMemberAsOrphan;
use AregoLife\Repsite\Dealer;
use AregoLife\Repsite\Dealership;
use AregoLife\Repsite\Enrollments;
use AregoLife\Repsite\MockData\Customer as MockData;

class CreateDistributorTest extends TestCase
{
	public function testGetDealershipTypes() { 
			$ds = new Dealership();
			$req = $ds->call_func('GetDealershipTypes',new GetDealershipTypes([
			'DealershipTypeID' => '""']));
			var_dump($ds->response_data());
		$this->assertEquals(0,0);
	}

	/** This doesn't give us a valid response. Don't use
	 *
	public function testCreateDistributor() {
		try{
			$md = new MockData();
			$enrollments = new Enrollments();
			$ret = $enrollments->call_func('EnrollMemberExtended',new EnrollMemberExtended(
				['CatalogueID' => '""',
				'FirstName' => $md->billing_first_name,
				'MiddleInitial' => 'D',
				'LastName' => $md->billing_last_name,
				'CompanyName' => $md->billing_company,
				'TaxPayerNumber' => $md->person()->ssn,
				'MailingAddress1' => $md->billing_address_1,
				'MailingAddress2' => $md->billing_address_2,
				'MailingCity' => $md->billing_city,
				'MailingState' => $md->billing_state,
				'MailingZip' => $md->billing_postcode,
				'MailingCountry' => 'USA',
				'BillingAddress1' => $md->billing_address_1,
				'BillingAddress2' => $md->billing_address_2,
				'BillingCity' => $md->billing_city,
				'BillingState' => $md->billing_state,
				'BillingZip' => $md->billing_postcode,
				'BillingCountry' => 'USA',
				'DayPhone' => $md->person()->phone,
				'EveningPhone' => $md->person()->phone,
				'MobilePhone' => $md->person()->phone,
				'Email' => $md->billing_email,
				'ProductNumber' => '""',
				'Price' => 0.00,
				'DealershipTypeCode' => '""',
				'MemberEnrollerID' => '""',
				'MemberEnrollerPosition' => '""',
				'SponsorMemberID' => '""',
				'SponsorPosition' => '""',
				'BinaryPlacementMemberID' => '""',
				'BinaryPlacementPosition' => '""',
				'BinaryPlacementLineage' => '""',	//L,R, or W (left,right,weak)
				'UniPlacementMemberID' =>'""',
				'UniPlacementPosition' => '""',
				'PaymentTypeCode' => '""',
				'CardAccountNumber' => '1000-2000-3000-4000',
				'CVV2Code' => '123',
				'CardHolderName' => 'John Doe',
				'CardExpirationMonth' => 10,
				'CardExpirationYear' => 2021,
				'ShippingCode' => '""',
				'ReplicatedWebsiteURL' => $md->billing_first_name,
				'ReplicatedWebsitePassword' => 'foobar',
				'AddlPaymentInfo' => '""',
				'OrderTotalAmt' => 0.00
			]));
			var_dump($ret);
			var_dump($enrollments->response_data());
			$this->assertEquals(0,0);
		}catch(\Exception $e){
			var_dump($e);
		}
	}
	 */

	public function testCreateMemberAsOrphan(){
		$enrollments = new Enrollments();
		try{
			srand(time());
			$req = $enrollments->call_func('EnrollMemberAsOrphan',new EnrollMemberAsOrphan([
				'TaxPayerNumber' => '123-11-1234',
				'CatalogueID' => '""', /** "Any active enrollment catalog in Firestorm" */
				'ProductNumber' => '""', /** "Must exist in specified catalog" */
				'Price' => 0.00,	/*
				"LEGACY NOT USED. The actual price
				of the enrollment will be calculated
				using the specified product pricing in
				the catalog. All tax, shipping and other
				fees will be calculated based on system
configurations
Must be >= 0 " --the docs
				 */
				'DealershipTypeCode' => '""', //"Must exist in tblDealershipType.Code" --the docs
				'MemberEnrollerID' => '1',
				'MemberEnrollerPosition' => 1,	//Almost always 1 in most cases (as stated in docs)
				'SponsorMemberID' => '1',	//Dealr ID
				'SponsorPosition' => 1,	//Almost always 1 in most cases (as stated in docs) 
				'BinaryPlacementMemberID' => '""',
				'BinaryPlacementPosition' => '""',
				'BinaryPlacementLineage' => '""',
				'UniPlacementMemberID' => '""',
				'UniPlacementPosition' => '""',
				'PaymentTypeCode' => '""',
				'CardAccountNumber' => '1000-2000-3000-4000',
				'CVV2Code' => '123',
				'CardHolderName' => 'John L Doe',
				'CardExpirationMonth' => 10,
				'CardExpirationYear' => 2021,
				'ShippingCode' => '1234',
				'ReplicatedWebsiteURL' => 'aregobnull' . (rand() * 10),
				'ReplicatedWebsitePassword' => 'foobar',
				'AddlPaymentInfo' => '""',
				'FirstName' => 'John',
				'MiddleInitial' => 'L',
				'LastName' => 'Jefferson',
				'CompanyName' => 'Jefferson Incorprated',
				'MailingAddress1' => '1234 Foobar Street',
				'MailingAddress2' => '""',
				'MailingCity' => 'Taylorsville',
				'MailingState' => 'UT',
				'MailingZip' => '84123',
				'MailingCountry' => 'USA',
				'BillingAddress1' => '1234 Foobar Street',
				'BillingAddress2' => '""',
				'BillingCity' => 'Taylorsville',
				'BillingState' => 'UT',
				'BillingZip' => '84123',
				'BillingCountry' => 'USA',
				'DayPhone' => '801-123-1234',
				'EveningPhone' => '801-123-1234',
				'MobilePhone' => '801-123-1234',
				'Email' => 'aregocustomer@bnull.net',
				'SponsorMemberID' => '""',
				'Username' => 'aregobnull' . (rand() * 10),
				'WebsitePassword' => 'foobar',
				'CustomerType' => '""',
			]),'EnrollMemberAsOrphanResult');
		}catch(\Exception $e){
			var_dump('Exception',$req,$e);
		}
		var_dump($enrollments->response_data());
	}

	public function testDealerLocator(){
		$ds = new Dealer();
		try{
			srand(time());
			$req = $ds->call_func('DealerLocator',new DealerLocator([
				'MaxDistanceInMiles' => 5000,
				'SourceZipCode' => 84123,
				'LastName' => '""',
				'MaxResults' => 70,
				'FirstName' => '""',
				'StateID' => '""'
			]));
		}catch(\Exception $e){
			var_dump('Exception',$req,$e);
		}
		var_dump($ds->response_data());
		$this->assertEquals(0,0);
	}

}
