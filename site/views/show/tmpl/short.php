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
//JHtml::_('behavior.calendar');

$data = $this->item;
$show_status = $data->show_status;
$user = JFactory::getUser();

JHtml::_('behavior.modal','a.modal');

$user_id = isset($data->summary->summary_user)?$data->summary->summary_user:$user->id;

$isAdmin = TOESHelper::isAdmin();
$club = TOESHelper::getClub($data->show_id);
$params = JComponentHelper::getParams('com_toes');

$show_ended = false;

$end_date = (int)date('d', strtotime($data->show_end_date));
$end_date_month = (int)date('m', strtotime($data->show_end_date));
$end_date_year = (int)date('Y', strtotime($data->show_end_date));

$show_endtime = mktime(0, 0, 0, $end_date_month, $end_date + 1, $end_date_year );

if(time() > $show_endtime) {
	$show_ended = true;
}

$url = JURI::getInstance();
$show_link = $url->getScheme().'://'.$url->getHost().JRoute::_('index.php?option=com_toes&view=shows',false).'#show'.$data->show_id;

?>

<script type="text/javascript">
	jQuery('document').ready(function(){
		jQuery('.send-invoice').on('click',function(){
			
			var parent = jQuery(this).parent();
			
			var rel = jQuery(this).attr('rel');
			var ids = rel.split(';');
			var data = 'show_id='+ids[0]+'&club_id='+ids[1];
			
			jQuery.ajax({
				url:'index.php?option=com_toes&task=show.sendInvoice',
				data: data,
				type: 'post'
			}).done(function(responseText){
				if(responseText === '1') {
					jbox_alert('<?php echo JText::_('COM_TOES_INVOICE_SENT');?>');
					parent.text('<?php echo JText::_('COM_TOES_INVOICE_SENT');?>');
				} else {
					jbox_alert(responseText);
				}
			}).fail(function(){
				jbox_alert("<?php echo JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST');?>");
			});
		});
	});
</script>

<br/>
<div class="show-details-short">
	<br/>
    <div class="action-buttons" style="width: auto;" >
        <span style="float:right;" class="hasTip" title="<?php echo JText::_('CLOSE');?>">
            <a href="javascript:void(0)" class="close-show-view" onclick="close_show();" >
                <i class="fa fa-minus-circle"></i> 
            </a>
        </span>

        <span style="float:right;" class="hasTip" title="<?php echo JText::_('COM_TOES_DIRECT_LINK_TO_SHOW');?>">
            <a href="javascript:void(0)" onclick="displayLink(this);" class="display-show-link" rel="<?php echo $show_link;?>">
                <i class="fa fa-link"></i> 
            </a>
        </span>
        
        <?php if($isAdmin || TOESHelper::is_clubowner($user->id, $club->club_id)) : ?>
            <span style="float:right;" class="hasTip" title="<?php echo JText::_('COM_TOES_COPY_SHOW_HELP');?>">
                <input class="copyshow" type="button" value="<?php echo JText::_('COM_TOES_COPY_SHOW');?>" onclick="copyshow('<?php echo $data->show_id?>')" />
            </span>
        <?php endif; ?>
        
        <?php if($isAdmin && $data->show_uses_toes == 1 && $show_ended) : ?>
        	<?php if($this->invoice) : ?>
                <?php echo JText::_('COM_TOES_INVOICE_SENT');?>
        	<?php else : ?>
	            <span style="float:right;" class="hasTip" title="<?php echo JText::_('COM_TOES_SEND_INVOICE_HELP');?>">
	                <input type="button" value="<?php echo JText::_('COM_TOES_SEND_INVOICE');?>" class="send-invoice" rel="<?php echo $data->show_id.';'.$club->club_id ?>" />
	            </span>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="title"><?php echo JText::_('COM_TOES_SHOW_DETAILS'); ?></div>
    <div class="clr"></div>
    <div class ="block">
        <div class="date">
            <?php
            $start_date = date('d', strtotime($data->show_start_date));
            $start_date_month = date('M', strtotime($data->show_start_date));
            $start_date_year = date('Y', strtotime($data->show_start_date));

            $end_date = date('d', strtotime($data->show_end_date));
            $end_date_month = date('M', strtotime($data->show_end_date));
            $end_date_year = date('Y', strtotime($data->show_end_date));

            echo $start_date_month.' '.$start_date;

            if ($end_date_year != $start_date_year){
                echo ' '.$start_date_year;
            }

            if ($end_date_month != $start_date_month){
                if(date('t', strtotime($data->show_start_date)) != $start_date)
                    echo ' - '.date('t', strtotime($data->show_start_date));
                if($end_date == '01')
                    echo ', ' .$end_date_month.' '.$end_date;
                else
                    echo ', ' .$end_date_month.' 01 - '.$end_date;
            } else {
                if($start_date != $end_date)
                    echo ' - ' . $start_date_month.' '.$end_date;
            }

            echo ' '.$end_date_year;

            ?>
        </div>
        
        <div class="club-name" >
            <?php echo $data->club_name; ?>
        </div>
        <div class="clr"></div>

        <div>
            <label>
                <?php echo JText::_('COM_TOES_SHOW_FORMAT'); ?>
            </label>

            <span>
                <?php 
                    $is_continuous = false;
					$is_alernative = false;
                    switch ($data->show_format)
                    {
                        case 1:
                            echo JText::_('Back to Back');
                            break;
                        case 2:
                            echo JText::_('Alternative');
							$is_alernative = true;
                            break;
                        case 3:
                            echo JText::_('Continuous');
                            $is_continuous = true;
                            break;
                }
                 ?>
            </span>
        </div>

        <div>
            <label>
                <?php echo JText::_('COM_TOES_SHOW_SHOWHALL') . ':'; ?>
            </label>
            <span>
                <?php
				if ($data->venue_name)
					echo $data->venue_name.'<br/>';
                if ($data->address_line_1)
                    echo $data->address_line_1 . '<br/>';
                if ($data->address_line_2)
                    echo $data->address_line_2 . '<br/>';
                if ($data->address_line_3)
                    echo $data->address_line_3 . '<br/>';
                if ($data->address_city)
                    echo $data->address_city . ' , ';
                if($data->address_state)
                    echo $data->address_state . ' , ';
                if($data->address_zip_code)
                    echo $data->address_zip_code;
                echo '<br/>';
                echo $data->address_country . '<br/>';
                ?>
                <?php if($data->show_venue){?>
                <a  target="_blank" href="index.php?option=com_toes&view=show&layout=map&venue_id=<?php echo $data->show_venue;?>&tmpl=component">
					<i class="fa fa-info-circle"></i>&nbsp;View show venue location in Google Maps
				</a>
				<?php } ?>
            </span>
        </div>
        
        <div class="judges">
            <label>
                <?php echo JText::_('COM_TOES_SHOW_JUDGES') . ':'; ?>
            </label>
            <span>
                <?php
                    if($this->judges || $this->congress_judges)
                    {
                        if($is_continuous)
                        {
                            $i = 0;
                            foreach($this->judges as $show_day=>$ring_timings)
                            {
                                foreach($ring_timings as $ring_timing=>$judges)
                                {
                                    foreach($judges as $judge)
                                    {
                                        if($i!=0)
                                            echo ', ';
                                        switch ($judge->ring_format)
                                        {
                                            case 'Allbreed':
                                                $ring_format = 'AB';
                                                break;
                                            case 'Specialty':
                                                $ring_format = 'SP';
                                                break;
                                        }

                                        echo $judge->name.'('.$ring_format.')';
                                        $i++;
                                    }
                                }
                            }
                            echo '<br/>';
                            foreach($this->congress_judges as $show_day=>$ring_timings)
                            {
                                foreach($ring_timings as $ring_timing=>$judges)
                                {
                                    if(isset($this->congress_judges[$show_day][$ring_timing]) && count($this->congress_judges[$show_day][$ring_timing]) > 0)
                                    {
                                        echo JText::_('COM_TOES_ENTRY_CONGRESS').': ';
                                        $i = 0;
                                        foreach($this->congress_judges[$show_day][$ring_timing] as $judge)
                                        {
                                            if($i!=0)
                                                echo ', ';
                                            echo $judge->name.'('.$judge->ring_name.')';
                                            $i++;
                                        }
                                    }
                                }
                            }
                        }
                        else 
                        {
							foreach($this->item->showdays as $showday)
							{
								$show_day = $showday->show_day_date;
								if(isset($this->ring_timings[$show_day]) && count($this->ring_timings[$show_day]) > 0)
								{
									echo date('l',strtotime($show_day)).'<br/>';
									foreach($this->ring_timings[$show_day] as $ring_timing)
									{
										echo "&nbsp;&nbsp;&nbsp;&nbsp;";
										switch ($ring_timing)
										{
											case '1':
												echo 'AM: ';
												break;
											case '2':
												echo 'PM: ';
												break;
										}
										if(isset($this->judges[$show_day][$ring_timing]) && count($this->judges[$show_day][$ring_timing]) > 0)
										{
											$i = 0;
											foreach($this->judges[$show_day][$ring_timing] as $judge)
											{
												if($i!=0)
													echo ', ';
												switch ($judge->ring_format)
												{
													case 'Allbreed':
														$ring_format = 'AB';
														break;
													case 'Specialty':
														$ring_format = 'SP';
														break;
												}

												echo $judge->name.'('.$ring_format.')';
												$i++;
											}
											echo '<br/>';
										}
										else
										{
											if($ring_timing)
												echo '<br/>';
										}
										if(isset($this->congress_judges[$show_day][$ring_timing]) && count($this->congress_judges[$show_day][$ring_timing]) > 0)
										{
											if($ring_timing)
												echo "&nbsp;&nbsp;&nbsp;&nbsp;";
											echo "&nbsp;&nbsp;&nbsp;&nbsp;".JText::_('COM_TOES_ENTRY_CONGRESS').': ';
											$i = 0;
											foreach($this->congress_judges[$show_day][$ring_timing] as $judge)
											{
												if($i!=0)
													echo ', ';
												echo $judge->name.'('.$judge->ring_name.')';
												$i++;
											}
											echo '<br/>';
										}
									}
								}
							}
                        }
                    }
                ?>
            </span>
        </div>

        <div class="judges">
            <label>
                <?php echo JText::_('COM_TOES_SHOW_COMMENTS') . ':'; ?>
            </label>
            <span>
                <?php
                    if ($data->show_comments)
                        echo $data->show_comments;
                    else
                        echo '-';
                ?>
            </span>
        </div>
		
		<?php if($data->show_uses_toes == '1' &&  ( ($isAdmin && $params->get('show_cat_count_for_admin')) || ($user->authorise('toes.access_cat_count','com_toes') && $data->show_display_counts) ) ): ?>
        <div class="judges">
            <label>
                <?php echo JText::_('COM_TOES_CAT_COUNT_SECTION_TITLE') . ':'; ?>
            </label>
            <span>
                <?php
					$db = JFactory::getDBO();

					$query = "SELECT `sd`.*
					FROM `#__toes_show_day` AS `sd` 
					WHERE `sd`.`show_day_show` = {$data->show_id} ";
					$db->setQuery($query);
					$show_days = $data->showdays;

					$showClasses = array(
							array(
							'LH Kitten'=>'LH Kitten',
							'SH Kitten'=>'SH Kitten'
								),
							array(
							'LH Cat'=>'LH Cat',
							'SH CAT'=>'SH Cat'
								),
							array(
							'LH Alter'=>'LH Alter',
							'SH Alter'=>'SH Alter'
								),
							array(
							'LH HHP Kitten'=>'LH HHP Kitten',
							'SH HHP Kitten'=>'SH HHP Kitten'
								),
							array(
							'LH HHP'=>'LH HHP',
							'SH HHP'=>'SH HHP'
								),
							array(
							'LH NT'=>'LH NT',
							'SH NT'=>'SH NT'
								),
							array(
							'LH ANB'=>'LH ANB',
							'SH ANB'=>'SH ANB'
								),
							array(
							'LH PNB'=>'LH PNB',
							'SH PNB'=>'SH PNB'
								),
							array(
							'Ex Only'=>'Ex Only'
								)
					);

					if($is_alernative)
					{
						$whr = array();
						$whr[] = "`e`.`entry_show` = {$data->show_id}";
						
						$query = TOESQueryHelper::getShowSummariesAMSessionQuery($whr);
						$db->setQuery($query);
						$am_summary = $db->loadObjectList();

						$query = TOESQueryHelper::getShowSummariesPMSessionQuery($whr);
						$db->setQuery($query);
						$pm_summary = $db->loadObjectList();

						$final_am_summary = array();
						foreach($am_summary as $smry)
						{
							$final_am_summary[$smry->show_class][$smry->show_day_id] = $smry->cat_count;
						}

						$final_pm_summary = array();
						foreach($pm_summary as $smry)
						{
							$final_pm_summary[$smry->show_class][$smry->show_day_id] = $smry->cat_count;
						}

						$am_totals = array();
						$pm_totals = array();

						$tbl = '';

						$tbl .= '<table style="text-align:center;">';

						$tbl .= '<tr>';
						$tbl .= '<td style="width:30%;" >&nbsp;</td>';
						for($i = 0; $i < count($show_days); $i++)
						{
							$tbl .= '<td style="width:20%;" colspan="2">'.date('l',strtotime($show_days[$i]->show_day_date)).'</td>';
						}
						$tbl .= '</tr>';
						$tbl .= '<tr>';
						$tbl .= '<td style="width:30%;">&nbsp;</td>';
						for($i = 0; $i < count($show_days); $i++)
						{
							$tbl .= '<td>AM</td>';
							$tbl .= '<td>PM</td>';
						}
						$tbl .= '</tr>';
						
						
						foreach($showClasses as $show_group)
						{
							$cnt_am_total = array();
							$cnt_pm_total = array();

							$show_class_am_count = 0;
							$show_class_pm_count = 0;

							foreach($show_group as $show_class)
							{
								if( (isset($final_am_summary[$show_class]) && $final_am_summary[$show_class]) || (isset($final_pm_summary[$show_class]) && $final_pm_summary[$show_class]) )
								{
									$tbl .= '<tr>';
									$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper($show_class).'</td>';
									for($i = 0; $i < count($show_days); $i++)
									{
										$am_cnt = isset($final_am_summary[$show_class][$show_days[$i]->show_day_id])?$final_am_summary[$show_class][$show_days[$i]->show_day_id]:0;

										$show_class_am_count += $am_cnt;

										if(isset($cnt_am_total[$show_days[$i]->show_day_id]))
											$cnt_am_total[$show_days[$i]->show_day_id] += $am_cnt;
										else
											$cnt_am_total[$show_days[$i]->show_day_id] = $am_cnt;

										if(isset($am_totals[$show_days[$i]->show_day_id]))
											$am_totals[$show_days[$i]->show_day_id] += $am_cnt;
										else
											$am_totals[$show_days[$i]->show_day_id] = $am_cnt;

										$pm_cnt = isset($final_pm_summary[$show_class][$show_days[$i]->show_day_id])?$final_pm_summary[$show_class][$show_days[$i]->show_day_id]:0;

										$show_class_pm_count += $pm_cnt;

										if(isset($cnt_pm_total[$show_days[$i]->show_day_id]))
											$cnt_pm_total[$show_days[$i]->show_day_id] += $pm_cnt;
										else
											$cnt_pm_total[$show_days[$i]->show_day_id] = $pm_cnt;

										if(isset($pm_totals[$show_days[$i]->show_day_id]))
											$pm_totals[$show_days[$i]->show_day_id] += $pm_cnt;
										else
											$pm_totals[$show_days[$i]->show_day_id] = $pm_cnt;

										$tbl .= '<td>'.$am_cnt.'</td>'.'<td>'.$pm_cnt.'</td>';
									}
									$tbl .= '</tr>';
								}
							}

							if($show_class != 'Ex Only')
							{
								if($show_class_pm_count || $show_class_pm_count)
								{
									$tbl .= '<tr>';
									$tbl .= '<td style="width:30%;text-align:left;" >AB '.strtoupper(str_replace('SH ', '', $show_class)).'</td>';
									for($i = 0; $i < count($show_days); $i++)
									{
										$am_cnt = isset($cnt_am_total[$show_days[$i]->show_day_id])?$cnt_am_total[$show_days[$i]->show_day_id]:0;
										$pm_cnt = isset($cnt_pm_total[$show_days[$i]->show_day_id])?$cnt_pm_total[$show_days[$i]->show_day_id]:0;

										$tbl .= '<td>'.$am_cnt.'</td>'.'<td>'.$pm_cnt.'</td>';
									}
									$tbl .= '</tr>';
								}
							}
							if($show_class_pm_count || $show_class_pm_count)
							{
								$tbl .= '<tr>';
								$tbl .= '<td>&nbsp;</td>' ;
								$tbl .= '</tr>';
							}
						}

						$whr = array();
						$whr[] = "ring_show = {$data->show_id}";
						
						$query = TOESQueryHelper::getCongressSummaryQuery($whr);
						$db->setQuery($query);
						$congress_summary = $db->loadObjectList();

						$final_congress_summary = array();
						foreach($congress_summary as $smry)
						{
							$final_congress_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
						}

						$query = TOESQueryHelper::getCongressSummaryAMSessionQuery($whr);
						$db->setQuery($query);
						$congress_summary_am_session = $db->loadObjectList();

						$query = TOESQueryHelper::getCongressSummaryPMSessionQuery($whr);
						$db->setQuery($query);
						$congress_summary_pm_session = $db->loadObjectList();

						$final_congress_am_summary = array();
						foreach($congress_summary_am_session as $smry)
						{
							$final_congress_am_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
						}
						$final_congress_pm_summary = array();
						foreach($congress_summary_pm_session as $smry)
						{
							$final_congress_pm_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
						}

						foreach($final_congress_summary as $ring_name=>$smry)
						{
							$tbl .= '<tr>';
							$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper($ring_name).'</td>';
							for($i = 0; $i < count($show_days); $i++)
							{
								$am_cnt = isset($final_congress_am_summary[$ring_name][$show_days[$i]->show_day_id])?
									$final_congress_am_summary[$ring_name][$show_days[$i]->show_day_id]:'-';

								$pm_cnt = isset($final_congress_pm_summary[$ring_name][$show_days[$i]->show_day_id])?
									$final_congress_pm_summary[$ring_name][$show_days[$i]->show_day_id]:'-';

								$tbl .= '<td>'.$am_cnt.'</td>'.'<td>'.$pm_cnt.'</td>';
							}
							$tbl .= '</tr>';
						}
						if($congress_summary)
						{
							$tbl .= '<tr>';
							$tbl .= '<td>&nbsp;</td>' ;
							$tbl .= '</tr>';
						}
						
						$tbl .= '<tr>';
						$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper(JText::_('COM_TOES_PLACEHOLDERS')).'</td>';
						for($i = 0; $i < count($show_days); $i++)
						{
							$query = $db->getQuery(true);

							$query->select('count(pd.placeholder_day_showday)');
							$query->from('#__toes_placeholder_day AS pd');
							$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
							$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
							$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
							$query->where('p.placeholder_show = ' . $data->show_id);
							$query->where('pd.placeholder_participates_AM = 1');
							$query->where('pd.placeholder_day_showday = ' . $show_days[$i]->show_day_id);
							$query->where('(es.entry_status = '.$db->quote('New').' OR es.entry_status = '.$db->quote('Accepted').' OR es.entry_status = '.$db->quote('Confirmed').' OR es.entry_status = '.$db->quote('Confirmed & Paid').')');

							//echo nl2br(str_replace('#__', 'j35_', $query));die;
							$db->setQuery($query);
							$am_placeholders = $db->loadResult();

							if(isset($am_totals[$show_days[$i]->show_day_id]))
								$am_totals[$show_days[$i]->show_day_id] += $am_placeholders;
							else
								$am_totals[$show_days[$i]->show_day_id] = $am_placeholders;

							$query = $db->getQuery(true);

							$query->select('count(pd.placeholder_day_showday)');
							$query->from('#__toes_placeholder_day AS pd');
							$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
							$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
							$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
							$query->where('p.placeholder_show = ' . $data->show_id);
							$query->where('pd.placeholder_participates_PM = 1');
							$query->where('pd.placeholder_day_showday = ' . $show_days[$i]->show_day_id);
							$query->where('(es.entry_status = '.$db->quote('New').' OR es.entry_status = '.$db->quote('Accepted').' OR es.entry_status = '.$db->quote('Confirmed').' OR es.entry_status = '.$db->quote('Confirmed & Paid').')');

							//echo nl2br(str_replace('#__', 'j35_', $query));
							$db->setQuery($query);
							$pm_placeholders = $db->loadResult();

							if(isset($pm_totals[$show_days[$i]->show_day_id]))
								$pm_totals[$show_days[$i]->show_day_id] += $pm_placeholders;
							else
								$pm_totals[$show_days[$i]->show_day_id] = $pm_placeholders;

							if($am_placeholders || $pm_placeholders)
								$tbl .= '<td>'.$am_placeholders.'</td>'.'<td>'.$pm_placeholders.'</td>';
							else
								$tbl .= '<td>0</td>'.'<td>0</td>';
						}
						$tbl .= '</tr>';
						$tbl .= '<tr>';
						$tbl .= '<td>&nbsp;</td>' ;
						$tbl .= '</tr>';
						
						$tbl .= '<tr>';
						$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper(JText::_('COM_TOES_TOTALS')).'</td>';
						for($i = 0; $i < count($show_days); $i++)
						{
							$am_cnt = isset($am_totals[$show_days[$i]->show_day_id])?$am_totals[$show_days[$i]->show_day_id]:0;
							$pm_cnt = isset($pm_totals[$show_days[$i]->show_day_id])?$pm_totals[$show_days[$i]->show_day_id]:0;

							$tbl .= '<td>'.$am_cnt.'</td>'.'<td>'.$pm_cnt.'</td>';
						}
						$tbl .= '</tr>';
						$tbl .= '</table>';
					
					} else {

						$whr = array();
						$whr[] = "`e`.`entry_show` = {$data->show_id}";
						$query = TOESQueryHelper::getShowSummariesQuery($whr);
						//echo nl2br(str_replace('#__', 'j35_', $query));
						$db->setQuery($query);
						$summary = $db->loadObjectList();

						$final_summary = array();
						foreach($summary as $smry)
						{
							$final_summary[$smry->show_class][$smry->show_day_id] = $smry->cat_count;
						}

						$tbl = '';
						$totals = array();

						$tbl .= '<table style="text-align:center;">';

						$tbl .= '<tr>';
						$tbl .= '<td style="width:30%;" >&nbsp;</td>';
						for($i = 0; $i < count($show_days); $i++)
						{
							$tbl .= '<td style="width:20%;">'.date('l',strtotime($show_days[$i]->show_day_date)).'</td>';
						}
						$tbl .= '</tr>';
						
						foreach($showClasses as $show_group)
						{
							$cnt_total = array();

							$show_class_count = 0;
							foreach($show_group as $show_class)
							{
								if(isset($final_summary[$show_class]) && $final_summary[$show_class])
									$show_day_count = $final_summary[$show_class];
								else
									$show_day_count ='';

								if($show_day_count)
								{
									$tbl .= '<tr>';
									$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper($show_class).'</td>';
									for($i = 0; $i < count($show_days); $i++)
									{
										$cnt = isset($show_day_count[$show_days[$i]->show_day_id])?$show_day_count[$show_days[$i]->show_day_id]:0;

										$show_class_count += $cnt;

										if(isset($cnt_total[$show_days[$i]->show_day_id]))
											$cnt_total[$show_days[$i]->show_day_id] += $cnt;
										else
											$cnt_total[$show_days[$i]->show_day_id] = $cnt;

										if(isset($totals[$show_days[$i]->show_day_id]))
											$totals[$show_days[$i]->show_day_id] += $cnt;
										else
											$totals[$show_days[$i]->show_day_id] = $cnt;

										$tbl .= '<td>'.$cnt.'</td>' ;
									}
									$tbl .= '</tr>';
								}
							}

							if($show_class != 'Ex Only')
							{
								if($show_class_count)
								{
									$tbl .= '<tr>';
									$tbl .= '<td style="width:30%;text-align:left;" >AB '.strtoupper(str_replace('SH ', '', $show_class)).'</td>';
									for($i = 0; $i < count($show_days); $i++)
									{
										$cnt = isset($cnt_total[$show_days[$i]->show_day_id])?$cnt_total[$show_days[$i]->show_day_id]:0;

										$tbl .= '<td>'.$cnt.'</td>' ;
									}
									$tbl .= '</tr>';
								}
							}
							if($show_class_count)
							{
								$tbl .= '<tr>';
								$tbl .= '<td>&nbsp;</td>' ;
								$tbl .= '</tr>';
							}
						}
	
						$whr = array();
						$whr[] = "ring_show = {$data->show_id}";
						$query = TOESQueryHelper::getCongressSummaryQuery($whr);
						$db->setQuery($query);
						$congress_summary = $db->loadObjectList();
						
						$final_congress_summary = array();
						foreach($congress_summary as $smry)
						{
							$final_congress_summary[$smry->ring_name][$smry->ring_show_day] = $smry->Count;
						}

						foreach($final_congress_summary as $ring_name=>$smry)
						{
							$tbl .= '<tr>';
							$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper($ring_name).'</td>';
							for($i = 0; $i < count($show_days); $i++)
							{
								if(isset($smry[$show_days[$i]->show_day_id]))
									$tbl .= '<td>'.$smry[$show_days[$i]->show_day_id].'</td>' ;
								else
									$tbl .= '<td> - </td>';

							}
							$tbl .= '</tr>';
						}
						if($congress_summary)
						{
							$tbl .= '<tr>';
							$tbl .= '<td>&nbsp;</td>' ;
							$tbl .= '</tr>';
						}

						$tbl .= '<tr>';
						$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper(JText::_('COM_TOES_PLACEHOLDERS')).'</td>';
						for($i = 0; $i < count($show_days); $i++)
						{
							$query = $db->getQuery(true);

							$query->select('count(pd.placeholder_day_showday)');
							$query->from('#__toes_placeholder_day AS pd');
							$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
							$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
							$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
							$query->where('p.placeholder_show = ' . $data->show_id);
							$query->where('pd.placeholder_day_showday = ' . $show_days[$i]->show_day_id);
							$query->where('(es.entry_status = '.$db->quote('New').' OR es.entry_status = '.$db->quote('Accepted').' OR es.entry_status = '.$db->quote('Confirmed').' OR es.entry_status = '.$db->quote('Confirmed & Paid').')');

							//echo nl2br(str_replace('#__', 'j35_', $query));
							$db->setQuery($query);
							$placeholders = $db->loadResult();

							if(isset($totals[$show_days[$i]->show_day_id]))
								$totals[$show_days[$i]->show_day_id] += $placeholders;
							else
								$totals[$show_days[$i]->show_day_id] = $placeholders;

							if($placeholders)
								$tbl .= '<td>'.$placeholders.'</td>' ;
							else
								$tbl .= '<td>0</td>' ;
						}
						$tbl .= '</tr>';
						$tbl .= '<tr>';
						$tbl .= '<td>&nbsp;</td>' ;
						$tbl .= '</tr>';
						
						$tbl .= '<tr>';
						$tbl .= '<td style="width:30%;text-align:left;" >'.strtoupper(JText::_('COM_TOES_TOTALS')).'</td>';
						for($i = 0; $i < count($show_days); $i++)
						{
							$cnt = isset($totals[$show_days[$i]->show_day_id])?$totals[$show_days[$i]->show_day_id]:0;

							$tbl .= '<td>'.$cnt.'</td>' ;
						}

						$tbl .= '</tr>';
						$tbl .= '</table>';
					}
					echo $tbl;
				?>
            </span>
        </div>
		<?php endif; ?>
        
        <br/>
        <?php if($user->id): ?>
			<?php if($data->show_uses_toes != '1'): ?>
				<div class="block">
					<?php echo JText::_('COM_TOES_SHOW_NOT_USING_TOES'); ?>
				</div>
			<?php elseif($user->authorise('toes.access_entry_block_of_not_opened_show','com_toes') || ($show_status != 'Planned' && $show_status != 'Approved')): ?>
				<div class="entries" style="border:1px solid;">
					<div class="block">
						<div class="details">
							<label class="full-length"><?php echo JText::_('COM_TOES_SHOW_CURRENT_ENTRIES'); ?></label>
							<div class="entries">
								<div class="show-entries-header">
									<span class="action-buttons">&nbsp;</span>
									<span class="name"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_NAME_HEADER');?></span>
									<span class="status"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_STATUS_HEADER');?></span>
									<span class="days"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_DAYS_HEADER');?></span>
									<span class="class"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CLASS_HEADER');?></span>
									<span class="congress"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_CONGRESS_HEADER');?></span>
									<span class="exh"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_EXH_HEADER');?></span>
									<span class="forsale"><?php echo JText::_('COM_TOES_SHOW_ENTRIES_FORSALE_HEADER');?></span>
								</div>
								<div class="clr"><hr/></div>
								<?php
								
								$allow_edit_summary = false;
								
								if (@$data->entries) {
									$prev_cat='';
									$prev_entry_status = '';
								?>
									<?php
									foreach ($data->entries as $entry) {
										if($is_continuous)
											$days = JText::_('JALL');
										else
											$days = $entry->showdays;

										if($entry->congress)
											$congress_names = $entry->congress;
										else
											$congress_names = '-';
										
										if($entry->entry_status == 'New' || $entry->entry_status == 'Rejected' || $entry->entry_status == 'Waiting List') {
											$allow_edit_summary = true;
										}

									?>
										<div class="item <?php echo ($entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed')?'grey_entry':''?>">
											<span class="action-buttons">
												&nbsp;
												<?php if($entry->cat != $prev_cat || $prev_entry_status != $entry->entry_status) :?>
													<span class="hasTip" title="<?php echo JText::_('VIEW_DETAILS');?>">
														<a href="javascript:void(0)" onclick="view_entry_details(this,'cat-details-<?php echo $entry->entry_id;?>',1);" rel="<?php echo $entry->entry_id; ?>" class="view-entry-details">
															<i class="fa fa-file-text-o"></i>
														</a>
													</span>
													<?php if( $data->show_allow_exhibitor_cancellation || (!$data->show_allow_exhibitor_cancellation && $entry->entry_status == 'New') ): ?>
														<?php if($show_status == 'Open' && ($entry->entry_status != 'Cancelled' && $entry->entry_status != 'Cancelled & Confirmed') ): ?>
															<span class="hasTip" title="<?php echo JText::_('CANCEL_ENTRY');?>">
																<a href="javascript:void(0)" rel="<?php echo $entry->entry_id.';'.$entry->show_id; ?>" class="cancel-entry" onclick="cancel_entry(this,'<?php echo ($entry->entry_status == 'New')?JText::_('COM_TOES_CONFIRM_TO_CANCEL_ENTRY'):JText::_('COM_TOES_EXHIBITOR_CONFIRM_TO_CANCEL_ENTRY');?>');">
																	<i class="fa fa-remove"></i>
																</a>
															</span>
														<?php endif; ?>
													<?php endif; ?>
													<?php if($show_status == 'Open' && ($entry->entry_status == 'Rejected' || $entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed') ): ?>
														<span class="hasTip" title="<?php echo JText::_('REENTER_ENTRY');?>">
															<a href="javascript:void(0)" rel="<?php echo $entry->entry_id.';'.$entry->show_id; ?>" class="reenter-entry" onclick="reenter_entry(this);">
																<i class="fa fa-power-off"></i>
															</a>
														</span>
													<?php endif; ?>
													<?php if($show_status == 'Open' && ($entry->entry_status == 'New' || $entry->entry_status == 'Rejected' || $entry->entry_status == 'Waiting List') ) : ?>
														<span class="hasTip" title="<?php echo JText::_('EDIT_ENTRY');?>">
															<a href="javascript:void(0)" onclick="edit_entry('<?php echo $entry->cat.';'.$entry->show_id.';'.$entry->summary_user; ?>','add-entry-div-<?php echo $user_id; ?>');" class="edit-entry">
																<i class="fa fa-edit"></i>
															</a>
														</span>
													<?php endif; ?>
													<?php
														$prev_cat = $entry->cat;
														$prev_entry_status = $entry->entry_status;
													?>
												<?php endif; ?>
											</span>
											<span class="name"><?php echo $entry->cat_prefix_abbreviation.' '.$entry->cat_title_abbreviation.' '.$entry->copy_cat_name.' '.$entry->cat_suffix_abbreviation;?></span>
											<span class="status"><?php echo $entry->entry_status;?></span>
											<span class="days">
												<?php echo $days;?>
											</span>
											<span class="class"><?php echo $entry->Show_Class;?></span>
											<span class="congress"><?php echo $congress_names;?></span>
											<span class="exh"><?php echo ($entry->exhibition_only)?JText::_('JYES'):JText::_('JNO');?></span>
											<span class="forsale"><?php echo ($entry->for_sale)?JText::_('JYES'):JText::_('JNO');?></span>
										</div>
										<div class="cat-details clr" id="cat-details-<?php echo $entry->entry_id;?>" style="display: none;"></div>
									<?php
									}
								}

								if(@$data->entries && @$data->placeholders)
								{
								?>
									<div class="clr" style="padding: 0; border-bottom: 1px dashed #000;"></div>
								<?php
								}

								if(@$data->placeholders)
								{
									$prev_placeholder = '';
									$prev_placeholder_status = '';
									foreach ($data->placeholders as $placeholder) {
										if($is_continuous)
											$days = JText::_('JALL');
										else
											$days = $placeholder->showdays;
										
										if($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Waiting List') {
											$allow_edit_summary = true;
										}

										?>
										<div class="item <?php echo ($placeholder->entry_status == 'Cancelled' || $placeholder->entry_status == 'Cancelled & Confirmed')?'grey_entry':''?>">
											<span class="action-buttons">
												&nbsp;
												<?php if($placeholder->placeholder_id != $prev_placeholder || $prev_placeholder_status != $placeholder->entry_status) :?>
													<?php if( $data->show_allow_exhibitor_cancellation || (!$data->show_allow_exhibitor_cancellation && $placeholder->entry_status == 'New') ): ?>
														<?php if($show_status == 'Open' && $placeholder->entry_status != 'Cancelled' && $placeholder->entry_status != 'Cancelled & Confirmed' ): ?>
															<?php if($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Waiting List'): ?>
																<span class="hasTip" title="<?php echo JText::_('DELETE_PLACEHOLDER');?>">
																	<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id.';'.$placeholder->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this,'<?php echo JText::_('COM_TOES_CONFIRM_TO_DELETE_PLACEHOLDER');?>');">
																		<i class="fa fa-trash"></i>
																	</a>
																</span>
															<?php else: ?>
																<span class="hasTip" title="<?php echo JText::_('CANCEL_PLACEHOLDER');?>">
																	<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id.';'.$placeholder->placeholder_show; ?>" class="cancel-placeholder" onclick="cancel_placeholder(this,'<?php echo JText::_('COM_TOES_CONFIRM_TO_REJECT_PLACEHOLDER');?>');">
																		<i class="fa fa-remove"></i>
																	</a>
																</span>
															<?php endif; ?>
														<?php endif; ?>
													<?php endif; ?>
													<?php if($show_status == 'Open' && ($placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Cancelled' || $placeholder->entry_status == 'Cancelled & Confirmed') ): ?>
														<span class="hasTip" title="<?php echo JText::_('REENTER_PLACEHOLDER');?>">
															<a href="javascript:void(0)" rel="<?php echo $placeholder->placeholder_day_id.';'.$placeholder->placeholder_show; ?>" class="reenter-placeholder" onclick="reenter_placeholder(this);">
																<i class="fa fa-power-off"></i>
															</a>
														</span>
													<?php endif; ?>
													<?php if($show_status == 'Open' && ($placeholder->entry_status == 'New' || $placeholder->entry_status == 'Rejected' || $placeholder->entry_status == 'Waiting List') ) : ?>
														<span class="hasTip" title="<?php echo JText::_('EDIT_PLACEHOLDER');?>">
															<a href="javascript:void(0)" onclick="edit_placeholder('<?php echo $placeholder->placeholder_id; ?>','<?php echo $user_id;?>','add-placeholder-div-<?php echo $user_id; ?>');" class="edit-placeholder">
																<i class="fa fa-edit"></i>
															</a>
														</span>
													<?php endif; ?>
													<?php if(($show_status == 'Open' && $placeholder->entry_status == 'New') || $placeholder->entry_status == 'Accepted' || $placeholder->entry_status == 'Confirmed' || $placeholder->entry_status == 'Confirmed & Paid') : ?>
														<span class="hasTip" title="<?php echo JText::_('CONVERT_TO_ENTRY');?>">
															<a href="javascript:void(0)" onclick="convert_placeholder('<?php echo $placeholder->placeholder_id; ?>','<?php echo $placeholder->placeholder_show; ?>','<?php echo $user_id;?>','add-placeholder-div-<?php echo $user_id; ?>');" class="convert-placeholder">
																<i class="fa fa-refresh"></i>
															</a>
														</span>
													<?php endif; ?>
													<?php
														$prev_placeholder = $placeholder->placeholder_id;
														$prev_placeholder_status = $placeholder->entry_status;
													?>
												<?php endif; ?>
											</span>
											<span class="name"><?php echo JText::_('COM_TOES_PLACEHOLDER');?></span>
											<span class="status"><?php echo $placeholder->entry_status;?></span>
											<span class="days"><?php echo $days;?></span>
											<span class="class">&nbsp;</span>
											<span class="congress">&nbsp;</span>
											<span class="exh">&nbsp;</span>
											<span class="forsale">&nbsp;</span>
										</div>
										<div class="clr" style="padding: 0;"></div>
									<?php
									}
								}
								?>
							</div>
						</div>
						<div class="clr"></div>
						<?php if($data->show_uses_toes != '1'): ?>
							<?php echo JText::_('COM_TOES_SHOW_NOT_USING_TOES'); ?>
						<?php else: ?>
							<?php
							if( ($isAdmin && $params->get('show_add_entry_for_admin')) || ($data->show_start_date >= date('Y-m-d') && $show_status == 'Open')) : ?>
								<?php /* if(TOESHelper::isShowHasSpace($data->show_id)) : */ ?>
									<input type="hidden" id="add_user" name="add_user" value="<?php echo $user_id;?>" />
									<div class="add-entry-div" id="add-entry-div-<?php echo $user_id;?>">
										<a href="javascript:void(0);" onclick="add_new_entry(<?php echo $data->show_id;?>,'add-entry-div-<?php echo $user_id; ?>','new');">
											<i class="fa fa-plus-circle"></i>
											<?php echo JText::_('COM_TOES_ADD_CAT'); ?>
										</a>
									</div>
									<?php if($isAdmin || TOESHelper::is_entryclerk($user->id, $data->show_id) || TOESHelper::is_showmanager($user->id, $data->show_id)): ?>
										<div class="add-entry-div" >
											<a href="javascript:void(0);" onclick="add_third_party_entry(<?php echo $data->show_id;?>,'add-entry-div-<?php echo $user_id; ?>');">
												<i class="fa fa-plus-circle"></i>
												<?php echo JText::_('COM_TOES_ADD_THIRD_PARTY_ENTRY'); ?>
											</a>
										</div>
									<?php endif; ?>
									<div class="add-placeholder-div" id="add-placeholder-div-<?php echo $user_id;?>">
										<a href="javascript:void(0);" onclick="add_new_placeholder(<?php echo $data->show_id;?>,'add-placeholder-div-<?php echo $user_id; ?>');">
											<i class="fa fa-plus-circle"></i>
											<?php echo JText::_('COM_TOES_ADD_PLACEHOLDER'); ?>
										</a>
									</div>
									<?php if($isAdmin || TOESHelper::is_entryclerk($user->id, $data->show_id) || TOESHelper::is_showmanager($user->id, $data->show_id)): ?>
									<div class="add-placeholder-div">
										<a href="javascript:void(0);" onclick="add_third_party_placeholder(<?php echo $data->show_id;?>,'add-placeholder-div-<?php echo $user_id; ?>');">
											<i class="fa fa-plus-circle"></i>
											<?php echo JText::_('COM_TOES_ADD_PLACEHOLDER_FOR_THIRD_PARTY'); ?>
										</a>
									</div>
									<?php endif; ?>
								<?php /* else : ?>
									<div style="padding:10px;">
										<?php echo JText::_("COM_TOES_SHOW_IS_FULL");?>
									</div>
								<?php endif; */ ?>
							<?php elseif($data->show_start_date < date('Y-m-d')): ?>		
								<?php if($isAdmin || TOESHelper::is_entryclerk($user->id, $data->show_id) || TOESHelper::is_showmanager($user->id, $data->show_id)): ?>
									<div class="add-entry-div" id="add-entry-div-<?php echo $user_id;?>">
										<a href="javascript:void(0);" onclick="add_third_party_entry(<?php echo $data->show_id;?>,'add-entry-div-<?php echo $user_id; ?>');">
											<i class="fa fa-plus-circle"></i>
											<?php echo JText::_('COM_TOES_ADD_THIRD_PARTY_ENTRY'); ?>
										</a>
									</div>
									<div class="add-placeholder-div" id="add-placeholder-div-<?php echo $user_id;?>">
										<a href="javascript:void(0);" onclick="add_third_party_placeholder(<?php echo $data->show_id;?>,'add-placeholder-div-<?php echo $user_id; ?>');">
											<i class="fa fa-plus-circle"></i>
											<?php echo JText::_('COM_TOES_ADD_PLACEHOLDER_FOR_THIRD_PARTY'); ?>
										</a>
									</div>
								<?php endif; ?>
							<?php elseif($show_status == 'Planned' || $show_status == 'Approved'): ?>
								<div style="padding:10px;">
									<?php echo JText::_("COM_TOES_SHOW_NOT_OPEN");?>
								</div>
							<?php elseif($show_status == 'Closed'): ?>
								<div style="padding:10px;">
									<?php echo JText::_("COM_TOES_SHOW_IS_CLOSED");?>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<?php if(@$data->entries && isset($data->summary)) : ?>
					<div class ="block">
						<div class="details">
							<label class="full-length"><?php echo JText::_('COM_TOES_SHOW_SUMMARY'); ?></label>
							<div class="summary-details">
								<div>
									<label style="font-weight:normal;"><?php echo ( $data->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_SINGLE_CAGES') );?></label>
										<span><?php echo $data->summary->summary_single_cages;?></span>
								</div>
								<div>
									<label style="font-weight:normal;"><?php echo ( $data->show_bring_your_own_cages ? JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_SPACES') : JText::_('COM_TOES_SHOW_SUMMARY_NUMBER_OF_DOUBLE_CAGES') );?></label>
									<span><?php echo $data->summary->summary_double_cages;?></span>
								</div>
								<div>
									<label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_PERSONAL_CAGES');?></label>
									<span><?php echo $data->summary->summary_personal_cages?JText::_('JYES'):JText::_('JNO');?></span>
								</div>
								<div>
									<label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_GROOMING_SPACE');?></label>
									<span><?php echo $data->summary->summary_grooming_space?JText::_('JYES'):JText::_('JNO');?></span>
								</div>
								<div>
									<label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_BENCHING_REQUEST');?></label>
									<span><?php echo $data->summary->summary_benching_request;?></span>
								</div>
								<div>
									<label style="font-weight:normal;"><?php echo JText::_('COM_TOES_SHOW_SUMMARY_REMARKS');?></label>
									<span><?php echo $data->summary->summary_remarks;?></span>
								</div>

							</div>
						</div>
						<div class="clr"></div>
						<?php if($allow_edit_summary) : ?>
							<a href="javascript:void(0);" onclick="edit_summary(<?php echo $data->summary->summary_id;?>);">
								<i class="fa fa-edit"></i>
								<?php echo JText::_('COM_TOES_EDIT_SUMMARY'); ?>
							</a>
							<div class="edit-summary-div" id="edit-summary-<?php echo $data->summary->summary_id;?>-div">
							</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		<?php else:
			  if($data->show_uses_toes == '1')
			  echo '<div class="block">'.JText::_('COM_TOES_NOT_LOGGED_IN_LOG_IN_FOR_MORE').'</div>' ;
			
			  endif; ?>

        <div class="entry-clerk">
            <label>
                <?php echo JText::_('COM_TOES_SHOW_ENTRYCLERK') . ':'; ?>
            </label>
            <?php foreach($data->entryclerks as $entryclerk): ?>
            <span>
                <?php
                if($entryclerk->entry_clerk_name)
                    echo $entryclerk->entry_clerk_name . '<br/>';
                /*
                if ($entryclerk->entry_clerk_address_line_1)
                    echo $entryclerk->entry_clerk_address_line_1. '<br/>';
                if ($entryclerk->entry_clerk_address_line_2)
                    echo $entryclerk->entry_clerk_address_line_2. '<br/>';
                if ($entryclerk->entry_clerk_address_line_3)
                    echo $entryclerk->entry_clerk_address_line_3. '<br/>';
                if ($entryclerk->entry_clerk_address_city)
                    echo $entryclerk->entry_clerk_address_city.', ';
                if ($entryclerk->entry_clerk_address_state)
                    echo $entryclerk->entry_clerk_address_state;
                if ($entryclerk->entry_clerk_address_zip_code)
                    echo ' ' . $entryclerk->entry_clerk_address_zip_code . '<br/>';
                if($entryclerk->entry_clerk_address_country)
                    echo $entryclerk->entry_clerk_address_country . '<br/>';
                */

                if($data->show_use_club_entry_clerk_address)
                    echo '<a href="mailto:'.$data->show_email_address_entry_clerk.'">'.$data->show_email_address_entry_clerk . '</a><br/>';
                elseif($entryclerk->entry_clerk_email)
                    echo '<a href="mailto:'.$entryclerk->entry_clerk_email.'">'.$entryclerk->entry_clerk_email . '</a><br/>';
                
                if(!$entryclerk->private && $entryclerk->entry_clerk_phone_number)
                {
                    echo JText::_('COM_TOES_PHONE').': ';
                    /*if($entryclerk->entry_clerk_phone_international_access_code)
                        echo $entryclerk->entry_clerk_phone_international_access_code.' ';
                    if($entryclerk->entry_clerk_phone_area_code)
                        echo $entryclerk->entry_clerk_phone_area_code.' ';*/
                    echo $entryclerk->entry_clerk_phone_number . '<br/>';
                }
                ?>
            </span>
            <?php endforeach; ?>
        </div>
        <div class="show-manager">
            <label>
                <?php echo JText::_('COM_TOES_SHOW_SHOWMANAGER') . ':'; ?>
            </label>
            <?php foreach($data->showmanagers as $showmanger):?>
            <span>
                <?php
                    if($showmanger->show_manager_name)
                        echo $showmanger->show_manager_name.'<br/>';
                    
                    if($data->show_use_club_show_manager_address)
                        echo '<a href="mailto:'.$data->show_email_address_show_manager.'">'.$data->show_email_address_show_manager.'</a><br/>';
                    elseif($showmanger->show_manager_email)
                        echo '<a href="mailto:'.$showmanger->show_manager_email.'">'.$showmanger->show_manager_email.'</a><br/>';
                    
                    if(!$showmanger->private && $showmanger->show_manager_phone_number)
                    {
                        echo JText::_('COM_TOES_PHONE').': ';
                        echo $showmanger->show_manager_phone_number . '<br/>';
                    }
                ?>
            </span>
            <?php endforeach; ?>
        </div>
        <div class="motto">
            <label>
                <?php echo JText::_('COM_TOES_SHOW_MOTTO') . ':'; ?>
            </label>
            <span>
                <?php echo $data->show_motto;?>
            </span>
        </div>
        <div class="flyer">
            <label>
                <?php echo JText::_('COM_TOES_SHOW_FLYER') . ':'; ?>
            </label>
            <span>
                <a target="_blank" href="<?php echo TOESHelper::addhttp($data->show_flyer);?>" >
                    <?php echo $data->show_flyer;?>
                </a>
            </span>
        </div>
    </div>
</div>
<div style="display:none" id="copy_show_<?php echo $data->show_id?>">
<form method="post" id="copyform" action="<?php echo JURI::root()?>index.php?option=com_toes&task=show.copy&show_id=<?php echo $data->show_id?>&tmpl=component">


 <p>Start Date of New Show: <input name="start_date" id="start_date" type="text" id="datepicker" value="<?php echo $data->show_start_date?>" ></p>
 <p id="start_date_formatted"></p>
<?php /*
<p>
<input type="button" class="btn" value="Copy" onclick="copytheshow(<?php echo $data->show_id?>)" >
<input type="button" class="btn" value="Cancel" onclick="cancelcopy()" >
</p>
*/ ?> 

<input type="hidden"  value="=<?php echo $data->show_id?>" name="show_id" id="copy_show_id">
<input type="hidden"  value="com_toes" name="option" >
<input type="hidden"  value="show.copy" name="task" >
 

</form>
</div>
