
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


?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'states.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
	}
</script>
<style type="text/css">
.field-text {  float: left; margin-right: 10px;}
.field-value {  float: left;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_toes&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
				
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<?php echo $this->form->renderField('name'); ?>
					<?php echo $this->form->renderField('alpha_2'); ?>
					<?php echo $this->form->renderField('alpha_3'); ?>
					<?php echo $this->form->renderField('country_uses_states'); ?>
					<div class="control-group" >
						<div class="control-label" >
							<label id="region_name-lbl" for="region_name" class="hasPopover required" title=""  data-content="<?php echo JText::_('COM_TOES_COMPETATIVE_REGION');?>" data-original-title="<?php echo JText::_('COM_TOES_COMPETATIVE_REGION');?>">
								<?php echo JText::_('COM_TOES_COMPETATIVE_REGION');?>
								<span class="star">&nbsp;*</span>
							</label>
						</div>
						<div class="controls" >
							<input id="region_name" name="region_name" type="text" value="<?php echo @$this->item->region_name; ?>" class="inputbox" size="40" required="true"/>
						</div>
					</div>
					<div class="clr"></div>

					<?php echo $this->form->renderField('competitive_region'); ?>
					<?php echo $this->form->renderField('id'); ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>

<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery( "#region_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=countries.getRegions&tmpl=component',
		  select: function( event, ui ) {
			jQuery( "#jform_competitive_region" ).val(ui.item.key);
			jQuery( "#region_name" ).val(ui.item.value);
		  }
		});    
	});
</script>