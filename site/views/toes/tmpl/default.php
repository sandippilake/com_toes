<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();

$userId		= $user->get('id');
$extension	= $this->escape($this->state->get('filter.extension'));

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<div id="cpanel">
	<fieldset>
		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=clubs">
			<div class="toes-icon toes-icon-clubs">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_CLUBS'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=breeds">
			<div class="toes-icon toes-icon-breeds">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_BREEDS'); ?></span>
			</a>
		</div>
		
		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=categories">
			<div class="toes-icon toes-icon-categories">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_CATEGORIES'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=divisions">
			<div class="toes-icon toes-icon-divisions">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_DIVISIONS'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=colors">
			<div class="toes-icon toes-icon-colors">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_COLORS'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=genders">
			<div class="toes-icon toes-icon-genders">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_GENDERS'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=cats">
			<div class="toes-icon toes-icon-cats">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_CATS'); ?></span>
			</a>
		</div>

		<div class="icon">
			<a href="index.php?option=com_toes&amp;view=judges">
			<div class="toes-icon toes-icon-cats">&nbsp;</div>
			<span><?php echo JText::_('COM_TOES_JUDGES'); ?></span>
			</a>
		</div>
		
	</fieldset>
</div>