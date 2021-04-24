<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the mailtmpls package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewMailtmpls extends JViewLegacy
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
			$isNew		= ($this->item->tmpl_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('COM_TOES_MAIL_TMPL_ADD'): JText::_('COM_TOES_MAIL_TMPL_EDIT'), $isNew?'category-add':'category-edit');
			JToolBarHelper::save('mailtmpls.save');
			JToolBarHelper::cancel('mailtmpls.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('mailtmpls');

			$this->sidebar = JHtmlSidebar::render();

			JToolBarHelper::title(JText::_('COM_TOES_MAIL_TMPLS'), 'blue-transparant');
			JToolBarHelper::addNew('mailtmpls.add');
			JToolBarHelper::editList('mailtmpls.edit');
			JToolBarHelper::deleteList('', 'mailtmpls.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
