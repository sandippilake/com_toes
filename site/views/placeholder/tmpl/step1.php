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

$show = TOESHelper::getShowDetails($this->placeholder->placeholder_show);
$isAlternative = TOESHelper::isAlternative($this->placeholder->placeholder_show);

?>
<?php if($this->placeholder->edit) : ?>
    <h3><?php echo JText::_('EDIT_PLACEHOLDER')?></h3>
<?php else :?>
    <h3><?php echo JText::_('NEW_PLACEHOLDER')?></h3>
<?php endif; ?>
    
<?php if(@$this->placeholder->type == 'third_party'): ?>
    <label class="label"><?php echo JText::_("COM_TOES_SELECTED_USER")." : ";?></label>
    <?php echo TOESHelper::getUserInfo($this->placeholder->placeholder_exhibitor)->name;?>
    <br/>
<?php endif; ?>

<label class="label"><?php echo JText::_("COM_TOES_SELECT_SHOW_DAYS");?></label>
<div>
    <?php if($this->showdays): ?>
        <?php foreach($this->showdays as $showday):
				$disabled = '';

				$am_available = false;
				$pm_available = false;

				if($isAlternative)
				{
					$am_available = TOESHelper::getAvailableSpaceforDay($showday->show_day_id,'1');
					$pm_available = TOESHelper::getAvailableSpaceforDay($showday->show_day_id,'2');
				}
				else
				{
					$am_available = true;
					$pm_available = true;
				}

				if($isAlternative)
				{
					if($am_available || $pm_available)
						$warning = '';
					else
					{
						$disabled = 'disabled="disabled"';
						$warning = JText::_('COM_TOES_ALTERNATIVE_SHOWDAY_DONT_HAVE_ENOUGH_SPACE');
					}
				}
				else
				{
					if(TOESHelper::getAvailableSpaceforDay($showday->show_day_id))
						$warning = '';
					else
					{
						if(!$show->show_use_waiting_list)
							$disabled = 'disabled="disabled"';
						$warning = JText::_('COM_TOES_SHOWDAY_DONT_HAVE_ENOUGH_SPACE');
					}
				}
            ?>
            <input onclick="check_AM_PM_for_placeholder(this,<?php echo $showday->show_day_id;?>);" type="checkbox" id="show_day_<?php echo $showday->show_day_id;?>" value="<?php echo $showday->show_day_id;?>" name="showday[]" <?php echo ( isset($this->placeholder->showdays) && in_array($showday->show_day_id, explode(',',$this->placeholder->showdays)) )?'checked="checked"':'';?> />
            &nbsp;
            
            <label style="box-shadow: none; padding: 5px 0;background:none;" for="show_day_<?php echo $showday->show_day_id;?>"><?php echo date('l',  strtotime($showday->show_day_date)); ?></label>
            &nbsp;<?php echo $warning;?>
			<span style="padding-left:30px;<?php echo $isAlternative?'display:block':'display:none';?>" >
				<?php
					$am_rings = TOESHelper::getShowdayRings($showday->show_day_id,'1');
					if($am_rings)
					{
						$am_available = TOESHelper::getAvailableSpaceforDay($showday->show_day_id,'1');
						$disabled = '';
						$checked = ( isset($this->placeholder->placeholder_for_AM) && in_array($showday->show_day_id, explode(',',$this->placeholder->placeholder_for_AM)) )?'checked="checked"':'';
						if(!$am_available)
						{
							$checked = "";
							$disabled = 'disabled="disabled"';
							$warning = JText::_('COM_TOES_ALTERNATIVE_SHOW_SESSION_DONT_HAVE_ENOUGH_SPACE');
						}
						else
						{
							$disabled = '';
							$warning = '';
						}
						$style = "";						
					}
					else
					{
						$checked = "";
						$disabled = 'disabled="disabled"';
						$style = "style='display:none;'";
					}
				?>
				<span <?php echo $style;?> >
					<input onclick="check_show_day_for_placeholder(this,'PM',<?php echo $showday->show_day_id;?>);" type="checkbox" id="placeholder_participates_AM_<?php echo $showday->show_day_id;?>" value="<?php echo $showday->show_day_id;?>" name="placeholder_participates_AM[]" <?php echo $checked; ?> <?php echo $disabled;?> /> 
					<label for="placeholder_participates_AM_<?php echo $showday->show_day_id;?>">AM</label>
					&nbsp;<?php echo $warning;?>
				</span>
				<?php if($am_rings):?>
					<br/>
				<?php endif;?>
				<?php
					$pm_rings = TOESHelper::getShowdayRings($showday->show_day_id,'2');
					if($pm_rings)
					{
						$pm_available = TOESHelper::getAvailableSpaceforDay($showday->show_day_id,'2');
						$disabled = '';
						$checked = ( isset($this->placeholder->placeholder_for_PM) && in_array($showday->show_day_id, explode(',',$this->placeholder->placeholder_for_PM)) )?'checked="checked"':'';
						if(!$pm_available)
						{
							$checked = "";
							$disabled = 'disabled="disabled"';
							$warning = JText::_('COM_TOES_ALTERNATIVE_SHOW_SESSION_DONT_HAVE_ENOUGH_SPACE');
						}
						else
						{
							$disabled = '';
							$warning = '';
						}
						$style = "";
					}
					else
					{
						$checked = "";
						$disabled = 'disabled="disabled"';
						$style = "style='display:none;'";
					}
				?>
				<span <?php echo $style;?> >
					<input onclick="check_show_day_for_placeholder(this,'AM',<?php echo $showday->show_day_id;?>);" type="checkbox" id="placeholder_participates_PM_<?php echo $showday->show_day_id;?>" value="<?php echo $showday->show_day_id;?>" name="placeholder_participates_PM[]" <?php echo $checked; ?> <?php echo $disabled;?> /> 
					<label for="placeholder_participates_PM_<?php echo $showday->show_day_id;?>">PM</label>
					&nbsp;<?php echo $warning;?>
				</span>
			</span>
            <br/>
        <?php endforeach;?>
    <?php endif;?>
</div>

<div class="fieldbg" >
    <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
    <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
    <input type="hidden" value="<?php echo $this->placeholder->edit;?>" id="edit" name="edit" />
    <input type="hidden" value="<?php echo $this->placeholder->placeholder_show;?>" id="show_id" name="show_id" />
    <input type="hidden" value="<?php echo $this->placeholder->placeholder_exhibitor;?>" id="user_id" name="user_id" />
    <input type="hidden" value="<?php echo @$this->placeholder->placeholder_id;?>" id="placeholder_id" name="placeholder_id" />
    <?php if($app->input->getVar('type') == 'third_party'): ?>
        <input onclick="placeholder_previous_step('step1');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
    <?php else: ?>
        <input onclick="cancel_edit_placeholder();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <?php endif; ?>
    <input onclick="placeholder_next_step('step1');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
</div>
