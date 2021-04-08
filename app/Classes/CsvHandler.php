<?php
namespace App\Classes;

class CsvHandler
{
	/**
	 * processCsvFileContent function
	 *
	 * @param [type] $fileName
	 * @return void
	 */
	public function processCsvFileContent($fileName) {
		$header = [];
		$rows = [];

		$rowCount = 1;
		if (($file = fopen($fileName, 'r')) !== false) {
		    while (($data = fgetcsv($file)) !== false) {
		        if ($rowCount == 1) {
		            $header = $data;
		        } else {
		            $tempArray = [];
		            for ($i = 0; $i < count($header); $i++) {
		                $tempArray[$header[$i]] = $data[$i];
		            }
		            $rows[] = $tempArray;
		        }
		        $rowCount++;
		    }
		    fclose($file);
		}

		return ['header' => $header, 'rows' => $rows];	
	}

	/**
	 * static sortCsv function
	 *
	 * @param [type] $a
	 * @param [type] $b
	 * @return void
	 */
	public static function sortArray($a, $b) {
		$result = -1 * ($a['priority'] <=> $b['priority']);
	    return $result == 0 ? $a['created_at'] <=> $b['created_at'] : $result;
	}

	/**
	 * sortCsvFile function
	 *
	 * @param [type] $csvFile
	 * @return void
	 */
	public function sortCsvFile($csvFile) {
		usort($csvFile['rows'], "static::sortArray");
		return $csvFile;
	}
}