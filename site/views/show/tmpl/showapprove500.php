<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.modal');
JHtml::_('formbehavior2.select2','select');

$app = JFactory::getApplication();
$id = $app->input->getInt('id');
$hash = $app->input->get('hash');

$checkurl = $this->checkurlhash;
$rejectedshows = $this->checkshowisrejected;

?>
<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_toes&view=shows'); ?>"
 method="post" class="form-validate" enctype="multipart/form-data">
<div id="toes">
	<div class="showapprove">
		<?php 
		if(empty($rejectedshows))
		{
		?>
			<div class="control-group">
				<div class="control-label">
					<label>Reason</label>
				</div>
				<div class="controls">
					<textarea name="reason" id="reason" required></textarea>
				</div>
			</div>
			<?php 
				if($checkurl == $hash)
				{ ?>
					<button onclick="approveshow('<?php echo $id?>','<?php echo $hash;?>');"> Approve</button>
					
					
		<?php   } ?>	
					<button onclick="disapproveshow('<?php echo $id?>','<?php echo $hash;?>');"> Reject</button>
<?php }
	else
	{
		JFactory::getApplication()->enqueueMessage('Shows are rejected by '.$rejectedshows->club_name);
	}
		
		?>	
		
	</div> 
	
</div>
			<!--input type="hidden" name="option" value="com_toes"/>
			<input type="hidden" id="task" name="task" value="show.approveshow"/-->
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<input type="hidden" name="hash" value="<?php echo $hash;?>" />
			<?php echo JHtml::_('form.token'); ?>
</form>
<script>
	var hash = "<?php echo $hash; ?>";
	
	var checkurl = "<?php echo $checkurl; ?>";
	if(!hash == checkurl)
	{
		alert("URL Is incorrect");
	}
	
function approveshow(id,hash)
{
	var str = jQuery('#reason').val();
	
	jQuery.ajax({
			method: "POST",
			url: "index.php?option=com_toes&task=show.approveshow&id="+id+"&hash="+hash+"&tmpl=component",	
			data: {data:str},
			success:function(data){
					
					if(data == 1)
					{		
						window.location.href = "index.php?option=com_toes&view=shows";
					}
								
		}	
		});
}
function disapproveshow(id,hash)
{
	var str = jQuery('#reason').val();
	
	jQuery.ajax({
			method: "POST",
			url: "index.php?option=com_toes&task=show.disapproveshow&id="+id+"&hash="+hash+"&tmpl=component",	
			data: {data:str},
			success:function(data){
					
					if(data == 1)
					{		
						window.location.href = "index.php?option=com_toes&view=shows";
					}
								
		}	
		});
	
}
</script>
<style>
.form-horizontal .control-label {
    float: left;
    width: 160px;
    padding-top: 5px;
    text-align: right;
}
.form-horizontal .control-group {
    margin-bottom: 20px;
}
.form-horizontal .control-label {
    float: left;
    width: 160px;
    padding-top: 5px;
    text-align: right;
}
.form-horizontal .controls {
    margin-left: 180px;
}
.disapprove{
	margin: 10px !important;
    float: left;
    background-color: buttonface;
    color: #000 !important;
   //border: 1px solid #000;
    padding: 2px 3px;
    font: 12px Arial, sans-serif;
    display: block !important;
    position: relative;
}	
.approve{
	float: left;
    margin: 10px;
}
.showapprove{padding: 10px;float:left;}
</style>
