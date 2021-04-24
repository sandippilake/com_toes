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
class ToesModelCountries extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'c.id', 'c.name', 'c.alpha_2', 'c.alpha_3', 'c.country_uses_states', 'r.competitive_region_id', 'region_id'
			);
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('c.name', 'asc');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		$query->select('c.id, c.name, c.alpha_2, c.alpha_3, c.country_uses_states');
		$query->select('CONCAT(r.competitive_region_name," (",r.competitive_region_abbreviation,")") AS region_name');
		$query->from('#__toes_country AS c');
		$query->join('left','#__toes_competitive_region AS r ON r.competitive_region_id = c.competitive_region');
		$query->order($db->escape($this->getState('list.ordering', 'c.name')).' '.$db->escape($this->getState('list.direction', 'ASC')));
	
		// Filter by search keyword
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where('c.name LIKE ' . $db->quote('%'.$search.'%'));
		}

		// Filter by country
		$region_id = $this->getState('filter.region_id');

		if ($region_id)
		{
			$query->where('c.competitive_region = ' . (int) $region_id);
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
		$form = $this->loadForm('com_toes.countries', 'countries', array('control' => 'jform', 'load_data' => true));

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
			$data = $app->getUserState('com_toes.edit.country.data', array());

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

		if($pk && isset($this->_cache[$pk]->competitive_region) && $this->_cache[$pk]->competitive_region) {
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);

			$query->select('competitive_region_name');
			$query->from('#__toes_competitive_region');
			$query->where('competitive_region_id = '.$this->_cache[$pk]->competitive_region);

			$db->setQuery($query);
			$region = $db->loadObject();

			if($region) {
				$this->_cache[$pk]->region_name = $region->competitive_region_name;
			} else {
				$this->_cache[$pk]->region_name = '';
			}
		} else {
			$this->_cache[$pk]->region_name = '';
		}

		return $this->_cache[$pk];
	}

	public function getTable($type = 'country', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int)$this->getState('country.id');
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
		$this->setState('country.id', $table->id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
