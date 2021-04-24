<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$app = JFactory::getApplication();

$user = JFactory::getUser();
$show = TOESHelper::getShowDetails($this->entry->show_id); 

?>
<?php if($this->entry->edit) : ?>
    <h3><?php echo JText::_('EDIT_ENTRY')?></h3>
<?php else :?>
    <h3><?php echo JText::_('NEW_ENTRY')?></h3>
<?php endif; ?>

<?php if($this->entry->user_id != $user->id): ?>
    <label class="label"><?php echo JText::_("COM_TOES_SELECTED_USER")." : ";?></label>
<?php echo TOESHelper::getUserInfo($this->entry->user_id)->name;?>
<br/>
<?php endif; ?>
<label class="label"><?php echo JText::_("COM_TOES_SELECTED_CAT")." : ";?></label>
<?php echo $this->cat->cat_prefix_abbreviation.' '.$this->cat->cat_title_abbreviation.' '.$this->cat->cat_name.' '.$this->cat->cat_suffix_abbreviation;?>
<br/>
<label class="label"><?php echo JText::_("COM_TOES_SELECTED_SHOWDAYS")." : ";?></label>
<?php echo implode(', ', $this->selected_showdays);?>
<br/>
<label class="label"><?php echo JText::_("COM_TOES_ENTRY_EXH_ONLY")." : ";?></label>
<?php echo $this->entry->exh_only?JText::_('JYES'):JText::_('JNO');?>
<br/>
<label class="label"><?php echo JText::_("COM_TOES_ENTRY_FOR_SALE")." : ";?></label>
<?php echo $this->entry->for_sale?JText::_('JYES'):JText::_('JNO');?>
<br/>
<label class="label"><?php echo JText::_("COM_TOES_ENTRY_AGENT_NAME")." : ";?></label>
<?php echo $this->entry->agent_name;?>
<br/>
<label class="label"><?php echo JText::_("COM_TOES_SELECTED_CONGRESS")." : ";?></label>
<?php echo ($this->selected_congress)?implode(', ', $this->selected_congress):' - ';?>

<br/><br/>
<label class="label"><?php echo JText::_("COM_TOES_UPDATE_SUMMARY");?></label>
<br/>
<?php echo JText::_("COM_TOES_UPDATE_SUMMARY_COMMENT");?>
<br/>
<div>
    <label for="exh_only"><?php echo ( $show->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_CAGES') ) ?></label>
    <input type="text" name="summary_single_cages" value="<?php echo @$this->summary->summary_single_cages?$this->summary->summary_single_cages:0;?>" />
</div>
    
<div>
    <label><?php echo ( $show->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_CAGES') ) ?></label>
    <input type="text" name="summary_double_cages" value="<?php echo @$this->summary->summary_double_cages?$this->summary->summary_double_cages:0;?>" />
</div>

<div>
    <label for="exh_only"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_PERSONAL_CAGES') ?></label>
    <select name="summary_personal_cages">
        <?php if(!$show->show_bring_your_own_cages):?>
            <option value="0" <?php echo (!@$this->summary->summary_personal_cages)?'selected="selected"':''?> ><?php echo JText::_('JNO');?></option>
        <?php endif; ?>
        <option value="1" <?php echo (@$this->summary->summary_personal_cages == 1)?'selected="selected"':''?> ><?php echo JText::_('JYES')?></option>
    </select>
    <?php if($show->show_bring_your_own_cages):?>
        <label style="font-weight: normal;padding-left:5px;"><?php echo JText::_('COM_TOES_BRING_YOUR_OWN_CAGE_SHOW') ?></label>
    <?php endif; ?>
</div>
    
<div>
    <label for="for_sale"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_GROOMING_SPACE') ?></label>
    <select name="summary_grooming_space">
        <option value="0" <?php echo (!@$this->summary->summary_grooming_space)?'selected="selected"':''?> ><?php echo JText::_('JNO');?></option>
        <option value="1" <?php echo (@$this->summary->summary_grooming_space == 1)?'selected="selected"':''?> ><?php echo JText::_('JYES')?></option>
    </select>
</div>

<div>
    <label for="exh_only"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_BENCHING_REQUEST') ?></label>
    <textarea cols="15" rows="5" name="summary_benching_request"><?php echo @$this->summary->summary_benching_request;?></textarea>
</div>
    
<div>
    <label for="for_sale"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_REMARKS') ?></label>
    <textarea cols="15" rows="5" name="summary_remarks"><?php echo @$this->summary->summary_remarks;?></textarea>
</div>
    
<div>
    <label for="for_sale"><?php echo JText::_('COM_TOES_ACCEPT_SHOW_RULES_BUTTON_LABEL') ?></label>
    <input type="checkbox" id="accept_rule" value="1" name="accept_rule"  /> 
	<span class="hasTip icon-info-sign" title="<?php echo JText::_('COM_TOES_TICA_SHOW_RULES'); ?>"></span>
</div>

<div class="fieldbg" >
    <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
    <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
    <input type="hidden" value="<?php echo isset($this->summary->summary_user)?$this->summary->summary_user:'0';?>" name="summary_user" />
    <input type="hidden" value="<?php echo $user->id;?>" name="current_user" />
    <input type="hidden" value="<?php echo $this->entry->show_id;?>" name="show_id" />
    <input type="hidden" id="add_entry_user" name="add_entry_user" value="<?php echo $app->input->getVar('user_id'); ?>" />
    <input type="hidden" id="edit" name="edit" value="<?php echo $this->entry->edit; ?>" />
    <?php if($this->entry->edit):?>
        <input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <?php endif;?>
    <input onclick="previous_step('step5');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
    <input id="save_btn" onclick="next_step('step5');" type="button" name="button" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
</div>