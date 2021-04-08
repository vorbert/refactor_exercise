<?php 
use PHPUnit\Framework\TestCase;
use App\Classes\FulfillableOrders;

class GetFulfillableOrdersTest extends TestCase
{
	
	public function testIfFulfillableOrdersHasAttributes(): void {
		$this->assertClassHasAttribute("argC", '\\App\\Classes\\FulfillableOrders', "FulfillableOrders doesn't has argc as attribute");
		$this->assertClassHasAttribute("argV", '\\App\\Classes\\FulfillableOrders', "FulfillableOrders doesn't has argc as attribute");
		$this->assertClassHasAttribute("stock", '\\App\\Classes\\FulfillableOrders', "FulfillableOrders doesn't has argc as attribute");
	}

	public function testcheckInputArgumentsFunctionWorks() {
		$fulfillableOrders = new FulfillableOrders(2, array("get_fulfillable_orders-v2.php", '{"1":8,"2":4,"3":5}'));
		$this->assertTrue($fulfillableOrders->checkInputArguments());
	}
}