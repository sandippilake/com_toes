<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$exibitor = TOESHelper::getUserInfo($this->entry->user_id);
$app = JFactory::getApplication();
JHtml::_('behavior.formvalidation');

$params = JComponentHelper::getParams('com_toes');
$tica_organization_document_type_ids = $params->get('tica_organization_document_type_ids');
$tica_organization_document_type_ids_array = explode(',',$tica_organization_document_type_ids);
 
if(count($this->documents))$have_docs = true;
else
$have_docs = false; 
?>
<?php /*
<link href="../components/com_toes/assets/css/jquery.fileuploader.min.css" media="all" rel="stylesheet">
<script src="../components/com_toes/assets/js/jquery.fileuploader.min.js" type="text/javascript"></script>
<link href="../components/com_toes/assets/css/jquery.fileuploader-theme-thumbnails.css" media="all" rel="stylesheet">
*/?> 
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" type="text/javascript" defer="defer"></script>
<h3><?php echo JText::_('NEW_ENTRY')?></h3>

<?php $user = JFactory::getUser(); ?>
<?php /** */ ?>
	<div id="document_type_block" style="display:none">
		<div class="fieldbg" >
            <div class="form-label" >
                <label for="type_of_document" id="type_of_document-lbl" style="width:100%">
					<?php echo JText::_('COM_TOES_DOCUMENT_TYPE'); ?>
                </label>
            </div>
            <div class="form-input" >
				<?php echo $this->document_types?>
            </div> 
        </div> 
		<div class="clr"></div>
	</div>
	<form  action="<?php echo JRoute::_('index.php?option=com_toes&view=shows'); ?>" class="form-validate" method="post" name="adminForm" id="adminForm">
	
	
	<div class="clr"></div>
    <div class="fistouter" >
		<div>		 
		<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_QUICK_INTRO_ELIGIBILITY');?>
		</div>
    </div>
    <div class="clr"></div>
	 
	 
	
	<div class="clr"></div>
    <div class="fistouter" >
		 
		<div><?php echo JText::_('COM_TOES_HAS_TICA_REGISTRATION_NUMBER');?></div>
			
		<input type="radio" name="has_tica_reg_number" 
		class="has_tica_reg_number_option" id="has_tica_reg_number_1" value="1" <?php if($have_docs)echo '';else echo 'checked';?> >
		<?php echo JText::_('COM_TOES_HAS_TICA_REGISTRATION_NUMBER')?><br/>
		
		<input type="radio" name="has_tica_reg_number" 
		class="has_tica_reg_number_option" id="has_tica_reg_number_0" value="0" <?php if($have_docs)echo 'checked';else echo '';?> >
		<?php echo JText::_('COM_TOES_DOES_NOT_YET_HAVE_TICA_REGISTRATION_NUMBER')?>
			
		 
		 
    </div>
    <div class="clr"></div>
    
    
    <div class="fistouter reginfo"  style="display:<?php echo $have_docs?'block':'none';?>">
		<div class="fieldblank" >
			<div class="block-title"><?php echo JText::_('COM_TOES_REGISTRATION_INFORMATION'); ?></div>
		</div>
		<div class="fieldbg" >
			 <div class="registration_number_explanation">
				<?php echo JText::_('COM_TOES_REGISTRATION_NUMBER_EXPLANATION_TEXT');?>
			</div>	
		</div>
	</div>
	<div class="clr"></div>
    <div class="fieldbg div_registration_number" style="display:<?php echo $have_docs?'none':'block';?>">
		<div class="form-label" >
			<label class="required" for="registration_number" id="registration_number-lbl" style="width:100%">
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
    <div class="clr"></div>    
	<div id="document_container" style="display:<?php echo $have_docs?'block':'none';?>">
		 
		<?php if(count($this->documents)){
			//var_dump($this->documents);
				
			foreach($this->documents as $d){
				$file_type = mime_content_type(JPATH_BASE.DS.$d->cat_document_file_name);
				$file_size = filesize(JPATH_BASE.DS.$d->cat_document_file_name); 
				//$tica_organization_document_type_ids_array
				 
				?>
					
				<div id="dt_<?php echo $d->cat_document_id?>" class="dtdiv" style="clear:both">
						<?php echo JText::_("$d->allowed_registration_document_title_language_constant");?>
						
						<div class="del_document_type" style="text-align:left;display:inline" data-id="<?php echo $d->cat_document_registration_document_type_id?>" data-docid="<?php echo $d->cat_document_id?>" >
							<a class="uploaded">
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
				value="<?php echo  basename($d->cat_document_file_name)?>"		class="document_file required converted uploaded">
					</div>
					<input type="hidden" class="document_type_id" name="document_type_id[]" value="<?php echo $d->cat_document_registration_document_type_id?>"/>
					<div class="clr"></div>
				</div>
				<script>
				//jQuery(document).on('ready',function(){
				jQuery('div#dt_<?php echo $d->cat_document_id?> .document_file').fileuploader({fileMaxSize:5,limit:1,enableApi: true , 
				files :[{name :'<?php echo trim(basename($d->cat_document_file_name))?>',type:'<?php echo trim($file_type)?>',
				size: '<?php echo $file_size?>',file:'<?php echo JURI::root().trim($d->cat_document_file_name)?>' }],onRemove: function(item, listEl, parentEl, newInputEl, inputEl){
				jQuery('div#dt_<?php echo $d->cat_document_id?> .document_file').removeClass('uploaded');
				jQuery.ajax({url:'index.php?option=com_toes&task=entry.removedocument&cat_document_id=<?php echo $d->cat_document_id?>&tmpl=component'
					,success:function(data){
						//alert(data);
						if(data=='1')
						alert("Document removed, please upload another.IF you want to completely remove then please click on trash icon above");
						}
					});
				return true;} });
				//});
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
	 
	 
	 
	
	 
	<div class="fieldbg" id="checklist_div" style="display:<?php echo $have_docs?'block':'none';?>">
		<?php 
		if(count($this->document_types_list) >0){?>
			<ul id="checklist_documents">
				<?php 
				$i = 0;
				foreach($this->document_types_list as $dt){
					++$i;
					//if($i == 1)continue;
					$checked = '';
					?>
					<li id="checklist_<?php echo $dt->value?>"><?php echo JText::_($dt->text)?> </li>
				<?php } ?>
			</ul>
		<?php } ?>
	</div>	
<?php /**/?>
    <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
    <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
    <input type="hidden" value="<?php echo $this->entry->show_id; ?>" name="show_id" />
    <input type="hidden" value="<?php echo $app->input->getInt('cat_id'); ?>" name="cat_id" id="cat_id"/>
    
 
    <input type="hidden" id="docs" name="docs" value="" />
    <?php if((isset($this->entry->placeholder_id) && $this->entry->placeholder_id) || !$this->entry->edit):?>
        <input onclick="previous_step('step1_5');" type="button" name="button" value="<?php echo JText::_('COM_TOES_BACK'); ?>" />
    <?php else: ?>
        <input onclick="cancel_edit_entry();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <?php endif;?>
    <?php //if($this->cats): ?>
        <?php /*<input onclick="next_step('step1_5');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" /> */ ?>
        <input id="nextbtn" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
    <?php //endif;?>
</form>


<script>
	var tica_organization_document_type_ids_array = new Array;
	<?php if(count($tica_organization_document_type_ids_array)){?>
	<?php foreach($tica_organization_document_type_ids_array as $arr){?>
	tica_organization_document_type_ids_array.push('<?php echo $arr?>');		
	<?php } ?>
	<?php } ?>
	console.log(tica_organization_document_type_ids_array);
	
	
	var weights_array = JSON.parse('<?php echo json_encode($this->document_weights);?> ');
	
	
	 
	console.log(weights_array);
	
	
	
	
	
	
	
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

	var checkicon = '<i class="fa fa-check" aria-hidden="true"></i>';
	var timesicon = '<i class="fa fa-times" aria-hidden="true"></i>';
	//jQuery('input.document_file').fileuploader({fileMaxSize:5,limit:1});
	var selectedtypes = new Array;
	
	jQuery(document).on('click','input.has_tica_reg_number_option',function(){
		if(jQuery('input#has_tica_reg_number_1').attr('checked')){
		
		//if(jQuery(this).attr('checked')){
			jQuery('div.div_registration_number').show();	
			jQuery('div#document_container').hide();
			jQuery('div.reginfo').hide();	
			jQuery('div.checklist_div').hide();	
		}else{
			jQuery('div.div_registration_number').hide();	
			jQuery('div#document_container').show();	
			jQuery('div.reginfo').show();
			jQuery('div.checklist_div').show();
		}
	});
	
	jQuery('div.del_document_type').each(function(){
		var document_type = jQuery(this).attr('data-id');  
		console.log(document_type); 
		jQuery('ul#checklist_documents li#checklist_'+document_type).find('i.fa-times').remove() ;
		jQuery('ul#checklist_documents li#checklist_'+document_type).prepend(checkicon) ; 
		 
	});			
	
	jQuery('div.fileuploader-input-button').remove();
	jQuery(document).on('ready',function(){
		
	console.log('domready');
	
		
		
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
	 
	   
	
	
	
	});
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
	jQuery(document).on('change','input#registration_number',function(){
	var registration_number = jQuery(this).val().trim().toUpperCase();
	if(registration_number.length < 14)
	{
		alert('Length must be 14');
		jQuery(this).val('');
		return false;
	}
	if(registration_number.length > 14)
	{
		registration_number = registration_number.substring(0, 14);
	}
	if(ntype2.in_array(registration_number.substring(0, 2))){
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
                        alert('Month can not be greater than 12');
                        jQuery(this).val('');
                        return;
                    }
                    if(registration_number.substring(5,7) > 31)
                    {
                        alert('Date can not be greater than 31');
                        jQuery(this).val('');
                        return false;
                    }
                    if(registration_number.substring(7,9) > 99)
                    {
                        alert('Year can not be greater than 99');
                        jQuery(this).val('');
                        return false;
                    }
                }
                else
                {
                    alert('Wrong birthdate in registration number');
                    jQuery(this).val('');
                    return false;
                }

                if(isNaN(registration_number.substring(10,14)))	
                {
                    alert('Last 4 must be number');
                    jQuery(this).val('');
                    return false;
                }	

                 

            }else if(ntype3.in_array(registration_number.substring(0, 3))){
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
                        alert('Month can not be greater than 12');
                        jQuery(this).val('');
                        return;
                    }
                    if(registration_number.substring(6,8) > 31)
                    {
                         alert('Date can not be greater than 31');
                        jQuery(this).val('');
                        return false;
                    }
                    if(registration_number.substring(8,10) > 99)
                    {
                        alert('Year can not be greater than 99');
                        jQuery(this).val('');
                        return false;
                    }
                }
                else
                {
                    alert('Wrong birthdate in registration number');
                    jQuery(this).val('');
                    return false;
                }


                if(isNaN(registration_number.substring(11,14)))	
                {
                    alert('Last 4 must be number');
                    jQuery(this).val('');
                    return false;
                }	

                
            } else {
                alert('Wrong format');
                jQuery(this).val('');
                return false;
            }
    jQuery(this).val( registration_number);        
            
	
		
		
	// validation above this	
	 
	var registration_number = jQuery(this).val().trim().replace(/\s{1,}/g, '');
	console.log(registration_number);
	jQuery.ajax({
	url:'index.php?option=com_toes&task=entry.checkregistration_number&tmpl=component',	
	method:'post',	
	data:{registration_number:jQuery(this).val(),cat_id:jQuery('input#registration_number').val()},
	success:function(data){
		//alert(data);
		if(data == '-1')
		jQuery('input#registration_number').val('');
		
		
	}
	});	
	
	
	
	});
	jQuery('#nextbtn').on('click',function(){
	var validator = document.formvalidator;
	 
	var still_required = "";
	
	//var has_tica_reg_number =	jQuery('input#has_tica_reg_number').attr('checked');
	var has_tica_reg_number = false;
	
	if(jQuery('input#has_tica_reg_number_1').attr('checked'))has_tica_reg_number = true;
	
	
	
	
	
	if(has_tica_reg_number){
	//jQuery('div#document_container').remove();	
	if(!jQuery('input#registration_number').val() ||jQuery('input#registration_number').val() =='PENDING'){
		
	still_required +=("<?php echo JText::_('COM_TOES_ENTER_REGISTRATION_NUMBER_OR_UPLOAD_DOCUMENTS'); ?>"); 
	} 		
	 
	}else{
	
	//jQuery('div.div_registration_number').remove();
	if(jQuery('div.dtdiv').length > 0){
		//check weights of documents if any
		
		
		jQuery('div.dtdiv .document_file').each(function(){
			// console.log(jQuery(this));
			var api = jQuery.fileuploader.getInstance(jQuery(this));
			console.log(api.getFiles());
			
			if(api.getFiles().length <= 0 && !jQuery(this).hasClass('uploaded') ){
				still_required += "\n* <?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_NO_FILE_SELECTED'); ?>";	
			}
			
		});	
		jQuery('div.dtdiv .organization_select').each(function(){
			if(!validator.validate(jQuery(this)))
			{
				still_required += "\n* <?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_NO_ORGANIZATION_SELECTED'); ?>";
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
		
		
		
		
		
		
			
	}else{
	 	
		still_required += "\n* <?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_NO_INFO_UPLOADED'); ?>";
	}
	
	
	 
	}
	
	 
	//if(jQuery('div#document_container select.dtype').length == 1 && jQuery('div#document_container select.dtype').val()=='0'){
		 
	
	//}
	
	
	
		
 
	
	if(still_required){
		alert(still_required);
		return false;
	}else{
		jQuery('div.dtdiv .document_file').each(function(){
			
			var api = jQuery.fileuploader.getInstance(jQuery(this));
			if(api.getFiles().length > 0)
			api.uploadStart();
			
			
		});	
		
		jQuery('select.dtype').attr('disabled',false);
		next_step('step1_5');
	}
});
jQuery(document).on('click','a.add_document_type_btn',function(){
	//alert(jQuery('div#document_container select.dtype').length);
	//jQuery(this).nextAll().remove();	
	//jQuery('div#document_container').append(jQuery('div#document_type_block').html());
	
	
	/*
	if(!jQuery('div#document_container select.dtype:last').val()){
	jQuery('div#document_container').append(jQuery('div#document_type_block').html());	
	}
	*/
	 
	if(jQuery('div#document_container select.dtype:last').length){ 
	if(jQuery('div#document_container select.dtype:last').val()){
	// alert(jQuery('div#document_container select.dtype:last').val());
	jQuery('div#document_container').append(jQuery('div#document_type_block').html());	
	}else{
	alert('No value selected for previously selected document');	
		
	}
	}else{
	jQuery('div#document_container').append(jQuery('div#document_type_block').html());			
	}
	 
	 
	var selectedtypes = new Array;
	jQuery('input.document_type_id').each(function(){
		 selectedtypes.push(jQuery(this).val());		
	});
	console.log(selectedtypes);
	jQuery('select.dtype:last > option').each(function(){
	if(jQuery.inArray(jQuery(this).attr('value'),selectedtypes)!= -1)
	jQuery(this).remove();	
		
	});
	
		
	//jQuery(this).remove();	
	});

 
 

/*
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
					jQuery('select#organization_'+document_type).closest('div.docrow').hide();
				}
				  
				 
				jQuery('div#document_container input.document_file').each(function(){
					var id = '';
					if(!jQuery(this).hasClass('converted')){
						id = jQuery(this).attr('id');
						var doc_no = id.replace('document_','');
						jQuery(this).fileuploader({
							fileMaxSize:5,
							limit:1,
							enableApi: true,
							upload: {
								// upload URL {String}
								url: 'index.php?option=com_toes&task=entry.imageupload',
								// upload data {null, Object}
								// you can also change this Object in beforeSend callback
								// example: { option_1: 'yes', option_2: 'ok' }
								data:  { doc_no: doc_no, cat_id: '<?php echo $this->entry->cat_id; ?>' },
								type: 'POST',
								enctype: 'multipart/form-data',
								start: false,
								synchron: true,
								chunk: false,
								beforeSend: function(item, listEl, parentEl, newInputEl, inputEl) {
									item.upload.data.org_id = jQuery('#organization_'+doc_no).val();
									return true;
								},
								onSuccess: function(data, item, listEl, parentEl, newInputEl, inputEl, textStatus, jqXHR) {
									item.html.find('.column-actions').append(
										'<a class="fileuploader-action fileuploader-action-remove fileuploader-action-success" title="Remove"><i></i></a>'
										);
									console.log(data);
									// console.log(id);
									var data = JSON.parse(data);
									if(data.isSuccess){
										var doc_name = data.files[0].name;
										console.log(doc_name);
										jQuery('#doc_'+doc_no).val(doc_name);
									}
									setTimeout(function() {
										item.html.find('.progress-bar2').fadeOut(400);
									}, 400);
								}
							}
						});	
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
	}
});*/





	/*
	jQuery(document).on('click','a.add_document_type_btn',function(){
	alert(jQuery('div#document_container select.dtype').length);
	//jQuery(this).nextAll().remove();	
	//jQuery('div#document_container').append(jQuery('div#document_type_block').html());
	
	if(!jQuery('div#document_container select.dtype:last').val()){
	jQuery('div#document_container').append(jQuery('div#document_type_block').html());	
	}
	 
	 
	 
	var selectedtypes = new Array;
	jQuery('input.document_type_id').each(function(){
		 selectedtypes.push(jQuery(this).val());		
	});
	console.log(selectedtypes);
	jQuery('select.dtype:last > option').each(function(){
	if(jQuery.inArray(jQuery(this).attr('value'),selectedtypes)!==-1)
	jQuery(this).remove();	
		
	});
	
		
	jQuery(this).remove();	
	});
	*/
jQuery(document).on('click','div.del_document_type a',function(){
	
	if(jQuery(this).hasClass('uploaded')){
		var docid = jQuery(this).parent('div.del_document_type').data('docid'); 
		
		jQuery.ajax({
			url:'index.php?option=com_toes&task=entry.removedocument&cat_document_id='+docid+'&tmpl=component',
			success:function(data){
				console.log(data);
				if(data=='1'){
				 
				//
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
				}
				//
				}
		});
				
		
	}else{
	
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
	}
	
	
	
	
	
});

/*
function getdocumenttypeids(){
	jQuery('input#document_type_ids').val('');
	jQuery('ul#checklist_documents li').each(function(){
		jQuery(this).find('i.fa-times').remove() ;
		jQuery(this).find('i.fa-check').remove() ;
		jQuery(this).prepend(timesicon) ;			
	});
		
		
	var document_type_ids = new Array;
	jQuery('div.dtdiv div.del_document_type').each(function(){
		var document_type = jQuery(this).attr('data-id');
		if(	document_type_ids.indexOf(document_type) == '-1')
		document_type_ids.push(document_type);
		//jQuery('select#document_types option').attr('disabled',false);
		jQuery('select#document_types option').each(function(){
			if(jQuery(this).attr('value') == document_type ){
				jQuery(this).attr('disabled',true);
			}
		});
			
		jQuery('ul#checklist_documents li#checklist_'+document_type).find('i.fa-times').remove() ;
		jQuery('ul#checklist_documents li#checklist_'+document_type).prepend(checkicon) ;
			
		jQuery('select#document_types').val('0');
	});	
	 console.log(document_type_ids);
	jQuery('input#document_type_ids').val(document_type_ids.join(','));	
}
*/

</script>
