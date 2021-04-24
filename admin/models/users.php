<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * users Component users Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class ToesModelUsers extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) 
		{
			$config['filter_fields'] = array('id','username');
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('username', 'asc');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		/*	
		$query->select('user.id, user.name,	user.username 	');
		$query->from('#__users as user');
		$query->select('ugmap.group_id');
		$query->join('LEFT', '#__user_usergroup_map as ugmap ON user.id = ugmap.user_id');
		$query->select('ug.title');
		$query->join('LEFT', '#__usergroups as ug ON ugmap.group_id = ug.id');
		
		$query->select('toho.user, toho.organization_official_type as organization_official_type_id');
		$query->join('LEFT', '#__toes_organization_has_official as toho ON user.id = toho.user');
		$query->select('toot.organization_official_type');
		$query->join('LEFT', '#__toes_organization_official_type as toot ON toho.organization_official_type = toot.organization_official_type_id');
		
		$query->where("ug.title IN ('Organization Officials','Club Officials','Show Officials')");
		*/
		
		$query = "";
		
		$query .= "SELECT user.id, user.name, user.username ,ugmap.group_id,ug.title,
					(
						CASE ug.title
						WHEN 'Organization Officials' THEN (SELECT organization_official_type FROM #__toes_organization_has_official WHERE user = user.id)
						WHEN 'Club Officials' THEN (SELECT club_official_type FROM #__toes_club_official WHERE user = user.id)
						WHEN 'Show Officials' THEN (SELECT show_official_type FROM #__toes_show_has_official WHERE user = user.id)
						END
					) AS roll_id,
					(
						CASE ug.title WHEN 'Organization Officials' THEN (SELECT organization_official_type FROM #__toes_organization_official_type WHERE organization_official_type_id = roll_id)
						WHEN 'Club Officials' THEN (SELECT club_official_type FROM #__toes_club_official_type WHERE club_official_type_id = roll_id)	
						WHEN 'Show Officials' THEN (SELECT show_official_type FROM #__toes_show_official_type WHERE show_official_type_id = roll_id)
						END
					) AS roll
				FROM #__users as user LEFT JOIN #__user_usergroup_map as ugmap ON user.id = ugmap.user_id 
				LEFT JOIN #__usergroups as ug ON ugmap.group_id = ug.id 
				WHERE ug.title IN ('Organization Officials','Club Officials','Show Officials') ORDER BY username asc";
		
		
		
		
		//$query->order($db->escape($this->getState('list.ordering', 'username')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		
		//echo $query;
		//die;
		
		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		return $items;
	}
	
	
	public function getItem()
	{
		$app = JFactory::getApplication();
		$pk = $app->input->getVar('id', '');
		/*
		$false	= false;
		$table = $this->getTable();
		$return = $table->load($pk);
		if ($return === false && $table->getError()) 
		{
			$this->setError($table->getError());
			return $false;
		}

		$properties = $table->getProperties(1);
		$this->_cache[$pk] = JArrayHelper::toObject($properties, 'JObject');
		*/

		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		/*
		$query->select('user.id, user.name,	user.username 	');
		$query->from('#__users as user');
		$query->select('ugmap.group_id');
		$query->join('LEFT', '#__user_usergroup_map as ugmap ON user.id = ugmap.user_id');
		$query->select('ug.title');
		$query->join('LEFT', '#__usergroups as ug ON ugmap.group_id = ug.id');
		
		$query->select('toho.user, toho.organization_official_type as organization_official_type_id');
		$query->join('LEFT', '#__toes_organization_has_official as toho ON user.id = toho.user');
		$query->select('toot.organization_official_type');
		$query->join('LEFT', '#__toes_organization_official_type as toot ON toho.organization_official_type = toot.organization_official_type_id');
		*/
		//$query->where('user.id = '.$pk);
		
		$query = "";
		
		$query .= "SELECT user.id, user.name, user.username ,ugmap.group_id,ug.title,
					(
						CASE ug.title
						WHEN 'Organization Officials' THEN (SELECT organization_official_type FROM #__toes_organization_has_official WHERE user = user.id)
						WHEN 'Club Officials' THEN (SELECT club_official_type FROM #__toes_club_official WHERE user = user.id)
						WHEN 'Show Officials' THEN (SELECT show_official_type FROM #__toes_show_has_official WHERE user = user.id)
						END
					) AS roll_id,
					(
						CASE ug.title WHEN 'Organization Officials' THEN (SELECT organization_official_type FROM #__toes_organization_official_type WHERE organization_official_type_id = roll_id)
						WHEN 'Club Officials' THEN (SELECT club_official_type FROM #__toes_club_official_type WHERE club_official_type_id = roll_id)	
						WHEN 'Show Officials' THEN (SELECT show_official_type FROM #__toes_show_official_type WHERE show_official_type_id = roll_id)
						END
					) AS roll
				FROM #__users as user LEFT JOIN #__user_usergroup_map as ugmap ON user.id = ugmap.user_id 
				LEFT JOIN #__usergroups as ug ON ugmap.group_id = ug.id 
				WHERE ug.title IN ('Organization Officials','Club Officials','Show Officials') AND user.id = ".$pk;
			
		//echo $query;die;
		
		$db->setQuery($query);
		return $db->loadObject();

		//return $this->_cache[$pk];
	}
	
	public function getuser_organization()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `organization_id` as value, concat(`organization_name`,'(',`organization_abbreviation`,')') AS `text` FROM `#__toes_organization`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}

	public function getTable($type = 'user', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState('user.id');
		$isNew		= true;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
			
		switch ($data['user_group'])
		{
			case 'Organization Officials':
				$official_type = '1';
				break;
			case 'Club Officials':
				$official_type = '2';
				break;
			case 'Show Officials':
				$official_type = '3';
				break;
		}
			
		if($official_type == '1')
		{
			$query = "SELECT * FROM #__toes_organization_has_official WHERE user = ".$data['id'];
			$db->setQuery($query);
			$org_roll_id = $db->loadObject();
			if($org_roll_id)
			$query = "UPDATE #__toes_organization_has_official SET organization_official_type = ".$data['official_type_id']." WHERE user =".$data['id']." ";
			else
			$query = "INSERT INTO #__toes_organization_has_official (user, organization_official_type) VALUE(".$data['id'].",".$data['official_type_id'].")";
			
			$db->setQuery($query);
			$db->query();
		}
		else if($official_type == '2')
		{
			$query = "SELECT * FROM #__toes_club_official WHERE user = ".$data['id'];
			$db->setQuery($query);
			$club_roll_id = $db->loadObject();
			if($club_roll_id)
			$query = "UPDATE #__toes_club_official SET club_official_type = ".$data['official_type_id']." WHERE user =".$data['id']." ";
			else
			$query = "INSERT INTO #__toes_club_official (user, club_official_type) VALUE(".$data['id'].",".$data['official_type_id'].")";
			
			$db->setQuery($query);
			$db->query();
		}
		else if($official_type == '3')
		{
			$query = "SELECT * FROM #__toes_show_has_official WHERE user = ".$data['id'];
			$db->setQuery($query);
			$show_roll_id = $db->loadObject();
			if($show_roll_id)
			$query = "UPDATE #__toes_show_has_official SET show_official_type = ".$data['official_type_id']." WHERE user =".$data['id']." ";
			else
			$query = "INSERT INTO #__toes_show_has_official (user, show_official_type) VALUE(".$data['id'].",".$data['official_type_id'].")";
			
			$db->setQuery($query);
			$db->query();
		}
						
		$user = JFactory::getUser();
		
		// Clean the cache.
		$this->cleanCache();
		//$this->setState('user.id', $table->id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
