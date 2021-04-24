<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Template styles list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class ToesControllerToes extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 *
	 * @since	1.6
	 */
	public function getModel($name = 'toe', $prefix = 'ToesModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		//var_dump($model);die;
		
		return $model;
	}
	
	
	
}
