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
 
$entry_id = $app->input->getInt('entry_id');
$show_id = $app->input->getInt('show_id');
$cat = $app->input->getInt('cat');
$summary_id = $app->input->getInt('summary_id'); 

if(count($this->documents)){?>
			<div class="org_doc" id="org_doc_<?php echo $entry->entry_id?>_<?php echo $entry->entry_show?>">
			<?php /*
			<div class="cleardocs" style="width:100%;clear:both;text-align:right">X</div>
			*/ ?> 
				<table width="100%">
				<tr><td><?php echo JText::_('COM_TOES_DOCUMENT_TYPE')?></td><td><?php echo JText::_('COM_TOES_ORGANIZATION')?></td><td><?php echo JText::_('COM_TOES_DOCUMENT')?></td><td></td></tr>
				<?php foreach($this->documents as $doc){?>
				<tr><td><?php echo JText::_($doc->allowed_registration_document_name_language_constant)?></td>
				<td><?php echo $doc->recognized_registration_organization_name?></td>			
				<td><?php echo basename($doc->cat_document_file_name)?></td>	
				<td><a target="_blank" href="<?php echo JURI::root().$doc->cat_document_file_name?>">View</a></td></tr>
				<?php } ?>
				</table>
				
				<input type="hidden" id="document_cat_id" value="<?php echo $cat?>"/>
				<input type="hidden" id="document_entry_id" value="<?php echo $entry_id?>"/>
				<input type="hidden" id="document_show_id" value="<?php echo $show_id?>"/>
				<?php if($this->entrystatus != 'Accepted'){?>
				<span class="hasTip" title="<?php echo JText::_('APPROVE_DOCUMENTS'); ?>">
					<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" class="approve-documents" onclick="approve_documents('<?php echo $summary_id?>');" >
						<i class="fa fa-check"></i> 
					</a>
				</span>
				<span class="hasTip" title="<?php echo JText::_('REJECT_DOCUMENTS'); ?>">
					<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" class="reject-documents" onclick="reject_documents('<?php echo $summary_id?>');" >
						<i class="fa fa-power-off"></i> 
					</a>
				</span>
				<?php } ?>
			</div>
			<?php } ?>
			 
<style>
a.modal{position:relative!important;display:block!important;}
</style>
 
