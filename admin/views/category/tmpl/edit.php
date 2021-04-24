
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
		if (task == 'category.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_taskmanager&layout=edit&category_id='.(int) $this->item->category_id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
	
		<fieldset class="adminform">
			<legend><?php echo JText::_('JDETAILS');?></legend>
			<ul class="adminformlist">
				<li>
					<?php echo JText::_('COM_TOES_CATEGORY_NAME');?>
					<input 	name="category" type="text" value="<?php echo @$this->item->category; ?>" class="inputbox" size="40" required="true"/>
				</li>

				<li><?php echo JText::_('COM_TOES_CATEGORY_ORGANIZATION_NAME');?>
				<?php echo $this->category_orglist; ?></li>
				
				<?php if (@$this->item->category_id) : ?>
				<li>
					<?php echo JText::_('COM_TOES_CATEGORY_ID');?><span class="readonly">
					<?php echo @$this->item->category_id; ?></span>
				</li>
				<?php endif; ?>
				
			</ul>
		</fieldset>
		
		<input type="hidden" name="category_id" value="<?php echo @$this->item->category_id; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>
