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
JHTML::_('behavior.modal');
JHtml::_('bootstrap.framework');
JHtml::_('behavior.formvalidation');

JHtml::_('formbehavior2.select2','select');

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
$paper_sizes = TOESHelper::getPapersizeOptions();

?>
 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="<?php echo JURI::root()?>components/com_toes/assets/css/jquery.fileuploader.min.css" media="all" rel="stylesheet">
<script src="<?php echo JURI::root()?>components/com_toes/assets/js/jquery.fileuploader.min.js" type="text/javascript"></script>
<link href="<?php echo JURI::root()?>components/com_toes/assets/css/jquery.fileuploader-theme-thumbnails.css" media="all" rel="stylesheet">


<div id="toes">
	<div class ="show-details-main">
		
		<?php require __DIR__.'/header.php'; ?>

		<form action="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&id=' . $data->show_id); ?>" method="post">
			<div class="filter-block" style="float:right;width:auto;background: none;">
				<div class="filter-field" >
					<label for="entry_status_filter" class="lbl" style="width:auto">
						<?php echo JText::_('COM_TOES_SHOW_ENTRY_STATUS_FILTER'); ?> :
					</label>
					<select class="filter-selectlist" name="entry_status_filter" id="entry_status_filter" onchange="this.form.submit();">
						<?php
						foreach ($this->entrystatuses as $b) {
							if (@$this->state->get('filter.entry_status') == $b->value)
								$sel = 'selected="selected"';
							else
								$sel = '';

							echo '<option value="' . $b->value . '" ' . $sel . '>' . $b->text . '</option>';
						}
						?>
					</select>
				</div>
				<div class="filter-field" >
					<label for="entry_user_filter" class="lbl" style="width:auto">
						<?php echo JText::_('COM_TOES_SHOW_ENTRY_USER_FILTER'); ?> :
					</label>
					<select class="filter-selectlist" name="entry_user_filter" id="entry_user_filter" onchange="this.form.submit();">
						<?php
						foreach ($this->entryusers as $b) {
							if (@$this->state->get('filter.entry_user') == $b->value)
								$sel = 'selected="selected"';
							else
								$sel = '';

							echo '<option value="' . $b->value . '" ' . $sel . '>' . $b->text . '</option>';
						}
						?>
					</select>
				</div>
				<div class="filter-field" >
					<label for="entry_type_filter" class="lbl" style="width:auto">
						<?php echo JText::_('COM_TOES_SHOW_ENTRY_TYPE_FILTER'); ?> :
					</label>
					<select class="filter-selectlist" name="entry_type_filter" id="entry_type_filter" onchange="this.form.submit();">
						<option value="" <?php echo (@$this->state->get('filter.entry_type') == '') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_TOES_BOTH') ?></option>
						<option value="1" <?php echo (@$this->state->get('filter.entry_type') == '1') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_TOES_ENTRIES') ?></option>
						<option value="2" <?php echo (@$this->state->get('filter.entry_type') == '2') ? 'selected="selected"' : '' ?> ><?php echo JText::_('COM_TOES_PLACEHOLDERS') ?></option>
					</select>
				</div>
			</div>
			<div class="title" style="padding-top:20px;">
				<?php echo JText::_('COM_TOES_SHOW_ENTRIES'); ?>
			</div>
		</form>
		<div class="clr"></div>
		<br/>

		<div>
			<?php $user_id = $user->id; ?>
			<?php if ($data->show_uses_toes && $data->show_status != 'Held'): ?>
				<div>
					<br/>
					<?php if (($isAdmin && $params->get('show_add_entry_for_admin')) || ($data->show_start_date >= date('Y-m-d') && $data->show_status == 'Open') || ($data->show_start_date < date('Y-m-d'))) : ?>
						<input type="hidden" id="add_user" name="add_user" value="<?php echo $user_id; ?>" />
						<?php if ($isAdmin || TOESHelper::is_entryclerk($user->id, $data->show_id) || TOESHelper::is_showmanager($user->id, $data->show_id)): ?>
							<a href="javascript:void(0);" onclick="add_third_party_entry(<?php echo $data->show_id; ?>, 'add-entry-div-entryclerk');">
								<i class="fa fa-plus-circle"></i> 
								<?php echo JText::_('COM_TOES_ADD_THIRD_PARTY_ENTRY'); ?>
							</a>
						<?php endif; ?>
						<?php if ($isAdmin || TOESHelper::is_entryclerk($user->id, $data->show_id) || TOESHelper::is_showmanager($user->id, $data->show_id)): ?>
							<a href="javascript:void(0);" onclick="add_third_party_placeholder(<?php echo $data->show_id; ?>, 'add-entry-div-entryclerk');">
								<i class="fa fa-plus-circle"></i> 
								<?php echo JText::_('COM_TOES_ADD_PLACEHOLDER_FOR_THIRD_PARTY'); ?>
							</a>
						<?php endif; ?>
					<?php endif; ?>
				</div>
				<div style="text-align: left;" class="add-entry-div" id="add-entry-div-entryclerk"></div>
			<?php endif; ?>
		</div>
		<div class="clr"></div>
		<br/>

		<div class ="seconouter">
			<form action="<?php echo JRoute::_('index.php?option=com_toes&view=entryclerk&id=' . $data->show_id); ?>" method="post" name="adminForm" id="adminForm">
				<div class ="block entryclerk">
					<div class="details">
						<div class="entries">

							<?php
							$this->cnt = 0;
							foreach ($this->users as $user) :
								if ($user->id == 0)
									continue;

								if ($this->state->get('filter.entry_user') && $this->state->get('filter.entry_user') != $user->id)
									continue;

								$entries = array();
								if ($this->state->get('filter.entry_type') != 2) {
									$filter_status = $this->state->get('filter.entry_status');

									if (isset($data->entries[$user->id])) {
										foreach ($data->entries[$user->id] as $entry) {
											if ($filter_status) {
												if ($filter_status == $entry->entry_status) {
													$entries[] = $entry;
												}
											} else {
												$entries[] = $entry;
											}
										}
									}
									//$entries = TOESHelper::getEntries($user->id, $data->show_id, $this->state->get('filter.entry_status'));
								}

								$placeholders = array();
								if ($this->state->get('filter.entry_type') != 1) {
									$filter_status = $this->state->get('filter.entry_status');
									if (isset($data->placeholders[$user->id])) {
										foreach ($data->placeholders[$user->id] as $placeholder) {
											if ($filter_status) {
												if ($filter_status == $placeholder->entry_status) {
													$placeholders[] = $placeholder;
												}
											} else {
												$placeholders[] = $placeholder;
											}
										}
									}
									//$placeholders = TOESHelper::getPlaceholders($user->id, $data->show_id, $this->state->get('filter.entry_status'));
								}

								if (!$entries && !$placeholders) {
									continue;
								}
								?>
								<div class="user-section" id="user-section-<?php echo $user->id; ?>">
									<?php 
										$this->user = &$user;
										$this->entries = $entries;
										$this->placeholders = $placeholders;

										echo $this->loadTemplate('userentries'); 
									?>
								</div>
								<br/>                                
							<?php endforeach; ?>

							<?php
							if ($this->cnt == 0) {
								echo JText::_('COM_TOES_SHOW_DONT_HAS_ENTRIES');
							} 
							?>
							<div class="pagination col-lg-12">               
								<?php echo $this->pagination->getPagesLinks(); ?>
							</div>
							<input type="hidden" name="limitstart" value="<?php echo $this->pagination->limitstart; ?>">
						</div>
					</div>
					<div class="clr"></div>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	//jQuery('div.doc_div').hide();
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

    function confirm_sync(show_id)
    {
		new jBox('Confirm',{
	        content: "<?php echo JText::_('COM_TOES_CONFIRM_SYNC_SHOW_DATA'); ?>",
	        width: '500px',
	        cancelButton : "<?php echo JText::_('JNO'); ?>",
	        confirmButton: "<?php echo JText::_('JYES'); ?>",
	        confirm: function() {
		        jQuery.ajax({
		            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.sync_db&action=show&show_id='+show_id,
		            type: 'post',
		        }).done(function(responseText){
					responseText = responseText.trim();
		        	jQuery('#loader').hide();
		            if(responseText == 1)
		                location.reload();
		            else
		                jbox_alert(responseText);
		        });
		
		        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
				jQuery('#progress-box').hide();
				jQuery('#progress-log-text').hide();
		        jQuery('#loader').show();
		    }
	    }).open(); 
    }
    
    jQuery(document).on('click','div.cleardocs a',function(){
			jQuery(this).closest('div.org_doc').parent().hide();
		});
		
	jQuery(document).on('click','a.documents-show',function(){
				
	            var rel = jQuery(this).attr('rel');
	            var ids = rel.split(';'); 
				jQuery('div#doc_div_'+ids[0]).show(); 
	        });

    jQuery(document).ready(function(){
		
		 jQuery('div.doc_div').hide(); 
		 
		if(jQuery('.approve-entry').length)
		{ 
	        jQuery('.approve-entry').on('click',function(){
				
				var rel = jQuery(this).attr('rel');
				var ids = rel.split(';');
				if(jQuery('span#docs_'+ids[0]+'_'+ids[1]).length && jQuery('div#doc_div_'+ids[0]).css('display') == 'none'){				
				jQuery('span#docs_'+ids[0]+'_'+ids[1]+' a.documents-show').trigger('click');	
				}else{            
	            jQuery.ajax({
	                url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.updateStatus&status=Accepted&entry_id='+ids[0],
	                type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    if(responseText == 1)
                        location.reload();
                    else
                        jbox_alert(responseText);
                }).fail(function(){
                    jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                });
                }
	        }); 
			
		}

        if(jQuery('.reject-entry').length)
        {
			jQuery('.reject-entry').on('click',function(){
	            var rel = jQuery(this).attr('rel');
	            var ids = rel.split(';');
	            if(jQuery('span#docs_'+ids[0]+'_'+ids[1]).length && jQuery('div#doc_div_'+ids[0]).css('display') == 'none'){				
				jQuery('span#docs_'+ids[0]+'_'+ids[1]+' a.documents-show').trigger('click');	
				}else{       
	
				jQuery.ajax({
					url: '<?php echo JUri::root(); ?>index.php?option=com_toes&view=entry&layout=reject&entry_id='+ids[0],
					type: 'post'
				}).done(function(responseText){
					responseText = responseText.trim();
					new jBox('Modal',{
						title: '<?php echo JText::_('COM_TOES_ENTRY_REFUSAL_POPUP_TITLE');?>',
						content: responseText,
						responsiveWidth: '600px',
						onCloseComplete: function(){
							this.destroy();
						}
					}).open();
				});
				}
	        });
    	}

        if(jQuery('.approve-placeholder').length)
        {
	        jQuery('.approve-placeholder').on('click',function(){
	            var rel = jQuery(this).attr('rel');
	            var ids = rel.split(';');
	            
	            jQuery.ajax({
	                url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=placeholder.updateStatus&status=Accepted&day_id='+ids[0],
	                type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    if(responseText == 1)
                        location.reload();
                    else
                        jbox_alert(responseText);
                }).fail(function(){
                    jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                });
	        });
        }

        if(jQuery('.reject-placeholder').length)
        {
	        jQuery('.reject-placeholder').on('click',function(){
	            var rel = jQuery(this).attr('rel');
	            var ids = rel.split(';');
	            
				new jBox('Confirm',{
			        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_REJECT_PLACEHOLDER'); ?>",
			        width: '400px',
			        cancelButton : NO_BUTTON,
			        confirmButton: YES_BUTTON,
			        confirm: function() {
                        jQuery.ajax({
                            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=placeholder.updateStatus&status=Rejected&day_id='+ids[0],
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            if(responseText == 1)
                                location.reload();
                            else
                                jbox_alert(responseText);
                        }).fail(function(){
                            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                        });
                    }
	            }).open();    
	        });
        }
        
        if(jQuery('#close_show').length)
        {
            jQuery('#close_show').on('click',function(){
                var rel = jQuery(this).attr('rel');

				new jBox('Confirm',{
			        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_CLOSE_SHOW'); ?>",
			        width: '400px',
			        cancelButton : NO_BUTTON,
			        confirmButton: YES_BUTTON,
			        confirm: function() {
                        jQuery.ajax({
                            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.updateStatus&status=Closed&id='+rel,
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            if(responseText == 1)
                            {
                                jQuery.ajax({
                                    url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.getEntriesNeedsConfirmation&id='+rel,
                                    type: 'post',
                                }).done(function(responseText){
									responseText = responseText.trim();
                                    if(responseText == 1)
                                    {
                                        jbox_alert("<?php echo JText::_('COM_TOES_NEED_TO_CONFIRM_ENTRIES_WARNING'); ?>");
                                    }
                                    setInterval(function(){location.reload()},2000);
                                }).fail(function(){
                                    jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                                });
                            }
                            else
                                jbox_alert(responseText);
                        }).fail(function(){
                            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                        });
                    }
                }).open();    
            });
        }

        if(jQuery('#open_show').length)
        {
            jQuery('#open_show').on('click',function(){
                var rel = jQuery(this).attr('rel');

                jQuery.ajax({
                    url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.updateStatus&status=Open&id='+rel,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    if(responseText == 1)
                        location.reload();
                    else
                        jbox_alert(responseText);
                }).fail(function(){
                    jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                });
            });
        }
	});
 
	function validate_confirm_entries(show_id, user_id, summary_id) {

		var amount_valid = false;
		
		var total_amount = jQuery('#summary_total_fees_'+summary_id).val();
		var paid_amount = jQuery('#summary_fees_paid_'+summary_id).val();

		if(jQuery.isNumeric(total_amount) && jQuery.isNumeric(paid_amount)) {
			amount_valid = true;
		}
		
		if(total_amount <= 0) {
			amount_valid = false;
		}

		if(amount_valid === true) {
			confirm_entries(show_id, user_id);
		} else {
			new jBox('Confirm',{
				content: "<?php echo JText::_('COM_TOES_AMOUNT_INFO_MISSING_CONFIRMATION'); ?>",
				width: '400px',
				cancelButton : '<?php echo JText::_('COM_TOES_GO_BACK'); ?>',
				confirmButton: '<?php echo JText::_('COM_TOES_CONFIRM_ENTRIES'); ?>',
				confirm: function() {
					confirm_entries(show_id, user_id);
				}, 
				cacnel: function() {

				}
			}).open();    
		}
	}
 
	function reload_user_section(user_id, show_id) {
		jQuery.ajax({
			url: '<?php echo JUri::root(); ?>index.php?option=com_toes&view=entryclerk&layout=default_userentries&user_id='+user_id+'&id='+show_id,
			type: 'post',
		}).done(function(responseText){
			jQuery('#loader').hide();
			responseText = responseText.trim();
			jQuery('#user-section-'+user_id).html(responseText);
		}).fail(function(){
			jQuery('#loader').hide();
			jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
		});

		jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
		jQuery('#loader').show();
	}
 
	function confirm_entries(show_id, user_id)
    {
		jQuery.ajax({
			url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.confirmEntries&user_id='+user_id+'&show_id='+show_id,
			type: 'post',
		}).done(function(responseText){
			jQuery('#loader').hide();
			responseText = responseText.trim();
			if(responseText == 1)
			{
				jbox_notice("<?php echo JText::_('COM_TOES_CONFIRMATION_MAIL_SEND'); ?>",'green');
				reload_user_section(user_id, show_id);
			}
			else if(responseText == 'resend')
			{
				new jBox('Confirm',{
					content: "<?php echo JText::_('COM_TOES_NO_NEW_INFORMATION_FOR_CONFIRMATION'); ?>",
					width: '400px',
					cancelButton : NO_BUTTON,
					confirmButton: YES_BUTTON,
					confirm: function() {
						jQuery.ajax({
							url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.confirmEntries&resend=1&user_id='+user_id+'&show_id='+show_id,
							type: 'post',
						}).done(function(responseText){
							jQuery('#loader').hide();
							responseText = responseText.trim();
							if(responseText == 1)
							{
								jbox_notice("<?php echo JText::_('COM_TOES_CONFIRMATION_MAIL_SEND'); ?>",'green');
								reload_user_section(user_id, show_id);
							}
							else
								jbox_alert(responseText);
						}).fail(function(){
							jQuery('#loader').hide();
							jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
						});
						jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
						jQuery('#loader').css('padding-top', (myHeight/2)+'px');
						jQuery('#progress-box').hide();
						jQuery('#progress-log-text').hide();
						jQuery('#loader').show();
					}
				}).open();    
			}
			else
				jbox_alert(responseText);
		}).fail(function(){
			jQuery('#loader').hide();
			jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
		});

		jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
		jQuery('#loader').show();
    }
    ///////////////////
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
								data:  { doc_no: doc_no, cat_id: jQuery('input#cat_id').val() },
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
	jQuery('div#document_container  a.add_document_type_btn').remove();
	}
});
     
</script>
 
