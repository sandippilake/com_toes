
<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$user = JFactory::getUser();
//$canDo = TemplatesHelper::getActions();

/*
$result	= new JObject;
$actions = array(
	'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
);
foreach ($actions as $action) {
	$result->set($action, $user->authorise($action, 'com_services'));
}
$canDo = $result;
*/

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		alert(task);
		if (task == 'breeds.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
	}
</script>
<style type="text/css">
.field-text {  float: left; margin-right: 10px;}
.field-value {  float: left;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_toes&layout=edit&breed_id='.(int) $this->item->breed_id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
				
		<fieldset class="adminform">
			<legend><?php echo JText::_('JDETAILS');?></legend>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED');?>:
				</div>
				<div class="field-value" >
					<input 	name="breed_name" type="text" value="<?php echo @$this->item->breed_name; ?>" class="inputbox" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
						
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED_ABBREVIATION');?>:
				</div>
				<div class="field-value" >
					<input 	name="breed_abbreviation" type="text" value="<?php echo @$this->item->breed_abbreviation; ?>" class="inputbox" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED_GROUP');?>:
				</div>
				<div class="field-value" >
					<input 	name="breed_group" type="text" value="<?php echo @$this->item->breed_group; ?>" class="inputbox" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED_HAIR_LENGTH');?>:
				</div>
				<div class="field-value" >
					<input 	name="breed_hair_length" type="text" value="<?php echo @$this->item->breed_hair_length; ?>" class="inputbox" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED_COLOR_RESTRICTIONS');?>:
				</div>
				<div class="field-value" >
					<?php 
					$pub[] = JHTML::_('select.option','0','No'); 
					$pub[] = JHTML::_('select.option','1','Yes'); 
					$published =JHTML::_('select.radiolist',$pub,'breed_color_restrictions','class="inputbox"','value','text',@$this->item->breed_color_restrictions); 
					echo $published ; ?>
				</div>
			</div>
			<div class="clr"></div>
									
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED_STATUS_NAME');?>:
				</div>
				<div class="field-value" >
					<?php echo $this->breed_statuslist; ?>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED_ORGANIZATION_NAME');?>:
				</div>
				<div class="field-value" >
					<?php echo $this->breed_orglist; ?>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_BREED_ID');?>:
				</div>
				<div class="field-value">
					<span class="readonly"><?php echo @$this->item->breed_id; ?></span>
				</div>
			</div>
			<div class="clr"></div>
						
		</fieldset>
		
		<input type="hidden" name="breed_id" value="<?php echo @$this->item->breed_id; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>

