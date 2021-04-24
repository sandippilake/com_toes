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
class ToesViewToes extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		
			$this->items		= $this->get('Items');
			$this->pagination	= $this->get('Pagination');
			$this->state		= $this->get('State');
			
			//var_dump($this->items);die;
			
			// Check for errors.
			if (count($errors = $this->get('Errors'))) {
				JError::raiseError(500, implode("\n", $errors));
				return false;
			}

				// Check if there are no matching items
			if(!count($this->items)) {
				JFactory::getApplication()->enqueueMessage(
					JText::_('No Tasks')
					, 'warning'
				);
			}
			$this->addToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$state	= $this->get('State');
		
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, 'com_toes'));
		}
		
		$canDo	= $result;
		if(!class_exists('JToolbarHelper')) {
			require_once JPATH_ADMINISTRATOR . '/includes/toolbar.php';
		}
		JToolBarHelper::title(JText::_('COM_TOES'), 'blue-transparant');
	
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_toes');
			JToolBarHelper::divider();
		}
	}
}
