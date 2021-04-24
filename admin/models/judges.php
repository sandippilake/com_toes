<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * TOES Component judges Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_toes
 * @since		1.6
 */
class ToesModelJudges extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('j.judge_id','j.judge_abbreviation','s.judge_status','l.judge_level','j.judge_organization');
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('u.name', 'asc');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('j.judge_id, j.judge_organization, j.judge_abbreviation ');
		$query->from('#__toes_judge as j');

		$query->select('u.name, u.email');
		$query->join('left','#__users as u ON u.id = j.user');
		
		$query->select('s.judge_status');
		$query->join('LEFT', '#__toes_judge_status AS s ON s.judge_status_id = j.judge_status');
		
		$query->select('l.judge_level');
		$query->join('LEFT', '#__toes_judge_level AS l ON l.judge_level_id = j.judge_level');
		
		$query->order($db->escape($this->getState('list.ordering', 'u.name')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		
	
		// Filter by search keyword
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where('u.name LIKE ' . $db->quote('%'.$search.'%'));
		}
		//echo $query;die;
		 
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

		return $this->_cache[$pk];
	}
	
	public function getjudge_organization()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `organization_id` as value, concat(`organization_name`,'(',`organization_abbreviation`,')') AS `text` FROM `#__toes_organization`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}

	public function getjudge_user()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
	
		$query = "SELECT a.`id` as value, concat(b.`lastname`,' ',b.`firstname`,' - ',a.`username`) AS `text` 
				FROM #__users as a 
				LEFT JOIN #__comprofiler as b ON a.id = b.user_id
				WHERE concat(b.`lastname`,' ',b.`firstname`,' - ',a.`username`) != ''
				ORDER BY b.lastname";       

		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}
	
	public function getjudge_status()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `judge_status_id` as value, `judge_status` AS `text` FROM `#__toes_judge_status`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getjudge_level()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
	
		$query="SELECT `judge_level_id` as value, `judge_level` AS `text` FROM `#__toes_judge_level`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}
	
	public function getTable($type = 'judge', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['judge_id'])) ? $data['judge_id'] : (int)$this->getState('judge.judge_id');
		$isNew		= true;

		if ($pk > 0) {
			$table->load($pk);
			$isNew = false;
		}
		
		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Prepare the row for saving
		//$this->prepareTable($table);

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

		$user = JFactory::getUser();
		
		// Clean the cache.
		$this->cleanCache();
		$this->setState('judge.judge_id', $table->judge_id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
