<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/cache.class.php');
	$tpl->name = 'reportcache';
	$tpl->menuitem = MNU_CACHES_REPORT;

	$login->verify();
	if ($login->userid == 0)
		$tpl->redirect_login();

	$cacheid = isset($_REQUEST['cacheid']) ? $_REQUEST['cacheid']+0 : 0;
	$cache = new cache($cacheid);
	if ($cache->exist() == false)
		$tpl->error(ERROR_CACHE_NOT_EXISTS);

	if ($cache->allowView() == false)
		$tpl->error(ERROR_NO_ACCESS);

	$reportreason = isset($_REQUEST['reason']) ? $_REQUEST['reason']+0 : 0;
	$reportnote = isset($_REQUEST['note']) ? $_REQUEST['note'] : '';

	$maxreason = sql_value('SELECT MAX(`id`) FROM `cache_report_reasons`', 0);

	if (isset($_REQUEST['ok']))
	{
		$bError = false;
		if ($reportnote == '')
		{
			$bError = true;
			$tpl->assign('errorNoteEmpty', true);
		}

		if ($reportreason < 1 || $reportreason > $maxreason)
		{
			$bError = true;
			$tpl->assign('errorReasonEmpty', true);
		}

		if ($bError == false)
		{
			if ($cache->report($login->userid, $reportreason, $reportnote))
			{
				$reasontext = sql_value("SELECT IFNULL(`tt`.`text`, `crr`.`name`)
							   FROM `cache_report_reasons` AS `crr`
							  INNER JOIN `sys_trans_text` AS `tt` ON `tt`.`trans_id`=`crr`.`trans_id`
							  WHERE `crr`.`id` =&1
							    AND `tt`.`lang`='&2'",
							    'unknown', $reportreason, $opt['template']['locale']);

				$tpl->assign('reasontext', $reasontext);
				$tpl->assign('success', true);
			}
			else
			{
				$tpl->assign('errorUnkown', true);
			}
		}
	}

	$rs = sql("SELECT `cache_report_reasons`.`id`, IFNULL(`sys_trans_text`.`text`, `cache_report_reasons`.`name`) AS `name`
	             FROM `cache_report_reasons`
	        LEFT JOIN `sys_trans` ON `cache_report_reasons`.`trans_id`=`sys_trans`.`id` AND `cache_report_reasons`.`name`=`sys_trans`.`text`
	        LEFT JOIN `sys_trans_text` ON `sys_trans`.`id`=`sys_trans_text`.`trans_id` AND `sys_trans_text`.`lang`='&1'",
	          $opt['template']['locale']);

	$tpl->assign_rs('reasons', $rs);
	sql_free_result($rs);

	$tpl->assign('reason', $reportreason);
	$tpl->assign('note', $reportnote);

	$tpl->assign('cacheid', $cacheid);
	$tpl->assign('cachename', $cache->getName());

	$tpl->display();
?>