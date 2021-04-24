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


$isAdmin = TOESHelper::isAdmin();
$show_id = $app->input->getInt('id', 0);
$data = $this->show;

$isContinuous = ($data->show_format == 'Continuous') ? 1 : 0;

$user = $this->user;
//var_dump($user);
$entries = $this->entries;
$placeholders = $this->placeholders;

$db = JFactory::getDBO();

?>
<style>
div.doc_row{width:100%;clear:both}
div.doc_type_col{width:40%;margin:2px 6px;display:inline;float:left}
div.doc_org_col{width:40%;margin:2px 6px;display:inline;float:left}
div.doc_view_col{width:10%;margin:2px 6px;display:inline;float:left}
</style>
<style>
div.doc_row{width:100%;clear:both}
div.doc_type_col{width:35%;margin:2px 6px;display:inline;float:left}
div.doc_org_col{width:35%;margin:2px 6px;display:inline;float:left}
div.doc_view_col{width:7%;margin:2px 6px;display:inline;float:left}
.doc_div{display:none;border:1px solid #420a0a;}
span.breedabbrev_eo,span.status_eo,span.catalognumber_eo,span.name_eo,span.days_eo,span.class_eo,span.exhibitor_eo{display:inline-block;width:10%}
a.modal{position:relative!important;display:inline!important;}
</style>

<div class="user-div item">
	<?php if ($user): ?>
		<span style="font-weight: bold;padding: 0;">
			<?php if ($user->firstname || $user->lastname): ?>
				<?php echo $user->firstname . ' ' . $user->lastname; ?><br/>
			<?php else: ?>
				<?php echo $user->name; ?><br/>
			<?php endif; ?>
		</span>
		<?php
		if ($user->cb_address1) {
			echo $user->cb_address1 . ',';
		}
		if ($user->cb_address2) {
			echo ' ' . $user->cb_address2 . ',';
		}
		if ($user->cb_address3) {
			echo ' ' . $user->cb_address3;
		}
		if ($user->cb_address1 || $user->cb_address2 || $user->cb_address3) {
			echo '<br/>';
		}

		if ($user->cb_zip) {
			echo $user->cb_zip . ',';
		}
		if (isset($user->address_city) && $user->address_city) {
			echo ' ' . $user->address_city . ',';
		} else if ($user->cb_city) {
			echo ' ' . $user->cb_city . ',';
		}
		
		if (trim(isset($user->address_state)) && trim($user->address_state)) {
			
			echo ' ' . $user->address_state . ' -';
		}  else if ($user->cb_state) {
			echo ' ' . $user->cb_state . ',';
		}
		//if ($user->address_country) {
		if (isset($user->address_country) && $user->address_country) {
			echo ' ' . $user->address_country;
		} else if ($user->cb_country) {
			echo ' ' . $user->cb_country;
		}
		
		if ($user->cb_zip || $user->cb_zip || $user->cb_state || $user->cb_country) {
			echo '<br/>';
		}
		if ($user->email) {
			echo JText::_('COM_TOES_EMAIL') . ' : <a href="mailto:' . $user->email . '">' . $user->email . '</a><br/>';
		}
		if ($user->cb_phonenumber) {
			echo JText::_('COM_TOES_PHONE') . ' : ' . $user->cb_phonenumber;
		}
		?>
	<?php endif; ?>
</div>


<div class="show-entries-header">
	<span class="exhibitor_eo"></span>
	<span class="status_eo"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_STATUS_HEADER'); ?></span>
	<span class="catalognumber_eo"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CATALOG_NUMBER_HEADER'); ?></span>
	<span class="breedabbrev_eo"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_BREED_ABBREV_HEADER'); ?></span>
	<span class="name_eo"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER'); ?></span>
	<span class="days_eo"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER'); ?></span>
	<span class="class_eo"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER'); ?></span>	 
	<div class="clr"><hr/></div>
</div>
<?php
if ($entries):
	$prev_cat = 0;
	$prev_entry_status = '';
	$entries_cnt = 0;
	foreach ($entries as $entry) :
		//var_dump($entry->cat);

		$entries_cnt++;
		if ($isContinuous)
			$days = JText::_('JALL');
		else
			$days = $entry->showdays;
		if ($entry->congress)
			$congress_names = $entry->congress;
		else
			$congress_names = '-';
		?>
		<div class="item <?php echo ($entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed') ? 'grey_entry' : '' ?>">
			<span class="exhibitor_eo">
				<?php if ($entry->cat != $prev_cat || $prev_entry_status != $entry->entry_status) : ?>
						<span class="hasTip" title="<?php echo JText::_('VIEW_DETAILS'); ?>">
							<a href="javascript:void(0)" rel="<?php echo $item->entry_id; ?>" onclick="view_entry_details(this, 'cat-<?php echo $entry->entry_id; ?>', 0);" class="view-entry-details">
								<i class="fa fa-file-text-o"></i> 
							</a>
						</span>
						<?php //if ($data->show_status != 'Held'): ?>						  
							<?php //if (($item->entry_status == 'New' || $item->entry_status == 'Accepted' )&& (!$item->cat_registration_number || strtolower($item->cat_registration_number) == 'pending') && $item->documents > 0 ) : ?>
							<?php if ((!$entry->cat_registration_number || strtolower($entry->cat_registration_number) == 'pending') && $entry->documents > 0 ) : ?>
								<span id="docs_<?php echo $entry->entry_id?>_<?php echo $entry->entry_show?>" class="hasTip" title="<?php echo JText::_('COM_TOES_REGISTRATION_OPEN_DOCUMENT_VIEW'); ?>">
									<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show. ';' . $entry->cat; ?>" class="documents-show" >
										<i class="fa fa-eye"></i> 
									</a>
								</span>
							<?php endif; ?>
						<?php //endif; ?>
						<?php
						$prev_cat = $entry->cat;
						$prev_entry_status = $entry->entry_status;
						?>
				<?php endif; ?>
			</span>
			<span class="status_eo"><?php echo $entry->entry_status; ?></span>
			<span class="catalognumber_eo"><?php echo $entry->catalog_number; ?></span>
			<span class="breedabbrev_eo"><?php echo $entry->breed_abbreviation; ?></span>
			<span class="name_eo"><?php echo $entry->cat_prefix_abbreviation . ' ' . $entry->cat_title_abbreviation . ' ' . $entry->copy_cat_name . ' ' . $entry->cat_suffix_abbreviation; ?></span>
			<span class="days_eo">
				<?php echo $days; ?>
			</span>
			<span class="class_eo"><?php echo $entry->Show_Class; ?></span>			 
			<div class="clr"></div>
			<div id="doc_div_<?php echo $entry->entry_id?>" class="doc_div">
			 
			<?php
				$db = JFactory::getDBO(); 
				$query = "select d.*,o.recognized_registration_organization_name,o.recognized_registration_organization_affiliation,t.allowed_registration_document_name_language_constant from `#__toes_cat_document` as d 
				LEFT JOIN `#__toes_recognized_registration_organization` as o on o.recognized_registration_organization_id = d.cat_document_registration_document_organization_id
				LEFT JOIN `#__toes_allowed_registration_document_type` as t on t.allowed_registration_document_id = d.cat_document_registration_document_type_id
				where d.`cat_document_cat_id`=".$entry->cat;
				//echo '<br/>';
				$db->setQuery($query);
				$this->documents = $db->loadObjectList();
				//var_dump($this->documents);
			if(count($this->documents)){?>
			<div class="org_doc" id="org_doc_<?php echo $entry->entry_id?>_<?php echo $entry->entry_show?>">
			<div class="cleardocs" style="width:100%;clear:both;text-align:right;color:red">
				<span class="hasTip" title="<?php echo JText::_('COM_TOES_REGISTRATION_CLOSE_DOCUMENT_VIEW'); ?>">
				<a href="javascript:void(null);" >
				<i class="fa fa-remove"></i>
				</a>
				</span>
			</div>
				 
				<div class="doc_row"><div class="doc_type_col"><?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_TYPE_HEADER')?></div><div class="doc_org_col"><?php echo JText::_('COM_TOES_REGISTRATION_ORGANIZATION_HEADER')?></div><div class="doc_view_col"><?php echo JText::_('COM_TOES_REGISTRATION_DOCUMENT_FILENAME')?></div></div>
				<?php foreach($this->documents as $doc){?>
				<div class="doc_row">
					<div class="doc_type_col">
						<?php echo JText::_($doc->allowed_registration_document_name_language_constant)?>
						<a class="modal" href="index.php?option=com_toes&view=executiveoffice&layout=documenttype&tmpl=component&doc_id=<?php echo $doc->cat_document_id?>"  rel="{handler:'iframe'}">
						<i class="fa fa-edit"></i>
						</a>
					</div>
					<div class="doc_org_col">
						<?php echo $doc->recognized_registration_organization_name?>
						<a class="modal" href="index.php?option=com_toes&view=executiveoffice&layout=organization&tmpl=component&doc_id=<?php echo $doc->cat_document_id?>"  rel="{handler:'iframe'}">
						<i class="fa fa-edit"></i>
						</a>
					</div>			
					<div class="doc_view_col">
						<span class="hasTip" title="<?php echo JText::_('COM_TOES_REGISTRATION_VIEW_DOCUMENT'); ?>">
						<a target="_blank"  href="<?php echo JURI::root().$doc->cat_document_file_name?>">
						<i class="fa fa-eye"></i>
						</a>
						</span>
					</div>		
				</div>
				<?php } ?>
				 
				<div class="clr"></div>
				<?php //if($item->entry_status != 'Accepted'){?>
				<span class="hasTip" title="<?php echo JText::_('APPROVE_DOCUMENTS'); ?>">
					<a href="javascript:void(0)" rel="<?php echo $item->entry_id . ';' . $item->entry_show; ?>" class="approve-documents" onclick="eo_approve_documents('<?php echo $item->cat?>');" >
						<i class="fa fa-check"></i> 
					</a>
				</span>
				<span class="hasTip" title="<?php echo JText::_('REJECT_DOCUMENTS'); ?>">
					<a href="javascript:void(0)" rel="<?php echo $item->entry_id . ';' . $item->entry_show; ?>" class="reject-documents" onclick="eo_reject_documents('<?php echo $item->cat?>');" >
						<i class="fa fa-power-off"></i> 
					</a>
				</span>
			</div>
			<?php }   ?>
			<div class="clr"></div>
			</div>
			 
			<div class="clr"></div>
		</div>
		<div id="cat-<?php echo $entry->entry_id; ?>" class="cat-details" style="display: none;"></div>
		<div class="clr"></div>

		<?php
		$this->cnt++;
	endforeach;
	?>
<?php endif; ?>                                
  


<style>
.doc_div{display:none;border:1px solid #420a0a;}
</style>					
