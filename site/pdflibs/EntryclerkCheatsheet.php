<?php
jimport('tcpdf.tcpdf');
set_time_limit(5000);
error_reporting(E_ALL);
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

if ($show->page_ortientation == 'Automatic') {
	if (count($show_days) == 3)
		$page_orientation = 'L';
	else
		$page_orientation = PDF_PAGE_ORIENTATION;
}
else if ($show->page_ortientation == 'Landscape')
	$page_orientation = 'L';
else
	$page_orientation = PDF_PAGE_ORIENTATION;

$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format ? $page_format : PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new TCPDF($page_orientation, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_CHEAT_SHEET_REPORT'));

$params = JComponentHelper::getParams('com_toes');

$font_size = ($show->show_catalog_font_size) ? (int) $show->show_catalog_font_size : 10;
$pdf->font_size = $font_size;

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 5, 10);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
//$pdf->setLanguageArray($l);

$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);

// ---------------------------------------------------------

$previous_day = '';
$previous_class = '';
$previous_breed = '';

$previous_breed_entries = 1;
$previous_catalog_number = 0;

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

foreach($showday_entries as $show_day_id => $show_day_entries) {
	// add a page
	$pdf->AddPage();

	$block = '<div style="text-align:center;text-transform:uppercase;">'.date('l Y-m-d', strtotime($show_days[$show_day_id]->show_day_date)).'</div><br/>';
	$pdf->SetFont('ptsanscaption', '', $font_size + 6);
	$pdf->writeHTML($block, true, false, false, false, '');

	foreach($show_day_entries as $show_day_class => $show_day_class_entries) {

		$show_class_block = '<div style="text-align:center;font-weight:bold;">'.$show_day_class.'</div>';

		$table_block = '<table width="100%">
							<tr>
								<td style="width:30%;text-align:center;border:1px solid #999;">'.JText::_('COM_TOES_BREED').'</td>
								<td style="width:20%;text-align:center;border:1px solid #999;">'.JText::_('COM_TOES_NUMBERS').'</td>
								<td style="width:10%;text-align:center;border:1px solid #999;">'.JText::_('COM_TOES_COUNT').'</td>
								<td style="width:10%;text-align:center;border:1px solid #999;">'.JText::_('COM_TOES_REAL_COUNT').'</td>
								<td style="width:10%;text-align:center;border:1px solid #999;">'.JText::_('COM_TOES_FIRST').'</td>
								<td style="width:10%;text-align:center;border:1px solid #999;">'.JText::_('COM_TOES_SECOND').'</td>
								<td style="width:10%;text-align:center;border:1px solid #999;">'.JText::_('COM_TOES_THIRD').'</td>
							</tr>
						';
			
		$show_class_count = 0;
		
		foreach($show_day_class_entries as $show_day_class_breed => $show_day_class_breed_entries) {
			
			$catlog_numbers = $show_day_class_breed_entries['start_catalog_number'];
			
			if(isset($show_day_class_breed_entries['end_catalog_number']) && $show_day_class_breed_entries['end_catalog_number'] != $show_day_class_breed_entries['start_catalog_number']) {
				$catlog_numbers .= ' - '.$show_day_class_breed_entries['end_catalog_number'];
			}
			
			$table_block .= '<tr>
								<td style="text-align:left;border:1px solid #999;">&nbsp;&nbsp;'.$show_day_class_breed.'</td>
								<td style="text-align:right;border:1px solid #999;">'.$catlog_numbers.'&nbsp;&nbsp;</td>
								<td style="text-align:right;border:1px solid #999;">'.$show_day_class_breed_entries['count'].'&nbsp;&nbsp;</td>
								<td style="text-align:right;border:1px solid #999;">&nbsp;</td>
								<td style="text-align:right;border:1px solid #999;">&nbsp;</td>
								<td style="text-align:right;border:1px solid #999;">&nbsp;</td>
								<td style="text-align:right;border:1px solid #999;">&nbsp;</td>
							</tr>
						';
			
			$show_class_count += $show_day_class_breed_entries['count'];
		}
		
		$table_block .= '<tr>
							<td style="text-align:center;border:1px solid #999;" colspan="2">'.JText::_('COM_TOES_TOTAL').'</td>
							<td style="text-align:right;border:1px solid #999;">'.$show_class_count.'&nbsp;&nbsp;</td>
							<td style="text-align:right;border:1px solid #999;">&nbsp;</td>
							<td style="text-align:right;border:1px solid #999;" colspan="3">&nbsp;</td>
						</tr>
					</table><br/><br/>
					';
		
		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {

			$pdf->SetFont('ptsanscaption', '', $font_size + 3);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');

			$pdf->SetFont('ptsansnarrow', '', $font_size);
			$pdf->writeHTML($table_block, true, false, false, false, '');

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				$print_block = 0;
			} else {
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
	}
	
	$catalog_numbers_block = '';
	$width = 0;
	$margins = $pdf->getMargins();
	$margin_to_count = $margins['left'] + $margins['right'];
	
	$pdf->ln(2);
	
	$block = '<div style="text-align:center;text-transform:uppercase;">'.date('l Y-m-d', strtotime($show_days[$show_day_id]->show_day_date)).' - '.JText::_('COM_TOES_EC_NUMBERS').'</div><br/>';
	$pdf->SetFont('ptsanscaption', '', $font_size + 6);
	$pdf->writeHTML($block, true, false, false, false, '');
	
	$pdf->ln(2);
	$pdf->SetFont('ptsansnarrow', '', $font_size + 4);	
	foreach($showclass_entries as $showday_id => $showclass_day_entries) {
		if($show_day_id != $showday_id) {
			continue;
		}
		
		foreach($showclass_day_entries as $show_class => $showclass_day_class_entries) {
			$width = 0;
			foreach($showclass_day_class_entries as $entry) {
				if($entry->gender_short_name == 'M' || $entry->gender_short_name == 'N') {
					$pdf->SetFillColor(51, 51, 255);
				} else {
					$pdf->SetFillColor(255, 51, 153);
				}

				$pdf->cell(10, 5, $entry->catalog_number,0, 0, 'C', true);
				$pdf->cell(5,5,'');
				$width += 15;

				if($width + $margin_to_count > $pdf->getPageWidth()) {
					$pdf->ln();
					$pdf->ln();
					$width = 0;
				}
			}
			$pdf->ln();
			$pdf->ln();
			$catalog_numbers_block .= '<br/>';
		}
	}
}

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
/*
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
	JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod(TOES_PDF_PATH . DS . $show_id, 0777);

if (file_exists(TOES_PDF_PATH . DS . $show_id . DS . 'cheatsheet.pdf'))
	unlink(TOES_PDF_PATH . DS . $show_id . DS . 'cheatsheet.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'cheatsheet.pdf', 'F');
*/
$pdf->Output( $show_id . '_cheatsheet.pdf', 'I');
//============================================================+
// END OF FILE
//============================================================+
