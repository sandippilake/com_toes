<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

$class_count = 0;
$breed_count = 0;
$gender_count = 0;
$hairlength_count = 0;
$category_count = 0;
$division_count = 0;
$color_count = 0;
$title_count = 0;
$cwd_count = 0;

?>
<style type="text/css">
.data_div {
    border: 1px solid;
    border-radius: 5px;
    padding: 5px;
    min-height: 30px;
}
.data_lable
{
    background: none repeat scroll 0 0 #DEE8F3;
    border-radius: 4px 4px 4px 4px;
    font-family: "arial";
    font-size: 12px;
    font-weight: normal;
    line-height: 12px;
    list-style: none outside none;
    margin: 2px 1px 1px;
    padding: 2px 5px 4px;    
}
td{
    vertical-align: top;
}
input {
	background-color: #ffffff;
}
</style>

<div id="loader" class="loader">
    <span id="loader-container">
        <img id="loader-img" src="media/com_toes/images/loading.gif" />
        <?php echo JText::_('COM_TOES_LOADING'); ?>
    </span>
    <div id="progress-box">
        <span id="progress-bar">&nbsp;</span>
        <br/>
        <span id="progress-count">
            <label id="progress-count-processed">?</label> / <label id="progress-count-total">?</label>
        </span>
    </div>
</div>

<div>
    <?php echo JText::_('COM_TOES_SELECT_CONGRESS_CRITERIA'); ?>
</div>

<table style="width: 100%;">
    <thead>
        <tr>
            <th style="width:5%; text-align: left;"><input type="checkbox" id="filter_all" name="filter_all" onchange="select_all_filter(this);" /></th>
            <th style="width:20%; text-align: left;"><?php echo JText::_('COM_TOES_CRITERIA'); ?></th>
            <th style="width:35%; text-align: left;"><?php echo JText::_('COM_TOES_LOOKUP'); ?></th>
            <th style="width:5%; text-align: left;">&nbsp;</th>
            <th style="text-align: left;"><?php echo JText::_('COM_TOES_SELECTED_VALUE'); ?></th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td>&nbsp;</td>
            <td><?php echo JText::_('COM_TOES_SHOW_CLASS');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->show_classes, 'show_class', '', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'show_class\',\'class\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <!-- <input type="text" id="class_text" name="class_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="class_place">
                    <?php 
                    if(isset($this->filters->class_text) && $this->filters->class_text) { 
                        $class_text = explode(',', $this->filters->class_text);
                        $class_values = explode(',', $this->filters->class_value); 
                        $class_count = count($class_text);
                        for($i = 0; $i< count($class_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="class_<?php echo $class_values[$i];?>">
                                <?php echo $class_text[$i];?>&nbsp;&nbsp;
                                <span class="class_remove" onclick="removevalue('class',<?php echo $class_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_class" name="count_class" value="<?php echo $class_count;?>">
                <input type="hidden" id="class_value" name="class_value" value="<?php echo @$this->filters->class_value;?>" />
            </td>
        </tr>

        <tr>
            <td><input onclick="toggle_field(this,'breedname');" type="checkbox" value="1" id="breed_filter" name="breed_filter" <?php echo (@$this->filters->breed_filter)?'checked="checked"':'';?> /></td>
            <td><label for="breed_filter"><?php echo JText::_('COM_TOES_BREED');?></label></td>
            <td>
                <input type="text" id="breedname" style="color: #999;" value="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_BREED');?>" onblur="if(this.value==''){this.value='<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_BREED');?>'; this.style.color='#999';}" onfocus="if(this.value=='<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_BREED');?>'){ this.value='';this.style.color='#000';}" >
                <?php echo JHTML::_('select.genericlist',$this->breeds, 'copy_cat_breed', 'style="display:none;"', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'copy_cat_breed\',\'breed\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <!-- <input type="text" id="breed_text" name="breed_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="breed_place">
                    <?php     
                    //echo '<pre>';
                    //print_r($this->filters);
                    //echo '</pre>';                
                    if(isset($this->filters->breed_text) && $this->filters->breed_text) { 
                        $breed_text = explode(',', $this->filters->breed_text);
                        //$breed_values = explode(',', $this->filters->class_value); 
                        $breed_values = explode(',', $this->filters->breed_value); 
                        //var_dump($breed_values);
                        $breed_count = count($breed_text);
                        for($i = 0; $i< count($breed_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="breed_<?php echo $breed_values[$i];?>">
                                <?php echo $breed_text[$i];?>&nbsp;&nbsp;
                                <span class="breed_remove" onclick="removevalue('breed',<?php echo $breed_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_breed" name="count_breed" value="<?php echo $breed_count;?>">
                <input type="hidden" id="breed_value" name="breed_value" value="<?php echo @$this->filters->breed_value;?>" />
                
            </td>
        </tr>

        <tr>
            <td><input onclick="toggle_field(this,'gender');" type="checkbox" value="1" id="gender_filter" name="gender_filter" <?php echo (@$this->filters->gender_filter)?'checked="checked"':'';?> /></td>
            <td><label for="gender_filter"><?php echo JText::_('COM_TOES_GENDER');?></label></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->genders, 'gender', '', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'gender\',\'gender\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <!-- <input type="text" id="gender_text" name="gender_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="gender_place">
                    <?php 
                    if(isset($this->filters->gender_text) && $this->filters->gender_text) { 
                        $gender_text = explode(',', $this->filters->gender_text);
                        $gender_values = explode(',', $this->filters->gender_value); 
                        $gender_count = count($gender_text);
                        for($i = 0; $i< count($gender_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="gender_<?php echo $gender_values[$i];?>">
                                <?php echo $gender_text[$i];?>&nbsp;&nbsp;
                                <span class="gender_remove" onclick="removevalue('gender',<?php echo $gender_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_gender" name="count_gender" value="<?php echo $gender_count;?>">
                <input type="hidden" id="gender_value" name="gender_value" value="<?php echo @$this->filters->gender_value;?>" />
            </td>
        </tr>

        <tr>
            <td><input type="checkbox" value="1" id="newtrait_filter" name="newtrait_filter" <?php echo (@$this->filters->newtrait_filter)?'checked="checked"':'';?> /></td>
            <td><label for="newtrait_filter"><?php echo JText::_('COM_TOES_NEW_TRAIT');?></label><br/><br/></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td><input onclick="toggle_field(this,'copy_cat_hairlength');" type="checkbox" value="1" id="hairlength_filter" name="hairlength_filter" <?php echo (@$this->filters->hairlength_filter)?'checked="checked"':'';?> /></td>
            <td><label for="hairlength_filter"><?php echo JText::_('COM_TOES_HAIRLENGTH');?></label></td>
            <td id="cat_hairlength">
                <?php echo JHTML::_('select.genericlist',$this->hairlengths, 'copy_cat_hairlength', '', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'copy_cat_hairlength\',\'hairlength\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <!-- <input type="text" id="hairlength_text" name="hairlength_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="hairlength_place">
                    <?php 
                    if(isset($this->filters->hairlength_text) && $this->filters->hairlength_text) { 
                        $hairlength_text = explode(',', $this->filters->hairlength_text);
                        $hairlength_values = explode(',', $this->filters->hairlength_value); 
                        $hairlength_count = count($hairlength_text);
                        for($i = 0; $i< count($hairlength_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="hairlength_<?php echo $hairlength_values[$i];?>">
                                <?php echo $hairlength_text[$i];?>&nbsp;&nbsp;
                                <span class="hairlength_remove" onclick="removevalue('hairlength',<?php echo $hairlength_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_hairlength" name="count_hairlength" value="<?php echo $hairlength_count;?>">
                <input type="hidden" id="hairlength_value" name="hairlength_value" value="<?php echo @$this->filters->hairlength_value;?>" />
            </td>
        </tr>

        <tr>
            <td><input onclick="toggle_field(this,'copy_cat_category');" type="checkbox" value="1" id="category_filter" name="category_filter" <?php echo (@$this->filters->category_filter)?'checked="checked"':'';?> /></td>
            <td><label for="category_filter"><?php echo JText::_('COM_TOES_CATEGORY');?></label></td>
            <td id="cat_category">
                <?php echo JHTML::_('select.genericlist',$this->categories, 'copy_cat_category', '', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'copy_cat_category\',\'category\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <!-- <input type="text" id="category_text" name="category_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="category_place">
                    <?php 
                    if(isset($this->filters->class_text) && $this->filters->category_text) { 
                        $category_text = explode(',', $this->filters->category_text);
                        $category_values = explode(',', $this->filters->category_value); 
                        $category_count = count($category_text);
                        for($i = 0; $i< count($category_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="category_<?php echo $category_values[$i];?>">
                                <?php echo $category_text[$i];?>&nbsp;&nbsp;
                                <span class="category_remove" onclick="removevalue('category',<?php echo $category_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_category" name="count_category" value="<?php echo $category_count;?>">
                <input type="hidden" id="category_value" name="category_value" value="<?php echo @$this->filters->category_value;?>" />
            </td>
        </tr>

        <tr>
            <td><input onclick="toggle_field(this,'copy_cat_division');" type="checkbox" value="1" id="division_filter" name="division_filter" <?php echo (@$this->filters->division_filter)?'checked="checked"':'';?> /></td>
            <td><label for="division_filter"><?php echo JText::_('COM_TOES_DIVISION');?></label></td>
            <td id="cat_division">
                <?php echo JHTML::_('select.genericlist',$this->divisions, 'copy_cat_division', '', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'copy_cat_division\',\'division\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <!-- <input type="text" id="division_text" name="division_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="division_place">
                    <?php 
                    if(isset($this->filters->division_text) && $this->filters->division_text) { 
                        $division_text = explode(',', $this->filters->division_text);
                        $division_values = explode(',', $this->filters->division_value); 
                        $division_count = count($division_text);
                        for($i = 0; $i< count($division_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="division_<?php echo $division_values[$i];?>">
                                <?php echo $division_text[$i];?>&nbsp;&nbsp;
                                <span class="division_remove" onclick="removevalue('division',<?php echo $division_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_division" name="count_division" value="<?php echo $division_count;?>">
                <input type="hidden" id="division_value" name="division_value" value="<?php echo @$this->filters->division_value;?>" />
            </td>
        </tr>

        <tr>
            <td><input onclick="toggle_field(this,'colorname');" type="checkbox" value="1" id="color_filter" name="color_filter" <?php echo (@$this->filters->color_filter)?'checked="checked"':'';?> /></td>
            <td><label for="color_filter"><?php echo JText::_('COM_TOES_COLOR');?></label></td>
            <td id="cat_color">
                <input type="text" id="colorname" style="color: #999;" value="<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_COLOR');?>" onblur="if(this.value==''){this.value='<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_COLOR');?>'; this.style.color='#999';}" onfocus="if(this.value=='<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_COLOR');?>'){ this.value='';this.style.color='#000';}" >
                <?php echo JHTML::_('select.genericlist',$this->colors, 'copy_cat_color', 'style="display:none;"', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'copy_cat_color\',\'color\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <span style="float:right;margin-right: -25px;" >
                    <a href="javascript:void(0);" title="<?php echo JText::_('COM_TOES_CLEAR_ALL_VALUES');?>" onclick="clear_all_color();">
                        <i class="fa fa-remove"></i> &nbsp;
                    </a> 
                </span>

                <!-- <input type="text" id="color_text" name="color_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="color_place">
                    <?php 
                    if(isset($this->filters->color_text) && $this->filters->color_text) { 
                        $color_text = explode(',', $this->filters->color_text);
                        $color_values = explode(',', $this->filters->color_value); 
                        $color_count = count($color_text);
                        for($i = 0; $i< count($color_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="color_<?php echo $color_values[$i];?>">
                                <?php echo $color_text[$i];?>&nbsp;&nbsp;
                                <span class="color_remove" onclick="removevalue('color',<?php echo $color_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_color" name="count_color" value="<?php echo $color_count;?>">
                <input type="hidden" id="color_value" name="color_value" value="<?php echo @$this->filters->color_value;?>" />
            </td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>
                <input type="text" id="color_search" style="color: #999;" value="<?php echo JText::_('COM_TOES_TYPE_WILDCARD');?>" onblur="if(this.value==''){this.value='<?php echo JText::_('COM_TOES_TYPE_WILDCARD');?>'; this.style.color='#999';}" onfocus="if(this.value=='<?php echo JText::_('COM_TOES_TYPE_WILDCARD');?>'){ this.value='';this.style.color='#000';}" >
                <!-- <a id="color_search_button" href="javascript:void(0);" onclick="search_color();">
                    <i class="fa fa-search"></i>
                </a>
                <br/>
                <div id="color_search_results">
                    <select name="colorsearchresults" id="colorsearchresults" multiple="" size="10" style="display:none" >
                    </select>
                </div> -->
            </td>
            <td style="text-align: center;">
                <?php //echo '<a id="multiple_color_copy" style="color:#666;" href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_MULTIPLE_VALUE').'" onclick="return false;"><i class="fa fa-arrow-right"></i></a>';?>
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_color_wildcard(\'color_search\',\'cwd\',\''.JText::_('COM_TOES_TYPE_WILDCARD').'\');"><i class="fa fa-arrow-right"></i></a>';?>                
            </td>
            <td>
                <!-- <input type="text" id="color_text" name="color_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="cwd_place">
                    <?php 
                    if(isset($this->filters->cwd_value) && $this->filters->cwd_value) { 
                        $cwd_text = explode(',', $this->filters->cwd_value);
                        $cwd_count = count($cwd_text);
                        for($i = 0; $i< count($cwd_text);$i++)
                        {
                        ?>
                            <label class="data_lable" rel="<?php echo $cwd_text[$i];?>" id="cwd_<?php echo $i;?>">
                                %<?php echo $cwd_text[$i];?>%&nbsp;&nbsp;
                                <span class="cwd_remove" onclick="removewildcard('cwd','<?php echo $i;?>');">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_cwd" name="count_cwd" value="<?php echo $cwd_count;?>">
                <input type="hidden" id="cwd_value" name="cwd_value" value="<?php echo @$this->filters->cwd_value;?>" />
            </td>
        </tr>
        <tr>
            <td><input onclick="toggle_field(this,'title');" type="checkbox" value="1" id="title_filter" name="title_filter" <?php echo (@$this->filters->title_filter)?'checked="checked"':'';?> /></td>
            <td><label for="title_filter"><?php echo JText::_('COM_TOES_TITLE');?></label></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->titles, 'title', '', 'value', 'text');?>
            </td>
            <td style="text-align: center;">
                <?php echo '<a href="javascript:void(0);" title="'.JText::_('COM_TOES_COPY_VALUE').'" onclick="copy_field(\'title\',\'title\');"><i class="fa fa-arrow-right"></i></a>';?>
            </td>
            <td>
                <!-- <input type="text" id="title_text" name="title_text" value="" readonly="readonly" /> -->
                <div class="data_div" id="title_place">
                    <?php 
                    if(isset($this->filters->title_text) && $this->filters->title_text) { 
                        $title_text = explode(',', $this->filters->title_text);
                        $title_values = explode(',', $this->filters->title_value); 
                        $title_count = count($title_text);
                        for($i = 0; $i< count($title_text);$i++)
                        {
                        ?>
                            <label class="data_lable" id="title_<?php echo $title_values[$i]?>">
                                <?php echo $title_text[$i];?>&nbsp;&nbsp;
                                <span class="title_remove" onclick="removevalue('title',<?php echo $title_values[$i]?>);">
                                    <i class="fa fa-remove"></i>
                                </span>
                            </label>
                        <?php
                        }
                    } 
                    ?>
                </div>
                <input type="hidden" id="count_title" name="count_title" value="<?php echo $title_count;?>">
                <input type="hidden" id="title_value" name="title_value" value="<?php echo @$this->filters->title_value;?>" />
            </td>
        </tr>

        <tr>
            <td><input type="checkbox" value="1" id="manual_filter" name="manual_filter" <?php echo (@$this->filters->manual_filter)?'checked="checked"':'';?> /></td>
            <td><label for="manual_filter"><?php echo JText::_('COM_TOES_MANUAL_SELECTION');?></label></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">
                <input type="hidden" name="ring_index" id="ring_index" value="<?php echo $this->filters->ring_index;?>" />
                <input type="hidden" name="ring_name" id="ring_name" value="<?php echo $this->filters->ring_name;?>" />
                <input type="hidden" name="ring_id" id="ring_id" value="<?php echo $this->filters->ring_id;?>" />
                <input type="checkbox" name="copy_cat_new_trait" id="copy_cat_new_trait" style="display: none" />
                <input type="button" value="<?php echo JText::_('SAVE');?>" onclick="save_congress_criteria();" />
                <input type="button" value="<?php echo JText::_('CANCEL');?>" onclick="if (congress_filter_modal) congress_filter_modal.close();" />
            </td>
        </tr>
    </tbody>
</table>
<br/>

<script type="text/javascript">

    jQuery(document).ready(function(){
        var all_selected = '<?php echo @$this->filters->breed_filter && @$this->filters->gender_filter && @$this->filters->newtrait_filter && @$this->filters->hairlength_filter && @$this->filters->category_filter 
                && @$this->filters->division_filter && @$this->filters->color_filter && @$this->filters->title_filter && @$this->filters->manual_filter;?>';
        
        if(all_selected == '1')
            jQuery('#filter_all').set('checked',1);

        toggle_field(jQuery('#breed_filter'),'breedname');
        toggle_field(jQuery('#gender_filter'),'gender');
        toggle_field(jQuery('#hairlength_filter'),'copy_cat_hairlength');
        toggle_field(jQuery('#category_filter'),'copy_cat_category');
        toggle_field(jQuery('#division_filter'),'copy_cat_division');
        toggle_field(jQuery('#color_filter'),'colorname');
        toggle_field(jQuery('#title_filter'),'title');

		jQuery( "#breedname" ).autocomplete({
		  source: 'index.php?option=com_toes&task=entry.getBreeds&tmpl=component',
		  select: function( event, ui ) {
		  	jQuery( "#copy_cat_breed" ).val(ui.item.key);
		  	jQuery( "#breedname" ).val(ui.item.value);
		  }
		});    

		jQuery( "#colorname" ).autocomplete({
		  source: 'index.php?option=com_toes&task=entry.getColors&tmpl=component&category='+jQuery('#copy_cat_category').val()+'&division='+jQuery('#copy_cat_division').val(),
		  select: function( event, ui ) {
		  	jQuery( "#copy_cat_color" ).val(ui.item.key);
		  	jQuery( "#colorname" ).val(ui.item.value);
		  }
		});    
        
        /*
        var fields = new Array("class","breed","gender","hairlength","category","division","color","title");
        
        for(var i=0;i<fields.length;i++)
        {
            var field = fields[i];
            var selected_values = jQuery('#'+field+'_value').get('value').split(',');
            for(var j=0;j<selected_values.length;j++)
            {
                jQuery.ajax({
                    url: 'index.php?option=com_toes&task=entry.get'+field+'Name&tmpl=component',
                    data: 'id='+selected_values[j],
                    type: 'post',
                }).done(function(responseText){
                    var data = responseText.split(';');
                    var field = data[0];
                    var text = data[1];
                    var value = data[2];
                    if(text)
                    {
                        var c=jQuery('#count_'+field).value;
                        var d=parseInt(c)+parseInt(1);

                        var str = '<label class="data_lable" id="'+field+'_'+c+'">'+text+'&nbsp;&nbsp;<span class="'+field+'_remove" onclick="removevalue(\''+field+'\','+c+','+value+');"><i class="fa fa-remove"></i></span>'+'</label>';

                        jQuery('#'+field+'_place').html(jQuery('#'+field+'_place').html() + str);
                        jQuery('#count_'+field).val() = d;
                    }
                });                
            }
        }
        */
    });
    
    function search_color()
    {
        if(jQuery('#color_search').val() && jQuery('#color_search').val() != '<?php echo JText::_('COM_TOES_TYPE_TO_SEARCH_AND_ADD_MULTPLE_COLOR')?>')
        {
            var search = jQuery('#color_search').val();
            jQuery.ajax({
                url: 'index.php?option=com_toes&task=entry.searchColors',
                data: 'search='+search,
                type: 'post',
          }).done( function(responseText){
                jQuery('#loader').hide();
                if(responseText == 'error')
                {
                    jbox_alert(responseText);
                }
                else
                {
                    var select = jQuery('#colorsearchresults');
                    select.options.length = 0;
                    var myvalue = JSON.decode(responseText, true);
                    myvalue.each( function(avalue, i) {
                        if (avalue) {
                            select.options[select.options.length] = new Option(avalue[1], avalue[0]);
                        }
                    });
                    
                    if(select.options.length == 0)
                    {
                        select.options[select.options.length] = new Option('No match found', 0);
                        jQuery('#multiple_color_copy').prop("onclick","return false;");
                        jQuery('#multiple_color_copy').css("color","#666");
                        jQuery('#colorsearchresults').css('display','none');
                    }
                    else
                    {
                        jQuery('#multiple_color_copy').prop("onclick","copy_multiple_color();");
                        jQuery('#multiple_color_copy').css("color","#c3b71e");
                        jQuery('#colorsearchresults').css('display','block');
                    }
                }
            });        
        }

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
        jQuery('#loader').show();
    }
    
    function toggle_field(ele,field)
    {
        if(jQuery(ele).is(':checked'))
        {
            jQuery('#'+field).prop('disabled',0);
            if(field == 'colorname')
            {
                jQuery('#color_search').prop('disabled',0);
                //jQuery('#colorsearchresults').prop('disabled',0);
                //jQuery('#color_search_button').prop("onclick","search_color();");
            }
        }
        else
        {
            jQuery('#'+field).prop('disabled',1);
            if(field == 'colorname')
            {
                jQuery('#color_search').prop('disabled',1);
                //jQuery('#colorsearchresults').prop('disabled',1);
                //jQuery('#color_search_button').prop("onclick","return false;");
            }
        }
    }
    
</script>
