<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the venue package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_toes
 * @since		1.6
 */
class ToesViewVenues extends JViewLegacy
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
			$isNew		= (@$this->item->venue_id == 0);
			
			$document = JFactory::getDocument();
			$document->addStyleSheet(JURI::root().'components/com_toes/assets/jqueryui/jquery-ui.css');
			$document->addScript(JURI::root().'components/com_toes/assets/jqueryui/jquery-ui.min.js',"text/javascript", true);

			JToolBarHelper::title($isNew ? JText::_('Add venue'): JText::_('Edit venue'), $isNew?'article-add':'article-edit');
			JToolBarHelper::save('venues.save');
			JToolBarHelper::cancel('venues.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('venues');

			$this->sidebar = JHtmlSidebar::render();
	
			JToolBarHelper::title(JText::_('COM_TOES_VENUES'), 'blue-transparant');
			JToolBarHelper::addNew('venues.add');
			JToolBarHelper::editList('venues.edit');
			JToolBarHelper::deleteList('', 'venues.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
