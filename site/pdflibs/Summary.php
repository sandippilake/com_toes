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
$pdf->SetTitle(JText::_('COM_TOES_SHOW_SUMMARY'));

// set default header data
$header_logo = '/media/com_toes/images/paw32X32.png';
$pdf->SetHeaderData($header_logo, 10, JText::_('COM_TOES_SHOW_SUMMARY'), JText::_('COM_TOES_WEBSITE'));

$show = TOESHelper::getShowDetails($show_id);
$is_alternative = ($show->show_format == 'Alternative') ? true : false;

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
$pdf->SetFont('ptsans', '', 12);

// add a page
$pdf->AddPage();

$db = JFactory::getDBO();
$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;

$query = TOESQueryHelper::getShowSummariesQuery($whr);
$db->setQuery($query);
$summary = $db->loadObjectList();

if($is_alternative)
{
	$query = TOESQueryHelper::getShowSummariesAMSessionQuery($whr);
	$db->setQuery($query);
	$am_summary = $db->loadObjectList();

	$query = TOESQueryHelper::getShowSummariesPMSessionQuery($whr);
	$db->setQuery($query);
	$pm_summary = $db->loadObjectList();
}

$query = "SELECT `sd`.*
FROM `#__toes_show_day` AS `sd` 
WHERE `sd`.`show_day_show` = {$show_id} ";
$db->setQuery($query);
$show_days = $db->loadObjectList();

$showClasses = array(
        array(
        'LH Kitten'=>'LH Kitten',
        'SH Kitten'=>'SH Kitten'
            ),
        array(
        'LH Cat'=>'LH Cat',
        'SH CAT'=>'SH Cat'
            ),
        array(
        'LH Alter'=>'LH Alter',
        'SH Alter'=>'SH Alter'
            ),
        array(
        'LH HHP Kitten'=>'LH HHP Kitten',
        'SH HHP Kitten'=>'SH HHP Kitten'
            ),
        array(
        'LH HHP'=>'LH HHP',
        'SH HHP'=>'SH HHP'
            ),
        array(
        'LH NT'=>'LH NT',
        'SH NT'=>'SH NT'
            ),
        array(
        'LH ANB'=>'LH ANB',
        'SH ANB'=>'SH ANB'
            ),
        array(
        'LH PNB'=>'LH PNB',
        'SH PNB'=>'SH PNB'
            ),
        array(
        'Ex Only'=>'Ex Only'
            )
);

if($is_alternative)
{
	$final_am_summary = array();
	foreach($am_summary as $smry)
	{
		$final_am_summary[$smry->show_class][$smry->show_day_id] = $smry->cat_count;
	}    
	
	$final_pm_summary = array();
	foreach($pm_summary as $smry)
	{
		$final_pm_summary[$smry->show_class][$smry->show_day_id] = $smry->cat_count;
	}    
}
else
{
	$final_summary = array();
	foreach($summary as $smry)
	{
		$final_summary[$smry->show_class][$smry->show_day_id] = $smry->cat_count;
	}
}

$totals = array();

if($is_alternative)
{
	$tbl = '
	<table cellspacing="0" style="padding:1px 5px" border="0">
	    <tr>
	        <td width="40%"></td>
	        ';
	for($i = 0; $i < count($show_days); $i++)
	{
	    $tbl .= '<td colspan="2" style="border-bottom:1px solid #E8E8E8" align="center">'.strtoupper(date('D',  strtotime($show_days[$i]->show_day_date))).'</td>
	            ';
	}
	
	$tbl .= '
	        <td></td>
	    </tr>
	    ';
	
	$tbl .= '
	    <tr>
	        <td width="40%"></td>
	        ';
	for($i = 0; $i < count($show_days); $i++)
	{
	    $tbl .= '<td style="border-bottom:1px solid #E8E8E8" align="center">AM</td>
	    		<td style="border-bottom:1px solid #E8E8E8" align="center">PM</td>
	            ';
	}
	
	$tbl .= '
	        <td></td>
	    </tr>
	    ';
	$am_totals = array();
	$pm_totals = array();
	foreach($showClasses as $show_group)
	{
		$cnt_am_total = array();
		$cnt_pm_total = array();

		$show_class_am_count = 0;
		$show_class_pm_count = 0;

		foreach($show_group as $show_class)
		{
			if( (isset($final_am_summary[$show_class]) && $final_am_summary[$show_class]) || (isset($final_pm_summary[$show_class]) && $final_pm_summary[$show_class]) )
			{
				$tbl .= '
					<tr>
						<td width="40%" align="right">'.strtoupper($show_class).'&nbsp;&nbsp;</td>
					';
				for($i = 0; $i < count($show_days); $i++)
				{
					$am_cnt = isset($final_am_summary[$show_class][$show_days[$i]->show_day_id])?$final_am_summary[$show_class][$show_days[$i]->show_day_id]:0;

					$show_class_am_count += $am_cnt;

					if(isset($cnt_am_total[$show_days[$i]->show_day_id]))
						$cnt_am_total[$show_days[$i]->show_day_id] += $am_cnt;
					else
						$cnt_am_total[$show_days[$i]->show_day_id] = $am_cnt;

					if(isset($am_totals[$show_days[$i]->show_day_id]))
						$am_totals[$show_days[$i]->show_day_id] += $am_cnt;
					else
						$am_totals[$show_days[$i]->show_day_id] = $am_cnt;

					$pm_cnt = isset($final_pm_summary[$show_class][$show_days[$i]->show_day_id])?$final_pm_summary[$show_class][$show_days[$i]->show_day_id]:0;

					$show_class_pm_count += $pm_cnt;

					if(isset($cnt_pm_total[$show_days[$i]->show_day_id]))
						$cnt_pm_total[$show_days[$i]->show_day_id] += $pm_cnt;
					else
						$cnt_pm_total[$show_days[$i]->show_day_id] = $pm_cnt;

					if(isset($pm_totals[$show_days[$i]->show_day_id]))
						$pm_totals[$show_days[$i]->show_day_id] += $pm_cnt;
					else
						$pm_totals[$show_days[$i]->show_day_id] = $pm_cnt;

					$tbl .= '
							<td border="0" align="center">'.$am_cnt.'</td>
							<td border="0" align="center">'.$pm_cnt.'</td>
							';
				}
				$tbl .= '
						<td></td>
					</tr>
					';
			}
		}

		if($show_class != 'Ex Only')
		{
			if($show_class_pm_count || $show_class_pm_count)
			{
				$tbl .= '
					<tr>
						<td width="40%" align="right">AB '.strtoupper(str_replace('SH ', '', $show_class)).'</td>
					';
				for($i = 0; $i < count($show_days); $i++)
				{
					$am_cnt = isset($cnt_am_total[$show_days[$i]->show_day_id])?$cnt_am_total[$show_days[$i]->show_day_id]:0;
					$pm_cnt = isset($cnt_pm_total[$show_days[$i]->show_day_id])?$cnt_pm_total[$show_days[$i]->show_day_id]:0;

					$tbl .= '
							<td style="background-color:#E8E8E8" border="0" align="center">'.$am_cnt.'</td>
							<td style="background-color:#E8E8E8" border="0" align="center">'.$pm_cnt.'</td>
							';
				}
				$tbl .= '
						<td></td>
					</tr>
					';
			}
		}

		if($show_class_pm_count || $show_class_pm_count)
		{
			$tbl .= '
					<tr>
						<td colspan="'.(count($show_days)+2).'"></td>
					</tr>
					';
		}
	}

	$whr = array();
	$whr[] = "ring_show = {$show_id}";

	$query = TOESQueryHelper::getCongressSummaryQuery($whr);
	$db->setQuery($query);
	$congress_summary = $db->loadObjectList();

	$final_congress_summary = array();
	foreach($congress_summary as $smry)
	{
		$final_congress_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
	}

	$query = TOESQueryHelper::getCongressSummaryAMSessionQuery($whr);
	$db->setQuery($query);
	$congress_summary_am_session = $db->loadObjectList();

	$query = TOESQueryHelper::getCongressSummaryPMSessionQuery($whr);
	$db->setQuery($query);
	$congress_summary_pm_session = $db->loadObjectList();

	$final_congress_am_summary = array();
	foreach($congress_summary_am_session as $smry)
	{
		$final_congress_am_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
	}
	$final_congress_pm_summary = array();
	foreach($congress_summary_pm_session as $smry)
	{
		$final_congress_pm_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
	}

	foreach($final_congress_summary as $ring_name=>$smry)
	{
		$tbl .= '
			<tr>
				<td width="40%" align="right">'.strtoupper($ring_name).'&nbsp;&nbsp;</td>
			';
		for($i = 0; $i < count($show_days); $i++)
		{
			$am_cnt = isset($final_congress_am_summary[$ring_name][$show_days[$i]->show_day_id])?
				$final_congress_am_summary[$ring_name][$show_days[$i]->show_day_id]:'-';

			$pm_cnt = isset($final_congress_pm_summary[$ring_name][$show_days[$i]->show_day_id])?
				$final_congress_pm_summary[$ring_name][$show_days[$i]->show_day_id]:'-';


			$tbl .= '
					<td style="" border="0" align="center">'.$am_cnt.'</td>
					<td style="" border="0" align="center">'.$pm_cnt.'</td>
					';
		}
		$tbl .= '
				<td></td>
			</tr>
			';
	}

	$tbl .= '
		<tr>
			<td width="40%" align="right">'.strtoupper(JText::_('COM_TOES_PLACEHOLDERS')).'&nbsp;&nbsp;</td>
		';
	for($i = 0; $i < count($show_days); $i++)
	{
		$query = $db->getQuery(true);

		$query->select('count(pd.placeholder_day_showday)');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('p.placeholder_show = ' . $show_id);
		$query->where('pd.placeholder_participates_AM = 1');
		$query->where('pd.placeholder_day_showday = ' . $show_days[$i]->show_day_id);
		$query->where('(es.entry_status = '.$db->quote('New').' OR es.entry_status = '.$db->quote('Accepted').' OR es.entry_status = '.$db->quote('Confirmed').' OR es.entry_status = '.$db->quote('Confirmed & Paid').')');

		//echo nl2br(str_replace('#__', 'j35_', $query));die;
		$db->setQuery($query);
		$am_placeholders = $db->loadResult();

		if(isset($am_totals[$show_days[$i]->show_day_id]))
			$am_totals[$show_days[$i]->show_day_id] += $am_placeholders;
		else
			$am_totals[$show_days[$i]->show_day_id] = $am_placeholders;

		$query = $db->getQuery(true);

		$query->select('count(pd.placeholder_day_showday)');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('p.placeholder_show = ' . $show_id);
		$query->where('pd.placeholder_participates_PM = 1');
		$query->where('pd.placeholder_day_showday = ' . $show_days[$i]->show_day_id);
		$query->where('(es.entry_status = '.$db->quote('New').' OR es.entry_status = '.$db->quote('Accepted').' OR es.entry_status = '.$db->quote('Confirmed').' OR es.entry_status = '.$db->quote('Confirmed & Paid').')');

		//echo nl2br(str_replace('#__', 'j35_', $query));die;
		$db->setQuery($query);
		$pm_placeholders = $db->loadResult();

		if(isset($pm_totals[$show_days[$i]->show_day_id]))
			$pm_totals[$show_days[$i]->show_day_id] += $pm_placeholders;
		else
			$pm_totals[$show_days[$i]->show_day_id] = $pm_placeholders;

		if($am_placeholders || $pm_placeholders)
			$tbl .= '
					<td style="" border="0" align="center">'.$am_placeholders.'</td>
					<td style="" border="0" align="center">'.$pm_placeholders.'</td>
					';
		else
			$tbl .= '
					<td style="" border="0" align="center">0</td>
					<td style="" border="0" align="center">0</td>
					';
	}
	$tbl .= '
			<td></td>
		</tr>
		';
	/*$tbl .= '
				<tr>
					<td colspan="'.(count($show_days)+2).'"></td>
				</tr>
				';*/


	$tbl .= '
		<tr>
			<td width="40%" align="right">'.JText::_('COM_TOES_TOTALS').'&nbsp;&nbsp;</td>
		';
	for($i = 0; $i < count($show_days); $i++)
	{
		$am_cnt = isset($am_totals[$show_days[$i]->show_day_id])?$am_totals[$show_days[$i]->show_day_id]:0;
		$pm_cnt = isset($pm_totals[$show_days[$i]->show_day_id])?$pm_totals[$show_days[$i]->show_day_id]:0;

		$tbl .= '
				<td style="background-color:#E8E8E8" border="0" align="center">'.$am_cnt.'</td>
				<td style="background-color:#E8E8E8" border="0" align="center">'.$pm_cnt.'</td>
				';
	}
	$tbl .= '
			<td></td>
		</tr>
		';

	$tbl .= '
	</table>
	';
}
else
{
	$tbl = '
	<table cellspacing="0" style="padding:1px 5px" border="0">
	    <tr>
	        <td width="40%"></td>
	        ';
	for($i = 0; $i < count($show_days); $i++)
	{
	    $tbl .= '<td style="border-bottom:1px solid #E8E8E8" align="center">'.strtoupper(date('D',  strtotime($show_days[$i]->show_day_date))).'</td>
	            ';
	}
	
	$tbl .= '
	        <td></td>
	    </tr>
	    ';
	foreach($showClasses as $show_group)
	{
		$cnt_total = array();

		$show_class_count = 0;
		foreach($show_group as $show_class)
		{
			if(isset($final_summary[$show_class]) && $final_summary[$show_class])
				$show_day_count = $final_summary[$show_class];
			else
				$show_day_count ='';

			if($show_day_count)
			{
				$tbl .= '
					<tr>
						<td width="40%" align="right">'.strtoupper($show_class).'&nbsp;&nbsp;</td>
					';
				for($i = 0; $i < count($show_days); $i++)
				{
					$cnt = isset($show_day_count[$show_days[$i]->show_day_id])?$show_day_count[$show_days[$i]->show_day_id]:0;

					$show_class_count += $cnt;

					if(isset($cnt_total[$show_days[$i]->show_day_id]))
						$cnt_total[$show_days[$i]->show_day_id] += $cnt;
					else
						$cnt_total[$show_days[$i]->show_day_id] = $cnt;

					if(isset($totals[$show_days[$i]->show_day_id]))
						$totals[$show_days[$i]->show_day_id] += $cnt;
					else
						$totals[$show_days[$i]->show_day_id] = $cnt;

					$tbl .= '
							<td border="0" align="center">'.$cnt.'</td>
							';
				}
				$tbl .= '
						<td></td>
					</tr>
					';
			}
			/*else
			{
				$tbl .= '
					<tr>
						<td width="40%" align="right">'.strtoupper($show_class).'&nbsp;&nbsp;</td>
					';
				for($i = 0; $i < count($show_days); $i++)
				{
					$tbl .= '
							<td border="0" align="center">0</td>
							';
				}
				$tbl .= '
						<td></td>
					</tr>
					';
			}*/
		}


		if($show_class != 'Ex Only')
		{
			if($show_class_count)
			{
				$tbl .= '
					<tr>
						<td width="40%" align="right">AB '.strtoupper(str_replace('SH ', '', $show_class)).'</td>
					';
				for($i = 0; $i < count($show_days); $i++)
				{
					$cnt = isset($cnt_total[$show_days[$i]->show_day_id])?$cnt_total[$show_days[$i]->show_day_id]:0;

					$tbl .= '
							<td style="background-color:#E8E8E8" border="0" align="center">'.$cnt.'</td>
							';
				}
				$tbl .= '
						<td></td>
					</tr>
					';
			}
		}

		if($show_class_count)
		{
			$tbl .= '
					<tr>
						<td colspan="'.(count($show_days)+2).'"></td>
					</tr>
					';
		}
	}

	$whr = array();
	$whr[] = "ring_show = {$show_id}";

	$query = TOESQueryHelper::getCongressSummaryQuery($whr);
	$db->setQuery($query);
	$congress_summary = $db->loadObjectList();

	$final_congress_summary = array();
	foreach($congress_summary as $smry)
	{
		$final_congress_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
	}

	foreach($final_congress_summary as $ring_name=>$smry)
	{
		$tbl .= '
			<tr>
				<td width="40%" align="right">'.strtoupper($ring_name).'&nbsp;&nbsp;</td>
			';
		for($i = 0; $i < count($show_days); $i++)
		{
			if(isset($smry[$show_days[$i]->show_day_id]))
				$tbl .= '
						<td style="" border="0" align="center">'.$smry[$show_days[$i]->show_day_id].'</td>
						';
			else
				$tbl .= '
						<td style="" border="0" align="center">-</td>
						';

		}
		$tbl .= '
				<td></td>
			</tr>
			';
	}

	$tbl .= '
		<tr>
			<td width="40%" align="right">'.strtoupper(JText::_('COM_TOES_PLACEHOLDERS')).'&nbsp;&nbsp;</td>
		';
	for($i = 0; $i < count($show_days); $i++)
	{
		$query = $db->getQuery(true);

		$query->select('count(pd.placeholder_day_showday)');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('p.placeholder_show = ' . $show_id);
		$query->where('pd.placeholder_day_showday = ' . $show_days[$i]->show_day_id);
		$query->where('(es.entry_status = '.$db->quote('Accepted').' OR es.entry_status = '.$db->quote('Confirmed').' OR es.entry_status = '.$db->quote('Confirmed & Paid').')');

		//echo nl2br(str_replace('#__', 'j35_', $query));die;
		$db->setQuery($query);
		$placeholders = $db->loadResult();

		if(isset($totals[$show_days[$i]->show_day_id]))
			$totals[$show_days[$i]->show_day_id] += $placeholders;
		else
			$totals[$show_days[$i]->show_day_id] = $placeholders;

		if($placeholders)
			$tbl .= '
					<td style="" border="0" align="center">'.$placeholders.'</td>
					';
		else
			$tbl .= '
					<td style="" border="0" align="center">0</td>
					';
	}
	$tbl .= '
			<td></td>
		</tr>
		';
	/*$tbl .= '
				<tr>
					<td colspan="'.(count($show_days)+2).'"></td>
				</tr>
				';*/


	$tbl .= '
		<tr>
			<td width="40%" align="right">'.JText::_('COM_TOES_TOTALS').'&nbsp;&nbsp;</td>
		';
	for($i = 0; $i < count($show_days); $i++)
	{
		$cnt = isset($totals[$show_days[$i]->show_day_id])?$totals[$show_days[$i]->show_day_id]:0;

		$tbl .= '
				<td style="background-color:#E8E8E8" border="0" align="center">'.$cnt.'</td>
				';
	}
	$tbl .= '
			<td></td>
		</tr>
		';

	$tbl .= '
	</table>
	';
}
$pdf->writeHTML($tbl, true, false, false, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
/*
jimport('joomla.filesystem.folder');
if(!file_exists(TOES_PDF_PATH.DS.$show_id))
    JFolder::create (TOES_PDF_PATH.DS.$show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'show_summary.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'show_summary.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH.DS.$show_id.DS.'show_summary.pdf', 'F');
*/
$pdf->Output( $show_id . '_show_summary.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
