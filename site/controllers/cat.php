<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template style controller class.
 *
 * @package	Joomla
 * @subpackage	com_toes
 */
class TOESControllerCat extends JControllerForm {

    public function cancel($key = NULL){
        
		$app = JFactory::getApplication();
        $show_id = $app->input->getInt('show_id');
        if($show_id)
        {
            $session = JFactory::getSession();
            $session->set('add_show',$show_id); 
            $app = JFactory::getApplication();
            $app->redirect(JRoute::_('index.php?option=com_toes&view=shows'));
        }
        
        $this->setRedirect(JRoute::_('index.php?option=com_toes&view=cats', JText::_('COM_TOES_CAT_ADDED_SUCCESS')));
    }
    
    /**
     * @var		string	The prefix to use with controller messages.
     * @since	1.6
     */
    public function save($key = NULL, $urlVar = NULL) {
		var_dump($_FILES);
		var_dump($_POST);
		//die;
		
		$app = JFactory::getApplication();
        $model = parent::getModel('cat', 'ToesModel', array('ignore_request' => true));
        $post = $app->input->post->getArray();
			
        if ($model->save($post))
            $this->setRedirect(JRoute::_('index.php?option=com_toes&view=cats'), JText::_('COM_TOES_CAT_ADDED_SUCCESS'));
        else
            $this->setRedirect(JRoute::_('index.php?option=com_toes&view=cat&layout=edit'), JText::_('COM_TOES_CAT_ADDED_UNSUCCESS'));
    }
    
    public function getUserFullName()
    {
        $app = JFactory::getApplication();
        $username = $app->input->getVar('username');

        if ($username) {
            $db = JFactory::getDBO();
            $like = $db->Quote($db->escape(strtolower($username), true), false);

            $query = "SELECT user.id, user.name, cb.firstname, cb.lastname 
                FROM #__users as user
                LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                WHERE LOWER(user.username) LIKE  " . $like;

            $db->setQuery($query);
            $user = $db->loadObject();
            if ($user) {
                if($user->firstname || $user->lastname)
                    echo "{$user->firstname} {$user->lastname}";
                else
                    echo "{$user->name}";
            }
            else
                echo 0;
        }
        else
            echo 0;

        $app->close();
    }

    public function get_prefilled_form() {
        $app = JFactory::getApplication();
        $registration_number = $app->input->getVar('registration_number');

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__toes_cat_registration_number');
        $query->where('cat_registration_number = ' . $db->quote($registration_number));
        $db->setQuery($query);
        $registration_number_details = $db->loadObject();

        if (isset($registration_number_details->cat_registration_number_cat))
            echo $registration_number_details->cat_registration_number_cat;
        else
            echo '0';
        $app->close();
    }

    public function set_date() {
        $app = JFactory::getApplication();
        $date = $app->input->getVar('date');
        $date = trim($date);
        
        if (strlen($date) == 6) {
            if ('20'.substr($date, 4, 2) > date('Y'))
                $year = '19'.substr($date, 4, 2);
            else
                $year = '20'.substr($date, 4, 2);

            $date_in_dateformat = $year.'-'.substr($date, 0, 2).'-'.substr($date, 2, 2);

            //echo $date_in_dateformat;
            echo trim($date_in_dateformat);
        }
        else
            echo '';

        $app->close();
    }

    public function isHHP() {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $breed = $app->input->getInt('breed',0);

        $query = "SELECT `breed_id` FROM `#__toes_breed` WHERE breed_group = 'Household Pet'";
        $db->setQuery($query);
        $breed_id = $db->loadResult();

        if ($breed == $breed_id)
            echo 1;
        else
            echo 0;

        $app->close();
    }    

    public function set_breed() {
        $app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $breed = $app->input->getInt('breed',0);

        $query = "SELECT `breed_id` FROM `#__toes_breed` WHERE breed_group = 'Household Pet'";
        $db->setQuery($query);
        $breed_id = $db->loadResult();

        if ($breed) {
            if ($breed == $breed_id)
                echo 1;
            else
                echo 0;

            $app->close();
        }

        if ($breed_id) {
            $query = "SELECT breed_id AS value, concat(breed_name,'(',breed_abbreviation,')') AS text
                    FROM #__toes_breed 
                    WHERE breed_organization = 1 
                    AND breed_id = " . $breed_id." 
                    ORDER BY breed_name ASC";

            $db->setQuery($query);
            $breeds = $db->loadObjectList();

            echo JHTML::_('select.genericlist', $breeds, 'breed', 'class="inputbox required" onchange="checkstatus();"', 'value', 'text', $breed_id);
        }
        else
            echo 0;

        $app->close();
    }

    public function unset_breed() 
    {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $breed = $app->input->getInt('breed',0);

        $query = "SELECT breed_id AS value, concat(breed_name,' (',breed_abbreviation,')') AS text
            FROM #__toes_breed 
            WHERE breed_organization = 1 
            ORDER BY breed_name ASC";

        $db->setQuery($query);
        $breeds = $db->loadObjectList();
		
        array_unshift($breeds, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_BREED')));

        if(count($breeds))
            echo JHTML::_('select.genericlist', $breeds, 'breed', 'class="inputbox required" onchange="checkstatus();"', 'value', 'text', $breed);
        else
            echo 0;

        $app->close();
    }

    public function set_hair_length() {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $rgn_prefix = $app->input->getVar('rgn_prefix');

        $query = "SELECT `cat_hair_length_id` FROM `#__toes_cat_hair_length` WHERE cat_hair_length_abbreviation = ".$db->quote($rgn_prefix);
        $db->setQuery($query);
        $rgn_prefix_id = $db->loadResult();

        if ($rgn_prefix_id)
            echo $rgn_prefix_id;
        else
            echo 0;

        $app->close();
    }

    public function checkbreedstatus() {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $breed = $app->input->getInt('breed',0);

        $ems_filter = $app->input->getVar('ems_filter');
        $ems_filter = substr($ems_filter, 4);

        if($ems_filter)
        {
            $filters = explode(' ', $ems_filter);
            
            if(in_array('71', $filters))
                echo 1;
            else
                echo 0;
        }
        else
        {
            // $breed_statuses = array('Advanced New Breed', 'Preliminary New Breed', 'Registration Only', 'Experimental');
            // $breed_statuses_ch = array('Championship', 'Non Championship');

            $breed_statuses_for_new_trait_unchecked = array('Experimental');
            $breed_statuses_for_new_trait_checked = array('Championship', 'Non Championship','Registration Only', 'Advanced New Breed', 'Preliminary New Breed');

            $query = "SELECT bs.breed_status 
                    FROM `#__toes_breed` as b 
                    LEFT JOIN `#__toes_breed_has_status` as bhs ON (bhs.breed_has_status_breed = b.breed_id  AND NOW() BETWEEN bhs.breed_has_status_since AND bhs.breed_has_status_until)  
                    LEFT JOIN `#__toes_breed_status` as bs ON bs.breed_status_id = bhs.breed_has_status_status
                    WHERE b.breed_id = " . $breed;

            $db->setQuery($query);
            $breed_detail = $db->loadObject();

            if ($breed) {
                if (in_array($breed_detail->breed_status, $breed_statuses_for_new_trait_unchecked))
                    echo 1;
                if (in_array($breed_detail->breed_status, $breed_statuses_for_new_trait_checked))
                    echo 0;
            }
        }
        $app->close();
    }

    public function check_championship() {
    	$app = JFactory::getApplication();
        $breed = $app->input->getInt('breed',0);

        $db = JFactory::getDbo();

        $query = "SELECT bhs.breed_has_status_status 
                FROM `#__toes_breed` as b 
                LEFT JOIN `#__toes_breed_has_status` as bhs ON (bhs.breed_has_status_breed = b.breed_id  AND NOW() BETWEEN bhs.breed_has_status_since AND bhs.breed_has_status_until)  
                WHERE b.breed_id = " . $breed;
        
        //$query = "SELECT `breed_status` FROM `#__toes_breed` WHERE breed_id = " . (int) $breed;
        $db->setQuery($query);
        $breed_status = $db->loadResult();

        echo (int) $breed_status;
        $app->close();
    }

    public function changesuffix() {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $gender = $app->input->getVar('gender');
        $suffix = $app->input->getInt('suffix');

        $query = $db->getQuery(true);
        $query->select('cat_suffix_id AS value, concat(cat_suffix," ","(",cat_suffix_abbreviation,")") AS text, cat_suffix_abbreviation ');
        $query->from('#__toes_cat_suffix');    
        $query->where('cat_suffix_organization = 1');
        $query->order('cat_suffix_id ASC');
        
        $db->setQuery($query);
        $suffixes = $db->loadObjectList();

        $options = array();
        foreach($suffixes as $item)
        {
            if( (($gender == 1 || $gender == 3) && $item->cat_suffix_abbreviation != 'OD')
                || (($gender == 2 || $gender == 4) && $item->cat_suffix_abbreviation != 'OS'))
            $options[] = $item;
        }
		
        echo JHTML::_('select.genericlist', $options, 'suffix', 'class="inputbox required" data-minimum-results-for-search="Infinity"', 'value', 'text', $suffix);
        $app->close();
    }

    public function changetitle() {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $breed = $app->input->getVar('breed');
        $gender = $app->input->getVar('gender');
        $title = $app->input->getInt('title');
        
        $isHHP = false;
        $query = "SELECT `breed_id` FROM `#__toes_breed` WHERE breed_group = 'Household Pet'";
        $db->setQuery($query);
        $breed_id = $db->loadResult();

        if ($breed == $breed_id)
            $isHHP = true;
        else
            $isHHP = false;
        
        $HHP_titles = array('', 'Master', 'GRM', 'DGM', 'TGM', 'QGM', 'SGM');
        $notHHP_male_titles = array('', 'CH', 'GRC', 'DGC', 'TGC', 'QGC', 'SGC');
        $notHHP_neuter_titles = array('', 'CH', 'GRC', 'DGC', 'TGC', 'QGC', 'SGC', 'CHA', 'GRCA', 'DGCA', 'TGCA', 'QGCA', 'SGCA');

        $query = $db->getQuery(true);
        $query->select('cat_title_id AS value, concat(cat_title," ","(",cat_title_abbreviation,")") AS text, cat_title_abbreviation ');
        $query->from('#__toes_cat_title');    
        $query->where('cat_title_organization = 1');
        $query->order('cat_title_id ASC');
        
        $db->setQuery($query);
        $titles = $db->loadObjectList();

        $options = array();
        foreach($titles as $item)
        {
            if($isHHP)
            {
                if(in_array($item->cat_title_abbreviation, $HHP_titles))
                {
                    $options[] = $item;
                }
            }
            else if($gender == 1 || $gender == 2)
            {
                if(in_array($item->cat_title_abbreviation, $notHHP_male_titles))
                {
                    $options[] = $item;
                }
            }
            else if($gender == 3 || $gender == 4)
            {
                if(in_array($item->cat_title_abbreviation, $notHHP_neuter_titles))
                {
                    $options[] = $item;
                }
            }
            else
                $options[] = $item;
        }
		
        echo JHTML::_('select.genericlist', $options, 'title', 'class="inputbox required"  data-minimum-results-for-search="Infinity"', 'value', 'text', $title);
        $app->close();
    }

    public function changehairlength() {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();

        $ems_filter = $app->input->getVar('ems_filter');
        $ems_filter = substr($ems_filter, 4);

        if($ems_filter)
        {
            $filters = explode(' ', $ems_filter);
            
            $query = $db->getQuery(true);
            $query->select('h.cat_hair_length_id AS value, concat(h.cat_hair_length,\' (\',h.cat_hair_length_abbreviation,\')\') AS text');
            $query->from('#__toes_cat_hair_length AS h');
            $query->order('h.cat_hair_length_id ASC');

            if(in_array('81', $filters))
                $query->where("h.cat_hair_length_abbreviation = 'LH'");
            else
                $query->where("h.cat_hair_length_abbreviation = 'SH'");
            
            $db->setQuery($query);
            $options = $db->loadObjectList();
            echo JHTML::_('select.genericlist', $options, 'cat_hair_length', 'class="inputbox required"  data-minimum-results-for-search="Infinity"', 'value', 'text');
        }
        else
        {
            $breed = $app->input->getVar('breed');
            $hairlength = $app->input->getInt('hairlength');
            $reg_number = $app->input->getVar('reg_number');
            
            if($reg_number == 'LH' || $reg_number == 'SH')
            {
                $query = $db->getQuery(true);
                $query->select('h.cat_hair_length_id AS value, concat(h.cat_hair_length,\' (\',h.cat_hair_length_abbreviation,\')\') AS text');
                $query->from('#__toes_cat_hair_length AS h');
                $query->order('h.cat_hair_length_id ASC');
                $query->where("h.cat_hair_length_abbreviation = " . $db->quote($reg_number));

                //echo $query;
                $db->setQuery($query);
                $options = $db->loadObjectList();

                echo JHTML::_('select.genericlist', $options, 'cat_hair_length', 'class="inputbox required"  data-minimum-results-for-search="Infinity"', 'value', 'text');
            }
            else
            {
                $query = $db->getQuery(true);
                $query->select('b.breed_hair_length');
                $query->from('#__toes_breed AS b');
                $query->where("b.breed_id = " . $db->quote($breed));

                $db->setQuery($query);
                $hairlength_id = $db->loadResult();

                $query = $db->getQuery(true);
                $query->select('h.cat_hair_length_id AS value, concat(h.cat_hair_length,\' (\',h.cat_hair_length_abbreviation,\')\') AS text');
                $query->from('#__toes_cat_hair_length AS h');
                $query->order('h.cat_hair_length_id ASC');

                if($hairlength_id){
                    $query->where("h.cat_hair_length_id = " . $hairlength_id);
                }

                $db->setQuery($query);
                $options = $db->loadObjectList();

                if (!$hairlength_id)
                array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_HAIRLENGTH')));

                if(!$hairlength_id)
                    $hairlength_id = $hairlength;
                echo JHTML::_('select.genericlist', $options, 'cat_hair_length', 'class="inputbox required" data-minimum-results-for-search="Infinity"', 'value', 'text', $hairlength_id);
            }
        }
        
        $app->close();
    }

    public function changebreed() {
    	$app = JFactory::getApplication();
        $db = JFactory::getDbo();
        $breed = $app->input->getVar('breed',0);
    
        $ems_filter = $app->input->getVar('ems_filter');
        $ems_filter = substr($ems_filter, 0, 3);

        if($ems_filter)
        {
            $query = "SELECT `color_helper_breed_tica_abbreviation` FROM `#__toes_color_helper_ems_breed` WHERE LOWER(color_helper_breed_ems_abbreviation) = {$db->quote(strtolower($ems_filter))}";
            $db->setQuery($query);
            $brds = $db->loadColumn();
        }
        else
            $brds = array();

        $query = "SELECT breed_id AS value, concat(breed_name,' (',breed_abbreviation,')') AS text
                FROM #__toes_breed 
                WHERE breed_organization = 1 ";
                
        if($brds)
        {
            $query .= "AND (";
            $whr = array();
            foreach($brds as $brd)
            {
                $whr[] = "breed_abbreviation = " . $db->quote($brd);
            }
            $query .= implode(' OR ', $whr);
            $query .= ") ";
        }
        $query .= "ORDER BY breed_name ASC";

        $db->setQuery($query);
        $breeds = $db->loadObjectList();

        if($brds)
        {
            $brdlist = $breeds;
        }
        else
        {
            $brdlist = array();
            $brdlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_BREED'));
            $brdlist = array_merge($brdlist, $breeds);
        }

        echo JHTML::_('select.genericlist', $brdlist, 'breed', 'class="inputbox required" onchange="checkstatus();"', 'value', 'text', $breed);

        $app->close();
    }    
    
    public function changecatlist() {
    	$app = JFactory::getApplication();
        $breed = $app->input->getVar('breed');
        $category_id = $app->input->getInt('category');
        $color_id = $app->input->getInt('color');
        $db = JFactory::getDbo();

        $ems_filter = $app->input->getVar('ems_filter');
        $ems_filter = substr($ems_filter, 4);

		if($color_id && !$category_id) {

            $query = "SELECT `bcdc`.`category`
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    WHERE (`bcdc`.`organization` = 1) AND `bcdc`.`color` = ".$color_id;
			
			$db->setQuery($query);
			$new_category_id = $db->loadResult();
			
            $query = "SELECT DISTINCT `c`.`category_id` as value, `c`.`category` as text
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    LEFT JOIN `#__toes_category` AS `c` ON `c`.`category_id` = `bcdc`.`category`
                    WHERE (`bcdc`.`organization` = 1)";

            if ($breed)
                $query .=" AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";

            //$query.= " ORDER BY c.`category` ASC";
            $query.= " ORDER BY c.`category_id` ASC";
            $db->setQuery($query);
            $categories = $db->loadObjectList();

            $catlist = array();
            $catlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_CATEGORY'));
            $catlist = array_merge($catlist, $categories);

	        echo JHTML::_('select.genericlist', $catlist, 'category', 'class="inputbox required"  data-minimum-results-for-search="Infinity" onchange="changedivision();changecolor();"', 'value', 'text', $new_category_id);
	        $app->close();
		}

        if($ems_filter)
        {
            $filters = explode(' ', $ems_filter);
            
            $query = "SELECT `c`.`category_id` as value, `c`.`category` as text
                    FROM `#__toes_category` AS `c` ";
            
            if(in_array('31', $filters))
                $query .= "WHERE `c`.`category` = 'Sepia'";
            else if(in_array('32', $filters))
                $query .= "WHERE `c`.`category` = 'Mink'";
            else if(in_array('33', $filters))
                $query .= "WHERE `c`.`category` = 'Pointed'";
            else
                $query .= "WHERE `c`.`category` = 'Traditional'";
            
            $db->setQuery($query);
            $categories = $db->loadObjectList();
        
            $catlist = $categories;
        }
        else
        {
            $query = "SELECT DISTINCT `c`.`category_id` as value, `c`.`category` as text
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    LEFT JOIN `#__toes_category` AS `c` ON `c`.`category_id` = `bcdc`.`category`
                    WHERE (`bcdc`.`organization` = 1)";

            if ($breed)
                $query .=" AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";

            //$query.= " ORDER BY c.`category` ASC";
            $query.= " ORDER BY c.`category_id` ASC";
            $db->setQuery($query);
            $categories = $db->loadObjectList();

            $catlist = array();
            $catlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_CATEGORY'));
            $catlist = array_merge($catlist, $categories);
        }

        echo JHTML::_('select.genericlist', $catlist, 'category', 'class="inputbox required"  data-minimum-results-for-search="Infinity" onchange="changedivision();changecolor();"', 'value', 'text', $category_id);
        $app->close();
    }

    public function changedivisionlist() {
    	$app = JFactory::getApplication();
        $breed = $app->input->getVar('breed');
        $category = $app->input->getVar('category');
        $division_id = $app->input->getInt('division');
        $color_id = $app->input->getInt('color');
        $db = JFactory::getDbo();

        $ems_filter = $app->input->getVar('ems_filter');
        $ems_filter = substr($ems_filter, 4);


		if($color_id && !$division_id) {
            $query = "SELECT `bcdc`.`division`
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    WHERE (`bcdc`.`organization` = 1) AND `bcdc`.`color` = ".$color_id;
			
			$db->setQuery($query);
			$new_division_id = $db->loadResult();
            
            $query = "SELECT DISTINCT `d`.`division_id` as value, `d`.`division_name` AS `text`
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    LEFT JOIN `#__toes_division` AS `d` ON `d`.`division_id` = `bcdc`.`division`
                    WHERE (`bcdc`.`organization` = 1)";

            if ($breed)
                $query .= " AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";
            if ($category)
                $query .= " AND (`bcdc`.`category` = " . $db->quote($category) . " ) ";

            //$query.= " ORDER BY d.`division_name` ASC";
            $query.= " ORDER BY d.`division_id` ASC";

            $db->setQuery($query);
            $divisions = $db->loadObjectList();

	        $divisionlist = array();
	        $divisionlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_DIVISION'));
	        $divisionlist = array_merge($divisionlist, $divisions);
	
	        echo JHTML::_('select.genericlist', $divisionlist, 'division', 'class="inputbox required" onchange="changecolor();"', 'value', 'text', $new_division_id);
	        $app->close();
		}
		
		if($ems_filter)
        {
            $filters = explode(' ', $ems_filter);

            $query = "SELECT DISTINCT `d`.`division_id` as value, `d`.`division_name` AS `text`
                    FROM `#__toes_division` AS `d`
                    ";
            
            $first_check = array('21', '22', '23', '24', '25');
            $second_check = array('01', '02', '03', '04', '05', '09');
            
            $whr = array();
            $included = array();
            if(array_intersect($first_check, $filters)) {
                $included[] = "d.`division_name` LIKE " . $db->quote('Tabby');
                $included[] = "d.`division_name` LIKE " . $db->quote('Tabby & White');
                $included[] = "d.`division_name` LIKE " . $db->quote('Silver/Smoke');
                $included[] = "d.`division_name` LIKE " . $db->quote('Silver/Smoke & White');
            }
            else {
                $included[] = "d.`division_name` LIKE " . $db->quote('Solid');
                $included[] = "d.`division_name` LIKE " . $db->quote('Solid & White');
                $included[] = "d.`division_name` LIKE " . $db->quote('Tortie');
                $included[] = "d.`division_name` LIKE " . $db->quote('Tortie & White');
                $included[] = "d.`division_name` LIKE " . $db->quote('Silver/Smoke');
                $included[] = "d.`division_name` LIKE " . $db->quote('Silver/Smoke & White');
            }
            $whr[] = implode(' OR ', $included);

            $included = array();
            if(array_intersect($second_check, $filters)) {
                $included[] = "d.`division_name` LIKE " . $db->quote('Solid & White');
                $included[] = "d.`division_name` LIKE " . $db->quote('Tortie & White');
                $included[] = "d.`division_name` LIKE " . $db->quote('Tabby & White');
                $included[] = "d.`division_name` LIKE " . $db->quote('Silver/Smoke & White');
                $whr[] = implode(' OR ', $included);
            }
            
            $included = array();
            if(in_array('s', $filters))
            {
                $included[] = "d.`division_name` LIKE " . $db->quote('Silver/Smoke');
                $included[] = "d.`division_name` LIKE " . $db->quote('Silver/Smoke & White');
                $whr[] = implode(' OR ', $included);
            }
            
            if($whr)
            {
                $included = array_unique($included);
                $query .= "WHERE (";
                $query .= implode(') AND (', $whr);
                $query .= ") ";
            }

            //$query.= " ORDER BY d.`division_name` ASC";
            $query.= " ORDER BY d.`division_id` ASC";
            
            //echo $query;
            $db->setQuery($query);
            $divisions = $db->loadObjectList();
        }
        else
        {
            $query = "SELECT DISTINCT `d`.`division_id` as value, `d`.`division_name` AS `text`
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    LEFT JOIN `#__toes_division` AS `d` ON `d`.`division_id` = `bcdc`.`division`
                    WHERE (`bcdc`.`organization` = 1)";

            if ($breed)
                $query .= " AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";
            if ($category)
                $query .= " AND (`bcdc`.`category` = " . $db->quote($category) . " ) ";

            //$query.= " ORDER BY d.`division_name` ASC";
            $query.= " ORDER BY d.`division_id` ASC";

            $db->setQuery($query);
            $divisions = $db->loadObjectList();
        }
        
        $divisionlist = array();
        $divisionlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_DIVISION'));
        $divisionlist = array_merge($divisionlist, $divisions);

        echo JHTML::_('select.genericlist', $divisionlist, 'division', 'class="inputbox required" onchange="changecolor();"', 'value', 'text', $division_id);
        $app->close();
    }

    public function changecolorlist() {
    	$app = JFactory::getApplication();
        $breed = $app->input->getVar('breed');
        $category = $app->input->getVar('category');
        $division = $app->input->getVar('division');
        $color_id = $app->input->getInt('color');
        $db = JFactory::getDbo();

        $ems_filter = $app->input->getVar('ems_filter');
        $ems_filter = substr($ems_filter, 4);

        if($ems_filter)
        {
            $filters = explode(' ', $ems_filter);
            
            $whr = array();
            if(in_array('y', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%golden%'";
                $first_check = array('21', '22', '23', '24', '25');
                if(array_intersect($first_check, $filters)) {
                    $included[] = "`c`.`color_name` LIKE '%brown%'";
                    $included[] = "`c`.`color_name` LIKE '%black%'";
                    $included[] = "`c`.`color_name` LIKE '%seal%'";
                    $included[] = "`c`.`color_name` LIKE '%sable%'";
                }
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('s', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%silver%'";
                $whr[] = implode(' OR ', $included);
            }

            if(in_array('11', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%shaded%'";
                $included[] = "`c`.`color_name` LIKE '%chinchilla%'";
                $whr[] = implode(' OR ', $included);
            }

            if(in_array('12', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%shell%'";
                $included[] = "`c`.`color_name` LIKE '%chinchilla%'";
                $whr[] = implode(' OR ', $included);
            }

            if(in_array('w', $filters))
            {
                $included[] = "`c`.`color_name` LIKE '%white%'";
                $whr[] = implode(' OR ', $included);
            }

            if(in_array('d', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%red%'";
                $whr[] = implode(' OR ', $included);
            }

            if(in_array('e', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%cream%'";
                $whr[] = implode(' OR ', $included);
            }

            if(in_array('f', $filters) || in_array('g', $filters) || in_array('j', $filters) || in_array('h', $filters) || in_array('q', $filters) || in_array('r', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%tortie%'";
                $included[] = "`c`.`color_name` LIKE '%torbie%'";
                $whr[] = implode(' OR ', $included);
            }

            if(in_array('n', $filters) || in_array('f', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%black%'";
                $included[] = "`c`.`color_name` LIKE '%brown%'";
                $included[] = "`c`.`color_name` LIKE '%seal%'";
                $included[] = "`c`.`color_name` LIKE '%sable%'";
                $included[] = "`c`.`color_name` LIKE '%ruddy%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('a', $filters) || in_array('g', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%blue%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('b', $filters) || in_array('j', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%chocolate%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('c', $filters) || in_array('j', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%lilac%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('p', $filters) || in_array('r', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%fawn%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('o', $filters) || in_array('q', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%cinnamon%'";
                $included[] = "`c`.`color_name` LIKE '%sorrel%'";
                $whr[] = implode(' OR ', $included);
            }
            
            
            if(in_array('22', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%classic%'";
                $included[] = "`c`.`color_name` LIKE '%marbled%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('23', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%mackerel%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('24', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%spotted%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('25', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%ticked%'";
                $whr[] = implode(' OR ', $included);
            }
            
            if(in_array('04', $filters))
            {
                $included = array();
                $included[] = "`c`.`color_name` LIKE '%mitted%'";
                $whr[] = implode(' OR ', $included);
            }
            
            
            $query = "SELECT `c`.`color_id` as value, `c`.`color_name` AS `text`
                FROM `#__toes_color` AS `c` ";
              
            if($whr)
            {
                $query .= "WHERE (";
                $query .= implode(') AND (', $whr);
                $query .= ") ";
            }
            
            if ($category)
                $query .= " AND (`c`.`color_category` = " . $db->quote($category) . " ) ";
            if ($division)
                $query .= " AND (`c`.`color_division` = " . $db->quote($division) . " ) ";            
            
            //echo $query;
            $db->setQuery($query);
            $colors = $db->loadObjectList();
        }
        else
        {
            $query = "SELECT DISTINCT `c`.`color_id` as value, `c`.`color_name` AS `text`
                    FROM `#__toes_breed_category_division_color` AS `bcdc`
                    LEFT JOIN `#__toes_color` AS `c` ON `c`.`color_id` = `bcdc`.`color`
                    WHERE (`bcdc`.`organization` = 1) ";

            if ($breed)
                $query .= " AND (`bcdc`.`breed` = " . $db->quote($breed) . " ) ";
            if ($category)
                $query .= " AND (`bcdc`.`category` = " . $db->quote($category) . " ) ";
            if ($division)
                $query .= " AND (`bcdc`.`division` = " . $db->quote($division) . " ) ";

            //$query.= " ORDER BY c.`color_name` ASC";
            $query.= " ORDER BY c.`color_id` ASC";

            $db->setQuery($query);
            $colors = $db->loadObjectList();
        }
        $colorlist = array();
        $colorlist[] = JHTML::_('select.option', '', JText::_('COM_TOES_SELECT_COLOR'));
        $colorlist = array_merge($colorlist, $colors);

        echo JHTML::_('select.genericlist', $colorlist, 'color', 'class="inputbox required" onchange="check_cat_division();" ', 'value', 'text', $color_id);
        $app->close();
    }

    public function removepeoplefromcat() {
    	$app = JFactory::getApplication();
        $user = $app->input->getVar('user');
        $relation_type = $app->input->getVar('relation_type');
        $other_relation = $app->input->getVar('other_relation');
        $cat_id = $app->input->getVar('cat_id');

        switch ($relation_type) {
            case '1':
                $cat_relates_to_user = 'Owner';
                break;
            case '2':
                $cat_relates_to_user = 'Breeder';
                break;
            case '3':
                $cat_relates_to_user = $other_relation;
                break;
        }

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query = "DELETE FROM `#__toes_cat_relates_to_user` WHERE `of_cat` = " . $db->quote($cat_id) . "
                AND `person_is` = " . $db->quote($user) . " 
                AND `cat_user_connection_type` = 
                (SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE cat_user_connection_type  = " . $db->quote($cat_relates_to_user) . ")
                ";

        $db->setQuery($query);
        if ($db->query())
            echo 'ok';
        else
            echo '1';
        $app->close();
    }

    public function getUsers() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape($q, true) . '%', false);
            
            $query = "SELECT CONCAT(b.firstname,' ',b.lastname,' (',a.username,')') AS `key`, CONCAT(b.firstname,' ',b.lastname,' - ',a.username) AS value
                    FROM #__users as a 
                    LEFT JOIN #__comprofiler as b ON a.id = b.user_id
                    WHERE (concat(LOWER(b.firstname),' ',LOWER(b.lastname),' ',LOWER(a.username)) LIKE ".$like." ) 
                    ORDER BY b.lastname LIMIT 0, 10";
					
            $db->setQuery($query);
            $users = $db->loadObjectList();
            if (count($users)) {
            	echo json_encode($users);
            }
        }
        $app->close();
    }

    public function getregistration_number() {
    	$app = JFactory::getApplication();
        $q = $app->input->getVar('term');

        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape($q, true) . '%', false);

            $query = "SELECT crn.cat_registration_number_cat AS `key`,crn.cat_registration_number AS value 	
			FROM #__toes_cat_registration_number as crn
			INNER JOIN #__toes_cat as c ON crn.cat_registration_number_cat = c.cat_id
			LEFT JOIN #__toes_cat_prefix as p ON c.cat_prefix = p.cat_prefix_id
			LEFT JOIN #__toes_cat_title as t ON c.cat_title = t.cat_title_id
			WHERE LOWER(cat_registration_number) LIKE  " . $like ." LIMIT 0, 20";

            $db->setQuery($query);
            $registration_numbers = $db->loadObjectList();
            if (count($registration_numbers)) {
                echo json_encode($registration_numbers);
            }
        }
        $app->close();
    }

    public function getcat_sireordam() {
        $app = JFactory::getApplication();
        $q = $app->input->getVar('term');
        $gender = $app->input->getVar('gender');
		
        if ($q) {
            $db = JFactory::getDBO();
            $like = $db->Quote('%' . $db->escape(strtolower($q), true) . '%', false);

            $query = "SELECT r.cat_registration_number,c.cat_name,p.cat_prefix_abbreviation 
			,t.cat_title_abbreviation,s.cat_suffix_abbreviation,g.gender_name FROM #__toes_cat as c 
			LEFT JOIN #__toes_cat_registration_number as r ON c.cat_id = r.cat_registration_number_cat	 
			LEFT JOIN #__toes_cat_prefix as p ON c.cat_prefix = p.cat_prefix_id
			LEFT JOIN #__toes_cat_suffix as s ON c.cat_suffix = s.cat_suffix_id
			LEFT JOIN #__toes_cat_gender as g ON c.cat_gender = g.gender_id
			LEFT JOIN #__toes_cat_title as t ON c.cat_title = t.cat_title_id";
            $query .= " WHERE ( LOWER(r.cat_registration_number) LIKE  " . $like;
            $query .= " OR LOWER(p.cat_prefix) LIKE  " . $like;
            $query .= " OR LOWER(s.cat_suffix) LIKE  " . $like;
            $query .= " OR LOWER(t.cat_title) LIKE  " . $like;
            $query .= " OR LOWER(c.cat_name) LIKE  " . $like." )";
            
            if($gender == 'm')
            {
                $query .= " AND (g.gender_name = 'Male' OR g.gender_name =  'Male Neuter')";	
            }
            if($gender == 'f')
            {
                $query .= " AND (g.gender_name = 'Female' OR g.gender_name =  'Female Spay')";	
            }
			
			$query ." LIMIT 0, 10";

            $db->setQuery($query);
            $cats = $db->loadObjectList();
			$results = array();
            if (count($cats)) {
                foreach ($cats as $cat) {
                	$c = new stdClass();
					$c->key = $cat->cat_registration_number;
					//$c->value = ($cat->cat_prefix_abbreviation?$cat->cat_prefix_abbreviation.' ':'').($cat->cat_title_abbreviation?$cat->cat_title_abbreviation.' ':'').($cat->cat_suffix_abbreviation?$cat->cat_suffix_abbreviation.' ':'').($cat->cat_name?$cat->cat_name.' ':'').($cat->cat_registration_number?$cat->cat_registration_number:'');
					$c->value = ($cat->cat_prefix_abbreviation?$cat->cat_prefix_abbreviation.' ':'').($cat->cat_title_abbreviation?$cat->cat_title_abbreviation.' ':'').($cat->cat_suffix_abbreviation?$cat->cat_suffix_abbreviation.' ':'').($cat->cat_name?$cat->cat_name:'');
					
					$results[] = $c;
                }
				echo json_encode($results);
            }
        }

        $app->close();
    }
    
    public function getrelatedusers() {
        $app = JFactory::getApplication();
        $cat_id = $app->input->getVar('cat_id');
        
        $db = JFactory::getDbo();
        
        $query = "SELECT crtu.person_is as user_id, cuct.cat_user_connection_type as type 
            FROM #__toes_cat_relates_to_user AS crtu 
            LEFT JOIN #__toes_cat_user_connection_type as cuct on cuct.cat_user_connection_type_id = crtu.cat_user_connection_type	
            WHERE crtu.of_cat =  " . $cat_id . "
            ORDER BY cuct.cat_user_connection_type_id
            ";

        $db->setQuery($query);
        $users = $db->loadObjectList();

        $owner = array(); 
        $breeder = array(); 
        $agent = array(); 
        $lessee = array(); 

        foreach($users as $user)
        {
            switch ($user->type)
            {
                case 'Owner':
                    $owner[] = $user->user_id; 
                    break;
                case 'Breeder':
                    $breeder[] = $user->user_id; 
                    break;
                case 'Agent':
                    $agent[] = $user->user_id; 
                    break;
                case 'Lessee':
                    $lessee[] = $user->user_id; 
                    break;
            }
        }
        
        echo 'o:'.  implode(',', $owner).';';
        echo 'b:'.  implode(',', $breeder).';';
        echo 'a:'.  implode(',', $agent).';';
        echo 'l:'.  implode(',', $lessee).';';
        $app->close();
    }
    
    public function getcatname()
    {
        $app = JFactory::getApplication();
        $cat_id = $app->input->getVar('cat_id');
        
        $cat_details = TOESHelper::getCatDetails($cat_id);
        
        echo $cat_details->cat_prefix_abbreviation.''.$cat_details->cat_title_abbreviation.''.$cat_details->cat_suffix_abbreviation.''.$cat_details->cat_name;
        
        $app->close();
    }
    
    public function requestPermission()
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        
        $permission = $app->input->getVar('permission');
        $cat_id = $app->input->getVar('cat_id');
        $user_id = $app->input->getVar('user_id');
        $users = $app->input->getVar('users');
        
        $query = "SELECT email FROM `#__users` WHERE id IN ({$users})";
        $db->setQuery($query);
        $user_emails = $db->loadColumn();
        
        $user = TOESHelper::getUserInfo($user_id);
        $cat = TOESHelper::getCatDetails($cat_id);
        
        $cat_name = ($cat->cat_prefix_abbreviation?$cat->cat_prefix_abbreviation.' ':'').($cat->cat_title_abbreviation?$cat->cat_title_abbreviation.' ':'').($cat->cat_suffix_abbreviation?$cat->cat_suffix_abbreviation.' ':'').($cat->cat_name?$cat->cat_name.' ':'');
        
        $name ='';
        if($user->firstname || $user->lastname)
            $name = $user->firstname.' '.$user->lastname;
        else
            $name = $user->name;

		/*
		
        $subject = $name." requested permission to relate with ". $cat_name;

        $body = "Hello, <br/><br/>";
        $body .= $name." requested permission to relate with ". $cat_name ." AS ".$permission."<br/><br/>";
        
        $body .= "To Accept the request please click on following link...<br/>";
        
        
        $link = JURI::root()."index.php?option=com_toes&task=cat.grantPermission&cat_id=".$cat_id."&user_id=".$user_id."&permission=".$permission."&token=".time();
        $body .= "<a href='".$link."'>$link</a><br/>";
        
        $body .="Thanks!";
		/*
        $config     = JFactory::getConfig();
        $fromname   = $config->get('fromname');
        $fromemail  = $config->get('mailfrom');

		
        $mail = JFactory::getMailer();

        $mail->SetFrom($fromemail, $fromname);
        $mail->setSubject($subject);
        $mail->setBody($body);
        $mail->addRecipient($user_emails);
        $mail->IsHTML(TRUE);
        
        if($mail->Send())
            $app->enqueueMessage(JText::_('COM_TOES_REQUEST_SENT'));
        else
        {
            $app->enqueueMessage($mail->ErrorInfo,'error');
        }
		*/
        
		$mailTemplate = TOESMailHelper::getTemplate('cat_relation_permission');

		if($mailTemplate) {
			$subject = $mailTemplate->mail_subject;
			$body = $mailTemplate->mail_body;
		} else {
			$subject = $name." requested permission to relate with ". $cat_name;

			$body = "Hello, <br/><br/>";
			$body .= $name." requested permission to relate with ". $cat_name ." AS ".$permission."<br/><br/>";

			$body .= "To Accept the request please click on following link...<br/>";


			$link = JURI::root()."index.php?option=com_toes&task=cat.grantPermission&cat_id=".$cat_id."&user_id=".$user_id."&permission=".$permission."&token=".time();
			$body .= "<a href='".$link."'>$link</a><br/>";

			$body .="Thanks!";
		}

		$subject = str_replace('[name]', $name, $subject);
		$subject = str_replace('[cat_name]', $cat_name, $subject);		
		
		$body = str_replace('[name]', $name, $body);
		$body = str_replace('[cat_name]', $cat_name, $body);
		$body = str_replace('[permission]', $permission, $body);
		
		$link = JURI::root()."index.php?option=com_toes&task=cat.grantPermission&cat_id=".$cat_id."&user_id=".$user_id."&permission=".$permission."&token=".time();
		
		$body = str_replace('[link]', $link, $body);
		
		if(TOESMailHelper::sendMail('cat_relation_permission', $subject, $body, $user_emails)){
			$app->enqueueMessage(JText::_('COM_TOES_REQUEST_SENT'));
		} else {
			$app->enqueueMessage(JText::_('COM_TOES_MAIL_SENDING_ERROR'),'error');
		}
		
        
        $app->redirect(JRoute::_('index.php?option=com_toes&view=cats'));
    }
    
    function grantPermission()
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        $user= JFactory::getUser();
        
        /*if(!$user->id)
        {
            $uri = JURI::getInstance();
            
            $return = base64_encode($uri->get('_uri'));
            
            $app->enqueueMessage(JText::_('PLEASE_LOGIN'));
            $app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.$return));
        }*/
        
        $permission = $app->input->getVar('permission');
        $cat_id = $app->input->getVar('cat_id');
        $user_id = $app->input->getVar('user_id');
        $token = $app->input->getvar('token');
        $now = time();

        if($user->id)
        {
            $query = "SELECT person_is 
                FROM #__toes_cat_relates_to_user
                WHERE of_cat =  " . $cat_id ."
                AND person_is = ".$user->id;

            $db->setQuery($query);
            $isRelated = $db->loadResult();
            
            if(!$isRelated)
            {
                $app->enqueueMessage(JText::_('COM_TOES_NOAUTH'),'error');
                $app->redirect(JRoute::_('index.php?option=com_toes&view=cats'));
            }
        }
        
        $diff = (($now-$token)/(60*60*24));
        if(($now-$token)>0 && $diff < 2 )
        {
            $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE cat_user_connection_type  = ".$db->quote($permission);
            $db->setQuery($query);
            $connection_type_id = $db->loadResult();

            $query = "SELECT * FROM `#__toes_cat_relates_to_user` WHERE `person_is` = ".$user_id." AND `of_cat` = ".$cat_id. " AND `cat_user_connection_type` = ".$connection_type_id;
            $db->setQuery($query);
            $isPresent = $db->loadObject();

            if($isPresent)
            {
                $app->enqueueMessage(JText::_('COM_TOES_USER_ALREADY_LINKED'));
                $app->redirect(JRoute::_('index.php?option=com_toes&view=cats'));
                return;
            }

            $query ="INSERT INTO `#__toes_cat_relates_to_user` 
                SET `person_is` = ".$user_id.",
                    `of_cat` = ".$cat_id. ",
                    `cat_user_connection_type` = ".$connection_type_id;
            $db->setQuery($query);

            if($db->query())
            {
                $app->enqueueMessage(JText::_('COM_TOES_REQUEST_ACCPTED'));
            }
            else
            {
                $app->enqueueMessage($db->getErrorMsg(),'error');
            }

            $app->redirect(JRoute::_('index.php?option=com_toes&view=cats'));
        }
        else
        {
            $app->enqueueMessage(JText::_('COM_TOES_REQUEST_TIME_OUT'),'error');
            $app->redirect(JRoute::_('index.php?option=com_toes&view=cats'));
        }
    }
    
    function linkCat()
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        
        $permission = $app->input->getVar('permission');
        $cat_id = $app->input->getVar('cat_id');
        $user_id = $app->input->getVar('user_id');

        $query = "SELECT cat_user_connection_type_id FROM #__toes_cat_user_connection_type WHERE cat_user_connection_type  = ".$db->quote($permission);
        $db->setQuery($query);
        $connection_type_id = $db->loadResult();

        $query = "SELECT * FROM `#__toes_cat_relates_to_user` WHERE `person_is` = ".$user_id." AND `of_cat` = ".$cat_id. " AND `cat_user_connection_type` = ".$connection_type_id;
        $db->setQuery($query);
        $isPresent = $db->loadObject();

        if($isPresent)
        {
            $app->enqueueMessage(JText::_('COM_TOES_USER_ALREADY_LINKED'));
            $app->redirect(JRoute::_('index.php?option=com_toes&view=cat&layout=edit&id='.$cat_id));
            return;
        }

        $query ="INSERT INTO `#__toes_cat_relates_to_user` 
            SET `person_is` = ".$user_id.",
                `of_cat` = ".$cat_id. ",
                `cat_user_connection_type` = ".$connection_type_id;
        $db->setQuery($query);

        if($db->query())
        {
            $app->enqueueMessage(JText::_('COM_TOES_CAT_LINKED'));
        }
        else
        {
            $app->enqueueMessage($db->getErrorMsg(),'error');
        }

        $app->redirect(JRoute::_('index.php?option=com_toes&view=cat&layout=edit&id='.$cat_id));
    }
    
    function removeImage() {
    	$app = JFactory::getApplication();
        $db  = JFactory::getDbo();
				
        $cat_id = $app->input->getVar('cat_id');
        $cat_img_id = $app->input->getInt('cat_img_id');

		$image_path = JPATH_ROOT.'/media/com_toes/cats/'.$cat_id.'/';
		$image_url = JUri::root().'/media/com_toes/cats/'.cat_id.'/';
		
		$query = "SELECT `file_name` FROM `#__toes_cat_images` WHERE `cat_id` = {$cat_id} AND `cat_img_id` = {$cat_img_id}";
		$db->setQuery($query);
		$file_name = $db->loadResult();

		$query = "DELETE FROM `#__toes_cat_images` WHERE `cat_id` = {$cat_id} AND `cat_img_id` = {$cat_img_id}";
		$db->setQuery($query);
		
		if($db->query()) {
			if($file_name && file_exists($image_path.$file_name)) {
				unlink($image_path.$file_name);
			}
			
			echo '1';
		} else {
			echo 'error';
		}
    	
    	$app->close();
    }
	
	function saveChangedDetails() {
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$hash = $app->input->getInt('hash');

		$query = $db->getQuery(true);
		$query->select('id, cat_id, entry_id, hash, created + INTERVAL 3 DAY AS endtime');
		$query->from('#__toes_cat_changes');
		$query->where('hash = '.$db->quote($hash));
		
		$db->setQuery($query);
		$changes = $db->loadObject();
		
		if($changes){
			$endtime = strtotime($changes->endtime);	
			$now = time();
			
			if($now < $endtime) {
				$entry_id =$changes->entry_id;
				$cat_id = $changes->cat_id;
				$entry = TOESHelper::getEntryDetails($entry_id);

				$query = "UPDATE `#__toes_cat` SET 
						cat_breed = " . $db->quote($entry->copy_cat_breed) . ",
						cat_new_trait = " . $db->quote($entry->copy_cat_new_trait) . ",
						cat_category = " . $db->quote($entry->copy_cat_category) . ",
						cat_division = " . $db->quote($entry->copy_cat_division) . ",
						cat_color = " . $db->quote($entry->copy_cat_color) . ",
						cat_name = " . $db->quote($entry->copy_cat_cat_name) . ",
						cat_gender = " . $db->quote($entry->copy_cat_gender) . ",
						cat_hair_length = " . $db->quote($entry->copy_cat_hair_length) . ",
						cat_date_of_birth = " . $db->quote($entry->copy_cat_date_of_birth) . ",
						cat_prefix = " . $db->quote($entry->copy_cat_prefix) . ",
						cat_title = " . $db->quote($entry->copy_cat_title) . ",
						cat_suffix = " . $db->quote($entry->copy_cat_suffix) . ",
						cat_sire = " . $db->quote($entry->copy_cat_sire_name) . ",
						cat_dam = " . $db->quote($entry->copy_cat_dam_name) . ",
						cat_breeder = " . $db->quote($entry->copy_cat_breeder_name) . ",
						cat_owner = " . $db->quote($entry->copy_cat_owner_name) . ",
						cat_lessee = " . $db->quote($entry->copy_cat_lessee_name) . ",
						cat_competitive_region = " . $db->quote($entry->copy_cat_competitive_region) . "
						WHERE cat_id = " .$cat_id;

				//echo nl2br(str_replace('#__', 'j35_', $query));
				$db->setQuery($query);
				if ($db->query()) {

					$query = "UPDATE `#__toes_cat_registration_number` SET 
								cat_registration_number = " . $db->quote($entry->copy_cat_registration_number) . "
								WHERE cat_registration_number_cat = " .$cat_id;
					$db->setQuery($query);
					$db->execute();

					$query = $db->getQuery(true);
					$query->delete('#__toes_cat_changes');
					$query->where('hash = '.$db->quote($hash));
					$db->setQuery($query);
					$db->execute();
					$app->enqueueMessage(JText::_('COM_TOES_CAT_CHANGES_ACCEPTED'));
					$app->redirect(JRoute::_('index.php?option=com_toes&view=shows'));
				} else {
					$app->enqueueMessage(JText::_('COM_TOES_ERROR_DURING_PROCESSING_REQUEST'));
					$app->redirect(JRoute::_('index.php?option=com_toes&view=shows'));
				}
			} else {

				$query = $db->getQuery(true);
				$query->delete('#__toes_cat_changes');
				$query->where('hash = '.$db->quote($hash));
				$db->setQuery($query);
				$db->execute();

				$app->enqueueMessage(JText::_('COM_TOES_URL_IS_EXPIRED'));
				$app->redirect(JRoute::_('index.php?option=com_toes&view=shows'));
			}
			
		} else {
			$this->enqueueMessage(JText::_('COM_TOES_URL_IS_INVALID'));
			$app->redirect(JRoute::_('index.php?option=com_toes&view=shows'));
		}
	}
	
	function cancelChangedDetails() {
		$db = JFactory::getDbo();
		$app = JFactory::getApplication();

		$hash = $app->input->get('hash','');

		$query = $db->getQuery(true);
		$query->delete('#__toes_cat_changes');
		$query->where('hash = '.$db->quote($hash));
		$db->setQuery($query);
		$db->execute();
		
		$app->enqueueMessage(JText::_('COM_TOES_CAT_CHANGES_REJECTED'));
		$app->redirect(JRoute::_('index.php?option=com_toes&view=shows'));
	}
}
