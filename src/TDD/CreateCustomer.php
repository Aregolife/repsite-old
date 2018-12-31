<?php
require dirname(__FILE__) . '/../../vendor/autoload.php';
use PHPUnit\Framework\TestCase;
use SoapConsumer;
#use \AregoLife\Repsite\Structures\EnrollMemberExtended;
use \AregoLife\Repsite\Structures\EnrollCustomer;
#use \AregoLife\Repsite\Structures\EnrollMemberAsOrphan;
#use \AregoLife\Repsite\Structures\GetCatalogueList;

class CreateCustomerTest extends TestCase
{
    public function testCreateWordpressCustomer() {
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

    }
}
?>
