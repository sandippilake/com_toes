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
class ToesModelVenues extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('v.venue_id','v.venue_name','v.venue_website','a.address_city'
			,'a.address_zip_code','a.address_state','a.address_country');
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
		parent::populateState('venue_name', 'asc');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		//`cntry`.`name` AS `address_country_name`, `cntry`.`country_uses_states`->$query->join("LEFT","`#__toes_country` AS `cntry` ON `cntry`.`id` = `a`.`address_country`");
		//`state`.`name` AS `address_state_name`->$query->join("LEFT","`#__toes_states_per_country` AS `state` ON `state`.`id` = `a`.`address_state`");
		//`city`.`name` AS `address_city_name`->$query->join("LEFT","`#__toes_cities_per_state` AS `city` ON `city`.`id` = `a`.`address_city`");
		$query->select('v.venue_id, v.venue_name, v.venue_website, v.venue_address, a.address_id, a.address_line_1, a.address_line_2, a.address_line_3, a.address_city AS `address_city_name`, a.address_zip_code, a.address_state  AS `address_state_name`, a.address_country AS `address_country_name`, a.address_type');
		//$query->select('`city`.`name` AS `address_city_name`,`state`.`name` AS `address_state_name`,`cntry`.`name` AS `address_country_name`, `cntry`.`country_uses_states`');
		$query->from('#__toes_venue as v');
		$query->join('LEFT', '#__toes_address AS a ON a.address_id = v.venue_address');
		$query->order($db->escape($this->getState('list.ordering', 'v.venue_name')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		
		$search = $this->getState('filter.search');
		if($search)
		{
			$query->where('v.venue_name LIKE "%'.$search.'%"');
		}
		
		//echo $query;die;
		 
		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		//var_dump($items);
		return $items;
	}
	
	
	public function getItem()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$pk = $app->input->getVar('id', 0);
//`cntry`.`name` AS `address_country_name`->$query->join("LEFT","`#__toes_country` AS `cntry` ON `cntry`.`id` = `a`.`address_country`");
//`state`.`name` AS `address_state_name`->$query->join("LEFT","`#__toes_states_per_country` AS `state` ON `state`.`id` = `a`.`address_state`");
//`city`.`name` AS `address_city_name`->$query->join("LEFT","`#__toes_cities_per_state` AS `city` ON `city`.`id` = `a`.`address_city`");

		if($pk)
		{
			$query	= $db->getQuery(true);
			$query->select('v.venue_id, v.venue_name, v.venue_website, v.venue_address, a.address_id, a.address_line_1, a.address_line_2, a.address_line_3, a.address_city, a.address_zip_code, a.address_state, a.address_country, a.address_type');
			$query->select('`a`.`address_city` AS `address_city_name`,`a`.`address_state` AS `address_state_name`,`a`.`address_country` AS `address_country_name`');
			//$query->select('`city`.`name` AS `address_city_name`,`state`.`name` AS `address_state_name`,`cntry`.`name` AS `address_country_name`, `cntry`.`country_uses_states`');
			$query->from('#__toes_venue as v');
			$query->join('LEFT', '#__toes_address AS a ON a.address_id = v.venue_address');
			$query->where('v.venue_id='.$pk);
			
			$db->setQuery($query);
			$venue = $db->loadObject();
		}
		else
		{
			$venue = new stdClass();
			$venue->venue_id = 0;
			$venue->venue_name = '';
			$venue->venue_website = '';
			$venue->venue_address = 0;
			$venue->address_id = 0;
			$venue->address_line_1 = '';
			$venue->address_line_2 = '';
			$venue->address_line_3 = '';
			$venue->address_city = '';
			$venue->address_zip_code = '';
			$venue->address_state = '';
			$venue->address_country = '';
			$venue->address_type = 1;
			
		}
		
		$this->_cache[$pk] = $venue;

		return $this->_cache[$pk];
	}
	
	public function getTable($type = 'venue', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	public function getAddressTable($type = 'address', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		
		$address_table = $this->getAddressTable();
		$address_id	= (!empty($data['address_id'])) ? $data['address_id'] : (int)$this->getState('venue.address_id');
		$isNew		= true;

		if ($address_id > 0) {
			$address_table->load($address_id);
			$isNew = false;
		}
		
		// Bind the data.
		if (!$address_table->bind($data)) {
			$this->setError($address_table->getError());
			return false;
		}
		// Prepare the row for saving
		//$this->prepareTable($table);

		// Check the data.
		if (!$address_table->check()) {
			$this->setError($address_table->getError());
			return false;
		}

		// Store the data.
		if (!$address_table->store()) {
			$this->setError($address_table->getError());
			return false;
		}
		
		$data['venue_address'] = $address_table->address_id;
		
		$table		= $this->getTable();
		$pk			= (!empty($data['venue_id'])) ? $data['venue_id'] : (int)$this->getState('venue.venue_id');
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
		$this->setState('venue.address_id', $address_table->address_id);
		$this->setState('venue.venue_id', $table->venue_id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
