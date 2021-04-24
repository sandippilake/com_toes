<?php

jimport('tcpdf.tcpdf');

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
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_CHECKIN_SHEET'));

// set default header data
$show = TOESHelper::getShowDetails($show_id);

$isContinuous = ($show->show_format == 'Continuous')?1:0;

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(5, 5, 5);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
//$pdf->setLanguageArray($l);
$pdf->setPrintHeader(FALSE);
$pdf->setPrintFooter(FALSE);

// ---------------------------------------------------------
// add a page
$pdf->AddPage();

$query = $db->getQuery(true);

$query->select('distinct(s.summary_user) as user_id, IF(c.lastname IS NOT NULL OR c.firstname IS NOT NULL, concat(c.lastname,", ",c.firstname), u.name) as user_name, (s.summary_total_fees - s.summary_fees_paid) as balance, summary_benching_area, summary_entry_clerk_note as comments');
$query->from('#__toes_summary AS s');
$query->join('left', '#__comprofiler AS c ON c.id = s.summary_user');
$query->join('left', '#__users AS u ON u.id = s.summary_user');
$query->join('left', '#__toes_entry AS e ON e.summary = s.summary_id');
$query->join('left', '#__toes_entry_status AS es ON es.entry_status_id = e.status');

$query->where('s.summary_show=' . $show_id);
$query->where('es.entry_status IN ("Confirmed","Confirmed & Paid")');
$query->order('c.lastname ASC, c.firstname ASC');

$db->setQuery($query);
$exhibitors = $db->loadObjectList();

//if(!$exhibitors)
	//echo JText::_('COM_TOES_NO_EXHIBITORS').'<br/>';

$pdf->SetFillColor(255, 255, 255);

$header_block = '<table style="width:100%;border-bottom:2px solid #000;"><tr>';
$header_block .='<td style="width:15%" align="center">' . JText::_('COM_TOES_NAME') . '</td>';
$header_block .='<td style="width:20%" align="center">' . JText::_('COM_TOES_CIS_SHOW_DAYS_INFO') . '</td>';
$header_block .='<td style="width:15%" align="left">' . JText::_('COM_TOES_BALANCE') . '</td>';
$header_block .='<td style="width:35%" align="center">' . JText::_('COM_TOES_COMMENTS') . '</td>';
$header_block .='<td style="width:15%" align="center">' . JText::_('COM_TOES_ROW') . '</td>';
$header_block .='</tr></table>';
// setfontissue $pdf->SetFont('ptsanscaption', '', 12);
$pdf->writeHTML($header_block, true, false, false, false, '');

foreach ($exhibitors as $exhibitor) {

	$entries = TOESHelper::getEntries($exhibitor->user_id, $show_id, $entry_status);
	
	if (!$entries) {
		continue;		
	}
	
	$exhibitor_header_block = '<table style="width:100%;background-color:#DDD"><tr>';
	$exhibitor_header_block .='<td style="width:15%" ><strong>' . $exhibitor->user_name . '</strong></td>';
	$exhibitor_header_block .='<td style="width:20%" >&nbsp;</td>';
	$exhibitor_header_block .='<td style="width:15%" align="left">' . $show->show_currency_used .' '.money_format('%i',$exhibitor->balance) . '</td>';
	$exhibitor_header_block .='<td style="width:35%" >' . $exhibitor->comments . '</td>';
	$exhibitor_header_block .='<td style="width:15%" align="right">' . $exhibitor->summary_benching_area . '</td>';
	$exhibitor_header_block .='</tr></table>';

	$exhibitor_block = '';
	
	foreach($entries as $entry)
	{
		if($isContinuous) {
			$showdays = '';
		} else {
			$showdays = $entry->showdays;
		}
		
		$exhibitor_block .= '<table cellpadding="2" cellspacing="2" style="background-color:#FFF">
							<tr>
								';
		$exhibitor_block .= '<td width="15%" align="center"><i>' . (($entry->congress)?$entry->congress:'') . '</i></td>';
		$exhibitor_block .= '<td width="20%" align="center">' . $showdays . '</td>';
		$exhibitor_block .= '<td width="10%" align="right"><strong>' . $entry->catalog_number . '</strong></td>';
		$exhibitor_block .= '<td width="20%" align="left">' . $entry->Show_Class . '</td>';
		$exhibitor_block .= '<td width="35%" align="left">' . $entry->cat_prefix_abbreviation . ' ' . $entry->cat_title_abbreviation . ' ' . $entry->copy_cat_name . ' ' . $entry->cat_suffix_abbreviation . '</td>';

		$exhibitor_block .= '
						</tr>
					</table>';
	}
	
	$pdf->startTransaction();
	$block_page = $pdf->getPage();
	$print_block = 2; // 2 tries max
	while ($print_block > 0) {

// setfontissue 		$pdf->SetFont('ptsanscaption', '', 11);
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_header_block, 0, true, false, false, false, '');
		
// setfontissue 		$pdf->SetFont('freesans', '', 10);
		$pdf->writeHTMLCell(0, 0, 10, $pdf->GetY(), $exhibitor_block, 0, true, false, false, false, '');
		
		// do not split BLOCKS in multiple pages
		if ($pdf->getPage() == $block_page) {
			$print_block = 0;
		} else {
			// rolls back to the last (re)start
			$pdf = $pdf->rollbackTransaction();
			$pdf->AddPage();

			$header_block = '<table style="width:100%;border-bottom:2px solid #000;"><tr>';
			$header_block .='<td style="width:15%" align="center">' . JText::_('COM_TOES_NAME') . '</td>';
			$header_block .='<td style="width:20%" align="center">' . JText::_('COM_TOES_CIS_SHOW_DAYS_INFO') . '</td>';
			$header_block .='<td style="width:15%" align="left">' . JText::_('COM_TOES_BALANCE') . '</td>';
			$header_block .='<td style="width:35%" align="center">' . JText::_('COM_TOES_COMMENTS') . '</td>';
			$header_block .='<td style="width:15%" align="center">' . JText::_('COM_TOES_ROW') . '</td>';
			$header_block .='</tr></table>';
// setfontissue 			$pdf->SetFont('ptsanscaption', '', 12);
			$pdf->writeHTML($header_block, true, false, false, false, '');

			$block_page = $pdf->getPage();
			--$print_block;
		}
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

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'checkinsheet.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'checkinsheet.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'checkinsheet.pdf', 'F');
*/
$pdf->Output( $show_id . '_checkinsheet.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
