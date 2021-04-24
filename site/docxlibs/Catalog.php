<?php

jimport('phpword.Autoloader');

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

Autoloader::register();
Settings::loadConfig();

$writers = array('Word2007' => 'docx');
$params = JComponentHelper::getParams('com_toes');

function write($phpWord, $writers, $show_id) {
	$result = '';

	// Write documents
	foreach ($writers as $writer => $extension) {
		if (!is_null($extension)) {
			$xmlWriter = IOFactory::createWriter($phpWord, $writer);
			$xmlWriter->save(JPATH_BASE . "/media/com_toes/DOCX/" . $show_id . "/catalog.docx");
		} else {
			echo "0";
		}
	}
	return;
}

$phpWord = new \PhpOffice\PhpWord\PhpWord();
$phpWord->addFontStyle('rStyle', array('bold' => true, 'italic' => true, 'size' => 16, 'allCaps' => true, 'doubleStrikethrough' => true));
//$phpWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceAfter' => 100));
$phpWord->addTitleStyle(1, array('bold' => true), array('spaceAfter' => 240));

$db = JFactory::getDbo();
$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();
$page_format = $page_format ? $page_format : 'A4';

$sectionSettings = array(
	'orientation' => 'landscape',
	//'pageSizeW'=>308.4,
	//'pageSizeH'=>487.04,
	'paper' => $page_format,
	'marginLeft' => 100,
	'marginRight' => 100,
	'pageNumberingStart' => 1,
	'name' => 'ptsanscaption'
);

$section = $phpWord->createSection($sectionSettings);

$skip_division_best = array(
	'LH PNB',
	'SH PNB',
	'LH ANB',
	'SH ANB',
	'LH HHP Kitten',
	'SH HHP Kitten',
	'Ex Only',
	'For Sale'
);

$skip_breed_best = array(
	'LH HHP',
	'SH HHP',
	'LH HHP Kitten',
	'SH HHP Kitten',
	'Ex Only',
	'For Sale'
);

$file = JRequest::getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Process started";
$data = array(
	'total' => '?',
	'processed' => '?',
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

$db = JFactory::getDBO();

$time = time();
$whr = array();
$whr[] = '`e`.`late_entry` = 0';
$whr[] = '`e`.`entry_show` = '.$show_id;

$query = TOESQueryHelper::getCatalogNumberingbasisQuery($whr);

$db->setQuery($query);
$entries = $db->loadObjectList();

$query = "SELECT `show_day_id`, `show_day_date` FROM `#__toes_show_day` WHERE `show_day_show` = {$show_id} ORDER BY `show_day_date`";
$db->setQuery($query);
$show_days = $db->loadObjectList();

$query = "SELECT `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation`, `rf`.`ring_format` AS format 
FROM `#__toes_ring` AS `r`
LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`) 
LEFT JOIN `#__toes_ring_format` AS `rf` ON `rf`.`ring_format_id` = `r`.`ring_format` 
WHERE `r`.`ring_show` = {$show_id} AND ((`r`.`ring_format` = 1) OR (`r`.`ring_format` = 2))
ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`;
";
$db->setQuery($query);
$rings = $db->loadObjectList();

foreach ($rings as $ring) {
	$show_day_rings[$ring->ring_show_day][] = $ring;
}

$show = TOESHelper::getShowDetails($show_id);

$lineStyle = array('weight' => 1, 'width' => 1050, 'height' => 0, 'color' => 'b2a68b');

$footer = $section->createFooter();
$footer->addLine($lineStyle);
$show = TOESHelper::getShowDetails($show_id);
if ($show) {
	$text = $show->club_name . ' - ' . $show->Show_location . ' - ' . $show->show_dates;
} else
	$text = '';
$footer->addText($text, array('size' => 10, 'italic' => true));


$is_continuous = ($show->show_format == 'Continuous') ? true : false;
$is_alernative = ($show->show_format == 'Alternative') ? true : false;


$previous_class = '';
$previous_breed = '';
$previous_division = '';
$previous_color = '';
$previous_catalog_number = '';

$previous_breed_entries = 1;
$previous_division_entries = 1;

$show_day_entries = array();
$catalog_numbers = array();

$temp_entries = array();
$showday_entries = array();
foreach ($entries as $entry) {
	$showday_entries[$entry->show_day][$entry->catalog_number] = $entry;
	$show_day_entries[$entry->show_day][$entry->catalog_number] = $entry->catalog_number;
	if (!in_array($entry->catalog_number, $catalog_numbers)) {
		$catalog_numbers[] = $entry->catalog_number;
		$temp_entries[] = $entry;
	}
}
$entries = $temp_entries;
$cur = 0;
$total = count($catalog_numbers);
$processed = 0;
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);


$is_continuous = ($show->show_format == 'Continuous') ? true : false;
$is_alernative = ($show->show_format == 'Alternative') ? true : false;

$breed_title_color = $division_title_color = $bod_color = $bob_color = '#000000';

if (isset($show->show_colored_catalog)) {
	if ($params->get('breed_title_color'))
		$breed_title_color = $params->get('breed_title_color');
	if ($params->get('division_title_color'))
		$division_title_color = $params->get('division_title_color');
	if ($params->get('bod_color'))
		$bod_color = $params->get('bod_color');
	if ($params->get('bob_color'))
		$bob_color = $params->get('bob_color');
}
$flag = 0;
$k = 0;
$available_classes = array();
if (count($show_days) == 2) {
//$html="<strong>vaishali</strong>";	
//\PhpOffice\PhpWord\Shared\Html::addHtml($section, $html);

	foreach ($entries as $entry) {

		//echo $sectionid= $section->getSectionId();
		//echo "<br/>";

		if ($previous_class != $entry->show_class) {
			//	echo $k.' ';

			$k = 0;
			$kflag = false;
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;

			if ($previous_class != '')
				$section->addPageBreak();


			//set header 
			$section = $phpWord->createSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 200, 'marginBottom' => 200, 'headerHeight' => 100, 'footerHeight' => 500, 'orientation' => 'landscape'));
			$section->addTitle('');
			//$section->getStyle()->setPageNumberingStart(1);
			$newheader = $section->createHeader();
			//$newheader->addText('vaishali'.$sectionid,array('size'=>18));
			$fontStyleTitle = array('size' => 16, 'bold' => true);
			$fontStyleSubTitle = array('size' => 10, 'bold' => false);
			$paragraphStyleTitle = array('spaceBefore' => 0);
			$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF');
			$lineStyle = array('weight' => 1, 'width' => 710, 'height' => 10, 'color' => '#FFFFFF');
			$newheader->addLine($lineStyle);
			$newheadertable = $newheader->addTable($styleTable);

			$newheadertable->addRow(200);
			$logo = JURI::root() . 'media/com_toes/images/paw32X32.png';
			$newheadertable->addCell(500)->addImage($logo, array('align' => 'left'));


			//$table->addCell(4000)->addText(JText::_('COM_TOES_EXHIBITORLIST_HEADER').' '.JText::_('COM_TOES_SITE_TITLE'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));

			$nhcell1 = $newheadertable->addCell(6000);
			$nhcell1->addText(JText::_('COM_TOES_CATALOG'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
			$nhcell1->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
			$nhcell2 = $newheadertable->addCell(8000);
			$nhcell2->addText(strtoupper($entry->show_class), array('size' => 18, 'bold' => true));
			/* $newheadertable->addRow();
			  $newheadertable->addCell(9000,array('colspan'=>2))->addText('___',array('width'=>'9000')); */
			$newheader->addText();
			$lineStyle = array('weight' => 1, 'width' => 1050, 'height' => 10, 'color' => '#000000');
			$newheader->addLine($lineStyle);


			//judge_block
			if ($entry->show_class != 'Ex Only') {
				$tablestyle = array('borderSize' => 0, 'borderColor' => 'FFFFFF');
				$newheadertable2 = $newheader->addTable();
				$newheadertable2->addRow(100);

				if ($show->show_format != 'Alternative') {
					//	echo count($show_day_rings[$show_days[0]->show_day_id]);die;
					$cell1 = $newheadertable2->addCell(5000, array('gridSpan' => count($show_day_rings[$show_days[0]->show_day_id])));
					//$cell1->addTextRun();
					$cell1->addText(($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[0]->show_day_date))));
					$newheadertable2->addCell(7000, array('gridSpan' => 1))->addText(' ');
					$cell2 = $newheadertable2->addCell(5000, array('gridSpan' => count($show_day_rings[$show_days[1]->show_day_id])));
					//$cell2->addTextRun();
					$cell2->addText((($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[1]->show_day_date)))));
					$newheadertable2->addRow(100);
					$judge_block = '';


					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {

						$newheadertable2->addCell(800)->addText($first_show_day->judge_abbreviation);
					}
				} else {
					$newheadertable2->addCell(7000, array('gridSpan' => count($show_day_rings[$show_days[0]->show_day_id]), 'align' => 'left'))->addText((count($session_rings[$show_days[0]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM' : ' '));

					$newheadertable2->addCell(7000, array('gridSpan' => count($show_day_rings[$show_days[0]->show_day_id]), 'align' => 'right'))->addText((count($session_rings[$show_days[0]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM' : '  '));
					$newheadertable2->addRow(100);

					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {

						$newheadertable2->addCell(800)->addText((($first_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $first_show_day->judge_abbreviation);
					}
				}
				$newheadertable2->addCell(7000, array('align' => 'center', 'gridSpan' => 1))->addText();


				if ($show->show_format != 'Alternative') {

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$newheadertable2->addCell(800)->addText($second_show_day->judge_abbreviation);
					}
				} else {

					$newheadertable2->addCell(6000, array('colspan' => count($show_day_rings[$show_days[1]->show_day_id]), 'align' => 'left'))->addText((count($session_rings[$show_days[1]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM' : '&nbsp;'));
					$newheadertable2->addCell(6000, array('colspan' => count($show_day_rings[$show_days[1]->show_day_id]), 'align' => 'right'))->addText((count($session_rings[$show_days[1]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM' : '&nbsp;'));
					$newheadertable2->addRow(100);

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$newheadertable2->addCell(800, array('colspan' => (($second_show_day->ring_timing == 1) ? 'left' : 'right')))->addText($second_show_day->judge_abbreviation);
					}
				}
			}
			if ($previous_class != $entry->show_class) {

				$previous_class = $entry->show_class;
				$previous_breed = '';
				$previous_division = '';
				$previous_color = '';

				$log = "Processing " . $entry->show_class . ".... ";
			}
		}
		//$k++;
		$table = $section->addTable();

		if ($previous_catalog_number != $entry->catalog_number) {
			$table->addRow(500);

			$entry_block = '';
			if ($entry->show_class != 'Ex Only') {
				foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
					if ($show->show_format != 'Alternative') {
						if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number]))
							$table->addCell(800, array('align' => 'right'))->addText('___');
						else
							$table->addCell(800, array('valign' => 'top'))->addText(' ');
					}
					else {
						if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number])) {
							if (($first_show_day->ring_timing == 1 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($first_show_day->ring_timing == 2 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_PM))
								$table->addCell(800, array('align' => 'right'))->addText('___');
							else
								$table->addCell(800, array('align' => (($first_show_day->ring_timing == 1) ? 'left' : 'right'), ' valign' => "top"))->addText(' ');
						} else
							$table->addCell(800, array('align' => (($first_show_day->ring_timing == 1) ? 'left' : 'right'), ' valign' => "top"))->addText(' ');
					}
				}
			}
			else {
				$table->addCell(4500, array('valign' => 'top', 'gridSpan' => count($show_day_rings[$show_days[0]->show_day_id])))->addText(' ');
			}

			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');


			/* 	$cell3=$table->addCell(700);


			  $entry_block=  $entry->catalog_number . '  '.
			  ( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name) ) . ' '
			  . $entry->catalog_age_and_gender . ' '
			  . $entry->catalog_number ;
			  $cell3->addText($entry_block);
			  if (trim($entry->catalog_registration_number) == '')
			  $reg_number = JText::_('PENDING');
			  else
			  $reg_number = $entry->catalog_registration_number;

			  $cell3->addText($reg_number . '   ' . JText::_('COM_TOES_CATALOG_BORN') . ' ' . $entry->catalog_birthdate,array('align'=>'left') );

			  if ($isNotHHP) {
			  $cell3->addText(strtoupper($entry->catalog_sire),array('align'=>'left') );

			  $cell3->addText( strtoupper($entry->catalog_dam));
			  }
			  $entry_block =  $entry->catalog_region . '
			  ' .
			  ($entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '<br/>' : '')
			  . ($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '<br/>' : '')
			  . ($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '<br/>' : '')
			  . ($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '')
			  . ' ' . $entry->catalog_region ;

			  $cell3->addText($entry_block);
			  $table->addRow(100);
			  //$cell4=$table->addCell(700);
			  if ($entry->show_class != 'Ex Only') {
			  foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
			  if ($show->show_format != 'Alternative') {
			  if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number]))
			  $table->addCell(700,array('align'=>'right','valign'=>'top'))->addText('___');
			  else
			  $table->addCell(700,array('align'=>'right','valign'=>'top'))->addText(' ');

			  }
			  else {
			  if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number])) {
			  if (($second_show_day->ring_timing == 1 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($second_show_day->ring_timing == 2 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_PM))
			  $table->addCell(700,array('align'=>(($second_show_day->ring_timing == 1) ? 'left' : 'right'),'valign'=>'top'))->addText('___');
			  else
			  $table->addCell(700,array('align'=>(($second_show_day->ring_timing == 1) ? 'left' : 'right') ))->addText(' ');
			  } else
			  $table->addCell(700,array('align'=>(($second_show_day->ring_timing == 1) ? 'left' : 'right') ))->addText(' ');

			  }
			  }
			  } else
			  $table->addCell(700)->addText(' '); */
		}

		$cell3 = $table->addCell(7000, array('align' => "center", 'valign' => "top", 'rowspan' => ($isNotHHP ? 4 : 2)));
		$cell3table = $cell3->addTable();
		//$cell3table->addRow(500,array('align'=>"center", 'valign'=>"center"));

		$print_block = 2;
		while ($print_block > 1) {

			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$previous_breed = $entry->breed_name;
					$previous_breed_entries = 1;
					$previous_division_entries = 1;
					$cell3table->addRow(500, array('align' => "center", 'valign' => "center"));
					$cell3table->addCell(7000, array('colspan' => 4))->addText(strtoupper($entry->breed_name), array('valign' => 'center', 'align' => 'center', 'bold' => true, 'size' => 12, 'underline' => 'single'), array('align' => "center", 'valign' => "center"));
				} else
					$previous_breed_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
					$previous_division_entries = 1;
					if ($previous_breed != $entry->breed_name)
						$previous_breed = $entry->breed_name;
					if ($previous_division != $entry->catalog_division)
						$previous_division = $entry->catalog_division;
					$cell3table->addRow(500, array('align' => "center", 'valign' => "center"));
					$cell3table->addCell(7000, array('colspan' => 4))->addText(strtoupper($entry->catalog_division), array('align' => 'center', 'valign' => 'center', 'bold' => false, 'size' => 12, 'underline' => 'single'), array('align' => "center", 'valign' => "center"));
				} else
					$previous_division_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {

					$previous_division = $entry->catalog_division;
					$cell3table->addRow(500, array('align' => "center", 'valign' => "center"));

					$cell3table->addCell(7000, array('colspan' => 4))->addText(strtoupper($entry->color_name), array('align' => 'center', 'valign' => 'center', 'bold' => true, 'size' => 12), array('align' => "center", 'valign' => "center"));
				}
			}
			--$print_block;
		}
		//$cell3->addTextRun(array('valign'=>'center'));
		$cell3table->addRow(300);

		/*  $cell3table->addCell(800)->addText('v');
		  //	$cell3table->addCell(4000)->addText('vaishali');
		  $cell3table->addCell(800)->addText('D');
		  $cell3table->addCell(800)->addText('B'); */
		$cell31 = $cell3table->addCell(800);
		$cell32 = $cell3table->addCell(4500, array('align' => "center", 'valign' => "top"));
		$cell33 = $cell3table->addCell(800);
		$cell34 = $cell3table->addCell(800);

		//var_dump($entry);die;
		if (isset($show->show_catalog_cat_names_bold))
			$catnamestyle = array('bold' => true, 'size' => 10);
		else
			$catnamestyle = array('bold' => false, 'size' => 10);
		//$cell32->addText(  strtoupper($entry->catalog_cat_name),$catnamestyle  );

		if (trim($entry->catalog_registration_number) == '')
			$reg_number = JText::_('PENDING');
		else
			$reg_number = $entry->catalog_registration_number;
		//$table->addRow(100);

		$cell32->addText(strtoupper($entry->catalog_cat_name), array('align' => 'left'));
		$cell32->addText($reg_number . '   ' . JText::_('COM_TOES_CATALOG_BORN') . ' ' . $entry->catalog_birthdate);


		if ($isNotHHP) {
			$cell32->addText(strtoupper($entry->catalog_sire), array('align' => "left"));
			$cell32->addText(strtoupper($entry->catalog_dam), array('align' => "left"));
		}



		$cell31->addText($entry->catalog_number, array('valign' => 'top', 'align' => 'left', 'spaceAfter' => 500, 'height' => 500));
		$cell31->addText(' ');

		//$cell32->addText(( ($show->show_catalog_cat_names_bold) ? (  strtoupper($entry->catalog_cat_name) ) : strtoupper($entry->catalog_cat_name) ),array('valign'=>'top','align'=>'left'));
		//	$cell32->addText(strtoupper($entry->catalog_cat_name),array('align'=>'left'));
		$cell33->addText($entry->catalog_age_and_gender, array('align' => 'right', 'valign' => 'top'));
		$cell33->addText(' ');
		$cell34->addText($entry->catalog_number, array('align' => 'right', 'valign' => 'top'));
		/* $entry_block = '<tr>
		  <td width="9%" align="left" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_number . '</td>
		  <td width="69%" align="left" >' . ( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name) ) . '</td>
		  <td width="13%" align="right" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_age_and_gender . '</td>
		  <td width="9%" align="right" valign="top" rowspan="' . ($isNotHHP ? 5 : 3) . '">' . $entry->catalog_number . '</td>
		  </tr>'; */

		$cell3table->addRow(300);
		$cell41 = $cell3table->addCell(800);
		$cell42 = $cell3table->addCell(4500, array('align' => "center", 'valign' => "center"));
		$cell43 = $cell3table->addCell(800);
		$cell44 = $cell3table->addCell(800);

		$cell41->addText($entry->catalog_region);
		$cell42->addText(($entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '' : ''));

		$cell42->addText(($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '' : ''));
		$cell42->addText($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '' : '');
		$cell42->addText($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '');

		$cell43->addText($entry->catalog_region);

		if ($entry->show_class != 'Ex Only') {

			foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
				if ($show->show_format != 'Alternative') {

					if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number]))
						$table->addCell(500)->addText('___');
					else
						$table->addCell(500)->addText(' ');
				}
				else {
					if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number])) {

						if (($second_show_day->ring_timing == 1 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($second_show_day->ring_timing == 2 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_PM))
							$table->addCell(500)->addText('___');
						else
							$table->addCell(500)->addText(' ');
					} else
						$table->addCell(500)->addText(' ');
				}
			}
		}



		//}


		$section->addPageBreak();
		$print_block = 2; // 2 tries max
		while ($print_block > 1) {
			//$table->addRow(100);
			//if ($previous_catalog_number != $entry->catalog_number) {
			/* 	if ($previous_breed != $entry->breed_name) {
			  $previous_breed_entries = 1;
			  $previous_division_entries = 1;
			  $breed_block = '<div style="text-align:center; font-weight:bold;color:' . $breed_title_color . '; text-decoration:underline;">' . strtoupper($entry->breed_name) . '</div>';
			  $cell->addText(strtoupper($entry->breed_name));


			  } else
			  $previous_breed_entries++;

			  if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
			  $previous_division_entries = 1;

			  $breed_block = '<div style="text-align:center;font-weight:bold;color:' . $division_title_color . '; text-decoration:underline;">' . htmlentities(strtoupper($entry->catalog_division)) . '</div>';
			  //echo $breed_block;die;
			  $cell->addText(strtoupper($entry->catalog_division) );

			  } else
			  $previous_division_entries++;

			  if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
			  $breed_block = '<div style="text-align:center; font-weight:bold;">' . strtoupper($entry->color_name) . '</div>';
			  $cell->addText(strtoupper($entry->color_name) );

			  } */

			/* $pdf->SetFont('ptsansnarrow', '', $font_size);
			  $pdf->writeHTML($entry_block, true, false, false, false, ''); */
			//}

			--$print_block;

			$next = $cur + 1;
			if (in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($entries) ||
				( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
				$best_division_block = '<table width="100%">
                                    ';
				//$table->addRow(100);
				$kflag = true;
				for ($i = 1; $i <= $previous_division_entries; $i++) {

					$table->addRow(300);
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {

						$table->addCell(800, array('align' => (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right')))->addText('___');
					}

					if (!in_array($entry->show_class, $skip_breed_best) && ($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name))
						$table->addCell(5000)->addText(JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i), null, array('valign' => 'center', 'align' => 'center'));
					else
						$table->addCell(5000)->addText(JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i), array('align' => 'center'), array('valign' => 'center', 'align' => 'center'));

					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {

						$table->addCell(800)->addText('___', null, array('align' => (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right')));
					}


					if ($i == 3)
						break;
				}

				$table->addRow(400)->addCell(9000, array('gridspan' => (count($show_day_rings[$show_days[0]->show_day_id]) * 2 + 1)))->addText('');
			}

			if (in_array($entry->show_class, $skip_breed_best)) {
				$previous_breed_entries = 0;
			} else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {

				$kflag = true;

				for ($i = 1; $i <= $previous_breed_entries; $i++) {

					$table->addRow(300);
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {

						$table->addCell(800)->addText('___', null, array('align' => (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right')));
					}

					$table->addCell(6000)->addText(JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i), array('color' => $bob_color, 'align' => 'center'), array('valign' => 'center', 'align' => 'center'));

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {


						$table->addCell(800)->addText('___', array('align' => (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right')));
					}



					if ($i == 3)
						break;
				}
				$table->addRow(400)->addCell(9000, array('gridspan' => (count($show_day_rings[$show_days[0]->show_day_id]) * 2 + 1)))->addText('');
			}

			$cur++;

			$k++;
		}
	}
}else {
	foreach ($entries as $entry) {
		if ($previous_class != $entry->show_class) {
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;

			if ($previous_class != '')
				$section->addPageBreak();


			$section = $phpWord->createSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 200, 'marginBottom' => 200, 'headerHeight' => 100, 'footerHeight' => 500, 'orientation' => 'landscape'));
			$section->addTitle(' ');
			//$section->getStyle()->setPageNumberingStart(1);
			$newheader = $section->createHeader();
			//$newheader->addText('vaishali'.$sectionid,array('size'=>18));
			$fontStyleTitle = array('size' => 16, 'bold' => true);
			$fontStyleSubTitle = array('size' => 10, 'bold' => false);
			$paragraphStyleTitle = array('spaceBefore' => 0);
			$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF');
			$lineStyle = array('weight' => 1, 'width' => 710, 'height' => 10, 'color' => '#FFFFFF');
			$newheader->addLine($lineStyle);
			$newheadertable = $newheader->addTable($styleTable);

			$newheadertable->addRow(200);
			$logo = JURI::root() . 'media/com_toes/images/paw32X32.png';
			$newheadertable->addCell(500)->addImage($logo, array('align' => 'left'));


			//$table->addCell(4000)->addText(JText::_('COM_TOES_EXHIBITORLIST_HEADER').' '.JText::_('COM_TOES_SITE_TITLE'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));

			$nhcell1 = $newheadertable->addCell(6000);
			$nhcell1->addText(JText::_('COM_TOES_CATALOG'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
			$nhcell1->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
			$nhcell2 = $newheadertable->addCell(8000);
			$nhcell2->addText(strtoupper($entry->show_class), array('size' => 18, 'bold' => true));
			/* $newheadertable->addRow();
			  $newheadertable->addCell(9000,array('colspan'=>2))->addText('___',array('width'=>'9000')); */
			$newheader->addText();
			$lineStyle = array('weight' => 1, 'width' => 1050, 'height' => 10, 'color' => '#000000');
			$newheader->addLine($lineStyle);


			$table1 = $section->addTable();
			$table1->addRow(500);

			$table1->addCell((count($show_days) == 3 ? 5000 : 6000))->addText('');


			if ($show->show_format != 'Alternative') {
				//echo "hi";die;
				foreach ($show_days as $show_day) {
					if (count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$cell1 = $table1->addCell((count($show_days) == 3 ? 4000 : 8000));
					$ntable1 = $cell1->addTable();
					$ntable1->addRow(500);
					$ntable1->addCell(8000, array('gridspan' => (count($show_days))))->addText((($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))));


					$ntable1->addRow(500);
					foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						//echo $pr_show_day->judge_abbreviation.'<br>';
						$ntable1->addCell(500)->addText($pr_show_day->judge_abbreviation, array('size' => 10), array('align' => 'left'));
					}
					//die;
				}
			} else {
				foreach ($show_days as $show_day) {
					if (count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;

					$cell2 = $table1->addCell((count($show_days) == 3 ? 4000 : 8000));
					$ntable2 = $cell2->addTable();
					$ntable2->addCell(500, array('gridspan' => count($show_day_rings[$show_day->show_day_id])))->addText((count($session_rings[$show_day->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM' : ' '));
					$ntable2->addCell(500, array('gridspan' => count($show_day_rings[$show_day->show_day_id])))->addText((count($session_rings[$show_day->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM' : '&nbsp;'));


					foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						$ntable2->addCell(500)->addText($pr_show_day->judge_abbreviation, array('size' => 10), array('align' => (($pr_show_day->ring_timing == 1) ? 'left' : 'right')));
					}
				}
			}


			if ($entry->show_class != 'Ex Only') {
				/* 	$pdf->SetFont('ptsansnarrow', '', $font_size);
				  $pdf->writeHTML($judge_block, true, false, false, false, '');
				  $pdf->ln(); */
			}

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
		}




		/* $table1=$section->addTable();
		  $table1->addRow();
		  $cell1=$table1->addCell((count($show_days) == 3 ? 4000 : 6000)); */
		if ($previous_catalog_number != $entry->catalog_number) {
			if ($previous_breed != $entry->breed_name) {
				$previous_breed = $entry->breed_name;
				$previous_breed_entries = 1;
				$previous_division_entries = 1;
				$section->addText(strtoupper($entry->breed_name), array('bold' => true, 'size' => 14, 'color' => $breed_title_color, 'underline' => 'single'), array('align' => 'left'));
			} else
				$previous_breed_entries++;

			if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
				$previous_division_entries = 1;

				if ($previous_breed != $entry->breed_name)
					$previous_breed = $entry->breed_name;
				if ($previous_division != $entry->catalog_division)
					$previous_division = $entry->catalog_division;

				$section->addText(strtoupper($entry->catalog_division), array('color' => $division_title_color, 'underline' => 'single', 'size' => 14), array('align' => 'left'));
			} else
				$previous_division_entries++;

			if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
				if ($previous_breed != $entry->breed_name)
					$previous_breed = $entry->breed_name;
				if ($previous_division != $entry->catalog_division)
					$previous_division = $entry->catalog_division;
				$section->addText(strtoupper($entry->color_name), array('bold' => true, 'size' => 14), array('align' => 'left'));
			}
		}


		if ($previous_catalog_number != $entry->catalog_number) {

			$table2 = $section->addTable();
			$table2->addRow(2000);


			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');
			$cell2 = $table2->addCell((count($show_days) == 3 ? 5000 : 6000));


			$ntable2 = $cell2->addTable();
			$ntable2->addRow(200);
			//$ntable2->addCell(3000)->addText( strtoupper($entry->catalog_cat_name)  ,array('bold'=>(($show->show_catalog_cat_names_bold)? true :false)),array('align'=>'left'));
			$ntable2->addCell((count($show_days) == 3 ? 3000 : 4000))->addText(strtoupper($entry->catalog_cat_name), null, array('align' => 'left'));
			$ntable2->addCell(1000)->addText($entry->catalog_age_and_gender, null, array('align' => 'right'));
			$ntable2->addCell(1000)->addText($entry->catalog_number, null, array('align' => 'right'));


			if (trim($entry->catalog_registration_number) == '')
				$reg_number = JText::_('PENDING');
			else
				$reg_number = $entry->catalog_registration_number;

			$ntable2->addRow(200);
			$ntable2->addCell((count($show_days) == 3 ? 5000 : 6000), array('gridspan' => 3))->addText(strtoupper($reg_number) . '   ' . JText::_('COM_TOES_CATALOG_BORN') . ' ' . $entry->catalog_birthdate);

			$ntable2->addRow(200);
			$cat_info_cell = $ntable2->addCell(3000);

			if ($isNotHHP) {
				$cat_info_cell->addText(strtoupper($entry->catalog_sire));
				$cat_info_cell->addText(strtoupper($entry->catalog_dam));
			}

			if ($entry->catalog_breeder)
				$cat_info_cell->addText(strtoupper($entry->catalog_breeder));
			if ($entry->catalog_owner)
				$cat_info_cell->addText(strtoupper($entry->catalog_owner));
			if ($entry->catalog_lessee)
				$cat_info_cell->addText($entry->catalog_lessee);
			if ($entry->catalog_agent)
				$cat_info_cell->addText($entry->catalog_agent);
			
			$ntable2->addCell(1000)->addText($entry->catalog_region,array('align' => 'right'));

			if ($entry->show_class != 'Ex Only') {
				if ($show->show_format != 'Alternative') {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$cell3 = $table2->addCell((count($show_days) == 3 ? 4000 : 6000));
						$ntable3 = $cell3->addTable();
						$ntable3->addRow(200);

						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {

							if (isset($show_day_entries[$show_day->show_day_id][$entry->catalog_number]))
								$ntable3->addCell(600)->addText('___', null, array('align' => 'left'));
							else
								$ntable3->addCell(600)->addText('___', null, array('align' => 'left'));
						}
					}
				} else {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;


						$cell4 = $table2->addCell((count($show_days) == 3 ? 4000 : 6000));
						$ntable4 = $cell4->addTable();
						$ntable4->addRow(200);

						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {

							if (isset($show_day_entries[$show_day->show_day_id][$entry->catalog_number])) {
								if (($pr_show_day->ring_timing == 1 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($pr_show_day->ring_timing == 2 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_PM))
									$ntable4->addCell(600)->addText('___', null, array('align' => (($pr_show_day->ring_timing == 1) ? 'left' : 'right')));
								else
									$ntable4->addCell(600)->addText('___', null, array('align' => (($pr_show_day->ring_timing == 1) ? 'left' : 'right')));
							} else
								$ntable4->addCell(600)->addText(' ', null, array('align' => (($pr_show_day->ring_timing == 1) ? 'left' : 'right')));
						}
					}
				}
			} else
				$table2->addCell((count($show_days) == 3 ? 5000 : 8000))->addText('');
		}

		/* $pdf->startTransaction();
		  $block_page = $pdf->getPage(); */
		$print_block = 2; // 2 tries max
		while ($print_block > 1) {


			$next = $cur + 1;
			if (in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($entries) ||
				( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
				$table3 = $section->addTable();

				for ($i = 1; $i <= $previous_division_entries; $i++) {

					$table3->addRow(500);
					if (!in_array($entry->show_class, $skip_breed_best) && ($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name)) {
						$table3->addCell((count($show_days) == 3 ? 5000 : 6000))->addText(JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i), array('color' => $bob_color));
					} else
						$table3->addCell((count($show_days) == 3 ? 5000 : 6000))->addText(JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i), array('color' => $bob_color));


					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$cell4 = $table3->addCell((count($show_days) == 3 ? 4000 : 5000));
						$ntable4 = $cell4->addTable();
						$ntable4->addRow(500);

						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {

							$ntable4->addCell(500)->addText('___', null, array('align' => (($show->show_format != 'Alternative' || $pr_show_day->ring_timing == 1) ? 'left' : 'right')));
						}
					}


					if ($i == 3)
						break;
				}
			}

			if (in_array($entry->show_class, $skip_breed_best)) {
				$previous_breed_entries = 0;
			} else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {
				$table4 = $section->addTable();


				for ($i = 1; $i <= $previous_breed_entries; $i++) {
					$table4->addRow(500);

					$table4->addCell((count($show_days) == 3 ? 6000 : 8000))->addText(JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i), array('color' => $bob_color));

					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;

						$cell5 = $table4->addCell((count($show_days) == 3 ? 4000 : 5000));
						$ntable5 = $cell5->addTable();
						$ntable5->addRow(500);

						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$ntable5->addCell(500, array('gridspan' => 2))->addText('___', null, array('align' => (($show->show_format != 'Alternative' || $pr_show_day->ring_timing == 1) ? 'left' : 'right')));
						}
					}

					//	$best_breed_block .= '</tr>';
					if ($i == 3)
						break;
				}
			}
			//$pdf->ln(1);
			--$print_block;

			// do not split BLOCKS in multiple pages
			/* if ($pdf->getPage() == $block_page) {
			  if ($previous_catalog_number != $entry->catalog_number) {
			  if ($previous_breed != $entry->breed_name) {
			  $previous_division = '';
			  $previous_color = '';
			  $previous_breed = $entry->breed_name;
			  }

			  if ($previous_division != $entry->catalog_division) {
			  $previous_color = '';
			  $previous_division = $entry->catalog_division;
			  }

			  if ($previous_color != $entry->color_name)
			  $previous_color = $entry->color_name;

			  $previous_catalog_number = $entry->catalog_number;

			  $processed++;
			  $fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
			  $data = array(
			  'total' => $total,
			  'processed' => $processed,
			  'log' => $log
			  );
			  fputs($fp, serialize($data));
			  fclose($fp);
			  }

			  $print_block = 0;
			  } else {
			  $previous_breed_entries--;
			  $previous_division_entries--;
			  // rolls back to the last (re)start
			  $pdf = $pdf->rollbackTransaction();
			  $pdf->AddPage();

			  $header_logo = JURI::root() . 'media/com_toes/images/paw32X32.jpg';
			  $show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			  if($params->get('use_logo_for_pdf')) {
			  $show_class_block .='<img src="' . $header_logo . '" />';
			  } else {
			  $show_class_block .=' ';
			  }
			  $show_class_block .='</td><td style="width:26%">';
			  $show_class_block .= '<span style="font-size:40px; font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span style="font-size:35px">' . JText::_('COM_TOES_WEBSITE') . '</span>';
			  $show_class_block .='</td>';
			  $show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:70px">' . strtoupper($entry->show_class) . '</div></td>';
			  $show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			  $pdf->SetFont('ptsanscaption', '', $font_size + 2);
			  $pdf->writeHTML($show_class_block, true, false, false, false, '');

			  $judge_block = '<table width="100%">
			  <tr>
			  ';

			  $judge_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">&nbsp;</td>
			  ';

			  if ($show->show_format != 'Alternative') {
			  foreach ($show_days as $show_day) {
			  if (count($show_day_rings[$show_day->show_day_id]) == 0)
			  continue;
			  $judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
			  <table width="100%">
			  <tr>
			  <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . '</td>
			  </tr>
			  <tr>
			  ';
			  foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
			  $judge_block .= '<td align="left">' . $pr_show_day->judge_abbreviation . '</td>
			  ';
			  }
			  $judge_block .= '       </tr>
			  </table>
			  </td>
			  ';
			  }
			  } else {
			  foreach ($show_days as $show_day) {
			  if (count($show_day_rings[$show_day->show_day_id]) == 0)
			  continue;
			  $judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
			  <table>
			  <tr>
			  <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM' : '&nbsp;') . '</td>
			  <td align="right" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM' : '&nbsp;') . '</td>
			  </tr>
			  <tr>
			  ';
			  foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
			  $judge_block .= '<td colspan="2" align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $pr_show_day->judge_abbreviation . '</td>
			  ';
			  }
			  $judge_block .= '       </tr>
			  </table>
			  </td>
			  ';
			  }
			  }

			  $judge_block .= '</tr>
			  </table>';

			  if ($entry->show_class != 'Ex Only') {
			  $pdf->SetFont('ptsansnarrow', '', $font_size);
			  $pdf->writeHTML($judge_block, true, false, false, false, '');
			  $pdf->ln(1);
			  }

			  if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
			  $pdf->SetFont('ptsans', 'b', $font_size + 2);
			  $breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

			  $pdf->writeHTML($breed_block, true, false, false, false, '');
			  $pdf->ln(1);

			  $previous_color = $entry->color_name;
			  } else if ($previous_breed == $entry->breed_name) {
			  $pdf->SetFont('ptsans', 'b', $font_size + 2);
			  $breed_block = '<span style="text-align:left;color:' . $breed_title_color . '; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

			  $pdf->writeHTML($breed_block, true, false, false, false, '');
			  $pdf->ln(1);

			  $previous_breed = $entry->breed_name;
			  }

			  $block_page = $pdf->getPage();
			  --$print_block;
			  } */
		}
		$cur++;
	}
}



$file = JRequest::getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Processing Congress Entries....";
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

$section->addPageBreak();

$query = TOESQueryHelper::getJudgesBookCongressData($whr);

$db->setQuery($query);
$congress_catalog = $db->loadObjectList();

$query = "SELECT `r`.`ring_id`, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation`
  FROM `#__toes_ring` AS `r`
  LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`)
  WHERE (`r`.`ring_format` = 3) AND `r`.`ring_show` = {$show_id}
  ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`";
$db->setQuery($query);
$temp_rings = $db->loadObjectList();
$pre_judge_name = '';
$congress_rings = array();
foreach ($temp_rings as $ring) {
	$congress_rings[strtolower($ring->ring_name)][] = $ring;
}

$final_entries = array();
$temp_entries = array();
foreach ($entries as $entry) {
	$temp_entries[$entry->catalog_number] = $entry;
}

$ctlg_numbers = array();
$cngrs_entries_by_ring_id = array();
foreach ($congress_catalog as $entry) {
	$cngrs_entries_by_ring_id[$entry->ring_id][] = $entry->catalog_number;
	$temp_entries[$entry->catalog_number]->show_class = $entry->show_class;
	if (!isset($ctlg_numbers[strtolower($entry->ring_name)]))
		$ctlg_numbers[strtolower($entry->ring_name)] = array();
	if (!in_array($entry->catalog_number, $ctlg_numbers[strtolower($entry->ring_name)])) {
		$ctlg_numbers[strtolower($entry->ring_name)][] = $entry->catalog_number;
		$final_entries[strtolower($entry->ring_name)][] = $temp_entries[$entry->catalog_number];
	}
}
$section->addPageBreak();

$temp_entries = array();
foreach ($final_entries as $ring_number => $congress_entries) {
	$temp_entries[$ring_number] = TOESHelper::aasort($congress_entries, 'catalog_number');
}
$final_entries = $temp_entries;

foreach ($final_entries as $ring_number => $cng_entries) {
	unset($congress_entries);
	$congress_entries = array_values($cng_entries);

	$previous_class = '';
	$previous_breed = '';
	$previous_division = '';
	$previous_color = '';
	$previous_catalog_number = '';

	$previous_breed_entries = 1;
	$previous_division_entries = 1;

	$cur = 0;

	$rings = $congress_rings[$ring_number];
	$judge_name = end($rings)->ring_name;
	//$judge_abbreviation = $congress_rings[$ring_number]->judge_abbreviation;

	if ($pre_judge_name != $judge_name) {
		//	echo $judge_name.'<br>';
		$pre_judge_name = end($rings)->ring_name;
		$section = $phpWord->addSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 200, 'marginBottom' => 200, 'headerHeight' => 50, 'footerHeight' => 50, 'orientation' => 'landscape'));
		$section->addTitle(' ');
		//$section->getStyle()->setPageNumberingStart(1);
		$newheader = $section->createHeader();
		//$newheader->addText('vaishali'.$sectionid,array('size'=>18));
		$fontStyleTitle = array('size' => 16, 'bold' => true);
		$fontStyleSubTitle = array('size' => 10, 'bold' => false);
		$paragraphStyleTitle = array('spaceBefore' => 0);
		$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF');
		$lineStyle = array('weight' => 1, 'width' => 700, 'height' => 0, 'color' => '#FFFFFF');
		$newheader->addLine($lineStyle);
		$newheadertable = $newheader->addTable($styleTable);

		$newheadertable->addRow();
		$logo = JURI::root() . 'media/com_toes/images/paw32X32.png';
		$newheadertable->addCell(500)->addImage($logo, array('align' => 'left'));


		//$table->addCell(4000)->addText(JText::_('COM_TOES_EXHIBITORLIST_HEADER').' '.JText::_('COM_TOES_SITE_TITLE'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));

		$nhcell1 = $newheadertable->addCell(6000);
		$nhcell1->addText(JText::_('COM_TOES_CATALOG'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
		$nhcell1->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
		$nhcell2 = $newheadertable->addCell(6000);
		$newheader->addText();
		$nhcell2->addText(strtoupper(strtoupper($judge_name)), array('size' => 18, 'bold' => true));
		$lineStyle = array('weight' => 1, 'width' => 1100, 'height' => 0, 'color' => '#000000');
		$newheader->addLine($lineStyle);
	}


	$table1 = $newheader->addTable();
	$table1->addRow(100);
	$table1->addCell(11000, array('rowspan' => count($ring)))->addText(' ');

	foreach ($rings as $ring) {
		$table1->addCell(1000, array('align' => 'right'))->addText($ring->judge_abbreviation, null, array('align' => 'right'));
	}

	$file = JRequest::getVar('file', '');
	$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
	$log = "Processing Congress Entries for " . $judge_name . " ....";
	$data = array(
		'total' => $total,
		'processed' => $processed,
		'log' => $log
	);
	fputs($fp, serialize($data));
	fclose($fp);

	foreach ($congress_entries as $entry) {

		$table2 = $section->addTable();
		if ($previous_breed != $entry->breed_name) {
			$previous_breed_entries = 1;
			$previous_division_entries = 1;
			$previous_breed = $entry->breed_name;
			$table2->addRow(500);
			$table2->addCell(10000)->addText(strtoupper($entry->breed_name), array('size' => 12, 'align' => 'left', 'bold' => true, 'color' => $breed_title_color, 'underline' => 'single'));
		} else
			$previous_breed_entries++;

		if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
			$previous_division_entries = 1;
			$previous_division = $entry->catalog_division;

			$breed_block = '<span style="text-align:left; text-decoration:underline;color:' . $division_title_color . '; padding:5px 0;">' . strtoupper($entry->catalog_division) . '</span>';
			$table2->addRow(100);
			$table2->addCell(9000)->addText(strtoupper($entry->catalog_division), array('size' => 12, 'align' => 'left', 'color' => $breed_title_color, 'underline' => 'single'));
		} else
			$previous_division_entries++;

		if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {

			$table2->addRow(100);
			$table2->addCell(9000)->addText(strtoupper($entry->color_name), array('size' => 12, 'align' => 'left', 'bold' => true, 'color' => $breed_title_color));
		}




		if ($previous_catalog_number != $entry->catalog_number) {

			$table2->addRow();


			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');


			$table2->addCell(9000, array('align' => 'left'))->addText(( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name)));
			$table2->addCell(1000, array('rowspan' => ($isNotHHP ? 4 : 2), 'valign' => 'top', 'align' => 'right'))->addText($entry->catalog_age_and_gender);
			$table2->addCell(1000, array('rowspan' => ($isNotHHP ? 5 : 3), 'valign' => 'top', 'align' => 'right'))->addText($entry->catalog_number);


			foreach ($rings as $ring) {
				if (in_array($entry->catalog_number, $cngrs_entries_by_ring_id[$ring->ring_id])) {

					$table2->addCell(1000, array('align' => 'right'))->addText('____', null, array('align' => 'right'));
				} else {
					$table2->addCell(1000, array('valign' => 'right'))->addText(' ', null, array('align' => 'right'));
				}
			}

			if (trim($entry->catalog_registration_number) == '')
				$reg_number = JText::_('PENDING');
			else
				$reg_number = $entry->catalog_registration_number;
			$table2->addRow(100);
			$table2->addCell(9000, array('colspan' => (count($rings) + 3), 'align' => 'left'))->addText(strtoupper($reg_number) . '   ' . JText::_('COM_TOES_CATALOG_BORN') . ' ' . $entry->catalog_birthdate);

			if ($isNotHHP) {
				$table2->addRow(100);
				$table2->addCell(9000, array('colspan' => (count($rings) + 3), 'align' => 'left'))->addText(strtoupper($entry->catalog_sire));

				$table2->addRow(100);
				$table2->addCell(9000, array('colspan' => (count($rings) + 3), 'align' => 'left'))->addText(strtoupper($entry->catalog_dam));
			}
			$table2->addRow(300);
			$cell1 = $table2->addCell(4000, array('align' => 'left', 'colspan' => (count($rings) + 3)));

			if ($entry->catalog_breeder)
				$cell1->addText(strtoupper($entry->catalog_breeder));
			if ($entry->catalog_owner)
				$cell1->addText(strtoupper($entry->catalog_owner));
			if ($entry->catalog_lessee)
				$cell1->addText(strtoupper($entry->catalog_lessee));
			if ($entry->catalog_agent)
				$cell1->addText(strtoupper($entry->catalog_agent));
			$cell1->addText($entry->catalog_region, null, array('align' => 'center'));
		}


		$print_block = 2; // 2 tries max
		/* while ($print_block > 1) { */
		/* if ($previous_catalog_number != $entry->catalog_number) {
		  if ($previous_breed != $entry->breed_name) {
		  $previous_breed_entries = 1;
		  $previous_division_entries = 1;
		  $pdf->SetFont('ptsans', 'b', $font_size + 4);
		  $breed_block = '<span style="text-align:left; font-weight:bold;color:' . $breed_title_color . '; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->breed_name) . '</span>';

		  $pdf->writeHTML($breed_block, true, false, false, false, '');
		  $pdf->ln(1);
		  //					\PhpOffice\PhpWord\Shared\Html::addHtml($section,$breed_block );
		  } else
		  $previous_breed_entries++;

		  if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
		  $previous_division_entries = 1;

		  //	$pdf->SetFont('ptsans', '', $font_size + 2);
		  $breed_block = '<span style="text-align:left; text-decoration:underline;color:' . $division_title_color . '; padding:5px 0;">' . strtoupper($entry->catalog_division) . '</span>';

		  //$pdf->writeHTML($breed_block, true, false, false, false, '');
		  $pdf->ln(1);
		  //\PhpOffice\PhpWord\Shared\Html::addHtml($section,$breed_block );
		  } else
		  $previous_division_entries++;

		  if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
		  //$pdf->SetFont('ptsans', 'b', $font_size + 2);
		  $breed_block = '<span style="text-align:left; font-weight:bold; padding:5px 0;">' . strtoupper($entry->color_name) . '</span>';

		  //$pdf->writeHTML($breed_block, true, false, false, false, '');
		  //$pdf->ln(1);
		  //\PhpOffice\PhpWord\Shared\Html::addHtml($section,$breed_block );
		  }

		  //	$pdf->SetFont('ptsansnarrow', '', $font_size);
		  //$pdf->writeHTML($entry_block, true, false, false, false, '');
		  //\PhpOffice\PhpWord\Shared\Html::addHtml($section,$entry_block );
		  } */


		$next = $cur + 1;
		//var_dump($congress_entries[$next]);
		if ($entry->show_class != 'Ex Only' && in_array($entry->show_class, $skip_division_best)) {
			$previous_division_entries = 0;
		} else if ($next == count($congress_entries) ||
			( isset($congress_entries[$next]) && ($entry->breed_name != $congress_entries[$next]->breed_name || $entry->catalog_division != (isset($congress_entries[$next]->catalog_division) ? $congress_entries[$next]->catalog_division : '')))) {
			$table4 = $section->addTable();

			for ($i = 1; $i <= $previous_division_entries; $i++) {
				$table4->addRow(500);

				if (!in_array($entry->show_class, $skip_breed_best) && ($next == count($congress_entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != (isset($congress_entries[$next]->breed_name) ? $congress_entries[$next]->breed_name : '')))
					$table4->addCell(10000, array('color' => $bob_color, 'colspan' => count($rings)))->addText(JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i));
				else
					$table4->addCell(10000, array('color' => $bob_color, 'colspan' => count($rings)))->addText(JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i));

				foreach ($rings as $ring) {
					$table4->addCell(1000)->addText('____', null, array('align' => 'right'));
				}

				if ($i == 3)
					break;
			}
		}



		if ($entry->show_class != 'Ex Only' && in_array($entry->show_class, $skip_breed_best)) {
			$previous_breed_entries = 0;
		} else if ($previous_breed_entries > $previous_division_entries && ($next == count($congress_entries) || ($entry->breed_name != $congress_entries[$next]->breed_name))) {
			$table5 = $section->addTable();

			for ($i = 1; $i <= $previous_breed_entries; $i++) {

				$table5->addRow(500);
				$table5->addCell(10000, array('color' => $bob_color, 'colspan' => count($rings)))->addText(JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i));

				foreach ($rings as $ring) {
					$table5->addCell(1000)->addText('____', null, array('align' => 'right'));
				}

				if ($i == 3)
					break;
			}
			//$section->addText(' ');
			--$print_block;
		}
		//$section->addText(' ');

		$cur++;
	}
}

$file = JRequest::getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Generating Final sheets....";
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

//Final Sheets
$new_breed_classes = array(
	'PNB',
	'ANB',
	'NT',
	'Ex Only',
	'For Sale'
);
$section->addPageBreak();
$temp_show_days = array();
foreach ($show_days as $show_day) {
	$temp_show_days[$show_day->show_day_id] = $show_day;
}

$max_rings_on_day = 0;
$ring_counts = array();
foreach ($show_day_rings as $show_day => $rings) {
	$cnt = 0;
	foreach ($rings as $ring) {
		if ($ring->format == 'Specialty')
			$cnt += 2;
		else
			$cnt++;
	}

	$ring_counts[$show_day] = $cnt;
	if ($max_rings_on_day < $cnt)
		$max_rings_on_day = $cnt;
}

/* if ($max_rings_on_day > 10 || $page_orientation == 'L')
  $orientation = 'L';
  else
  $orientation = 'P'; */

foreach ($show_day_rings as $show_day => $rings) {


	$section = $phpWord->createSection(array('marginLeft' => 200, 'marginRight' => 200, 'marginTop' => 200, 'marginBottom' => 200, 'headerHeight' => 50, 'footerHeight' => 50, 'orientation' => 'landscape'));
	$section->addTitle(' ');
	//$section->getStyle()->setPageNumberingStart(1);
	$newheader = $section->createHeader();
	//$newheader->addText('vaishali'.$sectionid,array('size'=>18));
	$fontStyleTitle = array('size' => 16, 'bold' => true);
	$fontStyleSubTitle = array('size' => 10, 'bold' => false);
	$paragraphStyleTitle = array('spaceBefore' => 0);
	$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF');
	$lineStyle = array('weight' => 1, 'width' => 700, 'height' => 0, 'color' => '#FFFFFF');
	$newheader->addLine($lineStyle);
	$newheadertable = $newheader->addTable($styleTable);

	$newheadertable->addRow();
	$logo = JURI::root() . 'media/com_toes/images/paw32X32.png';
	$newheadertable->addCell(500)->addImage($logo, array('align' => 'left'));


	//$table->addCell(4000)->addText(JText::_('COM_TOES_EXHIBITORLIST_HEADER').' '.JText::_('COM_TOES_SITE_TITLE'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));

	$nhcell1 = $newheadertable->addCell(6000);
	$nhcell1->addText(JText::_('COM_TOES_CATALOG'), $fontStyleTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
	$nhcell1->addText(JText::_('COM_TOES_WEBSITE'), $fontStyleSubTitle, array_merge($paragraphStyleTitle, array('align' => 'left')));
	$nhcell2 = $newheadertable->addCell(6000);

	$nhcell2->addText(JText::_('FINAL_SHEETS'), array('size' => 18, 'bold' => true, 'allCaps' => true));
	$lineStyle = array('weight' => 1, 'width' => 1100, 'height' => 0, 'color' => '#000000');
	$newheader->addText();
	$newheader->addLine($lineStyle);
	$newheader->addText(strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))));



	foreach ($available_classes as $class) {
		if ($class == 'Ex Only' || $class == 'For Sale')
			continue;

		$current_index = 0;
		while ($max_rings_on_day > $current_index) {
			$table6 = $section->addTable();
			$table6->addRow(500);

			$i = 0;

			$table6->addCell(1200)->addText($class, null, array('align' => 'center'));
			foreach ($rings as $ring) {
				if ($i < $current_index) {
					if ($ring->format == 'Specialty') {
						$i +=2;
					} else {
						$i++;
					}
					continue;
				}

				if ($ring->format == 'Specialty') {
					$table6->addCell(2000)->addText($ring->judge_abbreviation . ' (LH)   ');

					$i++;
					$table6->addCell(2000)->addText($ring->judge_abbreviation . ' (SH)   ');

					$i++;
				} else {
					$table6->addCell(2000)->addText($ring->judge_abbreviation . ' (AB)   ');

					$i++;
				}
				if ($i >= $current_index + 10)
					break;
			}


			if (!in_array($class, $new_breed_classes)) {
				for ($j = 1; $j <= 10; $j++) {
					$table6->addRow(300);
					$table6->addCell(2000)->addText($j . '  ', null, array('align' => 'center'));


					for ($k = $current_index; $k < $i; $k++) {
						$table6->addCell(2000, array('borderSize' => 1, 'borderColor' => '#000'))->addText(' ');
					}
				}

				$table6->addRow(300);
				$table6->addCell(2000)->addText(JText::_('COUNT'), null, array('align' => 'center'));


				for ($k = $current_index; $k < $i; $k++) {
					$table6->addCell(2000, array('borderSize' => 1, 'borderColor' => '#000'))->addText(' ');
				}
			} else {
				$table6->addRow(300);
				$table6->addCell(2000)->addText('  ', null, array('align' => 'center'));


				for ($k = $current_index; $k < $i; $k++) {
					$table6->addCell(2000, array('borderSize' => 1, 'borderColor' => '#000'))->addText(' ');
				}


				$table6->addRow(300);
				$table6->addCell(2000)->addText(JText::_('COUNT'), null, array('align' => 'center'));


				for ($k = $current_index; $k < $i; $k++) {
					$table6->addCell(2000, array('borderSize' => 1, 'borderColor' => '#000'))->addText(' ');
				}
			}
			$current_index = $i;

			$section->addText();
			$section->addText();

			$print_block = 2; // 2 tries max
			while ($print_block > 1) {

				/* 	if (!$is_continuous) {
				  $show_day_block = '<div>' . strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))) . '</div>';
				  \PhpOffice\PhpWord\Shared\Html::addHtml($section, $show_day_block);

				  } */


				--$print_block;
			}

			if ($current_index >= $ring_counts[$show_day])
				break;
		}
	}

	$query = "SELECT `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation`
            FROM `#__toes_ring` AS `r`
            LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`)
            WHERE (`r`.`ring_format` = 3) AND `r`.`ring_show` = {$show_id} AND `r`.`ring_show_day` = {$show_day}
            ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`";
	$db->setQuery($query);
	$congress_rings = $db->loadObjectList();

	if ($congress_rings) {
		$section->addText();
		$section->addText(JText::_('CONGRESS'), array('size' => 16));


		$current_index = 0;
		$styleTable1 = array('borderSize' => 8, 'borderColor' => 'FFFFFF', 'cellMargin' => 25, 'rules' => 'cols');
		$bordeinferior = array('borderRightSize' => 0, 'borderBottomColor' => 'CCCCCC', 'borderBottomSize' => 50, 'borderRightColor' => '00CCFF', 'borderTopSize' => 0, 'borderTopColor' => '00CCFF', 'borderLeftSize' => 0, 'borderLeftColor' => '00CCFF');
		$phpWord->addTableStyle('congress_rings', $styleTable1, $bordeinferior);
		while (count($congress_rings) > $current_index) {
			$table7 = $section->addTable('congress_rings');
			$table7->addRow(300);

			$i = 0;

			$table7->addCell(1500)->addText(' ');
			foreach ($congress_rings as $ring) {
				if ($i < $current_index) {
					$i++;
					continue;
				}
				$table7->addCell(12000 / count($congress_rings))->addText($ring->judge_abbreviation . ' (' . $ring->ring_name . ')  ');

				$i++;
				if ($i >= $current_index + 10)
					break;
			}


			for ($j = 1; $j <= 10; $j++) {
				$table7->addRow(300);

				$table7->addCell(1500)->addText($j . '  ');
				for ($k = $current_index; $k < $i; $k++) {
					$table7->addCell(15000 / count($congress_rings), array('borderSize' => 2, 'borderColor' => '#000', 'borderBottomColor' => '009900', 'borderBottomSize' => 50))->addText(' ');
				}
			}

			$table7->addRow(300);
			$table7->addCell(1500)->addText(JText::_('COUNT'), null, array('align' => 'center'));


			for ($k = $current_index; $k < $i; $k++) {
				$cell1 = $table7->addCell(15000 / count($congress_rings), array('borderSize' => 2, 'borderColor' => '#000', 'borderBottomColor' => '009900', 'borderBottomSize' => 50));
				$cell1->addText(' ');
			}



			$section->addText(' ');
			$section->addText(' ');
			$current_index = $i;


			$print_block = 2; // 2 tries max
			while ($print_block > 1) {


				/* if (!$is_continuous) {
				  $show_day_block = '<div>' . strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))) . '</div>';
				  \PhpOffice\PhpWord\Shared\Html::addHtml($section, $show_day_block);

				  } */


				--$print_block;
			}
		}
	}
}
//}


$file = JRequest::getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Generating DOCX file....";
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(JPATH_BASE . "/media/com_toes/DOCX" . DS . $show_id))
	JFolder::create(JPATH_BASE . "/media/com_toes/DOCX/" . $show_id, 0777);
else
	chmod(JPATH_BASE . "/media/com_toes/DOCX" . DS . $show_id, 0777);

if (file_exists(JPATH_BASE . "/media/com_toes/DOCX" . DS . $show_id . DS . 'catalog.docx'))
	unlink(JPATH_BASE . "/media/com_toes/DOCX" . DS . $show_id . DS . 'catalog.docx');

echo write($phpWord, $writers, $show_id);
//Close and output PDF document


//============================================================+
// END OF FILE                                                
//============================================================+
