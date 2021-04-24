<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the regnumberformat package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewSmtpaccounts extends JViewLegacy
{
	//protected $items;
	//protected $pagination;
	//protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$layout = $app->input->getVar('layout');
		
		if($layout == 'edit')
		{
			//die('hi');
			$this->item		= $this->get('Item');
			$this->form		= $this->get('Form');
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->smtp_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('COM_TOES_SMTP_ACCOUNT_ADD'): JText::_('COM_TOES_SMTP_ACCOUNT_EDIT'), $isNew?'category-add':'category-edit');
			JToolBarHelper::save('smtpaccounts.save');
			JToolBarHelper::cancel('smtpaccounts.cancel', 'JTOOLBAR_CLOSE');
			JToolBarHelper::divider();
		}
		else
		{	
			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');
			$this->state		= $this->get('State');
			$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');

            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }

			TOESHelper::addSubmenu('smtpaccounts');

			$this->sidebar = JHtmlSidebar::render();

			JToolBarHelper::title(JText::_('COM_TOES_SMTP_ACCOUNTS'), 'blue-transparant');
			JToolBarHelper::addNew('smtpaccounts.add');
			JToolBarHelper::editList('smtpaccounts.edit');
			JToolBarHelper::deleteList('', 'smtpaccounts.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
