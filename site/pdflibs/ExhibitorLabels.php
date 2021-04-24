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

$db = JFactory::getDbo();
$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format ? $page_format : PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_EXHIBITOR_LABELS'));

// set default header data
$show = TOESHelper::getShowDetails($show_id);


// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 5, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(0);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, 0);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
//$pdf->setLanguageArray($l);
$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);

// ---------------------------------------------------------

$pdf->footer_text = date('M d, Y', time());

// add a page
$pdf->AddPage();

$exhibitors = TOESHelper::getShowExhibitors($show_id);

if(!$exhibitors)
	echo JText::_('COM_TOES_NO_EXHIBITORS').'<br/>';

$pdf->SetFillColor(255, 255, 255);

$i = 0;
$j = 0;

$exhibitor_block = '<table style="width:100%;"><tr><td colspan="3" style="height:40px;">&nbsp;</td></tr><tr>';

foreach ($exhibitors as $exhibitor) {
	
	if($i!= 0 && $i%3 == 0)
	{
		$exhibitor_block .= '</tr>
						<tr><td colspan="3" style="height:35px;">&nbsp;</td></tr>
					</table>';
		
		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {
			$pdf->SetFont('freesans', '', 10);
			$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');
			$pdf->Ln(1);

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

		$exhibitor_block = '<table style="width:100%;"><tr><td colspan="3" style="height:40px;">&nbsp;</td></tr><tr>';
	}

	$exhibitor_block .= '<td style="width:33%;"><strong>' . $exhibitor->user_name . '</strong></td>';
	$i++;
	
}

if($i%3!=0)
{
	while($i%3!=0)
	{
		$exhibitor_block .= '<td style="width:33%;">&nbsp;</td>';
		$i++;
	}
}

$exhibitor_block .= '</tr>
				<tr><td colspan="3" style="height:35px;">&nbsp;</td></tr>
			</table>';

$pdf->SetFont('freesans', '', 10);
$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');
$pdf->Ln(1);

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
/*
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
	JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'exhibitorlabels.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'exhibitorlabels.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'exhibitorlabels.pdf', 'F');
*/
$pdf->Output( $show_id . '_exhibitorlabels.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
