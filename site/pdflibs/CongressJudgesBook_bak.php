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

        public function getPgBuffer($page_number){
            return $this->getPageBuffer($page_number);
        }

        public function setPgBuffer($page_number, $data){
            $this->setPageBuffer($page_number, $data);
        }

        public function customFooter(){
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
								<td style="width:50%;text-align:left;">'.JText::_('COM_TOES_JUDGE').' : '.$this->judge_name.'</td>
								<td style="width:50%;text-align:right;">'.JText::_('COM_TOES_RING').' : '.$this->ring_number.'</td>
							</tr>
						</table>';
					break;
				default:
					$entry_block = '<table>
						<tr style="line-height:150%;">
							<td colspan="2" style="text-align:center;">&nbsp;</td>
						</tr>
						<tr>
							<td style="width:50%;text-align:left;">'.JText::_('COM_TOES_JUDGE').' : '.$this->judge_name.'</td>
							<td style="width:50%;text-align:right;">'.JText::_('COM_TOES_RING').' : '.$this->ring_number.'</td>
						</tr>
					</table>';
			}

            //$this->SetFont('cmunrm', '', 12);
            $this->writeHTMLCell( $this->footer_w, 0, $this->GetX(), $this->GetY(), $entry_block, 0, true, false, false, false, '');            
	}
}

$selected_values = $app->input->getVar('judge_id', '');
$single_copy = $app->input->getInt('copy', 1);
$cover_copy = $app->input->getInt('coverpage', 0);
$file = $app->input->getVar('file', '');

$selected_rings = array();
if($selected_values)
{
    $selected_rings = explode(',', $selected_values);
}

$skip_division_best = array(
            'LH PNB',
            'SH PNB',
            'LH ANB',
            'SH ANB',
            'LH HHP Kitten',
            'SH HHP Kitten'
);


$skip_breed_best = array(
            'LH HHP',
            'SH HHP',
            'LH HHP Kitten',
            'SH HHP Kitten'
);

$default_color = '#000000';
/*
$breed_color_title_color = '#C08E82';
$best_division_color = '#2727D3';
$best_breed_color = '#46A2C7';
$page_index_color = '#2727D3';
*/

$breed_color_title_color = $best_division_color = $best_breed_color = $page_index_color = $default_color;

$db = JFactory::getDBO();

$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;

$query = TOESQueryHelper::getJudgesBookCongressData($whr);

$db->setQuery($query);
$entries = $db->loadObjectList();

$query = "SELECT `show_day_id`, `show_day_date` FROM `#__toes_show_day` WHERE `show_day_show` = {$show_id} ORDER BY `show_day_date`";
$db->setQuery($query);
$temp_show_days = $db->loadObjectList();

foreach($temp_show_days as $show_day)
{
    $show_days[$show_day->show_day_id] = $show_day;
}

$query = "SELECT `r`.`ring_id`, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation` 
        FROM `#__toes_ring` AS `r`
        LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`) 
        WHERE (`r`.`ring_format` = 3) AND `r`.`ring_show` = {$show_id}
        ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`";
$db->setQuery($query);
$temp_rings = $db->loadObjectList();

foreach ($temp_rings as $ring)
{
    $rings[$ring->ring_id] = $ring;
}

$judges = array();
foreach ($rings as $ring)
{
    $judges[$ring->ring_judge] = TOESHelper::getJudgeInfo($ring->ring_judge);
}

$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format?$page_format:PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_CONGRESS_JUDGES_BOOK'));

// set default header data
$tica_logo = 'https://www.tica.org/media/com_toes/images/logo/ticawithtag_blackfill100.png';

$show = TOESHelper::getShowDetails($show_id);

$is_continuous = ($show->show_format=='Continuous')?true:false;
$is_alternative = ($show->show_format=='Alternative')?true:false;

$pdf->footer_text = $show->club_name.' - '.$show->Show_location.' - '.$show->show_dates;

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

$params = JComponentHelper::getParams('com_toes');

// ---------------------------------------------------------

// add a page
$pdf->AddPage();
$pdf->show_count_footer = 0;

$previous_class = '';
$previous_breed = '';
$previous_division = '';
$previous_color = '';
$previous_catalog_number = '';

$previous_breed_entries = 1;
$previous_division_entries = 1;
$first_half_last_page = 1;

$final_entries = array();
foreach ($entries as $entry)
{
    if($selected_rings)
    {
        if(in_array($entry->ring_id, $selected_rings))
            $final_entries[$entry->ring_id][] = $entry;
    }
    else
        $final_entries[$entry->ring_id][] = $entry;
}

$temp_entries = array();
foreach ($final_entries as $ring_number => $congress_entries) {
	$temp_entries[$ring_number] = TOESHelper::aasort($congress_entries, 'catalog_number');
}
$final_entries = $temp_entries;

$prev_pdf_page = 1;

$j=0;
$total = 0;
$processed = 0;
foreach($final_entries as $ring_number=>$entries)
{
    $total += count($entries);
}

$fp = fopen(TOES_LOG_PATH.DS.$file.'.txt', 'w');
$data = array(
    'total'=>$total,
    'processed'=>$processed
);
fputs($fp, serialize($data));
fclose($fp);

foreach($final_entries as $ring_number=>$ring_entries)
{
    /*if($j>1)
       break;*/
    
    if($j!=0)
    {
        if($j%2 == 0)
        {
            $curPage = $pdf->getPage();
            if($curPage > $first_half_last_page)
            {
                $prev_pdf_page = $curPage + 1;
                $pdf->setPage($curPage);
                $pdf->AddPage();
            }
            else
            {
                $prev_pdf_page = $first_half_last_page + 1;
                $pdf->setPage($first_half_last_page);
                $pdf->AddPage();
            }
        }
        else 
        {
            $pdf->customFooter();
            $first_half_last_page = $pdf->getPage();
        }
    }

    if($j%2 == 0)
    {
        $x = 5;
    }
    else
    {
        $x = $pdf->getPageWidth()/2 + 10;
    }

    $pdf->setPage($prev_pdf_page);

    $w = ($pdf->getPageWidth()/2) - 15;

    $pdf->footer_x = $x;
    $pdf->footer_w = $w;
    
    if(is_numeric($rings[$ring_number]->ring_judge))
        $pdf->judge_name =  $judges[$rings[$ring_number]->ring_judge]->name;
    else
        $pdf->judge_name = '';

    if(is_numeric($rings[$ring_number]->ring_number))
        $pdf->ring_number = $rings[$ring_number]->ring_number;
    else
        $pdf->ring_number = ' ';

    $pdf->ring_name = $rings[$ring_number]->ring_name;
     
    if($cover_copy)
    {
        $show_day_date = '';
        if($is_continuous)
            $show_day_date .= $show->show_dates;
        else
            $show_day_date .= date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));
        
        if($is_alternative)
        {
            if($rings[$ring_number]->ring_timing == 1)
                $show_day_date .= " AM";
            else
                $show_day_date .= " PM";
        }
        $front_page = '<div>&nbsp;</div>';
        $front_page .= '<div style="text-align:center;">'.JText::_('COM_TOES_TICA_FULL').'</div>';

        $front_page .= '<div style="text-align:center;"><img src="'.$tica_logo.'" /></div>';
        $front_page .= '<div style="text-align:center;font-weight:bold;font-size:25px;">'.JText::_('COM_TOES_JUDGES_BOOK').'</div>';

        //$front_page .= '<div style="text-align:center;">'.JText::_('COM_TOES_JUDGES_BOOK_NOTE').'</div><br/>';
        $front_page .= '<div style="">'.JText::_('COM_TOES_JUDGES_BOOK_JUDGE').' : '.$pdf->judge_name.'</div>';
        $front_page .= '<div style="">'.JText::_('COM_TOES_JUDGES_BOOK_CLUB').' : '.$show->club_name.'</div>';
        $front_page .= '<div style="">'.JText::_('COM_TOES_JUDGES_BOOK_PLACE').' : '.$show->Show_location.'</div>';
        $front_page .= '<div style="">'.JText::_('COM_TOES_JUDGES_BOOK_DATE').' : '.$show_day_date.'</div>';
        $front_page .= '<div style="">'.JText::_('COM_TOES_JUDGES_BOOK_AB').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.JText::_('COM_TOES_JUDGES_BOOK_LH').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.JText::_('COM_TOES_JUDGES_BOOK_SH').'</div><br/><br/>';

        $front_page .= '<div style="text-align:center;">'.JText::_('COM_TOES_JUDGES_BOOK_AWARD_NOTE').'</div><br/>';
        $front_page .= '<div style="text-align:center;">'.JText::_('COM_TOES_JUDGES_BOOK_SIGN').'</div><br/>';
        $front_page .= '<div style="">'.JText::_('COM_TOES_JUDGES_BOOK_RING_CLERK').'</div>';
        $front_page .= '<div style="">'.JText::_('COM_TOES_JUDGES_BOOK_RING_ASS_CLERK').'</div><br/><br/>';

        $front_page .= '<div style="text-align:center;">'.JText::_('COM_TOES_JUDGES_BOOK_NOTICE').'</div>';


        $pdf->SetFont('cmunrm', '', 12);
        $pdf->writeHTMLCell( $w, 0, $x, 5, $front_page, 0, true, false, false, false, '');

        $j++;
        $fp = fopen(TOES_LOG_PATH.DS.$file.'.txt', 'w');
        $data = array(
            'total'=>count($final_entries),
            'processed'=>$j
        );
        fputs($fp, serialize($data));
        fclose($fp);        
        continue;

        /*
        $buffer = $pdf->getPgBuffer($pdf->getPage());

        $pdf->setPrintFooter(FALSE);
        $pdf->AddPage();
        $pdf->setPrintFooter(TRUE);
    
        if(!$single_copy)
        {
            for ($index = 0; $index < 2 ; $index++) 
            {
                $pdf->setPgBuffer($pdf->getPage(), $buffer);

                $pdf->setPrintFooter(FALSE);
                $pdf->AddPage();
                $pdf->setPrintFooter(TRUE);
            }
        }
        */
    }

    $previous_class = '';
    $previous_breed = '';
    $previous_division = '';
    $previous_color = '';
    $previous_catalog_number = '';
    
    $previous_breed_entries = 1;
    $previous_division_entries = 1;

    $cur = 0;
    
    $page_number = 1;

	$pdf->show_count_footer = 1;
	
	if($is_alternative)
	{
		$entries = array();
		foreach($ring_entries as $entry) {
			if ($rings[$ring_number]->ring_timing == 1 && !$entry->entry_participates_AM)
				continue;
			if ($rings[$ring_number]->ring_timing == 2 && !$entry->entry_participates_PM)
				continue;
			$entries[] = $entry;
		}
	}
	else
	{
		$entries = $ring_entries;
	}

	if($is_continuous)
		$show_day_date = $show->show_dates;
	else
		$show_day_date = date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));
	if($is_alternative)
	{
		if($rings[$ring_number]->ring_timing == 1)
			$show_day_date .= " AM";
		else
			$show_day_date .= " PM";
	}
	$show_class_block  = '<table style=""><tr><td style="width:15%">&nbsp;</td>';
	$show_class_block .= '<td style="width:70%"><span style="text-align:center; font-size:25px; ">'.JText::_('COM_TOES_JUDGES_BOOK').'</span></td>';
	$show_class_block .= '<td style="text-align:right;width:15%;color:'.$page_index_color.';">page '.$page_number.'</td></tr><tr><td colspan="3" style="width:100%">';
	$show_class_block .= '<span style="text-align:center; ">'.strtoupper($show->club_name).' <br/>'.$show_day_date.'</span>';
	//$show_class_block .= '<div style="text-align:center; ">'.$pdf->ring_name.' '.JText::_('CONGRESS').' - '.strtoupper($entry->show_class).'</div></td>';
	$show_class_block .= '<div style="text-align:center; ">'.$pdf->ring_name.'</div></td>';
	$show_class_block .= '</tr></table>';

	$pdf->SetFont('cmunrm', '', 12);
	$pdf->writeHTMLCell( $w, 0, $x, 5, $show_class_block, 0, true, false, false, false, '');
	$pdf->ln(2);

	$header_block = '<table style="text-align:center;padding:3px;margin:0;">
						<tr>
							';
	$header_block .= '<td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AGE').'</td>
							<td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_NO').'</td>
							<td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AWARD').'</td>
						';
	$header_block .= '
						</tr>
					</table>';
	$pdf->SetFont('cmunrm', '', 12);
	$pdf->writeHTMLCell( $w , 0, $x, $pdf->GetY(), $header_block, 0, true, false, false, false, '');
	$page_number++;

    foreach($entries as $entry)
    {
		/*if($previous_class != $entry->show_class)
        {
            if($previous_class != '')
            {
                $buffer = $pdf->getPgBuffer($pdf->getPage());

                if($j%2 == 1)
                    $pdf->customFooter();
                $pdf->AddPage();

                if(!$single_copy)
                {
                    for ($index = 0; $index < 2 ; $index++) 
                    {
                        $pdf->setPgBuffer($pdf->getPage(), $buffer);

                        if($j%2 == 1)
                            $pdf->customFooter();
                        $pdf->AddPage();
                    }
                }
            }

            if($is_continuous)
                $show_day_date = $show->show_dates;
            else
                $show_day_date = date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));
			if($is_alternative)
			{
				if($rings[$ring_number]->ring_timing == 1)
					$show_day_date .= " AM";
				else
					$show_day_date .= " PM";
			}
            $show_class_block  = '<table style=""><tr><td style="width:15%;color:'.$page_index_color.';">page '.$page_number.'</td>';
            $show_class_block .= '<td style="width:70%"><span style="text-align:center; font-size:25px; ">'.JText::_('COM_TOES_JUDGES_BOOK').'</span>';
            $show_class_block .= '</td><td style="width:15%">&nbsp;</td></tr><tr><td colspan="3" style="width:100%">';
            $show_class_block .= '<span style="text-align:center; ">'.strtoupper($show->club_name).' <br/>'.$show_day_date.'</span>';
            $show_class_block .= '<div style="text-align:center; ">'.$pdf->ring_name.' '.JText::_('CONGRESS').' - '.strtoupper($entry->show_class).'</div></td>';
            $show_class_block .= '</tr></table>';

            $pdf->SetFont('cmunrm', '', 12);
            $pdf->writeHTMLCell( $w, 0, $x, 5, $show_class_block, 0, true);
            $pdf->ln(2);

            $header_block = '<table style="text-align:center;padding:3px;margin:0;">
                                <tr>
                                    ';
            $header_block .= '<td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AGE').'</td>
                                    <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_NO').'</td>
                                    <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AWARD').'</td>
                                ';
            $header_block .= '
                                </tr>
                            </table>';
            $pdf->SetFont('cmunrm', '', 12);
            $pdf->writeHTMLCell( $w , 0, $x, $pdf->GetY(), $header_block, 0, true, false, false, false, '');
            $page_number++;

            $previous_class = $entry->show_class;
            $previous_breed = '';
            $previous_division = '';
            $previous_color = '';
        }*/

        $pdf->startTransaction();
        $block_page = $pdf->getPage();
        $print_block = 2; // 2 tries max
        while ($print_block > 0) 
        {
            if($previous_catalog_number != $entry->catalog_number)
            {
				if ($previous_breed != $entry->breed_abbreviation) {
					$previous_breed_entries = 1;
					$previous_division_entries = 1;
				}
				else
				{
					$previous_breed_entries++;

					if ($previous_division != $entry->catalog_division)
						$previous_division_entries = 1;
					else
						$previous_division_entries++;
				}

				if($previous_breed != $entry->breed_abbreviation || $previous_division != $entry->catalog_division || $previous_color != $entry->judges_book_color)
                {
                    if($previous_breed != $entry->breed_abbreviation)
                    {                    
                        $breed_block='<table style="font-weight:bold;text-align:center;padding:5px;margin:0;" ><tr><td style="width:99%;color:'.$breed_color_title_color.';border:1px solid #999;">'.strtoupper($entry->breed_name).'</td></tr></table>';
                        $pdf->SetFont('ptsans', '', 10);
                        $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $breed_block, 0, true, false, false, false, '');
                    }
                    //else if($previous_class == $entry->show_class && $previous_breed != '' && $previous_division == $entry->catalog_division && $print_block == 2)
					else if($previous_breed != '' && $previous_division == $entry->catalog_division && $print_block == 2)
                    {
		                if($show->show_print_extra_line_at_end_of_color_class_in_judges_book)
		                {
	                    	$entry_block = '<table style="text-align:center;padding:5px;margin:0;">
	                                            <tr>
	                                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
	                                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
	                                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
	                                            </tr>
	                                        </table>';
	                        $pdf->SetFont('cmunrm', '', 10);
	                        $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
	                	}
                    }

                    if($show->show_print_division_title_in_judges_books)
                    {
                    	if ($previous_class != $entry->show_class || $previous_breed != $entry->breed_abbreviation || $previous_division != $entry->catalog_division) {
                    		$pdf->SetFont('ptsans', '', 10);
							$division = TOESHelper::replaceJudgeBookDivisionNames($entry->catalog_division);
							$breed_block = '<table style="text-align:center;padding:5px;margin:0;" ><tr><td style="width:99%;color:' . $breed_color_title_color . ';border:1px solid #999;">' . strtoupper($division) . '</td></tr></table>';
                    		$pdf->writeHTMLCell($w, 0, $x, $pdf->GetY(), $breed_block, 0, true, false, false, false, '');
                    	}
                    }
                    
                    $breed_block='<table style="text-align:center;padding:5px;margin:0;" ><tr><td style="width:99%;color:'.$breed_color_title_color.';border:1px solid #999;">'.strtoupper($entry->judges_book_color).'</td></tr></table>';
                    $pdf->SetFont('ptsans', '', 10);
                    $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $breed_block, 0, true, false, false, false, '');
                }

				//$is_breeder = TOESHelper::is_catbreeder($judges[$rings[$ring_number]->ring_judge]->user_id, $entry->cat_id);
				$is_breeder = false;

                $entry_block = '<table style="text-align:center;;padding:5px;margin:0;">
                                    <tr>
                                        <td style="width:33%;border:1px solid #999;">'.$entry->judges_book_age_and_gender.'</td>
                                        <td style="width:33%;border:1px solid #999;">'.$entry->catalog_number.'</td>
                                        <td style="width:33%;border:1px solid #999;">'.($is_breeder?'PO':'&nbsp;').'</td>
                                    </tr>
                                </table>';

                $pdf->SetFont('cmunrm', '', 10);
                $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
            }

			$next = $cur + 1;
			if (in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($entries) ||
					( isset($entries[$next]) && ($entry->breed_abbreviation != @$entries[$next]->breed_abbreviation || $entry->catalog_division != @$entries[$next]->catalog_division))) {
                if($show->show_print_extra_line_at_end_of_color_class_in_judges_book)
                {
					$entry_block = '<table style="text-align:center;padding:5px;margin:0;">
										<tr>
											<td style="border:1px solid #999;width:33%;">&nbsp;</td>
											<td style="border:1px solid #999;width:33%;">&nbsp;</td>
											<td style="border:1px solid #999;width:33%;">&nbsp;</td>
										</tr>
									</table>';
	
					$pdf->SetFont('cmunrm', '', 10);
					$pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
                }

				$judge_block = '<table style="text-align:center;font-weight:bold;padding:5px;margin:0;">
									';
				for($i = 1; $i <= $previous_division_entries; $i++) {
					if ($i == 4)
						break;

					$judge_block .= '<tr>
									';
					if (!in_array($entry->show_class, $skip_breed_best) && (($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && ($entry->breed_abbreviation != @$entries[$next]->breed_abbreviation))))
						$judge_block .= '<td style="border:1px solid #999;width:66%;text-align:right; color:' . $best_breed_color . ';">' . JText::_('COM_TOES_SHORT_BEST_BREED_DIVISION_ENTRY_' . $i) . ' ' . $entry->breed_abbreviation . '</td>
									';
					else
						$judge_block .= '<td style="border:1px solid #999;width:66%;text-align:right; color:' . $best_division_color . ';">' . JText::_('COM_TOES_SHORT_BEST_DIVISION_ENTRY_' . $i) . '</td>
									';
					$judge_block .= '<td style="width:33%;border:1px solid #999;"></td>';

					$judge_block .= '</tr>';
				}

                if($show->show_print_extra_lines_for_bod_and_bob_in_judges_book)
                {
					for($i = $i; $i <= 3; $i++) {
						$judge_block .= '<tr>
											<td style="border:1px solid #999;width:66%;">&nbsp;</td>
											<td style="border:1px solid #999;width:33%;">&nbsp;</td>
										</tr>';
					}
                }

				$judge_block .= '
							</table>';

				$pdf->SetFont('ptsansnarrow', '', 10);
				$pdf->writeHTMLCell($w, 0, $x, $pdf->GetY(), $judge_block, 0, true, false, false, false, '');

				/* if(($i != 3) && !(!in_array($entry->show_class, $skip_breed_best) && ($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && ($entry->show_class != $entries[$next]->show_class || $entry->breed_abbreviation != $entries[$next]->breed_abbreviation))))
				  {
				  $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
				  } */
				if (!in_array($entry->show_class, $skip_breed_best) && ($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && ($entry->show_class != @$entries[$next]->show_class || $entry->breed_abbreviation != @$entries[$next]->breed_abbreviation))) {
					$pdf->Ln(3);
					$pdf->Line($x, $pdf->GetY(), $x + $w, $pdf->GetY());
					$pdf->Ln(3);
				}
			}

            // do not split BLOCKS in multiple pages
            if ($pdf->getPage() == $block_page) {
                if($previous_catalog_number != $entry->catalog_number)
                {
                    if($previous_breed != $entry->breed_abbreviation)
                    {
                        $previous_division='';
                        $previous_color = '';
                        $previous_breed = $entry->breed_abbreviation;
                    }

                    if($previous_division != $entry->catalog_division)
                    {
                        $previous_color = '';
                        $previous_division = $entry->catalog_division;
                    }

                    if($previous_color != $entry->judges_book_color)
                        $previous_color = $entry->judges_book_color;

                    $previous_catalog_number = $entry->catalog_number;
                }

                $print_block = 0;
            } else {
				$previous_breed_entries--;
				$previous_division_entries--;
                // rolls back to the last (re)start
                $pdf = $pdf->rollbackTransaction();

                $buffer = $pdf->getPgBuffer($pdf->getPage());

                if($j%2 == 1)
                    $pdf->customFooter();
                $pdf->AddPage();

                if(!$single_copy)
                {
                    for ($index = 0; $index < 2 ; $index++) 
                    {
                        $pdf->setPgBuffer($pdf->getPage(), $buffer);

                        if($j%2 == 1)
                            $pdf->customFooter();
                        $pdf->AddPage();
                    }
                }
				$pdf->show_count_footer = 1;

                if($is_continuous)
                    $show_day_date = $show->show_dates;
                else
                    $show_day_date = date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));
				if($is_alternative)
				{
					if($rings[$ring_number]->ring_timing == 1)
						$show_day_date .= " AM";
					else
						$show_day_date .= " PM";
				}
                $show_class_block  = '<table style=""><tr><td style="width:15%">&nbsp;</td>';
                $show_class_block .= '<td style="width:70%"><span style="text-align:center; font-size:25px; ">'.JText::_('COM_TOES_JUDGES_BOOK').'</span></td>';
                $show_class_block .= '<td style="text-align:right;width:15%;color:'.$page_index_color.';">page '.$page_number.'</td></tr><tr><td colspan="3" style="width:100%">';
                $show_class_block .= '<span style="text-align:center; ">'.strtoupper($show->club_name).' <br/>'.$show_day_date.'</span>';
                //$show_class_block .= '<div style="text-align:center; ">'.$pdf->ring_name.' '.JText::_('CONGRESS').' - '.strtoupper($entry->show_class).'</div></td>';
				$show_class_block .= '<div style="text-align:center; ">'.$pdf->ring_name.'</div></td>';
                $show_class_block .= '</tr></table>';

                $pdf->SetFont('cmunrm', '', 12);
                $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $show_class_block, 0, true, false, false, false, '');
				$pdf->ln(2);
                $page_number++;

                $header_block = '<table style="text-align:center;padding:3px;margin:0;">
                                    <tr>
                                        ';
                $header_block .= '<td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AGE').'</td>
                                        <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_NO').'</td>
                                        <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AWARD').'</td>
                                    ';
                $header_block .= '
                                    </tr>
                                </table>';
                $pdf->SetFont('cmunrm', '', 12);
                $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $header_block, 0, true, false, false, false, '');

                $block_page = $pdf->getPage();
                --$print_block;
            }
        }
        $cur++;
        $processed++;
        $fp = fopen(TOES_LOG_PATH.DS.$file.'.txt', 'w');
        $data = array(
            'total'=>$total,
            'processed'=>$processed
        );
        fputs($fp, serialize($data));
        fclose($fp);

        if(in_array($entry->show_class,$skip_breed_best))
        {
            $previous_breed_entries = 0;
        }
        else if( $previous_breed_entries > $previous_division_entries && ($cur == count($entries) || (isset($entries[$cur]) && ($entry->breed_abbreviation != @$entries[$cur]->breed_abbreviation) )))
        {
            $pdf->startTransaction();
            $block_page = $pdf->getPage();
            $print_block = 2; // 2 tries max
            while ($print_block > 0) 
            {
                $entry_block = '<table style="text-align:center;padding:5px;margin:0;">
                                    <tr>
                                        <td style="border:1px solid #999;width:33%;">&nbsp;</td>
                                        <td style="border:1px solid #999;width:33%;">&nbsp;</td>
                                        <td style="border:1px solid #999;width:33%;">&nbsp;</td>
                                    </tr>
                                </table>';

                $pdf->SetFont('cmunrm', '', 10);
                //$pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');

                $judge_block = '<table style="text-align:center;font-weight:bold;;padding:5px;margin:0;">
                                    ';
                for($i=1; $i<= $previous_breed_entries; $i++)
                {
                    if($i == 4)
                        break;

                    $judge_block .= '<tr>
                                    ';

                    $judge_block .= '<td style="border:1px solid #999;width:66%;text-align:right; color:'.$best_breed_color.';">'.JText::_('COM_TOES_SHORT_BEST_BREED_ENTRY_'.$i).' '.$entry->breed_abbreviation.'</td>
                                    ';

                    $judge_block .= '<td style="border:1px solid #999;width:33%"></td>';

                    $judge_block .= '</tr>';
                }

                if($show->show_print_extra_lines_for_bod_and_bob_in_judges_book)
                {
	                for($i=$i; $i <= 3; $i++)
	                {
	                    $judge_block .= '<tr>
	                                        <td style="border:1px solid #999;width:66%;">&nbsp;</td>
	                                        <td style="border:1px solid #999;width:33%;">&nbsp;</td>
										</tr>';
					}
				}

                $judge_block .= '
                            </table>';

                $pdf->SetFont('ptsansnarrow', '', 10);
                $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $judge_block, 0, true, false, false, false, '');
                
                //$pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
                $pdf->Ln(3);
                $pdf->Line($x, $pdf->GetY(), $x+$w, $pdf->GetY());
                $pdf->Ln(3);
                
                // do not split BLOCKS in multiple pages
                if ($pdf->getPage() == $block_page) {
                    $print_block = 0;
                } else {
                    // rolls back to the last (re)start
                    $pdf = $pdf->rollbackTransaction();
                    $buffer = $pdf->getPgBuffer($pdf->getPage());

                    if($j%2 == 1)
                        $pdf->customFooter();
                    $pdf->AddPage();

                    if(!$single_copy)
                    {
                        for ($index = 0; $index < 2 ; $index++) 
                        {
                            $pdf->setPgBuffer($pdf->getPage(), $buffer);

                            if($j%2 == 1)
                                $pdf->customFooter();
                            $pdf->AddPage();
                        }
                    }
					$pdf->show_count_footer = 1;

                    if($is_continuous)
                        $show_day_date = $show->show_dates;
                    else
                        $show_day_date = date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));
					if($is_alternative)
					{
						if($rings[$ring_number]->ring_timing == 1)
							$show_day_date .= " AM";
						else
							$show_day_date .= " PM";
					}
                    $show_class_block  = '<table style=""><tr><td style="width:15%">&nbsp;</td>';
                    $show_class_block .= '<td style="width:70%"><span style="text-align:center; font-size:25px; ">'.JText::_('COM_TOES_JUDGES_BOOK').'</span></td>';
                    $show_class_block .= '<td style="text-align:right;width:15%;color:'.$page_index_color.';">page '.$page_number.'</td></tr><tr><td colspan="3" style="width:100%">';
                    $show_class_block .= '<span style="text-align:center; ">'.strtoupper($show->club_name).' <br/>'.$show_day_date.'</span>';
                    $show_class_block .= '<div style="text-align:center; ">'.$pdf->ring_name.' '.JText::_('CONGRESS').' - '.strtoupper($entry->show_class).'</div></td>';
                    $show_class_block .= '</tr></table>';
        
                    $pdf->SetFont('cmunrm', '', 12);
                    $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $show_class_block, 0, true, false, false, false, '');
					$pdf->ln(2);
                    $page_number++;

                    $header_block = '<table style="text-align:center;padding:3px;margin:0;" >
                                        <tr>
                                            ';
                    $header_block .= '<td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AGE').'</td>
                                            <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_NO').'</td>
                                            <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AWARD').'</td>
                                        ';
                    $header_block .= '
                                        </tr>
                                    </table>';
                    $pdf->SetFont('cmunrm', '', 12);
                    $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $header_block, 0, true, false, false, false, '');

                    $block_page = $pdf->getPage();
                    --$print_block;
                }
            }
        }
        
        /*if($cur == count($entries) || ( isset($entries[$cur]) && $previous_class != @$entries[$cur]->show_class))
        {
            $class = str_replace('LH ', '', $previous_class);
            $class = str_replace('SH ', '', $class);

            $special_case = false;
            if(strstr($class, 'NT') || strstr($class, 'PNB') || strstr($class, 'ANB'))
            {
                $special_case = true;
            }
            
            /*if($rings[$ring_number]->ring_format == '2')
                $class_to_print = $previous_class;
            else
            {
                //$class_to_print = 'AB '.$class;
                $class_to_print = $class;
            }*/
            
            /*$class_to_print = $pdf->ring_name;
            
            if($special_case)
                $class_to_print = $class;
            
            if((!$special_case && $rings[$ring_number]->ring_format == '2') || ($cur == count($entries) || (isset($entries[$cur]) && !strstr(@$entries[$cur]->show_class, $class))))
            {
                $buffer = $pdf->getPgBuffer($pdf->getPage());

                if($j%2 == 1)
                    $pdf->customFooter();
                $pdf->AddPage();

                if(!$single_copy)
                {
                    for ($index = 0; $index < 2 ; $index++) 
                    {
                        $pdf->setPgBuffer($pdf->getPage(), $buffer);

                        if($j%2 == 1)
                            $pdf->customFooter();
                        $pdf->AddPage();
                    }
                }
                    
                if($is_continuous)
                    $show_day_date = $show->show_dates;
                else
                    $show_day_date = date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));
				if($is_alternative)
				{
					if($rings[$ring_number]->ring_timing == 1)
						$show_day_date .= " AM";
					else
						$show_day_date .= " PM";
				}
                $show_class_block  = '<table style=""><tr><td style="width:15%;color:'.$page_index_color.';">page '.$page_number.'</td>';
                $show_class_block .= '<td style="width:70%"><span style="text-align:center; font-size:25px; ">'.JText::_('COM_TOES_JUDGES_BOOK').'</span>';
                $show_class_block .= '</td><td style="width:15%">&nbsp;</td></tr><tr><td colspan="3" style="width:100%">';
                $show_class_block .= '<div style="text-align:center;"><img src="'.$tica_logo.'" /></div>';
                $show_class_block .= '<div style="text-align:center; ">'.strtoupper($show->club_name).' <br/>'.$show_day_date.'</div>';
                $show_class_block .= '<div style="text-align:center; font-size:25px">'.JText::_('COM_TOES_FINALS').'</div>';
                $show_class_block .= '<div style="text-align:center; ">'.JText::_('COM_TOES_BEST').' '.strtoupper($class_to_print).' '.JText::_('COM_TOES_AWARDS').'</div></td>';
                $show_class_block .= '</tr></table>';

                $pdf->SetFont('cmunrm', '', 12);
                $pdf->writeHTMLCell( $w, 0, $x, 5, $show_class_block, 0, true, false, false, false, '');
                $page_number++;
                $pdf->ln(2);

                $entry_block = '<table style="text-align:center;padding:5px;margin:0;">
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AWARD').'</td>
                                        <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_NO').'</td>
                                    </tr>
                                </table>';

                $pdf->SetFont('cmunrm', '', 10);
                $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');

                $entry_block = '<table style="padding:5px;margin:0;">
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_FIRST_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_SECOND_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_THIRD_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_FORTH_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_FIFTH_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_SIXTH_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_SEVENTH_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_EIGHTH_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_NINTH_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_TENTH_BEST').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;">&nbsp;</td>
                                        <td style="width:33%;">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td style="width:66%;border:1px solid #999;">'.JText::_('COUNT').'</td>
                                        <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                                    </tr>
                                </table>';

                $pdf->SetFont('cmunrm', '', 10);
                $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
                $pdf->ln(1);
            }
        }*/
    }
    
    for($k=0;$k<1;$k++)
    {
        $buffer = $pdf->getPgBuffer($pdf->getPage());

        if($j%2 == 1)
            $pdf->customFooter();
        $pdf->AddPage();

        if(!$single_copy)
        {
            for ($index = 0; $index < 2 ; $index++) 
            {
                $pdf->setPgBuffer($pdf->getPage(), $buffer);

                if($j%2 == 1)
                    $pdf->customFooter();
                $pdf->AddPage();
            }
        }
		$pdf->show_count_footer = 0;

        if($is_continuous)
            $show_day_date = $show->show_dates;
        else
            $show_day_date = date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));
		if($is_alternative)
		{
			if($rings[$ring_number]->ring_timing == 1)
				$show_day_date .= " AM";
			else
				$show_day_date .= " PM";
		}
        $show_class_block  = '<table style=""><tr><td style="width:15%">&nbsp;</td>';
        $show_class_block .= '<td style="width:70%"><span style="text-align:center; font-size:25px; ">'.JText::_('COM_TOES_JUDGES_BOOK').'</span></td>';
        $show_class_block .= '<td style="text-align:right;width:15%;color:'.$page_index_color.';">page '.$page_number.'</td></tr><tr><td colspan="3" style="width:100%">';
        $show_class_block .= '<div style="text-align:center;"><img src="'.$tica_logo.'" /></div>';
        $show_class_block .= '<div style="text-align:center; ">'.strtoupper($show->club_name).' <br/>'.$show_day_date.'</div>';
        $show_class_block .= '<div style="text-align:center; font-size:25px">'.JText::_('COM_TOES_FINALS').'</div>';
        $show_class_block .= '<div style="text-align:center; ">'.JText::_('COM_TOES_BEST').'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.JText::_('COM_TOES_AWARDS').'</div></td>';
        $show_class_block .= '</tr></table>';

        $pdf->SetFont('cmunrm', '', 12);
        $pdf->writeHTMLCell( $w, 0, $x, 5, $show_class_block, 0, true, false, false, false, '');
        $pdf->ln(2);
        $page_number++;

        $entry_block = '<table style="text-align:center;padding:5px;margin:0;">
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_AWARD').'</td>
                                <td style="width:33%;border:1px solid #999;">'.JText::_('COM_TOES_ENTRY_NO').'</td>
                            </tr>
                        </table>';

        $pdf->SetFont('cmunrm', '', 10);
        $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');

        $entry_block = '<table style="padding:5px;margin:0;">
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_FIRST_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_SECOND_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_THIRD_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_FORTH_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_FIFTH_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_SIXTH_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_SEVENTH_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_EIGHTH_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_NINTH_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COM_TOES_TENTH_BEST').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;">&nbsp;</td>
                                <td style="width:33%;">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="width:66%;border:1px solid #999;">'.JText::_('COUNT').'</td>
                                <td style="width:33%;border:1px solid #999;">&nbsp;</td>
                            </tr>
                        </table>';

        $pdf->SetFont('cmunrm', '', 10);
        $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $entry_block, 0, true, false, false, false, '');
    }

    $buffer = $pdf->getPgBuffer($pdf->getPage());

    if(!$single_copy)
    {
        if($j%2 == 1)
            $pdf->customFooter();
        $pdf->AddPage();

        for ($index = 0; $index < 2 ; $index++) 
        {
            $pdf->setPgBuffer($pdf->getPage(), $buffer);

            if($j%2 == 1)
                $pdf->customFooter();
            if($index != 1)
                $pdf->AddPage();
        }
    }
    
    if($j%2 == 1)
        $pdf->customFooter();
    $pdf->AddPage();    

	$pdf->show_count_footer = 0;
	
    $judge_fee = @$judges[$rings[$ring_number]->ring_judge]->judge_fee;
    $show_day_date = '';
    if($is_continuous)
        $show_day_date .= $show->show_dates;
    else
        $show_day_date .= date('M d, Y',  strtotime($show_days[$rings[$ring_number]->ring_show_day]->show_day_date));

    if($is_alternative)
    {
        if($rings[$ring_number]->ring_timing == 1)
            $show_day_date .= " AM";
        else
            $show_day_date .= " PM";
    }

    $expense_block = '<div style="text-align:center;"><img src="'.$tica_logo.'" />';
    $expense_block .= '<br/>'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET').'</div><br/>';
    $expense_block .= '<table><tr><td align="left">'.$pdf->judge_name.'</td><td align="right">'.$show_day_date.'</td></tr></table>';
    $expense_block .= '<div style="font-weight:bold;">'.JText::_('COM_TOES_JUDGING_FESS').' : </div><br/>';

    $judge_rings = TOESHelper::getJudgeRings($show->show_id, $rings[$ring_number]->ring_judge);

    $i = 0;
    $count = 0;

    $expense_block .= '<table style="padding:2px;margin:0;">';

    $expense_block .= '<tr>';
    $expense_block .= '<td>'.JText::_('COM_TOES_JUDGE_FEE_PER_CAT').' :</td>';
    $expense_block .= '<td>'.$judge_fee.' USD</td>';
    $expense_block .= '<td colspan="2">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="4">&nbsp;</td>';
    $expense_block .= '</tr>';

    foreach($judge_rings as $ring)
    {
        $expense_block .= '<tr>';
        if($i == 0)
            $expense_block .= '<td style="width:20%;">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_COUNTS').': </td>';
        else
            $expense_block .= '<td style="width:20%;">&nbsp;</td>';

        if($ring->ring_format == 3)
            $expense_block .= '<td style="width:45%;">'.$ring->ring_name.'</td>';
        else
        {
            $showday_date = date('M d, Y',  strtotime($ring->show_day_date));
            $expense_block .= '<td style="width:45%;">'.$showday_date.'</td>';
        }

        $expense_block .= '<td style="width:10%;">'.$ring->count.'</td>';
        $expense_block .= '<td style="width:25%;">&nbsp;</td>';
        $expense_block .= '</tr>';
        $count += $ring->count;
        $i++;
    }   

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="4">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '<td align="right" colspan="2">'.JText::_('COM_TOES_TOTAL').' : '.$count.' X '.$judge_fee.' USD</td>';
    $expense_block .= '<td align="right"> = '.($count*$judge_fee).' USD</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td align="right" colspan="3">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_SEMINAR_FEE_TEXT').':<br/>____ X 50 USD</td>';
    $expense_block .= '<td align="right" valign="bottom">=____USD</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="4">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td align="right" colspan="3" style="font-weight:bold">'.JText::_('COM_TOES_TOTAL_JUDGING_FEES').' :</td>';
    $expense_block .= '<td align="right">=____USD</td>';
    $expense_block .= '</tr>';

    $expense_block .= '</table>';


    $expense_block .= '<table style="padding:2px;margin:0;">';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="4" style="font-weight:bold;">'.JText::_('COM_TOES_TRANSPORTATION').' :</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="4">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td style="width:10%;">&nbsp;</td>';
    $expense_block .= '<td style="width:40%;">'.JText::_('COM_TOES_TRANSPORTATION_AIRFARE').'</td>';
    $expense_block .= '<td style="width:40%;">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '<td style="width:10%;">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '<td>'.JText::_('COM_TOES_TRANSPORTATION_TAXI').'</td>';
    $expense_block .= '<td>'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '<td>'.JText::_('COM_TOES_TRANSPORTATION_PARKING').'</td>';
    $expense_block .= '<td>'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '<td colspan="2">' . JText::_('COM_TOES_TRANSPORTATION_CAR_MILEAGE') .' '. $params->get('irs_miles','0.55') .' USD / miles '.' = '. $params->get('irs_km','0.34') .' USD / KM </td>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '<td colspan="2">'.JText::_('COM_TOES_TRANSPORTATION_ACTUAL_MILEAGE').'</td>';
    $expense_block .= '<td>&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="4">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td align="right" colspan="2">'.JText::_('COM_TOES_TOTAL_TRANSPORTATION').' = </td>';
    $expense_block .= '<td align="right" colspan="2">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '</table>'; 

    $expense_block .= '<div>&nbsp;</div>'; 

    $expense_block .= '<table style="padding:2px;margin:0;">';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="5" style="font-weight:bold;">'.JText::_('COM_TOES_MEALS').' :</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td style="width:10%;">&nbsp;</td>';
    $expense_block .= '<td style="width:30%;">&nbsp;</td>';
    $expense_block .= '<td style="width:20%;">'.JText::_('COM_TOES_MEALS_BREAKFAST').'</td>';
    $expense_block .= '<td style="width:20%;">'.JText::_('COM_TOES_MEALS_LUNCH').'</td>';
    $expense_block .= '<td style="width:20%;">'.JText::_('COM_TOES_MEALS_DINNER').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td style="width:10%;">&nbsp;</td>';
    $expense_block .= '<td style="width:30%;">'.date('l',  strtotime($temp_show_days[0]->show_day_date)-86400).'</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '</tr>';

    foreach($show_days as $show_day){
        $expense_block .= '<tr>';
        $expense_block .= '<td style="width:10%;">&nbsp;</td>';
        $expense_block .= '<td style="width:30%;">'.date('l',  strtotime($show_day->show_day_date)).'</td>';
        $expense_block .= '<td style="width:20%;">_____</td>';
        $expense_block .= '<td style="width:20%;">_____</td>';
        $expense_block .= '<td style="width:20%;">_____</td>';
        $expense_block .= '</tr>';
    }

    $expense_block .= '<tr>';
    $expense_block .= '<td style="width:10%;">&nbsp;</td>';
    $expense_block .= '<td style="width:30%;">'.date('l',  strtotime($show_day->show_day_date)+86400).'</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="5">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td style="width:10%;">&nbsp;</td>';
    $expense_block .= '<td style="width:30%;">'.JText::_('COM_TOES_TOTAL').' :</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '<td style="width:20%;">_____</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="5">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td >&nbsp;</td>';
    $expense_block .= '<td align="right" colspan="2">'.JText::_('COM_TOES_TOTAL_MEALS').' = </td>';
    $expense_block .= '<td align="right" colspan="2">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '</table>';         

    $expense_block .= '<div>&nbsp;</div>'; 

    $expense_block .= '<table style="padding:2px;margin:0;">';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="5" style="font-weight:bold;">'.JText::_('COM_TOES_MISCELLANEOUS').' :</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td colspan="5">&nbsp;</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td >&nbsp;</td>';
    $expense_block .= '<td align="right" colspan="2">'.JText::_('COM_TOES_TOTAL_MISCELLANEOUS').' = </td>';
    $expense_block .= '<td align="right" colspan="2">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '</table>';

    $expense_block .= '<div>&nbsp;</div>'; 

    $expense_block .= '<table style="padding:2px;margin:0;">';

    $expense_block .= '<tr>';
    $expense_block .= '<td >'.JText::_('COM_TOES_SUBTOTAL').' </td>';
    $expense_block .= '<td align="right">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td >'.JText::_('COM_TOES_DONATION').' </td>';
    $expense_block .= '<td align="right">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td >'.JText::_('COM_TOES_ADVANCE_PAYMENT').' </td>';
    $expense_block .= '<td align="right">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '<tr>';
    $expense_block .= '<td >'.JText::_('COM_TOES_BALANCE').' </td>';
    $expense_block .= '<td align="right">'.JText::_('COM_TOES_JUDGE_EXPENSE_SHEET_CURRENCY_OPTIONS').'</td>';
    $expense_block .= '</tr>';

    $expense_block .= '</table>';        

    $expense_block .= '<div style="font-weight:bold;">'.JText::_('COM_TOES_JUDGE_SIGNATURE').' : </div><br/>';

    $pdf->SetFont('cmunrm', '', 8);
    $pdf->writeHTMLCell( $w, 0, $x, $pdf->GetY(), $expense_block, 0, true, false, false, false, '');

    if(!$single_copy)
    {
        if($j%2 == 1)
            $pdf->customFooter();
        $pdf->AddPage();

        for ($index = 0; $index < 2 ; $index++) 
        {
            $pdf->setPgBuffer($pdf->getPage(), $buffer);

            if($j%2 == 1)
                $pdf->customFooter();
            if($index != 1)
                $pdf->AddPage();
        }
    }    
    
    $j++;
}

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if(!file_exists(TOES_PDF_PATH.DS.$show_id))
    JFolder::create (TOES_PDF_PATH.DS.$show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if($cover_copy)
{
	if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'congressjudgesbook_cover.pdf'))
		unlink (TOES_PDF_PATH.DS.$show_id.DS.'congressjudgesbook_cover.pdf');

	//Close and output PDF document
	$pdf->Output(TOES_PDF_PATH.DS.$show_id.DS.'congressjudgesbook_cover.pdf', 'F');
}
else
{
	if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'congressjudgesbook.pdf'))
		unlink (TOES_PDF_PATH.DS.$show_id.DS.'congressjudgesbook.pdf');

	//Close and output PDF document
	$pdf->Output(TOES_PDF_PATH.DS.$show_id.DS.'congressjudgesbook.pdf', 'F');
}

//============================================================+
// END OF FILE                                                
//============================================================+
