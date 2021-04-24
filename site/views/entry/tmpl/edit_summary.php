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
$show = TOESHelper::getShowDetails($this->summary->summary_show); 

?>
<h3><?php echo JText::_("COM_TOES_UPDATE_SUMMARY");?></h3>
<br/>
<?php echo JText::_("COM_TOES_UPDATE_SUMMARY_COMMENT");?>
<br/>
<div>
    <label for="exh_only"><?php echo ( $show->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_CAGES') ) ?></label>
    <input type="text" name="summary_single_cages" value="<?php echo $this->summary->summary_single_cages;?>" />
</div>
    
<div>
    <label><?php echo ( $show->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_CAGES') ) ?></label>
    <input type="text" name="summary_double_cages" value="<?php echo $this->summary->summary_double_cages;?>" />
</div>

<div>
    <label for="exh_only"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_PERSONAL_CAGES') ?></label>
    <select name="summary_personal_cages">
        <option value="0" <?php echo (!$this->summary->summary_personal_cages)?'selected="selected"':''?> ><?php echo JText::_('JNO');?></option>
        <option value="1" <?php echo ($this->summary->summary_personal_cages == 1)?'selected="selected"':''?> ><?php echo JText::_('JYES')?></option>
    </select>
</div>
    
<div>
    <label for="for_sale"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_GROOMING_SPACE') ?></label>
    <select name="summary_grooming_space">
        <option value="0" <?php echo (!$this->summary->summary_grooming_space)?'selected="selected"':''?> ><?php echo JText::_('JNO');?></option>
        <option value="1" <?php echo ($this->summary->summary_grooming_space == 1)?'selected="selected"':''?> ><?php echo JText::_('JYES')?></option>
    </select>
</div>

<div>
    <label for="exh_only"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_BENCHING_REQUEST') ?></label>
    <textarea cols="25" rows="5" name="summary_benching_request"><?php echo $this->summary->summary_benching_request;?></textarea>
</div>
    
<div>
    <label for="for_sale"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_REMARKS') ?></label>
    <textarea cols="25" rows="5" name="summary_remarks"><?php echo $this->summary->summary_remarks;?></textarea>
</div>
    
<div class="fieldbg" >
    <input type="hidden" value="<?php echo $this->summary->summary_id;?>" name="summary_id" />
    <input type="hidden" value="<?php echo $this->summary->summary_show;?>" name="show_id" />
    <input type="hidden" value="<?php echo $this->summary->summary_user;?>" name="summary_user" />
    <input type="hidden" value="<?php echo $user->id;?>" name="current_user" />
    <input onclick="save_summary();" type="button" name="button" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
    <input onclick="cancel_edit_summary();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
</div>