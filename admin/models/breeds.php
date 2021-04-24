<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_breeds
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * breeds Component breeds Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_breeds
 * @since		1.6
 */
class ToesModelBreeds extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('a.breed_id','a.breed_name','a.breed_abbreviation','a.breed_group'
			,'a.breed_hair_length','a.breed_color_restrictions','a.breed_organization','b.breed_status');
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('breed_name', 'asc');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('a.breed_id,a.breed_name,a.breed_organization,a.breed_abbreviation,a.breed_group,a.breed_hair_length,a.breed_color_restrictions ');
		$query->from('#__toes_breed as a');
		$query->select('b.breed_status');
		$query->join('LEFT', '#__toes_breed_status AS b ON b.breed_status_id = a.breed_status');
		$query->order($db->escape($this->getState('list.ordering', 'a.breed_name')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		
	
		// Filter by search keyword
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where('a.breed_name LIKE ' . $db->quote('%'.$search.'%'));
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
	
	public function getbreed_organization()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `organization_id` as value, concat(`organization_name`,'(',`organization_abbreviation`,')') AS `text` FROM `#__toes_organization`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}
	
	public function getbreed_status()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `breed_status_id` as value, `breed_status` AS `text` FROM `#__toes_breed_status`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}
		
	public function getTable($type = 'breed', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['breed_id'])) ? $data['breed_id'] : (int)$this->getState('breed.breed_id');
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
		$this->setState('breed.breed_id', $table->breed_id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
