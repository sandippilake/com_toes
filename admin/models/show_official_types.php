<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_organization_official_types
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * show_official_types Component show_official_types Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_show_official_types
 * @since		1.6
 */
class ToesModelShow_official_types extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'show_official_type_id','show_official_type'
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('show_official_type', 'asc');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('show_official_type_id,show_official_type');
		$query->from($db->quoteName('#__toes_show_official_type'));
		$query->order($db->escape($this->getState('list.ordering', 'show_official_type')).' '.$db->escape($this->getState('list.direction', 'ASC')));
	
		// Filter by search keyword
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where('show_official_type LIKE ' . $db->quote('%'.$search.'%'));
		}
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
	
	public function getshow_official_type_show()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `show_id` as value, concat(`show_name`,'(',`show_abbreviation`,')') AS `text` FROM `#__toes_show`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}

	public function getTable($type = 'show_official_type', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['show_official_type_id'])) ? $data['show_official_type_id'] : (int)$this->getState('show_official_type.show_official_type_id');
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
		$this->setState('show_official_type.show_official_type_id', $table->show_official_type_id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
