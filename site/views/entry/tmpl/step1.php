<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$exibitor = TOESHelper::getUserInfo($this->entry->user_id);
$app = JFactory::getApplication();

?>
<h3><?php echo JText::_('NEW_ENTRY')?></h3>

<?php $user = JFactory::getUser(); ?>
<?php if($this->entry->user_id != $user->id): ?>
    <label class="label"><?php echo JText::_("COM_TOES_SELECTED_USER")." : ";?></label>
    <?php echo $exibitor->name;?>
    <br/>
<?php endif; ?>

<label class="label"><?php echo JText::_("COM_TOES_SELECT_CAT");?></label>
<div>
    <?php if($this->totalcats): ?>
        <?php if($this->cats): ?>
            <?php foreach($this->cats as $cat):?>
                <input type="radio" value="<?php echo $cat->cat_id;?>" id="cat_<?php echo $cat->cat_id;?>" name="cat_id" <?php echo ((isset($this->entry->cat_id)) && ($this->entry->cat_id == $cat->cat_id))?'checked="checked"':'';?> /> 
                &nbsp;
                <label for="cat_<?php echo $cat->cat_id;?>" style="font-weight: normal;" >
                    <?php echo $cat->cat_prefix_abbreviation.' '.$cat->cat_title_abbreviation.' '.$cat->cat_name.' '.$cat->cat_suffix_abbreviation;?>
                </label>
                <?php 
                // sandy hack to show warning
                //var_dump($cat);
                if($cat->cat_breed == '24'){
					$showdate = new DateTime($cat->show_start_date, new DateTimeZone('UTC'));
					$cat_dob = new DateTime($cat->cat_date_of_birth, new DateTimeZone('UTC'));
					$interval = $showdate->diff($cat_dob);
					 
					$is_adult = false;
					$age_years = intval($interval->format('%y'));
					$age_months = intval($interval->format('%m'));
					if($age_years > 0) {
						$is_adult = true;
					} else {
						if($age_months >= 8) {
							$is_adult = true;
						}  
					}
					if($is_adult){
						
						if((int)$cat->cat_gender < 3){  
							echo JText::_('COM_TOES_HHP_MUST_BE_ALTERED');
						}
					}
					
				}     
                // hack end
                ?>
                <br/>
            <?php endforeach;?>
        <?php else:?>
            <?php echo JText::_('COM_TOES_ALL_CATS_ARE_ENTERED_IN_SHOW');?>
        <?php endif;?>
    <?php endif;?>
</div>

<br/>	
<a href="<?php echo JRoute::_('index.php?option=com_toes&view=cat&layout=edit&group=owner&username='.$exibitor->username.'&show_id='.$this->entry->show_id);?>"><?php echo JText::_('COM_TOES_ADD_NEW_CAT');?></a>
<br/>	

<div class="fieldbg" >
    <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
    <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
    <input type="hidden" value="<?php echo $this->entry->show_id; ?>" name="show_id" />
    <input type="hidden" id="add_entry_user" name="add_entry_user" value="<?php echo $this->entry->user_id; ?>" />
    <?php if($app->input->getVar('type') == 'third_party'): ?>
        <input onclick="previous_step('step1');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
    <?php else: ?>
        <input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <?php endif;?>
    <?php if($this->cats): ?>
        <input onclick="next_step('step1');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
    <?php endif;?>
</div>
