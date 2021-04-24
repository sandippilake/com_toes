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
				'position' => $this->rtl?'R':'L',
				'align' => $this->rtl?'R':'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'padding' => 0,
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => false
			);
			$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
		}
		if (empty($this->pagegroups)) {
			$pagenumtxt = $this->l['w_page'].' '.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
		} else {
			$pagenumtxt = $this->l['w_page'].' '.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
		}
		$this->SetY($cur_y);
		// Set font
		$this->SetFont('helvetica', 'I', 8);

		if ($this->getRTL()) {
			$this->SetX($this->original_rMargin);
			$this->Cell(0, 0, $this->footer_text, 'T', 0, 'R', 0, '', 0, false, 'T', 'M');
		} else {
			$this->SetX($this->original_lMargin);
			$this->Cell(0, 0, $this->footer_text, 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
		}
	}
}
$db = JFactory::getDbo();
$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format?$page_format:PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle(JText::_('COM_TOES_MASTER_EXHIBITOR_LIST'));

// set default header data
$header_logo = '/media/com_toes/images/paw32X32.png';
$pdf->SetHeaderData($header_logo, 10, JText::_('COM_TOES_MASTER_EXHIBITOR_LIST'), JText::_('COM_TOES_WEBSITE'));

$show = TOESHelper::getShowDetails($show_id);

$pdf->footer_text = $show->club_name.' - '.$show->Show_location.' - '.$show->show_dates;

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
//$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('ptsans', '', 11);

// add a page
$pdf->AddPage();

$db = JFactory::getDBO();

$query = "SELECT CONCAT_WS( ', ', `cb`.`lastname` , `cb`.`firstname` ) AS `Exhibitor` ,
GROUP_CONCAT( DISTINCT(`e`.`catalog_number`) ORDER BY CAST( `e`.`catalog_number` AS UNSIGNED ) ) AS `Entries`,
CONCAT_WS( ' ', `cb`.`cb_address1` , `cb`.`cb_address2` , `cb`.`cb_address3` ) AS `Address` ,
CONCAT_WS( ' ', if(`cprofcity`.`name` IS NOT NULL,`cprofcity`.`name`,`cb`.`cb_city`) , `cb`.`cb_zip` , `cprofstate`.`name` ) AS `City` ,
`cprofcntry`.`name` AS `Country` 
FROM `#__toes_entry` AS `e`
LEFT JOIN `#__toes_entry_status` AS `es` ON `e`.`status` = `es`.`entry_status_id`
LEFT JOIN `#__toes_summary` AS `s` ON `s`.`summary_id` = `e`.`summary`
LEFT JOIN `#__comprofiler` AS `cb` ON `cb`.`user_id` = `s`.`summary_user` 
LEFT JOIN `#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `cb`.`cb_country`
LEFT JOIN `#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `cb`.`cb_state`
LEFT JOIN `#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `cb`.`cb_city`
WHERE `e`.`entry_show` = $show_id AND ( (`es`.`entry_status` = 'Accepted') OR(`es`.`entry_status` = 'Confirmed') OR (`es`.`entry_status` = 'Confirmed & Paid') )
GROUP BY `e`.`entry_show`, `e`.`summary` ORDER BY `cb`.`lastname` ASC , `cb`.`firstname` ASC;";
//
$query = "SELECT CONCAT_WS( ', ', `cb`.`lastname` , `cb`.`firstname` ) AS `Exhibitor` ,
GROUP_CONCAT( DISTINCT(`e`.`catalog_number`) ORDER BY CAST( `e`.`catalog_number` AS UNSIGNED ) ) AS `Entries`,
CONCAT_WS( ' ', `cb`.`cb_address1` , `cb`.`cb_address2` , `cb`.`cb_address3` ) AS `Address` ,
CONCAT_WS( ' ', `cb`.`cb_city`, `cb`.`cb_zip` , `cb`.`cb_state` ) AS `City` ,
`cb`.`cb_country` AS `Country` 
FROM `#__toes_entry` AS `e`
LEFT JOIN `#__toes_entry_status` AS `es` ON `e`.`status` = `es`.`entry_status_id`
LEFT JOIN `#__toes_summary` AS `s` ON `s`.`summary_id` = `e`.`summary`
LEFT JOIN `#__comprofiler` AS `cb` ON `cb`.`user_id` = `s`.`summary_user` 
WHERE `e`.`entry_show` = $show_id AND ( (`es`.`entry_status` = 'Accepted') OR(`es`.`entry_status` = 'Confirmed') OR (`es`.`entry_status` = 'Confirmed & Paid') )
GROUP BY `e`.`entry_show`, `e`.`summary` ORDER BY `cb`.`lastname` ASC , `cb`.`firstname` ASC;";


$db->setQuery($query);
$exhibitor_list = $db->loadObjectList();

$i = 0;
foreach($exhibitor_list as $exhibitor)
{
    $address = '';
    if($exhibitor->Address)
        $address .= $exhibitor->Address.',  ';
    if($exhibitor->City)
        $address .= $exhibitor->City.',  ';
    if($exhibitor->Country)
        $address .= $exhibitor->Country;
        
    $pdf->Ln();    
    if($i%2)
        $pdf->SetFillColor(235, 235, 235);
    else
        $pdf->SetFillColor(255, 255, 255);

    $pdf->MultiCell(100, 0, strtoupper($exhibitor->Exhibitor), 0, 'L', 1, 0);
    $pdf->MultiCell(0, 0, $exhibitor->Entries, 0, 'R', 1, 1);
    $pdf->MultiCell(0, 0, "         ".strtoupper($address), 0, 'L', 1, 1);

    $i++;
}

$pdf->SetFillColor(255, 255, 255);
$pdf->MultiCell(100, 0, ' ', 0, 'L', 1, 0);
$pdf->MultiCell(0, 0, ' ', 0, 'R', 1, 1);

$pdf->MultiCell(100, 0, JText::_('COM_TOES_TOTAL_NUMBER_OF_EXHIBITORS_MASTER'), 0, 'L', 1, 0);
$pdf->MultiCell(0, 0, $i, 0, 'R', 1, 1);
// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if(!file_exists(TOES_PDF_PATH.DS.$show_id))
    JFolder::create (TOES_PDF_PATH.DS.$show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'master_exibitor_list.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'master_exibitor_list.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH.DS.$show_id.DS.'master_exibitor_list.pdf', 'F');

//============================================================+
// END OF FILE                                                
//============================================================+
