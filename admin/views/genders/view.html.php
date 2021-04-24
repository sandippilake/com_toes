<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the gender package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewGenders extends JViewLegacy
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
			$isNew		= ($this->item->gender_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('Add gender'): JText::_('Edit gender'), $isNew?'article-add':'article-edit');
			JToolBarHelper::save('genders.save');
			JToolBarHelper::cancel('genders.cancel', 'JTOOLBAR_CLOSE');
			JToolBarHelper::divider();
			//add toolbar complete
			
		
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

			TOESHelper::addSubmenu('genders');

			$this->sidebar = JHtmlSidebar::render();
	
			JToolBarHelper::title(JText::_('COM_TOES_GENDERS'), 'blue-transparant');
			JToolBarHelper::addNew('genders.add');
			JToolBarHelper::editList('genders.edit');
			JToolBarHelper::deleteList('', 'genders.delete');
			JToolBarHelper::divider();
			
			//addtoolbar complete
		}
		
		parent::display($tpl);
	}

}
