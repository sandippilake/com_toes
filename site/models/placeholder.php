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
class TOESModelPlaceholder extends JModelLegacy {
    
    public static function getUsers(){
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

    public function getShowdays() {
		$app = JFactory::getApplication();
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $session = JFactory::getSession();
        $placeholder = $session->get('placeholder');
        
        $show_id = $placeholder->placeholder_show;
        if(!$show_id)
        {
            $show_id = $app->input->getVar('show_id');
            $placeholder->placeholder_show = $show_id;
            $session->set('placeholder',$placeholder);
        }

        $query->select('s.show_day_id, s.show_day_date');
        $query->from('#__toes_show_day AS s');
        $query->where('s.show_day_show = ' . (int) $show_id);
       
        //echo nl2br(str_replace('#__', 'j35_', $query));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

	public function getSelectedShowday() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$session = JFactory::getSession();
		$placeholder = $session->get('placeholder');

		$isContinuous = TOESHelper::isContinuous($placeholder->placeholder_show);
		if ($isContinuous)
			return array(JText::_('JALL'));

		$showdays = $placeholder->showdays;

		$query->select('DAYNAME(s.show_day_date) as dayname, show_day_id');
		$query->from('#__toes_show_day AS s');
		$query->where('s.show_day_id in (' . $showdays . ' )');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$selected = $db->loadObjectList();

		$isAlternative = TOESHelper::isAlternative($placeholder->placeholder_show);
		$final = array();
		foreach ($selected as $day) {
			$day_string = $day->dayname;
			if ($isAlternative) {
				if (in_array($day->show_day_id, explode(',', $placeholder->placeholder_for_AM))) {
					$day_string .= ' AM';
					if (in_array($day->show_day_id, explode(',', $placeholder->placeholder_for_PM))) {
						$day_string .= '/PM';
					}
				} elseif (in_array($day->show_day_id, explode(',', $placeholder->placeholder_for_PM))) {
					$day_string .= ' PM';
				}
			}
			$final[] = $day_string;
		}

		return $final;
	}
    
	public function getSummary() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$session = JFactory::getSession();
		$placeholder = $session->get('placeholder');

		$show_id = $placeholder->placeholder_show;
		$user_id = $placeholder->placeholder_exhibitor;

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
    public function save_placeholder() {
        // Initialise variables;
        $session = JFactory::getSession();
        $placeholder = $session->get('placeholder');
        
        $db = JFactory::getDbo();
		
		$query = "SELECT summary_id FROM `#__toes_summary` "
				. " WHERE summary_show = " . $db->quote($placeholder->placeholder_show)
				. " AND summary_user = " . $db->quote($placeholder->placeholder_exhibitor);

		$db->setQuery($query);
		$summary_id = $db->loadResult();

		if ($summary_id) {
			$query = "UPDATE `#__toes_summary` SET "
					. " summary_benching_request = " . $db->quote($placeholder->benching_request) . ","
					. " summary_grooming_space = " . $db->quote($placeholder->grooming_space ? 1 : 0) . ","
					. " summary_single_cages = " . $db->quote($placeholder->single_cages) . ","
					. " summary_double_cages = " . $db->quote($placeholder->double_cages) . ","
					. " summary_personal_cages = " . $db->quote($placeholder->personal_cage ? 1 : 0) . ","
					. " summary_remarks = " . $db->quote($placeholder->remark) . ","
					. " summary_status = (SELECT s.`summary_status_id` FROM `#__toes_summary_status` AS s WHERE s.`summary_status` = 'Updated') "
					. " WHERE summary_id = " . $db->quote($summary_id);

			$db->setQuery($query);
			$db->query();
		} else {
			$query = "INSERT INTO `#__toes_summary` SET "
					. " summary_benching_request = " . $db->quote($placeholder->benching_request) . ","
					. " summary_grooming_space = " . $db->quote($placeholder->grooming_space ? 1 : 0) . ","
					. " summary_single_cages = " . $db->quote($placeholder->single_cages) . ","
					. " summary_double_cages = " . $db->quote($placeholder->double_cages) . ","
					. " summary_personal_cages = " . $db->quote($placeholder->personal_cage ? 1 : 0) . ","
					. " summary_remarks = " . $db->quote($placeholder->remark) . ","
					. " summary_show = " . $db->quote($placeholder->placeholder_show) . ","
					. " summary_user = " . $db->quote($placeholder->placeholder_exhibitor) . ","
					. " summary_status = 1 ";
			$db->setQuery($query);
			$db->query();

			$summary_id = $db->insertid();
		}

		$isAlternative = TOESHelper::isAlternative($placeholder->placeholder_show);

		$showdays = explode(',', $placeholder->showdays);
        $org_show_days = array();
		
        if(isset($placeholder->placeholder_id)){
            $org_placeholder = TOESHelper::getPlaceholderFullDetails($placeholder->placeholder_id);
			
			$placeholder->placeholder_for_AM = explode(',',$placeholder->placeholder_for_AM);
			$placeholder->placeholder_for_PM = explode(',',$placeholder->placeholder_for_PM);

            foreach($org_placeholder as $ph)
                $org_show_days[] = $ph->placeholder_day_showday;
		
            $excluded_showdays = array_diff($org_show_days, $showdays);
            
			for ($i = 0; $i < count($showdays); $i++) {
				if(!in_array($showdays[$i], $org_show_days))
				{
					$placeholder_status = '1';
					if($show->show_use_waiting_list) 
					{
						if($isAlternative)
						{
							$am_waiting = false;
							$pm_waiting = false;

							if(in_array($showdays[$i],$placeholder->placeholder_for_AM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i],'1'))
							{
								$am_waiting = true;
							}

							if(in_array($showdays[$i],$placeholder->placeholder_for_PM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i],'2'))
							{
								$pm_waiting = true;
							}

							if( ($am_waiting && $pm_waiting) || (!in_array($showdays[$i],$placeholder->placeholder_for_AM) && $pm_waiting) || (!in_array($showdays[$i],$placeholder->placeholder_for_PM) && $am_waiting) )
							{
								continue;
							}
							else if($am_waiting || $pm_waiting)
							{
								if($am_waiting)
								{
									$placeholder->placeholder_for_AM = array_diff($placeholder->placeholder_for_AM, array($showdays[$i]));
								}

								if($pm_waiting)
								{
									$placeholder->placeholder_for_PM = array_diff($placeholder->placeholder_for_PM, array($showdays[$i]));
								}
							}
						}
						else
						{
							if(!TOESHelper::getAvailableSpaceforDay($showdays[$i]))
							{
								$placeholder_status = '11';
							}
						}
					}

					$query = "INSERT INTO `#__toes_placeholder_day` 
								(`placeholder_day_placeholder`, 
								`placeholder_day_showday`,
								`placeholder_participates_AM`,
								`placeholder_participates_PM`,
								`placeholder_day_placeholder_status`
								)
								VALUES (" . $db->quote($placeholder->placeholder_id) . ","
						. $db->quote($showdays[$i]) . ","
						. $db->quote(in_array($showdays[$i], $placeholder->placeholder_for_AM)?'1':'0') . ","
						. $db->quote(in_array($showdays[$i], $placeholder->placeholder_for_PM)?'1':'0') . ","
						. $db->quote($placeholder_status) . " )";

					$db->setQuery($query);
					$db->query(); 
				}
				else
				{
					$placeholder_status = $org_placeholder[0]->placeholder_day_placeholder_status;
					if($show->show_use_waiting_list) 
					{
						if($isAlternative)
						{
							$am_waiting = false;
							$pm_waiting = false;

							if(in_array($showdays[$i],$placeholder->placeholder_for_AM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i],'1',null,$placeholder->placeholder_id))
							{
								$am_waiting = true;
							}

							if(in_array($showdays[$i],$placeholder->placeholder_for_PM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i],'2',null,$placeholder->placeholder_id))
							{
								$pm_waiting = true;
							}

							if( ($am_waiting && $pm_waiting) || (!in_array($showdays[$i],$placeholder->placeholder_for_AM) && $pm_waiting) || (!in_array($showdays[$i],$placeholder->placeholder_for_PM) && $am_waiting) )
							{
								continue;;
							}
							else if($am_waiting || $pm_waiting)
							{
								if($am_waiting)
								{
									$placeholder->placeholder_for_AM = array_diff($placeholder->placeholder_for_AM, array($showdays[$i]));
								}

								if($pm_waiting)
								{
									$placeholder->placeholder_for_PM = array_diff($placeholder->placeholder_for_PM, array($showdays[$i]));
								}
							}
						}
						else
						{
							if(!TOESHelper::getAvailableSpaceforDay($showdays[$i],null,null,$placeholder->placeholder_id))
							{
								$placeholder_status = '11';
							}
						}
					}
					
					$query = "UPDATE `#__toes_placeholder_day` SET 
								`placeholder_participates_AM` = {$db->quote(in_array($showdays[$i], $placeholder->placeholder_for_AM)?'1':'0')}, 
								`placeholder_participates_PM` = {$db->quote(in_array($showdays[$i], $placeholder->placeholder_for_PM)?'1':'0')},
								`placeholder_day_placeholder_status` = {$db->quote($placeholder_status)}
								WHERE `placeholder_day_placeholder` = {$placeholder->placeholder_id} 
								AND `placeholder_day_showday` = {$db->quote($showdays[$i])} ";

					$db->setQuery($query);
					$db->query(); 
				}
			}
            
            if($excluded_showdays)
            {
                $query = "DELETE FROM `#__toes_placeholder_day` WHERE `placeholder_day_placeholder` = {$placeholder->placeholder_id} AND `placeholder_day_showday` IN (".  implode(',', $excluded_showdays).")";
                $db->setQuery($query);
                $db->query();
            }
        }
        else 
        {
            $query = "INSERT INTO `#__toes_placeholder` 
                            (`placeholder_show`, 
                            `placeholder_exhibitor`,
                            `placeholder_summary`
                            )
                            VALUES (" . $db->quote($placeholder->placeholder_show) . ","
                    	. $db->quote($placeholder->placeholder_exhibitor) . ","
                    	. $db->quote($summary_id) . " )";

            $db->setQuery($query);
            if(!$db->query())
            {
                echo $db->getErrorMsg();
                return false;
            }
            $placeholder_id = $db->insertid();

            
			$placeholder->placeholder_for_AM = explode(',',$placeholder->placeholder_for_AM);
			$placeholder->placeholder_for_PM = explode(',',$placeholder->placeholder_for_PM);
            
			for ($i = 0; $i < count($showdays); $i++)
            {
				if($isAlternative)
				{
					$placeholder_status = '1';

					$am_waiting = false;
					$pm_waiting = false;
				
					if(in_array($showdays[$i],$placeholder->placeholder_for_AM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i],'1'))
					{
						$am_waiting = true;
					}

					if(in_array($showdays[$i],$placeholder->placeholder_for_PM) && !TOESHelper::getAvailableSpaceforDay($showdays[$i],'2'))
					{
						$pm_waiting = true;
					}

					if( ($am_waiting && $pm_waiting) || (!in_array($showdays[$i],$placeholder->placeholder_for_AM) && $pm_waiting) || (!in_array($showdays[$i],$placeholder->placeholder_for_PM) && $am_waiting) )
					{
						$placeholder_status = '11';
						
					}
					else if($am_waiting || $pm_waiting)
					{
						if($am_waiting)
						{
							$placeholder->placeholder_for_AM = array_diff($placeholder->placeholder_for_AM, array($showdays[$i]));
						}
						
						if($pm_waiting)
						{
							$placeholder->placeholder_for_PM = array_diff($placeholder->placeholder_for_PM, array($showdays[$i]));
						}
					}

					$query = "INSERT INTO `#__toes_placeholder_day` 
									(`placeholder_day_placeholder`, 
									`placeholder_day_showday`,
									`placeholder_participates_AM`,
									`placeholder_participates_PM`,
									`placeholder_day_placeholder_status`
									)
									VALUES (" . $db->quote($placeholder_id) . ","
							. $db->quote($showdays[$i]) . ","
							. $db->quote(in_array($showdays[$i], $placeholder->placeholder_for_AM)?'1':'0') . ","
							. $db->quote(in_array($showdays[$i], $placeholder->placeholder_for_PM)?'1':'0') . ","
							. $db->quote($placeholder_status) . " )";

					$db->setQuery($query);
					$db->query();                    
				}
				else
				{
					$placeholder_status = '1';
					if(!TOESHelper::getAvailableSpaceforDay($showdays[$i]))
						$placeholder_status = '11';

					$query = "INSERT INTO `#__toes_placeholder_day` 
									(`placeholder_day_placeholder`, 
									`placeholder_day_showday`,
									`placeholder_day_placeholder_status`
									)
									VALUES (" . $db->quote($placeholder_id) . ","
							. $db->quote($showdays[$i]) . ","
							. $db->quote($placeholder_status) . " )";

					$db->setQuery($query);
					$db->query();                    
				}
            }   
            
			$mailTemplate = TOESMailHelper::getTemplate('placeholder_created_notification');

			if($mailTemplate) {
				$subject = $mailTemplate->mail_subject;
				$body = $mailTemplate->mail_body;
			} else {
				$subject = JText::_('COM_TOES_EC_PLACEHOLDER_NOTIFICATION_MAIL_SUBJECT');
				$body = JText::_('COM_TOES_EC_PLACEHOLDER_NOTIFICATION_MAIL_CONTENT');
			}

            $show = TOESHelper::getShowDetails($placeholder->placeholder_show);
            $userInfo = TOESHelper::getUserInfo($placeholder->placeholder_exhibitor);

            $body = str_replace('[exhibitor]', $userInfo->name, $body);

            $body = str_replace('[City]', $show->address_city, $body);
            $body = str_replace('[, [State]]', $show->address_state?', '.$show->address_state:'', $body);
            $body = str_replace('[Country]', $show->address_country, $body);

            $body = str_replace('[club name]', $show->club_name, $body);

            $start_date = date('d', strtotime($show->show_start_date));
            $start_date_month = date('M', strtotime($show->show_start_date));
            $start_date_year = date('Y', strtotime($show->show_start_date));

            $end_date = date('d', strtotime($show->show_end_date));
            $end_date_month = date('M', strtotime($show->show_end_date));
            $end_date_year = date('Y', strtotime($show->show_end_date));

            $show_date =  $start_date_month.' '.$start_date;

            if ($end_date_year != $start_date_year){
                $show_date .= ' '.$start_date_year;
            }

            if ($end_date_month != $start_date_month){
                if(date('t', strtotime($show->show_start_date)) != $start_date)
                    $show_date .= ' - '.date('t', strtotime($show->show_start_date));
                if($end_date == '01')
                    $show_date .= ', ' .$end_date_month.' '.$end_date;
                else
                    $show_date .= ', ' .$end_date_month.' 01 - '.$end_date;
            } else {
                if($start_date != $end_date)
                    $show_date .= ' - ' . $start_date_month.' '.$end_date;
            }

            $show_date .= ' '.$end_date_year;

            $body = str_replace('[showdates]', $show_date, $body);

            $subject = str_replace('[exhibitor]', $userInfo->name, $subject);
            $subject = str_replace('[club name]', $show->club_name, $subject);
            $subject = str_replace('[showdates]', $show_date, $subject);
            
            if($show->show_use_club_entry_clerk_address)
            {
                $recipient = $show->show_email_address_entry_clerk;
            }
            else
            {
                $entryClerks = TOESHelper::getEntryClerks($placeholder->placeholder_show);

                foreach($entryClerks as $entryClerk) {
                    $recipient[] = $entryClerk->entry_clerk_email;
                }
            }

			/*
			$mail = JFactory::getMailer();
            $config     = JFactory::getConfig();
            $fromname   = $config->get('fromname');

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

			$params = JComponentHelper::getParams('com_toes');
			$bcc = '';
			if ($params->get('send_bcc_emails') == 1) {
				$bcc = $params->get('bcc_email');
			}
			TOESMailHelper::sendMail('placeholder_created_notification', $subject, $body, $recipient, '', '', '', $bcc);
        }
        
        $session->clear('placeholder');

        // Clean the cache.
        $this->cleanCache();
        return true;
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
    

    function updateStatus($placeholder_day_id, $status)
    {
        $user   = JFactory::getUser();
        $db     = JFactory::getDbo();  
        
        $is_allowed = false;
        
        $org_placeholder = TOESHelper::getPlaceholderDetails($placeholder_day_id);
        
        $placeholder_id = $org_placeholder->placeholder_id;
        $show_id = $org_placeholder->placeholder_show;
        $summary_id = $org_placeholder->placeholder_summary;

		$show = TOESHelper::getShowDetails($show_id);
        
        $org_status = $org_placeholder->entry_status;
        
        $is_official = (TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) 
            || TOESHelper::isAdmin())?true:false;

        $sql = $db->getQuery(true);
        $sql->select('`placeholder_day_showday`');
        $sql->from('`#__toes_placeholder_day`');
        $sql->where('`placeholder_day_placeholder` = '.$org_placeholder->placeholder_day_placeholder);
        $sql->where('`placeholder_day_placeholder_status` = '.$org_placeholder->placeholder_day_placeholder_status);

        $db->setQuery($sql);
        $showdays = $db->loadColumn();
        
        switch ($status)
        {
            case 'Accepted':
                    if(($org_status == 'Cancelled' || $org_status == 'Cancelled & Confirmed') && TOESHelper::getShowDetails($show_id)->show_status != 'Open') {
                    break;
                }
            case 'Rejected':
                if($is_official || ($org_status == 'Cancelled' || $org_status == 'Cancelled & Confirmed'))
                    $is_allowed = true;                
                else
                    $this->setError(JText::_('COM_TOES_NOAUTH'));
                break;
            case 'Cancelled':
                if($org_placeholder->placeholder_exhibitor == $user->id || $is_official)
                    $is_allowed = true;
                else
                    $this->setError(JText::_('COM_TOES_NOAUTH'));
                break;
            default:
                $is_allowed = true;
                break;
        }
        
        if($is_allowed)
        {
            $query_excuted = false;
            for($i = 0; $i < count($showdays); $i++)
            {
                if($status == "Cancelled")
                {
                    $placeholder_status = "Cancelled";
                    if($org_status == "New" || $org_status == "Rejected" || $org_status == "Waiting List") {
                        $query = $db->getQuery(true);
                        $query->delete('`#__toes_placeholder_day`');
                        $query->where('`placeholder_day_placeholder` = '.$placeholder_id);
                        $query->where('`placeholder_day_showday` = '.$showdays[$i]);

                        $db->setQuery($query);
                        $db->query();

						$last_placeholder = 0;
                        $query = $db->getQuery(true);
                        $query->select('`placeholder_day_placeholder`');
                        $query->from('`#__toes_placeholder_day`');
                        $query->where('`placeholder_day_placeholder` = '.$placeholder_id);
                        $db->setQuery($query);
                        if(!$db->loadResult())
                        {
                            $query = "DELETE "
                                ." FROM `#__toes_placeholder` "
                                ." WHERE  `placeholder_id` = {$placeholder_id}";
                            $db->setQuery($query);
                            $db->query();
							$last_placeholder = 1;
                        }
						
						/* log for deleted placeholder*/
						$query = $db->getQuery(true);
						$query->insert('`#__toes_log_placeholders`');
						$query->set('`placeholder_id` = '.$placeholder_id);
						$query->set('`exhibitor` = '.$org_placeholder->placeholder_exhibitor);
						$query->set('`show_day` = '.$showdays[$i]);
						if($is_official){
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ENTRYCLERK').($last_placeholder?'':(' Showday '.$showdays[$i]))));
						} else {
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_USER').($last_placeholder?'':(' Showday '.$showdays[$i]))));
						}
						$query->set('`changed_by` = '.$user->id);
						$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
						$db->setQuery($query);
						$db->query();
					
                        $query = $db->getQuery(true);
                        $query->select('e.`entry_id`');
                        $query->from('`#__toes_entry` AS e');
                        $query->where('e.`summary` = '.$summary_id);
                        $db->setQuery($query);
                        $e = $db->loadResult();
						if(!$e) {
	                        $query = $db->getQuery(true);
	                        $query->select('`placeholder_id`');
	                        $query->from('`#__toes_placeholder`');
	                        $query->where('`placeholder_summary` = '.$summary_id);
	                        $db->setQuery($query);
	                        $p = $db->loadResult();
	                        if(!$p)
	                        {
	                            $query = "DELETE "
	                            ." FROM `#__toes_summary` "
	                            ." WHERE `summary_id` = {$summary_id}";
	                            $db->setQuery($query);
	                            if($db->query())
								{
									/* log for deleted summary*/
									$query = $db->getQuery(true);
									$query->insert('`#__toes_log_summaries`');
									$query->set('`summary_id` = '.$summary_id);
									$query->set('`exhibitor` = '.$org_placeholder->placeholder_exhibitor);
									if($is_official) {
										$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ENTRYCLERK')));
									} else {
										$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_USER')));
									}
									$query->set('`changed_by` = '.$user->id);
									$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
									$db->setQuery($query);
									$db->query();
								}
	                        }
                        }
						
                        $query_excuted = true;
                        continue;
                    } else if($org_status == 'Confirmed') {
                        $placeholder_status = 'Cancelled & Confirmed';
                    } else if($org_status == 'Confirmed & Paid') {
                        $placeholder_status == "Cancelled & Paid";
                    }
                } 
                else if($status == "Accepted") 
                {
                    $placeholder_status = "Accepted";
					
					$ring_timing = null;
					if($org_placeholder->placeholder_participates_AM && $org_placeholder->placeholder_participates_PM){
						$ring_timing = null;
					} else if($org_placeholder->placeholder_participates_AM){
						$ring_timing = 1;
					} else if($org_placeholder->placeholder_participates_PM){
						$ring_timing = 2;
					}

					if($is_official)
                    {
                        if(!TOESHelper::getAvailableSpaceforDayforEC($showdays[$i], $ring_timing))
                        {
                            $error = JText::_('COM_TOES_COULD_NOT_ACTIVATE');
                            continue;
                        }
                    }                    
                    elseif(!TOESHelper::getAvailableSpaceforDay($showdays[$i], $ring_timing))
                    {
                        switch ($org_status)
                        {
                            case 'Cancelled':
                                $placeholder_status = 'Waiting List';
                                break;
                            case 'Cancelled & Confirmed':
                                $placeholder_status = 'Waiting List & Confirmed';
                                break;
                            case 'Cancelled & Paid':
                                $placeholder_status = 'Waiting List & Paid';
                                break;
                        }
                    }                
                    else
                    {
                        if($org_status == 'Cancelled & Confirmed') {
                            $placeholder_status = 'Confirmed';
                        }else if($org_status == 'Cancelled & Paid') {
                            $placeholder_status = 'Confirmed & Paid';
                        }
                    }
                }
                else
                    $placeholder_status = $status;

                $query = $db->getQuery(true);

                $query->update('#__toes_placeholder_day');
                $query->set('`placeholder_day_placeholder_status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = '.$db->quote($placeholder_status).')');

                $query->where('`placeholder_day_placeholder` = '.$placeholder_id);
                $query->where('`placeholder_day_showday` = '.$showdays[$i]);

                $db->setQuery($query);
                if($db->query()) {    
                    $query_excuted = true;
					if($status == "Cancelled"){
						/* log for cancelled placeholder*/
						$query = $db->getQuery(true);
						$query->insert('`#__toes_log_placeholders`');
						$query->set('`placeholder_id` = '.$placeholder_id);
						$query->set('`exhibitor` = '.$org_placeholder->placeholder_exhibitor);
						$query->set('`show_day` = '.$showdays[$i]);
						if($is_official){
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_ENTRYCLERK').' Showday '.$showdays[$i]));
						} else {
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_USER').' Showday '.$showdays[$i]));
						}
						$query->set('`changed_by` = '.$user->id);
						$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
						$db->setQuery($query);
						$db->query();
					}
				}
            }
            
            if($query_excuted)
            {
                if($show->show_use_waiting_list && ($status == "Rejected" || $status == "Cancelled"))
                {
                    TOESHelper::checkWaitingList($show_id);
                }

				if($org_status != "New" && $org_placeholder->placeholder_exhibitor == $user->id)
				{
					$user_info = TOESHelper::getUserInfo ( $user->id );
					
					$mailTemplate = TOESMailHelper::getTemplate('placeholder_cancelled_notification');

					if($mailTemplate) {
						$subject = $mailTemplate->mail_subject;
						$body = $mailTemplate->mail_body;
					} else {
						$subject = JText::_ ( 'COM_TOES_NOTIFICATION_EXHIBITOR_CANCELLED_PLACEHOLDER_SUBJECT' );
						$body = JText::_ ( 'COM_TOES_NOTIFICATION_EXHIBITOR_CANCELLED_PLACEHOLDER' );
					}
					
					$start_date = date ( 'd', strtotime ( $show->show_start_date ) );
					$start_date_month = date ( 'M', strtotime ( $show->show_start_date ) );
					$start_date_year = date ( 'Y', strtotime ( $show->show_start_date ) );
					
					$end_date = date ( 'd', strtotime ( $show->show_end_date ) );
					$end_date_month = date ( 'M', strtotime ( $show->show_end_date ) );
					$end_date_year = date ( 'Y', strtotime ( $show->show_end_date ) );
					
					$show_date = $start_date_month . ' ' . $start_date;
					
					if ($end_date_year != $start_date_year) {
						$show_date .= ' ' . $start_date_year;
					}
					
					if ($end_date_month != $start_date_month) {
						if (date ( 't', strtotime ( $show->show_start_date ) ) != $start_date)
							$show_date .= ' - ' . date ( 't', strtotime ( $show->show_start_date ) );
						if ($end_date == '01')
							$show_date .= ', ' . $end_date_month . ' ' . $end_date;
						else
							$show_date .= ', ' . $end_date_month . ' 01 - ' . $end_date;
					} else {
						if ($start_date != $end_date)
							$show_date .= ' - ' . $start_date_month . ' ' . $end_date;
					}
					
					$show_date .= ' ' . $end_date_year;
					
					$subject = str_replace ( '[club]', $show->club_name, $subject );
					$subject = str_replace ( '[showdates]', $show_date, $subject );
					$subject = str_replace ( '[show_location]', $show->Show_location, $subject);
					
					$show_days = TOESHelper::getShowDays ( $show_id );
					$selected_show_days = array ();
					foreach ( $show_days as $day ) {
						if (in_array ( $day->show_day_id, $showdays ))
							$selected_show_days [] = date ( 'l', strtotime ( $day->show_day_date ) );
					}
					
					$body = str_replace ( '[exhibitor]', $user_info->lastname . ' ' . $user_info->firstname, $body );
					$body = str_replace ( '[show_days]', implode ( ',', $selected_show_days ), $body );
					
					$entryClerks = TOESHelper::getEntryClerks ( $show_id );
					
					if ($show->show_use_club_entry_clerk_address) {
						$entryClerk = new stdClass ();
						$entryClerk->entry_clerk_name = JText::_ ( "COM_TOES_SHOW_ENTRYCLERK" );
						$entryClerk->entry_clerk_email = $show->show_email_address_entry_clerk;
						$entryClerks = array ();
						$entryClerks [] = $entryClerk;
					}
					
					$config     = JFactory::getConfig();
			        $fromname   = $config->get('fromname');
			        $fromemail  = $config->get('mailfrom');
					
					foreach ( $entryClerks as $usr ) {
						/*
						$mail = JFactory::getMailer ();
						
						$mail->SetFrom ( $fromemail, $fromname );
						$mail->setSubject ( $subject );
						$mail->setBody ( $body );
						$mail->addRecipient ( $usr->entry_clerk_email );
						$mail->IsHTML ( TRUE );
						
						$mail->Send ();
						*/
						TOESMailHelper::sendMail('placeholder_cancelled_notification', $subject, $body, $usr->entry_clerk_email);
					}
				}
                return true;
            }
            else
            {
                if($error)
                    $this->setError ($error);
                else
                    $this->setError(JText::_('ERROR_IN_SAVING_DETAILS'));
                return false;
            }
        }
        return false;
    }
	
	function delete_placeholder(){
        
		$app = JFactory::getApplication();
		$placeholder_day_id = $app->input->getInt('placeholder_day_id');
		
		$user   = JFactory::getUser();
        $db     = JFactory::getDbo();  
        
        $is_allowed = false;
        
        $org_placeholder = TOESHelper::getPlaceholderDetails($placeholder_day_id);
		
        $placeholder_id = $org_placeholder->placeholder_id;
        $show_id = $org_placeholder->placeholder_show;
        
        $is_official = (TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) 
            || TOESHelper::isAdmin())?true:false;

		if($is_official)
			$is_allowed = true;                
		else
			$this->setError(JText::_('COM_TOES_NOAUTH'));
		
        $sql = $db->getQuery(true);
        $sql->delete('`#__toes_placeholder_day`');
        $sql->where('`placeholder_day_placeholder` = '.$org_placeholder->placeholder_day_placeholder);
        $sql->where('`placeholder_day_placeholder_status` = '.$org_placeholder->placeholder_day_placeholder_status);

        $db->setQuery($sql);
        if(!$db->query())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}

		$sql = $db->getQuery(true);
        $sql->select('`placeholder_day_id`');
        $sql->from('`#__toes_placeholder_day`');
        $sql->where('`placeholder_day_placeholder` = '.$placeholder_id);

        $db->setQuery($sql);
        if(!$db->loadResult())
		{
			$sql = $db->getQuery(true);
			$sql->delete('`#__toes_placeholder`');
			$sql->where('`placeholder_id` = '.$placeholder_id);
			$db->setQuery($sql);
			if($db->query())
			{
				/* log for deleted placeholder*/
				$query = $db->getQuery(true);
				$query->insert('`#__toes_log_placeholders`');
				$query->set('`placeholder_id` = '.$placeholder_id);
				$query->set('`exhibitor` = '.$org_placeholder->placeholder_exhibitor);
				$query->set('`show_day` = '.$org_placeholder->placeholder_day_showday);
				$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ENTRYCLERK')));
				$query->set('`changed_by` = '.$user->id);
				$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
				$db->setQuery($query);
				$db->query();

				$query = $db->getQuery(true);
				$query->select('count(e.`entry_id`)');
				$query->from('`#__toes_entry` AS e');
				$query->join('LEFT', '#__toes_summary AS s ON s.summary_id = e.summary');
				$query->where('s.summary_user = '.$org_placeholder->placeholder_exhibitor);
				$query->where('s.summary_show = '.$org_placeholder->placeholder_show);
				$db->setQuery($query);
				$entries = $db->loadResult();

				$query = $db->getQuery(true);
				$query->select('count(p.`placeholder_id`)');
				$query->from('`#__toes_placeholder` AS p');
				$query->where('p.placeholder_exhibitor = '.$org_placeholder->placeholder_exhibitor);
				$query->where('p.placeholder_show = '.$org_placeholder->placeholder_show);

				$db->setQuery($query);
				$placeholders = $db->loadResult();

				if(!$entries && !$placeholders)
				{
					$query = $db->getQuery(true);
					$query->select('`summary_id`');
					$query->from('`#__toes_summary` AS `s`');
					$query->where('s.summary_user = '.$org_placeholder->placeholder_exhibitor);
					$query->where('s.summary_show = '.$org_placeholder->placeholder_show);
					$db->setQuery($query);
					$summary_id = $db->loadResult();

					$query = $db->getQuery(true);
					$query->delete('#__toes_summary');
					$query->where('s.summary_user = '.$org_placeholder->placeholder_exhibitor);
					$query->where('s.summary_show = '.$org_placeholder->placeholder_show);
					$db->setQuery($query);
					if($db->query()){
						/* log for deleted summary*/
						$query = $db->getQuery(true);
						$query->insert('`#__toes_log_summaries`');
						$query->set('`summary_id` = '.$summary_id);
						$query->set('`exhibitor` = '.$org_placeholder->placeholder_exhibitor);
						$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ENTRYCLERK')));
						$query->set('`changed_by` = '.$user->id);
						$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
						$db->setQuery($query);
						$db->query();
					}
				}


				TOESHelper::checkWaitingList($show_id);
				return true;
			}
			else
			{
				$this->setError($db->getErrorMsg());
				return false;
			}
		}
		else
		{
			TOESHelper::checkWaitingList($show_id);
			return true;
		}
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
}
