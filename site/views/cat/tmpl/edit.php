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
JHtml::_('behavior.modal','a.modalbox');
//JHtml::_('formbehavior2.select2','select.inputbox');

$user = JFactory::getUser();
$app = JFactory::getApplication();

if(isset($this->item->cat_id) && $this->item->cat_id) {
	$image_path = JPATH_ROOT.'/media/com_toes/cats/'.$this->item->cat_id.'/';
	$image_url = JUri::root().'/media/com_toes/cats/'.$this->item->cat_id.'/';
} else {
	$image_path = JPATH_ROOT.'/media/com_toes/cats/';
	$image_url = JUri::root().'/media/com_toes/cats/';
}

$params = JComponentHelper::getParams('com_toes');
$isColorHelperHidden = $params->get('hide_color_helper_for_non_admins');
$isAdmin = TOESHelper::isAdmin();
$isEditor = TOESHelper::isEditor();

$readonly = false;

$isLessee = false;

if ($isAdmin && $params->get('show_cat_search_for_admin'))
{
	$readonly = false;
}
else if ($isEditor && $params->get('show_cat_search_for_cateditor'))
{
	$readonly = false;
}
else if (isset($this->item->cat_id))
{
	if (!TOESHelper::is_onlyuser($user->id, $this->item->cat_id))
	{
		//$readonly = true;
		$isOwner = TOESHelper::is_catowner($user->id, $this->item->cat_id);
		$isLessee = TOESHelper::is_catlessee($user->id, $this->item->cat_id);
		$isBreeder = TOESHelper::is_catbreeder($user->id, $this->item->cat_id);

		if (TOESHelper::getCatUsers($this->item->cat_id, 'Owner'))
		{
			if ( !$isOwner && !$isLessee )
				$readonly = true;
		}
		else
		{
			if ( !$isBreeder && !$isLessee )
				$readonly = true;
		}
	}
}


$tica_organization_document_type_ids = $params->get('tica_organization_document_type_ids');
$tica_organization_document_type_ids_array = explode(',',$tica_organization_document_type_ids);

//var_dump($tica_organization_document_type_ids_array);
//var_dump($this->document_type_labels);
$allowed_extensions = trim($params->get('allowed_document_type_ext'));

if($allowed_extensions)
$allowed_extensions_array = explode(',',$allowed_extensions);
else
$allowed_extensions_array = [];

if(isset($this->item->documents) && count($this->item->documents))$have_docs = true;
else
$have_docs = false;


if(isset($this->item->cat_registration_number) && strtolower($this->item->cat_registration_number)!= 'pending' )
$has_reg_number = true;
else
$has_reg_number = false;



$db = JFactory::getDBO();
 
$HHP_breed_abbreviation = 'HHP';
$db->setQuery("select `breed_id` from `#__toes_breed` where `breed_abbreviation` = ".$db->Quote($HHP_breed_abbreviation));
$HHP_breed_id = $db->loadResult();
 

 

?>
<link href="<?php echo JURI::root();?>components/com_toes/assets/css/jquery.fileuploader.min.css" media="all" rel="stylesheet">
<script src="<?php echo JURI::root();?>components/com_toes/assets/js/jquery.fileuploader.js" type="text/javascript"></script>
<link href="<?php echo JURI::root();?>components/com_toes/assets/css/jquery.fileuploader-theme-thumbnails.css" media="all" rel="stylesheet">
 
<?php /* 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
*/ ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script> 
 
 
<script type="text/javascript">
    
    /*Element.implement({
        append: function(newhtml) {
            return this.adopt(new Element('div', {html: newhtml}).getChildren());
        }
    });*/    
    
    var ems_filter = '';

	function add_cat_image() {
		var html = '<br/><input type="file" name="cat_images[]">';
		jQuery('#cat_image_input_block').append(html);
	}
	var HHP_breed_id = '<?php echo $HHP_breed_id?>';
	<?php if(@$this->item->cat_breed){ ?>
	var cat_breed  = '<?php echo @$this->item->cat_breed ?>';
	<?php }else{ ?>
	var cat_breed  = '';
	<?php } ?>
	
	if(cat_breed == HHP_breed_id)
	var cat_is_hhp = true;
	else
	var cat_is_hhp = false;
	jQuery(document).ready(function(){
		
		// sandy hack for select2
		
		jQuery('select.inputbox').select2();
		
		
		jQuery('.cat_image_trash').on('click',function(){
			var parent = jQuery(this).parent();
			var rel = jQuery(this).attr('rel').split(';');
			var data = 'cat_id='+rel[0]+'&cat_img_id='+rel[1];
			var img_html = jQuery('#cat-image-'+rel[1]).html();
			
			new jBox('Confirm',{
		        content: "<?php echo JText::_('COM_TOES_CONFIRM_DELETE_IMAGE'); ?><br/><br/><div style='text-align:center;'>"+img_html+"</div>",
		        width: '500px',
		        cancelButton : "<?php echo JText::_('JNO'); ?>",
		        confirmButton: "<?php echo JText::_('JYES'); ?>",
		        confirm: function() {
					jQuery.ajax({
						url: 'index.php?option=com_toes&task=cat.removeImage',
						data: data
					}).done(function(response){
						if(response === '1') {
							parent.remove();
						} else {
							jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
						}
					}).fail(function(response){
						jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
					});
				}
		    }).open(); 
		});
	});


    function changesuffix()
    {
        var gender = jQuery('#gender').val();
        var suffix = jQuery('#suffix').val();                                
        
        var myElement = jQuery('#suffix_div');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changesuffix&gender='+gender+'&suffix='+suffix+'&tmpl=component',
            type: 'get',
        }).done( function(responseText){
            myElement.html(responseText);
            jQuery('#suffix').select2(); 
        });
    }

    function changetitle()
    {
        var breed = jQuery('#breed').val();
        var gender = jQuery('#gender').val();
        var title = jQuery('#title').val();
        var myElement = jQuery('#title_div');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changetitle&breed='+breed+'&gender='+gender+'&title='+title+'&tmpl=component',
            type: 'get',
        }).done( function(responseText){
            myElement.html(responseText);
            jQuery('#title').select2(); 
        });
    }

    function changebreed()
    {
        var myElement = jQuery('#breed-div');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changebreed&ems_filter='+ems_filter+'&tmpl=component',
            type: 'get',
            async: false,
        }).done( function(responseText){
            myElement.html(responseText);
			jQuery('#breed').select2(); 
       });
    }

    function changehairlength()
    {
        if(jQuery('#new_trait').is(':checked'))
            var breed = 0;
        else
            var breed = jQuery('#breed').val();
        
        var reg_number = jQuery('#registration_number').val();
		if(reg_number.substring(0, 2) == 'LH' || reg_number.substring(0, 2) == 'SH')
            reg_number = reg_number.substring(0, 2);
        else
            reg_number = '';
        var hairlength = jQuery('#cat_hair_length').val();                                
        
        var myElement = jQuery('#cat_hairlength');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changehairlength&breed='+breed+'&hairlength='+hairlength+'&reg_number='+reg_number+'&ems_filter='+ems_filter+'&tmpl=component',
            type: 'get',
        }).done( function(responseText){
            myElement.html(responseText);
			jQuery('#cat_hair_length').select2(); 
        });
    }

    function changecategory()
    {
        if(jQuery('#new_trait').is(':checked'))
            var breed = 0;
        else
            var breed = jQuery('#breed').val();
				
        var category = jQuery('#category').val();
			
        var myElement = jQuery('#cat_category');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changecatlist&breed='+breed+'&category='+category+'&ems_filter='+ems_filter+'&tmpl=component',
            type: 'get',
            async: false,
        }).done( function(responseText){
            myElement.html(responseText);
			jQuery('#category').select2(); 
        });
    }

    function changedivision()
    {
        if(jQuery('#new_trait').is(':checked'))
            var breed = 0;
        else
            var breed = jQuery('#breed').val();
		
        if(jQuery('#new_trait').is(':checked'))
            var category = 0;
        else
            var category = jQuery('#category').val();

        // var category = jQuery('#category').val();
        var division = jQuery('#division').val();
		
        var myElement = jQuery('#cat_division');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changedivisionlist&breed='+breed+'&category='+category+'&division='+division+'&ems_filter='+ems_filter+'&tmpl=component',
            type: 'get',
            async: false,
        }).done( function(responseText){
            myElement.html(responseText);
			jQuery('#division').select2(); 
        });
    }

    function changecolor()
    {
        if(jQuery('#new_trait').is(':checked'))
            var breed = 0;
        else
            var breed = jQuery('#breed').val();

        var category = jQuery('#category').val();
        var division = jQuery('#division').val();
        var color = jQuery('#color').val();
		
        var myElement = jQuery('#cat_color');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changecolorlist&breed='+breed+'&category='+category+'&division='+division+'&color='+color+'&ems_filter='+ems_filter+'&tmpl=component',
            type: 'get',
            async: false,
        }).done( function(responseText){
            myElement.html(responseText);
			jQuery('#color').select2(); 
        });
    }
    
    function check_cat_division() {
        if(jQuery('#new_trait').is(':checked'))
            var breed = 0;
        else
            var breed = jQuery('#breed').val();
				
        var category = jQuery('#category').val();
        var color = jQuery('#color').val();
			
        var myElement = jQuery('#cat_category');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changecatlist&breed='+breed+'&category='+category+'&ems_filter='+ems_filter+'&color='+color+'&tmpl=component',
            type: 'get',
            async: false,
        }).done( function(responseText){
            myElement.html(responseText);
			jQuery('#category').select2(); 
        });
        
        var category = jQuery('#category').val();
        var division = jQuery('#division').val();

        var myElement = jQuery('#cat_division');
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.changedivisionlist&breed='+breed+'&category='+category+'&division='+division+'&ems_filter='+ems_filter+'&color='+color+'&tmpl=component',
            type: 'get',
            async: false,
        }).done( function(responseText){
            myElement.html(responseText);
			jQuery('#division').select2(); 
        });
        changecolor();
    }

    function addusername_owner()
    {
        var c=jQuery('#countusername_owner').val();
        var d=parseInt(c)+parseInt(1);
		
        var crosscheckusername = jQuery('#crosscheckusername').val();	
        if(crosscheckusername != '')
        {	
            var curval=jQuery('#crosscheckusername').val();	
            if(curval=='' || curval=="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>")
			return;
			  
            flag = false;
            jQuery('input[name^=username_owner]').each(function(){
                if(curval == jQuery(this).val())
                {
                    alert("<?php echo JText::_('USER_IS_ALREADY_PRESENT') ?>");
                    flag = true;
                }
            });
			
            if(flag == false)
            {
                var username = jQuery('#crosscheckusername').val().split('(');
                username = username[1].split(')');
                username = username[0];
                var str='<label class="addedusername_owner" id="subusername_owner'+c+'">'+curval+'<span class="username_ownerRemove" onclick="removeusername_owner('+c+');">x</span>'+'</label>';

                jQuery('#username_ownerplace').html(jQuery('#username_ownerplace').html()+str);

                jQuery('#innerusername_owner').append('<input type="hidden" value="'+username+'" name="username_owner[]" id="username_ownerdata'+c+'"/>');
                jQuery('#countusername_owner').val(d);

                var cat_owner =jQuery('#cat_owner').val();
                if(!cat_owner)
                {
                    jQuery.ajax({
                        url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+username,
                        type: 'post',
                    }).done( function(responseText){
	                    if(responseText != '0')
	                        jQuery('#cat_owner').val(responseText);
                    });
                }
                //jQuery('#username').val("<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>");
                jQuery('#username').val('');
                jQuery('#username').css('color',"#999");
            }
        }
        else
        {
            alert("<?php echo JText::_('COM_TOES_NONREGISTERED_USER_ALERT'); ?>");
        }
		
        jQuery('#crosscheckusername').val('');	
    }

    function removeusername_owner(id)
    {
        var child = jQuery('#username_ownerdata'+id);
        var parent = jQuery('#innerusername_owner');
        child.remove();
			  
        var child = jQuery('#subusername_owner'+id);
        var parent = jQuery('#username_ownerplace');
        child.remove();
    }

    function addusername_breeder()
    {
        var c=jQuery('#countusername_breeder').val();
        var d=parseInt(c)+parseInt(1);
		
        var crosscheckusername = jQuery('#crosscheckusername').val();	
        if(crosscheckusername != '')
        {	
            var curval=jQuery('#crosscheckusername').val();	
            if(curval=='' || curval=="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>")
			return;
			 
            flag = false;
            jQuery('input[name^=username_breeder]').each(function(){
                if(curval == jQuery(this).val())
                {
                    alert("<?php echo JText::_('USER_IS_ALREADY_PRESENT') ?>");
                    flag = true;
                }
            });
			
            if(flag == false)
            {
                var username = jQuery('#crosscheckusername').val().split('(');
                username = username[1].split(')');
                username = username[0];
                var str='<label class="addedusername_breeder" id="subusername_breeder'+c+'">'+curval+'<span class="username_breederRemove" onclick="removeusername_breeder('+c+');">x</span>'+'</label>';

                jQuery('#username_breederplace').html(jQuery('#username_breederplace').html()+str);

                jQuery('#innerusername_breeder').append('<input type="hidden" value="'+username+'" name="username_breeder[]" id="username_breederdata'+c+'"/>');
                jQuery('#countusername_breeder').val(d);

                var cat_breeder =jQuery('#cat_breeder').val();
                if(!cat_breeder)
                {
                    jQuery.ajax({
                        url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+username,
                        type: 'post',
                    }).done( function(responseText){
                        if(responseText != '0')
                            jQuery('#cat_breeder').val(responseText);
                    });
                }
                //jQuery('#username').val("<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>");
                jQuery('#username').val('');
                jQuery('#username').css('color',"#999");
            }
        }
        else
        {
            alert("<?php echo JText::_('COM_TOES_NONREGISTERED_USER_ALERT'); ?>");
        }
		
        jQuery('#crosscheckusername').val('');		
    }

    function removeusername_breeder(id)
    {
        var child = jQuery('#username_breederdata'+id);
        var parent = jQuery('#innerusername_breeder');
        child.remove();
			  
			  
        var child = jQuery('#subusername_breeder'+id);
        var parent = jQuery('#username_breederplace');
        child.remove();
    }	

    function addusername_agent()
    {
        var c=jQuery('#countusername_agent').val();
        var d=parseInt(c)+parseInt(1);
		
        var crosscheckusername = jQuery('#crosscheckusername').val();	
        if(crosscheckusername != '')
        {	
            var curval=jQuery('#crosscheckusername').val();	
            if(curval=='' || curval=="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>")
			return;
			 
            flag = false;
            jQuery('input[name^=username_agent]').each(function(){
                if(curval == jQuery(this).val())
                {
                    alert("<?php echo JText::_('USER_IS_ALREADY_PRESENT') ?>");
                    flag = true;
                }
            });
			 
            if(flag == false)
            {
                var username = jQuery('#crosscheckusername').val().split('(');
                username = username[1].split(')');
                username = username[0];
                var str='<label class="addedusername_agent" id="subusername_agent'+c+'">'+curval+'<span class="username_agentRemove" onclick="removeusername_agent('+c+');">x</span>'+'</label>';

                jQuery('#username_agentplace').html(jQuery('#username_agentplace').html()+str);

                jQuery('#innerusername_agent').append('<input type="hidden" value="'+username+'" name="username_agent[]" id="username_agentdata'+c+'"/>');
                jQuery('#countusername_agent').val(d);
                //jQuery('#username').val("<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>");
                jQuery('#username').val('');
                jQuery('#username').css('color',"#999");
            }
        }
        else
        {
            alert("<?php echo JText::_('COM_TOES_NONREGISTERED_USER_ALERT'); ?>");
        }
		
        jQuery('#crosscheckusername').val('');		
    }

    function removeusername_agent(id)
    {
        var child = jQuery('#username_agentdata'+id);
        var parent = jQuery('#innerusername_agent');
        child.remove();
			  
        var child = jQuery('#subusername_agent'+id);
        var parent = jQuery('#username_agentplace');
        child.remove();
    }	
		
    function addusername_lessee()
    {
        var c=jQuery('#countusername_lessee').val();
        var d=parseInt(c)+parseInt(1);
		
        var crosscheckusername = jQuery('#crosscheckusername').val();	
        if(crosscheckusername != '')
        {	
            var curval=jQuery('#crosscheckusername').val();	
            if(curval=='' || curval=="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>")
			return false;
			
            flag = false;
            jQuery('input[name^=username_lessee]').each(function(){
                if(curval == jQuery(this).val())
                {
                    alert("<?php echo JText::_('USER_IS_ALREADY_PRESENT') ?>");
                    flag = true;
                }
            });
			
            if(flag == false)
            {
                var username = jQuery('#crosscheckusername').val().split('(');
                username = username[1].split(')');
                username = username[0];

                var str='<label class="addedusername_lessee" id="subusername_lessee'+c+'">'+curval+'<span class="username_lesseeRemove" onclick="removeusername_lessee('+c+');">x</span>'+'</label>';

                jQuery('#username_lesseeplace').html(jQuery('#username_lesseeplace').html()+str);

                jQuery('#innerusername_lessee').append('<input type="hidden" value="'+username+'" name="username_lessee[]" id="username_lesseedata'+c+'"/>');
                jQuery('#countusername_lessee').val(d);

                var cat_lessee =jQuery('#cat_lessee').val();
                if(!cat_lessee)
                {
                    jQuery.ajax({
                        url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+username,
                        type: 'post',
                    }).done( function(responseText){
                        if(responseText != '0')
                            jQuery('#cat_lessee').val(responseText);
                    });
	            }
	            //jQuery('#username').val("<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>");
	            jQuery('#username').val('');
                jQuery('#username').css('color',"#999");
            }
        }
        else
        {
            alert("<?php echo JText::_('COM_TOES_NONREGISTERED_USER_ALERT'); ?>");
        }
		
        jQuery('#crosscheckusername').val('');		
    }

    function removeusername_lessee(id)
    {
        var child = jQuery('#username_lesseedata'+id);
        var parent = jQuery('#innerusername_lessee');
        child.remove();
			  
        var child = jQuery('#subusername_lessee'+id);
        var parent = jQuery('#username_lesseeplace');
        child.remove();
    }	
		
    Array.prototype.in_array = function(p_val) {
        for(var i = 0, l = this.length; i < l; i++) 
        {
            if(this[i] == p_val) 
            {
                return true;
            }
        }
        return false;
    }
	
    function set_date_using_registration_number(date)
    {
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.set_date&date='+date+'&tmpl=component',
            type: 'post',
        }).done( function(responseText){
	        if(responseText != '')
	        {
	            jQuery("#date_of_birth").val(responseText.replace(/\s/g,''));
	            jQuery("#date_of_birth").css('color','#000');
	            var validator = document.formvalidator;
	            validator.validate('#date_of_birth');
	        }
        });

    }
    
    function set_breed()
    {
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.set_breed&tmpl=component',
            type: 'post',
        }).done( function(responseText){
            if(responseText != 0)
            {
                jQuery("#breed-div").html(responseText);
                checkstatus();
                jQuery("#pedigree").hide();
            }
            else
            {
                jQuery("#pedigree").show();	
            }
        });
    }
    
    function unset_breed()
    {
        var breed = jQuery('#breed').val();
		
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.unset_breed&breed='+breed+'&tmpl=component',
            type: 'post',
        }).done( function(responseText){
            if(responseText != 0)
            {
                jQuery("#breed-div").html(responseText);
                jQuery('#breed').select2();
                checkstatus();
            }
        });
    }
    
    function set_hair_length(rgn_prefix)
    {
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.set_hair_length&rgn_prefix='+rgn_prefix+'&tmpl=component',
            type: 'post',
        }).done( function(responseText){
            if(responseText != 0)
            {
                jQuery("#cat_hair_length").val(responseText);
            }
        });
    }
    
    var prev_reg_number = '';
    function get_prefilled_form(registration_number)
    {
        if(prev_reg_number == registration_number)
            return;
        else
            prev_reg_number = registration_number;
        
        if("<?php echo @$this->item->cat_id ?>")
        {
            if(registration_number == "<?php echo @$this->item->cat_registration_number ?>")
            {
                return;
            }
        }
        
        jQuery.ajax({
            url: 'index.php?option=com_toes&task=cat.get_prefilled_form&registration_number='+registration_number+'&tmpl=component',
            type: 'post',
            async: false,
        }).done( function(responseText){
            if(parseInt(responseText) > 0)
            {
                var cat_id = responseText;
                var user_id = "<?php echo $user->id; ?>";

                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=cat.getrelatedusers&tmpl=component',
                    data: 'cat_id='+cat_id,
                    type: 'post',
                    async: false,
                }).done( function(responseText){
                    var user_types = responseText.split(';');

                    var data = '';
                    var users = '';

                    var owners = new Array();
                    var breeders = new Array();
                    var lessees = new Array();
                    var agents = new Array();

                    for(var i=0;i<user_types.length;i++)
                    {
                        data = user_types[i].split(':');
                        switch(data[0])
                        {
                            case 'o':
                                if(data[1])
                                {
                                    users = data[1].split(',');
                                    for(var j=0;j<users.length;j++)
                                        owners.push(users[j]);
                                }
                                break;
                            case 'b':
                                if(data[1])
                                {
                                    users = data[1].split(',');
                                    for(var j=0;j<users.length;j++)
                                        breeders.push(users[j]);
                                }
                                break;
                            case 'l':
                                if(data[1])
                                {
                                    users = data[1].split(',');
                                    for(var j=0;j<users.length;j++)
                                        lessees.push(users[j]);
                                }
                                break;
                            case 'a':
                                if(data[1])
                                {
                                    users = data[1].split(',');
                                    for(var j=0;j<users.length;j++)
                                        agents.push(users[j]);
                                }
                                break;
                        }
                    }

                    if(owners.indexOf(user_id) != -1 || (owners.length == 0 && breeders.indexOf(user_id) != -1))
                    {
                        window.location = "index.php?option=com_toes&view=cat&layout=edit&id="+cat_id;
                        return;
                    }
                    else
                    {
                        jQuery.ajax({
                            url: 'index.php?option=com_toes&task=cat.getcatname&tmpl=component',
                            data: 'cat_id='+cat_id,
                            type: 'post',
                            async: false,
                        }).done( function(responseText){
							new jBox('Confirm',{
						        content: "There is already a cat with this registration number on file : "+responseText+" <br/> Is this the cat you were looking for?",
						        width: '400px',
						        cancelButton : NO_BUTTON,
						        confirmButton: YES_BUTTON,
						        cancel: function() {
                                    prev_reg_number = '';
                                    jQuery('#registration_number').val('');
                                    jQuery('#registration_number').focus();
						        },
						        confirm: function() {
                                    var html = '';
                                    if(owners.length > 0)
                                    {
                                        html += "Request the current owner to link you to this cat as <br/><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=owner&cat_id="+cat_id+"&user_id="+user_id+"&users="+owners.join(',')+"'>Owner</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=lessee&cat_id="+cat_id+"&user_id="+user_id+"&users="+owners.join(',')+"'>Lessee</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=breeder&cat_id="+cat_id+"&user_id="+user_id+"&users="+owners.join(',')+"'>Breeder</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=agent&cat_id="+cat_id+"&user_id="+user_id+"&users="+owners.join(',')+"'>Agent</a><br/>";
                                    }
                                    else if(lessees.length > 0)
                                    {
                                        html += "Request the current lessee to link you to this cat as <br/><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=owner&cat_id="+cat_id+"&user_id="+user_id+"&users="+lessees.join(',')+"'>Owner</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=lessee&cat_id="+cat_id+"&user_id="+user_id+"&users="+lessees.join(',')+"'>Lessee</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=breeder&cat_id="+cat_id+"&user_id="+user_id+"&users="+lessees.join(',')+"'>Breeder</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=agent&cat_id="+cat_id+"&user_id="+user_id+"&users="+lessees.join(',')+"'>Agent</a><br/>";
                                    }
                                    else if(breeders.length > 0)
                                    {
                                        html += "Request the current breeder to link you to this cat as <br/><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=owner&cat_id="+cat_id+"&user_id="+user_id+"&users="+breeders.join(',')+"'>Owner</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=lessee&cat_id="+cat_id+"&user_id="+user_id+"&users="+breeders.join(',')+"'>Lessee</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=breeder&cat_id="+cat_id+"&user_id="+user_id+"&users="+breeders.join(',')+"'>Breeder</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=agent&cat_id="+cat_id+"&user_id="+user_id+"&users="+breeders.join(',')+"'>Agent</a><br/>";
                                    }
                                    else if(agents.length > 0)
                                    {
                                        html += "Request the current agent to link you to this cat as <br/><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=owner&cat_id="+cat_id+"&user_id="+user_id+"&users="+agents.join(',')+"'>Owner</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=lessee&cat_id="+cat_id+"&user_id="+user_id+"&users="+agents.join(',')+"'>Lessee</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=breeder&cat_id="+cat_id+"&user_id="+user_id+"&users="+agents.join(',')+"'>Breeder</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.requestPermission&permission=agent&cat_id="+cat_id+"&user_id="+user_id+"&users="+agents.join(',')+"'>Agent</a><br/>";
                                    }
                                    else
                                    {
                                        html += "Link me to this cat as <br/><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.linkCat&permission=owner&cat_id="+cat_id+"&user_id="+user_id+"'>Owner</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.linkCat&permission=lessee&cat_id="+cat_id+"&user_id="+user_id+"'>Lessee</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.linkCat&permission=breeder&cat_id="+cat_id+"&user_id="+user_id+"'>Breeder</a><br/>";
                                        html += "<a href='index.php?option=com_toes&task=cat.linkCat&permission=agent&cat_id="+cat_id+"&user_id="+user_id+"'>Agent</a><br/>";
                                    }
                                    
									new jBox('Confirm',{
								        content: html,
								        width: '400px',
								        confirmButton: 'Ok',
								        confirm: function() {
                                            jQuery('#registration_number').val('');
                                            jQuery('#registration_number').focus();
                                            prev_reg_number = '';
								        }
							     	}).open();
                                }
                            }).open();
                        });
                    }
                });
            }	
            else
            {
                if(registration_number.substring(0, 2) == 'LH' || registration_number.substring(0, 2) == 'SH')
                {
                    set_breed();
                }
                else
                {
                    unset_breed();
                    jQuery("#pedigree").show();
                }
            }
        });
    }

    function cancelForm(form)
    {
        form.task.value = 'cat.cancel';
        form.submit();
    }
    
    var DateDiff = {

        inDays: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return parseInt((t2-t1)/(24*3600*1000));
        },

        inWeeks: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return parseInt((t2-t1)/(24*3600*1000*7));
        },

        inMonths: function(d1, d2) {
            var d1Y = d1.getFullYear();
            var d2Y = d2.getFullYear();
            var d1M = d1.getMonth();
            var d2M = d2.getMonth();

            return (d2M+12*d2Y)-(d1M+12*d1Y);
        },

        inYears: function(d1, d2) {
            return d2.getFullYear()-d1.getFullYear();
        }
    }
    var breed_status = '';
    
    jQuery(document).ready(function(){
        var today = <?php echo date('Y') . date('m') . date('d') ?>;
        
        document.formvalidator.setHandler('date', function (value) {
            var exp =/^(19|20)\d\d[- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/;

            var dob = value.split('-');
            var dob = dob[0]+dob[1]+dob[2];
            
            var d1 = new Date(value);
            var d2 = new Date();    
            
            if(DateDiff.inMonths(d1, d2) < 8)
            {
                jQuery("#title").val('1');
                jQuery("#suffix").val('1');

                jQuery("#field_title").hide();
                jQuery("#field_suffix").hide();
            }
            else
            {
                if(breed_status == 'reg_only')
                {
                    jQuery("#field_title").hide();
                    jQuery("#field_suffix").hide();
                }
                else
                {
                    jQuery("#field_title").show();
                    jQuery("#field_suffix").show();
                }
            }                

            if(new RegExp(exp).test(value))
            {
                if(dob > today)
                {
                    return false
                }
                return true;
            }
            else
                return false;
        });
        
        document.formvalidator.setHandler('regnumber', function (value) {
            jQuery('#breed').prop('disabled',1);
            var registration_number = value.toUpperCase();
            
            if(registration_number == 'PENDING')						
            {
                //####### as per ticket no 146
                jQuery("#prefix").val('1');
                jQuery("#title").val('1');
                jQuery("#suffix").val('1');
					
                jQuery("#field_prefix").hide();
                jQuery("#field_title").hide();
                jQuery("#field_suffix").hide();
		
                unset_breed();
                jQuery("#pedigree").show();
                
                jQuery('#breed').prop('disabled',0);
                return true; 
            }
			
            if(registration_number == '-')						
            {
                unset_breed();
                jQuery("#pedigree").show();
                jQuery('#breed').prop('disabled',0);
                return true; 
            }
				
            registration_number = registration_number.replace(/\s{1,}/g, '');
			
			var ntype3 = new Array();
			var ntype2 = new Array();
			<?php if($this->reg_number_formats) :
				foreach($this->reg_number_formats as $format) : ?>
				<?php if($format->type == 2) : ?>
					ntype2.push("<?php echo $format->regformat; ?>");
				<?php elseif($format->type == 3) : ?>
					ntype3.push("<?php echo $format->regformat; ?>");
				<?php endif; ?>
			<?php endforeach; 
			endif; ?>
			
			console.log(ntype3);
			console.log(ntype2);
			/*
            var ntype3 = ['SBT', 'SBV', 'SBP', 'AOP', 'BOP', 'COP', '01T', '02T', '01V', '02V', '03V', 'A1P', 'B1P', 'C1P', 'A2P', 'B2P', 'C2P', 'A3P', 'B3P', 'C3P', 'SBN', 'SBS', '01P', '02P', '03P', '03T']; 
            // var ntype2 = ['SH', 'LH', '03']; 
            var ntype2 = ['SH', 'LH'];
            */ 
            
            if(ntype2.in_array(registration_number.substring(0, 2)))
            {
                if(registration_number.charAt(2) != ' ')
                {
                    registration_number = registration_number.substring(0, 2)+' '+registration_number.substring(2);
                }
                if(registration_number.charAt(9) != ' ')
                {
                    registration_number = registration_number.substring(0, 9)+' '+registration_number.substring(9);
                }
                jQuery('#registration_number').val( registration_number);
                if(!isNaN(registration_number.substring(3,9)))	
                {
                    if(registration_number.substring(3,5) > 12)
                    {
                        //jQuery('#registration_number').val('');

                        //####### as per ticket no 146
                        jQuery("#prefix").val('1');
                        jQuery("#title").val('1');
                        jQuery("#suffix").val('1');

                        jQuery("#field_prefix").hide();
                        jQuery("#field_title").hide();
                        jQuery("#field_suffix").hide();

                        jQuery('#breed').prop('disabled',0);
                        return false;
                    }
                    if(registration_number.substring(5,7) > 31)
                    {
                        //jQuery('#registration_number').val('');

                        //####### as per ticket no 146
                        jQuery("#prefix").val('1');
                        jQuery("#title").val('1');
                        jQuery("#suffix").val('1');

                        jQuery("#field_prefix").hide();
                        jQuery("#field_title").hide();
                        jQuery("#field_suffix").hide();

                        jQuery('#breed').prop('disabled',0);
                        return false;
                    }
                    if(registration_number.substring(7,9) > 99)
                    {
                        //jQuery('#registration_number').val('');

                        //####### as per ticket no 146
                        jQuery("#prefix").val('1');
                        jQuery("#title").val('1');
                        jQuery("#suffix").val('1');

                        jQuery("#field_prefix").hide();
                        jQuery("#field_title").hide();
                        jQuery("#field_suffix").hide();

                        jQuery('#breed').prop('disabled',0);
                        return false;
                    }
                }
                else
                {
                    jQuery('#breed').prop('disabled',0);
                    return false;
                }

                if(isNaN(registration_number.substring(10,14)))	
                {
                    //####### as per ticket no 146
                    jQuery("#prefix").val('1');
                    jQuery("#title").val('1');
                    jQuery("#suffix").val('1');

                    jQuery("#field_prefix").hide();
                    jQuery("#field_title").hide();
                    jQuery("#field_suffix").hide();

                    jQuery('#breed').prop('disabled',0);
                    return false;
                }	

                if(registration_number)	
                {
                    set_date_using_registration_number(registration_number.substring(3,9));
                }	

                //######### spider hack ###########
                jQuery('#new_trait_section').show();

            }
            else if(ntype3.in_array(registration_number.substring(0, 3)))
            {
                if(registration_number.charAt(3) != ' ')
                {
                    registration_number = registration_number.substring(0, 3)+' '+registration_number.substring(3);
                }
                if(registration_number.charAt(10) != ' ')
                {
                    registration_number = registration_number.substring(0, 10)+' '+registration_number.substring(10);
                }
                jQuery('#registration_number').val( registration_number);
                if(!isNaN(jQuery('#registration_number').val().substring(4,10)))	
                {
                    if(registration_number.substring(4,6) > 12)
                    {
                        //####### as per ticket no 146
                        jQuery("#prefix").val('1');
                        jQuery("#title").val('1');
                        jQuery("#suffix").val('1');

                        jQuery("#field_prefix").hide();
                        jQuery("#field_title").hide();
                        jQuery("#field_suffix").hide();

                        jQuery('#breed').prop('disabled',0);
                        return false;
                    }
                    if(registration_number.substring(6,8) > 31)
                    {
                        //####### as per ticket no 146
                        jQuery("#prefix").val('1');
                        jQuery("#title").val('1');
                        jQuery("#suffix").val('1');

                        jQuery("#field_prefix").hide();
                        jQuery("#field_title").hide();
                        jQuery("#field_suffix").hide();

                        jQuery('#breed').prop('disabled',0);
                        return false;
                    }
                    if(registration_number.substring(8,10) > 99)
                    {
                        //jQuery('#registration_number').val('');

                        //####### as per ticket no 146
                        jQuery("#prefix").val('1');
                        jQuery("#title").val('1');
                        jQuery("#suffix").val('1');

                        jQuery("#field_prefix").hide();
                        jQuery("#field_title").hide();
                        jQuery("#field_suffix").hide();

                        jQuery('#breed').prop('disabled',0);
                        return false;
                    }
                }
                else
                {
                    jQuery('#breed').prop('disabled',0);
                    return false;
                }


                if(isNaN(registration_number.substring(11,14)))	
                {
                    //jQuery('#registration_number').val('');

                    //####### as per ticket no 146
                    jQuery("#prefix").val('1');
                    jQuery("#title").val('1');
                    jQuery("#suffix").val('1');

                    jQuery("#field_prefix").hide();
                    jQuery("#field_title").hide();
                    jQuery("#field_suffix").hide();

                    jQuery('#breed').prop('disabled',0);
                    return false;
                }	

                if(registration_number)	
                {
                    set_date_using_registration_number(registration_number.substring(4,10));
                }	

                //######### spider hack ###########
                jQuery('#new_trait_section').show();
            }
            else
            {
                //jQuery('#registration_number').val('');

                //####### as per ticket no 146
                jQuery("#prefix").val('1');
                jQuery("#title").val('1');
                jQuery("#suffix").val('1');

                jQuery("#field_prefix").hide();
                jQuery("#field_title").hide();
                jQuery("#field_suffix").hide();

                jQuery('#breed').prop('disabled',0);
                return false;
            }

            if(registration_number.length > 14)
            {
                registration_number = registration_number.substring(0, 14);
            }
            if(registration_number.length < 14)
            {
                //####### as per ticket no 146
                jQuery("#prefix").val('1');
                jQuery("#title").val('1');
                jQuery("#suffix").val('1');

                jQuery("#field_prefix").hide();
                jQuery("#field_title").hide();
                jQuery("#field_suffix").hide();

                jQuery('#breed').prop('disabled',0);
                return false;
            }
            
            jQuery('#registration_number').val( registration_number);
            get_prefilled_form(registration_number);
            jQuery('#breed').prop('disabled',0);
            return true; 
        });
    });
    
    function check_hhp()
    {
        var breed = jQuery('#breed').val();
         
        jQuery.ajax({url: 'index.php?option=com_toes&task=cat.set_breed&breed='+breed+'&tmpl=component',
            type: 'get',
        }).done(function(responseText){
			jQuery()
			console.log(responseText);
            if(parseInt(responseText) == 1)
            {
				//isHHP = true;
                jQuery("#pedigree").hide();
                jQuery("#add-breeder-div").hide();
                jQuery("#add-breeder-span").hide();
                jQuery("#reg-breeder-div").hide();
                jQuery("#cat_breeder").val('N/A');
                isHHP = true;
				jQuery('.nonHHP').hide();	
				//jQuery('div.div_registration_number').hide();	
                
            }
            else
            {
				//isHHP = false;
                jQuery("#pedigree").show();
                jQuery("#add-breeder-div").show();
                jQuery("#add-breeder-span").show();
                jQuery("#reg-breeder-div").show();
                isHHP = false;
				jQuery('.nonHHP').show();	
				//jQuery('div.div_registration_number').show();	
            }
        });
    }
    
    function checkstatus()
    {
        var breed = jQuery('#breed').val();
        jQuery.ajax({url: 'index.php?option=com_toes&task=cat.checkbreedstatus&breed='+breed+'&ems_filter='+ems_filter+'&tmpl=component',
            type: 'get',
        }).done(function(responseText){
            if(parseInt(responseText) == 1)
            {
                breed_status = 'reg_only';
                // as per ticket no 112
                jQuery("#new_trait").prop('checked',1);
                jQuery('#new_trait_section').show();
                
                // jQuery("#new_trait").prop('checked','checked');
                jQuery("#prefix").val('1');
                jQuery("#title").val('1');
                jQuery("#suffix").val('1');
				
                jQuery("#field_prefix").hide();
                jQuery("#field_title").hide();
                jQuery("#field_suffix").hide();
            }
            else
            {
                breed_status = '';
                var d1 = new Date(jQuery("#date_of_birth").val());
                var d2 = new Date();    

                if(DateDiff.inMonths(d1, d2) < 8)
                {
                    jQuery("#title").val('1');
                    jQuery("#suffix").val('1');

                    jQuery("#field_title").hide();
                    jQuery("#field_suffix").hide();
                }
                else
                {
                    jQuery("#field_title").show();
                    jQuery("#field_suffix").show();
                }                
                
                jQuery('#new_trait_section').show();
                /*jQuery("#new_trait").prop('checked',0);
                
                jQuery("#prefix").val('');
                jQuery("#title").val('');
                jQuery("#suffix").val('');
				 */

                jQuery("#field_prefix").show();
            }
			
            changehairlength();
            check_hhp();
            changecategory();
            changedivision();
            changecolor();
            changetitle();
            //var validator = document.formvalidator;
            //validator.validate('#breed');
        });
    }
    
    

</script>

<?php
$document = JFactory::getDocument();

$document->addStyleSheet('components/com_toes/assets/calendar/css/jscal2.css');
$document->addStyleSheet('components/com_toes/assets/calendar/css/border-radius.css');
$document->addStyleSheet('components/com_toes/assets/calendar/css/win2k/win2k.css');

$document->addScript('components/com_toes/assets/calendar/js/jscal2.js');
$document->addScript('components/com_toes/assets/calendar/js/unicode-letter.js');
$document->addScript('components/com_toes/assets/calendar/js/lang/nl.js');
$document->addScript('components/com_toes/assets/calendar/js/lang/en.js');
?>

<style type="text/css">
</style>

<div id="toes">
<form id="adminForm"  name="adminForm" action="<?php echo JRoute::_('index.php?option=com_toes&views=cats'); ?>" class="form-validate" method="post" enctype="multipart/form-data"> 

	<?php
	if (!$readonly)
	{
	?>
		<div class="action-buttons" >
			<input class="button button-4 save validate" type="button" onclick="validate_catform(this.form)" name="save" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
			<input class="button button-red cancel" type="button" name="cancel" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" onclick="cancelForm(this.form);"/>
		</div>
		<div class="clr"></div>
	<?php
	}
	?>
	<div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_ADD_EDIT_CAT'); ?></div>
        </div>
        <div class="clr"></div>
    </div>
	<div class="seconouter block-rg_number">
		 <div class="fieldbg" >
			<div class="form-label" >
				<label class="required" for="breed" id="breed-lbl">
					<?php echo JText::_('COM_TOES_BREED'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" id="breed-div">
				<select name="breed" id="breed" onchange="checkstatus();" class="inputbox required" >
					<?php
					foreach ($this->breed as $b)
					{
						$sel = '';
						if (@$this->item->cat_breed == $b->value)
							$sel = 'selected="selected" ';

						if ($readonly)
							$sel .= 'disabled="disabled" ';

						echo '<option value="' . $b->value . '" ' . $sel . '>' . $b->text . '</option>';
					}
					?>
				</select>
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_BREED_HELP'); ?>"></span>
			<div class="clr"></div>
		</div>
	</div>

    
    
    <div id="document_type_block" style="display:none">
		<div class="fieldbg" >
            <div class="form-label" >
                <label for="type_of_document" id="type_of_document-lbl">
					<?php echo JText::_('COM_TOES_DOCUMENT_TYPE'); ?>
                </label>
            </div>
            <div class="form-input" >
				<?php echo $this->document_types?>
            </div> 
        </div> 
		<div class="clr"></div>
	</div>
    
    <div class="clr"></div>
    <div class="fistouter" >
		<div>		 
		<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_QUICK_INTRO_ELIGIBILITY');?>
		</div>
    </div>
    <div class="clr"></div>
    
	
	 
    <div class="fistouter" >
		 
		 
			
		 
		<input type="radio" name="has_tica_reg_number" 
		class="has_tica_reg_number_option" id="has_tica_reg_number_1" value="1" <?php if(!$have_docs && (@$this->item->cat_breed != $HHP_breed_id) )echo 'checked';else echo '';?> >
		<?php echo JText::_('COM_TOES_HAS_TICA_REGISTRATION_NUMBER')?> 
		<br/>
		<input type="radio" name="has_tica_reg_number" 
		class="has_tica_reg_number_option" id="has_tica_reg_number_0" value="0" <?php if($have_docs ||(@$this->item->cat_breed == $HHP_breed_id) )echo 'checked';else echo '';?> >
		<?php echo JText::_('COM_TOES_DOES_NOT_YET_HAVE_TICA_REGISTRATION_NUMBER')?>	
		 
		 
    </div>
    <div class="clr"></div>
    
    
    
    
    
    
    
    <div class="fistouter reginfo" <?php echo (isset($this->item->cat_id) && isset($this->item->documents) && count($this->item->documents)) ? 'style="display:block"':'style="display:none"';?>  >
		<div class="fieldblank" >
			<div class="block-title"><?php echo JText::_('COM_TOES_REGISTRATION_INFORMATION'); ?></div>
		</div>
		<div class="fieldbg" >
			 <div class="registration_number_explanation">
				<?php echo JText::_('COM_TOES_REGISTRATION_NUMBER_EXPLANATION_TEXT');?>
			</div>	
		</div>
	</div>
	
	<div class="fieldbg div_registration_number">
		<div class="form-label" >
			<label class="required" for="registration_number" id="registration_number-lbl">
				<?php echo JText::_('COM_TOES_REGISTRATION_NUMBER'); ?>
				<span class="star">&nbsp;*</span>
			</label>
		</div>
		<div class="form-input" >
			<input type="text" size="25" value="<?php echo (@$this->item->cat_registration_number) ? @$this->item->cat_registration_number : 'PENDING'; //@$this->item->cat_registration_number       ?>" onblur="if(this.value==''){this.value='PENDING';}" onfocus="if(this.value=='PENDING'){ this.value='';}" id="registration_number" name="registration_number" <?php
				if ($readonly)
				{
				?> class="readonly" readonly="readonly" <?php
				}
				else
				{
				?> aria-required="true" required="required" class="required validate-regnumber" <?php } ?> style="text-transform:uppercase">
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_REGISTRATION_NUMBER_HELP'); ?>"></span>
		</div>
		<div class="clr"></div>
	</div> 
	<div id="document_container">
		<?php if(isset($this->item->documents) && count($this->item->documents)){
				
			foreach($this->item->documents as $d){
				$file_type = mime_content_type(JPATH_BASE.DS.$d->cat_document_file_name);
				$file_size = filesize(JPATH_BASE.DS.$d->cat_document_file_name); 
				//$tica_organization_document_type_ids_array
				 
				?>
				
				<div id="dt_<?php echo $d->cat_document_id?>" class="dtdiv" style="clear:both">
				 
						<?php echo JText::_($d->allowed_registration_document_title_language_constant)?>
						<div class="del_document_type" style="text-align:left;display:inline" data-id="<?php echo $d->cat_document_registration_document_type_id?>">
							<a>
								<i style="color:red" class="fa fa-times-circle" aria-hidden="true" title="<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_DELETE_THIS_DOCUMENT');?>"></i>
							</a>
						</div>

						
						<?php /*
						<h3 class="document_type"><?php echo JText::_($this->document_type->allowed_registration_document_name_language_constant)?></h3>
						*/ ?> 
						<div class="docrow" <?php if(in_array($d->cat_document_registration_document_type_id,$tica_organization_document_type_ids_array))echo 'style="display:none"';?> >
							<div class="form-label" ><span style=""><?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_RECOGNIZED_ORGANIZATION_LABEL');?></span></div>
							<div class="organization form-input" >
								<select name="organization_<?php echo $d->cat_document_registration_document_type_id?>" id="organization_<?php echo $d->cat_document_registration_document_type_id?>" 
								 class="inputbox organization_select required" >
									<?php									
									foreach ($this->organizations as $o)
									{
										$selected = '';
										if($o->value == $d->cat_document_registration_document_organization_id )$selected = 'selected';
										echo '<option value="' . $o->value . '" ' . $selected . '>' . $o->text . '</option>';
									}
									?>
								</select>
							</div>
						</div>
					<div class="clr"></div>
					<div class="form-label" ><span style="">&nbsp;</span></div>
					<div class="document form-input" >
						<input type="file" name="document_<?php echo $d->cat_document_registration_document_type_id?>" id="document_<?php echo $d->cat_document_registration_document_type_id?>" 
				value="<?php echo  basename($d->cat_document_file_name)?>"		class="document_file required converted">
					</div>
					<input type="hidden" class="document_type_id" name="document_type_id[]" value="<?php echo $d->cat_document_registration_document_type_id?>"/>
					<div class="clr"></div>
				</div>
				<script>
				jQuery(document).on('ready',function(){
				jQuery('div#dt_<?php echo $d->cat_document_id?> .document_file').fileuploader({fileMaxSize:5,limit:1,enableApi: true, extensions:<?php echo json_encode($allowed_extensions_array);?>,
				files :[{name :'<?php echo trim(basename($d->cat_document_file_name))?>',type:'<?php echo trim($file_type)?>',
				size: '<?php echo $file_size?>',file:'<?php echo JURI::root().trim($d->cat_document_file_name)?>' }]	});
				});
			</script>	
				<?php 
			}?>
			<div class="clr"></div>
			<a href="javascript:void(null)" class="add_document_type_btn">
				<i class="fa fa-upload" title="<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_ADD_ANOTHER_DOCUMENT');?>"></i>
				<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_ADD_ANOTHER_DOCUMENT');?>
			</a>
 	
		<?php	}else{
		?>
		<div class="clr"></div>
		
		<div class="fieldbg" >
            <div class="form-label" >
                <label for="type_of_document" id="type_of_document-lbl">
					<?php echo JText::_('COM_TOES_DOCUMENT_TYPE'); ?>
                </label>
            </div>
            <div class="form-input" >
				<?php echo $this->document_types?>
            </div> 
        </div> 
        <?php } ?>
		<div class="clr"></div>
		
			 
	</div>
	
	
	
	
	
	
	
	
	
	
	<div class="fistouter">
		<div class="fieldblank" >
			<div class="block-title"><?php echo JText::_('COM_TOES_CAT_INFORMATION'); ?></div>
		</div>
		<div class="clr"></div>
	</div>
	

	<div class="seconouter block-rg_number">
        
              
       

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="date_of_birth" id="date_of_birth-lbl">
					<?php echo JText::_('COM_TOES_DATE_OF_BIRTH'); ?>
                    <span class="star">&nbsp;*</span>
                </label>
            </div>
            <div class="form-input" >
                <input <?php echo (!@$this->item->cat_date_of_birth) ? 'style="color: #999;"' : '' ?> type="text" name="date_of_birth" id="date_of_birth" value="<?php echo isset($this->item->cat_date_of_birth) ? $this->item->cat_date_of_birth : 'YYYY-MM-DD' ?>" size="25" 
				  <?php if ($readonly){?>  class="readonly" readonly="readonly"  <?php } else { ?> class="required validate-date" <?php } ?>
				onfocus="if(this.value=='YYYY-MM-DD'){ this.value='';this.style.color='#000';}" onblur="if(this.value==''){this.value='YYYY-MM-DD';this.style.color='#999';}" />
                <i style="cursor:pointer;" class="fa fa-calendar" title="Click To Select Date" name="date_of_birth_selector" id="date_of_birth_selector"></i>
            </div>
            <span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_DATE_OF_BIRTH_HELP'); ?>"></span>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <div class="form-label" >
                <label class="required" for="gender" id="gender-lbl">
					<?php echo JText::_('COM_TOES_GENDER'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" >
				<select name="gender" id="gender" class="inputbox required" data-minimum-results-for-search="Infinity" onchange="changesuffix();changetitle();" >
					<?php
					  foreach ($this->gender as $g)
					  {
						  $sel = '';
						  if (@$this->item->cat_gender == $g->value)
							  $sel = 'selected="selected" ';

						  if ($readonly)
							  $sel .= 'disabled="disabled" ';

						  echo '<option value="' . $g->value . '" ' . $sel . ' >' . $g->text . '</option>';
					  }
					?>
				</select>
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_GENDER_HELP'); ?>"></span>
		</div>

		<div class="fieldbg" >
			<div class="form-label" >
				<label  for="cat_id_chip_number" id="cat_id_chip_number-lbl">
					<?php echo JText::_('COM_TOES_CAT_MICROCHIP_NUMBER'); ?>
				</label>
			</div>
			<div class="form-input" >
				<input type="text" size="25" value="<?php echo @$this->item->cat_id_chip_number ?>" id="cat_id_chip_number" name="cat_id_chip_number" >
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_CAT_MICROCHIP_NUMBER_TOOLTIP'); ?>"></span>
		</div>
		
		<div class="fieldbg" style="height:auto;" >
			<div class="form-label" >
				<label id="cat_images-lbl">
					<?php echo JText::_('COM_TOES_CAT_IMAGES'); ?>
				</label>
			</div>
			<div id="cat_image_input_block" class="form-input" >
				<input class="button button-blue" type="button" value="<?php echo JText::_('COM_TOES_ADD_IMAGE');?>" onclick="add_cat_image()" style="width: auto !important;" />
				<br/>
                <input type="file" name="cat_images[]" />
			</div>
			
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_CAT_IMAGES_TOOLTIP'); ?>"></span>
			<div class="clr"></div>
		</div>

		<div class="fieldbg" style="height:auto;" >
			<div class="form-label" >
				<label>&nbsp;</label>
			</div>
			<div class="form-input" >
				<?php if(isset($this->item->cat_images) && $this->item->cat_images){
					foreach ($this->item->cat_images as $image) { ?>
					<div class="img-container">
					<?php if($image->file_name && file_exists($image_path.$image->file_name)) : ?>
						<i rel="<?php echo $this->item->cat_id.';'.$image->cat_img_id;?>" title="Delete Image" class="cat_image_trash icon-trash"></i>
						<a id="cat-image-<?php echo $image->cat_img_id;?>" class="modalbox" href="<?php echo $image_url.$image->file_name;?>">
							<img src="<?php echo TOESImageHelper::processImage($image_url.$image->file_name,100,100); ?>" alt="<?php echo $image->file_name;?>" />
						</a>
					<?php endif; ?>
					</div>
				<?php }
				} ?>
				<div class="clr"></div>
			</div>
		</div>

		<div class="clr"></div>
		<br/>
	</div>

	<div class="fistouter">
		<div class="fieldblank" >
			<div class="block-title"><?php echo JText::_('COM_TOES_COAT'); ?></div>
		</div>
		<div class="clr"></div>
	</div>

	<div class="seconouter block-color">
		<br/>

		<?php if (!$isColorHelperHidden || TOESHelper::isAdmin()): ?>
		<div class="ems-helper-div">
			<label style="display: block;text-align: center;">
				<?php echo JText::_('COM_TOES_EMS_COLOR_HELPER_TITLE'); ?>&nbsp;
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_EMS_COLOR_HELPER_TITLE_TOOLTIP'); ?>"></span>
			</label>
			<label><?php echo JText::_('COM_TOES_FULL_EMS_CODE'); ?></label>&nbsp;
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_FULL_EMS_CODE_TOOLTIP'); ?>"></span>
			<input type="text" name="ems_filter" id="ems_filter" value="" size="15" />
			<br/>
			<span>
				<input id="activate-ems-filter" type="checkbox" name="activate" value="1"/>
				<label><?php echo JText::_('COM_TOES_APPLY_COLOR_HELPER_FILTERS'); ?></label>
			</span>
		</div>
		<?php endif; ?>
		<div style="float:left;">
			<div class="fieldbg" style="height:auto;" id="new_trait_section">
				<div class="form-label" style="background: none; box-shadow: none;">&nbsp;&nbsp;&nbsp;</div>
				<div class="form-input">
					<input type="checkbox" id="new_trait" name="new_trait" <?php if (@$this->item->cat_new_trait)
							  echo 'checked="checked"'; ?> value="1" onclick="changehairlength();changecategory();changedivision();changecolor();" <?php if ($readonly)
						  { ?> class="readonly" readonly="readonly" <?php } ?> />
					<label for="new_trait"><a href="<?php echo JRoute::_(JURI::root().'index.php?option=com_content&view=article&id=1542');?>" target="_blank"><?php echo JText::_('COM_TOES_NEW_TRAIT') ?></a></label>
					
					<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_NEW_TRAIT_HELP'); ?>"></span>
				</div>
			</div>
	
			<div class="fieldbg" >
				<div class="form-label" >
					<label class="required" for="cat_hair_length" id="cat_hair_length-lbl">
						<?php echo JText::_('COM_TOES_HAIRLENGTH'); ?>
						<span class="star">&nbsp;*</span>
					</label>
				</div>
				<div class="form-input" id="cat_hairlength">
					<select name="cat_hair_length" id="cat_hair_length" class="inputbox required" data-minimum-results-for-search="Infinity" >
						<?php
						  foreach ($this->hairlength as $h)
						  {
							  $sel = '';
							  if (@$this->item->cat_hair_length == $h->value)
								  $sel = 'selected="selected" ';
	
							  if ($readonly)
								  $sel .= 'disabled="disabled" ';
	
							  echo '<option value="' . $h->value . '" ' . $sel . ' >' . $h->text . '</option>';
						  }
						?>
					</select>
				</div>
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_HAIRLENGTH_HELP'); ?>"></span>
			</div>
	
			<div class="fieldbg" >
				<div class="form-label" >
					<label class="required" for="category" id="category-lbl">
						<?php echo JText::_('COM_TOES_CATEGORY'); ?>
						<span class="star">&nbsp;*</span>
					</label>
				</div>
				<div class="form-input" id="cat_category">
					<select name="category" id="category" onchange="changedivision();changecolor();" class="inputbox required"  data-minimum-results-for-search="Infinity" >
						<?php
						  foreach ($this->category as $c)
						  {
							  $sel = '';
							  if (@$this->item->cat_category == $c->value)
								  $sel = 'selected="selected" ';
	
							  if ($readonly)
								  $sel .= 'disabled="disabled" ';
	
							  echo '<option value="' . $c->value . '" ' . $sel . ' >' . $c->text . '</option>';
						  }
						?>
					</select>
				</div>
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_CATEGORY_HELP'); ?>"></span>
			</div>
	
			<div class="fieldbg" >
				<div class="form-label" >
					<label class="required" for="division" id="division-lbl">
						<?php echo JText::_('COM_TOES_DIVISION'); ?>
						<span class="star">&nbsp;*</span>
					</label>
				</div>
				<div class="form-input" id="cat_division">
					<select name="division" id="division" onchange="changecolor();" class="inputbox required" >
						<?php
						  foreach ($this->division as $d)
						  {
							  $sel = '';
							  if (@$this->item->cat_division == $d->value)
								  $sel = 'selected="selected" ';
	
							  if ($readonly)
								  $sel .= 'disabled="disabled" ';
	
							  echo '<option value="' . $d->value . '" ' . $sel . ' >' . $d->text . '</option>';
						  }
						?>
					</select>
				</div>
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_DIVISION_HELP'); ?>"></span>
			</div>
	
			<div class="fieldbg" >
				<div class="form-label" >
					<label class="required" for="color" id="color-lbl">
						<?php echo JText::_('COM_TOES_COLOR'); ?>
						<span class="star">&nbsp;*</span>
					</label>
				</div>
				<div class="form-input" id="cat_color">
					<select name="color" id="color" class="inputbox required" onchange="check_cat_division();" >
						<?php
						  foreach ($this->color as $col)
						  {
							  $sel = '';
							  if (@$this->item->cat_color == $col->value)
								  $sel = 'selected="selected" ';
	
							  if ($readonly)
								  $sel .= 'disabled="disabled" ';
	
							  echo '<option value="' . $col->value . '" ' . $sel . ' >' . $col->text . '</option>';
						  }
						?>
					</select>
				</div>
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_COLOR_HELP'); ?>"></span>
				<a href="<?php echo JRoute::_(JURI::root().'index.php?option=com_content&view=article&id=1543');?>" target="_blank"><i class="fa fa-info-circle"></i>&nbsp;Help me enter my cat’s color</a>
			</div>
		</div>
		<div class="clr"></div>
		<br/>
	</div>

	<div class="fistouter">
		<div class="fieldblank" >
			<div class="block-title"><?php echo JText::_('COM_TOES_NAME'); ?></div>
		</div>
		<div class="clr"></div>
	</div>

	<div class="seconouter block-name">
		<br/>
		<div class="fieldbg" id="field_prefix">
			<div class="form-label" >
				<label class="required" for="prefix" id="prefix-lbl">
					<?php echo JText::_('COM_TOES_PREFIX'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" >
				<select name="prefix" id="prefix" class="inputbox required"  data-minimum-results-for-search="Infinity" >
					<?php
					  foreach ($this->prefix as $p)
					  {
						  $sel = '';
						  if (@$this->item->cat_prefix == $p->value)
							  $sel = 'selected="selected" ';

						  if ($readonly)
							  $sel .= 'disabled="disabled" ';

						  echo '<option value="' . $p->value . '" ' . $sel . ' >' . $p->text . '</option>';
					  }
					?>
				</select>
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_PREFIX_HELP'); ?>"></span>
		</div>

		<div class="fieldbg" id="field_title">
			<div class="form-label" >
				<label class="required" for="title" id="title-lbl">
					<?php echo JText::_('COM_TOES_TITLE'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" id="title_div" >
				<select name="title" id="title" class="inputbox required"  data-minimum-results-for-search="Infinity" >
					<?php
					  foreach ($this->title as $t)
					  {
						  $sel = '';
						  if (@$this->item->cat_title && @$this->item->cat_title == $t->value)
							  $sel = 'selected="selected" ';

						  if ($readonly)
							  $sel .= 'disabled="disabled" ';

						  echo '<option value="' . $t->value . '" ' . $sel . ' >' . $t->text . '</option>';
					  }
					?>
				</select>
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_TITLE_HELP'); ?>"></span>
		</div>

		<div class="fieldbg" id="field_suffix">
			<div class="form-label" >
				<label class="required" for="suffix" id="suffix-lbl">
					<?php echo JText::_('COM_TOES_SUFFIX'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" id="suffix_div">
				<select name="suffix" id="suffix" class="inputbox required"  data-minimum-results-for-search="Infinity">
					<?php
					  foreach ($this->suffix as $s)
					  {
						  $sel = '';
						  if (@$this->item->cat_suffix == $s->value)
							  $sel = 'selected="selected" ';

						  if ($readonly)
							  $sel .= 'disabled="disabled" ';

						  echo '<option value="' . $s->value . '" ' . $sel . ' >' . $s->text . '</option>';
					  }
					?>
				</select>
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SUFFIX_HELP'); ?>"></span>
		</div>

		<div class="fieldbg" >
			<div class="form-label" >
				<label class="required" for="name" id="name-lbl">
					<?php echo JText::_('COM_TOES_NAME'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" >
				<input style="width:auto" type="text" size="50" value="<?php echo @$this->item->cat_name ?>" id="name" name="name" <?php if ($readonly){ ?> class="readonly" readonly="readonly" <?php } else { ?> class="required" aria-required="true" required="required" <?php } ?> />
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_NAME_HELP'); ?>"></span>
		</div>

		<div class="fieldbg" >
			<div class="form-label" >
				<label class="required" for="cat_competitive_region" id="cat_competitive_region-lbl">
					<?php echo JText::_('COM_TOES_COMPETITIVE_REGION'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" >
				<select name="cat_competitive_region" id="cat_competitive_region" class="inputbox required"  data-minimum-results-for-search="Infinity" >
					<?php
					  foreach ($this->competitiveregions as $cr)
					  {
						  $sel = '';
						  if(!isset($this->item->cat_competitive_region) && isset($this->user->cb_tica_region) && $this->user->cb_tica_region) {
							if($this->user->cb_tica_region == $cr->competitive_region_abbreviation) {
								$sel = 'selected="selected" ';
							}
						  } else if ($this->item->cat_competitive_region == $cr->value) {
							  $sel = 'selected="selected" ';
						  }

						  if ($readonly)
							  $sel .= 'disabled="disabled" ';

						  echo '<option value="' . $cr->value . '" ' . $sel . ' >' . $cr->text . '</option>';
					  }
					?>
				</select>
			</div>
			<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_COMPETITIVE_REGION_HELP'); ?>"></span>
			<a href="<?php echo JURI::root().'clubs/find-a-club'?>" target="_blank"><i class="fa fa-info-circle"></i>&nbsp;<?php echo JText::_('COM_TOES_TICA_REGION_HELPER'); ?></a>
		</div>

		<div class="clr"></div>
		<br/>
	</div>

	<div id="pedigree" class="nonHHP">
		<div class="fistouter nonHHP">
			<div class="fieldblank" >
				<div class="block-title"><?php echo JText::_('COM_TOES_PEDIGREE'); ?></div>
			</div>
			<div class="clr"></div>
		</div>

		<div class="seconouter block-pedigree nonHHP" >
			<br/>
			<div class="fieldbg">
				<div class="form-label" >
					<label for="sire" id="sire-lbl">
						<?php echo JText::_('COM_TOES_SIRE'); ?>
						<span class="star">&nbsp;*</span>
					</label>
				</div>
				<div class="form-input" >
					<input onblur="checkSire();" style="width:auto;" type="text" size="50" value="<?php echo @$this->item->cat_sire ?>" id="sire" name="sire" <?php if ($readonly)
						  { ?> class="readonly" readonly="readonly" <?php } ?> />
					<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_SIRE_HELP'); ?>"></span>
				</div>
			</div>

			<div class="fieldbg">
				<div class="form-label" >
					<label for="dam" id="dam-lbl">
						<?php echo JText::_('COM_TOES_DAM'); ?>
						<span class="star">&nbsp;*</span>
					</label>
				</div>
				<div class="form-input" >
					<input onblur="checkDam();" style="width:auto;" type="text" size="50" value="<?php echo @$this->item->cat_dam ?>" id="dam" name="dam" <?php if ($readonly)
						  { ?> class="readonly" readonly="readonly" <?php } ?> />
					<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_DAM_HELP'); ?>"></span>
				</div>
			</div>
			<div class="clr"></div>
			<br/>
		</div>
	</div>

	<div class="fistouter">
		<div class="fieldblank" >
			<div class="block-title"><?php echo JText::_('COM_TOES_TICA_REGISGRATIONS'); ?></div>
		</div>
		<div class="clr"></div>
	</div>

	<div class="seconouter block-rg_number">
		<br/>
		<div class="fieldbg" >
			<div class="form-label" >
				<label class="required" for="cat_owner" id="cat_owner-lbl">
					<?php echo JText::_('COM_TOES_REGISTRED_OWNER'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" >
				<input style="width:auto" type="text" size="25" value="<?php echo @$this->item->cat_owner ?>" id="cat_owner" name="cat_owner" <?php if ($readonly) { ?> class="readonly" readonly="readonly" <?php } else { ?> aria-required="true" required="required" class="required" <?php } ?> />
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_REGISTRED_OWNER_HELP'); ?>"></span>
			</div>
			<div class="clr"></div>
		</div>

		<div class="fieldbg" id="reg-breeder-div" >
			<div class="form-label" >
				<label class="required" for="cat_breeder" id="cat_breeder-lbl">
					<?php echo JText::_('COM_TOES_REGISTRED_BREEDER'); ?>
					<span class="star">&nbsp;*</span>
				</label>
			</div>
			<div class="form-input" >
				<input style="width:auto" type="text" size="25" value="<?php echo @$this->item->cat_breeder ?>" id="cat_breeder" name="cat_breeder"  <?php if ($readonly) { ?> class="readonly" readonly="readonly" <?php } else { ?> aria-required="true" required="required" class="required" <?php } ?> />
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_REGISTRED_BREEDER_HELP'); ?>"></span>
			</div>
			<div class="clr"></div>
		</div>

		<div class="fieldbg" >
			<div class="form-label" >
				<label for="cat_lessee" id="cat_lessee-lbl">
					<?php echo JText::_('COM_TOES_REGISTRED_LESSEE'); ?>
				</label>
			</div>
			<div class="form-input" >
	  			<input style="width:auto" type="text" size="25" value="<?php echo @$this->item->cat_lessee ?>" id="cat_lessee" name="cat_lessee" <?php if ($readonly) { ?> class="readonly" readonly="readonly" <?php } else { ?> aria-required="true" required="required" class="required" <?php } ?> />
   				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_REGISTRED_LESSEE_HELP'); ?>"></span>
			</div>
			<div class="clr"></div>
		</div>

		<div class="clr"></div>
		<br/>
	</div>

	<div class="fistouter">
		<div class="fieldblank" >
			<div class="block-title">
				<?php echo JText::_('COM_TOES_PEOPLE'); ?>
			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="seconouter block-people">
		<br/><br/>
		<div>
			<div class="form-input">
				<input type="text" id="username" style="margin-top: 34px;color: #999;width:auto !important;" value="" 
				placeholder="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>"
				<?php /*
				onblur="if(this.value==''){this.value=\"<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>\"; this.style.color='#999';}" 
				onfocus="if(this.value==\"<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_USER'); ?>\"){ this.value='';this.style.color='#000';}" 
				*/?>
				<?php if ($readonly){ ?> class="readonly" readonly="readonly" <?php } ?> />
				<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_USERNAME_SEARCH_HELP'); ?>"></span>
			</div>

			<div class="cat-users-link-div">
				<div class="actions-btns" id="add-owner-div">
					<?php if(!$isLessee):?>
						<input onclick="addusername_owner()" type="button" name="add_owner" value="<?php echo JText::_('COM_TOES_ADD_USER_TO_OWNER') ?>" class="button button-blue" />
					<?php else : ?>
						&nbsp;
					<?php endif;?>
				</div>
				<div class="actions-btns nonHHP" id="add-breeder-div">
					<input onclick="addusername_breeder()" type="button" name="add_breeder" value="<?php echo JText::_('COM_TOES_ADD_USER_TO_BREEDER') ?>" class="button button-blue" />
				</div>
				<div class="actions-btns" id="add-agent-div">
					<input onclick="addusername_agent()" type="button" name="add_agent" value="<?php echo JText::_('COM_TOES_ADD_USER_TO_AGENT') ?>" class="button button-blue" />
				</div>
				<div class="actions-btns" id="add-lessee-div">
					<input onclick="addusername_lessee()" type="button" name="add_lessee" value="<?php echo JText::_('COM_TOES_ADD_USER_TO_LESSEE') ?>" class="button button-blue" />
				</div>
			</div>
			<div class="cat-users-name-div">
				<span id="add-owner-span" class="labels">
					<label class="required" for="owner" id="owner-lbl">
						<?php echo JText::_('COM_TOES_OWNER'); ?>
						<span class="star">&nbsp;*</span>
					</label>
					<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_OWNER_HELP'); ?>"></span>
					<div id="innerusername_owner"></div>
					<div  id="username_ownerplace"></div>
					<input type="hidden" id="countusername_owner" name="countusername_owner" value="1" />
					<div class="clr"></div>
				</span>
				<div class="clr"></div>
				<span id="add-breeder-span"  class="labels nonHHP">
					<label class="required" for="breeder" id="breeder-lbl">
						<?php echo JText::_('COM_TOES_BREEDER'); ?>
						<span class="star">&nbsp;*</span>
					</label>
					<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_BREEDER_HELP'); ?>"></span>
					<div id="innerusername_breeder"></div>
					<div  id="username_breederplace"></div>
					<input type="hidden" id="countusername_breeder" name="countusername_breeder" value="1">
					<div class="clr"></div>
				</span>
				<div class="clr"></div>
				<span id="add-agent-span" class="labels">
					<label class="required" for="agent" id="agent-lbl">
						<?php echo JText::_('COM_TOES_AGENT'); ?>
						<span class="star">&nbsp;*</span>
					</label>
					<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_AGENT_HELP'); ?>"></span>
					<div id="innerusername_agent"></div>
					<div  id="username_agentplace"  ></div>
					<input type="hidden" id="countusername_agent" name="countusername_agent" value="1" />
					<div class="clr"></div>
				</span>
				<div class="clr"></div>
				<span id="add-lessee-span" class="labels">
					<label class="required" for="lessee" id="lessee-lbl">
						<?php echo JText::_('COM_TOES_LESSEE'); ?>
						<span class="star">&nbsp;</span>
					</label>
					<span class="hasTip icon-info-circle" title="<?php echo JText::_('COM_TOES_LESSEE_HELP'); ?>"></span>
					<div id="innerusername_lessee"></div>
					<div id="username_lesseeplace"  ></div>
					<input type="hidden" id="countusername_lessee" name="countusername_lessee" value="1" />
					<div class="clr"></div>
				</span>
			</div>
		</div>
		<div class="clr"></div>
		<br/>
	</div>
	
	<!-- checklist -->
	<?php /*
	<div class="clr"></div>
			<br/>
	<div class="fieldbg" id="reg-breeder-div" >
	<?php 
	 
	if(count($this->document_types_list) >0){?>
	<ul id="checklist_documents">
	<?php 
	$i = 0;
	foreach($this->document_types_list as $dt){
	++$i;
	if($i == 1)continue;
			 
			$checked = '';
			 
	?>
	<li id="checklist_<?php echo $dt->value?>"><?php echo JText::_($dt->text)?> </li>
	<?php } ?>
	</ul>
		
	<?php } ?>
	</div>		
	*/ ?>		
	<!-- -->

	<?php if (!$readonly){ ?>
		<div class="clr"></div>
			<br/>
			<div class="action-buttons" >
				<input class="button button-4 save validate" type="button" onclick="validate_catform(this.form)" name="save" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
				<input class="button button-red cancel" type="button" name="cancel" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" onclick="cancelForm(this.form);"/>
			</div>
			<div class="clr"></div>
	<?php } ?>

	<input type="hidden" name="crosscheckusername" id="crosscheckusername" value="" />
	<input type="hidden" name="sire_r" id="sire_r"
		value="<?php if (isset($this->item->cat_sire_reg_number)) echo $this->item->cat_sire_reg_number; ?>"/>
	<input type="hidden" name="dam_r" id="dam_r"
		value="<?php if (isset($this->item->cat_dam_reg_number)) echo $this->item->cat_dam_reg_number; ?>" />
	<input type="hidden" name="show_id" value="<?php echo $app->input->getInt('show_id', 0); ?>" />
	<input type="hidden" name="task" value="cat.save" />
	<input type="hidden" name="view" value="cats" />
	<input type="hidden" name="id" value="<?php echo @$this->item->cat_id; ?>" />
	<input type="hidden" name="readOnly" id="readOnly" value="<?php echo $readonly ? 1 : 0 ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="document_type_ids" id="document_type_ids" value=""/>
	<div class="clr"></div>
</form>
</div>
<?php //var_dump($this->item);?>
 
<script type="text/javascript">
	
	var isHHP = false;
	
	jQuery(document).on('ready',function(){
		jQuery('select#breed').on('change',function(){
		if(jQuery(this).val() == '24'){		 
		isHHP = true;
		cat_is_hhp = true;
		jQuery('.nonHHP').hide();	
		jQuery('#add-breeder-div').hide();	
		//jQuery('div.div_registration_number').hide();	
		}else{
		isHHP = false;
		cat_is_hhp = false;
		jQuery('.nonHHP').show();	
		jQuery('#add-breeder-div').show();	
		//jQuery('div.div_registration_number').show();	
		}
	});
		
	});
	
	 
	var tica_organization_document_type_ids_array = new Array;
	<?php if(count($tica_organization_document_type_ids_array)){?>
	<?php foreach($tica_organization_document_type_ids_array as $arr){?>
	tica_organization_document_type_ids_array.push('<?php echo $arr?>');		
	<?php } ?>
	<?php } ?>
	console.log(tica_organization_document_type_ids_array);
	
	var weights_array = JSON.parse('<?php echo json_encode($this->document_weights);?> ');	 
	console.log(weights_array);
	 
	<?php /**/  ?>
	<?php if(count($this->document_type_labels) > 0){?>
	var dtlabels = JSON.parse('<?php echo addslashes(json_encode($this->document_type_labels));?>');
	<?php } ?>
	console.log(dtlabels.length);
	<?php /**/ ?>
	
	var cat_id = '<?php echo @$this->item->cat_id?>';
	console.log(cat_id);
	
	<?php if( @$this->item->documents && count(@$this->item->documents)){?>
	var has_documents = true;	
	<?php }else{ ?>
	var has_documents = false;		
	<?php } ?>
	
	
	var has_reg_number = false;
	<?php if($has_reg_number){?>
	has_reg_number = true;	
	<?php } ?>
	
	
	 
	
	var checkicon = '<i class="fa fa-check" aria-hidden="true"></i>';
	var timesicon = '<i class="fa fa-times" aria-hidden="true"></i>';
	//jQuery('input.document_file').fileuploader({fileMaxSize:5,limit:1});
	var selectedtypes = new Array;
	jQuery(document).on('ready',function(){
		
	 
		
	if(cat_id){
		if(has_reg_number){		 
		jQuery('input#has_tica_reg_number').attr('checked',true);	
		jQuery('div.div_registration_number').show();	
		jQuery('div#document_container').hide();
		jQuery('div.reginfo').hide();	
		}else{
		jQuery('input#has_tica_reg_number').attr('checked',false);		
		jQuery('div.div_registration_number').hide();		
		jQuery('div#document_container').show();	
		jQuery('div.reginfo').show();	
		}
	}else{
	jQuery('input#has_tica_reg_number').attr('checked',true);	
	jQuery('div.div_registration_number').show();	
	jQuery('div#document_container').hide();
	jQuery('div.reginfo').hide();	
	}	
	
	
	
	
	
	jQuery(document).on('click','input.has_tica_reg_number_option',function(){
		if(jQuery('input#has_tica_reg_number_1').attr('checked')){
		
		//if(jQuery(this).attr('checked')){
			jQuery('div.div_registration_number').show();	
			jQuery('div#document_container').hide();	
			jQuery('div.reginfo').hide();
		}else{
			jQuery('div.div_registration_number').hide();	
			if(!cat_is_hhp)	
			jQuery('div#document_container').show();
			if(!cat_is_hhp)	
			jQuery('div.reginfo').show();
		}
	});	
	
	// sandy hack to preselect second radio if cat is HHP and no registration number
	if(cat_is_hhp){
	jQuery('div#document_container').hide();
	jQuery('div.reginfo').hide();
	
	 
	if(!jQuery('#registration_number').val() ||	jQuery('#registration_number').val() == 'PENDING'){
	jQuery('.has_tica_reg_number_option').attr('checked',false);
	jQuery('#has_tica_reg_number_0').attr('checked',true);
	jQuery('input#has_tica_reg_number_0').trigger('click');
	}else{
	jQuery('#has_tica_reg_number_1').attr('checked',true);
	jQuery('input#has_tica_reg_number_1').trigger('click');	
	}
	 
	}
	 
		
		
	jQuery('input.document_type_id').each(function(){
		 selectedtypes.push(jQuery(this).val());
		 console.log(jQuery(this).val());
		 //jQuery(this).closest('div.dtdiv select.dtype').val(jQuery(this).val());	
	});
	console.log(selectedtypes);
	jQuery('select.dtype:last > option').each(function(){
	if(jQuery.inArray(jQuery(this).attr('value'),selectedtypes)!==-1)
	jQuery(this).remove();	
		
	});
	
	
	jQuery('div.fileuploader-input-button').remove();
	});
	function getdocumenttypeids(){
		jQuery('input#document_type_ids').val('');
		//jQuery('ul#checklist_documents li').each(function(){
 		//jQuery(this).find('i.fa-times').remove() ;
 		//jQuery(this).find('i.fa-check').remove() ;
		//jQuery(this).prepend(timesicon) ;			
		//});
		 
		 
	var document_type_id = new Array;
	 
	jQuery('div.dtdiv div.del_document_type').each(function(){
		var document_type = jQuery(this).attr('data-id');
		 
		document_type_id.push(document_type);
		 
		jQuery('select#document_types option').each(function(){
		if(jQuery(this).attr('value') == document_type ){
			jQuery(this).attr('disabled',true);
		}
		
		 
		});
		 
		//jQuery('ul#checklist_documents li#checklist_'+document_type).find('i.fa-times').remove() ;
		//jQuery('ul#checklist_documents li#checklist_'+document_type).prepend(checkicon) ;
		 
		jQuery('select#document_types').val('0');
	});	
	 
	console.log(document_type_ids);
	jQuery('input#document_type_ids').val(document_type_id.join(','));	
	}
	jQuery(document).ready(function(){
	//jQuery('div.div_registration_number').hide();	
	//jQuery('div#document_type_block').hide();	
	getdocumenttypeids();
	});
	jQuery(document).on('click','div.del_document_type a',function(){
		 
		var dtype_val = jQuery(this).parent('div.del_document_type').data('id');
		 
		
		jQuery('div#document_container select.dtype').each(function(){
			 
			if(jQuery(this).val() == dtype_val && jQuery(this).attr('disabled')){
				
			jQuery(this).closest('div.fieldbg').remove();
			}
		});
		jQuery(this).closest('div.dtdiv').remove();
		
	 
		if(jQuery('a.add_document_type_btn').length <= 0){
			jQuery('div#document_container').append('<div class="clr"></div><a href="javascript:void(null)" class="add_document_type_btn"><?php echo JText::_("COM_TOES_REGISTRATION_DOCUMENT_TYPE_ADD_ANOTHER_DOCUMENT");?></a>');
			
		}
		 
		 
		 
		 
		getdocumenttypeids();
	});
	
	jQuery(document).on('click','a.add_document_type_btn',function(){
	jQuery('div#document_container').append(jQuery('div#document_type_block').html());
	//jQuery('div#document_container select.dtype').addClass('inputbox required');
	 
	 
	var selectedtypes = new Array;
	jQuery('input.document_type_id').each(function(){
		 selectedtypes.push(jQuery(this).val());		
	});
	console.log(selectedtypes);
	jQuery('select.dtype:last > option').each(function(){
	if(jQuery.inArray(jQuery(this).attr('value'),selectedtypes)!==-1 ||jQuery(this).attr('value') =='0' )
	jQuery(this).remove();	
		
	});
	
	//jQuery('div#document_container select.dtype:last').select2();	
	jQuery(this).remove();	
	});
	
	 
	jQuery(document).on('change','select.dtype',function(){
	
	 
	var processed_document_type = new Array;
	
	console.log(processed_document_type);
	var document_type = jQuery(this).val();
	if(!document_type)return;
	if(processed_document_type.indexOf(document_type) == '-1'){
	
	console.log(document_type);
	jQuery(this).closest('div.fieldbg').hide();
	processed_document_type.push(document_type);
	//if(parseInt(document_type)>0){
		jQuery.ajax({
			url:'<?php echo JURI::root()?>index.php?option=com_toes&view=cat&layout=raw&format=raw&id='+parseInt(document_type),	
			method : 'GET',
			success : function(str){
				
				//var div = '<div id=""></div>';
				jQuery('div#document_container').append(str);	
				
				if(tica_organization_document_type_ids_array.in_array(document_type)){
					jQuery('select#organization_'+document_type).val('0');
					//alert(jQuery('select#organization_'+document_type).val());
					//jQuery('select#organization_'+document_type).closest('div.docrow').hide();
				}
				  
				 
				jQuery('div#document_container input.document_file').each(function(){
					var id = '';
					if(!jQuery(this).hasClass('converted')){
						id = jQuery(this).attr('id');
						var doc_no = id.replace('document_','');
						jQuery(this).fileuploader({fileMaxSize:5,limit:1,enableApi: true, extensions:<?php echo json_encode($allowed_extensions_array);?>});	
						jQuery(this).addClass('converted');	
					}
					jQuery('ul#checklist_documents li#checklist_'+document_type).remove('i.fa-cross') ;
					jQuery('ul#checklist_documents li#checklist_'+document_type).prepend(checkicon) ;
					getdocumenttypeids();
				});
				
				jQuery('div.fileuploader-input-button').remove();
				jQuery('select#organization_'+document_type).select2();
				 
			}
		});
	 
	jQuery(this).attr('disabled',true);
	jQuery('div#document_container  a.add_document_type_btn').remove();
	}
});
		 
		
		
		/*
		jQuery(document).on('change','select.dtype',function(){
		
		//tica_organization_document_type_ids_array
		var document_type = jQuery(this).val();
		if(!document_type)return;
		console.log(document_type);
		
		jQuery(this).closest('div.fieldbg').hide(); 
		jQuery('div.div_registration_number').hide();	
		
		//if(parseInt(document_type)>0){
		jQuery.ajax({
		url:'<?php echo JURI::root()?>index.php?option=com_toes&view=cat&layout=raw&format=raw&id='+parseInt(document_type),	
		method : 'GET',
		success : function(str){
		//var div = '<div id=""></div>';
		jQuery('div#document_container').append(str);	
		jQuery('select#organization_'+document_type).select2();
		
		//jQuery('div#document_container input.document_file').fileuploader({});
		jQuery('div#document_container input.document_file').each(function(){
		if(!jQuery(this).hasClass('converted')){
		jQuery(this).fileuploader({fileMaxSize:5,limit:1,enableApi: true, extensions:<?php echo json_encode($allowed_extensions_array);?>});	
		jQuery(this).addClass('converted');	
		}
		//jQuery('ul#checklist_documents li#checklist_'+document_type).remove('i.fa-cross') ;
		//jQuery('ul#checklist_documents li#checklist_'+document_type).prepend(checkicon) ;
		getdocumenttypeids();
		 
		});
		
		 
			
		 
		
		 
		if(tica_organization_document_type_ids_array.in_array(document_type)){
			jQuery('select#organization_'+document_type).val('0');
			//alert(jQuery('select#organization_'+document_type).val());
			jQuery('select#organization_'+document_type).closest('div.docrow').hide();
		}
		 
		jQuery(this).closest('div.fieldbg').hide();  
		jQuery('div.fileuploader-input-button').remove();
		}
		});
		
		//jQuery(this).attr('disabled',true);
		 
	});
		*/
	
	 
	 
	
	function checkSire(){
		if(jQuery('#sire').val().indexOf( jQuery('#sire_r').val() ) == -1 )
		{
			jQuery('#sire_r').val('');
		}
	}

	function checkDam(){
		if(jQuery('#dam').val().indexOf( jQuery('#dam_r').val() ) == -1 )
		{
			jQuery('#dam_r').val('');
		}
	}

	var closedate = <?php echo date('Y') . date('m') . date('d') - 1 ?>;
	<?php if (!$readonly) { ?>
		dob_cal = new Calendar({
			inputField: "date_of_birth",
			dateFormat: "%Y-%m-%d",
			trigger: "date_of_birth_selector",
			max: Calendar.intToDate(closedate),
			bottomBar: false,
			onSelect: function() {
				jQuery('#date_of_birth').css('color','#000');
				document.formvalidator.validate('#date_of_birth');
				this.hide();
			}
		});
	<?php } ?>

	<?php
	if (@$this->item->cat_breed)
	{
		if (!$readonly)
		{
	?>
			jQuery('#breed').val(<?php echo @$this->item->cat_breed ?>);
			jQuery('#category').val(<?php echo @$this->item->cat_category ?>);
			jQuery('#division').val(<?php echo @$this->item->cat_division ?>);
			jQuery('#color').val(<?php echo @$this->item->cat_color ?>);

			changehairlength();
			changecategory();
			changedivision();
			changecolor();
			unset_breed();
	<?php
		}
	}
	?>
</script>

<script type="text/javascript">

    <?php if (!$isColorHelperHidden || TOESHelper::isAdmin()): ?>
        jQuery('#activate-ems-filter').on('click',function(){
            if(parseInt(jQuery('input[id=activate-ems-filter]:checked').val()))
                ems_filter = jQuery('#ems_filter').val();
            else
                ems_filter = '';
            changebreed();
            checkstatus();
        });
    <?php endif; ?>

	jQuery(document).ready(function(){
		jQuery( "#username" ).autocomplete({
		  source: 'index.php?option=com_toes&task=cat.getUsers&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#crosscheckusername" ).val(ui.item.key);
		  	jQuery( "#username" ).val(ui.item.value);
		  }
		});  
		
		  
		/* sandy commented
		jQuery( "#registration_number" ).autocomplete({
		  source: 'index.php?option=com_toes&task=cat.getregistration_number&tmpl=component',
		  select: function( event, ui ) {
		  	//jQuery( "#user_id" ).val(ui.item.key);
		  	jQuery( "#registration_number" ).val(ui.item.value);
		  	
			var validator = document.formvalidator;
			validator.validate('#registration_number');
			get_prefilled_form(ui.item.value);
		  }
		});   
		*/ 
	
		jQuery( "#sire" ).autocomplete({
		  minLength:5,
		  source: 'index.php?option=com_toes&task=cat.getcat_sireordam&gender=m&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#sire_r" ).val(ui.item.key);
		  	jQuery( "#sire" ).val(ui.item.value.replace(' '+ui.item.key, ''));
		  }
		});    
	
		jQuery( "#dam" ).autocomplete({
		  minLength:5,
		  source: 'index.php?option=com_toes&task=cat.getcat_sireordam&gender=f&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#dam_r" ).val(ui.item.key);
		  	jQuery( "#dam" ).val(ui.item.value.replace(' '+ui.item.key, ''));
		  }
		});   
	}); 

	function addusername_owner_onload(user)
	{
		var c=jQuery('#countusername_owner').val();
		var d=parseInt(c)+parseInt(1);

		//var curval=jQuery('#username_owner').val();
		var curval='';
		var username = '';

		jQuery.ajax({
			url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+user,
			type: 'post',
			async: false,
		}).done( function(responseText){
			if(responseText != '0')
			{
				username = responseText;
				curval = responseText+' ('+user+')';
			}
		});

		if(curval=='')
			return;
		var str='<label class="addedusername_owner" id="subusername_owner'+c+'">'+curval;
			<?php if(!$isLessee): ?>
				str +='<span class="username_ownerRemove" onclick="removeusername_owner('+c+');">x</span>';
			<?php endif; ?>
			str +='</label>';

		jQuery('#username_ownerplace').html(jQuery('#username_ownerplace').html()+str);

		jQuery('#innerusername_owner').append('<input type="hidden" value="'+user+'" name="username_owner[]" id="username_ownerdata'+c+'"/>');
		jQuery('#countusername_owner').val(d);

		var cat_owner =jQuery('#cat_owner').val();
		if(!cat_owner)
		{
			jQuery('#cat_owner').val(username);
		}
		user='';
	}

	function addusername_breeder_onload(user)
	{
		var c=jQuery('#countusername_breeder').val();
		var d=parseInt(c)+parseInt(1);

		var curval='';
		var username = '';
		
		jQuery.ajax({
			url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+user,
			type: 'post',
			async: false,
		}).done( function(responseText){
			if(responseText != '0')
			{
				username = responseText;
				curval = responseText+' ('+user+')';
			}
		});

		if(curval=='')
			return;
		var str='<label class="addedusername_breeder" id="subusername_breeder'+c+'">'+curval+'<span class="username_breederRemove" onclick="removeusername_breeder('+c+');">x</span>'+
			'</label>';

		jQuery('#username_breederplace').html(jQuery('#username_breederplace').html()+str);

		jQuery('#innerusername_breeder').append('<input type="hidden" value="'+user+'" name="username_breeder[]" id="username_breederdata'+c+'"/>');
		jQuery('#countusername_breeder').val(d);

		var cat_breeder =jQuery('#cat_breeder').val();
		if(!cat_breeder)
		{
			jQuery('#cat_breeder').val(username);
		}
		user='';
	}

	function addusername_agent_onload(user)
	{
		var c=jQuery('#countusername_agent').val();
		var d=parseInt(c)+parseInt(1);

		var curval='';
		var username = '';

		jQuery.ajax({
			url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+user,
			type: 'post',
			async: false,
		}).done( function(responseText){
			if(responseText != '0')
			{
				username = responseText;
				curval = responseText+' ('+user+')';
			}
		});

		if(curval=='')
			return;
		var str='<label class="addedusername_agent" id="subusername_agent'+c+'">'+curval+'<span class="username_agentRemove" onclick="removeusername_agent('+c+');">x</span></label>';

		jQuery('#username_agentplace').html(jQuery('#username_agentplace').html()+str);

		jQuery('#innerusername_agent').append('<input type="hidden" value="'+user+'" name="username_agent[]" id="username_agentdata'+c+'"/>');
		jQuery('#countusername_agent').val(d);
		user='';
	}

	function addusername_lessee_onload(user)
	{
		var c=jQuery('#countusername_lessee').val();
		var d=parseInt(c)+parseInt(1);

		//var curval=jQuery('#username').val();
		var curval='';
		var username = '';

		jQuery.ajax({
			url: 'index.php?option=com_toes&task=cat.getUserFullname&username='+user,
			type: 'post',
			async: false,
		}).done( function(responseText){
			if(responseText != '0')
			{
				username = responseText;
				curval = responseText+' ('+user+')';
			}
		});

		if(curval=='')
			return;
		var str='<label class="addedusername_lessee" id="subusername_lessee'+c+'">'+curval+'<span class="username_lesseeRemove" onclick="removeusername_lessee('+c+');">x</span>'+
			'</label>';

		jQuery('#username_lesseeplace').html(jQuery('#username_lesseeplace').html()+str);

		jQuery('#innerusername_lessee').append('<input type="hidden" value="'+user+'" name="username_lessee[]" id="username_lesseedata'+c+'"/>');
		jQuery('#countusername_lessee').val(d);

		var cat_lessee =jQuery('#cat_lessee').val();
		if(!cat_lessee)
		{
			jQuery('#cat_lessee').val(username);
		}
		user='';
	}

	<?php
		if (isset($this->item->cat_id))
		{
			if (isset($this->cat_owner) && count($this->cat_owner))
			{
				foreach ($this->cat_owner as $cat_owner)
				{
	?>
					addusername_owner_onload("<?php echo $cat_owner->username; ?>");
	<?php
				}
			}
		}
	?>

	<?php
		if (isset($this->item->cat_id))
		{
			if (isset($this->cat_breeder) && count($this->cat_breeder))
			{
				foreach ($this->cat_breeder as $cat_breeder)
				{
					?>
					addusername_breeder_onload("<?php echo $cat_breeder->username; ?>");
					<?php
				}
			}
		}
	?>

	<?php
		if (isset($this->item->cat_id))
		{
			if (isset($this->cat_other) && count($this->cat_other))
			{
				foreach ($this->cat_other as $cat_other)
				{
					if ($cat_other->cat_user_connection_type == 'Agent')
					{
	?>
						addusername_agent_onload("<?php echo $cat_other->username; ?>");
	<?php
					}
					if ($cat_other->cat_user_connection_type == 'Lessee')
					{
	?>
						addusername_lessee_onload("<?php echo $cat_other->username; ?>");
	<?php
					}
				}
			}
		}
	?>

    <?php
	$group = $app->input->getVar('group');
	$username = $app->input->getVar('username','');
	$related_user = $username ? $username : $user->username;
	if (!isset($this->item->cat_id) || (isset($this->item->cat_id) && $this->item->cat_id == 0))
	{
		switch ($group)
		{
			case 'owner':
				echo 'addusername_owner_onload("' . $related_user . '");';
				break;
			case 'breeder':
				echo 'addusername_breeder_onload("' . $related_user . '");';
				break;
			case 'agent':
				echo 'addusername_agent_onload("' . $related_user . '");';
				break;
			case 'lessee':
				echo 'addusername_lessee_onload("' . $related_user . '");';
				break;
		}
	}
    ?>
    
    function validate_catform(form)
    {
		//isHHP
        var errMsg = '';

        var validator = document.formvalidator;
        var still_required = '';
        
        //var has_tica_reg_number =	jQuery('input#has_tica_reg_number').attr('checked');
        var has_tica_reg_number = false;
        if(jQuery('input#has_tica_reg_number_1').attr('checked'))has_tica_reg_number = true;
        
        
        if(!isHHP){
		if(has_tica_reg_number){
		//jQuery('div#document_container').remove();
		
		 
		if(jQuery('input#registration_number').val() == 'PENDING'){
			
			still_required += "\n* <?php echo JText::_('COM_TOES_REGISTRATION_NUMBER_CAN_NOT_BE_PENDING'); ?>";
		}
		 
		 
				
		if(!validator.validate('#registration_number'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_REGISTRATION_NUMBER'); ?>";
        }	
		}else{
        
        jQuery('div.div_registration_number').hide();        
        if(jQuery('div#document_container select.dtype').length == 1 && jQuery('div#document_container select.dtype').val()=='0'){
			if(!jQuery('input#registration_number').val() ||jQuery('input#registration_number').val() =='PENDING'){
				still_required += "\n* <?php echo JText::_('COM_TOES_ENTER_REGISTRATION_NUMBER_OR_UPLOAD_DOCUMENTS'); ?>"; 
			} 	
		}
		if(jQuery('div#document_container select.dtype').length > 0){
			var dtypes_selected = 0
			jQuery('div#document_container select.dtype').each(function(){
			/*
			if(!validator.validate(jQuery(this)))
				{
					still_required += "\n* <?php echo JText::_('COM_TOES_SELECT_DOC_TYPE_TEST_OK_NOT'); ?>";
				}
			*/
			if(jQuery(this).val())++dtypes_selected;
			});
			
			if(dtypes_selected <= 0){
				still_required += "\n* <?php echo JText::_('COM_TOES_SELECT_DOC_TYPE_AT_LEAST_ONE'); ?>";
			}
			
			 
			jQuery('div.dtdiv .document_file').each(function(){
				
				 
				if(!jQuery(this).val()){ 
				
				// check if there are any files uploaded	
				if(jQuery(this).closest('div.form-input').find('ul.fileuploader-items-list li').length <= 0){  
				still_required += "\n* <?php echo JText::_('COM_TOES_DOC'); ?>";						
				}
				}
				 
				
			});	
			
			
			
			jQuery('div.dtdiv .organization_select').each(function(){
				if(!validator.validate(jQuery(this)))
				{
					still_required += "\n* <?php echo JText::_('COM_TOES_ORG'); ?>";
				}
				 
				
			});	
			var weight = 0;
			jQuery('div.del_document_type').each(function(){
				var doctypeid = jQuery(this).data('id');
				for(var i = 0;i< weights_array.length ; i++){
				if(weights_array[i].id == doctypeid){
				weight += parseInt(weights_array[i].weight);
				break;	
				}	
					
				}
				
			}); 
			
			if(parseInt(weight) < 2){
			if(parseInt(weight) == 1)		
			still_required += "<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_ONLY_ONE_PARENT_PEDIGREE_UPLOADED');?>";
			if(parseInt(weight) == 0)	
			still_required += "<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_NO_INFO_UPLOADED');?>";
			}
		
		}
		}
		
		}
		
		
		

        
        if(!validator.validate('#breed'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_BREED'); ?>";
        }
        if(!validator.validate('#date_of_birth'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_DATE_OF_BIRTH'); ?>";
        }
        if(!validator.validate('#gender'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_GENDER'); ?>";
        }
        if(!validator.validate('#cat_hair_length'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_HAIRLENGTH'); ?>";
        }
        if(!validator.validate('#category'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_CATEGORY'); ?>";
        }
        if(!validator.validate('#division'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_DIVISION'); ?>";
        }
        if(!validator.validate('#color'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_COLOR'); ?>";
        }
        if(!validator.validate('#prefix'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_PREFIX'); ?>";
        }
        if(!validator.validate('#title'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_TITLE'); ?>";
        }
        if(!validator.validate('#suffix'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_SUFFIX'); ?>";
        }
        if(!validator.validate('#name'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_NAME'); ?>";
        }
        if(!validator.validate('#cat_competitive_region'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_COMPETITIVE_REGION'); ?>";
        }
        if(!validator.validate('#cat_owner'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_REGISTRED_OWNER'); ?>";
        }
        // HHP condition
        if(!isHHP && !validator.validate('#cat_breeder'))
        {
            still_required += "\n* <?php echo JText::_('COM_TOES_REGISTRED_BREEDER'); ?>";
        }
        
         
         
        
        
		
		 
        
        
        var breed = jQuery('#breed').val();

        jQuery.ajax({
        	url: 'index.php?option=com_toes&task=cat.isHHP&breed='+breed+'&tmpl=component',
            type: 'get',
            async: false,
        }).done(function(responseText){
            if(responseText != 1)
            {
                var sire = form.sire;
                var dam = form.dam;

                if(sire.value == "")
                {
                    still_required += "\n* <?php echo JText::_('COM_TOES_SIRE'); ?>";
                }
                if(dam.value == "")
                {
                    still_required += "\n* <?php echo JText::_('COM_TOES_DAM'); ?>";
                }

                var sire_r = form.sire_r;
                var dam_r = form.dam_r;

                if(sire.value.search(sire_r.value) == -1)
                {
                    sire_r.value = '';
                }

                if(dam.value.search(dam_r.value) == -1)
                {
                    dam_r.value = '';
                }
            }
        });
        
        if(still_required)
            errMsg += "<?php echo JText::_('COM_TOES_REQUIRED_FIELD'); ?>"+still_required;

        var users = new Array();
        jQuery('input[name^=username_owner]').each(function(){
            users.push(jQuery(this).val());
        });
        if(!isHHP)
        jQuery('input[name^=username_breeder]').each(function(){
            users.push(jQuery(this).val());
        });
        jQuery('input[name^=username_agent]').each(function(){
            users.push(jQuery(this).val());
        });
        jQuery('input[name^=username_lessee]').each(function(){
            users.push(jQuery(this).val());
        });
        
        if(users.length == 0)
        {
            errMsg += "\n\n<?php echo JText::_('COM_TOES_NEED_ATLEAST_ONE_USER'); ?>";
        }
        
        if(errMsg)
        {
            alert(errMsg);
            return false;
        }
        
        var edit_privileges_users = new Array();
        jQuery('input[name^=username_owner]').each(function(){
            edit_privileges_users.push(jQuery(this).val());
        });
        jQuery('input[name^=username_lessee]').each(function(){
            edit_privileges_users.push(jQuery(this).val());
        });
        
        if(!edit_privileges_users.in_array("<?php echo $user->username; ?>"))
        {
            var answer = confirm("<?php echo JText::_('COM_TOES_USER_EDIT_CAT_PRIVILAGES_QUESTION'); ?>");
            if(!answer)
            {
                return false;	
            }
        }
        
        //alert('HHP is good');
        //return false;
        
        jQuery('select.dtype').attr('disabled',false);
        
        form.submit();
    }
</script>

