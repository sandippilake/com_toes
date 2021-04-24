<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

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
$pdf->SetTitle(JText::_('COM_TOES_BENCHING_CARDS_REPORT'));

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

if(!$exhibitors) {
	echo JText::_('COM_TOES_NO_EXHIBITORS');
	return;
}

$pdf->SetFillColor(255, 255, 255);

$i = 0;
$x = 5;
$y = $last_y = $last_ending_y = 10;

$w = ($pdf->getPageWidth() / 2) - 10;
$h = 40;

foreach ($exhibitors as $exhibitor) {
	
	$number = ($exhibitor->summary_double_cages * 2) + $exhibitor->summary_single_cages + $exhibitor->summary_grooming_space;
	

	$exhibitor_block = '<table cellpadding="10" width="100%"><tr><td>';
	$exhibitor_block .= '<table width="100%"><tr>';
	$exhibitor_block .= '<td width="80%"><br/><span style="font-weight:bolder;font-size:21px;">'.$exhibitor->user_name.'</span></td>';
	$exhibitor_block .= '<td align="right" width="20%"><br/><span style="font-weight:600;font-size:25px;">'.$number.'</span></td>';
	$exhibitor_block .= '</tr></table>';
	$exhibitor_block .= '<table width="100%"><tr>';
	$exhibitor_block .= '<td>'.JText::_('COM_TOES_SHOW_SUMMARY_BENCHING_REQUEST').'</td>';
	$exhibitor_block .= '</tr><tr>';
	$exhibitor_block .= '<td><span style="font-weight:bolder;font-size:13px;">'.($exhibitor->summary_benching_request?$exhibitor->summary_benching_request:JText::_('JNONE')).'</span></td>';
	$exhibitor_block .= '</tr></table>';
	
	$exhibitor_block .= '<br/><br/><table width="100%"><tr>';
	$exhibitor_block .= '<td>'.JText::_('COM_TOES_ENTRIES').': '.$exhibitor->entries.'</td>';
	$exhibitor_block .= '<td>'.JText::_('COM_TOES_GROOMS').': '.$exhibitor->summary_grooming_space.'</td>';
	$exhibitor_block .= '</tr><tr>';
	$exhibitor_block .= '<td>'.JText::_('COM_TOES_DOUBLE_SPACES').': '.$exhibitor->summary_double_cages.'</td>';
	$exhibitor_block .= '<td>'.JText::_('COM_TOES_SINGLE_SPACES').': '.$exhibitor->summary_single_cages.'</td>';
	$exhibitor_block .= '</tr></table>';
	$exhibitor_block .= '</td></tr></table>';

	$pdf->startTransaction();
	$block_page = $pdf->getPage();
	$print_block = 2; // 2 tries max
	while ($print_block > 0) {

		//$pdf->SetFont('freesans','',8);
		$pdf->writeHTMLCell($w, $h, $x, $y, $exhibitor_block, true, true, false, false, false, '');

		// do not split BLOCKS in multiple pages
		if ($pdf->getPage() == $block_page) {
			$print_block = 0;
		} else {
			// rolls back to the last (re)start
			$pdf = $pdf->rollbackTransaction();
			$pdf->AddPage();

			$x = 5;
			$y = $last_y = $last_ending_y = 10;
			
			$block_page = $pdf->getPage();
			--$print_block;
		}
	}
	
	if ($i % 2 == 0) {
		$x = $pdf->getPageWidth() / 2 + 5;
		$y = $last_y;
		$last_ending_y = $pdf->GetY();
	} else {
		$x = 5;
		if($pdf->GetY() < $last_ending_y) {
			$y = $last_y = $last_ending_y;
		} else {
			$y = $last_y = $pdf->GetY();
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

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'benchingcards.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'benchingcards.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'benchingcards.pdf', 'F');
*/
$pdf->Output( $show_id . '_benchingcards.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
