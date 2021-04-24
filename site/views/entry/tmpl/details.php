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

$exhibitor = $app->input->getInt('exhibitor');
if($exhibitor)
    $readonly = 'disabled';
else
    $readonly = '';
?>

<table style="width: 100%;">
    <thead>
        <tr>
            <th style="width:20%; text-align: left;">&nbsp;</th>
            <th style="width:35%; text-align: left;"><?php echo JText::_('COM_TOES_ENTRY_INFORMATION'); ?></th>
            <th style="width:10%; text-align: left;">&nbsp;</th>
            <th style="width:35%; text-align: left;"><?php echo JText::_('COM_TOES_RECENT_CAT_INFORMATION'); ?></th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td><?php echo JText::_('COM_TOES_BREED');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->breeds, 'copy_cat_breed', 'onchange="changehairlength();changeCategory();" '.$readonly, 'value', 'text',$this->entry_details->copy_cat_breed);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffBreed = false;
                    if($this->entry_details->breed_name != $this->cat_details->breed_name)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_breed\',\''.$this->cat_details->cat_breed.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffBreed = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffBreed)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->breed_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_NEW_TRAIT');?></td>
            <td>
                <input value="1" type="checkbox" name="copy_cat_new_trait" id="copy_cat_new_trait" <?php if($this->entry_details->copy_cat_new_trait) echo 'checked="checked"';?> onclick="changehairlength();changeCategory();" <?php echo $readonly;?> />
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffTrait = false;
                    if($this->entry_details->copy_cat_new_trait != $this->cat_details->cat_new_trait)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_new_trait\',\''.$this->cat_details->cat_new_trait.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffTrait = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffTrait)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_new_trait?'True':'False'; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_CATEGORY');?></td>
            <td id="cat_category">
                <?php echo JHTML::_('select.genericlist',$this->categories, 'copy_cat_category', 'onchange="changeDivision();" '.$readonly, 'value', 'text',$this->entry_details->copy_cat_category);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffCategory = false;
                    if($this->entry_details->category != $this->cat_details->category)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_category\',\''.$this->cat_details->cat_category.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffCategory = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffCategory)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->category; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_DIVISION');?></td>
            <td id="cat_division">
                <?php echo JHTML::_('select.genericlist',$this->divisions, 'copy_cat_division', 'onchange="changeColor();" '.$readonly, 'value', 'text',$this->entry_details->copy_cat_division);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffDivision = false;
                    if($this->entry_details->division_name != $this->cat_details->division_name)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_division\',\''.$this->cat_details->cat_division.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffDivision = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffDivision)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->division_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_COLOR');?></td>
            <td id="cat_color">
                <?php echo JHTML::_('select.genericlist',$this->colors, 'copy_cat_color', $readonly, 'value', 'text',$this->entry_details->copy_cat_color);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffColor = false;
                    if($this->entry_details->color_name != $this->cat_details->color_name)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_color\',\''.$this->cat_details->cat_color.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffColor = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffColor)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->color_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_NAME');?></td>
            <td><input name="copy_cat_name" id="copy_cat_name" value="<?php echo $this->entry_details->copy_cat_name; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffName = false;
                    if($this->entry_details->copy_cat_name != $this->cat_details->cat_name)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_name\',\''.$this->cat_details->cat_name.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffName = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffName)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_GENDER');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->genders, 'copy_cat_gender', $readonly, 'value', 'text',$this->entry_details->copy_cat_gender);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffGender = false;
                    if($this->entry_details->gender_short_name != $this->cat_details->gender_short_name)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_gender\',\''.$this->cat_details->cat_gender.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffGender = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffGender)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->gender_short_name; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_HAIRLENGTH');?></td>
            <td id="cat_hairlength">
                <?php echo JHTML::_('select.genericlist',$this->hairlengths, 'copy_cat_hair_length', $readonly, 'value', 'text',$this->entry_details->copy_cat_hair_length);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffHairlength = false;
                    if($this->entry_details->copy_cat_hair_length != $this->cat_details->cat_hair_length)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_hair_length\',\''.$this->cat_details->cat_hair_length.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffGender = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffHairlength)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->breed_hair_length; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_REGISTRATION_NUMBER');?></td>
            <td><input name="copy_cat_registration_number" id="copy_cat_registration_number" value="<?php echo $this->entry_details->copy_cat_registration_number; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffRegnumber = false;
                    if($this->entry_details->copy_cat_registration_number != $this->cat_details->cat_registration_number)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_registration_number\',\''.$this->cat_details->cat_registration_number.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffRegnumber = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffRegnumber)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_registration_number; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_DATE_OF_BIRTH');?></td>
            <td>
                <input name="copy_cat_date_of_birth" id="copy_cat_date_of_birth" value="<?php echo $this->entry_details->copy_cat_date_of_birth; ?>" <?php echo $readonly;?> />
                <span id="entry-dob"><?php echo date('M d, Y',strtotime($this->entry_details->copy_cat_date_of_birth)); ?></span>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffDOB = false;
                    if($this->entry_details->copy_cat_date_of_birth != $this->cat_details->cat_date_of_birth)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_date_of_birth\',\''.$this->cat_details->cat_date_of_birth.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffDOB = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffDOB)?'style="background: #FFFC17;"':'';?>><?php echo date('M d, Y',strtotime($this->cat_details->cat_date_of_birth)); ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_PREFIX');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->prefixes, 'copy_cat_prefix', $readonly, 'value', 'text',$this->entry_details->copy_cat_prefix);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffPrefix = false;
                    if($this->entry_details->cat_prefix_abbreviation != $this->cat_details->cat_prefix_abbreviation)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_prefix\',\''.$this->cat_details->cat_prefix.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffPrefix = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffPrefix)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_prefix_abbreviation; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_TITLE');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->titles, 'copy_cat_title', $readonly, 'value', 'text',$this->entry_details->copy_cat_title);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffTitle = false;
                    if($this->entry_details->cat_title_abbreviation != $this->cat_details->cat_title_abbreviation)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_title\',\''.$this->cat_details->cat_title.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffTitle = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffTitle)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_title_abbreviation; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_SUFFIX');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->suffixes, 'copy_cat_suffix', $readonly, 'value', 'text',$this->entry_details->copy_cat_suffix);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffSuffix = false;
                    if($this->entry_details->cat_suffix_abbreviation != $this->cat_details->cat_suffix_abbreviation)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_suffix\',\''.$this->cat_details->cat_suffix.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffSuffix = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffSuffix)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_suffix_abbreviation; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_SIRE');?></td>
            <td><input name="copy_cat_sire_name" id="copy_cat_sire_name" value="<?php echo $this->entry_details->copy_cat_sire_name; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffSire = false;
                    if($this->entry_details->copy_cat_sire_name != $this->cat_details->cat_sire)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_sire_name\',\''.$this->cat_details->cat_sire.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffSire = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffSire)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_sire; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_DAM');?></td>
            <td><input name="copy_cat_dam_name" id="copy_cat_dam_name" value="<?php echo $this->entry_details->copy_cat_dam_name; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffDam = false;
                    if($this->entry_details->copy_cat_dam_name != $this->cat_details->cat_dam)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_dam_name\',\''.$this->cat_details->cat_dam.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffDam = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffDam)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_dam; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_BREEDER');?></td>
            <td><input name="copy_cat_breeder_name" id="copy_cat_breeder_name" value="<?php echo $this->entry_details->copy_cat_breeder_name; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffBreeder = false;
                    if($this->entry_details->copy_cat_breeder_name != $this->cat_details->cat_breeder)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_breeder_name\',\''.$this->cat_details->cat_breeder.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffBreeder = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffBreeder)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_breeder; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_OWNER');?></td>
            <td><input name="copy_cat_owner_name" id="copy_cat_owner_name" value="<?php echo $this->entry_details->copy_cat_owner_name; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffOwner = false;
                    if($this->entry_details->copy_cat_owner_name != $this->cat_details->cat_owner)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_owner_name\',\''.$this->cat_details->cat_owner.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffOwner = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffOwner)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_owner; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_LESSEE');?></td>
            <td><input name="copy_cat_lessee_name" id="copy_cat_lessee_name" value="<?php echo $this->entry_details->copy_cat_lessee_name; ?>" <?php echo $readonly;?> /></td>
            <td style="text-align: center;">
                <?php 
                    $isDiffLessee = false;
                    if($this->entry_details->copy_cat_lessee_name != $this->cat_details->cat_lessee)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_lessee_name\',\''.$this->cat_details->cat_lessee.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffLessee = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffLessee)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->cat_lessee; ?></td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_AGENT');?></td>
            <td>
                <input name="copy_cat_agent_name" id="copy_cat_agent_name" value="<?php echo $this->entry_details->copy_cat_agent_name; ?>" <?php echo $readonly;?> />
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td><?php echo JText::_('COM_TOES_COMPETITIVE_REGION');?></td>
            <td>
                <?php echo JHTML::_('select.genericlist',$this->competitiveregions, 'copy_cat_competitive_region', $readonly, 'value', 'text',$this->entry_details->copy_cat_competitive_region);?>
            </td>
            <td style="text-align: center;">
                <?php 
                    $isDiffRegion = false;
                    if($this->entry_details->competitive_region_abbreviation != $this->cat_details->competitive_region_abbreviation)
                    {
                        if(!$exhibitor)
                            echo '<a href="javascript:void(0);" onclick="copy_new_field(\'copy_cat_competitive_region\',\''.$this->cat_details->cat_competitive_region.'\');"><i class="fa fa-arrow-left"></i> '.JText::_('COM_TOES_COPY_TEXT').'</a>';
                        $isDiffRegion = true;
                    }
                ?>
            </td>
            <td <?php echo ($isDiffRegion)?'style="background: #FFFC17;"':'';?>><?php echo $this->cat_details->competitive_region_abbreviation; ?></td>
        </tr>

        <?php if(!$exhibitor) : ?>
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
                <input type="button" value="<?php echo JText::_('SAVE');?>" onclick="save_entry_details(<?php echo $this->entry_details->entry_id?>,<?php echo $this->cat_details->cat_id?>,<?php echo $this->entry_details->show_id?>);" />
                <input type="button" value="<?php echo JText::_('CANCEL');?>" onclick="cancel_entry_details();" />
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<input name="exhibitor" id="exhibitor" type="hidden" value="<?php echo $exhibitor;?>" />
<br/>


