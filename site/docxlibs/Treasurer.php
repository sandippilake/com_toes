<?php
jimport('phpword.Autoloader');

use PhpOffice\PhpWord\Autoloader;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

Autoloader::register();
Settings::loadConfig();

$writers = array('Word2007' => 'docx');

function write($phpWord, $writers,$show_id)
{

	$result = '';

	// Write documents
	foreach ($writers as $writer => $extension) {

	//	$result .= date('H:i:s') . " Write to {$writer} format";
		if (!is_null($extension)) {
			$xmlWriter = IOFactory::createWriter($phpWord, $writer);

			$xmlWriter->save(JPATH_BASE. "/media/com_toes/DOCX/".$show_id."/treasurer.docx");

		} else {
			$result .= ' ... NOT DONE!';
		}

		//$result .= EOL;
	}



	return 1;
}


$phpWord = new \PhpOffice\PhpWord\PhpWord();
$phpWord->addFontStyle('rStyle', array('bold' => true, 'italic' => true, 'size' => 16, 'allCaps' => true, 'doubleStrikethrough' => true));
//$phpWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceAfter' => 100));
$phpWord->addTitleStyle(1, array('bold' => true), array('spaceAfter' => 240));

$db = JFactory::getDbo();
$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();
$page_format = $page_format?$page_format:'A4';

$sectionSettings = array(
'orientation' => 'landscape',
//'pageSizeW'=>308.4,
//'pageSizeH'=>487.04,
'paper'=>$page_format,
'marginLeft'=>100,
'marginRight'=>100,
'headerHeight'=>10

);

//$section = $phpWord->createSection($sectionSettings);
$section = $phpWord->createSection(array('marginLeft' => 200, 'marginRight' => 400, 'marginTop' => 0, 'marginBottom' => 200, 'headerHeight' => 0, 'footerHeight' => 500,'pageNumberingStart'=>1,'orientation' => 'landscape'));

$header = $section->createHeader();



$lineStyle = array('weight' => 1, 'width' => 1050, 'height' => 0, 'color' => '#000000');
$header = array('size' => 16, 'bold' => true);

$footer = $section->createFooter();
$footer->addLine($lineStyle);
/*$ftable=$footer->addTable();
$ftable->addRow(500);
$ftable->addCell(5000)->addText(' ');*/
$footer->addText( date('M d, Y', time()),array('size'=>10),array('align'=>'right'));
//$footer->addPreserveText('Page {PAGE} of {NUMPAGES}.',array('size'=>10));


$show = TOESHelper::getShowDetails($show_id);
$section->addTitle('');
$newheader = $section->createHeader();
$wlineStyle = array('weight' => 1, 'width' => 1050, 'height' => 10, 'color' => '#FFFFFF');
$newheader->addLine($wlineStyle);
$table1=$newheader->addTable();
$table1->addRow(500);
$cell1=$table1->addCell(4000);
$cell1->addText( $show->club_name,array('size'=>12),array('align'=>'left'));

$cell2=$table1->addCell(8000);
$cell2->addText(JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT'),array('size'=>12),array('align'=>'center'));

$cell3=$table1->addCell(4500);
$cell3->addText( $show->Show_location,array('size'=>12),array('align'=>'right'));
$cell3->addText( $show->show_dates,array('size'=>12),array('align'=>'right'));
$newheader->addText(' ',array('width'=>9000));

$newheader->addLine($lineStyle);

$i = 0;
$styleTable = array('borderSize' => 0, 'borderColor' => 'FFFFFF', 'cellMargin' => 10);
$styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');

$phpWord->setDefaultFontName('Tahoma');
$phpWord->setDefaultFontSize(14);
$styleTable = array('borderSize'=>0,'cellMargin'=>10,'borderColor' => 'FFFFFF');
$table = $section->addTable($styleTable);

	
$club = TOESHelper::getClub($show_id);
$show = TOESHelper::getShowDetails($show_id);
$show_days = TOESHelper::getShowDays($show_id);

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


/*$pdf->SetFillColor(255, 255, 255);

$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
$header_block .='<td style="width:30%" align="left"><span style="font-size:40px; ">' . $club->club_name . '</span></td>';
$header_block .='<td style="width:40%" align="center"><div style="font-size:45px">' . JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT') . '</div></td>';
$header_block .='<td style="width:30%" align="right"><span style="font-size:40px; ">' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
$pdf->SetFont('freesans', '', 12);
$pdf->writeHTML($header_block, true, false, false, false, '');*/
$table1=$newheader->addTable();
$table1->addRow(500);
$table1->addCell(2000,array('cellMargin'=>50))->addText('');
$table1->addCell(count($show_days)*1500,array('gridspan'=>count($show_days)))->addText(JText::_('NUMBER_OF_ENTRIES'),array('size'=>10),array('align'=>'center'));
$table1->addCell(count($show_days)*1500,array('gridspan'=>count($show_days)))->addText(JText::_('NUMBER_OF_PLACEHOLDERS'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1500,array('gridspan'=>2))->addText(JText::_('CAGES_PLACES'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('PERSONAL'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('BENCHING'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('GROOMING'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('TOTAL_FEES'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('TOTAL_PAID'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('TOTAL_DUE'),array('size'=>10),array('align'=>'center'));
$table1->addCell(15+(3-count($show_days))*1500)->addText(JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_PRIVATE_NOTE_TITLE'),array('size'=>10),array('align'=>'center'));
$table1->addRow(500);
$table1->addCell(2000)->addText(JText::_('EXHIBITOR'),array('size'=>10),array('align'=>'center'));
foreach ($show_days as $show_day)
{
	$table1->addCell(1000)->addText(date('D',  strtotime($show_day->show_day_date)),array('size'=>10),array('align'=>'center'));
	//$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
}
foreach ($show_days as $show_day)
{
	$table1->addCell(1000)->addText( date('D',  strtotime($show_day->show_day_date)),array('size'=>10),array('align'=>'center'));
	//$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
}
$table1->addCell(1000)->addText(JText::_('SINGLE'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('DOUBLE'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('CAGES'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('AREA'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText(JText::_('SPACE'),array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText($show->show_currency_used,array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText($show->show_currency_used,array('size'=>10),array('align'=>'center'));
$table1->addCell(1000)->addText($show->show_currency_used,array('size'=>10),array('align'=>'center'));
$table1->addCell(((15+(3-count($show_days))*1500)))->addText('');
/*$exibitor_header_block = '<table width="100%">
                    <tr>
                        ';

$exibitor_header_block .= '<td width="17%" align="left">&nbsp;</td>';
$exibitor_header_block .= '<td width="'.(count($show_days)*5).'%" align="center" colspan="3">' . JText::_('NUMBER_OF_ENTRIES') . '</td>';
$exibitor_header_block .= '<td width="'.(count($show_days)*5).'%" align="center" colspan="3">' . JText::_('NUMBER_OF_PLACEHOLDERS') . '</td>';
$exibitor_header_block .= '<td width="10%" align="center" colspan="2">' . JText::_('CAGES_PLACES') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('PERSONAL') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('BENCHING') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('GROOMING') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_FEES') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_PAID') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_DUE') . '</td>';
$exibitor_header_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center">' . JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_PRIVATE_NOTE_TITLE') . '</td>';

$exibitor_header_block .= '</tr><tr>';

$exibitor_header_block .= '<td width="17%" align="left">' . JText::_('EXHIBITOR') . '</td>';
foreach ($show_days as $show_day)
{
	$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
}
foreach ($show_days as $show_day)
{
	$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
}
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('SINGLE') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('DOUBLE') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('CAGES') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('AREA') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('SPACE') . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
$exibitor_header_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center"> &nbsp; </td>';

$exibitor_header_block .= '
                </tr>
            </table>';*/

/*$pdf->SetFont('freesans', '', 10);
$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_header_block, 0, true, false, false, false, '');
$pdf->Ln(2);*/

$i = 0;

$summary_single_cages_sum = 0;
$summary_double_cages_sum = 0;

$summary_total_fees_sum = 0;
$summary_fees_paid_sum = 0;

foreach ($exhibitors as $exhibitor) {
	//$table2=$section->addTable(array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')));
	$table2=$section->addTable(array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')));
	$table2->addRow(500);
	/*$exibitor_block = '<table cellpadding="2" cellspacing="2" style="background-color:' . (($i % 2) ? '#FFF' : '#DDD') . ';">
                        <tr>
                            ';*/

	$table2->addCell(2000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText($exhibitor->exhibitor,array('size'=>10),array('align'=>'left'));
	//$exibitor_block .= '<td width="17%" align="left">' . $exhibitor->exhibitor . '</td>';
	foreach ($show_days as $show_day)
	{
		$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD'),'size'=>10))->addText((isset($exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-'),array('size'=>10),array('align'=>'center') );
		//$exibitor_block .= '<td width="5%" align="center">' . (isset($exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-') . '</td>';
	}
	foreach ($show_days as $show_day)
	{
		$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText((isset($exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-'),array('size'=>10),array('align'=>'center'));
		//$exibitor_block .= '<td width="5%" align="center">' . (isset($exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-') . '</td>';
	}
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText($exhibitor->summary_single_cages,array('size'=>10),array('align'=>'center'));
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText($exhibitor->summary_double_cages,array('size'=>10),array('align'=>'center'));
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText(($exhibitor->summary_personal_cages ? JText::_('JYES') : JText::_('JNO')),array('size'=>10),array('align'=>'center'));
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText($exhibitor->summary_benching_area,array('size'=>10),array('align'=>'center'));
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText(($exhibitor->summary_grooming_space ? JText::_('JYES') : JText::_('JNO')),array('size'=>10),array('align'=>'center'));
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText($exhibitor->summary_total_fees,array('size'=>10),array('align'=>'center'));
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText($exhibitor->summary_fees_paid,array('size'=>10),array('align'=>'center'));
	$table2->addCell(1000,array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText(($exhibitor->summary_total_fees - $exhibitor->summary_fees_paid),array('size'=>10),array('align'=>'center'));
	$table2->addCell((15+(3-count($show_days))*1500),array('bgColor'=>(($i % 2) ? 'FFFFFF' : 'DDDDDD')))->addText(($exhibitor->summary_entry_clerk_private_note),array('size'=>10),array('align'=>'center'));
	/*$exibitor_block .= '<td width="5%" align="center">' . $exhibitor->summary_single_cages . '</td>';
	$exibitor_block .= '<td width="5%" align="center">' . $exhibitor->summary_double_cages . '</td>';
	$exibitor_block .= '<td width="5%" align="center">' . ($exhibitor->summary_personal_cages ? JText::_('JYES') : JText::_('JNO')) . '</td>';
	$exibitor_block .= '<td width="5%" align="center">' . $exhibitor->summary_benching_area . '</td>';
	$exibitor_block .= '<td width="5%" align="center">' . ($exhibitor->summary_grooming_space ? JText::_('JYES') : JText::_('JNO')) . '</td>';
	$exibitor_block .= '<td width="5%" align="center">' . $exhibitor->summary_total_fees . '</td>';
	$exibitor_block .= '<td width="5%" align="center">' . $exhibitor->summary_fees_paid . '</td>';
	$exibitor_block .= '<td width="5%" align="center">' . ($exhibitor->summary_total_fees - $exhibitor->summary_fees_paid) . '</td>';
	$exibitor_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center">' . ($exhibitor->summary_entry_clerk_private_note) . '</td>';

	$exibitor_block .= '
                    </tr>
                </table>';*/

	$summary_single_cages_sum += ($exhibitor->summary_single_cages ? $exhibitor->summary_single_cages : 0);
	$summary_double_cages_sum += ($exhibitor->summary_double_cages ? $exhibitor->summary_double_cages : 0);

	$summary_total_fees_sum += ($exhibitor->summary_total_fees ? $exhibitor->summary_total_fees : 0);
	$summary_fees_paid_sum += ($exhibitor->summary_fees_paid ? $exhibitor->summary_fees_paid : 0);

	/*$pdf->startTransaction();
	$block_page = $pdf->getPage();*/
	$print_block = 2; // 2 tries max
	/*while ($print_block > 0) {

		$pdf->SetFont('freesans', '', 10);
		//$pdf->writeHTML($exibitor_block, true, false, false, false, '');
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_block, 0, true, false, false, false, '');

		// do not split BLOCKS in multiple pages
		if ($pdf->getPage() == $block_page) {
			$print_block = 0;
		} else {
			// rolls back to the last (re)start
			$pdf = $pdf->rollbackTransaction();
			$pdf->AddPage();

			$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
			$header_block .='<td style="width:30%" align="left"><span style="font-size:40px; ">' . $club->club_name . '</span></td>';
			$header_block .='<td style="width:40%" align="center"><div style="font-size:45px">' . JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT') . '</div></td>';
			$header_block .='<td style="width:30%" align="right"><span style="font-size:40px; ">' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
			$pdf->SetFont('freesans', '', 12);
			$pdf->writeHTML($header_block, true, false, false, false, '');

			$exibitor_header_block = '<table width="100%">
                                <tr>
                                    ';

			$exibitor_header_block .= '<td width="17%" align="left">&nbsp;</td>';
			$exibitor_header_block .= '<td width="'.(count($show_days)*5).'%" align="center" colspan="3">' . JText::_('NUMBER_OF_ENTRIES') . '</td>';
			$exibitor_header_block .= '<td width="'.(count($show_days)*5).'%" align="center" colspan="3">' . JText::_('NUMBER_OF_PLACEHOLDERS') . '</td>';
			$exibitor_header_block .= '<td width="10%" align="center" colspan="2">' . JText::_('CAGES_PLACES') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('PERSONAL') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('BENCHING') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('GROOMING') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_FEES') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_PAID') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_DUE') . '</td>';
			$exibitor_header_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center">' . JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_PRIVATE_NOTE_TITLE') . '</td>';

			$exibitor_header_block .= '</tr><tr>';

			$exibitor_header_block .= '<td width="17%" align="left">' . JText::_('EXHIBITOR') . '</td>';
			foreach ($show_days as $show_day)
			{
				$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
			}
			foreach ($show_days as $show_day)
			{
				$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
			}
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('SINGLE') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('DOUBLE') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('CAGES') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('AREA') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('SPACE') . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
			$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
			$exibitor_header_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center"> &nbsp; </td>';

			$exibitor_header_block .= '
                            </tr>
                        </table>';

			$pdf->SetFont('freesans', '', 10);
			$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_header_block, 0, true, false, false, false, '');
			$pdf->Ln(2);

			$block_page = $pdf->getPage();
			--$print_block;
		}
	}*/
	$i++;
}

$section->addText();
//$section->addLine(array('weight' => 1, 'width' => 700, 'height' => 5, 'color' => '#000000'));
$table3=$section->addTable(array('borderTopSize'=>10));
$table3->addRow(500);

/*$exibitor_block = '<table cellpadding="2" cellspacing="2" style="width:100%;border-top:1px solid #000;">
                    <tr>
                        ';*/
$table3->addCell(2000)->addText(JText::_('COUNT'),array('size'=>10),array('align'=>'left'));
//$exibitor_block .= '<td width="17%" align="left">' . JText::_('COUNT') . '</td>';
foreach ($show_days as $show_day)
{
	$table3->addCell(1000)->addText((isset($show_days_count[$show_day->show_day_id])?$show_days_count[$show_day->show_day_id]:'0'),array('size'=>10),array('align'=>'left'));
	//$exibitor_block .= '<td width="5%" align="center">' . (isset($show_days_count[$show_day->show_day_id])?$show_days_count[$show_day->show_day_id]:'0') . '</td>';
}
foreach ($show_days as $show_day)
{
	$table3->addCell(1000)->addText((isset($placeholder_show_days_count[$show_day->show_day_id])?$placeholder_show_days_count[$show_day->show_day_id]:'0'),array('size'=>10),array('align'=>'left'));
	//$exibitor_block .= '<td width="5%" align="center">' . (isset($placeholder_show_days_count[$show_day->show_day_id])?$placeholder_show_days_count[$show_day->show_day_id]:'0') . '</td>';
}
$table3->addCell(1000)->addText($summary_single_cages_sum,array('size'=>10),array('align'=>'center'));
$table3->addCell(1000)->addText($summary_double_cages_sum,array('size'=>10),array('align'=>'center'));
$table3->addCell(1000)->addText('-',array('size'=>10),array('align'=>'center'));
$table3->addCell(1000)->addText('-',array('size'=>10),array('align'=>'center'));
$table3->addCell(1000)->addText('-',array('size'=>10),array('align'=>'center'));
$table3->addCell(1000)->addText($summary_total_fees_sum,array('size'=>10),array('align'=>'center'));
$table3->addCell(1000)->addText($summary_fees_paid_sum,array('size'=>10),array('align'=>'center'));
$table3->addCell(1000)->addText(($summary_total_fees_sum - $summary_fees_paid_sum),array('size'=>10),array('align'=>'center'));
$table3->addCell(((15+(3-count($show_days))*1500)))->addText('-',array('size'=>10),array('align'=>'center'));
/*$exibitor_block .= '<td width="5%" align="center">' . $summary_single_cages_sum . '</td>';
$exibitor_block .= '<td width="5%" align="center">' . $summary_double_cages_sum . '</td>';
$exibitor_block .= '<td width="5%" align="center"> - </td>';
$exibitor_block .= '<td width="5%" align="center"> - </td>';
$exibitor_block .= '<td width="5%" align="center"> - </td>';
$exibitor_block .= '<td width="5%" align="center">' . $summary_total_fees_sum . '</td>';
$exibitor_block .= '<td width="5%" align="center">' . $summary_fees_paid_sum . '</td>';
$exibitor_block .= '<td width="5%" align="center">' . ($summary_total_fees_sum - $summary_fees_paid_sum) . '</td>';
$exibitor_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center"> - </td>';

$exibitor_block .= '
                </tr>
            </table>';*/

/*$pdf->startTransaction();
$block_page = $pdf->getPage();*/
$print_block = 2; // 2 tries max
/*while ($print_block > 0) {

	$pdf->SetFont('freesans', '', 10);
	//$pdf->writeHTML($exibitor_block, true, false, false, false, '');
	$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_block, 0, true, false, false, false, '');

	// do not split BLOCKS in multiple pages
	if ($pdf->getPage() == $block_page) {
		$print_block = 0;
	} else {
		// rolls back to the last (re)start
		$pdf = $pdf->rollbackTransaction();
		$pdf->AddPage();

		$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
		$header_block .='<td style="width:30%" align="left"><span style="font-size:40px; ">' . $club->club_name . '</span></td>';
		$header_block .='<td style="width:40%" align="center"><div style="font-size:45px">' . JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT') . '</div></td>';
		$header_block .='<td style="width:30%" align="right"><span style="font-size:40px; ">' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
		$pdf->SetFont('freesans', '', 12);
		$pdf->writeHTML($header_block, true, false, false, false, '');

		$exibitor_header_block = '<table width="100%">
                                <tr>
                                    ';

		$exibitor_header_block .= '<td width="17%" align="left">&nbsp;</td>';
		$exibitor_header_block .= '<td width="'.(count($show_days)*5).'%" align="center" colspan="3">' . JText::_('NUMBER_OF_ENTRIES') . '</td>';
		$exibitor_header_block .= '<td width="'.(count($show_days)*5).'%" align="center" colspan="3">' . JText::_('NUMBER_OF_PLACEHOLDERS') . '</td>';
		$exibitor_header_block .= '<td width="10%" align="center" colspan="2">' . JText::_('CAGES_PLACES') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('PERSONAL') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('BENCHING') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('GROOMING') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_FEES') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_PAID') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('TOTAL_DUE') . '</td>';
		$exibitor_header_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center">' . JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_PRIVATE_NOTE_TITLE') . '</td>';
		
		$exibitor_header_block .= '</tr><tr>';

		$exibitor_header_block .= '<td width="17%" align="left">' . JText::_('EXHIBITOR') . '</td>';
		foreach ($show_days as $show_day)
		{
			$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
		}
		foreach ($show_days as $show_day)
		{
			$exibitor_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
		}
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('SINGLE') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('DOUBLE') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('CAGES') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('AREA') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . JText::_('SPACE') . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
		$exibitor_header_block .= '<td width="5%" align="center">' . $show->show_currency_used . '</td>';
		$exibitor_header_block .= '<td width="'.((15+(3-count($show_days))*5)).'%" align="center"> &nbsp; </td>';
		
		$exibitor_header_block .= '
                            </tr>
                        </table>';

		$pdf->SetFont('freesans', '', 10);
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_header_block, 0, true, false, false, false, '');
		$pdf->Ln(2);

		$block_page = $pdf->getPage();
		--$print_block;
	}
}*/

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if(!file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id))
    JFolder::create(JPATH_BASE. "/media/com_toes/DOCX/".$show_id, 0777);
else
	chmod (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id, 0777);

if(file_exists(JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'treasurer.docx'))
	unlink (JPATH_BASE. "/media/com_toes/DOCX".DS.$show_id.DS.'treasurer.docx');

write($phpWord, $writers,$show_id);
//Close and output PDF document


//============================================================+
// END OF FILE                                                
//============================================================+
