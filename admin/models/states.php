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
class ToesModelStates extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'state.id', 'state.name', 'country.name', 'country_id'
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('state.name', 'asc');
	}
	
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.country_id');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('state.id, state.name, state.country_id, country.name as country_name');
		$query->from('#__toes_states_per_country AS state ');
		$query->join('left','#__toes_country AS country ON state.country_id = country.id');
		$query->order($db->escape($this->getState('list.ordering', 'state.name')).' '.$db->escape($this->getState('list.direction', 'ASC')));
	
		// Filter by search keyword
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where('state.name LIKE ' . $db->quote('%'.$search.'%'));
		}

		// Filter by country
		$country_id = $this->getState('filter.country_id');

		if ($country_id)
		{
			$query->where('state.country_id = ' . (int) $country_id);
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
		$form = $this->loadForm('com_toes.states', 'states', array('control' => 'jform', 'load_data' => true));

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
			$data = $app->getUserState('com_toes.edit.state.data', array());

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

			$query->select('name AS country_name');
			$query->from('#__toes_country');
			$query->where('id = '.$this->_cache[$pk]->country_id);

			$db->setQuery($query);
			$item = $db->loadObject();

			if($item) {
				$this->_cache[$pk]->country_name = $item->country_name;
			} else {
				$this->_cache[$pk]->country_name = '';
			}
		} else {
			$this->_cache[$pk]->country_name = '';
		}
		

		return $this->_cache[$pk];
	}

	public function getTable($type = 'state', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState('state.id');
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
		
		// Clean the cache.
		$this->cleanCache();
		$this->setState('state.id', $table->id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
