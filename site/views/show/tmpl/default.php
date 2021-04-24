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
JHTML::_('behavior.modal');
$app = JFactory::getApplication();

$user = JFactory::getUser();
$pk = $app->input->getInt('id');

$data = $this->item;
$show_status = TOESHelper::getShowDetails($data->show_id)->show_status;

$user_id = isset($data->summary->summary_user)?$data->summary->summary_user:$user->id;
?>

<div class ="show-details-main">
    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_SHOW_DETAILS'); ?></div>
        </div>
        <div class="clr"></div>
    </div>
    <div class ="seconouter">
        <br/>
        <div class ="block">
            <label for="club" class="lbl">
                <?php echo JText::_('COM_TOES_SHOW_CLUB'); ?>
            </label>

            <span>
                <?php echo $data->club_name; ?>
            </span>
        </div>

        <div class ="block">
            <label for="club" class="lbl">
                <?php echo JText::_('COM_TOES_SHOW_SHOW'); ?>
            </label>
            <span>
                <?php
                $start_date = date('d', strtotime($data->show_start_date));
                $start_date_month = date('M', strtotime($data->show_start_date));
                $start_date_year = date('Y', strtotime($data->show_start_date));

                $end_date = date('d', strtotime($data->show_end_date));
                $end_date_month = date('M', strtotime($data->show_end_date));
                $end_date_year = date('Y', strtotime($data->show_end_date));

                echo $start_date_month.' '.$start_date;

                if ($end_date_year != $start_date_year){
                    echo ' '.$start_date_year;
                }

                if ($end_date_month != $start_date_month){
                    if(date('t', strtotime($data->show_start_date)) != $start_date)
                        echo ' - '.date('t', strtotime($data->show_start_date));
                    if($end_date == '01')
                        echo ', ' .$end_date_month.' '.$end_date;
                    else
                        echo ', ' .$end_date_month.' 01 - '.$end_date;
                } else {
                    if($start_date != $end_date)
                        echo ' - ' . $start_date_month.' '.$end_date;
                }

                echo ' '.$end_date_year;


                if ($data->address_city)
                    echo ', ' . $data->address_city . ', ';
                if ($data->address_state)
                    echo $data->address_state . ', ';
                if ($data->address_city)
                    echo $data->address_country;
                ?>
            </span>
        </div>

        <div class ="block">
            <label class="lbl">
                <?php echo JText::_('COM_TOES_SHOW_FORMAT'); ?>
            </label>

            <span>
                <?php 
                    switch ($data->show_format)
                    {
                        case 1:
                            echo JText::_('Back to Back');
                            break;
                        case 2:
                            echo JText::_('Alternative');
                            break;
                        case 3:
                            echo JText::_('Continuous');
                            break;
                }
                 ?>
            </span>
        </div>
        <div class="clr"></div>
        <br/>

        <div class ="block">
            <div class="img">
                <img alt="cats..." src="media/com_toes/images/cat2.png" style="width:48px;"/>
            </div>

            <div class="details">
                <label class="full-length"><?php echo JText::_('COM_TOES_SHOW_CURRENT_ENTRIES'); ?></label>
                <div class="entries">
                    <div class="show-entries-header">
                        <span class="action-buttons">&nbsp;</span>
                        <span class="name"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER');?></span>
                        <span class="status"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_STATUS_HEADER');?></span>
                        <span class="days"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER');?></span>
                        <span class="class"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER');?></span>
                        <span class="congress"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER');?></span>
                        <span class="exh"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER');?></span>
                        <span class="forsale"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_FORSALE_HEADER');?></span>
                    </div>
                    <?php
                    if (@$data->entries) {
                        $prev_cat='';
                        $prev_entry_status = '';
                    ?>
                        <?php
                        $i = 1;
                        foreach ($data->entries as $entry) {
                            $days = $entry->showdays;
                            if($entry->congress)
                                $congress_names = $entry->congress;
                            else
                                $congress_names = '-';
                        ?>
                            <div class="item <?php echo ($entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed')?'grey_entry':''?>">
                                <span class="action-buttons">
                                    &nbsp;
                                    <?php if($entry->cat != $prev_cat || $prev_entry_status != $entry->entry_status) :?>
                                        <span class="hasTip" title="<?php echo JText::_('VIEW_DETAILS');?>">
                                            <a href="javascript:void(0)" rel="<?php echo $entry->cat; ?>" class="view-entry-details">
                                                <img alt="<?php echo JText::_('VIEW_DETAILS');?>" src="media/com_toes/images/view16X16.png" />
                                            </a>
                                        </span>
                                        <?php if($entry->entry_status != 'Cancelled' && $entry->entry_status != 'Cancelled & Confirmed' ): ?>
                                            <span class="hasTip" title="<?php echo JText::_('CANCEL_ENTRY');?>">
                                                <a href="javascript:void(0)" rel="<?php echo $entry->entry_id.';'.$entry->show_id; ?>" class="cancel-entry" onclick="cancel_entry(this,'<?php echo JText::_('COM_TOES_CONFIRM_TO_CANCEL_ENTRY');?>');">
                                                    <img alt="<?php echo JText::_('CANCEL_ENTRY');?>" src="media/com_toes/images/delete16X16.png" />
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                        <?php if($show_status == 'Open' && ($entry->entry_status == 'Rejected' || $entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed') ): ?>
                                            <span class="hasTip" title="<?php echo JText::_('REENTER_ENTRY');?>">
                                                <a href="javascript:void(0)" rel="<?php echo $entry->entry_id.';'.$entry->show_id; ?>" class="reenter-entry" onclick="reenter_entry(this);">
                                                    <img alt="<?php echo JText::_('REENTER_ENTRY');?>" src="media/com_toes/images/reactivate16X16.png" />
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                        <?php if($show_status == 'Open' && ($entry->entry_status == 'New' || $entry->entry_status == 'Accepted' || $entry->entry_status == 'Rejected' || $entry->entry_status == 'Waiting List') ) : ?>
                                            <span class="hasTip" title="<?php echo JText::_('EDIT_ENTRY');?>">
                                                <a href="javascript:void(0)" onclick="edit_entry('<?php echo $entry->cat.';'.$entry->show_id.';'.$entry->summary_user; ?>','add-entry-div-<?php echo $user_id; ?>');" class="edit-entry">
                                                    <img alt="<?php echo JText::_('EDIT_ENTRY');?>" src="media/com_toes/images/edit16X16.png" />
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                        <?php 
                                            $prev_cat = $entry->cat; 
                                            $prev_entry_status = $entry->entry_status;
                                        ?>
                                    <?php endif; ?>
                                </span>
                                <span class="name"><?php echo $entry->cat_prefix_abbreviation.' '.$entry->cat_title_abbreviation.' '.$entry->copy_cat_name.' '.$entry->cat_suffix_abbreviation;?></span>
                                <span class="status"><?php echo $entry->entry_status;?></span>
                                <span class="days">
                                    <?php echo $days;?>
                                </span>
                                <span class="class"><?php echo $entry->Show_Class;?></span>
                                <span class="congress"><?php echo $congress_names;?></span>
                                <span class="exh"><?php echo ($entry->exhibition_only)?JText::_('JYES'):JText::_('JNO');?></span>
                                <span class="forsale"><?php echo ($entry->for_sale)?JText::_('JYES'):JText::_('JNO');?></span>
                                <div class="clr"></div>
                            </div>
                            <?php if($entry->cat != @$data->entries[$i]->cat) :?>
                                <div id="cat-<?php echo $entry->cat; ?>" class="cat-details" ></div>
                                <div class="clr"></div>
                            <?php endif; ?>
                        <?php
                            $i++;
                        }
                    }

                    if(@$data->entries && @$data->placeholders)
                    {
                    ?>
                        <div class="clr" style="padding: 0; border-bottom: 1px dashed #000;"></div>
                    <?php
                    }

                    if(@$data->placeholders)
                    {
                        $prev_placeholder = '';
                        $prev_placeholder_status = '';
                        foreach ($data->placeholders as $placeholder) {
                            $days = $placeholder->showdays;
                            ?>
                            <div class="item <?php echo ($placeholder->entry_status == 'Cancelled' || $placeholder->entry_status == 'Cancelled & Confirmed')?'grey_entry':''?>">
                                <span class="action-buttons">
                                    &nbsp;
                                    <?php if($placeholder->placeholder_id != $prev_placeholder || $prev_placeholder_status != $placeholder->entry_status) :?>
                                        <?php if($placeholder->entry_status != 'Cancelled' && $placeholder->entry_status != 'Cancelled & Confirmed' ): ?>
                                            <?php if($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Waiting List'): ?>
                                                <span class="hasTip" title="<?php echo JText::_('DELETE_PLACEHOLDER');?>">
                                                    <a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id.';'.$placeholder->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this,'<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_PLACEHOLDER');?>');">
                                                        <img alt="<?php echo JText::_('DELETE_PLACEHOLDER');?>" src="media/com_toes/images/cancel16X16.png" />
                                                    </a>
                                                </span>
                                            <?php else: ?>
                                                <span class="hasTip" title="<?php echo JText::_('CANCEL_PLACEHOLDER');?>">
                                                    <a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id.';'.$placeholder->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this,'<?php echo JText::_('COM_TOES_CONFIRM_TO_REJECT_PLACEHOLDER');?>');">
                                                        <img alt="<?php echo JText::_('CANCEL_PLACEHOLDER');?>" src="media/com_toes/images/delete16X16.png" />
                                                    </a>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($show_status == 'Open' && ($placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Cancelled' || $placeholder->entry_status == 'Cancelled & Confirmed') ): ?>
                                            <span class="hasTip" title="<?php echo JText::_('REENTER_PLACEHOLDER');?>">
                                                <a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id.';'.$placeholder->placeholder_show; ?>" class="reenter-placeholder" onclick="reenter_placeholder(this);">
                                                    <img alt="<?php echo JText::_('REENTER_PLACEHOLDER');?>" src="media/com_toes/images/reactivate16X16.png" />
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                        <?php if($show_status == 'Open' && ($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Accepted' || $placeholder->entry_status == 'Waiting List') ) : ?>
                                            <span class="hasTip" title="<?php echo JText::_('EDIT_PLACEHOLDER');?>">
                                                <a href="javascript:void(0)" onclick="edit_placeholder('<?php echo $placeholder->placeholder_id; ?>','<?php echo $user_id;?>','add-placeholder-div-<?php echo $user_id; ?>');" class="edit-placeholder">
                                                    <img alt="<?php echo JText::_('EDIT_PLACEHOLDER');?>" src="media/com_toes/images/edit16X16.png" />
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                        <?php if(($show_status == 'Open' && $placeholder->entry_status == 'New') || $placeholder->entry_status == 'Accepted' || $placeholder->entry_status == 'Confirmed' || $placeholder->entry_status == 'Confirmed & Paid') : ?>
                                            <span class="hasTip" title="<?php echo JText::_('CONVERT_TO_ENTRY');?>">
                                                <a href="javascript:void(0)" onclick="convert_placeholder('<?php echo $placeholder->placeholder_id; ?>','<?php echo $placeholder->placeholder_show; ?>','<?php echo $user_id;?>','add-placeholder-div-<?php echo $user_id; ?>');" class="convert-placeholder">
                                                    <img alt="<?php echo JText::_('CONVERT_TO_ENTRY');?>" src="media/com_toes/images/convert16X16.png" />
                                                </a>
                                            </span>
                                        <?php endif; ?>
                                        <?php 
                                            $prev_placeholder = $placeholder->placeholder_id; 
                                            $prev_placeholder_status = $placeholder->entry_status; 
                                        ?>
                                    <?php endif; ?>
                                </span>
                                <span class="name"><?php echo JText::_('COM_TOES_PLACEHOLDER');?></span>
                                <span class="status"><?php echo $placeholder->entry_status;?></span>
                                <span class="days"><?php echo $days;?></span>
                                <span class="class">&nbsp;</span>
                                <span class="congress">&nbsp;</span>
                                <span class="exh">&nbsp;</span>
                                <span class="forsale">&nbsp;</span>
                            </div>
                            <div class="clr" style="padding: 0;"></div>
                        <?php
                        }
                    }                    
                    ?>
                </div>
            </div>
            <div class="clr"></div>
            <?php if($data->show_uses_toes != '1'): ?>
                <?php echo JText::_('COM_TOES_SHOW_NOT_USING_TOES'); ?>
            <?php else: ?>
                <?php if($show_status == 'Open') : ?>
                    <?php /* if(TOESHelper::isShowHasSpace($data->show_id)) :  */ ?>
                        <input type="hidden" id="add_user" name="add_user" value="<?php echo $user_id;?>" />
                        <div class="add-entry-div" id="add-entry-div-<?php echo $user_id;?>">
                            <a href="javascript:void(0);" onclick="add_new_entry(<?php echo $data->show_id;?>,'add-entry-div-<?php echo $user_id; ?>');">
                                <img alt="<?php echo JText::_('COM_TOES_ADD_CAT');?>" src="media/com_toes/images/add16X16.png" />
                                <?php echo JText::_('COM_TOES_ADD_CAT'); ?>
                            </a>
                        </div>
                        <?php if(TOESHelper::isAdmin() || TOESHelper::is_entryclerk($user->id, $data->show_id)): ?>
                            <div class="add-entry-div" >
                                <a href="javascript:void(0);" onclick="add_third_party_entry(<?php echo $data->show_id;?>,'add-entry-div-<?php echo $user_id; ?>');">
                                    <img alt="<?php echo JText::_('COM_TOES_ADD_THIRD_PARTY_ENTRY');?>" src="media/com_toes/images/add16X16.png" />
                                    <?php echo JText::_('COM_TOES_ADD_THIRD_PARTY_ENTRY'); ?>
                                </a>
                            </div>                                    
                        <?php endif; ?>
                        <div class="add-placeholder-div" id="add-placeholder-div-<?php echo $user_id;?>">
                            <a href="javascript:void(0);" onclick="add_new_placeholder(<?php echo $data->show_id;?>,'add-placeholder-div-<?php echo $user_id; ?>');">
                                <img alt="<?php echo JText::_('COM_TOES_ADD_PLACEHOLDER');?>" src="media/com_toes/images/add16X16.png" />
                                <?php echo JText::_('COM_TOES_ADD_PLACEHOLDER'); ?>
                            </a>
                        </div>
                    <?php /*else: ?>
                        <div style="padding:10px;">
                            <?php echo JText::_("COM_TOES_SHOW_IS_FULL");?>
                        </div>
                    <?php endif; */?>
                <?php elseif($show_status == 'Approved'): ?>
                    <div style="padding:10px;">
                        <?php echo JText::_("COM_TOES_SHOW_NOT_OPEN");?>
                    </div>
                <?php elseif($show_status == 'Closed'): ?>
                    <div style="padding:10px;">
                        <?php echo JText::_("COM_TOES_SHOW_IS_CLOSED");?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if(@$data->entries && isset($data->summary)) : ?>
        <div class ="block">
            <div class="img">
                <img alt="summary..." src="media/com_toes/images/information.png" style="width:48px"/>
            </div>

            <div class="details">
                <label class="full-length"><?php echo JText::_('COM_TOES_SHOW_SUMMARY'); ?></label>
                <div class="summary-details">
                    <br/>
                    <div>
                        <label style="font-weight:normal;"><?php echo ( $data->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_CAGES') );?></label>
                            <span><?php echo $data->summary->summary_single_cages;?></span>
                    </div>
                    <div>
                        <label style="font-weight:normal;"><?php echo ( $data->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_CAGES') );?></label>
                        <span><?php echo $data->summary->summary_double_cages;?></span>
                    </div>
                    <div>
                        <label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_PERSONAL_CAGES');?></label>
                        <span><?php echo $data->summary->summary_personal_cages?JText::_('JYES'):JText::_('JNO');?></span>
                    </div>
                    <div>
                        <label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_GROOMING_SPACE');?></label>
                        <span><?php echo $data->summary->summary_grooming_space?JText::_('JYES'):JText::_('JNO');?></span>
                    </div>
                    <div>
                        <label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_BENCHING_REQUEST');?></label>
                        <span><?php echo $data->summary->summary_benching_request;?></span>
                    </div>
                    <br/>
                    <div>
                        <label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_REMARKS');?></label>
                        <span><?php echo $data->summary->summary_remarks;?></span>
                    </div>
                    <div class="clr"></div>
                    <a href="javascript:void(0);" onclick="edit_summary(<?php echo $data->summary->summary_id;?>);">
                        <img alt="<?php echo JText::_('COM_TOES_EDIT_SUMMARY');?>" src="media/com_toes/images/edit16X16.png" />
                        <?php echo JText::_('COM_TOES_EDIT_SUMMARY'); ?>
                    </a>
                    <div class="edit-summary-div" id="edit-summary-<?php echo $data->summary->summary_id;?>-div">
                    </div>
                </div>
            </div>
            <div class="clr"></div>
        </div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">

    jQuery(document).ready(function(){
        jQuery('.view-entry-details').on('click',function(){
            var rel = jQuery(this).attr('rel');
            
            if(jQuery('#cat-'+rel+':visible').length)
            {
                jQuery('.cat-details').hide();
                return;
            }
            
            jQuery('.cat-details').hide();
            
            jQuery('#cat-'+rel).show();
            jQuery('#cat-'+rel).html('<img alt="loading..." src="media/com_toes/images/loading.gif" />');
            
            jQuery.ajax({
                url: 'index.php?option=com_toes&view=cat&layout=short&id='+rel+'&tmpl=component',
                type: 'post',
			}).done( function(responseText){
				responseText = responseText.trim();
                jQuery('#cat-'+rel).html(responseText);
		  	});
        });
    });

</script>

