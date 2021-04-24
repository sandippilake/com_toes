<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$readonly = '';
?>

<table style="width: 100%;">
    <thead>
        <tr>
            <th style="width:20%; text-align: left;">&nbsp;</th>
            <th style="width:35%; text-align: left;"><?php echo JText::_('COM_TOES_RECENT_CAT_INFORMATION'); ?></th>
            <th style="width:10%; text-align: left;">&nbsp;</th>
            <th style="width:35%; text-align: left;"><?php echo JText::_('COM_TOES_ENTRY_INFORMATION'); ?></th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td><?php echo JText::_('COM_TOES_BREED');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->breeds, 'cat_breed', 'onchange="changeCathairlength();changeCatCategory();" '.$readonly, 'value', 'text',$this->cat_details->cat_breed);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffBreed = false;
                    if($this->cat_details->breed_name != $this->entry_details->breed_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_breed\',\''.$this->entry_details->copy_cat_breed.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffBreed = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffBreed)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->breed_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_NEW_TRAIT');?></td>
            <td>
                <input value="1" type="checkbox" name="cat_new_trait" id="cat_new_trait" <?php if($this->cat_details->cat_new_trait) echo 'checked="checked"';?> onclick="changeCathairlength();changeCatCategory();" <?php echo $readonly;?> />
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffTrait = false;
                    if($this->cat_details->cat_new_trait != $this->entry_details->copy_cat_new_trait)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_new_trait\',\''.$this->entry_details->copy_cat_new_trait.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffTrait = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffTrait)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_new_trait?'True':'False'; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_CATEGORY');?></td>
            <td id="cat_category_div">
                <?php echo JHTML::_('select.genericlist',$this->categories, 'cat_category', 'onchange="changeCatDivision();" '.$readonly, 'value', 'text',$this->cat_details->cat_category);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffCategory = false;
                    if($this->cat_details->category != $this->entry_details->category)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_category\',\''.$this->entry_details->copy_cat_category.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffCategory = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffCategory)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->category; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_DIVISION');?></td>
            <td id="cat_division_div">
                <?php echo JHTML::_('select.genericlist',$this->divisions, 'cat_division', 'onchange="changeCatColor();" '.$readonly, 'value', 'text',$this->cat_details->cat_division);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffDivision = false;
                    if($this->cat_details->division_name != $this->entry_details->division_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_division\',\''.$this->entry_details->copy_cat_division.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffDivision = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffDivision)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->division_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_COLOR');?></td>
            <td id="cat_color_div">
                <?php echo JHTML::_('select.genericlist',$this->colors, 'cat_color', $readonly, 'value', 'text',$this->cat_details->cat_color);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffColor = false;
                    if($this->cat_details->color_name != $this->entry_details->color_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_color\',\''.$this->entry_details->copy_cat_color.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffColor = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffColor)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->color_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_NAME');?></td>
            <td><input name="cat_name" id="cat_name" value="<?php echo $this->cat_details->cat_name; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffName = false;
                    if($this->cat_details->cat_name != $this->entry_details->copy_cat_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_name\',\''.$this->entry_details->copy_cat_name.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffName = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffName)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_GENDER');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->genders, 'cat_gender', $readonly, 'value', 'text',$this->cat_details->cat_gender);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffGender = false;
                    if($this->cat_details->gender_short_name != $this->entry_details->gender_short_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_gender\',\''.$this->entry_details->copy_cat_gender.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffGender = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffGender)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->gender_short_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_HAIRLENGTH');?></td>
            <td id="cat_hairlength">
                <?php echo JHTML::_('select.genericlist',$this->hairlengths, 'cat_hair_length', $readonly, 'value', 'text',$this->cat_details->cat_hair_length);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffHairlength = false;
                    if($this->cat_details->cat_hair_length != $this->entry_details->copy_cat_hair_length)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_hair_length\',\''.$this->entry_details->copy_cat_hair_length.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffGender = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffHairlength)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->breed_hair_length; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_REGISTRATION_NUMBER');?></td>
            <td><input name="cat_registration_number" id="cat_registration_number" value="<?php echo $this->cat_details->cat_registration_number; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffRegnumber = false;
                    if($this->cat_details->cat_registration_number != $this->entry_details->copy_cat_registration_number)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_registration_number\',\''.$this->entry_details->copy_cat_registration_number.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffRegnumber = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffRegnumber)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_registration_number; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_DATE_OF_BIRTH');?></td>
            <td>
                <input name="cat_date_of_birth" id="cat_date_of_birth" value="<?php echo $this->cat_details->cat_date_of_birth; ?>" <?php echo $readonly;?> />
                <span id="entry-dob"><?php echo date('M d, Y',strtotime($this->cat_details->cat_date_of_birth)); ?></span>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffDOB = false;
                    if($this->cat_details->cat_date_of_birth != $this->entry_details->copy_cat_date_of_birth)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_date_of_birth\',\''.$this->entry_details->copy_cat_date_of_birth.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffDOB = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffDOB)?'style="background: #FFFC17;"':'';?>><?php echo date('M d, Y',strtotime($this->entry_details->copy_cat_date_of_birth)); ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_PREFIX');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->prefixes, 'cat_prefix', $readonly, 'value', 'text',$this->cat_details->cat_prefix);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffPrefix = false;
                    if($this->cat_details->cat_prefix_abbreviation != $this->entry_details->cat_prefix_abbreviation)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_prefix\',\''.$this->entry_details->copy_cat_prefix.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffPrefix = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffPrefix)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->cat_prefix; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_TITLE');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->titles, 'cat_title', $readonly, 'value', 'text',$this->cat_details->cat_title);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffTitle = false;
                    if($this->cat_details->cat_title_abbreviation != $this->entry_details->cat_title_abbreviation)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_title\',\''.$this->entry_details->copy_cat_title.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffTitle = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffTitle)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->cat_title; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_SUFFIX');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->suffixes, 'cat_suffix', $readonly, 'value', 'text',$this->cat_details->cat_suffix);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffSuffix = false;
                    if($this->cat_details->cat_suffix_abbreviation != $this->entry_details->cat_suffix_abbreviation)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_suffix\',\''.$this->entry_details->copy_cat_suffix.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffSuffix = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffSuffix)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->cat_suffix; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_SIRE');?></td>
            <td><input name="cat_sire_name" id="cat_sire" value="<?php echo $this->cat_details->cat_sire; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffSire = false;
                    if($this->cat_details->cat_sire != $this->entry_details->copy_cat_sire_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_sire\',\''.$this->entry_details->copy_cat_sire_name.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffSire = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffSire)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_sire_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_DAM');?></td>
            <td><input name="cat_dam_name" id="cat_dam" value="<?php echo $this->cat_details->cat_dam; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffDam = false;
                    if($this->cat_details->cat_dam != $this->entry_details->copy_cat_dam_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_dam\',\''.$this->entry_details->copy_cat_dam_name.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffDam = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffDam)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_dam_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_BREEDER');?></td>
            <td><input name="cat_breeder_name" id="cat_breeder" value="<?php echo $this->cat_details->cat_breeder; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffBreeder = false;
                    if($this->cat_details->cat_breeder != $this->entry_details->copy_cat_breeder_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_breeder\',\''.$this->entry_details->copy_cat_breeder_name.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffBreeder = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffBreeder)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_breeder_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_OWNER');?></td>
            <td><input name="cat_owner_name" id="cat_owner" value="<?php echo $this->cat_details->cat_owner; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffOwner = false;
                    if($this->cat_details->cat_owner != $this->entry_details->copy_cat_owner_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_owner\',\''.$this->entry_details->copy_cat_owner_name.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffOwner = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffOwner)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_owner_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_LESSEE');?></td>
            <td><input name="cat_lessee_name" id="cat_lessee" value="<?php echo $this->cat_details->cat_lessee; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffLessee = false;
                    if($this->cat_details->cat_lessee != $this->entry_details->copy_cat_lessee_name)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_lessee\',\''.$this->entry_details->copy_cat_lessee_name.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffLessee = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffLessee)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->copy_cat_lessee_name; ?></td>
        </tr>

        <?php /*<tr>
            <td><?php echo JText::_('COM_TOES_AGENT');?></td>
            <td>
                <input name="cat_agent_name" id="cat_agent_name" value="<?php echo $this->cat_details->cat_agent_name; ?>" <?php echo $readonly;?> />
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>*/ ?>

        <tr>
            <td><?php echo JText::_('COM_TOES_COMPETITIVE_REGION');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->competitiveregions, 'cat_competitive_region', $readonly, 'value', 'text',$this->cat_details->cat_competitive_region);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffRegion = false;
                    if($this->cat_details->competitive_region_abbreviation != $this->entry_details->competitive_region_abbreviation)
                    {
                        echo '<a href="javascript:void(0);" onclick="copy_new_field(\'cat_competitive_region\',\''.$this->entry_details->copy_cat_competitive_region.'\');"><img alt="<---" src="media/com_toes/images/leftarrow32X32.png" /> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffRegion = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffRegion)?'style="background: #FFFC17;"':'';?>><?php echo $this->entry_details->competitive_region_abbreviation; ?></td>
        </tr>

        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="button" value="<?php echo JText::_('SAVE');?>" onclick="save_cat_detail_changes(<?php echo $this->entry_details->entry_id;?>,<?php echo $this->entry_details->cat;?>);" />
                <input type="button" value="<?php echo JText::_('CANCEL');?>" onclick="cancel_cat_detail_changes(<?php echo $this->entry_details->entry_id;?>,<?php echo $this->entry_details->cat;?>);" />
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </tbody>
</table>
<br/>
