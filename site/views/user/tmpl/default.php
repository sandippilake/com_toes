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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior2.select2','select');
?>

<script type="text/javascript">
	
    function cancelForm(form)
    {
        form.task.value = '';
        form.submit();
    }
    
    function submitform(form)
    {
        var submit = true;
    
        if(!form.official_user.value)
        {
            //alert(<?php echo JText::_('COM_TOES_USER_BLANK') ?>);
            submit = false;
        }
        if(!form.official.value)
        {
            //alert(<?php echo JText::_('COM_TOES_OFFICIAL_BLANK') ?>);
            submit = false;
        }
        if(!form.official_roll.value)
        {
            //alert(<?php echo JText::_('COM_TOES_OFFICIAL_ROLL_BLANK') ?>);
            submit = false;
        }
        
        var official = '<?php echo $app->input->getVar('official');?>';
        if(official == 'organization' && form.official_roll.value == 4)
        {
            if(!form.region.value)
            {
                //alert(<?php echo JText::_('COM_TOES_REGION_BLANK') ?>);
                submit = false;
            }
        }
    
        if(submit)
            form.submit();
        else
            jbox_alert('<?php echo JText::_('PLEASE_FILL_ALL_DATA'); ?>');
    }
   
	function getshows(club_id)
    {
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=user.changeshows&club='+club_id+'&tmpl=component',
            type: 'get',
        }).done(function(responseText){
            jQuery('#changed_officials').html(responseText);
            jQuery('#official').select2();
        });
    }
        
    function show_regions(val)
    {
        var official = '<?php echo $app->input->getVar('official');?>';
        
        if(official == 'organization' && val == 4)
            jQuery('#regions').css('display','block');
        else
            jQuery('#regions').css('display','none');
    }
   
   
</script>

<style type="text/css">
    .fieldbg { float: left; margin: 0 0 10px; padding: 0 10px 0 15px; width: 100%;}
    .fieldbg input{ margin-bottom: 0px;}
</style>

<?php
	if($app->input->getVar('official') == 'organization')
    {
		$lable = JText::_('COM_TOES_OFFICIAL_SELECT_ORGANIZATION');	
		$select = JText::_('COM_TOES_SELECT_ORGANIZATION');		
	}
	if($app->input->getVar('official') == 'club')
    {
		$lable = JText::_('COM_TOES_OFFICIAL_SELECT_CLUB');
		$select = JText::_('COM_TOES_SELECT_CLUB');			
	}
	if($app->input->getVar('official') == 'show')
    {
		$lable = JText::_('COM_TOES_OFFICIAL_SELECT_SHOW');	
		$select = JText::_('COM_TOES_SELECT_SHOW');		
	}
?>
<div id="toes">
	<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_toes&view=users'); ?>" method="post" class="form-validate" enctype="multipart/form-data" onsubmit="submitform(this.form);"> 
	    <div class="fistouter">
	        <div class="fieldblank" >
	            <div class="block-title">
	            	<?php 
            			switch ($app->input->getVar('official')) {
							case 'organization':
									echo JText::_('COM_TOES_ADD_ORGANIZATION_OFFICIALS'); 
								break;
							case 'club':
									echo JText::_('COM_TOES_ADD_CLUB_OFFICIALS'); 
								break;
							case 'show':
									echo JText::_('COM_TOES_ADD_SHOW_OFFICIALS'); 
								break;
						}
	            	?>
            	</div>
	        </div>
	        <div class="clr"></div>
	    </div>

	    <div class="block-rg_number seconouter">
			<br/><br/>
	        <?php  if ($app->input->getVar('official') == 'show') { ?>
	            <div class="fieldbg" >
	                <div class="form-label" ><label class="hasTip required" for="clubs" id="clubs-lbl"><?php echo JText::_('COM_TOES_CLUB'); ?><span class="star">&nbsp;*</span></label></div>
	                <div class="form-input" >
	                    <?php 
	                    
	                    $user_rolllist = array();
						$user_rolllist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_CLUB'));
						$user_rolllist = array_merge($user_rolllist, $this->clubs);
	
						echo JHTML::_('select.genericlist', $user_rolllist, 'club', 'onchange="getshows(this.value);"', 'value', 'text', '');
									
	                    ?>
	                </div>
	                <div class="clr"></div>
	            </div>
            <?php } ?>
	
	        <div class="fieldbg" >
	            <div class="form-label" ><label class="hasTip required" for="official" id="official-lbl"><?php echo $lable; ?><span class="star">&nbsp;*</span></label></div>
	            <div class="form-input" id="changed_officials">
	                <?php 
	                $user_rolllist = array();
					$user_rolllist[] = JHTML::_('select.option', '', $select);
					if($this->officials)
					$user_rolllist = array_merge($user_rolllist, $this->officials);
	
					if ($app->input->getVar('official') == 'organization')
						echo JHTML::_('select.genericlist', $user_rolllist, 'official', 'data-minimum-results-for-search="Infinity" ', 'value', 'text', 1);
					else
						echo JHTML::_('select.genericlist', $user_rolllist, 'official', 'data-minimum-results-for-search="Infinity" ', 'value', 'text', '');
							
	                ?>
	            </div>
	            <div class="clr"></div>
	        </div>
	
	        <div class="fieldbg" >
	            <div class="form-label" ><label class="hasTip required" for="official_users" id="official_users-lbl"><?php echo JText::_('COM_TOES_USER'); ?><span class="star">&nbsp;*</span></label></div>
	            <div class="form-input" >
	                <input type="text" size="30" class="required" value="" id="official_users" name="official_users">
	            </div>
	            <div class="clr"></div>
	        </div>
	
	        <div class="fieldbg" >
	            <div class="form-label" ><label class="hasTip required" for="official_roll" id="official_roll-lbl"><?php echo JText::_('COM_TOES_OFFICIAL_ROLL'); ?><span class="star">&nbsp;*</span></label></div>
	            <div class="form-input" >
                <?php 
	                $user_rolllist = array();
					$user_rolllist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_ROLL'));
					$user_rolllist = array_merge($user_rolllist, $this->official_rolls);
					echo JHTML::_('select.genericlist', $user_rolllist, 'official_roll', 'data-minimum-results-for-search="Infinity" onchange="show_regions(this.value);"', 'value', 'text', '');
				?>
	            </div>
	            <div class="clr"></div>
	        </div>
	
	        <?php  if ($app->input->getVar('official') == 'organization') { ?>
		        <div class="fieldbg" id="regions">
		            <div class="form-label" ><label class="hasTip required" for="region" id="region-lbl"><?php echo JText::_('COM_TOES_REGION'); ?><span class="star">&nbsp;*</span></label></div>
		            <div class="form-input" >
		                <?php 
		                    $region_list[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_REGION'));
		                    $region_list = array_merge($region_list, $this->regions);
		                    echo JHTML::_('select.genericlist', $region_list, 'region', 'data-minimum-results-for-search="Infinity" ', 'value', 'text', '');
		                ?>
		            </div>
		            <div class="clr"></div>
		        </div>
            <?php } ?>
	        <div class="clr"></div>
		    <br/><br/>
	    </div>
		
		    <input type="hidden" name="official_type" value="<?php echo $app->input->getVar('official'); ?>" />
		    <input type="hidden" name="official_user" id="official_user" value="" />
		    <input type="hidden" name="task" value="user.save" />
		    <input type="hidden" name="id" value="" />
		
		    <div class="fieldbg" style="text-align: right" >
		        <input class="save validate button button-4" type="button" name="save" value="Save" onclick="submitform(this.form);" />
		        <input class="cancel button button-red" type="button" name="cancel" value="Cancel" onclick="cancelForm(this.form);"/>
		    </div>
	
		    <?php echo JHtml::_('form.token'); ?>
		    <div class="clr"></div>
	</form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script> 
<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery( "#official_users" ).autocomplete({
		  source: 'index.php?option=com_toes&task=user.getUsers&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#official_user" ).val(ui.item.key);
		  	jQuery( "#official_users" ).val(ui.item.value);
		  }
		});
		jQuery('select').select2();
	});    
</script>
