<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the division package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewDivisions extends JViewLegacy
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
			$division_org = $this->get('division_organization');
		
			$division_orglist 	= array();
			$division_orglist[] = JHTML::_('select.option', '0', JText::_( 'Select division Organization' ) );
			$division_orglist 	= array_merge( $division_orglist, $division_org );
			$this->division_orglist = JHTML::_('select.genericlist', $division_orglist, 'division_organization', '', 'value', 'text', @$this->item->division_organization);
		
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->division_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('Add division'): JText::_('Edit division'), $isNew?'article-add':'article-edit');
			JToolBarHelper::save('Divisions.save');
			JToolBarHelper::cancel('Divisions.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('divisions');

			$this->sidebar = JHtmlSidebar::render();
	
			JToolBarHelper::title(JText::_('COM_TOES_DIVISIONS'), 'blue-transparant');
			JToolBarHelper::addNew('divisions.add');
			JToolBarHelper::editList('divisions.edit');
			JToolBarHelper::deleteList('', 'divisions.delete');
			JToolBarHelper::divider();
			
			//addtoolbar complete
		}
		
		parent::display($tpl);
	}

}
