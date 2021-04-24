<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$app = JFactory::getApplication();

$user = JFactory::getUser();
$isAdmin = TOESHelper::isAdmin();
$show_id = $app->input->getInt('id', 0);
$data = $this->show;

$isContinuous = ($data->show_format == 'Continuous') ? 1 : 0;

$params = JComponentHelper::getParams('com_toes');

if ($params->get('sync_db') && $user->authorise('toes.access_sync_options', 'com_toes')) {
	$allowed_sync = 1;
} else {
	$allowed_sync = 0;
}

?>

<div id="loader" class="loader">
	<span id="loader-container">
		<img id="loader-img" src="media/com_toes/images/loading.gif" alt="" />
		<?php echo JText::_('COM_TOES_LOADING'); ?>
	</span>
	<div id="progress-box">
		<span id="progress-bar">&nbsp;</span>
		<br/>
		<span id="progress-count">
			<label id="progress-count-processed">?</label> / <label id="progress-count-total">?</label>
		</span>
	</div>
	<span style="display: inline-block;" id="progress-log-text">&nbsp;</span>
</div>

<div style="padding: 0 15px;">
	<?php if ($data->show_status != 'Held'): ?>
		<a id="edit_show" rel="<?php echo $show_id; ?>" href="<?php echo JRoute::_('index.php?option=com_toes&view=show&layout=edit&id=' . $show_id); ?>" >
			<i class="fa fa-edit"></i> 
			<?php echo JText::_('EDIT_SHOW'); ?>
		</a>
		<?php if ($data->show_status != 'Open'): ?>
			<a id="open_show" href ="javascript:void(0);" rel="<?php echo $show_id; ?>" >
				<i class="fa fa-unlock"></i> 
				<?php echo JText::_('OPEN_SHOW_FOR_ENTRIES'); ?>
			</a>
		<?php else : ?>
			<a id="close_show" href ="javascript:void(0);" rel="<?php echo $show_id; ?>" >
				<i class="fa fa-lock"></i>
				<?php echo JText::_('CLOSE_SHOW_FOR_ENTRIES'); ?>
			</a>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ($allowed_sync) : ?>
		<input class="add" type="button" name="sync" value="<?php echo JText::_('COM_TOES_SYNC_SHOW_DATA'); ?>" onclick="confirm_sync(<?php echo $show_id; ?>);"/>
	<?php endif; ?>	
</div>
<div class="clr"></div>
<br/>

<div>
	<div class ="block">
		<span style="font-weight: bold;">
			<?php echo $data->club_name; ?>
		</span>
		<span style="font-style: italic;">
			<?php
			echo '<i class="fa fa-calendar"></i> ';
			echo $this->show_dates;
			?>
		</span>
		<span>
			<?php 
			$address = '';
			
			//if (@$data->address_city)
			if (isset($data->address_city) && $data->address_city) 
				$address .= $data->address_city . ', ';
			//if (@$data->address_state)
			if (isset($data->address_state) && $data->address_state) 
				$address .= $data->address_state . ', ';
			//if (@$data->address_country)
			if (isset($data->address_country) && $data->address_country) 
				$address .= $data->address_country;
			
			if($address) {
				echo '<i class="fa fa-map-marker"></i> '.$address;
			}
			?>
		</span>
		<span>
			<?php echo " ( ".$data->show_format." ) "; ?>
		</span>
	</div>
</div>
<br/>

<div style="text-align: center;">
	<span class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&id=' . $data->show_id); ?>">
			<?php echo JText::_('COM_TOES_SHOW_ENTRIES'); ?>
		</a>
	</span>
	<span class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&layout=chrono&id=' . $data->show_id); ?>">
			<?php echo JText::_('COM_TOES_ENTRY_CLERK_LIST_VIEW'); ?>
		</a>
	</span>
	<span class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&layout=entrychanges&id=' . $data->show_id); ?>">
			<?php echo JText::_('COM_TOES_CHANGES_VIEW'); ?>
		</a>
	</span>
	<span class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
		<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&layout=reports&id=' . $data->show_id); ?>">
			<?php echo JText::_('COM_TOES_SHOW_DOCUMENTS'); ?>
		</a>
	</span>
</div>
<div class="clr"></div>
<br/>
