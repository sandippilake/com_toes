<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Template style controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @since		1.6
 */
class ToesControllerToe extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_TOES';
	
	public function cancel()
	{
		parent::cancel();
		$this->setRedirect(JRoute::_('index.php?option=com_toes&view=toes', false));
	}
	
	public function save()
	{
		parent::save();
		$this->setRedirect(JRoute::_('index.php?option=com_toes&view=toes', false));
	}

}
