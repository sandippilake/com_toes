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
 * TOES Component regnumberformats Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_toes
 * @since		1.6
 */
class ToesModelCities extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'city.id' ,
				'city.name' ,
				'city.state_id' ,
				'city.country_id', 
				'state_id', 'state.name' ,
				'country_id', 'country.name'
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// List state information.
		parent::populateState('city.name', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.country_id');
		$id .= ':' . $this->getState('filter.state_id');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('city.id, city.name, city.state_id, city.country_id');
		$query->select('state.name AS state_name,country.name AS country_name');
		$query->from('#__toes_cities_per_state AS city');
		$query->join('left','#__toes_states_per_country AS state ON city.state_id = state.id');
		$query->join('left','#__toes_country AS country ON city.country_id = country.id');
		$query->order($db->escape($this->getState('list.ordering', 'city.name')).' '.$db->escape($this->getState('list.direction', 'ASC')));
	
		// Filter by search keyword
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where('city.name LIKE ' . $db->quote('%'.$search.'%'));
		}

		// Filter by country
		$country_id = $this->getState('filter.country_id');

		if ($country_id)
		{
			$query->where('city.country_id = ' . (int) $country_id);
		}
		
		// Filter by state
		$state_id = $this->getState('filter.state_id');

		if ($state_id)
		{
			$query->where('city.state_id = ' . (int) $state_id);
		}
		
		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		return $items;
	}
	
	public function getForm()
	{
		// Get the form.
		$form = $this->loadForm('com_toes.cities', 'cities', array('control' => 'jform', 'load_data' => true));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}
	
	protected function loadFormData()
	{
		$app = JFactory::getApplication();
		$layout = $app->input->get('layout','');
		
		if($layout == 'edit') {
			// Check the session for previously entered form data.
			$data = $app->getUserState('com_toes.edit.city.data', array());

			if (empty($data))
			{
				$data = $this->getItem();
			}
		} else {
			
			$data = parent::loadFormData();
		}
		
		return $data;
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

		if($pk) {
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);

			$query->select('city.id, state.name AS state_name, country.name AS country_name');
			$query->from('#__toes_cities_per_state AS city');
			$query->join('left','#__toes_states_per_country AS state ON city.state_id = state.id');
			$query->join('left','#__toes_country AS country ON city.country_id = country.id');
			$query->where('city.id = '.$pk);

			$db->setQuery($query);
			$city = $db->loadObject();

			if($city) {
				$this->_cache[$pk]->state_name = $city->state_name;
				$this->_cache[$pk]->country_name = $city->country_name;
			} else {
				$this->_cache[$pk]->state_name = '';
				$this->_cache[$pk]->country_name = '';
			}
		} else {
			$this->_cache[$pk]->state_name = '';
			$this->_cache[$pk]->country_name = '';
		}
		
		return $this->_cache[$pk];
	}

	public function getTable($type = 'city', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState('city.id');
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
		$this->setState('city.id', $table->id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
