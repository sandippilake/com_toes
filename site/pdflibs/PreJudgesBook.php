<?php
set_time_limit(3600);

$params = JComponentHelper::getParams('com_toes');
$useLF = $params->get('use_lf_instead_ff_for_printing',0);

$selected_values = $app->input->getVar('judge_id', '');

$start_page = $app->input->getInt('start_page',1);
$end_page = $app->input->getInt('end_page',1000000);

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

$selected_judges = array();
if($selected_values)
{
    $selected_values = explode(',', $selected_values);
    foreach ($selected_values as $judge_id) {
        $selected_judges[] = $judge_id;
    }
}

$db = JFactory::getDBO();

$whr = array();
$whr[] = '`e`.`entry_show` = '.$show_id;

$query = TOESQueryHelper::getJudgesBookData($whr);

$db->setQuery($query);
$entries = $db->loadObjectList();

$query = "SELECT `show_day_id`, `show_day_date` FROM `#__toes_show_day` WHERE `show_day_show` = {$show_id} ORDER BY `show_day_date`";
$db->setQuery($query);
$temp_show_days = $db->loadObjectList();

foreach($temp_show_days as $show_day)
{
    $show_days[$show_day->show_day_id] = $show_day;
}

$where = array("`r`.`ring_show` = {$show_id}");
$query = TOESQueryHelper::getCatlogRingInfoQuery($where);
$db->setQuery($query);
$temp_rings = $db->loadObjectList();

foreach ($temp_rings as $ring)
{
    $rings[$ring->ring_show_day.$ring->ring_timing.$ring->ring_number] = $ring;
}

foreach($show_days as $show_day)
{
    $ring = new stdClass();
	$ring->ring_id = 'AB';
    $ring->ring_show = $show_id;
    $ring->ring_show_day = $show_day->show_day_id;
    $ring->ring_timing = 0;
    $ring->ring_number = $show_day->show_day_id.'AB';
    $ring->ring_format = 1;
    $ring->ring_name = '';
    $ring->ring_judge = 'AB';
    $ring->judge_abbreviation = '';
    
    $rings[$ring->ring_show_day.$ring->ring_timing.$ring->ring_number] = $ring;

    $ring = new stdClass();
	$ring->ring_id = 'SP';
    $ring->ring_show = $show_id;
    $ring->ring_show_day = $show_day->show_day_id;
    $ring->ring_timing = 0;
    $ring->ring_number = $show_day->show_day_id.'SP';
    $ring->ring_format = 2;
    $ring->ring_name = '';
    $ring->ring_judge = 'SP';
    $ring->judge_abbreviation = '';
    
    $rings[$ring->ring_show_day.$ring->ring_timing.$ring->ring_number] = $ring;
}

$judges = array();
foreach ($rings as $ring)
{
    if(is_numeric($ring->ring_judge))
        $judges[$ring->ring_judge] = TOESHelper::getJudgeInfo($ring->ring_judge);
}

$show = TOESHelper::getShowDetails($show_id);

$is_continuous = ($show->show_format=='Continuous')?true:false;
$is_alternative = ($show->show_format=='Alternative')?true:false;

// ---------------------------------------------------------

$previous_class = '';
$previous_breed = '';
$previous_division = '';
$previous_color = '';
$previous_catalog_number = '';

$previous_breed_entries = 1;
$previous_division_entries = 1;

$show_day_entries = array();

foreach ($entries as $entry)
{
    $show_day_entries[$entry->show_day][] = $entry;
}

$final_entries = array();
foreach ($rings as $ring)
{
    foreach ($show_day_entries as $day=>$show_day)
    {
        if(isset($selected_judges) && $selected_judges)
        {
			if(in_array($ring->ring_id, $selected_judges) && $ring->ring_show_day == $day)
			{
				$final_entries[$ring->ring_show_day.$ring->ring_timing.$ring->ring_number] = $show_day;
			}
        }
        else
        {
            if(is_numeric($ring->ring_id))
                $final_entries[$ring->ring_show_day.$ring->ring_timing.$ring->ring_number] = $show_day;
        }
    }
}

$curLine = 0;
$page_number = 0;

jimport('joomla.filesystem.folder');
if(!file_exists(TOES_PDF_PATH.DS.$show_id))
    JFolder::create (TOES_PDF_PATH.DS.$show_id, 0777);
else
	chmod (TOES_PDF_PATH.DS.$show_id, 0777);

$handle = fopen(TOES_PDF_PATH.DS.$show->show_id.DS.'prejudgesbook.txt', 'w');

fwrite($handle, $initialize_string);
foreach($final_entries as $ring_number=>$ring_entries)
{
    if(is_numeric($rings[$ring_number]->ring_number))
        $ringNum = $rings[$ring_number]->ring_number;
    else
        $ringNum = '';
    
    $previous_class = '';
    $previous_breed = '';
    $previous_division = '';
    $previous_color = '';
    $previous_catalog_number = '';
    
    $previous_breed_entries = 1;
    $previous_division_entries = 1;

    $cur = 0;
	
	$page_number++;
	$page_number_to_print = 1;
	
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
        
    foreach($entries as $entry)
    {
        if($previous_class != $entry->show_class)
        {
            if($previous_class != '')
            {
                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    if($useLF)
                    {
                        if($number_of_lines_per_page > $curLine)
                        {
                            for($l = $curLine; $l < $number_of_lines_per_page; $l++)
                                fwrite($handle, $begin_of_line.$end_of_line);
                        }
                    }
                    else
                        fwrite($handle, $form_feed);
                }
                $page_number++;
				$page_number_to_print++;
            }

            if( $start_page <= $page_number && $page_number <= $end_page)
            {
                $curLine = 0;
                fwrite($handle, $begin_of_line.$end_of_line);
                $curLine++;

                $ring_num_text = JText::_('COM_TOES_RING').$ringNum;
                $show_class_text = strtoupper($entry->show_class);
                $page_num_text = 'P'.$page_number_to_print;

                $length = strlen($ring_num_text.$show_class_text.$page_num_text);
                $diff = $number_of_characters_per_line - $length;
                $spaces = '';
                for($k=0;$k<($diff/2);$k++)
                    $spaces .= ' ';

                $text = $begin_of_line.$ring_num_text.$spaces.$show_class_text.$spaces.$page_num_text.$end_of_line;
                fwrite($handle, $text);
                $curLine++;

				$club_name = strtoupper($show->club_abbreviation).'-'.$show->Show_location;
				$club_name = substr($club_name, 0, $club_name_max_characters);
                $text = $leftmarginforlocation.$club_name;
                $date = date('M-d',strtotime($show_days[$entry->show_day]->show_day_date));
				if($is_alternative)
				{
					if($rings[$ring_number]->ring_timing == 1)
						$date .= ' AM';
					else
						$date .= ' PM';
				}
                $length = strlen($text.$date);
                $diff = $number_of_characters_per_line - $length;
                $spaces = '';
                for($k=0;$k<$diff;$k++)
                    $spaces .= ' ';
                $text = $begin_of_line.$text.$spaces.$date.$end_of_line;

                fwrite($handle, $text);
                $curLine++;

                fwrite($handle, $begin_of_line.$end_of_line);
                $curLine++;
            }
            else
                $curLine = 4;

            $previous_class = $entry->show_class;
            $previous_breed = '';
            $previous_division = '';
            $previous_color = '';
        }
        
        if($previous_catalog_number != $entry->catalog_number)
        {
            //if($previous_color != $entry->judges_book_color)
        	if ($previous_breed != $entry->breed_abbreviation || $previous_division != $entry->catalog_division || $previous_color != $entry->judges_book_color)
            {
            	$lines_to_print = 2;
				if($show->show_print_division_title_in_judges_books && $previous_division != $entry->catalog_division)
					$lines_to_print++;
            	
                if($curLine+$lines_to_print > $number_of_printable_lines_per_page)
                {
                    if( $start_page <= $page_number && $page_number <= $end_page)
                    {
                        if($useLF)
                        {
                            if($number_of_lines_per_page > $curLine)
                            {
                                for($l = $curLine; $l < $number_of_lines_per_page; $l++)
                                    fwrite($handle, $begin_of_line.$end_of_line);
                            }
                        }
                        else
                            fwrite($handle, $form_feed);
                    }
                    $page_number++;
					$page_number_to_print++;
                    
                    if( $start_page <= $page_number && $page_number <= $end_page)
                    {
                        $curLine = 0;
                        fwrite($handle, $begin_of_line.$end_of_line);
                        $curLine++;

                        $ring_num_text = JText::_('COM_TOES_RING').$ringNum;
                        $show_class_text = strtoupper($entry->show_class);
                        $page_num_text = 'P'.$page_number_to_print;

                        $length = strlen($ring_num_text.$show_class_text.$page_num_text);
                        $diff = $number_of_characters_per_line - $length;
                        $spaces = '';
                        for($k=0;$k<($diff/2);$k++)
                            $spaces .= ' ';

                        $text = $begin_of_line.$ring_num_text.$spaces.$show_class_text.$spaces.$page_num_text.$end_of_line;
                        fwrite($handle, $text);
                        $curLine++;

						$club_name = strtoupper($show->club_abbreviation).'-'.$show->Show_location;
						$club_name = substr($club_name, 0, $club_name_max_characters);
						$text = $leftmarginforlocation.$club_name;
                        $date = date('M-d',strtotime($show_days[$entry->show_day]->show_day_date));
						if($is_alternative)
						{
							if($rings[$ring_number]->ring_timing == 1)
								$date .= ' AM';
							else
								$date .= ' PM';
						}
                        $length = strlen($text.$date);
                        $diff = $number_of_characters_per_line - $length;
                        $spaces = '';
                        for($k=0;$k<$diff;$k++)
                            $spaces .= ' ';
                        $text = $begin_of_line.$text.$spaces.$date.$end_of_line;

                        fwrite($handle, $text);
                        $curLine++;

                        fwrite($handle, $begin_of_line.$end_of_line);
                        $curLine++;
                    }
                    else
                        $curLine = 4;
                }
                
                if($show->show_print_division_title_in_judges_books && $previous_division != $entry->catalog_division)
                {
                	$division = TOESHelper::replaceJudgeBookDivisionNames($entry->catalog_division);
                	$text = strtoupper($division);
                	$length = strlen($text);
                	
                	if($length > $number_of_characters_per_line)
                	{
                		foreach($replace_texts as $item)
                		{
                			$text = str_replace($item['search'], $item['replace'], $text);
                			$length = strlen($text);
                			if($length <= $number_of_characters_per_line)
                			{
                				break;
                			}
                		}
                	}
                	
                	$diff = $number_of_characters_per_line - $length;
                	$spaces = '';
                	for($k=0;$k<($diff/2);$k++)
                		$spaces .= ' ';
                	
                	$text = $begin_of_line.$spaces.$text.$spaces.$end_of_line;
                	
                	if( $start_page <= $page_number && $page_number <= $end_page)
                		fwrite($handle, $text);
                	$curLine++;
				}
                
                $text = strtoupper($entry->judges_book_color);
                $length = strlen($text);
                
                if($length > $number_of_characters_per_line)
                {
                    foreach($replace_texts as $item)
                    {
                        $text = str_replace($item['search'], $item['replace'], $text);
                        $length = strlen($text);
                        if($length <= $number_of_characters_per_line)
                        {
                            break;
                        }
                    }
                }
                
                $diff = $number_of_characters_per_line - $length;
                $spaces = '';
                for($k=0;$k<($diff/2);$k++)
                    $spaces .= ' ';

                $text = $begin_of_line.$spaces.$text.$spaces.$end_of_line;
                
                if( $start_page <= $page_number && $page_number <= $end_page)
                    fwrite($handle, $text);
                $curLine++;
            }
            else
            {
                if($curLine+1 > $number_of_printable_lines_per_page)
                {
                    if( $start_page <= $page_number && $page_number <= $end_page)
                    {
                        if($useLF)
                        {
                            if($number_of_lines_per_page > $curLine)
                            {
                                for($l = $curLine; $l < $number_of_lines_per_page; $l++)
                                    fwrite($handle, $begin_of_line.$end_of_line);
                            }
                        }
                        else
                            fwrite($handle, $form_feed);
                    }
                    $page_number++;
					$page_number_to_print++;

                    if( $start_page <= $page_number && $page_number <= $end_page)
                    {
                        $curLine = 0;
                        fwrite($handle, $begin_of_line.$end_of_line);
                        $curLine++;

                        $ring_num_text = JText::_('COM_TOES_RING').$ringNum;
                        $show_class_text = strtoupper($entry->show_class);
                        $page_num_text = 'P'.$page_number_to_print;

                        $length = strlen($ring_num_text.$show_class_text.$page_num_text);
                        $diff = $number_of_characters_per_line - $length;
                        $spaces = '';
                        for($k=0;$k<($diff/2);$k++)
                            $spaces .= ' ';

                        $text = $begin_of_line.$ring_num_text.$spaces.$show_class_text.$spaces.$page_num_text.$end_of_line;
                        fwrite($handle, $text);
                        $curLine++;

						$club_name = strtoupper($show->club_abbreviation).'-'.$show->Show_location;
						$club_name = substr($club_name, 0, $club_name_max_characters);
						$text = $leftmarginforlocation.$club_name;
                        $date = date('M-d',strtotime($show_days[$entry->show_day]->show_day_date));
						if($is_alternative)
						{
							if($rings[$ring_number]->ring_timing == 1)
								$date .= ' AM';
							else
								$date .= ' PM';
						}
                        $length = strlen($text.$date);
                        $diff = $number_of_characters_per_line - $length;
                        $spaces = '';
                        for($k=0;$k<$diff;$k++)
                            $spaces .= ' ';
                        $text = $begin_of_line.$text.$spaces.$date.$end_of_line;

                        fwrite($handle, $text);
                        $curLine++;

                        fwrite($handle, $begin_of_line.$end_of_line);
                        $curLine++;
                    }
                    else
                        $curLine = 4;
                }
            }
            
            if( $start_page <= $page_number && $page_number <= $end_page)
            {
                fwrite($handle, $begin_of_line);
                $text = $entry->judges_book_age_and_gender;

                $length = strlen($text);
                if($length > $width_column_1)
                {
                    $text = $text;
                }
                else
                {
                    $diff = $width_column_1 - $length;
                    $spaces = '';
                    for($k=0;$k<$diff/2;$k++)
                        $spaces .= ' ';

                    $text = $spaces.$text.$spaces;
                }
                fwrite($handle, $text);

                $text = $entry->catalog_number;
                $length = strlen($text);
                if($length > $width_column_2)
                {
                    $text = $text;
                }
                else
                {
                    $diff = $width_column_2 - $length;
                    $spaces = '';
                    for($k=0;$k<$diff/2;$k++)
                        $spaces .= ' ';

                    $text = $spaces.$text.$spaces;
                }
                fwrite($handle, $text);

                $spaces = '';
                for($k=0;$k<$width_column_3;$k++)
                    $spaces .= ' ';

                fwrite($handle, $spaces.$end_of_line);
            }
            $curLine++;

            if($previous_breed != $entry->breed_abbreviation)
            {
                $previous_breed_entries = 1;
                $previous_division_entries = 1;
                $previous_division='';
                $previous_color = '';
                $previous_breed = $entry->breed_abbreviation;
            }
            else
                $previous_breed_entries++; 

            if($previous_division != $entry->catalog_division)
            {
                $previous_division_entries = 1;
                $previous_color = '';
                $previous_division = $entry->catalog_division;
            }
            else
                $previous_division_entries++;

            if($previous_color != $entry->judges_book_color)
                $previous_color = $entry->judges_book_color;

            $previous_catalog_number = $entry->catalog_number;
        }

        $cur++;

        if(in_array($entry->show_class,$skip_division_best))
        {
            $previous_division_entries = 0;
        }
        else if($cur == count($entries) ||
                ( isset($entries[$cur]) && ($entry->breed_abbreviation != $entries[$cur]->breed_abbreviation || $entry->catalog_division != $entries[$cur]->catalog_division)))
        {
        	if($show->show_print_extra_lines_for_bod_and_bob_in_judges_book)
        		$lines_to_print = ($previous_division_entries < 3)?$previous_division_entries:3;
        	else 
        		$lines_to_print = 3;        		
        	
            if(!in_array($entry->show_class, $skip_breed_best) && ($cur == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && ($entry->show_class != @$entries[$cur]->show_class || $entry->breed_abbreviation != @$entries[$cur]->breed_abbreviation)))
            {
                $lines_to_print = $lines_to_print + 1;
            }
            
            if($curLine+$lines_to_print > $number_of_printable_lines_per_page)
            {
                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    if($useLF)
                    {
                        if($number_of_lines_per_page > $curLine)
                        {
                            for($l = $curLine; $l < $number_of_lines_per_page; $l++)
                                fwrite($handle, $begin_of_line.$end_of_line);
                        }
                    }
                    else
                        fwrite($handle, $form_feed);
                }
                $page_number++;
				$page_number_to_print++;

                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    $curLine = 0;
                    fwrite($handle, $begin_of_line.$end_of_line);
                    $curLine++;

                    $ring_num_text = JText::_('COM_TOES_RING').$ringNum;
                    $show_class_text = strtoupper($entry->show_class);
                    $page_num_text = 'P'.$page_number_to_print;

                    $length = strlen($ring_num_text.$show_class_text.$page_num_text);
                    $diff = $number_of_characters_per_line - $length;
                    $spaces = '';
                    for($k=0;$k<($diff/2);$k++)
                        $spaces .= ' ';

                    $text = $begin_of_line.$ring_num_text.$spaces.$show_class_text.$spaces.$page_num_text.$end_of_line;
                    fwrite($handle, $text);
                    $curLine++;

					$club_name = strtoupper($show->club_abbreviation).'-'.$show->Show_location;
					$club_name = substr($club_name, 0, $club_name_max_characters);
					$text = $leftmarginforlocation.$club_name;
                    $date = date('M-d',strtotime($show_days[$entry->show_day]->show_day_date));
					if($is_alternative)
					{
						if($rings[$ring_number]->ring_timing == 1)
							$date .= ' AM';
						else
							$date .= ' PM';
					}
                    $length = strlen($text.$date);
                    $diff = $number_of_characters_per_line - $length;
                    $spaces = '';
                    for($k=0;$k<$diff;$k++)
                        $spaces .= ' ';
                    $text = $begin_of_line.$text.$spaces.$date.$end_of_line;

                    fwrite($handle, $text);
                    $curLine++;

                    fwrite($handle, $begin_of_line.$end_of_line);
                    $curLine++;
                }
                else
                    $curLine = 4;
            }            
            
            for($i=1; $i<= $previous_division_entries; $i++)
            {
                if($i == 4)
                    break;
                
                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    if(!in_array($entry->show_class, $skip_breed_best) && (($cur == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && ($entry->show_class != @$entries[$cur]->show_class || $entry->breed_abbreviation != @$entries[$cur]->breed_abbreviation))))
                    {
                        fwrite($handle, $begin_of_line);

                        $text = JText::_('COM_TOES_SHORT_BEST_BREED_DIVISION_ENTRY_'.$i).' '.$entry->breed_abbreviation;
                        $length = strlen($text);

                        $diff = ($width_column_1+$width_column_2) - $length;
                        $spaces = '';
                        for($k=0;$k<$diff;$k++)
                            $spaces .= ' ';

                        $text = $spaces.$text;

                        fwrite($handle, $text);

                        $spaces = '';
                        for($k=0;$k<$width_column_3;$k++)
                            $spaces .= ' ';

                        fwrite($handle, $spaces.$end_of_line);                
                    }
                    else
                    {
                        fwrite($handle, $begin_of_line);

                        $text = JText::_('COM_TOES_SHORT_BEST_DIVISION_ENTRY_'.$i);
                        $length = strlen($text);

                        $diff = ($width_column_1+$width_column_2) - $length;
                        $spaces = '';
                        for($k=0;$k<$diff;$k++)
                            $spaces .= ' ';

                        $text = $spaces.$text;

                        fwrite($handle, $text);

                        $spaces = '';
                        for($k=0;$k<$width_column_3;$k++)
                            $spaces .= ' ';

                        fwrite($handle, $spaces.$end_of_line);                
                    }
                }
                $curLine++;
            }
            
            if($show->show_print_extra_lines_for_bod_and_bob_in_judges_book)
            {
	            for($i=$i; $i <= 3; $i++)
	            {
	                if( $start_page <= $page_number && $page_number <= $end_page)
	                    fwrite($handle, $begin_of_line.$end_of_line);                
	                $curLine++;
	            }
            }
            
            if(!in_array($entry->show_class, $skip_breed_best) && ($cur == count($entries) && $previous_breed_entries == $previous_division_entries) || ($previous_breed_entries == $previous_division_entries && ($entry->show_class != @$entries[$cur]->show_class || $entry->breed_abbreviation != @$entries[$cur]->breed_abbreviation)))
            {
                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    $spaces = '';
                    for($k=0;$k<($number_of_characters_per_line-4);$k++)
                        $spaces .= 'X';
                    fwrite($handle, $begin_of_line.$spaces.$end_of_line);                
                }
                $curLine++;
            }
        }

        if(in_array($entry->show_class,$skip_breed_best))
        {
            $previous_breed_entries = 0;
        }
        else if( $previous_breed_entries > $previous_division_entries && ($cur == count($entries) || (isset($entries[$cur]) && ($previous_class != $entries[$cur]->show_class || $entry->breed_abbreviation != $entries[$cur]->breed_abbreviation) )))
        {
        	if($show->show_print_extra_lines_for_bod_and_bob_in_judges_book)
        		$lines_to_print = ($previous_breed_entries < 4)?($previous_breed_entries+1):4;
        	else
        		$lines_to_print = 4;
        	 
            if($curLine+$lines_to_print > $number_of_printable_lines_per_page)
            {
                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    if($useLF)
                    {
                        if($number_of_lines_per_page > $curLine)
                        {
                            for($l = $curLine; $l < $number_of_lines_per_page; $l++)
                                fwrite($handle, $begin_of_line.$end_of_line);
                        }
                    }
                    else
                        fwrite($handle, $form_feed);
                }
                $page_number++;
				$page_number_to_print++;

                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    $curLine = 0;
                    fwrite($handle, $begin_of_line.$end_of_line);
                    $curLine++;

                    $ring_num_text = JText::_('COM_TOES_RING').$ringNum;
                    $show_class_text = strtoupper($entry->show_class);
                    $page_num_text = 'P'.$page_number_to_print;

                    $length = strlen($ring_num_text.$show_class_text.$page_num_text);
                    $diff = $number_of_characters_per_line - $length;
                    $spaces = '';
                    for($k=0;$k<($diff/2);$k++)
                        $spaces .= ' ';

                    $text = $begin_of_line.$ring_num_text.$spaces.$show_class_text.$spaces.$page_num_text.$end_of_line;
                    fwrite($handle, $text);
                    $curLine++;

					$club_name = strtoupper($show->club_abbreviation).'-'.$show->Show_location;
					$club_name = substr($club_name, 0, $club_name_max_characters);
					$text = $leftmarginforlocation.$club_name;
                    $date = date('M-d',strtotime($show_days[$entry->show_day]->show_day_date));
					if($is_alternative)
					{
						if($rings[$ring_number]->ring_timing == 1)
							$date .= ' AM';
						else
							$date .= ' PM';
					}
                    $length = strlen($text.$date);
                    $diff = $number_of_characters_per_line - $length;
                    $spaces = '';
                    for($k=0;$k<$diff;$k++)
                        $spaces .= ' ';
                    $text = $begin_of_line.$text.$spaces.$date.$end_of_line;

                    fwrite($handle, $text);
                    $curLine++;

                    fwrite($handle, $begin_of_line.$end_of_line);
                    $curLine++;
                }
                else
                    $curLine = 4;
            }            
            
            for($i=1; $i<= $previous_breed_entries; $i++)
            {
                if($i == 4)
                    break;

                if( $start_page <= $page_number && $page_number <= $end_page)
                {
                    fwrite($handle, $begin_of_line);

                    $text = JText::_('COM_TOES_SHORT_BEST_BREED_ENTRY_'.$i).' '.$entry->breed_abbreviation;
                    $length = strlen($text);

                    $diff = ($width_column_1+$width_column_2) - $length;
                    $spaces = '';
                    for($k=0;$k<$diff;$k++)
                        $spaces .= ' ';

                    $text = $spaces.$text;

                    fwrite($handle, $text);

                    $spaces = '';
                    for($k=0;$k<$width_column_3;$k++)
                        $spaces .= ' ';

                    fwrite($handle, $spaces.$end_of_line);                
                }
                $curLine++;
            }

            if($show->show_print_extra_lines_for_bod_and_bob_in_judges_book)
            {
	            for($i=$i; $i <= 3; $i++)
	            {
	                if( $start_page <= $page_number && $page_number <= $end_page)
	                    fwrite($handle, $begin_of_line.$end_of_line);                
	                $curLine++;
	            }
            }
            
            if( $start_page <= $page_number && $page_number <= $end_page)
            {
                $spaces = '';
                for($k=0;$k<($number_of_characters_per_line-4);$k++)
                    $spaces .= 'X';
                fwrite($handle, $begin_of_line.$spaces.$end_of_line);                
            }
            $curLine++;
        }
    }
	
	if( $start_page <= $page_number && $page_number <= $end_page)
	{
		if($useLF)
		{
			if($number_of_lines_per_page > $curLine)
			{
				for($l = $curLine; $l < $number_of_lines_per_page; $l++)
					fwrite($handle, $begin_of_line.$end_of_line);
			}
		}
		else
			fwrite($handle, $form_feed);
	}
}
fclose($handle);
echo $page_number;
// ---------------------------------------------------------

//============================================================+
// END OF FILE                                                
//============================================================+
