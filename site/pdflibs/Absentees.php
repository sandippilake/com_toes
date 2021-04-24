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
// setfontissue 		$this->SetFont('freesans', 'I', 8);

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
$pdf->SetTitle(JText::_('COM_TOES_ABSENTEES_REPORT'));

// set default header data
$show = TOESHelper::getShowDetails($show_id);

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

$pdf->SetFillColor(255, 255, 255);

$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
$header_block .='<td style="width:30%" align="left">&nbsp;</td>';
$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('COM_TOES_ABSENTEES_REPORT') . '</div></td>';
$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
// setfontissue $pdf->SetFont('freesans', '', 12);
$pdf->writeHTML($header_block, true, false, false, false, '');

$table_header_block = '<table width="100%">
                    <tr>
                        ';

$table_header_block .= '<td width="10%" align="left">' . JText::_('COM_TOES_NUMBER') . '</td>';
foreach ($show_days as $show_day)
{
	$table_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
}
$table_header_block .= '<td width="20%" >' . JText::_('COM_TOES_BREED') . '</td>';
$table_header_block .= '<td width="30%" >' . JText::_('COM_TOES_COLOR') . '</td>';
$table_header_block .= '<td width="10%" >' . JText::_('COM_TOES_SEX') . '</td>';
$table_header_block .= '<td width="'.((3-count($show_days))*5+15).'%" >' . JText::_('COM_TOES_ABSENTEES_CHANGES') . '</td>';

$table_header_block .= '
                </tr>
            </table>';

// setfontissue $pdf->SetFont('freesans', '', 10);
$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $table_header_block, 0, true, false, false, false, '');
$pdf->Ln(2);

$i = 0;

foreach ($entries as $entry) {
	$table_block = '<table cellpadding="2" cellspacing="2" style="background-color:' . (($i % 2) ? '#FFF' : '#DDD') . ';">
                        <tr>
                            ';

	$table_block .= '<td width="10%" align="left">' . $entry->catalog_number . '</td>';
	foreach ($show_days as $show_day)
	{
		$table_block .= '<td width="5%" align="center">&nbsp;</td>';
	}
	$table_block .= '<td width="20%" >' . $entry->breed_name . '</td>';
	$table_block .= '<td width="30%" >' . $entry->color_name . '</td>';
	$table_block .= '<td width="10%" >' . $entry->gender_short_name . '</td>';
	$table_block .= '<td width="'.((3-count($show_days))*5+15).'%" >&nbsp;</td>';

	$table_block .= '
                    </tr>
                </table>';

	$pdf->startTransaction();
	$block_page = $pdf->getPage();
	$print_block = 2; // 2 tries max
	while ($print_block > 0) {

// setfontissue 		$pdf->SetFont('freesans', '', 10);
		//$pdf->writeHTML($exibitor_block, true, false, false, false, '');
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $table_block, 0, true, false, false, false, '');

		// do not split BLOCKS in multiple pages
		if ($pdf->getPage() == $block_page) {
			$print_block = 0;
		} else {
			// rolls back to the last (re)start
			$pdf = $pdf->rollbackTransaction();
			$pdf->AddPage();

			$header_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr>';
			$header_block .='<td style="width:30%" align="left"><span>&nbsp;</span></td>';
			$header_block .='<td style="width:40%" align="center"><div style="font-size:20px">' . JText::_('COM_TOES_ABSENTEES_REPORT') . '</div></td>';
			$header_block .='<td style="width:30%" align="right"><span>' . $show->Show_location . '</span><br/><span>' . $show->show_dates . '</span></td></tr></table>';
// setfontissue 			$pdf->SetFont('freesans', '', 12);
			$pdf->writeHTML($header_block, true, false, false, false, '');

			$table_header_block = '<table width="100%">
								<tr>
									';

			$table_header_block .= '<td width="10%" align="left">' . JText::_('NUMBER') . '</td>';
			foreach ($show_days as $show_day)
			{
				$table_header_block .= '<td width="5%" align="center">' . date('D',  strtotime($show_day->show_day_date)) . '</td>';
			}
			$table_header_block .= '<td width="20%" >' . JText::_('COM_TOES_BREED') . '</td>';
			$table_header_block .= '<td width="30%" >' . JText::_('COM_TOES_COLOR') . '</td>';
			$table_header_block .= '<td width="10%" >' . JText::_('COM_TOES_SEX') . '</td>';
			$table_header_block .= '<td width="'.((3-count($show_days))*5+15).'%" >' . JText::_('COM_TOES_ABSENTEES_CHANGES') . '</td>';

			$table_header_block .= '
							</tr>
						</table>';

// setfontissue 			$pdf->SetFont('freesans', '', 10);
			$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $table_header_block, 0, true, false, false, false, '');
			$pdf->Ln(2);

			$block_page = $pdf->getPage();
			--$print_block;
		}
	}
	$i++;
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

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'absentees.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'absentees.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'absentees.pdf', 'F');
*/
$pdf->Output( $show_id . '_absentees.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
