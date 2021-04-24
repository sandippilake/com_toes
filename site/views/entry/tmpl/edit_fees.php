<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
?>
<h3><?php echo JText::_("COM_TOES_UPDATE_FEES");?></h3>

<div>
    <label for="summary_total_fees"><?php echo JText::_('COM_TOES_TOTAL_FEES') ?></label>
    <input type="text" name="summary_total_fees" value="<?php echo $this->summary->summary_total_fees;?>" />
</div>
    
<div>
    <label for="summary_fees_paid"><?php echo JText::_('COM_TOES_FEES_PAID') ?></label>
    <input type="text" name="summary_fees_paid" value="<?php echo $this->summary->summary_fees_paid;?>" />
</div>

<div class="fieldbg">
    <input type="hidden" value="<?php echo $this->summary->summary_id;?>" name="summary_id" />
    <input onclick="save_fees();" type="button" name="button" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
    <input onclick="cancel_edit_fees();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
</div>