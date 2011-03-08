<?php
/***************************************************************************
											./lang/de/ocstyle/editcache.inc.php
															-------------------
		begin                : Mon July 6 2004
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

	 language vars

 ****************************************************************************/

	$submit = t('Change');
	$remove = t('Delete');
	$edit = t('Edit');

 	$error_wrong_node = t('This cache has been created on another Opencaching website. The cache can only be edited there.');

	$all_countries_submit = '<input type="submit" name="show_all_countries_submit" value="' . t('Show all') . '"/>';
 	$error_general = "<tr><td class='error' colspan='2'><b>" . t('Some errors occured, please check the marked fields.') . "</b></td></tr>";
	$name_message = '&nbsp;<span class="errormsg">' . t('Cachename is invalid') . '</span>';
	$date_message = '<span class="errormsg">' . t('date is invalid, format: TT-MM-JJJJ') . '</span>';
	$coords_message = '<span class="errormsg">' . t('The used coordinates are invalid.') . '</span>';
	$time_not_ok_message = '<span class="errormsg">' . t('The entered time is invalid.') . '</span>';
	$way_length_not_ok_message = '<span class="errormsg">' . t('The distance you have entered is invalid. Format aa.aaa') . '</span>';
	$sizemismatch_message = '&nbsp;<span class="errormsg">' . t('For virtual and webcam caches, the cache size has to be -no container-!') . '</span>';
	$status_message = '&nbsp;<span class="errormsg">' . t('The cache-status does not fit to your publishing options') . '</span>';
	$diff_not_ok_message = '&nbsp;<span class="errormsg">' . t('Choose both valuations!') . '</span>';
	$nopictures = '<tr><td colspan="2">' . t('No pictures available') . '</td></tr>';
	$pictureline = '<tr><td colspan="2"><a href="{link}">{title}</a> [<a href="picture.php?action=edit&uuid={uuid}">' . t('Edit') . '</a>] [<a href="picture.php?action=delete&uuid={uuid}">' . t('Delete') . '</a>]</td></tr>';
	$picturelines = '{lines}<tr><td colspan="2">&nbsp;</td></tr>';
	
	$nowaypoints = '<tr><td colspan="2">' . t('No waypoints available') . '</td></tr>';
	$waypointline = '<tr bgcolor="#ffffff"><td><img src="{wp_image}" />&nbsp;{wp_type}</td><td><table><tr><td>{wp_coordinate}</td></tr></table></tp><td>{wp_description}</td><td>[<a href="childwp.php?cacheid={cacheid}&childid={childid}">' . t('Edit') . '</a>] [<a href="childwp.php?cacheid={cacheid}&deleteid={childid}">' . t('Delete') . '</a>]</td></tr>';
	$waypointlines = '<tr><td colspan="2"><table bgcolor="#dddddd">{lines}</table></td></tr><tr><td colspan="2">&nbsp;</td></tr>';

	$nomp3 = '<tr><td colspan="2"><div class="notice">'.t('No Podcast files').'</div></td></tr>';
	$mp3line = '<tr><td colspan="2"><a href="{link}">{title}</a> [<a href="podcast.php?action=edit&uuid={uuid}">' . t('Edit') . '</a>] [<a href="podcast.php?action=delete&uuid={uuid}">' . t('Delete') . '</a>]</td></tr>';
	$mp3lines = '{lines}<tr><td colspan="2">&nbsp;</td></tr>';

	$cache_attrib_js = "new Array({id}, {selected}, '{img_undef}', '{img_large}')";
	$cache_attrib_pic = '<img id="attr{attrib_id}" src="{attrib_pic}" border="0" onmousedown="toggleAttr({attrib_id})" onmouseover="Tip(\'{html_desc}\', TITLE, \'{name}\', TITLEBGCOLOR, \'{color}\', TITLEFONTCOLOR, \'#000000\', BGCOLOR, \'#FFFFFF\', BORDERCOLOR, \'{color}\', CLICKCLOSE, true, DELAY, 0, FADEIN, false, FADEOUT, false, FONTCOLOR, \'#000080\', WIDTH, 500)" onmouseout="UnTip()" />&nbsp;';

	$cache_attrib_group = 
	'<table cellspacing="0" style="display:inline;border-spacing:0px;">
	     <tr><td bgcolor="{color}" style="line-height:9px;padding-top:2px;margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-top:1px solid gray;"><font size="1">{name}</font></td></tr>
	     <tr><td bgcolor="#F8F8F8" style="margin:0 0 0 0;border-left:1px solid gray;border-right:1px solid gray;border-bottom:1px solid gray;">{attribs}</td></tr>
	   </table>&nbsp;';

	$default_lang = t('EN');

	 $activation_form = '
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td>' . t('Publication:') . '</td>
			<td>
				<input type="radio" class="radio" name="publish" id="publish_now" value="now" {publish_now_checked}>&nbsp;<label for="publish_now">' . t('Publish now') . '</label><br />
				<input type="radio" class="radio" name="publish" id="publish_later" value="later" {publish_later_checked}>&nbsp;<label for="publish_later">' . t('Publish on') . '</label>
				<input class="input20" type="text" name="activate_day" maxlength="2" value="{activate_day}"/>.
				<input class="input20" type="text" name="activate_month" maxlength="2" value="{activate_month}"/>.
				<input class="input40" type="text" name="activate_year" maxlength="4" value="{activate_year}"/>&nbsp;
				<select name="activate_hour" class="input40">
					{activation_hours}
				</select>&nbsp;' . t('#time_suffix_label#') . '&nbsp;{activate_on_message}<br />
				<input type="radio" class="radio" name="publish" id="publish_notnow" value="notnow" {publish_notnow_checked}>&nbsp;<label for="publish_notnow">' . t('Do not publish now.') . '</label>
			</td>
		</tr>
		';
?>