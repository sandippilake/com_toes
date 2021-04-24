<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Judges view class for the TOES package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_toes
 * @since		1.6
 */
class ToesViewJudges extends JViewLegacy
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
			$judge_org = $this->get('judge_organization');
			$judge_status = $this->get('judge_status');
			$judge_level = $this->get('judge_level');
			$judge_user = $this->get('judge_user');
				
		
			$judge_orglist 	= array();
			$judge_orglist[] = JHTML::_('select.option', '0', JText::_( 'Select Organization' ) );
			$judge_orglist 	= array_merge( $judge_orglist, $judge_org );
			$this->judge_orglist = JHTML::_('select.genericlist', $judge_orglist, 'judge_organization', '', 'value', 'text', @$this->item->judge_organization);
		
			$judge_statuslist 	= array();
			$judge_statuslist[] = JHTML::_('select.option', '0', JText::_( 'Select status' ) );
			$judge_statuslist 	= array_merge( $judge_statuslist, $judge_status );
			$this->judge_statuslist = JHTML::_('select.genericlist', $judge_statuslist, 'judge_status', '', 'value', 'text', @$this->item->judge_status);
		
			$judge_levellist 	= array();
			$judge_levellist[] = JHTML::_('select.option', '0', JText::_( 'Select level' ) );
			$judge_levellist 	= array_merge( $judge_levellist, $judge_level );
			$this->judge_levellist = JHTML::_('select.genericlist', $judge_levellist, 'judge_level', '', 'value', 'text', @$this->item->judge_level);

			$judge_userlist 	= array();
			$judge_userlist[] = JHTML::_('select.option', '0', JText::_( 'Select user' ) );
			$judge_userlist 	= array_merge( $judge_userlist, $judge_user );
			$this->judge_userlist = JHTML::_('select.genericlist', $judge_userlist, 'user', '', 'value', 'text', @$this->item->user);
				
			//add toolbar
			$app->input->set('hidemainmenu', true);

			$user		= JFactory::getUser();
			$isNew		= ($this->item->judge_id == 0);
			
			JToolBarHelper::title($isNew ? JText::_('Add judge'): JText::_('Edit judge'), $isNew?'article-add':'article-edit');
			JToolBarHelper::save('judges.save');
			JToolBarHelper::cancel('judges.cancel', 'JTOOLBAR_CLOSE');
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

			TOESHelper::addSubmenu('judges');

			$this->sidebar = JHtmlSidebar::render();
	
			JToolBarHelper::title(JText::_('COM_TOES_JUDGES'), 'blue-transparant');
			JToolBarHelper::addNew('judges.add');
			JToolBarHelper::editList('judges.edit');
			JToolBarHelper::deleteList('', 'judges.delete');
			JToolBarHelper::divider();
		}
		
		parent::display($tpl);
	}
}
