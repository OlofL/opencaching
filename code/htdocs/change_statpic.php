<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	require('./lib2/web.inc.php');
	require_once('./lib2/logic/statpic.class.php');

	$login->verify();
	$tpl->name = 'change_statpic';
	$tpl->menuitem = MNU_MYPROFILE_DATA_STATPIC;

	if ($login->userid == 0)
		$tpl->redirect('login.php?target=change_statpic.php');

	if (isset($_REQUEST['cancel']))
		$tpl->redirect('myprofile.php');

	$sp = new statpic($login->userid);

	if (isset($_REQUEST['ok']))
	{
		$bError = false;

		if (isset($_REQUEST['statpic_text']))
		{
			if (!$sp->setText($_REQUEST['statpic_text']))
			{
				$bError = true;
				$tpl->assign('statpic_text_error', 1);
			}
		}

		if (isset($_REQUEST['statpic_style']))
			$sp->setStyle($_REQUEST['statpic_style']);

		if (!$bError)
		{
			$sp->save();
			$tpl->redirect('myprofile.php');
		}
	}

	$tpl->assign('statpic_text', isset($_REQUEST['statpic_text']) ? $_REQUEST['statpic_text'] : $sp->getText());
	$tpl->assign('statpic_style', isset($_REQUEST['statpic_style']) ? $_REQUEST['statpic_style'] : $sp->getStyle());

	$tpl->assign_rs('statpics', sql('SELECT `id`, `previewpath`, `description` FROM `statpics`'));

	$tpl->display();
?>