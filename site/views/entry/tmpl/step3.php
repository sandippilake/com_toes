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

JHtml::_('behavior.tooltip');

?>
<?php if($this->entry->edit) : ?>
    <h3><?php echo JText::_('EDIT_ENTRY')?></h3>
<?php else :?>
    <h3><?php echo JText::_('NEW_ENTRY')?></h3>
<?php endif; ?>

<?php $user = JFactory::getUser(); ?>
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

<br/><br/>
<label class="label"><?php echo JText::_("COM_TOES_SELECT_OPTIONS");?></label>
<div>
    <?php /*
    <input type="checkbox" id="exh_only" name="exh_only" value="1" <?php echo ( $this->exhibitionOnly || ( isset($this->entry->exh_only) && ($this->entry->exh_only == 1) ) )?'checked="checked"':'';?> <?php echo $this->exhibitionOnly?'disabled=""':''; ?> />
    <label for="exh_only"><?php echo JText::_('COM_TOES_ENTRY_EXH_ONLY') ?></label>
    */ ?>
    <label for="exh_only">&nbsp;</label>
    <select id="exh_only" name="exh_only" <?php echo $this->exhibitionOnly?'disabled=""':''; ?>>
        <option value="0" <?php echo ( !$this->exhibitionOnly || ( isset($this->entry->exh_only) && ($this->entry->exh_only == 0) ) )?'selected="selected"':''?> ><?php echo JText::_('COM_TOES_ENTRY_PARTICIPATING_IN_RINGS');?></option>
        <option value="1" <?php echo (  $this->exhibitionOnly || ( isset($this->entry->exh_only) && ($this->entry->exh_only == 1) ) )?'selected="selected"':'';?> ><?php echo JText::_('COM_TOES_ENTRY_EXH_ONLY')?></option>
    </select>
</div>

<?php 
if($this->is_adult_hhp_not_altered){
$user = JFactory::getUser();
$show_id = $this->show->show_id;
if(TOESHelper::is_entryclerk($user->id,$show_id) || TOESHelper::isAdmin() ){?>
	<input type="checkbox"  id="entryclerk_enable"><?php echo JText::_('COM_TOES_ALLOW_ENTRY_CLERK_TO_ENTER_A_WHOLE_HHP');?>
<?php }} ?>
    
<div>
    <label for="for_sale"><?php echo JText::_('COM_TOES_ENTRY_FOR_SALE') ?></label>
    <input type="checkbox" id="for_sale" name="for_sale" value="1" <?php echo ( isset($this->entry->for_sale) && ($this->entry->for_sale == 1) )?'checked="checked"':'';?> />
</div>

<div>
    <label for="agent_name"><?php echo JText::_('COM_TOES_ENTRY_AGENT_NAME') ?></label>
    <input type="text" id="agent_name" name="agent_name" value="<?php echo isset($this->entry->agent_name)?$this->entry->agent_name:'';?>" />
    <span class="hasTip icon-info-sign" title="<?php echo JText::_('COM_TOES_ENTRY_AGENT_NAME_HELP'); ?>"></span>
</div>

<div class="fieldbg" >
    <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
    <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
    <input type="hidden" value="<?php echo $this->entry->show_id;?>" name="show_id" />
    <input type="hidden" id="add_entry_user" name="add_entry_user" value="<?php echo $app->input->getVar('user_id'); ?>" />
    <input type="hidden" id="edit" name="edit" value="<?php echo $this->entry->edit; ?>" />
    <?php if($this->entry->edit):?>
        <input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <?php endif;?>
    <?php if(!$this->entry->edit || ($this->entry->edit && TOESHelper::getShowDetails($this->entry->show_id)->show_format != 'Continuous')):?>
        <input onclick="previous_step('step3');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
    <?php endif;?>
    <input onclick="next_step('step3');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
</div>
<script>
	jQuery('#entryclerk_enable').on('click',function(){
		if(jQuery(this).attr('checked'))
		jQuery('select#exh_only').attr('disabled',false);
		else
		jQuery('select#exh_only').attr('disabled',true);		
	})

</script>
