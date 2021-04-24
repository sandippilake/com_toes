<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * View to edit a template style.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESViewEntryclerk extends JViewLegacy {

    protected $item;

    /**
     * Display the view
     */
    public function display($tpl = null) {
		
		ini_set('memory_limit','128M');
		
        $app    = JFactory::getApplication();
        $user   = JFactory::getUser();
		$document = JFactory::getDocument();
        
        $pk = $app->input->getInt('id');
        $this->show = $this->get('ShowDetails');

		$isEntryClerk = TOESHelper::isAdmin() || TOESHelper::is_showofficial($user->id, $pk) || TOESHelper::is_clubowner($user->id, $this->show->club_id);
        
        if(!$isEntryClerk)
        {
            JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
            $app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
        }
		
		$layout = $app->input->getVar('layout');
		if($user->id == 6257)echo $layout;

		if($layout == 'default_userentries') {
			
			$model = $this->getModel();
			$user_id = $app->input->get('user_id');
			
			$users = array($user_id);
			
			$this->cnt = 0;
			$this->user = $model->getUserInformation($user_id);
			$this->entries = $model->getEntries($users,1);
			$this->placeholders = $model->getPlaceholders($users, 1);
			
			if($this->entries || $this->placeholders) {
				parent::display();
			} else {
				echo '';
			}
			$app->close();
		}
		
		$start_date = date('d', strtotime($this->show->show_start_date));
		$start_date_month = date('M', strtotime($this->show->show_start_date));
		$start_date_year = date('Y', strtotime($this->show->show_start_date));

		$end_date = date('d', strtotime($this->show->show_end_date));
		$end_date_month = date('M', strtotime($this->show->show_end_date));
		$end_date_year = date('Y', strtotime($this->show->show_end_date));

		$this->show_dates = '';
		$this->show_dates .= $start_date_month . ' ' . $start_date;

		if ($end_date_year != $start_date_year) {
			$this->show_dates .= ' ' . $start_date_year;
		}

		if ($end_date_month != $start_date_month) {
			if (date('t') != $start_date)
				$this->show_dates .= ' - ' . date('t');
			if ($end_date == '01')
				$this->show_dates .= ', ' . $end_date_month . ' ' . $end_date;
			else
				$this->show_dates .= ', ' . $end_date_month . ' 01 - ' . $end_date;
		} else {
			if ($start_date != $end_date)
				$this->show_dates .= ' - ' . $start_date_month . ' ' . $end_date;
		}

		$this->show_dates .= ' ' . $end_date_year;
		
		if($layout == 'matrix_printing' || $layout == 'reports') {

	        $this->pendingentries = $this->get('PendingEntries');
						
			$document->setTitle(JText::_('COM_TOES_SHOW_DOCUMENTATION').' - '.$this->show_dates);

			parent::display();
			return;
		} 
		
		$users = array();
		$model = $this->getModel();
		
        $this->state = $this->get('State');
		$this->entrystatuses = $this->get('Entrystatuseoptions');
		$this->entryusers = $this->get('Entryuseroptions');
		
		if($layout == 'chrono') {

			$this->entries = array();
			if ($this->state->get('filter.entry_type') != 2) {
				$this->entries = $model->getEntries($users);
			}

			$placeholders = array();
			if ($this->state->get('filter.entry_type') != 1) {
				$placeholders = $model->getPlaceholders($users);
			}

			$this->show->total_entries = array_merge($this->entries, $placeholders);
			$this->show->total_entries = TOESHelper::aasort($this->show->total_entries, 'timestamp');
        
			$document->setTitle(JText::_('COM_TOES_ENTRY_CLERK_LIST_VIEW').' - '.$this->show_dates);

		} else if($layout == 'entrychanges'){
			$this->entries = array();
			if ($this->state->get('filter.entry_type') != 2) {
				$this->entries = $model->getEntries($users);
			}
			
			$document->setTitle(JText::_('COM_TOES_CHANGES_VIEW').' - '.$this->show_dates);
			
		} else {
			 

			$this->users = $this->get('Items');
			$this->pagination = $this->get('Pagination');
			
			foreach($this->users as $user) {
				// Sandy added if condition as there was problem in one case at least
				if($user->id)
				$users[] = $user->id;
			}
			 
			$this->entries = array();
			if ($this->state->get('filter.entry_type') != 2) {
				$this->entries = $model->getEntries($users);
			}
			
			 

			$placeholders = array();
			if ($this->state->get('filter.entry_type') != 1) {
				$placeholders = $model->getPlaceholders($users);
			}
			
			$temp_entries = array();
			foreach ($this->entries as $entry) {
				$temp_entries[$entry->summary_user][] = $entry;
			}

			$temp_placeholders = array();
			foreach ($placeholders as $placeholder) {
				$temp_placeholders[$placeholder->placeholder_exhibitor][] = $placeholder;
			}

			$this->show->entries = $temp_entries;
			$this->show->placeholders = $temp_placeholders;

			// Check for errors.
			if (count($errors = $this->get('Errors'))) {
				JError::raiseError(500, implode("\n", $errors));
				return false;
			}
			
			$document->setTitle(JText::_('COM_TOES_ENTRY_CLERK_VIEW').' - '.$this->show_dates);
		}
		
        parent::display($tpl);
    }
}
