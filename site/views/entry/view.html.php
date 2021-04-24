<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
ini_set('display_error', 1);

/**
 * View to edit a template style.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESViewEntry extends JViewLegacy {

    /**
     * Display the view
     */
    public function display($tpl = null) {

		$app = JFactory::getApplication();
        $user = JFactory::getUser();
        $step = $app->input->getVar('step');

        require_once JPATH_BASE . DS . 'components' . DS . 'com_toes' . DS . 'models' . DS . 'entry.php';
        $model = new ToesModelEntry();
        
        //
        if($app->input->getVar('layout') == 'documents')
        {
			$entry_id = $app->input->getInt('entry_id');
			$show_id = $app->input->getInt('show_id');
            $summary_id = $app->input->getInt('summary_id'); 
            $this->documents = $model->getDocumentsforEntry();
			$this->entrystatus = $model->get('entrystatus');
            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }

            parent::display($tpl);
            $app->close();
        }
        //
		
        if($app->input->getVar('layout') == 'reject')
        {
			$this->entry_id = $app->input->getInt('entry_id');
			
			$db = JFactory::getDbo();
			$db->setQuery("SELECT `entry_refusal_reason_reason` FROM `#__toes_entry_refusal_reason` WHERE `entry_refusal_reason_entry`=".$this->entry_id);
			if($db->loadResult())
				$this->reason = $db->loadResult();
			else
				$this->reason = '';

			parent::display();
			$app->close();
		}
		
        if($app->input->getVar('layout') == 'congress_filters')
        {
            $this->ring_index = $app->input->getInt('index');
            $this->ring_name = $app->input->getVar('ring_name');
            $this->ring_id = $app->input->getInt('ring_id');
            
            $this->filters = '';
            if($this->ring_id)
            {
                $this->filters = TOESHelper::getCongressFilters($this->ring_id);
                if($this->filters)
                    $this->filters->ring_index = $this->ring_index;
            }
            
            if(!$this->filters)
            {
                $session = JFactory::getSession();
                $flag = false;
                if($session->has('congress_filters'))
                {
                    $str = $session->get('congress_filters');
                    $filters = unserialize($str);

                    foreach ($filters as $item)
                    {
                        if($item->ring_index == $this->ring_index)
                        {
                            $filter = $item;
                            
                            $db = JFactory::getDbo();

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(show_class)) AS class_text');
                            $query->from('#__toes_show_class');
                            $query->where('show_class_id IN ('.$filter->class_value.')');
                            $db->setQuery($query);
                            $filter->class_text = $db->loadResult();
                            

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(CONCAT(breed_name," (",breed_abbreviation,")"))) AS breed_text');
                            $query->from('#__toes_breed');
                            $query->where('breed_id IN ('.$filter->breed_value.')');
                            $db->setQuery($query);
                            $filter->breed_text = $db->loadResult();

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(CONCAT(gender_name," (",gender_short_name,")"))) AS gender_text');
                            $query->from('#__toes_cat_gender');
                            $query->where('gender_id IN ('.$filter->gender_value.')');
                            $db->setQuery($query);
                            $filter->gender_text = $db->loadResult();

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(CONCAT(cat_hair_length," (",cat_hair_length_abbreviation,")"))) AS hairlength_text');
                            $query->from('#__toes_cat_hair_length');
                            $query->where('cat_hair_length_id IN ('.$filter->hairlength_value.')');
                            $db->setQuery($query);
                            $filter->hairlength_text = $db->loadResult();

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(category)) AS category_text');
                            $query->from('#__toes_category');
                            $query->where('category_id IN ('.$filter->category_value.')');
                            $db->setQuery($query);
                            $filter->category_text = $db->loadResult();

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(division_name)) AS division_text');
                            $query->from('#__toes_division');
                            $query->where('division_id IN ('.$filter->division_value.')');
                            $db->setQuery($query);
                            $filter->division_text = $db->loadResult();

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(color_name)) AS color_text');
                            $query->from('#__toes_color');
                            $query->where('color_id IN ('.$filter->color_value.')');
                            $db->setQuery($query);
                            $filter->color_text = $db->loadResult();

                            $query = $db->getQuery(true);
                            $query->select('GROUP_CONCAT(DISTINCT(CONCAT(cat_title," (",cat_title_abbreviation,")"))) AS title_text');
                            $query->from('#__toes_cat_title');
                            $query->where('cat_title_id IN ('.$filter->title_value.')');
                            $db->setQuery($query);
                            $filter->title_text = $db->loadResult();
                            
                            $flag = true;
                        }
                    }
                }

                if(!$flag)
                {
                    $filter = new stdClass();

                    $filter->ring_index = $this->ring_index;
                    $filter->ring_name = $this->ring_name;
                    $filter->ring_id = 0;

                    $filter->breed_filter = 0;
                    $filter->gender_filter = 0;
                    $filter->newtrait_filter = 0;
                    $filter->hairlength_filter = 0;
                    $filter->category_filter = 0;
                    $filter->division_filter = 0;
                    $filter->color_filter = 0;
                    $filter->title_filter = 0;
                    $filter->manual_filter = 0;

                    $filter->class_value = '';
                    $filter->breed_value = '';
                    $filter->gender_value = '';
                    $filter->hairlength_value = '';
                    $filter->category_value = '';
                    $filter->division_value = '';
                    $filter->color_value = '';
                    $filter->title_value = '';
                    
                    $filter->cwd_value = '';
                }
                $this->filters = $filter;
            }
            
            $this->show_classes = $this->get('Showclasses');
            $this->breeds = $this->get('Breeds');
            $this->categories = $this->get('Categories');
            $this->divisions = $this->get('Divisions');
            $this->colors = $this->get('Colors');

            $this->genders = $this->get('Genders');
            $this->hairlengths = $this->get('Hairlengths');
            $this->titles = $this->get('Titles');
            
            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }
			
            parent::display($tpl);
			$app->close();
        }

        if($app->input->getVar('layout') == 'details')
        {
            $entry_id = $app->input->getInt('id');
            
            $this->breeds = $this->get('Breeds');
            $this->categories = $this->get('Categories');
            $this->divisions = $this->get('Divisions');
            $this->colors = $this->get('Colors');
			$this->hairlengths = $this->get('Hairlengths');
            $this->genders = $this->get('Genders');
            $this->titles = $this->get('Titles');
            $this->prefixes = $this->get('Prefixes');
            $this->suffixes = $this->get('Suffixes');
            $this->competitiveregions = $this->get('Competitiveregions');
            
            
            $this->entry_details = TOESHelper::getEntryDetails($entry_id);
            $this->cat_details = TOESHelper::getCatDetails($this->entry_details->cat);
            
            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }

            parent::display($tpl);
            $app->close();
        }
        
        if($app->input->getVar('layout') == 'edit_summary' || $app->input->getVar('layout') == 'edit_fees')
        {
            $summary_id = $app->input->getInt('summary_id');
            $this->summary = $model->getSummaryFromId($summary_id);

            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }

            parent::display($tpl);
            $app->close();
        }
        // echo $step;
        switch ($step) {
            case 'step0':
                $this->regions = $model->getTicaRegions();
                break;
            case 'step1':
                $this->totalcats = $model->getTotalCats();
                $this->cats = $model->getCats();
                break;
            case 'step1_5':
				//var_dump($_REQUEST);
                $this->document_types = $model->getdocument_types();
				$this->document_types_list = $model->getDocument_types_list();
				$this->documents = $model->getDocumentsforEntry();
				$this->organizations = $model->getOrganization();
				$this->reg_number_formats = $model->getRegNumberFormats();
				$this->document_type_labels = $model->getDocument_type_labels();
				$this->document_weights = $model->getDocument_weights();
				//var_dump($this->documents);
				
				$this->cat = TOESHelper::getCatDetails($this->entry->cat_id);
                $this->showdays = $model->getShowdays();
                break;
            case 'step2':
                $this->cat = TOESHelper::getCatDetails($this->entry->cat_id);
                $this->showdays = $model->getShowdays();
                break;
            case 'step3':
                $this->cat = TOESHelper::getCatDetails($this->entry->cat_id);
                $show = TOESHelper::getShowDetails($this->entry->show_id);
                $this->show = $show; // sandy hack to get  show_id
				$this->selected_showdays = $model->getSelectedShowday();
				$this->cat->breed_status = TOESHelper::getEntryBreedStatusonDate($this->entry->cat_id, $this->entry->show_id, $show->show_start_date);
				if(!$this->cat->breed_status)
					$this->cat->breed_status = TOESHelper::getCatBreedStatusonDate($this->entry->cat_id, $show->show_start_date);

				$this->exhibitionOnly = ($this->cat->breed_status == 'Registration Only' || $this->cat->breed_status == 'Experimental')?1:0;

				if(!$this->exhibitionOnly)
				{
					if(TOESHelper::isExOnlyforShow($this->entry->cat_id, $this->entry->showdays))
					{
						$this->exhibitionOnly = 1;
					}
					if(!$this->exhibitionOnly)
					{
						$isNT = false;
						$showdays = explode(',', $this->entry->showdays);
						foreach($showdays as $showday)
						{
							$class = '';
							$class = TOESHelper::getEntryclassonShowday($this->cat->cat_id, $showday);
							if(!$class)
								$class = TOESHelper::getCatclassonShowday($this->cat->cat_id, $showday);
							if(strpos($class, 'NT') || strpos($class, 'ANB') || strpos($class, 'PNB'))
							{
								$isNT = true;
							}
						}

						if($isNT && !$this->cat->cat_registration_number)
							$this->exhibitionOnly = 1;
					}
				}
				
				// sandy hack - if cat breed = 24 and is adult and is M/F then it should be  exhibitionOnly
				$this->is_adult_hhp_not_altered = false;
				if(!$this->exhibitionOnly){
				if($this->cat->breed_abbreviation == 'HHP' && ($this->cat->gender_short_name == 'M' || $this->cat->gender_short_name == 'F')){
					$showdate = new DateTime($show->show_start_date, new DateTimeZone('UTC'));
					$cat_dob = new DateTime($this->cat->cat_date_of_birth, new DateTimeZone('UTC'));
					$interval = $showdate->diff($cat_dob);
					 
					$is_adult = false;
					$age_years = intval($interval->format('%y'));
					$age_months = intval($interval->format('%m'));
					if($age_years > 0) {
						$is_adult = true;
					} else {
						if($age_months >= 8) {
							$is_adult = true;
						}  
					}
					if($is_adult){
						$this->exhibitionOnly = 1;
						$this->is_adult_hhp_not_altered = true; 
					}
					
				}
				}
				// end hack

                $this->selected_showdays = $model->getSelectedShowday();
                break;
            case 'step4':
                $dir = $app->input->getVar('dir','next');
                $this->cat = TOESHelper::getCatDetails($this->entry->cat_id);

                $show = TOESHelper::getShowDetails($this->entry->show_id);
				$is_alternative = ($show->show_format=='Alternative')?true:false;
				
				$this->cat->breed_status = TOESHelper::getEntryBreedStatusonDate($this->entry->cat_id, $this->entry->show_id, $show->show_start_date);
				if(!$this->cat->breed_status)
					$this->cat->breed_status = TOESHelper::getCatBreedStatusonDate($this->entry->cat_id, $show->show_start_date);
                
                $this->exhibitionOnly = ($this->cat->breed_status == 'Registration Only' || $this->cat->breed_status == 'Experimental')?1:0;
                
				if(!$this->exhibitionOnly)
				{
					if(TOESHelper::isExOnlyforShow($this->entry->cat_id, $this->entry->showdays))
					{
						$this->exhibitionOnly = 1;
					}

					if(!$this->exhibitionOnly)
					{
						$isNT = false;
						$showdays = explode(',', $this->entry->showdays);
						foreach($showdays as $showday)
						{
							$class = '';
							$class = TOESHelper::getEntryclassonShowday($this->cat->cat_id, $showday);
							if(!$class)
								$class = TOESHelper::getCatclassonShowday($this->cat->cat_id, $showday);
							if(strpos($class, 'NT') || strpos($class, 'ANB') || strpos($class, 'PNB'))
							{
								$isNT = true;
							}
						}

						if($isNT && !$this->cat->cat_registration_number)
							$this->exhibitionOnly = 1;
					}
				}

                $this->selected_showdays = $model->getSelectedShowday();
                $this->congress = $model->getCongress();
                
                if(!$this->exhibitionOnly && $this->congress)
                {
                    $applicable_for_congress = false;
                    $final_congress = array();
                    $selected_congress = array();
                    foreach($this->congress as $congress){
						if($is_alternative)
						{
							if($congress->ring_timing == 1)
							{
								if(!in_array($congress->ring_show_day, explode(',',$this->entry->entry_for_AM)))
								{
									continue;
								}
							}
							if($congress->ring_timing == 2)
							{
								if(!in_array($congress->ring_show_day, explode(',',$this->entry->entry_for_PM)))
								{
									continue;
								}
							}
						}

						if(isset($this->entry->entry_id))
						{
							if(TOESHelper::matchCongressFiltersforEntry($this->entry->entry_id, $congress->ring_id))
							{
								$applicable_for_congress = true;
								if(TOESHelper::isFilterManual($congress->ring_id))
									$final_congress[] = $congress;
								else
									$selected_congress[] = $congress->ring_id;
							}
						}
						else 
						{
	                        if(TOESHelper::matchCongressFilters($this->entry->cat_id, $congress->ring_id))
	                        {
	                            $applicable_for_congress = true;
	                            if(TOESHelper::isFilterManual($congress->ring_id))
	                                $final_congress[] = $congress;
	                            else
	                                $selected_congress[] = $congress->ring_id; 
	                        }
						}
                    }

                    if($applicable_for_congress)
                    {
                        $this->congress = $final_congress;
                        $this->automatic_congress = $selected_congress;

                        if(!$final_congress && $selected_congress && @$this->entry->participated_in_congress)
                        {
                            if($dir != 'prev')
                            {
                                $this->entry->congress = implode(',', $selected_congress);
                                $this->selected_congress = $model->getSelectedCongress();
                                $this->summary = $model->getSummary();
                                $this->setLayout('step5');
                            }
                            else
                            {
                                $this->setLayout('step3');
                            }
                        }
                    }
                    else
                    {
                        if($dir != 'prev')
                        {
                            $this->entry->congress = '';
                            $this->selected_congress = '';
                            $this->summary = $model->getSummary();
                            $this->setLayout('step5');
                        }
                        else
                        {
                            $this->setLayout('step3');
                        }
                    }                    
                }
                else
                {
                    if($dir != 'prev')
                    {
                        $this->entry->congress = '';
                        $this->selected_congress = '';
                        $this->summary = $model->getSummary();
                        $this->setLayout('step5');
                    }
                    else
                    {
                        $this->setLayout('step3');
                    }
                }
                
                break;
            case 'step5':
                $this->cat = TOESHelper::getCatDetails($this->entry->cat_id);
                $this->selected_showdays = $model->getSelectedShowday();
                $this->selected_congress = $model->getSelectedCongress();
                $this->summary = $model->getSummary();
                break;
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }

}
