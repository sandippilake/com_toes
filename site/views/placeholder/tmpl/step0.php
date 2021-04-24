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
?>
<div id="select-user">
    <h3><?php echo JText::_('NEW_PLACEHOLDER') ?></h3>

    <label class="label"><?php echo JText::_("COM_TOES_SELECT_USER"); ?></label>
    <div>
		<input type="text" size="30" value="" id="user_name" name="user_name" />
		<input type="hidden" name="user_id" id="user_id" value="" />

    </div>

    <br/>	
    <a href="javascript:void(0);" onclick="add_new_user();"><?php echo JText::_('COM_TOES_ADD_NEW_USER'); ?></a>
    <br/>	

    <div class="fieldbg" >
        <input type="hidden" value="placeholder" name="source" id="source" />
        <input type="hidden" value="<?php echo $app->input->getVar('type'); ?>" name="type" id="type"/>
        <input type="hidden" value="<?php echo $app->input->getVar('parent_div'); ?>" name="parent_div" id="parent_div"/>
        <input type="hidden" value="<?php echo $this->placeholder->placeholder_show; ?>" name="show_id" id="show_id"/>
        <input onclick="cancel_edit_placeholder();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
        <input onclick="placeholder_next_step('step0');" type="button" name="button" value="<?php echo JText::_('COM_TOES_NEXT'); ?>" />
    </div>
</div>

<div id="new-user" style="display: none;">
    <h3><?php echo JText::_("COM_TOES_ADD_NEW_USER"); ?></h3>
    <div class="clr"></div>
    <label for="firstname" class="label"><?php echo JText::_("COM_TOES_USER_FIRST_NAME"); ?></label>
    <span>
        <input type="text" name="firstname" id="firstname" value="" />
    </span>
    <div class="clr"></div>
    <label for="lastname" class="label"><?php echo JText::_("COM_TOES_USER_LAST_NAME"); ?></label>
    <span>
        <input type="text" name="lastname" id="lastname" value="" />
    </span>
    <div class="clr"></div>
    <label for="username" class="label"><?php echo JText::_("COM_TOES_USER_USERNAME"); ?></label>
    <span>
        <input type="text" name="username" id="username" value="" />
    </span>
    <div class="clr"></div>
    <label for="email" class="label"><?php echo JText::_("COM_TOES_USER_EMAIL"); ?></label>
    <span>
        <input type="text" name="email" id="email" value="" />
    </span>
    <div class="clr"></div>
    <label for="address1" class="label"><?php echo JText::_("COM_TOES_USER_ADDRESS1"); ?></label>
    <span>
        <input type="text" name="address1" id="address1" value="" />
    </span>
    <div class="clr"></div>
    <label for="address2" class="label"><?php echo JText::_("COM_TOES_USER_ADDRESS2"); ?></label>
    <span>
        <input type="text" name="address2" id="address2" value="" />
    </span>
    <div class="clr"></div>
    <label for="address3" class="label"><?php echo JText::_("COM_TOES_USER_ADDRESS3"); ?></label>
    <span>
        <input type="text" name="address3" id="address3" value="" />
    </span>
    <div class="clr"></div>
    <label for="country" class="label"><?php echo JText::_("COM_TOES_USER_COUNTRY"); ?></label>
    <span>
        <input type="text" name="country_name" id="country_name" value="" />
    </span>
    <div class="clr"></div>
	<div class="state_div">
		<label for="state" class="label"><?php echo JText::_("COM_TOES_USER_STATE"); ?></label>
		<span>
			<input type="text" name="state_name" id="state_name" value="" />
		</span>
	</div>
    <div class="clr"></div>
    <label for="city" class="label"><?php echo JText::_("COM_TOES_USER_CITY"); ?></label>
    <span>
        <input type="text" name="city_name" id="city_name" value="" />
    </span>
    <div class="clr"></div>
    <label for="zip" class="label"><?php echo JText::_("COM_TOES_USER_ZIP"); ?></label>
    <span>
        <input type="text" name="zip" id="zip" value="" />
    </span>
    <div class="clr"></div>
    <label for="phonenumber" class="label"><?php echo JText::_("COM_TOES_USER_PHONENUMBER"); ?></label>
    <span>
        <input type="text" name="phonenumber" id="phonenumber" value="" />
    </span>
    <div class="clr"></div>
    <label for="phonenumber" class="label"><?php echo JText::_("COM_TOES_SELECT_COMPETITIVE_REGION"); ?></label>
    <span>
		<?php echo JHtml::_('select.genericlist', $this->regions, 'tica_region', 'style="width:200px;"', 'key', 'value'); ?>
    </span>
    <div class="clr"></div>
    <input onclick="cancel_new_user();" type="button" name="button" value="<?php echo JText::_('COM_TOES_CANCEL'); ?>" />
    <input onclick="save_new_user();" type="button" name="button" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
	<input type="hidden" name="country" id="country" value="" />
	<input type="hidden" name="state" id="state" value="" />
	<input type="hidden" name="city" id="city" value="" />
</div>
