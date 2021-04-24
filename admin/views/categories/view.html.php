<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the Category package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewCategories extends JViewLegacy
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
			$category_org = $this->get('category_organization');
		
			$category_orglist 	= array();
			$category_orglist[] = JHTML::_('select.option', '0', JText::_( 'Select Category Organization' ) );
			$category_orglist 	= array_merge( $category_orglist, $category_org );
			$this->category_orglist = JHTML::_('select.genericlist', $category_orglist, 'category_organization', '', 'value', 'text', @$this->item->category_organization);
		
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->category_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('Add Category'): JText::_('Edit Category'), $isNew?'category-add':'category-edit');
			JToolBarHelper::save('categories.save');
			JToolBarHelper::cancel('categories.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('categories');

			$this->sidebar = JHtmlSidebar::render();

			JToolBarHelper::title(JText::_('COM_TOES_CATEGORIES'), 'blue-transparant');
			JToolBarHelper::addNew('categories.add');
			JToolBarHelper::editList('categories.edit');
			JToolBarHelper::deleteList('', 'categories.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
