<?php
jimport('tcpdf.tcpdf');

set_time_limit(5000);

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
			$this->Cell(0, 0, $this->footer_text, 'T', 0, 'R', 0, '', 0, false, 'T', 'M');
			$this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
		} else {
			$this->SetX($this->original_lMargin);
			$this->Cell(0, 0, $this->footer_text, 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
			$this->Cell(0, 0, $this->getAliasRightShift() . $pagenumtxt, 'T', 0, 'R');
		}
	}
}

$skip_division_best = array(
	'LH PNB',
	'SH PNB',
	'LH ANB',
	'SH ANB',
	'LH HHP Kitten',
	'SH HHP Kitten',
	'Ex Only',
	'For Sale'
);

$skip_breed_best = array(
	'LH HHP',
	'SH HHP',
	'LH HHP Kitten',
	'SH HHP Kitten',
	'Ex Only',
	'For Sale'
);

$file = $app->input->getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$data = array(
	'total' => '?',
	'processed' => '?'
);
fputs($fp, serialize($data));
fclose($fp);

$db = JFactory::getDBO();

$time = time();
$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;
$whr[] = '`e`.`late_entry` = 1';

$query = TOESQueryHelper::getCatalogNumberingbasisQuery($whr);

$db->setQuery($query);
$entries = $db->loadObjectList();

$query = "SELECT `show_day_id`, `show_day_date` FROM `#__toes_show_day` WHERE `show_day_show` = {$show_id} ORDER BY `show_day_date`";
$db->setQuery($query);
$show_days = $db->loadObjectList();

$query = TOESQueryHelper::getCatlogRingInfoQuery();
$query->select("`rf`.`ring_format` AS `format`");
$query->join("left","`#__toes_ring_format` AS `rf` ON `rf`.`ring_format_id` = `r`.`ring_format`");
$query->where("r.`ring_show` = {$show_id}");
$db->setQuery($query);
$rings = $db->loadObjectList();

foreach($rings as $ring) {
	$show_day_rings[$ring->ring_show_day][] = $ring;
}

$show = TOESHelper::getShowDetails($show_id);

if($show->page_ortientation == 'Automatic')
{
	if (count($show_days) == 3)
		$page_orientation = 'L';
	else
		$page_orientation = PDF_PAGE_ORIENTATION;
}
else if($show->page_ortientation == 'Landscape')
	$page_orientation = 'L';
else
	$page_orientation = PDF_PAGE_ORIENTATION;

$query = " SELECT p.`paper_size` FROM `#__toes_show` AS s LEFT JOIN `#__toes_paper_size` AS p ON p.`paper_size_id` = s.`show_paper_size` WHERE s.`show_id` = {$show_id}";
$db->setQuery($query);
$page_format = $db->loadResult();

$page_format = $page_format ? $page_format : PDF_PAGE_FORMAT;

// create new PDF document
$pdf = new MYPDF($page_orientation, PDF_UNIT, $page_format, true, 'UTF-8', false);

// set document information
$pdf->SetCreator("TICA");
$pdf->SetTitle(JText::_('COM_TOES_LATE_PAGES'));

$is_continuous = ($show->show_format == 'Continuous') ? true : false;
$is_alernative = ($show->show_format == 'Alternative') ? true : false;

$session_rings = array();
if($is_alernative)
{
	foreach($show_days as $showday)
	{
		$session_rings[$showday->show_day_id]['AM'] = TOESHelper::getShowdayRings($showday->show_day_id, 1);
		$session_rings[$showday->show_day_id]['PM'] = TOESHelper::getShowdayRings($showday->show_day_id, 2);
	}
}

$pdf->footer_text = $show->club_name . ' - ' . $show->Show_location . ' - ' . $show->show_dates;

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
// add a page
$pdf->AddPage();

$previous_class = '';
$previous_breed = '';
$previous_division = '';
$previous_color = '';
$previous_catalog_number = '';

$previous_breed_entries = 1;
$previous_division_entries = 1;

$show_day_entries = array();
$catalog_numbers = array();

$temp_entries = array();
$showday_entries = array();
foreach($entries as $entry) {
	$showday_entries[$entry->show_day][$entry->catalog_number] = $entry;
	$show_day_entries[$entry->show_day][$entry->catalog_number] = $entry->catalog_number;
	if (!in_array($entry->catalog_number, $catalog_numbers)) {
		$catalog_numbers[] = $entry->catalog_number;
		$temp_entries[] = $entry;
	}
}
$entries = $temp_entries;

$cur = 0;
$total = count($catalog_numbers);
$processed = 0;
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$data = array(
	'total' => $total,
	'processed' => $processed
);
fputs($fp, serialize($data));
fclose($fp);

$available_classes = array();
if (count($show_days) == 2) {
	foreach($entries as $entry) {
		if ($previous_class != $entry->show_class) {
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;

			if ($previous_class != '')
				$pdf->AddPage();

			$header_logo = 'media/com_toes/images/paw32X32.png';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			$show_class_block .='<img src="' . $header_logo . '" />';
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_LATE_PAGES') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			$pdf->SetFont('ptsanscaption', '', 10);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');

			$judge_block = '<table width="100%">
                                <tr>
                            ';
			if ($show->show_format != 'Alternative') {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr> 
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[0]->show_day_date)))) . '</td>
                                        </tr>
                                        <tr>
                                            ';
				foreach($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
					$judge_block .= '<td align="left">' . $first_show_day->judge_abbreviation . '</td>
                                    ';
				}
				$judge_block .= '       </tr>
                                    </table>
                                </td>
                                ';
			} else {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr> 
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['AM'])?strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM':'&nbsp;').'</td>
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['PM'])?strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM':'&nbsp;').'</td>
                                        </tr>
                                        <tr>
                                            ';
				foreach($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
					$judge_block .= '<td colspan="2" align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $first_show_day->judge_abbreviation . '</td>
                                    ';
				}
				$judge_block .= '       </tr>
                                    </table>
                                </td>
                                ';
			}

			$judge_block .= '<td width="40%" align="center">&nbsp;</td>
                                    ';

			if ($show->show_format != 'Alternative') {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr> 
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[1]->show_day_date)))) . '</td>
                                        </tr>
                                        <tr>
                                            ';

				foreach($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
					$judge_block .= '<td align="right">' . $second_show_day->judge_abbreviation . '</td>
                                            ';
				}
				$judge_block .= '</tr>
                                    </table>
                                </td>
                                ';
			} else {
				$judge_block .= '<td width="30%" align="right">
                                    <table width="100%">
                                        <tr> 
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['AM'])?strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM':'&nbsp;').'</td>
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['PM'])?strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM':'&nbsp;').'</td>
                                        </tr>
                                        <tr>
                                            ';

				foreach($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
					$judge_block .= '<td colspan="2" align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $second_show_day->judge_abbreviation . '</td>
                                            ';
				}
				$judge_block .= '</tr>
                                    </table>
                                </td>
                                ';
			}
			$judge_block .= '</tr>
                        </table>';

			if ($entry->show_class != 'Ex Only') {
				$pdf->SetFont('ptsansnarrow', '', 8);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln();
			}

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
		}

		$entry_block = '<table width="100%">
                            <tr>
                        ';
		$entry_block .= '<td width="30%" align="right">
                            <table width="100%">
                                <tr>
                                    ';

		if ($entry->show_class != 'Ex Only') {
			foreach($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
				if ($show->show_format != 'Alternative') {
					if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number]))
						$entry_block .= '<td align="left" valign="top">__</td>';
					else
						$entry_block .= '<td align="left" valign="top">&nbsp;</td>';
				}
				else {
						if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number])) {
							if (($first_show_day->ring_timing == 1 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($first_show_day->ring_timing == 2 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_PM))
							$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">__</td>';
						else
							$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
					}
					else
						$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
				}
			}
		}
		else
			$entry_block .= '<td></td>';

		$entry_block .= '       </tr>
                            </table>
                        </td>
                        ';

		$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

		$entry_block .= '<td width="40%" align="center">
                            <table width="100%">
                                ';

		$entry_block .= '<tr>
                            <td width="9%" align="left" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_number . '</td>
                            <td width="69%" align="left" >' . strtoupper($entry->catalog_cat_name) . '</td>
                            <td width="13%" align="right" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_age_and_gender . '</td>
                            <td width="9%" align="right" valign="top" rowspan="' . ($isNotHHP ? 5 : 3) . '">' . $entry->catalog_number . '</td>
                        </tr>';

		if (trim($entry->catalog_registration_number) == '')
			$reg_number = JText::_('PENDING');
		else
			$reg_number = $entry->catalog_registration_number;

		$entry_block .= '<tr>
                            <td align="left" >' . $reg_number . '&nbsp;&nbsp;&nbsp;' . JText::_('COM_TOES_CATALOG_BORN') . '&nbsp;' . $entry->catalog_birthdate . '</td>
                        </tr>';
		if ($isNotHHP) {
			$entry_block .= '<tr>
                                <td align="left" >' . strtoupper($entry->catalog_sire) . '</td>
                            </tr>';
			$entry_block .= '<tr>
                                <td align="left" >' . strtoupper($entry->catalog_dam) . '</td>
                            </tr>';
		}
		$entry_block .= '<tr>
                            <td align="left" >' . $entry->catalog_region . '</td>
                            <td align="left" >' .
				($isNotHHP && $entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '<br/>' : '')
				. ($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '<br/>' : '')
				. ($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '<br/>' : '')
				. ($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '')
				. '</td>
                            <td align="right">' . $entry->catalog_region . '</td>
                        </tr>';

		$entry_block .= '   </table>
                        </td>
                        ';

		$entry_block .= '<td width="30%" align="right">
                            <table width="100%">
                                <tr>
                                    ';

		if ($entry->show_class != 'Ex Only') {
			foreach($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
				if ($show->show_format != 'Alternative') {
					if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number]))
						$entry_block .= '<td align="right" valign="top">__</td>';
					else
						$entry_block .= '<td align="right" valign="top">&nbsp;</td>';
				}
				else {
					if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number])) {
							if (($second_show_day->ring_timing == 1 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($second_show_day->ring_timing == 2 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_PM))
							$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">__</td>';
						else
							$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
					}
					else
						$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
				}
			}
		}
		else
			$entry_block .= '<td></td>';

		$entry_block .= '       </tr>
                            </table>
                        </td>
                        ';

		$entry_block .= '</tr>
                    </table>';


		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {

			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$breed_block = '<div style="text-align:center; font-weight:bold; text-decoration:underline;">' . strtoupper($entry->breed_name) . '</div>';

					$pdf->SetFont('ptsans', 'b', 12);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
					$breed_block = '<div style="text-align:center; text-decoration:underline;">' . strtoupper($entry->catalog_division) . '</div>';

					$pdf->SetFont('ptsans', '', 10);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$breed_block = '<div style="text-align:center; font-weight:bold;">' . strtoupper($entry->color_name) . '</div>';

					$pdf->SetFont('ptsans', 'b', 10);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', 8);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_breed_entries = 1;
						$previous_division_entries = 1;
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}
					else
						$previous_breed_entries++;

					if ($previous_division != $entry->catalog_division) {
						$previous_division_entries = 1;
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}
					else
						$previous_division_entries++;

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;

					$processed++;
					$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
					$data = array(
						'total' => $total,
						'processed' => $processed
					);
					fputs($fp, serialize($data));
					fclose($fp);
				}

				$print_block = 0;
			} else {
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_logo = 'media/com_toes/images/paw32X32.png';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				$show_class_block .='<img src="' . $header_logo . '" />';
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_LATE_PAGES') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', 10);
				$pdf->writeHTML($show_class_block, true, false, false, false, '');

				$judge_block = '<table width="100%">
                                    <tr>
                                ';

				if ($show->show_format != 'Alternative') {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr> 
                                                <td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[0]->show_day_date)))) . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td align="left">' . $first_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				} else {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr> 
												<td align="left" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['AM'])?strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM':'&nbsp;').'</td>
												<td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['PM'])?strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM':'&nbsp;').'</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $first_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				}

				$judge_block .= '<td width="40%" align="center">&nbsp;</td>
                                        ';

				if ($show->show_format != 'Alternative') {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr> 
                                                <td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[1]->show_day_date)))) . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td align="right">' . $second_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				} else {
					$judge_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr> 
												<td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['AM'])?strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM':'&nbsp;').'</td>
												<td align="right" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['PM'])?strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM':'&nbsp;').'</td>
                                            </tr>
                                            <tr>
                                                ';

					foreach($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $second_show_day->judge_abbreviation . '</td>
                                                ';
					}
					$judge_block .= '</tr>
                                        </table>
                                    </td>
                                    ';
				}

				$judge_block .= '</tr>
                            </table>';

				if ($entry->show_class != 'Ex Only') {
					$pdf->SetFont('ptsansnarrow', '', 8);
					$pdf->writeHTML($judge_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 10);
					$breed_block = '<div style="text-align:center; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</div>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 10);
					$breed_block = '<div style="text-align:center; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</div>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_breed = $entry->breed_name;
				}

				$block_page = $pdf->getPage();
				--$print_block;
			}
		} // end while print_block        
		$pdf->ln(1);
		$cur++;
	}
} else {
	foreach($entries as $entry) {
		if ($previous_class != $entry->show_class) {
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;

			if ($previous_class != '')
				$pdf->AddPage();

			$header_logo = 'media/com_toes/images/paw32X32.png';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			$show_class_block .='<img src="' . $header_logo . '" />';
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_LATE_PAGES') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			$pdf->SetFont('ptsanscaption', '', 10);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');

			$judge_block = '<table width="100%">
                                <tr>
                            ';

			$judge_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">&nbsp;</td>
                            ';

			if ($show->show_format != 'Alternative') {
				foreach($show_days as $show_day) {
					if(count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                        <table>
                                            <tr> 
                                                <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						$judge_block .= '<td align="left">' . $pr_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				}
			} else {
				foreach($show_days as $show_day) {
					if(count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                        <table>
                                            <tr> 
												<td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['AM'])?strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM':'&nbsp;').'</td>
												<td align="right" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['PM'])?strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM':'&nbsp;').'</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $pr_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				}
			}

			$judge_block .= '</tr>
                        </table>';

			if ($entry->show_class != 'Ex Only') {
				$pdf->SetFont('ptsansnarrow', '', 8);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln();
			}

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
		}

		$entry_block = '<table width="100%">
                            <tr>
                        ';

		$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

		$entry_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">
                            <table>
                                ';

		$entry_block .= '<tr>
                            <td width="67%" align="left" >' . strtoupper($entry->catalog_cat_name) . '</td>
                            <td width="13%" align="right" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_age_and_gender . '</td>
                            <td width="10%" align="right" valign="top" rowspan="' . ($isNotHHP ? 5 : 3) . '">' . $entry->catalog_number . '</td>
                        </tr>';

		if (trim($entry->catalog_registration_number) == '')
			$reg_number = JText::_('PENDING');
		else
			$reg_number = $entry->catalog_registration_number;

		$entry_block .= '<tr>
                            <td align="left" >' . strtoupper($reg_number) . '&nbsp;&nbsp;&nbsp;' . JText::_('COM_TOES_CATALOG_BORN') . '&nbsp;' . $entry->catalog_birthdate . '</td>
                        </tr>';
		if ($isNotHHP) {
			$entry_block .= '<tr>
                                <td align="left" >' . strtoupper($entry->catalog_sire) . '</td>
                            </tr>';
			$entry_block .= '<tr>
                                <td align="left" >' . strtoupper($entry->catalog_dam) . '</td>
                            </tr>';
		}
		$entry_block .= '<tr>
                            <td align="left" >' .
				($entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '<br/>' : '')
				. ($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '<br/>' : '')
				. ($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '<br/>' : '')
				. ($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '')
				. '</td>
                            <td align="right">' . $entry->catalog_region . '</td>
                        </tr>';

		$entry_block .= '   </table>
                        </td>
                        ';
		if ($entry->show_class != 'Ex Only') {
			if ($show->show_format != 'Alternative') {
				foreach($show_days as $show_day) {
					if(count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                        <table width="100%">
                                            <tr>
                                                ';
					foreach($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						if (isset($show_day_entries[$show_day->show_day_id][$entry->catalog_number]))
							$entry_block .= '<td align="left" valign="top">____</td>';
						else
							$entry_block .= '<td align="left" valign="top">&nbsp;</td>';
					}
					$entry_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				}
			}
			else {
				foreach($show_days as $show_day) {
					if(count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                        <table width="100%">
                                            <tr>
                                                ';
					foreach($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						if (isset($show_day_entries[$show_day->show_day_id][$entry->catalog_number])) {
								if (($pr_show_day->ring_timing == 1 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($pr_show_day->ring_timing == 2 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_PM))
								$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">____</td>';
							else
								$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
						}
						else
							$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
					}
					$entry_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				}
			}
		}
		else
			$entry_block .= '<td></td>';

		$entry_block .= '</tr>
                    </table>';


		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {
			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 14);
					$breed_block = '<span style="text-align:left; font-weight:bold; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->breed_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
					$pdf->SetFont('ptsans', '', 12);
					$breed_block = '<span style="text-align:left; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->catalog_division) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$pdf->SetFont('ptsans', 'b', 12);
					$breed_block = '<span style="text-align:left; font-weight:bold; padding:5px 0;">' . strtoupper($entry->color_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', 10);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_breed_entries = 1;
						$previous_division_entries = 1;
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}
					else
						$previous_breed_entries++;

					if ($previous_division != $entry->catalog_division) {
						$previous_division_entries = 1;
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}
					else
						$previous_division_entries++;

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;

					$processed++;
					$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
					$data = array(
						'total' => $total,
						'processed' => $processed
					);
					fputs($fp, serialize($data));
					fclose($fp);
				}

				$print_block = 0;
			} else {
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_logo = 'media/com_toes/images/paw32X32.png';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				$show_class_block .='<img src="' . $header_logo . '" />';
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_LATE_PAGES') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', 10);
				$pdf->writeHTML($show_class_block, true, false, false, false, '');

				$judge_block = '<table width="100%">
                                    <tr>
                                ';

				$judge_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">&nbsp;</td>
                                ';

				if ($show->show_format != 'Alternative') {
					foreach($show_days as $show_day) {
						if(count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table width="100%">
                                                <tr> 
                                                    <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . '</td>
                                                </tr>
                                                <tr>
                                                    ';
						foreach($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td align="left">' . $pr_show_day->judge_abbreviation . '</td>
                                            ';
						}
						$judge_block .= '       </tr>
                                            </table>
                                        </td>
                                        ';
					}
				} else {
					foreach($show_days as $show_day) {
						if(count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table>
                                                <tr> 
													<td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['AM'])?strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM':'&nbsp;').'</td>
													<td align="right" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['PM'])?strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM':'&nbsp;').'</td>
                                                </tr>
                                                <tr>
                                                    ';
						foreach($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td colspan="2" align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">' . $pr_show_day->judge_abbreviation . '</td>
                                            ';
						}
						$judge_block .= '       </tr>
                                            </table>
                                        </td>
                                        ';
					}
				}

				$judge_block .= '</tr>
                            </table>';

				if ($entry->show_class != 'Ex Only') {
					$pdf->SetFont('ptsansnarrow', '', 8);
					$pdf->writeHTML($judge_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 12);
					$breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 12);
					$breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_breed = $entry->breed_name;
				}

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
		$cur++;
		$pdf->ln(1);
	}
}

// Congess Entries

$query = TOESQueryHelper::getJudgesBookCongressData($whr);

$db->setQuery($query);
$congress_catalog = $db->loadObjectList();

$query = "SELECT `r`.`ring_id`, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation` 
        FROM `#__toes_ring` AS `r`
        LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`) 
        WHERE (`r`.`ring_format` = 3) AND `r`.`ring_show` = {$show_id}
        ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`";
$db->setQuery($query);
$temp_rings = $db->loadObjectList();

$congress_rings = array();
foreach($temp_rings as $ring) {
	$congress_rings[strtolower($ring->ring_name)][] = $ring;
}

$final_entries = array();
$temp_entries = array();
foreach($entries as $entry) {
	$temp_entries[$entry->catalog_number] = $entry;
}

$ctlg_numbers = array();
$cngrs_entries_by_ring_id = array();
foreach($congress_catalog as $entry) {
	if(isset($temp_entries[$entry->catalog_number])) {
		$cngrs_entries_by_ring_id[$entry->ring_id][] = $entry->catalog_number;
		$temp_entries[$entry->catalog_number]->show_class = $entry->show_class;
		if (!isset($ctlg_numbers[strtolower($entry->ring_name)]))
			$ctlg_numbers[strtolower($entry->ring_name)] = array();
		if (!in_array($entry->catalog_number, $ctlg_numbers[strtolower($entry->ring_name)])) {
			$ctlg_numbers[strtolower($entry->ring_name)][] = $entry->catalog_number;
			$final_entries[strtolower($entry->ring_name)][] = $temp_entries[$entry->catalog_number];
		}
	}
}

unset($temp_entries);
foreach($final_entries as $ring_number => $congress_entries) {
	$temp_entries[$ring_number] = TOESHelper::aasort($congress_entries, 'catalog_number');
}
$final_entries = $temp_entries;

foreach($final_entries as $ring_number => $congress_entries) {
	$congress_entries = array_values($congress_entries);
	$previous_class = '';
	$previous_breed = '';
	$previous_division = '';
	$previous_color = '';
	$previous_catalog_number = '';

	$previous_breed_entries = 1;
	$previous_division_entries = 1;

	$cur = 0;

	$rings = $congress_rings[$ring_number];
	$judge_name = end($rings)->ring_name;
	//$judge_abbreviation = $congress_rings[$ring_number]->judge_abbreviation;
	$pdf->AddPage();

	$header_logo = 'media/com_toes/images/paw32X32.png';
	$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
	$show_class_block .='<img src="' . $header_logo . '" />';
	$show_class_block .='</td><td style="width:26%">';
	$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
	$show_class_block .='</td>';
	//$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name . '<br/>' . $entry->show_class) . '</div></td>';
	$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name) . '</div></td>';
	$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
	$pdf->SetFont('ptsanscaption', '', 10);
	$pdf->writeHTML($show_class_block, true, false, false, false, '');

	$judge_block = '<table width="100%">
						<tr>
							<td width="' . (100 - (count($rings) * 15)) . '%">&nbsp;</td>';
	foreach($rings as $ring) {
		$judge_block .= '<td width="15%">
							<table>
								<tr>
									<td align="left">' . $ring->judge_abbreviation . '</td>
								</tr>
							</table>
						</td>
					';
	}
	$judge_block .= '</tr>
				</table>';

	$pdf->SetFont('ptsansnarrow', '', 8);
	$pdf->writeHTML($judge_block, true, false, false, false, '');
	$pdf->ln();

	foreach($congress_entries as $entry) {

		/*if ($previous_class != $entry->show_class) {
			if ($previous_class != '')
				$pdf->AddPage();

			$header_logo = '/media/com_toes/images/paw32X32.png';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			$show_class_block .='<img src="' . $header_logo . '" />';
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			//$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name . '<br/>' . $entry->show_class) . '</div></td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name) . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			$pdf->SetFont('ptsanscaption', '', 10);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');

			$judge_block = '<table width="100%">
                                <tr>
                                    <td width="' . (100 - (count($rings) * 15)) . '%">&nbsp;</td>';
			foreach($rings as $ring) {
				$judge_block .= '<td width="15%">
                                    <table>
                                        <tr>
                                            <td align="left">' . $ring->judge_abbreviation . '</td>
                                        </tr>
                                    </table>
                                </td>
                            ';
			}
			$judge_block .= '</tr>
                        </table>';

			$pdf->SetFont('ptsansnarrow', '', 8);
			$pdf->writeHTML($judge_block, true, false, false, false, '');
			$pdf->ln();

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
		}*/

		if ($previous_catalog_number != $entry->catalog_number) {
			$entry_block = '<table width="100%">
                                <tr>
                            ';

			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

			$entry_block .= '<td width="' . (100 - (count($rings) * 15)) . '%">
                                <table>
                                    ';

			$entry_block .= '<tr>
                                <td width="67%" align="left" >' . strtoupper($entry->catalog_cat_name) . '</td>
                                <td width="13%" align="right" valign="top" rowspan="' . ($isNotHHP ? 4 : 2) . '">' . $entry->catalog_age_and_gender . '</td>
                                <td width="10%" align="right" valign="top" rowspan="' . ($isNotHHP ? 5 : 3) . '">' . $entry->catalog_number . '</td>
                            </tr>';

			if (trim($entry->catalog_registration_number) == '')
				$reg_number = JText::_('PENDING');
			else
				$reg_number = $entry->catalog_registration_number;

			$entry_block .= '<tr>
                                <td align="left" >' . strtoupper($reg_number) . '&nbsp;&nbsp;&nbsp;' . JText::_('COM_TOES_CATALOG_BORN') . '&nbsp;' . $entry->catalog_birthdate . '</td>
                            </tr>';
			if ($isNotHHP) {
				$entry_block .= '<tr>
                                    <td align="left" >' . strtoupper($entry->catalog_sire) . '</td>
                                </tr>';
				$entry_block .= '<tr>
                                    <td align="left" >' . strtoupper($entry->catalog_dam) . '</td>
                                </tr>';
			}
			$entry_block .= '<tr>
                                <td align="left" >' .
					($entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '<br/>' : '')
					. ($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '<br/>' : '')
					. ($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '<br/>' : '')
					. ($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '')
					. '</td>
                                <td align="right">' . $entry->catalog_region . '</td>
                            </tr>';

			$entry_block .= '   </table>
                            </td>
                            ';

			foreach($rings as $ring) {
				if(in_array($entry->catalog_number,$cngrs_entries_by_ring_id[$ring->ring_id]))
				{
					$entry_block .= '<td width="15%">
										<table width="100%">
											<tr>
												<td align="left" valign="top">__________</td>
											</tr>
										</table>
									</td>';
				}
				else
				{
					$entry_block .= '<td width="15%">&nbsp;</td>';
				}
			}

			$entry_block .= '</tr>
                    </table>';
		}

		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {
			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 14);
					$breed_block = '<span style="text-align:left; font-weight:bold; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->breed_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
					$pdf->SetFont('ptsans', '', 12);
					$breed_block = '<span style="text-align:left; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->catalog_division) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$pdf->SetFont('ptsans', 'b', 12);
					$breed_block = '<span style="text-align:left; font-weight:bold; padding:5px 0;">' . strtoupper($entry->color_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', 10);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_breed_entries = 1;
						$previous_division_entries = 1;
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}
					else
						$previous_breed_entries++;

					if ($previous_division != $entry->catalog_division) {
						$previous_division_entries = 1;
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}
					else
						$previous_division_entries++;

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;
				}

				$print_block = 0;
			} else {
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_logo = 'media/com_toes/images/paw32X32.png';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				$show_class_block .='<img src="' . $header_logo . '" />';
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				//$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name . '<br/>' . $entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', 10);
				$pdf->writeHTML($show_class_block, true, false, false, false, '');

				$judge_block = '<table width="100%">
                                <tr>
                                    <td width="' . (100 - (count($rings) * 15)) . '%">&nbsp;</td>';
				foreach($rings as $ring) {
					$judge_block .= '<td width="15%">
                                        <table>
                                            <tr>
                                               <td align="left">' . $ring->judge_abbreviation . '</td>
                                            </tr>
                                        </table>
                                    </td>';
				}
				$judge_block .= '</tr>
                                </table>';

				$pdf->SetFont('ptsansnarrow', '', 8);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln(1);

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 12);
					$breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', 12);
					$breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_breed = $entry->breed_name;
				}

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
		$cur++;
		$pdf->ln(1);
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

if(file_exists(TOES_PDF_PATH.DS.$show_id.DS.'latepages.pdf'))
	unlink (TOES_PDF_PATH.DS.$show_id.DS.'latepages.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'latepages.pdf', 'F');
*/
$pdf->Output( $show_id .'_latepages.pdf', 'I');
//============================================================+
// END OF FILE                                                
//============================================================+
