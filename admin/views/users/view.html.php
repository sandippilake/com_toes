<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the user package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class ToesViewUsers extends JViewLegacy
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
			
			if(!$this->item->roll_id)
			$this->item->roll_id = 0;
			
			$this->user_rolllist = $this->getuser_roll($this->item->title,@$this->item->roll_id);
				
			//add toolbar
			//$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->id == 0);
			$user	= JFactory::getUser();
					
			JToolBarHelper::title($isNew ? JText::_('Add User'): JText::_('Edit User'), '');
			JToolBarHelper::save('users.save');
			JToolBarHelper::cancel('users.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('users');

			$this->sidebar = JHtmlSidebar::render();
	
			JToolBarHelper::title(JText::_('Users'));
			//JToolBarHelper::addNew('users.add');
			JToolBarHelper::editList('users.edit');
			//JToolBarHelper::deleteList('', 'users.delete');
			JToolBarHelper::divider();
			
			//addtoolbar complete
		}
		
		parent::display($tpl);
	}
	
	public function getuser_roll($user_group = null,$roll_id)
	{
		if(!$roll_id)
		$roll_id = 0;
		
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		switch ($user_group)
		{
			case 'Organization Officials':
				$query = "SELECT organization_official_type_id as value, organization_official_type as text FROM #__toes_organization_official_type";
				break;
			case 'Club Officials':
				$query = "SELECT club_official_type_id as value, club_official_type as text FROM #__toes_club_official_type";
				break;
			case 'Show Officials':
				$query = "SELECT show_official_type_id as value, show_official_type as text FROM #__toes_show_official_type";
				break;
		}
		
		//echo $query;die;
		$db->setQuery($query);
		$user_rolls = $db->loadObjectList();
		
		$user_rolllist 	= array();
		$user_rolllist[] = JHTML::_('select.option', '0', JText::_( 'Select Roll' ) );
		$user_rolllist 	= array_merge( $user_rolllist, $user_rolls );
		return JHTML::_('select.genericlist', $user_rolllist, 'official_type_id', '', 'value', 'text', $roll_id);
	}

}
