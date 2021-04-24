<?php


defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class ToesViewOrganizations  extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
	 
		$db    = JFactory::getDbo();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		if(!$user->authorise('core.admin') || !$user->authorise('core.admin') )
		$app->redirect('index.php',JText::_('NOT_AUTH'));
		
		 
		
		$this->state      = $this->get('State');
		$this->items = $this->get('Items'); 
		 
		$this->pagination = $this->get('Pagination');
		 
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}
		$this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        
        $this->sortDirection = $this->state->get('list.direction');
		$this->sortColumn = $this->state->get('list.ordering');

		//$this->_prepareDocument();
		parent::display($tpl);
	}

	 
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
