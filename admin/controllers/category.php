<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Template style controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class ToesControllerCategory extends JControllerForm
{
	public function cancel()
	{
		parent::cancel();
		//$this->setRedirect(JRoute::_('index.php?option=com_toes&view=categories'));
	}
	//public function edit()
	//{
		//var_dump('lm khj b kjbhb');die;
	//	parent::display();
	//}

}
