<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template styles list controller class.
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESControllerEntryclerk extends JControllerAdmin {

	/**
	 * Proxy for getModel.
	 *
	 */
	public function getModel($name = 'entryclerk', $prefix = 'ToesModel', $config = array()) {
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	public function getExibitorListPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getExibitorListPDF($show_id);
		$app->close();
	}

	public function getExhibitorInfoEXL() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getExhibitorInfoEXL($show_id);
		$app->close();
	}

	public function getExhibitorLabelsEXL() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getExhibitorLabelsEXL($show_id);
		$app->close();
	}

	public function getExhibitorCardsPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getExhibitorCardsPDF($show_id);
		$app->close();
	}

	public function getMicrochipListPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getMicrochipListPDF($show_id);
		$app->close();
	}

	public function getLateExibitorListPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getLateExibitorListPDF($show_id);
		$app->close();
	}

	public function getMasterExibitorListPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getMasterExibitorListPDF($show_id);
		$app->close();
	}

	public function getMasterExibitorWOAListPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getMasterExibitorWOAListPDF($show_id);
		$app->close();
	}

	public function getLateMasterExibitorListPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getLateMasterExibitorListPDF($show_id);
		$app->close();
	}

	public function getSummaryPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getSummaryPDF($show_id);
		$app->close();
	}

	public function getSchedulingSummaryPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getSchedulingSummaryPDF($show_id);
		$app->close();
	}

	public function getCatalogPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getCatalogPDF($show_id);
		$app->close();
	}

	public function getLatePagesPDF() {
		//JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getLatePagesPDF($show_id);
		$app->close();
	}

	public function getMasterCatalogPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getMasterCatalogPDF($show_id);
		$app->close();
	}

	// spidderweb commented
	public function getMarkedCatalogPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getMarkedCatalogPDF($show_id);
		$app->close();
	}

	public function getMasterCatalogEXL() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getMasterCatalogEXL($show_id);
		$app->close();
	}

	public function getJudgesBookPDF() {
		//JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getJudgesBookPDF($show_id);
		$app->close();
	}

	public function getPreJudgesBookPDF() {
		
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getPreJudgesBookPDF($show_id);
		$app->close();
	}

	public function getCongressJudgesBookPDF() {
		//JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getCongressJudgesBookPDF($show_id);
		$app->close();
	}

	public function getPreCongressJudgesBookPDF() {
		//JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getPreCongressJudgesBookPDF($show_id);
		$app->close();
	}

	public function updateStatus() {
		$app = JFactory::getApplication();
		$status = $app->input->getVar('status');
		$entry_id = $app->input->getInt('entry_id');

		$model = $this->getModel();

		if ($model->updateStatus($entry_id, $status)) {
			echo 1;
		} else {
			echo $model->getError();
		}
		$app->close();
	}

	function confirmEntries() {
		$app = JFactory::getApplication();
		$user_id = $app->input->getInt('user_id');
		$show_id = $app->input->getInt('show_id');

		$resend = $app->input->getInt('resend');

		$model = $this->getModel();

		if ($model->confirmEntries($user_id, $show_id, ($resend ? false : true))) {
			echo 1;
		} else {
			echo $model->getError();
		}
		$app->close();
	}

	public function runCatalog() {
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('id');

		$model = $this->getModel();
		if ($model->runCatalog($show_id)) {
			echo 1;
		} else {
			echo $model->getError();
		}
		$app->close();
	}

	public function latePages() {
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('id');

		$model = $this->getModel();
		if ($model->latePages($show_id)) {
			echo 1;
		} else {
			echo $model->getError();
		}
		$app->close();
	}

	public function checkProgress() {
		$app = JFactory::getApplication();

		$file = $app->input->getVar('file', '');
		$fp = fopen(TOES_LOG_PATH . DS . $file . '.txt', 'r');
		$str = fgets($fp);
		$data = unserialize($str);
		if(!isset($data['log'])) {
			$data['log'] = '';
		}
		fclose($fp);
		echo 'T:' . $data['total'] . ';P:' . $data['processed'].';L:'.$data['log'];

		$app->close();
	}

	public function unlinkLogFile() {
		$app = JFactory::getApplication();
		$file = $app->input->getVar('file', '');
		if(file_exists(TOES_LOG_PATH . DS . $file . '.txt')){
			unlink(TOES_LOG_PATH . DS . $file . '.txt');
		}
		$app->close();
	}

	public function changePapersize() {
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');
		$paper_size = $app->input->getInt('paper_size');

		$model = $this->getModel();
		if ($model->changePapersize($show_id, $paper_size)) {
			echo 1;
		} else {
			echo $model->getError();
		}
		$app->close();
	}

	public function getTreasurerPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getTreasurerPDF($show_id);
		$app->close();
	}

	public function getTreasurerEXL() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getTreasurerEXL($show_id);
		$app->close();
	}

	public function getBenchingPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getBenchingPDF($show_id);
		$app->close();
	}

	public function updateChangestoEntry() {
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');
		$cat_id = $app->input->getInt('cat_id');

		$model = $this->getModel();

		if ($model->updateChangestoEntry($show_id, $cat_id)) {
			$isApplicable = TOESHelper::isApplicableForCongress($show_id, $cat_id);
			if ($isApplicable) {
				echo 'Congress: ' . $isApplicable;
			} else {
				echo '1';
			}
		} else {
			echo 'Error: ' . $model->getError();
		}
		$app->close();
	}

	public function participateInCongress() {
		$app = JFactory::getApplication();
		$congress_id = $app->input->getVar('congress_id');
		$show_id = $app->input->getInt('show_id');
		$cat_id = $app->input->getInt('cat_id');

		$model = $this->getModel();

		if ($model->participateInCongress($show_id, $cat_id, $congress_id)) {
			echo '1';
		} else {
			echo $model->getError();
		}
		$app->close();
	}

	public function getChangestoEntries() {
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');
		$filter_entry_type = $app->input->getInt('filter_entry_type');

		$show = TOESHelper::getShowDetails($show_id);

		$block = '<div class="block">';

		$entries = '';
		if ($filter_entry_type != 2) {
			$entries = TOESHelper::getShowEntries($show_id);
		}

		if ($entries) {
			$prev_cat = 0;
			$cat_count = 0;
			foreach ($entries as $entry) {

				if ($entry->cat == $prev_cat) {
					continue;
				}

				if ($entry->entry_status == 'Cancelled' || $entry->entry_status == 'Cancelled & Confirmed') {
					continue;
				}

				$diff = array();
				$cat_details = TOESHelper::getCatDetails($entry->cat);

				if ($entry->copy_cat_breed != $cat_details->cat_breed) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_BREED');
					$field->entry_value = $entry->breed_name;
					$field->cat_value = $cat_details->breed_name;
					$diff[] = $field;
				}

				if ($entry->copy_cat_new_trait != $cat_details->cat_new_trait) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_NEW_TRAIT');
					$field->entry_value = $entry->copy_cat_new_trait ? JText::_('JYES') : JText::_('JNO');
					$field->cat_value = $cat_details->cat_new_trait ? JText::_('JYES') : JText::_('JNO');
					$diff[] = $field;
				}

				if ($entry->copy_cat_category != $cat_details->cat_category) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_CATEGORY');
					$field->entry_value = $entry->category;
					$field->cat_value = $cat_details->category;
					$diff[] = $field;
				}

				if ($entry->copy_cat_division != $cat_details->cat_division) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_DIVISION');
					$field->entry_value = $entry->division_name;
					$field->cat_value = $cat_details->division_name;
					$diff[] = $field;
				}

				if ($entry->copy_cat_color != $cat_details->cat_color) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_COLOR');
					$field->entry_value = $entry->color_name;
					$field->cat_value = $cat_details->color_name;
					$diff[] = $field;
				}

				if ($entry->copy_cat_name != $cat_details->cat_name) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_NAME');
					$field->entry_value = $entry->copy_cat_name;
					$field->cat_value = $cat_details->cat_name;
					$diff[] = $field;
				}

				if ($entry->copy_cat_gender != $cat_details->cat_gender) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_GENDER');
					$field->entry_value = $entry->gender_short_name;
					$field->cat_value = $cat_details->gender_short_name;
					$diff[] = $field;
				}

				if ($entry->copy_cat_registration_number != $cat_details->cat_registration_number) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_REGISTRATION_NUMBER');
					$field->entry_value = strtoupper($entry->copy_cat_registration_number);
					$field->cat_value = strtoupper($cat_details->cat_registration_number);
					$diff[] = $field;
				}

				if ($entry->copy_cat_date_of_birth != $cat_details->cat_date_of_birth) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_DATE_OF_BIRTH');
					$field->entry_value = $entry->copy_cat_date_of_birth;
					$field->cat_value = $cat_details->cat_date_of_birth;
					$diff[] = $field;
				}

				if ($entry->copy_cat_prefix != $cat_details->cat_prefix) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_PREFIX');
					$field->entry_value = $entry->cat_prefix_abbreviation;
					$field->cat_value = $cat_details->cat_prefix_abbreviation;
					$diff[] = $field;
				}

				if ($entry->copy_cat_title != $cat_details->cat_title) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_TITLE');
					$field->entry_value = $entry->cat_title_abbreviation;
					$field->cat_value = $cat_details->cat_title_abbreviation;
					$diff[] = $field;
				}

				if ($entry->copy_cat_suffix != $cat_details->cat_suffix) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_SUFFIX');
					$field->entry_value = $entry->cat_suffix_abbreviation;
					$field->cat_value = $cat_details->cat_suffix_abbreviation;
					$diff[] = $field;
				}

				if ($entry->copy_cat_sire_name != $cat_details->cat_sire) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_SIRE');
					$field->entry_value = $entry->copy_cat_sire_name;
					$field->cat_value = $cat_details->cat_sire;
					$diff[] = $field;
				}

				if ($entry->copy_cat_dam_name != $cat_details->cat_dam) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_DAM');
					$field->entry_value = $entry->copy_cat_dam_name;
					$field->cat_value = $cat_details->cat_dam;
					$diff[] = $field;
				}

				if ($entry->copy_cat_breeder_name != $cat_details->cat_breeder) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_BREEDER');
					$field->entry_value = $entry->copy_cat_breeder_name;
					$field->cat_value = $cat_details->cat_breeder;
					$diff[] = $field;
				}

				if ($entry->copy_cat_owner_name != $cat_details->cat_owner) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_OWNER');
					$field->entry_value = $entry->copy_cat_owner_name;
					$field->cat_value = $cat_details->cat_owner;
					$diff[] = $field;
				}

				if ($entry->copy_cat_lessee_name != $cat_details->cat_lessee) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_LESSEE');
					$field->entry_value = $entry->copy_cat_lessee_name;
					$field->cat_value = $cat_details->cat_lessee;
					$diff[] = $field;
				}

				if ($entry->copy_cat_competitive_region != $cat_details->cat_competitive_region) {
					$field = new stdClass();
					$field->field = JText::_('COM_TOES_COMPETITIVE_REGION');
					$field->entry_value = $entry->competitive_region_abbreviation;
					$field->cat_value = $cat_details->competitive_region_abbreviation;
					$diff[] = $field;
				}

				if (!$diff)
					continue;

				$block .= '<br/>';
				$block .= '<div id="entry-cat-diff-' . $entry->cat . '">';
				$block .= '<table style="width:98%;">';
				$block .= '<tr>';
				$block .= '<td style="border:1px solid #000;padding-left:5px;font-weight: bold;vertical-align: top;width:27%;" rowspan="' . (count($diff) + 1) . '">';
				$block .= $entry->copy_cat_name;
				$block .= '</td>';
				$block .= '<td style="border:1px solid #000;padding-left:5px;font-weight: bold;width:10%;" >';
				$block .= JText::_('COM_TOES_FIELD');
				$block .= '</td>';
				$block .= '<td style="border:1px solid #000;padding-left:5px;font-weight: bold;width:28%;" >';
				$block .= JText::_('COM_TOES_ENTRY_VALUE');
				$block .= '</td>';
				$block .= '<td style="border:1px solid #000;padding-left:5px;font-weight: bold;width:28%;" >';
				$block .= JText::_('COM_TOES_CAT_VALUE');
				$block .= '</td>';
				$block .= '<td style="padding-left:5px;vertical-align: top;width:5%;" rowspan="' . (count($diff) + 1) . '">';
				if ($show->show_status != 'Held') {
					$block .= '<input type="button" value="' . JText::_('COM_TOES_UPDATE_ENTRY') . '" onclick="this.set(\'disable\', 0); updateChangestoEntry(' . $show->show_id . ',' . $entry->cat . ', \'entry-cat-diff-' . $entry->cat . '\')" />';
				}
				$block .= '</td>';
				$block .= '</tr>';

				foreach ($diff as $item) {

					$block .= '<tr>';
					$block .= '<td style="border:1px solid #000;padding-left:5px;width:10%;" >';
					$block .= $item->field;
					$block .= '</td>';
					$block .= '<td style="border:1px solid #000;padding-left:5px;width:28%;" >';
					$block .= $item->entry_value;
					$block .= '</td>';
					$block .= '<td style="border:1px solid #000;padding-left:5px;width:28%;" >';
					$block .= $item->cat_value;
					$block .= '</td>';
					$block .= '</tr>';
				}

				$block .= '</table>';
				$block .= '<div class="clr"></div>';
				$block .= '</div>';

				$cat_count++;
				$prev_cat = $entry->cat;
			}

			if (!$cat_count) {
				$block .= JText::_('COM_TOES_NO_DIFF');
			}
		}
		$block .= '<div class="clr"></div> <br/> </div>';

		echo $block;
		$app->close();
	}

	// DOCX reports
	
	 

	public function getEntryclerkCheatsheetPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getEntryclerkCheatsheetPDF($show_id);
		$app->close();
	}

	public function getEntryclerkCheatsheetEXL() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getEntryclerkCheatsheetEXL($show_id);
		$app->close();
	}

	public function getAbsenteesPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getAbsenteesPDF($show_id);
		$app->close();
	}

	public function getCheckinSheetPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getCheckinSheetPDF($show_id);
		$app->close();
	}

	public function getBlankJudgesBookPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getBlankJudgesBookPDF($show_id);
		$app->close();
	}

	public function getExhibitorLabelDetailsEXL() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getExhibitorLabelDetailsEXL($show_id);
		$app->close();
	}

	public function getSpaceSummaryPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getSpaceSummaryPDF($show_id);
		$app->close();
	}

	public function getBenchingCardsPDF() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getBenchingCardsPDF($show_id);
		$app->close();
	}
	
	 

	public function getBlankJudgesBookFinalSheet() {
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getBlankJudgesBookFinalSheet($show_id);
		$app->close();
	}

	public function getJudgesBookInA4PDF() {
		//JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getJudgesBookInA4PDF($show_id);
		$app->close();
	}

	public function getCongressJudgesBookInA4PDF() {
		//JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getCongressJudgesBookInA4PDF($show_id);
		$app->close();
	}

	public function getAbsenteesEXL() {
		JSession::checkToken( 'get' ) or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$show_id = $app->input->getInt('show_id');

		$model = $this->getModel();

		$model->getAbsenteesEXL($show_id);
		$app->close();
	}
}
