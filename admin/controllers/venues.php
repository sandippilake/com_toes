<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Template styles list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class ToesControllerVenues extends JControllerAdmin
{
	
	public function cancel()
	{
		//parent::cancel();
		$this->setRedirect('index.php?option=com_toes&view=venues');
	}
	
	public function edit()
	{
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
		$id = (int)$array[0];
		$this->setRedirect('index.php?option=com_toes&view=venues&layout=edit&id='.$id);
	}
	
	public function add()
	{
		$this->setRedirect('index.php?option=com_toes&view=venues&layout=edit');
	}
	
	public function save()
	{
		$app = JFactory::getApplication();
		$post = $app->input->post->getArray();
		$model = parent::getModel($name = 'venues', $prefix = 'ToesModel', $config = array());
		$model->save($post);
		$this->setRedirect('index.php?option=com_toes&view=venues','saved successfully');	
	}
	
	public function delete()
	{	
		$app = JFactory::getApplication();
		$array = $app->input->getVar('cid',  0, '', 'array');
				
		$app = JFactory::getApplication();
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="DELETE FROM `#__toes_venue` WHERE venue_id IN (".implode(',',$array).")";
		//echo $query;die;
		$db->setQuery($query);
		$db->query();
			
		$this->setRedirect('index.php?option=com_toes&view=venues','Venue(s) deleted successfully');	
	}

	public function getCountries() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');
		$check_state = $app->input->getVar('check_state');
        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT id AS `key`, `name` AS `value`, `country_uses_states` 
                FROM #__toes_country
                WHERE (LOWER(name) LIKE  " . $like. "
				OR LOWER(alpha_2) LIKE  " . $like.") ";
			
			if($check_state) {
				$query .= " AND country_uses_state = 1";
			}
			
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

			
            $query = "SELECT address_city AS `key`, `address_city` AS `value`  
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
}
