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
class TOESViewInvoiceshows extends JViewLegacy {

    protected $items;
    protected $pagination;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null) {
		
		$isEO = TOESHelper::isAdmin() || TOESHelper::isEO();
        
        if(!$isEO)
        {
            JError::raiseWarning(500, JText::_('COM_TOES_NOAUTH'));
            $app->redirect(JRoute::_('index.php?option=com_toes&view=shows',false));
        }
            
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        
         

        $this->state = $this->get('State');

        $this->clubs = $this->get('Clubs');
        //var_dump($this->clubs);
        
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_TOES_INVOICE_SHOWS'));
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
        parent::display($tpl);
    }

}
