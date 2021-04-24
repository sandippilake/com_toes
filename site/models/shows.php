<?php

/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Toes Component Shows Model
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESModelShows extends JModelList {

	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 */
	public function __construct($config = array()) {
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id',
				'club_abbreviation',
				'address_country',
				'address_state',
				'address_city'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null) {
		$club = $this->getUserStateFromRequest($this->context . '.filter.club', 'club_filter', '');
		$this->setState('filter.club', $club);
		
		if($club) {
			$item = TOESHelper::getClubDetails($club);
			$this->setState('filter.club_name', $item->club_name);
		}

		$country = $this->getUserStateFromRequest($this->context . '.filter.country', 'country_filter', '');
		$this->setState('filter.country', $country);
		
		if($country) {
			//$item = TOESHelper::getCountryDetails($country);
			//$this->setState('filter.country_name', $item->name);
			$this->setState('filter.country_name', $country);
		}

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'state_filter', '');
		$this->setState('filter.state', $state);
		
		if($state) {
			//$item = TOESHelper::getStateDetails($state);
			//$this->setState('filter.state_name', $item->name);
			$this->setState('filter.state_name', $state);
		}

		$city = $this->getUserStateFromRequest($this->context . '.filter.city', 'city_filter', '');
		$this->setState('filter.city', $city);
		
		if($city) {
			//$item = TOESHelper::getCityDetails($city);
			//$this->setState('filter.city_name', $item->name);
			$this->setState('filter.city_name', $city);
		}
		
		$region = $this->getUserStateFromRequest($this->context . '.filter.region', 'region_filter', '');
		$this->setState('filter.region', $region);
		
		 

		$entries = $this->getUserStateFromRequest($this->context . '.filter.entries', 'filter_my_entries', 0);
		$this->setState('filter.entries', $entries);

		$show_date_status = $this->getUserStateFromRequest($this->context . '.filter.show_date_status_filter', 'show_date_status_filter', 0);
		$this->setState('filter.show_date_status_filter', $show_date_status);

		parent::populateState('show_start_date', 'ASC');
	}

	public function getItems() {
		$db = $this->getDbo();

		$query = $this->getListQuery();
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach($items as $item)
		{
			if((int)$item->show_has_500_mile_conflict == 0)continue; 
			$query = $db->getQuery(true);
			$query->select("sca.existing_show_id as conf_show_id,conf_c.club_id as conf_club_id,sca.approval as clubapproval,rd.approval as rdapproval,rd.reject as rdreject");
			$query->select("conf_cr.competitive_region_regional_director as conf_competitive_region_regional_director");
			$query->select("conf_cr.competitive_region_id as conf_competitive_region_id");
			$query->from("#__toes_show_club_approval as sca");
			$query->join("left","`#__toes_club_organizes_show` AS `conf_cos` ON `conf_cos`.`show` = `sca`.`existing_show_id`");
			$query->join("left","`#__toes_club` AS `conf_c` ON `conf_c`.`club_id` = `conf_cos`.`club`");
			$query->join("left","`#__toes_competitive_region` AS `conf_cr` ON `conf_cr`.`competitive_region_id` = `conf_c`.`club_competitive_region`");
			$query->join("left","`#__toes_show_regional_director_approval` AS `rd` ON `rd`.`new_conflicting_show_id` = `sca`.`new_conflicting_show_id` AND `rd`.`existing_show_id` = `sca`.`existing_show_id` ");
			//$query->where("sca.current_show_id =".$item->show_id." AND (`sca`.`approval` = 0 OR `rd`.`reject` = 0 OR `rd`.`approval` = 0)");
			$query->where("sca.new_conflicting_show_id =".$item->show_id);
			//echo $query;
			$db->setQuery($query);
			$conflicted_shows = $db->loadObjectList();
			if($conflicted_shows)
			{
				$item->conflicted_shows = $conflicted_shows;
			}	
			//echo $query;
		}
		
		return $items;
	}

	protected function getListQuery() {
		// Create a new query object.
		$db = $this->getDbo();
		$user = JFactory::getUser();
		
		$query = TOESQueryHelper::getShowViewQuery();
		
		if (!TOESHelper::is_regionaldirector($user->id) && !(TOESHelper::isAdmin())) {
			$query->join('left', '`#__toes_club_official` AS co ON co.`club` = c.`club_id`');
			$query->join('left', '`#__toes_show_has_official` AS so ON so.`show` = s.`show_id`');
			if ($user->id)
				$query->where('(`ss`.`show_status` != "Planned" OR (`ss`.`show_status` = "Planned" AND (co.`user` = ' . $user->id . ' OR so.`user` = ' . $user->id . ')))');
			else
				$query->where('`ss`.`show_status` != "Planned"');
		}
			$query->select("`srd`.`new_conflicting_show_id`,`srd`.`existing_show_id` AS `conf_showid`,`srd`.`approval` AS `rd_approval`,`sc`.`approval`as `sc_approval`,`s`.`show_has_500_mile_conflict`");
			$query->join("left","`#__toes_show_regional_director_approval` AS `srd` ON (`srd`.`new_conflicting_show_id` = `s`.`show_id` )");
			$query->join("left","`#__toes_show_club_approval` AS `sc` ON (`sc`.`new_conflicting_show_id` = `s`.`show_id` ) ");
			

		// Filter by club
		$club = $this->getState('filter.club');
		if ($club) {
			$query->where('c.club_id = ' . $db->quote($club));
		}

		// Filter by country
		$country = $this->getState('filter.country');
		if ($country) {
			$query->where('va.address_country = ' . $db->quote($country));
		}

		// Filter by state
		$state = $this->getState('filter.state');
		if ($state) {
			$query->where('va.address_state = ' . $db->quote($state));
		}

		// Filter by city
		$city = $this->getState('filter.city');
		if ($city) {
			$query->where('va.address_city = ' . $db->quote($city));
		}
		
		// Filter by region
		$region = $this->getState('filter.region');
		if ($region) {
			$query->where('c.club_competitive_region = ' . $db->quote($region));
		}

		// Filter by shows which user subscribed or entered cat
		$entries = $this->getState('filter.entries');
		if ($entries == 1) {
			$query->join('left', '`#__toes_summary` AS smry ON smry.`summary_show` = s.`show_id`');
			$query->where('smry.summary_user = ' . $user->id);
		} elseif ($entries == 2) {
			$query->join('left', '`#__toes_user_subcribed_to_show` AS sbcr ON sbcr.`user_subcribed_to_show_show` = s.`show_id`');
			$query->where('sbcr.`user_subcribed_to_show_user` = ' . $user->id);
		}

		// Filter for Past/Future/All shows
		$show_date_status = $this->getState('filter.show_date_status_filter');
		if ($show_date_status) {
			switch ($show_date_status) {
				case 'all':
					break;
				case 'past':
					$query->where('s.show_end_date < CURDATE() - INTERVAL 2 DAY');
					break;
				default:
					$query->where('s.show_end_date >= CURDATE() - INTERVAL 2 DAY');
			}
		} else
			$query->where('s.show_end_date >= CURDATE() - INTERVAL 2 DAY');


		// Add the list ordering clause.
		$query->group('s.show_id');
		$query->order('s.show_start_date ASC, s.show_id');
		//echo $query; 
		//die;
		//echo nl2br(str_replace('#_', 'j35', $query));
		return $query;
	}

	
	function updateStatus($show_id, $status, $club_id = null) {
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$isAdmin = TOESHelper::isAdmin();
		$is_allowed = false;

		$query = $db->getQuery(true);

		$show = TOESHelper::getShowDetails($show_id);

		if (!$club_id)
			$club_id = TOESHelper::getClub($show_id)->club_id;

		$query->select('ss.`show_status`');
		$query->from('#__toes_show_status AS ss');
		$query->join('LEFT', '#__toes_show AS s ON s.`show_status` = ss.`show_status_id`');
		$query->where('s.`show_id` = ' . $show_id);

		$db->setQuery($query);
		$org_status = $db->loadResult();

		switch ($status) {
			case 'Approved':
			case 'Rejected':
				if (TOESHelper::is_regionaldirector($user->id) || $isAdmin)
					$is_allowed = true;
				else
					$this->setError(JText::_('COM_TOES_NOAUTH'));
				break;
			case 'Cancelled':
				if (TOESHelper::is_clubowner($user->id, $club_id) || $isAdmin)
					$is_allowed = true;
				else
					$this->setError(JText::_('COM_TOES_NOAUTH'));
				break;
			case 'Open':
			case 'Closed':
				if (TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, $club_id) || $isAdmin)
					$is_allowed = true;
				else
					$this->setError(JText::_('COM_TOES_NOAUTH'));
				break;
			default:
				$is_allowed = true;
				break;
		}

		if ($is_allowed) {
			if ($status == "Cancelled") {
				if ($org_status == "Planned" || $org_status == "Rejected") {

					$query = "DELETE "
							. " FROM `#__toes_ring` "
							. " WHERE ring_show_day in ( SELECT `show_day_id` FROM `#__toes_show_day` WHERE `show_day_show` = {$show_id} )";

					$db->setQuery($query);
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
					}

					$query = "DELETE "
							. " FROM `#__toes_show_day` "
							. " WHERE `show_day_show` = {$show_id} ";

					$db->setQuery($query);
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
					}

					$query = "DELETE "
							. " FROM `#__toes_show` "
							. " WHERE `show_id` = {$show_id} ";

					$db->setQuery($query);
					if ($db->query()){
						/* log for deleted show*/
						$query = $db->getQuery(true);
						$query->insert('`#__toes_log_shows`');
						$query->set('`show_id` = '.$show_id);
						if($isAdmin) {
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ADMIN')));
						} else {
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_CLUB_OFFICIAL')));
						}
						$query->set('`changed_by` = '.$db->quote($user->id));
						$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
						$db->setQuery($query);
						$db->query();
						
						return true;
					} else {
						$this->setError($db->getErrorMsg());
					}
					if ($this->getErrors()) {
						return false;
					}
				}
			}

			if ($status == 'Approved')
			{
				$approval_val = '1';
				$query1 = $db->getQuery(true);
				$query1 = "update `#__toes_show_regional_director_approval` set 
							`approval`=".$db->quote($approval_val)." 
							where existing_show_id=".$db->quote($show_id)." AND `new_conflicting_show_id`=".$db->quote($show_id);
				$db->setQuery($query1);
				$db->query();
		
				$query = $db->getQuery(true);
				$query->select('sca.*');
				$query->from('#__toes_show_club_approval as sca');
				$query->where('sca.new_conflicting_show_id ='.$show_id.' AND sca.approval = 0');
				$db->setQuery($query);
				$scaresult = $db->loadObject();
				
				$query1 = $db->getQuery(true);
				$query1->select('rd.*');
				$query1->from('#__toes_show_regional_director_approval as rd');
				$query1->where('rd.new_conflicting_show_id ='.$show_id.' AND rd.approval=0');
				$db->setQuery($query1);
				$rdresult = $db->loadObject();
				
				if($scaresult || $rdresult)
				{
					return true;
				}
						
			}


			$query = $db->getQuery(true);

			$query->update('#__toes_show');
			$query->set('`show_status` = (SELECT `show_status_id` FROM `#__toes_show_status` WHERE `show_status` = ' . $db->quote($status) . ')');
			$query->where('`show_id` = ' . $show_id);
			$db->setQuery($query);
			
			if ($db->query()) {
				$config = JFactory::getConfig();
				$fromname = $config->get('fromname');
				$fromemail = $config->get('mailfrom');

				if($status == "Cancelled"){
					/* log for cancelled show*/
					$query = $db->getQuery(true);
					$query->insert('`#__toes_log_shows`');
					$query->set('`show_id` = '.$show_id);
					if($isAdmin) {
						$query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_ADMIN')));
					} else {
						$query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_CLUB_OFFICIAL')));
					}
					$query->set('`changed_by` = '.$user->id);
					$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
					$db->setQuery($query);
					$db->query();
				}
				
				if ($status == 'Approved' || $status == 'Rejected') {
					
						$mailTemplate = TOESMailHelper::getTemplate('show_approval_notification');

						if($mailTemplate) {
							$subject = $mailTemplate->mail_subject;
							$body = $mailTemplate->mail_body;
						} else {
							$subject = JText::_('COM_TOES_SHOW_APPROVAL_NOTIFICATION_SUBJECT');
							$body = JText::_('COM_TOES_SHOW_APPROVAL_NOTIFICATION_MESSAGE');
						}

						$subject = str_replace('[action]', $status, $subject);

						$body = str_replace('[action]', $status, $body);
						$body = str_replace('[startdate]', $show->show_start_date, $body);
						$body = str_replace('[enddate]', $show->show_end_date, $body);
						$body = str_replace('[location]', $show->Show_location, $body);

						$entryClerks = TOESHelper::getEntryClerks($show_id);
						$showmanagers = TOESHelper::getShowManagers($show_id);

						if ($show->show_use_club_entry_clerk_address) {
							$entryClerk = new stdClass();
							$entryClerk->entry_clerk_name = JText::_("COM_TOES_SHOW_ENTRYCLERK");
							$entryClerk->entry_clerk_email = $show->show_email_address_entry_clerk;
							$entryClerks = array();
							$entryClerks[] = $entryClerk;
						}

						if ($show->show_use_club_show_manager_address) {
							$showmanager = new stdClass();
							$showmanager->show_manager_name = JText::_("COM_TOES_SHOW_SHOWMANAGER");
							$showmanager->show_manager_email = $show->show_email_address_show_manager;
							$showmanagers = array();
							$showmanagers[] = $showmanager;
						}

						foreach ($entryClerks as $usr) {
							/*
							$mail = JFactory::getMailer();

							$mail->SetFrom($fromemail, $fromname);
							$mail->setSubject($subject);
							$mail->setBody(str_replace('[firstname]', $usr->entry_clerk_name, $body));
							$mail->addRecipient($usr->entry_clerk_email);
							$mail->IsHTML(TRUE);

							$mail->Send();
							*/
							$user_message = str_replace('[firstname]', $usr->entry_clerk_name, $body);
							TOESMailHelper::sendMail('show_approval_notification', $subject, $user_message, $usr->entry_clerk_email);
						}

						foreach ($showmanagers as $usr) {
							/*
							$mail = JFactory::getMailer();

							$mail->SetFrom($fromemail, $fromname);
							$mail->setSubject($subject);
							$mail->setBody(str_replace('[firstname]', $usr->show_manager_name, $body));
							$mail->addRecipient($usr->show_manager_email);
							$mail->IsHTML(TRUE);

							$mail->Send();
							*/
							$user_message = str_replace('[firstname]', $usr->show_manager_email, $body);
							TOESMailHelper::sendMail('show_approval_notification', $subject, $user_message, $usr->show_manager_email);
						}
					
				}

				if ($status == 'Closed') {
					$entries = TOESHelper::getShowFinalEntriesCount($show_id);
					$exonly_entries = TOESHelper::getShowFinalExOnlyEntriesCount($show_id);

					$entries_cost = $show->show_cost_per_entry * ($entries - $exonly_entries);
					$min = ($entries_cost < $show->show_maximum_cost) ? $entries_cost : $show->show_maximum_cost;
					$total_cost = ( ( $min - $show->show_cost_fixed_rebate) * (1 - ($show->show_cost_procentual_rebate / 100)) );

					$query = $db->getQuery(true);
					$query->update('`#__toes_show`');
					$query->set('`show_cost_total_entries` = ' . $entries);
					$query->set('`show_cost_ex_only_entries` = ' . $exonly_entries);
					$query->set('`show_total_cost` = ' . $total_cost);
					$query->where('`show_id` = ' . $show_id);

					$db->setQuery($query);
					$db->query();
				}

				// Log changes
				$date_changed = 0;
				$location_changed = 0;
				$format_changed = 0;
				$status_changed = 1;
				$judges_changed = 0;
				$rings_changed = 0;
				$desc_changed = 0;

				if ($date_changed || $location_changed || $status_changed || $format_changed || $judges_changed || $rings_changed || $desc_changed) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_show_changes');
					$query->set('show_changes_show=' . $show_id);
					$query->set('show_changes_dates_changed=' . $date_changed);
					$query->set('show_changes_location_changed=' . $location_changed);
					$query->set('show_changes_show_status=' . $status_changed);
					$query->set('show_changes_show_format_changed=' . $format_changed);
					$query->set('show_changes_judges_changed=' . $judges_changed);
					$query->set('show_changes_rings_changed=' . $rings_changed);
					$query->set('show_changes_description_changed=' . $desc_changed);
					$query->set('show_changes_last_changed_on = NOW()');
					$query->set('show_changes_last_changed_by=' . $user->id);

					$db->setQuery($query);
					$db->query();

					$mailTemplate = TOESMailHelper::getTemplate('show_update_notification');

					if($mailTemplate) {
						$subject = $mailTemplate->mail_subject;
						$body = $mailTemplate->mail_body;
					} else {
						$subject = JText::_('COM_TOES_SHOW_UPDATE_EMAIL_SUBJECT');
						$body = JText::_('COM_TOES_SHOW_UPDATE_EMAIL_BODY');
					}

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

					$body = str_replace('[date_change]', '', $body);
					$body = str_replace('[location_change]', '', $body);

					$text = str_replace('[show status]', $show->show_status, JText::_('COM_TOES_SHOW_UPDATE_EMAIL_STATUS_CHANGES'));
					$body = str_replace('[status_change]', $text, $body);

					$body = str_replace('[format_change]', '', $body);
					$body = str_replace('[desc_change]', '', $body);
					$body = str_replace('[rings_change]', '', $body);

					$users = TOESHelper::getSubscribedUsers($show_id);

					foreach ($users as $usr) {
						/*
						$mail = JFactory::getMailer();

						$mail->SetFrom($fromemail, $fromname);
						$mail->setSubject($subject);
						$mail->setBody(str_replace('[firstname]', ($usr->firstname) ? $usr->firstname : $usr->name, $body));
						$mail->addRecipient($usr->email);
						$mail->IsHTML(TRUE);

						$mail->Send();
						*/
						$user_message = str_replace('[firstname]', ($usr->firstname) ? $usr->firstname : $usr->name, $body);
						TOESMailHelper::sendMail('show_update_notification', $subject, $user_message, $usr->email);
					}
				}

				return true;
			} else {
				$this->setError($db->getErrorMsg());
				return false;
			}
		}
		return false;
	}

	function getEntriesNeedsConfirmation($show_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('`e`.`entry_id`');
		$query->from('`#__toes_entry` AS `e`');
		$query->join('left', '`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)');
		$query->where('`estat`.`entry_status` = ' . $db->quote('Accepted'));
		$query->where('`e`.`entry_show` = ' . $show_id);

		$db->setQuery($query);
		$entries = $db->loadObjectList();

		if ($entries)
			return true;
		else
			return false;
	}

	function delete($show_id) {
		$db = JFactory::getDbo();

		$query = "SELECT * FROM #__toes_ring WHERE ring_show = $show_id";
		$db->setQuery($query);
		$rings = $db->loadColumn();

		foreach ($rings as $ring) {
			if ($ring->ring_format == 3)
				TOESHelper::deleteCongressFilters($ring->ring_id);
		}

		$query = "DELETE FROM #__toes_ring WHERE ring_show = $show_id";
		$db->setQuery($query);
		$db->query();

		$query = "DELETE FROM #__toes_show_day WHERE show_day_show = $show_id";
		$db->setQuery($query);
		$db->query();
		
		// delete from conflicted shows if 
		$query = "delete from `#__toes_show_club_approval` where `new_conflicting_show_id` =".$show_id
		." OR `existing_show_id` =".$show_id;
		$db->setQuery($query);
		$db->execute();
		$query = "delete from `#__toes_show_regional_director_approval` where `new_conflicting_show_id` =".$show_id
		." OR `existing_show_id` =".$show_id;
		$db->setQuery($query);
		$db->execute();

		$query = "DELETE FROM #__toes_show WHERE show_id = $show_id";
		$db->setQuery($query);
		if ($db->query()){
			/* log for deleted show*/
			
			$query = $db->getQuery(true);
			$query->insert('`#__toes_log_shows`');
			$query->set('`show_id` = '.$show_id);
			$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ADMIN')));
			$query->set('`changed_by` = '.$db->quote($user->id));
			$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
			$db->setQuery($query);
			$db->query();
			return true;
		} else {
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	function lockCatalog($show_id, $lock) {
		$db = JFactory::getDbo();

		if($lock == '1') {
			$query = $db->getQuery(true);
			$query->select('`e`.`entry_id`');
			$query->from("`#__toes_entry` AS `e`");
			$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
			$query->where('( `estat`.`entry_status` = '.$db->quote('Accepted').' 
							OR `estat`.`entry_status` = '.$db->quote('New').' 
							OR `estat`.`entry_status` = '.$db->quote('Confirmed').' 
							OR `estat`.`entry_status` = '.$db->quote('Confirmed & Paid').')');        
			$query->where('(`e`.`catalog_number` = 0 OR `e`.`catalog_number` IS NULL)');
			$query->where('`e`.`entry_show` = ' . $show_id);
			$db->setQuery($query);
			$entries = $db->loadColumn();
			if($entries) {
				$this->setError(JText::_("COM_TOES_LOCK_CATALOG_ERROR"));
				return false;
			} 
		}

		$query = $db->getQuery(true);
		$query->update('#__toes_show');
		$query->set('show_lock_catalog = ' . $lock);
		$query->where('show_id = ' . $show_id);

		$db->setQuery($query);
		if ($db->query()) {
			return true;
		} else {
			$this->setError($db->getErrorMsg());
		}
	}

	function lockLatepages($show_id, $lock) {
		$db = JFactory::getDbo();

		if($lock == '1') {
			$query = $db->getQuery(true);
			$query->select('`e`.`entry_id`');
			$query->from("`#__toes_entry` AS `e`");
			$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
			$query->where('( `estat`.`entry_status` = '.$db->quote('Accepted').' 
							OR `estat`.`entry_status` = '.$db->quote('New').' 
							OR `estat`.`entry_status` = '.$db->quote('Confirmed').' 
							OR `estat`.`entry_status` = '.$db->quote('Confirmed & Paid').')');        
			$query->where('(`e`.`catalog_number` = 0 OR `e`.`catalog_number` IS NULL)');
			$query->where('`e`.`entry_show` = ' . $show_id);
			$db->setQuery($query);
			$entries = $db->loadColumn();
	
			$query = $db->getQuery(true);
			$query->select('`p`.`placeholder_id`');
			$query->from("`#__toes_placeholder` AS `p`");
			$query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
			$query->join('LEFT', '#__toes_entry_status AS estat ON estat.entry_status_id = pd.placeholder_day_placeholder_status');
			$query->where('( `estat`.`entry_status` = '.$db->quote('Accepted').' 
						OR `estat`.`entry_status` = '.$db->quote('New').' 
						OR `estat`.`entry_status` = '.$db->quote('Confirmed').' 
						OR `estat`.`entry_status` = '.$db->quote('Confirmed & Paid').')');
			$query->where('`p`.`placeholder_show` = ' . $show_id);
			$db->setQuery($query);
			$placeholders = $db->loadColumn();
	
			if($entries || $placeholders) {
				$this->setError(JText::_("COM_TOES_LOCK_LATE_PAGES_ERROR"));
				return false;
			}
		}
		$query = $db->getQuery(true);
		$query->update('#__toes_show');
		$query->set('show_lock_late_pages = ' . $lock);
		$query->where('show_id = ' . $show_id);

		$db->setQuery($query);
		if ($db->query()) {
			return true;
		} else {
			$this->setError($db->getErrorMsg());
		}
	}

	function sync_db($action, $show_id = 0) {
		if ($action == 'show' && $show_id == 0)
			echo JText::_('COM_TOES_ERROR_IN_SYNC_SHOW_ID_REQUIRED');

		$result = '';

		switch ($action) {
			case 'common':
				$result = $this->sync_common_tables();
				break;
			case 'show':
				$result = $this->sync_show_tables($show_id);
				break;
			default:
				echo JText::_('COM_TOES_SYNC_ERROR');
				$result = false;
		}

		return $result;
	}

	function sync_common_tables() {
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_toes');

		$excluded_admins = $user->id . ',' . $params->get('admin_users_to_skip', '0');

		/* Connect to production DB */
		$options = array();
		$options['host'] = $params->get('prod_db_host');
		$options['user'] = $params->get('prod_db_user');
		$options['password'] = $params->get('prod_db_pass');
		$options['database'] = $params->get('prod_db_name');
		$options['prefix'] = $params->get('prod_db_prefix');

		$prod_db = JDatabase::getInstance($options);
		;

		if ($prod_db instanceof Exception) {
			echo 'Database Error: ' . (string) $prod_db;
			return false;
		}

		if ($prod_db->getErrorNum() > 0) {
			echo sprintf('Database connection error (%d): %s', $prod_db->getErrorNum(), $prod_db->getErrorMsg());
			return false;
		}

		/* Connect to test server DB */
		$options = array();
		$options['host'] = $params->get('test_db_host');
		$options['user'] = $params->get('test_db_user');
		$options['password'] = $params->get('test_db_pass');
		$options['database'] = $params->get('test_db_name');
		$options['prefix'] = $params->get('test_db_prefix');

		$db = JDatabase::getInstance($options);
		;

		if ($db instanceof Exception) {
			echo 'Database Error: ' . (string) $db;
			return false;
		}

		if ($db->getErrorNum() > 0) {
			echo sprintf('Database connection error (%d): %s', $db->getErrorNum(), $db->getErrorMsg());
			return false;
		}

		/* USERS table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__users');
		$query->where('id NOT IN (' . $excluded_admins . ')');
		$query->order('id ASC');

		$prod_db->setQuery($query);
		$prod_users = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__users');
		$query->where('id NOT IN (' . $excluded_admins . ')');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_users as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__users');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}

			$query = $db->getQuery(true);
			$query->update('#__users');
			$query->set('email = ' . $db->quote('test.toes@e-ware.be'));
			//$query->where('id NOT IN ('.$excluded_admins.')');
			$db->setQuery($query);
			$db->query();
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* USER GROUP MAP table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__user_usergroup_map');
		$query->where('user_id NOT IN (' . $excluded_admins . ')');
		$query->order('user_id ASC');

		$prod_db->setQuery($query);
		$prod_users_mapping = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__user_usergroup_map');
		$query->where('user_id NOT IN (' . $excluded_admins . ')');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_users_mapping as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__user_usergroup_map');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* COMPROFILER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__comprofiler');
		$query->where('id NOT IN (' . $excluded_admins . ')');
		$query->order('id ASC');

		$prod_db->setQuery($query);
		$prod_cb_users = $prod_db->loadObjectList();


		$query = $db->getQuery(true);
		$query->delete('#__comprofiler');
		$query->where('id NOT IN (' . $excluded_admins . ')');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_cb_users as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__comprofiler');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* CAT table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_cat');

		$prod_db->setQuery($query);
		$prod_cats = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_cat');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_cats as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_cat');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* CAT REGISTRATION NUMBER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_cat_registration_number');

		$prod_db->setQuery($query);
		$prod_cat_registration_numbers = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_cat_registration_number');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_cat_registration_numbers as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_cat_registration_number');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* CAT to CAT relation table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_cat_relates_to_cat');

		$prod_db->setQuery($query);
		$prod_cat_to_cat = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_cat_relates_to_cat');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_cat_to_cat as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_cat_relates_to_cat');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* CAT to USER relations table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_cat_relates_to_user');

		$prod_db->setQuery($query);
		$prod_cat_to_user = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_cat_relates_to_user');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_cat_to_user as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_cat_relates_to_user');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* VENUE table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_venue');

		$prod_db->setQuery($query);
		$prod_venues = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_venue');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_venues as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_venue');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* ADDRESS table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_address');

		$prod_db->setQuery($query);
		$prod_addresses = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_address');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_addresses as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_address');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* SHOW table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_show');

		$prod_db->setQuery($query);
		$prod_shows = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_show');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_shows as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_show');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}


		/* CLUB table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_club');

		$prod_db->setQuery($query);
		$prod_clubs = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_club');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_clubs as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_club');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* CLUB officials table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_club_official');

		$prod_db->setQuery($query);
		$prod_club_officials = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_club_official');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_club_officials as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_club_official');
				foreach ($item as $key => $value) {
					$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		/* Club organizes shows table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_club_organizes_show');

		$prod_db->setQuery($query);
		$prod_club_organizes_shows = $prod_db->loadObjectList();

		$query = $db->getQuery(true);
		$query->delete('#__toes_club_organizes_show');
		$db->setQuery($query);
		if ($db->query()) {
			foreach ($prod_club_organizes_shows as $item) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_club_organizes_show');
				foreach ($item as $key => $value) {
					$query->set('`' . $key . '`' . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		} else {
			echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
			return false;
		}

		return true;
	}

	function sync_show_tables($show_id) {
		$user = JFactory::getUser();
		$params = JComponentHelper::getParams('com_toes');

		/* Connect to production DB */
		$options = array();
		$options['host'] = $params->get('prod_db_host');
		$options['user'] = $params->get('prod_db_user');
		$options['password'] = $params->get('prod_db_pass');
		$options['database'] = $params->get('prod_db_name');
		$options['prefix'] = $params->get('prod_db_prefix');

		$prod_db = JDatabase::getInstance($options);
		;

		if ($prod_db instanceof Exception) {
			echo 'Database Error: ' . (string) $prod_db;
			return false;
		}

		if ($prod_db->getErrorNum() > 0) {
			echo sprintf('Database connection error (%d): %s', $prod_db->getErrorNum(), $prod_db->getErrorMsg());
			return false;
		}

		/* Connect to test server DB */
		$options = array();
		$options['host'] = $params->get('test_db_host');
		$options['user'] = $params->get('test_db_user');
		$options['password'] = $params->get('test_db_pass');
		$options['database'] = $params->get('test_db_name');
		$options['prefix'] = $params->get('test_db_prefix');

		$db = JDatabase::getInstance($options);
		;

		if ($db instanceof Exception) {
			echo 'Database Error: ' . (string) $db;
			return false;
		}

		if ($db->getErrorNum() > 0) {
			echo sprintf('Database connection error (%d): %s', $db->getErrorNum(), $db->getErrorMsg());
			return false;
		}

		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_show');
		$query->where('show_id = ' . $show_id);
		$prod_db->setQuery($query);
		$prod_show = $prod_db->loadObject();

		if (!$prod_show) {
			echo 'Show is not available on production.';
			return false;
		}

		$query = $db->getQuery(true);
		$query->delete('#__toes_placeholder_day');
		$query->where('placeholder_day_placeholder IN (SELECT placeholder_id FROM #__toes_placeholder WHERE placeholder_show = ' . $show_id . ')');
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_placeholder');
		$query->where('placeholder_show = ' . $show_id);
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->select('entry_id');
		$query->from('#__toes_entry');
		$query->where('show_day IN (SELECT show_day_id FROM #__toes_show_day WHERE show_day_show = ' . $show_id . ')');
		$db->setQuery($query);
		$entries = $db->loadColumn();

		$query = $db->getQuery(true);
		$query->delete('#__toes_entry_refusal_reason');
		$query->where('entry_refusal_reason_entry IN (' . implode(',', $entries) . ')');
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_entry_participates_in_congress');
		$query->where('entry_id IN (' . implode(',', $entries) . ')');
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_entry');
		$query->where('entry_id IN (' . implode(',', $entries) . ')');
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_summary');
		$query->where('summary_show = ' . $show_id);
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->select('ring_id');
		$query->from('#__toes_ring');
		$query->where('ring_show = ' . $show_id);
		$query->where('ring_format = 3');
		$db->setQuery($query);
		$rings = $db->loadObjectList();

		foreach ($rings as $ring)
			TOESHelper::deleteCongressFilters($ring->ring_id);

		$query = $db->getQuery(true);
		$query->delete('#__toes_ring');
		$query->where('ring_show = ' . $show_id);
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_user_subcribed_to_show');
		$query->where('user_subcribed_to_show_show = ' . $show_id);
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_show_has_official');
		$query->where('show = ' . $show_id);
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_show_changes');
		$query->where('show_changes_show = ' . $show_id);
		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_show_day');
		$query->where('show_day_show = ' . $show_id);
		$db->setQuery($query);
		$db->query();


		$query = $db->getQuery(true);
		$query->update('#__toes_show');
		foreach ($prod_show as $key => $value) {
			$query->set($key . ' = ' . $db->quote($value));
		}
		$query->where('show_id = ' . $show_id);

		$db->setQuery($query);
		if ($db->query()) {
			/* SHOW DAY table */
			$new_show_days = array();
			$query = $prod_db->getQuery(true);
			$query->select('*');
			$query->from('#__toes_show_day');
			$query->where('show_day_show = ' . $show_id);

			$prod_db->setQuery($query);
			$prod_show_days = $prod_db->loadObjectList();

			if ($prod_show_days) {
				foreach ($prod_show_days as $item) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_show_day');
					foreach ($item as $key => $value) {
						if ($key == 'show_day_id')
							$query->set($key . ' = 0');
						else
							$query->set($key . ' = ' . $db->quote($value));
					}
					$db->setQuery($query);
					$db->query();

					$new_show_days[$item->show_day_id] = $db->insertid();
				}
			}
			else {
				echo JText::_('COM_TOES_SYNC_ERROR') . ' ' . $prod_db->getErrorMsg();
				return false;
			}

			/* SHOW CHANGES table */
			$query = $prod_db->getQuery(true);
			$query->select('*');
			$query->from('#__toes_show_changes');
			$query->where('show_changes_show = ' . $show_id);

			$prod_db->setQuery($query);
			$prod_show_changes = $prod_db->loadObjectList();

			if ($prod_show_changes) {
				foreach ($prod_show_changes as $item) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_show_changes');
					foreach ($item as $key => $value) {
						$query->set($key . ' = ' . $db->quote($value));
					}
					$db->setQuery($query);
					$db->query();
				}
			}

			/*  USER SUBSCRIBED TO USERS table */
			$query = $prod_db->getQuery(true);
			$query->select('*');
			$query->from('#__toes_user_subcribed_to_show');
			$query->where('user_subcribed_to_show_show = ' . $show_id);

			$prod_db->setQuery($query);
			$prod_show_subscribed_users = $prod_db->loadObjectList();

			if ($prod_show_subscribed_users) {
				foreach ($prod_show_subscribed_users as $item) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_user_subcribed_to_show');
					foreach ($item as $key => $value) {
						$query->set($key . ' = ' . $db->quote($value));
					}

					$db->setQuery($query);
					$db->query();
				}
			}

			/* SHOW RINGS table */

			$new_ring_ids[] = array();

			$query = $prod_db->getQuery(true);
			$query->select('*');
			$query->from('#__toes_ring');
			$query->where('ring_show = ' . $show_id);

			$prod_db->setQuery($query);
			$prod_show_days = $prod_db->loadObjectList();

			if ($prod_show_days) {
				foreach ($prod_show_days as $item) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_ring');
					foreach ($item as $key => $value) {
						if ($key == 'ring_id')
							$query->set($key . ' = 0');
						else if ($key == "ring_show_day")
							$query->set($key . ' = ' . $db->quote($new_show_days[$value]));
						else
							$query->set($key . ' = ' . $db->quote($value));
					}
					$db->setQuery($query);
					$db->query();

					$new_ring_ids[$item->ring_id] = $db->insertid();

					if ($item->ring_format == 3) {
						$this->addCongressFilters($prod_db, $db, $item->ring_id, $new_ring_ids[$item->ring_id]);
					}
				}
			}

			/* SHOW SUMMARY table */
			$query = $prod_db->getQuery(true);
			$query->select('*');
			$query->from('#__toes_summary');
			$query->where('summary_show = ' . $show_id);

			$prod_db->setQuery($query);
			$prod_summaries = $prod_db->loadObjectList();

			if ($prod_summaries) {
				foreach ($prod_summaries as $item) {
					$query = $db->getQuery(true);
					$query->insert('#__toes_summary');
					foreach ($item as $key => $value) {
						if ($key == 'summary_id')
							$query->set($key . ' = 0');
						else
							$query->set($key . ' = ' . $db->quote($value));
					}
					$db->setQuery($query);
					$db->query();

					$new_summary = $db->insertid();

					if ($new_summary) {
						/* Entries table */
						$query = $prod_db->getQuery(true);
						$query->select('*');
						$query->from('#__toes_entry');
						$query->where('summary = ' . $item->summary_id);

						$prod_db->setQuery($query);
						$prod_entries = $prod_db->loadObjectList();

						if ($prod_entries) {
							foreach ($prod_entries as $entry) {
								$query = $db->getQuery(true);
								$query->insert('#__toes_entry');
								foreach ($entry as $key => $value) {
									if ($key == 'entry_id')
										$query->set($key . ' = 0');
									else if ($key == 'summary')
										$query->set($key . ' = ' . $db->quote($new_summary));
									else if ($key == 'show_day')
										$query->set($key . ' = ' . $db->quote($new_show_days[$value]));
									else
										$query->set($key . ' = ' . $db->quote($value));
								}
								$db->setQuery($query);
								$db->query();

								$new_entry = $db->insertid();

								if ($new_entry) {
									/* Entries Refusal reason table */
									$query = $prod_db->getQuery(true);
									$query->select('*');
									$query->from('#__toes_entry_refusal_reason');
									$query->where('entry_refusal_reason_entry = ' . $entry->entry_id);

									$prod_db->setQuery($query);
									$entry_refusal_reason = $prod_db->loadObject();

									if ($entry_refusal_reason) {
										$query = $db->getQuery(true);
										$query->insert('#__toes_entry_refusal_reason');
										foreach ($entry_refusal_reason as $key => $value) {
											if ($key == 'entry_refusal_reason_id')
												$query->set($key . ' = 0');
											else if ($key == 'entry_refusal_reason_entry')
												$query->set($key . ' = ' . $db->quote($new_entry));
											else
												$query->set($key . ' = ' . $db->quote($value));
										}
										$db->setQuery($query);
										$db->query();
									}

									/* Entries congress participation table */
									$query = $prod_db->getQuery(true);
									$query->select('*');
									$query->from('#__toes_entry_participates_in_congress');
									$query->where('entry_id = ' . $entry->entry_id);

									$prod_db->setQuery($query);
									$entry_congress_participations = $prod_db->loadObjectList();

									if ($entry_congress_participations) {
										foreach ($entry_congress_participations as $entry_congress_participation) {
											$query = $db->getQuery(true);
											$query->insert('#__toes_entry_participates_in_congress');
											foreach ($entry_congress_participation as $key => $value) {
												if ($key == 'entry_id')
													$query->set($key . ' = ' . $db->quote($new_entry));
												else if ($key == 'congress_id')
													$query->set($key . ' = ' . $db->quote($new_ring_ids[$value]));
												else
													$query->set($key . ' = ' . $db->quote($value));
											}
											$db->setQuery($query);
											$db->query();
										}
									}
								}
							}
						}

						/* Placeholders table */
						$query = $prod_db->getQuery(true);
						$query->select('*');
						$query->from('#__toes_placeholder');
						$query->where('placeholder_exhibitor = ' . $item->summary_user);
						$query->where('placeholder_show = ' . $show_id);

						$prod_db->setQuery($query);
						$prod_placeholders = $prod_db->loadObjectList();

						if ($prod_placeholders) {
							foreach ($prod_placeholders as $placeholder) {
								$query = $db->getQuery(true);
								$query->insert('#__toes_placeholder');
								foreach ($placeholder as $key => $value) {
									if ($key == 'placeholder_id')
										$query->set($key . ' = 0');
									else
										$query->set($key . ' = ' . $db->quote($value));
								}
								$db->setQuery($query);
								$db->query();

								$new_placeholder = $db->insertid();

								if ($new_placeholder) {
									/* Placeholder day table */
									$query = $prod_db->getQuery(true);
									$query->select('*');
									$query->from('#__toes_placeholder_day');
									$query->where('placeholder_day_placeholder = ' . $placeholder->placeholder_id);

									$prod_db->setQuery($query);
									$placeholder_days_participations = $prod_db->loadObjectList();

									if ($placeholder_days_participations) {
										foreach ($placeholder_days_participations as $placeholder_days_participation) {
											$query = $db->getQuery(true);
											$query->insert('#__toes_placeholder_day');
											foreach ($placeholder_days_participation as $key => $value) {
												if ($key == 'placeholder_day_id')
													$query->set($key . ' = 0');
												if ($key == 'placeholder_day_placeholder')
													$query->set($key . ' = ' . $db->quote($new_placeholder));
												else if ($key == 'placeholder_day_showday')
													$query->set($key . ' = ' . $db->quote($new_show_days[$value]));
												else
													$query->set($key . ' = ' . $db->quote($value));
											}
											$db->setQuery($query);
											$db->query();
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return true;
	}

	function addCongressFilters($prod_db, $db, $ring_id, $new_ring_id) {
		/* CONGRESS FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress');
		$query->where('congress_id = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress = $prod_db->loadObject();

		if ($prod_congress) {
			$query = $db->getQuery(true);
			$query->insert('#__toes_congress');
			foreach ($prod_congress as $key => $value) {
				if ($key == 'congress_id')
					$query->set($key . ' = ' . $db->quote($new_ring_id));
				else
					$query->set($key . ' = ' . $db->quote($value));
			}
			$db->setQuery($query);
			$db->query();
		}

		/* CONGRESS BREED FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_breed');
		$query->where('congress_breed_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_breeds = $prod_db->loadObjectList();

		if ($prod_congress_breeds) {
			foreach ($prod_congress_breeds as $prod_congress_breed) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_breed');
				foreach ($prod_congress_breed as $key => $value) {
					if ($key == 'congress_breed_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_breed_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS CATEGORY FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_category');
		$query->where('congress_category_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_categories = $prod_db->loadObjectList();

		if ($prod_congress_categories) {
			foreach ($prod_congress_categories as $prod_congress_category) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_category');
				foreach ($prod_congress_category as $key => $value) {
					if ($key == 'congress_category_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_category_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS COLOR FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_color');
		$query->where('congress_color_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_colors = $prod_db->loadObjectList();

		if ($prod_congress_colors) {
			foreach ($prod_congress_colors as $prod_congress_color) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_color');
				foreach ($prod_congress_color as $key => $value) {
					if ($key == 'congress_color_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_color_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS COLOR WILDCARD FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_color_wildcard');
		$query->where('congress_color_wildcard_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_color_wildcards = $prod_db->loadObjectList();

		if ($prod_congress_color_wildcards) {
			foreach ($prod_congress_color_wildcards as $prod_congress_color_wildcard) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_color_wildcard');
				foreach ($prod_congress_color_wildcard as $key => $value) {
					if ($key == 'congress_color_wildcard_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_color_wildcard_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS COMPETATIVE CLASS FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_competitive_class');
		$query->where('congress_competitive_class_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_competitive_classes = $prod_db->loadObjectList();

		if ($prod_congress_competitive_classes) {
			foreach ($prod_congress_competitive_classes as $prod_congress_competitive_class) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_competitive_class');
				foreach ($prod_congress_competitive_class as $key => $value) {
					if ($key == 'congress_competitive_class_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_competitive_class_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS DIVISION FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_division');
		$query->where('congress_division_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_divisions = $prod_db->loadObjectList();

		if ($prod_congress_divisions) {
			foreach ($prod_congress_divisions as $prod_congress_division) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_division');
				foreach ($prod_congress_division as $key => $value) {
					if ($key == 'congress_gender_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_division_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS GENDER FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_gender');
		$query->where('congress_gender_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_genders = $prod_db->loadObjectList();

		if ($prod_congress_genders) {
			foreach ($prod_congress_genders as $prod_congress_gender) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_gender');
				foreach ($prod_congress_gender as $key => $value) {
					if ($key == 'congress_gender_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_gender_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS HAIR LENGTH FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_hair_length');
		$query->where('congress_hair_length_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_hair_lengths = $prod_db->loadObjectList();

		if ($prod_congress_hair_lengths) {
			foreach ($prod_congress_hair_lengths as $prod_congress_hair_length) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_hair_length');
				foreach ($prod_congress_hair_length as $key => $value) {
					if ($key == 'congress_hair_length_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_hair_length_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}

		/* CONGRESS TITLE FILTER table */
		$query = $prod_db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_congress_title');
		$query->where('congress_title_congress = ' . $ring_id);
		$prod_db->setQuery($query);
		$prod_congress_titles = $prod_db->loadObjectList();

		if ($prod_congress_titles) {
			foreach ($prod_congress_titles as $prod_congress_title) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_congress_title');
				foreach ($prod_congress_title as $key => $value) {
					if ($key == 'congress_title_id')
						$query->set($key . ' = 0');
					else if ($key == 'congress_title_congress')
						$query->set($key . ' = ' . $db->quote($new_ring_id));
					else
						$query->set($key . ' = ' . $db->quote($value));
				}
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}
