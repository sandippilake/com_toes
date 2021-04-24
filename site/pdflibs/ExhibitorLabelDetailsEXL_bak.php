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
	
	$exhibitor_text = "$exhibitor->user_name\n";
	$exhibitor_text .= "$exhibitor->summary_benching_request\n";
	$exhibitor_text .= JText::_('COM_TOES_SINGLE_SPACES').": $exhibitor->summary_single_cages / \n";
	$exhibitor_text .= JText::_('COM_TOES_DOUBLE_SPACES').": $exhibitor->summary_double_cages\n";
	$exhibitor_text .= JText::_('COM_TOES_GROOMING_SPACE').": $exhibitor->summary_grooming_space";
	
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('center');
	$sheet->setCellValueByColumnAndRow($col, $row, $exhibitor_text );
	$sheet->getStyleByColumnAndRow($col++, $row)->getAlignment()->setWrapText(true);
	$i++;
}

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
	JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'exhibitorlabeldetails.xls'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'exhibitorlabeldetails.xls');

//Close and output PDF document
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$objWriter->save(TOES_PDF_PATH . DS . $show_id . DS . 'exhibitorlabeldetails.xls');

//============================================================+
// END OF FILE
//============================================================+
