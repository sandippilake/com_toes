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
?>
<?php if($this->entry->edit) : ?>
    <h3><?php echo JText::_('EDIT_ENTRY')?></h3>
<?php else :?>
    <h3><?php echo JText::_('NEW_ENTRY')?></h3>
<?php endif; ?>

<?php if(!@$this->entry->participated_in_congress && $this->automatic_congress) : ?>
<div id="participate_in_congress_div">
    <label class="label"><?php echo JText::_("COM_TOES_PARTICIPATE_IN_CONGRESS");?></label>
    <div>
        <input type="button" value="<?php echo JText::_('JYES');?>" onclick="participate_in_congress(1);" />
        &nbsp;
        <input type="button" value="<?php echo JText::_('JNO');?>" onclick="participate_in_congress(0);" />
        <br/><br/>
        <input onclick="previous_step('step4');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
    </div>
</div>
<?php endif; ?>
    
<div id="select_congress_div" style="<?php echo (!@$this->entry->participated_in_congress && $this->automatic_congress)?'display:none':''; ?>">
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
    <br/>
    <label class="label"><?php echo JText::_("COM_TOES_ENTRY_EXH_ONLY")." : ";?></label>
    <?php echo $this->entry->exh_only?JText::_('JYES'):JText::_('JNO');?>
    <br/>
    <label class="label"><?php echo JText::_("COM_TOES_ENTRY_FOR_SALE")." : ";?></label>
    <?php echo $this->entry->for_sale?JText::_('JYES'):JText::_('JNO');?>
    <br/>
    <label class="label"><?php echo JText::_("COM_TOES_ENTRY_AGENT_NAME")." : ";?></label>
    <?php echo $this->entry->agent_name;?>

    <br/><br/>
    <?php echo JText::_("COM_TOES_SELECT_MANUAL_CONGRESS");?>
    <div>
        <?php if($this->congress): ?>
            <?php foreach($this->congress as $congress):?>
                <input type="checkbox" id="congress_<?php echo $congress->ring_id;?>" value="<?php echo $congress->ring_id;?>" name="congress[]" <?php echo ( isset($this->entry->congress) && (in_array($congress->ring_id, explode(',',$this->entry->congress))) )?'checked="checked"':'';?> /> 
                &nbsp;

                <label for="congress_<?php echo $congress->ring_id;?>"><?php echo $congress->ring_name; ?></label>
                <br/>
            <?php endforeach;?>
        <?php endif;?>
        <?php if($this->automatic_congress): ?>
            <?php foreach($this->automatic_congress as $congress):?>
                <input style="display: none;" type="checkbox" id="congress_<?php echo $congress;?>" value="<?php echo $congress;?>" name="congress[]" checked="checked" /> 
            <?php endforeach;?>
        <?php endif;?>
    </div>

    <div class="fieldbg" >
        <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
        <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
        <input type="hidden" name="congress_count" id="congress_count" value="<?php echo count($this->congress) ?>" />
        <input type="hidden" value="<?php echo $this->entry->show_id;?>" name="show_id" />
        <input type="hidden" id="add_entry_user" name="add_entry_user" value="<?php echo $app->input->getVar('user_id'); ?>" />
        <input type="hidden" id="edit" name="edit" value="<?php echo $this->entry->edit; ?>" />
        <?php if($this->entry->edit):?>
            <input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
        <?php endif;?>
        <input onclick="previous_step('step4');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
        <input onclick="next_step('step4');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
    </div>
</div>