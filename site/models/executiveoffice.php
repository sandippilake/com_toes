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
 * Template style model.
 *
 * @service	Joomla
 * @subservice	com_toes
 */
class TOESModelExecutiveoffice extends JModelList {

	public $_cache = array();
	
    /**
     * Method to auto-populate the model state.
     */
    protected function populateState($ordering = null, $direction = null) {
        $entry_status = $this->getUserStateFromRequest($this->context.'.filter.entry_status', 'entry_status_filter', '');
        $this->setState('filter.entry_status', $entry_status);

        $entry_user = $this->getUserStateFromRequest($this->context.'.filter.entry_user', 'entry_user_filter', '');
        $this->setState('filter.entry_user', $entry_user);

        $entry_type = $this->getUserStateFromRequest($this->context.'.filter.entry_type', 'entry_type_filter', '');
        $this->setState('filter.entry_type', $entry_type);

        parent::populateState();
    }

    /**
     * Method to get a single record.
     *
     * @param	integer	The id of the primary key.
     *
     * @return	mixed	Object on success, false on failure.
     */
    public function getShowDetails($pk = null) {
    	$app = JFactory::getApplication();
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Initialise variables.
        $pk = $app->input->getInt('id',0);

        if(isset($this->_cache[$pk])) {
          return $this->_cache[$pk]; 
		}

        //$query->select(' s.show_id, s.show_start_date, s.show_end_date, s.show_flyer, s.show_motto, s.catalog_runs, s.show_paper_size, s.show_currency_used ');
        $query->select('s.show_format ,s.show_id, s.show_start_date, s.show_end_date, s.show_flyer, s.show_motto, s.show_status, s.catalog_runs, s.show_extra_text_for_confirmation, s.show_paper_size, s.show_currency_used, s.show_comments, s.show_cost_per_entry, s.show_total_cost, s.show_uses_toes, s.show_bring_your_own_cages');
		$query->select('s.show_lock_catalog, s.show_lock_late_pages');
		$query->from('#__toes_show AS s');

        $query->select('sf.show_format_id, sf.show_format');
        $query->join('LEFT', '#__toes_show_format AS sf ON sf.show_format_id = s.show_format');
        
        $query->select('ss.show_status_id, ss.show_status');
        $query->join('LEFT', '#__toes_show_status AS ss ON ss.show_status_id = s.show_status');
        
        $query->select(' v.venue_name, a.address_line_1, a.address_line_2, a.address_line_3');//city.name AS address_city, state.name AS address_state, a.address_zip_code, cntry.name AS address_country
        $query->join('LEFT', '#__toes_venue AS v ON v.venue_id = s.show_venue');
        $query->join('LEFT', '#__toes_address AS a ON a.address_id = v.venue_address');
		//$query->join('LEFT', '`#__toes_country` AS `cntry` ON `cntry`.`id` = `a`.`address_country`');
		//$query->join('LEFT', '`#__toes_states_per_country` AS `state` ON `state`.`id` = `a`.`address_state`');
		//$query->join('LEFT', '`#__toes_cities_per_state` AS `city` ON `city`.`id` = `a`.`address_city`');

        $query->select('c.club_id, c.club_name, c.club_abbreviation, c.club_on_toes_bad_debt_list');
        $query->join('LEFT', '#__toes_club_organizes_show AS cs ON cs.show = s.show_id');
        $query->join('LEFT', '#__toes_club AS c ON c.club_id = cs.club');

        $query->where('s.show_id=' . (int) $pk);

        //echo nl2br(str_replace('#__', 'j35_', $query));
        $db->setQuery($query);
        $return = $db->loadObject();

        $this->_cache[$pk] = $return;

        return $this->_cache[$pk];
    }

    public function getPendingEntries() {
    	$app = JFactory::getApplication();
        $db = $this->getDbo();
        $user = JFactory::getUser();
        
        $query = $db->getQuery(true);

        // Initialise variables.
        $pk = $app->input->getInt('id',0);

        $query->select('`e`.`entry_id`');
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		//$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		//$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
        
        $query->where('(`estat`.`entry_status`="New" OR `estat`.`entry_status`="Accepted")');
        $query->where('`e`.`entry_show` = ' . (int) $pk);

        $db->setQuery($query);
        $entries = $db->loadObjectList();
        
        return $entries;
    }

    public function getUserInformation($user_id) {
        $db	= $this->getDbo();
     	$app = JFactory::getApplication();
        $query	= $db->getQuery(true);
        
        // Initialise variables.
        $pk = $app->input->getInt('id',0);
        
		$query->select('smry.summary_id, smry.summary_user, smry.summary_show, smry.summary_single_cages, smry.summary_double_cages, smry.summary_benching_request');
		$query->select('smry.summary_grooming_space, smry.summary_personal_cages, smry.summary_remarks, smry.summary_total_fees');
		$query->select('smry.summary_fees_paid, smry.summary_benching_area, smry.summary_entry_clerk_note, smry.summary_entry_clerk_private_note');

		$query->select('u.id, u.name, u.username, u.email');

		$query->select('c.firstname, c.lastname, c.avatar, c.cb_address1, c.cb_address2, c.cb_address3, c.cb_city, c.cb_zip');
		$query->select('c.cb_state, c.cb_country, c.cb_phonenumber, c.cb_tica_region');

		$query->from('#__toes_summary AS smry');
		$query->join('LEFT', '#__users AS u ON u.id = smry.summary_user');
        $query->join('LEFT', '#__comprofiler AS c ON c.id = smry.summary_user');
                
        $query->where('smry.summary_show=' . (int) $pk);
        $query->order('c.lastname ASC, c.firstname ASC');
        
		$query->where('smry.summary_user = '.$db->quote($user_id));

        //echo nl2br(str_replace('#_', 'j35', $query));
        $db->setQuery($query);
		
		return $db->loadObject();
    }
	public function getEntries($users, $override = 0) {
    	$app = JFactory::getApplication();
        $db = $this->getDbo();

        // Initialise variables.
        $pk = $app->input->getInt('id',0);

		$query = TOESQueryHelper::getEntryFullViewQuery();
		
        $query->select('`e`.`entry_date_created` AS `timestamp`, "entry" AS `type`, 
			GROUP_CONCAT(
				DISTINCT 
					CONCAT(" ",LEFT(DATE_FORMAT(`sd`.`show_day_date`,"%W"),3), 
						IF(`sf`.`show_format` = "Alternative",
							CONCAT(" ",
								IF (`e`.`entry_participates_AM` = 1,"AM",""), 
								IF (`e`.`entry_participates_AM` = 1 AND `e`.`entry_participates_PM` = 1 ,"/",""),  
								IF (`e`.`entry_participates_PM` = 1,"PM","")
							),
							""
						)
					)  
				ORDER BY `sd`.`show_day_date`
			 ) AS `showdays`'
		);

        $query->select('GROUP_CONCAT(DISTINCT(`r`.`ring_name`)) AS `congress`');
        $query->join('LEFT', '`#__toes_entry_participates_in_congress` AS `pc` ON `pc`.`entry_id` = `e`.`entry_id`');
        $query->join('LEFT', '`#__toes_ring` AS `r` ON `r`.`ring_id`= `pc`.`congress_id`');
        
        // sandy hack to check registration number
        $query->select('`crn`.`cat_registration_number`');
        $query->join('LEFT','`#__toes_cat_registration_number` as `crn` ON `e`.`cat` = `crn`.`cat_registration_number_cat` ');
        //$query->join('LEFT','`#__toes_cat_registration_number` as `crn` ON `e`.`cat` = `crn`.`cat_registration_number_cat` ');
        $query->select('(select count(*) from `#__toes_cat_document` where `cat_document_cat_id`= `e`.`cat` ) as documents ');
        
        $query->select(' `u`.`name` AS `exhibitor`');
        
        $query->where('`e`.`entry_show`=' . (int) $pk);
        // where registration_number is not provided or is pending
        $query->where('(`crn`.`cat_registration_number` is NULL OR `crn`.`cat_registration_number` =\'\' OR LOWER(`crn`.`cat_registration_number`) =\'pending\')');
        // where beed is not HHP
        $query->where('b.breed_abbreviation <> \'HHP\' ');
        //cat has cat_documentation_approved_by_the_EO = 0
        $query->where('`cat`.`cat_documentation_approved_by_the_EO`= 0');
        // entry is created on or after 1st mat 2019
        $maydate = '2019-05-01';
        $query->where(' DATE(`e`.`entry_date_created`) >='.$db->Quote($maydate));
        
        // filtering only those entries where   number of documents > 0
		$query->where('((select count(*) from `#__toes_cat_document` where `cat_document_cat_id`= `e`.`cat` ) > 0)');
		
        
        $query->group('`e`.`cat`, `Show_Class`, `e`.`status`');
        
        // Filter by status
        $entry_status = $this->getState('filter.entry_status');
        if ($entry_status) {
            $query->where('`estat`.`entry_status` = '.$db->quote($entry_status));
        }

        // Filter by entry_user
        $entry_user = $this->getState('filter.entry_user');
        if ($entry_user && !$override) {
            $query->where('`es`.`summary_user` = '.$db->quote($entry_user));
        } else if($users) {
			$query->where('`es`.`summary_user` IN ( '.  implode(',', $users).')');
		}
		
		
        
        $query->order('`e`.`entry_date_created`');

        //echo nl2br(str_replace('#__', 'tica2019_', $query));
       // die;
        $db->setQuery($query);
        $entries = $db->loadObjectList();
		
        return $entries;
    }
    public function getEntries_entryclerk($users, $override = 0) {
    	$app = JFactory::getApplication();
        $db = $this->getDbo();

        // Initialise variables.
        $pk = $app->input->getInt('id',0);

		$query = TOESQueryHelper::getEntryFullViewQuery();
		
        $query->select('`e`.`entry_date_created` AS `timestamp`, "entry" AS `type`, 
			GROUP_CONCAT(
				DISTINCT 
					CONCAT(" ",LEFT(DATE_FORMAT(`sd`.`show_day_date`,"%W"),3), 
						IF(`sf`.`show_format` = "Alternative",
							CONCAT(" ",
								IF (`e`.`entry_participates_AM` = 1,"AM",""), 
								IF (`e`.`entry_participates_AM` = 1 AND `e`.`entry_participates_PM` = 1 ,"/",""),  
								IF (`e`.`entry_participates_PM` = 1,"PM","")
							),
							""
						)
					)  
				ORDER BY `sd`.`show_day_date`
			 ) AS `showdays`'
		);

        $query->select('GROUP_CONCAT(DISTINCT(`r`.`ring_name`)) AS `congress`');
        $query->join('LEFT', '`#__toes_entry_participates_in_congress` AS `pc` ON `pc`.`entry_id` = `e`.`entry_id`');
        $query->join('LEFT', '`#__toes_ring` AS `r` ON `r`.`ring_id`= `pc`.`congress_id`');
        
        // sandy hack to check registration number
        $query->select('`crn`.`cat_registration_number`');
        $query->join('LEFT','`#__toes_cat_registration_number` as `crn` ON `e`.`cat` = `crn`.`cat_registration_number_cat` ');
        //$query->join('LEFT','`#__toes_cat_registration_number` as `crn` ON `e`.`cat` = `crn`.`cat_registration_number_cat` ');
        $query->select('(select count(*) from `#__toes_cat_document` where `cat_document_cat_id`= `e`.`cat` ) as documents ');
        
        $query->select(' `u`.`name` AS `exhibitor`');
        
        $query->where('`e`.`entry_show`=' . (int) $pk);
        $query->group('`e`.`cat`, `Show_Class`, `e`.`status`');
        
        // Filter by status
        $entry_status = $this->getState('filter.entry_status');
        if ($entry_status) {
            $query->where('`estat`.`entry_status` = '.$db->quote($entry_status));
        }

        // Filter by entry_user
        $entry_user = $this->getState('filter.entry_user');
        if ($entry_user && !$override) {
            $query->where('`es`.`summary_user` = '.$db->quote($entry_user));
        } else if($users) {
			$query->where('`es`.`summary_user` IN ( '.  implode(',', $users).')');
		}
        
        $query->order('`e`.`entry_date_created`');

       //echo nl2br(str_replace('#__', 'tica2019_', $query));
       // die;
        $db->setQuery($query);
        $entries = $db->loadObjectList();
		
        return $entries;
    }

    public function getPlaceholders($users, $override = 0) {
    	$app = JFactory::getApplication();
        $db = $this->getDbo();
        
        $query = $db->getQuery(true);

        // Initialise variables.
        $pk = $app->input->getInt('id',0);

        $query->select('p.*, pd.*, pd.placeholder_day_date_created AS timestamp, "placeholder" AS type, 
			GROUP_CONCAT(
				DISTINCT CONCAT(" ", LEFT(DATE_FORMAT(sd.show_day_date,"%W"),3) , 
					IF(sf.show_format = "Alternative",
						CONCAT(" ",
							IF (pd.placeholder_participates_AM = 1,"AM",""), 
							IF (pd.placeholder_participates_AM = 1 AND pd.placeholder_participates_PM = 1 ,"/",""),  
							IF (pd.placeholder_participates_PM = 1,"PM","")
						),
						""
					)
				)
				ORDER BY sd.show_day_date 
			) AS showdays, es.entry_status');
        $query->from('#__toes_placeholder AS p');
        $query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
        $query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
        $query->join('LEFT', '#__toes_show AS s ON s.show_id = p.placeholder_show');
        $query->join('LEFT', '#__toes_show_format AS sf ON sf.show_format_id = s.show_format');
        $query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');

        $query->select(' u.name AS exhibitor');
        $query->join('LEFT', '#__users AS u ON u.id = p.placeholder_exhibitor');
        
        $query->where('p.placeholder_show = ' . $pk);
        $query->group('p.placeholder_id, pd.placeholder_day_placeholder_status');
        
        // Filter by status
        $entry_status = $this->getState('filter.entry_status');
        if ($entry_status) {
            $query->where('es.entry_status = '.$db->quote($entry_status));
        }

        // Filter by entry_user
        $entry_user = $this->getState('filter.entry_user');
        if ($entry_user && !$override) {
            $query->where('p.placeholder_exhibitor = '.$db->quote($entry_user));
        } else if($users) {
			$query->where('p.placeholder_exhibitor IN ( '.  implode(',', $users).')');
		}
        
        $query->order('pd.placeholder_day_date_created ');

        //echo nl2br(str_replace('#__', 'j35_', $query));
        $db->setQuery($query);
        $placeholders = $db->loadObjectList();
        
        return $placeholders;
    }
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param	type	The table type to instantiate
     * @param	string	A prefix for the table class name. Optional.
     * @param	array	Configuration array for model. Optional.
     * @return	JTable	A database object
     */
    public function getTable($type = 'Show', $prefix = 'ToesTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getEntrystatuseoptions() {
    	$app = JFactory::getApplication();
        $db	= $this->getDbo();
        $query	= $db->getQuery(true);
        
        $query->select('entry_status as value, entry_status as text');
        $query->from('#__toes_entry_status');

        //echo nl2br(str_replace('#_', 'j35', $query));
        $db->setQuery($query);
        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TOES_SELECT_STATUS')));
        
        return $options;
    }

    public function getListQuery() {
        $db	= $this->getDbo();
     	$app = JFactory::getApplication();
        $query	= $db->getQuery(true);
        
        // Initialise variables.
        $pk = $app->input->getInt('id',0);
        
		$query->select('smry.summary_id, smry.summary_user, smry.summary_show, smry.summary_single_cages, smry.summary_double_cages, smry.summary_benching_request');
		$query->select('smry.summary_grooming_space, smry.summary_personal_cages, smry.summary_remarks, smry.summary_total_fees');
		$query->select('smry.summary_fees_paid, smry.summary_benching_area, smry.summary_entry_clerk_note, smry.summary_entry_clerk_private_note');

		$query->select('u.id, u.name, u.username, u.email');

		$query->select('c.firstname, c.lastname, c.avatar, c.cb_address1, c.cb_address2, c.cb_address3, c.cb_city, c.cb_zip');
		$query->select('c.cb_state, c.cb_country, c.cb_phonenumber, c.cb_tica_region');
		
		//$query->select("`cprofcity`.`name` AS `address_city`, `cprofstate`.`name` AS `address_state`,`cprofcntry`.`name` AS `address_country`");

		$query->from('#__toes_summary AS smry');
		$query->join('LEFT', '#__users AS u ON u.id = smry.summary_user');
        $query->join('LEFT', '#__comprofiler AS c ON c.id = smry.summary_user');
		//$query->join("left", "`#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `c`.`cb_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `c`.`cb_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `c`.`cb_city`");
                
        $query->where('smry.summary_show=' . (int) $pk);
        $query->order('c.lastname ASC, c.firstname ASC');
        
		// Filter by entry_user
        $entry_type = $this->getState('filter.entry_type');
		
        // Filter by status
        $entry_status = $this->getState('filter.entry_status');
        if ($entry_status) {
			$query->join('LEFT', '#__toes_entry AS e ON e.summary = smry.summary_id');
			$query->join('LEFT', '#__toes_entry_status AS estat ON estat.entry_status_id = e.status');

			$query->join('LEFT', '#__toes_placeholder AS p ON p.placeholder_summary= smry.summary_id');
			$query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
			$query->join('LEFT', '#__toes_entry_status AS pstat ON pstat.entry_status_id = pd.placeholder_day_placeholder_status');
			
			$query->where('(`estat`.`entry_status` = '.$db->quote($entry_status).' OR `pstat`.`entry_status` = '.$db->quote($entry_status).')' );
			
			if ($entry_type == 1) {
				$query->where('e.entry_id IS NOT NULL');
			} else if($entry_type == 2) {
				$query->where('p.placeholder_id IS NOT NULL');
			}
        } else {
			if ($entry_type == 1) {
				$query->join('LEFT', '#__toes_entry AS e ON e.summary = smry.summary_id');
				$query->where('e.entry_id IS NOT NULL');
			} else if($entry_type == 2) {
				$query->join('LEFT', '#__toes_placeholder AS p ON p.placeholder_summary= smry.summary_id');
				$query->where('p.placeholder_id IS NOT NULL');
			}
		}

		
        
		
		// Filter by entry_user
        $entry_user = $this->getState('filter.entry_user');
        if ($entry_user) {
            $query->where('smry.summary_user = '.$db->quote($entry_user));
        }
		
		$query->group('smry.summary_user');

        //echo nl2br(str_replace('#_', 'j35', $query));
       //die;
        return $query;
    }

    public function getEntryuseroptions() {
        $db	= $this->getDbo();
     	$app = JFactory::getApplication();
        $query	= $db->getQuery(true);
        
        // Initialise variables.
        $pk = $app->input->getInt('id',0);
        
        $query->select('distinct(s.summary_user) as value, IF(c.lastname IS NOT NULL OR c.firstname IS NOT NULL, concat(c.firstname," ",c.lastname), u.name) as text');
        $query->from('#__toes_summary AS s');
        $query->join('left','#__comprofiler AS c ON c.id = s.summary_user');
		$query->join('left','#__users AS u ON u.id = s.summary_user');
                
        $query->where('s.summary_show=' . (int) $pk);
        $query->order('c.lastname ASC, c.firstname ASC');

        //echo nl2br(str_replace('#_', 'j35', $query));
        $db->setQuery($query);
        $options = $db->loadObjectList();
        array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TOES_SELECT_USER')));
        
        return $options;
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

    function updateStatus($entry_id, $status)
    {
    	$app = JFactory::getApplication();
        $user   = JFactory::getUser();
        $db     = JFactory::getDbo();  
        
        $is_allowed = false;
       
        $org_entry = TOESHelper::getEntryDetails($entry_id);
        
        $cat_id = $org_entry->cat;
        $show_id = $org_entry->show_id;
		
		$show = TOESHelper::getShowDetails($show_id);
        
        $org_status = $org_entry->entry_status;
        $summary_id = $org_entry->summary;
        
        $user_summary = TOESHelper::getSummary($show_id, $user->id);
                
        $is_official = (TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) 
            || TOESHelper::isAdmin())?true:false;
        
        $sql = $db->getQuery(true);
        $sql->select('sd.`show_day_id`');
        $sql->from('`#__toes_entry` AS e');
        $sql->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = e.show_day');
        $sql->where('e.`cat` = '.$cat_id);
        $sql->where('sd.`show_day_show` = '.$show_id);
        $sql->where('e.`status` = '.$org_entry->status);

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
                if( ($user_summary && $summary_id == $user_summary->summary_id) || $is_official)
                    $is_allowed = true;
                else
                    $this->setError(JText::_('COM_TOES_NOAUTH'));
                break;
            default:
                $is_allowed = true;
                break;
        }
        
        $error = '';
        if($is_allowed)
        {
            $query_excuted = false;
            for($i = 0; $i < count($showdays); $i++)
            {
                if($status == "Accepted") {
                    $entry_status = "Accepted";
					
					$ring_timing = null;
					if($org_entry->entry_participates_AM && $org_entry->entry_participates_PM){
						$ring_timing = 3;
					} else if($org_entry->entry_participates_AM){
						$ring_timing = 1;
					} else if($org_entry->entry_participates_PM){
						$ring_timing = 2;
					}
					
                    if($is_official)
                    {
						if($ring_timing == 3) {
							if(!TOESHelper::getAvailableSpaceforDayforEC($showdays[$i], 1) && !TOESHelper::getAvailableSpaceforDayforEC($showdays[$i], 2)) {
								$error = JText::_('COM_TOES_COULD_NOT_ACTIVATE');
								continue;
							}
						} else if(!TOESHelper::getAvailableSpaceforDayforEC($showdays[$i], $ring_timing)) {
                            $error = JText::_('COM_TOES_COULD_NOT_ACTIVATE');
                            continue;
                        }
                    }
					elseif($ring_timing == 3)
					{
						if(!TOESHelper::getAvailableSpaceforDay($showdays[$i], 1) && !TOESHelper::getAvailableSpaceforDay($showdays[$i], 2)) {
							$error = JText::_('COM_TOES_COULD_NOT_ACTIVATE');
							continue;
						}
					}
                    elseif(!TOESHelper::getAvailableSpaceforDay($showdays[$i], $ring_timing))
                    {
                        switch ($org_status)
                        {
                            case 'Cancelled':
                                $entry_status = 'Waiting List';
                                break;
                            case 'Cancelled & Confirmed':
                                $entry_status = 'Waiting List & Confirmed';
                                break;
                            case 'Cancelled & Paid':
                                $entry_status = 'Waiting List & Paid';
                                break;
                        }
                    }
                    else
                    {
                        if($org_status == 'Cancelled & Confirmed') {
                            $entry_status = 'Confirmed';
                        } else if($org_status == 'Cancelled & Paid') {
                            $entry_status = 'Confirmed & Paid';
                        }
                    }
                } elseif($status == "Cancelled") {
                    $entry_status = "Cancelled";

					$query = "SELECT `entry_id` "
						." FROM `#__toes_entry` "
						." WHERE `cat` = {$cat_id} "
						." AND show_day = {$showdays[$i]}";

					$db->setQuery($query);
					$entry_id = $db->loadResult();

					if($org_status == "New" || $org_status == "Rejected" || $org_status == 'Waiting List') {
						
						$query = "DELETE "
                                ." FROM `#__toes_entry` "
                                ." WHERE `cat` = {$cat_id} "
                                ." AND show_day = {$showdays[$i]}";

                        $db->setQuery($query);
                        if($db->query())
                        {
							/* log for deleted entry*/
							$query = $db->getQuery(true);
							$query->insert('`#__toes_log_entries`');
							$query->set('`entry_id` = '.$entry_id);
							$query->set('`cat` = '.$cat_id);
							$query->set('`exhibitor` = '.$org_entry->summary_user);
							$query->set('`show_day` = '.$showdays[$i]);
							if($is_official) {
								$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_ENTRYCLERK')));
							} else {
								$query->set('`description` = '.$db->quote(JText::_('COM_TOES_DELETED_BY_USER')));
							}
							$query->set('`changed_by` = '.$user->id);
							$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
							$db->setQuery($query);
							$db->query();
							
							$query = "DELETE FROM `#__toes_entry_participates_in_congress` 
									WHERE `entry_id` = {$entry_id}";
							$db->setQuery($query);
							$db->query();
							
                            $query = $db->getQuery(true);
                            $query->select('e.`entry_id`');
                            $query->from('`#__toes_entry` AS e');
                            $query->where('e.`summary` = '.$summary_id);
                            $db->setQuery($query);
                            $e = $db->loadResult();
							if(!$e) 
							{
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
										$query->set('`exhibitor` = '.$org_entry->summary_user);
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
                        }
                    } else if($org_status == 'Confirmed') {
                        $entry_status = 'Cancelled & Confirmed';
                    } else if($org_status == 'Confirmed & Paid') {
                        $entry_status == "Cancelled & Paid";
                    }
                }
                else
                    $entry_status = $status;

                if($entry_status) {
                    $query = $db->getQuery(true);
                
                    $query->update('#__toes_entry AS e');
                    $query->set('e.`status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = '.$db->quote($entry_status).')');

                    $query->where('e.`cat` = '.$cat_id);
                    $query->where('e.`show_day` = '.$showdays[$i]);

                    $db->setQuery($query);
                    if($db->query()) {
                        $query_excuted = true;
                        if($status == "Cancelled"){
                            /* log for cancelled entry*/
                            $query = $db->getQuery(true);
                            $query->insert('`#__toes_log_entries`');
                            $query->set('`entry_id` = '.$entry_id);
                            $query->set('`cat` = '.$cat_id);
                            $query->set('`exhibitor` = '.$org_entry->summary_user);
                            $query->set('`show_day` = '.$showdays[$i]);
                            if($is_official) {
                                $query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_ENTRYCLERK')));
                            } else {
                                $query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_USER')));
                            }
                            $query->set('`changed_by` = '.$user->id);
                            $query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
                            $db->setQuery($query);
                            $db->query();
                        }
                    }
                }
            }
            
            if($query_excuted)
            {
                if($status == "Cancelled")
                {
                    $query = $db->getQuery(true);
                    $query->select('e.`entry_id`');
                    $query->from('`#__toes_entry` AS e');
                    $query->where('e.`summary` = '.$summary_id);
                    $query->join('LEFT',' `#__toes_entry_status` AS es ON e.`status` = es.`entry_status_id`' );
                    $query->where('es.`entry_status` NOT LIKE "Rejected"');
                    $query->where('es.`entry_status` NOT LIKE "Cancelled"');
                    $db->setQuery($query);
                    $entries = $db->loadResult();

                    $query = $db->getQuery(true);
                    $query->select('p.`placeholder_id`');
                    $query->from('`#__toes_placeholder` AS p');
                    $query->where('p.`placeholder_summary` = '.$summary_id);
                    $query->join('LEFT',' `#__toes_placeholder_day` AS pd ON pd.`placeholder_day_placeholder` = p.`placeholder_id`' );
                    $query->join('LEFT',' `#__toes_entry_status` AS es ON es.`entry_status_id` = pd.`placeholder_day_placeholder_status`' );
                    $query->where('es.`entry_status` NOT LIKE "Rejected"');
                    $query->where('es.`entry_status` NOT LIKE "Cancelled"');
                    $db->setQuery($query);
                    $placeholders = $db->loadResult();

                    if(!$entries && !$placeholders)
                    {
                        $query = $db->getQuery(true);
                        $query->update('`#__toes_summary` AS smry');
                        $query->set('smry.`summary_status` = (SELECT s.`summary_status_id` FROM `#__toes_summary_status` AS s WHERE s.`summary_status` = "Cancelled")');
                        $query->where('smry.`summary_id` = '.$summary_id);
                        $db->setQuery($query);
                        $db->query();

						/* log for cancelled summary*/
						$query = $db->getQuery(true);
						$query->insert('`#__toes_log_summaries`');
						$query->set('`summary_id` = '.$summary_id);
						$query->set('`exhibitor` = '.$org_entry->summary_user);
						
						if($is_official) {
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_ENTRYCLERK')));
						} else {
							$query->set('`description` = '.$db->quote(JText::_('COM_TOES_CANCELLED_BY_USER')));
						}
						$query->set('`changed_by` = '.$user->id);
						$query->set('`time_changed` = '.$db->quote(date('Y-m-d h:i:s')));
						$db->setQuery($query);
						$db->query();
					}  
                    
					if($show->show_use_waiting_list)
						TOESHelper::checkWaitingList($show_id);
					
					if($org_status != "New" && ($user_summary && $summary_id == $user_summary->summary_id))
					{
						$user_info = TOESHelper::getUserInfo($user->id);
						 
						$mailTemplate = TOESMailHelper::getTemplate('entry_cancelled_entryclerk_notification');

						if($mailTemplate) {
							$subject = $mailTemplate->mail_subject;
							$body = $mailTemplate->mail_body;
						} else {
							$subject = JText::_('COM_TOES_NOTIFICATION_EXHIBITOR_CANCELLED_ENTRY_SUBJECT');
							$body = JText::_('COM_TOES_NOTIFICATION_EXHIBITOR_CANCELLED_ENTRY');
						}
						 
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
					
						$subject = str_replace('[club]', $show->club_name, $subject);
						$subject = str_replace('[showdates]', $show_date, $subject);
						$subject = str_replace('[show_location]', $show->Show_location, $subject);
					
						$show_days = TOESHelper::getShowDays($show_id);
						$selected_show_days = array();
						foreach ($show_days as $day)
						{
							if(in_array($day->show_day_id, $showdays))
								$selected_show_days[] = date('l', strtotime($day->show_day_date));
						}
							
						$body = str_replace('[exhibitor]', $user_info->lastname.' '.$user_info->firstname , $body);
						$body = str_replace('[cat]', $org_entry->copy_cat_name, $body);
						$body = str_replace('[show_class]', $org_entry->Show_Class, $body);
						$body = str_replace('[show_days]', implode(',', $selected_show_days), $body);
						 
						$entryClerks = TOESHelper::getEntryClerks($show_id);
						$showManagers = TOESHelper::getShowManagers($show_id);
						 
						if($show->show_use_club_entry_clerk_address)
						{
							$entryClerk = new stdClass();
							$entryClerk->entry_clerk_name = JText::_("COM_TOES_SHOW_ENTRYCLERK");
							$entryClerk->entry_clerk_email = $show->show_email_address_entry_clerk;
							$entryClerks = array();
							$entryClerks[] = $entryClerk;
						}
						$config     = JFactory::getConfig();
				        $fromname   = $config->get('fromname');
				        $fromemail  = $config->get('mailfrom');
						
						if($entryClerks) {
							foreach($entryClerks as $usr) {
								/* 
								$mail = JFactory::getMailer();
								 
								$mail->SetFrom($fromemail, $fromname);
								$mail->setSubject($subject);
								$mail->setBody($body);
								$mail->addRecipient($usr->entry_clerk_email);
								$mail->IsHTML(TRUE);
								 
								$mail->Send();
								*/
								
								TOESMailHelper::sendMail('entry_cancelled_entryclerk_notification', $subject, $body, $usr->entry_clerk_email);
							}
						} else if($showManagers) {
							foreach($showManagers as $usr) {
								/* 
								$mail = JFactory::getMailer();
								 
								$mail->SetFrom($fromemail, $fromname);
								$mail->setSubject($subject);
								$mail->setBody($body);
								$mail->addRecipient($usr->show_manager_email);
								$mail->IsHTML(TRUE);
								 
								$mail->Send();
								*/
								TOESMailHelper::sendMail('entry_cancelled_entryclerk_notification', $subject, $body, $usr->show_manager_email);
							}
						}
					}
                } 
                else if($status == "Accepted") 
                {
                    $query = $db->getQuery(true);
                    $query->select('e.`entry_id`');
                    $query->from('`#__toes_entry` AS e');
                    $query->where('e.`summary` = '.$summary_id);
                    $query->join('LEFT',' `#__toes_entry_status` AS es ON e.`status` = es.`entry_status_id`' );
                    $query->where('es.`entry_status` NOT LIKE "Rejected"');
                    $query->where('es.`entry_status` NOT LIKE "Cancelled"');
                    $db->setQuery($query);
                    if($db->loadResult())
                    {
                        $query = $db->getQuery(true);
                        $query->select('s.`summary_status`');
                        $query->from('#__toes_summary AS smry');
                        $query->join('LEFT',' `#__toes_summary_status` AS s ON smry.`summary_status` = s.`summary_status_id`' );
                        $query->where('smry.`summary_id` = '.$summary_id);
                        $db->setQuery($query);
                        if($db->loadResult() == 'Cancelled')
                        {
                            $query = $db->getQuery(true);
                            $query->update('#__toes_summary AS smry');
                            $query->set('smry.`summary_status` = (SELECT s.`summary_status_id` FROM `#__toes_summary_status` AS s WHERE s.`summary_status` = "Updated")');
                            $query->where('smry.`summary_id` = '.$summary_id);
                            $db->setQuery($query);
                            $db->query();
                        }
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
    
    function confirmEntries($user_id, $show_id, $confirm_new = true)
    {
        $db         = JFactory::getDbo();
        
        $user       = TOESHelper::getUserInfo($user_id);
        $show       = TOESHelper::getShowDetails($show_id);
		$isAlternative = ($show->show_format == 'Alternative')?true:false;
		$isContinuous = ($show->show_format == 'Continuous')?true:false;
        $club       = TOESHelper::getClub($show->show_id);
        $summary    = TOESHelper::getSummary($show_id, $user_id);
        
        $showManagers = TOESHelper::getShowManagers($show_id);
        $entryClerks = TOESHelper::getEntryClerks($show_id);
        
        $language = JFactory::getLanguage();
        
        $db->setQuery("SELECT * FROM #__users WHERE id = {$user_id}");
        $user_details = $db->loadObject();
        $params = new JRegistry($user_details->params);
        $language->load('com_toes', JPATH_BASE, $params->get('language'));
        
        unset($query);
        $query = TOESQueryHelper::getEntryFullViewQuery();

        $query->select('`r`.`ring_name` AS `congress_name`');
        $query->join('LEFT', '`#__toes_entry_participates_in_congress` AS `pc` ON `pc`.`entry_id`= `e`.`entry_id`');
        $query->join('LEFT', '`#__toes_ring` AS `r` ON `r`.`ring_id`= `pc`.`congress_id`');
        
        $query->where('`es`.`summary_user` = '.$user_id);
        $query->where('`e`.`entry_show` = '.$show_id);
        $query->where('`estat`.`entry_status` = "Confirmed" OR `estat`.`entry_status` = "Confirmed & Paid"');
        
        $db->setQuery($query);
        $old_entries = $db->loadObjectList();
        
        $temp_entries = array();
        foreach($old_entries as $entry)
        {
            $temp_entries[$entry->cat.'_'.$entry->Show_Class][] = $entry;
        }
        $previous_entries = $temp_entries;

        $query = $db->getQuery(true);
        $query->select('p.*, sd.show_day_date');
        $query->from('#__toes_placeholder_day as pd');
        $query->join('left', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
        $query->join('left','#__toes_placeholder as p ON p.placeholder_id = pd.placeholder_day_placeholder');
        $query->where('p.placeholder_exhibitor = '.$user_id);
        $query->where('p.placeholder_show = '.$show_id);

        $query->join('left', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
        $query->where('es.entry_status = "Confirmed" OR es.entry_status = "Confirmed & Paid"');

        $db->setQuery($query);
        $old_placeholders = $db->loadObjectList();
        
        $temp_placeholders = array();
        foreach($old_placeholders as $placeholder)
        {
            $temp_placeholders[$placeholder->placeholder_id][] = $placeholder;
        }
        $old_placeholders = $temp_placeholders;

        if($confirm_new)
        {
	        $query = TOESQueryHelper::getEntryFullViewQuery();

            $query->select('`r`.`ring_name` AS `congress_name`');
            $query->join('LEFT', '`#__toes_entry_participates_in_congress` AS `pc` ON `pc`.`entry_id`= `e`.`entry_id`');
            $query->join('LEFT', '`#__toes_ring` AS `r` ON `r`.`ring_id`= `pc`.`congress_id`');

            $query->where('`es`.`summary_user` = '.$user_id);
            $query->where('`e`.`entry_show` = '.$show_id);
            $query->where('`estat`.`entry_status` = "Accepted"');
			
            $db->setQuery($query);
            $new_entries = $db->loadObjectList();
            
            $query = $db->getQuery(true);
            $query->select('p.*, pd.*, sd.show_day_date');
            $query->from('#__toes_placeholder_day as pd');
            $query->join('left', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
            $query->join('left','#__toes_placeholder as p ON p.placeholder_id = pd.placeholder_day_placeholder');
            $query->where('p.placeholder_exhibitor = '.$user_id);
            $query->where('p.placeholder_show = '.$show_id);
            
            $query->join('left', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
            $query->where('es.entry_status = "Accepted"');

            $db->setQuery($query);
            $new_placeholders = $db->loadObjectList();

            if($new_placeholders)
            {
                $temp_placeholders = array();
                foreach($new_placeholders as $placeholder)
                {
                    $temp_placeholders[$placeholder->placeholder_id][] = $placeholder;

                    $query = $db->getQuery(true);
                    $query->update('#__toes_placeholder_day AS pd');
                    $query->set('pd.`placeholder_day_placeholder_status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = "Confirmed")');
                    $query->where('pd.`placeholder_day_id` = '.$placeholder->placeholder_day_id);
                    $db->setQuery($query);
                    $db->query();

                }
                $new_placeholders = $temp_placeholders;
            }

            if(!$new_entries && !$new_placeholders)
            {
                if($summary->summary_status == 'Confirmed')
                {
                    $this->setError('resend');
                    return false;
                }
                else {
                    $new_entries = array();
				}
            }

            $temp_entries = array();
            foreach($new_entries as $entry)
            {
                $temp_entries[$entry->cat.'_'.$entry->Show_Class][] = $entry;

                $query = $db->getQuery(true);
                $query->update('#__toes_entry AS e');
                $query->set('e.`status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = "Confirmed")');
                $query->where('e.`entry_id` = '.$entry->entry_id);
                $db->setQuery($query);
                $db->query();

            }
            $new_entries = $temp_entries;
        }
        else
        {
            $new_entries = array();
            $new_placeholders = array();
        }
        
        if($summary->summary_status != 'Confirmed')
        {
            $query = $db->getQuery(true);
            $query->update('#__toes_summary AS smry');
            $query->set('smry.`summary_status` = (SELECT s.`summary_status_id` FROM `#__toes_summary_status` AS s WHERE s.`summary_status` = "Confirmed")');
            $query->where('smry.`summary_user` = '.$user_id);
            $query->where('smry.`summary_show` = '.$show_id);
            $db->setQuery($query);
            $db->query();
        }

		$mailTemplate = TOESMailHelper::getTemplate('entry_confirmation_notification', $user_id);

		if($mailTemplate) {
			$subject = $mailTemplate->mail_subject;
			$body = $mailTemplate->mail_body;
		} else {
			$subject = JText::_('COM_TOES_CONFIRMATION_EMAIL_SUBJECT');
			$body = JText::_('COM_TOES_CONFIRMATION_EMAIL_BODY');
		}
        
        $cat_block = JText::_('COM_TOES_CONFIRMATION_EMAIL_CAT_BLOCK');
        
        $body = str_replace('[Firstname]', $user->firstname, $body);
        $body = str_replace('[Lastname]', $user->lastname, $body);
        
        $body = str_replace('[club_abbreviation]', $club->club_abbreviation, $body);
        
        $body = str_replace('[City]', $show->address_city, $body);
        $body = str_replace('[, [State]]', $show->address_state?', '.$show->address_state:'', $body);
        $body = str_replace('[Country]', $show->address_country, $body);

        $body = str_replace('[total_sum]', $summary->summary_total_fees, $body);
        $body = str_replace('[sum_paid]', $summary->summary_fees_paid, $body);
        $body = str_replace('[due_sum]', ($summary->summary_total_fees - $summary->summary_fees_paid), $body);
        
        if($show->show_extra_text_for_confirmation)
        {
            $extra_info = str_replace('[extra_information]', $show->show_extra_text_for_confirmation, JText::_('COM_TOES_CONFIRMATION_EMAIL_EXTRA_INFORMATION_BLOCK'));
            $body = str_replace('[Extra Information Block]', $extra_info, $body);
        }
        else {
            $body = str_replace('[Extra Information Block]', '', $body);
        }
        
        $body = str_replace('[show_currency_used]', $show->show_currency_used, $body);

        $paypal_info = JText::_('COM_TOES_CONFIRMATION_EMAIL_PAYPAL_BLOCK');
        if($club->club_paypal)
            $paypal_info = str_replace ('[paypal_address]',$club->club_paypal,$paypal_info);
        else
            $paypal_info ='';
        
        $is_bank_info_available = $club->club_iban || $club->club_bic || $club->club_account_holder_name;
        
        $bank_info = JText::_('COM_TOES_CONFIRMATION_EMAIL_BANK_BLOCK');
        if($is_bank_info_available)
        {
            $bank_info = str_replace('[IBAN_number]', $club->club_iban, $bank_info);
            $bank_info = str_replace('[BIC_Code]', $club->club_bic, $bank_info);
            $bank_info = str_replace('[account_holder_name]', $club->club_account_holder_name, $bank_info);
            $bank_info = str_replace('[account_holder_address]', $club->club_account_holder_address, $bank_info);
            $bank_info = str_replace('[account_holder_ZIP]', $club->club_account_holder_zip, $bank_info);
            $bank_info = str_replace('[account_holder_city]', $club->club_account_holder_city, $bank_info);
            $bank_info = str_replace('[account_holder_state]', $club->club_account_holder_state, $bank_info);
            $bank_info = str_replace('[account_holder_country]', $club->club_account_holder_country, $bank_info);
        }
        else
            $bank_info = '';

        if($paypal_info && $bank_info)
        {
            $payment_info = JText::_('COM_TOES_CONFIRMATION_EMAIL_PAYMENT_BLOCK');
            $payment_info = str_replace('[PAYPAL Block]', $paypal_info, $payment_info);
            $payment_info = str_replace('[BANK Block]', $bank_info, $payment_info);

            $body = str_replace('[PAYMENT Block]', $payment_info, $body);
        }
        else
            $body = str_replace('[PAYMENT Block]', '', $body);
        
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
        
        $body = str_replace('[Startdate] - [Enddate]', $show_date, $body);

		// 30-10-2014 e-ware.be ticket 8
        $subject = str_replace('[club name]', $club->club_name, $subject);
        $subject = str_replace('[showdates]', $show_date, $subject);
		
        $new_entries_text = '';
        foreach($new_entries as $item) {
            $days = array();
            $congress_names = array();
            foreach($item as $entry){
				if(!$isContinuous)
				{
					$day_string = date('D', strtotime($entry->show_day_date));
					if($isAlternative)
					{
						if($entry->entry_participates_AM)
						{
							$day_string .= ' AM';
							if($entry->entry_participates_PM)
							{
								$day_string .= '/PM';
							}
						}
						elseif($entry->entry_participates_PM)
						{
							$day_string .= ' PM';
						}
					}
					$days[] = $day_string;
				}
                if($entry->congress_name)
                    $congress_names[] = $entry->congress_name;
            }
			if($isContinuous)
				$days = JText::_('JALL');
			else
				$days = implode(',',array_unique($days));
            if($congress_names)
                $congress_names = implode(',',$congress_names);
            else
                $congress_names = '';

            $new_entries_text .= $cat_block;

            $new_entries_text = str_replace('[cat_prefix]', $entry->cat_prefix_abbreviation, $new_entries_text);
            $new_entries_text = str_replace('[cat_title]', $entry->cat_title_abbreviation, $new_entries_text);
            $new_entries_text = str_replace('[cat_suffix]', $entry->cat_suffix_abbreviation, $new_entries_text);
            $new_entries_text = str_replace('[cat_name]', $entry->copy_cat_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_registration_number]', JText::_('COM_TOES_REGISTRATION_NUMBER').' : '.$entry->copy_cat_registration_number, $new_entries_text);
            $new_entries_text = str_replace('[cat_breed]', JText::_('COM_TOES_BREED').' : '.$entry->breed_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_gender]', JText::_('COM_TOES_GENDER').' : '.$entry->gender_name, $new_entries_text);
            $new_entries_text = str_replace('[show_class]', JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER').' : '.$entry->Show_Class, $new_entries_text);
            $new_entries_text = str_replace('[cat_category]', JText::_('COM_TOES_CATEGORY').' : '.$entry->category, $new_entries_text);
            $new_entries_text = str_replace('[cat_division]', JText::_('COM_TOES_DIVISION').' : '.$entry->division_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_color]', JText::_('COM_TOES_COLOR').' : '.$entry->color_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_date_of_birth]', JText::_('COM_TOES_DATE_OF_BIRTH').' : '.date('M d, Y',strtotime($entry->copy_cat_date_of_birth)), $new_entries_text);
            $new_entries_text = str_replace('[age]', JText::_('COM_TOES_CAT_AGE').' : '.($entry->age_years?$entry->age_years.' Y':'').' '.($entry->age_months?$entry->age_months.' M':''), $new_entries_text);
            $new_entries_text = str_replace('[show_days]', JText::_('COM_TOES_ENTRY_SHOW_DAYS').' : '.$days, $new_entries_text);
            $new_entries_text = str_replace('[cat_sire]', JText::_('COM_TOES_SIRE').' : '.$entry->copy_cat_sire_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_dam]', JText::_('COM_TOES_DAM').' : '.$entry->copy_cat_dam_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_breeder]', JText::_('COM_TOES_BREEDER').' : '.$entry->copy_cat_breeder_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_owner]', JText::_('COM_TOES_OWNER').' : '.$entry->copy_cat_owner_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_lessee]', JText::_('COM_TOES_LESSEE').' : '.$entry->copy_cat_lessee_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_agent]', JText::_('COM_TOES_AGENT').' : '.$entry->copy_cat_agent_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_competitive_region]', JText::_('COM_TOES_COMPETITIVE_REGION').' : '.$entry->competitive_region_name, $new_entries_text);
            $new_entries_text = str_replace('[cat_for_sale]', JText::_('COM_TOES_ENTRY_FOR_SALE').' : '.($entry->for_sale?JText::_('JYES'):JText::_('JNO')), $new_entries_text);
            
            if($congress_names)
                $new_entries_text = str_replace('[congress]', JText::_('COM_TOES_ENTRY_CONGRESS').' : '.$congress_names, $new_entries_text);
            else
                $new_entries_text = str_replace('[congress]', '', $new_entries_text);
        }
        $body = str_replace('[New Entries Block]', $new_entries_text?$new_entries_text:JText::_('COM_TOES_NO_NEW_CATS_CONFIRMED'), $body);
        
        $previous_entries_text = '';
        foreach($previous_entries as  $item) {
            $days = array();
            $congress_names = array();
            foreach($item as $entry){
				if(!$isContinuous)
				{
					$day_string = date('D', strtotime($entry->show_day_date));
					if($isAlternative)
					{
						if($entry->entry_participates_AM)
						{
							$day_string .= ' AM';
							if($entry->entry_participates_PM)
							{
								$day_string .= '/PM';
							}
						}
						elseif($entry->entry_participates_PM)
						{
							$day_string .= ' PM';
						}
					}
					$days[] = $day_string;
				}
                if($entry->congress_name)
                    $congress_names[] = $entry->congress_name;
            }
			if($isContinuous)
				$days = JText::_('JALL');
			else
	            $days = implode(',',array_unique($days));
            if($congress_names)
                $congress_names = implode(',',$congress_names);
            else
                $congress_names = '';

            $previous_entries_text .= $cat_block;

            $previous_entries_text = str_replace('[cat_prefix]', $entry->cat_prefix_abbreviation.' ', $previous_entries_text);
            $previous_entries_text = str_replace('[cat_title]', $entry->cat_title_abbreviation.' ', $previous_entries_text);
            $previous_entries_text = str_replace('[cat_suffix]', $entry->cat_suffix_abbreviation, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_name]', $entry->copy_cat_name.' ', $previous_entries_text);
            $previous_entries_text = str_replace('[cat_registration_number]', JText::_('COM_TOES_REGISTRATION_NUMBER').' : '.$entry->copy_cat_registration_number, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_breed]', JText::_('COM_TOES_BREED').' : '.$entry->breed_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_gender]', JText::_('COM_TOES_GENDER').' : '.$entry->gender_name, $previous_entries_text);
            $previous_entries_text = str_replace('[show_class]', JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER').' : '.$entry->Show_Class, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_category]', JText::_('COM_TOES_CATEGORY').' : '.$entry->category, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_division]', JText::_('COM_TOES_DIVISION').' : '.$entry->division_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_color]', JText::_('COM_TOES_COLOR').' : '.$entry->color_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_date_of_birth]', JText::_('COM_TOES_DATE_OF_BIRTH').' : '.date('M d, Y',strtotime($entry->copy_cat_date_of_birth)), $previous_entries_text);
            $previous_entries_text = str_replace('[age]', JText::_('COM_TOES_CAT_AGE').' : '.($entry->age_years?$entry->age_years.' Y':'').' '.($entry->age_months?$entry->age_months.' M':''), $previous_entries_text);
            $previous_entries_text = str_replace('[show_days]', JText::_('COM_TOES_ENTRY_SHOW_DAYS').' : '.$days, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_sire]', JText::_('COM_TOES_SIRE').' : '.$entry->copy_cat_sire_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_dam]', JText::_('COM_TOES_DAM').' : '.$entry->copy_cat_dam_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_breeder]', JText::_('COM_TOES_BREEDER').' : '.$entry->copy_cat_breeder_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_owner]', JText::_('COM_TOES_OWNER').' : '.$entry->copy_cat_owner_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_lessee]', JText::_('COM_TOES_LESSEE').' : '.$entry->copy_cat_lessee_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_agent]', JText::_('COM_TOES_AGENT').' : '.$entry->copy_cat_agent_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_competitive_region]', JText::_('COM_TOES_COMPETITIVE_REGION').' : '.$entry->competitive_region_name, $previous_entries_text);
            $previous_entries_text = str_replace('[cat_for_sale]', JText::_('COM_TOES_ENTRY_FOR_SALE').' : '.($entry->for_sale?JText::_('JYES'):JText::_('JNO')), $previous_entries_text);
            
            if($congress_names)
                $previous_entries_text = str_replace('[congress]', JText::_('COM_TOES_ENTRY_CONGRESS').' : '.$congress_names, $previous_entries_text);
            else
                $previous_entries_text = str_replace('[congress]', '', $previous_entries_text);
        }
        $body = str_replace('[Previous Entries Block]', $previous_entries_text?$previous_entries_text:JText::_('COM_TOES_NO_CATS_CONFIRMED'), $body);
        
        $body = str_replace('[single_cages]', $summary->summary_single_cages, $body);
        $body = str_replace('[double_cages]', $summary->summary_double_cages, $body);
        $body = str_replace('[benching_request]', $summary->summary_benching_request, $body);
        $body = str_replace('[remarks]', $summary->summary_remarks, $body);
		$body = str_replace('[summary_entry_clerk_note]', $summary->summary_entry_clerk_note?$summary->summary_entry_clerk_note:'-', $body);

        if($summary->summary_grooming_space)
            $body = str_replace('[grooming_space]', JText::_('COM_TOES_DO_REQUEST_GROOMING_SPACE'), $body);
        else
            $body = str_replace('[grooming_space]', JText::_('COM_TOES_DO_NOT_REQUEST_GROOMING_SPACE'), $body);
        
        if($summary->summary_personal_cages)
            $body = str_replace('[personal_cages]', JText::_('COM_TOES_WILL_BRING_PERSONAL_CAGE'), $body);
        else
            $body = str_replace('[personal_cages]', JText::_('COM_TOES_WILL_NOT_BRING_PERSONAL_CAGE'), $body);
        
        $showManagerEmails= "";
        if($show->show_use_club_show_manager_address)
        {
            $showManagerEmails = '<a href="mailto:'.$show->show_email_address_show_manager.'">'.$show->show_email_address_show_manager.'</a><br/>';
        }
        else
        {
        	if($showManagers) {
	            foreach($showManagers as $manager) {
	                $showManagerEmails .= '<a href="mailto:'.$manager->show_manager_email.'">'.$manager->show_manager_email.'</a><br/>';
	            }
			}
        }
        $body = str_replace('[showmanager_email_address]', $showManagerEmails, $body);
        
        $entryClerkEmails= "";
        if($show->show_use_club_entry_clerk_address)
        {
            $entryClerkEmails = '<a href="mailto:'.$show->show_email_address_entry_clerk.'">'.$show->show_email_address_entry_clerk.'</a><br/>';
        }
        else
        {
        	if($entryClerks) {
	            foreach($entryClerks as $clerk){
	                $entryClerkEmails .= '<a href="mailto:'.$clerk->entry_clerk_email.'">'.$clerk->entry_clerk_email.'</a><br/>';
            }
			}
        }
        $body = str_replace('[entry_clerk_email_address]', $entryClerkEmails, $body);
        
        //Placeholders
        $pd_block = '';
        if($new_placeholders)
        {
            $pd_block = "";
            $pd_block = JText::_('COM_TOES_NEW_PLACEHOLDERS').'<br>';
            $cnt = 1;
            foreach($new_placeholders as $placeholder)
            {
				if($isContinuous)
					$days = JText::_('JALL');
				else
				{
					$days = array();
					foreach($placeholder as $item){
						$day_string = date('D', strtotime($item->show_day_date));
						if($isAlternative)
						{
							if($item->placeholder_participates_AM)
							{
								$day_string .= ' AM';
								if($item->placeholder_participates_PM)
								{
									$day_string .= '/PM';
								}
							}
							elseif($item->placeholder_participates_PM)
							{
								$day_string .= ' PM';
							}
						}
						$days[] = $day_string;
					}
					$days = implode(',',array_unique($days));
				}

				$pd_block .= $cnt.". ".$days."<br/>";
                $cnt++;
            }
        }
        
        $body = str_replace('[NEW Placeholder Block]', $pd_block, $body);        

        $pd_block = '';
        if($old_placeholders)
        {
            $pd_block = "";
            $pd_block = JText::_('COM_TOES_PREVIOUS_PLACEHOLDERS').'<br>';
            $cnt = 1;
            foreach($old_placeholders as $placeholder)
            {
				if($isContinuous)
					$days = JText::_('JALL');
				else
				{
					$days = array();
					foreach($placeholder as $item){
						$day_string = date('D', strtotime($item->show_day_date));
						if($isAlternative)
						{
							if($item->placeholder_participates_AM)
							{
								$day_string .= ' AM';
								if($item->placeholder_participates_PM)
								{
									$day_string .= '/PM';
								}
							}
							elseif($item->placeholder_participates_PM)
							{
								$day_string .= ' PM';
							}
						}
						$days[] = $day_string;
					}
					$days = implode(',',array_unique($days));
				}

                $pd_block .= $cnt.". ".$days."<br/>";
                $cnt++;
            }
        }
        
        $body = str_replace('[OLD Placeholder Block]', $pd_block, $body);    
        
        $config     = JFactory::getConfig();
        $fromname   = $config->get('fromname');
        $fromemail  = $config->get('mailfrom');
        $recipient  = $user->email;

        $cc = array();
        if($show->show_use_club_entry_clerk_address)
            $cc[] = $show->show_email_address_entry_clerk;
        else
        {
			if($entryClerks) {
		        foreach($entryClerks as $entryClerk) {
		            $cc[] = $entryClerk->entry_clerk_email;
		        }
			} else if($showManagers) {
				foreach($showManagers as $manager) {
					$cc[] = $manager->show_manager_email;
				}	
			}
        }
        
		/*
        if($show->show_use_club_entry_clerk_address)
            $fromemail = $show->show_email_address_entry_clerk;
        else
        {
            if(isset($entryClerks[0]))
            {
                $fromemail = $entryClerks[0]->entry_clerk_email;
                $fromname = $entryClerks[0]->entry_clerk_name;
            }
        }
        */
		
		$replyTo = array();
		$replyTo_names = array();
        if(!$show->show_use_club_entry_clerk_address)
        {
			if($entryClerks) {
	            foreach($entryClerks as $entryClerk) {
	                $clerk = array();
	                $replyTo[] = $entryClerk->entry_clerk_email;
	                $replyTo_names[] = $entryClerk->entry_clerk_name;
	            }
			} else if($showManagers) {
	            foreach($showManagers as $manager) {
	                $clerk = array();
	                $replyTo[] = $manager->show_manager_email;
	                $replyTo_names[] = $manager->show_manager_name;
	            }
			}
        }
        else
        {
			$replyTo[] = $show->show_email_address_entry_clerk;
			$replyTo_names[] = $club->club_name;
        }
        
		/*
        $mail = JFactory::getMailer();
		
		$cparams = JComponentHelper::getParams('com_toes');

		if ($cparams->get('send_bcc_emails') == 1) {
			$mail->addBCC($cparams->get('bcc_email'));
		}
		
        //$mail->SetFrom($fromemail, $fromname);
        $mail->SetFrom($fromemail, $fromname);
        $mail->setSubject($subject);
        $mail->setBody($body);
        $mail->addRecipient($recipient,$user->firstname.' '.$user->lastname);
        $mail->addCC($cc);
        if($replyTO) {
			foreach($replyTO as $item) {
            	$mail->addReplyTo($item[0],$item[1]);
			}
		}

		$mail->IsHTML(TRUE);

        if($mail->Send())
            return true;
        else
            $this->setError($mail->ErrorInfo);
        */
		
		$cparams = JComponentHelper::getParams('com_toes');
		$bcc = '';
		if ($cparams->get('send_bcc_emails') == 1) {
			$bcc = $cparams->get('bcc_email');
		}
		
		if(TOESMailHelper::sendMail('entry_confirmation_notification', $subject, $body, $recipient, $user->firstname.' '.$user->lastname, $cc, '', $bcc, '', $replyTo, $replyTo_names)){
			return true;
		} else {
			$this->setError(JText::_('COM_TOES_MAIL_SENDING_ERROR'));
		}
		
        return false;
    }
    
    public function getMasterExibitorListPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'MasterExhibitorList.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getMasterExibitorWOAListPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'MasterExhibitorWOAList.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getLateMasterExibitorListPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'LateMasterExhibitorList.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getLateExibitorListPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'LateExhibitorList.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getExibitorListPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'ExhibitorList.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getExhibitorInfoEXL($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'ExhibitorInfoEXL.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }

    public function getExhibitorLabelsEXL($show_id)
    {
    	$user = JFactory::getUser();
 		$app = JFactory::getApplication();
    
    	if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
    	{
    		$error = '';
    		ob_start();
    		require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'ExhibitorLabelsEXL.php';
    		$error = ob_get_contents();
    		ob_end_clean();
    		if(!$error)
    			echo '1';
    		else
    			echo $error;
    	}
    	else
    	{
    		echo JText::_('COM_TOES_NOAUTH');
    	}
    }
    
    public function getExhibitorCardsPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'ExhibitorCards.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getMicrochipListPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'MicrochipList.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getSummaryPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'Summary.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getSchedulingSummaryPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'SchedulingSummary.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getCatalogPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'Catalog.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getLatePagesPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'LatePages.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getMasterCatalogPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'MasterCatalog.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getMasterCatalogEXL($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'MasterCatalogEXL.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getJudgesBookPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'JudgesBook.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getPreJudgesBookPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        error_reporting(E_ALL);
        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'PreJudgesBook.Config.php';
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'PreJudgesBook.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }

    public function getCongressJudgesBookPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'CongressJudgesBook.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getPreCongressJudgesBookPDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'PreJudgesBook.Config.php';
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'PreCongressJudgesBook.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }

    function runCatalog($show_id)
    {
        $db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
        $query->select('ring_format');
        $query->from('#__toes_ring');
        $query->where('`ring_show` = '.$show_id);
		$query->where('`ring_format` = 4');

        $db->setQuery($query);
        $ring_formats = $db->loadObjectList();

        if($ring_formats)
        {
            $this->setError(JText::_('COM_TOES_NEED_TO_CONFIRM_RING_FORMAT_ERROR'));
            return false;
        }

		$query = $db->getQuery(true);
        $query->select('e.entry_id');
        $query->from('#__toes_entry AS e');
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
        $query->where('( `estat`.`entry_status` = '.$db->quote('Accepted').' OR `estat`.`entry_status` = '.$db->quote('New').')');        
        $query->where('`e`.`entry_show` = '.$show_id);

        $db->setQuery($query);
        $entries = $db->loadObjectList();

        if($entries)
        {
            $this->setError(JText::_('COM_TOES_NEED_TO_CONFIRM_ENTRIES_ERROR'));
            return false;
        }

        $query = $db->getQuery(true);
        $query->select('e.entry_id');
        $query->from('#__toes_entry AS e');
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
        $query->where('( `estat`.`entry_status` = '.$db->quote('Confirmed').' OR `estat`.`entry_status` = '.$db->quote('Confirmed & Paid').')');        
        $query->where('`e`.`entry_show` = '.$show_id);
        
        $db->setQuery($query);
        $confirmed_entries = $db->loadColumn();
       
        $query = $db->getQuery(true);
        $query->update('#__toes_entry');
        $query->set('`late_entry` = 0');
        $query->where('`entry_id` IN ('.  implode(',', $confirmed_entries).')');

		$db->setQuery($query);
		$db->query();
		
        $competative_classes = array(
                            'LH_Kitten' => 1,
                            'SH_Kitten' => 51,
                            'LH_Cat' => 101,
                            'SH_Cat' => 201,
                            'LH_Alter' => 301,
                            'SH_Alter' => 351,
                            'LH_HHP_Kitten' => 401,
                            'LH_HHP' => 401,
                            'SH_HHP_Kitten' => 451,
                            'SH_HHP' => 451,
                            'LH_PNB' => 501,
                            'SH_PNB' => 501,
                            'LH_ANB' => 551,
                            'SH_ANB' => 551,
                            'LH_NT' => 601,
                            'SH_NT' => 601,
                            'Exh_Only' => 651,
                            'for_sale' => 701,
                            );

		$i = 1;
        foreach($competative_classes as $class => $catalog_start_number)
        {
			unset($entries);
            $query = TOESQueryHelper::getEntryFullViewQuery();
            $query->where('`late_entry` = 0');
            $query->where('`entry_show` = '.$show_id);
			
			$whr = TOESQueryHelper::getCompetativeClassConditionsForNumbering($class);
			$query->where($whr);
            
			$query->group("`entry_show`");
			$query->group("`cat`");
			
			$query->clear('order');
			$query->order("`breed_name` ASC");
			$query->order("`copy_cat_category` ASC");
			$query->order("`copy_cat_division` ASC");
			$query->order("`copy_cat_color` ASC");
			$query->order("`cat` ASC");

			$db->setQuery($query);
            $entries = $db->loadObjectList();
			
            $i = ($i > $catalog_start_number)?$i:$catalog_start_number;
            foreach ($entries as $entry)
            {
                $query = $db->getQuery(true);
                $query->select('e.entry_id');
                $query->from("`#__toes_entry` AS e");
                $query->where('e.`entry_show` = '.$entry->entry_show);        
                $query->where('e.`cat` = '.$entry->cat);
                $query->where('e.`entry_show_class` = '.$db->quote($entry->entry_show_class));

                $db->setQuery($query);
                $entryids = $db->loadColumn();
                
                $query = $db->getQuery(true);
                $query->update('#__toes_entry');
                $query->set('`catalog_number` = '.$i);
                $query->where('`entry_id` in ('.  implode(',', $entryids).')');
				//$query->where('`entry_id` in ('. $entry->entryids.')');
                
                $db->setQuery($query);
                $db->query();
                $i++;
            }
        }

        $query = $db->getQuery(true);
        $query->update('#__toes_show');
        $query->set('`catalog_runs` = `catalog_runs` + 1');
        $query->where('`show_id` = '.$show_id);

        $db->setQuery($query);
        $db->query();
		
		return true;
    }

    function latePages($show_id)
    {
        $db = JFactory::getDbo();

		$query = $db->getQuery(true);

        $query->select('`e`.`entry_id`');
        $query->from('`#__toes_entry` AS `e`');
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
        $query->where('( `estat`.`entry_status` = '.$db->quote('Accepted').' OR `estat`.`entry_status` = '.$db->quote('Confirmed').')');
        $query->where('`e`.`late_entry` = 0');
        $query->where('`e`.`catalog_number` IS NULL');
        $query->where('`e`.`entry_show` = '.$show_id);

        $db->setQuery($query);
        $confirmed_entries = $db->loadObjectList();

        if($confirmed_entries)
        {
            $this->setError(JText::_('COM_TOES_PENDING_CONFIRM_ENTRIES_ERROR'));
            return false;
        }
        
        $query = $db->getQuery(true);
        $query->update('#__toes_entry');
        $query->set('`catalog_number` = NULL');
        $query->where('`late_entry` = 1');
        $query->where('`entry_show` = '.$show_id);

        $db->setQuery($query);
        $db->query();
        
        $competative_classes = array(
                            'LH Kitten' => 1,
                            'SH Kitten' => 51,
                            'LH Cat' => 101,
                            'SH Cat' => 201,
                            'LH Alter' => 301,
                            'SH Alter' => 351,
                            'LH HHP Kitten' => 401,
                            'LH HHP' => 401,
                            'SH HHP Kitten' => 451,
                            'SH HHP' => 451,
                            'LH PNB' => 501,
                            'SH PNB' => 501,
                            'LH ANB' => 551,
                            'SH ANB' => 551,
                            'LH NT' => 601,
                            'SH NT' => 601,
                            'Ex Only' => 651,
                            'For Sale' => 701,
                            );
        
		$query = TOESQueryHelper::getEntryFullViewQuery();
        $query->where('e.`entry_show` = '.$show_id);

        $db->setQuery($query);
        $entries = $db->loadObjectList();

        $cur = 0;
        $prev_catalog_number = 0;
		$catalog_number = 0;
		$catalog_letter = 'A';
		
		if($entries)
		{
			$entry_class = $entries[$cur]->show_class;
			
			foreach ($entries as $entry) 
			{
				if($entry->catalog_number)
				{
					$catalog_number = $entry->catalog_number;
					$entry_class = $entry->show_class;
					$catalog_letter = 'A';
					continue;
				}
				else
				{
					if($entry_class == $entry->show_class)
					{
						$prev_catalog_number = $catalog_number;
						$new_catalog_number = $catalog_number.$catalog_letter;
					}
					else
					{
						if($entry->show_class == $entries[$cur]->show_class)
						{
							$prev_catalog_number = ($entries[$cur]->catalog_number-1);
							$new_catalog_number = ($entries[$cur]->catalog_number-1).$catalog_letter;
						}
						else
						{

							$catalog_number = ($entries[$cur]->catalog_number > $competative_classes[$entry->show_class])?$entries[$cur]->catalog_number:$competative_classes[$entry->show_class];

							$prev_catalog_number = $catalog_number;
							$new_catalog_number = $catalog_number.$catalog_letter;                        
						}
					}

					$query = $db->getQuery(true);
					$query->select('e.catalog_number');
					$query->from('#__toes_entry AS e');
					$query->where('e.`entry_id` = '.$entry->entry_id);

					$db->setQuery($query);
					$isAssigned = $db->loadResult();

					if(!$isAssigned)
					{
						$query = $db->getQuery(true);
						$query->select('e.entry_id');
						$query->from("`#__toes_entry` AS e");
						$query->where('e.`entry_show` = '.$entry->entry_show);        
						$query->where('e.`cat` = '.$entry->cat);
						$query->where('e.`late_entry` = 1');
						$query->where('e.`entry_show_class` = '.$db->quote($entry->entry_show_class));

						$db->setQuery($query);
						$entryids = $db->loadColumn();

						$query = $db->getQuery(true);
						$query->update('#__toes_entry');
						$query->set('`catalog_number` = '.$db->quote($new_catalog_number));
						$query->where('`entry_id` in ('.  implode(',', $entryids).')');

						$db->setQuery($query);
						$db->query();

						$new_catalog_number = '';
						$catalog_letter++;
					}
					else
						$new_catalog_number = '';

				}
				$cur++;
			}
		}
        
        return true;
    }
    
    
    function changePapersize($show_id, $paper_size)
    {
        $db = JFactory::getDbo();
        
        $query = $db->getQuery(true);
        $query->update('#__toes_show');
        $query->set('`show_paper_size` = '.$paper_size);
        $query->where('`show_id` = '.$show_id);

        $db->setQuery($query);
        if(!$db->query())
        {
            $this->setError($db->getErrorMsg());
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else 
            return true;
    }
    
    public function getTreasurerPDF($show_id)
    {
        $user = JFactory::getUser();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'Treasurer.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getTreasurerEXL($show_id)
    {
    	$user = JFactory::getUser();
		
    	if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
    	{
    		$error = '';
    		ob_start();
    		require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'TreasurerEXL.php';
    		$error = ob_get_contents();
    		ob_end_clean();
    		if(!$error)
    			echo '1';
    		else
    			echo $error;
    	}
    	else
    	{
    		echo JText::_('COM_TOES_NOAUTH');
    	}
    }

    public function getBenchingPDF($show_id)
    {
        $user = JFactory::getUser();

        if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
        {
			$error = '';
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'Benching.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function updateChangestoEntry($show_id, $cat_id) {
        $db = JFactory::getDbo();
        $cat_detail = TOESHelper::getCatDetails($cat_id);
        
        $sql = "SELECT `entry_id` FROM `#__toes_entry` WHERE `entry_show` = {$show_id} AND `cat`={$cat_id}";
        $db->setQuery($sql);
        $entry_ids = $db->loadColumn();
        
        $query = $db->getQuery(true);
        
        $query->update('`#__toes_entry`');
        
        $query->set('`copy_cat_breed` = '.$db->quote($cat_detail->cat_breed));
        $query->set('`copy_cat_new_trait` = '.$db->quote($cat_detail->cat_new_trait));
        $query->set('`copy_cat_gender` = '.$db->quote($cat_detail->cat_gender));
        $query->set('`copy_cat_hair_length` = '.$db->quote($cat_detail->cat_hair_length));
        $query->set('`copy_cat_registration_number` = '.$db->quote($cat_detail->cat_registration_number));
        $query->set('`copy_cat_date_of_birth` = '.$db->quote($cat_detail->cat_date_of_birth));
        $query->set('`copy_cat_category` = '.$db->quote($cat_detail->cat_category));
        $query->set('`copy_cat_division` = '.$db->quote($cat_detail->cat_division));
        $query->set('`copy_cat_color` = '.$db->quote($cat_detail->cat_color));
        $query->set('`copy_cat_prefix` = '.$db->quote($cat_detail->cat_prefix));
        $query->set('`copy_cat_title` = '.$db->quote($cat_detail->cat_title));
        $query->set('`copy_cat_suffix` = '.$db->quote($cat_detail->cat_suffix));
        $query->set('`copy_cat_sire_name` = '.$db->quote($cat_detail->cat_sire));
        $query->set('`copy_cat_dam_name` = '.$db->quote($cat_detail->cat_dam));
        $query->set('`copy_cat_breeder_name` = '.$db->quote($cat_detail->cat_breeder));
        $query->set('`copy_cat_owner_name` = '.$db->quote($cat_detail->cat_owner));
        $query->set('`copy_cat_lessee_name` = '.$db->quote($cat_detail->cat_lessee));
        $query->set('`copy_cat_name` = '.$db->quote($cat_detail->cat_name));
        $query->set('`copy_cat_competitive_region` = '.$db->quote($cat_detail->cat_competitive_region));
        
        $query->where('`entry_id` in ('.implode(',', $entry_ids).')');
        
        $db->setQuery($query);
        if(!$db->query())
        {
            $this->setError($db->getErrorMsg());
            return false;
        } else {
			foreach($entry_ids as $entry_id) {
				$this->updateEntryShowClass($entry_id);
			}
		}

        return true;
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
    
    public function participateInCongress($show_id, $cat_id, $congress_id) {
        $db = JFactory::getDbo();
        
        $sql = "SELECT `entry_id`, `show_day` FROM `#__toes_entry` WHERE `entry_show` = {$show_id} AND `cat`={$cat_id}";
        $db->setQuery($sql);
        $entries = $db->loadObjectList();
		$congress_id = explode(',', $congress_id);
        
        foreach($entries as $entry)
        {
            foreach($congress_id as $ring_id)
            {   
                $query = "SELECT * FROM 
                    #__toes_entry_participates_in_congress 
                    WHERE congress_id = {$ring_id}
                    AND entry_id = {$entry->entry_id}";

                $db->setQuery($query);

                if(!$db->loadResult())
                {
					$congress = TOESHelper::getRingDetails($ring_id);
					if($entry->show_day === $congress->ring_show_day) {
						$query = "INSERT INTO `#__toes_entry_participates_in_congress` values 
							( {$entry->entry_id}, {$ring_id} )";

						$db->setQuery($query);
						if(!$db->query())
						{
							$this->setError($db->getErrorMsg());
							return false;
						}
					}
                }
            }
        }
        return true;
    } 
    
    public function getEntryclerkCheatsheetPDF($show_id)
    {
        $user = JFactory::getUser();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'EntryclerkCheatsheet.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getEntryclerkCheatsheetEXL($show_id)
    {
        $user = JFactory::getUser();
		
        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'EntryclerkCheatsheetEXL.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getAbsenteesPDF($show_id)
    {
        $user = JFactory::getUser();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'Absentees.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getCheckinSheetPDF($show_id)
    {
        $user = JFactory::getUser();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'CheckinSheet.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getBlankJudgesBookPDF($show_id)
    {
        $user = JFactory::getUser();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'BlankJudgesBook.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }

    public function getExhibitorLabelDetailsEXL($show_id)
    {
    	$user = JFactory::getUser();
 		$app = JFactory::getApplication();
    
    	if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
    	{
    		$error = '';
    		ob_start();
    		require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'ExhibitorLabelDetailsEXL.php';
    		$error = ob_get_contents();
    		ob_end_clean();
    		if(!$error)
    			echo '1';
    		else
    			echo $error;
    	}
    	else
    	{
    		echo JText::_('COM_TOES_NOAUTH');
    	}
    }

    public function getSpaceSummaryPDF($show_id)
    {
    	$user = JFactory::getUser();
 		$app = JFactory::getApplication();
    
    	if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
    	{
    		$error = '';
    		ob_start();
    		require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'SpaceSummary.php';
    		$error = ob_get_contents();
    		ob_end_clean();
    		if(!$error)
    			echo '1';
    		else
    			echo $error;
    	}
    	else
    	{
    		echo JText::_('COM_TOES_NOAUTH');
    	}
    }

    public function getBenchingCardsPDF($show_id)
    {
    	$user = JFactory::getUser();
 		$app = JFactory::getApplication();
    
    	if(TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id) || TOESHelper::isAdmin())
    	{
    		$error = '';
    		ob_start();
    		require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'BenchingCards.php';
    		$error = ob_get_contents();
    		ob_end_clean();
    		if(!$error)
    			echo '1';
    		else
    			echo $error;
    	}
    	else
    	{
    		echo JText::_('COM_TOES_NOAUTH');
    	}
    } 
    
    public function getBlankJudgesBookFinalSheet($show_id)
    {
        $user = JFactory::getUser();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'BlankJudgesBookFinalSheet.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getJudgesBookInA4PDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'JudgesBookA4.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getCongressJudgesBookInA4PDF($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'CongressJudgesBookA4.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
    
    public function getAbsenteesEXL($show_id)
    {
        $user = JFactory::getUser();
		$app = JFactory::getApplication();

        if(TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $show_id) || TOESHelper::is_clubowner($user->id, TOESHelper::getClub($show_id)->club_id))
        {
			ob_start();
            require_once JPATH_COMPONENT.DS.'pdflibs'.DS.'AbsenteesEXL.php';
			$error = ob_get_contents();
			ob_end_clean();
			if(!$error)
				echo '1';
			else
				echo $error;
        }
        else
        {
            echo JText::_('COM_TOES_NOAUTH');
        }
    }
}
