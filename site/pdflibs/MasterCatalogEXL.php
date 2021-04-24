<?php

jimport('excel.PHPExcel');
set_time_limit(5000);

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
foreach ($rings as $ring) {
    $show_day_rings[$ring->ring_show_day][] = $ring;
}

// create new PDF document
$excel = new PHPExcel();

$excel->getProperties()
        ->setCreator("TICA")
        ->setLastModifiedBy("TICA")
        ->setTitle(JText::_('COM_TOES_MASTER_CATALOG'));

$excel->setActiveSheetIndex(0);

$show = TOESHelper::getShowDetails($show_id);

$is_continuous = ($show->show_format == 'Continuous') ? true : false;
$is_alternative = ($show->show_format == 'Alternative') ? true : false;

$session_rings = array();
if ($is_alternative) {
    foreach ($show_days as $showday) {
        $session_rings[$showday->show_day_id]['AM'] = TOESHelper::getShowdayRings($showday->show_day_id, 1);
        $session_rings[$showday->show_day_id]['PM'] = TOESHelper::getShowdayRings($showday->show_day_id, 2);
    }
}

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
    'processed' => $processed
);
fputs($fp, serialize($data));
fclose($fp);

$breed_title_color = $division_title_color = $bod_color = $bob_color = '#000000';

if ($show->show_colored_catalog) {
    $params = JComponentHelper::getParams('com_toes');
    if ($params->get('breed_title_color'))
        $breed_title_color = $params->get('breed_title_color');
    if ($params->get('division_title_color'))
        $division_title_color = $params->get('division_title_color');
    if ($params->get('bod_color'))
        $bod_color = $params->get('bod_color');
    if ($params->get('bob_color'))
        $bob_color = $params->get('bob_color');
}

$breed_title_color = str_replace('#', '', $breed_title_color);
$division_title_color = str_replace('#', '', $division_title_color);
$bod_color = str_replace('#', '', $bod_color);
$bob_color = str_replace('#', '', $bob_color);

$BorderStyleArray = array('borders' => array('outline' => array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => 'FF000000'),),),);

$available_classes = array();
if (count($show_days) == 2) {

    $first_day_rings = count($show_day_rings[$show_days[0]->show_day_id]);
    $second_day_rings = count($show_day_rings[$show_days[1]->show_day_id]);

    /* if($is_alternative)
      {
      $first_day_rings = $first_day_rings * 2;
      $second_day_rings = $second_day_rings * 2;
      } */

    $col = 0;
    $row = 1;

    foreach ($entries as $entry) {
        if ($previous_class != $entry->show_class) {

            $show_class = str_replace('LH', '', $entry->show_class);
            $show_class = str_replace('SH', '', $show_class);
            $show_class = trim($show_class);

            if ($previous_class == '') {
                $sheet = $excel->getActiveSheet();
                $sheet->setTitle($entry->show_class);
            } else {
                $new_sheet = new PHPExcel_Worksheet($excel, $entry->show_class);
                $sheet = $excel->addSheet($new_sheet);
            }

            $col = 0;
            foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
                $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(5);
            }

            $col = $col + 4;

            foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
                $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(5);
            }

            $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($first_day_rings + 1))->setWidth(50);

            $col = 0;
            $row = 1;

            if (!in_array($show_class, $available_classes))
                $available_classes[] = $show_class;

            $sheet->mergeCellsByColumnAndRow($col, $row, $col + ($first_day_rings + $second_day_rings + 3), $row);
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(18);
            $sheet->getStyleByColumnAndRow($col, $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet->getStyleByColumnAndRow($col, $row)->getFill()->getStartColor()->setARGB('FFCCCCCC');
            $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->show_class));

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->mergeCellsByColumnAndRow($col, $row, $col + ($first_day_rings + $second_day_rings + 3), $row);
            $row++;

            if ($entry->show_class != 'Ex Only') {

                if (!$is_alternative) {
                    $col = 0;
                    $sheet->mergeCellsByColumnAndRow($col, $row, ($col + $first_day_rings - 1), $row);
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->setCellValueByColumnAndRow($col, $row, (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[0]->show_day_date)))));

                    $col = $col + $first_day_rings + 4;

                    $sheet->mergeCellsByColumnAndRow($col, $row, ($col + $second_day_rings - 1), $row);
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->setCellValueByColumnAndRow($col, $row, (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_days[1]->show_day_date)))));

                    $row++;

                    $col = 0;
                    foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValueByColumnAndRow($col++, $row, $first_show_day->judge_abbreviation);
                    }

                    $col = $col + 4;

                    foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValueByColumnAndRow($col++, $row, $second_show_day->judge_abbreviation);
                    }

                    $row++;
                } else {
                    $col = 0;

                    if (count($session_rings[$show_days[0]->show_day_id]['AM'])) {
                        $sheet->mergeCellsByColumnAndRow($col, $row, $col - 1 + (count($session_rings[$show_days[0]->show_day_id]['AM'])), $row);
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                        $sheet->setCellValueByColumnAndRow($col, $row, strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' AM');
                    }

                    $col += count($session_rings[$show_days[0]->show_day_id]['AM']);

                    if (count($session_rings[$show_days[0]->show_day_id]['PM'])) {
                        $sheet->mergeCellsByColumnAndRow($col, $row, $col - 1 + (count($session_rings[$show_days[0]->show_day_id]['PM'])), $row);
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                        $sheet->setCellValueByColumnAndRow($col, $row, strtoupper(date('l', strtotime($show_days[0]->show_day_date))) . ' PM');
                    }

                    $col = $col + count($session_rings[$show_days[0]->show_day_id]['PM']) + 4;

                    if (count($session_rings[$show_days[1]->show_day_id]['AM'])) {
                        $sheet->mergeCellsByColumnAndRow($col, $row, $col - 1 + (count($session_rings[$show_days[1]->show_day_id]['AM'])), $row);
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                        $sheet->setCellValueByColumnAndRow($col, $row, strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' AM');
                    }

                    $col += count($session_rings[$show_days[0]->show_day_id]['AM']);

                    if (count($session_rings[$show_days[1]->show_day_id]['PM'])) {
                        $sheet->mergeCellsByColumnAndRow($col, $row, $col - 1 + (count($session_rings[$show_days[1]->show_day_id]['PM'])), $row);
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                        $sheet->setCellValueByColumnAndRow($col, $row, strtoupper(date('l', strtotime($show_days[1]->show_day_date))) . ' PM');
                    }

                    $row++;

                    $col = 0;
                    foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValueByColumnAndRow($col++, $row, $first_show_day->judge_abbreviation);
                    }

                    $col = $col + 4;

                    foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->setCellValueByColumnAndRow($col++, $row, $second_show_day->judge_abbreviation);
                    }

                    $row++;
                }

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                $sheet->mergeCellsByColumnAndRow($col, $row, $col + ($first_day_rings + $second_day_rings + 3), $row);
                $row++;
            }

            $previous_class = $entry->show_class;
            $previous_breed = '';
            $previous_division = '';
            $previous_color = '';
        }

        if ($previous_catalog_number != $entry->catalog_number) {

            if ($previous_breed != $entry->breed_name) {
                $previous_breed_entries = 1;
                $previous_division_entries = 1;
                $previous_division = '';
                $previous_color = '';
                $previous_breed = $entry->breed_name;

                $col = $first_day_rings + 1;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                $sheet->mergeCellsByColumnAndRow($col, $row, $col, $row);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $breed_title_color);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->breed_name));
            }
            else
                $previous_breed_entries++;

            if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
                $previous_division_entries = 1;
                $previous_color = '';
                $previous_division = $entry->catalog_division;

                $col = $first_day_rings + 1;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                $sheet->mergeCellsByColumnAndRow($col, $row, $col, $row);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setItalic(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(11);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $division_title_color);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->catalog_division));
            }
            else
                $previous_division_entries++;

            if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
                $previous_color = $entry->color_name;

                $col = $first_day_rings + 1;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                $sheet->mergeCellsByColumnAndRow($col, $row, $col, $row);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setUnderline(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->color_name));
            }

            $previous_catalog_number = $entry->catalog_number;

            $isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

            $reg_number = JText::_('PENDING');
            if (trim($entry->catalog_registration_number) != '')
                $reg_number = $entry->catalog_registration_number;

            $cat_block = $entry->catalog_cat_name . "\n" . $reg_number . '   ' . JText::_('COM_TOES_CATALOG_BORN') . ' ' . $entry->catalog_birthdate;

            if ($isNotHHP) {
                $cat_block .= "\n" . strtoupper($entry->catalog_sire);
                $cat_block .= "\n" . strtoupper($entry->catalog_dam);
            }

            $cat_block .= ($isNotHHP && $entry->catalog_breeder ? "\n" . strtoupper($entry->catalog_breeder) : '')
                    . ($entry->catalog_owner ? "\n" . strtoupper($entry->catalog_owner) : '')
                    . ($entry->catalog_lessee ? "\n" . strtoupper($entry->catalog_lessee) : '')
                    . ($entry->catalog_agent ? "\n" . strtoupper($entry->catalog_agent) : '');

            $col = 0;
            foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
                if ($entry->show_class != 'Ex Only') {
                    if (($first_show_day->ring_timing == 1 && isset($showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]) && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($first_show_day->ring_timing == 2 && isset($showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]) && $showday_entries[$show_days[0]->show_day_id][$entry->catalog_number]->entry_participates_PM))
                        $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
                }
                $col++;
            }


            $sheet->getRowDimension($row)->setRowHeight(80);

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_number);

            $sheet->mergeCellsByColumnAndRow($col, $row, $col, $row + 1);
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col, $row, $cat_block);
            $sheet->getStyleByColumnAndRow($col++, $row)->getAlignment()->setWrapText(true);

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_age_and_gender);

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_number);

            foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
                if ($entry->show_class != 'Ex Only') {
                    if (($second_show_day->ring_timing == 1 && isset($showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]) && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($second_show_day->ring_timing == 2 && isset($showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]) && $showday_entries[$show_days[1]->show_day_id][$entry->catalog_number]->entry_participates_PM))
                        $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
                }
                $col++;
            }

            $row++;
            $col = $first_day_rings;

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_region);

            $col++;

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_region);
            $row++;
        }

        $next = $cur + 1;
        if (in_array($entry->show_class, $skip_division_best)) {
            $previous_division_entries = 0;
        } else if ($next == count($entries) || ( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
            $col = $first_day_rings + 1;
            for ($i = 1; $i <= $previous_division_entries; $i++) {

                if (!in_array($entry->show_class, $skip_breed_best) && (($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name))) {
                    $col = 0;
                    foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                    $col++;
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bob_color);
                    $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i));
                    $col++;
                    $col++;
                    foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                    $row++;
                } else {
                    $col = 0;
                    foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                    $col++;
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setItalic(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(11);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bod_color);
                    $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i));
                    $col++;
                    $col++;
                    foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                    $row++;
                }

                if ($i == 3)
                    break;
            }
            $row++;
        }

        if (in_array($entry->show_class, $skip_breed_best)) {
            $previous_breed_entries = 0;
        } else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {
            $col = $first_day_rings + 1;
            for ($i = 1; $i <= $previous_breed_entries; $i++) {

                $col = 0;
                foreach ($show_day_rings[$show_days[0]->show_day_id] as $first_show_day) {
                    $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                }
                $col++;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bob_color);
                $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i));
                $col++;
                $col++;
                foreach ($show_day_rings[$show_days[1]->show_day_id] as $second_show_day) {
                    $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                }
                $row++;
                if ($i == 3)
                    break;
            }
            $row++;
        }

        $cur++;
    }
} else {

    $col = 0;
    $row = 1;

    foreach ($entries as $entry) {
        if ($previous_class != $entry->show_class) {

            $show_class = str_replace('LH', '', $entry->show_class);
            $show_class = str_replace('SH', '', $show_class);
            $show_class = trim($show_class);

            if (!in_array($show_class, $available_classes))
                $available_classes[] = $show_class;

            if ($previous_class == '') {
                $sheet = $excel->getActiveSheet();
                $sheet->setTitle($entry->show_class);
            } else {
                $new_sheet = new PHPExcel_Worksheet($excel, $entry->show_class);
                $sheet = $excel->addSheet($new_sheet);
            }
            $sheet->getColumnDimension('A')->setWidth(50);

            $col = 3;
            foreach ($show_days as $show_day) {
                foreach ($show_day_rings[$show_day->show_day_id] as $day) {
                    $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($col++))->setWidth(5);
                }
            }

            $total_rings = 0;
            foreach ($show_days as $show_day) {
                $day_rings[$show_day->show_day_id] = count($show_day_rings[$show_day->show_day_id]);
                $total_rings += count($show_day_rings[$show_day->show_day_id]);
            }

            $col = 0;
            $row = 1;

            $sheet->mergeCellsByColumnAndRow($col, $row, $col + ($total_rings + 2), $row);
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(18);
            $sheet->getStyleByColumnAndRow($col, $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $sheet->getStyleByColumnAndRow($col, $row)->getFill()->getStartColor()->setARGB('FFCCCCCC');
            $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->show_class));

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->mergeCellsByColumnAndRow($col, $row, $col + ($total_rings + 2), $row);
            $row++;

            if ($entry->show_class != 'Ex Only') {

                if (!$is_alternative) {
                    $col = 3;
                    foreach ($show_days as $show_day) {
                        if(!$day_rings[$show_day->show_day_id]) {
                            continue;
                        }
                        $sheet->mergeCellsByColumnAndRow($col, $row, ($col + $day_rings[$show_day->show_day_id] - 1), $row);
                        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                        $sheet->setCellValueByColumnAndRow($col, $row, (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))));

                        $col = $col + $day_rings[$show_day->show_day_id];
                    }

                    $row++;

                    $col = 3;
                    foreach ($show_days as $show_day) {
                        foreach ($show_day_rings[$show_day->show_day_id] as $day) {
                            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                            $sheet->setCellValueByColumnAndRow($col++, $row, $day->judge_abbreviation);
                        }
                    }

                    $row++;
                } else {
                    $col = 3;
                    foreach ($show_days as $show_day) {
                        if(!$day_rings[$show_day->show_day_id]) {
                            continue;
                        }

                        if (count($session_rings[$show_day->show_day_id]['AM'])) {
                            $sheet->mergeCellsByColumnAndRow($col, $row, ($col + count($session_rings[$show_day->show_day_id]['AM']) - 1), $row);
                            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                            $sheet->setCellValueByColumnAndRow($col, $row, (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . ' AM');
                        }

                        $col = $col + count($session_rings[$show_day->show_day_id]['AM']);

                        if (count($session_rings[$show_day->show_day_id]['PM'])) {
                            $sheet->mergeCellsByColumnAndRow($col, $row, ($col + count($session_rings[$show_day->show_day_id]['PM']) - 1), $row);
                            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                            $sheet->setCellValueByColumnAndRow($col, $row, (($is_continuous) ? '' : strtoupper(date('l', strtotime($show_day->show_day_date)))) . ' PM');
                        }

                        $col = $col + count($session_rings[$show_day->show_day_id]['PM']);
                    }

                    $row++;

                    $col = 3;
                    foreach ($show_days as $show_day) {
                        foreach ($show_day_rings[$show_day->show_day_id] as $day) {
                            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                            $sheet->setCellValueByColumnAndRow($col++, $row, $day->judge_abbreviation);
                        }
                    }

                    $row++;
                }

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                $sheet->mergeCellsByColumnAndRow($col, $row, $col + $total_rings + 2, $row);
                $row++;
            }

            $previous_class = $entry->show_class;
            $previous_breed = '';
            $previous_division = '';
            $previous_color = '';
        }

        if ($previous_catalog_number != $entry->catalog_number) {
            if ($previous_breed != $entry->breed_name) {
                $previous_breed_entries = 1;
                $previous_division_entries = 1;
                $previous_division = '';
                $previous_color = '';
                $previous_breed = $entry->breed_name;

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $breed_title_color);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->breed_name));
            }
            else
                $previous_breed_entries++;

            if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
                $previous_division_entries = 1;
                $previous_color = '';
                $previous_division = $entry->catalog_division;

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setItalic(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(11);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $division_title_color);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->catalog_division));
            }
            else
                $previous_division_entries++;

            if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
                $previous_color = $entry->color_name;

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setUnderline(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->color_name));
            }

            $previous_catalog_number = $entry->catalog_number;

            $isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

            $reg_number = JText::_('PENDING');
            if (trim($entry->catalog_registration_number) != '')
                $reg_number = $entry->catalog_registration_number;

            $cat_block = $entry->catalog_cat_name . "\n" . $reg_number . '   ' . JText::_('COM_TOES_CATALOG_BORN') . ' ' . $entry->catalog_birthdate;

            if ($isNotHHP) {
                $cat_block .= "\n" . strtoupper($entry->catalog_sire);
                $cat_block .= "\n" . strtoupper($entry->catalog_dam);
            }

            $cat_block .= ($entry->catalog_breeder ? "\n" . strtoupper($entry->catalog_breeder) : '')
                    . ($entry->catalog_owner ? "\n" . strtoupper($entry->catalog_owner) : '')
                    . ($entry->catalog_lessee ? "\n" . strtoupper($entry->catalog_lessee) : '')
                    . ($entry->catalog_agent ? "\n" . strtoupper($entry->catalog_agent) : '');


            $col = 0;
            $sheet->getRowDimension($row)->setRowHeight(80);

            $sheet->mergeCellsByColumnAndRow($col, $row, $col, $row + 1);
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col, $row, $cat_block);
            $sheet->getStyleByColumnAndRow($col++, $row)->getAlignment()->setWrapText(true);

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_age_and_gender);

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_number);

            $col = 3;
            foreach ($show_days as $show_day) {
                foreach ($show_day_rings[$show_day->show_day_id] as $day) {
                    if ($entry->show_class != 'Ex Only') {
                        if (($day->ring_timing == 1 && isset($showday_entries[$show_day->show_day_id][$entry->catalog_number]) && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_AM) || ($day->ring_timing == 2 && isset($showday_entries[$show_day->show_day_id][$entry->catalog_number]) && $showday_entries[$show_day->show_day_id][$entry->catalog_number]->entry_participates_PM))
                            $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
                    }
                    $col++;
                }
            }

            $row++;
            $col = 1;
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_region);

            $row++;
        }

        $next = $cur + 1;
        if (in_array($entry->show_class, $skip_division_best)) {
            $previous_division_entries = 0;
        } else if ($next == count($entries) || ( isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name || $entry->catalog_division != $entries[$next]->catalog_division))) {
            for ($i = 1; $i <= $previous_division_entries; $i++) {
                $col = 0;
                if (!in_array($entry->show_class, $skip_breed_best) && (($next == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $entries[$next]->breed_name))) {
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bob_color);
                    $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i));

                    $col = 3;
                    foreach ($show_days as $show_day) {
                        foreach ($show_day_rings[$show_day->show_day_id] as $day) {
                            $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                        }
                    }

                    $row++;
                } else {
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setItalic(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(11);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bod_color);
                    $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i));

                    $col = 3;
                    foreach ($show_days as $show_day) {
                        foreach ($show_day_rings[$show_day->show_day_id] as $day) {
                            $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                        }
                    }

                    $row++;
                }
                if ($i == 3)
                    break;
            }
            $row++;
        }

        if (in_array($entry->show_class, $skip_breed_best)) {
            $previous_breed_entries = 0;
        } else if ($previous_breed_entries > $previous_division_entries && ($next == count($entries) || (isset($entries[$next]) && ($entry->show_class != $entries[$next]->show_class || $entry->breed_name != $entries[$next]->breed_name)))) {
            for ($i = 1; $i <= $previous_breed_entries; $i++) {
                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bob_color);
                $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i));

                $col = 3;
                foreach ($show_days as $show_day) {
                    foreach ($show_day_rings[$show_day->show_day_id] as $day) {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                }

                $row++;
                if ($i == 3)
                    break;
            }
            $row++;
        }
        $cur++;
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
foreach ($temp_rings as $ring) {
    $congress_rings[strtolower($ring->ring_name)][] = $ring;
}

$final_entries = array();
$temp_entries = array();
foreach ($entries as $entry) {
    $temp_entries[$entry->catalog_number] = $entry;
}

$ctlg_numbers = array();
foreach ($congress_catalog as $entry) {
    $temp_entries[$entry->catalog_number]->show_class = $entry->show_class;
    if (!isset($ctlg_numbers[strtolower($entry->ring_name)]))
        $ctlg_numbers[strtolower($entry->ring_name)] = array();
    if (!in_array($entry->catalog_number, $ctlg_numbers[strtolower($entry->ring_name)])) {
        $ctlg_numbers[strtolower($entry->ring_name)][] = $entry->catalog_number;
        $final_entries[strtolower($entry->ring_name)][] = $temp_entries[$entry->catalog_number];
    }
}

unset($temp_entries);
$temp_entries = array();
foreach ($final_entries as $ring_number => $congress_entries) {
    $temp_entries[$ring_number] = TOESHelper::aasort($congress_entries, 'catalog_number');
}
$final_entries = $temp_entries;

foreach ($final_entries as $ring_number => $congress_entries) {
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

    $new_sheet = new PHPExcel_Worksheet($excel, str_replace('/','-',$judge_name));
    $sheet = $excel->addSheet($new_sheet);

    $sheet->getColumnDimension('A')->setWidth(50);

    $row = 1;
    $col = 0;
    $sheet->mergeCellsByColumnAndRow($col, $row, $col + (count($rings) + 2), $row);
    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(18);
    $sheet->getStyleByColumnAndRow($col, $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyleByColumnAndRow($col, $row)->getFill()->getStartColor()->setARGB('FFCCCCCC');
    $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($judge_name));

    //$sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
    //$sheet->mergeCellsByColumnAndRow($col, $row, $col+(count($rings)+2), $row);
    //$sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->show_class));
    //$sheet->setCellValueByColumnAndRow($col, $row++, '');

    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
    $sheet->mergeCellsByColumnAndRow($col, $row, $col + (count($rings) + 2), $row);
    $row++;

    $col = 3;
    foreach ($rings as $ring) {
        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
        $sheet->setCellValueByColumnAndRow($col++, $row, $ring->judge_abbreviation);
    }
    $row++;

    foreach ($congress_entries as $entry) {

        /* if ($previous_class != $entry->show_class) {
          $col = 0;
          $sheet->mergeCellsByColumnAndRow($col, $row, $col+(count($rings)+2), $row);
          $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
          $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($judge_name));

          $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
          $sheet->mergeCellsByColumnAndRow($col, $row, $col+(count($rings)+2), $row);
          //$sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->show_class));
          $sheet->setCellValueByColumnAndRow($col, $row++, '');

          $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
          $sheet->mergeCellsByColumnAndRow($col, $row, $col+(count($rings)+2), $row);
          $row++;

          $col = 3;
          foreach ($rings as $ring) {
          $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
          $sheet->setCellValueByColumnAndRow($col++, $row, $ring->judge_abbreviation );
          }
          $row++;

          $previous_class = $entry->show_class;
          $previous_breed = '';
          $previous_division = '';
          $previous_color = '';
          } */

        if ($previous_catalog_number != $entry->catalog_number) {
            if ($previous_breed != $entry->breed_name) {
                $previous_breed_entries = 1;
                $previous_division_entries = 1;
                $previous_division = '';
                $previous_color = '';
                $previous_breed = $entry->breed_name;

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $breed_title_color);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->breed_name));
            }
            else
                $previous_breed_entries++;

            if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division) {
                $previous_division_entries = 1;
                $previous_color = '';
                $previous_division = $entry->catalog_division;

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setItalic(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(11);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $division_title_color);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->catalog_division));
            }
            else
                $previous_division_entries++;

            if ($previous_breed != $entry->breed_name || $previous_division != $entry->catalog_division || $previous_color != $entry->color_name) {
                $previous_color = $entry->color_name;

                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setUnderline(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->setCellValueByColumnAndRow($col, $row++, strtoupper($entry->color_name));
            }

            $previous_catalog_number = $entry->catalog_number;

            $isNotHHP = ($entry->show_class != 'SH HHP' && $entry->show_class != 'LH HHP' && $entry->show_class != 'SH HHP Kitten' && $entry->show_class != 'LH HHP Kitten');

            $reg_number = JText::_('PENDING');
            if (trim($entry->catalog_registration_number) != '')
                $reg_number = $entry->catalog_registration_number;

            $cat_block = $entry->catalog_cat_name . "\n" . $reg_number . '   ' . JText::_('COM_TOES_CATALOG_BORN') . ' ' . $entry->catalog_birthdate;

            if ($isNotHHP) {
                $cat_block .= "\n" . strtoupper($entry->catalog_sire);
                $cat_block .= "\n" . strtoupper($entry->catalog_dam);
            }

            $cat_block .= ($entry->catalog_breeder ? "\n" . strtoupper($entry->catalog_breeder) : '')
                    . ($entry->catalog_owner ? "\n" . strtoupper($entry->catalog_owner) : '')
                    . ($entry->catalog_lessee ? "\n" . strtoupper($entry->catalog_lessee) : '')
                    . ($entry->catalog_agent ? "\n" . strtoupper($entry->catalog_agent) : '');

            $col = 0;
            $sheet->getRowDimension($row)->setRowHeight(80);

            $sheet->mergeCellsByColumnAndRow($col, $row, $col, $row + 1);
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col, $row, $cat_block);
            $sheet->getStyleByColumnAndRow($col++, $row)->getAlignment()->setWrapText(true);

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_age_and_gender);

            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_number);

            foreach ($rings as $ring) {
                $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
            }

            $row++;
            $col = 1;
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
            $sheet->setCellValueByColumnAndRow($col++, $row, $entry->catalog_region);

            $row++;
        }

        $next = $cur + 1;
        if ($entry->show_class != 'Ex Only' && in_array($entry->show_class, $skip_division_best)) {
            $previous_division_entries = 0;
        } else if ($next == count($congress_entries) || ( isset($congress_entries[$next]) && ($entry->show_class != $congress_entries[$next]->show_class || $entry->breed_name != $congress_entries[$next]->breed_name || $entry->catalog_division != $congress_entries[$next]->catalog_division))) {
            for ($i = 1; $i <= $previous_division_entries; $i++) {
                $col = 0;

                if (!in_array($entry->show_class, $skip_breed_best) && (($next == count($congress_entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && $entry->breed_name != $congress_entries[$next]->breed_name))) {
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bob_color);
                    $sheet->setCellValueByColumnAndRow($col, $row, JText::_('COM_TOES_BEST_BREED_DIVISION_ENTRY_' . $i));

                    $col = 3;
                    foreach ($rings as $ring) {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                    $row++;
                } else {
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setItalic(true);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(11);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bod_color);
                    $sheet->setCellValueByColumnAndRow($col, $row, JText::_('COM_TOES_BEST_DIVISION_ENTRY_' . $i));

                    $col = 3;
                    foreach ($rings as $ring) {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                    $row++;
                }

                if ($i == 3)
                    break;
            }
            $row++;
        }

        if ($entry->show_class != 'Ex Only' && in_array($entry->show_class, $skip_breed_best)) {
            $previous_breed_entries = 0;
        } else if ($previous_breed_entries > $previous_division_entries && ($next == count($congress_entries) || (isset($congress_entries[$next]) && ($entry->show_class != $congress_entries[$next]->show_class || $entry->breed_name != $congress_entries[$next]->breed_name)))) {
            for ($i = 1; $i <= $previous_breed_entries; $i++) {
                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('left');
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setVertical('top');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(12);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->getColor()->setARGB('FF' . $bob_color);
                $sheet->setCellValueByColumnAndRow($col, $row, JText::_('COM_TOES_BEST_BREED_ENTRY_' . $i));

                $col = 3;
                foreach ($rings as $ring) {
                    $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                }
                $row++;

                if ($i == 3)
                    break;
            }
            $row++;
        }
        $cur++;
    }
}

//Final Sheets
$new_breed_classes = array(
    'PNB',
    'ANB',
    'NT',
    'Ex Only',
    'For Sale'
);

$temp_show_days = array();
foreach ($show_days as $show_day) {
    $temp_show_days[$show_day->show_day_id] = $show_day;
}

$max_rings_on_day = 0;
foreach ($show_day_rings as $show_day => $rings) {
    $cnt = 0;
    foreach ($rings as $ring) {
        if ($ring->format == 'Specialty')
            $cnt += 2;
        else
            $cnt++;
    }
    if ($max_rings_on_day < $cnt)
        $max_rings_on_day = $cnt;
}

foreach ($show_day_rings as $show_day => $rings) {

    if (!$is_continuous) {
        $new_sheet = new PHPExcel_Worksheet($excel, JText::_('FINALS') . ' ' . date('l', strtotime($temp_show_days[$show_day]->show_day_date)));
        $sheet = $excel->addSheet($new_sheet);
    } else {
        $new_sheet = new PHPExcel_Worksheet($excel, JText::_('FINALS'));
        $sheet = $excel->addSheet($new_sheet);
    }

    $row = 1;
    $col = 0;
    $sheet->mergeCellsByColumnAndRow($col, $row, $max_rings_on_day, $row);
    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setSize(18);
    $sheet->getStyleByColumnAndRow($col, $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyleByColumnAndRow($col, $row)->getFill()->getStartColor()->setARGB('FFCCCCCC');

    if (!$is_continuous)
        $sheet->setCellValueByColumnAndRow($col, $row++, JText::_('FINALS') . ' ' . date('l', strtotime($temp_show_days[$show_day]->show_day_date)));
    else
        $sheet->setCellValueByColumnAndRow($col, $row++, JText::_('FINALS'));

    $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
    $sheet->mergeCellsByColumnAndRow($col, $row, $max_rings_on_day, $row);
    $row++;

    foreach ($available_classes as $class) {
        if ($class == 'Ex Only' || $class == 'For Sale')
            continue;

        $col = 0;
        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
        $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
        $sheet->setCellValueByColumnAndRow($col++, $row, $class);

        foreach ($rings as $ring) {
            if ($ring->format == 'Specialty') {
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
                $sheet->setCellValueByColumnAndRow($col++, $row, $ring->judge_abbreviation . ' (LH)');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
                $sheet->setCellValueByColumnAndRow($col++, $row, $ring->judge_abbreviation . ' (SH)');
            } else {
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
                $sheet->setCellValueByColumnAndRow($col++, $row, $ring->judge_abbreviation . ' (AB)');
            }
        }

        $row++;

        if (!in_array($class, $new_breed_classes)) {
            for ($j = 1; $j <= 10; $j++) {
                $col = 0;
                $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
                $sheet->setCellValueByColumnAndRow($col++, $row, $j);

                foreach ($rings as $ring) {
                    if ($ring->format == 'Specialty') {
                        $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                    }
                    $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                }
                $row++;
            }
            $col = 0;
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COUNT'));

            foreach ($rings as $ring) {
                if ($ring->format == 'Specialty') {
                    $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                }
                $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
            }
            $row++;
        } else {
            $col = 0;
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($col++, $row, '1');
            foreach ($rings as $ring) {
                if ($ring->format == 'Specialty') {
                    $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                }
                $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
            }
            $row++;

            $col = 0;
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COUNT'));

            foreach ($rings as $ring) {
                if ($ring->format == 'Specialty') {
                    $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
                }
                $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
            }
            $row++;
        }

        $sheet->getStyleByColumnAndRow(0, $row)->getAlignment()->setHorizontal('center');
        $sheet->mergeCellsByColumnAndRow(0, $row, $max_rings_on_day, $row);
        $row++;
    }

    $query = "SELECT `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`, `r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation` 
            FROM `#__toes_ring` AS `r`
            LEFT JOIN `#__toes_judge` AS `j` ON (`j`.`judge_id` = `r`.`ring_judge`) 
            WHERE (`r`.`ring_format` = 3) AND `r`.`ring_show` = {$show_id} AND `r`.`ring_show_day` = {$show_day}
            ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`";
    $db->setQuery($query);
    $congress_rings = $db->loadObjectList();

    if ($congress_rings) {

        $col = 0;
        $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('CONGRESS'));

        foreach ($congress_rings as $ring) {
            $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($col++, $row, $ring->judge_abbreviation . ' (' . $ring->ring_name . ')');
        }
        $row++;

        for ($j = 1; $j <= 10; $j++) {
            $col = 0;
            $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
            $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
            $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow($col++, $row, $j);
            foreach ($congress_rings as $ring) {
                $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
            }
            $row++;
        }

        $col = 0;
        $sheet->getStyleByColumnAndRow($col, $row)->getAlignment()->setHorizontal('center');
        $sheet->getStyleByColumnAndRow($col, $row)->applyFromArray($BorderStyleArray);
        $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
        $sheet->setCellValueByColumnAndRow($col++, $row, JText::_('COUNT'));
        foreach ($congress_rings as $ring) {
            $sheet->getStyleByColumnAndRow($col++, $row)->applyFromArray($BorderStyleArray);
        }
        $row++;
    }
}

// ---------------------------------------------------------
/*
jimport('joomla.filesystem.folder');
if (!file_exists(TOES_PDF_PATH . DS . $show_id))
    JFolder::create(TOES_PDF_PATH . DS . $show_id, 0777);
else
    chmod(TOES_PDF_PATH . DS . $show_id, 0777);

if (file_exists(TOES_PDF_PATH . DS . $show_id . DS . 'mastercatalog.xls'))
    unlink(TOES_PDF_PATH . DS . $show_id . DS . 'mastercatalog.xls');
*/
//Close and output PDF document
$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
//$objWriter->save(TOES_PDF_PATH . DS . $show_id . DS . 'mastercatalog.xls');
header('Content-type: application/vnd.ms-excel');

// It will be called file.xls
header('Content-Disposition: attachment; filename="'.$show_id.'_mastercatalog.xls"');
$objWriter->save('php://output');
//============================================================+
// END OF FILE                                                
//============================================================+
