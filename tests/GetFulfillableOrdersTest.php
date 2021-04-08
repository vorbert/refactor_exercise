<?php 
use PHPUnit\Framework\TestCase;
use App\Classes\FulfillableOrders;
use App\Classes\CsvHandler;

class GetFulfillableOrdersTest extends TestCase
{
	
	public function testIfFulfillableOrdersHasAttributes(): void {
		$this->assertClassHasAttribute("argC", '\\App\\Classes\\FulfillableOrders', "FulfillableOrders doesn't has argc as attribute");
		$this->assertClassHasAttribute("argV", '\\App\\Classes\\FulfillableOrders', "FulfillableOrders doesn't has argv as attribute");
		$this->assertClassHasAttribute("stock", '\\App\\Classes\\FulfillableOrders', "FulfillableOrders doesn't has stock as attribute");
	}

	public function testIfCsvFileExists(): void {
		$this->assertFileExists(__DIR__ . '/../app/orders.csv');
	}

	public function testcheckInputArgumentsFunctionWorksAndReturnsTrue() {
		$fulfillableOrders = new FulfillableOrders(2, array("get_fulfillable_orders-v2.php", '{"1":8,"2":4,"3":5}'));

		$this->assertIsBool($fulfillableOrders->checkInputArguments());
		$this->assertTrue($fulfillableOrders->checkInputArguments());
	}

	public function testIfProcessCsvFileContentReturnsProperArrayAndValues() {
		$csvHandler = new CsvHandler();
		$csvFile = $csvHandler->processCsvFileContent(__DIR__ . '/../app/orders.csv');

		$this->assertIsArray($csvFile);

		$this->assertArrayHasKey('header', $csvFile);
		$this->assertArrayHasKey('rows', $csvFile);

		$this->assertEquals($csvFile['header'][0], 'product_id');
		$this->assertEquals($csvFile['header'][1], 'quantity');
		$this->assertEquals($csvFile['header'][2], 'priority');
		$this->assertEquals($csvFile['header'][3], 'created_at');

		$this->assertArrayHasKey('product_id', $csvFile['rows'][0]);
		$this->assertArrayHasKey('quantity', $csvFile['rows'][0]);
		$this->assertArrayHasKey('priority', $csvFile['rows'][0]);
		$this->assertArrayHasKey('created_at', $csvFile['rows'][0]);

		$this->assertEquals($csvFile['rows'][0]['product_id'], '1');
		$this->assertEquals($csvFile['rows'][0]['quantity'], '2');
		$this->assertEquals($csvFile['rows'][0]['priority'], '3');
		$this->assertEquals($csvFile['rows'][0]['created_at'], '2021-03-25 14:51:47');

		$this->assertEquals($csvFile['rows'][1]['product_id'], '2');
		$this->assertEquals($csvFile['rows'][1]['quantity'], '1');
		$this->assertEquals($csvFile['rows'][1]['priority'], '2');
		$this->assertEquals($csvFile['rows'][1]['created_at'], '2021-03-21 14:00:26');

		$this->assertEquals($csvFile['rows'][2]['product_id'], '2');
		$this->assertEquals($csvFile['rows'][2]['quantity'], '4');
		$this->assertEquals($csvFile['rows'][2]['priority'], '1');
		$this->assertEquals($csvFile['rows'][2]['created_at'], '2021-03-22 17:41:32');
	}

	public function testIfSortArrayFunctionWorks() {
		$csvHandler = new CsvHandler();

		$a = ['priority' => 1, 'created_at' => '2021-03-25 19:08:22'];
		$b = ['priority' => 3, 'created_at' => '2021-03-23 05:01:29'];

		$this->assertEquals($csvHandler->sortArray($a, $b), 1);

		$b = ['priority' => 1, 'created_at' => '2021-03-25 19:08:22'];
		$a = ['priority' => 3, 'created_at' => '2021-03-23 05:01:29'];

		$this->assertEquals($csvHandler->sortArray($a, $b), -1);

		$a = ['priority' => 3, 'created_at' => '2021-03-25 19:08:22'];
		$b = ['priority' => 3, 'created_at' => '2021-03-23 05:01:29'];

		$this->assertEquals($csvHandler->sortArray($a, $b), 1);

		$b = ['priority' => 3, 'created_at' => '2021-03-25 19:08:22'];
		$a = ['priority' => 3, 'created_at' => '2021-03-23 05:01:29'];

		$this->assertEquals($csvHandler->sortArray($a, $b), -1);
	}

	public function testIfSortingByPriorityAndCreatedAtWorks() {
		$csvHandler = new CsvHandler();
		$csvFile = $csvHandler->processCsvFileContent(__DIR__ . '/../app/orders.csv');

		$this->assertEquals($csvFile['rows'][1]['product_id'], '2');
		$this->assertEquals($csvFile['rows'][1]['quantity'], '1');
		$this->assertEquals($csvFile['rows'][1]['priority'], '2');
		$this->assertEquals($csvFile['rows'][1]['created_at'], '2021-03-21 14:00:26');

		$csvFile = $csvHandler->sortCsvFile($csvFile);

		$this->assertEquals($csvFile['rows'][1]['product_id'], '1');
		$this->assertEquals($csvFile['rows'][1]['quantity'], '2');
		$this->assertEquals($csvFile['rows'][1]['priority'], '3');
		$this->assertEquals($csvFile['rows'][1]['created_at'], '2021-03-25 14:51:47');
	}

	public function testIfRenderCsvVariableGiveBackPropeData() {
		$expected = "product_id          quantity            priority            created_at          \n================================================================================\n1                   2                   high                2021-03-25 14:51:47 \n2                   1                   medium              2021-03-21 14:00:26 \n3                   1                   medium              2021-03-22 12:31:54 \n2                   2                   low                 2021-03-24 11:02:06 \n1                   1                   low                 2021-03-25 19:08:22 \n";

		$fulfillableOrders = new FulfillableOrders(2, array("get_fulfillable_orders-v2.php", '{"1":2,"2":3,"3":1}'));
		$csvHandler = new CsvHandler();
		$csvFile = $csvHandler->processCsvFileContent(__DIR__ . '/../app/orders.csv');
		$csvFile = $csvHandler->sortCsvFile($csvFile);
		$result = $fulfillableOrders->renderCsvFileToVariable($csvFile);

		$this->assertEquals($result, $expected);
	}
}