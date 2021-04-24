<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the color package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewColors extends JViewLegacy
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
			$color_org = $this->get('color_organization');
			$color_category = $this->get('color_category');
			$color_division = $this->get('color_division');
		
			$color_orglist 	= array();
			$color_orglist[] = JHTML::_('select.option', '0', JText::_( 'Select color Organization' ) );
			$color_orglist 	= array_merge( $color_orglist, $color_org );
			$this->color_orglist = JHTML::_('select.genericlist', $color_orglist, 'color_organization', '', 'value', 'text', @$this->item->color_organization);
		
			$color_categorylist 	= array();
			$color_categorylist[] = JHTML::_('select.option', '0', JText::_( 'Select color Category' ) );
			$color_categorylist 	= array_merge( $color_categorylist, $color_category );
			$this->color_categorylist = JHTML::_('select.genericlist', $color_categorylist, 'color_category', '', 'value', 'text', @$this->item->color_category);
		
			$color_divisionlist 	= array();
			$color_divisionlist[] = JHTML::_('select.option', '0', JText::_( 'Select color Division' ) );
			$color_divisionlist 	= array_merge( $color_divisionlist, $color_division );
			$this->color_divisionlist = JHTML::_('select.genericlist', $color_divisionlist, 'color_division', '', 'value', 'text', @$this->item->color_division);
		
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->color_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('Add color'): JText::_('Edit color'), $isNew?'article-add':'article-edit');
			JToolBarHelper::save('colors.save');
			JToolBarHelper::cancel('colors.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('colors');

			$this->sidebar = JHtmlSidebar::render();
	
			JToolBarHelper::title(JText::_('COM_TOES_COLORS'), 'blue-transparant');
			JToolBarHelper::addNew('colors.add');
			JToolBarHelper::editList('colors.edit');
			JToolBarHelper::deleteList('', 'colors.delete');
			JToolBarHelper::divider();
			
			//addtoolbar complete
		}
		
		parent::display($tpl);
	}

}
