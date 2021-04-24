<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class ToesViewOrganization  extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;

	public function display($tpl = null){
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		if(!$user->authorise('core.admin') || !$user->authorise('core.admin') )
		$app->redirect('index.php',JText::_('NOT_AUTH'));
		$layout = $this->getLayout();
		
		 
		 
		
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');
		 
		
		
		$this->prices = $this->get('prices');
		
		 
		// Check for errors.
		if (count($errors = $this->get('Errors')))		{
			throw new Exception(implode("\n", $errors));
		}

		//$this->addToolbar();
		parent::display($tpl);
	}

	 
}
