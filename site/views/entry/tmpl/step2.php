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

$isNT = false;
foreach($this->showdays as $showday)
{
	$class = '';
	$class = TOESHelper::getEntryclassonShowday($this->cat->cat_id, $showday->show_day_id);
	if(!$class)
		$class = TOESHelper::getCatclassonShowday($this->cat->cat_id, $showday->show_day_id);
	if(strpos($class, 'NT') || strpos($class, 'ANB') || strpos($class, 'PNB'))
	{
		$isNT = true;
	}
}

$show = TOESHelper::getShowDetails($this->entry->show_id);
$isAlternative = TOESHelper::isAlternative($this->entry->show_id);

$isKitten = true;

$placeholder = '';
$placeholder_show_days = array();
$AM_placeholders = array();
$PM_placeholders = array();
if(isset($this->entry->placeholder_id) && $this->entry->placeholder_id)
{
    $placeholder = TOESHelper::getPlaceholderFullDetails($this->entry->placeholder_id);

    foreach($placeholder as $day)
	{
        $placeholder_show_days[] = $day->placeholder_day_showday;

		if($day->placeholder_participates_AM)
			$AM_placeholders[] = $day->placeholder_day_showday;

		if($day->placeholder_participates_PM)
			$PM_placeholders[] = $day->placeholder_day_showday;
	}
}

?>
<?php if($this->entry->edit) : ?>
    <h3><?php echo JText::_('EDIT_ENTRY')?></h3>
<?php else :?>
    <h3><?php echo JText::_('NEW_ENTRY')?></h3>
<?php endif; ?>
<?php
if($isNT && !$this->cat->cat_registration_number)
	echo '<p class="error">'.JText::_('COM_TOES_REGISTRATION_NUMBER_REQUIRED_NT_PNB_ANB').'</p><br/>';
?>
<?php $user = JFactory::getUser(); ?>
<?php if($this->entry->user_id != $user->id): ?>
    <label class="label"><?php echo JText::_("COM_TOES_SELECTED_USER")." : ";?></label>
<?php echo TOESHelper::getUserInfo($this->entry->user_id)->name;?>
<br/>
<?php endif; ?>
<label class="label"><?php echo JText::_("COM_TOES_SELECTED_CAT")." : ";?></label>
<?php echo $this->cat->cat_prefix_abbreviation.' '.$this->cat->cat_title_abbreviation.' '.$this->cat->cat_name.' '.$this->cat->cat_suffix_abbreviation;?>

<br/><br/>
<label class="label"><?php echo JText::_("COM_TOES_SELECT_SHOW_DAYS");?></label>
<div>
    <?php if($this->showdays): ?>
        <?php foreach($this->showdays as $showday):

			$am_available = false;
			$pm_available = false;

			if(TOESHelper::isKittenonDay($this->cat->cat_id, $showday->show_day_id,3))
			{
				$checked = '';
				$disabled = 'disabled="disabled"';
				$warning = '<p class="warning">'.JText::_('COM_TOES_KITTEN_TOO_YOUNG').'</p>';
			}
			else
			{
				$isKitten = false;
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

				$disabled = '';
				if($placeholder_show_days && in_array($showday->show_day_id, $placeholder_show_days))
				{
					$checked = 'checked="checked"';
					$disabled = 'disabled="disabled"';
					$warning = '';
				}
				else
				{
					$checked = ( isset($this->entry->showdays) && in_array($showday->show_day_id, explode(',',$this->entry->showdays)) )?'checked="checked"':'';
					if($isAlternative)
					{
						if($am_available || $pm_available)
							$warning = '';
						else
						{
							$disabled = 'disabled="disabled"';
							$warning = '<p class="warning">'.JText::_('COM_TOES_ALTERNATIVE_SHOWDAY_DONT_HAVE_ENOUGH_SPACE').'</p>';
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
							$warning = '<p class="warning">'.JText::_('COM_TOES_SHOWDAY_DONT_HAVE_ENOUGH_SPACE').'</p>';
						}
					}
				}

				if(!$disabled && TOESHelper::isKittenonDay($this->cat->cat_id, $showday->show_day_id,4))
				{
					$warning .= '<p class="warning">'.JText::_('COM_TOES_KITTEN_IS_3_MONTHS').'</p>';
				}
			}
        ?>
            <input onclick="check_AM_PM_for_entry(this,<?php echo $showday->show_day_id;?>);" type="checkbox" id="show_day_<?php echo $showday->show_day_id;?>" value="<?php echo $showday->show_day_id;?>" name="showday[]" <?php echo $checked; ?> <?php echo $disabled;?> /> 
            &nbsp;
            
            <label for="show_day_<?php echo $showday->show_day_id;?>"><?php echo date('l',  strtotime($showday->show_day_date)); ?></label>
            &nbsp;<?php echo $warning;?>
			<span style="padding-left:30px;<?php echo $isAlternative?'display:block':'display:none';?>" >
				<?php
					$am_rings = TOESHelper::getShowdayRings($showday->show_day_id,'1');
					if($am_rings)
					{
						if($AM_placeholders && in_array($showday->show_day_id, $AM_placeholders))
						{
							$checked = 'checked="checked"';
							$disabled = 'disabled="disabled"';
							$warning = '';
						}
						else
						{
							$checked = ( isset($this->entry->entry_for_AM) && in_array($showday->show_day_id, explode(',',$this->entry->entry_for_AM)) )?'checked="checked"':'';
							if(!$am_available)
							{
								$checked = "";
								$disabled = 'disabled="disabled"';
								$warning = '<p class="warning">'.JText::_('COM_TOES_ALTERNATIVE_SHOW_SESSION_DONT_HAVE_ENOUGH_SPACE').'</p>';
							}
							else
							{
								$disabled = '';
								$warning = '';
							}
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
					<input onclick="check_show_day_for_entry(this,'PM',<?php echo $showday->show_day_id;?>);" type="checkbox" id="entry_participates_AM_<?php echo $showday->show_day_id;?>" value="<?php echo $showday->show_day_id;?>" name="entry_participates_AM[]" <?php echo $checked; ?> <?php echo $disabled;?> /> 
					<label for="entry_participates_AM_<?php echo $showday->show_day_id;?>">AM</label>
					&nbsp;<?php echo $warning;?>
				</span>
				<?php if($am_rings):?>
					<br/>
				<?php endif;?>
				<?php
					$pm_rings = TOESHelper::getShowdayRings($showday->show_day_id,'2');
					if($pm_rings)
					{
						if($PM_placeholders && in_array($showday->show_day_id, $PM_placeholders))
						{
							$checked = 'checked="checked"';
							$disabled = 'disabled="disabled"';
							$warning = '';
						}
						else
						{
							$checked = ( isset($this->entry->entry_for_PM) && in_array($showday->show_day_id, explode(',',$this->entry->entry_for_PM)) )?'checked="checked"':'';
							if(!$pm_available)
							{
								$checked = "";
								$disabled = 'disabled="disabled"';
								$warning = '<p class="warning">'.JText::_('COM_TOES_ALTERNATIVE_SHOW_SESSION_DONT_HAVE_ENOUGH_SPACE').'</p>';
							}
							else
							{
								$disabled = '';
								$warning = '';
							}
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
					<input onclick="check_show_day_for_entry(this,'AM',<?php echo $showday->show_day_id;?>);" type="checkbox" id="entry_participates_PM_<?php echo $showday->show_day_id;?>" value="<?php echo $showday->show_day_id;?>" name="entry_participates_PM[]" <?php echo $checked; ?> <?php echo $disabled;?> /> 
					<label for="entry_participates_PM_<?php echo $showday->show_day_id;?>">PM</label>
					&nbsp;<?php echo $warning;?>
				</span>
			</span>
            <br/>
        <?php endforeach;?>
    <?php endif;?>
</div>

<div class="fieldbg" >
	<?php if($isKitten): ?>
		<input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
	<?php else: ?>
		<input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
		<input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
		<input type="hidden" value="<?php echo $this->entry->show_id;?>" name="show_id" />
		<input type="hidden" id="add_entry_user" name="add_entry_user" value="<?php echo $app->input->getVar('user_id'); ?>" />
		<input type="hidden" id="edit" name="edit" value="<?php echo $this->entry->edit; ?>" />
		<?php if((isset($this->entry->placeholder_id) && $this->entry->placeholder_id) || !$this->entry->edit):?>
			<input onclick="previous_step('step2');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
		<?php else : ?>
			<input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
		<?php endif;?>
		<input onclick="next_step('step2');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
	<?php endif; ?>
</div>
