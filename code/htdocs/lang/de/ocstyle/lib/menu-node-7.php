<?php
/***************************************************************************
											./lang/de/ocstyle/lib/menu.php
															-------------------
		begin                : Mon June 14 2004
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
 *
 *  Unicode Reminder メモ
 *
 * $menu contains the entire menu structure
 *
 * possible array elements are:
 *
 * title         displayed HTML title
 * menustring    displayed menu text
 * siteid        unique id of this page
 * visible       bool, if true this site is shown in the menu structure
 * quicklinks    array of relativ pages
 *               (contains assotiativ array with href and text for each page)
 * submenu       array of submenues. Only the first 3 levels are displayed, deeper levels
 *               are only for the breadcrump. Each submenu has the same structure as $menu
 * navicolor     (only top-level menus) backgroundcolor of the menu
 * filename      filename for href
 *
 ****************************************************************************/

global $menu;

$menu = array(
	array(
		'title' => t(''),
		'menustring' => t('Start'),
		'siteid' => 'start',
		'visible' => true,
		'filename' => 'index.php'
	),
	array(
		'title' => t('My profile'),
		'menustring' => t('My profile'),
		'siteid' => 'myhome',
		'visible' => true,
		'filename' => 'myhome.php',
		'navicolor' => '#E8DDE4'
	),
	array(
		'title' => t('Caches'),
		'menustring' => t('Caches'),
		'siteid' => 'search',
		'visible' => true,
		'filename' => 'search.php',
		'navicolor' => '#FFFFC5',
		'submenu' => array(
			array(
				'title' => t('Search'),
				'menustring' => t('Search'),
				'visible' => true,
				'filename' => 'search.php',
				'siteid' => 'search',
				'submenu' => array(
					array(
						'title' => t('Show Geocache'),
						'menustring' => t('Show Geocache'),
						'visible' => false,
						'filename' => 'viewcache.php',
						'siteid' => 'viewcache',
						'submenu' => array(
							array(
								'title' => t('Create a logentry'),
								'menustring' => t('Create a logentry'),
								'visible' => false,
								'filename' => 'log.php',
								'siteid' => 'log_cache'
							),
							array(
								'title' => t('Edit logentry'),
								'menustring' => t('Edit logentry'),
								'visible' => false,
								'filename' => 'editlog.php',
								'siteid' => 'editlog'
							),
							array(
								'title' => t('Remove logentry'),
								'menustring' => t('Remove logentry'),
								'visible' => false,
								'filename' => 'removelog.php',
								'siteid' => 'removelog_logowner'
							),
							array(
								'title' => t('Remove logentry'),
								'menustring' => t('Remove logentry'),
								'visible' => false,
								'filename' => 'removelog.php',
								'siteid' => 'removelog_cacheowner'
							),
							array(
								'title' => t('Edit cache'),
								'menustring' => t('Edit cache'),
								'visible' => false,
								'filename' => 'editcache.php',
								'siteid' => 'editcache'
							),
							array(
								'title' => t('Add description'),
								'menustring' => t('Add description'),
								'visible' => false,
								'filename' => 'newdesc.php',
								'siteid' => 'newdesc'
							),
							array(
								'title' => t('Edit description'),
								'menustring' => t('Edit description'),
								'visible' => false,
								'filename' => 'editdesc.php',
								'siteid' => 'editdesc'
							),
							array(
								'title' => t('Remove description'),
								'menustring' => t('Remove description'),
								'visible' => false,
								'filename' => 'removedesc.php',
								'siteid' => 'removedesc'
							)
						)
					),
					array(
						'title' => t('Select city'),
						'menustring' => t('Select city'),
						'visible' => false,
						'filename' => 'search.php',
						'siteid' => 'selectlocid'
					),
					array(
						'title' => t('Show search result'),
						'menustring' => t('Result'),
						'visible' => false,
						'filename' => 'search.php',
						'siteid' => 'search.result.caches'
					),
					array(
						'title' => t('Recommendations'),
						'menustring' => t('Recommendations'),
						'visible' => false,
						'filename' => 'recommendations.php',
						'siteid' => 'recommendations'
					)
				)
			)
		)
	),
	array(
		'title' => t('Map'),
		'menustring' => t('Map'),
		'siteid' => 'map',
		'visible' => true,
		'filename' => 'map2.php'
	)
);

/*
 * mnu_MainMenuIndexFromPageId - returns the top level menu
 *
 * menustructure   normally $menu
 * pageid          siteid to search for
 */
function mnu_MainMenuIndexFromPageId($menustructure, $pageid)
{
	/* selmenuitem contains the selected (bold) menu item */
	global $mnu_selmenuitem;

	for ($i = 0, $ret = -1; ($i < count($menustructure)) && ($ret == -1); $i++)
	{
		if ($menustructure[$i]['siteid'] == $pageid)
		{
			$mnu_selmenuitem = $menustructure[$i];
			return $i;
		}
		else
		{
			if (isset($menustructure[$i]['submenu']))
			{
				$ret = mnu_MainMenuIndexFromPageId($menustructure[$i]['submenu'], $pageid);
				if ($ret != -1) return $i;
			}
		}
	}

	return $ret;
}

/*
 * mnu_EchoMainMenu - echos the top level menus
 *
 * selmenuid   p.e. mnu_MainMenuIndexFromPageId($menu, $siteid)
 */
function mnu_EchoMainMenu($selmenuid)
{
	global $menu;
	$c = 0;
	for ($i = 0; $i < count($menu); $i++)
	{
		if ($menu[$i]['visible'] == true)
		{
			$sItem = '<a href="' . $menu[$i]['filename'] . '">' . htmlspecialchars(t($menu[$i]['menustring']), ENT_COMPAT, 'UTF-8') . '</a>';
			$sItemSel = '<a href="' . $menu[$i]['filename'] . '" class=\'selected bg-green06\'>' . htmlspecialchars(t($menu[$i]['menustring']), ENT_COMPAT, 'UTF-8') . '</a>'; 

			if ($menu[$i]['siteid'] == $selmenuid)
				/* $sItem = '<li>TODO:selected' . $sItem . '</li>'; */
				$sItem = '<li>' . $sItemSel . '</li>';
			else
				$sItem = '<li>' . $sItem . '</li>';

			echo $sItem . "\n";

			$c++;
		}
	}
}

/*
 * mnu_EchoSubMenu - echos the 2. and 3. menu level
 *
 * menustructure   $menu
 * pageid          siteid to search for
 * level           has to be 1
 * bHasSubmenu     has to be false
 */
function mnu_EchoSubMenu($menustructure, $pageid, $level, $bHasSubmenu)
{
	/* enthält die Hintergrundfarbe des Menüs */
	global $mnu_bgcolor;

	if (!$bHasSubmenu)
	{
		/* prüfen, ob ein Submenü vorhanden ist */
		for ($i = 0, $bSubmenu = false; ($i < count($menustructure)) && ($bSubmenu == false); $i++)
		{
			if (isset($menustructure[$i]['submenu']))
			{
				$bSubmenu = true;
			}
		}
	}

	if (!$bHasSubmenu)
	{
		$cssclass = 'group1';
	}
	else
	{
		if ($level == 1)
		{
			$cssclass = 'group1';
		}
		else
		{
			$cssclass = 'group2';
		}
	}

	for ($i = 0; $i < count($menustructure); $i++)
	{
		if ($menustructure[$i]['visible'] == true)
		{
			if ($menustructure[$i]['siteid'] == $pageid)
			{
				echo '<li class="' . $cssclass . ' group_active"><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars(t($menustructure[$i]['menustring']), ENT_COMPAT, 'UTF-8') . '</a></li>' . "\n";
			}
			else
			{
				if (isset($menustructure[$i]['submenu']))
				{
					if (mnu_IsMenuParentOf($menustructure[$i]['submenu'], $pageid))
					{
						echo '<li class="' . $cssclass . ' group_active"><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars(t($menustructure[$i]['menustring']), ENT_COMPAT, 'UTF-8') . '</a></li>' . "\n";
					}
					else
					{
						echo '<li class="' . $cssclass . '"><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars(t($menustructure[$i]['menustring']), ENT_COMPAT, 'UTF-8') . '</a></li>' . "\n";
					}
				}
				else
				{
					echo '<li class="' . $cssclass . '"><a href="' . $menustructure[$i]['filename'] . '">' . htmlspecialchars(t($menustructure[$i]['menustring']), ENT_COMPAT, 'UTF-8') . '</a></li>' . "\n";
				}
			}

			if (isset($menustructure[$i]['submenu']))
			{
				/* rekursiver Aufruf zur Ausgabe der 3. Ebene */
				mnu_EchoSubMenu($menustructure[$i]['submenu'], $pageid, $level + 1, true);
			}
		}
	}
}

/*
 * mnu_IsMenuParentOf - returns true if menuitemid is part of $parentmenuitems, otherwise false
 *
 * parentmenuitems   p.e. $menu
 * menuitemid        siteid to search for
 */
function mnu_IsMenuParentOf($parentmenuitems, $menuitemid)
{
	for ($i = 0; $i < count($parentmenuitems); $i++)
	{
		if ($parentmenuitems[$i]['siteid'] == $menuitemid) return true;

		if (isset($parentmenuitems[$i]['submenu']))
		{
			$ret = mnu_IsMenuParentOf($parentmenuitems[$i]['submenu'], $menuitemid);
			if ($ret == true) return true;
		}
	}

	return false;
}

/*
 * mnu_EchoBreadCrumb - echos the breadcrumb
 *
 * pageid          siteid to search for
 * mainmenuindex   index of the top level menu
 */
function mnu_EchoBreadCrumb($pageid, $mainmenuindex)
{
	global $menu;

	echo htmlspecialchars(t($menu[$mainmenuindex]['menustring']), ENT_COMPAT, 'UTF-8');

	if (isset($menu[$mainmenuindex]['submenu']) && ($menu[$mainmenuindex]['siteid'] != $pageid))
	{
		mnu_prv_EchoBreadCrumbSubItem($pageid, $menu[$mainmenuindex]['submenu']);
	}
}

/*
 * mnu_prv_EchoBreadCrumbSubItem - private helper function
 */
function mnu_prv_EchoBreadCrumbSubItem($pageid, $menustructure)
{
	for ($i = 0; $i < count($menustructure); $i++)
	{
		if ($menustructure[$i]['siteid'] == $pageid)
		{
			echo '&nbsp;&gt;&nbsp;' . htmlspecialchars(t($menustructure[$i]['menustring']), ENT_COMPAT, 'UTF-8');
			return;
		}
		else
		{
			if (isset($menustructure[$i]['submenu']))
			{
				if (mnu_IsMenuParentOf($menustructure[$i]['submenu'], $pageid))
				{
					echo '&nbsp;&gt;&nbsp;' . htmlspecialchars(t($menustructure[$i]['menustring']), ENT_COMPAT, 'UTF-8');
					mnu_prv_EchoBreadCrumbSubItem($pageid, $menustructure[$i]['submenu']);
					return;
				}
			}
		}
	}
}
?>