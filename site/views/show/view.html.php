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
class TOESViewShow extends JViewLegacy {

    protected $item;

    /**
     * Display the view
     */
    public function display($tpl = null) {

        $app    = JFactory::getApplication();
        $user   = JFactory::getUser();
        $layout = $app->input->getVar('layout');
		
        $this->item = $this->get('Item');
		
		if($layout == 'rdshowapprove500')
		{
			$this->rdshowapproveurl = $this->get('rdshowapproveurl');
			$this->showsrejected = $this->get('Showsrejected');
		}
		if($layout == 'conflicted_rdshowapprove500')
		{	
			$this->conflicted_rdshowapproveurl = $this->get('conflicted_rdshowapproveurl');
			$this->rdcheckshowisrejected = $this->get('Rdcheckshowisrejected');
		}	
		if($layout == 'showapprove500')
		{	
			$this->checkurlhash = $this->get('Checkurlhash');
			$this->checkshowisrejected = $this->get('Checkshowisrejected');
		}	
       
       
        
        if ($layout == 'default' || empty($layout)) {
            $this->item->entries = TOESHelper::getEntries($user->id, $this->item->show_id);
            $this->item->summary = $this->get('Summary');
            
            $this->item->placeholders = TOESHelper::getPlaceholders($user->id, $this->item->show_id);
        }

        if ($layout == 'short') {
            $this->item->entryclerks = $this->get('EntryClerks');
            $this->item->showmanagers = $this->get('ShowManagers');
            $this->item->judges = $this->get('Judges');
            $this->item->congress_judges = $this->get('CongressJudges');
            $this->item->entries = TOESHelper::getEntries($user->id, $this->item->show_id);
            //var_dump($this->item->entries);
            
            $this->item->summary = $this->get('Summary');
            
            $this->item->placeholders = TOESHelper::getPlaceholders($user->id, $this->item->show_id);
            
			$this->item->showdays = $this->get('ShowDays');
			
			$this->invoice = $this->get('Invoice');
			
            $this->judges = array();
            $this->congress_judges = array();
			$this->ring_timings = array();
            
            foreach($this->item->judges as $judge)
            {
                if(!$judge->ring_timing)
                    $ring_timing = 0;
                else
                    $ring_timing = $judge->ring_timing;
                $this->judges[$judge->show_day_date][$ring_timing][] = $judge;
				
				if(!(isset($this->ring_timings[$judge->show_day_date]) && in_array($ring_timing, $this->ring_timings[$judge->show_day_date])))
					$this->ring_timings[$judge->show_day_date][] = $ring_timing;
            }

            foreach($this->item->congress_judges as $judge)
            {
                if(!$judge->ring_timing)
                    $ring_timing = 0;
                else
                    $ring_timing = $judge->ring_timing;
                $this->congress_judges[$judge->show_day_date][$ring_timing][] = $judge;

				if(!(isset($this->ring_timings[$judge->show_day_date]) && in_array($ring_timing, $this->ring_timings[$judge->show_day_date])))
					$this->ring_timings[$judge->show_day_date][] = $ring_timing;
            }
			foreach($this->item->showdays as $showday)
			{
				if(isset($this->ring_timings[$showday->show_day_date]))
					ksort($this->ring_timings[$showday->show_day_date]);
			}
			parent::display($tpl);
			$app->close();
			//return;
        }
        if ($layout == 'copyshowconfirm') {
			
			//parent::display($tpl);
			//$app->close();
			
			
		}
		if ($layout == 'map') {
			$this->venue = $this->get('venue');
			
			parent::display($tpl);
			$app->close();
			
			
		}
        

        if ($layout == 'edit') {

            if($this->item)
            {
               if( !TOESHelper::isAdmin() && !TOESHelper::is_clubowner($user->id, $this->item->club_id) && !TOESHelper::is_showmanager($user->id, $this->item->show_id))
               {
                    JError::raiseWarning(500, JText::_('EDIT_SHOW_NOAUTH'));
                    $app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
               }
            }
            else
            {
                if(!TOESHelper::isAdmin() && !TOESHelper::is_clubofficial($user->id) )
                {
                    JError::raiseWarning(500, JText::_('ADD_SHOW_NOAUTH'));
                    $app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
                }
				$this->item = new stdClass();
            }
            
            $this->item->showdays = $this->get('ShowDays');
            $this->item->rings = $this->get('Rings');
            $this->item->ringtimings = $this->get('RingTimings');
            $this->item->ringformats = $this->get('Ringformats');
            $this->item->ringjudgs = $this->get('Ringjudgs');

            $clubslist = $this->get('Clubs');
            $this->clubslist = JHTML::_('select.genericlist', $clubslist, 'club', ' class="inputbox required"', 'value', 'text', @$this->item->club_id);

            $showformatslist = $this->get('showformats');
            $this->showformatslist = JHTML::_('select.genericlist', $showformatslist, 'show_format', ' class="inputbox required" onchange="checkFormat(this);"', 'value', 'text', @$this->item->show_format);

            $fontsizelist = $this->get('fontsizes');
            $this->fontsizelist = JHTML::_('select.genericlist', $fontsizelist, 'show_catalog_font_size', ' class="inputbox required"', 'value', 'text', @$this->item->show_catalog_font_size);

            $pageorientationlist = $this->get('pageorientations');
            $this->pageorientationlist = JHTML::_('select.genericlist', $pageorientationlist, 'show_catalog_page_orientation', ' class="inputbox required"', 'value', 'text', @$this->item->show_catalog_page_orientation);
            
            $this->showofficialtypes = $this->get('Showofficialtype');
            if (isset($this->item->show_id)) {
                foreach ($this->showofficialtypes as $so) {
                    $so->showofficial = TOESHelper::getshowofficialuser($this->item->show_id, $so->show_official_type_id);
                }
            }
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
        parent::display($tpl);
    }
   
}
