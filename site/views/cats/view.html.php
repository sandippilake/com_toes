<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Categories view class for the Category package.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESViewCats extends JViewLegacy {

    /**
     * Display the view
     */
    public function display($tpl = null) {

		$app = JFactory::getApplication();
        $user = JFactory::getUser();

        $owned_catids = TOESHelper::getUserCatRelations($user->id, 'Owner');
        $breeded_catids = TOESHelper::getUserCatRelations($user->id, 'Breeder');
        $lessee_catids = TOESHelper::getUserCatRelations($user->id, 'Lessee');
        $agented_catids = TOESHelper::getUserCatRelations($user->id, 'Agent');
       
        $this->datamycat = TOESHelper::getUserCats($user->id, 'Owner');
        $this->databred = TOESHelper::getUserCats($user->id, 'Breeder');
        $this->datalesseecat = TOESHelper::getUserCats($user->id, 'Lessee');
        $this->dataagentcat = TOESHelper::getUserCats($user->id, 'Agent');
        
        //$this->dataothercat = TOESHelper::getUserCats($user->id, 'Other');
	
        parent::display($tpl);
    }

}
