<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

?>

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
				<?php echo JText::_('COM_TOES_ENTRY_CLERK_LIST_VIEW'); ?>
			</div>
		</form>
		<div class="clr"></div>
		<br/>

		<div class="seconouter">
			<div class ="block entryclerk">
				<div class="details">
					<br/>
					<div class="entries list-view">
						<div class="show-entries-header">
							<span class="action-buttons">&nbsp;</span>
							<span class="status"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_STATUS_HEADER'); ?></span>
							<span class="name"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER'); ?></span>
							<span class="days"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER'); ?></span>
							<span class="class"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER'); ?></span>
							<span class="exh"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER'); ?></span>
							<span class="forsale"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_FORSALE_HEADER'); ?></span>
							<span class="congress"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER'); ?></span>
							<span class="exhibitor"><?php echo JText::_('EXHIBITOR'); ?></span>
							<span class="created"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_DATE_CREATED_HEADER'); ?></span>
							<div class="clr"><hr/></div>
						</div>
						<?php
						$cnt = 0;
						if ($data->total_entries) {
							$prev_cat = '';
							$prev_placeholder = '';
							$prev_entry_status = '';
							
							foreach ($data->total_entries as $entry) {

								if ($isContinuous){
									$days = JText::_('JALL');
								} else {
									$days = $entry->showdays;
								}

								if (isset($entry->congress) && $entry->congress) {
									$congress_names = $entry->congress;
								} else {
									$congress_names = '-';
								}

								if ($entry->type == 'entry') {
									?>
									<div class="item <?php echo ($entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed') ? 'grey_entry' : '' ?>">
										<span class="action-buttons">
											<?php if ($entry->cat != $prev_cat || $prev_entry_status != $entry->entry_status) : ?>
												<span class="hasTip" title="<?php echo JText::_('VIEW_DETAILS'); ?>">
													<a href="javascript:void(0)" rel="<?php echo $entry->entry_id; ?>" onclick="view_entry_details(this, 'list-cat-<?php echo $entry->entry_id; ?>', 0);" class="view-entry-details">
														<i class="fa fa-file-text-o"></i> 
													</a>
												</span>
												<?php if ($data->show_status != 'Held'): ?>
													<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Rejected'): ?>
														<span class="hasTip" title="<?php echo JText::_('APPROVE_ENTRY'); ?>">
															<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->show_id; ?>" class="approve-entry">
																<i class="fa fa-check"></i> 
															</a>
														</span>
													<?php endif; ?>
													<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Accepted'): ?>
														<span class="hasTip" title="<?php echo JText::_('REJECT_ENTRY'); ?>">
															<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->show_id; ?>" class="reject-entry">
																<i class="fa fa-remove"></i> 
															</a>
														</span>
													<?php endif; ?>
													<span class="hasTip" title="<?php echo JText::_('EDIT_ENTRY'); ?>">
														<a href="javascript:void(0)" onclick="edit_entry('<?php echo $entry->cat . ';' . $entry->show_id . ';' . $entry->summary_user; ?>', 'entry-<?php echo $entry->entry_id ?>');" class="edit-entry">
															<i class="fa fa-edit"></i> 
														</a>
													</span>
													<?php if ($entry->entry_status != 'Cancelled' && $entry->entry_status != 'Cancelled & Confirmed'): ?>
														<span class="hasTip" title="<?php echo JText::_('CANCEL_ENTRY'); ?>">
															<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->show_id; ?>" class="cancel-entry" onclick="cancel_entry(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_REJECT_ENTRY'); ?>');">
																<i class="fa fa-remove"></i> 
															</a>
														</span>
													<?php endif; ?>
													<span class="hasTip" title="<?php echo JText::_('DELETE_ENTRY'); ?>">
														<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->show_id; ?>" class="delete-entry" onclick="delete_entry(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_ENTRY'); ?>');">
															<i class="fa fa-trash"></i> 
														</a>
													</span>
												<?php endif; ?>
												<?php
												$prev_cat = $entry->cat;
												$prev_entry_status = $entry->entry_status;
												?>
											<?php endif; ?>
											&nbsp;
										</span>
										<span class="status"><?php echo $entry->entry_status; ?></span>
										<span class="name"><?php echo $entry->cat_prefix_abbreviation . ' ' . $entry->cat_title_abbreviation . ' ' . $entry->copy_cat_name . ' ' . $entry->cat_suffix_abbreviation; ?></span>
										<span class="days">
											<?php echo $days; ?>
										</span>
										<span class="class"><?php echo $entry->Show_Class; ?></span>
										<span class="exh"><?php echo ($entry->exhibition_only) ? JText::_('JYES') : JText::_('JNO'); ?></span>
										<span class="forsale"><?php echo ($entry->for_sale) ? JText::_('JYES') : JText::_('JNO'); ?></span>
										<span class="congress"><?php echo $congress_names; ?></span>
										<span class="exhibitor"><?php echo $entry->exhibitor; ?></span>
										<span class="created"><?php echo date('M d, H:i', strtotime($entry->entry_date_created)); ?></span>
										<div class="clr"></div>
										<?php if (isset($data->total_entries[$cnt]) || (isset($data->total_entries[$cnt]) && $data->total_entries->type != $entry->type || $data->total_entries->cat != $entry->cat)) { ?>
											<div id="list-cat-<?php echo $entry->entry_id; ?>" class="cat-details" style="display: none;"></div>
											<div class="clr"></div>
											<div class="add-entry-div" id="entry-<?php echo $entry->entry_id ?>"></div>
											<div class="clr"></div>
										<?php } ?>
									</div>
								<?php } else { ?>
									<div class="item <?php echo ($entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed') ? 'grey_entry' : '' ?>">
										<span class="action-buttons">
											<?php if ($entry->placeholder_id != $prev_placeholder || $prev_placeholder_status != $entry->entry_status) : ?>
												<?php if ($data->show_status != 'Held'): ?>
													<?php if ($entry->entry_status != 'Cancelled' && $entry->entry_status != 'Cancelled & Confirmed'): ?>
														<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Rejected' || $entry->entry_status == 'Waiting List'): ?>
															<?php /* <span class="hasTip" title="<?php echo JText::_('DELETE_PLACEHOLDER');?>">
															  <a href="javascript:void(0)" rel="<?php echo $entry->placeholder_day_id.';'.$entry->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this,'<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_PLACEHOLDER');?>');">
															  <i class="fa fa-trash"></i> 
															  </a>
															  </span> */ ?>
														<?php else: ?>
															<span class="hasTip" title="<?php echo JText::_('CANCEL_PLACEHOLDER'); ?>">
																<a href="javascript:void(0)" rel="<?php echo $entry->placeholder_day_id . ';' . $entry->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_REJECT_PLACEHOLDER'); ?>');">
																	<i class="fa fa-remove"></i> 
																</a>
															</span>
														<?php endif; ?>
													<?php endif; ?>                                        
													<?php if (($entry->entry_status == 'Rejected' || $entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed')): ?>
														<span class="hasTip" title="<?php echo JText::_('REENTER_PLACEHOLDER'); ?>">
															<a href="javascript:void(0)" rel="<?php echo $entry->placeholder_day_id . ';' . $entry->placeholder_show; ?>" class="reenter-placeholder" onclick="reenter_placeholder(this);">
																<i class="fa fa-power-off"></i> 
															</a>
														</span>
													<?php endif; ?>
													<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Rejected'): ?>
														<span class="hasTip" title="<?php echo JText::_('APPROVE_PLACEHOLDER'); ?>">
															<a href="javascript:void(0)" rel="<?php echo $entry->placeholder_day_id . ';' . $entry->placeholder_show; ?>" class="approve-placeholder">
																<i class="fa fa-check"></i> 
															</a>
														</span>
													<?php endif; ?>
													<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Accepted'): ?>
														<span class="hasTip" title="<?php echo JText::_('REJECT_PLACEHOLDER'); ?>">
															<a href="javascript:void(0)" rel="<?php echo $entry->placeholder_day_id . ';' . $entry->placeholder_show; ?>" class="reject-placeholder">
																<i class="fa fa-remove"></i> 
															</a>
														</span>
													<?php endif; ?>
													<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Accepted' || $entry->entry_status == 'Waiting List') : ?>
														<span class="hasTip" title="<?php echo JText::_('EDIT_PLACEHOLDER'); ?>">
															<a href="javascript:void(0)" onclick="edit_placeholder('<?php echo $entry->placeholder_id; ?>', '<?php echo $entry->placeholder_exhibitor; ?>', 'placeholder-<?php echo $entry->placeholder_day_id ?>');" class="edit-placeholder">
																<i class="fa fa-edit"></i> 
															</a>
														</span>
													<?php endif; ?>
													<span class="hasTip" title="<?php echo JText::_('DELETE_PLACEHOLDER'); ?>">
														<a href="javascript:void(0)" rel="<?php echo $entry->placeholder_day_id . ';' . $entry->placeholder_show; ?>" class="delete-placeholder" onclick="delete_placeholder(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_PLACEHOLDER'); ?>');">
															<i class="fa fa-trash"></i> 
														</a>
													</span>
												<?php endif; ?>
												<?php
												$prev_placeholder = $entry->placeholder_id;
												$prev_placeholder_status = $entry->entry_status;
												?>                                        
											<?php endif; ?>
											&nbsp;
										</span>
										<span class="status"><?php echo $entry->entry_status; ?></span>
										<span class="name"><?php echo JText::_('COM_TOES_PLACEHOLDER'); ?></span>
										<span class="days">
											<?php echo $days; ?>
										</span>
										<span class="class">&nbsp;</span>
										<span class="exh">&nbsp;</span>
										<span class="forsale">&nbsp;</span>
										<span class="congress">&nbsp;</span>
										<span class="exhibitor"><?php echo $entry->exhibitor; ?></span>
										<span class="created"><?php echo date('M d, H:i', strtotime($entry->placeholder_day_date_created)); ?></span>
										<div class="clr"></div>
										<?php if (isset($data->total_entries[$cnt]) || (isset($data->total_entries[$cnt]) && $data->total_entries->type != $entry->type || $data->total_entries->placeholder_id != $entry->placeholder_id)) { ?>
											<div class="add-placeholder-div" id="placeholder-<?php echo $entry->placeholder_day_id ?>"></div>
										<?php } ?>
									</div>  
									<?php
								}
								$cnt++;
							}
						}
						if ($cnt == 0) {
							echo JText::_('COM_TOES_SHOW_DONT_HAS_ENTRIES');
						}
						?>
					</div>
				</div>
				<div class="clr"></div>
			</div>
		</div>
	</div>
</div>

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

    jQuery(document).ready(function(){
		if(jQuery('.approve-entry').length)
		{
	        jQuery('.approve-entry').on('click',function(){
	            var rel = jQuery(this).attr('rel');
	            var ids = rel.split(';');
	            
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
	        });
		}

        if(jQuery('.reject-entry').length)
        {
			jQuery('.reject-entry').on('click',function(){
	            var rel = jQuery(this).attr('rel');
	            var ids = rel.split(';');
	
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
</script>