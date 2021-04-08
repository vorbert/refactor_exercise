<?php
namespace App\Classes;

use App\Classes\CsvHandler;

class FulfillableOrders
{
	public $argC, $argV, $stock;
	
	public function __construct($argc, $argv) {
		$this->argC = $argc;
		$this->argV = $argv;
		$this->checkInputArguments();
		$this->processFulfillableOrders();
	}

	private static function sortCsv($a, $b) {
		$result = -1 * ($a['priority'] <=> $b['priority']);
	    return $result == 0 ? $a['created_at'] <=> $b['created_at'] : $result;
	}

	public function checkInputArguments() {
		if ($this->argC != 2) {
		    echo "Ambiguous number of parameters!\n";
		    exit(1);
		}

		if (($this->stock = json_decode($this->argV[1])) == null) {
		    echo "Invalid json!\n";
		    exit(1);
		}

		if (!file_exists(__DIR__ . '/../orders.csv')) {
			echo "Missing csv file!\n";
		    exit(1);
		}

		return true;
	}

	private function processFulfillableOrders() {
		try {
			$csvHandler = new CsvHandler();
			$csvFile = $csvHandler->processCsvFileContent('orders.csv');
			usort($csvFile['rows'], "static::sortCsv");
			$this->renderCsvFile($csvFile);
		} catch(\Exception $e) {
			echo "Something wnt wrong!\n";
			echo $e->getMessage();
		}
	}

	private function renderCsvFile($csvFile) {
		foreach ($csvFile['header'] as $headerColumn) {
	    	echo str_pad($headerColumn, 20);
		}
		echo "\n";

		foreach ($csvFile['header'] as $headerColumn) {
		    echo str_repeat('=', 20);
		}
		echo "\n";

		foreach ($csvFile['rows'] as $row) {
		    if ($this->stock->{$row['product_id']} >= $row['quantity']) {
		        foreach ($csvFile['header'] as $headerColumn) {
		            if ($headerColumn == 'priority') {
		            	switch($row['priority']) {
		            		case 1:
		            			$text = 'low';
		            			break;
	            			case 2:
	            				$text = 'medium';
	            				break;
            				default:
            					$text = 'high';
		            	}
		                echo str_pad($text, 20);
		            } else {
		                echo str_pad($row[$headerColumn], 20);
		            }
		        }
		        echo "\n";
		    }
		}
	}
}