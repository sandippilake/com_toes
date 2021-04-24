
<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::_('jquery.framework');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
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
		if (task == 'venues.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
	}
</script>
<style type="text/css">
.field-text {  float: left; margin-right: 10px;}
.field-value {  float: left;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_toes&layout=edit&venue_id='.@$this->item->venue_id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
				
		<fieldset class="adminform">
			<legend><?php echo JText::_('JDETAILS');?></legend>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_VENUE');?>:
				</div>
				<div class="field-value" >
					<input 	name="venue_name" type="text" value="<?php echo @$this->item->venue_name; ?>" class="inputbox required" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
						
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_ADDRESS_LINE_1');?>:
				</div>
				<div class="field-value" >
					<input 	name="address_line_1" type="text" value="<?php echo @$this->item->address_line_1; ?>" class="inputbox required" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_ADDRESS_LINE_2');?>:
				</div>
				<div class="field-value" >
					<input 	name="address_line_2" type="text" value="<?php echo @$this->item->address_line_2; ?>" class="inputbox" size="40" />
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_ADDRESS_LINE_3');?>:
				</div>
				<div class="field-value" >
					<input 	name="address_line_3" type="text" value="<?php echo @$this->item->address_line_3; ?>" class="inputbox" size="40" />
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_ADDRESS_COUNTRY');?>:
				</div>
				<div class="field-value" >
					<input id="address_country_name" name="address_country_name" type="text" value="<?php echo @$this->item->address_country_name; ?>" class="inputbox" size="40" required="true" onkeydown="prev_country_check(event);" onblur="clear_prev_country();checkCountry();"/>
					<input id="address_country" name="address_country" type="hidden" value="<?php echo @$this->item->address_country; ?>" class="inputbox required" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>

			<?php
				if(@$this->item->country_uses_states == 1) {
					$state_style = '';
				} else {
					$state_style = 'style="display:none"';
				}
			?>
			
			<div class="fieldbg state_div" <?php echo $state_style;?> >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_ADDRESS_STATE');?>:
				</div>
				<div class="field-value" >
					<input id="address_state_name" name="address_state_name" type="text" value="<?php echo (@$this->item->country_uses_states == 1)?@$this->item->address_state_name:''; ?>" class="inputbox" size="40" onkeydown="prev_state_check(event);" onblur="clear_prev_state();check_state();"/>
					<input id="address_state" name="address_state" type="hidden" value="<?php echo (@$this->item->country_uses_states == 1)?@$this->item->address_state:'0'; ?>" class="inputbox " size="40"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_ADDRESS_CITY');?>:
				</div>
				<div class="field-value" >
					<input id="address_city_name" name="address_city_name" type="text" value="<?php echo @$this->item->address_city_name; ?>" class="inputbox" size="40" required="true" onkeydown="prev_city_check(event);" onblur="clear_prev_city();checkCity();"/>
					<input id="address_city" name="address_city" type="hidden" value="<?php echo @$this->item->address_city; ?>" class="inputbox required" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_ADDRESS_ZIP_CODE');?>:
				</div>
				<div class="field-value" >
					<input 	name="address_zip_code" type="text" value="<?php echo @$this->item->address_zip_code; ?>" class="inputbox required" size="40" required="true"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_VENUE_WEBSITE');?>:
				</div>
				<div class="field-value" >
					<input 	name="venue_website" type="text" value="<?php echo @$this->item->venue_webiste; ?>" class="inputbox" size="40"/>
				</div>
			</div>
			<div class="clr"></div>
			
			<div class="fieldbg" >
				<div class="field-text" >
					<?php echo JText::_('COM_TOES_VENUE_ID');?>:
				</div>
				<div class="field-value">
					<span class="readonly"><?php echo @$this->item->venue_id; ?></span>
				</div>
			</div>
			<div class="clr"></div>
						
		</fieldset>
		
		<input type="hidden" name="venue_id" value="<?php echo @$this->item->venue_id; ?>" />
		<input type="hidden" name="venue_address" value="<?php echo @$this->item->venue_address; ?>" />
		<input type="hidden" name="address_id" value="<?php echo @$this->item->address_id; ?>" />
		<input type="hidden" name="address_type" value="<?php echo @$this->item->address_type; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>

<script type="text/javascript">
	var prev_country_text = '';
	var prev_country = '';
	var prev_country_set = false;

	function prev_country_check(evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode

		if(charCode == 27 && prev_country && prev_country_text)
		{
			jQuery('#address_country_name').val(prev_country_text);
			jQuery('#address_country').val(prev_country);
			prev_country_set = false;
			return true;
		}

		if(charCode == 16 || charCode == 17 || charCode == 18 || charCode == 20 ||
				charCode == 9 || charCode == 13 || charCode == 93 || charCode == 36 ||
				charCode == 33 || charCode == 34 || charCode == 35 || (charCode >= 112 && charCode <= 123) ||
				(charCode == 65 && evt.ctrlKey === true))
			return true;

		if(prev_country_set)
			return true;

		prev_country_text = jQuery('#address_country_name').val();
		prev_country = jQuery('#address_country').val();
		jQuery('#address_country').val('');
		prev_country_set = true;

		return true;
	}

	function clear_prev_country()
	{
		prev_country_text = '';
		prev_country = '';
		prev_country_set = false;
	}

	function checkCountry()
	{
		if(jQuery('#address_country_name').val() != '' && !jQuery('#address_country').val())
		{
			alert("<?php echo JText::_('COM_TOES_PLEASE_SELECT_COUNTRY_FROM_LIST');?>");
			jQuery('#address_country').val('');
			jQuery('#address_country_name').val('');
			jQuery('#address_country_name').focus();
		} else if(jQuery('#address_country_name').val() == '') {
			jQuery('#address_country').val('');
		}
	}
	
	var prev_state_text = '';
	var prev_state = '';
	var prev_state_set = false;

	function prev_state_check(evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode

		if(charCode == 27 && prev_state && prev_state_text)
		{
			jQuery('#address_state_name').val(prev_state_text);
			jQuery('#address_state').val(prev_state);
			prev_state_set = false;
			return true;
		}

		if(charCode == 16 || charCode == 17 || charCode == 18 || charCode == 20 ||
				charCode == 9 || charCode == 13 || charCode == 93 || charCode == 36 ||
				charCode == 33 || charCode == 34 || charCode == 35 || (charCode >= 112 && charCode <= 123) ||
				(charCode == 65 && evt.ctrlKey === true))
			return true;

		if(prev_state_set)
			return true;

		prev_state_text = jQuery('#address_state_name').val();
		prev_state = jQuery('#address_state').val();
		jQuery('#address_state').val('');
		prev_state_set = true;

		return true;
	}

	function clear_prev_state()
	{
		prev_state_text = '';
		prev_state = '';
		prev_state_set = false;
	}

	function check_state()
	{
		if(jQuery('#address_state_name').val() != '' && !jQuery('#address_state').val())
		{
			alert("<?php echo JText::_('COM_TOES_PLEASE_SELECT_STATE_FROM_LIST');?>");
			jQuery('#address_state').val('');
			jQuery('#address_state_name').val('');
			jQuery('#address_state_name').focus();
		} else if(jQuery('#address_state_name').val() == '') {
			jQuery('#address_state').val('');
		}
	}
	
	var prev_city_text = '';
	var prev_city = '';
	var prev_city_set = false;

	function prev_city_check(evt)
	{
		var charCode = (evt.which) ? evt.which : evt.keyCode

		if(charCode == 27 && prev_city && prev_city_text)
		{
			jQuery('#address_city_name').val(prev_city_text);
			jQuery('#address_city').val(prev_city);
			prev_city_set = false;
			return true;
		}

		if(charCode == 16 || charCode == 17 || charCode == 18 || charCode == 20 ||
				charCode == 9 || charCode == 13 || charCode == 93 || charCode == 36 ||
				charCode == 33 || charCode == 34 || charCode == 35 || (charCode >= 112 && charCode <= 123) ||
				(charCode == 65 && evt.ctrlKey === true))
			return true;

		if(prev_city_set)
			return true;

		prev_city_text = jQuery('#address_city_name').val();
		prev_city = jQuery('#address_city').val();
		jQuery('#address_city').val('');
		prev_city_set = true;

		return true;
	}

	function clear_prev_city()
	{
		prev_city_text = '';
		prev_city = '';
		prev_city_set = false;
	}

	function checkCity()
	{
		if(jQuery('#address_city_name').val() != '' && !jQuery('#address_city').val())
		{
			alert("<?php echo JText::_('COM_TOES_PLEASE_SELECT_STATE_FROM_LIST');?>");
			jQuery('#address_city').val('');
			jQuery('#address_city_name').val('');
			jQuery('#address_city_name').focus();
		} else if(jQuery('#address_city_name').val() == '') {
			jQuery('#address_city').val('');
		}
	}
	
	jQuery(document).ready(function(){
		jQuery( "#address_country_name" ).autocomplete({
		  source: 'index.php?option=com_toes&task=venues.getCountries&tmpl=component',
		  select: function( event, ui ) {
			jQuery( "#address_country" ).val(ui.item.key);
			jQuery( "#address_country_name" ).val(ui.item.value);

			jQuery('#address_state').val(0);
			jQuery('#address_state_name').val('');
			jQuery('#address_city').val(0);
			jQuery('#address_city_name').val('');
			
			if(ui.item.country_uses_states == 0) {
				 jQuery('.state_div').hide();
			}
		  }
		});    

		jQuery( "#address_state_name" ).autocomplete({
		  source: function( request, response ) {
			jQuery.ajax({
			  url: 'index.php?option=com_toes&task=venues.getStates&tmpl=component',
			  dataType: "json",
			  data: {
				term: request.term, 
				country_id: jQuery( "#address_country" ).val()
			  }
			}).done(function( data ) {
				response( data );
			});
		  },
		  select: function( event, ui ) {
			jQuery( "#address_state" ).val(ui.item.key);
			jQuery( "#address_state_name" ).val(ui.item.value);

			jQuery('#address_city').val(0);
			jQuery('#address_city_name').val('');
		  }
		}); 

		jQuery( "#address_city_name" ).autocomplete({
		  source: function( request, response ) {
			jQuery.ajax({
			  url: 'index.php?option=com_toes&task=venues.getCities&tmpl=component',
			  dataType: "json",
			  data: {
				term: request.term, 
				state_id: jQuery( "#address_state" ).val(),
				country_id: ((jQuery( "#address_state" ).val()>0)?0:jQuery( "#address_country" ).val())
			  }
			}).done(function( data ) {
				response( data );
			});
		  },
		  select: function( event, ui ) {
			jQuery( "#address_city" ).val(ui.item.key);
			jQuery( "#address_city_name" ).val(ui.item.value);
		  }
		}); 
	});
</script>