#!/usr/bin/php -q
<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 ***************************************************************************/

	$opt['rootpath'] = '../../../';

	// chdir to proper directory (needed for cronjobs)
	chdir(substr(realpath($_SERVER['PHP_SELF']), 0, strrpos(realpath($_SERVER['PHP_SELF']), '/')));

	require($opt['rootpath'] . 'lib2/cli.inc.php');
	require($opt['rootpath'] . 'lib2/logic/coordinate.class.php');

	$nXMin = 5.5;
	$nXMax = 15.5;
	$nYMin = 47;
	$nYMax = 55.5;

	$nHeight = 500;
	$nWidth = 400;

	$img = imagecreate($nWidth, $nHeight);

	$cWhite = imagecolorallocate($img, 255, 255, 255);
	$cBlack = imagecolorallocate($img, 0, 0, 0);
	$cBlue = imagecolorallocate($img, 0, 0, 255);

	imagefilledrectangle($img, 0, 0, $nWidth, $nHeight, $cWhite);

	$nLabel = 1;

	$rsBez = sql("SELECT gid, x1, y1, x2, y2, f_NUTS_ID AS f_name FROM gis.NUTS_RG_03M_2003 WHERE LENGTH(f_NUTS_ID)=4 AND f_CNTR_CODE='DE' ORDER BY y1 DESC, x1");
	while ($rBez = sql_fetch_assoc($rsBez))
	{
		$x1 = $nWidth - $nWidth * ($nXMax - $rBez['x1']) / ($nXMax - $nXMin);
		$x2 = $nWidth - $nWidth * ($nXMax - $rBez['x2']) / ($nXMax - $nXMin);
		$y1 = $nHeight * ($nYMax - $rBez['y1']) / ($nYMax - $nYMin);
		$y2 = $nHeight * ($nYMax - $rBez['y2']) / ($nYMax - $nYMin);

		// text zeichnen
		$rBez['f_name'] = @iconv('UTF-8', 'ISO-8859-1', sql_value("SELECT name FROM nuts_codes WHERE code='&1'", '', $rBez['f_name']));
		echo $nLabel . ' ' . $rBez['f_name'] . "\n";
		imagestring($img, 3, ($x1+$x2)/2, ($y1+$y2)/2, $nLabel, $cBlue);
		$nLabel++;

		$rsRects = sql("SELECT x1, y1, x2, y2, seq FROM gis.NUTS_RG_03M_2003_num WHERE gid='&1' ORDER BY gid, eseq, seq", $rBez['gid']);
		while ($r = sql_fetch_assoc($rsRects))
		{
			$x1 = $nWidth - $nWidth * ($nXMax - $r['x1']) / ($nXMax - $nXMin);
			$x2 = $nWidth - $nWidth * ($nXMax - $r['x2']) / ($nXMax - $nXMin);
			$y1 = $nHeight * ($nYMax - $r['y1']) / ($nYMax - $nYMin);
			$y2 = $nHeight * ($nYMax - $r['y2']) / ($nYMax - $nYMin);
		
			imageline($img, $x1, $y1, $x2, $y2, $cBlack);
			
			//echo $r['seq'] . "\n";
		}
		sql_free_result($rsRects);
	}
	sql_free_result($rsBez);

	imagegif($img, 'map.gif');
?>