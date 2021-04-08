<?php
namespace App\Classes;

use App\Classes\CsvHandler;

class FulfillableOrders
{
	public $argC, $argV, $stock;
	
	/**
	 * __construct function
	 *
	 * @param [type] $argc
	 * @param [type] $argv
	 */
	public function __construct($argc, $argv) {
		$this->argC = $argc;
		$this->argV = $argv;
		$this->checkInputArguments();
		$this->processFulfillableOrders();
	}

	/**
	 * checkInputArguments function
	 *
	 * @return void
	 */
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

	/**
	 * processFulfillableOrders function
	 *
	 * @return void
	 */
	private function processFulfillableOrders() {
		try {
			$csvHandler = new CsvHandler();
			$csvFile = $csvHandler->processCsvFileContent(__DIR__ . '/../orders.csv');
			$csvFile = $csvHandler->sortCsvFile($csvFile);
			$output = $this->renderCsvFileToVariable($csvFile);
			echo $output;
		} catch(\Exception $e) {
			echo "Something wnt wrong!\n";
			echo $e->getMessage();
		}
	}

	/**
	 * renderCsvFileToVariable function
	 *
	 * @param [type] $csvFile
	 * @return void
	 */
	public function renderCsvFileToVariable($csvFile) {
		$output = '';
		foreach ($csvFile['header'] as $headerColumn) {
	    	$output .= str_pad($headerColumn, 20);
		}
		$output .= "\n";

		foreach ($csvFile['header'] as $headerColumn) {
		    $output .= str_repeat('=', 20);
		}
		$output .= "\n";

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
		                $output .= str_pad($text, 20);
		            } else {
		                $output .= str_pad($row[$headerColumn], 20);
		            }
		        }
		        $output .= "\n";
		    }
		}
		return $output;
	}
}