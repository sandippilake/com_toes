<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 *
 * @package	Joomla.Administrator
 * @since	1.6
 */
class ToesViewClubs extends JViewLegacy {
    //protected $items;
    //protected $pagination;
    //protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
		$app = JFactory::getApplication();
        $layout = $app->input->getVar('layout');

        if ($layout == 'edit') {
            //die('hi');
            $this->item = $this->get('Item');
            $orgs = $this->get('Organizations');
            $regions = $this->get('CompetativeRegions');


            $orglist = array();
            $orglist[] = JHTML::_('select.option', '0', JText::_('COM_TOES_SELECT_ORGANIZATION'));
            $orglist = array_merge($orglist, $orgs);
            $this->orglist = JHTML::_('select.genericlist', $orglist, 'club_organization', '', 'value', 'text', @$this->item->club_organization);

            $regionlist = array();
            $regionlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_COMPETATIVE_REGION'));
            $regionlist = array_merge($regionlist, $regions);
            $this->regionlist = JHTML::_('select.genericlist', $regionlist, 'club_competitive_region', 'class="inputbox required"', 'value', 'text', @$this->item->club_competitive_region, 'club_competitive_region');


            //add toolbar
            //$app->input->set('hidemainmenu', true);

            $isNew = ($this->item->club_id == 0);

            JToolBarHelper::title($isNew ? JText::_('COM_TOES_ADD_CLUB') : JText::_('COM_TOES_EDIT_CLUB'), $isNew?'article-add':'article-edit');
            JToolBarHelper::save('clubs.save');
            JToolBarHelper::cancel('clubs.cancel', 'JTOOLBAR_CLOSE');
            JToolBarHelper::divider();
            //add toolbar complete
        } else {
            $this->items = $this->get('Items');
            $this->pagination = $this->get('Pagination');
            $this->state = $this->get('State');
			$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');

            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }

			TOESHelper::addSubmenu('clubs');

			$this->sidebar = JHtmlSidebar::render();
	
            //addtoolbar
            JToolBarHelper::title(JText::_('COM_TOES_CLUBS'), 'blue-transparant');
            JToolBarHelper::addNew('clubs.add');
            JToolBarHelper::editList('clubs.edit');
            JToolBarHelper::deleteList('', 'clubs.delete');
            JToolBarHelper::divider();
            //addtoolbar complete
        }

        parent::display($tpl);
    }
}
