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
		$cur_y = -25;
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
		
		switch ($this->page_layout) {
			case 'two_days' :
				$judge_block = '<table width="100%">
									<tr>
								';
				if ($this->is_alernative) {
					$judge_block .= '<td width="30%" align="right">
										<table width="100%">
											<tr>
												';
					foreach ($this->show_day_rings[$this->show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td align="left">____</td>
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
												';
					foreach ($this->show_day_rings[$this->show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
										';
					}
					$judge_block .= '       </tr>
										</table>
									</td>
									';
				}

				$judge_block .= '<td width="40%" align="center">' . JText::_('COM_TOES_COUNT_ON_PAGE') . '</td>
										';

				if ($this->is_alernative) {
					$judge_block .= '<td width="30%" align="right">
										<table width="100%">
											<tr>
												';

					foreach ($this->show_day_rings[$this->show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td align="right">____</td>
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
												';

					foreach ($this->show_day_rings[$this->show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
												';
					}
					$judge_block .= '</tr>
										</table>
									</td>
									';
				}
				$judge_block .= '</tr>
							</table>';

				$judge_block .= '<table width="100%">
									<tr>
								';
				if ($this->is_alernative) {
					$judge_block .= '<td width="30%" align="right">
										<table width="100%">
											<tr>
												';
					foreach ($this->show_day_rings[$this->show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td align="left">____</td>
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
												';
					foreach ($this->show_day_rings[$this->show_days[0]->show_day_id] as $first_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
										';
					}
					$judge_block .= '       </tr>
										</table>
									</td>
									';
				}

				$judge_block .= '<td width="40%" align="center">' . JText::_('COM_TOES_COUNT_SO_FAR') . '</td>
										';

				if ($this->is_alernative) {
					$judge_block .= '<td width="30%" align="right">
										<table width="100%">
											<tr>
												';

					foreach ($this->show_day_rings[$this->show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td align="right">____</td>
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
												';

					foreach ($this->show_day_rings[$this->show_days[1]->show_day_id] as $second_show_day) {
						$judge_block .= '<td colspan="2" align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
												';
					}
					$judge_block .= '</tr>
										</table>
									</td>
									';
				}
				$judge_block .= '</tr>
							</table>';
				break;
			case 'three_days':
				$judge_block = '<table width="100%">
									<tr>
								';

				$judge_block .= '<td width="' . (count($this->show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_COUNT_ON_PAGE') . '</td>
								';

				if (!$this->is_alernative) {
					foreach ($this->show_days as $show_day) {
						$judge_block .= '<td width="' . (count($this->show_days) == 3 ? '20%' : '50%') . '">
											<table>
												<tr>
													';
						foreach ($this->show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td align="left">____</td>
											';
						}
						$judge_block .= '       </tr>
											</table>
										</td>
										';
					}
				} else {
					foreach ($this->show_days as $show_day) {
						$judge_block .= '<td width="' . (count($this->show_days) == 3 ? '20%' : '50%') . '">
											<table>
												<tr>
													';
						foreach ($this->show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td colspan="2" align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
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

				break;
				$judge_block .= '<table width="100%">
									<tr>
								';

				$judge_block .= '<td width="' . (count($this->show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_COUNT_SO_FAR') . '</td>
								';

				if (!$this->is_alernative) {
					foreach ($this->show_days as $show_day) {
						$judge_block .= '<td width="' . (count($this->show_days) == 3 ? '20%' : '50%') . '">
											<table>
												<tr>
													';
						foreach ($this->show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td align="left">____</td>
											';
						}
						$judge_block .= '       </tr>
											</table>
										</td>
										';
					}
				} else {
					foreach ($this->show_days as $show_day) {
						$judge_block .= '<td width="' . (count($this->show_days) == 3 ? '20%' : '50%') . '">
											<table>
												<tr>
													';
						foreach ($this->show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td colspan="2" align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
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

				break;
			case 'congress':
				$judge_block = '<table width="100%">
									<tr>
										<td width="' . (100 - (count($this->congress_rings) * 15)) . '%">' . JText::_('COM_TOES_COUNT_ON_PAGE') . '</td>';
				foreach ($this->congress_rings as $ring) {
					$judge_block .= '<td width="15%">
										<table>
											<tr>
												<td align="left">__________</td>
											</tr>
										</table>
									</td>';
				}
				$judge_block .= '</tr>
							</table>';
				$judge_block .= '<table width="100%">
									<tr>
										<td width="' . (100 - (count($this->congress_rings) * 15)) . '%">' . JText::_('COM_TOES_COUNT_SO_FAR') . '</td>';
				foreach ($this->congress_rings as $ring) {
					$judge_block .= '<td width="15%">
										<table>
											<tr>
												<td align="left">__________</td>
											</tr>
										</table>
									</td>';
				}
				$judge_block .= '</tr>
							</table>';

				break;
			default:
				$judge_block = '<table width="100%">
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
							</table>';
				$judge_block .= '<table width="100%">
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
							</table>';
		}

		$this->SetFont('ptsansnarrow', '', $this->font_size);
		$this->writeHTML($judge_block, true, false, false, false, '');
		$this->ln();
		
		// Set font
		$this->SetFont('helvetica', 'I', $this->font_size);

		$this->SetX($this->original_lMargin);
		$this->Cell(0, 0, $this->footer_text, 'T', 0, 'L', 0, '', 0, false, 'T', 'M');
		$this->Cell(0, 0, $this->getAliasRightShift() . $pagenumtxt, 'T', 0, 'R');
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
$log = "Process started";
$data = array(
	'total' => '?',
	'processed' => '?',
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

$db = JFactory::getDBO();

$time = time();
$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;

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

$show_day_rings = array();
$days = array();
foreach ($rings as $ring) {
	$show_day_rings[$ring->ring_show_day][] = $ring;
	$days[] = $ring->ring_show_day;
}

foreach ($show_days as $key => $day) {
	if(!in_array($day->show_day_id, $days)) {
		unset($show_days[$key]);
	}
}
$show_days = array_values($show_days);

$show = TOESHelper::getShowDetails($show_id);

if ($show->page_ortientation == 'Automatic') {
	if (count($show_days) == 3)
		$page_orientation = 'L';
	else
		$page_orientation = PDF_PAGE_ORIENTATION;
}
else if ($show->page_ortientation == 'Landscape')
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
$pdf->SetTitle(JText::_('COM_TOES_MASTER_CATALOG'));

$params = JComponentHelper::getParams('com_toes');

$font_size = ($show->show_catalog_font_size) ? (int) $show->show_catalog_font_size : 10;
$pdf->font_size = $font_size;

$is_continuous = ($show->show_format == 'Continuous') ? true : false;
$is_alernative = ($show->show_format == 'Alternative') ? true : false;

$session_rings = array();
if ($is_alernative) {
	foreach ($show_days as $showday) {
		$session_rings[$showday->show_day_id]['AM'] = TOESHelper::getShowdayRings($showday->show_day_id, 1);
		$session_rings[$showday->show_day_id]['PM'] = TOESHelper::getShowdayRings($showday->show_day_id, 2);
	}
}

$pdf->show = $show;
$pdf->show_day_rings = $show_day_rings;
$pdf->show_days = $show_days;
$pdf->session_rings = $session_rings;
$pdf->is_continuous = $is_continuous;
$pdf->is_alernative = $is_alernative;

$pdf->footer_text = $show->club_name . ' - ' . $show->Show_location . ' - ' . $show->show_dates;

$breed_title_color = $division_title_color = $bod_color = $bob_color = '#000000';

if ($show->show_colored_catalog) {
	if ($params->get('breed_title_color'))
		$breed_title_color = $params->get('breed_title_color');
	if ($params->get('division_title_color'))
		$division_title_color = $params->get('division_title_color');
	if ($params->get('bod_color'))
		$bod_color = $params->get('bod_color');
	if ($params->get('bob_color'))
		$bob_color = $params->get('bob_color');
}

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(10, 5, 10);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER + PDF_MARGIN_FOOTER);

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
foreach ($entries as $entry) {
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
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

$available_classes = array();
if (count($show_days) == 2) {
	$pdf->page_layout = 'two_days';
	foreach ($entries as $entry) {
		if ($previous_class != $entry->show_class) {
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;

			if ($previous_class != '')
				$pdf->AddPage();
			if ($entry->show_class == 'Ex Only')
				$pdf->page_layout = '';

			$header_logo = '/media/com_toes/images/paw32X32.png';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			if($params->get('use_logo_for_pdf')) {
				$show_class_block .='<img src="' . $header_logo . '" />';
			} else {
				$show_class_block .=' ';
			}
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_MASTER_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			$pdf->SetFont('ptsanscaption', '', $font_size + 2);
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
				foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
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
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                        </tr>
                                        <tr>
                                            ';
				foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
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

				foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
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
                                            <td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
                                            <td align="right" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                        </tr>
                                        <tr>
                                            ';

				foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
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
				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln();
			}

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
			
			$log = "Processing ".$entry->show_class.".... ";
			
		}

		if ($previous_catalog_number != $entry->catalog_number) {
			$entry_block = '<table width="100%">
                                <tr>
                            ';
			$entry_block .= '<td width="30%" align="right">
                                <table width="100%">
                                    <tr>
                                        ';
			if ($entry->show_class != 'Ex Only') {
				foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
					if ($show->show_format != 'Alternative') {
						if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number]))
							$entry_block .= '<td align="left" valign="top">____</td>';
						else
							$entry_block .= '<td align="left" valign="top">&nbsp;</td>';
					}
					else {
						if (isset($show_day_entries[$show_days[0]->show_day_id][$entry->catalog_number])) {
							if (($first_show_day->ring_timing == 1 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($first_show_day->ring_timing == 2 && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_PM))
								$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">____</td>';
							else
								$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
						} else
							$entry_block .= '<td align="' . (($first_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
					}
				}
			}
			else {
				$entry_block .= '<td></td>';
			}
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
                                <td width="69%" align="left" >' . ( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name) ) . '</td>
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
                                <td align="left" valign="bottom" >' . $entry->catalog_region . '</td>
                                <td align="left" >' .
					($entry->catalog_breeder ? strtoupper($entry->catalog_breeder) . '<br/>' : '')
					. ($entry->catalog_owner ? strtoupper($entry->catalog_owner) . '<br/>' : '')
					. ($entry->catalog_lessee ? strtoupper($entry->catalog_lessee) . '<br/>' : '')
					. ($entry->catalog_agent ? strtoupper($entry->catalog_agent) : '')
					. '</td>
                                <td align="right" valign="bottom">' . $entry->catalog_region . '</td>
                            </tr>';

			$entry_block .= '   </table>
                            </td>
                            ';

			$entry_block .= '<td width="30%" align="right">
                                <table width="100%">
                                    <tr>
                                        ';
			if ($entry->show_class != 'Ex Only') {
				foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
					if ($show->show_format != 'Alternative') {
						if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number]))
							$entry_block .= '<td align="right" valign="top">____</td>';
						else
							$entry_block .= '<td align="right" valign="top">&nbsp;</td>';
					}
					else {
						if (isset($show_day_entries[$show_days[1]->show_day_id][$entry->catalog_number])) {
							if (($second_show_day->ring_timing == 1 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($second_show_day->ring_timing == 2 && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_PM))
								$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">____</td>';
							else
								$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
						} else
							$entry_block .= '<td align="' . (($second_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
					}
				}
			} else
				$entry_block .= '<td></td>';

			$entry_block .= '       </tr>
                                </table>
                            </td>
                            ';
			$entry_block .= '</tr>
                        </table>';
		}

		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {

			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$previous_breed_entries = 1;
					$previous_division_entries = 1;
					$breed_block = '<div style="text-align:center; font-weight:bold;color:' . $breed_title_color . '; text-decoration:underline;">' . strtoupper($entry->breed_name) . '</div>';

					$pdf->SetFont('ptsans', 'b', $font_size + 4);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_breed_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
					$previous_division_entries = 1;
					$breed_block = '<div style="text-align:center;color:' . $division_title_color . '; text-decoration:underline;">' . strtoupper($entry->catalog_division) . '</div>';

					$pdf->SetFont('ptsans', '', $font_size + 2);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_division_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$breed_block = '<div style="text-align:center; font-weight:bold;">' . strtoupper($entry->color_name) . '</div>';

					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			$next = $cur + 1;
			if (in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($entries) ||
					( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
				$best_division_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_division_entries; $i++) {
					$best_division_block .= '<tr style="line-height: 150%;">
                                    ';

					$best_division_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$best_division_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                        ';
					}
					$best_division_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';

					if (!in_array($entry->show_class, $skip_breed_best) && (($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name)))
						$best_division_block .= '<td style="color:' . $bob_color . ';" width="40%" align="center">' . JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i) . '</td>
                                                ';
					else
						$best_division_block .= '<td style="color:' . $bod_color . ';" width="40%" align="center">' . JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i) . '</td>
                                                ';

					$best_division_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$best_division_block .= '<td colspan="2" align="' . (($show->show_format == 'Alternative' && $second_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                                ';
					}
					$best_division_block .= '</tr>
                                        </table>
                                    </td>
                                    ';

					$best_division_block .= '</tr>';
					if ($i == 3)
						break;
				}

				$best_division_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_division_block, true, false, false, false, '');
				$pdf->ln(1);
			}

			if (in_array($entry->show_class, $skip_breed_best)) {
				$previous_breed_entries = 0;
			} else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {
				$best_breed_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_breed_entries; $i++) {
					$best_breed_block .= '<tr style="line-height: 150%;">
                                    ';

					$best_breed_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
						$best_breed_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $first_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                                ';
					}
					$best_breed_block .= '</tr>
                                        </table>
                                    </td>
                                    ';

					$best_breed_block .= '<td style="color:' . $bob_color . ';" width="40%" align="center">' . JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i) . '</td>
                                            ';

					$best_breed_block .= '<td width="30%" align="right">
                                        <table width="100%">
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
						$best_breed_block .= '<td colspan="2" align="' . (($show->show_format == 'Alternative' && $second_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                                ';
					}
					$best_breed_block .= '</tr>
                                        </table>
                                    </td>
                                    ';

					$best_breed_block .= '</tr>';
					if ($i == 3)
						break;
				}
				$best_breed_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_breed_block, true, false, false, false, '');

				$pdf->ln(1);
			}
			$pdf->ln(1);

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}

					if ($previous_division != $entry->catalog_division) {
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;

					$processed++;
					$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
					$data = array(
						'total' => $total,
						'processed' => $processed,
						'log' => $log
					);
					fputs($fp, serialize($data));
					fclose($fp);
				}

				$print_block = 0;
			} else {
				$previous_breed_entries--;
				$previous_division_entries--;
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_logo = '/media/com_toes/images/paw32X32.png';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				if($params->get('use_logo_for_pdf')) {
					$show_class_block .='<img src="' . $header_logo . '" />';
				} else {
					$show_class_block .=' ';
				}
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_MASTER_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', $font_size + 2);
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
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
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
												<td align="left" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
												<td align="right" colspan="' . count($show_day_rings[$show_days[0]->show_day_id]) . '">' . (count($session_rings[$show_days[0]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
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
					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
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
												<td align="left" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM' : '&nbsp;') . '</td>
												<td align="right" colspan="' . count($show_day_rings[$show_days[1]->show_day_id]) . '">' . (count($session_rings[$show_days[1]->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                            </tr>
                                            <tr>
                                                ';

					foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
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
					$pdf->SetFont('ptsansnarrow', '', $font_size);
					$pdf->writeHTML($judge_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<div style="text-align:center; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</div>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<div style="text-align:center; color:' . $breed_title_color . '; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</div>';

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
	$pdf->page_layout = 'three_days';
	foreach ($entries as $entry) {
		if ($previous_class != $entry->show_class) {
			$show_class = str_replace('LH', '', $entry->show_class);
			$show_class = str_replace('SH', '', $show_class);
			$show_class = trim($show_class);

			if (!in_array($show_class, $available_classes))
				$available_classes[] = $show_class;

			if ($previous_class != '')
				$pdf->AddPage();

			$header_logo = '/media/com_toes/images/paw32X32.png';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			if($params->get('use_logo_for_pdf')) {
				$show_class_block .='<img src="' . $header_logo . '" />';
			} else {
				$show_class_block .=' ';
			}
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_MASTER_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			$pdf->SetFont('ptsanscaption', '', $font_size + 2);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');

			$judge_block = '<table width="100%">
                                <tr>
                            ';

			$judge_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">&nbsp;</td>
                            ';

			if ($show->show_format != 'Alternative') {
				foreach ($show_days as $show_day) {
					if (count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                        <table>
                                            <tr>
                                                <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
						$judge_block .= '<td align="left">' . $pr_show_day->judge_abbreviation . '</td>
                                        ';
					}
					$judge_block .= '       </tr>
                                        </table>
                                    </td>
                                    ';
				}
			} else {
				foreach ($show_days as $show_day) {
					if (count($show_day_rings[$show_day->show_day_id]) == 0)
						continue;
					$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                        <table>
                                            <tr>
												<td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM' : '&nbsp;') . '</td>
												<td align="right" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                            </tr>
                                            <tr>
                                                ';
					foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
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
				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln();
			}

			$previous_class = $entry->show_class;
			$previous_breed = '';
			$previous_division = '';
			$previous_color = '';
		}

		if ($previous_catalog_number != $entry->catalog_number) {
			$entry_block = '<table width="100%">
                                <tr>
                            ';

			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

			$entry_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">
                                <table>
                                    ';

			$entry_block .= '<tr>
								<td width="67%" align="left" >' . ( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name) ) . '</td>
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
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table width="100%">
                                                <tr>
                                                    ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
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
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table width="100%">
                                                <tr>
                                                    ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							if (isset($show_day_entries[$show_day->show_day_id][$entry->catalog_number])) {
								if (($pr_show_day->ring_timing == 1 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($pr_show_day->ring_timing == 2 && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_PM))
									$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">____</td>';
								else
									$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
							} else
								$entry_block .= '<td align="' . (($pr_show_day->ring_timing == 1) ? 'left' : 'right') . '" valign="top">&nbsp;</td>';
						}
						$entry_block .= '       </tr>
                                            </table>
                                        </td>
                                        ';
					}
				}
			} else
				$entry_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '"></td>';

			$entry_block .= '</tr>
                        </table>';
		}

		$pdf->startTransaction();
		$block_page = $pdf->getPage();
		$print_block = 2; // 2 tries max
		while ($print_block > 0) {
			if ($previous_catalog_number != $entry->catalog_number) {
				if ($previous_breed != $entry->breed_name) {
					$previous_breed_entries = 1;
					$previous_division_entries = 1;
					$pdf->SetFont('ptsans', 'b', $font_size + 4);
					$breed_block = '<span style="text-align:left; font-weight:bold;color:' . $breed_title_color . '; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->breed_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_breed_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
					$previous_division_entries = 1;
					$pdf->SetFont('ptsans', '', $font_size + 2);
					$breed_block = '<span style="text-align:left;color:' . $division_title_color . '; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->catalog_division) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_division_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left; font-weight:bold; padding:5px 0;">' . strtoupper($entry->color_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			$next = $cur + 1;
			if (in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($entries) ||
					( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
				$best_division_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_division_entries; $i++) {
					$best_division_block .= '<tr style="line-height: 150%;">
                                    ';
					if (!in_array($entry->show_class, $skip_breed_best) && (($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name)))
						$best_division_block .= '<td style="color:' . $bob_color . ';" width="' . (count($show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i) . '</td>
                                    ';
					else
						$best_division_block .= '<td style="color:' . $bod_color . ';" width="' . (count($show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i) . '</td>
                                    ';
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$best_division_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table>
                                                <tr>
                                                    ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$best_division_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                            ';
						}
						$best_division_block .= '       </tr>
                                            </table>
                                        </td>
                                        ';
					}

					$best_division_block .= '</tr>';
					if ($i == 3)
						break;
				}

				$best_division_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_division_block, true, false, false, false, '');
				$pdf->ln(1);
			}

			if (in_array($entry->show_class, $skip_breed_best)) {
				$previous_breed_entries = 0;
			} else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {
				$best_breed_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_breed_entries; $i++) {
					$best_breed_block .= '<tr style="line-height: 150%;">
                                    ';

					$best_breed_block .= '<td style="color:' . $bob_color . ';" width="' . (count($show_days) == 3 ? '40%' : '50%') . '">' . JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i) . '</td>
                                    ';

					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$best_breed_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table>
                                                <tr>
                                                    ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$best_breed_block .= '<td colspan="2" align="' . (($show->show_format != 'Alternative' || $pr_show_day->ring_timing == 1) ? 'left' : 'right') . '">____</td>
                                            ';
						}
						$best_breed_block .= '       </tr>
                                            </table>
                                        </td>
                                        ';
					}

					$best_breed_block .= '</tr>';
					if ($i == 3)
						break;
				}
				$best_breed_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($best_breed_block, true, false, false, false, '');
				$pdf->ln(1);
			}
			$pdf->ln(1);

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}

					if ($previous_division != $entry->catalog_division) {
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;

					$processed++;
					$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
					$data = array(
						'total' => $total,
						'processed' => $processed,
						'log' => $log
					);
					fputs($fp, serialize($data));
					fclose($fp);
				}

				$print_block = 0;
			} else {
				$previous_breed_entries--;
				$previous_division_entries--;
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage();

				$header_logo = '/media/com_toes/images/paw32X32.png';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				if($params->get('use_logo_for_pdf')) {
					$show_class_block .='<img src="' . $header_logo . '" />';
				} else {
					$show_class_block .=' ';
				}
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_MASTER_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', $font_size + 2);
				$pdf->writeHTML($show_class_block, true, false, false, false, '');

				$judge_block = '<table width="100%">
                                    <tr>
                                ';

				$judge_block .= '<td width="' . (count($show_days) == 3 ? '40%' : '50%') . '">&nbsp;</td>
                                ';

				if ($show->show_format != 'Alternative') {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table width="100%">
                                                <tr>
                                                    <td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . '</td>
                                                </tr>
                                                <tr>
                                                    ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
							$judge_block .= '<td align="left">' . $pr_show_day->judge_abbreviation . '</td>
                                            ';
						}
						$judge_block .= '       </tr>
                                            </table>
                                        </td>
                                        ';
					}
				} else {
					foreach ($show_days as $show_day) {
						if (count($show_day_rings[$show_day->show_day_id]) == 0)
							continue;
						$judge_block .= '<td width="' . (count($show_days) == 3 ? '20%' : '50%') . '">
                                            <table>
                                                <tr>
													<td align="left" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['AM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' AM' : '&nbsp;') . '</td>
													<td align="right" colspan="' . count($show_day_rings[$show_day->show_day_id]) . '">' . (count($session_rings[$show_day->show_day_id]['PM']) ? strtoupper(date('l', strtotime($show_day->show_day_date))) . ' PM' : '&nbsp;') . '</td>
                                                </tr>
                                                <tr>
                                                    ';
						foreach ($show_day_rings[$show_day->show_day_id] as $pr_show_day) {
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
					$pdf->SetFont('ptsansnarrow', '', $font_size);
					$pdf->writeHTML($judge_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left;color:' . $breed_title_color . '; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_breed = $entry->breed_name;
				}

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
		$cur++;
	}
}

$file = $app->input->getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Processing Congress Entries....";
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

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
foreach ($temp_rings as $ring) {
	$congress_rings[strtolower($ring->ring_name)][] = $ring;
}

$final_entries = array();
$temp_entries = array();
foreach ($entries as $entry) {
	$temp_entries[$entry->catalog_number] = $entry;
}

$ctlg_numbers = array();
$cngrs_entries_by_ring_id = array();
foreach ($congress_catalog as $entry) {
	$cngrs_entries_by_ring_id[$entry->ring_id][] = $entry->catalog_number;
	$temp_entries[$entry->catalog_number]->show_class = $entry->show_class;
	if (!isset($ctlg_numbers[strtolower($entry->ring_name)]))
		$ctlg_numbers[strtolower($entry->ring_name)] = array();
	if (!in_array($entry->catalog_number, $ctlg_numbers[strtolower($entry->ring_name)])) {
		$ctlg_numbers[strtolower($entry->ring_name)][] = $entry->catalog_number;
		$final_entries[strtolower($entry->ring_name)][] = $temp_entries[$entry->catalog_number];
	}
}

$temp_entries = array();
foreach ($final_entries as $ring_number => $congress_entries) {
	$temp_entries[$ring_number] = TOESHelper::aasort($congress_entries, 'catalog_number');
}
$final_entries = $temp_entries;
$orientation = 'P';
$pdf->page_layout = '';	

foreach ($final_entries as $ring_number => $cng_entries) {
	unset($congress_entries);
	$congress_entries = array_values($cng_entries);

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

	$pdf->congress_rings = $rings;

	//$judge_abbreviation = $congress_rings[$ring_number]->judge_abbreviation;
	$pdf->AddPage($orientation);
	$pdf->page_layout = 'congress';

	$header_logo = '/media/com_toes/images/paw32X32.png';
	$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
	if($params->get('use_logo_for_pdf')) {
		$show_class_block .='<img src="' . $header_logo . '" />';
	} else {
		$show_class_block .=' ';
	}
	$show_class_block .='</td><td style="width:26%">';
	$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
	$show_class_block .='</td>';
	//$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name . '<br/>' . $entry->show_class) . '</div></td>';
	$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name) . '</div></td>';
	$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
	$pdf->SetFont('ptsanscaption', '', $font_size + 2);
	$pdf->writeHTML($show_class_block, true, false, false, false, '');

	$judge_block = '<table width="100%">
						<tr>
							<td width="' . (100 - (count($rings) * 15)) . '%">&nbsp;</td>';
	foreach ($rings as $ring) {
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

	$pdf->SetFont('ptsansnarrow', '', $font_size);
	$pdf->writeHTML($judge_block, true, false, false, false, '');
	$pdf->ln(1);

	$file = $app->input->getVar('file', '');
	$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
	$log = "Processing Congress Entries for ".$judge_name." ....";
	$data = array(
		'total' => $total,
		'processed' => $processed,
		'log' => $log
	);
	fputs($fp, serialize($data));
	fclose($fp);

	foreach ($congress_entries as $entry) {

		/* if ($previous_class != $entry->show_class) {
		  if ($previous_class != '')
		  $pdf->AddPage($orientation);

		  $header_logo = '/media/com_toes/images/paw32X32.png';
		  $show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
		  if($params->get('use_logo_for_pdf')) {
			  $show_class_block .='<img src="' . $header_logo . '" />';
		  } else {
		      $show_class_block .=' ';
		  }
		  $show_class_block .='</td><td style="width:26%">';
		  $show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
		  $show_class_block .='</td>';
		  $show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name . '<br/>' . $entry->show_class) . '</div></td>';
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
		  } */

		if ($previous_catalog_number != $entry->catalog_number) {
			$entry_block = '<table width="100%">
                                <tr>
                            ';

			$isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

			$entry_block .= '<td width="' . (100 - (count($rings) * 15)) . '%">
                                <table>
                                    ';

			$entry_block .= '<tr>
								<td width="67%" align="left" >' . ( ($show->show_catalog_cat_names_bold) ? ('<b>' . strtoupper($entry->catalog_cat_name) . '</b>') : strtoupper($entry->catalog_cat_name) ) . '</td>
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

			foreach ($rings as $ring) {
				if (in_array($entry->catalog_number, $cngrs_entries_by_ring_id[$ring->ring_id])) {
					$entry_block .= '<td width="15%">
										<table width="100%">
											<tr>
												<td align="left" valign="top">__________</td>
											</tr>
										</table>
									</td>';
				} else {
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
					$previous_breed_entries = 1;
					$previous_division_entries = 1;
					$pdf->SetFont('ptsans', 'b', $font_size + 4);
					$breed_block = '<span style="text-align:left; font-weight:bold;color:' . $breed_title_color . '; text-decoration:underline; padding:5px 0;">' . strtoupper($entry->breed_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_breed_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_division != $entry->catalog_division) {
					$previous_division_entries = 1;
					$pdf->SetFont('ptsans', '', $font_size + 2);
					$breed_block = '<span style="text-align:left; text-decoration:underline;color:' . $division_title_color . '; padding:5px 0;">' . strtoupper($entry->catalog_division) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				} else
					$previous_division_entries++;

				if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left; font-weight:bold; padding:5px 0;">' . strtoupper($entry->color_name) . '</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($entry_block, true, false, false, false, '');
			}

			$next = $cur + 1;
			if ($entry->show_class != 'Ex Only' && in_array($entry->show_class, $skip_division_best)) {
				$previous_division_entries = 0;
			} else if ($next == count($congress_entries) ||
					( isset($congress_entries[$next]) && ($entry->breed_name != $congress_entries[$next]->breed_name || $entry->catalog_division != $congress_entries[$next]->catalog_division))) {

				$judge_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_division_entries; $i++) {
					$judge_block .= '<tr style="line-height: 150%;">
                                    ';
					if (!in_array($entry->show_class, $skip_breed_best) && (($next == count($congress_entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $congress_entries[$next]->breed_name)))
						$judge_block .= '<td style="color:' . $bob_color . ';" width="' . (100 - (count($rings) * 15)) . '%">' . JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i) . '</td>
                                    ';
					else
						$judge_block .= '<td style="color:' . $bod_color . ';" width="' . (100 - (count($rings) * 15)) . '%">' . JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i) . '</td>
                                    ';
					foreach ($rings as $ring) {
						$judge_block .= '<td width="15%">
                                            <table width="100%">
                                                <tr>
                                                    <td align="left">__________</td>
                                                </tr>
                                            </table>
                                        </td>
                                        ';
					}
					$judge_block .= '</tr>';
					if ($i == 3)
						break;
				}

				$judge_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($judge_block, true, false, false, false, '');

				$pdf->ln(1);
			}

			if ($entry->show_class != 'Ex Only' && in_array($entry->show_class, $skip_breed_best)) {
				$previous_breed_entries = 0;
			} else if ($previous_breed_entries > $previous_division_entries && ($next == count($congress_entries) || (isset($congress_entries[$next]) && $entry->breed_name != $congress_entries[$next]->breed_name))) {
				$judge_block = '<table width="100%">
                                    ';
				for ($i = 1; $i <= $previous_breed_entries; $i++) {
					$judge_block .= '<tr style="line-height: 150%;">
                                        <td style="color:' . $bob_color . ';" width="' . (100 - (count($rings) * 15)) . '%">' . JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i) . '</td>';

					foreach ($rings as $ring) {

						$judge_block .= '<td width="15%">
                                            <table width="100%">
                                                <tr>
                                                    <td align="left">__________</td>
                                                </tr>
                                            </table>
                                        </td>';
					}

					$judge_block .= '</tr>';
					if ($i == 3)
						break;
				}
				$judge_block .= '
                            </table>';

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln(1);
			}
			$pdf->ln(1);

			// do not split BLOCKS in multiple pages
			if ($pdf->getPage() == $block_page) {
				if ($previous_catalog_number != $entry->catalog_number) {
					if ($previous_breed != $entry->breed_name) {
						$previous_division = '';
						$previous_color = '';
						$previous_breed = $entry->breed_name;
					}

					if ($previous_division != $entry->catalog_division) {
						$previous_color = '';
						$previous_division = $entry->catalog_division;
					}

					if ($previous_color != $entry->color_name)
						$previous_color = $entry->color_name;

					$previous_catalog_number = $entry->catalog_number;
				}

				$print_block = 0;
			} else {
				$previous_breed_entries--;
				$previous_division_entries--;
				// rolls back to the last (re)start
				$pdf = $pdf->rollbackTransaction();
				$pdf->AddPage($orientation);

				$header_logo = '/media/com_toes/images/paw32X32.png';
				$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
				if($params->get('use_logo_for_pdf')) {
					$show_class_block .='<img src="' . $header_logo . '" />';
				} else {
					$show_class_block .=' ';
				}
				$show_class_block .='</td><td style="width:26%">';
				$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
				$show_class_block .='</td>';
				//$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name . '<br/>' . $entry->show_class) . '</div></td>';
				$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . strtoupper($judge_name) . '</div></td>';
				$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
				$pdf->SetFont('ptsanscaption', '', $font_size + 2);
				$pdf->writeHTML($show_class_block, true, false, false, false, '');

				$judge_block = '<table width="100%">
                                <tr>
                                    <td width="' . (100 - (count($rings) * 15)) . '%">&nbsp;</td>';
				foreach ($rings as $ring) {
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

				$pdf->SetFont('ptsansnarrow', '', $font_size);
				$pdf->writeHTML($judge_block, true, false, false, false, '');
				$pdf->ln(1);

				if ($previous_color == $entry->color_name && $previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left; font-weight:bold;">( ' . strtoupper($entry->color_name) . ' ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_color = $entry->color_name;
				} else if ($previous_breed == $entry->breed_name) {
					$pdf->SetFont('ptsans', 'b', $font_size + 2);
					$breed_block = '<span style="text-align:left;color:' . $breed_title_color . '; font-weight:bold;">( ' . strtoupper($entry->breed_name) . ' ' . JText::_('COM_TOES_CONTINUED') . ' )</span>';

					$pdf->writeHTML($breed_block, true, false, false, false, '');
					$pdf->ln(1);

					$previous_breed = $entry->breed_name;
				}

				$block_page = $pdf->getPage();
				--$print_block;
			}
		}
		$cur++;
	}
}

$file = $app->input->getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Generating Final sheets....";
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

//Final Sheets
$new_breed_classes = array(
	'PNB',
	'ANB',
	'NT',
	'Ex Only',
	'For Sale'
);

$classes = array('Kitten', 'Cat', 'Alter','HHP Kitten', 'HHP');

$class_cnt = 0;
$class_group = [];
$class_groups = [];
foreach ($classes as $class) {	
	if(!in_array($class, $available_classes)){
		continue;
	}

	$class_group[] = $class;
	$class_cnt++;

	if($class_cnt == 2) {
		$class_groups[] = $class_group;
		$class_group = [];
		$class_cnt = 0;
	}
}

if($class_cnt == 1) {
	$class_group[] = 'PNB';
	$class_group[] = 'ANB';
	$class_group[] = 'NT';
	$class_groups[] = $class_group;
} else {
	$class_group = [];
	$class_group[] = 'PNB';
	$class_group[] = 'ANB';
	$class_group[] = 'NT';

	if(in_array('PNB', $available_classes) && in_array('ANB', $available_classes) && in_array('NT', $available_classes)){
		$class_groups[] = $class_group;
	} else {
		$arr = array_merge(end($class_groups), $class_group);
		array_pop($class_groups);
		$class_groups[] = $arr;
	}
} 

// $class_groups = array(
// 	array('Kitten', 'Cat'),
// 	array('Alter','HHP Kitten'),
// 	array('HHP','PNB','ANB','NT')
// );

$rings = TOESHelper::getShowRings($show_id);

$show_day_rings = array();
foreach ($rings as $ring) {
	$show_date_string = date('d-M-Y', strtotime($ring->show_day_date));

	if($is_alernative) {
		if($ring->ring_timing == 1) {
			$show_date_string .= " (AM)";
		} else {
			$show_date_string .= " (PM)";
		}
	}

	$show_day_rings[$show_date_string][] = $ring;
}

$max_rings_on_day = 0;
$ring_counts = array();
foreach ($show_day_rings as $show_day => $rings) {
	$cnt = 0;
	foreach ($rings as $ring) {
		if($ring->format == 'Congress') {
			continue;
		}
		if ($ring->format == 'Specialty')
			$cnt += 2;
		else
			$cnt++;
	}
	
	$ring_counts[$show_day] = $cnt;
	if ($max_rings_on_day < $cnt)
		$max_rings_on_day = $cnt;
}

// if ($max_rings_on_day > 10 || $page_orientation == 'L')
// 	$orientation = 'L';
// else
// 	$orientation = 'P';
$orientation = 'P';
$pdf->page_layout = '';	

foreach ($show_day_rings as $show_day => $day_rings) {
	//$pdf->AddPage($orientation);
	$header_logo = '/media/com_toes/images/paw32X32.png';
	$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
	if($params->get('use_logo_for_pdf')) {
		$show_class_block .='<img src="' . $header_logo . '" />';
	} else {
		$show_class_block .=' ';
	}
	$show_class_block .='</td><td style="width:26%">';
	$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
	$show_class_block .='</td>';
	$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . JText::_('FINAL_SHEETS') . '</div></td>';
	$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
	//$pdf->SetFont('ptsanscaption', '', $font_size + 2);
	//$pdf->writeHTML($show_class_block, true, false, false, false, '');

	//if (!$is_continuous) {
		//$show_day_block = '<div>' . strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))) . '</div>';
		$show_day_block = '<table width="100%"><tr>';
		$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_SHOW_CLUB_NAME').'</td>';
		$show_day_block .= '<td width="25%">'.$show->club_name.'</td>';
		$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_LOCATION').'</td>';
		$show_day_block .= '<td width="25%">'.$show->Show_location.'</td>';
		$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('JDATE').'</td>';
		$show_day_block .= '<td width="20%">'.$show_day.'</td>';
		$show_day_block .= '</tr></table>';
		//$pdf->SetFont('ptsanscaption', '', $font_size + 2);
		//$pdf->writeHTML($show_day_block, true, false, false, false, '');
	//}

	$rings = array();
	$congress_rings = array();
	foreach ($day_rings as $ring) {
		if($ring->format != 'Congress') {
			$rings[] = $ring;
		} else {
			$congress_rings[] = $ring;
		}
	}
	
	if($rings) {
		$judge_header_index = 0;
		$judge_header_block = array();
		$current_index = 0;
		
		while ($max_rings_on_day > $current_index) {
			$class_final_block = '<table width="100%">';
			$i = 0;
			$class_final_block .= '<tr style="line-height: 200%;">';
			$class_final_block .= '<td width="10%">' . JText::_('COM_TOES_CATALOG_FINAL_SHEETS_RING_NUMBER') . '</td>';
			
			foreach ($rings as $ring) {
				if ($i < $current_index) {
					if ($ring->format == 'Specialty') {
						$i +=2;
					} else {
						$i++;
					}
					continue;
				}

				if ($ring->format == 'Specialty') {
					$class_final_block .= '<td align="center">';
					$class_final_block .= $ring->ring_number;
					$class_final_block .= '</td>';
					$i++;
					$class_final_block .= '<td align="center">';
					$class_final_block .= $ring->ring_number;
					$class_final_block .= '</td>';
					$i++;
				} else {
					if ($ring->format == 'Congress') {
						$class_final_block .= '<td align="center">';
						$class_final_block .= $ring->ring_name;
						$class_final_block .= '</td>';
					} else {
						$class_final_block .= '<td align="center">';
						$class_final_block .= $ring->ring_number;
						$class_final_block .= '</td>';
					}
					$i++;
				}
				if ($i >= $current_index + 8)
					break;
			}
			$class_final_block .= '</tr>';
			
			$i = 0;
			$class_final_block .= '<tr style="line-height: 200%;">';
			$class_final_block .= '<td width="10%">' . JText::_('COM_TOES_CATALOG_FINAL_SHEETS_JUDGE_INITIAL') . '</td>';

			foreach ($rings as $ring) {
				if ($i < $current_index) {
					if ($ring->format == 'Specialty') {
						$i +=2;
					} else {
						$i++;
					}
					continue;
				}

				if ($ring->format == 'Specialty') {
					$class_final_block .= '<td align="center">';
					$class_final_block .= $ring->judge_abbreviation;
					$class_final_block .= '</td>';
					$i++;
					$class_final_block .= '<td align="center">';
					$class_final_block .= $ring->judge_abbreviation;
					$class_final_block .= '</td>';
					$i++;
				} else {
					$class_final_block .= '<td align="center">';
					$class_final_block .= $ring->judge_abbreviation;
					$class_final_block .= '</td>';
					$i++;
				}
				if ($i >= $current_index + 8)
					break;
			}
			$class_final_block .= '</tr>';
			$i = 0;
			$class_final_block .= '<tr style="line-height: 200%;">';
			$class_final_block .= '<td width="10%">AB/LH/SH</td>';

			foreach ($rings as $ring) {
				if ($i < $current_index) {
					if ($ring->format == 'Specialty') {
						$i +=2;
					} else {
						$i++;
					}
					continue;
				}

				if ($ring->format == 'Specialty') {
					$class_final_block .= '<td align="center">';
					$class_final_block .= ' LH ';
					$class_final_block .= '</td>';
					$i++;
					$class_final_block .= '<td align="center">';
					$class_final_block .= ' SH ';
					$class_final_block .= '</td>';
					$i++;
				} else {
					if ($ring->format == 'Congress') {
						$class_final_block .= '<td align="center">';
						$class_final_block .= '&nbsp;';
						$class_final_block .= '</td>';
					} else {
						$class_final_block .= '<td align="center">';
						$class_final_block .= ' AB ';
						$class_final_block .= '</td>';
					}
					$i++;
				}
				if ($i >= $current_index + 8)
					break;
			}
			$class_final_block .= '</tr>';
			$class_final_block .= '</table>';
			$current_index = $i;

			$judge_header_block[$judge_header_index] = $class_final_block;
			$judge_header_index++;
			
			if($current_index >= $ring_counts[$show_day]) {
				break;
			}
		}
		
		/*$judge_header_index = 0;
		$pdf->SetFont('ptsanscaption', '', $font_size);
		$pdf->writeHTML($judge_header_block[$judge_header_index], true, false, false, false, '');
		$pdf->ln(1);*/
		
//		$previous_class = '';
		foreach ($class_groups as $class_group) {
			$current_index = 0;
			$judge_header_index = 0;
			while ($max_rings_on_day > $current_index) {
				$i = 0;
				foreach ($rings as $ring) {
					if ($i < $current_index) {
						if ($ring->format == 'Specialty') {
							$i +=2;
						} else {
							$i++;
						}
						continue;
					}
					if ($ring->format == 'Specialty') {
						$i +=2;
					} else {
						$i++;
					}
					if ($i >= $current_index + 8)
						break;
				}

				$class_final_block = '';
				foreach ($class_group as $class) {
					
					if(!in_array($class, $available_classes)){
						continue;
					}

					/*if(in_array($previous_class, $class_group) && !in_array($class, $class_group)) {
						$pdf->AddPage($orientation);

						$header_logo = '/media/com_toes/images/paw32X32.png';
						$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
						if($params->get('use_logo_for_pdf')) {
							$show_class_block .='<img src="' . $header_logo . '" />';
						} else {
							$show_class_block .=' ';
						}
						$show_class_block .='</td><td style="width:26%">';
						$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
						$show_class_block .='</td>';
						$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . JText::_('FINAL_SHEETS') . '</div></td>';
						$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
						$pdf->SetFont('ptsanscaption', '', $font_size + 2);
						$pdf->writeHTML($show_class_block, true, false, false, false, '');

						if (!$is_continuous) {
							//$show_day_block = '<div>' . strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))) . '</div>';
							$show_day_block = '<table width="100%"><tr>';
							$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_SHOW_CLUB_NAME').'</td>';
							$show_day_block .= '<td width="25%">'.$show->club_name.'</td>';
							$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_LOCATION').'</td>';
							$show_day_block .= '<td width="25%">'.$show->Show_location.'</td>';
							$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('JDATE').'</td>';
							$show_day_block .= '<td width="20%">'.$show_day.'</td>';
							$show_day_block .= '</tr></table>';
							$pdf->SetFont('ptsanscaption', '', $font_size + 2);
							$pdf->writeHTML($show_day_block, true, false, false, false, '');
						}
						$pdf->SetFont('ptsanscaption', '', $font_size);
						$pdf->writeHTML($judge_header_block[$judge_header_index], true, false, false, false, '');
					}
				
		
					if ($class == 'Ex Only' || $class == 'For Sale') {
						continue;
					}
		
					$previous_class = $class;
					*/

					$class_final_block .= '<table width="100%">';
					$class_final_block .= '<tr style="line-height: 200%;">';
					$class_final_block .= '<td width="10%">' . $class . '</td>';
					for ($k = $current_index; $k < $i; $k++) {
						$class_final_block .= '<td>';
						$class_final_block .= '&nbsp;';
						$class_final_block .= '</td>';
					}
					$class_final_block .= '</tr>';

					if (!in_array($class, $new_breed_classes)) {
						for ($j = 1; $j <= 10; $j++) {
							$class_final_block .= '<tr style="line-height: 200%;">';
							if($j == 1) {
								$class_final_block .= '<td width="10%" align="center">Best</td>';
							} else {
								$class_final_block .= '<td width="10%" align="center">' . TOESHelper::addOrdinalNumberSuffix($j) . '</td>';
							}

							for ($k = $current_index; $k < $i; $k++) {
								$class_final_block .= '<td align="center">';
								$class_final_block .= '__________';
								$class_final_block .= '</td>';
							}
							$class_final_block .= '</tr>';
						}

						$class_final_block .= '<tr style="line-height: 200%;">';
						$class_final_block .= '<td width="10%" align="center">' . JText::_('COUNT') . '</td>';

						for ($k = $current_index; $k < $i; $k++) {
							$class_final_block .= '<td align="center">';
							$class_final_block .= '__________';
							$class_final_block .= '</td>';
						}
						$class_final_block .= '</tr>';
					} else {
						$class_final_block .= '<tr style="line-height: 200%;">';
						$class_final_block .= '<td width="10%" align="center">Best</td>';

						for ($k = $current_index; $k < $i; $k++) {
							$class_final_block .= '<td align="center">';
							$class_final_block .= '__________';
							$class_final_block .= '</td>';
						}
						$class_final_block .= '</tr>';

						$class_final_block .= '<tr style="line-height: 200%;">';
						$class_final_block .= '<td width="10%" align="center">' . JText::_('COUNT') . '</td>';

						for ($k = $current_index; $k < $i; $k++) {
							$class_final_block .= '<td align="center">';
							$class_final_block .= '__________';
							$class_final_block .= '</td>';
						}
						$class_final_block .= '</tr>';
					}
					$class_final_block .= '</table><br/><br/>';
				
					/*if($current_index >= $ring_counts[$show_day]) {
						continue;
					}*/
				}
				$current_index = $i;

				/*if(!$class_final_block) {
					continue;
				}*/

				if($class_final_block) {
					$pdf->AddPage($orientation);
					$header_logo = '/media/com_toes/images/paw32X32.png';
					$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
					if($params->get('use_logo_for_pdf')) {
						$show_class_block .='<img src="' . $header_logo . '" />';
					} else {
						$show_class_block .=' ';
					}
					$show_class_block .='</td><td style="width:26%">';
					$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
					$show_class_block .='</td>';
					$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . JText::_('FINAL_SHEETS') . '</div></td>';
					$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
					$pdf->SetFont('ptsanscaption', '', $font_size + 2);
					$pdf->writeHTML($show_class_block, true, false, false, false, '');

					//if (!$is_continuous) {
						//$show_day_block = '<div>' . strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))) . '</div>';
						$show_day_block = '<table width="100%"><tr>';
						$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_SHOW_CLUB_NAME').'</td>';
						$show_day_block .= '<td width="25%">'.$show->club_name.'</td>';
						$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_LOCATION').'</td>';
						$show_day_block .= '<td width="25%">'.$show->Show_location.'</td>';
						$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('JDATE').'</td>';
						$show_day_block .= '<td width="20%">'.$show_day.'</td>';
						$show_day_block .= '</tr></table>';
						$pdf->SetFont('ptsanscaption', '', $font_size + 2);
						$pdf->writeHTML($show_day_block, true, false, false, false, '');
					//}

					$pdf->SetFont('ptsanscaption', '', $font_size);
					$pdf->writeHTML($judge_header_block[$judge_header_index], true, false, false, false, '');
					$judge_header_index++;
					$pdf->SetFont('ptsansnarrow', '', $font_size + 1);
					$pdf->writeHTML($class_final_block, true, false, false, false, '');
					$pdf->ln(1);
				}

				if($current_index >= $ring_counts[$show_day]) {
					break;
				}
			}
		}
	}
	
	// $query = "SELECT `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation`
    //         FROM `#__toes_ring` AS `r`
    //         LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`)
    //         WHERE (`r`.`ring_format` = 3) AND `r`.`ring_show` = {$show_id} AND `r`.`ring_show_day` = {$show_day}
    //         ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`";
	// $db->setQuery($query);
	// $congress_rings = $db->loadObjectList();

	if ($congress_rings) {

		//if($rings) {
			$pdf->AddPage($orientation);

			$header_logo = '/media/com_toes/images/paw32X32.png';
			$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
			if($params->get('use_logo_for_pdf')) {
				$show_class_block .='<img src="' . $header_logo . '" />';
			} else {
				$show_class_block .=' ';
			}
			$show_class_block .='</td><td style="width:26%">';
			$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
			$show_class_block .='</td>';
			$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . JText::_('FINAL_SHEETS') . '</div></td>';
			$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
			$pdf->SetFont('ptsanscaption', '', $font_size + 2);
			$pdf->writeHTML($show_class_block, true, false, false, false, '');

			//if (!$is_continuous) {
				//$show_day_block = '<div>' . strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))) . '</div>';
				$show_day_block = '<table width="100%"><tr>';
				$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_SHOW_CLUB_NAME').'</td>';
				$show_day_block .= '<td width="25%" align="center">'.$show->club_name.'</td>';
				$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_LOCATION').'</td>';
				$show_day_block .= '<td width="25%" align="center">'.$show->Show_location.'</td>';
				$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('JDATE').'</td>';
				$show_day_block .= '<td width="20%" align="center">'.$show_day.'</td>';
				$show_day_block .= '</tr></table>';
				$pdf->SetFont('ptsanscaption', '', $font_size + 2);
				$pdf->writeHTML($show_day_block, true, false, false, false, '');
			//}
		//}

		$current_index = 0;
		while (count($congress_rings) > $current_index) {
			$class_final_block = '<table>';
			$i = 0;
			$class_final_block .= '<tr style="line-height: 200%;">';
			$class_final_block .= '<td width="10%">'.JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER').'</td>';

			foreach ($congress_rings as $ring) {
				if ($i < $current_index) {
					$i++;
					continue;
				}
				$class_final_block .= '<td align="center">';
				$class_final_block .= $ring->ring_name;
				$class_final_block .= '</td>';
				$i++;
				if ($i >= $current_index + 10)
					break;
			}
			$class_final_block .= '</tr>';

			$i = 0;
			$class_final_block .= '<tr style="line-height: 200%;">';
			$class_final_block .= '<td width="10%">'.JText::_('COM_TOES_CATALOG_FINAL_SHEETS_JUDGE_INITIAL').'</td>';

			foreach ($congress_rings as $ring) {
				if ($i < $current_index) {
					$i++;
					continue;
				}
				$class_final_block .= '<td align="center">';
				$class_final_block .= $ring->judge_abbreviation;
				$class_final_block .= '</td>';
				$i++;
				if ($i >= $current_index + 10)
					break;
			}
			$class_final_block .= '</tr>';

			$class_final_block .= '<tr style="line-height: 200%;">';
			$class_final_block .= '<td align="center">';
			$class_final_block .= '&nbsp;';
			$class_final_block .= '</td>';

			for ($k = $current_index; $k < $i; $k++) {
				$class_final_block .= '<td align="center">';
				$class_final_block .= '&nbsp;';
				$class_final_block .= '</td>';
			}
			$class_final_block .= '</tr>';

			for ($j = 1; $j <= 10; $j++) {
				$class_final_block .= '<tr style="line-height: 200%;">';
				if($j == 1) {
					$class_final_block .= '<td width="10%" align="center">Best</td>';
				} else {
					$class_final_block .= '<td width="10%" align="center">' . TOESHelper::addOrdinalNumberSuffix($j) . '</td>';
				}

				for ($k = $current_index; $k < $i; $k++) {
					$class_final_block .= '<td align="center">';
					$class_final_block .= '__________';
					$class_final_block .= '</td>';
				}
				$class_final_block .= '</tr>';
			}

			$class_final_block .= '<tr style="line-height: 200%;">';
			$class_final_block .= '<td width="10%" align="center">' . JText::_('COUNT') . '</td>';

			for ($k = $current_index; $k < $i; $k++) {
				$class_final_block .= '<td align="center">';
				$class_final_block .= '__________';
				$class_final_block .= '</td>';
			}
			$class_final_block .= '</tr>';

			$class_final_block .= '</table><br/><br/>';
			$current_index = $i;

			$pdf->startTransaction();
			$block_page = $pdf->getPage();
			$print_block = 2; // 2 tries max
			while ($print_block > 0) {
				$pdf->SetFont('ptsansnarrow', '', $font_size + 1);
				$pdf->writeHTML($class_final_block, true, false, false, false, '');
				$pdf->ln(1);
				// do not split BLOCKS in multiple pages
				if ($pdf->getPage() == $block_page) {
					$print_block = 0;
				} else {
					// rolls back to the last (re)start
					$pdf = $pdf->rollbackTransaction();
					$pdf->AddPage($orientation);

					$header_logo = '/media/com_toes/images/paw32X32.png';
					$show_class_block = '<table style="width:100%;border-bottom:1px solid #000;"><tr><td style="width:4%">';
					if($params->get('use_logo_for_pdf')) {
						$show_class_block .='<img src="' . $header_logo . '" />';
					} else {
						$show_class_block .=' ';
					}
					$show_class_block .='</td><td style="width:26%">';
					$show_class_block .= '<span style="font-weight:bold;">' . JText::_('COM_TOES_CATALOG') . '</span><br/><span>' . JText::_('COM_TOES_WEBSITE') . '</span>';
					$show_class_block .='</td>';
					$show_class_block .='<td style="width:40%"><div style="text-align:center; font-weight:bold;font-size:25px">' . JText::_('FINAL_SHEETS') . '</div></td>';
					$show_class_block .='<td style="width:30%">&nbsp;</td></tr></table>';
					$pdf->SetFont('ptsanscaption', '', $font_size + 2);
					$pdf->writeHTML($show_class_block, true, false, false, false, '');

					//if (!$is_continuous) {
						//$show_day_block = '<div>' . strtoupper(date('l', strtotime($temp_show_days[$show_day]->show_day_date))) . '</div>';
						$show_day_block = '<table width="100%"><tr>';
						$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_SHOW_CLUB_NAME').'</td>';
						$show_day_block .= '<td width="25%" align="center">'.$show->club_name.'</td>';
						$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('COM_TOES_LOCATION').'</td>';
						$show_day_block .= '<td width="25%" align="center">'.$show->Show_location.'</td>';
						$show_day_block .= '<td width="10%" style="font-weight:bold;">'.JText::_('JDATE').'</td>';
						$show_day_block .= '<td width="20%" align="center">'.$show_day.'</td>';
						$show_day_block .= '</tr></table>';
						$pdf->SetFont('ptsanscaption', '', $font_size + 2);
						$pdf->writeHTML($show_day_block, true, false, false, false, '');
					//}

					$block_page = $pdf->getPage();
					--$print_block;
				}
			}
		}
	}
}

$file = $app->input->getVar('file', '');
$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'w');
$log = "Generating PDF file....";
$data = array(
	'total' => $total,
	'processed' => $processed,
	'log' => $log
);
fputs($fp, serialize($data));
fclose($fp);

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
	JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
	chmod(TOES_PDF_PATH . DS . $show_id, 0777);

if (file_exists(TOES_PDF_PATH . DS . $show_id . DS . 'mastercatalog.pdf'))
	unlink(TOES_PDF_PATH . DS . $show_id . DS . 'mastercatalog.pdf');

//Close and output PDF document
$pdf->Output(TOES_PDF_PATH . DS . $show_id . DS . 'mastercatalog.pdf', 'F');

//============================================================+
// END OF FILE
//============================================================+
