<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * View to edit a template style.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
 
 
class toesViewtoe extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		//echo 'i m here';die;
		
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		//$this->canDo	= ContentHelper::getActions($this->state->get('filter.category_id'));
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
	
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$app = JFactory::getApplication();
		$app->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, 'com_toes'));
		}

		$canDo		= $result;
		
		JToolBarHelper::title(
			$isNew ? JText::_('Add Task')
			: JText::_('Edit Task'), ''
		);

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			JToolBarHelper::apply('toe.apply');
			JToolBarHelper::save('toe.save');
		}
	
	//	if (empty($this->item->id))  {
	//		JToolBarHelper::cancel('toe.cancel');
	//	} else {
	//		JToolBarHelper::cancel('toe.cancel', 'JTOOLBAR_CLOSE');
			JToolBarHelper::cancel('toe.cancel', 'JTOOLBAR_CLOSE');
	//	}
		JToolBarHelper::divider();
			
	}
}
