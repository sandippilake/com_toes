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
 * Categories Component Categories Model
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESModelUsers extends JModelList {

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     * @see		JController
     * @since	1.6
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('id');
        }

        parent::__construct($config);
    }

    public function getOrgOfficial() {
        $db = JFactory::getDBO();
        //cntry.name as cb_country->LEFT JOIN #__toes_country as cntry ON cntry.id = cb.cb_country
        //state.name as cb_state->LEFT JOIN #__toes_states_per_country as state ON state.id = cb.cb_state
        // LEFT JOIN #__toes_cities_per_state as city ON city.id = cb.cb_city
		$query = "SELECT user.id as user_id, toot.organization_official_type as roll,
                    toot.organization_official_type_id as roll_id,
                    toho.organization_id as official_id, user.username as uname, 
                    cr.competitive_region_id, cr.competitive_region_abbreviation, cr.competitive_region_confirmation_by_rd_needed,
                    cb.firstname, cb.lastname, cb.cb_state as cb_state, cb.cb_country as cb_country
                FROM #__toes_organization_has_official as toho 
                LEFT JOIN #__users AS user ON user.id = toho.user
                LEFT JOIN #__toes_organization_official_type as toot ON toho.organization_official_type  = toot.organization_official_type_id
                LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                LEFT JOIN #__toes_competitive_region AS cr ON cr.competitive_region_regional_director = toho.user 
                ORDER BY cr.competitive_region_name
                ";
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getClubOfficials() {
    	$app = JFactory::getApplication();
		$user = JFactory::getUser();
        $club = $app->input->getInt('club_filter');
        $club_user = $app->input->getInt('club_user_filter');
		$order = $app->input->get('club_order','club_name');
        $order_dir = $app->input->get('club_order_dir','asc');
		
        $where = array();
		$whr = "";

        if ($club) {
            $where[] = " tco.club= " . $club . " ";
        }

		if ($club_user) {
            $where[] = " tco.user = " . $club_user . " ";
        }

		
        $db = JFactory::getDBO();
        // cntry.name as cb_country->LEFT JOIN #__toes_country as cntry ON cntry.id = cb.cb_country
        //state.name as cb_state->LEFT JOIN #__toes_states_per_country as state ON state.id = cb.cb_state
        //->LEFT JOIN #__toes_cities_per_state as city ON city.id = cb.cb_city
        $query = "SELECT user.id as user_id, tcot.club_official_type as roll, 
	            tcot.club_official_type_id as roll_id, tco.club as official_id, 
	            c.club_name as club, user.username as uname, 
	            cb.firstname, cb.lastname, cb.cb_state as cb_state, cb.cb_country as cb_country 
	            FROM #__toes_club_official as tco
	            LEFT JOIN #__toes_club_official_type as tcot ON tco.club_official_type = tcot.club_official_type_id
	            LEFT JOIN #__toes_club as c ON tco.club  = c.club_id
	            LEFT JOIN #__users AS user ON user.id = tco.user
	            LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
				";
		if(!TOESHelper::isAdmin()) {
	        if(TOESHelper::is_clubofficial($user->id))
	        {
	            $where[] = " tco.club IN (SELECT club FROM #__toes_club_official WHERE user = ". $user->id.")" ;
	        }
	        else
	        {
	            $where[] = " 0";
	        }        
		}

    	$whr = ( count($where) ? ' WHERE ' . implode(' AND ', $where) : '' );
		
		if($whr) {
			$query .= $whr;
		}	
		
		$query .= " ORDER BY $order $order_dir";

		//echo str_replace('#__', 'j35_', nl2br($query));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
	
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the total.
		$query = $this->getShowOfficialQuery();

		try
		{
			$total = (int) $this->_getListCount($query);
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $total;

		return $this->cache[$store];
	}
	
	public function getShowOfficials() {
		$query = $this->getShowOfficialQuery();
		
		$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
        return $items;
	}
	

    public function getShowOfficialQuery() {
    	$app = JFactory::getApplication();    	
        $db = JFactory::getDBO();
		$user = JFactory::getUser();
		
		$club = $app->input->getInt('show_club_filter');
		$show_user = $app->input->getInt('user_filter');
        $roll = $app->input->getInt('show_roll_filter');
        $location = $app->input->getInt('show_location_filter');
        $status = $app->input->getInt('show_status_filter');
        $date_status = $app->input->getVar('show_date_status_filter');
		$order = $app->input->get('show_order','club_name');
        $order_dir = $app->input->get('show_order_dir','asc');

        //echo $date_status;die;

        $where = array();
        $whr = '';
		
        if ($club) {
            $where[] = " tcos.club = " . $club . " ";
        }
        if ($show_user) {
            $where[] = " tsho.user = " . $show_user . " ";
        }
        if ($location) {
            $where[] = " ts.show_venue = " . $location . " ";
        }
        if ($roll) {
            $where[] = " tsho.show_official_type = " . $roll . " ";
        }
        if ($status) {
            $where[] = " ts.show_status = " . $status . " ";
        }

        if ($date_status) {
            if ($date_status == 'past')
                $where[] = " ts.show_end_date 	<= " . $db->quote(date('Y-m-d')) . " ";
            if ($date_status == 'future')
                $where[] = " ts.show_end_date 	>= " . $db->quote(date('Y-m-d')) . " ";
        }

		//cntry.name AS address_country->LEFT JOIN #__toes_country as cntry ON cntry.id = ta.address_country
		//city.name AS address_city-> LEFT JOIN #__toes_cities_per_state as city ON city.id = ta.address_city
		//state.name AS address_state-> LEFT JOIN #__toes_states_per_country as state ON state.id = ta.address_state
		$query = "SELECT user.id as user_id, user.username as uname, tsho.show as official_id,
				tsot.show_official_type as roll, tsot.show_official_type_id as roll_id,  
				ts.show_id, ts.show_start_date, ts.show_end_date, 
				tv.venue_name, ta.address_city AS address_city, ta.address_state AS address_state, ta.address_country AS address_country,
                c.club_name as club, c.club_id, cb.firstname, cb.lastname
                
                FROM #__toes_show_has_official as tsho 
                LEFT JOIN #__toes_show_official_type as tsot ON tsho.show_official_type = tsot.show_official_type_id
                
                LEFT JOIN #__toes_club_organizes_show as tcos ON tsho.show  = tcos.show
                LEFT JOIN #__toes_club as c ON tcos.club = c.club_id
                
                LEFT JOIN #__toes_show as ts ON tsho.show = ts.show_id 	
                LEFT JOIN #__toes_venue as tv ON ts.show_venue = tv.venue_id
                LEFT JOIN #__toes_address as ta ON tv.venue_address = ta.address_id
                
				LEFT JOIN #__users AS user ON user.id = tsho.user
                LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                ";
		if(!TOESHelper::isAdmin()) {
	        if(TOESHelper::is_clubofficial($user->id))
	        {
	        	$query .="LEFT JOIN #__toes_club_official as tco ON c.club_id = tco.club";
	            $where[] = " tco.user = ". $user->id ;
	        }
	        else if(TOESHelper::is_showmanager($user->id))
	        {
	            $where[] = " tsho.`user` = " . $user->id . " AND tsot.`show_official_type` = 'Show Manager'" ;
	        }        
		}
                
    	$whr = ( count($where) ? ' WHERE ' . implode(' AND ', $where) : '' );
		
		if($whr) {
			$query .= $whr;
		}	
		
		$query .= " ORDER BY $order $order_dir";
		
		//echo str_replace('#__', 'j35_', nl2br($query));
        return $query;
    }
    
    public function getclublist() 
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
		
		// club_id 	club_name 	club_abbreviation 	club_website 	club_email 	club_organization
        $query->select('club_id AS value, concat(club_name,\' (\',club_abbreviation,\')\') AS text');
        $query->from('#__toes_club');
        $query->order('club_name ASC');

        // Get the options.
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    public function getclubrolllist() 
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
		
		// club_official_type_id 	club_official_type 
        $query->select('club_official_type_id AS value, club_official_type AS text');
        $query->from('#__toes_club_official_type');
        $query->order('club_official_type_id ASC');

        // Get the options.
        $db->setQuery($query);

        return $db->loadObjectList();
    }
    
    public function getshowrolllist() 
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
		
		// show_official_type_id 	show_official_type
        $query->select('show_official_type_id AS value, show_official_type AS text');
        $query->from('#__toes_show_official_type');
        $query->order('show_official_type_id ASC');

        // Get the options.
        $db->setQuery($query);

        return $db->loadObjectList();
    }
    
    public function getshow_statuslist() 
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
		
		// show_status_id 	show_status
        $query->select('show_status_id AS value, show_status AS text');
        $query->from('#__toes_show_status');
        $query->order('show_status_id ASC');

        // Get the options.
        $db->setQuery($query);

        return $db->loadObjectList();
    }
    
    public function getlocationlist() 
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
		
		//  venue_id 	venue_name 	venue_website 	venue_address
        $query->select('venue_id AS value, venue_name AS text');
        $query->from('#__toes_venue');
        $query->order('venue_id ASC');

        // Get the options.
        $db->setQuery($query);

        return $db->loadObjectList();
    }

}
