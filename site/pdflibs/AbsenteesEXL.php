<?php
jimport('excel.PHPExcel');
set_time_limit(5000);

$db = JFactory::getDBO();

$time = time();
$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;

$query = TOESQueryHelper::getCatalogNumberingbasisQuery($whr);
$query->group("`e`.`cat`");

$db->setQuery($query);
$entries = $db->loadObjectList();

$query = "SELECT `show_day_id`, `show_day_date` FROM `#__toes_show_day` WHERE `show_day_show` = {$show_id} ORDER BY `show_day_date`";
$db->setQuery($query);
$show_days = $db->loadObjectList('show_day_id');

$show = TOESHelper::getShowDetails($show_id);

$previous_day = '';
$previous_class = '';
$previous_breed = '';

$showday_entries = array();
$showclass_entries = array();
foreach ($entries as $entry) {
	
	if($previous_day != $entry->show_day || $previous_class != $entry->Show_Class || $previous_breed != $entry->breed_name) {
		$showday_entries[$entry->show_day][$entry->Show_Class][$entry->breed_name]['start_catalog_number'] = $entry->catalog_number;
		if($previous_day && $previous_class && $previous_breed) {
			$showday_entries[$previous_day][$previous_class][$previous_breed]['end_catalog_number'] = $previous_catalog_number;
			$showday_entries[$previous_day][$previous_class][$previous_breed]['count'] = $previous_breed_entries;
		}
		$previous_breed_entries = 0;
	}
	
	$showclass_entries[$entry->show_day][$entry->Show_Class][] = $entry;
	
	$previous_day = $entry->show_day;
	$previous_class = $entry->Show_Class;
	$previous_breed = $entry->breed_name;
	$previous_catalog_number = $entry->catalog_number;
	$previous_breed_entries++;
	
}
if($previous_day && $previous_class && $previous_breed) {
	$showday_entries[$previous_day][$previous_class][$previous_breed]['end_catalog_number'] = $previous_catalog_number;
	$showday_entries[$previous_day][$previous_class][$previous_breed]['count'] = $previous_breed_entries;
}

// create new PDF document
$excel = new PHPExcel();

$excel->getProperties()
        ->setCreator("TICA")
        ->setLastModifiedBy("TICA")
        ->setTitle(JText::_('COM_TOES_ABSENTEES_REPORT'));

$excel->setActiveSheetIndex(0);

$sheet = $excel->getActiveSheet();


$row = 2;

$col = 0;
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(5);

foreach ($show_days as $show_day) {
	$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(5);
}

$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(25);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(30);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(5);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(35);

		
$col = 0;
$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_NUMBER') );

foreach ($show_days as $show_day)
{
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, date('D',  strtotime($show_day->show_day_date)) );
}

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BREED') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_COLOR') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_SEX') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_ABSENTEES_CHANGES') );
$row++;

foreach ($entries as $entry) {

	$col = 0;
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_number );

	foreach ($show_days as $show_day)
	{
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, '' );
	}

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $entry->breed_name );

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $entry->color_name );

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, $entry->gender_short_name );

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, '' );
	$row++;
}

// ---------------------------------------------------------
/*
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
    JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'absentees.xls'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'absentees.xls');
*/
//Close and output PDF document
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
//$objWriter->save(TOES_PDF_PATH . DS . $show_id . DS . 'absentees.xls');
header('Content-type: application/vnd.ms-excel');

// It will be called file.xls
header('Content-Disposition: attachment; filename="'.$show_id.'_absentees.xls"');
$objWriter->save('php://output');

//============================================================+
// END OF FILE                                                
//============================================================+
