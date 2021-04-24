<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template styles list controller class.
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESControllerShows extends JControllerAdmin {

    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'shows', $prefix = 'ToesModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        
        return $model;
    }

	public function getClubs() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT `club_id` AS `key`, `club_name` AS `value` 
                FROM #__toes_club
                WHERE LOWER(club_name) LIKE  " . $like;

            $db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
                echo json_encode($users);
            }
        }
        $app->close();
	}

	public function getCountries() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT id AS `key`, `name` AS `value`, `country_uses_states`  
                FROM #__toes_country
                WHERE LOWER(name) LIKE  " . $like. "
				OR LOWER(alpha_2) LIKE  " . $like;
			
            $db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
                echo json_encode($users);
            }
        }
        $app->close();
	}

	public function getStates() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');
        $cntry_id = $app->input->getVar('country_id');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT id AS `key`, `name` AS `value`  
                FROM #__toes_states_per_country
                WHERE LOWER(name) LIKE  " . $like;
				
			if($cntry_id) {
				$query .= " AND country_id = ".$cntry_id;
			}

            $db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
                echo json_encode($users);
            }
        }
        $app->close();
	}

	public function getCities() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');
        $state_id = $app->input->getVar('state_id');
		$cntry_id = $app->input->getVar('country_id');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

			
            $query = "SELECT distinct(address_city) AS `key`, `address_city` AS `value`  
                FROM #__toes_address
                WHERE LOWER(address_city) LIKE  " . $like;
			 
			 /*
            $query = "SELECT id AS `key`, `name` AS `value`  
                FROM #__toes_cities_per_state
                WHERE LOWER(name) LIKE  " . $like;
			*/
			if($state_id) {
				$query .= " AND state_id = ".$state_id;
			}
			if($cntry_id) {
				$query .= " AND country_id = ".$cntry_id;
			}

			$db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
                echo json_encode($users);
            }
        }
        $app->close();
	}
    
    public function getEntriesNeedsConfirmation()
    {
        $app    = JFactory::getApplication();
        $show_id = $app->input->getInt('id');
        
        $model = $this->getModel();
        if($model->getEntriesNeedsConfirmation($show_id))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
    }

    public function updateStatus()
    {
        $app    = JFactory::getApplication();
        $status = $app->input->getVar('status');
        $show_id = $app->input->getInt('id');
        $club_id = $app->input->getInt('club_id');
        
        $model = $this->getModel();
      
        if($model->updateStatus($show_id, $status, $club_id))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        
        $app->close();
    }
	
	public function delete()
	{
        $app    = JFactory::getApplication();
        $show_id = $app->input->getInt('id');
        $model = $this->getModel();
        if($model->delete($show_id))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
	}

	public function lockLatepages()
	{
        $app    = JFactory::getApplication();
        $show_id = $app->input->getInt('show_id');

        $model = $this->getModel();
        if($model->lockLatepages($show_id,'1'))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
	}

	public function lockCatalog()
	{
        $app    = JFactory::getApplication();
        $show_id = $app->input->getInt('show_id');

        $model = $this->getModel();
        if($model->lockCatalog($show_id,'1'))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
	}

	public function unlockLatepages()
	{
        $app    = JFactory::getApplication();
        $show_id = $app->input->getInt('show_id');

        $model = $this->getModel();
        if($model->lockLatepages($show_id,'0'))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
	}

	public function unlockCatalog()
	{
        $app    = JFactory::getApplication();
        $show_id = $app->input->getInt('show_id');

        $model = $this->getModel();
        if($model->lockCatalog($show_id,'0'))
        {
            echo 1;
        }
        else
        {
            echo $model->getError();
        }
        $app->close();
    }
    
    public function validateCatalogLocking() {
        $app    = JFactory::getApplication();
        $db     = JFactory::getDbo();
        $show_id = $app->input->getInt('show_id');

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
            echo JText::_("COM_TOES_LOCK_CATALOG_ERROR");
        } else {
            $query = $db->getQuery(true);
            $query->select('`e`.`entry_id`');
            $query->from("`#__toes_entry` AS `e`");
            $query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
            $query->where('`estat`.`entry_status` = '.$db->quote('Waiting List'));        
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
                        OR `estat`.`entry_status` = '.$db->quote('Waiting List').' 
                        OR `estat`.`entry_status` = '.$db->quote('Confirmed & Paid').')');
            $query->where('`p`.`placeholder_show` = ' . $show_id);
            $db->setQuery($query);
            $placeholders = $db->loadColumn();

            if($entries || $placeholders) {
                echo 2;
            } else {
                echo 1;
            }
        }

        $app->close();
    }
    
    public function validateLatePagesLocking() {
        $app    = JFactory::getApplication();
        $db     = JFactory::getDbo();
        $show_id = $app->input->getInt('show_id');

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
            echo JText::_("COM_TOES_LOCK_LATE_PAGES_ERROR");
        } else {
            $query = $db->getQuery(true);
            $query->select('`e`.`entry_id`');
            $query->from("`#__toes_entry` AS `e`");
            $query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
            $query->where('`estat`.`entry_status` = '.$db->quote('Waiting List'));        
            $query->where('(`e`.`catalog_number` = 0 OR `e`.`catalog_number` IS NULL)');
            $query->where('`e`.`entry_show` = ' . $show_id);
            $db->setQuery($query);
            $entries = $db->loadColumn();

            $query = $db->getQuery(true);
            $query->select('`p`.`placeholder_id`');
            $query->from("`#__toes_placeholder` AS `p`");
            $query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
            $query->join('LEFT', '#__toes_entry_status AS estat ON estat.entry_status_id = pd.placeholder_day_placeholder_status');
            $query->where('`estat`.`entry_status` = '.$db->quote('Waiting List'));
            $query->where('`p`.`placeholder_show` = ' . $show_id);
            $db->setQuery($query);
            $placeholders = $db->loadColumn();

            if($entries || $placeholders) {
                echo 2;
            } else {
                echo 1;
            }
        }
        $app->close();
    }

	public function sync_db()
	{
		$app    = JFactory::getApplication();
		$action = $app->input->getVar('action');
		$show_id = $app->input->getInt('show_id',0);
		
		$model = $this->getModel();
		if($model->sync_db($action, $show_id))
		{
			echo 1;
		}
		else
		{
			echo $model->getError();
		}
		$app->close();
	} 
	
	public function conflicted_clubofficialapproveshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$conf_showid = $app->input->getInt('conf_showid');
		$show_id = $app->input->getInt('show_id');
		
		$approval = '1';
		$query1 = $db->getQuery(true);
		$query1 = "update `#__toes_show_club_approval` set 
				`approval`=".$db->quote($approval)." 
				where existing_show_id=".$db->quote($conf_showid)." AND `new_conflicting_show_id`=".$db->quote($show_id);
		$db->setQuery($query1);
		$db->query();
		
		$query = $db->getQuery(true);
		$query->select('count(*)');
		$query->from('#__toes_show_club_approval as sca');
		$query->where('sca.new_conflicting_show_id ='.$db->quote($show_id).'AND sca.approval= 0');
		$db->setQuery($query);
		$scaresult = $db->loadResult();
		if(!$scaresult)$scaresult = 0;
		
		$query1 = $db->getQuery(true);
		$query1->select('count(*)');
		$query1->from('#__toes_show_regional_director_approval as rd');
		$query1->where('rd.new_conflicting_show_id ='.$db->quote($show_id).'AND rd.approval= 0');
		$db->setQuery($query1);
		$rdresult = $db->loadResult();
		if(!$rdresult)$rdresult = 0;
		
		if( $scaresult == 0 && $rdresult == 0)
		{
			$status = 'Approved';
			$query = $db->getQuery(true);
			$query->update('#__toes_show');
			$query->set('`show_status` = (SELECT `show_status_id` FROM `#__toes_show_status` WHERE `show_status` = ' . $db->quote($status) . ')');
			$query->where('`show_id` = ' . $db->quote($show_id));
			$db->setQuery($query);
			$db->query();
		}
		echo 1;
		exit();
		
	}
	
	public function conflicted_rdapproveshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$reason = $app->input->get('data','','raw');
		$approval = '1';
		$reject = '0';
		$query1 = $db->getQuery(true);
		$query1 = "update `#__toes_show_regional_director_approval` set `reason`=".$db->quote($_POST['reason']).",
				`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
				where existing_show_id=".$db->quote($_POST['conf_showid'])." AND `new_conflicting_show_id`=".$db->quote($_POST['show_id']);
		$db->setQuery($query1);
		$db->query();
		
		$query = $db->getQuery(true);
		$query->select('count(*)');
		$query->from('#__toes_show_club_approval as sca');
		$query->where('sca.new_conflicting_show_id ='.$db->quote($_POST['show_id']).'AND sca.approval= 0');
		$db->setQuery($query);
		$scaresult = $db->loadResult();
		if(!$scaresult)$scaresult = 0;
		
		$query1 = $db->getQuery(true);
		$query1->select('count(*)');
		$query1->from('#__toes_show_regional_director_approval as rd');
		$query1->where('rd.new_conflicting_show_id ='.$db->quote($_POST['show_id']).'AND rd.approval= 0');
		$db->setQuery($query1);
		$rdresult = $db->loadResult();
		if(!$rdresult)$rdresult = 0;
		
		if($scaresult == 0 && $rdresult == 0)
		{
			$status = 'Approved';
			$query = $db->getQuery(true);
			$query->update('#__toes_show');
			$query->set('`show_status` = (SELECT `show_status_id` FROM `#__toes_show_status` WHERE `show_status` = ' . $db->quote($status) . ')');
			$query->where('`show_id` = ' . $db->quote($_POST['show_id']));
			
			$db->setQuery($query);
			$db->query();
		}
	
		echo 1;
		exit();
	}
	
	public function conflicted_rdrejectshow()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$reason = $app->input->get('data','','raw');
		$approval = '0';
		$reject = '1';
		$query1 = $db->getQuery(true);
		$query1 = "update `#__toes_show_regional_director_approval` set `reason`=".$db->quote($_POST['reason']).",
				`reject`=".$db->quote($reject).",`approval`=".$db->quote($approval)." 
				where existing_show_id=".$db->quote($_POST['conf_showid'])." AND `new_conflicting_show_id`=".$db->quote($_POST['show_id']);
		$db->setQuery($query1);
		$db->query();
		
		
		echo 1;
		exit();
	}
}
