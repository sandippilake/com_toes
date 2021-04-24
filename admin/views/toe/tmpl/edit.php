
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
		if (task == 'toe.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
			Joomla.submitform(task, document.getElementById('service-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_toes&layout=edit&id='.(int)$this->item->id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
	
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('New Task') : JText::sprintf('Edit Task', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('name'); ?>
				<?php echo $this->form->getInput('name'); ?></li>

				<li><?php echo $this->form->getLabel('user_id'); ?>
				<?php echo $this->form->getInput('user_id'); ?></li>

				<li><?php echo $this->form->getLabel('category_id'); ?>
				<?php echo $this->form->getInput('category_id'); ?></li>

				<li><?php echo $this->form->getLabel('start_date'); ?>
				<?php echo $this->form->getInput('start_date'); ?></li>
				
				<li><?php echo $this->form->getLabel('start_time'); ?>
				<?php echo $this->form->getInput('start_time'); ?></li>

				<li><?php echo $this->form->getLabel('end_date'); ?>
				<?php echo $this->form->getInput('end_date'); ?></li>
				
				<li><?php echo $this->form->getLabel('end_time'); ?>
				<?php echo $this->form->getInput('end_time'); ?></li>

				<li><?php echo $this->form->getLabel('due_date'); ?>
				<?php echo $this->form->getInput('due_date'); ?></li>
				
				<li><?php echo $this->form->getLabel('due_time'); ?>
				<?php echo $this->form->getInput('due_time'); ?></li>
				
				<li><?php echo $this->form->getLabel('status'); ?>
				<?php echo $this->form->getInput('status'); ?></li>
				
				<li><?php echo $this->form->getLabel('priority'); ?>
				<?php echo $this->form->getInput('priority'); ?></li>
				
				<li><?php echo $this->form->getLabel('recurring'); ?>
				<?php echo $this->form->getInput('recurring'); ?></li>
									
				<?php if ($this->item->id) : ?>
				<li><?php echo $this->form->getLabel('id'); ?>
				<span class="readonly"><?php echo $this->item->id; ?></span></li>
				<?php endif; ?>
				
			</ul>
			<div class="clr"></div>
			<?php echo $this->form->getLabel('description'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description'); ?>
			<div class="clr"></div>
		</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>
