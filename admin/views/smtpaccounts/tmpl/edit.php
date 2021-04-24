
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
		if (task == 'smtpaccounts.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
	}
</script>
<style type="text/css">
.field-text {  float: left; margin-right: 10px;}
.field-value {  float: left;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_toes&layout=edit&smtp_id='.(int) $this->item->smtp_id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
				
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid form-horizontal-desktop">
					<?php echo $this->form->renderField('smtp_name'); ?>
					<?php echo $this->form->renderField('smtp_host'); ?>
					<?php echo $this->form->renderField('smtp_auth'); ?>
					<?php echo $this->form->renderField('smtp_user'); ?>
					<?php echo $this->form->renderField('smtp_pass'); ?>
					<?php echo $this->form->renderField('smtp_port'); ?>
					<?php echo $this->form->renderField('smtp_secure'); ?>
					<?php echo $this->form->renderField('published'); ?>
					<?php echo $this->form->renderField('smtp_id'); ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>
