<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template styles list controller class.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESControllerCats extends JControllerAdmin {

    /**
     * Proxy for getModel.
     */
    public function getModel($name = 'cat', $prefix = 'ToesModel', $config = array()) {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    public function removecat() {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $cat_id = $app->input->getVar('cat_id');
        $connection_type = $app->input->getVar('connection_type');

        $query = "SELECT cat_user_connection_type_id 
                FROM #__toes_cat_user_connection_type 
                WHERE cat_user_connection_type = " . $db->quote($connection_type);
        $db->setQuery($query);
        $cat_user_connection_type_id = $db->loadResult();

        $query = "DELETE FROM `#__toes_cat_relates_to_user` WHERE `of_cat` = " . $db->quote($cat_id) . " AND `cat_user_connection_type` = " . $db->quote($cat_user_connection_type_id) . "  ";
        $db->setQuery($query);

        if ($db->query())
            echo 1;
        else
            echo 0;

        $app->close();
    }

    public function getCatByName() {
        $app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);
			
              $query = "SELECT cat_id AS `key`, cat_name AS value 
                    FROM #__toes_cat 
                    WHERE LOWER(cat_name) LIKE ".$like." 
                    ORDER BY cat_name";       
                    
            $db->setQuery($query);
            $cats = $db->loadObjectList();
            if (count($cats)) {
                echo json_encode($cats);
            }
        }
        $app->close();
    }

	
    public function getCatByRegnumber() {
		$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);
			
              $query = "SELECT c.cat_id AS `key`, CONCAT(c.cat_name,' - ',r.cat_registration_number) AS value
                    FROM #__toes_cat AS c 
					LEFT JOIN #__toes_cat_registration_number AS r ON r.cat_registration_number_cat = c.cat_id
                    WHERE LOWER(r.cat_registration_number) LIKE ".$like."  
                    ORDER BY c.cat_name";       
                    
            $db->setQuery($query);
            $cats = $db->loadObjectList();
            if (count($cats)) {
                echo json_encode($cats);
            }
        }
        $app->close();
   }

}
