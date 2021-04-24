<?php

jimport('tcpdf.tcpdf');

set_time_limit(3600);

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	public $footer_text = '';

	public function getRemainingHeight() {
		list($this->x, $this->y) = $this->checkPageRegions(0, $this->x, $this->y);
		return ($this->h - $this->lMargin - $this->y);
	}

	public function getPgBuffer($page_number) {
		return $this->getPageBuffer($page_number);
	}

	public function setPgBuffer($page_number, $data) {
		$this->setPageBuffer($page_number, $data);
	}

	public function customFooter() {
		$this->setFooter();
	}

	// Page footer
	public function Footer() {
		// Set font
		$this->SetFont('ptsans', '', 10);

		$this->SetY(-15);
		$this->SetX($this->footer_x);
		//$this->Cell($this->footer_w, 0, JText::_('COM_TOES_JUDGE').' : '.$this->judge_name);
		switch($this->show_count_footer)
		{
			case 1:
				$entry_block = '<table>
		                    <tr style="line-height:150%;">
		                        <td colspan="2" style="text-align:center;">' . JText::_('COM_TOES_COUNT_ON_PAGE').'______ / ______' . JText::_('COM_TOES_COUNT_SO_FAR').'</td>
		                    </tr>
		                    <tr>
		                        <td style="width:50%;text-align:left;">' . JText::_('COM_TOES_JUDGE') . ' : &nbsp;&nbsp;&nbsp;</td>
		                        <td style="width:50%;text-align:right;">' . JText::_('COM_TOES_RING') . ' : &nbsp;&nbsp;&nbsp;</td>
		                    </tr>
		                </table>';
				break;
			default :
				$entry_block = '<table>
		                    <tr style="line-height:150%;">
		                        <td colspan="2" style="text-align:center;">&nbsp;</td>
		                    </tr>
		                    <tr>
		                        <td style="width:50%;text-align:left;">' . JText::_('COM_TOES_JUDGE') . ' : &nbsp;&nbsp;&nbsp;</td>
		                        <td style="width:50%;text-align:right;">' . JText::_('COM_TOES_RING') . ' : &nbsp;&nbsp;&nbsp;</td>
		                    </tr>
		                </table>';
				break;
		}
		//$this->SetFont('cmunrm', '', 12);
		$this->writeHTMLCell($this->footer_w, 0, $this->GetX(), $this->GetY(), $entry_block, 0, true, false, false, false, '');
	}

}

$db = JFactory::getDBO();

$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format ? $page_format : PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $page_format, true, 'UTF-8', false);

$x = 5;
$w = ($pdf->getPageWidth() / 2) - 15;
$pdf->footer_x = $x;
$pdf->footer_w = $w;

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_BLANK_JUDGE_BOOK'));

$params = JComponentHelper::getParams('com_toes');

// set default header data
$tica_logo = 'https://www.tica.org/media/com_toes/images/logo/ticawithtag_blackfill100.png';

$show = TOESHelper::getShowDetails($show_id);

$is_continuous = ($show->show_format == 'Continuous') ? true : false;
$is_alternative = ($show->show_format == 'Alternative') ? true : false;

$pdf->footer_text = $show->club_name . ' - ' . $show->Show_location . ' - ' . $show->show_dates;
$pdf->show_count_footer = 1;

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
//$pdf->setPrintFooter(FALSE);

$pdf->setColor('text', 0, 0, 0);

// ---------------------------------------------------------
// add a page
$pdf->AddPage();

$page_number = 1;
$prev_pdf_page = 1;

for($j=0; $j<2; $j++) {
	/* if($j>1)
	  break; */
	
	if ($j % 2 == 0) {
		$x = 5;
	} else {
		$x = $pdf->getPageWidth() / 2 + 10;
	}

	$pdf->setPage($prev_pdf_page);

	$w = ($pdf->getPageWidth() / 2) - 15;

	$pdf->footer_x = $x;
	$pdf->footer_w = $w;
	
	$show_day_date = JText::_('JDATE')." : &nbsp;&nbsp;&nbsp;&nbsp;";
	
	$show_class_block = '<table><tr><td style="width:15%">&nbsp;</td>';
	$show_class_block .= '<td style="width:70%"><span style="text-align:center; font-size:25px; ">' . JText::_('COM_TOES_JUDGES_BOOK') . '</span></td>';
	$show_class_block .= '<td style="text-align:right;width:15%;color:#000000;">page &nbsp;&nbsp;&nbsp;</td></tr><tr><td colspan="3" style="width:100%">';
	$show_class_block .= '<span style="text-align:center;">' . strtoupper($show->club_name) . '<br/>' . $show_day_date . '</span></td>';
	$show_class_block .= '</tr></table>';

	$pdf->SetFont('cmunrm', '', 12);
	$pdf->writeHTMLCell($w, 0, $x, 5, $show_class_block, 0, true);
	$pdf->ln(2);
	
	$header_block = '<table style="text-align:center;padding:3px;margin:0;">
						<tr>
							';
	$header_block .= '<td style="width:33%;border:1px solid #999;">' . JText::_('COM_TOES_ENTRY_AGE') . '</td>
							<td style="width:33%;border:1px solid #999;">' . JText::_('COM_TOES_ENTRY_NO') . '</td>
							<td style="width:33%;border:1px solid #999;">' . JText::_('COM_TOES_ENTRY_AWARD') . '</td>
						';
	$header_block .= '
						</tr>
					</table>';
	$pdf->SetFont('cmunrm', '', 12);
	$pdf->writeHTMLCell($w, 0, $x, $pdf->GetY(), $header_block, 0, true, false, false, false, '');

	for($i=0;$i<32;$i++) {
		$entry_block = '<table style="text-align:center;padding:5px;margin:0;">
							<tr>
								<td style="width:33%;border:1px solid #999;">&nbsp;</td>
								<td style="width:33%;border:1px solid #999;">&nbsp;</td>
								<td style="width:33%;border:1px solid #999;">&nbsp;</td>
							</tr>
						</table>';
		$pdf->SetFont('cmunrm', '', 10);
		$pdf->writeHTMLCell($w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
	}
	
	if ($j % 2 != 0) {
		$pdf->customFooter();
	}	
}

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
/*
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id)) {
	JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
} else {
	chmod(TOES_PDF_PATH . DS . $show_id, 0777);
}

if (file_exists(TOES_PDF_PATH . DS . $show_id . DS . 'blankjudgesbook.pdf')) {
	unlink(TOES_PDF_PATH . DS . $show_id . DS . 'blankjudgesbook.pdf');
}

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'blankjudgesbook.pdf', 'F');
*/
$pdf->Output( $show_id . '_blankjudgesbook.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
