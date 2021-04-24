<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Genericart
 * @author     spiderweb <sandip.pilake@gmail.com>
 * @copyright  2016 spiderweb
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

use Joomla\Utilities\ArrayHelper;
/**
 * Genericart model.
 *
 * @since  1.6
 */
class ToesModelOrganizations extends JModelList
{
	public function __construct($config = array()){
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
			'recognized_registration_organization_affiliation','recognized_registration_organization_name','recognized_registration_organization_abbreviation' 
			);
		}
		parent::__construct($config);
	}


 
	protected function populateState($ordering = null, $direction = null){
		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$jinput = $app->input;
		//var_dump($_REQUEST);
		 
		
         
        if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array')) {			
			foreach ($filters as $name => $value) {
				$this->setState('filter.' . $name, $value);
			}
		}
		//echo 'gfdg';
		//echo $app->input->get('filter_order','recognized_registration_organization_name');
		
 		$this->setState('list.ordering', $app->input->get('filter_order','recognized_registration_organization_name'));
		$this->setState('list.direction', $app->input->get('filter_order_Dir','ASC'));
		
		parent::populateState($ordering,$direction);
		
		$limit = $app->getUserStateFromRequest($this->context . '.limit', 'limit', 0, 'int');
		$start = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
		$this->setState('list.limit', $limit);
        $this->setState('list.start', $start);
		
		/*

        $ordering  = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);
        $direction = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', $ordering);

       // $start = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0, 'int');
       // $limit = $app->getUserStateFromRequest($this->context . '.limit', 'limit', 10, 'int');

        if ($limit == 0) {
            $limit = $app->get('list_limit', 0);
        }

        $this->setState('list.limit', $limit);
        $this->setState('list.start', $start);
        
        
        // Receive & set filters
      
		if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array')) {
			 
			 
			
			foreach ($filters as $name => $value) {
				$this->setState('filter.' . $name, $value);
			}
		}
		 

		$this->setState('list.ordering', $app->input->get('filter_order'));
		$this->setState('list.direction', $app->input->get('filter_order_Dir'));
		parent::populateState('recognized_registration_organization_name', 'ASC');
		*/
	}
	  
	 
	 
	protected function getListQuery()
	{
		 
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		//$query = "select * from `#__toes_recognized_registration_organization` ORDER BY recognized_registration_organization_id "; 
		$query->select('*');
		$query->from("`#__toes_recognized_registration_organization`"); 
		
		$affiliation = strtolower($this->getState('filter.affiliation'));
		if($affiliation)
		$query->where("LOWER(recognized_registration_organization_affiliation) =".$db->Quote($affiliation));
		
		
		$search = $this->getState('filter.search');
		if($search){
		$search_word = '%'.strtolower(trim($search)).'%';	
		
		$query->where("LOWER(recognized_registration_organization_name) LIKE  ".$db->Quote($search_word)." OR
		LOWER(recognized_registration_organization_abbreviation) LIKE ".$db->Quote($search_word));
			
		}
		
		
		$query->order($db->escape($this->getState('list.ordering', 'recognized_registration_organization_name')).' '.
		$db->escape($this->getState('list.direction', 'ASC')));
		
		//echo  $query;
		//die;
		return $query;
				
		 
	} 
	public function allrecords(){
		$db =  JFactory::getDBO();
		$query = $this->getListQuery();
		return $db->setQuery($query)->loadObjectList();
		
		
	}
	 
	 
	 
}
