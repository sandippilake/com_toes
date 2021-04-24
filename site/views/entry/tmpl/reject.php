<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$user = JFactory::getUser();

?>
<div class="fieldbg" >
	<textarea rows="5" name="entry_refusal_reason_reason"><?php echo $this->reason;?></textarea>
</div>

<div class="fieldbg" >
	<input type="hidden" value="<?php echo $this->entry_id;?>" name="reject_entry_id" id="reject_entry_id" />
	<input onclick="save_reject_reason();" type="button" class="button button-4" name="button" value="<?php echo JText::_('COM_TOES_ENTRY_REFUSAL_CONFIRM'); ?>" />
	<?php /* <input onclick="window.parent.SqueezeBox.close();" type="button" name="button" value="<?php echo JText::_('COM_TOES_ENTRY_REFUSAL_CANCEL'); ?>" /> */ ?>
</div>
