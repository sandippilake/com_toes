<?php

/**
 * @version		$Id: controller.php 15 2009-11-02 18:37:15Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Hello World Component Controller
 */
class TOESController extends JControllerLegacy {

    public function display($cachable = false, $urlparams = false) {
        $cachable = true;
		error_reporting(E_ALL);
		$app = JFactory::getApplication();
		if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
		{
	        // Set the default view name and format from the Request.
	        $vName = $app->input->getCmd('view', 'shows');
	        $app->input->set('view', $vName);
	
	        //echo $vName;die;
	
	        return parent::display($cachable, array('Itemid' => 'INT'));
		}
		
		//JHtml::_('jquery.framework');
		

		$params = JComponentHelper::getParams('com_toes');

        $document = JFactory::getDocument();
		//$document->addScript('components/com_toes/assets/jqueryui/jquery-ui.min.js',"text/javascript", true);
		//$document->addScript('https://code.jquery.com/jquery-1.12.4.js',"text/javascript", true);
		$document->addScript('https://code.jquery.com/ui/1.12.1/jquery-ui.js',"text/javascript", true);
		
		
		
        $document->addStyleSheet('components/com_toes/assets/toes.css');
		//$document->addStyleSheet('components/com_toes/assets/jqueryui/jquery-ui.css');
		//$document->addStyleSheet('components/com_toes/assets/bootstrap/css/bootstrap.min.css');
		$document->addStyleSheet('https://stackpath.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
		$document->addStyleSheet('//use.fontawesome.com/releases/v5.5.0/css/all.css');
        
        $document->addStyleSheet('components/com_toes/assets/jBox/jBox.css');
        $document->addStyleSheet('components/com_toes/assets/jBox/themes/ModalBorder.css');
        $document->addStyleSheet('components/com_toes/assets/jBox/themes/NoticeBorder.css');
        $document->addStyleSheet('components/com_toes/assets/jBox/themes/TooltipBorder.css');
        $document->addStyleSheet('components/com_toes/assets/jBox/themes/TooltipDark.css');
        
        $document->addScript('components/com_toes/assets/jBox/jBox.min.js',"text/javascript", true);
		
		// $document->addScript('components/com_toes/assets/bootstrap/js/bootstrap.min.js',"text/javascript", true);
		
		$maps_api_key = $params->get('maps_api_key','AIzaSyB3liClaw71SWzQw6QVFnls8nBbF9IJ4Aw');
	   	//$document->addScript('//maps.googleapis.com/maps/api/js?key='.$maps_api_key.'&libraries=places&callback=initAutocomplete');

	  	$layout = $app->input->get('layout');
		if($layout == 'default_showofficials' || $layout == 'default_clubofficials') {
		} else {		
	        $siteURL  = JURI::root();
	        $document->addScriptDeclaration("var SiteURL = '" . $siteURL . "';\n");
	        
	        echo '<script>var SiteURL =\''.$siteURL.'\';</script>';
	        
	        $lang = JFactory::getLanguage();
	        
	        if(file_exists(JPATH_ROOT.'components/com_toes/assets/'.$lang->get('tag').'.js')) : 
	        ?>
	            <script src="<?php echo JURI::root();?>components/com_toes/assets/<?php echo $lang->get('tag');?>.js"></script>
	        <?php else : ?>
	            <script src="<?php echo JURI::root();?>components/com_toes/assets/en-GB.js"></script>
	        <?php endif;?>
	        
	        <script src="<?php echo JURI::root();?>components/com_toes/assets/toes.js"></script>
	      
        <?php 
		}
        // Set the default view name and format from the Request.
        $vName = $app->input->getCmd('view', 'shows');
        $app->input->set('view', $vName);

        //echo $vName;die;

        return parent::display($cachable, array('Itemid' => 'INT'));
    }
}
