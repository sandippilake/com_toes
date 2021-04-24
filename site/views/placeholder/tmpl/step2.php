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
$show = TOESHelper::getShowDetails($this->placeholder->placeholder_show); 

?>
<?php if($this->placeholder->edit) : ?>
    <h3><?php echo JText::_('EDIT_PLACEHOLDER')?></h3>
<?php else :?>
    <h3><?php echo JText::_('NEW_PLACEHOLDER')?></h3>
<?php endif; ?>

<?php if($this->placeholder->placeholder_exhibitor != $user->id): ?>
    <label class="label"><?php echo JText::_("COM_TOES_SELECTED_USER")." : ";?></label>
<?php echo TOESHelper::getUserInfo($this->placeholder->placeholder_exhibitor)->name;?>
<br/>
<?php endif; ?>
<label class="label"><?php echo JText::_("COM_TOES_SELECTED_SHOWDAYS")." : ";?></label>
<?php echo implode(', ', $this->selected_showdays);?>
<br/>

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
    <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
    <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
    <input type="hidden" value="<?php echo $this->placeholder->edit;?>" id="edit" name="edit" />
    <input type="hidden" value="<?php echo $this->placeholder->placeholder_show;?>" id="show_id" name="show_id" />
    <input type="hidden" value="<?php echo $this->placeholder->placeholder_exhibitor;?>" id="user_id" name="user_id" />
    <input type="hidden" value="<?php echo @$this->placeholder->placeholder_id;?>" id="placeholder_id" name="placeholder_id" />
    <?php if($app->input->getVar('type') == 'third_party'): ?>
        <input onclick="placeholder_previous_step('step2');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
    <?php else: ?>
        <input onclick="cancel_edit_placeholder();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <?php endif; ?>
    <input id="save_btn" onclick="placeholder_next_step('step2');" type="button" name="button" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
</div>