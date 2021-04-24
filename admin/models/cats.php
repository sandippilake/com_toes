<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_cats
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * cats Component cats Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_cats
 * @since		1.6
 */
class ToesModelcats extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array('tc.cat_id','tc.cat_name');
		}

		parent::__construct($config);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		// List state information.
		parent::populateState('cat_name', 'asc');
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		/*
		 cat_id 	cat_breed 	cat_category 	cat_division 	cat_color 	cat_date_of_birth
		 cat_gender 	cat_prefix 	cat_title 	cat_name 	cat_suffix 	cat_sire 	cat_dam 	
		 cat_breeder 	cat_owner 	cat_competitive_region 	cat_new_trait
		*/
	
		$query="SELECT tc.*,tcgr.category,td.division_name
				,tcg.gender_name,tcp.cat_prefix, tcs.cat_suffix, tcrn.cat_registration_number 
				,date_format(tc.cat_date_of_birth,'%m/%d/%y') as da
				FROM #__toes_cat as tc
				LEFT JOIN #__toes_category as tcgr ON tc.cat_category = tcgr.category_id 
				LEFT JOIN #__toes_division as td ON tc.cat_division = td.division_id 
				LEFT JOIN #__toes_cat_gender as tcg ON tc.cat_gender = tcg.gender_id 
				LEFT JOIN #__toes_cat_prefix as tcp ON tc.cat_prefix= tcp.cat_prefix_id 
				LEFT JOIN #__toes_cat_suffix as tcs ON tc.cat_suffix= tcs.cat_suffix_id 
				LEFT JOIN #__toes_cat_registration_number as tcrn ON tc.cat_id= tcrn.cat_registration_number_cat 
				ORDER BY tc.cat_name
				";
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
	
	public function getcat_organization()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `organization_id` as value, concat(`organization_name`,'(',`organization_abbreviation`,')') AS `text` FROM `#__toes_organization`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}
	
	public function getcat_category()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `category_id` as value, `category` AS `text` FROM `#__toes_category`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}
	
	public function getcat_division()
	{
		$user	= JFactory::getUser();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query="SELECT `division_id` as value, `division_name` AS `text` FROM `#__toes_division`";
		//echo $query;die;
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}

	public function getTable($type = 'cat', $prefix = 'toesTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function save($data)
	{
		// Initialise variables;
		$user	= JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$table		= $this->getTable();
		$pk			= (!empty($data['cat_id'])) ? $data['cat_id'] : (int)$this->getState('cat.cat_id');
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
		$this->setState('cat.cat_id', $table->cat_id);

		return true;
	
	}
	
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}
	
}
