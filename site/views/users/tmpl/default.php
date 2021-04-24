<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

$user = JFactory::getUser();

?>

<script type="text/javascript">
    var myWidth;
    var myHeight;

    if( typeof( window.innerWidth ) == 'number' ) { 
        //Non-IE 
        myWidth = window.innerWidth;
        myHeight = window.innerHeight; 
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) { 
        //IE 6+ in 'standards compliant mode' 
        myWidth = document.documentElement.clientWidth; 
        myHeight = document.documentElement.clientHeight; 
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) { 
        //IE 4 compatible 
        myWidth = document.body.clientWidth; 
        myHeight = document.body.clientHeight; 
    }            

	function removeofficial(user_id,roll_id,official_id,region_id,official,question)
    {
 		new jBox('Confirm',{
	        content: question,
	        width: '400px',
	        cancelButton : NO_BUTTON,
	        confirmButton: YES_BUTTON,
	        confirm: function() {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=users.delete&id='+user_id+'&official='+official+'&roll_id='+roll_id+'&official_id='+official_id+'&region='+region_id+'&tmpl=component',
                    type: 'get'
                }).done( function(responseText){
					jQuery('#loader').hide();
                    if(responseText == 1)
                        location.reload(true);
                }).fail(function(){
					jQuery('#loader').hide();
					jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
				});
		        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		        jQuery('#loader').show();
            }
        }).open();   
    }
</script>
<div id="toes">
	<div id="loader" class="loader">
		<span id="loader-container">
			<img id="loader-img" src="media/com_toes/images/loading.gif" alt="" />
			<?php echo JText::_('COM_TOES_LOADING'); ?>
		</span>
	</div>

	<div style="text-align: center;">
		<?php if ($user->authorise('toes.manage_org_officials','com_toes')): ?>
		<span class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=users'); ?>">
				<?php echo JText::_('COM_TOES_ORGANIZATION_OFFICIALS'); ?>
			</a>
		</span>
		<?php endif; ?>
		<?php if ($user->authorise('toes.manage_club_officials','com_toes')): ?>
		<span class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=users&layout=clubofficials'); ?>">
				<?php echo JText::_('COM_TOES_CLUB_OFFICIALS'); ?>
			</a>
		</span>
		<?php endif; ?>
		<?php if ($user->authorise('toes.manage_show_officials','com_toes')): ?>
		<span class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<a class="button button-4" href="<?php echo JRoute::_('index.php?option=com_toes&view=users&layout=showofficials'); ?>">
				<?php echo JText::_('COM_TOES_SHOW_OFFICIALS'); ?>
			</a>
		</span>
		<?php endif; ?>
	</div>
	<div class="clr"></div>
	<br/>	
	
	<?php if ($user->authorise('toes.manage_org_officials','com_toes')): ?>
	<div class="pull-right">
		<input class="button button-4" type="button" value="<?php echo JText::_('COM_TOES_ADD'); ?>" onclick="window.location='<?php echo JRoute::_('index.php?option=com_toes&view=user&official=organization'); ?>'">
	</div>
	<div class ="pull-left outerdiv">
		<div class ="fistouter">
			<div class="block-title" ><?php echo JText::_('COM_TOES_ORGANIZATION_OFFICIALS'); ?></div>
			<div class="clr"></div>
		</div>

		<div class ="seconouter">
			<div class="seconouter-row hidden-phone">
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"><?php echo JText::_('COM_TOES_ROLE'); ?></div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12"><?php echo JText::_('COM_TOES_REGION'); ?></div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6"><?php echo JText::_('COM_TOES_NAME'); ?></div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6"><?php echo JText::_('COM_TOES_FIRST_NAME'); ?></div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6"><?php echo JText::_('COM_TOES_STATE'); ?></div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-6"><?php echo JText::_('COM_TOES_APPROVAL_NEEDED'); ?></div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12"></div>
			</div>
			<div class="clr"></div>
			<?php
			foreach ($this->OrgOfficial as $data) {
				?>
				<div class="seconouter-row-col">
					<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_ROLE'); ?></div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->roll; ?></div>
					<div class="visible-phone clr"></div>
					<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_REGION'); ?></div>
					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-8" ><?php echo ($data->roll == 'Regional Director')?$data->competitive_region_abbreviation:'-'; ?></div>
					<div class="visible-phone clr"></div>
					<?php /* */ ?>
					<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_NAME'); ?></div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->lastname; ?></div>
					<div class="visible-phone clr"></div>
					 
					<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_FIRST_NAME'); ?></div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo $data->firstname; ?></div>
					<div class="visible-phone clr"></div>
					 
					<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_STATE'); ?></div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-8" ><?php echo ($data->cb_state?$data->cb_state.'-' :'').$data->cb_country; ?></div>
					<div class="visible-phone clr"></div>
					<?php /**/?> 
					<div class="visible-phone col-xs-4"><?php echo JText::_('COM_TOES_APPROVAL_NEEDED'); ?></div>
					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-8" ><?php echo ($data->roll == 'Regional Director')?($data->competitive_region_confirmation_by_rd_needed?JText::_('JYES'):JText::_('JNO')):'-'; ?></div>
					<div class="visible-phone clr"></div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12" >
						<span class="hasTip" title="<?php echo JText::_('COM_TOES_DELETE'); ?>">
							<a href="javascript::void();" onclick="removeofficial('<?php echo $data->user_id; ?>','<?php echo $data->roll_id; ?>','<?php echo $data->official_id; ?>','<?php echo $data->competitive_region_id;?>','organization','<?php echo JText::sprintf('REMOVE_OFFICIAL_QUESTION', $data->roll, $data->firstname, $data->lastname, $data->uname) ?>');" class="cancel-show" >
								<i class="fa fa-trash large"></i>
							</a>
						</span>
						<span class="hasTip" title="<?php echo JText::_('COM_TOES_EDIT'); ?>">
							<a href="<?php echo JRoute::_('index.php?option=com_toes&view=user&official=organization&layout=organization&user_id='.$data->user_id.'&roll='.$data->roll_id.'&organization_id='.$data->official_id.'&region='.$data->competitive_region_id); ?>" class="edit-show" >
								<i class="fa fa-edit large"></i>
							</a>
						</span>
					</div>
					<div class="clr"></div>
				</div>
				<div class="clr"></div>
				<?php
			}
			?>
		</div>
	</div>
	<?php endif; ?>
</div>
