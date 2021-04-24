<?php

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select.stateslist');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
 
$user = JFactory::getUser();
?> 
 
<form	action="<?php echo JRoute::_('index.php?option=com_toes&task=organization.save'); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="organization-form" class="organization-validate">

	<div class="form-horizontal">
		 
		 <div class="horizontal">
			<?php echo $this->form->renderFieldset('');?>
		 </div>
		 
		  
		 
		
		 
		<input type="hidden" name="task" value="organization.save"/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<input type="button" value="Save" onclick="save_organization(); "/>
</form>
 
 
<script>
function save_organization(){
	
	 if(!document.formvalidator.isValid(document.id('product-form'))){
		alert('Please make sure that all values are entered correctly');
		//return;
	}
	document.id('organization-form').submit();
}
</script>

