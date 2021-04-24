<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the breed package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewBreeds extends JViewLegacy
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
			$breed_org = $this->get('breed_organization');
			$breed_status = $this->get('breed_status');
			
		
			$breed_orglist 	= array();
			$breed_orglist[] = JHTML::_('select.option', '0', JText::_( 'Select breed Organization' ) );
			$breed_orglist 	= array_merge( $breed_orglist, $breed_org );
			$this->breed_orglist = JHTML::_('select.genericlist', $breed_orglist, 'breed_organization', '', 'value', 'text', @$this->item->breed_organization);
		
			$breed_statuslist 	= array();
			$breed_statuslist[] = JHTML::_('select.option', '0', JText::_( 'Select breed status' ) );
			$breed_statuslist 	= array_merge( $breed_statuslist, $breed_status );
			$this->breed_statuslist = JHTML::_('select.genericlist', $breed_statuslist, 'breed_status', '', 'value', 'text', @$this->item->breed_status);
		
			
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->breed_id == 0);
			$user	= JFactory::getUser();
			
			JToolBarHelper::title($isNew ? JText::_('Add breed'): JText::_('Edit breed'), $isNew?'article-add':'article-edit');
			JToolBarHelper::save('breeds.save');
			JToolBarHelper::cancel('breeds.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('breeds');

			$this->sidebar = JHtmlSidebar::render();
	

			JToolBarHelper::title(JText::_('COM_TOES_BREEDS'), 'blue-transparant');
			JToolBarHelper::addNew('breeds.add');
			JToolBarHelper::editList('breeds.edit');
			JToolBarHelper::deleteList('', 'breeds.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}

}
