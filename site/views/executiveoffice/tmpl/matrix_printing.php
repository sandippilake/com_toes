<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$app = JFactory::getApplication();
 
$show_id = $app->input->getInt('id');
$last_page = $app->input->getInt('last_page',1);
$download = $app->input->getInt('download',0);

if($this->pendingentries)
{
	$a_return = '';
	$a_style = 'style="color:#666666;"';
}
else
{
	$a_return = '';
	$a_style = '';
}
?>
<div id="print_option_box">
	<div id="pre-judge-print-options">
		<input type="radio" value="0" id="page_options_all" name="page_options" checked /> <?php echo JText::_('COM_TOES_ALL_PAGES');?> <br/>
		<input type="radio" value="1" id="page_options_x_end" name="page_options" /> <input type="text" size="3" value="1" name="page_options_1_x" id="page_options_1_x" style="width:auto;" /> <?php echo JText::_('COM_TOES_PAGES_TO_END');?> <br/>
		<input type="radio" value="2" id="page_options_x_y" name="page_options" /> <input type="text" size="3" value="1" name="page_options_2_x" id="page_options_2_x" style="width:auto;" /> <?php echo JText::_('COM_TOES_TO');?> <input type="text" size="3" value="<?php echo $last_page;?>" name="page_options_2_y" id="page_options_2_y" style="width:auto;" /> <br/>

		<?php if($download) : ?>
			<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>downloadPreJudgesBook(<?php echo $show_id; ?>);" >
				<img alt="<?php echo JText::_('COM_TOES_GENERATE_MATRIX_PRINTING_IN_FILE'); ?>" src="media/com_toes/images/judges_book32X32.png" />
				<?php echo JText::_('COM_TOES_GENERATE_MATRIX_PRINTING_IN_FILE'); ?>
			</a>
		<?php else: ?>
			<a <?php echo $a_style; ?> href="javascript:void(0);" onclick="<?php echo $a_return; ?>printPreJudgeFile(<?php echo $show_id; ?>);" >
				<img alt="<?php echo JText::_('COM_TOES_GENERATE_PRE_JUDGES_BOOK'); ?>" src="media/com_toes/images/judges_book32X32.png" />
				<?php echo JText::_('COM_TOES_PRINT_PRE_JUDGES_BOOK'); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
