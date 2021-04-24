<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();

// Access check.
if (!JFactory::getUser()->authorise('core.manage', $app->input->getCmd('extension'))) {
    return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// load toes helper 
JLoader::register('TOESHelper', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/toes.php');

// Execute the task.
$controller = JControllerLegacy::getInstance('Toes');
$controller->execute($app->input->getVar('task'));
$controller->redirect();
