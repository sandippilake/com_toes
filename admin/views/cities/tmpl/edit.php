
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
		if (task == 'cities.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
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
					<div class="control-group" >
						<div class="control-label" >
							<label id="country_name-lbl" for="country_name" class="hasPopover required" title=""  data-content="<?php echo JText::_('COM_TOES_COUNTRY_NAME_DESC');?>" data-original-title="<?php echo JText::_('COM_TOES_COUNTRY_NAME');?>">
								<?php echo JText::_('COM_TOES_COUNTRY_NAME');?>
								<span class="star">&nbsp;*</span>
							</label>
						</div>
						<div class="controls" >
							<input id="country_name" name="country_name" type="text" value="<?php echo @$this->item->country_name; ?>" class="inputbox" size="40" required="true"/>
						</div>
					</div>
					<div class="clr"></div>

					<div class="control-group state_div" >
						<div class="control-label" >
							<label id="state_name-lbl" for="state_name" class="hasPopover" title=""  data-content="<?php echo JText::_('COM_TOES_STATE_NAME_DESC');?>" data-original-title="<?php echo JText::_('COM_TOES_STATE_NAME');?>">
								<?php echo JText::_('COM_TOES_STATE_NAME');?>
							</label>
						</div>
						<div class="controls" >
							<input id="state_name" name="state_name" type="text" value="<?php echo @$this->item->state_name; ?>" class="inputbox" size="40" />
						</div>
					</div>
					<div class="clr"></div>

					<?php echo $this->form->renderField('state_id'); ?>
					<?php echo $this->form->renderField('country_id'); ?>
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
		jQuery( "#country_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=venues.getCountries&tmpl=component',
		  select: function( event, ui ) {
			jQuery( "#jform_country_id" ).val(ui.item.key);
			jQuery( "#country_name" ).val(ui.item.value);

			jQuery('#jform_state_id').val(0);
			jQuery('#state_name').val('');
			if(ui.item.country_uses_states == 0) {
				 jQuery('.state_div').hide();
			}
		  }
		});    

		jQuery( "#state_name" ).autocomplete({
		  source: function( request, response ) {
			jQuery.ajax({
			  url: 'index.php?option=com_toes&task=venues.getStates&tmpl=component',
			  dataType: "json",
			  data: {
				term: request.term, 
				country_id: jQuery( "#jform_country_id" ).val()
			  }
			}).done(function( data ) {
				response( data );
			});
		  },
		  select: function( event, ui ) {
			jQuery( "#jform_state_id" ).val(ui.item.key);
			jQuery( "#state_name" ).val(ui.item.value);
		  }
		}); 
	});
</script>