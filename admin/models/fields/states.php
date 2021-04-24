<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JFormFieldStates extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'states';

	/**
	 * Method to get a list of categories that respects access controls and can be used for
	 * either category assignment or parent category assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$app = JFactory::getApplication();
		$country_id = $app->getUserState('com_toes.cities.filter.country_id');
		
		$query->select('a.id AS value, a.name AS text');
		$query->from('#__toes_states_per_country AS a');
		$query->order('a.name ASC');
		if($country_id) {
			$query->where('a.country_id = '.$country_id);
		}

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}
	
		// Get the current user object.
		$user = JFactory::getUser();
		
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TOES_SELECT_STATE')));
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
		
	}
}