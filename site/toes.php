<?php

/**
 * @version		$Id: toes.php 15 2009-11-02 18:37:15Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if(!defined('DS'))define('DS',DIRECTORY_SEPARATOR);

define('TOES_MEDIA_PATH', JPATH_ROOT.DS.'media'.DS.'com_toes');
define('TOES_LOG_PATH', JPATH_ROOT.DS.'media'.DS.'com_toes'.DS.'logs');
define('TOES_PDF_PATH', JPATH_ROOT.DS.'media'.DS.'com_toes'.DS.'PDF');

$app = JFactory::getApplication();

// load toes helper 
JLoader::register('TOESHelper', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/toes.php');
JLoader::register('TOESImageHelper', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/image.php');
JLoader::register('TOESQueryHelper', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/query.php');
JLoader::register('TOESMailHelper', JPATH_COMPONENT_ADMINISTRATOR. '/helpers/mail.php');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('TOES');

// Perform the Request task
$controller->execute($app->input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
