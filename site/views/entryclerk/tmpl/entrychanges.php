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
				<?php echo JText::_('COM_TOES_CHANGES_VIEW'); ?>
			</div>
		</form>
		<div class="clr"></div>
		<br/>

		<div class="seconouter">
			<div class="block">
				<?php
				if (@$this->state->get('filter.entry_type') != 2) {
					//$entries = TOESHelper::getShowEntries($data->show_id);
					$entries = $this->entries;
					$cats = TOESHelper::getShowCats($show_id);
				} else {
					$entries = '';
					$cats = '';
				}

				if ($entries):
					$prev_cat = 0;
					$cat_count = 0;
					foreach ($entries as $entry) :

						if ($entry->cat == $prev_cat)
							continue;

						if ($entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed')
							continue;

						$diff = array();
						$cat_details = TOESHelper::getCatDetails($entry->cat);

						if ($entry->copy_cat_breed != $cat_details->cat_breed) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_BREED');
							$field->entry_value = $entry->breed_name;
							$field->cat_value = $cat_details->breed_name;
							$diff[] = $field;
						}

						if ($entry->copy_cat_new_trait != $cat_details->cat_new_trait) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_NEW_TRAIT');
							$field->entry_value = $entry->copy_cat_new_trait ? JText::_('JYES') : JText::_('JNO');
							$field->cat_value = $cat_details->cat_new_trait ? JText::_('JYES') : JText::_('JNO');
							$diff[] = $field;
						}

						if ($entry->copy_cat_category != $cat_details->cat_category) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_CATEGORY');
							$field->entry_value = $entry->category;
							$field->cat_value = $cat_details->category;
							$diff[] = $field;
						}

						if ($entry->copy_cat_division != $cat_details->cat_division) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_DIVISION');
							$field->entry_value = $entry->division_name;
							$field->cat_value = $cat_details->division_name;
							$diff[] = $field;
						}

						if ($entry->copy_cat_color != $cat_details->cat_color) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_COLOR');
							$field->entry_value = $entry->color_name;
							$field->cat_value = $cat_details->color_name;
							$diff[] = $field;
						}

						if ($entry->copy_cat_name != $cat_details->cat_name) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_NAME');
							$field->entry_value = $entry->copy_cat_name;
							$field->cat_value = $cat_details->cat_name;
							$diff[] = $field;
						}

						if ($entry->copy_cat_gender != $cat_details->cat_gender) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_GENDER');
							$field->entry_value = $entry->gender_short_name;
							$field->cat_value = $cat_details->gender_short_name;
							$diff[] = $field;
						}

						if ($entry->copy_cat_registration_number != $cat_details->cat_registration_number) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_REGISTRATION_NUMBER');
							$field->entry_value = strtoupper($entry->copy_cat_registration_number);
							$field->cat_value = strtoupper($cat_details->cat_registration_number);
							$diff[] = $field;
						}

						if ($entry->copy_cat_date_of_birth != $cat_details->cat_date_of_birth) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_DATE_OF_BIRTH');
							$field->entry_value = $entry->copy_cat_date_of_birth;
							$field->cat_value = $cat_details->cat_date_of_birth;
							$diff[] = $field;
						}

						if ($entry->copy_cat_prefix != $cat_details->cat_prefix) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_PREFIX');
							$field->entry_value = $entry->cat_prefix_abbreviation;
							$field->cat_value = $cat_details->cat_prefix_abbreviation;
							$diff[] = $field;
						}

						if ($entry->copy_cat_title != $cat_details->cat_title) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_TITLE');
							$field->entry_value = $entry->cat_title_abbreviation;
							$field->cat_value = $cat_details->cat_title_abbreviation;
							$diff[] = $field;
						}

						if ($entry->copy_cat_suffix != $cat_details->cat_suffix) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_SUFFIX');
							$field->entry_value = $entry->cat_suffix_abbreviation;
							$field->cat_value = $cat_details->cat_suffix_abbreviation;
							$diff[] = $field;
						}

						if ($entry->copy_cat_sire_name != $cat_details->cat_sire) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_SIRE');
							$field->entry_value = $entry->copy_cat_sire_name;
							$field->cat_value = $cat_details->cat_sire;
							$diff[] = $field;
						}

						if ($entry->copy_cat_dam_name != $cat_details->cat_dam) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_DAM');
							$field->entry_value = $entry->copy_cat_dam_name;
							$field->cat_value = $cat_details->cat_dam;
							$diff[] = $field;
						}

						if ($entry->copy_cat_breeder_name != $cat_details->cat_breeder) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_BREEDER');
							$field->entry_value = $entry->copy_cat_breeder_name;
							$field->cat_value = $cat_details->cat_breeder;
							$diff[] = $field;
						}

						if ($entry->copy_cat_owner_name != $cat_details->cat_owner) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_OWNER');
							$field->entry_value = $entry->copy_cat_owner_name;
							$field->cat_value = $cat_details->cat_owner;
							$diff[] = $field;
						}

						if ($entry->copy_cat_lessee_name != $cat_details->cat_lessee) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_LESSEE');
							$field->entry_value = $entry->copy_cat_lessee_name;
							$field->cat_value = $cat_details->cat_lessee;
							$diff[] = $field;
						}

						if ($entry->copy_cat_competitive_region != $cat_details->cat_competitive_region) {
							$field = new stdClass();
							$field->field = JText::_('COM_TOES_COMPETITIVE_REGION');
							$field->entry_value = $entry->competitive_region_abbreviation;
							$field->cat_value = $cat_details->competitive_region_abbreviation;
							$diff[] = $field;
						}

						if (!$diff)
							continue;
						?>
						<br/>
						<div id="entry-cat-diff-<?php echo $entry->cat; ?>">
							<table style="width:98%;">
								<tr>
									<td style="border:1px solid #000;padding-left:5px;font-weight: bold;vertical-align: top;width:27%;" rowspan="<?php echo count($diff) + 1; ?>">
										<?php echo $entry->copy_cat_name; ?>
									</td>
									<td style="border:1px solid #000;padding-left:5px;font-weight: bold;width:10%;" >
										<?php echo JText::_('COM_TOES_FIELD'); ?>
									</td>
									<td style="border:1px solid #000;padding-left:5px;font-weight: bold;width:28%;" >
										<?php echo JText::_('COM_TOES_ENTRY_VALUE'); ?>
									</td>
									<td style="border:1px solid #000;padding-left:5px;font-weight: bold;width:28%;" >
										<?php echo JText::_('COM_TOES_CAT_VALUE'); ?>
									</td>
									<td style="padding-left:5px;vertical-align: top;width:5%;" rowspan="<?php echo count($diff) + 1; ?>">
										<?php if ($data->show_status != 'Held') : ?>
											<input type="button" value="<?PHP echo JText::_('COM_TOES_UPDATE_ENTRY'); ?>" onclick="jQuery(this).prop('disable', 0);updateChangestoEntry(<?php echo $data->show_id; ?>,<?php echo $entry->cat; ?>, 'entry-cat-diff-<?php echo $entry->cat; ?>')" />
										<?php endif; ?>
									</td>
								</tr>

								<?php
								foreach ($diff as $item):
									?>
									<tr>
										<td style="border:1px solid #000;padding-left:5px;width:10%;" >
											<?php echo $item->field; ?>
										</td>
										<td style="border:1px solid #000;padding-left:5px;width:28%;" >
											<?php echo $item->entry_value; ?>
										</td>
										<td style="border:1px solid #000;padding-left:5px;width:28%;" >
											<?php echo $item->cat_value; ?>
										</td>
									</tr>
									<?php
								endforeach;
								?>
							</table>
							<div class="clr"></div>
						</div>
						<?php
						$cat_count++;
						$prev_cat = $entry->cat;

					endforeach;

					if (!$cat_count){
						echo JText::_('COM_TOES_NO_DIFF');
					}
					?>
				<?php endif; ?>              
				<div class="clr"></div>
				<br/>
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
    
    function updateChangestoEntry(show_id,cat_id,parent_div)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.updateChangestoEntry',
            type: 'post',
            data: 'show_id='+show_id+'&cat_id='+cat_id,
        }).done(function(responseText){
			responseText = responseText.trim();
            if(responseText == 1)
            {
                jbox_notice("<?php echo JText::_('COM_TOES_ENTRY_UPDATED'); ?>",'green');
                jQuery('#'+parent_div).html('');
            }
            else
            {
                if(responseText.indexOf('Error: ') != -1)
                {
                    jbox_alert(responseText.replace('Error: ',''));
                }
                else
                {
                    var congress_ids = responseText.replace('Congress: ','');
                    var html = '<input value="<?php echo JText::_('JYES');?>" type="button" onClick="participateInCongress(\''+show_id+'\',\''+cat_id+'\',\''+congress_ids+'\',\''+parent_div+'\')"/> &nbsp;';
                    html += '<input value="<?php echo JText::_('JNO');?>"  type="button" onClick="jQuery(\'#'+parent_div+'\').html(\'\');location.reload();"/>';
                    
                    jQuery('#'+parent_div).html('<div><?php echo JText::_('COM_TOES_SHOULD_CAT_PARTICIPATE_IN_CONGRESS')?><br/><br/>'+html+'</div>');
                }
            }
        });
    }
    
    function participateInCongress(show_id, cat_id, congress_ids, parent_div)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.participateInCongress',
            type: 'post',
            data: 'show_id='+show_id+'&congress_id='+congress_ids+'&cat_id='+cat_id,
        }).done(function(responseText){
			responseText = responseText.trim();
            if(responseText === '1')
            {
                jbox_notice("<?php echo JText::_('COM_TOES_ENTRY_UPDATED'); ?>",'green');
                jQuery('#'+parent_div).html('');
				location.reload();
            }
            else
            {
                jbox_alert(responseText);
            }
        });
    }
</script>