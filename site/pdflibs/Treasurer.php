<?php

jimport('tcpdf.tcpdf');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	public $footer_text = '';

	public function getRemainingHeight() {
		list($this->x, $this->y) = $this->checkPageRegions(0, $this->x, $this->y);
		return ($this->h - $this->lMargin - $this->y);
	}

	// Page footer
	public function Footer() {
		$cur_y = -15;
		$this->SetTextColor(0, 0, 0);
		//set style for cell border
		$line_width = 0.85 / $this->k;
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		//print document barcode
		$barcode = $this->getBarcode();
		if (!empty($barcode)) {
			$this->Ln($line_width);
			$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
			$style = array(
				'position' => $this->rtl ? 'R' : 'L',
				'align' => $this->rtl ? 'R' : 'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0, 0, 0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
		}
		if (empty($this->pagegroups)) {
			$pagenumtxt = $this->l['w_page'] . ' ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages();
		} else {
			$pagenumtxt = $this->l['w_page'] . ' ' . $this->getPageNumGroupAlias() . ' / ' . $this->getPageGroupAlias();
		}
		$this->SetY($cur_y);
		// Set font
// setfontissue		$this->SetFont('freesans', 'I', 8);

		if ($this->getRTL()) {
			$this->SetX($this->original_rMargin);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'R');
			$this->Cell(0, 0, $this->footer_text, 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
		} else {
			$this->SetX($this->original_lMargin);
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
			$this->Cell(0, 0, $this->footer_text, 'T', 0, 'R', 0, '', 0, false, 'T', 'M');
		}
	}

}

$db = JFactory::getDbo();
$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format ? $page_format : PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new MYPDF('L', PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_TREASURER'));

// set default header data
$show = TOESHelper::getShowDetails($show_id);
$show_days = TOESHelper::getShowDays($show_id);

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

// ---------------------------------------------------------

$pdf->footer_text = date('M d, Y', time());

// set font
// setfontissue $pdf->SetFont('freesans', '', 12);

// add a page
$pdf->AddPage();

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

$pdf->SetFillColor(255, 255, 255);

$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
$header_block .='<td style="width:30%" align="left"><span>' . $club->club_name . '</span></td>';
$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT') . '</div></td>';
$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
// setfontissue $pdf->SetFont('freesans', '', 12);
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

// setfontissue $pdf->SetFont('freesans', '', 10);
$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_header_block, 0, true, false, false, false, '');
$pdf->Ln(2);

$i = 0;

$summary_single_cages_sum = 0;
$summary_double_cages_sum = 0;

$summary_total_fees_sum = 0;
$summary_fees_paid_sum = 0;

foreach ($exhibitors as $exhibitor) {
	$exibitor_block = '<table cellpadding="2" cellspacing="2" style="background-color:' . (($i % 2) ? '#FFF' : '#DDD') . ';">
                        <tr>
                            ';

	$exibitor_block .= '<td width="17%" align="left">' . $exhibitor->exhibitor . '</td>';
	foreach ($show_days as $show_day)
	{
		$exibitor_block .= '<td width="5%" align="center">' . (isset($exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-') . '</td>';
	}
	foreach ($show_days as $show_day)
	{
		$exibitor_block .= '<td width="5%" align="center">' . (isset($exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id]) ? $exhibitor_placeholder_show_day_count[$exhibitor->summary_user][$show_day->show_day_id] : '-') . '</td>';
	}
	$exibitor_block .= '<td width="5%" align="center">' . $exhibitor->summary_single_cages . '</td>';
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
                </table>';

	$summary_single_cages_sum += ($exhibitor->summary_single_cages ? $exhibitor->summary_single_cages : 0);
	$summary_double_cages_sum += ($exhibitor->summary_double_cages ? $exhibitor->summary_double_cages : 0);

	$summary_total_fees_sum += ($exhibitor->summary_total_fees ? $exhibitor->summary_total_fees : 0);
	$summary_fees_paid_sum += ($exhibitor->summary_fees_paid ? $exhibitor->summary_fees_paid : 0);

	$pdf->startTransaction();
	$block_page = $pdf->getPage();
	$print_block = 2; // 2 tries max
	while ($print_block > 0) {

// setfontissue		$pdf->SetFont('freesans', '', 10);
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
			$header_block .='<td style="width:30%" align="left"><span>' . $club->club_name . '</span></td>';
			$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT') . '</div></td>';
			$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
// setfontissue			$pdf->SetFont('freesans', '', 12);
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

// setfontissue			$pdf->SetFont('freesans', '', 10);
			$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_header_block, 0, true, false, false, false, '');
			$pdf->Ln(2);

			$block_page = $pdf->getPage();
			--$print_block;
		}
	}
	$i++;
}

$exibitor_block = '<table cellpadding="2" cellspacing="2" style="width:100%;border-top:1px solid #000;">
                    <tr>
                        ';

$exibitor_block .= '<td width="17%" align="left">' . JText::_('COUNT') . '</td>';
foreach ($show_days as $show_day)
{
	$exibitor_block .= '<td width="5%" align="center">' . (isset($show_days_count[$show_day->show_day_id])?$show_days_count[$show_day->show_day_id]:'0') . '</td>';
}
foreach ($show_days as $show_day)
{
	$exibitor_block .= '<td width="5%" align="center">' . (isset($placeholder_show_days_count[$show_day->show_day_id])?$placeholder_show_days_count[$show_day->show_day_id]:'0') . '</td>';
}
$exibitor_block .= '<td width="5%" align="center">' . $summary_single_cages_sum . '</td>';
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
            </table>';

$pdf->startTransaction();
$block_page = $pdf->getPage();
$print_block = 2; // 2 tries max
while ($print_block > 0) {

// setfontissue	$pdf->SetFont('freesans', '', 10);
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
		$header_block .='<td style="width:30%" align="left"><span>' . $club->club_name . '</span></td>';
		$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('COM_TOES_SHOW_ENTRY_N_FINANCIAL_REPORT') . '</div></td>';
		$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
// setfontissue		$pdf->SetFont('freesans', '', 12);
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

// setfontissue		$pdf->SetFont('freesans', '', 10);
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exibitor_header_block, 0, true, false, false, false, '');
		$pdf->Ln(2);

		$block_page = $pdf->getPage();
		--$print_block;
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
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'treasurer.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'treasurer.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'treasurer.pdf', 'F');
*/
$pdf->Output( $show_id . '_treasurer.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
