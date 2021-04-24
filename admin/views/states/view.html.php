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
class ToesViewStates extends JViewLegacy
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
		$layout = $app->input->get('layout');
		
		if($layout == 'edit')
		{
			//die('hi');
			$this->item		= $this->get('Item');
			$this->form		= $this->get('Form');
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->id == 0);
			$user	= JFactory::getUser();
			
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'components/com_toes/assets/jqueryui/jquery-ui.css');
			$document->addScript(JURI::root().'components/com_toes/assets/jqueryui/jquery-ui.min.js',"text/javascript", true);
					
			JToolBarHelper::title($isNew ? JText::_('COM_TOES_STATE_ADD'): JText::_('COM_TOES_STATE_EDIT'), $isNew?'category-add':'category-edit');
			JToolBarHelper::save('states.save');
			JToolBarHelper::cancel('states.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('states');

			$this->sidebar = JHtmlSidebar::render();

			JToolBarHelper::title(JText::_('COM_TOES_STATES'), 'blue-transparant');
			JToolBarHelper::addNew('states.add');
			JToolBarHelper::editList('states.edit');
			JToolBarHelper::deleteList('', 'states.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
