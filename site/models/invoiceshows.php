<?php

/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Categories Component Categories Model
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESModelInvoiceshows extends JModelList {

    /**
     * Constructor.
     *
     * @param	array	An optional associative array of configuration settings.
     */
    public function __construct($config = array()) {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array();
        }

        parent::__construct($config);
    }
    protected function populateState($ordering = null, $direction = null)
	{
            

            parent::populateState($ordering, $direction);

            $app = JFactory::getApplication();
			$db = JFactory::getDbo();
			
			$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
			$this->setState('filter.search', $search);
			
			
			$invoicesent = $app->getUserStateFromRequest($this->context . '.filter.invoicesent', 'filter_invoicesent', '', 'string');
			$this->setState('filter.invoicesent', $invoicesent); 

            //$this->setState('list.limit', $limit);
           // $this->setState('list.start', $start);
         
	}
	protected function getListQuery()
	{
		 
		$db    = JFactory::getDbo();
		$app = JFactory::getApplication();
		 
		$start_date = '2019-01-01';
		$invoicesent = (int) $this->state->get('filter.invoicesent');
		 
		 
		$query = $db->getQuery(true);
		 
		$query->select("s.*,v.venue_name,c.club_name");
		$query->from("`#__toes_show` as s");
		$query->join("INNER","`#__toes_venue` as v ON  s.show_venue = v.venue_id"); 
		$query->join("INNER","`#__toes_club_organizes_show` as cs ON  s.show_id = cs.show "); 
		$query->join("INNER","`#__toes_club` as c ON  c.club_id = cs.club"); 
		$query->where("s.show_uses_toes = 1");
		$query->where("s.show_start_date >=".$db->Quote($start_date));
		
		if($invoicesent >= 0)
		$query->where("s.eo_notified_to_invoice_this_show = ".$invoicesent);
		
		/*
		$query = "select s.*,v.venue_name,c.club_name from `#__toes_show` as s JOIN `#__toes_venue` as v ON s.show_venue = v.venue_id 
		JOIN `#__toes_club_organizes_show` as cs ON s.show_id = cs.show 
		JOIN `#__toes_club` as c ON c.club_id = cs.cclub  where s.`show_start_date` >= ". $db->Quote($start_date) ; 
		*/
		 
		 
		
		
		$query->order("s.show_start_date ASC");
		//$query .= " ORDER BY s.show_start_date DESC";
		//echo $query;
		//die;
		 
									 
		return $query;
	}
}
