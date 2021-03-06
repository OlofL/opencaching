<?php
	/***************************************************************************
															./lib/search.gpx.inc.php
																-------------------
			begin                : November 1 2005 
			copyright            : (C) 2005 The OpenCaching Group
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
                                     				                                
		GPX search output
		
	****************************************************************************/

	require_once('./config2/childwp.inc.php');

function getCacheNote($userid, $cacheid)
{
  $cacheNoteHandler = new CacheNote_Handler();
  $cacheNote = $cacheNoteHandler->getCacheNote($userid, $cacheid);

  if (isset($cacheNote['note']) || isset($cacheNote['latitude']) || isset($cacheNote['longitude']))
    return $cacheNote;

  return null;
}

	global $content, $bUseZip, $sqldebug;

	$gpxHead = 
'<?xml version="1.0" encoding="utf-8"?>
<gpx xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xsi:schemaLocation="http://www.topografix.com/GPX/1/0 http://www.topografix.com/GPX/1/0/gpx.xsd"
     xmlns="http://www.topografix.com/GPX/1/0"
     version="1.0"
     creator="www.opencaching.de">
  <desc>Geocache (HasChildren)</desc>
  <author>www.opencaching.de</author>
  <url>http://www.opencaching.de</url>
  <urlname>www.opencaching.de</urlname>
  <time>{time}</time>
';
	
	$gpxLine = 
'
	<wpt lat="{lat}" lon="{lon}">
		<time>{time}</time>
		<name>{waypoint}</name>
		<desc>{cachename}</desc>
		<src>www.opencaching.de</src>
		<url>' . $absolute_server_URI . 'viewcache.php?cacheid={cacheid}</url>
		<urlname>{cachename}</urlname>
		<sym>Geocache</sym>
		<type>Geocache|{type}</type>
		<extensions>
			<cache id="{cacheid}" status="{status}">
				<name>{cachename}</name>
				<owner userid="{userid}">{owner}</owner>
				<locale></locale>
				<state>{state}</state>
				<country>{country}</country>
				<type>{type}</type>
				<container>{container}</container>
				<difficulty>{difficulty}</difficulty>
				<terrain>{terrain}</terrain>
				<short_description html="true">{shortdesc}</short_description>
				<long_description html="true">{desc}</long_description>
				{hints}
				<licence></licence>
				<logs>
					{logs}
				</logs>
			</cache>
		</extensions>
	</wpt>
	{cache_waypoints}
';

	$gpxLog = '
<log id="{id}">
	<date>{date}</date>
	<finder id="{userid}">{username}</finder>
	<type>{type}</type>
	<text>{text}</text>
</log>
';

// 	<name><![CDATA[{waypoint} {wp_stage}]]></name>

$gpxWaypoints = '
<wpt lat="{wp_lat}" lon="{wp_lon}">
	<time>{time}</time>
	<name>{name}</name>
	<cmt>{wp_type_name} {desc}</cmt>
	<desc>{desc}</desc>
	<url>' . $absolute_server_URI . 'viewcache.php?cacheid={cacheid}</url>
	<urlname><![CDATA[{waypoint} {wp_stage}]]></urlname>
	<sym>{wp_type}</sym>
	<type>Waypoint|{wp_type}</type>
	<gsak:wptExtension xmlns:gsak="http://www.gsak.net/xmlv1/4">
		<gsak:Parent>{parent}</gsak:Parent>
	</gsak:wptExtension>
</wpt>
';

	$gpxFoot = '</gpx>';

	$gpxTimeFormat = 'Y-m-d\TH:i:s\Z';

	$gpxStatus[0] = 'Unavailable'; // andere
	$gpxStatus[1] = 'Available';
	$gpxStatus[2] = 'Unavailable';
	$gpxStatus[3] = 'Archived';
	
	$gpxContainer[0] = 'Other';
	$gpxContainer[2] = 'Micro';
	$gpxContainer[3] = 'Small';
	$gpxContainer[4] = 'Regular';
	$gpxContainer[5] = 'Large';
	$gpxContainer[6] = 'Large';
	$gpxContainer[7] = 'Virtual';

	// known by gpx
	$gpxType[0] = 'Unknown Cache';
	$gpxType[2] = 'Traditional Cache';
	$gpxType[3] = 'Multi-cache';
	$gpxType[4] = 'Virtual Cache';
	$gpxType[5] = 'Webcam Cache';
	$gpxType[6] = 'Event Cache';

	// unknown ... converted
	$gpxType[7] = 'Unknown Cache';
	$gpxType[8] = 'Unknown Cache';
	$gpxType[10] = 'Traditional Cache';
	$gpxType[11] = 'Unknown Cache';
	$gpxType[12] = 'Unknown Cache';
	$gpxType[13] = 'Unknown Cache';
	$gpxType[14] = 'Unknown Cache';
	
	$gpxLogType[0] = 'Other';
	$gpxLogType[1] = 'Found it';
	$gpxLogType[2] = 'Didn\'t find it';
	$gpxLogType[3] = 'Write note';

	//prepare the output
	$caches_per_page = 20;
	
	$sql = 'SELECT '; 

	if (isset($lat_rad) && isset($lon_rad))
	{
		$sql .= getSqlDistanceFormula($lon_rad * 180 / 3.14159, $lat_rad * 180 / 3.14159, 0, $multiplier[$distance_unit]) . ' `distance`, ';
	}
	else
	{
		if ($usr === false)
		{
			$sql .= '0 distance, ';
		}
		else
		{
			//get the users home coords
			$rs_coords = sql_slave("SELECT `latitude`, `longitude` FROM `user` WHERE `user_id`='&1'", $usr['userid']);
			$record_coords = sql_fetch_array($rs_coords);
			
			if ((($record_coords['latitude'] == NULL) || ($record_coords['longitude'] == NULL)) || (($record_coords['latitude'] == 0) || ($record_coords['longitude'] == 0)))
			{
				$sql .= '0 distance, ';
			}
			else
			{
				//TODO: load from the users-profile
				$distance_unit = 'km';

				$lon_rad = $record_coords['longitude'] * 3.14159 / 180;   
        $lat_rad = $record_coords['latitude'] * 3.14159 / 180; 

				$sql .= getSqlDistanceFormula($record_coords['longitude'], $record_coords['latitude'], 0, $multiplier[$distance_unit]) . ' `distance`, ';
			}
			mysql_free_result($rs_coords);
		}
	}
	$sAddJoin = '';
	$sAddGroupBy = '';
	$sAddField = '';
	$sGroupBy = '';
	if ($options['sort'] == 'bylastlog')
	{
		$sAddField = ', MAX(`cache_logs`.`date`) AS `lastLog`';
		$sAddJoin = ' LEFT JOIN `cache_logs` ON `caches`.`cache_id`=`cache_logs`.`cache_id`';
		$sGroupBy = ' GROUP BY `cache_logs`.`cache_id`';
	}
	$sql .= '`caches`.`cache_id` `cache_id`, `caches`.`status` `status`, `caches`.`type` `type`, `caches`.`size` `size`, `caches`.`longitude` `longitude`, `caches`.`latitude` `latitude`, `caches`.`user_id` `user_id`, 
	            IF(IFNULL(`stat_caches`.`toprating`,0)>3, 4, IFNULL(`stat_caches`.`toprating`, 0)) `ratingvalue`,
		 `cache_location`.`adm2` `state`' . 
				      $sAddField
		 . ' FROM `caches`
	  LEFT JOIN `stat_caches` ON `caches`.`cache_id`=`stat_caches`.`cache_id`
	  LEFT JOIN `cache_location` ON `caches`.`cache_id`=`cache_location`.`cache_id`' .
				      $sAddJoin 
		. ' WHERE `caches`.`cache_id` IN (' . $sqlFilter . ')' . 
				      $sGroupBy;
	$sortby = $options['sort'];

	$sql .= ' ORDER BY ';
	if ($options['orderRatingFirst'])
		$sql .= '`ratingvalue` DESC, ';

	if ($sortby == 'bylastlog')
	{
		$sql .= '`lastLog` DESC, ';
		$sortby = 'bydistance';
	}

	if (isset($lat_rad) && isset($lon_rad) && ($sortby == 'bydistance'))
	{
		$sql .= '`distance` ASC';
	}
	else if ($sortby == 'bycreated')
	{
		$sql .= '`caches`.`date_created` DESC';
	}
	else // by name
	{
		$sql .= '`caches`.`name` ASC';
	}

	//startat?
	$startat = isset($_REQUEST['startat']) ? $_REQUEST['startat'] : 0;
	if (!is_numeric($startat)) $startat = 0;
	
	if (isset($_REQUEST['count']))
		$count = $_REQUEST['count'];
	else
		$count = $caches_per_page;
	
	if ($count == 'max') $count = 500;
	if (!is_numeric($count)) $count = 0;
	if ($count < 1) $count = 1;
	if ($count > 500) $count = 500;

	$sqlLimit = ' LIMIT ' . $startat . ', ' . $count;

	// temporäre tabelle erstellen
	sql_slave('CREATE TEMPORARY TABLE `gpxcontent` ' . $sql . $sqlLimit);

	$rsCount = sql_slave('SELECT COUNT(*) `count` FROM `gpxcontent`');
	$rCount = sql_fetch_array($rsCount);
	mysql_free_result($rsCount);
	
	if ($rCount['count'] == 1)
	{
		$rsName = sql_slave('SELECT `caches`.`wp_oc` `wp_oc` FROM `gpxcontent`, `caches` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` LIMIT 1');
		$rName = sql_fetch_array($rsName);
		mysql_free_result($rsName);
		
		$sFilebasename = $rName['wp_oc'];
	}
	else
		$sFilebasename = 'ocde' . $options['queryid'];
		
	$bUseZip = ($rCount['count'] > 20);
	$bUseZip = $bUseZip || (isset($_REQUEST['zip']) && ($_REQUEST['zip'] == '1'));
	
	if ($bUseZip == true)
	{
		$content = '';
		require_once($rootpath . 'lib/phpzip/ss_zip.class.php');
		$phpzip = new ss_zip('',6);
	}

	// ok, ausgabe starten
	
	if ($sqldebug == false)
	{
		if ($bUseZip == true)
		{
			header("content-type: application/zip");
			header('Content-Disposition: attachment; filename=' . $sFilebasename . '.zip');
		}
		else
		{
			header("Content-type: application/gpx");
			header("Content-Disposition: attachment; filename=" . $sFilebasename . ".gpx");
		}
	}
	
	$gpxHead = mb_ereg_replace('{time}', date($gpxTimeFormat, time()), $gpxHead);
	append_output($gpxHead);

	// ok, ausgabe ...
	
	$rs = sql_slave('SELECT SQL_BUFFER_RESULT `gpxcontent`.`cache_id` `cacheid`, `gpxcontent`.`longitude` `longitude`, `gpxcontent`.`latitude` `latitude`, `gpxcontent`.`state` `state`, `caches`.`wp_oc` `waypoint`, `caches`.`date_hidden` `date_hidden`, `caches`.`name` `name`, `caches`.`country` `country`, `caches`.`terrain` `terrain`, `caches`.`difficulty` `difficulty`, `caches`.`desc_languages` `desc_languages`, `caches`.`size` `size`, `caches`.`type` `type`, `caches`.`status` `status`, `user`.`username` `username`, `caches`.`user_id` `userid`, `cache_desc`.`desc` `desc`, `cache_desc`.`short_desc` `short_desc`, `cache_desc`.`hint` `hint` FROM `gpxcontent`, `caches`, `user`, `cache_desc` WHERE `gpxcontent`.`cache_id`=`caches`.`cache_id` AND `caches`.`cache_id`=`cache_desc`.`cache_id` AND `caches`.`default_desclang`=`cache_desc`.`language` AND `gpxcontent`.`user_id`=`user`.`user_id`');
	while($r = sql_fetch_array($rs))
	{
		$thisline = $gpxLine;
		
		$lat = sprintf('%01.5f', $r['latitude']);
		$thisline = mb_ereg_replace('{lat}', $lat, $thisline);
		
		$lon = sprintf('%01.5f', $r['longitude']);
		$thisline = mb_ereg_replace('{lon}', $lon, $thisline);

		$time = date($gpxTimeFormat, strtotime($r['date_hidden']));
		$thisline = mb_ereg_replace('{time}', $time, $thisline);
		$thisline = mb_ereg_replace('{waypoint}', $r['waypoint'], $thisline);
		$thisline = mb_ereg_replace('{cacheid}', $r['cacheid'], $thisline);
		$thisline = mb_ereg_replace('{cachename}', xmlentities($r['name']), $thisline);
		$thisline = mb_ereg_replace('{country}', $r['country'], $thisline);
		$thisline = mb_ereg_replace('{state}', xmlentities($r['state']), $thisline);
		
		if ($r['hint'] == '')
			$thisline = mb_ereg_replace('{hints}', '', $thisline);
		else
			$thisline = mb_ereg_replace('{hints}', '<encoded_hints>' . xmlentities(strip_tags($r['hint'])) . '</encoded_hints>', $thisline);

		$thisline = mb_ereg_replace('{shortdesc}', xmlentities($r['short_desc']), $thisline);
		$thisline = mb_ereg_replace('{desc}', xmlentities($r['desc']), $thisline);

		if (isset($gpxType[$r['type']]))
		$thisline = mb_ereg_replace('{type}', $gpxType[$r['type']], $thisline);
		else
		$thisline = mb_ereg_replace('{type}', $gpxType[0], $thisline);

		if (isset($gpxContainer[$r['size']]))
		$thisline = mb_ereg_replace('{container}', $gpxContainer[$r['size']], $thisline);
		else
		$thisline = mb_ereg_replace('{container}', $gpxContainer[0], $thisline);

		if (isset($gpxStatus[$r['status']]))
		$thisline = mb_ereg_replace('{status}', $gpxStatus[$r['status']], $thisline);
		else
		$thisline = mb_ereg_replace('{status}', $gpxStatus[0], $thisline);

		$sDiffDecimals = '';
		if ($r['difficulty'] % 2) $sDiffDecimals = '.5';
		$r['difficulty'] -= $r['difficulty'] % 2;
		$thisline = mb_ereg_replace('{difficulty}', ($r['difficulty']/2) . $sDiffDecimals, $thisline);

		$sTerrDecimals = '';
		if ($r['terrain'] % 2) $sTerrDecimals = '.5';
		$r['terrain'] -= $r['terrain'] % 2;
		$thisline = mb_ereg_replace('{terrain}', ($r['terrain']/2) . $sTerrDecimals, $thisline);

		$thisline = mb_ereg_replace('{owner}', xmlentities($r['username']), $thisline);
		$thisline = mb_ereg_replace('{userid}', xmlentities($r['userid']), $thisline);

		// logs ermitteln
		if ($usr === false)
		{
			$user_id = 0;
		}
		else
		{
			$user_id = $usr['userid'];
		}

		$logentries = '';
		$cache_note = false;

		if ($user_id != 0)
		{
			$cacheNote = getCacheNote($user_id, $r['cacheid']);

			if ($cacheNote)
			{
				$thislog = $gpxLog;
				
				$thislog = mb_ereg_replace('{id}', $r['cacheid'], $thislog);
				$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat), $thislog);
				$thislog = mb_ereg_replace('{userid}', xmlentities($user_id), $thislog);
				$thislog = mb_ereg_replace('{username}', xmlentities('Cache note'), $thislog);
				$thislog = mb_ereg_replace('{type}', $gpxLogType[3], $thislog);
				$thislog = mb_ereg_replace('{text}', xmlentities($cacheNote['note']), $thislog);

				$logentries .= $thislog . "\n";
			}

			// my logs
			$rsLogs = sql_slave("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `user`.`user_id` FROM `cache_logs`, `user` WHERE `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 AND `user`.`user_id`=&2 ORDER BY `cache_logs`.`date` DESC", $r['cacheid'], $user_id);
			while ($rLog = sql_fetch_array($rsLogs))
			{
				$thislog = $gpxLog;
				
				$thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
				$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
				$thislog = mb_ereg_replace('{userid}', xmlentities($rLog['user_id']), $thislog);
				$thislog = mb_ereg_replace('{username}', xmlentities($rLog['username']), $thislog);
				
				if (isset($gpxLogType[$rLog['type']]))
					$logtype = $gpxLogType[$rLog['type']];
				else
					$logtype = $gpxLogType[0];
					
				$thislog = mb_ereg_replace('{type}', $logtype, $thislog);
				$thislog = mb_ereg_replace('{text}', xmlentities($rLog['text']), $thislog);
				
				$logentries .= $thislog . "\n";
			}
		}

		// first 20 logs (except mine)
		$rsLogs = sql_slave("SELECT `cache_logs`.`id`, `cache_logs`.`type`, `cache_logs`.`date`, `cache_logs`.`text`, `user`.`username`, `user`.`user_id` FROM `cache_logs`, `user` WHERE `cache_logs`.`user_id`=`user`.`user_id` AND `cache_logs`.`cache_id`=&1 AND `user`.`user_id`!=&2 ORDER BY `cache_logs`.`date` DESC LIMIT 20", $r['cacheid'], $user_id);
		while ($rLog = sql_fetch_array($rsLogs))
		{
			$thislog = $gpxLog;
			
			$thislog = mb_ereg_replace('{id}', $rLog['id'], $thislog);
			$thislog = mb_ereg_replace('{date}', date($gpxTimeFormat, strtotime($rLog['date'])), $thislog);
			$thislog = mb_ereg_replace('{userid}', xmlentities($rLog['user_id']), $thislog);
			$thislog = mb_ereg_replace('{username}', xmlentities($rLog['username']), $thislog);
			
			if (isset($gpxLogType[$rLog['type']]))
				$logtype = $gpxLogType[$rLog['type']];
			else
				$logtype = $gpxLogType[0];
				
			$thislog = mb_ereg_replace('{type}', $logtype, $thislog);
			$thislog = mb_ereg_replace('{text}', xmlentities($rLog['text']), $thislog);
			
			$logentries .= $thislog . "\n";
		}
		$thisline = mb_ereg_replace('{logs}', $logentries, $thisline);

		$waypoints = '';
		$wphandler = new ChildWp_Handler();
		$childWaypoints = $wphandler->getChildWps($r['cacheid']);

		foreach ($childWaypoints as $childWaypoint)
		{
			$thiswp = $gpxWaypoints;
			$lat = sprintf('%01.5f', $childWaypoint['latitude']);
			$thiswp = str_replace('{wp_lat}', $lat, $thiswp);
			$lon = sprintf('%01.5f', $childWaypoint['longitude']);
			$thiswp = str_replace('{wp_lon}', $lon, $thiswp);
			$thiswp = str_replace('{name}', 'W'.$childWaypoint['childid'].substr($r['waypoint'], 2) , $thiswp);
			$thiswp = str_replace('{waypoint}', $childWaypoint['childid'], $thiswp);
			$thiswp = str_replace('{cacheid}', $r['cacheid'], $thiswp);
			$thiswp = str_replace('{time}', $time, $thiswp);
			$thiswp = str_replace('{wp_type_name}', $childWaypoint['name'], $thiswp);
			$thiswp = str_replace('{wp_stage}',$childWaypoint['name'], $thiswp);
			$thiswp = str_replace('{desc}', xmlentities($childWaypoint['description']), $thiswp);
			if ($childWaypoint['type']==1){$thiswp = str_replace('{wp_type}', "Parking Area", $thiswp);}
			if ($childWaypoint['type']==2){$thiswp = str_replace('{wp_type}', "Flag, Green", $thiswp);}
			$thiswp = str_replace('{parent}', $r['waypoint'], $thiswp);

			$waypoints .= $thiswp;
		}

		if ($cacheNote && (!empty($cacheNote['latitude']) || !empty($cacheNote['longitude'])))
		{
			$thiswp = $gpxWaypoints;
			$lat = sprintf('%01.5f', $cacheNote['latitude']);
			$thiswp = str_replace('{wp_lat}', $lat, $thiswp);
			$lon = sprintf('%01.5f', $cacheNote['longitude']);
			$thiswp = str_replace('{wp_lon}', $lon, $thiswp);
			$thiswp = str_replace('{name}', 'NOTE'.substr($r['waypoint'], 2) , $thiswp);
			$thiswp = str_replace('{waypoint}', $cacheNote['id'], $thiswp);
			$thiswp = str_replace('{cacheid}', $r['cacheid'], $thiswp);
			$thiswp = str_replace('{time}', $time, $thiswp);
			$thiswp = str_replace('{wp_type_name}', 'note', $thiswp);
			$thiswp = str_replace('{wp_stage}','note', $thiswp);
			$thiswp = str_replace('{desc}', xmlentities($cacheNote['note']), $thiswp);
			$thiswp = str_replace('{wp_type}', "Flag, Red", $thiswp);
			$thiswp = str_replace('{parent}', $r['waypoint'], $thiswp);

			$waypoints .= $thiswp;
		}

		$thisline = str_replace('{cache_waypoints}', $waypoints, $thisline);

		append_output($thisline);
	}
	mysql_free_result($rs);
	
	append_output($gpxFoot);

	if ($sqldebug == true) sqldbg_end();
	
	// phpzip versenden
	if ($bUseZip == true)
	{
		$phpzip->add_data($sFilebasename . '.gpx', $content);
		echo $phpzip->save($sFilebasename . '.zip', 'b');
	}

	exit;
	
	function xmlentities($str)
	{
		$from[0] = '&'; $to[0] = '&amp;';
		$from[1] = '<'; $to[1] = '&lt;';
		$from[2] = '>'; $to[2] = '&gt;';
		$from[3] = '"'; $to[3] = '&quot;';
		$from[4] = '\''; $to[4] = '&apos;';
		$from[5] = ']]>'; $to[5] = ']] >';

		for ($i = 0; $i <= 4; $i++)
			$str = mb_ereg_replace($from[$i], $to[$i], $str);

		return filterevilchars($str);
	}
	
	function filterevilchars($str)
	{
		return mb_ereg_replace('[\\x00-\\x09|\\x0B-\\x0C|\\x0E-\\x1F]', '', $str);
	}

	function append_output($str)
	{
		global $content, $bUseZip, $sqldebug;
		if ($sqldebug == true) return;
		
		if ($bUseZip == true)
			$content .= $str;
		else
			echo $str;
	}
?>
