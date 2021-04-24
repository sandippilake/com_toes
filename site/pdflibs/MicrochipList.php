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
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_MICROCHIP_LIST'));

// set default header data
$header_logo = '/media/com_toes/images/paw32X32.png';
$pdf->SetHeaderData($header_logo, 10, JText::_('COM_TOES_MICROCHIP_LIST'), JText::_('COM_TOES_WEBSITE'));

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
$pdf->SetFont('ptsans', '', 10);

// add a page
$pdf->AddPage();

$db = JFactory::getDBO();
$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;
$whr[] = '`e`.`late_entry` = 0';

$query = TOESQueryHelper::getEntryFullViewQuery($whr);
$query->select('`cat`.`cat_id_chip_number`');

$query->group('`e`.`cat`');
$query->clear('order');
$query->order('`cprof`.`lastname`');

$db->setQuery($query);
$entries = $db->loadObjectList();

$final_entries = array();
foreach($entries as $entry)
{
    $final_entries[$entry->summary_user][] = $entry;
}

foreach($final_entries as $entries)
{
    $i = 0;
    $header_block = '<table style="padding:3px;margin:0;width:100%;">';
    foreach($entries as $entry)
    {
        if($i == 0)
        {
            $header_block .= '
                                <tr>';
            $header_block .= '
                                <td style="width:30%;border:1px solid #000;" rowspan="'.count($entries).'">'.strtoupper($entry->lastname.' '.$entry->firstname).'</td>
                                <td style="width:50%;border:1px solid #000;">&nbsp;&nbsp;'.strtoupper($entry->copy_cat_name).'</td>
                                <td style="width:20%;text-align:center;border:1px solid #000;">'.($entry->cat_id_chip_number?$entry->cat_id_chip_number:'-').'</td>
                            ';
            $header_block .= '</tr>';
        }
        else
        {
            $header_block .= '
                                <tr>';
            $header_block .= '
                                <td style="width:50%;border:1px solid #000;">&nbsp;&nbsp;'.strtoupper($entry->copy_cat_name).'</td>
                                <td style="width:20%;text-align:center;border:1px solid #000;">'.($entry->cat_id_chip_number?$entry->cat_id_chip_number:'-').'</td>
                            ';
            $header_block .= '</tr>';
        }
        $i++;
    }
    $header_block .= '
                    </table>';
    
    $pdf->startTransaction();
    $block_page = $pdf->getPage();
    $print_block = 2; // 2 tries max
    while ($print_block > 0) 
    {    
        //echo $header_block;
        $pdf->writeHTMLCell( 0 , 0, $pdf->GetX(), $pdf->GetY(), $header_block, 0, true, false, false, false, '');    
        
        if ($pdf->getPage() == $block_page) {
            $print_block = 0;
        } else {
            // rolls back to the last (re)start
            $pdf = $pdf->rollbackTransaction();
            $block_page = $pdf->getPage();
            --$print_block;
        }
    }
    $pdf->Ln(4);
}

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
/*
jimport('joomla.filesystem.folder');
if(!file_exists(TOES_PDF_PATH.DS.$show_id))
    JFolder::create(TOES_PDF_PATH.DS.$show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'microchip_list.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'microchip_list.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH.DS.$show_id.DS.'microchip_list.pdf', 'F');
*/
$pdf->Output( $show_id .'_microchip_list.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
