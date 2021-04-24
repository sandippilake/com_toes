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
class ToesViewRegnumberformats extends JViewLegacy
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
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->rnf_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('COM_TOES_REGISTRATION_NUMBER_FORMAT_ADD'): JText::_('COM_TOES_REGISTRATION_NUMBER_FORMAT_EDIT'), $isNew?'category-add':'category-edit');
			JToolBarHelper::save('regnumberformats.save');
			JToolBarHelper::cancel('regnumberformats.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('regnumberformats');

			$this->sidebar = JHtmlSidebar::render();

			JToolBarHelper::title(JText::_('COM_TOES_REGISTRATION_NUMBER_FORMATS'), 'blue-transparant');
			JToolBarHelper::addNew('regnumberformats.add');
			JToolBarHelper::editList('regnumberformats.edit');
			JToolBarHelper::deleteList('', 'regnumberformats.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
