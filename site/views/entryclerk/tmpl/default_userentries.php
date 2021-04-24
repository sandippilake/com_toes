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
<div>
	<strong><?php echo JText::_('COM_TOES_SHOW_SUMMARY'); ?></strong><br/>
	<span>
		<?php echo ( $data->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_CAGES') ) . ' - '; ?>
		<?php echo $user->summary_single_cages; ?>
	</span>
	<span>
		<?php echo ( $data->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_CAGES') ) . ' - '; ?>
		<?php echo $user->summary_double_cages; ?>
	</span>
	<span>
		<?php echo JText::_('COM_TOES_SHOW_SUMMARY_PERSONAL_CAGES') . ' - '; ?>
		<?php echo $user->summary_personal_cages ? JText::_('JYES') : JText::_('JNO'); ?>
	</span>
	<span>
		<?php echo JText::_('COM_TOES_SHOW_SUMMARY_GROOMING_SPACE') . ' - '; ?>
		<?php echo $user->summary_grooming_space ? JText::_('JYES') : JText::_('JNO'); ?>
	</span>
	<br/>
	<span>
		<?php echo JText::_('COM_TOES_SHOW_SUMMARY_BENCHING_REQUEST') . ' - '; ?>
		<?php echo $user->summary_benching_request; ?>
	</span>
	<br/>
	<span>
		<?php echo JText::_('COM_TOES_SHOW_SUMMARY_REMARKS') . ' - '; ?>
		<?php echo $user->summary_remarks; ?>
	</span>
	<br/>
	<span>
		<?php echo JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_NOTE_TITLE') . ' - '; ?>
		<?php echo $user->summary_entry_clerk_note; ?>
	</span>
	<br/>
	<span>
		<?php echo JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_PRIVATE_NOTE_TITLE') . ' - '; ?>
		<?php echo $user->summary_entry_clerk_private_note; ?>
	</span>
	<div class="clr"></div>
	<a href="javascript:void(0);" onclick="edit_summary(<?php echo $user->summary_id; ?>);">
		<i class="fa fa-edit"></i> 
		<?php echo JText::_('COM_TOES_EDIT_SUMMARY'); ?>
	</a>
	<div class="edit-summary-div" id="edit-summary-<?php echo $user->summary_id; ?>-div">
	</div>
</div>  

<div class="show-entries-header">
	<span class="exhibitor"></span>
	<span class="status"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_STATUS_HEADER'); ?></span>
	<span class="catalognumber"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CATALOG_NUMBER_HEADER'); ?></span>
	<span class="breedabbrev"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_BREED_ABBREV_HEADER'); ?></span>
	<span class="name"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER'); ?></span>
	<span class="days"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER'); ?></span>
	<span class="class"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER'); ?></span>
	<span class="exh"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER'); ?></span>
	<span class="congress"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER'); ?></span>
	<span class="created"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_DATE_CREATED_HEADER'); ?></span>
	<div class="clr"><hr/></div>
</div>
<?php
if ($entries):
	$prev_cat = 0;
	$prev_entry_status = '';
	$entries_cnt = 0;
	foreach ($entries as $entry) :

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
			<span class="exhibitor">
				<?php if ($entry->cat != $prev_cat || $prev_entry_status != $entry->entry_status) : ?>
					<span class="hasTip" title="<?php echo JText::_('VIEW_DETAILS'); ?>">
						<a href="javascript:void(0)" rel="<?php echo $entry->entry_id; ?>" onclick="view_entry_details(this, 'cat-<?php echo $entry->entry_id; ?>', 0);" class="view-entry-details">
							<i class="fa fa-file-text-o"></i> 
						</a>
					</span>
					<?php if ($data->show_status != 'Held'): ?>
						<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Rejected'): ?>
							<span class="hasTip" title="<?php echo JText::_('APPROVE_ENTRY'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" id="approve_entry_<?php echo $entry->entry_id; ?>" class="approve-entry">
									<i class="fa fa-check"></i> 
								</a>
							</span>
						<?php endif; ?>
						<?php if ($entry->entry_status == 'New' || $entry->entry_status == 'Accepted'): ?>
							<span class="hasTip" title="<?php echo JText::_('REJECT_ENTRY'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" id="reject_entry_<?php echo $entry->entry_id; ?>" class="reject-entry">
									<i class="fa fa-remove"></i> 
								</a>
							</span>
						<?php endif; ?>
						<?php if ($data->show_status == 'Open' && ($entry->entry_status == 'Rejected' || $entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed')): ?>
							<span class="hasTip" title="<?php echo JText::_('REENTER_ENTRY'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" class="reenter-entry" onclick="reenter_entry(this);">
									<i class="fa fa-power-off"></i> 
								</a>
							</span>
						<?php endif; ?>
						<span class="hasTip" title="<?php echo JText::_('EDIT_ENTRY'); ?>">
							<a href="javascript:void(0)" onclick="edit_entry('<?php echo $entry->cat . ';' . $entry->entry_show . ';' . $entry->summary_user; ?>', 'add-entry-div-<?php echo $user->id; ?>');" class="edit-entry">
								<i class="fa fa-edit"></i> 
							</a>
						</span>
						<?php if ($entry->entry_status != 'Cancelled' && $entry->entry_status != 'Cancelled & Confirmed'): ?>
							<span class="hasTip" title="<?php echo JText::_('CANCEL_ENTRY'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" class="cancel-entry" onclick="cancel_entry(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_REJECT_ENTRY'); ?>');">
									<i class="fa fa-remove"></i> 
								</a>
							</span>
						<?php endif; ?>
						<span class="hasTip" title="<?php echo JText::_('DELETE_ENTRY'); ?>">
							<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" class="delete-entry" onclick="delete_entry(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_ENTRY'); ?>');">
								<i class="fa fa-trash"></i> 
							</a>
						</span>
						<?php if ((!$entry->cat_registration_number || strtolower($entry->cat_registration_number) == 'pending') && $entry->documents > 0 ) : ?>
							<span id="docs_<?php echo $entry->entry_id?>_<?php echo $entry->entry_show?>" class="hasTip" title="<?php echo JText::_('COM_TOES_REGISTRATION_OPEN_DOCUMENT_VIEW'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show. ';' . $entry->cat; ?>" class="documents-show" >
									<i class="fa fa-eye"></i> 
								</a>
							</span>
						<?php endif; ?>
					<?php endif; ?>
					<?php
					$prev_cat = $entry->cat;
					$prev_entry_status = $entry->entry_status;
					?>
				<?php endif; ?>
			</span>
			<span class="status"><?php echo $entry->entry_status; ?></span>
			<span class="catalognumber"><?php echo $entry->catalog_number; ?></span>
			<span class="breedabbrev"><?php echo $entry->breed_abbreviation; ?></span>
			<span class="name"><?php echo $entry->cat_prefix_abbreviation . ' ' . $entry->cat_title_abbreviation . ' ' . $entry->copy_cat_name . ' ' . $entry->cat_suffix_abbreviation; ?></span>
			<span class="days">
				<?php echo $days; ?>
			</span>
			<span class="class"><?php echo $entry->Show_Class; ?></span>
			<span class="exh"><?php echo ($entry->exhibition_only) ? JText::_('JYES') : JText::_('JNO'); ?></span>
			<span class="congress"><?php echo $congress_names; ?></span>
			<span class="created"><?php echo date('M d, H:i', strtotime($entry->entry_date_created)); ?></span>
			<div class="clr"></div>
			<div id="doc_div_<?php echo $entry->entry_id?>" class="doc_div">
			 
			<?php
			 
			$query = "select d.*,o.recognized_registration_organization_name,o.recognized_registration_organization_affiliation,t.allowed_registration_document_name_language_constant from `#__toes_cat_document` as d 
			LEFT JOIN `#__toes_recognized_registration_organization` as o on o.recognized_registration_organization_id = d.cat_document_registration_document_organization_id
			LEFT JOIN `#__toes_allowed_registration_document_type` as t on t.allowed_registration_document_id = d.cat_document_registration_document_type_id
			where d.`cat_document_cat_id`=".$entry->cat;
			$db->setQuery($query);
			$this->documents = $db->loadObjectList();
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
					<div class="doc_type_col"><?php echo JText::_($doc->allowed_registration_document_name_language_constant)?></div>
					<div class="doc_org_col"><?php echo $doc->recognized_registration_organization_name?></div>			
					<div class="doc_view_col">
						<span class="hasTip" title="<?php echo JText::_('COM_TOES_REGISTRATION_VIEW_DOCUMENT'); ?>">
						<a target="_blank"  href="<?php echo JURI::root().$doc->cat_document_file_name?>">
						<i class="fa fa-eye"></i>
						</a>
						</span>
						</div>
				</div>
				<?php } ?>
				 
				<?php if($entry->entry_status != 'Accepted'){?>
				<span class="hasTip" title="<?php echo JText::_('APPROVE_DOCUMENTS'); ?>">
					<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" class="approve-documents" onclick="approve_documents('<?php echo $entry->entry_id?>');" >
						<i class="fa fa-check"></i> 
					</a>
				</span>
				<span class="hasTip" title="<?php echo JText::_('REJECT_DOCUMENTS'); ?>">
					<a href="javascript:void(0)" rel="<?php echo $entry->entry_id . ';' . $entry->entry_show; ?>" class="reject-documents" onclick="reject_documents('<?php echo $entry->entry_id?>');" >
						<i class="fa fa-remove"></i>
					</a>
				</span>
				<?php } ?>
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
<?php
if ($entries && $placeholders) {
	echo '<div class="clr" style="padding: 0; border-bottom: 1px dashed #000;"></div>';
}
if ($placeholders) :
	$prev_placeholder = '';
	$prev_placeholder_status = '';
	foreach ($placeholders as $placeholder) :
		if ($isContinuous)
			$days = JText::_('JALL');
		else
			$days = $placeholder->showdays;
		?>
		<div class="item <?php echo ($placeholder->entry_status == 'Cancelled' || $placeholder->entry_status == 'Cancelled & Confirmed') ? 'grey_entry' : '' ?>">
			<span class="exhibitor">
				<?php if ($placeholder->placeholder_id != $prev_placeholder || $prev_placeholder_status != $placeholder->entry_status) : ?>
					<?php if ($data->show_status != 'Held'): ?>
						<?php if ($placeholder->entry_status != 'Cancelled' && $placeholder->entry_status != 'Cancelled & Confirmed'): ?>
							<?php if ($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Waiting List'): ?>
								<?php /* <span class="hasTip" title="<?php echo JText::_('DELETE_PLACEHOLDER');?>">
								  <a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id.';'.$placeholder->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this,'<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_PLACEHOLDER');?>');">
								  <i class="fa fa-trash"></i> 
								  </a>
								  </span> */ ?>
							<?php else: ?>
								<span class="hasTip" title="<?php echo JText::_('CANCEL_PLACEHOLDER'); ?>">
									<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id . ';' . $placeholder->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_REJECT_PLACEHOLDER'); ?>');">
										<i class="fa fa-remove"></i> 
									</a>
								</span>
							<?php endif; ?>
						<?php endif; ?>
						<?php if ($placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Cancelled' || $placeholder->entry_status == 'Cancelled & Confirmed') : ?>
							<span class="hasTip" title="<?php echo JText::_('REENTER_PLACEHOLDER'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id . ';' . $placeholder->placeholder_show; ?>" class="reenter-placeholder" onclick="reenter_placeholder(this);">
									<i class="fa fa-power-off"></i> 
								</a>
							</span>
						<?php endif; ?>
						<?php if ($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Rejected') : ?>
							<span class="hasTip" title="<?php echo JText::_('APPROVE_PLACEHOLDER'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id . ';' . $placeholder->placeholder_show; ?>" class="approve-placeholder">
									<i class="fa fa-check"></i> 
								</a>
							</span>
						<?php endif; ?>
						<?php if (($data->show_status == 'Open' && $placeholder->entry_status == 'New') || $placeholder->entry_status == 'Accepted' || $placeholder->entry_status == 'Confirmed' || $placeholder->entry_status == 'Confirmed & Paid') : ?>
							<span class="hasTip" title="<?php echo JText::_('CONVERT_TO_ENTRY'); ?>">
								<a href="javascript:void(0)" onclick="convert_placeholder('<?php echo $placeholder->placeholder_id; ?>', '<?php echo $placeholder->placeholder_show; ?>', '<?php echo $placeholder->placeholder_exhibitor; ?>', 'add-placeholder-div-<?php echo $placeholder->placeholder_exhibitor; ?>');" class="convert-placeholder">
									<i class="fa fa-refresh"></i> 
								</a>
							</span>
						<?php endif; ?>
						<?php if ($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Accepted'): ?>
							<span class="hasTip" title="<?php echo JText::_('REJECT_PLACEHOLDER'); ?>">
								<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id . ';' . $placeholder->placeholder_show; ?>" class="reject-placeholder">
									<i class="fa fa-remove"></i> 
								</a>
							</span>
						<?php endif; ?>
						<?php if ($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Accepted' || $placeholder->entry_status == 'Waiting List') : ?>
							<span class="hasTip" title="<?php echo JText::_('EDIT_PLACEHOLDER'); ?>">
								<a href="javascript:void(0)" onclick="edit_placeholder('<?php echo $placeholder->placeholder_id; ?>', '<?php echo $placeholder->placeholder_exhibitor; ?>', 'add-placeholder-div-<?php echo $user->id; ?>');" class="edit-placeholder">
									<i class="fa fa-edit"></i> 
								</a>
							</span>
						<?php endif; ?>
						<span class="hasTip" title="<?php echo JText::_('DELETE_PLACEHOLDER'); ?>">
							<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id . ';' . $placeholder->placeholder_show; ?>" class="delete-placeholder" onclick="delete_placeholder(this, '<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_PLACEHOLDER'); ?>');">
								<i class="fa fa-trash"></i> 
							</a>
						</span>
					<?php endif; ?>
					<?php
					$prev_placeholder = $placeholder->placeholder_id;
					$prev_placeholder_status = $placeholder->entry_status;
					?>
				<?php endif; ?>
			</span>
			<span class="status"><?php echo $placeholder->entry_status; ?></span>
			<span class="name"><?php echo JText::_('COM_TOES_PLACEHOLDER'); ?></span>
			<span class="days">
				<?php echo $days; ?>
			</span>
			<span class="class">&nbsp;</span>
			<span class="exh">&nbsp;</span>
			<span class="forsale">&nbsp;</span>
			<span class="congress">&nbsp;</span>
			<span class="created"><?php echo date('M d, H:i', strtotime($placeholder->placeholder_day_date_created)); ?></span>
			<div class="clr"></div>
		</div>                                
		<?php
		$this->cnt++;
	endforeach;
	?>
<?php endif; ?>
<div class="add-entry-div" id="add-entry-div-<?php echo $user->id; ?>"></div>
<div class="clr"></div>
<div class="add-placeholder-div" id="add-placeholder-div-<?php echo $user->id; ?>"></div>
<div class="clr"></div>
<div class="fees">
	<?php /*
	  <a href="javascript:void(0);" onclick="edit_fees(<?php echo $user->summary_id; ?>);">
	  <i class="fa fa-edit"></i> 
	  </a>
	 */ ?>
	<span>
		<?php echo JText::_('COM_TOES_TOTAL_FEES') . ' : ' . $data->show_currency_used . ' ' . $user->summary_total_fees; ?>
	</span>
	<span>
		<?php echo JText::_('COM_TOES_FEES_PAID') . ' : ' . $data->show_currency_used . ' ' . $user->summary_fees_paid; ?>
	</span>
	<span>
		<?php echo JText::_('COM_TOES_FEES_OWED') . ' : '; ?>
		<span style="font-weight:bold; color:<?php echo (($user->summary_total_fees - $user->summary_fees_paid) < 0) ? '#F00' : '#000'; ?>">
			<?php
			echo $data->show_currency_used . ' ' . ($user->summary_total_fees - $user->summary_fees_paid);
			?>
		</span>
	</span>
</div>
<div class="edit-fees-div" id="edit-fees-<?php echo $user->summary_id; ?>-div">
	<div>
		<label for="summary_benching_area_<?php echo $user->summary_id; ?>"><?php echo JText::_('COM_TOES_BENCHING_AREA_INPUT') ?></label>
		<input type="text" id="summary_benching_area_<?php echo $user->summary_id; ?>" name="summary_benching_area_<?php echo $user->summary_id; ?>" value="<?php echo $user->summary_benching_area; ?>" />
	</div>

	<div>
		<label for="summary_entry_clerk_note_<?php echo $user->summary_id; ?>"><?php echo JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_NOTE_TITLE') ?></label>
		<textarea cols="25" rows="5" id="summary_entry_clerk_note_<?php echo $user->summary_id; ?>" name="summary_entry_clerk_note_<?php echo $user->summary_id; ?>"><?php echo $user->summary_entry_clerk_note; ?></textarea>
	</div>

	<div>
		<label for="summary_entry_clerk_private_note_<?php echo $user->summary_id; ?>"><?php echo JText::_('COM_TOES_CONFIRMATION_EMAIL_ENTRY_CLERK_PRIVATE_NOTE_TITLE') ?></label>
		<textarea cols="25" rows="5" id="summary_entry_clerk_private_note_<?php echo $user->summary_id; ?>" name="summary_entry_clerk_private_note_<?php echo $user->summary_id; ?>"><?php echo $user->summary_entry_clerk_private_note; ?></textarea>
	</div>

	<div>
		<label for="summary_total_fees_<?php echo $user->summary_id; ?>"><?php echo JText::_('COM_TOES_TOTAL_FEES') ?></label>
		<input type="text" id="summary_total_fees_<?php echo $user->summary_id; ?>" name="summary_total_fees_<?php echo $user->summary_id; ?>" value="<?php echo $user->summary_total_fees; ?>" />
	</div>

	<div>
		<label for="summary_fees_paid_<?php echo $user->summary_id; ?>"><?php echo JText::_('COM_TOES_FEES_PAID') ?></label>
		<input type="text" id="summary_fees_paid_<?php echo $user->summary_id; ?>" name="summary_fees_paid_<?php echo $user->summary_id; ?>" value="<?php echo $user->summary_fees_paid; ?>" />
	</div>

	<div class="fieldbg">
		<input onclick="save_fees('<?php echo $user->summary_id; ?>');" type="button" name="button" value="<?php echo JText::_('COM_TOES_SAVE'); ?>" />
	</div>
</div>
<div class="clr"></div>
<br/>
<?php if ($data->show_status != 'Held'): ?>
	<span>
		<a href="javascript:void(0);" title="<?php echo JText::_('COM_TOES_CONFIRM_ENTRIES'); ?>" onclick="validate_confirm_entries(<?php echo $show_id; ?>,<?php echo $user->summary_user; ?>,<?php echo $user->summary_id; ?>);" >
			<i class="fa fa-envelope"></i> 
			<?php echo JText::_('COM_TOES_CONFIRM_ENTRIES'); ?>
		</a>
	</span>
	<div class="clr"></div>
	<br/>
<?php endif; ?>
<div class="clr" style="border-bottom:2px solid #000;"></div>
<style>
.doc_div{display:none;border:1px solid #420a0a;}
</style>					
