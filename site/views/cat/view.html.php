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
class TOESViewCat extends JViewLegacy {

    protected $item;

    /**
     * Display the view
     */
    public function display($tpl = null) {
		require_once JPATH_COMPONENT.'/models/entry.php';
		$entry_model = new TOESModelEntry();
        
		$app = JFactory::getApplication();
		
		$user = JFactory::getUser();
        
        if(!$user->id)
        {
            JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
            $app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
        }
		
        $cat_id = $app->input->getInt('id');
        
        if($app->input->getVar('layout') == 'changes')
        {
        	
        	
        	
            $entry_id = $app->input->getInt('entry_id');
            
            $this->breeds = $entry_model->getBreeds();
            $this->categories = $entry_model->getCategories();
            $this->divisions = $entry_model->getDivisions();
            $this->colors = $entry_model->getColors();
			$this->hairlengths = $entry_model->getHairlengths();
            $this->genders = $entry_model->getGenders();
            $this->titles = $entry_model->getTitles();
            $this->prefixes = $entry_model->getPrefixes();
            $this->suffixes = $entry_model->getSuffixes();
            $this->competitiveregions = $entry_model->getCompetitiveregions();
            
            $this->entry_details = TOESHelper::getEntryDetails($entry_id);
            $this->cat_details = TOESHelper::getCatDetails($this->entry_details->cat);

            
            // Check for errors.
            if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
            }
            
            parent::display($tpl);
            $app->close();
        }
        
        $this->item = TOESHelper::getCatDetails($cat_id);
        
        if ($this->item) {
            $this->cat_owner = TOESHelper::getCatUserRelations($this->item->cat_id,'Owner');
            $this->cat_breeder = TOESHelper::getCatUserRelations($this->item->cat_id,'Breeder');
            $this->cat_other =  TOESHelper::getCatUserRelations($this->item->cat_id,'Other');
            
            $cat_sire = TOESHelper::getCatCatRelations($this->item->cat_id,'Sire');
            if ($cat_sire)
            {
                $sire_details = TOESHelper::getCatDetails($cat_sire);
                
                $this->item->cat_sire_reg_number = $sire_details->cat_registration_number;
                $this->item->cat_sire = ($sire_details->cat_prefix_abbreviation?$sire_details->cat_prefix_abbreviation.' ':'').($sire_details->cat_title_abbreviation?$sire_details->cat_title_abbreviation.' ':'').($sire_details->cat_suffix_abbreviation?$sire_details->cat_suffix_abbreviation.' ':'').($sire_details->cat_name?$sire_details->cat_name.' ':'').($sire_details->cat_registration_number?$sire_details->cat_registration_number:'');
            }
			
            $cat_dam = TOESHelper::getCatCatRelations($this->item->cat_id,'Dam');
            if ($cat_dam)
            {
                $dam_details = TOESHelper::getCatDetails($cat_dam);
                
                $this->item->cat_dam_reg_number = $dam_details->cat_registration_number;
                $this->item->cat_dam = ($dam_details->cat_prefix_abbreviation?$dam_details->cat_prefix_abbreviation.' ':'').($dam_details->cat_title_abbreviation?$dam_details->cat_title_abbreviation.' ':'').($dam_details->cat_suffix_abbreviation?$dam_details->cat_suffix_abbreviation.' ':'').($dam_details->cat_name?$dam_details->cat_name.' ':'').($dam_details->cat_registration_number?$dam_details->cat_registration_number:'');
            }
            
            $this->item->cat_detail_name = $this->item->cat_prefix_abbreviation . ' ' . $this->item->cat_title_abbreviation . ' ' . $this->item->cat_name . ' ' . $this->item->cat_suffix_abbreviation;
        	$this->item->cat_images = TOESHelper::getCatImages($this->item->cat_id);
        	//
        	$this->item->documents = $this->get('documents');
        	//var_dump($this->item->documents);
        	$this->organizations = $this->get('organization');
			$this->document_type = $this->get('documenttype');
        	
        }
        
        $this->hairlength = $this->get('Hairlength');
        $this->competitiveregions = $this->get('Competitiveregion');
        $this->category = $this->get('Category');
        $this->division = $this->get('Division');
        $this->color = $this->get('Color');
        $this->breed = $this->get('breed');
        
        $this->gender = $this->get('gender');
        $this->title = $this->get('title');
        $this->prefix = $this->get('prefix');
        $this->suffix = $this->get('suffix');
        
        $this->document_types = $this->get('document_types');
        $this->document_types_list = $this->get('document_types_list');
        $this->organization = $this->get('organization');
        $this->document_type_labels = $this->get('document_type_labels');
        
        $this->document_weights = $entry_model->getDocument_weights();
        // echo "<pre>";
        // var_dump($this->organization);
        // var_dump($this->organization);
        
		$this->user = TOESHelper::getUserInfo($user->id);
		
		$this->reg_number_formats = $this->get('RegNumberFormats');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        parent::display($tpl);
    }
}
