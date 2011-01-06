<?php
/***************************************************************************
																./log.php
															-------------------
		begin                : July 4 2004
		copyright            : (C) 2004 The OpenCaching Group
		forum contact at     : http://www.opencaching.com/phpBB2

	***************************************************************************/

/***************************************************************************
	*
	*   This program is free software; you can redistribute it and/or modify
	*   it under the terms of the GNU General Public License as published by
	*   the Free Software Foundation; either version 2 of the License, or
	*   (at your option) any later version.
	*
	***************************************************************************/

/****************************************************************************

   Unicode Reminder メモ

	 log a cache visit

	 used template(s): log

	 GET Parameter: cacheid

 ****************************************************************************/

	//prepare the templates and include all neccessary
	require_once('./lib/common.inc.php');
	require($stylepath.'/smilies.inc.php');

	$no_tpl_build = false;
	//Preprocessing
	if ($error == false)
	{
		//cacheid
		$cache_id = 0;
		if (isset($_REQUEST['cacheid']))
		{
			$cache_id = $_REQUEST['cacheid'];
		}

		if ($usr === false)
		{
			$tplname = 'login';

			tpl_set_var('username', '');
			tpl_set_var('target', 'log.php?cacheid=' . urlencode($cache_id));
			tpl_set_var('message', $login_required);
			tpl_set_var('message_start', '');
			tpl_set_var('message_end', '');
		}
		else
		{
			//set here the template to process
			$tplname = 'log_cache';

			require($stylepath . '/log_cache.inc.php');
			require($stylepath.'/rating.inc.php');

			$cachename = '';
			if ($cache_id != 0)
			{
				//get cachename
				$rs = sql("SELECT `caches`.`name`, `caches`.`user_id`, `caches`.`logpw`, `caches`.`wp_gc`, `caches`.`wp_nc`, `caches`.`type`, `caches`.`status` FROM `caches` INNER JOIN `cache_status` ON `caches`.`status`=`cache_status`.`id` WHERE (`cache_status`.`allow_user_log`=1 OR `caches`.`user_id`='&1') AND `caches`.`cache_id`='&2'", $usr['userid'], $cache_id);

				if (mysql_num_rows($rs) == 0)
				{
					$cache_id = 0;
				}
				else
				{
					$record = sql_fetch_array($rs);

					// only the owner is allowed to make logs to not published caches
					if ($record['user_id'] == $usr['userid'] || $record['status'] != 5)
					{
						$cachename = $record['name'];
						$cache_user_id = $record['user_id'];
						$use_log_pw = (($record['logpw'] == NULL) || ($record['logpw'] == '')) ? false : true;
						if ($use_log_pw) $log_pw = $record['logpw'];
						$wp_gc = $record['wp_gc'];
						$wp_nc = $record['wp_nc'];
						$cache_type = $record['type'];
					}
					else
					{
						$cache_id = 0;
					}
				}

				sql_free_result($rs);
			}

			if ($cache_id != 0)
			{
				$all_ok = false;
				$log_text  = isset($_POST['logtext']) ? ($_POST['logtext']) : '';
				$log_type = isset($_POST['logtype']) ? ($_POST['logtype']+0) : 1;
				$log_date_day = isset($_POST['logday']) ? ($_POST['logday']+0) : date('d');
				$log_date_month = isset($_POST['logmonth']) ? ($_POST['logmonth']+0) : date('m');
				$log_date_year = isset($_POST['logyear']) ? ($_POST['logyear']+0) : date('Y');
				$top_cache = isset($_POST['rating']) ? $_POST['rating']+0 : 0;

				// check if user has exceeded his top5% limit
				$user_founds = sqlValue("SELECT IFNULL(`stat_user`.`found`, 0) FROM `user` LEFT JOIN `stat_user` ON `user`.`user_id`=`stat_user`.`user_id` WHERE `user`.`user_id`='" .  sql_escape($usr['userid']) . "'", 0);
				$user_tops = sqlValue("SELECT COUNT(`user_id`) FROM `cache_rating` WHERE `user_id`='" .  sql_escape($usr['userid']) . "'", 0);

				if (($user_founds * rating_percentage/100) < 1)
				{
					$top_cache = 0;
					$anzahl = (1 - ($user_founds * rating_percentage/100)) / (rating_percentage/100);
					if ($anzahl > 1)
					{
						$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
					}
					else
					{
						$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
					}
				}
				elseif ($user_tops < floor($user_founds * rating_percentage/100))
				{
					$rating_msg = mb_ereg_replace('{chk_sel}', '', $rating_allowed.'<br />'.$rating_stat);
					$rating_msg = mb_ereg_replace('{max}', floor($user_founds * rating_percentage/100), $rating_msg);
					$rating_msg = mb_ereg_replace('{curr}', $user_tops, $rating_msg);
				}
				else
				{
					$top_cache = 0;
					$anzahl = ($user_tops + 1 - ($user_founds * rating_percentage/100)) / (rating_percentage/100);
					if ($anzahl > 1)
					{
						$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
					}
					else
					{
						$rating_msg = mb_ereg_replace('{anzahl}', $anzahl, $rating_too_few_founds);
					}
					$rating_msg .= '<br />'.$rating_maxreached;
				}
				tpl_set_var('rating_message', mb_ereg_replace('{rating_msg}', $rating_msg, $rating_tpl));

				// descMode auslesen, falls nicht gesetzt aus dem Profil laden
				if (isset($_POST['descMode']))
					$descMode = $_POST['descMode']+0;
				else
				{
					if (sqlValue("SELECT `no_htmledit_flag` FROM `user` WHERE `user_id`='" .  sql_escape($usr['userid']) . "'", 1) == 1)
						$descMode = 1;
					else
						$descMode = 3;
				}
				if (($descMode < 1) || ($descMode > 3)) $descMode = 3;

				// fuer alte Versionen von OCProp
				if ((isset($_POST['submit']) || isset($_POST['submitform'])) && !isset($_POST['version3']))
				{
					die('Your client may be outdated!');
				}

				if ($descMode != 1)
				{
					// check input
					require_once($rootpath . 'lib/class.inputfilter.php');
					$myFilter = new InputFilter($allowedtags, $allowedattr, 0, 0, 1);
					$log_text = $myFilter->process($log_text);
				}
				else
				{
					// escape text
					$log_text = nl2br(htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'));
				}

				//validate data
				if (is_numeric($log_date_month) && is_numeric($log_date_day) && is_numeric($log_date_year))
				{
					$date_not_ok = (checkdate($log_date_month, $log_date_day, $log_date_year) == false);
					if($date_not_ok == false)
					{
						if (isset($_POST['submitform']))
						{
							if(mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year)>=mktime())
							{
								$date_not_ok = true;
							}
							else
							{
								$date_not_ok = false;
							}
						}
					}
				}
				else
				{
					$date_not_ok = true;
				}

				if ($cache_type == 6)
				{
					switch($log_type)
					{
						case 1:
						case 2:
							$logtype_not_ok = true;
							break;
						default:
							$logtype_not_ok = false;
							break;
					}
				}
				else
				{
					switch($log_type)
					{
						case 7:
						case 8:
							$logtype_not_ok = true;
							break;
						default:
							$logtype_not_ok = false;
							break;
					}
				}

				// not a found log? then ignore the rating
				if ($log_type != 1 && $log_type != 7)
				{
					$top_cache = 0;
				}

				$pw_not_ok = false;
				if (isset($_POST['submitform']))
				{
					$all_ok = ($date_not_ok == false) && ($logtype_not_ok == false);

					if (($all_ok) && ($use_log_pw) && $log_type == 1)
					{
						if (isset($_POST['log_pw']))
						{
							if (strtolower($log_pw) != strtolower($_POST['log_pw']))
							{
								$pw_not_ok = true;
								$all_ok = false;
							}
						}
						else
						{
							$pw_not_ok = true;
							$all_ok = false;
						}
					}
				}

				if (isset($_POST['submitform']) && ($all_ok == true))
				{
					$log_date = date('Y-m-d', mktime(0, 0, 0, $log_date_month, $log_date_day, $log_date_year));

					//add logentry to db
					sql("INSERT INTO `cache_logs` (`id`, `cache_id`, `user_id`, `type`, `date`, `text`, `text_html`, `text_htmledit`, `uuid`, `node`)
					         VALUES ('', '&1', '&2', '&3', '&4', '&5', '&6', '&7', UUID(), '&8')",
					         $cache_id, $usr['userid'], $log_type, $log_date, $log_text, (($descMode != 1) ? 1 : 0), (($descMode == 3) ? 1 : 0), $oc_nodeid);

					// do not use slave server for the next time ...
					db_slave_exclude();

					// update cache_status
					$rs = sql("SELECT `log_types`.`cache_status` FROM `log_types` WHERE `id`='&1'", $log_type);
					if($record = sql_fetch_array($rs))
					{
						$cache_status = $record['cache_status'];
						if($cache_status != 0)
						{
							$rs = sql("UPDATE `caches` SET `status`='&1' WHERE `cache_id`='&2'", $cache_status, $cache_id);
						}
					}
					else
					{
						die("OPS!");
					}

					// update top-list
					if ($top_cache == 1)
						sql("INSERT IGNORE INTO `cache_rating` (`user_id`, `cache_id`) VALUES('&1', '&2')", $usr['userid'], $cache_id);
					else
						sql("DELETE FROM `cache_rating` WHERE `user_id`='&1' AND `cache_id`='&2'", $usr['userid'], $cache_id);

					//call eventhandler
					require_once($rootpath . 'lib/eventhandler.inc.php');
					event_new_log($cache_id, $usr['userid']+0);

					//redirect to viewcache
					$no_tpl_build = true;
					//include('viewcache.php');
					tpl_redirect('viewcache.php?cacheid=' . $cache_id);
				}
				else
				{
					//build logtypeoptions
					$logtypeoptions = '';
					$rsLogTypes = sql("SELECT `log_types`.`id`, IFNULL(`sys_trans_text`.`text`, `log_types`.`name`) AS `name`
											         FROM `caches` 
								         INNER JOIN `cache_type` ON `caches`.`type`=`cache_type`.`id` 
								         INNER JOIN `cache_logtype` ON `cache_type`.`id`=`cache_logtype`.`cache_type_id` 
								         INNER JOIN `log_types` ON `cache_logtype`.`log_type_id`=`log_types`.`id` 
									        LEFT JOIN `sys_trans` ON `log_types`.`trans_id`=`sys_trans`.`id` 
									        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='" . sql_escape($locale) . "' 
											        WHERE `caches`.`cache_id`='" . ($cache_id+0) . "'
											     ORDER BY `log_types`.`id` ASC");
					while ($rLogTypes = sql_fetch_assoc($rsLogTypes))
					{
						$sSelected = ($rLogTypes['id'] == $log_type) ? ' selected="selected"' : '';
						$logtypeoptions .= '<option value="' . $rLogTypes['id'] . '"' . $sSelected . '>' . htmlspecialchars($rLogTypes['name'], ENT_COMPAT, 'UTF-8') . '</option>' . "\n";
					}
					sql_free_result($rsLogTypes);

					//set tpl vars
					tpl_set_var('cachename', htmlspecialchars($cachename, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('cacheid', htmlspecialchars($cache_id, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logday', htmlspecialchars($log_date_day, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logmonth', htmlspecialchars($log_date_month, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logyear', htmlspecialchars($log_date_year, ENT_COMPAT, 'UTF-8'));
					tpl_set_var('logtypeoptions', $logtypeoptions);
					tpl_set_var('reset', $reset);
					tpl_set_var('submit', $submit);
					tpl_set_var('date_message', '');

					// Text / normal HTML / HTML editor
					tpl_set_var('use_tinymce', (($descMode == 3) ? 1 : 0));

					if ($descMode == 1)
						tpl_set_var('descMode', 1);
					else if ($descMode == 2)
						tpl_set_var('descMode', 2);
					else
					{
						// TinyMCE
						$headers = tpl_get_var('htmlheaders') . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/tiny_mce_gzip.js"></script>' . "\n";
						$headers .= '<script language="javascript" type="text/javascript" src="resource2/tinymce/config/log.js.php?logid=0"></script>' . "\n";
						tpl_set_var('htmlheaders', $headers);

						tpl_set_var('descMode', 3);
					}

					if ($descMode != 1)
						tpl_set_var('logtext', htmlspecialchars($log_text, ENT_COMPAT, 'UTF-8'), true);
					else
						tpl_set_var('logtext', $log_text);

					$listed_on = array();
					if($wp_gc > "")
						$listed_on[] = '<a href="http://www.geocaching.com/seek/cache_details.aspx?wp='.$wp_gc.'"  target="_blank">geocaching.com</a> <a href="http://www.geocaching.com/seek/log.aspx?wp='.$wp_gc.'" target="_blank">(loggen)</a>';
					if($wp_nc > "")
						$listed_on[] = 'navicache.com';

					if(sizeof($listed_on))
					{
						tpl_set_var('listed_start', "");
						tpl_set_var('listed_end', "");
						tpl_set_var('listed_on', sizeof($listed_on) == 0 ? $listed_only_oc : implode(", ", $listed_on));
					}
					else
					{
					tpl_set_var('listed_start', "<!--");
					tpl_set_var('listed_end', "-->");
					}
					if ($use_log_pw == true)
					{
						if ($pw_not_ok == true)
						{
							tpl_set_var('log_pw_field', $log_pw_field_pw_not_ok);
						}
						else
						{
							tpl_set_var('log_pw_field', $log_pw_field);
						}
					}
					else
					{
						tpl_set_var('log_pw_field', '');
					}

					if ($date_not_ok == true)
					{
						tpl_set_var('date_message', $date_message);
					}

					// build smilies
					$smilies = '';
					if ($descMode != 3)
					{
						for($i=0; $i<count($smileyshow); $i++)
						{
							if($smileyshow[$i] == '1')
							{
								$tmp_smiley = $smiley_link;
								$tmp_smiley = mb_ereg_replace('{smiley_image}', $smileyimage[$i], $tmp_smiley);
								$smilies = $smilies . mb_ereg_replace('{smiley_text}', ' '.$smileytext[$i].' ', $tmp_smiley) . '&nbsp;';
							}
						}
					}
					tpl_set_var('smilies', $smilies);
				}
			}
			else
			{
				// no cache found
				$no_tpl_build = true;
			}
		}
	}

	if ($no_tpl_build == false)
	{
		//make the template and send it out
		tpl_BuildTemplate(false);
	}
?>
