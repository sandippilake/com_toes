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

// $data = $this->item;
// $show_status = $data->show_status;
$user = JFactory::getUser();

JHtml::_('behavior.modal','a.modal');

// $isAdmin = TOESHelper::isAdmin();
// $club = TOESHelper::getClub($data->show_id);
// $params = JComponentHelper::getParams('com_toes');

// echo "<pre>";
// var_dump($this->conflictedshows);
$isConflictingRd = false;
?>


<div class="show-details-short">
	<table class="table-striped">
		<tr>
			<th>date</th>
			<th>address</th>
			<th>club name</th>
			<th>aprrove/reject</th>
		</tr>
		<?php foreach($this->conflictedshows as $conflict){?>
		<tr>
			<td>
				<?php 
					$start_date = date('d', strtotime($conflict->show_start_date));
					$start_date_month = date('M', strtotime($conflict->show_start_date));

					$end_date = date('d', strtotime($conflict->show_end_date));
					$end_date_month = date('M', strtotime($conflict->show_end_date));

					echo $start_date;

					if ($end_date_month != $start_date_month){
						if(date('t',strtotime($conflict->show_start_date)) != $start_date)
							echo ' - '.date('t',strtotime($conflict->show_start_date));
						if($end_date == '01')
							echo ', ' .$end_date_month.' '.$end_date;
						else
							echo ', ' .$end_date_month.' 01 - '.$end_date;
					} else {
						if($start_date != $end_date)
							echo ' - ' . $end_date;
					}

				?>
			</td>
			<td>
				<?php 
					if($conflict->address_city)
						echo $conflict->address_city.', ';
					if($conflict->address_state)
						echo $conflict->address_state.', ';
					if($conflict->address_country)
						echo $conflict->address_country; 
				
					if($conflict->show_is_annual) 
						echo '<p style="font-style: normal;display: inline-block; padding: 0 10px; margin: 0 10px;" class="info">'.JText::_('COM_TOES_ANNUAL_TAG').'</p>';
					else if($conflict->show_is_regional) 
						echo '<p style="font-style: normal;display: inline-block; padding: 0 10px; margin: 0 10px;" class="info">'.JText::_('COM_TOES_REGIONAL_TAG').'</p>';
				?>
			</td>
			<td>
				<?php /* if($conflict->existing_show_region_rd_approval_required)
					echo $conflict->existing_show_region_rd_user_id; */
				?>
				<?php echo $conflict->club_name; ?>
			</td>
			<td>
				<?php if($conflict->existing_show_region_rd_user_id == $user->id){?>
					<span class="hasTip" title="<?php echo "You are regional director of this show";?>">
					<?php if(!$conflict->existing_show_region_rd_approved){?>
							<a href="javascript:void(0)" rel="<?php echo $conflict->existing_show_id; ?>" class="approve-conflict" id="e_rd-<?php echo $conflict->conflicting_show_id.'-'.$conflict->existing_show_id.'-'.$conflict->id; ?>">
								<i class="fa fa-check"></i> 
							</a>
							<a href="javascript:void(0)" rel="<?php echo $conflict->existing_show_id; ?>" class="reject-conflict" id="e_rd-<?php echo $conflict->conflicting_show_id.'-'.$conflict->existing_show_id.'-'.$conflict->id; ?>">
								<i class="fa fa-remove"></i> 
							</a>
					<?php }else if($conflict->existing_show_region_rd_approved == '-1'){?>
						<i class="fa fa-remove" style="color:red"></i>
					<?php }else if($conflict->existing_show_region_rd_approved == '1'){?>
						<i class="fa fa-check" style="color:green"></i>
					<?php }?>
					</span>
				<?php }?>
				<?php if($conflict->conflicting_show_region_rd_user_id == $user->id){
					$isConflictingRd = true;
				}
				?>
				<?php 
				$str = "|".$user->id."|";
				if(strpos($conflict->existing_club_official_user_ids, $str) !== false){ ?>
					<span class="hasTip" title="<?php echo "You a club offical of this show";?>">
						
						<?php if(!$conflict->existing_club_official_approved){?>
							<a href="javascript:void(0)" rel="<?php echo $conflict->existing_show_id; ?>" class="approve-conflict" id="e_co-<?php echo $conflict->conflicting_show_id.'-'.$conflict->existing_show_id.'-'.$conflict->id; ?>">
								<i class="fa fa-check"></i> 
							</a>
							<a href="javascript:void(0)" rel="<?php echo $conflict->existing_show_id; ?>" class="reject-conflict" id="e_co-<?php echo $conflict->conflicting_show_id.'-'.$conflict->existing_show_id.'-'.$conflict->id; ?>">
								<i class="fa fa-remove"></i> 
							</a>
					<?php }else if($conflict->existing_club_official_approved == '-1'){?>
						<i class="fa fa-remove" style="color:red"></i>
					<?php }else if($conflict->existing_club_official_approved == '1'){?>
						<i class="fa fa-check" style="color:green"></i>
					<?php }?>
					</span>
				<?php }?>
			</td>
		</tr>
		<?php }?>
		<?php if($isConflictingRd){?>
		<tr>
			<td></td>
			<td></td>
			<td></td>
			<td>
				<span class="hasTip" title="<?php echo "You are regional director of the conflicting show";?>">
					<?php if(!$this->conflictedshows[0]->conflicting_show_region_rd_approved){?>
							<a href="javascript:void(0)" rel="<?php echo $this->conflictedshows[0]->existing_show_id; ?>" class="approve-conflict" id="c_rd-<?php echo $this->conflictedshows[0]->conflicting_show_id.'-'.$this->conflictedshows[0]->existing_show_id.'-'.$this->conflictedshows[0]->id; ?>">
								<i class="fa fa-check"></i> 
							</a>
							<a href="javascript:void(0)" rel="<?php echo $this->conflictedshows[0]->existing_show_id; ?>" class="reject-conflict" id="c_rd-<?php echo $this->conflictedshows[0]->conflicting_show_id.'-'.$this->conflictedshows[0]->existing_show_id.'-'.$this->conflictedshows[0]->id; ?>">
								<i class="fa fa-remove"></i> 
							</a>
					<?php }else if($this->conflictedshows[0]->conflicting_show_region_rd_approved == '-1'){?>
						<i class="fa fa-remove" style="color:red"></i>
					<?php }else if($this->conflictedshows[0]->conflicting_show_region_rd_approved == '1'){?>
						<i class="fa fa-check" style="color:green"></i>
					<?php }?>
					</span>
				</span>
			</td>
		</tr>
		<?php }?>
	</table>
</div>
