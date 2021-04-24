<?php

/**
 * @service	Joomla
 * @subservice	com_toes
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

/**
 * Template style model.
 *
 * @service	Joomla
 * @subservice	com_toes
 */
class TOESModelShow extends JModelList {

	/**
	 * Method to auto-populate the model state.
	 */
	protected function populateState($ordering = null, $direction = null) {
		$entry_status = $this->getUserStateFromRequest($this->context . '.filter.entry_status', 'entry_status_filter', '');
		$this->setState('filter.entry_status', $entry_status);

		$entry_user = $this->getUserStateFromRequest($this->context . '.filter.entry_user', 'entry_user_filter', '');
		$this->setState('filter.entry_user', $entry_user);

		parent::populateState();
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null) {
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);
		//cntry.name AS address_country, cntry.country_uses_states->$query->join('left', '`#__toes_country` AS `cntry` ON `cntry`.`id` = `a`.`address_country`');
		//state.name AS address_state->$query->join('left', '`#__toes_states_per_country` AS `state` ON `state`.`id` = `a`.`address_state`');
		//city.name AS address_city->$query->join('left', '`#__toes_cities_per_state` AS `city` ON `city`.`id` = `a`.`address_city`');
		$query->select(' s.show_format ,s.show_id, s.show_start_date, s.show_end_date, s.show_flyer, s.show_motto, s.catalog_runs, s.show_extra_text_for_confirmation, s.show_currency_used, s.show_comments, s.show_cost_per_entry, s.show_total_cost, s.show_uses_toes, s.show_uses_ticapp, s.show_is_regional, s.show_is_annual, s.show_bring_your_own_cages, s.show_use_club_entry_clerk_address, s.show_email_address_entry_clerk, s.show_use_club_show_manager_address, s.show_email_address_show_manager, s.show_display_counts, s.show_use_waiting_list');
		$query->select(' s.show_cost_total_entries, s.show_cost_ex_only_entries, s.show_maximum_cost, s.show_cost_fixed_rebate, s.show_cost_procentual_rebate, s.show_cost_invoice_date, s.show_cost_amount_paid ');
		$query->select(' s.show_print_extra_lines_for_bod_and_bob_in_judges_book, s.show_print_extra_line_at_end_of_color_class_in_judges_book, s.show_licensed');
		$query->select(' s.show_print_division_title_in_judges_books, s.show_allow_exhibitor_cancellation');
		$query->select(' s.show_catalog_font_size, s.show_colored_catalog, s.show_catalog_cat_names_bold, s.show_catalog_page_orientation,s.show_venue');
		$query->from('#__toes_show AS s');
		
		$query->select('st.show_status');
		$query->join('LEFT', '#__toes_show_status AS st ON st.show_status_id = s.show_status');

		$query->select(' v.venue_name, a.address_line_1, a.address_line_2, a.address_line_3,
						a.address_city AS address_city, a.address_state AS address_state, a.address_zip_code,
						 a.address_country AS address_country,a.address_latitude,a.address_longitude,a.address_zip_code');
		$query->join('LEFT', '#__toes_venue AS v ON v.venue_id = s.show_venue');
		$query->join('LEFT', '#__toes_address AS a ON a.address_id = v.venue_address');
		$query->select('c.club_id, c.club_name, c.club_abbreviation');
		$query->join('LEFT', '#__toes_club_organizes_show AS cs ON cs.show = s.show_id');
		$query->join('LEFT', '#__toes_club AS c ON c.club_id = cs.club');

		$query->where('s.show_id=' . (int) $pk);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObject();
	}

	public function getShowDays() {
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);

		$query->select('sd.*');
		$query->from('#__toes_show_day AS sd');
		$query->where('`sd`.`show_day_show` = ' . (int) $pk);
		$query->order('sd.show_day_date ASC');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getEntryClerks() {
		$app = JFactory::getApplication();
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);
		//cprofcntry.name AS entry_clerk_address_country->$query->join("left", "`#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `cprof`.`cb_country`");
		//cprofstate.name AS entry_clerk_address_state->$query->join("left", "`#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `cprof`.`cb_state`");
		//cprofcity.name AS entry_clerk_address_city->$query->join("left", "`#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `cprof`.`cb_city`");
		$query->select('`s`.`show_id`');
		$query->from('`#__toes_show` AS `s`');

		$query->select(' u.name AS entry_clerk_name, u.email AS entry_clerk_email');
		$query->select(' cprof.cb_address1 AS entry_clerk_address_line_1, cprof.cb_address2 AS entry_clerk_address_line_2, cprof.cb_address3 AS entry_clerk_address_line_3');
		$query->select(' cprof.cb_zip AS entry_clerk_address_zip_code');
		$query->select(' cprof.cb_phonenumber AS entry_clerk_phone_number, cprof.cb_privacy AS private');

		$query->join('LEFT', '`#__toes_show_has_official` AS `so2` ON `so2`.`show` = `s`.`show_id` AND `so2`.`show_official_type` = 2');
		$query->join('LEFT', '`#__users` AS `u` ON `u`.`id` = `so2`.`user`');
		$query->join("left", '`#__comprofiler` AS `cprof` ON `u`.`id` = `cprof`.`user_id`');
		$query->where('s.show_id=' . (int) $pk);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$return = $db->loadObjectList();

		$this->_cache[$pk] = $return;

		return $this->_cache[$pk];
	}

	public function getShowManagers() {
		$app = JFactory::getApplication();
		$db = $this->getDbo();

		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);

		$query->select('s.show_id');
		$query->from('#__toes_show AS s');

		$query->select(' u.name AS show_manager_name, u.email AS show_manager_email, cprof.cb_phonenumber AS show_manager_phone_number, cprof.cb_privacy AS private');
		$query->join('LEFT', '#__toes_show_has_official AS so1 ON so1.show = s.show_id AND so1.show_official_type = 1');
		$query->join('LEFT', '#__users AS u ON u.id = so1.user');
		$query->join("left", '`#__comprofiler` AS `cprof` ON `u`.`id` = `cprof`.`user_id`');

		$query->where('s.show_id=' . (int) $pk);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$return = $db->loadObjectList();

		$this->_cache[$pk] = $return;

		return $this->_cache[$pk];
	}

	public function getSummary() {
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$user = JFactory::getUser();

		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);

		$query->select(' smry.summary_id, smry.summary_single_cages, smry.summary_double_cages, smry.summary_benching_request, smry.summary_grooming_space, smry.summary_personal_cages, smry.summary_remarks');
		$query->from('#__toes_summary AS smry');

		$query->where('smry.summary_show =' . (int) $pk);
		$query->where('smry.summary_user =' . $user->id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$summary = $db->loadObject();

		return $summary;
	}

	public function getJudges() {
		$db = $this->getDbo();
		$app = JFactory::getApplication();
		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);

		$query->select('j.judge_id, ju.name, js.judge_status, jl.judge_level, rf.ring_format, r.ring_timing, r.ring_name, sd.show_day_id, sd.show_day_date');
		$query->from('#__toes_show AS s');
		$query->join('LEFT', '#__toes_ring AS r ON r.ring_show = s.show_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = r.ring_show_day');
		$query->join('LEFT', '#__toes_ring_format AS rf ON rf.ring_format_id = r.ring_format');
		$query->join('LEFT', '#__toes_judge AS j ON j.judge_id = r.ring_judge');
		$query->join('LEFT', '#__users AS ju ON ju.id = j.user');
		$query->join('LEFT', '#__toes_judge_status AS js ON js.judge_status_id = j.judge_status');
		$query->join('LEFT', '#__toes_judge_level AS jl ON jl.judge_level_id = j.judge_level');

		$query->where('s.show_id=' . (int) $pk);
		$query->where('rf.ring_format != "Congress"');
		//$query->group('j.judge_id');
		$query->order('r.ring_show_day ASC, r.ring_timing ASC, r.ring_number ASC, ju.name ASC');
		//$query->order('ju.name ASC');
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getCongressJudges() {
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);

		$query->select('j.judge_id, ju.name, js.judge_status, jl.judge_level, r.ring_timing, r.ring_name, sd.show_day_id, sd.show_day_date');
		$query->from('#__toes_show AS s');
		$query->join('LEFT', '#__toes_ring AS r ON r.ring_show = s.show_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = r.ring_show_day');
		$query->join('LEFT', '#__toes_ring_format AS rf ON rf.ring_format_id = r.ring_format');
		$query->join('LEFT', '#__toes_judge AS j ON j.judge_id = r.ring_judge');
		$query->join('LEFT', '#__users AS ju ON ju.id = j.user');
		$query->join('LEFT', '#__toes_judge_status AS js ON js.judge_status_id = j.judge_status');
		$query->join('LEFT', '#__toes_judge_level AS jl ON jl.judge_level_id = j.judge_level');

		$query->where('s.show_id=' . (int) $pk);
		$query->where('rf.ring_format = "Congress"');
		//$query->group('j.judge_id');
		$query->order('r.ring_show_day ASC, r.ring_timing ASC, r.ring_number ASC, ju.name ASC');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 */
	public function getTable($type = 'Show', $prefix = 'ToesTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}
	
	function getVenue(){
	$app = JFactory::getApplication();
	$db = $this->getDbo();
	$query = $db->getQuery(true);	
	$venue_id = $app->input->getInt('venue_id');
	$db->setQuery("select v.venue_name,a.address_latitude,a.address_longitude,a.address_line_1,a.address_line_2,a.address_line_3,a.address_zip_code,a.address_city,
	a.address_state,a.address_country  from `#__toes_venue` as v JOIN `#__toes_address` as a ON v.venue_address = a.address_id 
	where v.venue_id =".$venue_id);	
	return $db->loadObject();			
	}

	public function getRings() {
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$pk = $app->input->getInt('id', 0);

		if ($pk) {
			//    $query->select('r.*, sd.show_day_date as show_day');
			//    $query->from('#__toes_ring as r');
			//    $query->join('LEFT', '#__toes_show_day AS sd ON r.ring_show_day=sd.show_day_id');
			//    $query->where('r.ring_show =' . (int) $pk);

			$query = "SELECT r.*, sd.show_day_date as show_day, concat(concat(cb.firstname,' ',cb.lastname),' - ',tjl.judge_level) as ring_judge_name, concat(cb2.firstname,' ',cb2.lastname) as ring_clerk_name
                FROM #__toes_ring as r
                LEFT JOIN #__toes_show_day AS sd ON r.ring_show_day=sd.show_day_id
                LEFT JOIN #__toes_judge as tj ON r.ring_judge  = tj.judge_id
                LEFT JOIN #__comprofiler as cb ON tj.user = cb.user_id
                LEFT JOIN #__toes_judge_level AS tjl ON tjl.judge_level_id = tj.judge_level 
                LEFT JOIN #__comprofiler as cb2 ON r.ring_clerk = cb2.user_id
                WHERE r.ring_show =" . (int) $pk;

			$db->setQuery($query);
			return $db->loadObjectlist();
		}
	}

	public function getRingjudgs() {
		$db = $this->getDbo();

		$query = "SELECT concat(concat(cb.firstname,' ',cb.lastname),' - ',tjl.judge_level) as text, tj.judge_id as value 
                FROM #__toes_judge as tj 
                LEFT JOIN #__users AS u ON tj.user = u.id 
                LEFT JOIN #__comprofiler as cb ON u.id  = cb.user_id
                LEFT JOIN #__toes_judge_level AS tjl ON tjl.judge_level_id = tj.judge_level 
                ORDER BY cb.lastname";
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TOES_SELECT_JUDGE')));
		return $options;
	}

	public function getRingformats() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('ring_format as text, ring_format_id as value');
		$query->from('#__toes_ring_format');

		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TOES_SELECT')));
		return $options;
	}

	public function getRingTimings() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('timing as text, ring_timing_id as value');
		$query->from('#__toes_ring_timing');

		$db->setQuery($query);
		$options = $db->loadObjectList();
		return $options;
	}

	public function getClubs() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		$query->select('c.club_id AS value, concat(c.club_name,\'(\',c.club_abbreviation,\')\') AS text');
		$query->from('#__toes_club as c');
		$query->where('c.club_organization = 1');
		$query->order('c.club_name ASC');

		if (!TOESHelper::isAdmin() && TOESHelper::is_clubofficial($user->id)) {
			$query->join('LEFT', '#__toes_club_official AS co ON co.club = c.club_id');
			$query->where('co.user = ' . $user->id);
		}

		//echo str_replace('#_', 'j35', nl2br($query));
		$db->setQuery($query);
		$options = $db->loadObjectList();

		$option_list = array();
		$option_list[] = JHtml::_('select.option', '', JText::_('COM_TOES_SELECT_CLUB'));
		$options = array_merge($option_list, $options);
		return $options;
	}

	public function getShowofficialtype() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query = "SELECT * FROM #__toes_show_official_type ";

		// Get the options.
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}

	public function getshowformats() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('show_format_id AS value, show_format AS text');
		$query->from('#__toes_show_format');
		$query->where('show_format_organization = 1');
		$query->order('show_format_id ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT')));
		return $options;
	}

	public function getfontsizes() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		$query->select('font_size_size_value AS value, font_size_size_name AS text');
		$query->from('#__toes_font_size');
	
		// Get the options.
		$db->setQuery($query);
	
		$options = $db->loadObjectList();
		//array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT')));
		return $options;
	}
	

	public function getpageorientations() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
	
		$query->select('id AS value, page_ortientation AS text');
		$query->from('#__toes_catalog_page_orientation');
	
		// Get the options.
		$db->setQuery($query);
	
		$options = $db->loadObjectList();
		//array_unshift($options, JHtml::_('select.option', '', JText::_('COM_TOES_SELECT')));
		return $options;
	}
	
	public function getEntrystatuses() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('entry_status as value, entry_status as text');
		$query->from('#__toes_entry_status');

		//echo nl2br(str_replace('#_', 'j35', $query));
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TOES_SELECT_STATUS')));

		return $options;
	}

	public function getEntryusers() {
		$app = JFactory::getDbo();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$show_id = $app->input->getInt('id', 0);

		$query->select('distinct(`s`.`summary_user`) as `value`, concat(`c`.`firstname`," ",`c`.`lastname`) as `text`');
		$query->from('`#__toes_summary` AS `s`');
		$query->join('left', '`#__comprofiler` AS `c` ON `c`.`id` = `s`.`summary_user`');
		$query->where('`s`.`summary_show` = '.$show_id);
		
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$options = $db->loadObjectList();
		array_unshift($options, JHtml::_('select.option', '0', JText::_('COM_TOES_SELECT_USER')));

		return $options;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 * @return	boolean	True on success.
	 */
	public function save($data) {
		//var_dump($data);
		//die;
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$datetime  = JFactory::getDate();
		
		if($data['id'])
		$isnew = false;
		else
		$isnew = true;

		$session = JFactory::getSession();
		$filters = array();
		if ($session->has('congress_filters')) {
			$str = $session->get('congress_filters');
			$filters = unserialize($str);
		}

		$data['show_comments'] = $app->input->get('show_comments', '', 'raw');
		$data['show_extra_text_for_confirmation'] = $app->input->get('show_extra_text_for_confirmation', '', 'raw');
		
		
		 
		$processvenue = true;	
		if(!$isnew){
		if(isset($data['edit_venue']) && $data['edit_venue'])	
		$processvenue = true;
		else
		$processvenue = false;	
			
		}	
		 
		if($processvenue){
		$query = "SELECT * FROM #__toes_venue WHERE venue_name  = " . $db->quote($data['venue_name']);
		$db->setQuery($query);
		$venue_detail = $db->loadObject();
		
		//radius search using latitude and longitude
			$address = $data['venue_name'];
			$params = JComponentHelper::getParams('com_toes');
			$radius = $params->get('show_miles');
			
			
			
			if($radius > 0 && $isnew){
				if($radius && $address && $data['lat']!== '0.00000000' && $data['lng']!== '0.00000000' && $data['lat']!== '' && $data['lng']!== ''){
					$query3 = $db->getQuery(true);
					$query3 .='select s.show_id,v.venue_id,a.address_latitude,a.address_longitude,s.show_start_date,s.show_end_date';
					$query3 .=' ,(ACOS( SIN(RADIANS('.$data['lat'].')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$data['lat'].')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$data['lng'].')) ) * 3963.1676) AS distance'; 
					$query3	.=' FROM `#__toes_show` as s ';
					$query3 .=' LEFT join `#__toes_venue` as v ON s.`show_venue` = v.`venue_id`';
					$query3 .=' LEFT join `#__toes_address` as a ON v.`venue_address` = a.`address_id`';
					$query3 .=' WHERE ((( s.`show_end_date` BETWEEN '.$db->quote($data['show_start_date']).' AND ' .$db->quote($data['show_end_date']). ' ) OR
								( s.`show_start_date` BETWEEN '.$db->quote($data['show_start_date']).' AND ' .$db->quote($data['show_end_date']). ' )) AND 
								( s.show_status = 2  OR s.show_status = 1 OR s.show_status = 4 OR s.show_status = 7) AND
								(((ACOS( SIN(RADIANS('.$data['lat'].')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$data['lat'].')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$data['lng'].')) ) * 3963.1676) <= '.$radius.') OR (a.address_latitude = '.$data['lat'].' AND a.address_longitude = '.$data['lng'].')) AND s.show_id !='.$db->quote($data['id']).')';
					$db->setQuery($query3);
					/*
					$query3 = $db->getQuery(true);
					$query3 .='select s.show_id,v.venue_id';
					$query3 .= ' ,(3959 * acos( cos( radians('.$data['lat'].'))* cos(radians(a.address_latitude))*cos(radians(a.address_longitude)-radians('.$data['lng'].'))+sin(radians('.$data['lng'].') )*sin(radians(a.address_latitude))))
								AS distance ' ;
					$query3	.=' FROM `#__toes_show` as s ';
					$query3 .='LEFT join `#__toes_venue` as v ON s.`show_venue` = v.`venue_id`';
					$query3 .='LEFT join `#__toes_address` as a ON v.`venue_address` = a.`address_id`';
					$query3 .=' WHERE  ( ( s.`show_end_date` BETWEEN '.$db->quote($data['show_start_date']).' AND ' .$db->quote($data['show_end_date']). ' )  
								AND ( s.`show_start_date` BETWEEN '.$db->quote($data['show_start_date']).' AND ' .$db->quote($data['show_end_date']). ' )
								 AND ( s.show_status = 2  OR s.show_status = 1 OR s.show_status = 4 OR s.show_status = 7) AND s.show_id !='.$db->quote($data['id']).' AND (a.address_latitude <> \'0.00000000\' AND a.address_longitude <> \'0.00000000\') )';
					$query3 .= ' HAVING distance <= '.$radius;//.' OR distance IS NULL';//
					
					//echo $query3; AND s.show_id !='.$db->quote($data['id']).'
					$db->setQuery($query3);
					*/ 
					$radiusresult = $db->loadObjectList();
					/* 
					echo $query3;
					echo '<pre>';
					var_dump($radiusresult);
					echo '</pre>';
					*/
					//die;
					 
				 
				}	
			}
		
		
		if ($venue_detail->venue_id) {
			/* $query = "UPDATE #__toes_address SET address_line_1 = " . $db->quote($data['address_line_1']) . " 
			  , address_line_2= " . $db->quote($data['address_line_2']) . "
			  , address_line_3= " . $db->quote($data['address_line_3']) . "
			  , address_zip_code= " . $db->quote($data['address_zip_code']) . "
			  , address_city= " . $db->quote($data['address_city']) . "
			  , address_state= " . $db->quote($data['address_state']) . "
			  , address_country= " . $db->quote($data['address_country']) . "
			  , address_type= 1
			  WHERE address_id= " . $db->quote($venue_detail->venue_address) . "  ";
			  $db->setQuery($query);
			  $db->query(); */

			$venue_id = $venue_detail->venue_id;
			//var_dump($venue_id);
			$query = "UPDATE `#__toes_address` SET 
                    `address_latitude`= " .$db->quote($data['lat']). " , `address_longitude` = " .$db->quote($data['lng']). ",
                    `address_country` = ".$db->quote($data['address_country_name']).",`address_zip_code`=".$db->quote($data['address_zip_code']).",
                    `address_line_2` = ".$db->quote($data['address_line_2']).",`address_city`=".$db->quote($data['address_city_name']).",
                    `address_line_3` = ".$db->quote($data['address_line_3']).",`address_line_1`=".$db->quote($data['address_line_1']).",
                    `address_state` = ".$db->quote($data['address_state_name'])."
						where address_id =".$db->quote($venue_detail->venue_address);
                     //echo $query;die;
			$db->setQuery($query);
			$db->query();
		} else {
			$query = "INSERT INTO `#__toes_address` 
                    (`address_line_1`, `address_line_2`, `address_line_3`, `address_zip_code`
                    , `address_city`, `address_state`, `address_country`, `address_type`,`address_latitude`,`address_longitude`)
                     VALUES (" . $db->quote($data['address_line_1']) . "," . $db->quote($data['address_line_2']) . "," . $db->quote($data['address_line_3']) . "
                     ," . $db->quote($data['address_zip_code']) . "," . $db->quote($data['address_city_name']) . "," . $db->quote($data['address_state_name']) . "
                     ," . $db->quote($data['address_country_name']) . ",1," .$db->quote($data['lat']). "," .$db->quote($data['lng']).")";
			$db->setQuery($query);
			$db->query();

			$address_id = $db->insertid();

			$query = "INSERT INTO `#__toes_venue` 
                    (`venue_name`, `venue_address`)
                    VALUES (" . $db->quote($data['venue_name']) . "," . $db->quote($address_id) . ")";
			$db->setQuery($query);
			$db->query();

			$venue_id = $db->insertid();
		}
		}

		$query = "SELECT show_format_id FROM #__toes_show_format WHERE show_format = " . $db->quote('Alternative');
		$db->setQuery($query);
		$alternative_id = $db->loadResult();

		$region = TOESHelper::getRegionDetails(TOESHelper::getClubDetails($data['club'])->club_competitive_region);
		$need_approval = false;

		// Log changes
		$date_changed = 0;
		$location_changed = 0;
		$format_changed = 0;
		$status_changed = 0;
		$judges_changed = 0;
		$rings_changed = 0;
		$desc_changed = 0;

		if ($data['id']) {
			$query = "SELECT * FROM `#__toes_show` WHERE `show_id` = " . $data['id'];
			$db->setQuery($query);
			$org_show_details = $db->loadObject();
			//$org_show_details = TOESHelper::getShowDetails($data['id']);
			$send_rd_mail = false;

			if ($processvenue && $org_show_details->show_venue != $venue_id )
				$location_changed = 1;

			if ($org_show_details->show_start_date != $data['show_start_date'] || $org_show_details->show_end_date != $data['show_end_date'])
				$date_changed = 1;

			if ($org_show_details->show_comments != $data['show_comments'])
				$desc_changed = 1;

			if ($org_show_details->show_format != $data['show_format'])
				$format_changed = 1;

			if ( ($processvenue && $org_show_details->show_venue != $venue_id) || $org_show_details->show_start_date != $data['show_start_date'] || $org_show_details->show_end_date != $data['show_end_date']) {
				$send_rd_mail = true;
				if ($region->competitive_region_confirmation_by_rd_needed) {
					if ($org_show_details->show_status != 1)
						$status_changed = 1;

					$show_status = ', show_status = 1';
					$need_approval = true;
				}
				else
					$show_status = '';
			}
			else
				$show_status = '';

			//where show_id is available in case edited form save
			$query = "UPDATE #__toes_show SET show_start_date = " . $db->quote($data['show_start_date']) . " 
                    , show_end_date= " . $db->quote($data['show_end_date']) . "
                    , show_published= 1
                    , show_organization= 1
                    " . $show_status . "
                    , show_format= " . $db->quote($data['show_format']) . "
                    , show_extra_text_for_confirmation= " . $db->quote($data['show_extra_text_for_confirmation']) . "
                    , show_comments= " . $db->quote($data['show_comments']) . "
                    , show_currency_used= " . $db->quote($data['show_currency_used']) . "
                    , show_motto= " . $db->quote($data['show_motto']);
            if($processvenue)
                $query .= " , show_venue= " . $db->quote($venue_id);
			if ($data['show_flyer'])
				$query .= " , show_flyer= " . $db->quote($data['show_flyer']);
			if (isset($data['show_uses_toes']))
				$query .= " , show_uses_toes = 1";
			else
				$query .= " , show_uses_toes = 0";

			if (isset($data['show_uses_ticapp']))
				$query .= " , show_uses_ticapp = 1";
			else
				$query .= " , show_uses_ticapp = 0";

			if (isset($data['show_is_regional']))
				$query .= " , show_is_regional = 1";
			else
				$query .= " , show_is_regional = 0";

			if (isset($data['show_is_annual']))
				$query .= " , show_is_annual = 1";
			else
				$query .= " , show_is_annual = 0";

			if (isset($data['show_display_counts']))
				$query .= " , show_display_counts = 1";
			else
				$query .= " , show_display_counts = 0";

			if ($data['show_format'] != $alternative_id && isset($data['show_use_waiting_list']))
				$query .= " , show_use_waiting_list = 1";
			else
				$query .= " , show_use_waiting_list = 0";

			if (isset($data['show_bring_your_own_cages']))
				$query .= " , show_bring_your_own_cages = 1";
			else
				$query .= " , show_bring_your_own_cages = 0";
			
			if (isset($data['show_catalog_cat_names_bold']))
				$query .= " , show_catalog_cat_names_bold = 1";
			else
				$query .= " , show_catalog_cat_names_bold = 0";
			
			if (isset($data['show_colored_catalog']))
				$query .= " , show_colored_catalog = 1";
			else
				$query .= " , show_colored_catalog = 0";
				
			$query .= " , show_catalog_font_size = ".$data['show_catalog_font_size'];
			$query .= " , show_catalog_page_orientation = ".$data['show_catalog_page_orientation'];

			if (isset($data['show_allow_exhibitor_cancellation']))
				$query .= " , show_allow_exhibitor_cancellation = 1";
			else
				$query .= " , show_allow_exhibitor_cancellation = 0";
				
			$query .= " , show_licensed = ".$data['show_licensed'];

			if ($data['show_cost_per_entry'])
				$query .= " , show_cost_per_entry= " . $db->quote($data['show_cost_per_entry']);
			if ($data['show_total_cost'])
				$query .= " , show_total_cost= " . $db->quote($data['show_total_cost']);
			if ($data['show_cost_total_entries'])
				$query .= " , show_cost_total_entries= " . $db->quote($data['show_cost_total_entries']);
			if ($data['show_cost_ex_only_entries'])
				$query .= " , show_cost_ex_only_entries= " . $db->quote($data['show_cost_ex_only_entries']);
			if ($data['show_maximum_cost'])
				$query .= " , show_maximum_cost= " . $db->quote($data['show_maximum_cost']);
			if ($data['show_cost_fixed_rebate'])
				$query .= " , show_cost_fixed_rebate= " . $db->quote($data['show_cost_fixed_rebate']);
			if ($data['show_cost_procentual_rebate'])
				$query .= " , show_cost_procentual_rebate= " . $db->quote($data['show_cost_procentual_rebate']);
			if ($data['show_cost_invoice_date'])
				$query .= " , show_cost_invoice_date= " . $db->quote($data['show_cost_invoice_date']);
			if ($data['show_cost_amount_paid'])
				$query .= " , show_cost_amount_paid= " . $db->quote($data['show_cost_amount_paid']);

			if (isset($data['show_print_division_title_in_judges_books']))
				$query .= " , show_print_division_title_in_judges_books = 1";
			else
				$query .= " , show_print_division_title_in_judges_books = 0";
			
			if (isset($data['show_print_extra_lines_for_bod_and_bob_in_judges_book']))
				$query .= " , show_print_extra_lines_for_bod_and_bob_in_judges_book = 1";
			else
				$query .= " , show_print_extra_lines_for_bod_and_bob_in_judges_book = 0";
			
			if (isset($data['show_print_extra_line_at_end_of_color_class_in_judges_book']))
				$query .= " , show_print_extra_line_at_end_of_color_class_in_judges_book = 1";
			else
				$query .= " , show_print_extra_line_at_end_of_color_class_in_judges_book = 0";
			
			if (isset($data['show_use_club_entry_clerk_address']))
				$query .= " , show_use_club_entry_clerk_address = 1";
			else
				$query .= " , show_use_club_entry_clerk_address = 0";

			$query .= " , show_email_address_entry_clerk = {$db->quote($data['show_email_address_entry_clerk'])}";

			if (isset($data['show_use_club_show_manager_address']))
				$query .= " , show_use_club_show_manager_address = 1";
			else
				$query .= " , show_use_club_show_manager_address = 0";

			$query .= " , show_email_address_show_manager = {$db->quote($data['show_email_address_show_manager'])}";

			$query .= " WHERE show_id= " . $db->quote($data['id']) . "  ";
			
			$db->setQuery($query);
			if ($db->query()) {
				$show_id = $data['id'];
			} else {
				$this->setError($db->getErrorMsg());
				return false;
			}

			$show_id = $data['id'];
			// sandy hack #https://ticaorg.atlassian.net/browse/TICAORG-95
			/*
			$org_start_date = date_create($org_show_details->show_start_date);
			$new_start_date = date_create($data['show_start_date']);
			$current_date = date_create("now");
			
			if(
			( abs(date_diff($org_start_date,$new_start_date)) >= 1)
			||
			($processvenue &&  date_diff($current_date,$new_start_date) < 30 )
			
			){
				 
				
				// send email to regional director
				$query1 = $db->getQuery(true);
				$query1->select('co.firstname,co.lastname,tc.club_competitive_region,comp.competitive_region_regional_director');
				$query1->from('#__toes_club_organizes_show as s');
				$query1->join('LEFT','#__toes_club as tc ON s.club = tc.club_id');
				$query1->join('LEFT','#__toes_competitive_region as comp ON tc.club_competitive_region = comp.competitive_region_id');
				$query1->join('LEFT','#__comprofiler as co ON comp.competitive_region_regional_director = co.user_id');
				$query1->where('s.show='.$show_id);
				$db->setQuery($query1);
				$regionaldirector = $db->loadObject();
				
				
				$mailTemplate = TOESMailHelper::getTemplate('show_edited_start_date_location_changed');
				$subject = $mailTemplate->mail_subject;
				$body = $mailTemplate->mail_body;				 
				 
				TOESMailHelper::sendMail('show_edited_start_date_location_changed', $subject, $body, $regionaldirector->email);
				 
			}
			
			
			
			*/
			// end hack
			
			
		} else {

			if ($region->competitive_region_confirmation_by_rd_needed) {
				$show_status = '1';
				$need_approval = true;
			}
			else
				//spider
				if(count($radiusresult) > 0)
				{
					$show_status = '1';
				}
				else
				{
					$show_status = '2';
				}
				
				//$show_status = '2';

			$club = TOESHelper::getClubDetails($data['club']);

			//in case new show is saved
			$query = "INSERT INTO `#__toes_show` 
                    (`show_start_date`, `show_end_date`, `show_format`, `show_venue`, `show_published`, `show_organization`, `show_status`, `show_flyer`, `show_motto`, `show_extra_text_for_confirmation`, `show_currency_used`, `show_comments`, `show_cost_per_entry`, `show_uses_toes`, `show_uses_ticapp`, `show_bring_your_own_cages`, `show_use_club_entry_clerk_address`, `show_email_address_entry_clerk`, `show_use_club_show_manager_address`, `show_email_address_show_manager`, 
					`show_is_regional`, `show_is_annual`, `show_display_counts`, `show_use_waiting_list`, `show_print_division_title_in_judges_books`, `show_print_extra_lines_for_bod_and_bob_in_judges_book` , `show_print_extra_line_at_end_of_color_class_in_judges_book`,
					`show_catalog_font_size`, `show_catalog_page_orientation`, `show_colored_catalog`, `show_allow_exhibitor_cancellation`, `show_catalog_cat_names_bold`)
                    VALUES (" . $db->quote($data['show_start_date']) . ","
					. $db->quote($data['show_end_date']) . ","
					. $db->quote($data['show_format']) . ","
					. $db->quote($venue_id) . ", 1, 1,"
					. $show_status . ","
					. $db->quote($data['show_flyer']) . ","
					. $db->quote($data['show_motto']) . ","
					. $db->quote($data['show_extra_text_for_confirmation']) . ","
					. $db->quote($data['show_currency_used']) . ","
					. $db->quote($data['show_comments']) . ","
					. $db->quote($club->club_cost_per_entry) . ","
					. (@$data['show_uses_toes'] ? '1' : '0') . ","
					. (@$data['show_uses_ticapp'] ? '1' : '0') . ","
					. (@$data['show_bring_your_own_cages'] ? '1' : '0') . ","
					. (@$data['show_use_club_entry_clerk_address'] ? '1' : '0') . ","
					. $db->quote($data['show_email_address_entry_clerk']) . ","
					. (@$data['show_use_club_show_manager_address'] ? '1' : '0') . ","
					. $db->quote($data['show_email_address_show_manager']) . ","
					. (@$data['show_is_regional'] ? '1' : '0') . ","
					. (@$data['show_is_annual'] ? '1' : '0') . ","
					. (@$data['show_display_counts'] ? '1' : '0') . ","
					. ($data['show_format'] != $alternative_id && @$data['show_use_waiting_list'] ? '1' : '0') . ","
					. (@$data['show_print_division_title_in_judges_books'] ? '1' : '0') . ","
					. (@$data['show_print_extra_lines_for_bod_and_bob_in_judges_book'] ? '1' : '0') . ","
					. (@$data['show_print_extra_line_at_end_of_color_class_in_judges_book'] ? '1' : '0') . ","
					. $db->quote($data['show_catalog_font_size']) . ","
					. $db->quote($data['show_catalog_page_orientation']) . ","
					. (@$data['show_colored_catalog'] ? '1' : '0') . ","
					. (@$data['show_allow_exhibitor_cancellation'] ? '1' : '0') . ","
					. (@$data['show_catalog_cat_names_bold'] ? '1' : '0')
					. ")";
			$db->setQuery($query);
			
			if ($db->query()) {
				$show_id = $db->insertid();
				$send_rd_mail = true;
			} else {
				$this->setError($db->getErrorMsg());
				return false;
			}
				
		}

		$query = "SELECT * FROM #__toes_club_organizes_show WHERE `show` = " . $db->quote($show_id);
		$db->setQuery($query);
		$club_show_relation = $db->loadObject();

		if ($club_show_relation) {
			$query = "UPDATE #__toes_club_organizes_show 
                    SET `club` = " . $db->quote($data['club']) . " 
                    WHERE `show` = " . $db->quote($show_id);
			$db->setQuery($query);
			$db->query();
		} else {
			$query = "INSERT INTO `#__toes_club_organizes_show` 
                    (`club`, `show`) VALUES (" . $db->quote($data['club']) . "," . $db->quote($show_id) . ")";
			$db->setQuery($query);
			$db->query();
		}

		if ($send_rd_mail)
			$this->sendMailtoRD($show_id, $region, $need_approval);

		$org_show_days = TOESHelper::getShowDays($show_id);
		$transfer_to_waiting = false;
		$check_waiting_list = false;

		$org_show_day_ids = explode(',', $data['org_show_day_ids']);
		$org_show_day_dates = explode(',', $data['org_show_day_dates']);

		$end_date = strtotime($data['show_end_date']);
		$start_date = strtotime($data['show_start_date']);
		$datediff = $end_date - $start_date;
		$diffday = floor($datediff / (60 * 60 * 24));

		$query = "SELECT show_format_id FROM #__toes_show_format WHERE show_format = " . $db->quote('Continuous');
		$db->setQuery($query);
		$continuous_id = $db->loadResult();

		if ($continuous_id == $data['show_format'])
			$end_date = $start_date;

		if (count($org_show_day_ids) > $diffday)
			$diffday = count($org_show_day_ids);

		for($i = 0; $i <= $diffday; $i++) {
			$date = strtotime("+$i days", strtotime($data['show_start_date']));
			if ($date >= $start_date && $date <= $end_date) {
				if ($org_show_day_ids[$i]) {
					$org_cat_limit = 0;
					foreach($org_show_days as $day) {
						if ($org_show_day_ids[$i] == $day->show_day_id)
							$org_cat_limit = $day->show_day_cat_limit;
					}

					$query = "UPDATE #__toes_show_day 
                            SET show_day_date = " . $db->quote(date('Y-m-d', $date)) . ",
                                show_day_cat_limit = " . $data['show_day_cat_limit_' . $i] . "
                            WHERE show_day_id = " . $db->quote($org_show_day_ids[$i]);
					$db->setQuery($query);
					$db->query();

					if ($data['show_day_cat_limit_' . $i] < $org_cat_limit) {
						$transfer_to_waiting = true;
					} elseif ($data['show_day_cat_limit_' . $i] > $org_cat_limit) {
						$check_waiting_list = true;
					}
				} else {
					$query = "INSERT INTO `#__toes_show_day` (`show_day_show`, `show_day_date`, `show_day_cat_limit`) VALUES (" . $db->quote($show_id) . "," . $db->quote(date('Y-m-d', $date)) . ", " . $data['show_day_cat_limit_' . $i] . ")";
					$db->setQuery($query);
					$db->query();
				}
			} else if (isset($org_show_day_ids[$i])) {
				$query = "DELETE FROM `#__toes_show_day` WHERE show_day_id = " . $org_show_day_ids[$i];
				//echo ' delete show day<br/> '.$query.'<br/><br/>';
				$db->setQuery($query);
				$db->query();

				$query = "DELETE FROM `#__toes_ring` WHERE ring_show_day = " . $org_show_day_ids[$i];
				//echo ' delete ring<br/> '.$query.'<br/><br/>';
				$db->setQuery($query);
				$db->query();
			}
		}

		$org_ring_ids = explode(',', $data['org_ring_ids']);
		$org_ring_days = explode(',', $data['org_ring_days']);

		//select ring format id for 'congress' and if it is then save 'ring_name '
		$query = "SELECT ring_format_id FROM #__toes_ring_format WHERE ring_format = " . $db->quote('Congress');
		$db->setQuery($query);
		$ring_format_Congress_id = $db->loadResult();

		$k = 1;

		if (count($org_ring_ids) > $data['count_rings'] - 1)
			$ringcount = count($org_ring_ids);
		else
			$ringcount = $data['count_rings'] - 1;

		for($j = 0; $j < $ringcount; $j++) {
			if ($data['ring_show_day'][$j] && $data['ring_formats'][$j] && $data['ring_judge'][$j] && $data['ring_judge_id'][$j]) {
				$query = "SELECT show_day_id FROM #__toes_show_day 
                        WHERE date_format(show_day_date,'%Y-%m-%d') = " . $db->quote($data['ring_show_day'][$j]) . "
                        AND show_day_show=" . $db->quote($show_id);
				$db->setQuery($query);
				$show_day_id = $db->loadResult();

				if ($ring_format_Congress_id == $data['ring_formats'][$j])
					$congress = $data['ring_congress_name'][$j];
				else
					$congress = '';

				if ($show_day_id) {
					if ($data['ring_id'][$j]) {
						$org_ring_details = TOESHelper::getRingDetails($data['ring_id'][$j]);

						if ($org_ring_details->ring_format_id != $data['ring_formats'][$j] ||
								$org_ring_details->ring_timing != ($data['show_format'] == $alternative_id ? $data['ring_timings'][$j] : 0) ||
								$org_ring_details->ring_name != $congress ||
								$org_ring_details->ring_number != $data['ring_number'][$j])
							$rings_changed = 1;
						
						if ($org_ring_details->ring_judge != $data['ring_judge_id'][$j])
							$judges_changed = 1;
						
						$query = "UPDATE `#__toes_ring` 
						SET `ring_show_day` =	" . $db->quote($show_day_id) . "
						, `ring_format` = " . $db->quote($data['ring_formats'][$j]) . "
						, `ring_judge` = " . $db->quote($data['ring_judge_id'][$j]) . "
						, `ring_clerk` = " . $db->quote($data['ring_clerk_id'][$j]) . "
						, `ring_name` = " . $db->quote($congress) . "
						, `ring_number` = " . $db->quote($data['ring_number'][$j]) . "                                
						, `ring_timing` = " . $db->quote(($data['show_format'] == $alternative_id ? $data['ring_timings'][$j] : 'NULL')) . "                                
						WHERE `ring_id` =" . $data['ring_id'][$j];
						$db->setQuery($query);
						$db->query();

						$ring_id = $data['ring_id'][$j];

						$query = $db->getQuery(true);
						$query->select('congress_id');
						$query->from('#__toes_congress');
						$query->where('congress_id=' . $ring_id);
						$db->setQuery($query);
						$isCongressFilterAvilable = $db->loadResult();

						if ($ring_format_Congress_id == $data['ring_formats'][$j] && !$isCongressFilterAvilable) {
							$rings_changed = 1;
							$ring_index = $data['ring_index'][$j];
							if ($filters) {
								foreach($filters as $filter) {
									if ($filter->ring_index == $ring_index) {
										TOESHelper::deleteCongressFilters($ring_id);
										$query = $db->getQuery(true);
										$query->insert('#__toes_congress');
										$query->set('congress_name=' . $db->quote($congress));
										$query->set('congress_breed_switch=' . $filter->breed_filter);
										$query->set('congress_gender_switch=' . $filter->gender_filter);
										$query->set('congress_new_trait_switch=' . $filter->newtrait_filter);
										$query->set('congress_hair_length_switch=' . $filter->hairlength_filter);
										$query->set('congress_category_switch=' . $filter->category_filter);
										$query->set('congress_division_switch=' . $filter->division_filter);
										$query->set('congress_color_switch=' . $filter->color_filter);
										$query->set('congress_title_switch=' . $filter->title_filter);
										$query->set('congress_manual_select_switch=' . $filter->manual_filter);
										$query->set('congress_id=' . $ring_id);

										$db->setQuery($query);
										$db->query();

										$values = explode(',', $filter->class_value);
										foreach($values as $value) {
											$query = $db->getQuery(true);
											$query->insert('#__toes_congress_competitive_class');
											$query->set('congress_competitive_class_competitive_class=' . $value);
											$query->set('congress_competitive_class_congress=' . $ring_id);

											$db->setQuery($query);
											$db->query();
										}

										if ($filter->breed_filter && $filter->breed_value) {
											$values = explode(',', $filter->breed_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_breed');
												$query->set('congress_breed_breed=' . $value);
												$query->set('congress_breed_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->gender_filter && $filter->gender_value) {
											$values = explode(',', $filter->gender_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_gender');
												$query->set('congress_gender_gender=' . $value);
												$query->set('congress_gender_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->hairlength_filter && $filter->hairlength_value) {
											$values = explode(',', $filter->hairlength_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_hair_length');
												$query->set('congress_hair_length_hair_length=' . $value);
												$query->set('congress_hair_length_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->category_filter && $filter->category_value) {
											$values = explode(',', $filter->category_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_category');
												$query->set('congress_category_category=' . $value);
												$query->set('congress_category_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->division_filter && $filter->division_value) {
											$values = explode(',', $filter->division_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_division');
												$query->set('congress_division_division=' . $value);
												$query->set('congress_division_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->color_filter && $filter->color_value) {
											$values = explode(',', $filter->color_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_color');
												$query->set('congress_color_color=' . $value);
												$query->set('congress_color_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->color_filter && $filter->cwd_value) {
											$values = explode(',', $filter->cwd_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_color_wildcard');
												$query->set('congress_color_wildcard_wildcard=' . $db->quote($value));
												$query->set('congress_color_wildcard_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->title_filter && $filter->title_value) {
											$values = explode(',', $filter->title_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_title');
												$query->set('congress_title_title=' . $value);
												$query->set('congress_title_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}
									}
								}
							}
						}
					} else {
						//ring_clerk_access_code  
						/*	
						 if($data['ring_clerk_id'][$j]) {   							 
							$digits = $this->generateaccesscode();
						} else {
							$access_code = "";
						} 
						*/
						
						// sandy hack to generate and use access code always
						$digits = $this->generateaccesscode();
						
						
						$query = "INSERT INTO `#__toes_ring` 
                        (`ring_show_day`, `ring_format`, `ring_judge`, `ring_clerk`, `ring_clerk_access_code`, `ring_show`, `ring_organization`, `ring_number`, `ring_name`, `ring_timing` ) 
                        VALUES (" . $db->quote($show_day_id) . "," . $db->quote($data['ring_formats'][$j]) . "
                        ," . $db->quote($data['ring_judge_id'][$j]) . "," . $db->quote($data['ring_clerk_id'][$j]) . "," . $db->quote($digits) . "
						," . $db->quote($show_id) . ",1," . $db->quote($data['ring_number'][$j]) . "
                        ," . $db->quote($congress) . ", " . $db->quote(($data['show_format'] == $alternative_id ? $data['ring_timings'][$j] : 'NULL')) . " )";
						//echo ' new rings for new formed show day ids<br/> '.$query.'<br/><br/>';
						$db->setQuery($query);
						$db->query();
							
						/*if($data['ring_clerk_id'][$j]) {
							$access_code = JUserHelper::genRandomPassword(20);
						} else {
							$access_code = "";
						}
						
						$query = "INSERT INTO `#__toes_ring` 
                        (`ring_show_day`, `ring_format`, `ring_judge`, `ring_clerk`, `ring_clerk_access_code`, `ring_show`, `ring_organization`, `ring_number`, `ring_name`, `ring_timing` ) 
                        VALUES (" . $db->quote($show_day_id) . "," . $db->quote($data['ring_formats'][$j]) . "
                        ," . $db->quote($data['ring_judge_id'][$j]) . "," . $db->quote($data['ring_clerk_id'][$j]) . "," . $db->quote($access_code) . "
						," . $db->quote($show_id) . ",1," . $db->quote($data['ring_number'][$j]) . "
                        ," . $db->quote($congress) . ", " . $db->quote(($data['show_format'] == $alternative_id ? $data['ring_timings'][$j] : 'NULL')) . " )";
						//echo ' new rings for new formed show day ids<br/> '.$query.'<br/><br/>';
						$db->setQuery($query);
						$db->query();
						*/
						$ring_id = $db->insertid();

						if ($data['id'])
							$rings_changed = 1;

						if ($ring_format_Congress_id == $data['ring_formats'][$j]) {
							$ring_index = $data['ring_index'][$j];
							if ($filters) {
								foreach($filters as $filter) {
									if ($filter->ring_index == $ring_index) {
										TOESHelper::deleteCongressFilters($ring_id);
										$query = $db->getQuery(true);
										$query->insert('#__toes_congress');
										$query->set('congress_name=' . $db->quote($congress));
										$query->set('congress_breed_switch=' . $filter->breed_filter);
										$query->set('congress_gender_switch=' . $filter->gender_filter);
										$query->set('congress_new_trait_switch=' . $filter->newtrait_filter);
										$query->set('congress_hair_length_switch=' . $filter->hairlength_filter);
										$query->set('congress_category_switch=' . $filter->category_filter);
										$query->set('congress_division_switch=' . $filter->division_filter);
										$query->set('congress_color_switch=' . $filter->color_filter);
										$query->set('congress_title_switch=' . $filter->title_filter);
										$query->set('congress_manual_select_switch=' . $filter->manual_filter);
										$query->set('congress_id=' . $ring_id);

										$db->setQuery($query);
										$db->query();

										$values = explode(',', $filter->class_value);
										foreach($values as $value) {
											$query = $db->getQuery(true);
											$query->insert('#__toes_congress_competitive_class');
											$query->set('congress_competitive_class_competitive_class=' . $value);
											$query->set('congress_competitive_class_congress=' . $ring_id);

											$db->setQuery($query);
											$db->query();
										}

										if ($filter->breed_filter && $filter->breed_value) {
											$values = explode(',', $filter->breed_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_breed');
												$query->set('congress_breed_breed=' . $value);
												$query->set('congress_breed_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->gender_filter && $filter->gender_value) {
											$values = explode(',', $filter->gender_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_gender');
												$query->set('congress_gender_gender=' . $value);
												$query->set('congress_gender_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->hairlength_filter && $filter->hairlength_value) {
											$values = explode(',', $filter->hairlength_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_hair_length');
												$query->set('congress_hair_length_hair_length=' . $value);
												$query->set('congress_hair_length_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->category_filter && $filter->category_value) {
											$values = explode(',', $filter->category_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_category');
												$query->set('congress_category_category=' . $value);
												$query->set('congress_category_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->division_filter && $filter->division_value) {
											$values = explode(',', $filter->division_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_division');
												$query->set('congress_division_division=' . $value);
												$query->set('congress_division_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->color_filter && $filter->color_value) {
											$values = explode(',', $filter->color_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_color');
												$query->set('congress_color_color=' . $value);
												$query->set('congress_color_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->color_filter && $filter->cwd_value) {
											$values = explode(',', $filter->cwd_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_color_wildcard');
												$query->set('congress_color_wildcard_wildcard=' . $db->quote($value));
												$query->set('congress_color_wildcard_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->title_filter && $filter->title_value) {
											$values = explode(',', $filter->title_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_title');
												$query->set('congress_title_title=' . $value);
												$query->set('congress_title_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}
									}
								}
							}
						}
					}
				}
			} else if (isset($data['ring_id'][$j]) && $data['ring_id'][$j]) {
				$query = "DELETE FROM `#__toes_ring` WHERE ring_id =" . $data['ring_id'][$j];
				//echo ' new rings for new formed show day ids<br/> '.$query.'<br/><br/>';
				$db->setQuery($query);
				if ($db->query())
				{
					echo 'true';
					$rings_changed = 1;
				}

				TOESHelper::deleteCongressFilters($data['ring_id'][$j]);
			}
			$k++;
		}

		if(isset($data['ring_id']))
		$excluded_rings = array_diff($org_ring_ids, $data['ring_id']);
		else
		$excluded_rings = $org_ring_ids;
		
		
		if($excluded_rings) {
			foreach($excluded_rings as $ring) {
				if($ring) {
					$query = "DELETE FROM `#__toes_ring` WHERE ring_id =" . $ring;
					$db->setQuery($query);
					if ($db->query())
						$rings_changed = 1;
		
					TOESHelper::deleteCongressFilters($ring);
				}
			}
		}
		
		$query = "DELETE FROM `#__toes_show_has_official` WHERE `show` = " . $db->quote($show_id);
		$db->setQuery($query);
		$db->query();

		$query = " select * from #__toes_show_official_type order by show_official_type_id ASC";
		$db->setQuery($query);
		$showofficials = $db->loadObjectList();

		foreach($showofficials as $so) {
			$show_official_users = @$data['username_' . $so->show_official_type_id];

			if (count($show_official_users)) {
				for($s = 0; $s < count($show_official_users); $s++) {
					$show_official_username = $show_official_users[$s];
					if ($show_official_username) {
						$query = "SELECT `id` FROM #__users WHERE `username` = " . $db->quote($show_official_username);
						$db->setQuery($query);
						$show_official_userid = $db->loadResult();

						if ($show_official_userid) {
							$query = "INSERT INTO `#__toes_show_has_official` (`show`, `user`, `show_official_type`) 
                                    VALUES (" . $db->quote($show_id) . "," . $db->quote($show_official_userid) . "," . $db->quote($so->show_official_type_id) . ")";
							$db->setQuery($query);
							$db->query();
						}
					}
				}
			}
		}
		
		$show = TOESHelper::getShowDetails($show_id);
		
		if($show->show_use_waiting_list)
		{
			if ($transfer_to_waiting)
				TOESHelper::transferToWaitingList($show_id);
			if ($check_waiting_list)
				TOESHelper::checkWaitingList($show_id);
		}
		else
		{
			TOESHelper::deleteWaitingList($show_id);
		}
		
		/* conflict */
		 
		 
		$query = $db->getQuery(true);
		$query = "delete FROM `#__toes_show_club_approval` WHERE new_conflicting_show_id =" . $show_id;
		$db->setQuery($query);
		$db->query();	
	 
		$query = $db->getQuery(true);
		$query = "delete FROM `#__toes_show_regional_director_approval` WHERE new_conflicting_show_id =" . $show_id;
		$db->setQuery($query);
		$db->query();	
		 
		//spider 
		if(!$data['id'])
		{
			$date = $datetime->tosql();
			$rd_showdetails = $show_id . "," .$show_id;
			$rd_md5showdetails = md5($rd_showdetails);
			$query1 = $db->getQuery(true);
			$query1 = "insert into `#__toes_show_regional_director_approval` (`existing_show_id`,`new_conflicting_show_id`,`datetime`,`hash`) VALUES
						(".$show_id.",".$show_id.",".$db->quote($date).",".$db->quote($rd_md5showdetails).")";
			$db->setQuery($query1);
			//echo $query1;
			$db->query();
			
		}
		//$currentshow_id = $show_id;
		if($radiusresult) // send mail
		{
			$url = JURI::getInstance();
			foreach($radiusresult as $r)
			{		
				$existingmonth = date('F',strtotime($r->show_start_date));
				$existing_startday = date('d',strtotime($r->show_start_date));
				$existing_endday = date('d',strtotime($r->show_end_date));
				$existingyear = date('y',strtotime($r->show_start_date));
				
				$conflictingmonth = date('F',strtotime($show->show_start_date));
				$conflictingyear = date('y',strtotime($show->show_start_date));
				$conflicting_startday = date('d',strtotime($show->show_start_date));
				$conflicting_endday = date('d',strtotime($show->show_end_date));
				
				//$show_id = conflicting show_id
				$showid = $r->show_id; //existing showid
				
				$date = $datetime->tosql();
				
				
				$query = $db->getQuery(true);
				$query->select('o.club,c.user,c.club_official_type,t.club_official_type,cl.club_name,
								cp.firstname,cp.lastname,s.show_start_date,s.show_end_date,
								v.venue_name,u.email,ss.show_status');
				$query->from('#__toes_club_organizes_show as o');
				$query->join('LEFT','#__toes_club_official as c ON c.club = o.club');
				$query->join('LEFT','#__toes_club_official_type as t ON t.club_official_type_id = c.club_official_type');
				$query->join('LEFT','#__toes_club as cl ON o.club = cl.club_id');
				$query->join('LEFT','#__toes_show as s ON o.show = s.show_id');
				$query->join('LEFT','#__toes_venue as v ON s.show_venue = v.venue_id');
				$query->join('LEFT','#__comprofiler as cp ON cp.user_id = c.user');
				$query->join('LEFT','#__users as u ON cp.user_id = u.id');
				$query->join('LEFT','#__toes_show_status as ss ON s.show_status = ss.show_status_id');
				$query->where($db->quoteName('o.show').'='.$showid);
				$db->setQuery($query);
				$existingclub_official = $db->loadObject();
				//$club_official = $db->loadObject();
				
				
				$query1 = $db->getQuery(true);
				$query1->select('o.club,c.user,c.club_official_type,t.club_official_type,cl.club_name,
								cp.firstname,cp.lastname,s.show_start_date,s.show_end_date,v.venue_name,u.email');
				$query1->from('#__toes_club_organizes_show as o');
				$query1->join('LEFT','#__toes_club_official as c ON c.club = o.club');
				$query1->join('LEFT','#__toes_club_official_type as t ON t.club_official_type_id = c.club_official_type');
				$query1->join('LEFT','#__toes_club as cl ON o.club = cl.club_id');
				$query1->join('LEFT','#__toes_show as s ON o.show = s.show_id');
				$query1->join('LEFT','#__toes_venue as v ON s.show_venue = v.venue_id');
				$query1->join('LEFT','#__comprofiler as cp ON cp.user_id = c.user');
				$query1->join('LEFT','#__users as u ON cp.user_id = u.id');
				$query1->where($db->quoteName('o.show').'='.$show_id);
				$db->setQuery($query1);
				$conflictingclub_official = $db->loadObject();
				//$otherclub_official = $db->loadObject();
				
				$query1 = $db->getQuery(true);
				$query1->select('c.club_id,c.club_name');
				$query1->from('#__toes_club as c');
				$query1->join('LEFT','#__toes_club_organizes_show as s ON s.club = c.club_id');
				$query1->where('s.show='.$show_id);
				$db->setQuery($query1);
				$affectedclub = $db->loadObject	();
				
				$km = round($r->distance * 1.609,1);
				$miles = round($r->distance * 0.621,1);
				
				$query1 = $db->getQuery(true);
				$query1->select('co.firstname,co.lastname,tc.club_competitive_region,comp.competitive_region_regional_director');
				$query1->from('#__toes_club_organizes_show as s');
				$query1->join('LEFT','#__toes_club as tc ON s.club = tc.club_id');
				$query1->join('LEFT','#__toes_competitive_region as comp ON tc.club_competitive_region = comp.competitive_region_id');
				$query1->join('LEFT','#__comprofiler as co ON comp.competitive_region_regional_director = co.user_id');
				$query1->where('s.show='.$r->show_id);
				$db->setQuery($query1);
				$existing_regionaldirector = $db->loadObject();		
				//$other_regionaldirector = $db->loadObject();		
				
				$existing_address = TOESHelper::getShowDetails($r->show_id);
				
				$query2 = $db->getQuery(true);
				$query2->select('co.firstname,co.lastname,tc.club_competitive_region,comp.competitive_region_regional_director');
				$query2->from('#__toes_club_organizes_show as s');
				$query2->join('LEFT','#__toes_club as tc ON s.club = tc.club_id');
				$query2->join('LEFT','#__toes_competitive_region as comp ON tc.club_competitive_region = comp.competitive_region_id');
				$query2->join('LEFT','#__comprofiler as co ON comp.competitive_region_regional_director = co.user_id');
				$query2->where('s.show='.$show_id);
				$db->setQuery($query2);
				$conflicting_regionaldirector = $db->loadObject();
				
				//send mail to club official	
				$showdetails = $show_id . "," .$showid . "," . $date;
				$md5showdetails = md5($showdetails);
				
					if($existingclub_official)
					{
						//echo "existingclub_official";
						$user = JFactory::getUser($existingclub_official->user);
						
						$config = JFactory::getConfig();
						$mailTemplate = TOESMailHelper::getTemplate('radius_search_using_latitude_longitude');
						$users = TOESHelper::getSubscribedUsers($show_id);
						$fromname = $config->get('fromname');
						
						if($mailTemplate) {
							$subject = $mailTemplate->mail_subject;
							$body = $mailTemplate->mail_body;
						} else {
							$subject = JText::_('COM_TOES_SHOW_SHOWS_FROM_GIVEN_DISTANCE');
							$body = JText::_('COM_TOES_SHOW_SHOWS_FROM_GIVEN_DISTANCE');
						}
						$body = str_replace('[existing_club]',$existingclub_official->club_name, $body);
						
						$body = str_replace('[show_status]',$existingclub_official->show_status, $body);
						
						$body = str_replace('[existing_startday]',$existing_startday, $body);
						
						$body = str_replace('[existing_endday]',$existing_endday, $body);
						
						$body = str_replace('[existingmonth]',$existingmonth, $body);
						
						$body = str_replace('[existingyear]',$existingyear, $body);
						
						$body = str_replace('[conflicting_startday]',$conflicting_startday, $body);
						
						$body = str_replace('[conflicting_endday]',$conflicting_endday, $body);
						
						$body = str_replace('[conflictingmonth]',$conflictingmonth, $body);
						
						$body = str_replace('[conflictingyear]',$conflictingyear, $body);
						
						$body = str_replace('[existing_location]',$existingclub_official->venue_name, $body);
						
						$body = str_replace('[conflicting_club]',$affectedclub->club_name, $body);
						
						$body = str_replace('[conflicting_location]',$conflictingclub_official->venue_name, $body);
						
						$body = str_replace('[existing_clubofficial_firstname]',$existingclub_official->firstname, $body);
						
						$body = str_replace('[existing_clubofficial_lastname]',$existingclub_official->lastname, $body);
						
						$body = str_replace('[conflicting_clubofficial_firstname]',$conflictingclub_official->firstname, $body);
						
						$body = str_replace('[conflicting_clubofficial_lastname]',$conflictingclub_official->lastname, $body);
						
						$body = str_replace('[km]',$km, $body);
										
						$body = str_replace('[miles]',$miles, $body);
						
						if($existing_regionaldirector->club_competitive_region == $conflicting_regionaldirector->club_competitive_region)
						{
							//echo "same club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						
						}
						else
						{ //echo "diff club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						}
						 
						$body = str_replace('[show_id]',$r->show_id, $body);
						
						$body = str_replace('[firstname]',$existingclub_official->firstname, $body);	

						$body = str_replace('[lastname]',$existingclub_official->lastname, $body);
						
						$body = str_replace('[club_official]',$existingclub_official->club_official_type, $body);	
																
						$body = str_replace('[show_manager]',null, $body);
						
						$body = str_replace('[name_of_club]',$existingclub_official->club_name, $body);	
								
						$body = str_replace('[club_name]',$existingclub_official->club_name, $body);
						
						$body = str_replace('[start_date]',$data['show_start_date'], $body);	
						
						$body = str_replace('[end_date]',$data['show_end_date'], $body);	
						
						$body = str_replace('[location]',$existingclub_official->venue_name, $body);
						
						//$body = str_replace('[show_link]',$show_link, $body);
								
						$body = str_replace('[other_club]',$affectedclub->club_name, $body);
						
						$body = str_replace('[other_club_start_date]',$existingclub_official->show_start_date, $body);
								
						$body = str_replace('[other_club_end_date]',$existingclub_official->show_end_date, $body);
						
						$body = str_replace('[other_club_location]',$conflictingclub_official->venue_name, $body);
						
						$body = str_replace('[show_id]',$r->show_id, $body);
						
						$body = str_replace('[km]',$km, $body);
										
						$body = str_replace('[miles]',$miles, $body);
						
						$body = str_replace('[other_firstname]',$existingclub_official->firstname, $body);	
								
						$body = str_replace('[other_lastname]',$existingclub_official->lastname, $body);
								
						$body = str_replace('[other_email]',$existingclub_official->email, $body);
						
						$body = str_replace('[firstname_of_rd]',$existing_regionaldirector->firstname, $body);
										
						$body = str_replace('[lastname_of_rd]',$existing_regionaldirector->lastname, $body);
						
						$body = str_replace('[other_firstname_of_rd]',$conflicting_regionaldirector->firstname, $body);
										
						$body = str_replace('[other_lastname_of_rd]',$conflicting_regionaldirector->lastname, $body);
						 
						TOESMailHelper::sendMail('radius_search_using_latitude_longitude', $subject, $body, $user->email);
						//var_dump($body);
					}	
				
				
				$query = $db->getQuery(true);
				$query->select('o.*,s.show_official_type,cp.firstname,cp.lastname,cl.club_name,
								ts.show_start_date,ts.show_end_date,v.venue_name,u.email,o.user');
				$query->from('#__toes_show_has_official as o');
				$query->join('LEFT','#__toes_show_official_type as s ON o.show_official_type = s.show_official_type_id');
				$query->join('LEFT','#__comprofiler as cp ON cp.user_id = o.user');
				$query->join('LEFT','#__toes_club_organizes_show as cos ON cos.show = o.show');
				$query->join('LEFT','#__toes_club as cl ON cos.club = cl.club_id');
				$query->join('LEFT','#__toes_show as ts ON o.show = ts.show_id');
				$query->join('LEFT','#__toes_venue as v ON ts.show_venue = v.venue_id');
				$query->join('LEFT','#__users as u ON cp.user_id = u.id');
				$query->where($db->quoteName('o.show').'='.$r->show_id);
				$db->setQuery($query);
				$show_manager = $db->loadObjectList();
				//$show_manager = $db->loadObject();
			
					if(isset($show_manager))
					{
						foreach($show_manager as $m)
						{
							//echo "show_manager";
							$user = JFactory::getUser($m->user);
							$mailTemplate = TOESMailHelper::getTemplate('radius_search_using_latitude_longitude');
							$config = JFactory::getConfig();
							$users = TOESHelper::getSubscribedUsers($show_id);
							$fromname = $config->get('fromname');
							
							if($mailTemplate) {
								 $subject = $mailTemplate->mail_subject;
								 $body = $mailTemplate->mail_body;
							} else {
								$subject = JText::_('COM_TOES_SHOW_SHOWS_FROM_GIVEN_DISTANCE');
								$body = JText::_('COM_TOES_SHOW_SHOWS_FROM_GIVEN_DISTANCE');
							}
							$body = str_replace('[existing_club]',$existingclub_official->club_name, $body);
							
							$body = str_replace('[show_status]',$existingclub_official->show_status, $body);
							
							$body = str_replace('[existing_startday]',$existing_startday, $body);
							
							$body = str_replace('[existing_endday]',$existing_endday, $body);
							
							$body = str_replace('[existingmonth]',$existingmonth, $body);
							
							$body = str_replace('[existingyear]',$existingyear, $body);
							
							$body = str_replace('[conflicting_startday]',$conflicting_startday, $body);
							
							$body = str_replace('[conflicting_endday]',$conflicting_endday, $body);
							
							$body = str_replace('[conflictingmonth]',$conflictingmonth, $body);
							
							$body = str_replace('[conflictingyear]',$conflictingyear, $body);
							
							$body = str_replace('[existing_location]',$existingclub_official->venue_name, $body);
							
							$body = str_replace('[conflicting_club]',$affectedclub->club_name, $body);
							
							$body = str_replace('[conflicting_location]',$conflictingclub_official->venue_name, $body);
							
							$body = str_replace('[existing_clubofficial_firstname]',$existingclub_official->firstname, $body);
							
							$body = str_replace('[existing_clubofficial_lastname]',$existingclub_official->lastname, $body);
							
							$body = str_replace('[conflicting_clubofficial_firstname]',$conflictingclub_official->firstname, $body);
							
							$body = str_replace('[conflicting_clubofficial_lastname]',$conflictingclub_official->lastname, $body);
							
							$body = str_replace('[km]',$km, $body);
											
							$body = str_replace('[miles]',$miles, $body);
							
							if($existing_regionaldirector->club_competitive_region == $conflicting_regionaldirector->club_competitive_region)
							{
								//echo "same club";
								$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
							
								$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
								
								$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
							
								$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
								
								$body = str_replace('[existing_city]',$existing_address->address_city, $body);
							
								$body = str_replace('[existing_state]',$existing_address->address_state, $body);
							
								$body = str_replace('[existing_country]',$existing_address->address_country, $body);
								
								$body = str_replace('[conflicting_city]',$show->address_city, $body);
							
								$body = str_replace('[conflicting_state]',$show->address_state, $body);
							
								$body = str_replace('[conflicting_country]',$show->address_country, $body);
							
							}
							else
							{ //echo "diff club";
								$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
							
								$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
								
								$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
							
								$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
								
								$body = str_replace('[existing_city]',$existing_address->address_city, $body);
							
								$body = str_replace('[existing_state]',$existing_address->address_state, $body);
							
								$body = str_replace('[existing_country]',$existing_address->address_country, $body);
								
								$body = str_replace('[conflicting_city]',$show->address_city, $body);
							
								$body = str_replace('[conflicting_state]',$show->address_state, $body);
							
								$body = str_replace('[conflicting_country]',$show->address_country, $body);
							}
							
							 
							$body = str_replace('[show_id]',$r->show_id, $body);
							
							$body = str_replace('[firstname]',$m->firstname, $body);	
											
							$body = str_replace('[lastname]',$m->lastname, $body);
							
							$body = str_replace('[club_official]',null, $body);
							
							$body = str_replace('[show_manager]',$m->show_official_type, $body);
							
							$body = str_replace('[name_of_club]',$m->club_name, $body);	
							
							$body = str_replace('[club_name]',$m->club_name, $body);	
							
							$body = str_replace('[start_date]',$data['show_start_date'], $body);	
									
							$body = str_replace('[end_date]',$data['show_end_date'], $body);
							
							$body = str_replace('[location]',$m->venue_name, $body);
							
							//$body = str_replace('[show_link]',$showlink, $body);
							
							$body = str_replace('[other_club]',$affectedclub->club_name, $body);
							
							$body = str_replace('[other_club_start_date]',$m->show_start_date, $body);
							
							$body = str_replace('[other_club_end_date]',$m->show_end_date, $body);
							
							$body = str_replace('[other_club_location]',$conflictingclub_official->venue_name, $body);
							
							$body = str_replace('[show_id]',$r->show_id, $body);
							
							$body = str_replace('[km]',$km, $body);
											
							$body = str_replace('[miles]',$miles, $body);
							
							$body = str_replace('[other_firstname]',$conflictingclub_official->firstname, $body);	
											
							$body = str_replace('[other_lastname]',$conflictingclub_official->lastname, $body);
							
							$body = str_replace('[other_email]',$conflictingclub_official->email, $body);
											
							$body = str_replace('[firstname_of_rd]',$existing_regionaldirector->firstname, $body);
							
							$body = str_replace('[lastname_of_rd]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[other_firstname_of_rd]',$conflicting_regionaldirector->firstname, $body);
											
							$body = str_replace('[other_lastname_of_rd]',$conflicting_regionaldirector->lastname, $body);
							 
							TOESMailHelper::sendMail('radius_search_using_latitude_longitude', $subject, $body, $user->email);
							
							 var_dump($body);
						}
					}
					
					 
						$query5 = $db->getQuery(true);
						$query5 = "insert into `#__toes_show_club_approval` (`existing_show_id`,`new_conflicting_show_id`,`datetime`,`hash`) VALUES
								(".$showid.",".$db->quote($show_id).",".$db->quote($date).",".$db->quote($md5showdetails).")";
						$db->setQuery($query5);
						$db->query();
					
				
				//send mail to RD 
				$other_rd = $existing_regionaldirector->competitive_region_regional_director;
				$rd = $conflicting_regionaldirector->competitive_region_regional_director;
				
				
				if($other_rd == $rd)
				{
					$rdshowapprovaldetails = $show_id . "," .$date . "," . $showid;
					$rdmd5showapprovaldetails = md5($rdshowapprovaldetails);
					
					$existing_regionaldirector_user = JFactory::getUser($existing_regionaldirector->user);
					$db->setQuery("select * from `#__comprofiler` where `user_id` =".$existing_regionaldirector->user);
					$existing_regionaldirector_user_cb_data = $db->loadObject();
					
					$mailTemplate = TOESMailHelper::getTemplate('show_regional_director_notification');
					$config = JFactory::getConfig();
					$users = TOESHelper::getSubscribedUsers($r->show_id);
					$fromname = $config->get('fromname');
					
					if($mailTemplate) {
						 $subject = $mailTemplate->mail_subject;
						 $body = $mailTemplate->mail_body;
					} else {
						 $subject = JText::_('COM_TOES_SHOW_SHOW_REGIONAL_DIRECTOR_NOTIFICATION');
						 $body = JText::_('COM_TOES_SHOW_SHOW_REGIONAL_DIRECTOR_NOTIFICATION');
					}
					//
					$body = str_replace('[rd_firstname]',$existing_regionaldirector_user_cb_data->firstname, $body);
					$body = str_replace('[show_id]',$show_id, $body);					 
					//
					$body = str_replace('[existing_club]',$existingclub_official->club_name, $body);
						
						$body = str_replace('[show_status]',$existingclub_official->show_status, $body);
						
						$body = str_replace('[existing_startday]',$existing_startday, $body);
						
						$body = str_replace('[existing_endday]',$existing_endday, $body);
						
						$body = str_replace('[existingmonth]',$existingmonth, $body);
						
						$body = str_replace('[existingyear]',$existingyear, $body);
						
						$body = str_replace('[conflicting_startday]',$conflicting_startday, $body);
						
						$body = str_replace('[conflicting_endday]',$conflicting_endday, $body);
						
						$body = str_replace('[conflictingmonth]',$conflictingmonth, $body);
						
						$body = str_replace('[conflictingyear]',$conflictingyear, $body);
						
						$body = str_replace('[existing_location]',$existingclub_official->venue_name, $body);
						
						$body = str_replace('[conflicting_club]',$affectedclub->club_name, $body);
						
						$body = str_replace('[conflicting_location]',$conflictingclub_official->venue_name, $body);
						
						$body = str_replace('[existing_clubofficial_firstname]',$existingclub_official->firstname, $body);
						
						$body = str_replace('[existing_clubofficial_lastname]',$existingclub_official->lastname, $body);
						
						$body = str_replace('[conflicting_clubofficial_firstname]',$conflictingclub_official->firstname, $body);
						
						$body = str_replace('[conflicting_clubofficial_lastname]',$conflictingclub_official->lastname, $body);
						
						$body = str_replace('[km]',$km, $body);
										
						$body = str_replace('[miles]',$miles, $body);
						
						if($existing_regionaldirector->club_competitive_region == $conflicting_regionaldirector->club_competitive_region)
						{
							//echo "same club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						
						}
						else
						{ //echo "diff club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						}
					/*$body = str_replace('[show_id]',$r->show_id, $body);	
					
					$body = str_replace('[rd_firstname]',$existing_regionaldirector->firstname, $body);	
					
					$body = str_replace('[rd_lastname]',$existing_regionaldirector->lastname, $body);
					*/
					
					TOESMailHelper::sendMail('show_regional_director_notification', $subject, $body, $existing_regionaldirector_user->email);
					//echo "same";
					//var_dump($body);
					$query = $db->getQuery(true);
					$query = "insert into `#__toes_show_regional_director_approval` (`existing_show_id`,`new_conflicting_show_id`,`datetime`,`hash`) VALUES
								(".$showid.",".$db->quote($show_id).",".$db->quote($date).",".$db->quote($rdmd5showapprovaldetails).")";
					$db->setQuery($query);
						//echo $query;
					$db->query();	
				}
				else
				{
					$rd_showapprovaldetails = $showid . "," .$show_id . "," . $date;
					$rd_md5 = md5($rd_showapprovaldetails);
					if($other_rd) // conflicted shows rd
					{	
						$other_rdshowapprovaldetails = $show_id . "," .$showid . "," . $date;
						$other_rdmd5 = md5($other_rdshowapprovaldetails);
						$user = JFactory::getUser($conflicting_regionaldirector->user);
						$mailTemplate = TOESMailHelper::getTemplate('show_regional_director_notification');
						$config = JFactory::getConfig();
						$users = TOESHelper::getSubscribedUsers($r->show_id);
						$fromname = $config->get('fromname');
						
						if($mailTemplate) {
							 $subject = $mailTemplate->mail_subject;
							 $body = $mailTemplate->mail_body;
						} else {
							 $subject = JText::_('COM_TOES_SHOW_SHOW_REGIONAL_DIRECTOR_NOTIFICATION');
							 $body = JText::_('COM_TOES_SHOW_SHOW_REGIONAL_DIRECTOR_NOTIFICATION');
						}
						$body = str_replace('[existing_club]',$existingclub_official->club_name, $body);
						
						$body = str_replace('[show_status]',$existingclub_official->show_status, $body);
						
						$body = str_replace('[existing_startday]',$existing_startday, $body);
						
						$body = str_replace('[existing_endday]',$existing_endday, $body);
						
						$body = str_replace('[existingmonth]',$existingmonth, $body);
						
						$body = str_replace('[existingyear]',$existingyear, $body);
						
						$body = str_replace('[conflicting_startday]',$conflicting_startday, $body);
						
						$body = str_replace('[conflicting_endday]',$conflicting_endday, $body);
						
						$body = str_replace('[conflictingmonth]',$conflictingmonth, $body);
						
						$body = str_replace('[conflictingyear]',$conflictingyear, $body);
						
						$body = str_replace('[existing_location]',$existingclub_official->venue_name, $body);
						
						$body = str_replace('[conflicting_club]',$affectedclub->club_name, $body);
						
						$body = str_replace('[conflicting_location]',$conflictingclub_official->venue_name, $body);
						
						$body = str_replace('[existing_clubofficial_firstname]',$existingclub_official->firstname, $body);
						
						$body = str_replace('[existing_clubofficial_lastname]',$existingclub_official->lastname, $body);
						
						$body = str_replace('[conflicting_clubofficial_firstname]',$conflictingclub_official->firstname, $body);
						
						$body = str_replace('[conflicting_clubofficial_lastname]',$conflictingclub_official->lastname, $body);
						
						$body = str_replace('[km]',$km, $body);
										
						$body = str_replace('[miles]',$miles, $body);
						
						if($existing_regionaldirector->club_competitive_region == $conflicting_regionaldirector->club_competitive_region)
						{
							//echo "same club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						
						}
						else
						{ //echo "diff club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						}
						/*$body = str_replace('[show_id]',$r->show_id, $body);	
						
						$body = str_replace('[rd_firstname]',$existing_regionaldirector->firstname, $body);	
						
						$body = str_replace('[rd_lastname]',$existing_regionaldirector->lastname, $body);
						*/
						TOESMailHelper::sendMail('show_regional_director_notification', $subject, $body, $user->email);
						var_dump($body);
						//echo "different";
					}	
					
					if($rd)
					{
						$user = JFactory::getUser($conflicting_regionaldirector->user);
						$mailTemplate = TOESMailHelper::getTemplate('show_regional_director_notification');
						$config = JFactory::getConfig();
						
						$fromname = $config->get('fromname');
						
						if($mailTemplate) {
							 $subject = $mailTemplate->mail_subject;
							 $body = $mailTemplate->mail_body;
						} else {
							 $subject = JText::_('COM_TOES_SHOW_SHOW_REGIONAL_DIRECTOR_NOTIFICATION');
							 $body = JText::_('COM_TOES_SHOW_SHOW_REGIONAL_DIRECTOR_NOTIFICATION');
						}
						$body = str_replace('[existing_club]',$existingclub_official->club_name, $body);
						
						$body = str_replace('[show_status]',$existingclub_official->show_status, $body);
						
						$body = str_replace('[existing_startday]',$existing_startday, $body);
						
						$body = str_replace('[existing_endday]',$existing_endday, $body);
						
						$body = str_replace('[existingmonth]',$existingmonth, $body);
						
						$body = str_replace('[existingyear]',$existingyear, $body);
						
						$body = str_replace('[conflicting_startday]',$conflicting_startday, $body);
						
						$body = str_replace('[conflicting_endday]',$conflicting_endday, $body);
						
						$body = str_replace('[conflictingmonth]',$conflictingmonth, $body);
						
						$body = str_replace('[conflictingyear]',$conflictingyear, $body);
						
						$body = str_replace('[existing_location]',$existingclub_official->venue_name, $body);
						
						$body = str_replace('[conflicting_club]',$affectedclub->club_name, $body);
						
						$body = str_replace('[conflicting_location]',$conflictingclub_official->venue_name, $body);
						
						$body = str_replace('[existing_clubofficial_firstname]',$existingclub_official->firstname, $body);
						
						$body = str_replace('[existing_clubofficial_lastname]',$existingclub_official->lastname, $body);
						
						$body = str_replace('[conflicting_clubofficial_firstname]',$conflictingclub_official->firstname, $body);
						
						$body = str_replace('[conflicting_clubofficial_lastname]',$conflictingclub_official->lastname, $body);
						
						$body = str_replace('[km]',$km, $body);
										
						$body = str_replace('[miles]',$miles, $body);
						
						if($existing_regionaldirector->club_competitive_region == $conflicting_regionaldirector->club_competitive_region)
						{
							//echo "same club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						
						}
						else
						{ //echo "diff club";
							$body = str_replace('[existing_regional_director_firstname]',$existing_regionaldirector->firstname, $body);
						
							$body = str_replace('[existing_regional_director_lastname]',$existing_regionaldirector->lastname, $body);
							
							$body = str_replace('[conflicting_regional_director_firstname]',$conflicting_regionaldirector->firstname, $body);
						
							$body = str_replace('[conflicting_regional_director_lastname]',$conflicting_regionaldirector->lastname, $body);
							
							$body = str_replace('[existing_city]',$existing_address->address_city, $body);
						
							$body = str_replace('[existing_state]',$existing_address->address_state, $body);
						
							$body = str_replace('[existing_country]',$existing_address->address_country, $body);
							
							$body = str_replace('[conflicting_city]',$show->address_city, $body);
						
							$body = str_replace('[conflicting_state]',$show->address_state, $body);
						
							$body = str_replace('[conflicting_country]',$show->address_country, $body);
						}
						/*$body = str_replace('[show_id]',$show_id, $body);	
						
						$body = str_replace('[rd_firstname]',$conflicting_regionaldirector->firstname, $body);	
						
						$body = str_replace('[rd_lastname]',$conflicting_regionaldirector->lastname, $body);
						*/
						TOESMailHelper::sendMail('show_regional_director_notification', $subject, $body, $user->email);
						//var_dump($body);
						//echo "different";
					}
					
						$query = $db->getQuery(true);
						$query = "insert into `#__toes_show_regional_director_approval` (`existing_show_id`,`new_conflicting_show_id`,`datetime`,`hash`) VALUES
								(".$showid.",".$db->quote($show_id).",".$db->quote($date).",".$db->quote($rd_md5).")";
						$db->setQuery($query);
						//echo $query;
						$db->query();
				}	
					
			}	
			
			$db->setQuery("UPDATE `#__toes_show` SET `show_has_500_mile_conflict` = 1 where `show_id` =".$show_id)->execute();
			//die;
		}
		
						
		
		if ($data['id']) {
			$user = JFactory::getUser();

			if ($session->has('filters_changed')) {
				if ($session->get('filters_changed') == $data['id'])
					$rings_changed = 1;
			}

			if ($date_changed || $location_changed || $status_changed || $format_changed || $judges_changed || $rings_changed || $desc_changed) {
				$query = $db->getQuery(true);
				$query->insert('#__toes_show_changes');
				$query->set('show_changes_show=' . $show_id);
				$query->set('show_changes_dates_changed=' . $date_changed);
				$query->set('show_changes_location_changed=' . $location_changed);
				$query->set('show_changes_show_status=' . $status_changed);
				$query->set('show_changes_show_format_changed=' . $format_changed);
				$query->set('show_changes_judges_changed=' . $judges_changed);
				$query->set('show_changes_rings_changed=' . $rings_changed);
				$query->set('show_changes_description_changed=' . $desc_changed);
				$query->set('show_changes_last_changed_on = NOW()');
				$query->set('show_changes_last_changed_by=' . $user->id);

				$db->setQuery($query);
				$db->query();

				$mailTemplate = TOESMailHelper::getTemplate('show_update_notification');

				if($mailTemplate) {
					$subject = $mailTemplate->mail_subject;
					$body = $mailTemplate->mail_body;
				} else {
					$subject = JText::_('COM_TOES_SHOW_UPDATE_EMAIL_SUBJECT');
					$body = JText::_('COM_TOES_SHOW_UPDATE_EMAIL_BODY');
				}

				$body = str_replace('[City]', $show->address_city, $body);
				$body = str_replace('[, [State]]', $show->address_state ? ', ' . $show->address_state : '', $body);
				$body = str_replace('[Country]', $show->address_country, $body);

				$body = str_replace('[club name]', $show->club_name, $body);

				$start_date = date('d', strtotime($show->show_start_date));
				$start_date_month = date('M', strtotime($show->show_start_date));
				$start_date_year = date('Y', strtotime($show->show_start_date));

				$end_date = date('d', strtotime($show->show_end_date));
				$end_date_month = date('M', strtotime($show->show_end_date));
				$end_date_year = date('Y', strtotime($show->show_end_date));

				$show_date = $start_date_month . ' ' . $start_date;

				if ($end_date_year != $start_date_year) {
					$show_date .= ' ' . $start_date_year;
				}

				if ($end_date_month != $start_date_month) {
					if (date('t', strtotime($data->show_start_date)) != $start_date)
						$show_date .= ' - ' . date('t', strtotime($data->show_start_date));
					if ($end_date == '01')
						$show_date .= ', ' . $end_date_month . ' ' . $end_date;
					else
						$show_date .= ', ' . $end_date_month . ' 01 - ' . $end_date;
				} else {
					if ($start_date != $end_date)
						$show_date .= ' - ' . $start_date_month . ' ' . $end_date;
				}

				$show_date .= ' ' . $end_date_year;

				$body = str_replace('[showdates]', $show_date, $body);

				if ($date_changed) {
					$text = str_replace('[showdates]', $show_date, JText::_('COM_TOES_SHOW_UPDATE_EMAIL_DATE_CHANGES'));
					$body = str_replace('[date_change]', $text, $body);
				}
				else
					$body = str_replace('[date_change]', '', $body);

				if ($location_changed) {
					$text = str_replace('[City]', $show->address_city, JText::_('COM_TOES_SHOW_UPDATE_EMAIL_LOCATION_CHANGES'));
					$text = str_replace('[, [State]]', $show->address_state ? ', ' . $show->address_state : '', $text);
					$text = str_replace('[Country]', $show->address_country, $text);

					$body = str_replace('[location_change]', $text, $body);
				}
				else
					$body = str_replace('[location_change]', '', $body);

				if ($status_changed) {
					$text = str_replace('[show status]', $show->show_status, JText::_('COM_TOES_SHOW_UPDATE_EMAIL_STATUS_CHANGES'));
					$body = str_replace('[status_change]', $text, $body);
				}
				else
					$body = str_replace('[status_change]', '', $body);

				if ($format_changed) {
					$text = str_replace('[show format]', $show->show_format, JText::_('COM_TOES_SHOW_UPDATE_EMAIL_FORMAT_CHANGES'));
					$body = str_replace('[format_change]', $text, $body);
				}
				else
					$body = str_replace('[format_change]', '', $body);

				if ($desc_changed) {
					$text = str_replace('[show description]', $data['show_comments'], JText::_('COM_TOES_SHOW_UPDATE_EMAIL_DESC_CHANGES'));
					$body = str_replace('[desc_change]', $text, $body);
				}
				else
					$body = str_replace('[desc_change]', '', $body);

				if ($rings_changed || $judges_changed) {
					$text = JText::_('COM_TOES_SHOW_UPDATE_EMAIL_RINGS_CHANGES');
					if ($judges_changed && $rings_changed) {
						if ($date_changed || $format_changed)
							$text = JText::_('COM_TOES_SHOW_UPDATE_EMAIL_RINGS_MAY_CHANGES');

						$text = str_replace('[judges]', JText::_('COM_TOES_SHOW_UPDATE_EMAIL_RINGS_JUDEGS_CHANGES'), $text);
						$text = str_replace('[, ]', ', ', $text);
						$text = str_replace('[rings]', JText::_('COM_TOES_SHOW_UPDATE_EMAIL_RINGS_RINGS_CHANGES'), $text);
					}
					else if ($judges_changed) {
						$text = str_replace('[judges]', JText::_('COM_TOES_SHOW_UPDATE_EMAIL_RINGS_JUDEGS_CHANGES'), $text);
						$text = str_replace('[, ]', '', $text);
						$text = str_replace('[rings]', '', $text);
					} else {
						if ($date_changed || $format_changed)
							$text = JText::_('COM_TOES_SHOW_UPDATE_EMAIL_RINGS_MAY_CHANGES');

						$text = str_replace('[judges]', '', $text);
						$text = str_replace('[, ]', '', $text);
						$text = str_replace('[rings]', JText::_('COM_TOES_SHOW_UPDATE_EMAIL_RINGS_RINGS_CHANGES'), $text);
					}

					$body = str_replace('[rings_change]', $text, $body);
				}
				else
					$body = str_replace('[rings_change]', '', $body);

				$url = JURI::getInstance();
				$show_link = $url->getScheme() . '://' . $url->getHost() . JRoute::_('index.php?option=com_toes&view=shows', false) . '#show' . $show_id;

				$body = str_replace('[show_link]', $show_link, $body);

				$config = JFactory::getConfig();
				$fromname = $config->get('fromname');
				$fromemail = $config->get('mailfrom');

				$users = TOESHelper::getSubscribedUsers($show_id);

				foreach($users as $usr) {
					/*
					$mail = JFactory::getMailer();

					$mail->SetFrom($fromemail, $fromname);
					$mail->setSubject($subject);
					$mail->setBody(str_replace('[firstname]', ($usr->firstname) ? $usr->firstname : $usr->name, $body));
					$mail->addRecipient($usr->email);
					$mail->IsHTML(TRUE);

					$mail->Send();
					*/
					
					$user_message = str_replace('[firstname]', ($usr->firstname) ? $usr->firstname : $usr->name, $body);
					TOESMailHelper::sendMail('show_update_notification', $subject, $user_message, $usr->email);
				}
			}

			if ($session->has('filters_changed')) {
				$session->clear('filters_changed');
			}
		}

		if ($session->has('congress_filters')) {
			$session->clear('congress_filters');
		}

		// Clean the cache.
		$this->cleanCache();
		return $show_id;
	}

	/**
	 * Custom clean cache method
	 *
	 * @since	1.6
	 */
	protected function cleanCache($group = null, $client_id = 0) {
		parent::cleanCache('com_toes');
		parent::cleanCache('_system');
	}

	protected function sendMailtoRD($show_id, $region, $need_approval = true) {
		$show = TOESHelper::getShowDetails($show_id);
		$club = TOESHelper::getClub($show_id);
		$user = TOESHelper::getUserInfo($region->competitive_region_regional_director);

		$mail_action = '';
		if ($need_approval) {
			$mailTemplate = TOESMailHelper::getTemplate('show_approval_application');
			$mail_action = 'show_approval_application';
			
			if($mailTemplate) {
				$subject = $mailTemplate->mail_subject;
				$body = $mailTemplate->mail_body;
			} else {
				$subject = JText::_('COM_TOES_RD_NOTIFICATION_APPROVAL_EMAIL_SUBJECT');
				$body = JText::_('COM_TOES_RD_NOTIFICATION_APPROVAL_EMAIL_BODY');
			}
		} else {
			$mailTemplate = TOESMailHelper::getTemplate('show_wo_approval_application');
			$mail_action = 'show_wo_approval_application';
			
			if($mailTemplate) {
				$subject = $mailTemplate->mail_subject;
				$body = $mailTemplate->mail_body;
			} else {
				$subject = JText::_('COM_TOES_RD_NOTIFICATION_NO_APPROVAL_EMAIL_SUBJECT');
				$body = JText::_('COM_TOES_RD_NOTIFICATION_NO_APPROVAL_EMAIL_BODY');
			}
		}

		$subject = str_replace('[clubname]', $club->club_name, $subject);
		$subject = str_replace('[showdates]', $show->show_dates, $subject);

		$body = str_replace('[firstname]', $user->firstname, $body);

		$body = str_replace('[clubname]', $club->club_name, $body);

		$body = str_replace('[location]', $show->Show_location, $body);

		$body = str_replace('[showdates]', $show->show_dates, $body);

		$body = str_replace('[region_name]', $region->competitive_region_name, $body);
		$body = str_replace('[show_calendar_link]', JRoute::_(JURI::root() . 'index.php?option=com_toes&view=shows'), $body);

		$config = JFactory::getConfig();
		$fromname = $config->get('fromname');
		$fromemail = $config->get('mailfrom');
		$recipient = $user->email;

		/*
		$mail = JFactory::getMailer();

		$mail->SetFrom($fromemail, $fromname);
		$mail->setSubject($subject);
		$mail->setBody($body);
		$mail->addRecipient($recipient, $user->firstname . ' ' . $user->lastname);
		$mail->IsHTML(TRUE);

		if ($mail->Send())
		*/

		if (TOESMailHelper::sendMail($mail_action, $subject, $body, $recipient, $user->firstname . ' ' . $user->lastname)) {
			return true;
		} else {
			$this->setError(JText::_('COM_TOES_MAIL_SENDING_ERROR'));
		}

		return false;
	}
	
	public function copyshow(){
		
		 
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$datetime  = JFactory::getDate();
		$date = $datetime->tosql();

		$org_show_id = $app->input->getInt('show_id');
		
		$start_date = $app->input->getString('start_date');
		
		 

		if ($org_show_id) {
			if($start_date){
			$db->setQuery("select count(*) from `#__toes_show_day` where `show_day_show` =".$org_show_id);
			$show_days_count = $db->loadResult();
			
			$end_date = date('Y-m-d',strtotime(date('Y-m-d',strtotime($start_date." +".($show_days_count -1)." days"))));	
			
			}else{
				$this->setError(JText::_('ERROR_NO_START_DATE'));
				return false;				
			}
			 
			$query = "SELECT s.*,v.venue_name,a.address_latitude,a.address_longitude 
			FROM `#__toes_show` as s JOIN `#__toes_venue` as v ON s.show_venue = v.venue_id
			JOIN `#__toes_address` as a ON a.address_id = v.venue_address
			WHERE  s.`show_id` = " . $org_show_id;
			$db->setQuery($query);
			$show = $db->loadObject();
			$club = TOESHelper::getClub($org_show_id);			
			if (!TOESHelper::isAdmin() && !TOESHelper::is_clubowner($user->id, $club->club_id)) {
				$this->setError(JText::_('COM_TOES_NOAUTH'));
				return false;
			}
			$show->show_start_date = $start_date;	
			$show->show_end_date = $end_date;
			// check for 500 mile radius conflict
			
			$address = $show->venue_name;
			$params = JComponentHelper::getParams('com_toes');
			$radius = $params->get('show_miles');
			
			
			
			if($radius > 0 ){
				if($radius && $show->address_latitude!== '0.00000000' && $show->address_longitude!== '0.00000000' && $show->address_latitude!== '' && $show->address_longitude!== ''){
					$query = $db->getQuery(true);
					$query .='select s.show_id,v.venue_id,a.address_latitude,a.address_longitude';
					$query .=' ,(ACOS( SIN(RADIANS('.$show->address_latitude.')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$show->address_latitude.')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$show->address_longitude.')) ) * 3963.1676) AS distance'; 
					$query .=' FROM `#__toes_show` as s ';
					$query .=' LEFT join `#__toes_venue` as v ON s.`show_venue` = v.`venue_id`';
					$query .=' LEFT join `#__toes_address` as a ON v.`venue_address` = a.`address_id`';
					$query .=' WHERE ((( s.`show_end_date` BETWEEN '.$db->quote($start_date).' AND ' .$db->quote($end_date). ' ) OR
								( s.`show_start_date` BETWEEN '.$db->quote($start_date).' AND ' .$db->quote($end_date). ' )) AND 
								( s.show_status = 2  OR s.show_status = 1 OR s.show_status = 4 OR s.show_status = 7) AND
								(((ACOS( SIN(RADIANS('.$show->address_latitude.')) * SIN(RADIANS(a.address_latitude)) + COS(RADIANS('.$show->address_latitude.')) * COS(RADIANS(a.address_latitude)) * COS(RADIANS(a.address_longitude) - RADIANS('.$show->address_longitude.')) ) * 3963.1676) <= '.$radius.') OR (a.address_latitude = '.$show->address_latitude.' AND a.address_longitude = '.$show->address_longitude.')))';
					 
					//echo $query;
					$db->setQuery($query);
					$radiusresult = $db->loadObjectList();
					
				}	
			}
			//

			$region = TOESHelper::getRegionDetails($club->club_competitive_region);
			$need_approval = false;

			if ($region->competitive_region_confirmation_by_rd_needed) {
				$show_status = '1';
				$need_approval = true;
			}
			else
				$show_status = '2';
				
			if(count($radiusresult) > 0)
			{
				$show_status = '1';
			}
			 

			$query = "INSERT INTO #__toes_show (show_start_date,show_end_date,show_venue,show_flyer,show_motto,show_comments,show_extra_text_for_confirmation,
					show_format,show_published,show_status,show_organization,catalog_runs,show_paper_size,show_currency_used,show_cost_per_entry, 
					show_print_extra_lines_for_bod_and_bob_in_judges_book, show_print_extra_line_at_end_of_color_class_in_judges_book )
                    VALUES ({$db->quote($show->show_start_date)},
                    {$db->quote($show->show_end_date)},
                    {$show->show_venue},
                    {$db->quote($show->show_flyer)},
                    {$db->quote($show->show_motto)},
                    {$db->quote($show->show_comments)},
                    {$db->quote($show->show_extra_text_for_confirmation)},
                    {$show->show_format},
                    {$show->show_published},
                    {$show_status},
                    {$show->show_organization},
                    {$show->catalog_runs},
                    {$show->show_paper_size},
                    {$db->quote($show->show_currency_used)},
                    {$show->show_cost_per_entry}, 
                    {$show->show_print_extra_lines_for_bod_and_bob_in_judges_book}, 
                    {$show->show_print_extra_line_at_end_of_color_class_in_judges_book}
				)";
			 
			$db->setQuery($query);
			$db->execute();
			$show_id = $db->insertid();

			if ($show_id) {
				$query = "INSERT INTO `#__toes_club_organizes_show` (`club`,`show`)
                        VALUES ({$club->club_id}, {$show_id})";

				$db->setQuery($query);
				$db->execute();

				$this->sendMailtoRD($show_id, $region, $need_approval);

				$showdays = TOESHelper::getShowDays($org_show_id); 
				
				$i = 0;
				foreach($showdays as $showday) {
					++$i;
					$this_show_date = date('Y-m-d',strtotime($start_date." +$i days"));
					$query = "INSERT INTO `#__toes_show_day` (`show_day_show`,`show_day_date`,`show_day_cat_limit`)
                            VALUES (".$show_id.",".$db->quote($this_show_date).",".$showday->show_day_cat_limit.")";

					$db->setQuery($query);
					if ($db->execute()) {
						$show_day_id = $db->insertid();

						$query = "SELECT * FROM `#__toes_ring` WHERE `ring_show_day` = " . $showday->show_day_id;
						$db->setQuery($query);
						$rings = $db->loadObjectList();

						foreach($rings as $ring) {
							$query = "INSERT INTO `#__toes_ring` (`ring_show_day`,`ring_format`,`ring_judge`,`ring_clerk`,`ring_show`,`ring_organization`,`ring_number`,`ring_name`,`ring_timing`)
                                    VALUES (".$show_day_id.",".$ring->ring_format.",".$ring->ring_judge.",".$ring->ring_clerk.",".$show_id.",".$ring->ring_organization.",".$ring->ring_number.",".$db->quote($ring->ring_name).",".$ring->ring_timing.")";

							$db->setQuery($query);
							if ($db->execute()) {
								$ring_id = $db->insertid();
								if ($ring->ring_format == 3) {
									$filter = TOESHelper::getCongressFilters($ring->ring_id);
									if ($filter) {
										$query = $db->getQuery(true);
										$query->insert('#__toes_congress');
										$query->set('congress_name=' . $db->quote($ring->ring_name));
										$query->set('congress_breed_switch=' . $filter->breed_filter);
										$query->set('congress_gender_switch=' . $filter->gender_filter);
										$query->set('congress_new_trait_switch=' . $filter->newtrait_filter);
										$query->set('congress_hair_length_switch=' . $filter->hairlength_filter);
										$query->set('congress_category_switch=' . $filter->category_filter);
										$query->set('congress_division_switch=' . $filter->division_filter);
										$query->set('congress_color_switch=' . $filter->color_filter);
										$query->set('congress_title_switch=' . $filter->title_filter);
										$query->set('congress_manual_select_switch=' . $filter->manual_filter);
										$query->set('congress_id=' . $ring_id);

										$db->setQuery($query);
										$db->query();

										$values = explode(',', $filter->class_value);
										foreach($values as $value) {
											$query = $db->getQuery(true);
											$query->insert('#__toes_congress_competitive_class');
											$query->set('congress_competitive_class_competitive_class=' . $value);
											$query->set('congress_competitive_class_congress=' . $ring_id);

											$db->setQuery($query);
											$db->execute();
										}

										if ($filter->breed_filter && $filter->breed_value) {
											$values = explode(',', $filter->breed_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_breed');
												$query->set('congress_breed_breed=' . $value);
												$query->set('congress_breed_congress=' . $ring_id);

												$db->setQuery($query);
												$db->execute();
											}
										}

										if ($filter->gender_filter && $filter->gender_value) {
											$values = explode(',', $filter->gender_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_gender');
												$query->set('congress_gender_gender=' . $value);
												$query->set('congress_gender_congress=' . $ring_id);

												$db->setQuery($query);
												$db->execute();
											}
										}

										if ($filter->hairlength_filter && $filter->hairlength_value) {
											$values = explode(',', $filter->hairlength_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_hair_length');
												$query->set('congress_hair_length_hair_length=' . $value);
												$query->set('congress_hair_length_congress=' . $ring_id);

												$db->setQuery($query);
												$db->execute();
											}
										}

										if ($filter->category_filter && $filter->category_value) {
											$values = explode(',', $filter->category_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_category');
												$query->set('congress_category_category=' . $value);
												$query->set('congress_category_congress=' . $ring_id);

												$db->setQuery($query);
												$db->execute();
											}
										}

										if ($filter->division_filter && $filter->division_value) {
											$values = explode(',', $filter->division_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_division');
												$query->set('congress_division_division=' . $value);
												$query->set('congress_division_congress=' . $ring_id);

												$db->setQuery($query);
												$db->execute();
											}
										}

										if ($filter->color_filter && $filter->color_value) {
											$values = explode(',', $filter->color_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_color');
												$query->set('congress_color_color=' . $value);
												$query->set('congress_color_congress=' . $ring_id);

												$db->setQuery($query);
												$db->execute();
											}
										}

										if ($filter->title_filter && $filter->title_value) {
											$values = explode(',', $filter->title_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_title');
												$query->set('congress_title_title=' . $value);
												$query->set('congress_title_congress=' . $ring_id);

												$db->setQuery($query);
												$db->execute();
											}
										}
									}
								}
							}
						}
					}
				}

				$query = "SELECT * FROM `#__toes_show_has_official` WHERE `show` = " . $org_show_id;
				$db->setQuery($query);
				$show_officials = $db->loadObjectList();

				foreach($show_officials as $official) {
					$query = "INSERT INTO `#__toes_show_has_official` (`show`,`user`,`show_official_type`)
                            VALUES ({$show_id},{$official->user},{$official->show_official_type})";

					$db->setQuery($query);
					$db->execute();
				}
				//500 mile conflict mail
				if(count($radiusresult)) // send mail
				{	
				$url = JURI::getInstance();
				$fpc = fopen(JPATH_ROOT.'/logcopyshowemails.txt','a+');
				 
				foreach($radiusresult as $r){		
				$existingmonth = date('F',strtotime($r->show_start_date));
				$existing_startday = date('d',strtotime($r->show_start_date));
				$existing_endday = date('d',strtotime($r->show_end_date));
				$existingyear = date('y',strtotime($r->show_start_date));
				
				$conflictingmonth = date('F',strtotime($show->show_start_date));
				$conflictingyear = date('y',strtotime($show->show_start_date));
				$conflicting_startday = date('d',strtotime($show->show_start_date));
				$conflicting_endday = date('d',strtotime($show->show_end_date));
				 
				$showid = $r->show_id;  
				
				
				
				
				$query = $db->getQuery(true);
				$query->select('o.club,c.user,c.club_official_type,t.club_official_type,cl.club_name,
								cp.firstname,cp.lastname,s.show_start_date,s.show_end_date,
								v.venue_name,u.email,ss.show_status');
				$query->from('#__toes_club_organizes_show as o');
				$query->join('LEFT','#__toes_club_official as c ON c.club = o.club');
				$query->join('LEFT','#__toes_club_official_type as t ON t.club_official_type_id = c.club_official_type');
				$query->join('LEFT','#__toes_club as cl ON o.club = cl.club_id');
				$query->join('LEFT','#__toes_show as s ON o.show = s.show_id');
				$query->join('LEFT','#__toes_venue as v ON s.show_venue = v.venue_id');
				$query->join('LEFT','#__comprofiler as cp ON cp.user_id = c.user');
				$query->join('LEFT','#__users as u ON cp.user_id = u.id');
				$query->join('LEFT','#__toes_show_status as ss ON s.show_status = ss.show_status_id');
				$query->where($db->quoteName('o.show').'='.$showid);
				$db->setQuery($query);
				$existingclub_official = $db->loadObject();
				 
										
				$query1 = $db->getQuery(true);
				$query1->select('o.club,c.user,c.club_official_type,t.club_official_type,cl.club_name,
								cp.firstname,cp.lastname,s.show_start_date,s.show_end_date,v.venue_name,u.email');
				$query1->from('#__toes_club_organizes_show as o');
				$query1->join('LEFT','#__toes_club_official as c ON c.club = o.club');
				$query1->join('LEFT','#__toes_club_official_type as t ON t.club_official_type_id = c.club_official_type');
				$query1->join('LEFT','#__toes_club as cl ON o.club = cl.club_id');
				$query1->join('LEFT','#__toes_show as s ON o.show = s.show_id');
				$query1->join('LEFT','#__toes_venue as v ON s.show_venue = v.venue_id');
				$query1->join('LEFT','#__comprofiler as cp ON cp.user_id = c.user');
				$query1->join('LEFT','#__users as u ON cp.user_id = u.id');
				$query1->where($db->quoteName('o.show').'='.$show_id);
				$db->setQuery($query1);
				$conflictingclub_official = $db->loadObject();
				 
				
				$query1 = $db->getQuery(true);
				$query1->select('c.club_id,c.club_name');
				$query1->from('#__toes_club as c');
				$query1->join('LEFT','#__toes_club_organizes_show as s ON s.club = c.club_id');
				$query1->where('s.show='.$show_id);
				$db->setQuery($query1);
				$affectedclub = $db->loadObject	();
				/*
				$km = $r->distance * 1.609;
				$miles = $r->distance * 0.621;
				*/
				$km = round($r->distance * 1.609,1);
				$miles = round($r->distance * 0.621,1);
				
				$query1 = $db->getQuery(true);
				$query1->select('co.firstname,co.lastname,tc.club_competitive_region,comp.competitive_region_regional_director');
				$query1->from('#__toes_club_organizes_show as s');
				$query1->join('LEFT','#__toes_club as tc ON s.club = tc.club_id');
				$query1->join('LEFT','#__toes_competitive_region as comp ON tc.club_competitive_region = comp.competitive_region_id');
				$query1->join('LEFT','#__comprofiler as co ON comp.competitive_region_regional_director = co.user_id');
				$query1->where('s.show='.$r->show_id);
				$db->setQuery($query1);
				$existing_regionaldirector = $db->loadObject();		
				 
				
				$existing_address = TOESHelper::getShowDetails($r->show_id);
				
				$query2 = $db->getQuery(true);
				$query2->select('co.firstname,co.lastname,tc.club_competitive_region,comp.competitive_region_regional_director');
				$query2->from('#__toes_club_organizes_show as s');
				$query2->join('LEFT','#__toes_club as tc ON s.club = tc.club_id');
				$query2->join('LEFT','#__toes_competitive_region as comp ON tc.club_competitive_region = comp.competitive_region_id');
				$query2->join('LEFT','#__comprofiler as co ON comp.competitive_region_regional_director = co.user_id');
				$query2->where('s.show='.$show_id);
				$db->setQuery($query2);
				$conflicting_regionaldirector = $db->loadObject();
				
				 
				
				 
				
				//send mail to club official	
				$showdetails = $show_id . "," .$showid . "," . $date;
				$md5showdetails = md5($showdetails);
				
				
				
					if($existingclub_official)
					{
						fwrite($fpc, "Email to existing show official\n");
						$user = JFactory::getUser($existingclub_official->user);
						
						$config = JFactory::getConfig();
						$mailTemplate = TOESMailHelper::getTemplate('radius_search_using_latitude_longitude');
						$users = TOESHelper::getSubscribedUsers($show_id);
						$fromname = $config->get('fromname');
						$subject = $mailTemplate->mail_subject;
						$body = $mailTemplate->mail_body;						
						 
						$search_array = array('[firstname]','[lastname]','[show_id]','[club_official]','[show_manager]',
						'[name_of_club]','[club_name]','[start_date]','[end_date]','[location]','[other_club]','[other_club_start_date]',
						'[other_club_end_date]','[other_club_location]','[km]','[miles]','[other_firstname]','[other_lastname]','[other_email]',
						'[firstname_of_rd]','[lastname_of_rd]','[other_firstname_of_rd]','[other_lastname_of_rd]');
 						$replace_array = array($conflictingclub_official->firstname,$conflictingclub_official->lastname,$show_id,
 						$conflictingclub_official->club_official_type,'',$conflictingclub_official->club_name,$conflictingclub_official->club_name,
 						$start_date,$end_date,$conflictingclub_official->venue_name,$existingclub_official->club_name,$existingclub_official->show_start_date,
 						$existingclub_official->show_end_date,$existingclub_official->venue_name,$km,$miles,$existingclub_official->firstname,
 						$existingclub_official->lastname,$existingclub_official->email,$conflicting_regionaldirector->firstname,
 						$conflicting_regionaldirector->lastname,$existing_regionaldirector->firstname,$existing_regionaldirector->lastname);
 						$body = str_replace($search_array,$replace_array,$body);
 						 
						 			
						TOESMailHelper::sendMail('radius_search_using_latitude_longitude', $subject, $body, $user->email);
						fwrite($fp,"if existingclub_official \n"."\n");
						fwrite($fp,"radius_search_using_latitude_longitude\n".$body."\n");			 
						fwrite($fpc, $body."\n");
						
					}	
				
				
				$query = $db->getQuery(true);
				$query->select('o.*,s.show_official_type,cp.firstname,cp.lastname,cl.club_name,
								ts.show_start_date,ts.show_end_date,v.venue_name,u.email,o.user');
				$query->from('#__toes_show_has_official as o');
				$query->join('LEFT','#__toes_show_official_type as s ON o.show_official_type = s.show_official_type_id');
				$query->join('LEFT','#__comprofiler as cp ON cp.user_id = o.user');
				$query->join('LEFT','#__toes_club_organizes_show as cos ON cos.show = o.show');
				$query->join('LEFT','#__toes_club as cl ON cos.club = cl.club_id');
				$query->join('LEFT','#__toes_show as ts ON o.show = ts.show_id');
				$query->join('LEFT','#__toes_venue as v ON ts.show_venue = v.venue_id');
				$query->join('LEFT','#__users as u ON cp.user_id = u.id');
				$query->where($db->quoteName('o.show').'='.$r->show_id);
				$db->setQuery($query);
				$show_manager = $db->loadObjectList();
				//$show_manager = $db->loadObject();
			
					if(isset($show_manager))
					{
						foreach($show_manager as $m)
						{
							 
							
							fwrite($fpc, "Email to existing show manager\n");
							$user = JFactory::getUser($m->user);
							$mailTemplate = TOESMailHelper::getTemplate('radius_search_using_latitude_longitude');
							$config = JFactory::getConfig();
							$users = TOESHelper::getSubscribedUsers($show_id);
							$fromname = $config->get('fromname');
							 
							$subject = $mailTemplate->mail_subject;
							$body = $mailTemplate->mail_body;
							
 
							$search_array = array('[firstname]','[lastname]','[show_id]','[club_official]','[show_manager]',
							'[name_of_club]','[club_name]','[start_date]','[end_date]','[location]','[other_club]','[other_club_start_date]',
							'[other_club_end_date]','[other_club_location]','[km]','[miles]','[other_firstname]','[other_lastname]','[other_email]',
							'[firstname_of_rd]','[lastname_of_rd]','[other_firstname_of_rd]','[other_lastname_of_rd]');
							$replace_array = array($m->firstname,$m->lastname,$show_id,
							'',$m->show_official_type,$m->club_name,$m->club_name,
							$start_date,$end_date,$show_manager->venue_name,$existingclub_official->club_name,$existingclub_official->show_start_date,
							$existingclub_official->show_end_date,$existingclub_official->venue_name,$km,$miles,$existingclub_official->firstname,
							$existingclub_official->lastname,$existingclub_official->email,$conflicting_regionaldirector->firstname,
							$conflicting_regionaldirector->lastname,$existing_regionaldirector->firstname,$existing_regionaldirector->lastname);
							$body = str_replace($search_array,$replace_array,$body);
							 
							TOESMailHelper::sendMail('radius_search_using_latitude_longitude', $subject, $body, $user->email);
							fwrite($fp,"radius_search_using_latitude_longitude\n".$body."\n");
							fwrite($fpc, $body."\n");
							
						}
					}
						$query5 = $db->getQuery(true);
						$query5 = "insert into `#__toes_show_club_approval` (`existing_show_id`,`new_conflicting_show_id`,`datetime`,`hash`) VALUES
								(".$showid.",".$db->quote($show_id).",".$db->quote($date).",".$db->quote($md5showdetails).")";
						$db->setQuery($query5);
						$db->query();
					
				
				//send mail to RD 
				$other_rd = $existing_regionaldirector->competitive_region_regional_director;
				$rd = $conflicting_regionaldirector->competitive_region_regional_director;
				
				$conflicting_show_url = $url->getScheme() . '://' . $url->getHost() . JRoute::_('index.php?option=com_toes&view=shows', false) . '#show' . $show_id;
				$existing_show_url = $url->getScheme() . '://' . $url->getHost() . JRoute::_('index.php?option=com_toes&view=shows', false) . '#show' . $showid;

				$conflicting_show_link = '<a href="'.$conflicting_show_url.'">'.$conflicting_show_url.'</a>';
				$existing_show_link = '<a href="'.$existing_show_url.'">'.$existing_show_url.'</a>';

				
				if($other_rd == $rd)
				{
					fwrite($fpc, "Email to existing show regional director when both RD are same \n");
					$rdshowapprovaldetails = $show_id . "," .$date . "," . $showid;
					$rdmd5showapprovaldetails = md5($rdshowapprovaldetails);
					
					$user = JFactory::getUser($existing_regionaldirector->user);
					$mailTemplate = TOESMailHelper::getTemplate('show_regional_director_notification_same_region');
					$config = JFactory::getConfig();
					$users = TOESHelper::getSubscribedUsers($r->show_id);
					$fromname = $config->get('fromname');
					
					 
					$subject = $mailTemplate->mail_subject;
					$body = $mailTemplate->mail_body;
					
					$search_array = array('[firstname_of_rd]','[lastname_of_rd]','[name_of_club]','[club_name]','[start_date]',
					'[end_date]','[location]','[other_club]','[other_club_start_date]','[other_club_end_date]','[other_club_location]',
					'[miles]','[km]','[other_firstname]','[other_lastname]','[other_email]','[existing_show_link]','[conflicting_show_link]');
					
					//,'[other_firstname]','[other_lastname]','[other_email]','[firstname_of_rd]','[lastname_of_rd]');
					$replace_array = array($existing_regionaldirector->firstname,$existing_regionaldirector->lastname,$existingclub_official->club_name,
					$existingclub_official->club_name,$existingclub_official->show_start_date,$existingclub_official->show_end_date,$existingclub_official->venue_name,
					$conflictingclub_official->club_name,$conflictingclub_official->show_start_date,$conflictingclub_official->show_end_date,$conflictingclub_official->venue_name,
					$miles,$km,$conflictingclub_official->firstname,$conflictingclub_official->lastname,$conflictingclub_official->email,$existing_show_link,$conflicting_show_link);
					$body = str_replace($search_array,$replace_array,$body);
					
					TOESMailHelper::sendMail('show_regional_director_notification', $subject, $body, $user->email);
					fwrite($fpc, $body."\n");
					$query = $db->getQuery(true);
					$query = "insert into `#__toes_show_regional_director_approval` (`existing_show_id`,`new_conflicting_show_id`,`datetime`,`hash`) VALUES
								(".$showid.",".$db->quote($show_id).",".$db->quote($date).",".$db->quote($rdmd5showapprovaldetails).")";
					$db->setQuery($query);
					$db->query();	
				}
				else
				{
					$rd_showapprovaldetails = $showid . "," .$show_id . "," . $date;
					$rd_md5 = md5($rd_showapprovaldetails);
					$mailTemplate = TOESMailHelper::getTemplate('show_regional_director_notification_different_region');
					$subject = $mailTemplate->mail_subject;
					$body = $mailTemplate->mail_body;
					
					if($other_rd) // conflicted shows rd
					{	
						fwrite($fp, "Email to existing show regional director when both RD are NOT same \n");
						$other_rdshowapprovaldetails = $show_id . "," .$showid . "," . $date;
						$other_rdmd5 = md5($other_rdshowapprovaldetails);
						$user = JFactory::getUser($existing_regionaldirector->user);
						$search_array = array('[firstname_of_rd]','[lastname_of_rd]','[name_of_club]','[club_name]','[start_date]',
						'[end_date]','[location]','[other_club]','[other_club_start_date]','[other_club_end_date]','[other_club_location]',
						'[miles]','[km]','[other_firstname]','[other_lastname]','[other_email]','[existing_show_link]','[conflicting_show_link]');
						
						//,'[other_firstname]','[other_lastname]','[other_email]','[firstname_of_rd]','[lastname_of_rd]');
						$replace_array = array($existing_regionaldirector->firstname,$existing_regionaldirector->lastname,$existingclub_official->club_name,
						$existingclub_official->club_name,$existingclub_official->show_start_date,$existingclub_official->show_end_date,$existingclub_official->venue_name,
						$conflictingclub_official->club_name,$conflictingclub_official->show_start_date,$conflictingclub_official->show_end_date,$conflictingclub_official->venue_name,
						$miles,$km,$conflictingclub_official->firstname,$conflictingclub_official->lastname,$conflictingclub_official->email,$existing_show_link,$conflicting_show_link);
						$body = str_replace($search_array,$replace_array,$body);
						
						$config = JFactory::getConfig();
						$users = TOESHelper::getSubscribedUsers($r->show_id);
						$fromname = $config->get('fromname');						  
						TOESMailHelper::sendMail('show_regional_director_notification', $subject, $body, $user->email);
						fwrite($fpc, $body."\n");
					}					
					if($rd)
					{
						fwrite($fpc, "Email to conflicting show regional director when both RD are NOT same \n");
						$user = JFactory::getUser($conflicting_regionaldirector->user);
						$mailTemplate = TOESMailHelper::getTemplate('show_regional_director_notification');
						$config = JFactory::getConfig();						
						$fromname = $config->get('fromname');		
						$search_array = array('[firstname_of_rd]','[lastname_of_rd]','[name_of_club]','[club_name]','[start_date]',
						'[end_date]','[location]','[other_club]','[other_club_start_date]','[other_club_end_date]','[other_club_location]',
						'[miles]','[km]','[other_firstname]','[other_lastname]','[other_email]');
						
						//,'[other_firstname]','[other_lastname]','[other_email]','[firstname_of_rd]','[lastname_of_rd]');
						$replace_array = array($conflicting_regionaldirector->firstname,$conflicting_regionaldirector->lastname,$conflicting_regionaldirector->club_name,
						$existingclub_official->club_name,$existingclub_official->show_start_date,$existingclub_official->show_end_date,$existingclub_official->venue_name,
						$conflictingclub_official->club_name,$conflictingclub_official->show_start_date,$conflictingclub_official->show_end_date,$conflictingclub_official->venue_name,
						$miles,$km,$conflictingclub_official->firstname,$conflictingclub_official->lastname,$conflictingclub_official->email);
						$body = str_replace($search_array,$replace_array,$body);				  					 
						TOESMailHelper::sendMail('show_regional_director_notification', $subject, $body, $user->email);	
						fwrite($fpc, $body."\n");					 
					}
					
						$query = $db->getQuery(true);
						$query = "insert into `#__toes_show_regional_director_approval` (`existing_show_id`,`new_conflicting_show_id`,`datetime`,`hash`) VALUES
								(".$showid.",".$db->quote($show_id).",".$db->quote($date).",".$db->quote($rd_md5).")";
						$db->setQuery($query);						
						$db->query();
				}	
					
			}	
			fclose($fp);
			fclose($fpc);
			$db->setQuery("UPDATE `#__toes_show` SET `show_has_500_mile_conflict` = 1 where `show_id` =".$show_id)->execute();
			//die;
				}
				// end 500 mile conflict mail
				return $show_id;
			} else {
				echo $db->getErrorMsg();
				die;
				$this->setError(JText::_('ERROR_IN_SAVING_DETAILS'));
				return false;
			}
		} else {
			$this->setError(JText::_('ERROR_IN_SAVING_DETAILS'));
			return false;
		}
		
	}

	public function copy() {
		 
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$user = JFactory::getUser();

		$org_show_id = $app->input->getInt('show_id');
		
		$start_date = $app->input->getString('start_date');

		if ($org_show_id) {
			$query = "SELECT * FROM `#__toes_show` WHERE `show_id` = " . $org_show_id;
			$db->setQuery($query);
			$show = $db->loadObject();
			$club = TOESHelper::getClub($org_show_id);
			
			if($start_date){
			$db->setQuery("select count(*) from `#__toes_show_day` where `show_day_show` =".$org_show_id);
			$show_days = $db->loadResult();
			$show->show_start_date = $start_date;	
			$show->show_end_date = date('Y-m-d',strtotime(date('Y-m-d',strtotime($start_date." +".($show_days -1)." days"))));	
			}

			if (!TOESHelper::isAdmin() && !TOESHelper::is_clubowner($user->id, $club->club_id)) {
				$this->setError(JText::_('COM_TOES_NOAUTH'));
				return false;
			}

			$region = TOESHelper::getRegionDetails($club->club_competitive_region);
			$need_approval = false;

			if ($region->competitive_region_confirmation_by_rd_needed) {
				$show_status = '1';
				$need_approval = true;
			}
			else
				$show_status = '2';

			$query = "INSERT INTO #__toes_show (show_start_date,show_end_date,show_venue,show_flyer,show_motto,show_comments,show_extra_text_for_confirmation,
					show_format,show_published,show_status,show_organization,catalog_runs,show_paper_size,show_currency_used,show_cost_per_entry, 
					show_print_extra_lines_for_bod_and_bob_in_judges_book, show_print_extra_line_at_end_of_color_class_in_judges_book )
                    VALUES ({$db->quote($show->show_start_date)},
                    {$db->quote($show->show_end_date)},
                    {$show->show_venue},
                    {$db->quote($show->show_flyer)},
                    {$db->quote($show->show_motto)},
                    {$db->quote($show->show_comments)},
                    {$db->quote($show->show_extra_text_for_confirmation)},
                    {$show->show_format},
                    {$show->show_published},
                    {$show_status},
                    {$show->show_organization},
                    {$show->catalog_runs},
                    {$show->show_paper_size},
                    {$db->quote($show->show_currency_used)},
                    {$show->show_cost_per_entry}, 
                    {$show->show_print_extra_lines_for_bod_and_bob_in_judges_book}, 
                    {$show->show_print_extra_line_at_end_of_color_class_in_judges_book}
				)";
			 
			$db->setQuery($query);
			$db->query();
			$show_id = $db->insertid();

			if ($show_id) {
				$query = "INSERT INTO `#__toes_club_organizes_show` (`club`,`show`)
                        VALUES ({$club->club_id}, {$show_id})";

				$db->setQuery($query);
				$db->query();

				$this->sendMailtoRD($show_id, $region, $need_approval);

				$showdays = TOESHelper::getShowDays($org_show_id);

				foreach($showdays as $showday) {
					$query = "INSERT INTO `#__toes_show_day` (`show_day_show`,`show_day_date`,`show_day_cat_limit`)
                            VALUES ({$show_id},{$db->quote($showday->show_day_date)},{$showday->show_day_cat_limit})";

					$db->setQuery($query);
					if ($db->query()) {
						$show_day_id = $db->insertid();

						$query = "SELECT * FROM `#__toes_ring` WHERE `ring_show_day` = " . $showday->show_day_id;
						$db->setQuery($query);
						$rings = $db->loadObjectList();

						foreach($rings as $ring) {
							$query = "INSERT INTO `#__toes_ring` (`ring_show_day`,`ring_format`,`ring_judge`,`ring_clerk`,`ring_show`,`ring_organization`,`ring_number`,`ring_name`,`ring_timing`)
                                    VALUES ({$show_day_id},{$ring->ring_format},{$ring->ring_judge},{$ring->ring_clerk},{$show_id},{$ring->ring_organization},{$ring->ring_number},{$db->quote($ring->ring_name)},{$ring->ring_timing})";

							$db->setQuery($query);
							if ($db->query()) {
								$ring_id = $db->insertid();
								if ($ring->ring_format == 3) {
									$filter = TOESHelper::getCongressFilters($ring->ring_id);
									if ($filter) {
										$query = $db->getQuery(true);
										$query->insert('#__toes_congress');
										$query->set('congress_name=' . $db->quote($ring->ring_name));
										$query->set('congress_breed_switch=' . $filter->breed_filter);
										$query->set('congress_gender_switch=' . $filter->gender_filter);
										$query->set('congress_new_trait_switch=' . $filter->newtrait_filter);
										$query->set('congress_hair_length_switch=' . $filter->hairlength_filter);
										$query->set('congress_category_switch=' . $filter->category_filter);
										$query->set('congress_division_switch=' . $filter->division_filter);
										$query->set('congress_color_switch=' . $filter->color_filter);
										$query->set('congress_title_switch=' . $filter->title_filter);
										$query->set('congress_manual_select_switch=' . $filter->manual_filter);
										$query->set('congress_id=' . $ring_id);

										$db->setQuery($query);
										$db->query();

										$values = explode(',', $filter->class_value);
										foreach($values as $value) {
											$query = $db->getQuery(true);
											$query->insert('#__toes_congress_competitive_class');
											$query->set('congress_competitive_class_competitive_class=' . $value);
											$query->set('congress_competitive_class_congress=' . $ring_id);

											$db->setQuery($query);
											$db->query();
										}

										if ($filter->breed_filter && $filter->breed_value) {
											$values = explode(',', $filter->breed_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_breed');
												$query->set('congress_breed_breed=' . $value);
												$query->set('congress_breed_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->gender_filter && $filter->gender_value) {
											$values = explode(',', $filter->gender_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_gender');
												$query->set('congress_gender_gender=' . $value);
												$query->set('congress_gender_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->hairlength_filter && $filter->hairlength_value) {
											$values = explode(',', $filter->hairlength_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_hair_length');
												$query->set('congress_hair_length_hair_length=' . $value);
												$query->set('congress_hair_length_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->category_filter && $filter->category_value) {
											$values = explode(',', $filter->category_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_category');
												$query->set('congress_category_category=' . $value);
												$query->set('congress_category_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->division_filter && $filter->division_value) {
											$values = explode(',', $filter->division_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_division');
												$query->set('congress_division_division=' . $value);
												$query->set('congress_division_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->color_filter && $filter->color_value) {
											$values = explode(',', $filter->color_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_color');
												$query->set('congress_color_color=' . $value);
												$query->set('congress_color_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}

										if ($filter->title_filter && $filter->title_value) {
											$values = explode(',', $filter->title_value);
											foreach($values as $value) {
												$query = $db->getQuery(true);
												$query->insert('#__toes_congress_title');
												$query->set('congress_title_title=' . $value);
												$query->set('congress_title_congress=' . $ring_id);

												$db->setQuery($query);
												$db->query();
											}
										}
									}
								}
							}
						}
					}
				}

				$query = "SELECT * FROM `#__toes_show_has_official` WHERE `show` = " . $org_show_id;
				$db->setQuery($query);
				$show_officials = $db->loadObjectList();

				foreach($show_officials as $official) {
					$query = "INSERT INTO `#__toes_show_has_official` (`show`,`user`,`show_official_type`)
                            VALUES ({$show_id},{$official->user},{$official->show_official_type})";

					$db->setQuery($query);
					$db->query();
				}


				return $show_id;
			} else {
				echo $db->getErrorMsg();
				die;
				$this->setError(JText::_('ERROR_IN_SAVING_DETAILS'));
				return false;
			}
		} else {
			$this->setError(JText::_('ERROR_IN_SAVING_DETAILS'));
			return false;
		}
	}

	function subscribe($show_id, $user_id) {

		$db = JFactory::getDbo();
		$query = "SELECT s.`user_subcribed_to_show_user`
            FROM `#__toes_user_subcribed_to_show` as `s`
            WHERE `s`.`user_subcribed_to_show_user` = {$user_id}
            AND `s`.`user_subcribed_to_show_show` = {$show_id}";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$subscribed = $db->loadObject();

		if ($subscribed) {
			return true;
		}

		$query = "INSERT INTO `#__toes_user_subcribed_to_show` SET
            `user_subcribed_to_show_user` = {$user_id} ,
            `user_subcribed_to_show_show` = {$show_id}";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		if ($db->query()) {
			
			$mailTemplate = TOESMailHelper::getTemplate('subscribed_to_show');
			
			if($mailTemplate) {
				$subject = $mailTemplate->mail_subject;
				$body = $mailTemplate->mail_body;
			} else {
				$subject = JText::_('COM_TOES_MAIL_SUBSCRIBED_TO_SHOW_SUBJECT');
				$body = JText::_('COM_TOES_MAIL_SUBSCRIBED_TO_SHOW_BODY');
			}

			$show = TOESHelper::getShowDetails($show_id);
			$userInfo = TOESHelper::getUserInfo($user_id);

			$body = str_replace('[firstname]', ($userInfo->firstname) ? $userInfo->firstname : $userInfo->name, $body);

			$body = str_replace('[City]', $show->address_city, $body);
			$body = str_replace('[, [State]]', $show->address_state ? ', ' . $show->address_state : '', $body);
			$body = str_replace('[Country]', $show->address_country, $body);

			$body = str_replace('[club name]', $show->club_name, $body);

			$start_date = date('d', strtotime($show->show_start_date));
			$start_date_month = date('M', strtotime($show->show_start_date));
			$start_date_year = date('Y', strtotime($show->show_start_date));

			$end_date = date('d', strtotime($show->show_end_date));
			$end_date_month = date('M', strtotime($show->show_end_date));
			$end_date_year = date('Y', strtotime($show->show_end_date));

			$show_date = $start_date_month . ' ' . $start_date;

			if ($end_date_year != $start_date_year) {
				$show_date .= ' ' . $start_date_year;
			}

			if ($end_date_month != $start_date_month) {
				if (date('t', strtotime($show->show_start_date)) != $start_date)
					$show_date .= ' - ' . date('t', strtotime($show->show_start_date));
				if ($end_date == '01')
					$show_date .= ', ' . $end_date_month . ' ' . $end_date;
				else
					$show_date .= ', ' . $end_date_month . ' 01 - ' . $end_date;
			} else {
				if ($start_date != $end_date)
					$show_date .= ' - ' . $start_date_month . ' ' . $end_date;
			}

			$show_date .= ' ' . $end_date_year;

			$body = str_replace('[showdates]', $show_date, $body);

			$url = JURI::getInstance();
			$show_link = $url->getScheme() . '://' . $url->getHost() . JRoute::_('index.php?option=com_toes&view=shows', false) . '#show' . $show_id;

			$body = str_replace('[show_link]', $show_link, $body);

			/*
			$mail = JFactory::getMailer();
			$config = JFactory::getConfig();
			$fromname = $config->get('fromname');
			$fromemail = $config->get('mailfrom');

			$mail->SetFrom($fromemail, $fromname);
			$mail->setSubject($subject);
			$mail->setBody($body);
			$mail->addRecipient($userInfo->email);
			$mail->IsHTML(TRUE);

			$mail->Send();
			*/
			
		   TOESMailHelper::sendMail('subscribed_to_show', $subject, $body, $userInfo->email);
			
			return true;
		}
		else {
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	function unsubscribe($show_id, $user_id) {

		$db = JFactory::getDbo();
		$query = "SELECT s.`user_subcribed_to_show_user`
            FROM `#__toes_user_subcribed_to_show` as `s`
            WHERE `s`.`user_subcribed_to_show_user` = {$user_id}
            AND `s`.`user_subcribed_to_show_show` = {$show_id}";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$subscribed = $db->loadObject();

		if (!$subscribed) {
			return true;
		}

		$query = "DELETE FROM `#__toes_user_subcribed_to_show` WHERE
            `user_subcribed_to_show_user` = {$user_id} AND
            `user_subcribed_to_show_show` = {$show_id}";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		if ($db->query()) {
			
			$mail_template = TOESMailHelper::getTemplate('unsubscribed_from_show');
			
			if($mail_template) {
				$subject = $mail_template->mail_subject;
				$body = $mail_template->mail_body;
			} else {
				$subject = JText::_('COM_TOES_MAIL_UNSUBSCRIBED_FROM_SHOW_SUBJECT');
				$body = JText::_('COM_TOES_MAIL_UNSUBSCRIBED_FROM_SHOW_BODY');
			}

			$show = TOESHelper::getShowDetails($show_id);
			$userInfo = TOESHelper::getUserInfo($user_id);

			$body = str_replace('[firstname]', ($userInfo->firstname) ? $userInfo->firstname : $userInfo->name, $body);

			$body = str_replace('[City]', $show->address_city, $body);
			$body = str_replace('[, [State]]', $show->address_state ? ', ' . $show->address_state : '', $body);
			$body = str_replace('[Country]', $show->address_country, $body);

			$body = str_replace('[club name]', $show->club_name, $body);

			$start_date = date('d', strtotime($show->show_start_date));
			$start_date_month = date('M', strtotime($show->show_start_date));
			$start_date_year = date('Y', strtotime($show->show_start_date));

			$end_date = date('d', strtotime($show->show_end_date));
			$end_date_month = date('M', strtotime($show->show_end_date));
			$end_date_year = date('Y', strtotime($show->show_end_date));

			$show_date = $start_date_month . ' ' . $start_date;

			if ($end_date_year != $start_date_year) {
				$show_date .= ' ' . $start_date_year;
			}

			if ($end_date_month != $start_date_month) {
				if (date('t', strtotime($show->show_start_date)) != $start_date)
					$show_date .= ' - ' . date('t', strtotime($show->show_start_date));
				if ($end_date == '01')
					$show_date .= ', ' . $end_date_month . ' ' . $end_date;
				else
					$show_date .= ', ' . $end_date_month . ' 01 - ' . $end_date;
			} else {
				if ($start_date != $end_date)
					$show_date .= ' - ' . $start_date_month . ' ' . $end_date;
			}

			$show_date .= ' ' . $end_date_year;

			$body = str_replace('[showdates]', $show_date, $body);

			$url = JURI::getInstance();
			$show_link = $url->getScheme() . '://' . $url->getHost() . JRoute::_('index.php?option=com_toes&view=shows', false) . '#show' . $show_id;

			$body = str_replace('[show_link]', $show_link, $body);

			/*
			$mail = JFactory::getMailer();
			$config = JFactory::getConfig();
			$fromname = $config->get('fromname');
			$fromemail = $config->get('mailfrom');

			$mail->SetFrom($fromemail, $fromname);
			$mail->setSubject($subject);
			$mail->setBody($body);
			$mail->addRecipient($userInfo->email);
			$mail->IsHTML(TRUE);

			$mail->Send();
			*/
			
		   TOESMailHelper::sendMail('unsubscribed_to_show', $subject, $body, $userInfo->email);
			
			return true;
		}
		else {
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	function getInvoice() {
			
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Initialise variables.
		$pk = $app->input->getInt('id', 0);
		
		$query->select('`invoice_id`, `invoice_status`');
		$query->from('`#__toes_paypal_invoice_detail`');
		$query->where('`show_id` = '.$pk);
					
		$db->setQuery($query);
		
		return $db->loadObject();		
	}
	
	public function getCheckurlhash()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->get('id');
		$hash = $app->input->get('hash');
		
		$query = $db->getQuery(true);
		$query->select('`hash`');
		$query->from('#__toes_show_club_approval');
		$query->where('show_id='.$db->quote($id).' AND `hash`='.$db->quote($hash));
		//echo $query;
		$db->setQuery($query);
		$url = $db->loadResult();
		
		return $url;	
	}
	
	public function getCheckshowisrejected()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->get('id');
		$hash = $app->input->get('hash');
		$conflictshows ='';
		if($hash)
		{
			$query1 = $db->getQuery(true);
			$query1->select('a.*');
			$query1->from('#__toes_show_club_approval as a');
			$query1->where('a.show_id='.$id);
			$query1->order('a.id DESC');
			$query1->setLimit('1');
			$db->setQuery($query1);
			$conflicedrd_shows = $db->loadObject();
			
			$query2 = $db->getQuery(true);
			$query2->select('a.*,c.club_name,s.club');
			$query2->from('#__toes_show_club_approval as a');
			$query2->join('LEFT','#__toes_club_organizes_show as s ON s.show = a.show_id');
			$query2->join('LEFT','#__toes_club as c ON s.club = c.club_id');
			$query2->where('(a.reject = 1 AND a.approval = 0) AND a.datetime='.$db->quote($conflicedrd_shows->datetime));
			$db->setQuery($query2);
			$conflictshows = $db->loadObject();
		}
		
		return $conflictshows;
	}
	
	public function getRdshowapproveurl()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->get('id');
		$hash = $app->input->get('hash');
		
		$query = $db->getQuery(true);
		$query->select('rd.*');
		$query->from('#__toes_show_regional_director_approval as rd');
		$query->where('rd.current_show_id ='.$id);
		$query->order('rd.id DESC');
		$query->setLimit(1);
		$db->setQuery($query);
		$shows = $db->loadObject();
		
		$query1 = $db->getQuery(true);
		$query1->select('comp.competitive_region_regional_director');
		$query1->from('#__toes_club_organizes_show as s');
		$query1->join('LEFT','#__toes_club as tc ON s.club = tc.club_id');
		$query1->join('LEFT','#__toes_competitive_region as comp ON tc.club_competitive_region = comp.competitive_region_id');
		$query1->join('LEFT','#__comprofiler as co ON comp.competitive_region_regional_director = co.user_id');
		$query1->where('s.show='.$id);
		$db->setQuery($query1);
		$rd = $db->loadResult();
		//echo $query1;
		
		$query2 = $db->getQuery(true);
		$query2->select('comp.competitive_region_regional_director');
		$query2->from('#__toes_club_organizes_show as s');
		$query2->join('LEFT','#__toes_club as tc ON s.club = tc.club_id');
		$query2->join('LEFT','#__toes_competitive_region as comp ON tc.club_competitive_region = comp.competitive_region_id');
		$query2->join('LEFT','#__comprofiler as co ON comp.competitive_region_regional_director = co.user_id');
		$query2->where('s.show='.$shows->show_id);
		$db->setQuery($query2);
		$conflicted_rd = $db->loadResult();
		//echo $query2;
			
		if($rd == $conflicted_rd)
		{
			$query1 = $db->getQuery(true);
			$query1->select('reject');
			$query1->from('#__toes_show_regional_director_approval ');
			$query1->where('(approval = 0 AND reject = 1) AND datetime='.$db->quote($shows->datetime));//(approval = 0 AND reject = 0) AND 
			$db->setQuery($query1);
			$reject = $db->loadResult();
			
			$query1 = $db->getQuery(true);
			$query1->select('approval');
			$query1->from('#__toes_show_regional_director_approval ');
			$query1->where('(approval = 1 AND reject = 0) AND datetime='.$db->quote($shows->datetime));//(approval = 0 AND reject = 0) AND 
			$db->setQuery($query1);
			$approval = $db->loadResult();
			if($reject)
			{
				return 3;
			}
			elseif($approval)
			{
				return 1;
			}	
			else
			{
				return 1;
			}
		}
		else
		{
			$query2 = $db->getQuery(true);
			$query2->select('count(*)');
			$query2->from('#__toes_show_regional_director_approval ');
			$query2->where('datetime='.$db->quote($shows->datetime).' AND hash !='.$db->quote($hash));// (approval = 1 AND reject = 0) AND 
			$db->setQuery($query2);
			//echo $query2;
			$showcount = $db->loadResult();
		
			$query1 = $db->getQuery(true);
			$query1->select('*');
			$query1->from('#__toes_show_regional_director_approval ');
			$query1->where('datetime='.$db->quote($shows->datetime).' AND hash !='.$db->quote($hash));//(approval = 0 AND reject = 0) AND 
			$db->setQuery($query1);
			//echo $query1;
			//$approval = $db->loadResult();
			$approval = $db->loadObject();
			
		
			//return $approval;
			if($approval->approval == '0' && $approval->reject == '0')
			{
				return 2;
			}
			elseif($approval->approval == '1' && $approval->reject == '0')
			{
				return 1;
			}	
			else
			{
				return 3;
			}
		}
		
		/* 
		$query1 = $db->getQuery(true);
		$query1->select('approval,reject');
		$query1->from('#__toes_show_club_approval');
		$query1->where('(approval = 1 AND reject = 0)  AND current_show_id ='.$db->quote($id).' AND hash='.$db->quote($hash));
		echo $query1;
		$db->setQuery($query1);
		$approval = $db->loadResult();*/
		
	}
	
	public function getShowsrejected()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->get('id');
		$hash = $app->input->get('hash');
		
		$conflictshows ='';
		if($hash)
		{
			$query = $db->getQuery(true);
			$query->select('a.*');
			$query->from('#__toes_show_regional_director_approval as a');
			$query->where('a.current_show_id='.$db->quote($id));
			$query->order('a.id DESC');
			$query->setLimit(1);
			$db->setQuery($query);
			$rdshow = $db->loadObject();
			
			
			$query1 = $db->getQuery(true);
			$query1->select('c.club_name');
			$query1->from('#__toes_show_regional_director_approval as a');
			$query1->join('LEFT','#__toes_club_organizes_show as s ON s.show = a.show_id');
			$query1->join('LEFT','#__toes_club as c ON s.club = c.club_id');
			$query1->where('(a.reject = 1 AND a.approval = 0) AND a.datetime='.$db->quote($rdshow->datetime));
			//echo $query1;
			$db->setQuery($query1);
			$rejectedshow = $db->loadResult();
			
		}
		
		return $rejectedshow;		
	}
	
	public function getConflicted_rdshowapproveurl()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->get('id');
		$hash = $app->input->get('hash');
		
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('#__toes_show_club_approval as a');
		$query->where('a.show_id ='.$db->quote($id));
		$query->order('a.id DESC');
		$query->setLimit(1);
		$db->setQuery($query);
		$shows = $db->loadObject();
		
		$query1 = $db->getQuery(true);
		$query1->select('count(*)');
		$query1->from('#__toes_show_club_approval');
		$query1->where('show_id ='.$db->quote($id).' AND datetime='.$db->quote($shows->datetime));
		$db->setQuery($query1);
		$rdshow = $db->loadResult();
	
		$query = $db->getQuery(true);
		$query->select('count(approval)');
		$query->from('#__toes_show_club_approval');
		$query->where('(approval = 1 AND reject = 0 ) AND show_id ='.$db->quote($id).' AND datetime='.$db->quote($shows->datetime));
		$db->setQuery($query);
		//echo $query;
		$conflicted_approval = $db->loadResult();
		
		if($rdshow == $conflicted_approval)
		{
			return 1;
		}
		else
		{
			return 2;
		}
	}
	
	public function getRdcheckshowisrejected()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$id = $app->input->get('id');
		$hash = $app->input->get('hash');
		$conflictshows ='';
		if($hash)
		{
			$query1 = $db->getQuery(true);
			$query1->select('a.*');
			$query1->from('#__toes_show_club_approval as a');
			$query1->where('a.show_id='.$id);
			$query1->order('a.id DESC');
			$query1->setLimit('1');
			$db->setQuery($query1);
			$conflicedrd_shows = $db->loadObject();
			
			$query2 = $db->getQuery(true);
			$query2->select('a.*,c.club_name,s.club');
			$query2->from('#__toes_show_club_approval as a');
			$query2->join('LEFT','#__toes_club_organizes_show as s ON s.show = a.show_id');
			$query2->join('LEFT','#__toes_club as c ON s.club = c.club_id');
			$query2->where('(a.reject = 1 AND a.approval = 0) AND a.datetime='.$db->quote($conflicedrd_shows->datetime));
			$db->setQuery($query2);
			$conflictshows = $db->loadObject();
			
			/*
			$query1 = $db->getQuery(true);
			$query1->select('current_show_id,datetime');
			$query1->from('#__toes_show_club_approval');
			$query1->where('show_id='.$db->quote($id). ' AND `hash` = '.$db->quote($hash));
			echo $query1;
			$db->setQuery($query1);
			$currentshowid = $db->loadObject();
			
			$query2 = $db->getQuery(true);
			$query2->select('a.show_id,a.approval,a.reject,c.club_name,s.club');
			$query2->from('#__toes_show_club_approval as a');
			$query2->join('LEFT','#__toes_club_organizes_show as s ON s.show = a.show_id');
			$query2->join('LEFT','#__toes_club as c ON s.club = c.club_id');
			$query2->where('(a.reject = 1 AND a.approval = 0) AND a.current_show_id='.$currentshowid->current_show_id. ' AND datetime='.$db->quote($currentshowid->datetime));
			$db->setQuery($query2);
			$conflictshows = $db->loadObject();
			*/
		}
		
		return $conflictshows;
	}
	
	
	
	public function generateaccesscode()
	{
		$access_code = JUserHelper::genRandomPassword(12);
		$digits = $access_code;
		$digits = strtoupper($digits);
		$digits = implode('-', str_split($digits, 4));
		
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)');
		$query->from('#__toes_ring');
		$query->where('ring_clerk_access_code='.$db->quote($digits));
		$db->setQuery($query);
		$existaccesscode = $db->loadResult();
		
		if($existaccesscode)
		{
			$this->generateaccesscode();	
		}
		else
		{
			return $digits;
		}
	}
}
