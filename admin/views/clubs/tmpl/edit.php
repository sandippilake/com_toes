
<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_templates
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$user = JFactory::getUser();
//$canDo = TemplatesHelper::getActions();

?>


<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'clubs.cancel' || document.formvalidator.isValid(document.id('service-form'))) {
            Joomla.submitform(task, document.getElementById('service-form'));
        }
    }
</script>
<style type="text/css">
    .field-text {  float: left; margin-right: 10px; width: 150px;}
    .field-value {  float: left;}
</style>

<form action="<?php echo JRoute::_('index.php?option=com_toes&layout=edit&club_id=' . (int) $this->item->club_id); ?>" method="post" name="adminForm" id="service-form" class="form-validate">
    <div>
        <fieldset class="adminform">
            <legend><?php echo JText::_('JDETAILS'); ?></legend>

            <ul class="adminformlist">
                <li>
                    <label for="club_name" class="hasTip required" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_NAME'); ?><span class="star">&nbsp;*</span>
                    </label>
                    <input id="club_name" name="club_name" type="text" value="<?php echo @$this->item->club_name; ?>" class="inputbox required" size="40"/>
                </li>

                <li>
                    <label for="club_abbreviation" class="hasTip required" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_ABBRIVATION'); ?>:
                    </label>
                    <input id="club_abbreviation" name="club_abbreviation" type="text" value="<?php echo @$this->item->club_abbreviation; ?>" class="inputbox required" size="40"/>
                </li>

                <li>
                    <label class="hasTip" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_WEBSITE'); ?>:
                    </label>
                    <input name="club_website" type="text" value="<?php echo @$this->item->club_website; ?>" class="inputbox" size="40" />
                </li>

                <li>
                    <label for="club_email" class="hasTip required" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_EMAIL'); ?>:
                    </label>
                    <input id="club_email" name="club_email" type="text" value="<?php echo @$this->item->club_email; ?>" class="inputbox required" size="40"/>
                </li>

                <li>
                    <label class="hasTip" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_INVOICE_PAYPAL'); ?>:
                    </label>
                    <input name="club_invoice_paypal" type="text" value="<?php echo @$this->item->club_invoice_paypal; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_PAYPAL'); ?>:
                    </label>
                    <input name="club_paypal" type="text" value="<?php echo @$this->item->club_paypal; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_IBAN'); ?>:
                    </label>
                    <input name="club_iban" type="text" value="<?php echo @$this->item->club_iban; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_BIC'); ?>:
                    </label>
                    <input name="club_bic" type="text" value="<?php echo @$this->item->club_bic; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_ACCOUNT_HOLDER_NAME'); ?>:
                    </label>
                    <input name="club_account_holder_name" type="text" value="<?php echo @$this->item->club_account_holder_name; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip " title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_ACCOUNT_HOLDER_ADDRESS'); ?>:
                    </label>
                    <input name="club_account_holder_address" type="text" value="<?php echo @$this->item->club_account_holder_address; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip " title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_ACCOUNT_HOLDER_ZIP'); ?>:
                    </label>
                    <input name="club_account_holder_zip" type="text" value="<?php echo @$this->item->club_account_holder_zip; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip " title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_ACCOUNT_HOLDER_CITY'); ?>:
                    </label>
                    <input name="club_account_holder_city" type="text" value="<?php echo @$this->item->club_account_holder_city; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip " title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_ACCOUNT_HOLDER_STATE'); ?>:
                    </label>
                    <input name="club_account_holder_state" type="text" value="<?php echo @$this->item->club_account_holder_state; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip " title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_ACCOUNT_HOLDER_COUNTRY'); ?>:
                    </label>
                    <input name="club_account_holder_country" type="text" value="<?php echo @$this->item->club_account_holder_country; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_COST_PER_ENTRY'); ?>:
                    </label>
                    <input name="club_cost_per_entry" type="text" value="<?php echo @$this->item->club_cost_per_entry; ?>" class="inputbox" size="40"/>
                </li>

                <li>
                    <label class="hasTip " title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_ORGANIZATION'); ?>:
                    </label>
                    <?php echo $this->orglist; ?>
                </li>

                <li>
                    <label for="club_competitive_region" class="hasTip required" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_COMPETATIVE_REGION'); ?>:
                    </label>
                    <?php echo $this->regionlist; ?>
                </li>

                <li>
                    <label class="hasTip required" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_SHOW_BAD_DEBT_CLUB'); ?>:
                    </label>
                    <input name="club_on_toes_bad_debt_list" type="checkbox" value="1" class="inputbox" <?php echo $this->item->club_on_toes_bad_debt_list?'checked="checked"':'' ?> />
                </li>

                <li>
                    <label class="hasTip required" title="" aria-invalid="false">
                        <?php echo JText::_('COM_TOES_CLUB_ID'); ?>:
                    </label>
                    <span class="readonly"><?php echo @$this->item->club_id; ?></span>
                </li>
            </ul>

        </fieldset>
        </div>

    <input type="hidden" name="club_id" value="<?php echo @$this->item->club_id; ?>" />
    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
    <div class="clr"></div>
</form>