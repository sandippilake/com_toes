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

if (count(@$this->item->cat_id)) {
    if (count(@$this->cat_owner)) {
        $cat_owners = null;
        foreach ($this->cat_owner as $cat_owner) {
            $cat_owners[] = $cat_owner->text;
        }

        if (is_array($cat_owners))
            $cat_owners = implode(',', $cat_owners);
    }

    if (count(@$this->cat_breeder)) {
        $cat_breeders = null;
        foreach ($this->cat_breeder as $cat_breeder) {
            $cat_breeders[] = $cat_breeder->text;
        }

        if (is_array($cat_breeders))
            $cat_breeders = implode(',', $cat_breeders);
    }

    if (count(@$this->cat_other)) {
        $cat_agents = null;
        $cat_lessees = null;
        foreach ($this->cat_other as $cat_other) {
            if ($cat_other->relation == 'Agent') {
                $cat_agents[] = $cat_other->text;
            }
            if ($cat_other->relation == 'Lessee') {
                $cat_lessees[] = $cat_other->text;
            }
        }

        if (is_array($cat_agents))
            $cat_agents = implode(',', $cat_agents);
        if (is_array($cat_lessees))
            $cat_lessees = implode(',', $cat_lessees);
    }
}
?>

<style type="text/css">
    .field-text {  float: left; font-size: 15px; margin-right: 10px; width: 20%;}
    .field-value {  float: left; font-size: 15px;width: 60%;}
    .fieldbg { float: left; height: 28px; line-height: 28px; margin: 0 0 6px; padding: 0 10px 0 15px; width: 100%;}
    .fieldblank-text { float: left;font-family: 'GillSansItalic';font-size: 19px;margin-right: 5px;text-transform: capitalize;}
    .fieldblank { float: left; height: 28px;line-height: 28px;margin: 0 0 6px; padding: 0;width: 475px;}

    .addedusername_owner { background: none repeat scroll 0 0 #637193; border-radius: 4px 4px 4px 4px; clear: none; color: #FFFFFF; float: left; font-family: "arial"; font-size: 12px; font-weight: normal; line-height: 12px; list-style: none outside none; margin: 2px 1px 1px; padding: 2px 5px 4px; }
    .username_ownerRemove { color: #FFFFFF; cursor: pointer; font-size: 12px;  padding-left: 4px; }
    #username_ownerplace { border: 1px solid #CCCCCC; min-height: 35px; padding: 5px 6px;}

    .addedusername_breeder { background: none repeat scroll 0 0 #637193; border-radius: 4px 4px 4px 4px; clear: none; color: #FFFFFF; float: left; font-family: "arial"; font-size: 12px; font-weight: normal; line-height: 12px; list-style: none outside none; margin: 2px 1px 1px; padding: 2px 5px 4px; }
    .username_breederRemove { color: #FFFFFF; cursor: pointer; font-size: 12px;  padding-left: 4px; }
    #username_breederplace { border: 1px solid #CCCCCC; min-height: 35px; padding: 5px 6px;}

    .addedusername_agent { background: none repeat scroll 0 0 #637193; border-radius: 4px 4px 4px 4px; clear: none; color: #FFFFFF; float: left; font-family: "arial"; font-size: 12px; font-weight: normal; line-height: 12px; list-style: none outside none; margin: 2px 1px 1px; padding: 2px 5px 4px; }
    .username_agentRemove { color: #FFFFFF; cursor: pointer; font-size: 12px;  padding-left: 4px; }
    #username_agentplace { border: 1px solid #CCCCCC; min-height: 35px; padding: 5px 6px;}

    .addedusername_lessee { background: none repeat scroll 0 0 #637193; border-radius: 4px 4px 4px 4px; clear: none; color: #FFFFFF; float: left; font-family: "arial"; font-size: 12px; font-weight: normal; line-height: 12px; list-style: none outside none; margin: 2px 1px 1px; padding: 2px 5px 4px; }
    .username_lesseeRemove { color: #FFFFFF; cursor: pointer; font-size: 12px;  padding-left: 4px; }
    #username_lesseeplace { border: 1px solid #CCCCCC; min-height: 35px; padding: 5px 6px;}

</style>


<form id="adminForm" name="adminForm" action="index.php?option=com_toes" class="form-validate" method="post" enctype="multipart/form-data"> 

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_ADD_EDIT_CAT'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter block-rg_number">
        <br/>
        <div class="fieldbg" >
            <!-- <div class="form-label" > -->
            <label title="" class="form-label" for="registration_number" id="registration_number-lbl">
                <?php echo JText::_('COM_TOES_REGISTRATION_NUMBER'); ?>
            </label>
            <div class="form-input" >
                <?php echo @$this->item->cat_registration_number ?>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <label title="" class="form-label" for="breed" id="breed-lbl">
                <?php echo JText::_('COM_TOES_BREED'); ?>
            </label>
            <div class="form-input" >
                <?php
                foreach ($this->breed as $b) {
                    if (@$this->item->cat_breed == $b->value)
                        echo $b->text;
                }
                ?>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <label title="" class="form-label" for="date_of_birth" id="date_of_birth-lbl">
                <?php echo JText::_('COM_TOES_DATE_OF_BIRTH'); ?>
            </label>
            <div class="form-input" >
                <?php echo @$this->item->cat_date_of_birth ?>
            </div>
            <div class="clr"></div>
        </div>

        <div class="fieldbg" >
            <label title="" class="form-label" for="gender" id="gender-lbl">
                <?php echo JText::_('COM_TOES_GENDER'); ?>
            </label>
            <div class="form-input" >
                <?php
                foreach ($this->gender as $g) {
                    if (@$this->item->cat_gender == $g->value)
                        echo $g->text;
                }
                ?>
            </div>
        </div>
        <div class="clr"></div>
        <br/>
    </div>

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_COLOR'); ?></div>
        </div>
        <div class="clr"></div>
    </div>

    <div class="seconouter block-color">
        <br/>
        <div class="fieldbg" >
            <label title="" class="form-label" for="category" id="category-lbl">
                <?php echo JText::_('COM_TOES_CATEGORY'); ?>
            </label>
            <div class="form-input" id="cat_category">
                <?php
                foreach ($this->category as $c) {
                    if (@$this->item->cat_category == $c->value)
                        echo $c->text;
                }
                ?>
            </div>
        </div>

        <div class="fieldbg" >
            <label title="" class="form-label" for="division" id="division-lbl">
                <?php echo JText::_('COM_TOES_DIVISION'); ?>
            </label>
            <div class="form-input" id="cat_division">
                <?php
                foreach ($this->division as $d) {
                    if (@$this->item->cat_division == $d->value)
                        echo $d->text;
                }
                ?>
            </div>	
        </div>

        <div class="fieldbg" >
            <label title="" class="form-label" for="color" id="color-lbl">
                <?php echo JText::_('COM_TOES_COLOR'); ?>
            </label>
            <div class="form-input" id="cat_color">
                <?php
                foreach ($this->color as $col) {
                    if (@$this->item->cat_color == $col->value)
                        echo $col->text;
                }
                ?>
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

        <div class="fieldbg" >
            <label title="" class="form-label" for="name" id="name-lbl">
                <?php echo JText::_('COM_TOES_NAME'); ?>
            </label>
            <div class="form-input" >
                <?php echo @$this->item->cat_detail_name ?>
            </div>
        </div>
        <div class="clr"></div>
        <br/>
    </div>

    <div id="pedigree">
        <div class="fistouter">
            <div class="fieldblank" >
                <div class="block-title"><?php echo JText::_('COM_TOES_PEDIGREE'); ?></div>
            </div>
            <div class="clr"></div>
        </div>

        <div class="seconouter block-pedigree" >
            <br/>
            <div class="fieldbg">
                <label for="sire" class="form-label" id="sire-lbl">
                    <?php echo JText::_('COM_TOES_SIRE'); ?>
                </label>
                <div class="form-input" >
                    <?php echo @$this->item->cat_sire ?>
                </div>
            </div>

            <div class="fieldbg">
                <label for="dam" class="form-label" id="dam-lbl">
                    <?php echo JText::_('COM_TOES_DAM'); ?>
                </label>
                <div class="form-input" >
                    <?php echo @$this->item->cat_dam ?>
                </div>
            </div>
            <div class="clr"></div>
            <br/>
        </div>
    </div>

    <div class="fistouter">
        <div class="fieldblank" >
            <div class="block-title"><?php echo JText::_('COM_TOES_PEOPLE'); ?></div>
        </div>
        <div class="clr"></div>
    </div>
    <div class="seconouter block-people">
        <br/><br/>
        <div style="width:97%;padding:15px;">

            <label title="" class="hasTip required" for="owner" id="owner-lbl">
                <?php echo JText::_('COM_TOES_OWNER'); ?>
            </label>
            <div  id="username_ownerplace"><?php echo @$cat_owners ?></div>

            <label title="" class="hasTip required" for="breeder" id="breeder-lbl">
                <?php echo JText::_('COM_TOES_BREEDER'); ?>
            </label>
            <div  id="username_breederplace"><?php echo @$cat_breeders ?></div>

            <label title="" class="hasTip required" for="agent" id="agent-lbl">
                <?php echo JText::_('COM_TOES_AGENT'); ?>
            </label>
            <div  id="username_agentplace"  ><?php echo @$cat_agents ?></div>

            <label title="" class="hasTip required" for="lessee" id="lessee-lbl">
                <?php echo JText::_('COM_TOES_LESSEE'); ?>
            </label>
            <div  id="username_lesseeplace"  ><?php echo @$cat_lessees ?></div>

        </div>
        <div class="clr"></div>
        <br/>
    </div>

    <input type="hidden" name="sire_r" id="sire_r" value="" />
    <input type="hidden" name="dam_r" id="dam_r" value="" />
    <input type="hidden" name="task" value="cat.save" />
    <input type="hidden" name="view" value="cats" />
    <input type="hidden" name="id" value="<?php echo @$this->item->cat_id; ?>" />

    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>

