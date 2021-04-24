<?php
/**
 * @package	Joomla
 * @subpackage	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_toes');
$isMatrixPrintingHidden = $params->get('hide_matrix_printing_for_non_admins');
$isTrippleCopiesAvailable = $params->get('offer_tripple_copies_for_judge_book');

$paper_sizes = TOESHelper::getPapersizeOptions();
?>

<?php if(!$isMatrixPrintingHidden || $isAdmin):?>
	<!-- Required scripts -->
	<script type="text/javascript" src="components/com_toes/assets/qztray/js/dependencies/rsvp-3.1.0.min.js"></script>
	<script type="text/javascript" src="components/com_toes/assets/qztray/js/dependencies/sha-256.min.js"></script>
	<script type="text/javascript" src="components/com_toes/assets/qztray/js/qz-tray.js"></script>
<?php endif; ?>

<style type="text/css">
	.tab-links{
		border: 1px solid;
		padding: 2px;
		background: #ccc;
	}
</style>
<div id="toes">
	<div class ="show-details-main">
		
		<?php require __DIR__.'/header.php'; ?>
	
		<?php if($params->get('block_catalog_for_debt_clubs') && $data->club_on_toes_bad_debt_list): ?>
			<p class="error">
				<?php echo JText::_('COM_TOES_TOES_BAD_DEBT_LIST_TITLE');?>
				<br/>
				<span style="font-weight: normal; padding-left: 0px;">
					<?php echo str_replace('[club_name]', $data->club_name, JText::_('COM_TOES_TOES_BAD_DEBT_LIST_CONTENT'));?>
				</span>
			</p>	
		<?php else: ?>
			<div>
				<div>
					<div style="float:right;">
						<label for="entry_status_filter" class="lbl" style="width:auto">
							<?php echo JText::_('COM_TOES_SELECT_PAPER_SIZE'); ?> :
						</label>
						<?php echo JHTML::_('select.genericlist', $paper_sizes, 'show_paper_size', '', 'value', 'text', $data->show_paper_size); ?>
						<input type="button" value="<?php echo JText::_('SAVE'); ?>" onclick="changePapersize(<?php echo $show_id; ?>);" />			
					</div>
					<div class="title">
						<?php echo JText::_('COM_TOES_SHOW_DOCUMENTS');?>
					</div>
					<div class="clr"></div>
				</div>             

				<span id="documentation-tabs">
				<?php 
				if($this->pendingentries)
				{
					$disabled = 'disabled="disabled"';
					$a_return = 'return false;';
					$a_style = 'style="color:#666666;"';
				}
				else
				{
					$disabled = '';
					$a_return = '';
					$a_style = '';
				}

				if($params->get('show_grayed_report_options_for_admin') && $isAdmin) {
					$disabled = '';
					$a_return = '';
					$a_style = '';
				}


				?>

				<?php echo JHtml::_('bootstrap.startTabSet', 'documentation', array('active' => 'summaries')); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'documentation', 'summaries', JText::_('COM_TOES_SUMMARIES')); ?>
					<div class="seconouter">
						<div class ="block">
							<br/><br/>
							<!-- Summary -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_SUMMARY_REPORT'); ?> (
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getSummaryPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>

							<!-- Scheduling Summary -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_SCHEDULING_SUMMARY_REPORT'); ?> (
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getSchedulingSummaryPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>

							<!-- Cheat Sheet -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_CHEAT_SHEET_REPORT'); ?> (
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getEntryclerkCheatsheetPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> |
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getEntryclerkCheatsheetEXL&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_EXL'); ?>
							</a>
							)<br/>

							<!-- Absentees Report -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_ABSENTEES_REPORT'); ?> (
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getAbsenteesPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> |
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getAbsenteesEXL&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_EXL'); ?>
							</a>
							)<br/>

							<!-- Check-in Sheet Report -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_CHECKIN_SHEET'); ?> (
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getCheckinSheetPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>

							<!-- Space Summary -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_SPACE_SUMMARY_REPORT'); ?> (
							<a target="_blank" href="index.php?option=com_toes&task=entryclerk.getSpaceSummaryPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>
							<br/>
						</div>
					</div> 
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'documentation', 'catalogs-exhibitors', JText::_('COM_TOES_CATALOGS_EXHIBITORS')); ?>
					<div class ="seconouter">
						<div class ="block">
							<br/><br/>
							<?php if($this->pendingentries) :?>
							<span style="float:right;width: 300px;">
								<?php echo JText::_('COM_TOES_PENDING_ENTRIES_WARNING'); ?>
							</span>
							<?php endif;?>
							<?php 
								if($this->show->show_lock_catalog)
								{
									$catalog_locked = ' disabled="disabled"';
									$catalog_style = ' style="color:#666666;"';
									$catalog_return = 'return false;';
								}
								else
								{
									$catalog_locked = '';
									$catalog_style = '';
									$catalog_return = '';
								}
							?>
							<div style="<?php echo ($data->show_status == 'Held')?"display:none;":""; ?>">
								<a <?php echo $catalog_style;?> id="run_catalog" href="javascript:void(0);" rel="<?php echo $show_id . ';' . $data->catalog_runs; ?>" onclick="<?php echo $catalog_return;?>run_catalog();" >
									<i class="far fa-file-alt"></i> 
									<?php echo JText::_('COM_TOES_RUN_CATALOG'); ?>
								</a>
								<?php if($isAdmin || !$catalog_locked):?>
									<span style="padding-left:100px;">
										<input type="checkbox" id="lock-catalog" rel="<?php echo $show_id; ?>" <?php echo ($this->show->show_lock_catalog)?'checked="checked"':'';?> />
										<?php echo JText::_('COM_TOES_LOCK_CATALOG'); ?>
									</span>
								<?php endif; ?>
								<br/>
							</div>

							<!-- Catalog -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_CATALOG_REPORT'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getCatalogPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>
							<!-- Exhibitor -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_EXHIBITOR_REPORT'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getExibitorListPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> 
							)<br/>

							<!-- Exhibitor Cards -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_EXHIBITOR_CARDS'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getExhibitorCardsPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> 
							)<br/>

							<!-- Exhibitor Information -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_EXHIBITOR_INFORMATION_REPORT'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getExhibitorInfoEXL&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_EXL'); ?>
							</a> 
							)<br/>

							<!-- Exhibitor Labels -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_EXHIBITOR_LABELS'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getExhibitorLabelsEXL&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_EXL'); ?>
							</a> 
							)<br/>

							<!-- Exhibitor Labels 2 -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_EXHIBITOR_LABEL_DETAILS'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getExhibitorLabelDetailsEXL&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_EXL'); ?>
							</a> 
							)<br/>

							<!-- Microchip List -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_MICROCHIP_REPORT'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getMicrochipListPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>

							<br/>
						</div>
					</div> 
					<?php echo JHtml::_('bootstrap.endTab'); ?>

					<?php echo JHtml::_('bootstrap.addTab', 'documentation', 'late-pages-master-catalog', JText::_('COM_TOES_LATE_PAGES_MASTER_CATALOG')); ?>
					<div class ="seconouter">
						<div class="block">
							<br/><br/>
							<?php if($this->pendingentries) :?>
							<span style="float:right;width: 300px;">
								<?php echo JText::_('COM_TOES_PENDING_ENTRIES_WARNING'); ?>
							</span>
							<?php endif;?>
							<?php
								if($this->show->show_lock_late_pages)
								{
									$late_pages_locked = 'disabled';
									$late_pages_style = ' style="color:#666666;"';
									$late_pages_return = 'return false;';
								}
								else
								{
									$late_pages_locked = '';
									$late_pages_style = '';
									$late_pages_return = '';
								}
							?>
							<div style="<?php echo ($data->show_status == 'Held')?"display:none;":""; ?>">
								
								<a <?php echo $late_pages_style; ?> <?php echo $a_style; ?> id="late_pages" href="javascript:void(0);" rel="<?php echo $show_id; ?>" onclick="<?php echo $late_pages_return.$a_return; ?>late_pages();" >
								<?php /*
								<a <?php echo $late_pages_style; ?> <?php echo $a_style; ?> id="late_pages" rel="<?php echo $show_id; ?>" href="<?php echo JURI::root()?>index.php?option=com_toes&task=entryclerk.getLatePagesPDF&show_id=<?php echo $show_id?>" target="_blank">
								*/ ?>
									<i class="far fa-file-alt"></i> 
									<?php echo JText::_('COM_TOES_LATE_PAGES'); ?>
								</a>
								<?php if($isAdmin || !$late_pages_locked) : ?>
									<span style="padding-left:100px;">
										<input type="checkbox" id="lock-late-pages" rel="<?php echo $show_id; ?>" <?php echo ($this->show->show_lock_late_pages)?'checked="checked"':'';?> />
										<?php echo JText::_('COM_TOES_LOCK_LATE_PAGES'); ?>
									</span>
								<?php endif; ?>
								<br/>
							</div>

							<!-- Late Pages -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_LATE_PAGES_REPORT'); ?> ( 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getLatePagesPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> 							
							)<br/>

							<!-- Late Exhibitor -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_LATE_EXHIBITOR_REPORT'); ?> ( 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getLateExibitorListPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> 
							)<br/>

							<!-- Master Catalog -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_MASTER_CATALOG_REPORT'); ?> ( 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getMasterCatalogPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> | 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getMasterCatalogEXL&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_EXL'); ?>
							</a>
							)<br/>

							<?php // 
							// spiderweb commented 
						//	var_dump($data);
							if($data->show_uses_ticapp == 1)
							{
							?>

							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_MARKED_CATALOG_REPORT'); ?> ( 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getMarkedCatalogPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> 
							)<br/>

							<?php } ?>
							
							

							<!-- Master Exhibitor -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_MASTER_EXHIBITOR_REPORT'); ?> ( 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getMasterExibitorListPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>

							<!-- Master Exhibitor WOA -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_MASTER_EXHIBITOR_WOA_REPORT'); ?> ( 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getMasterExibitorWOAListPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/> 
							<br/>
						</div>
					</div> 
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'documentation', 'judges', JText::_('COM_TOES_JUDGES')); ?>
					<div class ="seconouter">
						<div class ="block">
							<?php if($this->pendingentries) :?>
							<br/><br/>
							<span style="float:right;width: 300px;">
								<?php echo JText::_('COM_TOES_PENDING_ENTRIES_WARNING'); ?>
							</span>
							<?php endif;?>
							<?php
							$judges = TOESHelper::getJudges($show_id);

							$congress_judges = TOESHelper::getCongressJudges($show_id);
							
							$prev_day = 0;
							$prev_judge = '';
							?>
							<form target="_blank" method="post" action="index.php" name="judges_form" >
								<h4>
									<label style="font-weight: normal;">
										<input <?php echo $disabled;?> type="radio" value="1" name="ring_format_option" <?php echo ($disabled?'':'checked="checked"');?> />
										<?php echo JText::_('COM_TOES_SHOW_RINGS'); ?>
									</label>
								</h4>
								<table style="width:100%;">
									<tr>
										<td>&nbsp;
											<?php foreach ($judges as $judge): ?>
												<?php if ($prev_day != $judge->show_day_id): ?>
													<?php if ($prev_judge) : ?>
														<br/>
														<label style="font-weight: normal;">
															<?php /*
															<input <?php echo $disabled;?> class="judge_ids rings_<?php echo $prev_day;?>" type="checkbox" name="judge_id[]" value="<?php echo 'AB'; ?>" /> 
															*/ ?> 
															<input <?php echo $disabled;?> class="judge_ids rings_<?php echo $prev_day;?>" type="checkbox" name="judge_id[]" value="<?php echo 'AB'.'_'.str_replace('rings_','',$prev_day); ?>" /> 
															<?php echo JText::_('COM_TOES_EXTRA_JUDGE_BOOK') . ' (AB) '; ?> 
														</label> <br/>
														<label style="font-weight: normal;">
															<?php /*
															<input <?php echo $disabled;?> class="judge_ids rings_<?php echo $prev_day;?>" type="checkbox" name="judge_id[]" value="<?php echo 'SP'; ?>" />
															*/ ?> 
															<input <?php echo $disabled;?> class="judge_ids rings_<?php echo $prev_day;?>" type="checkbox" name="judge_id[]" value="<?php echo 'SP'.'_'.str_replace('rings_','',$prev_day); ?>" />
															<?php echo JText::_('COM_TOES_EXTRA_JUDGE_BOOK') . ' (SP) '; ?>
														</label> <br/>
													<?php endif; ?>
												
													</td>
													<td style="vertical-align: top;">

													<?php
													if ($data->show_format != 'Continuous')
														echo '<div style="font-weight:bold">'.'<label ><input '.$disabled.' class="judge_ids" rel="rings_'.$judge->show_day_id.'" type="checkbox" onclick="select_ring_judges(this);" /> '. date('l', strtotime($judge->show_day_date)) . '</label> </div>';
													else
														echo '<div style="font-weight:bold"> <label ><input '.$disabled.' class="judge_ids" rel="rings_'.$judge->show_day_id.'" type="checkbox" onclick="select_ring_judges(this);"/> '.JText::_('COM_TOES_SELECT_ALL').'</label> </div>';
													?>
													<br/>
													<?php $prev_day = $judge->show_day_id; ?>
													<?php $prev_judge = $judge; ?>
												<?php endif; ?>
												
												<label style="font-weight: normal;">
													<input <?php echo $disabled;?> class="judge_ids rings_<?php echo $judge->show_day_id;?>" type="checkbox" name="judge_id[]" value="<?php echo $judge->ring_id; ?>" />
													<?php echo JText::_('COM_TOES_RING').' '.$judge->ring_number; ?>
													<?php echo ' - '.$judge->name; ?>
													<?php echo ' - '; ?>
													<?php
													switch ($judge->ring_format) {
														case 'Allbreed':
															echo 'AB';
															break;
														case 'Specialty':
															echo 'SP';
															break;
													}
													?>
												</label>
												<br/>
											<?php endforeach; ?>
											<br/>
											<?php
												if(count($judges) == 0)
												echo '</td><td style="vertical-align: top;"><div style="font-weight:bold"><label style="font-weight: normal;"><input '.$disabled.' rel="rings_'.$prev_day.'" type="checkbox" onclick="select_ring_judges(this);" /> '.JText::_('COM_TOES_SELECT_ALL').'</label> </div><br/>';
											?>
											<label style="font-weight: normal;">
												<input <?php echo $disabled;?> class="judge_ids rings_<?php echo $prev_day;?>" type="checkbox" name="judge_id[]" value="<?php echo 'AB'.'_'.str_replace('rings_','',$prev_day); ?>" /> 
												<?php echo JText::_('COM_TOES_EXTRA_JUDGE_BOOK') . ' - AB '; ?> 
											</label> <br/>
											<label style="font-weight: normal;">
												<input <?php echo $disabled;?> class="judge_ids rings_<?php echo $prev_day;?>" type="checkbox" name="judge_id[]" value="<?php echo 'SP'.'_'.str_replace('rings_','',$prev_day); ?>" />
												<?php echo JText::_('COM_TOES_EXTRA_JUDGE_BOOK') . ' - SP '; ?> 
											</label> <br/>
										</td>
									</tr>
								</table>
								<br/>
								
								<?php if ($congress_judges): ?>
									<h4>
										<label style="font-weight: normal;">
											<input <?php echo $disabled;?> type="radio" value="2" name="ring_format_option" />
											<?php echo JText::_('COM_TOES_SHOW_CONGRESS_RINGS'); ?>
										</label>
									</h4>
									<table style="width:100%;">
										<tr>
											<td>&nbsp;
												<?php 
												$prev_day = 0;
												foreach ($congress_judges as $judge): ?>
													<?php if ($prev_day != $judge->show_day_id): ?>
														</td>
														<td style="vertical-align: top;">
														<?php
														if ($data->show_format != 'Continuous')
															echo '<div style="font-weight:bold">' .'<label ><input disabled="disabled" class="congress_judge_ids" rel="congress_rings_'.$judge->show_day_id.'" type="checkbox" onclick="select_ring_judges(this);"/> '. date('l', strtotime($judge->show_day_date)) . '</label> </div>';
														else
															echo '<div style="font-weight:bold"><label ><input disabled="disabled" class="congress_judge_ids" rel="congress_rings_'.$judge->show_day_id.'" type="checkbox" onclick="select_ring_judges(this);"/> '.JText::_('COM_TOES_SELECT_ALL').'</label> </div>';
														?>
														<br/>
														<?php $prev_day = $judge->show_day_id; ?>
														<?php $prev_judge = $judge; ?>
													<?php endif; ?>       
													<label style="font-weight: normal;">
														<input disabled="disabled" class="congress_judge_ids congress_rings_<?php echo $judge->show_day_id;?>" type="checkbox" name="congress_judge_id[]" value="<?php echo $judge->ring_id; ?>" />
														<?php echo $judge->ring_name; ?>
													</label>
													<br/>
												<?php endforeach; ?>
												<br/>
											</td>
										</tr>
									</table>
								<?php endif; ?>
								<div>
									<i class="far fa-file-alt"></i> 
									<?php echo JText::_('COM_TOES_GENERATE_JUDGES_BOOK_COVER_PAGES'); ?> (
									<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>getJudgesBook(<?php echo $show_id; ?>,1);" >
										<?php echo JText::_('COM_TOES_PDF');?>
									</a> )
									<br/>

									<i class="far fa-file-alt"></i> 
									<?php echo JText::_('COM_TOES_GENERATE_JUDGES_BOOK_COVER_PAGES_IN_A4'); ?> (
									<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>getJudgesBookInA4(<?php echo $show_id; ?>,1);" >
										<?php echo JText::_('COM_TOES_PDF');?>
									</a> )
									<br/><br/>

									<a class="tab-links" href="javascript:void(0);" id="judge-laser-printing-heading">
										<?php echo JText::_('COM_TOES_LASER_PRINTING'); ?>
									</a>
									<div id="judge-laser-printing" style="padding:15px;border: 1px solid; width: 90%;">
										<i class="far fa-file-alt"></i> 
										<?php echo JText::_('COM_TOES_GENERATE_JUDGES_BOOK'); ?> (
										<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>getJudgesBook(<?php echo $show_id; ?>,0);" >
											<?php echo JText::_('COM_TOES_PDF');?>
										</a> )
										<br/>
										<i class="far fa-file-alt"></i> 
										<?php echo JText::_('COM_TOES_GENERATE_JUDGES_BOOK_IN_A4'); ?> (
										<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>getJudgesBookInA4(<?php echo $show_id; ?>);" >
											<?php echo JText::_('COM_TOES_PDF');?>
										</a> )
									</div>
									<div class="clr"></div>
									<br/>
									
									<!-- Blank Pages -->
									<i class="far fa-file-alt"></i> 
									<?php echo JText::_('COM_TOES_BLANK_JUDGE_BOOK'); ?> (
									<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getBlankJudgesBookPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
										<?php echo JText::_('COM_TOES_PDF'); ?>
									</a> )
									<br/>
									<i class="far fa-file-alt"></i> 
									<?php echo JText::_('COM_TOES_BLANK_JUDGE_BOOK_FINAL_SHEETS'); ?> (
									<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getBlankJudgesBookFinalSheet&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
										<?php echo JText::_('COM_TOES_PDF'); ?>
									</a> )
									<br/>
									<br/>

									<?php if( !$isMatrixPrintingHidden || $isAdmin):?>
										<br/>
										<a class="tab-links" href="javascript:void(0);" id="judge-laser-printing-heading">
											<?php echo JText::_('COM_TOES_MATRIX_PRINTING'); ?>
										</a>
										<div id="judge-laser-printing" style="padding:15px;border: 1px solid; width: 90%;">
											<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>getPreJudgesBook(<?php echo $show_id; ?>,0);" >
												<i class="far fa-file-alt"></i> 
												<?php echo JText::_('COM_TOES_PRINT_MATRIX_PRINTING'); ?>
											</a><br/>
											<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>getPreJudgesBook(<?php echo $show_id; ?>,1);" >
												<i class="far fa-file-alt"></i> 
												<?php echo JText::_('COM_TOES_GENERATE_MATRIX_PRINTING_IN_FILE'); ?>
											</a><br/>
										</div>
										<div class="clr"></div>
									<?php endif; ?>
								</div>
							</form>
							<div class="clr"></div>
						</div>
					</div>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
					<?php echo JHtml::_('bootstrap.addTab', 'documentation', 'finance-reporting', JText::_('COM_TOES_FINANCE_REPORTING')); ?>
					<div class ="seconouter">
						<div class ="block">
							<br/>

							<!-- Treasurer -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_TREASURER_REPORT'); ?> ( 
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getTreasurerPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a> |
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getTreasurerEXL&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_EXL'); ?>
							</a>
							)<br/>

							<!-- Benching -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_BENCHING_REPORT'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getBenchingPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>

							<!-- Benching Cards -->
							<i class="far fa-file-alt"></i> 
							<?php echo JText::_('COM_TOES_BENCHING_CARDS_REPORT'); ?> (
							<a <?php echo $a_style; ?> target="_blank" href="index.php?option=com_toes&task=entryclerk.getBenchingCardsPDF&show_id=<?php echo $show_id.'&'.JSession::getFormToken() .'=1'?>" >
								<?php echo JText::_('COM_TOES_PDF'); ?>
							</a>
							)<br/>
							<br/>
						</div>
					</div>
					<?php echo JHtml::_('bootstrap.endTab'); ?>
				<?php echo JHtml::_('bootstrap.endTabSet'); ?>
				</span>
			</div>
		<?php endif; ?>

		<div class="clr"></div>
	</div>
</div>
<script type="text/javascript">

    var myWidth;
    var myHeight;
    
    
    var printer_set = false; // sandy added

    if( typeof( window.innerWidth ) == 'number' ) { 
        //Non-IE 
        myWidth = window.innerWidth;
        myHeight = window.innerHeight; 
    } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) { 
        //IE 6+ in 'standards compliant mode' 
        myWidth = document.documentElement.clientWidth; 
        myHeight = document.documentElement.clientHeight; 
    } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) { 
        //IE 4 compatible 
        myWidth = document.body.clientWidth; 
        myHeight = document.body.clientHeight; 
    }            
    
    function select_ring_judges(ele)
    {
        var cls = jQuery(ele).attr('rel'); 
        
        if(ele.checked)
            jQuery('.'+cls).prop('checked',1);
        else
            jQuery('.'+cls).prop('checked',0);
    }

	<?php if(!$isMatrixPrintingHidden || $isAdmin):?>

    /// Authentication setup ///
    qz.security.setCertificatePromise(function(resolve, reject) {
        //Preferred method - from server
		//$.ajax("assets/signing/public-key.txt").then(resolve, reject);

        //Alternate method 1 - anonymous
		//resolve();

        //Alternate method 2 - direct
        resolve("-----BEGIN CERTIFICATE-----\n" +
                "MIIFAzCCAuugAwIBAgICEAIwDQYJKoZIhvcNAQEFBQAwgZgxCzAJBgNVBAYTAlVT\n" +
                "MQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0cmllcywgTExDMRswGQYD\n" +
                "VQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMMEHF6aW5kdXN0cmllcy5j\n" +
                "b20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1c3RyaWVzLmNvbTAeFw0x\n" +
                "NTAzMTkwMjM4NDVaFw0yNTAzMTkwMjM4NDVaMHMxCzAJBgNVBAYTAkFBMRMwEQYD\n" +
                "VQQIDApTb21lIFN0YXRlMQ0wCwYDVQQKDAREZW1vMQ0wCwYDVQQLDAREZW1vMRIw\n" +
                "EAYDVQQDDAlsb2NhbGhvc3QxHTAbBgkqhkiG9w0BCQEWDnJvb3RAbG9jYWxob3N0\n" +
                "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtFzbBDRTDHHmlSVQLqjY\n" +
                "aoGax7ql3XgRGdhZlNEJPZDs5482ty34J4sI2ZK2yC8YkZ/x+WCSveUgDQIVJ8oK\n" +
                "D4jtAPxqHnfSr9RAbvB1GQoiYLxhfxEp/+zfB9dBKDTRZR2nJm/mMsavY2DnSzLp\n" +
                "t7PJOjt3BdtISRtGMRsWmRHRfy882msBxsYug22odnT1OdaJQ54bWJT5iJnceBV2\n" +
                "1oOqWSg5hU1MupZRxxHbzI61EpTLlxXJQ7YNSwwiDzjaxGrufxc4eZnzGQ1A8h1u\n" +
                "jTaG84S1MWvG7BfcPLW+sya+PkrQWMOCIgXrQnAsUgqQrgxQ8Ocq3G4X9UvBy5VR\n" +
                "CwIDAQABo3sweTAJBgNVHRMEAjAAMCwGCWCGSAGG+EIBDQQfFh1PcGVuU1NMIEdl\n" +
                "bmVyYXRlZCBDZXJ0aWZpY2F0ZTAdBgNVHQ4EFgQUpG420UhvfwAFMr+8vf3pJunQ\n" +
                "gH4wHwYDVR0jBBgwFoAUkKZQt4TUuepf8gWEE3hF6Kl1VFwwDQYJKoZIhvcNAQEF\n" +
                "BQADggIBAFXr6G1g7yYVHg6uGfh1nK2jhpKBAOA+OtZQLNHYlBgoAuRRNWdE9/v4\n" +
                "J/3Jeid2DAyihm2j92qsQJXkyxBgdTLG+ncILlRElXvG7IrOh3tq/TttdzLcMjaR\n" +
                "8w/AkVDLNL0z35shNXih2F9JlbNRGqbVhC7qZl+V1BITfx6mGc4ayke7C9Hm57X0\n" +
                "ak/NerAC/QXNs/bF17b+zsUt2ja5NVS8dDSC4JAkM1dD64Y26leYbPybB+FgOxFu\n" +
                "wou9gFxzwbdGLCGboi0lNLjEysHJBi90KjPUETbzMmoilHNJXw7egIo8yS5eq8RH\n" +
                "i2lS0GsQjYFMvplNVMATDXUPm9MKpCbZ7IlJ5eekhWqvErddcHbzCuUBkDZ7wX/j\n" +
                "unk/3DyXdTsSGuZk3/fLEsc4/YTujpAjVXiA1LCooQJ7SmNOpUa66TPz9O7Ufkng\n" +
                "+CoTSACmnlHdP7U9WLr5TYnmL9eoHwtb0hwENe1oFC5zClJoSX/7DRexSJfB7YBf\n" +
                "vn6JA2xy4C6PqximyCPisErNp85GUcZfo33Np1aywFv9H+a83rSUcV6kpE/jAZio\n" +
                "5qLpgIOisArj1HTM6goDWzKhLiR/AeG3IJvgbpr9Gr7uZmfFyQzUjvkJ9cybZRd+\n" +
                "G8azmpBBotmKsbtbAU/I/LVk8saeXznshOVVpDRYtVnjZeAneso7\n" +
                "-----END CERTIFICATE-----\n" +
                "--START INTERMEDIATE CERT--\n" +
                "-----BEGIN CERTIFICATE-----\n" +
                "MIIFEjCCA/qgAwIBAgICEAAwDQYJKoZIhvcNAQELBQAwgawxCzAJBgNVBAYTAlVT\n" +
                "MQswCQYDVQQIDAJOWTESMBAGA1UEBwwJQ2FuYXN0b3RhMRswGQYDVQQKDBJRWiBJ\n" +
                "bmR1c3RyaWVzLCBMTEMxGzAZBgNVBAsMElFaIEluZHVzdHJpZXMsIExMQzEZMBcG\n" +
                "A1UEAwwQcXppbmR1c3RyaWVzLmNvbTEnMCUGCSqGSIb3DQEJARYYc3VwcG9ydEBx\n" +
                "emluZHVzdHJpZXMuY29tMB4XDTE1MDMwMjAwNTAxOFoXDTM1MDMwMjAwNTAxOFow\n" +
                "gZgxCzAJBgNVBAYTAlVTMQswCQYDVQQIDAJOWTEbMBkGA1UECgwSUVogSW5kdXN0\n" +
                "cmllcywgTExDMRswGQYDVQQLDBJRWiBJbmR1c3RyaWVzLCBMTEMxGTAXBgNVBAMM\n" +
                "EHF6aW5kdXN0cmllcy5jb20xJzAlBgkqhkiG9w0BCQEWGHN1cHBvcnRAcXppbmR1\n" +
                "c3RyaWVzLmNvbTCCAiIwDQYJKoZIhvcNAQEBBQADggIPADCCAgoCggIBANTDgNLU\n" +
                "iohl/rQoZ2bTMHVEk1mA020LYhgfWjO0+GsLlbg5SvWVFWkv4ZgffuVRXLHrwz1H\n" +
                "YpMyo+Zh8ksJF9ssJWCwQGO5ciM6dmoryyB0VZHGY1blewdMuxieXP7Kr6XD3GRM\n" +
                "GAhEwTxjUzI3ksuRunX4IcnRXKYkg5pjs4nLEhXtIZWDLiXPUsyUAEq1U1qdL1AH\n" +
                "EtdK/L3zLATnhPB6ZiM+HzNG4aAPynSA38fpeeZ4R0tINMpFThwNgGUsxYKsP9kh\n" +
                "0gxGl8YHL6ZzC7BC8FXIB/0Wteng0+XLAVto56Pyxt7BdxtNVuVNNXgkCi9tMqVX\n" +
                "xOk3oIvODDt0UoQUZ/umUuoMuOLekYUpZVk4utCqXXlB4mVfS5/zWB6nVxFX8Io1\n" +
                "9FOiDLTwZVtBmzmeikzb6o1QLp9F2TAvlf8+DIGDOo0DpPQUtOUyLPCh5hBaDGFE\n" +
                "ZhE56qPCBiQIc4T2klWX/80C5NZnd/tJNxjyUyk7bjdDzhzT10CGRAsqxAnsjvMD\n" +
                "2KcMf3oXN4PNgyfpbfq2ipxJ1u777Gpbzyf0xoKwH9FYigmqfRH2N2pEdiYawKrX\n" +
                "6pyXzGM4cvQ5X1Yxf2x/+xdTLdVaLnZgwrdqwFYmDejGAldXlYDl3jbBHVM1v+uY\n" +
                "5ItGTjk+3vLrxmvGy5XFVG+8fF/xaVfo5TW5AgMBAAGjUDBOMB0GA1UdDgQWBBSQ\n" +
                "plC3hNS56l/yBYQTeEXoqXVUXDAfBgNVHSMEGDAWgBQDRcZNwPqOqQvagw9BpW0S\n" +
                "BkOpXjAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBCwUAA4IBAQAJIO8SiNr9jpLQ\n" +
                "eUsFUmbueoxyI5L+P5eV92ceVOJ2tAlBA13vzF1NWlpSlrMmQcVUE/K4D01qtr0k\n" +
                "gDs6LUHvj2XXLpyEogitbBgipkQpwCTJVfC9bWYBwEotC7Y8mVjjEV7uXAT71GKT\n" +
                "x8XlB9maf+BTZGgyoulA5pTYJ++7s/xX9gzSWCa+eXGcjguBtYYXaAjjAqFGRAvu\n" +
                "pz1yrDWcA6H94HeErJKUXBakS0Jm/V33JDuVXY+aZ8EQi2kV82aZbNdXll/R6iGw\n" +
                "2ur4rDErnHsiphBgZB71C5FD4cdfSONTsYxmPmyUb5T+KLUouxZ9B0Wh28ucc1Lp\n" +
                "rbO7BnjW\n" +
                "-----END CERTIFICATE-----\n");
    });

    qz.security.setSignaturePromise(function(toSign) {
        return function(resolve, reject) {
            //Preferred method - from server
			//$.ajax("/secure/url/for/sign-message?request=" + toSign).then(resolve, reject);

            //Alternate method - unsigned
            resolve();
        };
    });

    /// Connection ///
    function launchQZ() {
        if (!qz.websocket.isActive()) {
            window.location.assign("qz:launch");
            //Retry 5 times, pausing 1 second between each attempt
            startConnection({ retries: 5, delay: 1 });
        }
    }

    function startConnection(config) {
        if (!qz.websocket.isActive()) {
            updateState('Waiting', 'default');

            qz.websocket.connect(config).then(function() {
                updateState('Active', 'success');
                findDefaultPrinter(true);
            }).catch(handleConnectionError);
        } else {
            displayMessage('An active connection with QZ already exists.', 'alert-warning');
        }
    }

    function endConnection() {
        if (qz.websocket.isActive()) {
            qz.websocket.disconnect().then(function() {
                updateState('Inactive', 'default');
            }).catch(handleConnectionError);
        } else {
            displayMessage('No active connection with QZ exists.', 'alert-warning');
        }
    }

    function handleConnectionError(err) {
        updateState('Error', 'danger');

        if (err.target != undefined) {
            if (err.target.readyState >= 2) { //if CLOSING or CLOSED
                displayError("Connection to QZ Tray was closed");
            } else {
                displayError("A connection error occurred, check log for details");
                console.error(err);
            }
        } else {
            displayError(err);
        }
    }

	function updateState(text, css) {
    	console.log(text);
    }    

    function displayError(err) {
        console.error(err);
    }
	
    /// Detection ///
    function findPrinter(query, set) {
        jQuery('#printerSearch').val(query);
        qz.printers.find(query).then(function(data) {
            console.log("<strong>Found:</strong> " + data);
            if (set) { setPrinter(data); }
        }).catch(displayError);
    }

    function findDefaultPrinter(set) {
        qz.printers.getDefault().then(function(data) {
            console.log("<strong>Found:</strong> " + data);
            if (set) { setPrinter(data); }
            var printer_set = true; // sandy added
        }).catch(displayError);
    }

    function findPrinters() {
        qz.printers.find().then(function(data) {
            var list = '';
            for(var i = 0; i < data.length; i++) {
                list += "&nbsp; " + data[i] + "<br/>";
            }

            console.log("<strong>Available printers:</strong><br/>" + list);
        }).catch(displayError);
    }

    function displayError(err) {
        console.error(err);
    }	
    /// QZ Config ///
    var cfg = null;
    function getUpdatedConfig() {
        if (cfg == null) {
            cfg = qz.configs.create(null);
        }

        updateConfig();
        return cfg
    }

    function updateConfig() {

        cfg.reconfigure({
                            altPrinting: false,
                            encoding: '',
                            endOfDoc: '',
                            perSpool: 1,

                            colorType: 'blackwhite',
                            copies: 1,
                            jobName: 'Judge Book',
                            density: 0,
                            duplex: false,
                            margins: 15,
                            printerTray: '',
                            rotation: 0,
                            scaleContent: true,
                            size: null,
                            units: 'in'
                        });
    }


    function setPrinter(printer) {
        var cf = getUpdatedConfig();
        cf.setPrinter(printer);
        console.log("setting printer "+printer);
    }
	
    <?php endif; ?>

    function confirm_sync(show_id)
    {
		new jBox('Confirm',{
	        content: "<?php echo JText::_('COM_TOES_CONFIRM_SYNC_SHOW_DATA'); ?>",
	        width: '500px',
	        cancelButton : "<?php echo JText::_('JNO'); ?>",
	        confirmButton: "<?php echo JText::_('JYES'); ?>",
	        confirm: function() {
		        jQuery.ajax({
		            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.sync_db&action=show&show_id='+show_id,
		            type: 'post',
		        }).done(function(responseText){
					responseText = responseText.trim();
		        	jQuery('#loader').hide();
		            if(responseText == 1)
		                location.reload();
		            else
		                jbox_alert(responseText);
		        });
		
		        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
				jQuery('#progress-box').hide();
				jQuery('#progress-log-text').hide();
		        jQuery('#loader').show();
		    }
	    }).open(); 
    }

    jQuery(document).ready(function(){
		if(jQuery('#lock-late-pages').length)
		{
			jQuery('#lock-late-pages').on('click', function(){
				var show_id = jQuery(this).attr('rel');
				if(this.checked)
				{
					jQuery.ajax({
						url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.validateLatePagesLocking&show_id='+show_id,
						type: 'post',
					}).done(function(responseText){
						responseText = responseText.trim();
						if(responseText == 1 || responseText == 2) {
							var confirmation_content = '';
							if(responseText == 2) {
								confirmation_content += "<?php echo JText::_('COM_TOES_CONFIRM_TO_CLOSE_LATE_PAGES_WARNING'); ?>"; 	 
							} else {
								confirmation_content += " <?php echo JText::_('COM_TOES_CONFIRM_TO_CLOSE_LATE_PAGES'); ?>"; 
							}

							new jBox('Confirm',{
								content: confirmation_content,
								width: '400px',
								cancelButton : NO_BUTTON,
								confirmButton: YES_BUTTON,
								cancel: function() {
									jQuery('#lock-late-pages').prop('checked',false);
								},
								confirm: function() {
									jQuery.ajax({
										url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.lockLatepages&show_id='+show_id,
										type: 'post',
									}).done(function(responseText){
										responseText = responseText.trim();
										if(responseText == 1) {
											location.reload();
										} else {
											jbox_alert(responseText);
											jQuery('#lock-late-pages').prop('checked',false);
										}
									}).fail(function(){
										jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
										jQuery('#lock-late-pages').prop('checked',false);
									});
								}
							}).open();

						} else {
							jbox_alert(responseText);
							jQuery('#lock-late-pages').prop('checked',false);
						}
					}).fail(function(){
						jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
						jQuery('#lock-late-pages').prop('checked',false);
					});
				}
				else
				{
					new jBox('Confirm',{
				        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_OPEN_LATE_PAGES'); ?>",
				        width: '400px',
				        cancelButton : NO_BUTTON,
				        confirmButton: YES_BUTTON,
				        cancel: function() {
							jQuery('#lock-late-pages').prop('checked',true);
						},
				        confirm: function() {
							jQuery.ajax({
								url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.unlockLatepages&show_id='+show_id,
								type: 'post',
							}).done(function(responseText){
								responseText = responseText.trim();
								if(responseText == 1) {
									location.reload();
								} else {
									jbox_alert(responseText);
									jQuery('#lock-late-pages').prop('checked',true);
								}
							}).fail(function(){
								jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
								jQuery('#lock-late-pages').prop('checked',true);
							});
						}
					}).open();
				}
			});
		}

		if(jQuery('#lock-catalog').length)
		{
			jQuery('#lock-catalog').on('click', function(){
				var show_id = jQuery(this).attr('rel');
				if(this.checked)
				{
					jQuery.ajax({
						url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.validateCatalogLocking&show_id='+show_id,
						type: 'post',
					}).done(function(responseText){
						responseText = responseText.trim();
						if(responseText == 1 || responseText == 2) {
							var confirmation_content = '';
							if(responseText == 2) {
								confirmation_content += "<?php echo JText::_('COM_TOES_CONFIRM_TO_CLOSE_CATALOG_WARNING'); ?>"; 	 
							} else {
								confirmation_content += " <?php echo JText::_('COM_TOES_CONFIRM_TO_CLOSE_CATALOG'); ?>"; 
							}

							new jBox('Confirm',{
								content: confirmation_content,
								width: '400px',
								cancelButton : NO_BUTTON,
								confirmButton: YES_BUTTON,
								cancel: function() {
									jQuery('#lock-catalog').prop('checked',false);
								},
								confirm: function() {
									jQuery.ajax({
										url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.lockCatalog&show_id='+show_id,
										type: 'post',
									}).done(function(responseText){
										responseText = responseText.trim();
										if(responseText == 1) {
											location.reload();
										} else {
											jbox_alert(responseText);
											jQuery('#lock-catalog').prop('checked',false);
										}
									}).fail(function(){
										jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
										jQuery('#lock-catalog').prop('checked',false);
									});
								}
							}).open();
						} else {
							jbox_alert(responseText);
							jQuery('#lock-catalog').prop('checked',false);
						}
					}).fail(function(){
						jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
						jQuery('#lock-catalog').prop('checked',false);
					});
				}
				else
				{
					new jBox('Confirm',{
				        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_OPEN_CATALOG'); ?>",
				        width: '400px',
				        cancelButton : NO_BUTTON,
				        confirmButton: YES_BUTTON,
				        cancel: function() {
							jQuery('#lock-catalog').prop('checked',true);
						},
				        confirm: function() {
							jQuery.ajax({
								url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.unlockCatalog&show_id='+show_id,
								type: 'post',
							}).done(function(responseText){
								responseText = responseText.trim();
								if(responseText == 1) {
									location.reload();
								} else {
									jbox_alert(responseText);
									jQuery('#lock-catalog').prop('checked',true);
								}
							}).fail(function(){
								jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
								jQuery('#lock-catalog').prop('checked',true);
							});
						}
					}).open();
				}
			});
		}
        
        if(jQuery('#close_show').length)
        {
            jQuery('#close_show').on('click',function(){
                var rel = jQuery(this).attr('rel');

				new jBox('Confirm',{
			        content: "<?php echo JText::_('COM_TOES_CONFIRM_TO_CLOSE_SHOW'); ?>",
			        width: '400px',
			        cancelButton : NO_BUTTON,
			        confirmButton: YES_BUTTON,
			        confirm: function() {
                        jQuery.ajax({
                            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.updateStatus&status=Closed&id='+rel,
                            type: 'post',
                        }).done(function(responseText){
							responseText = responseText.trim();
                            if(responseText == 1)
                            {
                                jQuery.ajax({
                                    url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.getEntriesNeedsConfirmation&id='+rel,
                                    type: 'post',
                                }).done(function(responseText){
									responseText = responseText.trim();
                                    if(responseText == 1)
                                    {
                                        jbox_alert("<?php echo JText::_('COM_TOES_NEED_TO_CONFIRM_ENTRIES_WARNING'); ?>");
                                    }
                                    setInterval(function(){location.reload()},2000);
                                }).fail(function(){
                                    jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                                });
                            }
                            else
                                jbox_alert(responseText);
                        }).fail(function(){
                            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                        });
                    }
                }).open();    
            });
        }

        if(jQuery('#open_show').length)
        {
            jQuery('#open_show').on('click',function(){
                var rel = jQuery(this).attr('rel');

                jQuery.ajax({
                    url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=shows.updateStatus&status=Open&id='+rel,
                    type: 'post',
                }).done(function(responseText){
					responseText = responseText.trim();
                    if(responseText == 1)
                        location.reload();
                    else
                        jbox_alert(responseText);
                }).fail(function(){
                    jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
                });
            });
        }
		
		jQuery('input[name=ring_format_option]').on('click',function(){
			var val = jQuery(this).val();
			
			if(val == '1') {
				jQuery('.judge_ids').prop('checked',false);
				jQuery('.congress_judge_ids').prop('checked',false);
				jQuery('.judge_ids').prop('disabled',false);
				jQuery('.congress_judge_ids').prop('disabled',true);
			} else {
				jQuery('.judge_ids').prop('checked',false);
				jQuery('.congress_judge_ids').prop('checked',false);
				jQuery('.judge_ids').prop('disabled',true);
				jQuery('.congress_judge_ids').prop('disabled',false);
			}
		});
	});
        
    function run_catalog()
	{
		var rel = jQuery('#run_catalog').attr('rel');
		var ids = rel.split(';');
		var show_id = ids[0];
		var catalog_number = ids[1];

		if(catalog_number != 0)
		{
			new jBox('Confirm',{
		        content: "<?php echo JText::_('COM_TOES_REASSIGN_CATALOG_NUMBER_WARNING'); ?>",
		        width: '400px',
		        cancelButton : NO_BUTTON,
		        confirmButton: YES_BUTTON,
		        confirm: function() {
					jQuery.ajax({
						url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.runCatalog&id='+show_id,
						type: 'post',
					}).done(function(responseText){
						responseText = responseText.trim();
						if(responseText == 1)
						{
							getCatalogPDF(show_id);
							jQuery('#loader').hide();
							//jbox_alert(responseText);
						}
						else
						{
							jQuery('#loader').hide();
							jbox_alert(responseText);
						}
					}).fail(function(){
						jQuery('#loader').hide();
						jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
					});
					jQuery('#loader').show();

					jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
					jQuery('#loader').css('padding-top', (myHeight/2)+'px');
				}
			}).open();            
		}
		else
		{
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.runCatalog&id='+show_id,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				if(responseText == 1)
				{
					getCatalogPDF(show_id);
					jQuery('#loader').hide();
					//jbox_alert(responseText);
				}
				else
				{
					jQuery('#loader').hide();
					jbox_alert(responseText);
				}
			}).fail(function(){
				jQuery('#loader').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
			jQuery('#loader').show();

			jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
			jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		}
	}

    function late_pages()
	{
		var show_id = jQuery('#late_pages').attr('rel');

		jQuery.ajax({
			url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.latePages&id='+show_id,
			type: 'post',
		}).done(function(responseText){
			responseText = responseText.trim();
			if(responseText == 1)
			{
				getLatePagesPDF(show_id);
			}
			else
			{
				jQuery('#loader').hide();
				jbox_alert(responseText);
			}
		}).fail( function(){
			jQuery('#loader').hide();
			jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
		});
		jQuery('#loader').show();

		jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
		jQuery('#loader').css('padding-top', (myHeight/2)+'px');
	}

    function getExibitorListPDF(show_id)
    {
        window.location = '<?php echo JURI::root()?>index.php?option=com_toes&task=entryclerk.getExibitorListPDF&show_id='+show_id;
		/*
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getExibitorListPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/exibitor_list.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });
        

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
        */
        
    }
    
    function getExhibitorInfoEXL(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getExhibitorInfoEXL&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/exhibitor_info.xls');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
	
    function getExhibitorLabelDetailsEXL(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getExhibitorLabelDetailsEXL&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/exhibitorlabeldetails.xls');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
	
    function getExhibitorLabelsEXL(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getExhibitorLabelsEXL&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/exhibitorlabels.xls');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
    
    function getExhibitorCardsPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getExhibitorCardsPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/exhibitorcards.pdf');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getMicrochipListPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getMicrochipListPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/microchip_list.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getLateExibitorListPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getLateExibitorListPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/late_exibitor_list.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getMasterExibitorListPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getMasterExibitorListPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/master_exibitor_list.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getMasterExibitorWOAListPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getMasterExibitorWOAListPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/master_exibitor_woa_list.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getSummaryPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getSummaryPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/show_summary.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getTreasurerPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getTreasurerPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/treasurer.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getTreasurerEXL(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getTreasurerEXL&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/treasurer.xls');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
    
    function getBenchingPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getBenchingPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
			if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/benching.pdf','_blank');
			else
				jbox_alert(responseText);
			
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getSchedulingSummaryPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getSchedulingSummaryPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			console.log(responseText);
			//return;
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/show_scheduling_summary.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

    function getCatalogPDF(show_id)
    {
        window.open('<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getCatalogPDF&show_id='+show_id+'&<?php echo JSession::getFormToken()?>=1');

		/*
        var x;
        var time = new Date().getTime();
        var file = 'log_'+time;
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getCatalogPDF&show_id='+show_id+'&<?php echo JSession::getFormToken()?>=1',
            data: 'file='+file,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            clearInterval(x);
            unlinkLogFile(file);
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            
			if(responseText == '1')
			{
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/catalog.pdf','_blank');
                //location.reload();
			}
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            clearInterval(x);
            unlinkLogFile(file);
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
        
        x = setInterval(function(){checkProgress(file)},2000);
        */
    }
    
    function getLatePagesPDF(show_id)
    {
		// sandy hack
		//window.location = '<?php echo JURI::root()?>index.php?option=com_toes&task=entryclerk.getLatePagesPDF&show_id='+show_id;
		 
        var x;
        var time = new Date().getTime();
        var file = 'log_'+time;
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getLatePagesPDF&show_id='+show_id,
            data: 'file='+file,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            clearInterval(x);
            unlinkLogFile(file);
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            if(responseText == '1')
			{
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/latepages.pdf','_blank');
				//location.reload();
			}
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            clearInterval(x);
            unlinkLogFile(file);
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
        
        x = setInterval(function(){checkProgress(file)},2000);
         
    }
    
    function getMasterCatalogPDF(show_id)
    {
        var x;
        var time = new Date().getTime();
        var file = 'log_'+time;
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getMasterCatalogPDF&show_id='+show_id,
            data: 'file='+file,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            clearInterval(x);
            unlinkLogFile(file);
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/mastercatalog.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            clearInterval(x);
            unlinkLogFile(file);
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
        
        x = setInterval(function(){checkProgress(file)},2000);
    }
    
    function getMasterCatalogEXL(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getMasterCatalogEXL&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/mastercatalog.xls');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jQuery('#progress-box').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
	
	function getBlankJudgesBook(show_id) {
		
		
		/*
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getBlankJudgesBookPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
			if(responseText == '1')
			{
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/blankjudgesbook.pdf','_blank');
			}
			else
				jbox_alert(responseText);
        }).fail(function(){
            clearInterval(x);
            unlinkLogFile(file);
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
        jQuery('#loader').show();
        */
	}
	
	function getBlankJudgesBookFinalSheet(show_id) {
		/*
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getBlankJudgesBookFinalSheet&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
			if(responseText == '1')
			{
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/blankjudgesbookfinalsheet.pdf','_blank');
			}
			else
				jbox_alert(responseText);
        }).fail(function(){
            clearInterval(x);
            unlinkLogFile(file);
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
        jQuery('#loader').show();
        */
	}
	
    function getJudgesBook(show_id,cover_page)
    {
        var judge_ids = new Array();
		var val = jQuery('input[name=ring_format_option]:checked').val();
        if(val == 1)
        {
            jQuery('.judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
        } else {
            jQuery('.congress_judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
		}

		var post_data; 
        var time = new Date().getTime();
        var file = 'log_'+time;
        
        if(judge_ids)
        {
            post_data = 'file='+file+'&coverpage='+cover_page+'&judge_id='+judge_ids.join(',');
        }
        else
            post_data = 'file='+file+'&coverpage='+cover_page;
        
        var x;
		
		if(val == 1)
		{
			window.location = '<?php echo JURI::root()?>index.php?option=com_toes&task=entryclerk.getJudgesBookPDF&show_id='+show_id+'&'+post_data+'&<?php echo  JSession::getFormToken()?>=1';
			/*
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getJudgesBookPDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				if(responseText == '1')
				{
					if(cover_page)
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/judgesbook_cover.pdf','_blank');
					else
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/judgesbook.pdf','_blank');
				}
				else
					jbox_alert(responseText);
			}).fail(function(){
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
			*/
			
		} else {
			window.location = '<?php echo JURI::root()?>index.php?option=com_toes&task=entryclerk.getCongressJudgesBookPDF&show_id='+show_id+'&'+post_data+'&<?php echo  JSession::getFormToken()?>=1';

			/*
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getCongressJudgesBookPDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				if(responseText == '1')
				{
					if(cover_page)
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/congressjudgesbook_cover.pdf','_blank');
					else
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/congressjudgesbook.pdf','_blank');
				}
				else
					jbox_alert(responseText);
			}).fail(function(){
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
			*/
		}

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
        
        x = setInterval(function(){checkProgress(file)},2000);
    }
    function getJudgesBook_bak(show_id,cover_page)
    {
        var judge_ids = new Array();
		var val = jQuery('input[name=ring_format_option]:checked').val();
        if(val == 1)
        {
            jQuery('.judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
        } else {
            jQuery('.congress_judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
		}

		var post_data; 
        var time = new Date().getTime();
        var file = 'log_'+time;
        
        if(judge_ids)
        {
            post_data = 'file='+file+'&coverpage='+cover_page+'&judge_id='+judge_ids.join(',');
        }
        else
            post_data = 'file='+file+'&coverpage='+cover_page;
        
        var x;
		
		if(val == 1)
		{
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getJudgesBookPDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				if(responseText == '1')
				{
					if(cover_page)
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/judgesbook_cover.pdf','_blank');
					else
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/judgesbook.pdf','_blank');
				}
				else
					jbox_alert(responseText);
			}).fail(function(){
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		} else {
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getCongressJudgesBookPDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				if(responseText == '1')
				{
					if(cover_page)
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/congressjudgesbook_cover.pdf','_blank');
					else
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/congressjudgesbook.pdf','_blank');
				}
				else
					jbox_alert(responseText);
			}).fail(function(){
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		}

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
        
        x = setInterval(function(){checkProgress(file)},2000);
    }

	
    function getJudgesBookInA4(show_id,cover_page)
    {
        var judge_ids = new Array();
		var val = jQuery('input[name=ring_format_option]:checked').val();
        if(val == 1)
        {
            jQuery('.judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
        } else {
            jQuery('.congress_judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
		}

		var post_data; 
        var time = new Date().getTime();
        var file = 'log_'+time;
        
        if(judge_ids)
        {
            post_data = 'file='+file+'&coverpage='+cover_page+'&judge_id='+judge_ids.join(',');
        }
        else
            post_data = 'file='+file+'&coverpage='+cover_page;
        
        var x;
		
		if(val == 1)
		{
			window.location = '<?php echo JURI::root()?>index.php?option=com_toes&task=entryclerk.getJudgesBookInA4PDF&show_id='+show_id+'&'+post_data;
			/*
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getJudgesBookInA4PDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				if(responseText == '1')
				{
					if(cover_page)
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/judgesbook_cover.pdf','_blank');
					else
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/judgesbook.pdf','_blank');
				}
				else
					jbox_alert(responseText);
			}).fail(function(){
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
			*/
		} else {
			window.location = '<?php echo JURI::root()?>index.php?option=com_toes&task=entryclerk.getCongressJudgesBookInA4PDF&show_id='+show_id+'&'+post_data;
	
			/*
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getCongressJudgesBookInA4PDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				if(responseText == '1')
				{
					if(cover_page)
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/congressjudgesbook_cover.pdf','_blank');
					else
						window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/congressjudgesbook.pdf','_blank');
				}
				else
					jbox_alert(responseText);
			}).fail(function(){
				clearInterval(x);
				unlinkLogFile(file);
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
			*/
		}

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
        
        x = setInterval(function(){checkProgress(file)},2000);
    }

    var last_page = 1;

    function getPreJudgesBook(show_id,download)
    {
        var judge_ids = new Array();
		var val = jQuery('input[name=ring_format_option]:checked').val();
        if(val == 1)
        {
            jQuery('.judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
        } else {
            jQuery('.congress_judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
		}
        
        var post_data; 
        if(judge_ids)
        {
            post_data = 'judge_id='+judge_ids.join(',');
        }
        else
            post_data = '';
        
        var x;
        if(val == 1)
        {
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getPreJudgesBookPDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				if(!isNaN(responseText))
				{
					last_page = responseText;

					jQuery.ajax({
						url: '<?php echo JUri::root(); ?>index.php?option=com_toes&view=entryclerk&layout=matrix_printing&tmpl=component&last_page='+responseText+'&id='+show_id+'&download='+download,
						data: post_data,
						type: 'post',
					}).done(function(responseText){
						responseText = responseText.trim();
						new jBox('Modal',{
							title: 'Print Options',
							content: responseText,
							width: '400px'
						}).open();
						jQuery('#loader').hide();
						if(!download) {
							launchQZ();
						}
					});
				}    
				else
				{
					jbox_alert(responseText);
					jQuery('#loader').hide();
				}

				//window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/prejudgesbook.txt','_blank');
			}).fail(function(){
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		} else {
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getPreCongressJudgesBookPDF&show_id='+show_id,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				if(!isNaN(responseText))
				{
					last_page = responseText;

					jQuery.ajax({
						url: '<?php echo JUri::root(); ?>index.php?option=com_toes&view=entryclerk&layout=matrix_printing&tmpl=component&last_page='+responseText+'&id='+show_id,
						data: post_data,
						type: 'post',
					}).done(function(responseText){
						responseText = responseText.trim();
						new jBox('Modal',{
							title: 'Print Options',
							content: responseText,
							width: '400px'
						}).open();
						jQuery('#loader').hide();
						launchQZ();
					});
				}    
				else
				{
					jbox_alert(responseText);
					jQuery('#loader').hide();
				}

				//window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/prejudgesbook.txt','_blank');
			}).fail(function(){
				jQuery('#loader').hide();
				jQuery('#progress-box').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		}

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
    
    function printPreJudgeFile(show_id)
    {
        var judge_ids = new Array();

		var val = jQuery('input[name=ring_format_option]:checked').val();
        if(val == 1)
        {
            jQuery('.judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
        } else {
            jQuery('.congress_judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
		}
        
        var post_data; 
        if(judge_ids)
        {
            post_data = 'judge_id='+judge_ids.join(',');
        }
        else
            post_data = '';
        
        var page_option = parseInt(jQuery('input[name=page_options]:checked').val());
        
        var start_page = 1;
        var end_page = last_page;
        switch(page_option)
        {
            case 0:
                start_page = 1;
                break;
            case 1:
                start_page = jQuery('#page_options_1_x').val();
                break;
            case 2:
                start_page = jQuery('#page_options_2_x').val();
                end_page = jQuery('#page_options_2_y').val();
                break;
        }
        
        if(val == 1)
        {
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getPreJudgesBookPDF&show_id='+show_id+'&start_page='+start_page+'&end_page='+end_page,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				var file = '<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/prejudgesbook.txt';

				if (qz && printer_set) {
					var config = getUpdatedConfig();

					var printData = [
						{ type: 'raw', format: 'file', data: file }
					];

					qz.print(config, printData).catch(displayError);
				}
				else
				{
					window.open(file,'_blank');                
				}
			}).fail(function(){
				jQuery('#loader').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		} else {
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getPreCongressJudgesBookPDF&show_id='+show_id+'&start_page='+start_page+'&end_page='+end_page,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				responseText = responseText.trim();
				var file = '<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/precongressjudgesbook.txt';

				if (qz && printer_set) {
					var config = getUpdatedConfig();

					var printData = [
						{ type: 'raw', format: 'file', data: file }
					];

					qz.print(config, printData).catch(displayError);
				}
				else
				{
					window.open(file,'_blank');                
				}
			}).fail(function(){
				jQuery('#loader').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		}
	}

    function downloadPreJudgesBook(show_id)
    {
        var judge_ids = new Array();

		var val = jQuery('input[name=ring_format_option]:checked').val();
        if(val == 1)
        {
            jQuery('.judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
        } else {
            jQuery('.congress_judge_ids:checked').each(function(){
                judge_ids.push(jQuery(this).val());
            });
		}
        
        var post_data; 
        if(judge_ids)
        {
            post_data = 'judge_id='+judge_ids.join(',');
        }
        else
            post_data = '';
        
        var page_option = parseInt(jQuery('input[name=page_options]:checked').val());
        
        var start_page = 1;
        var end_page = last_page;
        switch(page_option)
        {
            case 0:
                start_page = 1;
                break;
            case 1:
                start_page = jQuery('#page_options_1_x').val();
                break;
            case 2:
                start_page = jQuery('#page_options_2_x').val();
                end_page = jQuery('#page_options_2_y').val();
                break;
        }
        
        if(val == 1)
        {
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getPreJudgesBookPDF&show_id='+show_id+'&start_page='+start_page+'&end_page='+end_page,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				jQuery('#loader').hide();
				responseText = responseText.trim();
				var file = '<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/prejudgesbook.txt';
				window.open(file,'_blank');                

			}).fail(function(){
				jQuery('#loader').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		} else {
			jQuery.ajax({
				url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getPreCongressJudgesBookPDF&show_id='+show_id+'&start_page='+start_page+'&end_page='+end_page,
				data: post_data,
				type: 'post',
			}).done(function(responseText){
				jQuery('#loader').hide();
				responseText = responseText.trim();
				var file = '<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/precongressjudgesbook.txt';
				window.open(file,'_blank');                

			}).fail(function(){
				jQuery('#loader').hide();
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		}

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
    
    function checkProgress(file)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.checkProgress',
            type: 'post',
            data: 'file='+file,
        }).done(function(responseText){
			responseText = responseText.trim();
            if(responseText != 0)
            {
                var data = responseText.split(';'); 
                jQuery('#progress-box').show();
                jQuery('#progress-log-text').show();
                
                var processed = data[1].split(':')[1];
                var total = data[0].split(':')[1];
				var log_text = data[2].split(':')[1];
                var width;
                if(processed == 0)
                    width = 0;
                else
                    width = ((processed/total)*100);

                jQuery('#progress-bar').css('width',width+'%');
                jQuery('#progress-count-processed').text(processed);
                jQuery('#progress-count-total').text(total);
				jQuery('#progress-log-text').text(log_text);
            }
        });
    }
    
    function unlinkLogFile(file)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.unlinkLogFile',
            type: 'post',
            data: 'file='+file
        });
    }
    
    function changePapersize(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.changePapersize',
            type: 'post',
            data: 'show_id='+show_id+'&paper_size='+jQuery('#show_paper_size').val(),
        }).done(function(responseText){
			responseText = responseText.trim();
            if(responseText == 1)
                jbox_notice("<?php echo JText::_('COM_TOES_PAPER_SIZE_SAVED'); ?>",'green');
            else
                jbox_alert(responseText);
        });
    }
    
     
    
	function getEntryclerkCheatsheetPDF(show_id)
	{
		jQuery.ajax({
            url: 'index.php?option=com_toes&task=entryclerk.getEntryclerkCheatsheetPDF&show_id='+show_id,
            method: 'post',
        }).done(function(responseText){
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JURI::root(); ?>media/com_toes/PDF/'+show_id+'/cheatsheet.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert('<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>');
       });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
    
	function getEntryclerkCheatsheetEXL(show_id)
	{
		jQuery.ajax({
            url: 'index.php?option=com_toes&task=entryclerk.getEntryclerkCheatsheetEXL&show_id='+show_id,
            method: 'post',
        }).done(function(responseText){
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JURI::root(); ?>media/com_toes/PDF/'+show_id+'/cheatsheet.xls','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert('<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>');
       });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
    
	function getAbsenteesPDF(show_id)
	{
		jQuery.ajax({
            url: 'index.php?option=com_toes&task=entryclerk.getAbsenteesPDF&show_id='+show_id,
            method: 'post',
        }).done(function(responseText){
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JURI::root(); ?>media/com_toes/PDF/'+show_id+'/absentees.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert('<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>');
       });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
        jQuery('#loader').show();
    }
    
	function getAbsenteesEXL(show_id)
	{
		jQuery.ajax({
            url: 'index.php?option=com_toes&task=entryclerk.getAbsenteesEXL&show_id='+show_id,
            method: 'post',
        }).done(function(responseText){
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JURI::root(); ?>media/com_toes/PDF/'+show_id+'/absentees.xls','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert('<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>');
       });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
        jQuery('#loader').show();
    }
    
	function getCheckinSheetPDF(show_id)
	{
		jQuery.ajax({
            url: 'index.php?option=com_toes&task=entryclerk.getCheckinSheetPDF&show_id='+show_id,
            method: 'post',
        }).done(function(responseText){
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JURI::root(); ?>media/com_toes/PDF/'+show_id+'/checkinsheet.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert('<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>');
       });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
        jQuery('#loader').show();
    }

    function getSpaceSummaryPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getSpaceSummaryPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
            if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/space_summary.pdf','_blank');
			else
				jbox_alert(responseText);
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }
    
    function getBenchingCardsPDF(show_id)
    {
        jQuery.ajax({
            url: '<?php echo JUri::root(); ?>index.php?option=com_toes&task=entryclerk.getBenchingCardsPDF&show_id='+show_id,
            type: 'post',
        }).done(function(responseText){
			responseText = responseText.trim();
            jQuery('#loader').hide();
			if(responseText == '1')
				window.open('<?php echo JUri::root(); ?>media/com_toes/PDF/'+show_id+'/benchingcards.pdf','_blank');
			else
				jbox_alert(responseText);
			
        }).fail(function(){
            jQuery('#loader').hide();
            jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
        });

        jQuery('#loader').css('padding-left', ((myWidth-250)/2)+'px');
        jQuery('#loader').css('padding-top', (myHeight/2)+'px');
		jQuery('#progress-box').hide();
		jQuery('#progress-log-text').hide();
        jQuery('#loader').show();
    }

</script>
