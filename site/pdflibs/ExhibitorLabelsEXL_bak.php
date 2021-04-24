<?php
jimport('excel.PHPExcel');
set_time_limit(5000);

$show = TOESHelper::getShowDetails($show_id);

$exhibitors = TOESHelper::getShowExhibitors($show_id);

// create new PDF document
$excel = new PHPExcel();

$excel->getProperties()
->setCreator("TICA")
->setLastModifiedBy("TICA")
->setTitle(JText::_('COM_TOES_EXHIBITOR_LABELS'));

$excel->setActiveSheetIndex(0);

$sheet = $excel->getActiveSheet();

$col = 0;
$row = 1;

$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(29);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(1))->setWidth(29);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(2))->setWidth(29);

$sheet->getRowDimension($row)->setRowHeight(65);

$i = 0;
foreach ($exhibitors as $exhibitor) {

	if($i != 0 && $i%3 == 0 )
	{
		$col = 0;
		$row++;
		$sheet->getRowDimension($row)->setRowHeight(65);		
	}
	
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('center');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->user_name );
	$i++;
}

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
	JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'exhibitorlabels.xls'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'exhibitorlabels.xls');

//Close and output PDF document
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$objWriter->save(TOES_PDF_PATH . DS . $show_id . DS . 'exhibitorlabels.xls');

//============================================================+
// END OF FILE
//============================================================+
