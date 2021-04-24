<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die; 
$app = JFactory::getApplication();
$id  = $app->input->getInt('id');
?> 

<div id="dt_<?php echo $id?>" class="dtdiv">
	
	<h3 class="document_type"><?php echo JText::_($this->document_type->allowed_registration_document_type)?></h3>
	<h3 class="document_type"><?php echo JText::_($this->document_type->allowed_registration_document_name_language_constant)?></h3>
	<div class="del_document_type" data-id="<?php echo $id?>"><a>X</a></div>
	<div class="form-input organization" >
		<select name="organization_<?php echo $id?>[]" id="organization_<?php echo $id?>" 
		 class="inputbox organization_select required" >
			<?php
			foreach ($this->organizations as $o)
			{
				echo '<option value="' . $o->value . '" ' . $sel . '>' . $o->text . '</option>';
			}
			?>
		</select>
	</div>
	<div class="form-input document" >
		<input type="file" name="document_<?php echo $id?>[]" id="document_<?php echo $id?>" 
		class="document_file required">
	</div>
	<div class="clr"></div>
	<div class="form-input organization" >
		<select name="organization_<?php echo $id?>[]" id="organization_<?php echo $id?>" 
		 class="inputbox organization_select required" >
			<?php
			foreach ($this->organizations as $o)
			{
				echo '<option value="' . $o->value . '" ' . $sel . '>' . $o->text . '</option>';
			}
			?>
		</select>
	</div>
	<div class="form-input document" >
		<input type="file" name="document_<?php echo $id?>[]" id="document_<?php echo $id?>" 
		class="document_file required">
	</div>
	<div class="clr"></div>
</div>
