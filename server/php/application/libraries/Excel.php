<?php
require_once __DIR__.'/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
class Excel
{
	var $spreadsheet=null;
	var $name='';
	function __construct() {
		$this->spreadsheet = new Spreadsheet();
	}

	function setTitle(string $title)
	{
		$this->spreadsheet->getProperties()
		->setCreator('Small')
		->setCompany('V6plus')
		->setTitle($title)
		->setSubject($title);
		$this->name=$title;
		return $this;
	}

	function write($data,$header)
	{
		$sheet=$this->spreadsheet->getActiveSheet();
		$i=1;
		foreach ($header as $value) {
			$sheet->setCellValueByColumnAndRow($i++,1,$value);
		}
		$keys=array_keys($header);
		$row=2;
		foreach ($data as $item) {
			$i=1;
			foreach ($keys as $key) {
				$sheet->getCellByColumnAndRow($i++,$row)
				->setValueExplicit($item[$key],DataType::TYPE_STRING);
			}
			$row++;
		}
		return $this;
	}

	function download(string $name='')
	{
		$name=empty($name)?$this->name:$name;
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
		header('Cache-Control: max-age=0');
		$writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
		$writer->save('php://output');
	}
}
