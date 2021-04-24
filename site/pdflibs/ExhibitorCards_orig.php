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
		$this->SetFont('helvetica', 'I', 8);

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

$entry_status = array(
	'New',
	'Accepted',
	'Confirmed',
	'Confirmed & Paid'
);

$db = JFactory::getDbo();
$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format ? $page_format : PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_EXHIBITOR_CARDS'));

// set default header data
$show = TOESHelper::getShowDetails($show_id);

$isContinuous = ($show->show_format == 'Continuous')?1:0;

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

// add a page
$pdf->AddPage();

$exhibitors = TOESHelper::getShowExhibitors($show_id);

if(!$exhibitors)
	echo JText::_('COM_TOES_NO_EXHIBITORS').'<br/>';

$pdf->SetFillColor(255, 255, 255);

$i = 0;

foreach ($exhibitors as $exhibitor) {

	$entries = TOESHelper::getEntries($exhibitor->user_id, $show_id, $entry_status);
	$placeholders = TOESHelper::getPlaceholders($exhibitor->user_id, $show_id, $entry_status);
	
	if (!$entries && !$placeholders)
		continue;		
	
	$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
	$header_block .='<td style="width:30%" align="left"><span>' . $show->club_name . '</span></td>';
	$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('Exhibitor Cards') . '</div></td>';
	$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
	$pdf->SetFont('ptsanscaption', '', 12);
	$pdf->writeHTML($header_block, true, false, false, false, '');

	$exhibitor_block = '<table cellpadding="2" cellspacing="2" style="font-weight:bold;">
							<tr>
								<td align="left">' . $exhibitor->user_name . '</td>
							</tr>
							<tr>
								<td align="left">' . JText::_('COM_TOES_BENCHING_AREA_COLUMN_TITLE').' : '.$exhibitor->summary_benching_area . '</td>
							</tr>
						</table>';

	$pdf->SetFont('freesans', '', 12);
	$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');
	$pdf->Ln(4);

	$exhibitor_header_block = '<table style="width:100%;border-bottom:1px solid #000;">
						<tr>
							';
	$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CATALOG_NUMBER_HEADER') . '</td>';
	$exhibitor_header_block .= '<td width="20%" align="left">' . JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER') . '</td>';
	$exhibitor_header_block .= '<td width="15%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER') . '</td>';
	$exhibitor_header_block .= '<td width="15%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER') . '</td>';
	$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER') . '</td>';
	$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_FORSALE_HEADER') . '</td>';
	$exhibitor_header_block .= '<td width="20%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER') . '</td>';

	$exhibitor_header_block .= '
					</tr>
				</table>';

	$pdf->SetFont('freesans', '', 10);
	$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_header_block, 0, true, false, false, false, '');
	$pdf->Ln(2);
	
	foreach($entries as $entry)
	{
		if($isContinuous)
			$showdays = JText::_('JALL');
		else
			$showdays = $entry->showdays;
		
		$exhibitor_block = '<table cellpadding="2" cellspacing="2">
							<tr>
								';
		$exhibitor_block .= '<td width="10%" align="left">' . $entry->catalog_number . '</td>';
		$exhibitor_block .= '<td width="20%" align="center">' . $entry->cat_prefix_abbreviation . ' ' . $entry->cat_title_abbreviation . ' ' . $entry->copy_cat_name . ' ' . $entry->cat_suffix_abbreviation . '</td>';
		$exhibitor_block .= '<td width="15%" align="center">' . $showdays . '</td>';
		$exhibitor_block .= '<td width="15%" align="center">' . $entry->Show_Class . '</td>';
		$exhibitor_block .= '<td width="10%" align="center">' . ($entry->exhibition_only ? JText::_('JYES') : JText::_('JNO')) . '</td>';
		$exhibitor_block .= '<td width="10%" align="center">' . ($entry->for_sale ? JText::_('JYES') : JText::_('JNO')) . '</td>';
		$exhibitor_block .= '<td width="20%" align="center">' . (($entry->congress)?$entry->congress:'-') . '</td>';

		$exhibitor_block .= '
						</tr>
					</table>';

		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {

			$pdf->SetFont('freesans', '', 10);
			//$pdf->writeHTML($exibitor_block, true, false, false, false, '');
			$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				$print_block = 0;
			} else {
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();
				
				$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
				$header_block .='<td style="width:30%" align="left"><span>' . $show->club_name . '</span></td>';
				$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('Exhibitor Cards') . '</div></td>';
				$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', 12);
				$pdf->writeHTML($header_block, true, false, false, false, '');

				$exhibitor_block .= '<table cellpadding="2" cellspacing="2" style="font-weight:bold;">
										<tr>
											<td align="left">' . $exhibitor->user_name . '</td>
										</tr>
									</table>';

				$pdf->SetFont('freesans', '', 12);
				$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');
				$pdf->Ln(4);

				$exhibitor_header_block = '<table style="width:100%;border-bottom:1px solid #000;">
									<tr>
										';
				$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CATALOG_NUMBER_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="20%" align="left">' . JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="15%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="15%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_FORSALE_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="20%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER') . '</td>';

				$exhibitor_header_block .= '
								</tr>
							</table>';

				$pdf->SetFont('freesans', '', 10);
				$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_header_block, 0, true, false, false, false, '');
				$pdf->Ln(2);

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
	}
	
	foreach($placeholders as $placeholder)
	{
		if($isContinuous)
			$showdays = JText::_('JALL');
		else
			$showdays = $placeholder->showdays;

		$exhibitor_block = '<table cellpadding="2" cellspacing="2">
							<tr>
								';
		$exhibitor_block .= '<td width="10%" align="left"> - </td>';
		$exhibitor_block .= '<td width="20%" align="center">' . JText::_('COM_TOES_PLACEHOLDER') . '</td>';
		$exhibitor_block .= '<td width="15%" align="center">' . $showdays . '</td>';
		$exhibitor_block .= '<td width="15%" align="center"> - </td>';
		$exhibitor_block .= '<td width="10%" align="center"> - </td>';
		$exhibitor_block .= '<td width="10%" align="center"> - </td>';
		$exhibitor_block .= '<td width="20%" align="center"> - </td>';

		$exhibitor_block .= '
						</tr>
					</table>';

		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {

			$pdf->SetFont('freesans', '', 10);
			//$pdf->writeHTML($exibitor_block, true, false, false, false, '');
			$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				$print_block = 0;
			} else {
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
				$header_block .='<td style="width:30%" align="left"><span>' . $show->club_name . '</span></td>';
				$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('Exhibitor Cards') . '</div></td>';
				$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', 12);
				$pdf->writeHTML($header_block, true, false, false, false, '');

				$exhibitor_block .= '<table cellpadding="2" cellspacing="2" style="font-weight:bold;">
										<tr>
											<td align="left">' . $exhibitor->user_name . '</td>
										</tr>
									</table>';

				$pdf->SetFont('freesans', '', 12);
				$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');
				$pdf->Ln(4);

				$exhibitor_header_block = '<table style="width:100%;border-bottom:1px solid #000;">
									<tr>
										';
				$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CATALOG_NUMBER_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="20%" align="left">' . JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="15%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="15%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="10%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_FORSALE_HEADER') . '</td>';
				$exhibitor_header_block .= '<td width="20%" align="center">' . JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER') . '</td>';

				$exhibitor_header_block .= '
								</tr>
							</table>';

				$pdf->SetFont('freesans', '', 10);
				$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_header_block, 0, true, false, false, false, '');
				$pdf->Ln(2);

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
	}
	$pdf->AddPage();
}

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
	JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'exhibitorcards.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'exhibitorcards.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'exhibitorcards.pdf', 'F');

//============================================================+
// END OF FILE                                                
//============================================================+
