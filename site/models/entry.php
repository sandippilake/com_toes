<?php

/**
 * @service	Joomla
 * @subservice	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * @service	Joomla
 * @subservice	com_toes
 */
class TOESModelEntry extends JModelLegacy {

	public function getUsers() {
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();

		$query = "SELECT CONCAT(b.lastname,' ',b.firstname,' - ',u.username) as name, u.id 
                    FROM #__users as u 
                    LEFT JOIN #__comprofiler as b ON u.id = b.user_id
                    WHERE b.lastname IS NOT NULL
                    ORDER BY b.lastname";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getTotalCats() {
		$db = $this->getDbo();

		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$show_id = $entry->show_id;
		$user_id = $entry->user_id;
		if (!$show_id) {
			$show_id = $app->input->getVar('show_id');
			$entry->show_id = $show_id;
			$session->set('entry', $entry);
		}
		$query = "SELECT DISTINCT(vc.cat_id)
                FROM #__toes_cat AS vc 
				LEFT JOIN #__toes_cat_relates_to_user AS crtu ON crtu.of_cat = vc.cat_id
				LEFT JOIN #__toes_cat_user_connection_type AS type ON type.cat_user_connection_type_id = crtu.cat_user_connection_type
                WHERE crtu.person_is = " . $user_id . " AND type.cat_user_connection_type NOT LIKE 'Breeder'";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCats() {
		$db = $this->getDbo();
		$app = JFactory::getApplication();
		
		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$show_id = $entry->show_id;
		$user_id = $entry->user_id;
		if (!$show_id) {
			$show_id = $app->input->getVar('show_id');
			$entry->show_id = $show_id;
			$session->set('entry', $entry);
		}
		// sandy hack to fetch show details
		$db->setQuery("select * from `#__toes_show` where show_id =".$show_id);
		$show_record = $db->loadObject();

		$cats = $this->getConflicatedCats();

		if (isset($entry->cat_id))
			$cats[] = $entry->cat_id;
		// sandy hack added breed,age and gender to query
		$query = "SELECT DISTINCT(tc.cat_id), tc.cat_name, tcp.cat_prefix_abbreviation, tcs.cat_suffix_abbreviation, t.cat_title_abbreviation, date_format(tc.cat_date_of_birth,'%m/%d/%y') as da
                ,tc.cat_date_of_birth,".$db->Quote($show_record->show_start_date)." as show_start_date,DATEDIFF(".$db->Quote($show_record->show_start_date).",tc.cat_date_of_birth) as age,tc.cat_gender,tc.cat_breed
                FROM #__toes_cat AS tc
                LEFT JOIN #__toes_cat_prefix as tcp ON tc.cat_prefix = tcp.cat_prefix_id
                LEFT JOIN #__toes_cat_suffix as tcs ON tc.cat_suffix = tcs.cat_suffix_id
                LEFT JOIN #__toes_cat_title AS t ON t.cat_title_id = tc.cat_title
				LEFT JOIN #__toes_cat_relates_to_user AS crtu ON crtu.of_cat = tc.cat_id
				LEFT JOIN #__toes_cat_user_connection_type AS type ON type.cat_user_connection_type_id = crtu.cat_user_connection_type
                WHERE crtu.person_is = " . $user_id . " AND type.cat_user_connection_type NOT LIKE 'Breeder'
				AND crtu.of_cat NOT IN (SELECT e.cat FROM #__toes_entry AS e WHERE e.entry_show =  $show_id " . ($cats ? ' AND e.cat NOT IN ( ' . implode(',', $cats) . ')' : '') . " )";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	public function getdocument_types() {
        $app = JFactory::getApplication();
		$id = $app->input->getInt('id');
		$db = JFactory::getDbo();
		$db->setQuery("select * from `#__toes_allowed_registration_document_type` order by allowed_registration_document_id");
		$typelist =  $db->loadObjectList();

		$options[] = JHTML::_('select.option','',JText::_('COM_TOES_SELECT_DOCUMENT_TYPE'));
		foreach($typelist as $r) :
			$options[] = JHTML::_('select.option',$r->allowed_registration_document_id,JText::_($r->allowed_registration_document_name_language_constant));
		endforeach;
		 
		return JHTML::_('select.genericlist',$options,'document_type[]','class="dtype" style="width:500px!important"','value','text',null);
		
    }
    public function getdocument_types_list() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);


        $query->select('allowed_registration_document_id AS value, allowed_registration_document_type AS text');
        $query->from('#__toes_allowed_registration_document_type');
        $query->order('allowed_registration_document_id ASC');

        // Get the options.
        $db->setQuery($query);

        return $db->loadObjectList();
        
    }
	public function getConflicatedCats() {
		$db = $this->getDbo();

		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$show_id = $entry->show_id;
		$user_id = $entry->user_id;

		$placeholder_id = @$entry->placeholder_id;
		if (!$placeholder_id) {
			return array();
		}

		$placeholders = TOESHelper::getPlaceholderFullDetails($entry->placeholder_id);

		$query = "SELECT e.*
				FROM #__toes_entry AS e 
				LEFT JOIN #__toes_summary AS s ON s.summary_id = e.summary
				WHERE s.summary_show = {$show_id} AND s.summary_user = {$user_id}
				";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadObjectList();

		$cat_ids = array();

		$cats = array();
		$show_days = array();
		$AM_entries = array();
		$PM_entries = array();
		foreach ($entries as $entry) {
			$cats[$entry->cat] = $entry->cat;
			$show_days[$entry->cat][] = $entry->show_day;

			if (!isset($AM_entries[$entry->cat]))
				$AM_entries[$entry->cat] = array();
			if ($entry->entry_participates_AM)
				$AM_entries[$entry->cat][] = $entry->show_day;

			if (!isset($PM_entries[$entry->cat]))
				$PM_entries[$entry->cat] = array();
			if ($entry->entry_participates_PM)
				$PM_entries[$entry->cat][] = $entry->show_day;
		}

		$placeholder_showdays = array();
		foreach ($placeholders as $placeholder) {
			$placeholder_showdays[] = $placeholder->placeholder_day_showday;
		}

		foreach ($cats as $cat) {
			$available = true;

			if (!array_diff($placeholder_showdays, $show_days[$cat])) {
				foreach ($placeholders as $placeholder) {
					if (in_array($placeholder->placeholder_day_showday, $show_days[$cat])) {
						if ($placeholder->placeholder_participates_AM && in_array($placeholder->placeholder_day_showday, $AM_entries[$cat])) {
							$available = false;
						}

						if ($placeholder->placeholder_participates_PM && in_array($placeholder->placeholder_day_showday, $PM_entries[$cat])) {
							$available = false;
						}
					} else
						$available = false;
				}
			}

			if ($available && !in_array($cat, $cat_ids))
				$cat_ids[] = $cat;
		}

		return $cat_ids;
	}

	public function getShowdays() {
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$show_id = $entry->show_id;
		if (!$show_id) {
			$show_id = $app->input->getVar('show_id');
			$entry->show_id = $show_id;
			$session->set('entry', $entry);
		}

		$query->select('s.show_day_id, s.show_day_date');
		$query->from('#__toes_show_day AS s');
		$query->where('s.show_day_show =' . (int) $show_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getSelectedShowday() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$isContinuous = TOESHelper::isContinuous($entry->show_id);
		if ($isContinuous)
			return array(JText::_('JALL'));

		$showdays = $entry->showdays;

		$query->select('DAYNAME(s.show_day_date) as dayname, show_day_id');
		$query->from('#__toes_show_day AS s');
		$query->where('s.show_day_id in (' . $showdays . ' )');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$selected = $db->loadObjectList();

		$isAlternative = TOESHelper::isAlternative($entry->show_id);
		$final = array();
		foreach ($selected as $day) {
			$day_string = $day->dayname;
			if ($isAlternative) {
				if (in_array($day->show_day_id, explode(',', $entry->entry_for_AM))) {
					$day_string .= ' AM';
					if (in_array($day->show_day_id, explode(',', $entry->entry_for_PM))) {
						$day_string .= '/PM';
					}
				} elseif (in_array($day->show_day_id, explode(',', $entry->entry_for_PM))) {
					$day_string .= ' PM';
				}
			}
			$final[] = $day_string;
		}

		return $final;
	}

	public function getCongress() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$showdays = $entry->showdays;

		$query->select('*');
		$query->from('#__toes_ring as r');
		$query->join('LEFT', '#__toes_ring_format AS rf ON r.ring_format = rf.ring_format_id');
		$query->where('r.ring_show_day in (' . $showdays . ') AND rf.ring_format = ' . $db->quote('Congress'));
		//echo $query;
		$db->setQuery($query);
		return $db->loadObjectlist();
	}

	public function getSelectedCongress() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$congress = $entry->congress;

		if(!$congress) {
			return false;
		}
		$query->select('r.ring_name');
		$query->from('#__toes_ring as r');
		$query->where('r.ring_id in (' . $congress . ' )');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadColumn();
	}

	public function getSummary() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$session = JFactory::getSession();
		$entry = $session->get('entry');

		$show_id = $entry->show_id;
		$user_id = $entry->user_id;

		$query->select('s.*');
		$query->from('#__toes_summary as s');
		$query->where('s.summary_show = ' . $show_id . ' AND s.summary_user = ' . $user_id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getSummaryFromId($summary_id) {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('s.*');
		$query->from('#__toes_summary as s');
		$query->where('s.summary_id = ' . $summary_id);

		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 */
	public function save_entry() {
		// Initialise variables;
		$session = JFactory::getSession();
		$entry = $session->get('entry');
		 

		$user = JFactory::getUser();

		$is_offcial = (TOESHelper::is_clubowner($user->id, TOESHelper::getClub($entry->show_id)->club_id) || TOESHelper::is_showofficial($user->id, $entry->show_id) || TOESHelper::isAdmin()) ? true : false;

		$check_waiting_list = false;

		$send_notification = false;

		$show = TOESHelper::getShowDetails($entry->show_id);
		$isAlternative = TOESHelper::isAlternative($entry->show_id);

		$db = JFactory::getDbo();
		$edit = false;
		$congress_list = '';
		$congress_showdays = array();
		if ($entry->congress) {
			$query = "SELECT * 
                    FROM #__toes_ring 
                    WHERE ring_show = {$entry->show_id}
                    AND ring_id IN(" . $entry->congress . ")";
			$db->setQuery($query);
			$congress_list = $db->loadObjectlist();
		}
		if ($congress_list) {
			foreach ($congress_list as $congress) {
				$congress_showdays[] = $congress->ring_show_day;
			}
		}

		$cat_detail = TOESHelper::getCatDetails($entry->cat_id);

		$showdays = explode(',', $entry->showdays);

		$query = "SELECT show_day_id FROM #__toes_show_day WHERE `show_day_show`  = {$entry->show_id}";
		$db->setQuery($query);
		$org_showdays = $db->loadColumn();

		$query = "SELECT * FROM `#__toes_show_class`";
		$db->setQuery($query);
		$show_classes = $db->loadAssocList('show_class','show_class_id');

		$excluded_showdays = array_diff($org_showdays, $showdays);

		if ($cat_detail->cat_id) {

			$query = "SELECT summary_id FROM `#__toes_summary` "
					. " WHERE summary_show = " . $db->quote($entry->show_id)
					. " AND summary_user = " . $db->quote($entry->user_id);

			$db->setQuery($query);
			$summary_id = $db->loadResult();

			if ($summary_id) {
				$query = "UPDATE `#__toes_summary` SET "
						. " summary_benching_request = " . $db->quote($entry->benching_request) . ","
						. " summary_grooming_space = " . $db->quote($entry->grooming_space ? 1 : 0) . ","
						. " summary_single_cages = " . $db->quote($entry->single_cages) . ","
						. " summary_double_cages = " . $db->quote($entry->double_cages) . ","
						. " summary_personal_cages = " . $db->quote($entry->personal_cage ? 1 : 0) . ","
						. " summary_remarks = " . $db->quote($entry->remark) . ","
						. " summary_status = (SELECT s.`summary_status_id` FROM `#__toes_summary_status` AS s WHERE s.`summary_status` = 'Updated') "
						. " WHERE summary_id = " . $db->quote($summary_id);

				$db->setQuery($query);
				$db->query();
			} else {
				$query = "INSERT INTO `#__toes_summary` SET "
						. " summary_benching_request = " . $db->quote($entry->benching_request) . ","
						. " summary_grooming_space = " . $db->quote($entry->grooming_space ? 1 : 0) . ","
						. " summary_single_cages = " . $db->quote($entry->single_cages) . ","
						. " summary_double_cages = " . $db->quote($entry->double_cages) . ","
						. " summary_personal_cages = " . $db->quote($entry->personal_cage ? 1 : 0) . ","
						. " summary_remarks = " . $db->quote($entry->remark) . ","
						. " summary_show = " . $db->quote($entry->show_id) . ","
						. " summary_user = " . $db->quote($entry->user_id) . ","
						. " summary_status = 1 ";
				$db->setQuery($query);
				$db->query();

				$summary_id = $db->insertid();
			}

			if ($showdays) {

				$entry->entry_for_AM = explode(',', $entry->entry_for_AM);
				$entry->entry_for_PM = explode(',', $entry->entry_for_PM);

				for ($i = 0; $i < count($showdays); $i++) {
					$whr = array(
						"`cat` = {$cat_detail->cat_id}",
						"`show_day` = {$showdays[$i]} "
					);
					$query = TOESQueryHelper::getEntryFullViewQuery($whr);
					$db->setQuery($query);
					$orgentry = $db->loadObject();
					if ($orgentry) {
						$entry_id = $orgentry->entry_id;
					} else {
						$entry_id = 0;
					}
					
					if ($entry_id) {

						/* Calculate cat's age on show day and deciding its show_class */

						$query = "SELECT `show_day_date` FROM `#__toes_show_day` WHERE `show_day_id` = ".$showdays[$i];
						$db->setQuery($query);
						$show_day_date = $db->loadResult();

						$show_class = "";

						$query = "select `bs`.`breed_status` 
						FROM `#__toes_breed` AS `b`
						LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND ".$db->quote($show_day_date)." BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
						LEFT JOIN `#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
						WHERE `b`.`breed_id` = ".$orgentry->copy_cat_breed;

						$db->setQuery($query);
						$breed_status = $db->loadResult();

						$is_HHP = strpos(' '.$orgentry->breed_name,'Household') ? true : false ;
						
						$age_years = 0;
						$age_months = 0;

						$showdate = new DateTime($show_day_date, new DateTimeZone('UTC'));
						$cat_dob = new DateTime($orgentry->copy_cat_date_of_birth, new DateTimeZone('UTC'));
						$interval = $showdate->diff($cat_dob);

						$age_years = intval($interval->format('%y'));
						$age_months = intval($interval->format('%m'));

						$is_kitten = false;
						$is_adult = false;

						if($age_years > 0) {
							$is_adult = true;
						} else {
							if($age_months >= 8) {
								$is_adult = true;
							} elseif($age_months >= 4 && $age_months < 8) {
								$is_kitten = true;
							}
						}

						if ( $entry->exh_only == 1) {
							$show_class = 'Ex Only';
						} else {
							if ( $breed_status == 'Non Championship'){  #this means HHP
								if ( $is_HHP == true) {    # this is the only non championship class
									if ( $is_kitten == true) {  # HHP Kitten
										if ( $orgentry->cat_hair_length_abbreviation == 'LH') {
											$show_class = 'LH HHP Kitten';
										} else {
											$show_class = 'SH HHP Kitten';
										}
									} else {
										if ( $is_adult == true) {   #HHP
											if ( $orgentry->cat_hair_length_abbreviation == 'LH'){
												$show_class = 'LH HHP';
											} else {
												$show_class = 'SH HHP';
											}
										} else {
											if ($age_months >=3) {
												$show_class = 'Ex Only';
											} else {
												$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
											}
										}
									}
								} else {
									$show_class = 'ERROR - 2 - Please check view <Full Entry>';
								}
							} elseif ( $breed_status == 'Championship') {
								if ( $orgentry->copy_cat_new_trait ) {
									if($is_kitten || $is_adult) {
										if ( $orgentry->cat_hair_length_abbreviation == 'LH') {
											$show_class = 'LH NT';
										} else {
											$show_class = 'SH NT';
										} 
									} else {
										if ($age_months >=3) {
											$show_class = 'Ex Only';
										} else {
											$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
										}
									}
								} else {
									if ( $is_kitten == true) {
										if ( $orgentry->cat_hair_length_abbreviation == 'LH') {
											$show_class = 'LH Kitten';
										} else {
											$show_class = 'SH Kitten';
										}
									} else {
										if ( $is_adult == true) { 
											if ( $orgentry->cat_hair_length_abbreviation == 'LH') { 
												if( ($orgentry->gender_short_name == 'M') || ($orgentry->gender_short_name == 'F') ) {
													$show_class = 'LH Cat';
												 } else {
													$show_class = 'LH Alter';
												 }
											} else {
												if( ($orgentry->gender_short_name == 'M') || ($orgentry->gender_short_name == 'F') ) {
													$show_class = 'SH Cat';
												} else {
													$show_class = 'SH Alter';
												}
											}
										} else {
											if ($age_months >=3) {
												$show_class = 'Ex Only'; 
											} else {
												$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
											}
										}
									}
								}
							} else {  
								if( $breed_status == 'Advanced New Breed') {
									if($is_kitten || $is_adult) {
										if( $orgentry->cat_hair_length_abbreviation == 'LH') {
											$show_class = 'LH ANB';
										} else {
											$show_class = 'SH ANB';
										}
									} else {
										if ($age_months >= 3){
											$show_class = 'Ex Only';
										} else {
											$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
										}
									}
								} else {
									if( $breed_status == 'Preliminary New Breed') {
										if($is_kitten || $is_adult) {
											if( $orgentry->cat_hair_length_abbreviation == 'LH') {
												$show_class = 'LH PNB';
											} else {
												$show_class = 'SH PNB';
											}
										} else {
											if ($age_months >= 3) {
												$show_class = 'Ex Only';
											} else {
												$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
											}
										}
									} else {
										$show_class = 'Error - 4 - Please check view <Full Entry>';
									}
								}
							}
						}

						$entry_show = $entry->show_id;
						$entry_show_class = isset($show_classes[$show_class])?$show_classes[$show_class]:0;
						/* End - Age calculation */

						$entry_status = $orgentry->status;

						if($show->show_use_waiting_list) 
						{
							if ($isAlternative) {
								$am_waiting = false;
								$pm_waiting = false;

								if (!$entry->exh_only) {
									if (in_array($showdays[$i], $entry->entry_for_AM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i], '1', $entry_id)) {
										$am_waiting = true;
									}

									if (in_array($showdays[$i], $entry->entry_for_PM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i], '2', $entry_id)) {
										$pm_waiting = true;
									}

									if (($am_waiting && $pm_waiting) || (!in_array($showdays[$i], $entry->entry_for_AM) && $pm_waiting) || (!in_array($showdays[$i], $entry->entry_for_PM) && $am_waiting)) {
										continue;
									} else if ($am_waiting || $pm_waiting) {
										if ($am_waiting) {
											$entry->entry_for_AM = array_diff($entry->entry_for_AM, array($showdays[$i]));
										}

										if ($pm_waiting) {
											$entry->entry_for_PM = array_diff($entry->entry_for_PM, array($showdays[$i]));
										}
									}
								}
							} else {
								if (!$entry->exh_only) {
									if (!TOESHelper::getAvailableSpaceforDay($showdays[$i], null, $entry_id)) {
										$entry_status = '11';
									}
								} else {
									if ($entry_status == '11') {
										if (!$entry->congress || ($entry->congress && !in_array($showdays[$i], $congress_showdays))) {
											$entry_status = '1';
											$check_waiting_list = true;
										}
									}
								}
							}
						}
						
						//
						if($entry->copy_cat_registration_number)
						$copy_cat_registration_number = $entry->copy_cat_registration_number;
						else
						$copy_cat_registration_number = $orgentry->copy_cat_registration_number;

						$query = "UPDATE `#__toes_entry` 
                                SET `for_sale` = " . $db->quote($entry->for_sale) . ", 
                                `exhibition_only` = " . $db->quote($entry->exh_only) . ",
                                `entry_participates_AM` = " . $db->quote(in_array($orgentry->show_day, $entry->entry_for_AM) ? '1' : '0') . ",
                                `entry_participates_PM` = " . $db->quote(in_array($orgentry->show_day, $entry->entry_for_PM) ? '1' : '0') . ",
                                `status` = " . $db->quote($entry_status) . ",
                                `copy_cat_name` = " . $db->quote($orgentry->copy_cat_name) . ", 
                                `copy_cat_prefix` = " . $db->quote($orgentry->copy_cat_prefix) . ", 
                                `copy_cat_title` = " . $db->quote($orgentry->copy_cat_title) . ", 
                                `copy_cat_suffix` = " . $db->quote($orgentry->copy_cat_suffix) . ", 
                                `copy_cat_breed` = " . $db->quote($orgentry->copy_cat_breed) . ", 
                                `copy_cat_category` = " . $db->quote($orgentry->copy_cat_category) . ", 
                                `copy_cat_division` = " . $db->quote($orgentry->copy_cat_division) . ", 
                                `copy_cat_color` = " . $db->quote($orgentry->copy_cat_color) . ", 
                                `copy_cat_date_of_birth` = " . $db->quote($orgentry->copy_cat_date_of_birth) . ", 
                                `copy_cat_registration_number` = " . $db->quote($copy_cat_registration_number) . ", 
                                `copy_cat_gender` = " . $db->quote($orgentry->copy_cat_gender) . ", 
                                `copy_cat_new_trait` = " . $db->quote($orgentry->copy_cat_new_trait) . ", 
                                `copy_cat_sire_name` = " . $db->quote($orgentry->copy_cat_sire_name) . ", 
                                `copy_cat_dam_name` = " . $db->quote($orgentry->copy_cat_dam_name) . ", 
                                `copy_cat_breeder_name` = " . $db->quote($orgentry->copy_cat_breeder_name) . ", 
                                `copy_cat_owner_name` = " . $db->quote($orgentry->copy_cat_owner_name) . ", 
                                `copy_cat_agent_name` = " . $db->quote($entry->copy_cat_lessee_name) . ", 
                                `copy_cat_lessee_name` = " . $db->quote($orgentry->cat_lessee) . ", 
                                `copy_cat_competitive_region` = " . $db->quote($orgentry->copy_cat_competitive_region) . ", 
                                `copy_cat_hair_length` = " . $db->quote($orgentry->copy_cat_hair_length) . ", 
                                `summary` = " . $summary_id . ",
                                `entry_show` = ".$entry_show.",
                                `entry_show_class` = ".$entry_show_class.",
                                `entry_age_years` = ".$age_years.",
								`entry_age_months`= ".$age_months."
                                 WHERE `entry_id` = " . $entry_id;

						$db->setQuery($query);
						$db->query();

						$query = "DELETE FROM `#__toes_entry_participates_in_congress` 
                                WHERE `entry_id` = {$entry_id}";
						$db->setQuery($query);
						$db->query();

						if ($entry->congress) {
							$new_congress = explode(',', $entry->congress);
							foreach ($congress_list as $congress) {
								if ($showdays[$i] == $congress->ring_show_day && in_array($congress->ring_id, $new_congress)) {
									$query = "INSERT INTO `#__toes_entry_participates_in_congress` 
                                            (`entry_id`, `congress_id`)
                                            VALUES (" . $db->quote($entry_id) . "," . $db->quote($congress->ring_id) . ")";
									$db->setQuery($query);
									$db->query();
								}
							}
						}
						$edit = true;

					} else {

						/* Calculate cat's age on show day and deciding its show_class */

						$query = "SELECT `show_day_date` FROM `#__toes_show_day` WHERE `show_day_id` = ".$showdays[$i];
						$db->setQuery($query);
						$show_day_date = $db->loadResult();

						$show_class = "";

						$query = "select `bs`.`breed_status` 
						FROM `#__toes_breed` AS `b`
						LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND ".$db->quote($show_day_date)." BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
						LEFT JOIN `#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
						WHERE `b`.`breed_id` = ".$cat_detail->cat_breed;

						$db->setQuery($query);
						$breed_status = $db->loadResult();

						$is_HHP = strpos(' '.$cat_detail->breed_name,'Household') ? true : false ;

						$age_years = 0;
						$age_months = 0;

						$showdate = new DateTime($show_day_date, new DateTimeZone('UTC'));
						$cat_dob = new DateTime($cat_detail->cat_date_of_birth, new DateTimeZone('UTC'));
						$interval = $showdate->diff($cat_dob);

						$age_years = intval($interval->format('%y'));
						$age_months = intval($interval->format('%m'));

						$is_kitten = false;
						$is_adult = false;

						if($age_years > 0) {
							$is_adult = true;
						} else {
							if($age_months >= 8) {
								$is_adult = true;
							} elseif($age_months >= 4 && $age_months < 8) {
								$is_kitten = true;
							}
						}

						if ( $entry->exh_only == 1) {
							$show_class = 'Ex Only';
						} else {
							if ( $breed_status == 'Non Championship'){  #this means HHP
								if ( $is_HHP == true) {    # this is the only non championship class
									if ( $is_kitten == true) {  # HHP Kitten
										if ( $cat_detail->breed_hair_length == 'LH') {
											$show_class = 'LH HHP Kitten';
										} else {
											$show_class = 'SH HHP Kitten';
										}
									} else {
										if ( $is_adult == true) {   #HHP
											if ( $cat_detail->breed_hair_length == 'LH'){
												$show_class = 'LH HHP';
											} else {
												$show_class = 'SH HHP';
											}
										} else {
											if ($age_months >=3) {
												$show_class = 'Ex Only';
											} else {
												$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
											}
										}
									}
								} else {
									$show_class = 'ERROR - 2 - Please check view <Full Entry>';
								}
							} elseif ( $breed_status == 'Championship') {
								if ( $cat_detail->cat_new_trait ) {
									if($is_kitten || $is_adult) {
										if ( $cat_detail->breed_hair_length == 'LH') {
											$show_class = 'LH NT';
										} else {
											$show_class = 'SH NT';
										} 
									} else {
										if ($age_months >=3) {
											$show_class = 'Ex Only';
										} else {
											$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
										}
									}
								} else {
									if ( $is_kitten == true) {
										if ( $cat_detail->breed_hair_length == 'LH') {
											$show_class = 'LH Kitten';
										} else {
											$show_class = 'SH Kitten';
										}
									} else {
										if ( $is_adult == true) { 
											if ( $cat_detail->breed_hair_length == 'LH') { 
												if( ($cat_detail->gender_short_name == 'M') || ($cat_detail->gender_short_name == 'F') ) {
													$show_class = 'LH Cat';
												 } else {
													$show_class = 'LH Alter';
												 }
											} else {
												if( ($cat_detail->gender_short_name == 'M') || ($cat_detail->gender_short_name == 'F') ) {
													$show_class = 'SH Cat';
												} else {
													$show_class = 'SH Alter';
												}
											}
										} else {
											if ($age_months >=3) {
												$show_class = 'Ex Only'; 
											} else {
												$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
											}
										}
									}
								}
							} else {  
								if( $breed_status == 'Advanced New Breed') {
									if($is_kitten || $is_adult) {
										if( $cat_detail->breed_hair_length == 'LH') {
											$show_class = 'LH ANB';
										} else {
											$show_class = 'SH ANB';
										}
									} else {
										if ($age_months >= 3){
											$show_class = 'Ex Only';
										} else {
											$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
										}
									}
								} else {
									if( $breed_status == 'Preliminary New Breed') {
										if($is_kitten || $is_adult) {
											if( $cat_detail->breed_hair_length == 'LH') {
												$show_class = 'LH PNB';
											} else {
												$show_class = 'SH PNB';
											}
										} else {
											if ($age_months >= 3) {
												$show_class = 'Ex Only';
											} else {
												$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
											}
										}
									} else {
										$show_class = 'Error - 4 - Please check view <Full Entry>';
									}
								}
							}
						}

						$entry_show = $entry->show_id;
						$entry_show_class = isset($show_classes[$show_class])?$show_classes[$show_class]:0;
						/* End - Age calculation */
						
						$entry_status = '1';

						if($show->show_use_waiting_list) {
							if ($isAlternative) {
								$am_waiting = false;
								$pm_waiting = false;

								if (in_array($showdays[$i], $entry->entry_for_AM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i], '1')) {
									$am_waiting = true;
								}

								if (in_array($showdays[$i], $entry->entry_for_PM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i], '2')) {
									$pm_waiting = true;
								}

								if (($am_waiting && $pm_waiting) || (!in_array($showdays[$i], $entry->entry_for_AM) && $pm_waiting) || (!in_array($showdays[$i], $entry->entry_for_PM) && $am_waiting)) {
									$entry_status = '11';
									if ($entry->exh_only) {
										if (!$entry->congress || ($entry->congress && !in_array($showdays[$i], $congress_showdays))) {
											$entry_status = '1';
										}
									}

									if ($entry_status == '11')
										continue;
								}
								else if ($am_waiting || $pm_waiting) {
									if ($am_waiting) {
										$entry->entry_for_AM = array_diff($entry->entry_for_AM, array($showdays[$i]));
									}

									if ($pm_waiting) {
										$entry->entry_for_PM = array_diff($entry->entry_for_PM, array($showdays[$i]));
									}
								}
							} else {
								if (!TOESHelper::getAvailableSpaceforDay($showdays[$i], null, null, $entry->placeholder_id)) {
									$entry_status = '11';

									if ($entry->exh_only) {
										if (!$entry->congress || ($entry->congress && !in_array($showdays[$i], $congress_showdays))) {
											$entry_status = '1';
										}
									}
								}
							}
						}

						$timestamp = '';
						if ($entry->placeholder_id) {
							$placeholder = TOESHelper::getPlaceholderDetailsByShowday($entry->placeholder_id, $showdays[$i]);
							if($placeholder) {
								if ($placeholder->placeholder_day_date_created) {
									$timestamp = $placeholder->placeholder_day_date_created;
								}
								if ($placeholder->placeholder_day_placeholder_status == 5) {
									$entry_status = '2';
								} else if ($placeholder->placeholder_day_placeholder_status == 2) {
									$entry_status = '2';
								} else {
									$entry_status = '1';
								}
							}
						}

						$show_details = TOESHelper::getShowDetails($entry->show_id);
						
						//
						if($entry->copy_cat_registration_number)
						$copy_cat_registration_number = $entry->copy_cat_registration_number;
						else
						$copy_cat_registration_number = $cat_detail->cat_registration_number;

						$query = "INSERT INTO `#__toes_entry` 
                                (`cat`, 
                                `show_day`, 
                                `entry_participates_AM`, 
                                `entry_participates_PM`, 
                                `status`,
                                `for_sale`, 
                                `exhibition_only`, 
                                `copy_cat_name`, 
                                `copy_cat_prefix`, 
                                `copy_cat_title`, 
                                `copy_cat_suffix`, 
                                `copy_cat_breed`, 
                                `copy_cat_category`, 
                                `copy_cat_division`, 
                                `copy_cat_color`, 
                                `copy_cat_date_of_birth`, 
                                `copy_cat_registration_number`, 
                                `copy_cat_gender`, 
                                `copy_cat_new_trait`, 
                                `copy_cat_sire_name`, 
                                `copy_cat_dam_name`, 
                                `copy_cat_breeder_name`, 
                                `copy_cat_owner_name`, 
                                `copy_cat_agent_name`,
                                `copy_cat_lessee_name`, 
                                `copy_cat_competitive_region`, 
                                `copy_cat_hair_length`, 
                                `summary`,
                                `late_entry`,
                                `entry_date_created`,
                                `entry_show`,
                                `entry_show_class`,
                                `entry_age_years`,
                                `entry_age_months`
                                )
                                VALUES (" . $db->quote($cat_detail->cat_id) . ","
								. $db->quote($showdays[$i]) . ","
								. $db->quote(in_array($showdays[$i], $entry->entry_for_AM) ? '1' : '0') . ","
								. $db->quote(in_array($showdays[$i], $entry->entry_for_PM) ? '1' : '0') . ","
								. $db->quote($entry_status) . ","
								. $db->quote($entry->for_sale) . ","
								. $db->quote($entry->exh_only) . ", "
								. $db->quote($cat_detail->cat_name) . ", "
								. $db->quote($cat_detail->cat_prefix) . ", "
								. $db->quote($cat_detail->cat_title) . ", "
								. $db->quote($cat_detail->cat_suffix) . ", "
								. $db->quote($cat_detail->cat_breed) . ", "
								. $db->quote($cat_detail->cat_category) . ", "
								. $db->quote($cat_detail->cat_division) . ", "
								. $db->quote($cat_detail->cat_color) . ", "
								. $db->quote($cat_detail->cat_date_of_birth) . ", "
								//. $db->quote($cat_detail->cat_registration_number) . ","
								. $db->quote($copy_cat_registration_number) . ","
								. $db->quote($cat_detail->cat_gender) . ", "
								. $db->quote($cat_detail->cat_new_trait) . ", "
								. $db->quote($cat_detail->cat_sire) . ", "
								. $db->quote($cat_detail->cat_dam) . ", "
								. $db->quote($cat_detail->cat_breeder) . ", "
								. $db->quote($cat_detail->cat_owner) . ", "
								. $db->quote($entry->agent_name) . ", "
								. $db->quote($cat_detail->cat_lessee) . ", "
								. $db->quote($cat_detail->cat_competitive_region) . ", "
								. $db->quote($cat_detail->cat_hair_length) . ", "
								. $summary_id . ", "
								. ($show_details != 'Open' ? 1 : 0) . ", "
								. ($timestamp ? $db->quote($timestamp) : "now()") . ", "
                                . $entry_show . ", "
                                . $entry_show_class . ", "
                                . $age_years . ", "
								. $age_months
								. " )";

						$db->setQuery($query);
						$db->query();

						$entry_id = $db->insertid();

						$send_notification = true;

						if ($entry_id && $entry->congress) {
							$new_congress = explode(',', $entry->congress);
							foreach ($congress_list as $congress) {
								if ($showdays[$i] == $congress->ring_show_day && in_array($congress->ring_id, $new_congress)) {
									$query = "INSERT INTO `#__toes_entry_participates_in_congress` 
                                            (`entry_id`, `congress_id`)
                                            VALUES (" . $db->quote($entry_id) . "," . $db->quote($congress->ring_id) . ")";
									$db->setQuery($query);
									$db->query();
								}
							}
						}
					}
				}
			}

			if($excluded_showdays) {
				$query = "DELETE FROM `#__toes_entry` WHERE `cat` = {$cat_detail->cat_id} AND `show_day` IN ( " . implode(',', $excluded_showdays) . " )";
				$db->setQuery($query);
				$db->query();
			}

			if ($entry->placeholder_id) {
				$query = "DELETE "
						. " FROM `#__toes_placeholder_day` "
						. " WHERE `placeholder_day_placeholder` = {$entry->placeholder_id}";

				$db->setQuery($query);
				$db->query();

				$query = "DELETE "
						. " FROM `#__toes_placeholder` "
						. " WHERE  `placeholder_id` = {$entry->placeholder_id}";
				$db->setQuery($query);
				$db->query();
			}

			if ($show->show_use_waiting_list && $check_waiting_list) {
				TOESHelper::checkWaitingList($entry->show_id);
			}

			/*
			if ($edit == true && $is_offcial) {
				unset($query);
				$query = TOESQueryHelper::getEntryFullViewQuery();
				$query->where('`es`.`summary_user` = ' . $summary->summary_user);
				$query->where('`e`.`entry_show` = ' . $summary->summary_show);
				$query->where('`estat`.`entry_status` IN ("Confirmed", "Confirmed & Paid")');

				$db->setQuery($query);
				$old_entries = $db->loadObjectList();

				if ($old_entries) {
					require_once JPATH_BASE . '/components/com_toes/models/entryclerk.php';
					$entryClerkmodel = new TOESModelEntryclerk();
					$entryClerkmodel->confirmEntries($entry->user_id, $entry->show_id, false);
				}
			}
			*/

			if ($edit == false && $send_notification) {
				// Notification mail to entryclerk
				if($entry->placeholder_id) {
					$mailTemplate = TOESMailHelper::getTemplate('placeholder_converted_to_entry_entry_clerk_notification');
				} else {
					$mailTemplate = TOESMailHelper::getTemplate('entry_added_entryclerk_notification');
				}

				if($mailTemplate) {
					$subject = $mailTemplate->mail_subject;
					$body = $mailTemplate->mail_body;
				} else {
					$subject = JText::_('COM_TOES_EC_ENTRY_NOTIFICATION_MAIL_SUBJECT');
					$body = JText::_('COM_TOES_EC_ENTRY_NOTIFICATION_MAIL_CONTENT');
				}

				//$show = TOESHelper::getShowDetails($entry->show_id);
				$userInfo = TOESHelper::getUserInfo($entry->user_id);

				$body = str_replace('[exhibitor]', $userInfo->name, $body);

				$body = str_replace('[City]', $show->address_city, $body);
				$body = str_replace('[, [State]]', $show->address_state ? ', ' . $show->address_state : '', $body);
				$body = str_replace('[Country]', $show->address_country, $body);

				$body = str_replace('[club name]', $show->club_name, $body);

				$start_date = date('d', strtotime($show->show_start_date));
				$start_date_month = date('M', strtotime($show->show_start_date));
				$start_date_year = date('Y', strtotime($show->show_start_date));

				$end_date = date('d', strtotime($show->show_end_date));
				$end_date_month = date('M', strtotime($show->show_end_date));
				$end_date_year = date('Y', strtotime($show->show_end_date));

				$show_date = $start_date_month . ' ' . $start_date;

				if ($end_date_year != $start_date_year) {
					$show_date .= ' ' . $start_date_year;
				}

				if ($end_date_month != $start_date_month) {
					if (date('t', strtotime($show->show_start_date)) != $start_date)
						$show_date .= ' - ' . date('t', strtotime($show->show_start_date));
					if ($end_date == '01')
						$show_date .= ', ' . $end_date_month . ' ' . $end_date;
					else
						$show_date .= ', ' . $end_date_month . ' 01 - ' . $end_date;
				} else {
					if ($start_date != $end_date)
						$show_date .= ' - ' . $start_date_month . ' ' . $end_date;
				}

				$show_date .= ' ' . $end_date_year;

				$body = str_replace('[showdates]', $show_date, $body);

				$subject = str_replace('[exhibitor]', $userInfo->name, $subject);
				$subject = str_replace('[club name]', $show->club_name, $subject);
				$subject = str_replace('[showdates]', $show_date, $subject);

				if ($show->show_use_club_entry_clerk_address) {
					$recipient = $show->show_email_address_entry_clerk;
				} else {
					$entryClerks = TOESHelper::getEntryClerks($entry->show_id);
					if($entryClerks) {
						foreach ($entryClerks as $entryClerk) {
							$recipient[] = $entryClerk->entry_clerk_email;
						}
					} else {
						$showManagers = TOESHelper::getShowManagers($entry->show_id);
						
						foreach ($showManagers as $showManager) {
							$recipient[] = $showManager->show_manager_email;
						}	
					}
				}
				
				/*
				$mail = JFactory::getMailer();
				$config = JFactory::getConfig();
				$fromname = $config->get('fromname');

				$params = JComponentHelper::getParams('com_toes');

				if ($params->get('send_bcc_emails') == 1) {
					$mail->addBCC($params->get('bcc_email'));
				}

				//$mail->SetFrom($fromemail, $fromname);
				$mail->SetFrom('noreply@i-tica.com', $fromname);
				$mail->setSubject($subject);
				$mail->setBody($body);
				$mail->addRecipient($recipient);
				$mail->IsHTML(TRUE);

				$mail->Send();
				*/
				if($entry->placeholder_id) {
					TOESMailHelper::sendMail('placeholder_converted_to_entry_entry_clerk_notification', $subject, $body, $recipient);
				} else {
					TOESMailHelper::sendMail('entry_added_entryclerk_notification', $subject, $body, $recipient);
				}
				
				//Notification mail to exhibitor
				if($entry->placeholder_id) {
					$mailTemplate = TOESMailHelper::getTemplate('placeholder_converted_to_entry_exhibitor_notification');
				} else {
					$mailTemplate = TOESMailHelper::getTemplate('entry_added_exhibitor_notification');
				}

				if($mailTemplate) {
					$subject = $mailTemplate->mail_subject;
					$body = $mailTemplate->mail_body;
				} else {
					$subject = JText::_('COM_TOES_NEW_ENTRY_RECEIPT_CONFIRMATION_TO_EXHIBITOR_SUBJECT');
					$body = JText::_('COM_TOES_NEW_ENTRY_RECEIPT_CONFIRMATION_TO_EXHIBITOR');
				}

				$subject = str_replace('[club name]', $show->club_name, $subject);
				$subject = str_replace('[showdates]', $show_date, $subject);


				$body = str_replace('[exhibitor]', $userInfo->name, $body);
				$body = str_replace('[cat]', $cat_detail->cat_name, $body);

				$body = str_replace('[City]', $show->address_city, $body);
				$body = str_replace('[, [State]]', $show->address_state ? ', ' . $show->address_state : '', $body);
				$body = str_replace('[Country]', $show->address_country, $body);

				$body = str_replace('[club name]', $show->club_name, $body);

				$url = JURI::getInstance();
				$show_link = $url->getScheme() . '://' . $url->getHost() . JRoute::_('index.php?option=com_toes&view=shows', false) . '#show' . $entry->show_id;

				$body = str_replace('[show_link]', $show_link, $body);

				if (is_array($recipient))
					$body = str_replace('[entry-clerk email]', $recipient[0], $body);
				else
					$body = str_replace('[entry-clerk email]', $recipient, $body);

				/*
				$mail = JFactory::getMailer();
				$mail->SetFrom('noreply@i-tica.com', $fromname);
				$mail->setSubject($subject);
				$mail->setBody($body);
				$mail->addRecipient($userInfo->email);
				$mail->IsHTML(TRUE);

				$mail->Send();
				*/
				if($entry->placeholder_id) {
					TOESMailHelper::sendMail('placeholder_converted_to_entry_exhibitor_notification', $subject, $body, $userInfo->email);
				} else {
					TOESMailHelper::sendMail('entry_added_exhibitor_notification', $subject, $body, $userInfo->email);
				}
			}
			
			//var_dump($entry);
			$HHP_breed_abbreviation = 'HHP';
			$db->setQuery("select `breed_id` from `#__toes_breed` where `breed_abbreviation` = ".$db->Quote($HHP_breed_abbreviation));
			$HHP_breed_id = $db->loadResult();
			$db->setQuery("select `cat_breed` from `#__toes_cat` where `cat_id` =".$entry->cat_id );
			$cat_breed = $db->loadResult();
                
                
			
			//update registration number if breed is not HHP
			
			if( $cat_breed != $HHP_breed_id &&  $entry->copy_cat_registration_number){
			$db->setQuery("select * from `#__toes_cat_registration_number` where `cat_registration_number_cat` =".$cat_detail->cat_id);	
			$cat_registration_number_record = $db->loadObject();
			if($cat_registration_number_record){
			$db->setQuery("UPDATE `#__toes_cat_registration_number` SET `cat_registration_number` =".$db->Quote($entry->copy_cat_registration_number)."
			where `cat_registration_number_id` =".$cat_registration_number_record->cat_registration_number_id)->execute();	
			}else{
			$db->setQuery("INSERT INTO `#__toes_cat_registration_number`(`cat_registration_number`,`cat_registration_number_organization`,`cat_registration_number_cat`)
			VALUES(".$db->Quote($entry->copy_cat_registration_number).",1,".$cat_detail->cat_id.")  ")->execute();	
				
			}
				
				
			}
			
			
			
		}
		 
		$session->clear('entry');

		// Clean the cache.
		$this->cleanCache();
		return true;
	}

	public function save_summary() {
		// Initialise variables;
		$app = JFactory::getApplication();

		$summary_id = $app->input->getInt('summary_id', 0);
		$single_cages = $app->input->getVar('single_cages');
		$double_cages = $app->input->getVar('double_cages');
		$personal_cage = $app->input->getVar('personal_cage');
		$grooming_space = $app->input->getVar('grooming_space');
		$benching_request = base64_decode($app->input->getVar('benching_request'));
		$remark = base64_decode($app->input->getVar('remark'));

		$db = JFactory::getDbo();
		$user = JFactory::getUser();

		$query = "SELECT * FROM `#__toes_summary` WHERE `summary_id` = {$summary_id}";
		$db->setQuery($query);
		$summary = $db->loadObject();

		$is_offcial = (TOESHelper::is_clubowner($user->id, TOESHelper::getClub($summary->summary_show)->club_id) || TOESHelper::is_showofficial($user->id, $summary->summary_show) || TOESHelper::isAdmin()) ? true : false;

		if ($summary->summary_user != $user->id && !$is_offcial) {
			$this->setError('COM_TOES_NO_PERMISSION');
			return false;
		}

		if ($summary_id) {
			unset($query);
			$query = "UPDATE `#__toes_summary` SET "
					. " summary_benching_request = " . $db->quote($benching_request) . ","
					. " summary_grooming_space = " . $db->quote($grooming_space ? 1 : 0) . ","
					. " summary_single_cages = " . $db->quote($single_cages) . ","
					. " summary_double_cages = " . $db->quote($double_cages) . ","
					. " summary_personal_cages = " . $db->quote($personal_cage ? 1 : 0) . ","
					. " summary_remarks = " . $db->quote($remark) . ","
					. " summary_status = (SELECT s.`summary_status_id` FROM `#__toes_summary_status` AS s WHERE s.`summary_status` = 'Updated') "
					. " WHERE summary_id = " . $db->quote($summary_id);

			$db->setQuery($query);
			if ($db->query()) {
				/*if ($is_offcial) {
					unset($query);
					$query = TOESQueryHelper::getEntryFullViewQuery();
					$query->where('`es`.`summary_user` = ' . $summary->summary_user);
					$query->where('`e`.`entry_show` = ' . $summary->summary_show);
					$query->where('`estat`.`entry_status` IN ("Confirmed", "Confirmed & Paid")');
			
					$db->setQuery($query);
					$old_entries = $db->loadObjectList();

					if ($old_entries) {
						require_once JPATH_BASE . '/components/com_toes/models/entryclerk.php';
						$entryClerkmodel = new TOESModelEntryclerk();
						$entryClerkmodel->confirmEntries($summary->summary_user, $summary->summary_show, false);
					}
				}*/
				return true;
			} else {
				$this->setError(JText::_('COM_TOES_ERROR_IN_SAVING_SUMMARY'));
				return false;
			}
		}
		$this->setError(JText::_('COM_TOES_ERROR_IN_SAVING_SUMMARY'));
		return false;
	}

	public function save_fees() {
		// Initialise variables;
		$app = JFactory::getApplication();
		$summary_id = $app->input->getInt('summary_id', 0);
		$summary_total_fees = $app->input->getVar('summary_total_fees');
		$summary_fees_paid = $app->input->getVar('summary_fees_paid');
		$summary_benching_area = base64_decode($app->input->getVar('summary_benching_area', ''));
		$summary_entry_clerk_note = base64_decode($app->input->getVar('summary_entry_clerk_note', ''));
		$summary_entry_clerk_private_note = base64_decode($app->input->getVar('summary_entry_clerk_private_note', ''));

		$db = JFactory::getDbo();
		$user = JFactory::getUser();

		$query = "SELECT * FROM `#__toes_summary` WHERE `summary_id` = {$summary_id}";
		$db->setQuery($query);
		$summary = $db->loadObject();

		if (!TOESHelper::is_showofficial($user->id, $summary->summary_show) && !TOESHelper::isAdmin()) {
			$this->setError('COM_TOES_NO_PERMISSION');
			return false;
		}

		if ($summary_id) {
			unset($query);
			$query = "UPDATE `#__toes_summary` SET "
					. " summary_benching_area = " . $db->quote($summary_benching_area) . ","
					. " summary_total_fees = " . $db->quote($summary_total_fees) . ","
					. " summary_fees_paid = " . $db->quote($summary_fees_paid) . ","
					. " summary_entry_clerk_note = " . $db->quote($summary_entry_clerk_note) . ","
					. " summary_entry_clerk_private_note = " . $db->quote($summary_entry_clerk_private_note)
					. " WHERE summary_id = " . $db->quote($summary_id);
			$db->setQuery($query);
			if ($db->query()) {
				return true;
			} else {
				$this->setError(JText::_('COM_TOES_ERROR_IN_SAVING_SUMMARY'));
				return false;
			}
		}
		$this->setError(JText::_('COM_TOES_ERROR_IN_SAVING_SUMMARY'));
		return false;
	}

	/**
	 * Custom clean cache method
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0) {
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}

	public function getShowclasses() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('show_class_id AS value, show_class AS text');
		$query->from('#__toes_show_class');
		$query->order('show_class_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_SHOW_CLASS')));
		return $options;
	}

	public function getCompetitiveregions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('competitive_region_id AS value, concat(competitive_region_name,\' (\',competitive_region_abbreviation,\')\') AS text');
		$query->from('#__toes_competitive_region');
		$query->order('competitive_region_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_COMPETITIVE_REGION')));
		return $options;
	}

	public function getHairlengths() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('cat_hair_length_id AS value, concat(cat_hair_length,\' (\',cat_hair_length_abbreviation,\')\') AS text');
		$query->from('#__toes_cat_hair_length');
		$query->order('cat_hair_length_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_HAIRLENGTH')));
		return $options;
	}

	public function getCategories() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('category_id AS value, category AS text');
		$query->from('#__toes_category');
		$query->where('category_organization = 1');
		$query->order('category ASC');
		$query->order('category_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_CATEGORY')));
		return $options;
	}

	public function getDivisions() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('division_id AS value, division_name AS text');
		$query->from('#__toes_division');
		$query->where('division_organization = 1');
		$query->order('division_name ASC');
		$query->order('division_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_DIVISION')));
		return $options;
	}

	public function getColors() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('color_id AS value, color_name AS text');
		$query->from('#__toes_color');
		$query->where('color_organization = 1');
		$query->order('color_name ASC');
		$query->order('color_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_COLOR')));
		return $options;
	}

	public function getBreeds() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('breed_id AS value, concat(breed_name,\' (\',breed_abbreviation,\')\') AS text');
		$query->from('#__toes_breed');
		$query->where('breed_organization = 1');
		$query->order('breed_name ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_BREED')));
		return $options;
	}

	public function getGenders() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('gender_id AS value, concat(gender_name,\' (\',gender_short_name,\')\') AS text');
		$query->from('#__toes_cat_gender');
		$query->order('gender_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_GENDER')));
		return $options;
	}

	public function getTitles() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('cat_title_id AS value, concat(cat_title,\' (\',cat_title_abbreviation,\')\') AS text');
		$query->from('#__toes_cat_title');
		$query->where('cat_title_organization = 1');
		$query->order('cat_title_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		//array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_TITLE')));
		return $options;
	}

	public function getPrefixes() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('cat_prefix_id AS value, concat(cat_prefix,\' (\',cat_prefix_abbreviation,\')\') AS text');
		$query->from('#__toes_cat_prefix');
		$query->where('cat_prefix_organization = 1');
		$query->order('cat_prefix_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		//array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_PREFIX')));
		return $options;
	}

	public function getSuffixes() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('cat_suffix_id AS value, concat(cat_suffix,\' (\',cat_suffix_abbreviation,\')\') AS text');
		$query->from('#__toes_cat_suffix');
		$query->where('cat_suffix_organization = 1');
		$query->order('cat_suffix_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		//array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_SUFFIX')));
		return $options;
	}

	public function saveEntryDetails() {
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$show_id = $app->input->getInt('show_id');
		$cat_id = $app->input->getInt('cat_id');
		$breed = $app->input->getInt('breed');
		$new_trait = $app->input->getInt('new_trait', 0);
		$category = $app->input->getInt('category');
		$division = $app->input->getInt('division');
		$color = $app->input->getInt('color');
		$cat_name = base64_decode($app->input->getVar('cat_name'));
		$gender = $app->input->getInt('gender');
		$hairlength = $app->input->getInt('hairlength');
		$rgnnumber = $app->input->getVar('rgnnumber');
		$dob = $app->input->getVar('dob');
		$prefix = $app->input->getInt('prefix');
		$title = $app->input->getInt('title');
		$suffix = $app->input->getInt('suffix');
		$sire = base64_decode($app->input->getVar('sire'));
		$dam = base64_decode($app->input->getVar('dam'));
		$breeder = base64_decode($app->input->getVar('breeder'));
		$owner = base64_decode($app->input->getVar('owner'));
		$lessee = base64_decode($app->input->getVar('lessee'));
		$agent = base64_decode($app->input->getVar('agent'));
		$region = $app->input->getInt('region');


		$query = "SELECT `entry_id` FROM `#__toes_entry` WHERE `entry_show` = {$show_id} AND `cat`= {$cat_id}";
		$db->setQuery($query);
		$entry_ids = $db->loadColumn();

		if ($entry_ids) {
			$query = "UPDATE `#__toes_entry` SET 
                    copy_cat_breed = " . $db->quote($breed) . ",
                    copy_cat_new_trait = " . $db->quote($new_trait) . ",
                    copy_cat_category = " . $db->quote($category) . ",
                    copy_cat_division = " . $db->quote($division) . ",
                    copy_cat_color = " . $db->quote($color) . ",
                    copy_cat_name = " . $db->quote($cat_name) . ",
                    copy_cat_gender = " . $db->quote($gender) . ",
					copy_cat_hair_length = " . $db->quote($hairlength) . ",
                    copy_cat_registration_number = " . $db->quote($rgnnumber) . ",
                    copy_cat_date_of_birth = " . $db->quote($dob) . ",
                    copy_cat_prefix = " . $db->quote($prefix) . ",
                    copy_cat_title = " . $db->quote($title) . ",
                    copy_cat_suffix = " . $db->quote($suffix) . ",
                    copy_cat_sire_name = " . $db->quote($sire) . ",
                    copy_cat_dam_name = " . $db->quote($dam) . ",
                    copy_cat_breeder_name = " . $db->quote($breeder) . ",
                    copy_cat_owner_name = " . $db->quote($owner) . ",
                    copy_cat_lessee_name = " . $db->quote($lessee) . ",
                    copy_cat_agent_name = " . $db->quote($agent) . ",
                    copy_cat_competitive_region = " . $db->quote($region) . "
                    WHERE entry_id IN (" . implode(',', $entry_ids) . ")";
			//echo nl2br(str_replace('#__', 'j35_', $query));
			$db->setQuery($query);
			if ($db->query()){
				
				foreach($entry_ids as $entry_id) {
					$this->updateEntryShowClass($entry_id);
				}

				return true;
			} else {
				$this->setError($db->getErrorMsg());
				return false;
			}
		} else {
			$this->setError(JText::_('ERROR_IN_SAVING_DETAILS'));
			return false;
		}
	}

	function updateEntryShowClass($entry_id) {
		
		$db = JFactory::getDbo();
		$entry = TOESHelper::getEntryFullDetail($entry_id);
		
		$query = "SELECT * FROM `#__toes_show_class`";
		$db->setQuery($query);
		$show_classes = $db->loadAssocList('show_class','show_class_id');

		/* Calculate cat's age on show day and deciding its show_class */

		$show_class = "";

		$query = "select `bs`.`breed_status` 
		FROM `#__toes_breed` AS `b`
		LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND ".$db->quote($entry->show_day_date)." BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
		LEFT JOIN `#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
		WHERE `b`.`breed_id` = ".$entry->copy_cat_breed;

		$db->setQuery($query);
		$breed_status = $db->loadResult();

		$is_HHP = strpos(' '.$entry->breed_name,'Household') ? true : false ;

		$age_years = 0;
		$age_months = 0;

		$showdate = new DateTime($entry->show_day_date, new DateTimeZone('UTC'));
		$cat_dob = new DateTime($entry->copy_cat_date_of_birth, new DateTimeZone('UTC'));
		$interval = $showdate->diff($cat_dob);

		$age_years = intval($interval->format('%y'));
		$age_months = intval($interval->format('%m'));

		$is_kitten = false;
		$is_adult = false;

		if($age_years > 0) {
			$is_adult = true;
		} else {
			if($age_months >= 8) {
				$is_adult = true;
			} elseif($age_months >= 4 && $age_months < 8) {
				$is_kitten = true;
			}
		}

		if ( $entry->exh_only == 1) {
			$show_class = 'Ex Only';
		} else {
			if ( $breed_status == 'Non Championship'){  #this means HHP
				if ( $is_HHP == true) {    # this is the only non championship class
					if ( $is_kitten == true) {  # HHP Kitten
						if ( $entry->cat_hair_length_abbreviation == 'LH') {
							$show_class = 'LH HHP Kitten';
						} else {
							$show_class = 'SH HHP Kitten';
						}
					} else {
						if ( $is_adult == true) {   #HHP
							if ( $entry->cat_hair_length_abbreviation == 'LH'){
								$show_class = 'LH HHP';
							} else {
								$show_class = 'SH HHP';
							}
						} else {
							if ($age_months >=3) {
								$show_class = 'Ex Only';
							} else {
								$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
							}
						}
					}
				} else {
					$show_class = 'ERROR - 2 - Please check view <Full Entry>';
				}
			} elseif ( $breed_status == 'Championship') {
				if ( $entry->copy_cat_new_trait ) {
					if($is_kitten || $is_adult) {
						if ( $entry->cat_hair_length_abbreviation == 'LH') {
							$show_class = 'LH NT';
						} else {
							$show_class = 'SH NT';
						} 
					} else {
						if ($age_months >=3) {
							$show_class = 'Ex Only';
						} else {
							$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
						}
					}
				} else {
					if ( $is_kitten == true) {
						if ( $entry->cat_hair_length_abbreviation == 'LH') {
							$show_class = 'LH Kitten';
						} else {
							$show_class = 'SH Kitten';
						}
					} else {
						if ( $is_adult == true) { 
							if ( $entry->cat_hair_length_abbreviation == 'LH') { 
								if( ($entry->gender_short_name == 'M') || ($entry->gender_short_name == 'F') ) {
									$show_class = 'LH Cat';
								 } else {
									$show_class = 'LH Alter';
								 }
							} else {
								if( ($entry->gender_short_name == 'M') || ($entry->gender_short_name == 'F') ) {
									$show_class = 'SH Cat';
								} else {
									$show_class = 'SH Alter';
								}
							}
						} else {
							if ($age_months >=3) {
								$show_class = 'Ex Only'; 
							} else {
								$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
							}
						}
					}
				}
			} else {  
				if( $breed_status == 'Advanced New Breed') {
					if($is_kitten || $is_adult) {
						if( $entry->cat_hair_length_abbreviation == 'LH') {
							$show_class = 'LH ANB';
						} else {
							$show_class = 'SH ANB';
						}
					} else {
						if ($age_months >= 3){
							$show_class = 'Ex Only';
						} else {
							$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
						}
					}
				} else {
					if( $breed_status == 'Preliminary New Breed') {
						if($is_kitten || $is_adult) {
							if( $entry->cat_hair_length_abbreviation == 'LH') {
								$show_class = 'LH PNB';
							} else {
								$show_class = 'SH PNB';
							}
						} else {
							if ($age_months >= 3) {
								$show_class = 'Ex Only';
							} else {
								$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
							}
						}
					} else {
						$show_class = 'Error - 4 - Please check view <Full Entry>';
					}
				}
			}
		}

		$entry_show = $entry->show_id;
		$entry_show_class = isset($show_classes[$show_class])?$show_classes[$show_class]:0;
		/* End - Age calculation */

		$query = $db->getQuery(true);
		$query->update('#__toes_entry');
		$query->set('entry_show_class = '.$entry_show_class);
		$query->set('entry_age_years = '.$age_years);
		$query->set('entry_age_months = '.$age_months);
		$query->where('entry_id = '.$entry_id);

		$db->setQuery($query);
		$db->execute();
		
		return true;
	}
	
	function saveFilterCriteria() {
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$ring_index = $app->input->getInt('ring_index');
		$ring_id = $app->input->getInt('ring_id');

		$breed_filter = $app->input->getInt('breed_filter');
		$gender_filter = $app->input->getInt('gender_filter');
		$newtrait_filter = $app->input->getInt('newtrait_filter');
		$hairlength_filter = $app->input->getInt('hairlength_filter');
		$category_filter = $app->input->getInt('category_filter');
		$division_filter = $app->input->getInt('division_filter');
		$color_filter = $app->input->getInt('color_filter');
		$title_filter = $app->input->getInt('title_filter');
		$manual_filter = $app->input->getInt('manual_filter');

		$class_value = $app->input->getVar('class_value');
		$breed_value = $app->input->getVar('breed_value');
		$gender_value = $app->input->getVar('gender_value');
		$hairlength_value = $app->input->getVar('hairlength_value');
		$category_value = $app->input->getVar('category_value');
		$division_value = $app->input->getVar('division_value');
		$color_value = $app->input->getVar('color_value');
		$title_value = $app->input->getVar('title_value');

		$cwd_value = $app->input->getVar('cwd_value');

		if ($ring_id) {
			TOESHelper::deleteCongressFilters($ring_id);

			$ring = TOESHelper::getRingDetails($ring_id);

			$session = JFactory::getSession();
			$session->set('filters_changed', $ring->ring_show);

			$query = $db->getQuery(true);
			$query->insert('#__toes_congress');
			$query->set('congress_name=' . $db->quote($ring->ring_name));
			$query->set('congress_breed_switch=' . $breed_filter);
			$query->set('congress_gender_switch=' . $gender_filter);
			$query->set('congress_new_trait_switch=' . $newtrait_filter);
			$query->set('congress_hair_length_switch=' . $hairlength_filter);
			$query->set('congress_category_switch=' . $category_filter);
			$query->set('congress_division_switch=' . $division_filter);
			$query->set('congress_color_switch=' . $color_filter);
			$query->set('congress_title_switch=' . $title_filter);
			$query->set('congress_manual_select_switch=' . $manual_filter);
			$query->set('congress_id=' . $ring_id);

			$db->setQuery($query);
			$db->query();

			$values = explode(',', $class_value);
			foreach ($values as $value) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_competitive_class');
				$query->set('congress_competitive_class_competitive_class=' . $value);
				$query->set('congress_competitive_class_congress=' . $ring_id);

				$db->setQuery($query);
				$db->query();
			}

			if ($breed_filter && $breed_value) {
				$values = explode(',', $breed_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_breed');
					$query->set('congress_breed_breed=' . $value);
					$query->set('congress_breed_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}

			if ($gender_filter && $gender_value) {
				$values = explode(',', $gender_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_gender');
					$query->set('congress_gender_gender=' . $value);
					$query->set('congress_gender_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}

			if ($hairlength_filter && $hairlength_value) {
				$values = explode(',', $hairlength_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_hair_length');
					$query->set('congress_hair_length_hair_length=' . $value);
					$query->set('congress_hair_length_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}

			if ($category_filter && $category_value) {
				$values = explode(',', $category_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_category');
					$query->set('congress_category_category=' . $value);
					$query->set('congress_category_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}

			if ($division_filter && $division_value) {
				$values = explode(',', $division_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_division');
					$query->set('congress_division_division=' . $value);
					$query->set('congress_division_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}

			if ($color_filter && $color_value) {
				$values = explode(',', $color_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_color');
					$query->set('congress_color_color=' . $value);
					$query->set('congress_color_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}

			if ($color_filter && $cwd_value) {
				$values = explode(',', $cwd_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_color_wildcard');
					$query->set('congress_color_wildcard_wildcard=' . $db->quote($value));
					$query->set('congress_color_wildcard_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}

			if ($title_filter && $title_value) {
				$values = explode(',', $title_value);
				foreach ($values as $value) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_congress_title');
					$query->set('congress_title_title=' . $value);
					$query->set('congress_title_congress=' . $ring_id);

					$db->setQuery($query);
					$db->query();
				}
			}
		} else if ($ring_index) {
			$session = JFactory::getSession();

			$filter = new stdClass();

			$filter->ring_index = $ring_index;
			$filter->ring_id = $ring_id;

			$filter->breed_filter = $breed_filter;
			$filter->gender_filter = $gender_filter;
			$filter->newtrait_filter = $newtrait_filter;
			$filter->hairlength_filter = $hairlength_filter;
			$filter->category_filter = $category_filter;
			$filter->division_filter = $division_filter;
			$filter->color_filter = $color_filter;
			$filter->title_filter = $title_filter;
			$filter->manual_filter = $manual_filter;

			$filter->class_value = $class_value;
			$filter->breed_value = $breed_value;
			$filter->gender_value = $gender_value;
			$filter->hairlength_value = $hairlength_value;
			$filter->category_value = $category_value;
			$filter->division_value = $division_value;
			$filter->color_value = $color_value;
			$filter->title_value = $title_value;

			$filter->cwd_value = $cwd_value;

			$temp_filters = array();
			if ($session->has('congress_filters')) {
				$str = $session->get('congress_filters');
				$filters = unserialize($str);

				$flag = false;
				foreach ($filters as $item) {
					if ($item->ring_index == $ring_index) {
						$temp_filters[] = $filter;
						$flag = true;
					} else
						$temp_filters[] = $item;
					if (!$flag)
						$temp_filters[] = $filter;
				}
			}
			else {
				$temp_filters[] = $filter;
			}
			$filters = $temp_filters;

			$serialize_filters = serialize($filters);
			$session->set('congress_filters', $serialize_filters);
		} else {
			$this->setError(JText::_('ERROR_IN_SAVING_DETAILS'));
			return false;
		}
		return true;
	}

	public function saveUser() {
		 
		$db = JFactory::getDbo();
		$params = JComponentHelper::getParams('com_users');
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();

		$firstname = $app->input->getVar('firstname');
		$lastname = $app->input->getVar('lastname');
		$username = $app->input->getVar('username');
		$email = $app->input->getVar('email');
		if (!$email) {
			$email = 'noreply@i-tica.com';
		}
		$address1 = $app->input->getVar('address1');
		$address2 = $app->input->getVar('address2');
		$address3 = $app->input->getVar('address3');
		$city = $app->input->getVar('city');
		$zip = $app->input->getVar('zip');
		$state = $app->input->getVar('state');
		$country = $app->input->getVar('country');
		$phonenumber = $app->input->getVar('phonenumber');
		
		// sandy hack
		$tica_region_id = $app->input->getInt('tica_region');
		if($tica_region_id){
		$db->setQuery("select `competitive_region_abbreviation` from `#__toes_competitive_region` where `competitive_region_id` =".$tica_region_id);	
		$tica_region = $db->loadResult();
		}else{
		$tica_region = '';	
		}
		

		// Get required system objects
		$user = new JUser();

		jimport('joomla.user.helper');
		JTable::addIncludePath(JPATH_ROOT . '/libraries/joomla/database/table ');

		// If user registration is not allowed, show 403 not authorized.
		if ($params->get('allowUserRegistration') == '0') {
			$msg = JText::_('COM_TOES_ACCESS_FORBIDDEN');
			$this->setError($msg);
			return false;
		}

		// Get the groups the user should be added to after registration.
		$groups = array();

		// Get the default new user group, Registered if not specified.
		$system = $params->get('new_usertype', 2);

		$groups[] = $system;


		$lang = JFactory::getLanguage();
		$lang->load('com_users');

		$password = JUserHelper::genRandomPassword();
		$salt = JUserHelper::genRandomPassword(32);
		$crypt = JUserHelper::getCryptedPassword($password, $salt);
		$pwd = $crypt . ':' . $salt;

		// Set some initial user values
		$user->set('id', 0);
		$user->set('name', $firstname . ' ' . $lastname);
		$user->set('username', $username);
		$user->set('email', $email);
		$user->set('password', $pwd);
		$user->set('groups', $groups);
		$user->set('registerDate', date('Y-m-d H:i:s'));

		// If user activation is turned on, we need to set the activation information
		$useractivation = $params->get('useractivation');
		if ($useractivation == '1') {
			$user->set('activation', md5(JUserHelper::genRandomPassword()));
			$user->set('block', '1');
		}

		// If there was an error with registration, set the message and display form
		if (!$user->save()) {
			$this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
			return false;
		}

		if ($email == 'noreply@i-tica.com') {
			$user->email = 'user' . $user->id . '@i-tica.com';
			$user->save();
		}

		$query = "INSERT INTO `#__comprofiler` (`id`, `user_id`, `firstname`, `lastname`, `cb_address1`, `cb_address2`, `cb_address3`, `cb_city`, `cb_zip`, `cb_state`, `cb_country`, `cb_phonenumber`, `cb_tica_region`,  `cb_privacy`)
            VALUES ("
				. $user->id . ","
				. $user->id . ","
				. $db->quote($firstname) . ","
				. $db->quote($lastname) . ","
				. $db->quote($address1) . ","
				. $db->quote($address2) . ","
				. $db->quote($address3) . ","
				. $db->quote($city) . ","
				. $db->quote($zip) . ","
				. $db->quote($state) . ","
				. $db->quote($country) . ","
				. $db->quote($phonenumber) . ","
				. $db->quote($tica_region) . ","
				. "0)";
		$db->setQuery($query);
		if (!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		}
		// sandy hack for tica_guid
		while(true){
		$guid = JUserHelper::genRandomPassword(16);	
		$hexastr = strtoupper(bin2hex($guid));
		$hexa = substr($hexastr,0,8).'-'.substr($hexastr,8,4).'-'.substr($hexastr,12,4).'-'.substr($hexastr,16,4).'-'.substr($hexastr,20,12);
		$db->setQuery("select count(*) from `#__comprofiler` where `cb_tica_guid` =".$db->Quote($hexa));
		$exists = $db->loadResult();
		if(!$exists || $exists <= 0){
			$q = "UPDATE `#__comprofiler` SET `cb_tica_guid` =".$db->Quote($hexa)." where `id` =".$user->id;				
			$db->setQuery($q)->execute();	 
			break;
		}			
		}

		// Compile the notification mail values.
		$data = $user->getProperties();
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$data['siteurl'] = JUri::root();
		$data['password_clear'] = $password;

		// Handle account activation/confirmation emails.
		if ($useractivation == 2) {
			// Set the link to confirm the user email.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			$emailSubject = JText::sprintf(
							'COM_USERS_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']
			);

			$emailBody = JText::sprintf(
							'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY', $data['name'], $data['sitename'], $data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'], $data['siteurl'], $data['username'], $data['password_clear']
			);
		} elseif ($useractivation == 1) {
			// Set the link to activate the user account.
			$uri = JURI::getInstance();
			$base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			$emailSubject = JText::sprintf(
							'COM_USERS_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']
			);

			$emailBody = JText::sprintf(
							'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY', $data['name'], $data['sitename'], $data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'], $data['siteurl'], $data['username'], $data['password_clear']
			);
		} else {

			$emailSubject = JText::sprintf(
							'COM_USERS_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']
			);

			$emailBody = JText::sprintf(
							'COM_USERS_EMAIL_REGISTERED_BODY', $data['name'], $data['sitename'], $data['siteurl']
			);
		}

		// Send the registration email.
		$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

		//Send Notification mail to administrators
		if (($params->get('useractivation') < 2) && ($params->get('mail_to_admin') == 1)) {
			$emailSubject = JText::sprintf(
							'COM_USERS_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']
			);

			$emailBodyAdmin = JText::sprintf(
							'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY', $data['name'], $data['username'], $data['siteurl']
			);

			// get all admin users
			$query = 'SELECT name, email, sendEmail' .
					' FROM #__users' .
					' WHERE sendEmail=1';

			$db->setQuery($query);
			$rows = $db->loadObjectList();

			// Send mail to all superadministrators id
			foreach ($rows as $row) {
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);

				// Check for an error.
				if ($return !== true) {
					$this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
					return false;
				}
			}
		}
		// Check for an error.
		if ($return !== true) {
			$this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

			// Send a system message to administrators receiving system mails
			$db = JFactory::getDBO();
			$q = "SELECT id
				FROM #__users
				WHERE block = 0
				AND sendEmail = 1";
			$db->setQuery($q);
			$sendEmail = $db->loadColumn();
			if (count($sendEmail) > 0) {
				$jdate = new JDate();
				// Build the query to add the messages
				$q = "INSERT INTO " . $db->quoteName('#__messages') . " (" . $db->quoteName('user_id_from') .
						", " . $db->quoteName('user_id_to') . ", " . $db->quoteName('date_time') .
						", " . $db->quoteName('subject') . ", " . $db->quoteName('message') . ") VALUES ";
				$messages = array();

				foreach ($sendEmail as $userid) {
					$messages[] = "(" . $userid . ", " . $userid . ", '" . $jdate->toSql() . "', '" . JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT') . "', '" . JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username']) . "')";
				}
				$q .= implode(',', $messages);
				$db->setQuery($q);
				$db->query();
			}
			return false;
		}

		return $user->id;
	}

	function reject_entry() {
		$app = JFactory::getApplication();
		$entry_id = $app->input->getInt('entry_id');
		$reason = base64_decode($app->input->getVar('reason'));

		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		$is_allowed = false;

		$org_entry = TOESHelper::getEntryDetails($entry_id);
		$cat_id = $org_entry->cat;
		$show_id = $org_entry->entry_show;
		$org_status = $org_entry->entry_status;

		$is_official = (TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin()) ? true : false;

		if ($is_official || ($org_status == 'Cancelled' || $org_status == 'Cancelled & Confirmed'))
			$is_allowed = true;
		else {
			$this->setError(JText::_('COM_TOES_NOAUTH'));
			return false;
		}

		$sql = $db->getQuery(true);
		$sql->select('e.show_day');
		$sql->from('`#__toes_entry` AS e');
		$sql->where('e.`cat` = ' . $cat_id);
		$sql->where('e.`entry_show` = ' . $show_id);
		$sql->where('e.`status` = ' . $org_entry->status);

		$db->setQuery($sql);
		$showdays = $db->loadColumn();

		if ($is_allowed) {
			$rejected_showdays = array();
			for ($i = 0; $i < count($showdays); $i++) {
				$query = $db->getQuery(true);

				$query->update('#__toes_entry AS e');
				$query->set('e.`status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = ' . $db->quote('Rejected') . ')');

				$query->where('e.`cat` = ' . $cat_id);
				$query->where('e.`show_day` = ' . $showdays[$i]);

				$db->setQuery($query);
				if ($db->query()) {
					$rejected_showdays[] = $showdays[$i];

					$query = $db->getQuery(true);
					$query->select('e.`entry_id`');
					$query->from('#__toes_entry AS e');
					$query->where('e.`cat` = ' . $cat_id);
					$query->where('e.`show_day` = ' . $showdays[$i]);

					$db->setQuery($query);
					$updated_entry_id = $db->loadResult();

					$db->setQuery("SELECT `entry_refusal_reason_entry` FROM `#__toes_entry_refusal_reason` WHERE `entry_refusal_reason_entry`=" . $updated_entry_id);
					if (!$db->loadResult()) {
						$query = $db->getQuery(true);
						$query->insert('#__toes_entry_refusal_reason');
						$query->set('`entry_refusal_reason_entry` = ' . $updated_entry_id);
						$query->set('`entry_refusal_reason_reason` = ' . $db->quote($reason));

						$db->setQuery($query);
						$db->query();
					} else {
						$query = $db->getQuery(true);
						$query->update('#__toes_entry_refusal_reason');
						$query->set('`entry_refusal_reason_reason` = ' . $db->quote($reason));
						$query->where('`entry_refusal_reason_entry` = ' . $updated_entry_id);

						$db->setQuery($query);
						$db->query();
					}
				}
			}

			$sql = "SELECT GROUP_CONCAT(DISTINCT(LEFT(DATE_FORMAT(show_day_date,'%W'),3)) ORDER BY show_day_date) AS showdays FROM `#__toes_show_day` WHERE `show_day_id` IN (" . implode(',', $rejected_showdays) . ")";
			$db->setQuery($sql);
			$days = $db->loadResult();

			$exhibitor = TOESHelper::getUserInfo($org_entry->summary_user);
			$cat = TOESHelper::getCatDetails($cat_id);
			$show = TOESHelper::getShowDetails($show_id);
			$club = TOESHelper::getClub($show_id);
			$entryClerks = TOESHelper::getEntryClerks($show_id);
				
			$mailTemplate = TOESMailHelper::getTemplate('entry_rejected_exhibitor_notification');

			if($mailTemplate) {
				$subject = $mailTemplate->mail_subject;
				$body = $mailTemplate->mail_body;
			} else {
				$subject = JText::_('COM_TOES_REJECT_ENTRY_EMAIL_SUBJECT');
				$body = JText::_('COM_TOES_REJECT_EMAIL_BODY');
			}

			$body = str_replace('[Firstname]', $exhibitor->firstname, $body);
			$body = str_replace('[Lastname]', $exhibitor->lastname, $body);

			$body = str_replace('[cat name]', $cat->cat_name, $body);

			$body = str_replace('[club]', $club->club_name, $body);

			$start_date = date('d', strtotime($show->show_start_date));
			$start_date_month = date('M', strtotime($show->show_start_date));
			$start_date_year = date('Y', strtotime($show->show_start_date));

			$end_date = date('d', strtotime($show->show_end_date));
			$end_date_month = date('M', strtotime($show->show_end_date));
			$end_date_year = date('Y', strtotime($show->show_end_date));

			$show_date = $start_date_month . ' ' . $start_date;

			if ($end_date_year != $start_date_year) {
				$show_date .= ' ' . $start_date_year;
			}

			if ($end_date_month != $start_date_month) {
				if (date('t', strtotime($show->show_start_date)) != $start_date)
					$show_date .= ' - ' . date('t', strtotime($show->show_start_date));
				if ($end_date == '01')
					$show_date .= ', ' . $end_date_month . ' ' . $end_date;
				else
					$show_date .= ', ' . $end_date_month . ' 01 - ' . $end_date;
			} else {
				if ($start_date != $end_date)
					$show_date .= ' - ' . $start_date_month . ' ' . $end_date;
			}

			$show_date .= ' ' . $end_date_year;

			$body = str_replace('[startdate][ - [enddate]]', $show_date, $body);

			$body = str_replace('[show location]', $show->Show_location, $body);

			$entryClerkEmails = "";
			$entryClerk = new stdClass();
			if ($show->show_use_club_entry_clerk_address) {
				$entryClerkEmails = '<a href="mailto:' . $show->show_email_address_entry_clerk . '">' . $show->show_email_address_entry_clerk . '</a><br/>';
				$entryClerk->name = $show->show_email_address_entry_clerk;
				$entryClerk->email = $show->show_email_address_entry_clerk;
			} else {
				foreach ($entryClerks as $clerk) {
					$entryClerkEmails .= '<a href="mailto:' . $clerk->entry_clerk_email . '">' . $clerk->entry_clerk_email . '</a><br/>';
					$entryClerk->name = $clerk->entry_clerk_email;
					$entryClerk->email = $clerk->entry_clerk_email;
				}
			}
			$body = str_replace('([entryclerkemailaddress])', $entryClerkEmails, $body);

			$body = str_replace('[showdaylist]', $days, $body);

			$body = str_replace('[rejection reason]', $reason, $body);
			
			/*
			$mail = JFactory::getMailer();
			$mail->SetFrom($entryClerk->email, $entryClerk->name);
			$mail->setSubject($subject);
			$mail->setBody($body);
			$mail->addRecipient($exhibitor->email, $exhibitor->firstname . ' ' . $exhibitor->lastname);
			$mail->IsHTML(TRUE);

			if (!$mail->Send())
				$this->setError($mail->ErrorInfo);
			*/

			if(!TOESMailHelper::sendMail('entry_rejected_exhibitor_notification', $subject, $body, $exhibitor->email, $exhibitor->firstname . ' ' . $exhibitor->lastname))
			{
				$this->setError(JText::_('COM_TOES_MAIL_SENDING_ERROR'));
			}

			if ($show->show_use_waiting_list) {
				TOESHelper::checkWaitingList($show_id);
			}

			return true;
		} else
			return false;
	}

	function delete_entry() {
		$app = JFactory::getApplication();
		$entry_id = $app->input->getInt('entry_id');

		$user = JFactory::getUser();
		$db = JFactory::getDbo();

		$is_allowed = false;

		$org_entry = TOESHelper::getEntryDetails($entry_id);
		$cat_id = $org_entry->cat;
		$show_id = $org_entry->entry_show;
		$org_status = $org_entry->entry_status;
		$summary_id = $org_entry->summary;

		$is_official = (TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin()) ? true : false;

		if ($is_official)
			$is_allowed = true;
		else {
			$this->setError(JText::_('COM_TOES_NOAUTH'));
			return false;
		}

		$sql = $db->getQuery(true);
		$sql->select('e.`entry_id`');
		$sql->from('`#__toes_entry` AS e');
		$sql->where('e.`cat` = ' . $cat_id);
		$sql->where('e.`entry_show` = ' . $show_id);
		$sql->where('e.`status` = ' . $org_entry->status);

		$db->setQuery($sql);
		$entries_to_delete = $db->loadColumn();

		$sql = $db->getQuery(true);
		$sql->delete('`#__toes_entry_participates_in_congress`');
		$sql->where('`entry_id` in (' . implode(',', $entries_to_delete) . ')');
		$db->setQuery($sql);
		$db->query();

		$sql = $db->getQuery(true);
		$sql->select('show_day, entry_id');
		$sql->from('`#__toes_entry`');
		$sql->where('`entry_id` in (' . implode(',', $entries_to_delete) . ')');
		$db->setQuery($sql);
		$show_days = $db->loadObjectList('entry_id');
		
		$sql = $db->getQuery(true);
		$sql->delete('`#__toes_entry`');
		$sql->where('`entry_id` in (' . implode(',', $entries_to_delete) . ')');
		$db->setQuery($sql);
		if ($db->query()) {
			
			/* log for deleted entry*/
			foreach ($entries_to_delete as $entryid)
			{
				$query = $db->getQuery(true);
				$query->insert('`#__toes_log_entries`');
				$query->set('`entry_id` = '.$entryid);
				$query->set('`cat` = '.$cat_id);
				$query->set('`exhibitor` = '.$org_entry->summary_user);
				$query->set('`show_day` = '.$show_days[$entryid]->show_day);
				
				$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ENTRYCLERK')));
				$query->set('`changed_by` = '.$user->id);
				$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
				$db->setQuery($query);
				$db->query();
			}
			
			$query = $db->getQuery(true);
			$query->select('count(e.`entry_id`)');
			$query->from('`#__toes_entry` AS e');
			$query->where('e.`summary` = ' . $summary_id);
			$db->setQuery($query);
			$entries = $db->loadResult();

			$query = $db->getQuery(true);
			$query->select('count(p.`placeholder_id`)');
			$query->from('`#__toes_placeholder` AS p');
			$query->join('LEFT', '#__toes_summary AS s ON ( s.summary_user = p.placeholder_exhibitor AND s.summary_show = p.placeholder_show )');
			$query->where('s.`summary_id` = ' . $summary_id);
			$db->setQuery($query);
			$placeholders = $db->loadResult();

			if (!$entries && !$placeholders) {
				$query = "DELETE "
						. " FROM `#__toes_summary` "
						. " WHERE `summary_id` = {$summary_id}";
				$db->setQuery($query);
				if($db->query()){
					/* log for deleted summary*/
					$query = $db->getQuery(true);
					$query->insert('`#__toes_log_summaries`');
					$query->set('`summary_id` = '.$summary_id);
					$query->set('`exhibitor` = '.$org_entry->summary_user);
					$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ENTRYCLERK')));
					$query->set('`changed_by` = '.$user->id);
					$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
					$db->setQuery($query);
					$db->query();
				}
			}

			TOESHelper::checkWaitingList($show_id);
			return true;
		} else
			$this->setError($db->getErrorMsg());
		return false;
	}

    public function getTicaRegions() {
		$db = JFactory::getDBO();
		$query = "SELECT competitive_region_id AS `key`, CONCAT(competitive_region_name,' (',competitive_region_abbreviation,')') AS value
			FROM #__toes_competitive_region
			ORDER BY competitive_region_name";       

		$db->setQuery($query);
		$regions = $db->loadObjectList();
		return $regions;
    }
    public function getDocumentsforEntry(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		//$entry_id = $app->input->getInt('entry_id');
		//$show_id = $app->input->getInt('show_id');
		$cat = $app->input->getInt('cat');
		if(!$cat)
		$cat = $app->input->getInt('cat_id');
		$query = "select d.*,o.recognized_registration_organization_name,o.recognized_registration_organization_affiliation,t.allowed_registration_document_name_language_constant,t.allowed_registration_document_title_language_constant from `#__toes_cat_document` as d 
		JOIN `#__toes_recognized_registration_organization` as o on o.recognized_registration_organization_id = d.cat_document_registration_document_organization_id
		JOIN `#__toes_allowed_registration_document_type` as t on t.allowed_registration_document_id = d.cat_document_registration_document_type_id
		where d.`cat_document_cat_id`=".$cat;
		$db->setQuery($query);
		return $db->loadObjectList(); 		
	}
	public function getentrystatus(){
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$entry_id = $app->input->getInt('entry_id');
		$db->setQuery("select es.entry_status from `#__toes_entry` as e JOIN `#__toes_entry_status` as es ON
		e.status = es.entry_status_id where e.entry_id =".$entry_id);
		return $db->loadResult();		
	}
	 public function getOrganization() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);


        $query->select('recognized_registration_organization_id AS value, concat(recognized_registration_organization_name,\' (\',recognized_registration_organization_abbreviation,\')\') AS text');
        $query->from('#__toes_recognized_registration_organization');
        $query->order('recognized_registration_organization_name ASC');

        // Get the options.
        $db->setQuery($query);

        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_REGISTRATION_ORGANISATION')));
        return $options;
    }
    public function getRegNumberFormats() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('rnf_id, regformat, type');
        $query->from('#__toes_regnumber_formats');
        $query->order('rnf_id ASC');

        $db->setQuery($query);
        $formats = $db->loadObjectList();

        return $formats;
    }
    function getDocument_type_labels(){
		$db = JFactory::getDbo();
		$db->setQuery("select allowed_registration_document_id as value,allowed_registration_document_title_language_constant
		as text from `#__toes_allowed_registration_document_type` order by allowed_registration_document_id");
		$typelist =  $db->loadObjectList();
		if(count($typelist)){
		foreach($typelist as $t)
		$t->text = JText::_($t->text); 
		}
		return $typelist;
		
	}
    function getDocument_weights(){
		$db = JFactory::getDbo();
		$db->setQuery("select allowed_registration_document_id as id,allowed_registration_document_weight as weight from `#__toes_allowed_registration_document_type` order by allowed_registration_document_id");
		$weightlist =  $db->loadObjectList();
		 
		return $weightlist;
		
	}
}
