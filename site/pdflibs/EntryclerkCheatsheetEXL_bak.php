<?php
jimport('excel.PHPExcel');
set_time_limit(5000);

$db = JFactory::getDBO();

$time = time();
$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;

$query = TOESQueryHelper::getCatalogNumberingbasisQuery($whr);
$query->clear('order');
$query->order("`sd`.`show_day_id` ASC");
$query->order("`sc`.`show_class_id` ASC");
$query->order("`b`.`breed_name` ASC");
$query->order("`ctg`.`category_id` ASC");
$query->order("`dvs`.`division_id` ASC");
$query->order("`clr`.`color_id` ASC");
$query->order("`e`.`cat` ASC");

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
        ->setTitle(JText::_('COM_TOES_CHEAT_SHEET_REPORT'));

$excel->setActiveSheetIndex(0);

$sheet = $excel->getActiveSheet();


$row = 2;

$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(0))->setWidth(30);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(1))->setWidth(25);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(2))->setWidth(20);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(3))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(4))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(5))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex(6))->setWidth(10);

foreach($showday_entries as $show_day_id => $show_day_entries) {
	$col = 0;
	$sheet->mergeCellsByColumnAndRow($col, $row, 6, $row);
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(18);
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col, $row++, date('l Y-m-d', strtotime($show_days[$show_day_id]->show_day_date)));
	$row++;
	
	foreach($show_day_entries as $show_day_class => $show_day_class_entries) {
		$col = 0;
		$sheet->mergeCellsByColumnAndRow($col, $row, 6, $row);
		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->setCellValueByColumnAndRow($col, $row++, $show_day_class);
		$row++;
		
		$col = 0;
		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BREED') );

		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_NUMBERS') );

		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_COUNT') );

		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_REAL_COUNT') );

		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_FIRST') );

		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_SECOND') );

		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_THIRD') );
		$row++;

		$show_class_count = 0;
		
		foreach($show_day_class_entries as $show_day_class_breed => $show_day_class_breed_entries) {
			
			$catlog_numbers = $show_day_class_breed_entries['start_catalog_number'];
			
			if(isset($show_day_class_breed_entries['end_catalog_number']) && $show_day_class_breed_entries['end_catalog_number'] != $show_day_class_breed_entries['start_catalog_number']) {
				$catlog_numbers .= ' - '.$show_day_class_breed_entries['end_catalog_number'];
			}
			
			$col = 0;
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
			$sheet->setCellValueByColumnAndRow($col++, $row, $show_day_class_breed );

			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
			$sheet->setCellValueByColumnAndRow($col++, $row, $catlog_numbers );

			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
			$sheet->setCellValueByColumnAndRow($col++, $row, $show_day_class_breed_entries['count'] );

			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
			$sheet->setCellValueByColumnAndRow($col++, $row, '' );

			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
			$sheet->setCellValueByColumnAndRow($col++, $row, '' );

			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
			$sheet->setCellValueByColumnAndRow($col++, $row, '' );

			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
			$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
			$sheet->setCellValueByColumnAndRow($col++, $row, '' );
			$row++;
			
			$show_class_count += $show_day_class_breed_entries['count'];
		}
		
		$col = 0;
		$sheet->mergeCellsByColumnAndRow($col, $row, $col++, $row);
		$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_TOTAL') );

		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, $show_class_count );

		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row,  '' );

		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, '' );

		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, '' );

		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, '' );

		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('right');
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
		$sheet->setCellValueByColumnAndRow($col++, $row, '' );
		$row++;
		$row++;
	}
	$row++;
	
	$col = 0;
	$sheet->mergeCellsByColumnAndRow($col, $row, 6, $row);
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(18);
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col, $row++, date('l Y-m-d', strtotime($show_days[$show_day_id]->show_day_date)).' - '.JText::_('COM_TOES_EC_NUMBERS') );
	$row++;
	
	$col = 0;
	foreach($showclass_entries as $showday_id => $showclass_day_entries) {
		if($show_day_id != $showday_id) {
			continue;
		}
		
		$col = 0;
		foreach($showclass_day_entries as $show_class => $showclass_day_class_entries) {
			$col = 0;
			foreach($showclass_day_class_entries as $entry) {
				
				if($entry->gender_short_name == 'M' || $entry->gender_short_name == 'N') {
					$sheet->getStyleByColumnAndRow($col, $row)->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array( 'rgb' => '3333FF' ) ) ); 
				} else {
					$sheet->getStyleByColumnAndRow($col, $row)->getFill()->applyFromArray( array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array( 'rgb' => 'FF3399' ) ) ); 
				}

				$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
				$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
				$sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_number );
				
				if($col > 6) {
					$col = 0;
					$row++;
				}
			}
			
			$row++;
			$row++;
		}
	}
	
	$row++;
	$row++;
	
}

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
    JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'cheatsheet.xls'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'cheatsheet.xls');

//Close and output PDF document
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$objWriter->save(TOES_PDF_PATH . DS . $show_id . DS . 'cheatsheet.xls');

//============================================================+
// END OF FILE                                                
//============================================================+
