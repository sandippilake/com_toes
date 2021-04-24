<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die; 
JHtml::_('formbehavior2.select2','select.inputbox');
$app = JFactory::getApplication();
$id  = $app->input->getInt('id');
?> 

<div id="dt_<?php echo $id?>" class="dtdiv">
	<div  class="del_document_type" data-id="<?php echo $id?>" style="text-align:left;display:inline">
		<?php echo JText::_($this->document_type->allowed_registration_document_title_language_constant);?>
		<a>
		<i class="fa fa-trash" title="<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_DELETE_THIS_DOCUMENT');?>"></i>
		</a>
	</div>
	<div class="docrow">
	<div class="form-label" ><span style=""><?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_RECOGNIZED_ORGANIZATION_LABEL');?></span></div>
	<div class="form-input organization" >
		<select name="organization_<?php echo $id?>" id="organization_<?php echo $id?>" 
		 class="inputbox organization_select required" >
			<?php
			foreach ($this->organizations as $o)
			{
				echo '<option value="' . $o->value . '" ' . $sel . '>' . $o->text . '</option>';
			}
			?>
		</select>
	</div>
	</div>
	<div class="clr"></div>
	<div class="docrow">
	<div class="form-label" ><span style="">&nbsp;</span></div>
	<div class="form-input document" >
		<input type="file" name="document_<?php echo $id?>" id="document_<?php echo $id?>" 
		class="document_file required">
	</div>
	</div>
	<div class="clr"></div>
	<a href="javascript:void(null)" class="add_document_type_btn">
		<i class="fa fa-upload" title="<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_ADD_ANOTHER_DOCUMENT');?>"></i>
		<?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_ADD_ANOTHER_DOCUMENT');?>
	</a>
	<input type="hidden" class="document_type_id" name="document_type_id[]" value="<?php echo $id?>"/>
</div>
