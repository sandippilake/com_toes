<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * TOES component helper.
 *
 * @package	Joomla.Administrator
 * @subpackage	com_toes
 */
class TOESHelper {
	
	protected static $regionalDirectorRegion = array();
	protected static $clubOfficialClubs = array();
	protected static $clubOwnerClubs = array();
	protected static $entryClerkShows = array();
	protected static $masterClerkShows = array();
	protected static $showManagerShows = array();
	protected static $showOfficialShows = array();
	protected static $orgOfficial = array();
	
	protected static $userDetails = array();
	protected static $catDetails = array();
	protected static $showDetails = array();
	
	protected static $showSummeries = array();
	protected static $competativeRegions = array();
	protected static $subscribedShows = array();
	protected static $catCatRelations = array();
	protected static $catUserRelations = array();
	protected static $userCatRelations = array();
	protected static $userCats = array();
	protected static $showEntriesCount = array();
	
	protected static $judges = array();
	protected static $judgesRings = array();
	
	public static $extension = 'com_toes';

	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_DASHBOARD'),
			'index.php?option=com_toes',
			$vName == 'toes' || $vName == ''
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_CLUBS'),
			'index.php?option=com_toes&view=clubs',
			$vName == 'clubs'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_CATS'),
			'index.php?option=com_toes&view=cats',
			$vName == 'cats'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_REGISTRATION_NUMBER_FORMATS'),
			'index.php?option=com_toes&view=regnumberformats',
			$vName == 'regnumberformats'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_CATEGORIES'),
			'index.php?option=com_toes&view=categories',
			$vName == 'categories'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_DIVISIONS'),
			'index.php?option=com_toes&view=divisions',
			$vName == 'divisions'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_COLORS'),
			'index.php?option=com_toes&view=colors',
			$vName == 'colors'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_BREEDS'),
			'index.php?option=com_toes&view=breeds',
			$vName == 'breeds'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_GENDERS'),
			'index.php?option=com_toes&view=genders',
			$vName == 'genders'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_JUDGES'),
			'index.php?option=com_toes&view=judges',
			$vName == 'judges'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_VENUES'),
			'index.php?option=com_toes&view=venues',
			$vName == 'venues'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_SMTP_ACCOUNTS'),
			'index.php?option=com_toes&view=smtpaccounts',
			$vName == 'smtpaccounts'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_MAIL_TMPLS'),
			'index.php?option=com_toes&view=mailtmpls',
			$vName == 'mailtmpls'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_COUNTRIES'),
			'index.php?option=com_toes&view=countries',
			$vName == 'countries'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_STATES'),
			'index.php?option=com_toes&view=states',
			$vName == 'states'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_TOES_CITIES'),
			'index.php?option=com_toes&view=cities',
			$vName == 'cities'
		);
	}

	public static function aasort($array, $key) {
		$sorter = array();
		$ret = array();
		if($array) {
			reset($array);
			foreach ($array as $ii => $va) {
				$sorter[$ii] = $va->$key;
			}
			asort($sorter);
			foreach ($sorter as $ii => $va) {
				$ret[$ii] = $array[$ii];
			}
			$array = $ret;
		}	
		return $array;
	}

	public static function addhttp($url) {
		if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
			$url = "http://" . $url;
		}
		return $url;
	}

	public static function sendFile($file, $mime, $overrideFileName = '') {
		$mainframe = JFactory::getApplication();

		// send headers
		header("Content-Type: $mime");
		header("X-Sendfile: $file");

		list($start, $len) = self::http_rangeRequest(filesize($file));

		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Accept-Ranges: bytes');

		//application mime type is downloadable
		if (strtolower(substr($mime, 0, 11)) == 'application') {
			if ($overrideFileName == '') {
				$filename = basename($file);
			} else {
				$filename = $overrideFileName;
			}
			header('Content-Disposition: attachment; filename="' . $filename . '";');
		}

		$chunksize = 1 * (1024 * 1024);
		// send file contents
		$fp = @fopen($file, "rb");
		if ($fp) {
			fseek($fp, $start); //seek to start of range

			$chunk = ($len > $chunksize) ? $chunksize : $len;
			while (!feof($fp) && $chunk > 0) {
				@set_time_limit(0); // large files can take a lot of time
				print fread($fp, $chunk);
				flush();
				$len -= $chunk;
				$chunk = ($len > $chunksize) ? $chunksize : $len;
			}
			fclose($fp);
		} else {
			header("HTTP/1.0 500 Internal Server Error");
			print "Could not read $file - bad permissions?";
			//$vm_mainframe->close(true);
			$mainframe->close(true);
		}
	}

	public static function http_rangeRequest($size, $exitOnError = true) {
		$mainframe = JFactory::getApplication();

		if (!isset($_SERVER['HTTP_RANGE'])) {
			// no range requested - send the whole file
			header("Content-Length: $size");
			return array(0, $size);
		}

		$t = explode('=', $_SERVER['HTTP_RANGE']);
		if (!$t[0] == 'bytes') {
			// we only understand byte ranges - send the whole file
			header("Content-Length: $size");
			return array(0, $size);
		}

		$r = explode('-', $t[1]);
		$start = (int) $r[0];
		$end = (int) $r[1];
		if (!$end)
			$end = $size - 1;
		if ($start > $end || $start > $size || $end > $size) {
			if ($exitOnError) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				print 'Bad Range Request!';
				$mainframe->close(true);
			} else {
				return array(0, $size);
			}
		}

		$tot = $end - $start + 1;
		header('HTTP/1.1 206 Partial Content');
		header("Content-Range: bytes {$start}-{$end}/{$size}");
		header("Content-Length: $tot");

		return array($start, $tot);
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The article ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions() {
		// Reverted a change for version 2.5.6
		$user = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_toes';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

	public static function isAdmin($userid = null) {
		$user = JFactory::getUser($userid);
		
		if (in_array(8, $user->getAuthorisedGroups()))
			return true;
		else
			return false;
	}
	public static function isEO($userid = null) {
		$user = JFactory::getUser($userid);
		
		if (in_array(24, $user->getAuthorisedGroups()))
			return true;
		else
			return false;
	}

	public static function isEditor($userid = null) {
		$user = JFactory::getUser($userid);

		if (in_array(18, $user->getAuthorisedGroups()))
			return true;
		else
			return false;
	}

	public static function is_clubofficial($userid, $club = 0) {
		if ($userid == 0)
			return false;
		
		if (!isset(self::$clubOfficialClubs[$userid]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('club');
			$query->from('#__toes_club_official');
			$query->where('user = ' . $userid);
			$db->setQuery($query);
			
			self::$clubOfficialClubs[$userid] = $db->loadColumn();
		}

		if($club) {
			if (in_array($club, self::$clubOfficialClubs[$userid]))
				return true;
			else 
				return false;
		} else {
			if(self::$clubOfficialClubs[$userid])
				return true;
			else 
				return false;
		}
	}

	public static function is_clubowner($userid, $club) {
		if ($userid == 0)
			return false;
		
		if (!isset(self::$clubOwnerClubs[$userid]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('co.club');
			$query->from('#__toes_club_official AS co');
			$query->join('left','#__toes_club_official_type AS cot ON cot.club_official_type_id = co.club_official_type AND cot.club_official_type="Club Official"');
			$query->where('co.user = ' . $userid);
			$db->setQuery($query);
			
			self::$clubOwnerClubs[$userid] = $db->loadColumn();
		}
		
		if($club) {
			if (in_array($club, self::$clubOwnerClubs[$userid]))
				return true;
			else 
				return false;
		} else {
			if(self::$clubOwnerClubs[$userid])
				return true;
			else 
				return false;
		}
	}

	public static function is_regionaldirector($userid, $region = 0) {
		if ($userid == 0)
			return false;
		
		if(!isset(self::$regionalDirectorRegion[$userid])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
	
			$query->select('cr.competitive_region_id');
			$query->from('#__toes_organization_has_official AS o');
			$query->join('left', '#__toes_organization_official_type AS t ON t.organization_official_type_id = o.organization_official_type');

			$query->join('left', '#__toes_competitive_region AS cr ON cr.competitive_region_regional_director = o.user');
			//$query->where('cr.competitive_region_id = ' . $region);
	
			$query->where('o.user = ' . $userid);
			$query->where('t.organization_official_type = "Regional Director"');
			
			$db->setQuery($query);
			self::$regionalDirectorRegion[$userid] = $db->loadColumn();
			if(self::$regionalDirectorRegion[$userid] === null){
				self::$regionalDirectorRegion[$userid] = array();
			}
		}
	
		if($region) {
			if (in_array($region, self::$regionalDirectorRegion[$userid]))
				return true;
			else 
				return false;
		} else {
			if(self::$regionalDirectorRegion[$userid])
				return true;
			else 
				return false;
		}
	}

	public static function is_showofficial($userid, $show = 0) {
		if ($userid == 0)
			return false;

		if (!isset(self::$showOfficialShows[$userid]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('`show`');
			$query->from('#__toes_show_has_official');
			$query->where('user = ' . $userid);
	
			$db->setQuery($query);
			self::$showOfficialShows[$userid] = $db->loadColumn();
		}

		if($show) {
			if (in_array($show, self::$showOfficialShows[$userid]))
				return true;
			else 
				return false;
		} else {
			if(self::$showOfficialShows[$userid])
				return true;
			else 
				return false;
		}
	}

	public static function is_entryclerk($userid, $show = 0) {
		if ($userid == 0)
			return false;

		if (!isset(self::$entryClerkShows[$userid]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
	
			$query = "SELECT a.show FROM #__toes_show_has_official as a 
	                LEFT JOIN #__toes_show_official_type as b ON a.show_official_type = b.show_official_type_id 
	                WHERE b.show_official_type = " . $db->quote('Entry Clerk') . " 
	                AND a.user = " . $userid;
	
			$db->setQuery($query);
			self::$entryClerkShows[$userid] = $db->loadColumn();
		}

		if($show) {
			if (in_array($show, self::$entryClerkShows[$userid]))
				return true;
			else 
				return false;
		} else {
			if(self::$entryClerkShows[$userid])
				return true;
			else 
				return false;
		}
	}

	public static function is_masterclerk($userid, $show = 0) {
		if ($userid == 0)
			return false;

		if (!isset(self::$masterClerkShows[$userid]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
	
			$query = "SELECT a.show FROM #__toes_show_has_official as a 
	                LEFT JOIN #__toes_show_official_type as b ON a.show_official_type = b.show_official_type_id 
	                WHERE b.show_official_type = " . $db->quote('Master Clerk') . " 
	                AND a.user = " . $userid;
	
			$db->setQuery($query);
			self::$masterClerkShows[$userid]  = $db->loadColumn();
		}
		
		if($show) {
			if (in_array($show, self::$masterClerkShows[$userid]))
				return true;
			else 
				return false;
		} else {
			if(self::$masterClerkShows[$userid])
				return true;
			else 
				return false;
		}
	}

	public static function is_showmanager($userid, $show = 0) {
		if ($userid == 0)
			return false;

		if (!isset(self::$showManagerShows[$userid]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
	
			$query = "SELECT a.show FROM #__toes_show_has_official as a 
	                LEFT JOIN #__toes_show_official_type as b ON a.show_official_type = b.show_official_type_id 
	                WHERE b.show_official_type = " . $db->quote('Show Manager') . " 
	                AND a.user = " . $userid;
	
			$db->setQuery($query);
			self::$showManagerShows[$userid] = $db->loadColumn();
		}
		
		if($show) {
			if (in_array($show, self::$showManagerShows[$userid]))
				return true;
			else 
				return false;
		} else {
			if(self::$showManagerShows[$userid])
				return true;
			else 
				return false;
		}
	}

	public static function is_organizationofficial($userid) {
		if ($userid == 0)
			return false;

		if (!isset(self::$orgOfficial[$userid]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
	
			$query->select('organization_has_official_id')->from('#__toes_organization_has_official')->where('user = ' . $userid);
	
			$db->setQuery($query);
			self::$orgOfficial[$userid] = $db->loadResult();
		}

		if (self::$orgOfficial[$userid])
			return true;
		else
			return false;
	}

	public static function getUserCats($userid, $type = null, $skip_ids = null) {
		/*
		if (count($skip_ids))
			$query .=" AND vc.cat_id NOT IN(" . implode(',', $skip_ids) . ")";
		*/

		if(!isset(self::$userCats[$userid])) {
			$db = JFactory::getDbo();

			$query = TOESQueryHelper::getCatViewQuery();
			$query->select("date_format(c.cat_date_of_birth,'%m/%d/%y') as da, cuct.cat_user_connection_type");
			
			$query->join("left", "#__toes_cat_relates_to_user AS crtu ON crtu.of_cat = c.cat_id");
			$query->join("left", "#__toes_cat_user_connection_type AS cuct ON cuct.cat_user_connection_type_id = crtu.cat_user_connection_type");
			
			$query->where("crtu.person_is = " . $userid);
	 
	 		//echo str_replace('#_', 'j35', nl2br($query));die;
			$db->setQuery($query);
			$catslist = $db->loadObjectList();
	        
	        foreach($catslist as $item) {
	        	self::$userCats[$userid][$item->cat_user_connection_type][] = $item;
				unset($item->cat_user_connection_type);
	        }        
	        
			//Remove Owner and lessse cates from Breeders
			if(isset(self::$userCats[$userid]['Breeder']) && isset(self::$userCats[$userid]['Owner'])) {
				if(self::$userCats[$userid]['Breeder']){
					foreach (self::$userCats[$userid]['Breeder'] as $key => $item) {
						if(in_array($item, self::$userCats[$userid]['Owner'])) {
							unset(self::$userCats[$userid]['Breeder'][$key]);
						}
					}
				}
			}
			
			if(isset(self::$userCats[$userid]['Breeder']) && isset(self::$userCats[$userid]['Lessee'])) {
				if(self::$userCats[$userid]['Breeder']){
					foreach (self::$userCats[$userid]['Breeder'] as $key => $item) {
						if(in_array($item, self::$userCats[$userid]['Lessee'])) {
							unset(self::$userCats[$userid]['Breeder'][$key]);
						}
					}
				}
			}

			//Remove Owner cates from Lessee
			if(isset(self::$userCats[$userid]['Lessee']) && isset(self::$userCats[$userid]['Owner'])) {
				if(self::$userCats[$userid]['Lessee']){
					foreach (self::$userCats[$userid]['Lessee'] as $key => $item) {
						if(in_array($item, self::$userCats[$userid]['Owner'])) {
							unset(self::$userCats[$userid]['Lessee'][$key]);
						}
					}
				}
			}
			
			//Remove Owner and lessse cates from Agent
			if(isset(self::$userCats[$userid]['Agent']) && isset(self::$userCats[$userid]['Owner'])) {
				if(self::$userCats[$userid]['Agent']){
					foreach (self::$userCats[$userid]['Agent'] as $key => $item) {
						if(in_array($item, self::$userCats[$userid]['Owner'])) {
							unset(self::$userCats[$userid]['Agent'][$key]);
						}
					}
				}
			}
			
			if(isset(self::$userCats[$userid]['Agent']) && isset(self::$userCats[$userid]['Lessee'])) {
				if(self::$userCats[$userid]['Agent']){
					foreach (self::$userCats[$userid]['Agent'] as $key => $item) {
						if(in_array($item, self::$userCats[$userid]['Lessee'])) {
							unset(self::$userCats[$userid]['Agent'][$key]);
						}
					}
				}
			}
			
		}
	
		if($type && isset(self::$userCats[$userid][$type])) {
			return self::$userCats[$userid][$type];
		} else if($type == null && isset(self::$userCats[$userid])) {
			return self::$userCats[$userid];
		} else {
			return array();
		}
	}

	public static function getCatImages($cat) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query = "SELECT `cat_img_id`, `file_name`, `cat_id`
				FROM `#__toes_cat_images`
				WHERE `cat_id` = ".$cat;	 

		$db->setQuery($query);
		$images = $db->loadObjectList();
		if ($images)
			return $images;
		else
			return false;
		
	}

	public static function getCatUsers($cat, $type = null) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($type != null && $cat) {
			if ($type == 'Owner') {
				$query = "SELECT crtu.person_is as value ,user.username as text FROM #__toes_cat_relates_to_user AS crtu 
                        LEFT JOIN #__users as user on user.id = crtu.person_is	WHERE (crtu.of_cat =  " . $cat . ") 
                        AND (crtu.cat_user_connection_type IN 
                        (SELECT cuct.cat_user_connection_type_id FROM #__toes_cat_user_connection_type AS cuct 
                                WHERE cuct.cat_user_connection_type  = 'Owner')
                        ) ";
			} else if ($type == 'Breeder') {
				$query = "SELECT crtu.person_is as value ,user.username as text FROM #__toes_cat_relates_to_user AS crtu 
                        LEFT JOIN #__users as user on user.id = crtu.person_is	WHERE (crtu.of_cat =  " . $cat . ") 
                        AND (crtu.cat_user_connection_type IN 
                        (SELECT cuct.cat_user_connection_type_id FROM #__toes_cat_user_connection_type AS cuct 
                                WHERE cuct.cat_user_connection_type  = 'Breeder')
                        )  ";
			} else if ($type == 'Other') {
				$query = "SELECT crtu.person_is as value ,user.username as text,cuct.cat_user_connection_type as relation FROM #__toes_cat_relates_to_user AS crtu 
                        LEFT JOIN #__users as user on user.id = crtu.person_is
                        LEFT JOIN #__toes_cat_user_connection_type as cuct on cuct.cat_user_connection_type_id = crtu.cat_user_connection_type
                        WHERE (crtu.of_cat = " . $cat . ") 
                        AND NOT (crtu.cat_user_connection_type IN 
                        (SELECT cuct.cat_user_connection_type_id FROM #__toes_cat_user_connection_type AS cuct 
                                WHERE (cuct.cat_user_connection_type = 'Owner') OR (cuct.cat_user_connection_type = 'Breeder')
                                )
                        )  ";
			}
			else
				return false;
		}
		else {
			$query = "SELECT crtu.person_is as value, user.username as text FROM #__toes_cat_relates_to_user AS crtu 
                    LEFT JOIN #__users as user on user.id = crtu.person_is	WHERE (crtu.of_cat =  " . $cat . ") 
                    AND (crtu.cat_user_connection_type IN 
                    (SELECT cuct.cat_user_connection_type_id FROM #__toes_cat_user_connection_type AS cuct 
                            WHERE cuct.cat_user_connection_type  = 'Owner')
                    ) ";
		}

		$db->setQuery($query);
		$options = $db->loadObjectList();
		if ($options)
			return $options;
		else
			return false;
	}
	
	public static function getShowCats($show_id) {			
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getCatViewQuery();
		
		$query->join("left","#__toes_entry as e ON e.cat = c.cat_id");
		$query->where('`e`.`entry_show` = '.$show_id);
		
		$db->setQuery($query);
		self::$catDetails = $db->loadObjectList('cat_id');
		
		return self::$catDetails;
	} 

	public static function getCatDetails($cat_id) {
		if(!isset(self::$catDetails[$cat_id])){		
			$db = JFactory::getDbo();
			
			$whr = array();
			$whr[] = "c.cat_id = " . (int) $cat_id;
			
			$query = TOESQueryHelper::getCatViewQuery($whr);
	
			//echo str_replace('#_', 'j35', nl2br($query));
			$db->setQuery($query);
			self::$catDetails[$cat_id] = $db->loadObject();
		}
		
		return self::$catDetails[$cat_id];
	}
	
	public static function getUserCatRelations($userid, $type = null) {
		if(!isset(self::$userCatRelations[$userid])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
	
			$query = "SELECT vc.cat_id, cuct.cat_user_connection_type
	                FROM #__toes_cat AS vc 
	                LEFT JOIN #__toes_cat_relates_to_user AS crtu ON crtu.of_cat =  vc.cat_id
	                LEFT JOIN #__toes_cat_user_connection_type AS cuct ON cuct.cat_user_connection_type_id = crtu.cat_user_connection_type
	                WHERE crtu.person_is = " . $userid;
	 
	 		//echo str_replace('#_', 'j35', nl2br($query));
			$db->setQuery($query);
			$catslist = $db->loadObjectList();
	        
	        foreach($catslist as $item) {
	        	self::$userCatRelations[$userid][$item->cat_user_connection_type][] = $item->cat_id;
	        }        
	        
			if(isset(self::$userCatRelations[$userid]['Breeder']) && isset(self::$userCatRelations[$userid]['Owner'])) {
				self::$userCatRelations[$userid]['Breeder'] = array_diff(self::$userCatRelations[$userid]['Breeder'], self::$userCatRelations[$userid]['Owner']);
			}
		}
	
		if($type && isset(self::$userCatRelations[$userid][$type])) {
			return self::$userCatRelations[$userid][$type];
		} else if($type == null && isset(self::$userCatRelations[$userid])) {
			return self::$userCatRelations[$userid];
		} else {
			return array();
		}
	}

	public static function getCatUserRelations($cat, $type = null) {
		if(!$cat)
			return false; 
			
		if(!isset(self::$catUserRelations[$cat]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT crtu.person_is, user.username, cuct.cat_user_connection_type  
				FROM #__toes_cat_relates_to_user AS crtu 
	            LEFT JOIN #__toes_cat_user_connection_type AS cuct ON crtu.cat_user_connection_type = cuct.cat_user_connection_type_id
	            LEFT JOIN #__users as user on user.id = crtu.person_is 
	            WHERE (crtu.of_cat = " . $cat . ")
	            ";
	
			$db->setQuery($query);
			$user_relations = $db->loadObjectList();
			
			$result = array();
			
			if($user_relations) {
				foreach($user_relations as $relation){
					$result['userids'][] = $relation->person_is;
					$result[$relation->cat_user_connection_type][] = $relation; 
					//unset($relation->cat_user_connection_type);
				}
				$result['userids'] = array_unique($result['userids']);
			}
			self::$catUserRelations[$cat] = $result;
		}
		
		if($type == 'Other') {
			$cats = array();
			if(isset(self::$catUserRelations[$cat]['Lessee']) || isset(self::$catUserRelations[$cat]['Agent'])) {
				if(!isset(self::$catUserRelations[$cat]['Lessee'])) {
					$cats = self::$catUserRelations[$cat]['Agent'];
				} else if(!isset(self::$catUserRelations[$cat]['Agent'])) {
					$cats = self::$catUserRelations[$cat]['Lessee'];
				} else {
					$cats = array_merge(self::$catUserRelations[$cat]['Lessee'], self::$catUserRelations[$cat]['Agent']);
					$cats = array_unique($cats, SORT_REGULAR);
				}
			}
			return $cats;
		}

		if($type && isset(self::$catUserRelations[$cat][$type])) {
			return self::$catUserRelations[$cat][$type];
		} else if($type == null && isset(self::$catUserRelations[$cat])) {
			return self::$catUserRelations[$cat];
		} else {
			return array();
		}
	}

	public static function getCatCatRelations($cat, $type = null) {
		if(!$cat)
			return false; 
			
		if(!isset(self::$catCatRelations[$cat]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
       	 	$query = "SELECT c.cat_1_is, t.cat_to_cat_connection_type
                FROM #__toes_cat_relates_to_cat AS c
                LEFT JOIN #__toes_cat_cat_connection_type AS t ON t.cat_cat_connection_type_id = c.cat_cat_connection_type 
                WHERE c.of_cat_2 = " . $cat ;
	
			$db->setQuery($query);
			$cat_relations = $db->loadObjectList();
			
			$result = array();
			
			if($cat_relations) {
				foreach($cat_relations as $relation){
					$result[$relation->cat_to_cat_connection_type] = $relation->cat_1_is; 
				}
			}
			self::$catCatRelations[$cat] = $result;
		}

		if($type && isset(self::$catCatRelations[$cat][$type])) {
			return self::$catCatRelations[$cat][$type];
		} else if($type == null && isset(self::$catCatRelations[$cat])) {
			return self::$catUserRelations[$cat];
		} else {
			return null;
		}
	}
	
	public static function is_catowner($userid, $cat) {
		self::getUserCatRelations($userid);
		$catusertypes = self::$userCatRelations[$userid];

		if (isset($catusertypes['Owner']) && in_array($cat, $catusertypes['Owner'])) {
			return true;
		} else {
			return false;
		}
	}

	public static function is_catbreeder($userid, $cat) {
		$catusertypes = self::getUserCatRelations($userid);

		if (isset($catusertypes['Breeder']) && in_array($cat, $catusertypes['Breeder'])) {
			return true;
		} else {
			return false;
		}
	}

	public static function is_catlessee($userid, $cat) {
		self::getUserCatRelations($userid);
		$catusertypes = self::$userCatRelations[$userid];

		if (isset($catusertypes['Lessee']) && in_array($cat, $catusertypes['Lessee'])) {
			return true;
		} else {
			return false;
		}
	}

	public static function is_catagent($userid, $cat) {
		self::getUserCatRelations($userid);
		$catusertypes = self::$userCatRelations[$userid];

		if (isset($catusertypes['Agent']) && in_array($cat, $catusertypes['Agent'])) {
			return true;
		} else {
			return false;
		}
	}

	public static function is_onlyuser($userid, $cat) {
		$catusertypes = self::getCatUserRelations($cat);
				
		if(count($catusertypes['userids']) == 1 && in_array($userid, $catusertypes['userids'])) {
			return true;
		} else {
			return false;
		}
	}
	
	public static function getClub($show) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('c.*');
		$query->from('#__toes_club_organizes_show AS cs');
		$query->join('left', '#__toes_club AS c ON c.club_id = cs.club');
		$query->where('cs.show = ' . $show);
		$db->setQuery($query);
		$club = $db->loadObject();

		return $club;
	}

	public static function getClubDetails($club_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_club');
		$query->where('club_id = ' . $club_id);
		$db->setQuery($query);
		$club = $db->loadObject();

		return $club;
	}

	public static function getOfficials($type = null, $userid = 0) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($type != null) {
			if ($type == 'Organization Officials') {

				$query = "SELECT *, user.id as user_id, toot.organization_official_type as roll,
                            toot.organization_official_type_id as roll_id,
                            toho.organization_id as official_id, user.username as uname, 
                            cr.competitive_region_abbreviation, cr.competitive_region_confirmation_by_rd_needed
                        FROM #__toes_organization_has_official as toho 
                        LEFT JOIN #__users AS user ON user.id = toho.user
                        LEFT JOIN #__toes_organization_official_type as toot ON toho.organization_official_type  = toot.organization_official_type_id
                        LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                        LEFT JOIN #__toes_competitive_region AS cr ON cr.competitive_region_regional_director = toho.user 
                        ";
			} else if ($type == 'Club Officials') {
				$query = "SELECT *, user.id as user_id, tcot.club_official_type as roll, 
                            tcot.club_official_type_id as roll_id, tco.club as official_id, 
                            c.club_name as club, user.username as uname 
                        FROM #__users AS user 
                        INNER JOIN #__toes_club_official as tco ON user.id = tco.user
                        LEFT JOIN #__toes_club_official_type as tcot ON tco.club_official_type = tcot.club_official_type_id
                        LEFT JOIN #__toes_club as c ON tco.club  = c.club_id
                        LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                        ";
			} else if ($type == 'Show Officials') {
				$query = "SELECT user.id as user_id, user.username as uname, tsho.show as official_id,
						tsot.show_official_type as roll, tsot.show_official_type_id as roll_id,  
						ts.show_id, ts.show_start_date, ts.show_end_date, 
						tv.venue_name, 
		                c.club_name as club, c.club_id, cb.firstname, cb.lastname
		                
                        FROM #__users AS user 
                        INNER JOIN #__toes_show_has_official as tsho ON user.id = tsho.user
                        LEFT JOIN #__toes_show_official_type as tsot ON tsho.show_official_type = tsot.show_official_type_id
                       
                        LEFT JOIN #__toes_club_organizes_show as tcos ON tsho.show  = tcos.show
                        LEFT JOIN #__toes_club as c ON tcos.club  = c.club_id
                        
                        LEFT JOIN #__toes_show as ts ON tsho.show = ts.show_id 	
                        LEFT JOIN #__toes_venue as tv ON ts.show_venue = tv.venue_id
                        LEFT JOIN #__toes_address as ta ON tv.venue_address = ta.address_id
		                
		                                                
                        LEFT JOIN #__comprofiler as cb ON user.id  = cb.user_id
                        ";
                        //LEFT JOIN #__toes_country as cntry ON cntry.id = ta.address_country
		                //LEFT JOIN #__toes_states_per_country as state ON state.id = ta.address_state
		                //LEFT JOIN #__toes_cities_per_state as city ON city.id = ta.address_city
				if (!self::isAdmin()) {
					if ($userid) {
						if (self::is_clubofficial($userid)) {
							$query .= "LEFT JOIN #__toes_club_official as tco ON c.club_id = tco.club
                                        WHERE tco.user = " . $userid;
						} else if (self::is_showmanager($userid)) {
							$query .= "WHERE ts.show_id = (SELECT a.`show` FROM `#__toes_show_has_official` AS a
                                                        LEFT JOIN `#__toes_show_official_type` AS b ON b.`show_official_type_id` = a.`show_official_type`
                                                        WHERE a.`user` = " . $userid . " AND b.`show_official_type` = 'Show Manager')";
						}
					}
				}
			}
			else
				return false;

			//echo '<br/><br/>';
			//echo str_replace('#__', 'j35_', nl2br($query));

			$db->setQuery($query);
			$getOfficials = $db->loadObjectlist();

			return $getOfficials;
		}
		else
			return false;
	}

	public static function getShows() {
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getShowViewQuery();
		$db->setQuery($query);
		return $db->loadObjectlist();
	}

	public static function getShowDetails($show_id) {
		
		if(!$show_id) {
			return false;
		}
		
		if(!isset(self::$showDetails[$show_id])){
			$db = JFactory::getDbo();
			
			$whr = array();
			$whr[] = "`s`.`show_id` = " . $show_id;
			$query = TOESQueryHelper::getShowViewQuery($whr);
			
			$db->setQuery($query);
			self::$showDetails[$show_id] = $db->loadObject();
		}

		return self::$showDetails[$show_id];
	}

	public static function getRegions() {
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__toes_competitive_region ";
		$db->setQuery($query);
		$regions = $db->loadObjectlist();

		if ($regions)
			return $regions;
		else
			return false;
	}

	public static function getRegionDetails($region_id) {
		
		if(!isset(self::$competativeRegions[$region_id])) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('`#__toes_competitive_region`');
			$query->where('`competitive_region_id` = ' . $region_id);
			$db->setQuery($query);

			self::$competativeRegions[$region_id] = $db->loadObject();
		}

		return self::$competativeRegions[$region_id];
	}

	public static function getshowofficialuser($show_id = null, $official_type = null) {
		if (!$show_id)
			return false;

		if (!$official_type)
			return false;

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query = "SELECT user.username as showofficial FROM #__toes_show_has_official AS sho 
                LEFT JOIN #__users as user on user.id = sho.user WHERE sho.show = " . $show_id . "
                AND sho.show_official_type =" . $official_type;

		//echo $query;die;

		$db->setQuery($query);
		$options = $db->loadObjectList();
		return $options;
	}

	public static function getJudgeInfo($judge_id) {
		$db = JFactory::getDbo();
		
		if(!isset(self::$judges[$judge_id])) {
			$query = TOESQueryHelper::getJudgesViewQuery();
			$db->setQuery($query);
			self::$judges = $db->loadObjectList('judge_id');
		}

		if(isset(self::$judges[$judge_id])) {
			return self::$judges[$judge_id];
		} else {
			return false;
		}
	}
	
	public static function judge_rings($show_id, $judge_id){
		if(is_numeric($judge_id)){
		$db = JFactory::getDbo();
		$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT `r`.`ring_id`, r.ring_format, r.ring_number, r.ring_name, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, 
				`r`.`ring_judge`, `j`.`judge_abbreviation`, sd.show_day_date, COUNT( `e`.`entry_id` ) AS `count`
				FROM `#__toes_ring` AS `r`
				LEFT JOIN `#__toes_judge` AS `j` ON  `j`.`judge_id` = `r`.`ring_judge` 
				LEFT JOIN `#__toes_entry` AS `e` ON  `e`.`show_day` = `r`.`ring_show_day` 
				LEFT JOIN `#__toes_entry_status` AS `es` ON (`e`.`status` = `es`.`entry_status_id`)
				LEFT JOIN `#__toes_show_day` AS `sd` ON `sd`.`show_day_id` = `r`.`ring_show_day`
				WHERE `r`.`ring_show` = $show_id AND 
				(
					CASE `r`.`ring_timing`
						WHEN 1 THEN `e`.`entry_participates_AM` = 1
						WHEN 2 THEN `e`.`entry_participates_PM` = 1
						ELSE (`e`.`entry_participates_AM` = 0 AND `e`.`entry_participates_PM` = 0)
					END
				)
				AND
				(
					(
				  		(`r`.`ring_format` =1) OR (`r`.`ring_format` =2)
				 	) 
				  	OR 
				 	(
				  		(`r`.`ring_format` = 3) AND (`e`.`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` WHERE `congress_id`= `r`.`ring_id` ))
				 	)
				)
				AND `e`.`entry_show_class` <> '17'
				AND `r`.`ring_judge` =".$judge_id."
				AND ( (`es`.`entry_status` = 'Accepted') OR(`es`.`entry_status` = 'Confirmed') OR (`es`.`entry_status` = 'Confirmed & Paid') )
				GROUP BY `r`.`ring_show` , `r`.`ring_show_day`, `r`.`ring_timing` , `r`.`ring_id`, `r`.`ring_judge`
				ORDER BY  `r`.`ring_show` , `r`.`ring_judge`, `r`.`ring_show_day`, `r`.`ring_timing`";
			$db->setQuery($query);			 	 
			return $db->loadObjectList();
		}else
		return [];		
	}

	public static function getJudgeRings($show_id, $judge_id = 0) {
		
		$db = JFactory::getDbo();
/*
		$query = "SELECT `r`.`ring_id`, r.ring_format, r.ring_number, r.ring_name, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, 
			`r`.`ring_judge`, `j`.`judge_abbreviation`, sd.show_day_date, COUNT( `e`.`entry_id` ) AS `count`
			FROM `#__toes_ring` AS `r`
			LEFT JOIN `#__toes_judge` AS `j` ON  `j`.`judge_id` = `r`.`ring_judge` 
			LEFT JOIN `#__toes_entry` AS `e` ON  `e`.`show_day` = `r`.`ring_show_day` 
			LEFT JOIN `#__toes_entry_status` AS `es` ON (`e`.`status` = `es`.`entry_status_id`)
			LEFT JOIN `#__toes_show_day` AS `sd` ON `sd`.`show_day_id` = `r`.`ring_show_day`
			WHERE `r`.`ring_show` = $show_id AND 
			(
			 (
			  (`r`.`ring_format` =1) OR (`r`.`ring_format` =2)
			 ) 
			  OR 
			 (
			  (`r`.`ring_format` = 3) AND (`e`.`entry_id` IN (SELECT `entry_id` FROM `j35_toes_entry_participates_in_congress` WHERE `congress_id`= `r`.`ring_id` ))
			 )
			)
			AND `e`.`entry_show_class` <> '17'
			AND ( (`es`.`entry_status` = 'Accepted') OR(`es`.`entry_status` = 'Confirmed') OR (`es`.`entry_status` = 'Confirmed & Paid') )
			AND `r`.`ring_judge` = '$judge_id'
			GROUP BY `r`.`ring_show` , `r`.`ring_show_day`, `r`.`ring_timing` , `r`.`ring_id`, `r`.`ring_judge`
			ORDER BY  `r`.`ring_show` , `r`.`ring_judge`, `r`.`ring_show_day`, `r`.`ring_timing`";
		/*
		$query = "SELECT cnt.*, sd.show_day_date, r.ring_format, r.ring_number, r.ring_name
			FROM #__toes_view_count_per_ring AS cnt
			LEFT JOIN #__toes_ring AS r ON r.ring_id = cnt.ring_id
			LEFT JOIN #__toes_show_day AS sd ON sd.show_day_id = cnt.ring_show_day
			WHERE cnt.ring_show = $show_id 
			AND cnt.ring_judge = '$judge_id' ";
		*/
/*
		$db->setQuery($query);
		$rings = $db->loadObjectList();

		return $rings;
*/		
		if(!$show_id)
			return false; 
		self::$judgesRings = '';	
		if(!isset(self::$judgesRings[$show_id]))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query = "SELECT `r`.`ring_id`, r.ring_format, r.ring_number, r.ring_name, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, 
				`r`.`ring_judge`, `j`.`judge_abbreviation`, sd.show_day_date, COUNT( `e`.`entry_id` ) AS `count`
				FROM `#__toes_ring` AS `r`
				LEFT JOIN `#__toes_judge` AS `j` ON  `j`.`judge_id` = `r`.`ring_judge` 
				LEFT JOIN `#__toes_entry` AS `e` ON  `e`.`show_day` = `r`.`ring_show_day` 
				LEFT JOIN `#__toes_entry_status` AS `es` ON (`e`.`status` = `es`.`entry_status_id`)
				LEFT JOIN `#__toes_show_day` AS `sd` ON `sd`.`show_day_id` = `r`.`ring_show_day`
				WHERE `r`.`ring_show` = $show_id AND 
				(
					CASE `r`.`ring_timing`
						WHEN 1 THEN `e`.`entry_participates_AM` = 1
						WHEN 2 THEN `e`.`entry_participates_PM` = 1
						ELSE (`e`.`entry_participates_AM` = 0 AND `e`.`entry_participates_PM` = 0)
					END
				)
				AND
				(
					(
				  		(`r`.`ring_format` =1) OR (`r`.`ring_format` =2)
				 	) 
				  	OR 
				 	(
				  		(`r`.`ring_format` = 3) AND (`e`.`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` WHERE `congress_id`= `r`.`ring_id` ))
				 	)
				)
				AND `e`.`entry_show_class` <> '17'
				AND ( (`es`.`entry_status` = 'Accepted') OR(`es`.`entry_status` = 'Confirmed') OR (`es`.`entry_status` = 'Confirmed & Paid') )
				GROUP BY `r`.`ring_show` , `r`.`ring_show_day`, `r`.`ring_timing` , `r`.`ring_id`, `r`.`ring_judge`
				ORDER BY  `r`.`ring_show` , `r`.`ring_judge`, `r`.`ring_show_day`, `r`.`ring_timing`";
			$db->setQuery($query);
			$fp =fopen(JPATH_BASE.'/judge_rings_fetch.txt','w+');
			fwrite($fp,"query\n".$query."\n");			 
			$judge_rings = $db->loadObjectList();
			ob_start();		
			var_dump($judge_rings);
			$str = ob_get_clean();
			fwrite($fp,"This is judge ring dump\n".$str."\n");
			fclose($fp);
			
			$result = array();
			
			if($judge_rings) {
				foreach($judge_rings as $judge_ring){
					$result[$judge_ring->ring_judge][] = $judge_ring; 
				}
			}
			self::$judgesRings[$show_id] = $result;
			 
		}

		if($judge_id && isset(self::$judgesRings[$show_id][$judge_id])) {
			return self::$judgesRings[$show_id][$judge_id];
		} else if($judge_id == null && isset(self::$judgesRings[$show_id])) {
			return self::$judgesRings[$show_id];
		} else {
			return null;
		}
	}

	public static function getUserInfo($userid) {
		
		if(!isset(self::$userDetails[$userid])) {
			$db = JFactory::getDbo();
			$query = "SELECT u.id, u.name, u.username, u.email, cb.firstname, cb.lastname, cb.avatar, 
	            cb.cb_address1, cb.cb_address2, cb.cb_address3, cb.cb_city, cb.cb_zip, cb.cb_state, cb.cb_country, cb.cb_phonenumber, cb.cb_tica_region
	            FROM #__users AS u
	            LEFT JOIN #__comprofiler as cb on u.id = cb.user_id	
	           ";
	
			$db->setQuery($query);
			self::$userDetails = $db->loadObjectList('id');
		}
		
		return self::$userDetails[$userid];
	}

	public static function getEntryDetails($entry_id) {
		$db = JFactory::getDbo();

		$whr = array();
		$whr[] = "`e`.`entry_id` = {$entry_id}";
		
		$query = TOESQueryHelper::getEntryViewQuery($whr);
		$db->setQuery($query);
		$detail = $db->loadObject();

		return $detail;
	}

	public static function getEntryFullDetail($entry_id) {
		$db = JFactory::getDbo();

		$whr = array();
		$whr[] = "`e`.`entry_id` = {$entry_id}";
		
		$query = TOESQueryHelper::getEntryFullViewQuery($whr);
		$db->setQuery($query);
		$entries = $db->loadObject();

		return $entries;
	}

	public static function getEntryFullDetails($cat_id, $show_id) {
		$db = JFactory::getDbo();

		$whr = array();
		$whr[] = "`e`.`cat` = {$cat_id}";
		$whr[] = "`e`.`entry_show` = {$show_id}";
		
		$query = TOESQueryHelper::getEntryFullViewQuery($whr);
		$db->setQuery($query);
		$entries = $db->loadObjectList();

		return $entries;
	}

	public static function getClass($entry_id) {
		$entry = self::getEntryDetails($entry_id);

		if ($entry->breed_status == 'Championship' && $entry->copy_cat_hair_length == 1 && $entry->copy_cat_new_trait == 0 && $entry->is_kitten == 1 && $entry->is_adult == 0) {
			if ($entry->is_HHP)
				return 'HHP Kittens LH';
			else
				return 'Kittens LH';
		}
		else if ($entry->breed_status == 'Championship' && $entry->copy_cat_hair_length == 2 && $entry->copy_cat_new_trait == 0 && $entry->is_kitten == 1 && $entry->is_adult == 0) {
			if ($entry->is_HHP)
				return 'HHP Kittens SH';
			else
				return 'Kittens SH';
		}
		else if ($entry->breed_status == 'Championship' && $entry->copy_cat_hair_length == 1 && $entry->copy_cat_new_trait == 0 && $entry->is_kitten == 0 && $entry->is_adult == 1 && ( $entry->gender_short_name == 'M' || $entry->gender_short_name == 'F' )) {
			if ($entry->is_HHP)
				return 'HHP Adults LH';
			else
				return 'Adults LH';
		}
		else if ($entry->breed_status == 'Championship' && $entry->copy_cat_hair_length == 2 && $entry->copy_cat_new_trait == 0 && $entry->is_kitten == 0 && $entry->is_adult == 1 && ( $entry->gender_short_name == 'M' || $entry->gender_short_name == 'F' )) {
			if ($entry->is_HHP)
				return 'HHP Adults SH';
			else
				return 'Adults SH';
		}
		else if ($entry->breed_status == 'Championship' && $entry->copy_cat_hair_length == 1 && $entry->copy_cat_new_trait == 0 && $entry->is_kitten == 0 && $entry->is_adult == 1 && ( $entry->gender_short_name == 'N' || $entry->gender_short_name == 'S' )) {
			if ($entry->is_HHP)
				return 'HHP Alters LH';
			else
				return 'Alters LH';
		}
		else if ($entry->breed_status == 'Championship' && $entry->copy_cat_hair_length == 2 && $entry->copy_cat_new_trait == 0 && $entry->is_kitten == 0 && $entry->is_adult == 1 && ( $entry->gender_short_name == 'N' || $entry->gender_short_name == 'S' )) {
			if ($entry->is_HHP)
				return 'HHP Alters SH';
			else
				return 'Alters SH';
		}
		else if ($entry->breed_status == 'Championship' && $entry->is_HHP == 0 && $entry->copy_cat_hair_length == 1 && $entry->copy_cat_new_trait == 1)
			return 'New Traits LH';
		else if ($entry->breed_status == 'Championship' && $entry->is_HHP == 0 && $entry->copy_cat_hair_length == 2 && $entry->copy_cat_new_trait == 1)
			return 'New Traits SH';
		else if ($entry->breed_status == 'Preliminary New Breed' && $entry->copy_cat_hair_length == 1)
			return 'Preliminary New Breeds LH';
		else if ($entry->breed_status == 'Preliminary New Breed' && $entry->copy_cat_hair_length == 2)
			return 'Preliminary New Breeds LH';
		else if ($entry->breed_status == 'Advanced New Breed' && $entry->copy_cat_hair_length == 1)
			return 'Advanced New Breeds LH';
		else if ($entry->breed_status == 'Advanced New Breed' && $entry->copy_cat_hair_length == 2)
			return 'Advanced New Breeds SH';
		else
			return 'Other';
	}

	public static function getSummary($show_id, $user_id) {
		
		if(!isset(self::$showSummeries[$show_id][$user_id])) {
			$db = JFactory::getDbo();
	
			$query = $db->getQuery(true);
			$query->select('smry.summary_id, smry.summary_user, smry.summary_show, smry.summary_single_cages, smry.summary_double_cages, smry.summary_benching_request, smry.summary_grooming_space, smry.summary_personal_cages, smry.summary_remarks, smry.summary_total_fees, smry.summary_fees_paid, smry.summary_benching_area');
			$query->select('smry.summary_entry_clerk_note, smry.summary_entry_clerk_private_note');
			$query->from('#__toes_summary AS smry');
	
			$query->select('s.`summary_status`');
			$query->join('LEFT', '`#__toes_summary_status` AS s ON smry.`summary_status` = s.`summary_status_id`');
	
			$query->where('smry.summary_show =' . $show_id);
			//$query->where('smry.summary_user =' . $user_id);
	
			//echo nl2br(str_replace('#__', 'j35_', $query));
			$db->setQuery($query);
			self::$showSummeries[$show_id] = $db->loadObjectList('summary_user');
		}

		if(isset(self::$showSummeries[$show_id][$user_id])) {
			return self::$showSummeries[$show_id][$user_id];
		} else {
			return false;
		}
	}

	public static function getEntryClerks($show_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('so2.show AS show_id');
		$query->from('#__toes_show_has_official AS so2');
		//, cprofcntry.name AS entry_clerk_address_country
		$query->select(' u.name AS entry_clerk_name, u.email AS entry_clerk_email');
		$query->select(' cprof.cb_address1 AS entry_clerk_address_line_1, cprof.cb_address2 AS entry_clerk_address_line_2, cprof.cb_address3 AS entry_clerk_address_line_3');
		$query->select(' cprof.cb_zip AS entry_clerk_address_zip_code');
		$query->select(' cprof.cb_phonenumber AS entry_clerk_phone_number');
		$query->select(' `cprof`.`cb_country` AS entry_clerk_address_country');

		$query->join("left", "`#__users`  AS `u` ON `so2`.`user` = `u`.`id`");
		$query->join("left", "`#__comprofiler`  AS `cprof` ON `so2`.`user` = `cprof`.`user_id`");
		//$query->join("left", "`#__toes_country` AS `cprofcntry` ON `cprofcntry`.`id` = `cprof`.`cb_country`");//cprofcity.name AS entry_clerk_address_city, cprofstate.name AS entry_clerk_address_state,
		//$query->join("left", "`#__toes_states_per_country` AS `cprofstate` ON `cprofstate`.`id` = `cprof`.`cb_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `cprofcity` ON `cprofcity`.`id` = `cprof`.`cb_city`");

		$query->where('so2.show_official_type = 2');
		$query->where('so2.show =' . $show_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getShowManagers($show_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select(' smu.name AS show_manager_name, smu.email AS show_manager_email');
		$query->select('so1.show AS show_id');

		$query->from('#__toes_show_has_official AS so1');
		$query->join('LEFT', '#__users AS smu ON smu.id = so1.user');

		$query->where('so1.show_official_type = 1');
		$query->where('so1.show = ' . $show_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getShowRings($show_id) {
		$db = JFactory::getDbo();

		// $query = "SELECT r.*, sd.show_day_date as show_day, concat(concat(cb.firstname,' ',cb.lastname),' - ',tjl.judge_level) as ring_judge_name, tj.judge_abbreviation
        //     FROM #__toes_ring as r
        //     LEFT JOIN #__toes_show_day AS sd ON r.ring_show_day=sd.show_day_id
        //     LEFT JOIN #__toes_judge as tj ON r.ring_judge  = tj.judge_id
        //     LEFT JOIN #__comprofiler as cb ON tj.user = cb.user_id
        //     LEFT JOIN #__toes_judge_level AS tjl ON tjl.judge_level_id = tj.judge_level 
		// 	WHERE r.ring_show =" . $show_id. "
		// 	ORDER BY `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`";

		$query = $db->getQuery(true);
		$query->select('`r`.`ring_id`, `r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`');
		$query->select('`r`.`ring_format`, `r`.`ring_name`, `r`.`ring_judge`, `j`.`judge_abbreviation`');
		$query->select('`rf`.`ring_format` AS `format`, `sd`.`show_day_date`');
		
		$query->from('`#__toes_ring` AS `r`');
		$query->join('left','`#__toes_judge` AS `j` ON `j`.`judge_id` = `r`.`ring_judge`');
		$query->join('left','`#__toes_ring_format` AS `rf` ON `rf`.`ring_format_id` = `r`.`ring_format`');
		$query->join('left','`#__toes_show_day` AS `sd` ON `sd`.`show_day_id` = `r`.`ring_show_day`');
		
		$query->where('`r`.`ring_show` = '.$show_id);
		$query->order('`r`.`ring_show`, `r`.`ring_show_day`, `r`.`ring_timing`, `r`.`ring_number`');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getJudges($show_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('j.judge_id, ju.name, js.judge_status, jl.judge_level, rf.ring_format, r.ring_id, r.ring_number, r.ring_name, sd.show_day_id, sd.show_day_date');
		$query->from('#__toes_show AS s');
		$query->join('LEFT', '#__toes_ring AS r ON r.ring_show = s.show_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = r.ring_show_day');
		$query->join('LEFT', '#__toes_ring_format AS rf ON rf.ring_format_id = r.ring_format');
		$query->join('LEFT', '#__toes_judge AS j ON j.judge_id = r.ring_judge');
		$query->join('LEFT', '#__users AS ju ON ju.id = j.user');
		$query->join('LEFT', '#__toes_judge_status AS js ON js.judge_status_id = j.judge_status');
		$query->join('LEFT', '#__toes_judge_level AS jl ON jl.judge_level_id = j.judge_level');

		$query->where('s.show_id=' . $show_id);
		$query->where('rf.ring_format != "Congress"');
		//$query->group('j.judge_id');
		$query->order('r.ring_show_day ASC, r.ring_number ASC, ju.name ASC');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getCongressJudges($show_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('j.judge_id, ju.name, js.judge_status, jl.judge_level, rf.ring_format, r.ring_id, r.ring_number, r.ring_name, sd.show_day_id, sd.show_day_date');
		$query->from('#__toes_show AS s');
		$query->join('LEFT', '#__toes_ring AS r ON r.ring_show = s.show_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = r.ring_show_day');
		$query->join('LEFT', '#__toes_ring_format AS rf ON rf.ring_format_id = r.ring_format');
		$query->join('LEFT', '#__toes_judge AS j ON j.judge_id = r.ring_judge');
		$query->join('LEFT', '#__users AS ju ON ju.id = j.user');
		$query->join('LEFT', '#__toes_judge_status AS js ON js.judge_status_id = j.judge_status');
		$query->join('LEFT', '#__toes_judge_level AS jl ON jl.judge_level_id = j.judge_level');

		$query->where('s.show_id=' . $show_id);
		$query->where('rf.ring_format = "Congress"');
		//$query->group('j.judge_id');
		$query->order('r.ring_show_day ASC, ju.name ASC');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getPapersizeOptions() {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('paper_size_id AS value, paper_size AS text');
		$query->from('#__toes_paper_size');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$summary = $db->loadObjectList();

		return $summary;
	}

	public static function getShowEntries($show_id) {
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getEntryFullViewQuery();
		$query->select('e.entry_date_created AS timestamp, "entry" AS type, GROUP_CONCAT(DISTINCT(LEFT(DATE_FORMAT(e.show_day_date,"%W"),3))) AS showdays');

		$query->select('GROUP_CONCAT(DISTINCT(r.ring_name)) AS congress');
		$query->join('LEFT', '#__toes_entry_participates_in_congress AS pc ON pc.entry_id= e.entry_id');
		$query->join('LEFT', '#__toes_ring AS r ON r.ring_id= pc.congress_id');

		$query->where('`e`.`entry_show` = ' . $show_id);

		$query->group('`e`.`cat`, `Show_Class`, `e`.`status`');

		$query->order('`e`.`cat`');
		$query->order('`sd`.`show_day_date`, `e`.`entry_date_created`');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadObjectList();

		/* $temp_entries = array();
		  foreach($entries as $entry)
		  {
		  $temp_entries[$entry->cat.'_'.$entry->Show_Class][] = $entry;
		  } */

		return $entries;
	}

	public static function getEntries($user_id, $show_id, $entry_status = null) {
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getEntryFullViewQuery();
		$query->select('`e`.`entry_date_created` AS `timestamp`, "entry" AS `type`, 
			GROUP_CONCAT(
				DISTINCT 
					CONCAT(" ",LEFT(DATE_FORMAT(`sd`.`show_day_date`,"%W"),3), 
						IF(`sf`.`show_format` = "Alternative",
							CONCAT(" ",
								IF (`e`.`entry_participates_AM` = 1,"AM",""), 
								IF (`e`.`entry_participates_AM` = 1 AND `e`.`entry_participates_PM` = 1 ,"/",""),  
								IF (`e`.`entry_participates_PM` = 1,"PM","")
							),
							""
						)
					)  
				ORDER BY `sd`.`show_day_date`
			 ) AS `showdays`'
		);

		$query->select('GROUP_CONCAT(DISTINCT(`r`.`ring_name`)) AS congress');
		$query->join('LEFT', '`#__toes_entry_participates_in_congress` AS `pc` ON `pc`.`entry_id`= `e`.`entry_id`');
		$query->join('LEFT', '`#__toes_ring` AS `r` ON `r`.`ring_id` = `pc`.`congress_id`');

		$query->where('`e`.`entry_show`=' . $show_id);
		$query->where('`es`.`summary_user` = ' . $user_id);

		$query->group('`e`.`cat`, `Show_Class`, `e`.`status`');

		if(is_array($entry_status)) {
			$query->where('`estat`.`entry_status` IN (\'' . implode("', '",$entry_status).'\')');
		} elseif ($entry_status) {
			$query->where('`estat`.`entry_status` = ' . $db->quote($entry_status));
		}

		$query->order('`e`.`cat`');
		$query->order('`sd`.`show_day_date`, `e`.`entry_date_created`');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadObjectList();

		/* $temp_entries = array();
		  foreach($entries as $entry)
		  {
		  $temp_entries[$entry->cat.'_'.$entry->Show_Class][] = $entry;
		  } */

		return $entries;
	}

	public static function getShowPlaceholders($show_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('p.*, pd.*, pd.placeholder_day_date_created AS timestamp, "placeholder" AS type, 
			GROUP_CONCAT(
				DISTINCT CONCAT(" ", LEFT(DATE_FORMAT(sd.show_day_date,"%W"),3) , 
					IF(sf.show_format = "Alternative",
						CONCAT(" ",
							IF (pd.placeholder_participates_AM = 1,"AM",""), 
							IF (pd.placeholder_participates_AM = 1 AND pd.placeholder_participates_PM = 1 ,"/",""),  
							IF (pd.placeholder_participates_PM = 1,"PM","")
						),
						""
					)
				)
				ORDER BY sd.show_day_date 
			) AS showdays, es.entry_status');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_show AS s ON s.show_id = p.placeholder_show');
		$query->join('LEFT', '#__toes_show_format AS sf ON sf.show_format_id = s.show_format');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');

		$query->where('p.placeholder_show = ' . $show_id);

		$query->group('p.placeholder_id, pd.placeholder_day_placeholder_status');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholders = $db->loadObjectList();

		return $placeholders;
	}

	public static function getPlaceholders($user_id, $show_id, $entry_status = null) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('p.*, pd.*, pd.placeholder_day_date_created AS timestamp, "placeholder" AS type, 
			GROUP_CONCAT(
				DISTINCT CONCAT(" ", LEFT(DATE_FORMAT(sd.show_day_date,"%W"),3) , 
					IF(sf.show_format = "Alternative",
						CONCAT(" ",
							IF (pd.placeholder_participates_AM = 1,"AM",""), 
							IF (pd.placeholder_participates_AM = 1 AND pd.placeholder_participates_PM = 1 ,"/",""),  
							IF (pd.placeholder_participates_PM = 1,"PM","")
						),
						""
					)
				)
				ORDER BY sd.show_day_date 
			) AS showdays, es.entry_status');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_show AS s ON s.show_id = p.placeholder_show');
		$query->join('LEFT', '#__toes_show_format AS sf ON sf.show_format_id = s.show_format');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');

		$query->where('p.placeholder_exhibitor = ' . $user_id);
		$query->where('p.placeholder_show = ' . $show_id);

		$query->group('p.placeholder_id, pd.placeholder_day_placeholder_status');

		if(is_array($entry_status)) {
			$query->where('es.entry_status IN (\'' . implode("', '",$entry_status).'\')');
		} elseif ($entry_status)
			$query->where('es.entry_status = ' . $db->quote($entry_status));

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholders = $db->loadObjectList();

		return $placeholders;
	}

	public static function getPlaceholderFullDetails($placeholder_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('p.*, pd.*, sd.show_day_date, es.entry_status');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('p.placeholder_id = ' . $placeholder_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholders = $db->loadObjectList();

		return $placeholders;
		
	}

	public static function getPlaceholderDetails($placeholder_day_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('p.*, pd.*, sd.show_day_date, es.entry_status');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('pd.placeholder_day_id = ' . $placeholder_day_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholder = $db->loadObject();

		return $placeholder;
		
	}

	public static function getPlaceholderDetailsByShowday($placeholder_id, $show_day_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('p.*, pd.*, sd.show_day_date, es.entry_status');
		$query->from('#__toes_placeholder_day AS pd');
		$query->join('LEFT', '#__toes_placeholder AS p ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('p.placeholder_id = ' . $placeholder_id);
		$query->where('pd.placeholder_day_showday = ' . $show_day_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholder = $db->loadObject();

		return $placeholder;
	}

	public static function getAvailableSpaceforDay($show_day_id, $ring_timing = null, $entry_id = null, $placeholder_id = null) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('sd.*');
		$query->from('#__toes_show_day AS sd');
		$query->where('sd.show_day_id = ' . $show_day_id);

		//  echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$show_day = $db->loadObject();

		$query = TOESQueryHelper::getEntryFullViewQuery();
		
		$query->where('`estat`.`entry_status` IN ( "New", "Accepted", "Confirmed", "Confirmed & Paid")');
		$query->where('`sd`.`show_day_id` = ' . $show_day_id);

		$query->join('LEFT', '#__toes_entry_participates_in_congress AS pc ON pc.entry_id= e.entry_id');
		$query->join('LEFT', '#__toes_ring AS r ON r.ring_id= pc.congress_id');

		$query->where('( `sc`.`show_class` = "LH Kitten" OR `sc`.`show_class` = "SH Kitten" OR `sc`.`show_class` = "LH Cat" OR `sc`.`show_class` = "SH Cat"
                OR `sc`.`show_class` = "LH Alter" OR `sc`.`show_class` = "SH Alter" OR `sc`.`show_class` = "LH HHP Kitten" OR `sc`.`show_class` = "SH HHP Kitten"
                OR `sc`.`show_class` = "LH HHP" OR `sc`.`show_class` = "SH HHP" OR `sc`.`show_class` = "LH PNB" OR `sc`.`show_class` = "SH PNB"
                OR `sc`.`show_class` = "LH ANB" OR `sc`.`show_class` = "SH ANB" OR `sc`.`show_class` = "LH NT" OR `sc`.`show_class` = "SH NT" OR ( `sc`.`show_class` = "Ex Only" AND r.ring_name IS NOT NULL) )');
		
		$query->group('`e`.`cat`, `sc`.`show_class`, `e`.`status`');

		if ($entry_id)
			$query->where('`e`.`entry_id` != ' . $entry_id);

		if ($ring_timing == '1')
			$query->where('`e`.`entry_participates_AM` = 1');
		else if ($ring_timing == '2')
			$query->where('`e`.`entry_participates_PM` = 1');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$db->execute();
		$entries = $db->getNumRows();

		$query = $db->getQuery(true);
		$query->select('count(placeholder_id)');
		$query->from('#__toes_placeholder AS p');
		$query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('(es.entry_status = "New" OR es.entry_status = "Accepted" OR es.entry_status = "Confirmed" OR es.entry_status = "Confirmed & Paid")');
		$query->where('pd.placeholder_day_showday = ' . $show_day_id);
		$query->where('p.placeholder_show = ' . $show_day->show_day_show);

		if ($ring_timing == '1')
			$query->where('pd.placeholder_participates_AM = 1');
		else if ($ring_timing == '2')
			$query->where('pd.placeholder_participates_PM = 1');

		if ($placeholder_id)
			$query->where('p.placeholder_id != ' . $placeholder_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholders = $db->loadResult();
		
		//TODO: Need to change count to AM / PM for Alternative shows 
		$available_space = $show_day->show_day_cat_limit - ($entries + $placeholders);
		
		return ($available_space > 0) ? $available_space : 0;
	}

	public static function getAvailableSpaceforDayforEC($show_day_id, $ring_timing = null) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('sd.*');
		$query->from('#__toes_show_day AS sd');
		$query->where('sd.show_day_id = ' . $show_day_id);

		//  echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$show_day = $db->loadObject();

		$query = TOESQueryHelper::getEntryFullViewQuery();

		$query->where('`estat`.`entry_status` IN ( "Accepted", "Confirmed", "Confirmed & Paid")');
		$query->where('sd.show_day_id = ' . $show_day_id);

		$query->join('LEFT', '#__toes_entry_participates_in_congress AS pc ON pc.entry_id= e.entry_id');
		$query->join('LEFT', '#__toes_ring AS r ON r.ring_id= pc.congress_id');

		$query->where('( `sc`.`show_class` = "LH Kitten" OR `sc`.`show_class` = "SH Kitten" OR `sc`.`show_class` = "LH Cat" OR `sc`.`show_class` = "SH Cat"
                OR `sc`.`show_class` = "LH Alter" OR `sc`.`show_class` = "SH Alter" OR `sc`.`show_class` = "LH HHP Kitten" OR `sc`.`show_class` = "SH HHP Kitten"
                OR `sc`.`show_class` = "LH HHP" OR `sc`.`show_class` = "SH HHP" OR `sc`.`show_class` = "LH PNB" OR `sc`.`show_class` = "SH PNB"
                OR `sc`.`show_class` = "LH ANB" OR `sc`.`show_class` = "SH ANB" OR `sc`.`show_class` = "LH NT" OR `sc`.`show_class` = "SH NT" OR ( `sc`.`show_class` = "Ex Only" AND r.ring_name IS NOT NULL) )');

		if ($ring_timing == '1')
			$query->where('e.entry_participates_AM = 1');
		else if ($ring_timing == '2')
			$query->where('e.entry_participates_PM = 1');
		
		$query->group('e.cat, `sc`.`show_class`, e.status');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$db->execute();
		$entries = $db->getNumRows();

		$query = $db->getQuery(true);
		$query->select('count(placeholder_id)');
		$query->from('#__toes_placeholder AS p');
		$query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('(es.entry_status = "Accepted" OR es.entry_status = "Confirmed" OR es.entry_status = "Confirmed & Paid")');
		$query->where('pd.placeholder_day_showday = ' . $show_day_id);

		if ($ring_timing == '1')
			$query->where('pd.placeholder_participates_AM = 1');
		else if ($ring_timing == '2')
			$query->where('pd.placeholder_participates_PM = 1');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholders = $db->loadResult();

		$available_space = $show_day->show_day_cat_limit - ($entries + $placeholders);
	
		return ($available_space > 0) ? $available_space : 0;
	}

	public static function getShowDays($show_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('sd.*');
		$query->from('#__toes_show_day AS sd');
		$query->join('LEFT', '#__toes_show AS s ON sd.show_day_show = s.show_id');

		$query->where('s.show_id=' . $show_id);

		$query->order('sd.show_day_date ASC');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function isShowHasSpace($show_id) {
		$showdays = self::getShowDays($show_id);
		$user = JFactory::getUser();

		$space = 0;
		if (self::is_clubowner($user->id, self::getClub($show_id)->club_id) || self::is_showofficial($user->id, $show_id)) {
			foreach ($showdays as $day) {
				$space += self::getAvailableSpaceforDayforEC($day->show_day_id);
			}
		} else {
			foreach ($showdays as $day) {
				$space += self::getAvailableSpaceforDay($day->show_day_id);
			}
		}

		if ($space)
			return true;
		else
			return false;
	}

	public static function getWaitingEntries($show_day_id) {
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getEntryFullViewQuery();
		$query->select('`e`.`entry_date_created` AS timestamp, "entry" AS type, GROUP_CONCAT(DISTINCT(CONVERT(`e`.`show_day` , CHAR(8)))) AS showdays, `es`.`summary_user` AS user');
		
		$query->where('`estat`.`entry_status` IN ("Waiting List", "Waiting List & Confirmed", "Waiting List & Paid")');
		$query->where('`e`.`show_day` = ' . $show_day_id);
		$query->group('`e`.`cat`, `sc`.`show_class`');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadObjectList();

		return $entries;
	}

	public static function getWaitingPlaceholders($show_day_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select('p.*, pd.placeholder_day_date_created AS timestamp, "placeholder" AS type, GROUP_CONCAT(DISTINCT(CONVERT(pd.placeholder_day_showday , CHAR(8)))) AS showdays, es.entry_status, p.placeholder_exhibitor AS user');
		$query->from('#__toes_placeholder AS p');
		$query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('(es.entry_status = "Waiting List" OR es.entry_status = "Waiting List & Confirmed" OR es.entry_status = "Waiting List & Paid")');
		$query->where('pd.placeholder_day_showday = ' . $show_day_id);
		$query->group('p.placeholder_id');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholders = $db->loadObjectList();

		return $placeholders;
	}

	public static function getCongressFilters($ring_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('c.congress_id AS ring_id, c.congress_name AS ring_name');
		$query->select('c.congress_breed_switch AS breed_filter, c.congress_gender_switch AS gender_filter, c.congress_new_trait_switch AS newtrait_filter');
		$query->select('c.congress_hair_length_switch AS hairlength_filter, c.congress_category_switch AS category_filter');
		$query->select('c.congress_division_switch AS division_filter, c.congress_color_switch AS color_filter');
		$query->select('c.congress_title_switch AS title_filter, c.congress_manual_select_switch AS manual_filter');

		$query->from('#__toes_congress AS c');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(ccc.`congress_competitive_class_competitive_class` , CHAR(8)))) AS class_value');
		$query->join('LEFT', '#__toes_congress_competitive_class AS ccc ON ccc.congress_competitive_class_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(sc.show_class)) AS class_text');
		$query->join('LEFT', '#__toes_show_class AS sc ON ccc.congress_competitive_class_competitive_class = sc.show_class_id');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(cb.`congress_breed_breed` , CHAR(8)))) AS breed_value');
		$query->join('LEFT', '#__toes_congress_breed AS cb ON cb.congress_breed_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(CONCAT(breed.breed_name," (",breed.breed_abbreviation,")"))) AS breed_text');
		$query->join('LEFT', '#__toes_breed AS breed ON cb.congress_breed_breed = breed.breed_id');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(cg.`congress_gender_gender` , CHAR(8)))) AS gender_value');
		$query->join('LEFT', '#__toes_congress_gender AS cg ON cg.congress_gender_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(CONCAT(gender.gender_name," (",gender.gender_short_name,")"))) AS gender_text');
		$query->join('LEFT', '#__toes_cat_gender AS gender ON cg.`congress_gender_gender` = gender.gender_id');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(ch.`congress_hair_length_hair_length` , CHAR(8)))) AS hairlength_value');
		$query->join('LEFT', '#__toes_congress_hair_length AS ch ON ch.congress_hair_length_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(CONCAT(hairlength.cat_hair_length," (",hairlength.cat_hair_length_abbreviation,")"))) AS hairlength_text');
		$query->join('LEFT', '#__toes_cat_hair_length AS hairlength ON ch.congress_hair_length_hair_length = hairlength.cat_hair_length_id');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(cc.`congress_category_category` , CHAR(8)))) AS category_value');
		$query->join('LEFT', '#__toes_congress_category AS cc ON cc.congress_category_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(category.category)) AS category_text');
		$query->join('LEFT', '#__toes_category AS category ON cc.congress_category_category = category.	category_id');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(cd.`congress_division_division` , CHAR(8)))) AS division_value');
		$query->join('LEFT', '#__toes_congress_division AS cd ON cd.congress_division_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(division.division_name)) AS division_text');
		$query->join('LEFT', '#__toes_division AS division ON cd.congress_division_division = division.division_id');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(ccol.congress_color_color , CHAR(8)))) AS color_value');
		$query->join('LEFT', '#__toes_congress_color AS ccol ON ccol.congress_color_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(color.color_name)) AS color_text');
		$query->join('LEFT', '#__toes_color AS color ON ccol.congress_color_color = color.color_id');

		$query->select('GROUP_CONCAT(DISTINCT(cwd.congress_color_wildcard_wildcard)) AS cwd_value');
		$query->join('LEFT', '#__toes_congress_color_wildcard AS cwd ON cwd.congress_color_wildcard_congress = c.congress_id');

		$query->select('GROUP_CONCAT(DISTINCT(CONVERT(ct.congress_title_title , CHAR(8)))) AS title_value');
		$query->join('LEFT', '#__toes_congress_title AS ct ON ct.congress_title_congress = c.congress_id');
		$query->select('GROUP_CONCAT(DISTINCT(CONCAT(title.cat_title," (",title.cat_title_abbreviation	,")"))) AS title_text');
		$query->join('LEFT', '#__toes_cat_title AS title ON ct.congress_title_title = title.cat_title_id');

		$query->where('c.congress_id=' . $ring_id);

		$query->group('c.congress_id');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$filter = $db->loadObject();

		return $filter;
	}

	public static function deleteCongressFilters($ring_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress');
		$query->where('congress_id=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_competitive_class');
		$query->where('congress_competitive_class_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_breed');
		$query->where('congress_breed_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_gender');
		$query->where('congress_gender_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_hair_length');
		$query->where('congress_hair_length_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_category');
		$query->where('congress_category_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_division');
		$query->where('congress_division_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_color');
		$query->where('congress_color_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_color_wildcard');
		$query->where('congress_color_wildcard_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();

		$query = $db->getQuery(true);
		$query->delete('#__toes_congress_title');
		$query->where('congress_title_congress=' . $ring_id);

		$db->setQuery($query);
		$db->query();
	
	}

	public static function getRingDetails($ring_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('r.ring_id, r.ring_show_day, r.ring_judge, r.ring_show, r.ring_organization, r.ring_number, r.ring_name, r.ring_timing, rf.ring_format_id, rf.ring_format');
		$query->from('#__toes_ring AS r');
		$query->join('LEFT', '#__toes_ring_format AS rf ON rf.ring_format_id = r.ring_format');

		$query->where('r.ring_id=' . $ring_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObject();
	}

	public static function isFilterManual($congress_id) {
		$filter = self::getCongressFilters($congress_id);
		if ($filter->manual_filter)
			return true;
		else
			return false;
	}

	public static function searchColor($text) {
		$db = JFactory::getDBO();
		$like = $db->Quote('%' . $db->escape(strtolower($text), true) . '%', false);

		$query = "SELECT DISTINCT `c`.`color_id`
                FROM `#__toes_breed_category_division_color` AS `bcdc`
                LEFT JOIN `#__toes_color` AS `c` ON `c`.`color_id` = `bcdc`.`color`
                WHERE (`bcdc`.`organization` = 1) AND (`c`.`color_name` LIKE " . $like . ") 
                ORDER BY c.`color_id` ASC";

		$db->setQuery($query);
		$colors = $db->loadColumn();

		return $colors;
	}

	public static function isApplicableForCongress($show_id, $cat_id) {
		$db = JFactory::getDbo();

		$sql = $db->getQuery(true);

		$sql->select("GROUP_CONCAT(DISTINCT(CONVERT(`e`.`show_day` , CHAR(8)))) AS showdays");
		$sql->from("`#__toes_entry` AS `e`");
		$sql->where("`e`.`entry_show`= {$show_id}");
		$sql->where("`e`.`cat`= {$cat_id}");
		
		$db->setQuery($sql);
		$entry_showdays = $db->loadResult();
		
		$is_alternative = self::isAlternative($show_id);
		$entry_full_details = self::getEntryFullDetails($cat_id, $show_id);

		$entry_for_AM = array();
		$entry_for_PM = array();
		foreach ($entry_full_details as $item) {
			if ($item->entry_participates_AM)
				$entry_for_AM[] = $item->show_day;

			if ($item->entry_participates_PM)
				$entry_for_PM[] = $item->show_day;
		}

		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_ring as r');
		$query->join('LEFT', '#__toes_ring_format AS rf ON r.ring_format = rf.ring_format_id');
		$query->where('r.ring_show_day in (' . $entry_showdays . ') AND rf.ring_format = ' . $db->quote('Congress'));

		$db->setQuery($query);
		$congress_rings = $db->loadObjectlist();

		$applicable_for_congress = false;		
		foreach ($entry_full_details as $entry) {
			$query = "SELECT r.`ring_id` 
					FROM #__toes_ring AS r 
					LEFT JOIN #__toes_entry_participates_in_congress AS c ON c.congress_id = r.ring_id
					WHERE c.entry_id = {$entry->entry_id}";
			$db->setQuery($query);
			$participated_congresses = $db->loadColumn();

			if ($congress_rings) {
				foreach ($congress_rings as $congress) {
					if ($is_alternative) {
						if ($congress->ring_timing == 1) {
							if (!in_array($congress->ring_show_day, $entry_for_AM)) {
								continue;
							}
						}
						if ($congress->ring_timing == 2) {
							if (!in_array($congress->ring_show_day, $entry_for_PM)) {
								continue;
							}
						}
					}
					if (self::matchCongressFiltersforEntry($entry->entry_id, $congress->ring_id)) {
						if (!$participated_congresses)
							$applicable_for_congress[] = $congress->ring_id;
						else {
							if (!in_array($congress->ring_id, $participated_congresses)) {
								if($entry->show_day == $congress->ring_show_day) {
									$query = "INSERT INTO `#__toes_entry_participates_in_congress` values 
										( {$entry->entry_id}, {$congress->ring_id} )";

									$db->setQuery($query);
									if (!$db->query()) {
										$this->setError($db->getErrorMsg());
										return false;
									}
								}
							}
						}
					} else {
						if (in_array($congress->ring_id, $participated_congresses)) {
							$query = "DELETE FROM 
								#__toes_entry_participates_in_congress 
								WHERE congress_id = {$congress->ring_id}
								AND entry_id = {$entry->entry_id}";

							$db->setQuery($query);
							$db->query();
						}
					}
				}
			}
		}
		if (is_array($applicable_for_congress))
			$applicable_for_congress = implode(',', $applicable_for_congress);

		return $applicable_for_congress;
	}

	public static function matchCongressFilters($cat_id, $congress_id) {
		$cat = self::getCatDetails($cat_id);
		$congress = self::getRingDetails($congress_id);
		$filter = self::getCongressFilters($congress_id);

		$class_matched = false;
		$breed_matched = false;
		$gender_matched = false;
		$newtrait_matched = false;
		$hairlength_matched = false;
		$category_matched = false;
		$division_matched = false;
		$color_matched = false;
		$title_matched = false;

		$db = JFactory::getDBO();
		$cat_show_class = self::getCatclassonShowday($cat->cat_id, $congress->ring_show_day);
		$query = "SELECT sc.`show_class_id` FROM `#__toes_show_class` AS sc WHERE sc.show_class = ".$db->quote($cat_show_class);

		$db->setQuery($query);
		$class = $db->loadResult();

		$values = explode(',', $filter->class_value);
		if (in_array($class, $values)) {
			$class_matched = true;
		}

		if ($filter->breed_filter) {
			$values = explode(',', $filter->breed_value);
			if (in_array($cat->cat_breed, $values)) {
				$breed_matched = true;
			}
		}
		else
			$breed_matched = true;

		if ($filter->gender_filter) {
			$values = explode(',', $filter->gender_value);
			if (in_array($cat->cat_gender, $values)) {
				$gender_matched = true;
			}
		}
		else
			$gender_matched = true;

		if ($filter->newtrait_filter) {
			if ($cat->cat_new_trait)
				$newtrait_matched = true;
		}
		else
			$newtrait_matched = true;


		if ($filter->hairlength_filter) {
			$values = explode(',', $filter->hairlength_value);
			if (in_array($cat->cat_hair_length, $values)) {
				$hairlength_matched = true;
			}
		}
		else
			$hairlength_matched = true;

		if ($filter->category_filter) {
			$values = explode(',', $filter->category_value);
			if (in_array($cat->cat_category, $values)) {
				$category_matched = true;
			}
		}
		else
			$category_matched = true;

		if ($filter->division_filter) {
			$values = explode(',', $filter->division_value);
			if (in_array($cat->cat_division, $values)) {
				$division_matched = true;
			}
		}
		else
			$division_matched = true;

		if ($filter->color_filter) {
			$values = explode(',', $filter->color_value);
			if (in_array($cat->cat_color, $values)) {
				$color_matched = true;
			}

			if (!$color_matched) {
				if ($filter->cwd_value) {
					$values = explode(',', $filter->cwd_value);
					foreach ($values as $value) {
						$colors = self::searchColor($value);
						if (in_array($cat->cat_color, $colors)) {
							$color_matched = true;
							break;
						}
					}
				}
			}
		}
		else
			$color_matched = true;

		if ($filter->title_filter) {
			$values = explode(',', $filter->title_value);
			if (in_array($cat->cat_title, $values)) {
				$title_matched = true;
			}
		}
		else
			$title_matched = true;

		if ($class_matched && $breed_matched && $gender_matched && $newtrait_matched && $hairlength_matched
				&& $category_matched && $division_matched && $color_matched & $title_matched) {
			return true;
		}
		else
			return false;
	}

	public static function matchCongressFiltersforEntry($entry_id, $congress_id) {
		$entry = self::getEntryFullDetail($entry_id);
		$congress = self::getRingDetails($congress_id);
		$filter = self::getCongressFilters($congress_id);

		$class_matched = false;
		$breed_matched = false;
		$gender_matched = false;
		$newtrait_matched = false;
		$hairlength_matched = false;
		$category_matched = false;
		$division_matched = false;
		$color_matched = false;
		$title_matched = false;

		if($entry->Show_Class == 'Ex Only') {
			$db = JFactory::getDBO();
			$cat_show_class = self::getCatclassonShowday($entry->cat, $congress->ring_show_day);

			$values = explode(',', $filter->class_text);
			if (in_array($entry->Show_Class, $values) || in_array($cat_show_class, $values)) {
				$class_matched = true;
			}
		} else {
			$db = JFactory::getDBO();
			$cat_show_class = self::getCatclassonShowday($entry->cat, $congress->ring_show_day);

			$values = explode(',', $filter->class_text);

			if (in_array($cat_show_class, $values)) {
				$class_matched = true;
			}
		}
		
		if ($filter->breed_filter) {
			$values = explode(',', $filter->breed_value);
			if (in_array($entry->copy_cat_breed, $values)) {
				$breed_matched = true;
			}
		}
		else
			$breed_matched = true;
		
		if ($filter->gender_filter) {
			$values = explode(',', $filter->gender_value);
			if (in_array($entry->copy_cat_gender, $values)) {
				$gender_matched = true;
			}
		}
		else
			$gender_matched = true;

		if ($filter->newtrait_filter) {
			if ($entry->copy_cat_new_trait)
				$newtrait_matched = true;
		}
		else
			$newtrait_matched = true;


		if ($filter->hairlength_filter) {
			$values = explode(',', $filter->hairlength_value);
			if (in_array($entry->copy_cat_hair_length, $values)) {
				$hairlength_matched = true;
			}
		}
		else
			$hairlength_matched = true;

		if ($filter->category_filter) {
			$values = explode(',', $filter->category_value);
			if (in_array($entry->copy_cat_category, $values)) {
				$category_matched = true;
			}
		}
		else
			$category_matched = true;

		if ($filter->division_filter) {
			$values = explode(',', $filter->division_value);
			if (in_array($entry->copy_cat_division, $values)) {
				$division_matched = true;
			}
		}
		else
			$division_matched = true;

		if ($filter->color_filter) {
			$values = explode(',', $filter->color_value);
			if (in_array($entry->copy_cat_color, $values)) {
				$color_matched = true;
			}

			if (!$color_matched) {
				if ($filter->cwd_value) {
					$values = explode(',', $filter->cwd_value);
					foreach ($values as $value) {
						$colors = self::searchColor($value);
						if (in_array($entry->copy_cat_color, $colors)) {
							$color_matched = true;
							break;
						}
					}
				}
			}
		}
		else
			$color_matched = true;

		if ($filter->title_filter) {
			$values = explode(',', $filter->title_value);
			if (in_array($entry->copy_cat_title, $values)) {
				$title_matched = true;
			}
		}
		else
			$title_matched = true;
		
		if ($class_matched && $breed_matched && $gender_matched && $newtrait_matched && $hairlength_matched
				&& $category_matched && $division_matched && $color_matched & $title_matched) {
			return true;
		}
		else
			return false;
	}

	public static function checkWaitingList($show_id) {
		$db = JFactory::getDbo();

		$showdays = self::getShowDays($show_id);
		$isAlternative = self::isAlternative($show_id);

		if ($isAlternative) {
			return;
		} else {
			foreach ($showdays as $showday) {
				if (!self::getAvailableSpaceforDay($showday->show_day_id)) {
					continue;
				}

				$entries = self::getWaitingEntries($showday->show_day_id);
				$placeholders = self::getWaitingPlaceholders($showday->show_day_id);

				$waiting_entries = array_merge($entries, $placeholders);

				$waiting_entries = self::aasort($waiting_entries, 'timestamp');

				foreach ($waiting_entries as $entry) {
					$status = '';
					switch ($entry->entry_status) {
						case 'Waiting List':
							$status = 'New';
							break;
						case 'Waiting List & Confirmed':
							$status = 'Confirmed';
							break;
						case 'Waiting List & Paid':
							$status = 'Confirmed & Paid';
							break;
					}

					if ($entry->type == 'entry') {
						$query = $db->getQuery(true);
					
						$query->update('#__toes_entry AS e');
						$query->set('e.`status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = ' . $db->quote($status) . ')');

						$query->where('e.`cat` = ' . $entry->cat);
						$query->where('e.`show_day` = ' . $showday->show_day_id);

						$db->setQuery($query);
						
					} else {
						$query = $db->getQuery(true);
						
						$query->update('#__toes_placeholder_day');

						$query->set('`placeholder_day_placeholder_status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = ' . $db->quote($status) . ')');

						$query->where('`placeholder_day_placeholder` = ' . $entry->placeholder_id);
						$query->where('`placeholder_day_showday` = ' . $showday->show_day_id);

						$db->setQuery($query);
						
					}

					if ($db->query()) {
						unset($query);
						$query = TOESQueryHelper::getEntryFullViewQuery();
						$query->where('`es`.`summary_user` = ' . $entry->user);
						$query->where('`e`.`entry_show` = ' . $show_id);
						$query->where('`estat`.`entry_status` IN ("Confirmed", "Confirmed & Paid")');

						$db->setQuery($query);
						$old_entries = $db->loadObjectList();
						
						$query = $db->getQuery(true);
						$query->select('p.*, sd.show_day_date');
						$query->from('#__toes_placeholder_day as pd');
						$query->join('left', '#__toes_show_day AS sd ON sd.show_day_id = pd.placeholder_day_showday');
						$query->join('left', '#__toes_placeholder as p ON p.placeholder_id = pd.placeholder_day_placeholder');
						$query->where('p.placeholder_exhibitor = ' . $entry->user);
						$query->where('p.placeholder_show = ' . $show_id);

						$query->join('left', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
						$query->where('es.entry_status = "Confirmed" OR es.entry_status = "Confirmed & Paid"');

						$db->setQuery($query);
						$old_placeholders = $db->loadObjectList();
					
						if ($old_entries || $old_placeholders) {
							require_once JPATH_BASE . '/components/com_toes/models/entryclerk.php';
							$entryClerkmodel = new TOESModelEntryclerk();
							$entryClerkmodel->confirmEntries($entry->user, $show_id, false);
						}
					}

					if (!self::getAvailableSpaceforDay($showday->show_day_id)) {
						break;
					}
				}
			}
		}
	}

	public static function getNewEntries($show_day_id) {
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getEntryFullViewQuery();
		$query->select('`e`.`entry_date_created` AS timestamp, "entry" AS type, GROUP_CONCAT(DISTINCT(CONVERT(`e`.`show_day` , CHAR(8)))) AS `showdays`, `es`.`summary_user` AS `user`');
		
		$query->where('`estat`.`entry_status` = "New"');
		$query->where('`e`.`show_day` = ' . $show_day_id);
		$query->group('`e`.`cat`, `sc`.`show_class`');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadObjectList();

		return $entries;
	}

	public static function getNewPlaceholders($show_day_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('p.*, pd.placeholder_day_date_created AS timestamp, "placeholder" AS type, GROUP_CONCAT(DISTINCT(CONVERT(pd.placeholder_day_showday , CHAR(8)))) AS showdays, es.entry_status, p.placeholder_exhibitor AS user');
		$query->from('#__toes_placeholder AS p');
		$query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
		$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
		$query->where('(es.entry_status = "New")');
		$query->where('pd.placeholder_day_showday = ' . $show_day_id);
		$query->group('p.placeholder_id');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$placeholders = $db->loadObjectList();

		return $placeholders;
	}

	public static function transferToWaitingList($show_id) {
		$db = JFactory::getDbo();

		$showdays = self::getShowDays($show_id);
		$isAlternative = self::isAlternative($show_id);

		if ($isAlternative) {
			return;
		} else {
			foreach ($showdays as $showday) {
				if (self::getAvailableSpaceforDay($showday->show_day_id)) {
					continue;
				}

				$entries = self::getNewEntries($showday->show_day_id);
				$placeholders = self::getNewPlaceholders($showday->show_day_id);

				$new_entries = array_merge($entries, $placeholders);

				$new_entries = self::aasort($new_entries, 'timestamp');
				$new_entries = array_reverse($new_entries);

				foreach ($new_entries as $entry) {
					$status = 'Waiting List';

					if ($entry->type == 'entry') {
						if ($entry->Show_Class == "Ex Only") {
							$query = $db->getQuery(true);
							
							$query->select('count(entry_id)');
							$query->from('#__toes_entry_participates_in_congress');
							$query->where('entry_id = ' . $entry->entry_id);
							$db->setQuery($query);
							if ($db->loadResult() == 0)
								continue;
							
						}

						$query = $db->getQuery(true);
					
						$query->update('#__toes_entry AS e');
						$query->set('e.`status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = ' . $db->quote($status) . ')');

						$query->where('e.`cat` = ' . $entry->cat);
						$query->where('e.`show_day` = ' . $showday->show_day_id);

						$db->setQuery($query);
						$db->query();
						
					}
					else {
						$query = $db->getQuery(true);
						
						$query->update('#__toes_placeholder_day');

						$query->set('`placeholder_day_placeholder_status` = (SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` = ' . $db->quote($status) . ')');

						$query->where('`placeholder_day_placeholder` = ' . $entry->placeholder_id);
						$query->where('`placeholder_day_showday` = ' . $showday->show_day_id);

						$db->setQuery($query);
						$db->query();
						
					}

					$query = TOESQueryHelper::getEntryFullViewQuery();
					$query->clear('select');
					$query->select('count(`e`.`entry_id`)');
					$query->where('`estat`.`entry_status` IN ("New", "Accepted", "Confirmed", "Confirmed & Paid")');
					$query->where('`e`.`show_day` = ' . $showday->show_day_id);

					$query->join('LEFT', '`#__toes_entry_participates_in_congress` AS `pc` ON `pc`.`entry_id` = `e.entry_id`');
					$query->join('LEFT', '`#__toes_ring` AS `r` ON `r`.`ring_id` = `pc`.`congress_id`');

					$query->where('( `sc`.`show_class` = "LH Kitten" OR `sc`.`show_class` = "SH Kitten" OR `sc`.`show_class` = "LH Cat" OR `sc`.`show_class` = "SH Cat"
							OR `sc`.`show_class` = "LH Alter" OR `sc`.`show_class` = "SH Alter" OR `sc`.`show_class` = "LH HHP Kitten" OR `sc`.`show_class` = "SH HHP Kitten"
							OR `sc`.`show_class` = "LH HHP" OR `sc`.`show_class` = "SH HHP" OR `sc`.`show_class` = "LH PNB" OR `sc`.`show_class` = "SH PNB"
							OR `sc`.`show_class` = "LH ANB" OR `sc`.`show_class` = "SH ANB" OR `sc`.`show_class` = "LH NT" OR `sc`.`show_class` = "SH NT" OR ( `sc`.`show_class` = "Ex Only" AND r.ring_name IS NOT NULL) )');

					if ($entry_id) {
						$query->where('`e`.`entry_id` != ' . $entry_id);
					}

					//echo nl2br(str_replace('#__', 'j35_', $query));
					$db->setQuery($query);
					$entries = $db->loadResult();
					
					$query = $db->getQuery(true);
					$query->select('count(placeholder_id)');
					$query->from('#__toes_placeholder AS p');
					$query->join('LEFT', '#__toes_placeholder_day AS pd ON pd.placeholder_day_placeholder= p.placeholder_id');
					$query->join('LEFT', '#__toes_entry_status AS es ON es.entry_status_id = pd.placeholder_day_placeholder_status');
					$query->where('(es.entry_status = "New" OR es.entry_status = "Accepted" OR es.entry_status = "Confirmed" OR es.entry_status = "Confirmed & Paid")');
					$query->where('pd.placeholder_day_showday = ' . $showday->show_day_id);

					//echo nl2br(str_replace('#__', 'j35_', $query));
					$db->setQuery($query);
					$placeholders = $db->loadResult();
				
					//TODO: Need to change count to AM / PM for Alternative shows 
					$available_space = $showday->show_day_cat_limit - ($entries + $placeholders);

					if ($available_space == 0)
						break;
				}
			}
		}
	}

	public static function getUsers() {
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getUserViewQuery();
		$query->order('`name`');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function isSubscribedToShow($user_id, $show_id) {

		if(!isset(self::$subscribedShows[$user_id])){			
			$db = JFactory::getDbo();
			$query = "SELECT s.`user_subcribed_to_show_show`
	            FROM `#__toes_user_subcribed_to_show` as `s`
	            WHERE `s`.`user_subcribed_to_show_user` = {$user_id}";
	
			//echo nl2br(str_replace('#__', 'j35_', $query));
			$db->setQuery($query);
			self::$subscribedShows[$user_id] = $db->loadColumn();
		}
		
		if(in_array($show_id, self::$subscribedShows[$user_id])) {
			return true;
		} else {
			return false;
		}
	}

	public static function getSubscribedUsers($show_id) {
		$db = JFactory::getDbo();

		$query = TOESQueryHelper::getUserViewQuery();
		$query->select('`cprof`.`firstname`, `cprof`.`lastname`');
		$query->join("left","`#__toes_user_subcribed_to_show` as `s` ON `u`.`id` = `s`.`user_subcribed_to_show_user`");
		$query->where("`s`.`user_subcribed_to_show_show` = {$show_id}");
		$query->order("`u`.`name`");

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getCatBreedStatusonDate($cat_id, $date) {
		$db = JFactory::getDbo();

		$query = "SELECT `bs`.`breed_status` 
            FROM `#__toes_cat` AS `c`
            LEFT JOIN `#__toes_breed` AS `b` ON (`c`.`cat_breed` = `b`.`breed_id`)
            LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND {$db->quote($date)} BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
            LEFT JOIN `#__toes_breed_status` AS `bs` ON `bhs`.`breed_has_status_status` = `bs`.`breed_status_id`
            WHERE `c`.`cat_id` = {$cat_id}";

		$db->setQuery($query);
		$breed_status = $db->loadResult();

		return $breed_status;
	}

	public static function getEntryBreedStatusonDate($cat_id, $show_id, $date) {
		$db = JFactory::getDbo();

		$query = "SELECT `bs`.`breed_status` 
            FROM `#__toes_entry` AS `e`
            LEFT JOIN `#__toes_breed` AS `b` ON (`e`.`copy_cat_breed` = `b`.`breed_id`)
            LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND {$db->quote($date)} BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
            LEFT JOIN `#__toes_breed_status` AS `bs` ON `bhs`.`breed_has_status_status` = `bs`.`breed_status_id`
            WHERE `e`.`cat` = {$cat_id} AND `e`.`entry_show` = {$show_id}";

		$db->setQuery($query);
		$breed_status = $db->loadResult();

		return $breed_status;
	}

	public static function isAlternative($show_id) {
		$db = JFactory::getDbo();

		$whr = array();
		$whr[] = "`sf`.`show_format` = 'Alternative'";
		$whr[] = "`s`.`show_id` = " . $show_id;
		
		$query = TOESQueryHelper::getShowViewQuery($whr);

		$db->setQuery($query);
		if ($db->loadResult())
			return true;
		else
			return false;
	}

	public static function isContinuous($show_id) {
		$db = JFactory::getDbo();

		$whr = array();
		$whr[] = "`sf`.`show_format` = 'Continuous'";
		$whr[] = "`s`.`show_id` = " . $show_id;
		
		$query = TOESQueryHelper::getShowViewQuery($whr);

		$db->setQuery($query);
		if ($db->loadResult())
			return true;
		else
			return false;
	}

	public static function deleteWaitingList($show_id) {
		$db = JFactory::getDbo();

		$waiting_status = array(
			'Waiting List',
			'Waiting List & Confirmed',
			'Waiting List & Confirmed'
		);

		$query = "SELECT `entry_id` FROM #__toes_entry WHERE entry_show = " . $show_id . " AND status IN ( SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` IN ('Waiting List') )";
		$db->setQuery($query);
		$entry_ids = $db->loadColumn();

		if ($entry_ids) {
			$query = "DELETE * FROM #__toes_entry_participates_in_congress WHERE entry_id IN (" . implode(',', $entry_ids) . ");"
					."DELETE * FROM #__toes_entry_refusal_reason WHERE entry_refusal_reason_entry IN (" . implode(',', $entry_ids) . ");"
					."DELETE * FROM #__toes_entry WHERE entry_id IN (" . implode(',', $entry_ids) . ");"
					;
			$db->setQuery($query);
			$db->query();
		}

		/*
		$query = "SELECT DISTINCT(pd.`placeholder_day_placeholder`) FROM #__toes_placeholder_day AS pd 
			LEFT JOIN #__toes_placeholder AS p ON p.placeholder_id = pd.`placeholder_day_placeholder`
			WHERE p.placeholder_show = " . $show_id . " AND pd.placeholder_day_placeholder_status IN (" . implode(',', $waiting_status) . ")";
			*/
		
		$query = "SELECT DISTINCT(pd.`placeholder_day_placeholder`) FROM #__toes_placeholder_day AS pd 
			LEFT JOIN #__toes_placeholder AS p ON p.placeholder_id = pd.`placeholder_day_placeholder`
			WHERE p.placeholder_show = " . $show_id . " AND pd.placeholder_day_placeholder_status IN ( SELECT `entry_status_id` FROM `#__toes_entry_status` WHERE `entry_status` IN ('Waiting List') )";
				
		$db->setQuery($query);
		$placeholder_ids = $db->loadColumn();

		if ($placeholder_ids) {
			$query = "DELETE * FROM #__toes_placeholder_day WHERE placeholder_day_placeholder IN (" . implode(',', $placeholder_ids) . ");"
					."DELETE * FROM #__toes_placeholder WHERE placeholder_id IN (" . implode(',', $placeholder_ids) . ");"
					;
			$db->setQuery($query);
			$db->query();
		}
	}

	public static function getShowExhibitors($show_id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('distinct(s.summary_user) as user_id, IF(c.lastname IS NOT NULL OR c.firstname IS NOT NULL, concat(c.lastname,", ",c.firstname), u.name) as user_name');

		$query->select('s.summary_id, s.summary_user, s.summary_show, s.summary_single_cages, s.summary_double_cages, s.summary_benching_request');
		$query->select('s.summary_grooming_space, s.summary_personal_cages, s.summary_remarks, s.summary_total_fees');
		$query->select('s.summary_fees_paid, s.summary_benching_area, s.summary_entry_clerk_note, s.summary_entry_clerk_private_note');

		$query->select('count(e.catalog_number) AS entries');
			
		$query->from('#__toes_summary AS s');
		$query->join('left', '#__comprofiler AS c ON c.id = s.summary_user');
		$query->join('left', '#__users AS u ON u.id = s.summary_user');
		$query->join('left', '#__toes_entry AS e ON e.summary = s.summary_id');
		$query->join('left', '#__toes_entry_status AS es ON es.entry_status_id = e.status');

		$query->where('s.summary_show=' . $show_id);
		$query->where('es.entry_status IN ("Confirmed", "Confirmed & Paid")');
		$query->group('s.summary_user');
		$query->order('c.lastname ASC, c.firstname ASC');

		//echo nl2br(str_replace('#_', 'j35', $query));
		$db->setQuery($query);
		$exhibitors = $db->loadObjectList();

		return $exhibitors;
	}

	public static function getShowdayRings($showday_id, $timing = null) {
		$db = JFactory::getDbo();

		$query = "SELECT r.*, sd.show_day_date as show_day, concat(concat(cb.firstname,' ',cb.lastname),' - ',tjl.judge_level) as ring_judge_name
            FROM #__toes_ring as r
            LEFT JOIN #__toes_show_day AS sd ON r.ring_show_day=sd.show_day_id
            LEFT JOIN #__toes_judge as tj ON r.ring_judge  = tj.judge_id
            LEFT JOIN #__comprofiler as cb ON tj.user = cb.user_id
            LEFT JOIN #__toes_judge_level AS tjl ON tjl.judge_level_id = tj.judge_level 
            WHERE r.ring_show_day =" . $showday_id;
		
		if($timing)
			$query .= " AND r.ring_timing = ".$timing;

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function getCongressRings($show_id) {
		$db = JFactory::getDbo();

		$query = "SELECT r.*, sd.show_day_date as show_day, concat(concat(cb.firstname,' ',cb.lastname),' - ',tjl.judge_level) as ring_judge_name
            FROM #__toes_ring as r
            LEFT JOIN #__toes_show_day AS sd ON r.ring_show_day=sd.show_day_id
            LEFT JOIN #__toes_judge as tj ON r.ring_judge  = tj.judge_id
            LEFT JOIN #__comprofiler as cb ON tj.user = cb.user_id
            LEFT JOIN #__toes_judge_level AS tjl ON tjl.judge_level_id = tj.judge_level
            WHERE r.ring_format = 3 AND r.ring_show =" . $show_id;

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public static function isKittenonDay($cat_id, $showday, $months = 0)
	{
		$db = JFactory::getDbo();

		$query = "SELECT 
			GREATEST(
			DATE_FORMAT(`sd`.`show_day_date`, '%Y') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%Y') - (DATE_FORMAT(`sd`.`show_day_date`, '00-%m-%d') < DATE_FORMAT(`cat`.`cat_date_of_birth`, '00-%m-%d')),
			0
			) AS age_years,
			IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
			  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
				  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
					  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
					  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
				  ),
				  IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`cat_date_of_birth`, '%m'),
					  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
						  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
						  MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
					  ),
					  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
						  MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')+12,12),
						  MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
					  )
				  )
			  ),
			  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
				  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
					  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
						  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
						  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
					  ),
					  0
				  ),
				  0
			  )
			)
			AS `age_months`

			FROM `#__toes_cat` AS `cat` , `#__toes_show_day` AS `sd`
			WHERE `cat`.`cat_id` = $cat_id AND `sd`.`show_day_id` = $showday";

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$cat_age = $db->loadObject();
		
		if($cat_age->age_years > 0)
			return false;
		else if($cat_age->age_months < ($months?$months:3))
			return true;
	}

	public static function isExOnlyforShow($cat_id, $showdays)
	{
		$db = JFactory::getDbo();

		$showdays = explode(',', $showdays);
		
		foreach($showdays as $showday)
		{
			$query = "SELECT
				GREATEST(
				DATE_FORMAT(`sd`.`show_day_date`, '%Y') - DATE_FORMAT(`cat`.`cat_date_of_birth`, '%Y') - (DATE_FORMAT(`sd`.`show_day_date`, '00-%m-%d') < DATE_FORMAT(`cat`.`cat_date_of_birth`, '00-%m-%d')),
				0
				) AS age_years,
				IF ( DATE_FORMAT(`show_day_date`, '%Y')  >  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
				  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
					  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
						  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
						  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
					  ),
					  IF(DATE_FORMAT(`show_day_date`, '%m') = DATE_FORMAT(`cat_date_of_birth`, '%m'),
						  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
							  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
							  MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
						  ),
						  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
							  MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')+12,12),
							  MOD(DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1+12,12)
						  )
					  )
				  ),
				  IF ( DATE_FORMAT(`show_day_date`, '%Y')  =  DATE_FORMAT(`cat_date_of_birth`, '%Y') ,
					  IF(DATE_FORMAT(`show_day_date`, '%m') > DATE_FORMAT(`cat_date_of_birth`, '%m'),
						  IF (DATE_FORMAT(`show_day_date`, '%d')>=DATE_FORMAT(`cat_date_of_birth`, '%d'),
							  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m'),
							  DATE_FORMAT(`show_day_date`, '%m') - DATE_FORMAT(`cat_date_of_birth`, '%m')-1
						  ),
						  0
					  ),
					  0
				  )
				)
				AS `age_months`

				FROM `#__toes_cat` AS `cat` , `#__toes_show_day` AS `sd`
				WHERE `cat`.`cat_id` = $cat_id AND `sd`.`show_day_id` = {$showday}";

			//echo nl2br(str_replace('#__', 'j35_', $query));
			$db->setQuery($query);
			$cat_age = $db->loadObject();

			if($cat_age->age_years > 0)
				continue;
			else if( $cat_age->age_months == 3)
				return true;
		}
		return false;
	}
	public static function getShowEntriesByShowday($show_id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('count(entry_id) AS cnt, e.show_day');
		$query->from('#__toes_entry AS e');
		$query->group('e.show_day');
		$query->where('e.entry_show=' . $show_id);

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadAssocList('show_day', 'cnt');
		
		return $entries;
	}

	public static function getShowEntriesCount($show_id)
	{
		$db = JFactory::getDbo();

		if(!isset(self::$showEntriesCount[$show_id]))
		{
			$query = "SELECT `show_id` FROM `#__toes_show`";
			$db->setQuery($query);
			$show_ids = $db->loadColumn();
	
			$query = $db->getQuery(true);
			$query->select('count(entry_id) AS cnt, e.entry_show');
			$query->from('#__toes_entry AS e');
			//$query->join('LEFT', '#__toes_show_day AS sd ON sd.show_day_id = e.show_day');
			$query->group('e.entry_show');
			//$query->where('sd.show_day_show=' . $show_id);
	
			//echo nl2br(str_replace('#__', 'j35_', $query));
			$db->setQuery($query);
			$entries = $db->loadAssocList('entry_show', 'cnt');
			
			$query = $db->getQuery(true);
			$query->select('count(p.placeholder_id) AS cnt, p.placeholder_show');
			$query->from('#__toes_placeholder AS p');
			$query->group('p.placeholder_show');
			//$query->where('p.placeholder_show = ' . $show_id);
			//echo nl2br(str_replace('#__', 'j35_', $query));
			$db->setQuery($query);
			$placeholders = $db->loadAssocList('placeholder_show', 'cnt');
		
			foreach($show_ids as $showid) {
				if(isset($entries[$showid])) {
					$ent = (int)$entries[$showid];
				} else {
					$ent = 0;
				}
								
				if(isset($placeholders[$showid])) {
					$ph = (int)$placeholders[$showid];
				} else {
					$ph = 0;
				}
				self::$showEntriesCount[$showid] = $ent + $ph;
			}
		}
		
		return self::$showEntriesCount[$show_id];
	}

	public static function getCatclassonShowday($cat_id, $show_day)
	{
		/*$db = JFactory::getDBO();
		$query = "SELECT cc.Show_Class
                FROM `#__toes_view_cat_competitive_class` AS cc
                WHERE cc.`cat_id` = {$cat_id}
                AND cc.`show_day_id` = {$show_day}";

		$db->setQuery($query);
		$class = $db->loadResult();

		return $class;*/

		$db = JFactory::getDbo();
		$cat_detail = self::getCatDetails($cat_id);

		$query = "SELECT `show_day_date` FROM `#__toes_show_day` WHERE `show_day_id` = ".$show_day;
		$db->setQuery($query);
		$show_day_date = $db->loadResult();

		$show_class = "";

		$query = "select `bs`.`breed_status` 
		FROM `#__toes_breed` AS `b`
		LEFT JOIN `#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND ".$db->quote($show_day_date)." BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)
		LEFT JOIN `#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)
		WHERE `b`.`breed_id` = ".$cat_detail->cat_breed;

		$db->setQuery($query);
		$breed_status = $db->loadResult();

		$is_HHP = strpos($cat_detail->breed_name,'Household') ? true : false ;

		$age_years = 0;
		$age_months = 0;

		$showdate = new DateTime($show_day_date, new DateTimeZone('UTC'));
		$cat_dob = new DateTime($cat_detail->cat_date_of_birth, new DateTimeZone('UTC'));
		$interval = $showdate->diff($cat_dob);

		$age_years = intval($interval->format('%y'));
		$age_months = intval($interval->format('%m'));

		$is_kitten = false;
		$is_adult = false;

		if($age_years > 0) {
			$is_adult = true;
		} else {
			if($age_months >= 8) {
				$is_adult = true;
			} elseif($age_months >= 4 && $age_months < 8) {
				$is_kitten = true;
			}
		}

		if ( $breed_status == 'Non Championship'){  #this means HHP
			if ( $is_HHP == true) {    # this is the only non championship class
				if ( $is_kitten == true) {  # HHP Kitten
					if ( $cat_detail->breed_hair_length == 'LH') {
						$show_class = 'LH HHP Kitten';
					} else {
						$show_class = 'SH HHP Kitten';
					}
				} else {
					if ( $is_adult == true) {   #HHP
						if ( $cat_detail->breed_hair_length == 'LH'){
							$show_class = 'LH HHP';
						} else {
							$show_class = 'SH HHP';
						}
					} else {
						if ($age_months >=3) {
							$show_class = 'Error - 3';
						} else {
							$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
						}
					}
				}
			} else {
				$show_class = 'ERROR - 2';
			}
		} elseif ( $breed_status == 'Championship') {
			if ( $cat_detail->cat_new_trait ) {
				if($is_kitten || $is_adult) {
					if ( $cat_detail->breed_hair_length == 'LH') {
						$show_class = 'LH NT';
					} else {
						$show_class = 'SH NT';
					} 
				} else {
					if ($age_months >=3) {
						$show_class = 'Error - 3';
					} else {
						$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
					}
				}
			} else {
				if ( $is_kitten == true) {
					if ( $cat_detail->breed_hair_length == 'LH') {
						$show_class = 'LH Kitten';
					} else {
						$show_class = 'SH Kitten';
					}
				} else {
					if ( $is_adult == true) { 
						if ( $cat_detail->breed_hair_length == 'LH') { 
							if( ($cat_detail->gender_short_name == 'M') || ($cat_detail->gender_short_name == 'F') ) {
								$show_class = 'LH Cat';
							 } else {
								$show_class = 'LH Alter';
							 }
						} else {
							if( ($cat_detail->gender_short_name == 'M') || ($cat_detail->gender_short_name == 'F') ) {
								$show_class = 'SH Cat';
							} else {
								$show_class = 'SH Alter';
							}
						}
					} else {
						if ($age_months >=3) {
							$show_class = 'Error - 3'; 
						} else {
							$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
						}
					}
				}
			}
		} else {  
			if( $breed_status == 'Advanced New Breed') {
				if($is_kitten || $is_adult) {
					if( $cat_detail->breed_hair_length == 'LH') {
						$show_class = 'LH ANB';
					} else {
						$show_class = 'SH ANB';
					}
				} else {
					if ($age_months >= 3){
						$show_class = 'Error - 3';
					} else {
						$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
					}
				}
			} else {
				if( $breed_status == 'Preliminary New Breed') {
					if($is_kitten || $is_adult) {
						if( $cat_detail->breed_hair_length == 'LH') {
							$show_class = 'LH PNB';
						} else {
							$show_class = 'SH PNB';
						}
					} else {
						if ($age_months >= 3) {
							$show_class = 'Error - 3';
						} else {
							$show_class = 'Not allowed in Show Hall - Minimum age is 3 months';
						}
					}
				} else {
					$show_class = 'Error - 4';
				}
			}
		}
		
		return $show_class;
	}

	public static function getEntryclassonShowday($cat_id, $show_day) {
		$db = JFactory::getDbo();
	
		$query = "SELECT `sc`.`show_class`
            FROM `#__toes_entry` AS `e`
			LEFT JOIN `#__toes_show_class` AS `sc` ON `e`.`entry_show_class` = `sc`.`show_class_id`
            WHERE `e`.`cat` = {$cat_id} AND `e`.`show_day` = {$show_day}";

		$db->setQuery($query);
		$class = $db->loadResult();

		return $class;
	}

	public static function getShowFinalEntriesCount($show_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('count(`e`.`entry_id`)');
		$query->from('`#__toes_entry` AS `e`');
		$query->join('left','`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)');
		$query->where('`e`.`entry_show` = ' . $show_id);
		$query->where('`estat`.`entry_status` IN ("Confirmed", "Confirmed & Paid")');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadResult();

		return $entries;
	}

	public static function getShowFinalExOnlyEntriesCount($show_id) {
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select('count(`e`.`entry_id`)');
		$query->from('`#__toes_entry` AS `e`');
		$query->join('left','`#__toes_show_class` AS `sc` ON `e`.`entry_show_class` = `sc`.`show_class_id`');
		$query->join('left','`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)');
		$query->where('`e`.`entry_show` = ' . $show_id);
		$query->where('`sc`.`show_class` = "Ex Only" ');
		$query->where('`e`.`entry_id` NOT IN (SELECT `pc`.`entry_id` FROM `#__toes_entry_participates_in_congress` AS `pc`)');
		$query->where('`estat`.`entry_status` IN ("Confirmed", "Confirmed & Paid")');

		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		$entries = $db->loadResult();

		return $entries;
	}

	public static function getClubOfficials($club_id) {
		$db = JFactory::getDbo();
	
		$query = $db->getQuery(true);
	
		$query->select('cu.name AS co_name, cu.email AS co_email');
		$query->from('#__toes_club_official AS co');
		$query->join('LEFT', '#__users AS cu ON cu.id = co.user');
	
		$query->where('co.club =' . $club_id);
	
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public static function replaceJudgeBookDivisionNames($text) {
		$replace_array = array(
				'Traditional' => 'TRAD',
				'Pointed' => 'POINTED',
				'Sepia' => 'SEPIA',
				'Mink' => 'MINK',
				'Solid & White' => 'SOLID & WH',
				'Tortie & White' => 'TORTIE & WH',
				'Tabby & White' => 'TABBY & WH',
				'Silver/Smoke & White' => 'SILV/SMOKE & WH',
				'Silver/Smoke' => 'SILV/SMOKE',
				'Tabby' => 'TABBY',
				'Tortie' => 'TORTIE',
				'Solid' => 'SOLID'
		);
		
		foreach ($replace_array as $key => $value)
		{
			$text = str_replace($key, $value, $text);
		}
		return $text;
	}

	public static function getCountries(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`id`, `name`, `alpha_2`");
		$query->from("`#__toes_country`");
	
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
				
	}
	
	public static function getCountryDetails($country_id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`id`, `name`, `alpha_2`");
		$query->from("`#__toes_country`");
		$query->where('`id` = '.$country_id);
	
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObject();
				
	}

	public static function getStates($country_id = 0){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`id`, `name`, `abbreviation`, `country_id`");
		$query->from("`#__toes_states_per_country`");
		
		if($country_id) {
			$query->where('`country_id` = '.$country_id);
		}
	
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
				
	}

	public static function getStateDetails($state_id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`id`, `name`, `abbreviation`, `country_id`");
		$query->from("`#__toes_states_per_country`");
		$query->where('`id` = '.$state_id);
	
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObject();
				
	}
	
	public static function getCities($state_id = 0){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`id`, `name`, `state_id`");
		$query->from("`#__toes_cities_per_state`");
		
		if($state_id) {
			$query->where('`state_id` = '.$state_id);
		}
	
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObjectList();
				
	}
	
	/*public static function getCityDetails($city_id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`id`, `name`, `state_id`, `country_id`");
		$query->from("`#__toes_cities_per_state`");
		$query->where('`id` = '.$city_id);
	
		//echo nl2br(str_replace('#__', 'j35_', $query));
		$db->setQuery($query);
		return $db->loadObject();
	}*/
	
	public static function getEntryViewQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		//,  `cntry`.`name` AS `address_country`,`city`.`name` AS `address_city`, `state`.`name` AS `address_state`, 
		$query->select("`e`.`entry_id`, `e`.`summary`,`es`.`summary_user`, `u`.`email`,`cprof`.`firstname`, `cprof`.`lastname`, `cprof`.`cb_phonenumber` AS `phonenumber`,"
			. "`es`.`summary_benching_request`,`es`.`summary_grooming_space`, `es`.`summary_single_cages`, `es`.`summary_double_cages`, `es`.`summary_personal_cages`, "
			. "`es`.`summary_remarks`, `es`.`summary_total_fees`, `es`.`summary_fees_paid`, `e`.`status`, `estat`.`entry_status`, `e`.`entry_participates_AM`, `e`.`entry_participates_PM`, "
			. "`e`.`cat`, `e`.`show_day`, `e`.`copy_cat_name`, `e`.`copy_cat_prefix`,  `pfx`.`cat_prefix_abbreviation`,  `pfx`.`cat_prefix`, `e`.`copy_cat_title`,  `ttl`.`cat_title_abbreviation`, "
			. "`ttl`.`cat_title`, `e`.`copy_cat_suffix`,  `sfx`.`cat_suffix_abbreviation`,  `sfx`.`cat_suffix`, `e`.`entry_age_years` AS `age_years`,`e`.`entry_age_months` AS `age_months`, "
			. "`e`.`copy_cat_breed`, `b`.`breed_abbreviation`,  `b`.`breed_name`, `b`.`breed_hair_length`,  `bs`.`breed_status`,  `e`.`copy_cat_hair_length`, `hl`.`cat_hair_length_abbreviation`, "
			. "`e`.`copy_cat_category`, `ctg`.`category`, `e`.`copy_cat_division`, `dvs`.`division_name`, `e`.`copy_cat_color`, `clr`.`color_name`, `e`.`copy_cat_date_of_birth`,"
			. "`e`.`copy_cat_registration_number`, `e`.`copy_cat_gender`, `gdr`.`gender_short_name`, `gdr`.`gender_name`, `e`.`copy_cat_id_chip_number`, `e`.`copy_cat_new_trait`, "
			. "`e`.`copy_cat_sire_name`, `e`.`copy_cat_dam_name`, `e`.`copy_cat_breeder_name`, `e`.`copy_cat_owner_name`, `e`.`copy_cat_lessee_name`, `e`.`copy_cat_agent_name`, "
			. "`e`.`copy_cat_competitive_region`,  `rgn`.`competitive_region_abbreviation`, `rgn`.`competitive_region_name`, `e`.`exhibition_only`, `e`.`for_sale`, `e`.`copy_cat_sire`, "
			. "`e`.`copy_cat_dam`, `e`.`copy_cat_breeder`, `e`.`copy_cat_owner`, `sd`.`show_day_id`, `sd`.`show_day_show`, `sd`.`show_day_date`, `s`.`show_id`, `s`.`show_start_date`, "
			. "`s`.`show_end_date`, `s`.`show_venue`, `sv`.`venue_name`, `va`.`address_line_1`, `va`.`address_line_2`, `va`.`address_line_3`, "
			. "`va`.`address_zip_code`,  `s`.`show_flyer`, `s`.`show_motto`, `s`.`show_format` as `show_format_id`, `sf`.`show_format`, `s`.`show_published`, "
			. "`s`.`show_status` as `show_status_id`, `ss`.`show_status`, `e`.`late_entry` , `e`.`catalog_number`, `e`.`entry_date_created` ");
		
		$query->select("(
							(`e`.`copy_cat_name` = `cat`.`cat_name`) AND
							(`e`.`copy_cat_prefix` = `cat`.`cat_prefix`) AND
							(`e`.`copy_cat_title` = `cat`.`cat_title`) AND
							(`e`.`copy_cat_suffix` = `cat`.`cat_suffix`) AND
							(`e`.`copy_cat_sire_name`= `cat`.`cat_sire`) AND
							(`e`.`copy_cat_dam_name` = `cat_dam`) AND
							(`e`.`copy_cat_breeder_name` = `cat`.`cat_breeder`) AND
							(`e`.`copy_cat_owner_name`= `cat`.`cat_owner`) AND
							(`e`.`copy_cat_lessee_name` = `cat`.`cat_lessee`) 
						) AS `minor_differences`");
						
		$query->select("(
							(`e`.`copy_cat_breed` = `cat`.`cat_breed`) AND
							(`e`.`copy_cat_category`= `cat`.`cat_category`) AND
							(`e`.`copy_cat_division`=`cat`.`cat_division`) AND
							(`e`.`copy_cat_color` = `cat`.`cat_color`) AND
							(`e`.`copy_cat_hair_length` = `cat`.`cat_hair_length`) AND
							(`e`.`copy_cat_date_of_birth`=`cat`.`cat_date_of_birth`) AND
							(`e`.`copy_cat_gender` = `cat`.`cat_gender`) AND
							(`e`.`copy_cat_registration_number`) AND
							(`e`.`copy_cat_new_trait` = `cat`.`cat_new_trait`) AND
							(`e`.`copy_cat_competitive_region` = `cat`.`cat_competitive_region`) 
						) AS `major_differences`");
		
		$query->select("IF( (`gdr`.`gender_name` = 'Female Spay') OR (`gdr`.`gender_name` = 'Male Neuter'), TRUE, FALSE) AS `is_alter`");
		$query->select("IF (`b`.`breed_name` LIKE 'Household%', TRUE, FALSE) AS `is_HHP`");
		
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,false,true) AS is_kitten");
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,true,false) AS is_adult");
		
		$query->select('e.entry_show_class,`sc`.`show_class` AS `Show_Class`');
				
		$query->from("`#__toes_entry` AS `e` WITH INDEX(entry_id)");
		$query->join("left", "`#__toes_summary` AS `es` ON (`e`.`summary` = `es`.`summary_id`)");
		$query->join("left", "`#__users`  AS `u` ON (`es`.`summary_user` = `u`.`id`)");
		$query->join("left", "`#__comprofiler`  AS `cprof` ON (`es`.`summary_user` = `cprof`.`user_id`)");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_breed` AS `b` ON (`e`.`copy_cat_breed` = `b`.`breed_id`)");
		$query->join("left", "`#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND `show_day_date` BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)");
		$query->join("left", "`#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)");
		$query->join("left", "`#__toes_cat_hair_length` AS `hl` ON (`e`.`copy_cat_hair_length` = `hl`.`cat_hair_length_id`)");
		$query->join("left", "`#__toes_category` AS `ctg` ON (`e`.`copy_cat_category` = `ctg`.`category_id`)");
		$query->join("left", "`#__toes_division` AS `dvs` ON (`e`.`copy_cat_division` = `dvs`.`division_id`)");
		$query->join("left", "`#__toes_color` AS `clr` ON (`e`.`copy_cat_color` = `clr`.`color_id`)");
		$query->join("left", "`#__toes_cat_gender` AS `gdr` ON (`e`.`copy_cat_gender` = `gdr`.`gender_id`)");
		$query->join("left", "`#__toes_cat_prefix` AS `pfx` ON (`e`.`copy_cat_prefix` = `pfx`.`cat_prefix_id`)");
		$query->join("left", "`#__toes_cat_title` AS `ttl` ON (`e`.`copy_cat_title` = `ttl`.`cat_title_id`)");
		$query->join("left", "`#__toes_cat_suffix` AS `sfx` ON (`e`.`copy_cat_suffix` = `sfx`.`cat_suffix_id`)");
		$query->join("left", "`#__toes_competitive_region` AS `rgn` ON (`e`.`copy_cat_competitive_region` = `rgn`.`competitive_region_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_format` AS `sf` ON (`s`.`show_format` = `sf`.`show_format_id`)");
		$query->join("left", "`#__toes_show_status` AS `ss` ON (`s`.`show_status` = `ss`.`show_status_id`)");
		$query->join("left", "`#__toes_cat` AS `cat` ON (`e`.`cat` = `cat`.`cat_id`)");
		$query->join("left", "`#__toes_show_class` AS `sc` ON `e`.`entry_show_class` = `sc`.`show_class_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		/*
		$query->order("`Show_Class` ASC");
		$query->order("`breed_name` ASC");
		$query->order("`copy_cat_category` ASC");
		$query->order("`copy_cat_division` ASC");
		$query->order("`copy_cat_color` ASC");
		$query->order("`cat` ASC");
		*/
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getEntryFullViewQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		//`cntry`.`name` AS `address_country`,, `city`.`name` AS `address_city`, `state`.`name` AS `address_state`
		$query->select("`e`.`entry_id`, `e`.`summary`,`es`.`summary_user`, `u`.`email`, `cprof`.`firstname`, `cprof`.`lastname`, `cprof`.`cb_phonenumber` AS `phonenumber`,"
			. "`es`.`summary_benching_request`,`es`.`summary_grooming_space`, `es`.`summary_single_cages`, `es`.`summary_double_cages`, `es`.`summary_personal_cages`, "
			. "`es`.`summary_remarks`, `es`.`summary_total_fees`, `es`.`summary_fees_paid`, `e`.`status`, `estat`.`entry_status`, `e`.`entry_participates_AM`, `e`.`entry_participates_PM`, "
			. "`e`.`cat`, `e`.`show_day`, `e`.`copy_cat_name`, `e`.`copy_cat_prefix`,  `pfx`.`cat_prefix_abbreviation`,  `pfx`.`cat_prefix`, `e`.`copy_cat_title`,  `ttl`.`cat_title_abbreviation`, "
			. "`ttl`.`cat_title`, `e`.`copy_cat_suffix`,  `sfx`.`cat_suffix_abbreviation`,  `sfx`.`cat_suffix`, `e`.`entry_age_years` AS `age_years`,`e`.`entry_age_months` AS `age_months`, "
			. "`e`.`copy_cat_breed`, `b`.`breed_abbreviation`,  `b`.`breed_name`, `b`.`breed_hair_length`,  `bs`.`breed_status`,  `e`.`copy_cat_hair_length`, `hl`.`cat_hair_length_abbreviation`, "
			. "`e`.`copy_cat_category`, `ctg`.`category`, `e`.`copy_cat_division`, `dvs`.`division_name`, `e`.`copy_cat_color`, `clr`.`color_name`, `e`.`copy_cat_date_of_birth`,"
			. "`e`.`copy_cat_registration_number`, `e`.`copy_cat_gender`, `gdr`.`gender_short_name`, `gdr`.`gender_name`, `e`.`copy_cat_id_chip_number`, `e`.`copy_cat_new_trait`, "
			. "`e`.`copy_cat_sire_name`, `e`.`copy_cat_dam_name`, `e`.`copy_cat_breeder_name`, `e`.`copy_cat_owner_name`, `e`.`copy_cat_lessee_name`, `e`.`copy_cat_agent_name`, "
			. "`e`.`copy_cat_competitive_region`,  `rgn`.`competitive_region_abbreviation`, `rgn`.`competitive_region_name`, `e`.`exhibition_only`, `e`.`for_sale`, `e`.`copy_cat_sire`, "
			. "`e`.`copy_cat_dam`, `e`.`copy_cat_breeder`, `e`.`copy_cat_owner`, `sd`.`show_day_id`, `sd`.`show_day_show`, `sd`.`show_day_date`, `s`.`show_id`, `s`.`show_start_date`, "
			. "`s`.`show_end_date`, `s`.`show_venue`, `sv`.`venue_name`, `va`.`address_line_1`, `va`.`address_line_2`, `va`.`address_line_3`, "
			. "`va`.`address_zip_code`,  `s`.`show_flyer`, `s`.`show_motto`, `s`.`show_format` as `show_format_id`, `sf`.`show_format`, `s`.`show_published`, "
			. "`s`.`show_status` as `show_status_id`, `ss`.`show_status`, `e`.`late_entry` , `e`.`catalog_number`, `e`.`entry_date_created` ");
		
		$query->select("(
							(`e`.`copy_cat_name` = `cat`.`cat_name`) AND
							(`e`.`copy_cat_prefix` = `cat`.`cat_prefix`) AND
							(`e`.`copy_cat_title` = `cat`.`cat_title`) AND
							(`e`.`copy_cat_suffix` = `cat`.`cat_suffix`) AND
							(`e`.`copy_cat_sire_name`= `cat`.`cat_sire`) AND
							(`e`.`copy_cat_dam_name` = `cat_dam`) AND
							(`e`.`copy_cat_breeder_name` = `cat`.`cat_breeder`) AND
							(`e`.`copy_cat_owner_name`= `cat`.`cat_owner`) AND
							(`e`.`copy_cat_lessee_name` = `cat`.`cat_lessee`) 
						) AS `minor_differences`");
						
		$query->select("(
							(`e`.`copy_cat_breed` = `cat`.`cat_breed`) AND
							(`e`.`copy_cat_category`= `cat`.`cat_category`) AND
							(`e`.`copy_cat_division`=`cat`.`cat_division`) AND
							(`e`.`copy_cat_color` = `cat`.`cat_color`) AND
							(`e`.`copy_cat_hair_length` = `cat`.`cat_hair_length`) AND
							(`e`.`copy_cat_date_of_birth`=`cat`.`cat_date_of_birth`) AND
							(`e`.`copy_cat_gender` = `cat`.`cat_gender`) AND
							(`e`.`copy_cat_registration_number`) AND
							(`e`.`copy_cat_new_trait` = `cat`.`cat_new_trait`) AND
							(`e`.`copy_cat_competitive_region` = `cat`.`cat_competitive_region`) 
						) AS `major_differences`");
		
		$query->select("IF( (`gdr`.`gender_name` = 'Female Spay') OR (`gdr`.`gender_name` = 'Male Neuter'), TRUE, FALSE) AS `is_alter`");
		$query->select("IF (`b`.`breed_name` LIKE 'Household%', TRUE, FALSE) AS `is_HHP`");
		
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,false,true) AS is_kitten");
		$query->select("IF(10*`e`.`entry_age_years` + `e`.`entry_age_months` >= 8,true,false) AS is_adult");
		
		$query->select('e.entry_show_class,`sc`.`show_class` AS `Show_Class`');
				
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_summary` AS `es` ON (`e`.`summary` = `es`.`summary_id`)");
		$query->join("left", "`#__users`  AS `u` ON (`es`.`summary_user` = `u`.`id`)");
		$query->join("left", "`#__comprofiler`  AS `cprof` ON (`es`.`summary_user` = `cprof`.`user_id`)");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_breed` AS `b` ON (`e`.`copy_cat_breed` = `b`.`breed_id`)");
		$query->join("left", "`#__toes_breed_has_status` as bhs ON (`bhs`.`breed_has_status_breed` = `b`.`breed_id` AND `show_day_date` BETWEEN `bhs`.`breed_has_status_since` AND `bhs`.`breed_has_status_until`)");
		$query->join("left", "`#__toes_breed_status` AS `bs` ON (`bhs`.`breed_has_status_status` = `bs`.`breed_status_id`)");
		$query->join("left", "`#__toes_cat_hair_length` AS `hl` ON (`e`.`copy_cat_hair_length` = `hl`.`cat_hair_length_id`)");
		$query->join("left", "`#__toes_category` AS `ctg` ON (`e`.`copy_cat_category` = `ctg`.`category_id`)");
		$query->join("left", "`#__toes_division` AS `dvs` ON (`e`.`copy_cat_division` = `dvs`.`division_id`)");
		$query->join("left", "`#__toes_color` AS `clr` ON (`e`.`copy_cat_color` = `clr`.`color_id`)");
		$query->join("left", "`#__toes_cat_gender` AS `gdr` ON (`e`.`copy_cat_gender` = `gdr`.`gender_id`)");
		$query->join("left", "`#__toes_cat_prefix` AS `pfx` ON (`e`.`copy_cat_prefix` = `pfx`.`cat_prefix_id`)");
		$query->join("left", "`#__toes_cat_title` AS `ttl` ON (`e`.`copy_cat_title` = `ttl`.`cat_title_id`)");
		$query->join("left", "`#__toes_cat_suffix` AS `sfx` ON (`e`.`copy_cat_suffix` = `sfx`.`cat_suffix_id`)");
		$query->join("left", "`#__toes_competitive_region` AS `rgn` ON (`e`.`copy_cat_competitive_region` = `rgn`.`competitive_region_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_format` AS `sf` ON (`s`.`show_format` = `sf`.`show_format_id`)");
		$query->join("left", "`#__toes_show_status` AS `ss` ON (`s`.`show_status` = `ss`.`show_status_id`)");
		$query->join("left", "`#__toes_cat` AS `cat` ON (`e`.`cat` = `cat`.`cat_id`)");
		$query->join("left", "`#__toes_show_class` AS `sc` ON `e`.`entry_show_class` = `sc`.`show_class_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		$query->order("`e`.`entry_show_class` ASC");
		$query->order("`b`.`breed_name` ASC");
		$query->order("`e`.`copy_cat_category` ASC");
		$query->order("`e`.`copy_cat_division` ASC");
		$query->order("`e`.`copy_cat_color` ASC");
		$query->order("`e`.`cat` ASC");
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
		
	}
	
	public static function getCatalogNumberingbasisQuery($full_entry_view = '`#__toes_view_full_entry`', $where = array()){
		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true);
		
		$query->select("DISTINCT `e`.`show_id` , `e`.`show_day`, `e`.`entry_id`, `e`.`cat` , `sc`.`show_class` ,  `e`.`breed_name`");
		$query->select("CONCAT(`category`,' ',`division_name`, ' Division') AS `catalog_division`,`e`.`color_name` , `e`.`catalog_number`");
		$query->select("CONCAT_WS(' ', TRIM(CONCAT(IF(`e`.`cat_prefix_abbreviation`=NULL,'',CONCAT(`e`.`cat_prefix_abbreviation`,' ')), IF(`e`.`cat_title_abbreviation`,'',CONCAT(`e`.`cat_title_abbreviation`,' ')),IF(`e`.`cat_suffix_abbreviation`,'',`e`.`cat_suffix_abbreviation`))),`e`.`copy_cat_name`) AS `catalog_cat_name` ");
		$query->select("CONCAT(`age_years`,'.',`age_months`,' ',`gender_short_name`) AS `catalog_age_and_gender`");
		$query->select("IF(`copy_cat_registration_number`=NULL,'',`copy_cat_registration_number`) AS `catalog_registration_number`, `e`.`copy_cat_id_chip_number` AS `catalog_id_chip_number`");
		$query->select("UPPER(DATE_FORMAT(`copy_cat_date_of_birth`,'%b %d, %Y')) AS `catalog_birthdate`");
		$query->select("TRIM( CONCAT(IF(`e`.`cat_prefix_abbreviation`=NULL,'',CONCAT(`e`.`cat_prefix_abbreviation`,' ')), IF(`e`.`cat_title_abbreviation`,'',CONCAT(`e`.`cat_title_abbreviation`,' ')),IF(`e`.`cat_suffix_abbreviation`,'',`e`.`cat_suffix_abbreviation`)) ) AS `catalog_awards` ");
		$query->select("`e`.`copy_cat_sire_name` AS `catalog_sire`, `e`.`copy_cat_dam_name` AS `catalog_dam`");
		$query->select("IF(`e`.`copy_cat_breeder_name`=`e`.`copy_cat_owner_name`,CONCAT('B/O: ',`e`.`copy_cat_breeder_name`),CONCAT('B: ', `e`.`copy_cat_breeder_name`)) AS `catalog_breeder`");
		$query->select("IF(`e`.`copy_cat_breeder_name`=`e`.`copy_cat_owner_name`,NULL,CONCAT('O: ',`e`.`copy_cat_owner_name`)) AS `catalog_owner`");
		$query->select("IF(`e`.`copy_cat_lessee_name`=NULL,NULL,IF(`e`.`copy_cat_lessee_name`='',NULL,CONCAT('L: ',`e`.`copy_cat_lessee_name`))) AS `catalog_lessee`");
		$query->select("IF(`e`.`copy_cat_agent_name`=NULL,NULL,IF(`e`.`copy_cat_agent_name`='',NULL,CONCAT('A: ',`e`.`copy_cat_agent_name`))) AS `catalog_agent`");
		$query->select("`e`.`competitive_region_abbreviation` AS `catalog_region`, `e`.`cat_hair_length_abbreviation` AS `hair_length_abbreviation`, `e`.`summary_user`");
		$query->select("`e`.`firstname` , `e`.`lastname` , `c`.`cb_address1` , `c`.`cb_address2` , `c`.`cb_address3` , `c`.`cb_city` , `c`.`cb_zip` , `c`.`cb_state` , `c`.`cb_country`");
		$query->select("`e`.`status` , `e`.`entry_status` , `e`.`copy_cat_category` , `e`.`copy_cat_division`, `e`.`copy_cat_color`");
		$query->select("`e`.`late_entry` , `e`.`address_city`, `e`.`address_state`,`e`.`address_country`, `club`.`club_name`, `club`.`club_abbreviation` ");
		$query->select("`e`.`for_sale`, `e`.`age_years`, `e`.`age_months`, `e`.`gender_short_name` AS `cat_gender_abbreviation`, `e`.`breed_status`, `e`.`entry_participates_AM`, `e`.`entry_participates_PM`");

		$query->from($full_entry_view." AS e");
		
		$query->join("left","`#__toes_show_class` AS `sc` ON `e`.`Show_Class` = `sc`.`show_class`");
		$query->join("left","`#__comprofiler` AS `c` ON `c`.`user_id` = `e`.`summary_user`");
		$query->join("left","`#__toes_club_organizes_show` AS `cos` ON `cos`.`show` = `e`.`show_id`");
		$query->join("left","`#__toes_club` AS `club` ON `club`.`club_id` = `cos`.`club`");
		
		$query->where("(`sc`.`show_class_id` >0)");
		$query->where("(`sc`.`show_class_id` <= 17)");
		$query->where("( (`e`.`entry_status` = 'Accepted') OR(`e`.`entry_status` = 'Confirmed') OR (`e`.`entry_status` = 'Confirmed & Paid') )");
		
		$query->order("`sc`.`show_class_id` ASC");
		$query->order("`e`.`breed_name` ASC");
		$query->order("`e`.`copy_cat_category` ASC");
		$query->order("`e`.`copy_cat_division` ASC");
		$query->order("`e`.`copy_cat_color` ASC");
		$query->order("`e`.`cat` ASC");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}

		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getShowSummariesQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("`sc`.`show_class`, `s`.`show_id`, `sd`.`show_day_id`, `sd`.`show_day_date`, COUNT(`e`.`entry_id`) AS `cat_count`, `c`.`club_name` , `c`.`club_abbreviation`");
		//$query->select("concat_ws(' ',`city`.`name`,`state`.`name`,`cntry`.`name`) AS `Show_location`");
		$query->select("if(
							(date_format(`s`.`show_start_date`,'%Y') = date_format(`s`.`show_end_date`,'%Y')),
							if((date_format(`s`.`show_start_date`,'%b') = date_format(`s`.`show_end_date`,'%b')),
								concat(date_format(`s`.`show_start_date`,'%e'),'-',date_format(`s`.`show_end_date`,'%e'),' ',date_format(`s`.`show_start_date`,'%b'),' ',date_format(`s`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b'),date_format(`s`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b %Y'),date_format(`s`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");
		
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
	//	$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_class` AS `sc` ON (`e`.`entry_show_class` = `sc`.`show_class_id`)");
		$query->join("left", "`#__toes_club_organizes_show` AS `cos` ON (`cos`.`show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_club` AS `c` ON (`c`.`club_id` = `cos`.`club`)");

		$query->where("((`estat`.`entry_status` = 'New') OR (`estat`.`entry_status` = 'Accepted') OR (`estat`.`entry_status` = 'Confirmed') OR (`estat`.`entry_status` = 'Confirmed & Paid'))");
		$query->group("`e`.`show_day`,`sc`.`show_class`");
		$query->order("`sc`.`show_class` ASC, `e`.`show_day` ASC");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
	
		return $query;
	}

	public static function getShowSummariesAMSessionQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("`sc`.`show_class`, `s`.`show_id`, `sd`.`show_day_id`, `sd`.`show_day_date`, COUNT(`e`.`entry_id`) AS `cat_count`, `c`.`club_name` , `c`.`club_abbreviation`");
		//$query->select("concat_ws(' ',`city`.`name`,`state`.`name`,`cntry`.`name`) AS `Show_location`");
		$query->select("if(
							(date_format(`s`.`show_start_date`,'%Y') = date_format(`s`.`show_end_date`,'%Y')),
							if((date_format(`s`.`show_start_date`,'%b') = date_format(`s`.`show_end_date`,'%b')),
								concat(date_format(`s`.`show_start_date`,'%e'),'-',date_format(`s`.`show_end_date`,'%e'),' ',date_format(`s`.`show_start_date`,'%b'),' ',date_format(`s`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b'),date_format(`s`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b %Y'),date_format(`s`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");
		
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_class` AS `sc` ON (`e`.`entry_show_class` = `sc`.`show_class_id`)");
		$query->join("left", "`#__toes_club_organizes_show` AS `cos` ON (`cos`.`show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_club` AS `c` ON (`c`.`club_id` = `cos`.`club`)");

		$query->where("`e`.`entry_participates_AM` = 1");
		$query->where("((`estat`.`entry_status` = 'New') OR (`estat`.`entry_status` = 'Accepted') OR (`estat`.`entry_status` = 'Confirmed') OR (`estat`.`entry_status` = 'Confirmed & Paid'))");
		$query->group("`sc`.`show_class`, `e`.`show_day`");
		$query->order("`sc`.`show_class` ASC, `e`.`show_day` ASC");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getShowSummariesPMSessionQuery($where = array()) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("`sc`.`show_class`, `s`.`show_id`, `sd`.`show_day_id`, `sd`.`show_day_date`, COUNT(`e`.`entry_id`) AS `cat_count`, `c`.`club_name` , `c`.`club_abbreviation`");
		//$query->select("concat_ws(' ',`city`.`name`,`state`.`name`,`cntry`.`name`) AS `Show_location`");
		$query->select("if(
							(date_format(`s`.`show_start_date`,'%Y') = date_format(`s`.`show_end_date`,'%Y')),
							if((date_format(`s`.`show_start_date`,'%b') = date_format(`s`.`show_end_date`,'%b')),
								concat(date_format(`s`.`show_start_date`,'%e'),'-',date_format(`s`.`show_end_date`,'%e'),' ',date_format(`s`.`show_start_date`,'%b'),' ',date_format(`s`.`show_start_date`,'%Y')),
								concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b'),date_format(`s`.`show_end_date`,'%e %b %Y'))
							),
							concat_ws(' - ',date_format(`s`.`show_start_date`,'%e %b %Y'),date_format(`s`.`show_end_date`,'%e %b %Y'))
						) AS `show_dates`");
		
		$query->from("`#__toes_entry` AS `e`");
		$query->join("left", "`#__toes_entry_status` AS `estat` ON (`e`.`status` = `estat`.`entry_status_id`)");
		$query->join("left", "`#__toes_show_day` AS `sd` ON (`e`.`show_day` = `sd`.`show_day_id`)");
		$query->join("left", "`#__toes_show` AS `s` ON (`sd`.`show_day_show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_venue` AS `sv` ON (`s`.`show_venue` = `sv`.`venue_id`)");
		$query->join("left", "`#__toes_address` AS `va` ON (`sv`.`venue_address` = `va`.`address_id`)");
		//$query->join("left", "`#__toes_country` AS `cntry` ON `cntry`.`id` = `va`.`address_country`");
		//$query->join("left", "`#__toes_states_per_country` AS `state` ON `state`.`id` = `va`.`address_state`");
		//$query->join("left", "`#__toes_cities_per_state` AS `city` ON `city`.`id` = `va`.`address_city`");
		$query->join("left", "`#__toes_show_class` AS `sc` ON (`e`.`entry_show_class` = `sc`.`show_class_id`)");
		$query->join("left", "`#__toes_club_organizes_show` AS `cos` ON (`cos`.`show` = `s`.`show_id`)");
		$query->join("left", "`#__toes_club` AS `c` ON (`c`.`club_id` = `cos`.`club`)");

		$query->where("`e`.`entry_participates_PM` = 1");
		$query->where("((`estat`.`entry_status` = 'New') OR (`estat`.`entry_status` = 'Accepted') OR (`estat`.`entry_status` = 'Confirmed') OR (`estat`.`entry_status` = 'Confirmed & Paid'))");
		$query->group("`sc`.`show_class`, `e`.`show_day`");
		$query->order("`sc`.`show_class` ASC, `e`.`show_day` ASC");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
	
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getCongressSummaryQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`ring_id` , `ring_show_day` , `ring_show` , `ring_number` , `ring_name` , COUNT( `entry_id` ) AS `Count`");
		$query->from("`#__toes_ring`");
		$query->join("left","`#__toes_entry_participates_in_congress` ON ( `ring_id` = `congress_id` )");
		$query->where("`ring_format` = 3");
		$query->group("`ring_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}

	public static function getCongressSummaryAMSessionQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`r`.`ring_id` AS `ring_id`,`r`.`ring_show_day` AS `ring_show_day`,`r`.`ring_show` AS `ring_show`,`r`.`ring_number` AS `ring_number`");
		$query->select("`r`.`ring_name` AS `ring_name`,count(distinct `p`.`entry_id`) AS `Count`");
		$query->from("`#__toes_ring` AS `r`");
		$query->join("left","`#__toes_entry_participates_in_congress` AS `p` ON ( `r`.`ring_id` = `p`.`congress_id` )");
		$query->join("left","`#__toes_entry` AS `e` ON (`p`.`entry_id` = `e`.`entry_id`)");
		$query->where("`r`.`ring_format` = 3");
		$query->where("`e`.`entry_participates_AM` = 1");
		$query->group("`r`.`ring_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
	
	public static function getCongressSummaryPMSessionQuery($where = array()){
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select("`r`.`ring_id` AS `ring_id`,`r`.`ring_show_day` AS `ring_show_day`,`r`.`ring_show` AS `ring_show`,`r`.`ring_number` AS `ring_number`");
		$query->select("`r`.`ring_name` AS `ring_name`,count(distinct `p`.`entry_id`) AS `Count`");
		$query->from("`#__toes_ring` AS `r`");
		$query->join("left","`#__toes_entry_participates_in_congress` AS `p` ON ( `r`.`ring_id` = `p`.`congress_id` )");
		$query->join("left","`#__toes_entry` AS `e` ON (`p`.`entry_id` = `e`.`entry_id`)");
		$query->where("`r`.`ring_format` = 3");
		$query->where("`e`.`entry_participates_PM` = 1");
		$query->group("`r`.`ring_id`");
		
		if($where){
			foreach ($where as $w)
			{
				$query->where($w);
			}
		}
		
		//echo str_replace('#__', 'j35_', nl2br($query));
		return $query;
	}
			
	public static function getCompetativeClassConditionsForNumbering($show_class){
		$condition = '';
		switch ($show_class) {
			case 'LH_Kitten':
				$condition = "(`show_class`='LH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Championship')
                                                           )";
				break;
			case 'SH_Kitten':
				$condition = "(`show_class`='SH Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Championship') 
                                                           )";
				break;
			case 'LH_Cat':
				$condition = "(`show_class`='LH Cat') OR ((`show_class`='Ex Only') AND 
				(`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
								LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
								WHERE `r`.`ring_show_day` = `show_day`)
				) AND
				(`hair_length_abbreviation` = 'LH') AND
				((`age_years` >0) OR(`age_months`>=8)) AND
				((`cat_gender_abbreviation` = 'M') OR (`cat_gender_abbreviation` = 'F')) AND
				(`breed_status` = 'Championship')
			   )";
				break;
			case 'SH_Cat':
				$condition = "(`show_class`='SH Cat') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'M') OR (`cat_gender_abbreviation` = 'F')) AND
                                                            (`breed_status` = 'Championship')
                                                           )";
				break;
			case 'LH_Alter':
				$condition = "(`show_class`='LH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Championship')
                                                           )";
				break;
			case 'SH_Alter':
				$condition = "(`show_class`='SH Alter') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Championship')
                                                           )";
				break;
			case 'LH_HHP_Kitten':
				$condition = "(`show_class`='LH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )";
				break;
			case 'LH_HHP':
				$condition = "(`show_class`='LH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'LH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )";
				break;
			case 'SH_HHP_Kitten':
				$condition = "(`show_class`='SH HHP Kitten') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            (`age_years`=0) AND
                                                            (`age_months`<8) AND
                                                            (`age_months`>=4) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )";
				break;
			case 'SH_HHP':
				$condition = "(`show_class`='SH HHP') OR ((`show_class`='Ex Only') AND 
                                                            (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`)
                                                            ) AND
                                                            (`hair_length_abbreviation` = 'SH') AND
                                                            ((`age_years` >0) OR(`age_months`>=8)) AND
                                                            ((`cat_gender_abbreviation` = 'N') OR (`cat_gender_abbreviation` = 'S')) AND
                                                            (`breed_status` = 'Non Championship')
                                                           )";
				break;
			case 'LH_PNB':
				$condition = "(`show_class`='LH PNB')";
				break;
			case 'SH_PNB':
				$condition = "(`show_class`='SH PNB')";
				break;
			case 'LH_ANB':
				$condition = "(`show_class`='LH ANB')";
				break;
			case 'SH_ANB':
				$condition = "(`show_class`='SH ANB')";
				break;
			case 'LH_NT':
				$condition = "(`show_class`='LH NT')";
				break;
			case 'SH_NT':
				$condition = "(`show_class`='SH NT')";
				break;
			case 'Exh_Only':
				$condition = "(`show_class`='Ex Only') AND ( NOT `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`))";
				break;
			case 'for_sale':
				$condition = "(`show_class`='Ex Only') AND ( `for_sale`) AND NOT (`entry_id` IN (SELECT `entry_id` FROM `#__toes_entry_participates_in_congress` 
                                                                            LEFT JOIN `#__toes_ring` AS `r` ON `congress_id` = `r`.`ring_id`
                                                                            WHERE `r`.`ring_show_day` = `show_day`))";
				break;
		}
		return $condition;
	}

	public static function addOrdinalNumberSuffix($num) {
		if (!in_array(($num % 100),array(11,12,13))){
		  switch ($num % 10) {
			// Handle 1st, 2nd, 3rd
			case 1:  return $num.'<sup>st</sup>';
			case 2:  return $num.'<sup>nd</sup>';
			case 3:  return $num.'<sup>rd</sup>';
		  }
		}
		return $num.'<sup>th</sup>';
	  }
	
}
