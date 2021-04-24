<?php
jimport('excel.PHPExcel');
set_time_limit(5000);

$db = JFactory::getDBO();

// create new PDF document
$excel = new PHPExcel();

$excel->getProperties()
        ->setCreator("TICA")
        ->setLastModifiedBy("TICA")
        ->setTitle(JText::_('COM_TOES_TREASURER'));

$excel->setActiveSheetIndex(0);

$sheet = $excel->getActiveSheet();

$show = TOESHelper::getShowDetails($show_id);
$show_days = TOESHelper::getShowDays($show_id);
$club = TOESHelper::getClub($show_id);

$whr = array();
$whr[] = '`s`.`summary_show` = '.$show_id;
$query = TOESQueryHelper::getSummaryAndEntriesPerDayPerExhibitorQuery($whr);
$db->setQuery($query);
$exhibitors = $db->loadObjectList();

$whr = array();
$whr[] = '`p`.`placeholder_show` = '.$show_id;
$query = TOESQueryHelper::getPlaceholdersPerDayPerExhibitorQuery($whr);
$db->setQuery($query);
$placeholders = $db->loadObjectList();

$temp_exhibitors = array();
$exhibitor_show_day_count = array();
$show_days_count = array();
$exhibitor_placeholder_show_day_count = array();
$placeholder_show_days_count = array();

foreach ($exhibitors as $exhibitor) {
	$temp_exhibitors [$exhibitor->summary_user] = $exhibitor;
	if(isset($exhibitor_show_day_count[$exhibitor->summary_user][$exhibitor->show_day]))
		$exhibitor_show_day_count[$exhibitor->summary_user][$exhibitor->show_day] += $exhibitor->entries_per_day;
	else
		$exhibitor_show_day_count[$exhibitor->summary_user][$exhibitor->show_day] = $exhibitor->entries_per_day;

	if(isset($show_days_count[$exhibitor->show_day]))
		$show_days_count[$exhibitor->show_day] += $exhibitor->entries_per_day;
	else
		$show_days_count[$exhibitor->show_day] = $exhibitor->entries_per_day;

}

foreach ($placeholders as $placeholder) {
	
	if(!isset($temp_exhibitors[$placeholder->placeholder_exhibitor])) {
		$temp_exhibitors[$placeholder->placeholder_exhibitor] = $placeholder;
	}

	if(isset($exhibitor_placeholder_show_day_count[$placeholder->placeholder_exhibitor][$placeholder->placeholder_day_showday]))
		$exhibitor_placeholder_show_day_count[$placeholder->placeholder_exhibitor][$placeholder->placeholder_day_showday] += $placeholder->placeholders_per_day;
	else
		$exhibitor_placeholder_show_day_count[$placeholder->placeholder_exhibitor][$placeholder->placeholder_day_showday] = $placeholder->placeholders_per_day;

	if(isset($placeholder_show_days_count[$placeholder->placeholder_day_showday]))
		$placeholder_show_days_count[$placeholder->placeholder_day_showday] += $placeholder->placeholders_per_day;
	else
		$placeholder_show_days_count[$placeholder->placeholder_day_showday] = $placeholder->placeholders_per_day;

}
$exhibitors = $temp_exhibitors;

$col = 0;
$row = 1;

$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(30);
foreach ($show_days as $show_day)
	$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(10);
foreach ($show_days as $show_day)
	$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(15);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(15);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(15);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(10);
$sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(50);

$col = 0;
$row = 1;


$total_cols = 10 + (count($show_days)*2);


$sheet->mergeCellsByColumnAndRow($col, $row, $total_cols-1, $row);
$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col, $row++, JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT'));

$sheet->mergeCellsByColumnAndRow($total_cols-2, $row, $total_cols-1, $row);
$sheet->getStyleByColumnAndRow($total_cols-2, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($total_cols-2, $row)->getAlignment()->setHorizontal('right');
$sheet->setCellValueByColumnAndRow($total_cols-2, $row++, $show->Show_location);

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col, $row, $show->club_name);

$sheet->mergeCellsByColumnAndRow($total_cols-2, $row, $total_cols-1, $row);
$sheet->getStyleByColumnAndRow($total_cols-2, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($total_cols-2, $row)->getAlignment()->setHorizontal('right');
$sheet->setCellValueByColumnAndRow($total_cols-2, $row++, $show->show_dates);

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->mergeCellsByColumnAndRow($col, $row, $total_cols-1, $row);
$row++;

$col=0;

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, '' );

$sheet->mergeCellsByColumnAndRow($col, $row, $col+count($show_days)-1, $row);
$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('NUMBER_OF_ENTRIES') );

$col = $col+count($show_days)-1;

$sheet->mergeCellsByColumnAndRow($col, $row, $col+count($show_days)-1, $row);
$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('NUMBER_OF_PLACEHOLDERS') );

$col = $col+count($show_days)-1;

$sheet->mergeCellsByColumnAndRow($col, $row, $col+1, $row);
$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('CAGES_PLACES') );
$col++;

$col = 0;
$row++;

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('EXHIBITOR') );

foreach ($show_days as $show_day)
{
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
	$sheet->setCellValueByColumnAndRow($col++, $row, date('D',  strtotime($show_day->show_day_date)) );
}

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
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('SINGLE') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('DOUBLE') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_SHOW_SUMMARY_PERSONAL_CAGES') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_SHOW_SUMMARY_BENCHING_REQUEST') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_SHOW_SUMMARY_GROOMING_SPACE') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('TOTAL_FEES') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('TOTAL_PAID') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('TOTAL_DUE') );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_PRIVATE_NOTE_TITLE') );
$row++;

$i = 0;

$summary_single_cages_sum = 0;
$summary_double_cages_sum = 0;

$summary_total_fees_sum = 0;
$summary_fees_paid_sum = 0;

foreach ($exhibitors as $exhibitor) {

	$col = 0;
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->exhibitor );

	foreach ($show_days as $show_day)
	{
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->setCellValueByColumnAndRow($col++, $row, (isset($exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-') );
	}

	foreach ($show_days as $show_day)
	{
		$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
		$sheet->setCellValueByColumnAndRow($col++, $row, (isset($exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-') );
	}

	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->summary_single_cages );
	
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->summary_double_cages );
	
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col++, $row, ($exhibitor->summary_personal_cages ? JText::_('JYES') : JText::_('JNO')) );
	
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->summary_benching_area );
	
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col++, $row, ($exhibitor->summary_grooming_space ? JText::_('JYES') : JText::_('JNO')) );
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->summary_total_fees );
	$sheet->setCellValueByColumnAndRow($col++, $row, $exhibitor->summary_fees_paid );
	$sheet->setCellValueByColumnAndRow($col++, $row, ($exhibitor->summary_total_fees - $exhibitor->summary_fees_paid) );
	$sheet->setCellValueByColumnAndRow($col++, $row, ($exhibitor->summary_entry_clerk_private_note) );

	$summary_single_cages_sum += ($exhibitor->summary_single_cages ? $exhibitor->summary_single_cages : 0);
	$summary_double_cages_sum += ($exhibitor->summary_double_cages ? $exhibitor->summary_double_cages : 0);
	
	$summary_total_fees_sum += ($exhibitor->summary_total_fees ? $exhibitor->summary_total_fees : 0);
	$summary_fees_paid_sum += ($exhibitor->summary_fees_paid ? $exhibitor->summary_fees_paid : 0);
	
	
	$row++;
}

$col =0;

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COUNT') );

foreach ($show_days as $show_day)
{
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col++, $row, isset($show_days_count[$show_day->show_day_id])?$show_days_count[$show_day->show_day_id]:'0' );
}

foreach ($show_days as $show_day)
{
	$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
	$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
	$sheet->setCellValueByColumnAndRow($col++, $row, isset($placeholder_show_days_count[$show_day->show_day_id])?$placeholder_show_days_count[$show_day->show_day_id]:'0' );
}

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col++, $row, $summary_single_cages_sum );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col++, $row, $summary_double_cages_sum );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col++, $row, '-' );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col++, $row, '-' );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col++, $row, '-' );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->setCellValueByColumnAndRow($col++, $row, $summary_total_fees_sum );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->setCellValueByColumnAndRow($col++, $row, $summary_fees_paid_sum );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->setCellValueByColumnAndRow($col++, $row, ($summary_total_fees_sum - $summary_fees_paid_sum) );

$sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
$sheet->setCellValueByColumnAndRow($col++, $row, '-' );


// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
    JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'treasurer.xls'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'treasurer.xls');

//Close and output PDF document
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$objWriter->save(TOES_PDF_PATH . DS . $show_id . DS . 'treasurer.xls');

//============================================================+
// END OF FILE                                                
//============================================================+
