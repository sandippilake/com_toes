<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the cat_cat_connection_type package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewCat_cat_connection_types extends JViewLegacy
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
			$isNew		= ($this->item->cat_cat_connection_type_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('Add Cat to Cat Connection Type'): JText::_('Edit Cat to Cat Connection Type'), '');
			JToolBarHelper::save('cat_cat_connection_types.save');
			JToolBarHelper::cancel('cat_cat_connection_types.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('cat_cat_connection_types');

			$this->sidebar = JHtmlSidebar::render();
	
			JToolBarHelper::title(JText::_('Cat with Cat Connection Types'));
			JToolBarHelper::addNew('cat_cat_connection_types.add');
			JToolBarHelper::editList('cat_cat_connection_types.edit');
			JToolBarHelper::deleteList('', 'cat_cat_connection_types.delete');
			JToolBarHelper::divider();
			
			//addtoolbar complete
		}
		
		parent::display($tpl);
	}

}
