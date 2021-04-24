<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the cat package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewcats extends JViewLegacy
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
			$cat_org = $this->get('cat_organization');
			$cat_category = $this->get('cat_category');
			$cat_division = $this->get('cat_division');
		
			$cat_orglist 	= array();
			$cat_orglist[] = JHTML::_('select.option', '0', JText::_( 'Select cat Organization' ) );
			$cat_orglist 	= array_merge( $cat_orglist, $cat_org );
			$this->cat_orglist = JHTML::_('select.genericlist', $cat_orglist, 'cat_organization', '', 'value', 'text', @$this->item->cat_organization);
		
			$cat_categorylist 	= array();
			$cat_categorylist[] = JHTML::_('select.option', '0', JText::_( 'Select cat Category' ) );
			$cat_categorylist 	= array_merge( $cat_categorylist, $cat_category );
			$this->cat_categorylist = JHTML::_('select.genericlist', $cat_categorylist, 'cat_category', '', 'value', 'text', @$this->item->cat_category);
		
			$cat_divisionlist 	= array();
			$cat_divisionlist[] = JHTML::_('select.option', '0', JText::_( 'Select cat Division' ) );
			$cat_divisionlist 	= array_merge( $cat_divisionlist, $cat_division );
			$this->cat_divisionlist = JHTML::_('select.genericlist', $cat_divisionlist, 'cat_division', '', 'value', 'text', @$this->item->cat_division);
		
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->cat_id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('Add cat'): JText::_('Edit cat'), $isNew?'article-add':'article-edit');
			JToolBarHelper::save('cats.save');
			JToolBarHelper::cancel('cats.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('cats');

			$this->sidebar = JHtmlSidebar::render();

			JToolBarHelper::title(JText::_('COM_TOES_CATS'), 'blue-transparant');
			JToolBarHelper::deleteList('', 'cats.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
