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
		if (task == 'cat.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
	}
</script>
<style type="text/css">
.field-text {  float: left; margin-right: 10px;}
.field-value {  float: left;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_toes&layout=edit&cat_id='.(int) $this->item->cat_id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
				
		<fieldset class="adminform">
			<legend><?php echo JText::_('JDETAILS');?></legend>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_cat');?>:
				</div>
				<div class="field-value" >
					<input 	name="cat_name" type="text" value="<?php echo @$this->item->cat_name; ?>" class="inputbox" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_cat_CATEGORY_NAME');?>:
				</div>
				<div class="field-value" >
					<?php echo $this->cat_categorylist; ?>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_cat_DIVISION_NAME');?>:
				</div>
				<div class="field-value" >
					<?php echo $this->cat_divisionlist; ?>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_cat_ORGANIZATION_NAME');?>:
				</div>
				<div class="field-value" >
					<?php echo $this->cat_orglist; ?>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_cat_ID');?>:
				</div>
				<div class="field-value">
					<span class="readonly"><?php echo @$this->item->cat_id; ?></span>
				</div>
			</div>
			<div class="clr"></div>
						
		</fieldset>
		
		<input type="hidden" name="cat_id" value="<?php echo @$this->item->cat_id; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>
