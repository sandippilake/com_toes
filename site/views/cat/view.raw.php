<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * View to edit a template style.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
jimport('joomla.application.component.view'); 
class TOESViewCat extends JViewLegacy {

    protected $item;

    /**
     * Display the view
     */
    public function display($tpl = null) { 
		$this->organizations = $this->get('organization');
		$this->document_type = $this->get('documenttype');
		/*
		$params = JComponentHelper::getParams('com_toes');
		$double_document_id = $params->get('double_document_id');
		if($this->document_type->allowed_registration_document_id == $double_document_id)
		$this->setLayout('double');
		else
		*/
		$this->setLayout('single');
		 
        parent::display($tpl);
    }
}
