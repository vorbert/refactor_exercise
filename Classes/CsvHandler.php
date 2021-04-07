<?php

class Classes_CsvHandler
{
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
}