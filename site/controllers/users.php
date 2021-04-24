<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template styles list controller class.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESControllerUsers extends JControllerAdmin {

    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'user', $prefix = 'ToesModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    public function delete() {
    	$app = JFactory::getApplication();
        $user_id = $app->input->getVar('id');
        $official = $app->input->getVar('official');
        $roll_id = $app->input->getVar('roll_id');
        $official_id = $app->input->getVar('official_id');
        
        $region = $app->input->getVar('region');

        $db = JFactory::getDBO();

        if ($official == 'organization') {
            
            if($roll_id == 4)
            {
                $sql = "SELECT * FROM `#__toes_competitive_region` WHERE `competitive_region_regional_director` = {$user_id} AND `competitive_region_id` = {$region}";
                $db->setQuery($sql);
                if($db->loadResult())
                {
                    $sql = "UPDATE `#__toes_competitive_region` SET `competitive_region_regional_director` = 0 WHERE `competitive_region_id` = {$region}";
                    $db->setQuery($sql);
                    $db->query();
                }
            }
            
            $sql = "SELECT * FROM `#__toes_competitive_region` WHERE `competitive_region_regional_director` = {$user_id}";
            $db->setQuery($sql);
            if(!$db->loadResult())
            {
                $query = "DELETE FROM #__toes_organization_has_official WHERE `organization_official_type` = " . $roll_id . "
                                                     AND `organization_id` = " . $official_id . " AND `user` = " . $user_id;
            }
            else
            {
                $query = "SELECT * FROM `#__toes_organization_has_official`";
            }
                       
        } else if ($official == 'club') {
            $query = "DELETE FROM #__toes_club_official WHERE `club_official_type` = " . $roll_id . "
						 AND `club` = " . $official_id . " AND `user` = " . $user_id;
            
        } else if ($official == 'show') {
            $query = "DELETE FROM #__toes_show_has_official WHERE `show_official_type` = " . $roll_id . "
					  AND `show` = " . $official_id . " AND `user` = " . $user_id;
        }

        $db->setQuery($query);
        if ($db->query())
        {
            if($official == 'organization' && !TOESHelper::is_organizationofficial($user_id))
            {
                $query = $db->getQuery(true);
                $query->delete('#__user_usergroup_map');
                $query->where('user_id = '.$user_id);
                $query->where('group_id = (SELECT id FROM #__usergroups WHERE title = "Organization Official")');
                $db->setQuery($query);
                $db->query();
            } 
            else if($official == 'club' && !TOESHelper::is_clubofficial($user_id))
            {
                $query = $db->getQuery(true);
                $query->delete('#__user_usergroup_map');
                $query->where('user_id = '.$user_id);
                $query->where('group_id = (SELECT id FROM #__usergroups WHERE title = "Club Official")');
                $db->setQuery($query);
                $db->query();
            }

            echo 1;
        }
        else
            echo 0;
        
        $app->close();
    }

    public function getclublist() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        $user = JFactory::getUser();
        $isClubOfficial = TOESHelper::is_clubofficial($user->id);
        $isShowManager = TOESHelper::is_showmanager($user->id);
        
        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT c.`club_id` AS `key`, concat(c.`club_name`,' (',c.`club_abbreviation`,')') AS value
                    FROM `#__toes_club` AS c ";

            if(TOESHelper::isAdmin()) {
                $query .= " WHERE LOWER(c.`club_name`) LIKE  " . $like . "
			 		GROUP BY c.`club_id`
                    ORDER BY c.`club_name` ASC 
                    ";
            } 
            else if($isClubOfficial)
            {
                $query .= ' LEFT JOIN `#__toes_club_official` AS co ON co.`club` = c.`club_id`
                        WHERE co.`user` = '.$user->id. " 
                        AND LOWER(c.`club_name`) LIKE  " . $like . "
                        GROUP BY c.`club_id`
                        ORDER BY c.`club_name` ASC 
                        "; 
            }
            else if($isShowManager)
            {	
                $query .= " WHERE c.`club_id` IN (
                		SELECT cos.`club` FROM `#__toes_club_organizes_show` AS cos 
                        LEFT JOIN `#__toes_show_has_official` AS so ON so.`show` = cos.`show`
                        WHERE so.`user` = ".$user->id." AND so.`show_official_type` = (SELECT `show_official_type_id` FROM `#__toes_show_official_type` WHERE `show_official_type` = 'Show Manager') 
                        )
                        AND LOWER(c.`club_name`) LIKE " . $like . "
                        GROUP BY c.`club_id`
                        ORDER BY c.`club_name` ASC "; 
            }
            else
            {
                $query .= " WHERE 0";
            }
            
            //echo $query;
            $db->setQuery($query);
            $clubs = $db->loadObjectList();
            if (count($clubs)) {
                echo json_encode($clubs);
            }
        }
        $app->close();
    }

    public function getclubuserslist() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        $user = JFactory::getUser();
        $isClubOfficial = TOESHelper::is_clubofficial($user->id);
        $isShowManager = TOESHelper::is_showmanager($user->id);
        
        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT cb.`user_id` AS `key`, concat(cb.`lastname`,' ',cb.`firstname`) AS value
                    FROM `#__comprofiler` AS cb ";

            if(TOESHelper::isAdmin()) {
			$query .= " WHERE cb.`user_id` IN ( SELECT `user` FROM `#__toes_club_official`)
					AND ( LOWER(cb.`lastname`) LIKE  " . $like . " OR LOWER(cb.`firstname`) LIKE  " . $like . " )
                    ORDER BY cb.`lastname` ASC 
                    ";
            } 
            else if($isClubOfficial)
            {
                $query .= " WHERE cb.`user_id` IN ( SELECT `a`.`user` FROM `#__toes_club_official` AS `a` WHERE a.`club` IN ( SELECT b.`club` FROM `#__toes_club_official` AS `b` WHERE `b`.`user` = ".$user->id .") )
                        AND ( LOWER(cb.`lastname`) LIKE  " . $like . " OR LOWER(cb.`firstname`) LIKE  " . $like . " )
						ORDER BY cb.`lastname` ASC 
                        "; 
            }
            else
            {
                $query .= " WHERE 0";
            }
            
            //echo $query;
            $db->setQuery($query);
            $clubs = $db->loadObjectList();
            if (count($clubs)) {
                echo json_encode($clubs);
            }
        }
        $app->close();
    }

	public function getshowuserlist() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        $user = JFactory::getUser();
        $isClubOfficial = TOESHelper::is_clubofficial($user->id);
        $isShowManager = TOESHelper::is_showmanager($user->id);
        
        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT cb.`user_id` AS `key`, concat(cb.`lastname`,' ',cb.`firstname`) AS value
                    FROM `#__comprofiler` AS cb ";

			$query .= " WHERE cb.`user_id` IN ( SELECT `user` FROM `#__toes_show_has_official`)
				AND ( LOWER(cb.`lastname`) LIKE  " . $like . " OR LOWER(cb.`firstname`) LIKE  " . $like . " )
				ORDER BY cb.`lastname` ASC 
				";
            
            //echo $query;
            $db->setQuery($query);
            $clubs = $db->loadObjectList();
            if (count($clubs)) {
                echo json_encode($clubs);
            }
        }
        $app->close();
    }

	
    public function getlocationlist() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');
//cntry.name LEFT JOIN #__toes_country as cntry ON cntry.id = b.address_country
//state.name LEFT JOIN #__toes_states_per_country as `state` ON `state`.id = b.address_state
//city.name LEFT JOIN #__toes_cities_per_state as city ON city.id = b.address_city
        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT a.venue_id AS `key`, concat_ws('', a.venue_name,' ',b.address_city,' ',b.address_state,', ',b.address_country) AS value 
                    FROM #__toes_venue as a
                    LEFT JOIN #__toes_address as b ON a.venue_address = b.address_id
	               
	                
	               
                    WHERE concat_ws('', a.venue_name,' ',b.address_city,' ',`b`.address_state,' ',b.address_country) LIKE  " . $like . "
                    ORDER BY a.venue_id ASC 
                    ";
            //echo $query;
            $db->setQuery($query);
            $locations = $db->loadObjectList();
            if (count($locations)) {
                echo json_encode($locations);
            }
        }

        $app->close();
    }

}
