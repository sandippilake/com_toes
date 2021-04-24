<?php

/**
 * @package	Joomla.Administrator
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * @package	Joomla.Administrator
 * @since	1.6
 */
class ToesModelClubs extends JModelList {

    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array('a.club_id', 'a.club_name', 'a.club_abbreviation'
                , 'a.club_website', 'a.club_email', 'a.club_invoice_paypal', 'b.competitive_region_name','c.organization_name');
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication('administrator');
        // List state information.
        parent::populateState('club_name', 'asc');
    }

    protected function getListQuery() {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('a.club_id, a.club_name, a.club_abbreviation, a.club_website, a.club_email, a.club_invoice_paypal, a.club_paypal, a.club_iban, a.club_bic, a.club_account_holder_name, a.club_account_holder_address, a.club_account_holder_zip	club_account_holder_city, a.club_account_holder_state, a.club_account_holder_country, a.club_organization, a.club_competitive_region, a.club_cost_per_entry');
        $query->from('#__toes_club as a');
        $query->select('b.competitive_region_name');
        $query->join('LEFT', '#__toes_competitive_region AS b ON b.competitive_region_id = a.club_competitive_region');
        $query->select('c.organization_name');
        $query->join('LEFT', '#__toes_organization AS c ON c.organization_id = a.club_organization');
        $query->order($db->escape($this->getState('list.ordering', 'a.club_name')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
	
		// Filter by search keyword
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where('a.club_name LIKE ' . $db->quote('%'.$search.'%'));
		}

        return $query;
    }

    public function getItems() {
        $items = parent::getItems();
        return $items;
    }

    public function getItem() {
		$app = JFactory::getApplication();
        $pk = $app->input->getVar('id', '');
        $false = false;
        $table = $this->getTable();
        $return = $table->load($pk);
        if ($return === false && $table->getError()) {
            $this->setError($table->getError());
            return $false;
        }

        $properties = $table->getProperties(1);
        $this->_cache[$pk] = JArrayHelper::toObject($properties, 'JObject');

        return $this->_cache[$pk];
    }

    public function getOrganizations() {
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query = "SELECT `organization_id` as value, concat(`organization_name`,' ( ',`organization_abbreviation`,' ) ') AS `text` FROM `#__toes_organization`";
        //echo $query;die;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getCompetativeRegions() {
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query = "SELECT `competitive_region_id` as value, CONCAT(`competitive_region_name`,' ( ',`competitive_region_abbreviation`,' ) ') AS `text` FROM `#__toes_competitive_region`";
        //echo $query;die;
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getTable($type = 'club', $prefix = 'toesTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function save($data) {
        // Initialise variables;
        $user = JFactory::getUser();
        $dispatcher = JDispatcher::getInstance();
        $table = $this->getTable();
        $pk = (!empty($data['club_id'])) ? $data['club_id'] : (int) $this->getState('club.club_id');
        $isNew = true;

        if ($pk > 0) {
            $table->load($pk);
            $isNew = false;
        }
        
        if(!isset($data['club_on_toes_bad_debt_list']))
        	$data['club_on_toes_bad_debt_list'] = 0;

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
        $this->setState('club.club_id', $table->club_id);

        return true;
    }

    protected function cleanCache($group = null, $client_id = 0) {
        parent::cleanCache('com_toes');
        parent::cleanCache('_system');
    }

}
