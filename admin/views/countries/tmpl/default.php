<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();

$userId		= $user->get('id');
$extension	= $this->escape($this->state->get('filter.extension'));
//$ordering 	= ($listOrder == 'a.lft');
//$saveOrder 	= ($listOrder == 'a.lft' && $listDirn == 'asc');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_toes&view=countries'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
	<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
		<div class="clearfix"> </div>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
		    <table class="table table-striped adminlist">
				<thead>
					<tr>
						<th width="5">
							&#160;
						</th>
						<th style="text-align: center">
							<?php echo JHtml::_('grid.sort', 'COM_TOES_COUNTRY_NAME', 'c.name', $listDirn, $listOrder); ?>
						</th>
						<th style="text-align: center"class="nowrap">
							<?php echo JHtml::_('grid.sort', 'COM_TOES_COUNTRY_ALPHA_2', 'c.aplha_2', $listDirn, $listOrder); ?>
						</th>
						<th style="text-align: center"class="nowrap">
							<?php echo JHtml::_('grid.sort', 'COM_TOES_COUNTRY_ALPHA_3', 'c.aplha_3', $listDirn, $listOrder); ?>
						</th>
						<th style="text-align: center"class="nowrap">
							<?php echo JHtml::_('grid.sort', 'COM_TOES_COUNTRY_USES_STATE', 'c.country_uses_states', $listDirn, $listOrder); ?>
						</th>
						<th style="text-align: center"class="nowrap">
							<?php echo JHtml::_('grid.sort', 'COM_TOES_COMPETATIVE_REGION', 'c.competitive_region', $listDirn, $listOrder); ?>
						</th>
						<th style="text-align: center" width="1%" class="nowrap">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $item) :
						$canCreate	= $user->authorise('core.create',		'com_toes');
						$canEdit	= $user->authorise('core.edit',			'com_toes');
						
						$canChange	= $user->authorise('core.edit.state',	$extension.'.country.'.$item->id);
					?>
					<tr class="row<?php echo $i % 2; ?>">
						<td width="1%" class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td style="text-align: center">
							<?php echo $this->escape($item->name);?>
						</td>
						<td style="text-align: center">
							<?php echo $this->escape($item->alpha_2);?>
						</td>
						<td style="text-align: center">
							<?php echo $this->escape($item->alpha_3);?>
						</td>
						<td style="text-align: center">
							<span class="cb_template">
								<?php echo $item->country_uses_states?'<i class="fa fa-check"></i>':'<i class="fa fa-close"></i>';?>
							</span>
						</td>
						<td style="text-align: center">
							<?php echo $this->escape($item->region_name);?>
						</td>
						<td class="center">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>