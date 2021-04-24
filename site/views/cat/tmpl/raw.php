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
$cnt  = $app->input->getInt('cnt');
?> 

<div class="single">
 	
 	<div class="del_document_type" data-id="<?php echo $id?>">X</div>
	<div class="form-input organization" >
		<select name="organization_<?php echo $id?>" id="organization_<?php echo $id?>" 
		 class="inputbox organization_select" >
			<?php
			foreach ($this->organizations as $o)
			{
				echo '<option value="' . $o->value . '" ' . $sel . '>' . $o->text . '</option>';
			}
			?>
		</select>
	</div>
	<div class="form-input document" >
		<input type="file" name="document_<?php echo $id?>" id="document_<?php echo $id?>" 
		class="document_file">
	</div>
	<div class="clr"></div>
	
	
	<a href="javascript:void(null)" class="add_document_type_btn"></a>
</div>

