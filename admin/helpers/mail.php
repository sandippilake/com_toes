<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

/**
 * TOES mail component helper.
 *
 * @package	Joomla.Administrator
 * @subpackage	com_toes
 */
class TOESMailHelper {
	
	public static function getTemplate($action, $user_id = 0)
	{
		$db = JFactory::getDbo();
		$language = JFactory::getLanguage();
		$user_lang = $language->getTag();
		
		if($user_id) {
			$db->setQuery("SELECT * FROM `#__users` WHERE id = {$user_id}");
			$user_details = $db->loadObject();
			$params = new JRegistry($user_details->params);
			$user_lang = $params->get('language', $user_lang);
		}
		
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__toes_mail_templates');
		$query->where('action_name = ' . $db->quote($action));
		$db->setQuery($query);

		//return $db->loadObject('stdClass', true, $user_lang);
		return $db->loadObject();
	}

	public static function sendMail($action, $subject, $body, $to, $to_names = null, $cc = null, $cc_names = null, $bcc = null, $bcc_names = null, $replyTo = null, $replyTo_names = null)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->select(' `acc`.* ');
		$query->from('`#__toes_smtp_accounts` AS `acc`');
		$query->join('left','`#__toes_mail_templates` AS `tmpl` ON `tmpl`.smtp_id = `acc`.smtp_id');
		$query->where('`tmpl`.`action_name` = ' . $db->quote($action));
		$db->setQuery($query);

		$smtp_account = $db->loadObject();

		$mail = JFactory::getMailer();

		if($smtp_account) {
			
			$mail->useSmtp($smtp_account->smtp_auth, $smtp_account->smtp_host, $smtp_account->smtp_user, $smtp_account->smtp_pass, $smtp_account->smtp_secure, $smtp_account->smtp_port);
			
		} else {

			$mail = JFactory::getMailer();
			$config = JFactory::getConfig();
			$fromname = $config->get('fromname');
			$fromemail = $config->get('mailfrom');

			$mail->SetFrom($fromemail, $fromname);
		}

		$mail->setSubject($subject);
		$mail->setBody($body);

		$mail->addRecipient($to, $to_names);

		if($cc) {
			$mail->addCC($cc, $cc_names);
		}

		if($bcc) {
			$mail->addBcc($bcc, $bcc_names);
		}

		if($replyTo) {
			$mail->addBcc($replyTo, $replyTo_names);
		}

		$mail->IsHTML(TRUE);

		return $mail->Send();
	}
}