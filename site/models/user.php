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
//Service service

class TOESModelUser extends JModelLegacy {

    /**
     * Item cache.
     */
    private $_cache = array();

    public function getusers() {
        $db = JFactory::getDbo();

        $query = "SELECT * FROM #__users";
        //echo $query;
        $db->setQuery($query);
        $users = $db->loadObjectList();

        return $users;
    }

    public function getorganization_detail() {
		$app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $user = JFactory::getUser();

        $user_id = $app->input->getInt('user_id');
        $roll = $app->input->getInt('roll');
        $organization_id = $app->input->getInt('organization_id');
        $region = $app->input->getInt('region');

        $query = "SELECT *, user.id as user_id, toot.organization_official_type as roll,
                            toot.organization_official_type_id as roll_id,
                            toho.organization_id as official_id, user.username as uname, 
                            cr.competitive_region_abbreviation, cr.competitive_region_confirmation_by_rd_needed
                        FROM #__users AS user 
                        INNER JOIN #__toes_organization_has_official as toho ON user.id = toho.user
                        LEFT JOIN #__toes_organization_official_type as toot ON toho.organization_official_type  = toot.organization_official_type_id
                        LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                        LEFT JOIN #__toes_competitive_region AS cr ON cr.competitive_region_regional_director = user.id
                        WHERE user.id =" . $user_id . " AND toot.organization_official_type_id=" . $roll . "
                        AND toho.organization_id=" . $organization_id;
        if($region)
        $query .= " AND cr.competitive_region_id = ".$region;                

        //echo nl2br(str_replace('#_', 'j25', $query));
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getclub_detail() {
        $db = JFactory::getDbo();
		$app = JFactory::getApplication();
        $query = $db->getQuery(true);

        $user = JFactory::getUser();

        $user_id = $app->input->getInt('user_id');
        $roll = $app->input->getInt('roll');
        $club_id = $app->input->getInt('club_id');

        $query = "SELECT *, user.id as user_id, tcot.club_official_type as roll, 
                            tcot.club_official_type_id as roll_id, tco.club as official_id, 
                            c.club_name as club, user.username as uname 
                        FROM #__users AS user 
                        INNER JOIN #__toes_club_official as tco ON user.id = tco.user
                        LEFT JOIN #__toes_club_official_type as tcot ON tco.club_official_type = tcot.club_official_type_id
                        LEFT JOIN #__toes_club as c ON tco.club  = c.club_id
                        LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                        WHERE user.id =" . $user_id . " AND tco.club_official_type=" . $roll . "
                        AND c.club_id=" . $club_id;


        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getshow_detail() {
        $db = JFactory::getDbo();
		$app = JFactory::getApplication();
        $query = $db->getQuery(true);

        $user = JFactory::getUser();
        $userid = JFactory::getUser()->id;

        $user_id = $app->input->getInt('user_id');
        $roll = $app->input->getInt('roll');
        $show_id = $app->input->getInt('show_id');

        $query = "SELECT *, user.id as user_id, tsot.show_official_type as roll, 
                            tsot.show_official_type_id as roll_id, tsho.show as official_id, 
                            c.club_name as club, c.club_id, user.username as uname
                        FROM #__users AS user 
                        INNER JOIN #__toes_show_has_official as tsho ON user.id = tsho.user
                        LEFT JOIN #__toes_show_official_type as tsot ON tsho.show_official_type = tsot.show_official_type_id
                       
                        LEFT JOIN #__toes_club_organizes_show as tcos ON tsho.show  = tcos.show
                        LEFT JOIN #__toes_club as c ON tcos.club  = c.club_id
                        
                        LEFT JOIN #__toes_show as ts ON tsho.show = ts.show_id 	
                        LEFT JOIN #__toes_venue as tv ON ts.show_venue = tv.venue_id
                        LEFT JOIN #__toes_address as ta ON tv.venue_address = ta.address_id
                        
                        LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                         WHERE user.id =" . $user_id . " AND tsho.show_official_type=" . $roll . "
                        AND tsho.show=" . $show_id;
                      
        $db->setQuery($query);
        return $db->loadObject();
    }

    public function getRegions() {
        $db = JFactory::getDbo();
        $query = "SELECT competitive_region_id AS value, competitive_region_name AS text FROM #__toes_competitive_region ";
        $db->setQuery($query);
        $regions = $db->loadObjectlist();

        return $regions;
    }

    public function getOfficial_rolls() {
        $db = JFactory::getDbo();
		$app = JFactory::getApplication();
        $query = $db->getQuery(true);

        $official = $app->input->getVar('official');

        switch ($official) {
            case 'organization':
                $query = "SELECT organization_official_type_id as value, organization_official_type as text FROM #__toes_organization_official_type";
                break;
            case 'club':
                $query = "SELECT club_official_type_id as value, club_official_type as text FROM #__toes_club_official_type";
                break;
            case 'show':
                $query = "SELECT show_official_type_id as value, show_official_type as text FROM #__toes_show_official_type";
                break;
        }
        $db->setQuery($query);
        $user_rolls = $db->loadObjectList();

        return $user_rolls;
        /*
          $user_rolllist = array();
          $user_rolllist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_ROLL'));
          $user_rolllist = array_merge($user_rolllist, $user_rolls);
          return JHTML::_('select.genericlist', $user_rolllist, 'official_roll', 'onchange="show_regions(this.value);"', 'value', 'text', '');
         */
    }

    public function getOfficials() {
        $db = JFactory::getDbo();
		$app = JFactory::getApplication();
        $user = JFactory::getUser();
        $query = $db->getQuery(true);

        $official = $app->input->getVar('official');
        $isClubOfficial = TOESHelper::is_clubofficial($user->id);

        switch ($official) {
            case 'organization':
                $query = "SELECT organization_id as value, organization_name as text FROM `#__toes_organization`";
                break;
            case 'club':
                $query = "SELECT c.club_id as value, c.club_name as text FROM `#__toes_club` AS c ";
                if (!TOESHelper::isAdmin()) {
                    if ($isClubOfficial) {
                        $query .= ' LEFT JOIN `#__toes_club_official` AS `co` ON `co`.`club` = `c`.`club_id`
                                WHERE `co`.`user` = ' . $user->id;
                    }
                }
                break;
            case 'show':
                //$query = "SELECT show_id as value, show_motto as text FROM #__toes_show";
                $query = "";
                break;
        }
		if($query) {
	        $db->setQuery($query);
	        $user_rolls = $db->loadObjectList();
		} else {
			$user_rolls = array();
		}

        return $user_rolls;

        /*
          $user_rolllist = array();
          $user_rolllist[] = JHTML::_('select.option', '', 'Select ' . $official);
          if($user_rolls)
          $user_rolllist = array_merge($user_rolllist, $user_rolls);

          if ($official == 'organization')
          return JHTML::_('select.genericlist', $user_rolllist, 'official', '', 'value', 'text', 1);
          else
          return JHTML::_('select.genericlist', $user_rolllist, 'official', '', 'value', 'text', '');
         */
    }

    public function getClubs() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $user = JFactory::getUser();

        $isClubOfficial = TOESHelper::is_clubofficial($user->id);
        $isShowManager = TOESHelper::is_showmanager($user->id);

        $query = "SELECT c.`club_id` as value, c.`club_name` as text FROM #__toes_club AS c";
		if (!TOESHelper::isAdmin()) {
	        if ($isClubOfficial) {
	            $query .= ' LEFT JOIN `#__toes_club_official` AS co ON co.`club` = c.`club_id`
	                    WHERE co.`user` = ' . $user->id;
	        } else if ($isShowManager) {
	            $query .= " LEFT JOIN `#__toes_club_organizes_show` AS cos ON cos.`club` = c.`club_id`
	                    LEFT JOIN `#__toes_show_has_official` AS so ON so.`show` = cos.`show`
	                    WHERE so.`user` = " . $user->id . " AND so.`show_official_type` = (SELECT `show_official_type_id` FROM `#__toes_show_official_type` WHERE `show_official_type` = 'Show Manager')";
	        }
		}
        $db->setQuery($query);
        $user_rolls = $db->loadObjectList();

        return $user_rolls;
        /*
          $user_rolllist = array();
          $user_rolllist[] = JHTML::_('select.option', '', 'Select Club');
          $user_rolllist = array_merge($user_rolllist, $user_rolls);

          return JHTML::_('select.genericlist', $user_rolllist, 'club', 'onchange="getshows(this.value);"', 'value', 'text', '');
         */
    }

    /**
     * Method to save the form data.
     *
     * @param	array	The form data.
     * @return	boolean	True on success.
     */
    public function save($data) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        /*
          $query = "SELECT id FROM #__users WHERE username = " . $db->quote($data['official_user']);
          $db->setQuery($query);
          $user_id = $db->loadResult();
         */
        $user_id = $data['official_user'];

        //var_dump($data);die;

        switch ($data['official_type']) {
            case 'organization':
                if ($data['previous_user']) {
                    $query = "UPDATE `#__toes_organization_has_official` 
                        SET `organization_id` = " . $data['official'] . ",
                        `user` = " . $user_id . ",
                        `organization_official_type` = " . $data['official_roll'] . "
                        WHERE `organization_id` = " . $data['previous_organization'] . "
                        AND `user` = " . $data['previous_user'] . " AND `organization_official_type` = " . $data['previous_roll'];
                } else {
                    
                    $query = "SELECT * FROM `#__toes_organization_has_official` 
                        WHERE `user` = {$user_id}
                        AND `organization_official_type` = {$data['official_roll']}
                        AND `organization_id` = {$data['official']}
                        " ;
                    $db->setQuery($query);
                    $isPresent = $db->loadObject();
                    
                    if(!$isPresent)
                    {
                        $query = "INSERT INTO `#__toes_organization_has_official` 
                            ( organization_id, user, organization_official_type)  
                            VALUES(" . $data['official'] . ", " . $user_id . ", " . $data['official_roll'] . ")	";
                    }
                }

                break;
            case 'club':
                if ($data['previous_user']) {
                    $query = "UPDATE `#__toes_club_official` 
                            SET `club` = " . $data['official'] . ",`user` = " . $user_id . "
                            , `club_official_type` = " . $data['official_roll'] . "
                            WHERE `club` = " . $data['previous_club'] . "
                            AND `user` = " . $data['previous_user'] . " AND `club_official_type` = " . $data['previous_roll'];
                } else {
                    $query = "INSERT INTO `#__toes_club_official` 
                            ( club, user, club_official_type)  
                            VALUES(" . $data['official'] . ", " . $user_id . ", " . $data['official_roll'] . ")";
                }
                break;
            case 'show':
                if ($data['previous_user']) {
                    $query = "UPDATE `#__toes_show_has_official` 
                            SET `show` = " . $data['official'] . ",`user` = " . $user_id . "
                            , `show_official_type` = " . $data['official_roll'] . "
                            WHERE `show` = " . $data['previous_show'] . "
                            AND `user` = " . $data['previous_user'] . " AND `show_official_type` = " . $data['previous_roll'];
                } else {
                    $query = "INSERT INTO `#__toes_show_has_official` (`show` ,`user` ,`show_official_type`)
								VALUES (" . $data['official'] . ", " . $user_id . ", " . $data['official_roll'] . ")";
                }
                break;
        }

        //echo $query.'<br/>';die;
        $db->setQuery($query);
        if ($db->query()) {
            if ($data['official_type'] == 'organization') {
                if($data['official_roll'] == 4)
                {
                    $sql = "UPDATE `#__toes_competitive_region` SET `competitive_region_regional_director` = {$user_id} WHERE `competitive_region_id` = {$data['region']}";
                    //echo $sql;
                    $db->setQuery($sql);
                    $db->query();
                    
                    //#### spider hack ###
                    if($data['previous_region'] && $data['region'] != $data['previous_region'])
                    {
                        $sql = "UPDATE `#__toes_competitive_region` SET `competitive_region_regional_director` = 0 WHERE `competitive_region_id` = {$data['previous_region']}";
                        $db->setQuery($sql);
                        $db->query();
                    }
                    //#### spider hack complete ####
                
                    $query = $db->getQuery(true);
                    $query->select('user_id');
                    $query->from('#__user_usergroup_map AS m');
                    $query->join('left','#__usergroups AS g ON g.id = m.group_id');
                    $query->where('user_id = '.$user_id);
                    $query->where('title = "Organization Official"');
                    $db->setQuery($query);
                    if(!$db->loadResult())
                    {
                        $query = $db->getQuery(true);
                        $query->insert('#__user_usergroup_map');
                        $query->set('user_id = '.$user_id);
                        $query->set('group_id = (SELECT id FROM #__usergroups WHERE title = "Organization Official")');
                        $db->setQuery($query);
                        $db->query();
                    }
                }
                else
                {
                    if($data['previous_user'] && !TOESHelper::is_regionaldirector($data['previous_user']))
                    {
                        $query = $db->getQuery(true);
                        $query->delete('#__user_usergroup_map');
                        $query->where('user_id = '.$data['previous_user']);
                        $query->where('group_id = (SELECT id FROM #__usergroups WHERE title = "Organization Official")');
                        $db->setQuery($query);
                        $db->query();
                    }
                    
                    if(!TOESHelper::is_regionaldirector($user_id))
                    {
                        $query = $db->getQuery(true);
                        $query->delete('#__user_usergroup_map');
                        $query->where('user_id = '.$user_id);
                        $query->where('group_id = (SELECT id FROM #__usergroups WHERE title = "Organization Official")');
                        $db->setQuery($query);
                        $db->query();
                    }
                }                
            }
            elseif ($data['official_type'] == 'club') {

                $query = $db->getQuery(true);
                $query->select('user_id');
                $query->from('#__user_usergroup_map AS m');
                $query->join('left','#__usergroups AS g ON g.id = m.group_id');
                $query->where('user_id = '.$user_id);
                $query->where('title = "Club Official"');
                $db->setQuery($query);
                
                if(!$db->loadResult())
                {
                    $query = $db->getQuery(true);
                    $query->insert('#__user_usergroup_map');
                    $query->set('user_id = '.$user_id);
                    $query->set('group_id = (SELECT id FROM #__usergroups WHERE title = "Club Official")');
                    $db->setQuery($query);
                    $db->query();
                }
                
                if($data['previous_user'] && !TOESHelper::is_clubofficial($data['previous_user']))
                {
                    $query = $db->getQuery(true);
                    $query->delete('#__user_usergroup_map');
                    $query->where('user_id = '.$data['previous_user']);
                    $query->where('group_id = (SELECT id FROM #__usergroups WHERE title = "Club Official")');
                    $db->setQuery($query);
                    $db->query();
                }                
            }
        }

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
        parent::cleanCache('com_taskmanager');
        parent::cleanCache('_system');
    }
}
