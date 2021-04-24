<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
ini_set('display_error', 1);

/**
 * View to edit a template style.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESViewPlaceholder extends JViewLegacy {

    /**
     * Display the view
     */
    public function display($tpl = null) {

		$app = JFactory::getApplication();

        $step = $app->input->getVar('step');

        require_once JPATH_BASE . DS . 'components' . DS . 'com_toes' . DS . 'models' . DS . 'placeholder.php';
        $model = new ToesModelPlaceholder();
        
        switch ($step) {
            case 'step0':
                $this->regions = $model->getTicaRegions();
                break;
            case 'step1':
                $this->showdays = $model->getShowdays();
                break;
			case 'step2':
				$this->selected_showdays = $model->getSelectedShowday();
				$this->summary = $model->getSummary();
				break;
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }

}
