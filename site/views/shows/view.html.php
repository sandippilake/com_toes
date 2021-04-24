<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * Shows view class for the Toes package.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESViewShows extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
            
        $this->shows = $this->get('Items');
        //$this->pagination = $this->get('Pagination');
        
        //get conflicted shows
        $this->conflicted_shows = $this->get('Conflicted_shows');

        $this->state = $this->get('State');

        $this->clubs = $this->get('Clubs');
        //var_dump($this->clubs);
        
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_TOES_SHOW_CALENDAR'));
		
        parent::display($tpl);
    }

}
