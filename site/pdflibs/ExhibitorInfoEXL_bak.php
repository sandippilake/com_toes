<?php
jimport('excel.PHPExcel');
set_time_limit(5000);

$db = JFactory::getDBO();

$show = TOESHelper::getShowDetails($show_id);

$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;
$whr[] = "`es`.`entry_status` IN ('Accepted','Confirmed','Confirmed & Paid')";

$query = TOESQueryHelper::getExhibitorListBasisQuery($whr);

$db->setQuery($query);
$exhibitors = $db->loadObjectList();

// create new PDF document
$excel = new PHPExcel();

$excel->getProperties()
        ->setCreator("TICA")
        ->setLastModifiedBy("TICA")
        ->setTitle(JText::_('COM_TOES_EXHIBITOR_INFORMATION_REPORT'));

$excel->setActiveSheetIndex(0);

$sheet = $excel->getActiveSheet();

$col = 0;
$row = 1;

$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(30);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(1))->setWidth(30);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(2))->setWidth(50);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(3))->setWidth(30);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(4))->setWidth(20);

$sheet->mergeCellsByColumnAndRow($col, $row, 4, $row);
$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col, $row++, JText::_('COM_TOES_EXHIBITOR_INFORMATION_REPORT'));

$sheet->mergeCellsByColumnAndRow(3, $row, 4, $row);
$sheet->getStyleByColumnAndRow(3, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('right');
$sheet->setCellValueByColumnAndRow(3, $row++, $show->Show_location);

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col, $row, $show->club_name);

$sheet->mergeCellsByColumnAndRow(3, $row, 4, $row);
$sheet->getStyleByColumnAndRow(3, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow(3, $row)->getAlignment()->setHorizontal('right');
$sheet->setCellValueByColumnAndRow(3, $row++, $show->show_dates);

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->mergeCellsByColumnAndRow($col, $row, 4, $row);
$row++;

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_EXHIBITOR') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_EMAIL') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_ADDRESS') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_CITY') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_COUNTRY') );
$row++;

foreach ($exhibitors as $exhibitor) {

	$col = 0;
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->Exhibitor );

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->email );

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->Address );

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->City );

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->Country );
	$row++;
}

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
    JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'exhibitor_info.xls'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'exhibitor_info.xls');

//Close and output PDF document
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$objWriter->save(TOES_PDF_PATH . DS . $show_id . DS . 'exhibitor_info.xls');

//============================================================+
// END OF FILE                                                
//============================================================+
