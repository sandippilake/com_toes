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
 
 
class ToesViewCategory extends JViewLegacy
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
		
		$category_org = $this->get('category_organization');
				
		$category_orglist 	= array();
		$category_orglist[] = JHTML::_('select.option', '0', JText::_( 'Select Category Organization' ) );
		$category_orglist 	= array_merge( $category_orglist, $category_org );
		
		$this->category_orglist = JHTML::_('select.genericlist', $category_orglist, 'category_organization', '', 'value', 'text', $this->item->category_organization);
			
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');

		// Check for errors.
	/*	if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
	*/
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
		$app->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->category_id == 0);
		
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
			$isNew ? JText::_('Add Category')
			: JText::_('Edit Category'), ''
		);

		// If not checked out, can save the item.
		if ($canDo->get('core.edit')) {
			JToolBarHelper::apply('category.apply');
			JToolBarHelper::save('category.save');
		}
		
		JToolBarHelper::cancel('category.cancel', 'JTOOLBAR_CLOSE');
		JToolBarHelper::divider();
		
	}
}
