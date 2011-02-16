<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *   get/set has to be commited with save
 *   add/remove etc. is executed instantly
 ***************************************************************************/

require_once($opt['rootpath'] . 'lib2/logic/rowEditor.class.php');

class cache
{
	var $nCacheId = 0;

	var $reCache;

	static function cacheIdFromWP($wp)
	{
		$cacheid = 0;
		if (mb_strtoupper(mb_substr($wp, 0, 2)) == 'GC')
		{
			$rs = sql("SELECT `cache_id` FROM `caches` WHERE `wp_gc`='&1'", $wp);
			if (sql_num_rows($rs) != 1)
			{
				sql_free_result($rs);
				return null;
			}
			$r = sql_fetch_assoc($rs);
			sql_free_result($rs);

			$cacheid = $r['cache_id'];
		}
		else if (mb_strtoupper(mb_substr($wp, 0, 1)) == 'N')
		{
			$rs = sql("SELECT `cache_id` FROM `caches` WHERE `wp_nc`='&1'", $wp);
			if (sql_num_rows($rs) != 1)
			{
				sql_free_result($rs);
				return null;
			}
			$r = sql_fetch_assoc($rs);
			sql_free_result($rs);

			$cacheid = $r['cache_id'];
		}
		else
		{
			$cacheid = sql_value("SELECT `cache_id` FROM `caches` WHERE `wp_oc`='&1'", 0, $wp);
		}
		
		return $cacheid;
	}

	static function fromWP($wp)
	{
		$cacheid = cache::cacheIdFromWP($wp);
		if ($cacheid == 0)
			return null;

		return new cache($cacheid);
	}

	static function cacheIdFromUUID($uuid)
	{
		$cacheid = sql_value("SELECT `cache_id` FROM `caches` WHERE `uuid`='&1'", 0, $uuid);
		return $cacheid;
	}

	static function fromUUID($uuid)
	{
		$cacheid = cache::cacheIdFromUUID($uuid);
		if ($cacheid == 0)
			return null;

		return new cache($cacheid);
	}

	function __construct($nNewCacheId=ID_NEW)
	{
		$this->reCache = new rowEditor('caches');
		$this->reCache->addPKInt('cache_id', null, false, RE_INSERT_AUTOINCREMENT);
		$this->reCache->addString('uuid', '', false, RE_INSERT_OVERWRITE|RE_INSERT_UUID);
		$this->reCache->addInt('node', 0, false);
		$this->reCache->addDate('date_created', time(), true, RE_INSERT_IGNORE);
		$this->reCache->addDate('last_modified', time(), true, RE_INSERT_IGNORE);
		$this->reCache->addInt('user_id', 0, false);
		$this->reCache->addString('name', '', false);
		$this->reCache->addDouble('longitude', 0, false);
		$this->reCache->addDouble('latitude', 0, false);
		$this->reCache->addInt('type', 1, false);
		$this->reCache->addInt('status', 5, false);
		$this->reCache->addString('country', '', false);
		$this->reCache->addDate('date_hidden', time(), false);
		$this->reCache->addInt('size', 1, false);
		$this->reCache->addFloat('difficulty', 1, false);
		$this->reCache->addFloat('terrain', 1, false);
		$this->reCache->addString('logpw', '', false);
		$this->reCache->addFloat('search_time', 0, false);
		$this->reCache->addFloat('way_length', 0, false);
		$this->reCache->addString('wp_oc', null, true);
		$this->reCache->addString('wp_gc', '', false);
		$this->reCache->addString('wp_nc', '', false);
		$this->reCache->addString('desc_languages', '', false, RE_INSERT_IGNORE);
		$this->reCache->addString('default_desclang', '', false);
		$this->reCache->addDate('date_activate', null, true);
		$this->reCache->addInt('need_npa_recalc', 1, false, RE_INSERT_IGNORE);

		$this->nCacheId = $nNewCacheId+0;

		if ($nNewCacheId == ID_NEW)
		{
			$this->reCache->addNew(null);
		}
		else
		{
			$this->reCache->load($this->nCacheId);
		}
	}

	function exist()
	{
		return $this->reCache->exist();
	}

	function getCacheId()
	{
		return $this->nCacheId;
	}
	function getStatus()
	{
		return $this->reCache->getValue('status');
	}
	function getType()
	{
		return $this->reCache->getValue('type');
	}
	function getName()
	{
		return $this->reCache->getValue('name');
	}
	function getLongitude()
	{
		return $this->reCache->getValue('longitude');
	}
	function getLatitude()
	{
		return $this->reCache->getValue('latitude');
	}
	function getUserId()
	{
		return $this->reCache->getValue('user_id');
	}
	function getUsername()
	{
		return sql_value("SELECT `username` FROM `user` WHERE `user_id`='&1'", '', $this->getUserId());
	}
	function getWPOC()
	{
		return $this->reCache->getValue('wp_oc');
	}
	function getWPGC()
	{
		return $this->reCache->getValue('wp_gc');
	}
	function getWPNC()
	{
		return $this->reCache->getValue('wp_nc');
	}

	function getUUID()
	{
		return $this->reCache->getValue('uuid');
	}
	function getLastModified()
	{
		return $this->reCache->getValue('last_modified');
	}
	function getDateCreated()
	{
		return $this->reCache->getValue('date_created');
	}
	function getNode()
	{
		return $this->reCache->getValue('node');
	}
	function setNode($value)
	{
		return $this->reCache->setValue('node', $value);
	}
	function setStatus($value)
	{
		if (sql_value("SELECT COUNT(*) FROM `cache_status` WHERE `id`='&1'", 0, $value) == 1)
		{
			return $this->reCache->setValue('status', $value);
		}
		else
		{
			return false;
		}
	}

	function getAnyChanged()
	{
		return $this->reCache->getAnyChanged();
	}

	// return if successfull (with insert)
	function save()
	{
		if ($this->reCache->save())
		{
			sql_slave_exclude();
			return true;
		}
		else
			return false;
	}

	function requireLogPW()
	{
		return $this->reCache->getValue('logpw') != '';
	}

	// TODO: use prepared one way hash
	function validateLogPW($nLogType, $sLogPW)
	{
		if ($sLogPW == '')
			return true;

		if (sql_value("SELECT `require_password` FROM `log_types` WHERE `id`='&1'", 0, $nLogType) == 0)
			return true;

		return ($sLogPW == $this->reCache->getValue('logpw'));
	}

	static function visitCounter($nVisitUserId, $sRemoteAddr, $nCacheId)
	{
		// delete cache_visits older 1 day 60*60*24 = 86400
		sql("DELETE FROM `cache_visits` WHERE `cache_id`='&1' AND `user_id_ip`!='0' AND NOW()-`last_modified`>86400", $nCacheId);

		if ($nVisitUserId==0)
			$sIdentifier = $sRemoteAddr;
		else
			$sIdentifier = $nVisitUserId;

		// note the visit of this user
		sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`) VALUES (&1, '&2', 1)
				ON DUPLICATE KEY UPDATE `count`=`count`+1", $nCacheId, $sIdentifier);

		// if the previous statement does an INSERT, it was the first visit for this user
		if (sql_affected_rows() == 1)
		{
			if ($nVisitUserId != sql_value("SELECT `user_id` FROM `caches` WHERE `cache_id`='&1'", 0, $nCacheId))
			{
				// increment the counter for this cache
				sql("INSERT INTO `cache_visits` (`cache_id`, `user_id_ip`, `count`) VALUES (&1, '0', 1)
						ON DUPLICATE KEY UPDATE `count`=`count`+1", $nCacheId);
			}
		}
	}

	static function getLogsCount($cacheid)
	{
		//prepare the logs
		$rsLogs = sql("SELECT COUNT(*) FROM `cache_logs` WHERE `cache_id`='&1'", $cacheid);
		$rLog = sql_fetch_assoc($rsLogs);		
		sql_free_result($rsLogs);

		return $rLog;
	}


	static function getLogsArray($cacheid, $start, $count)
	{
		//prepare the logs
		$rsLogs = sql("
			SELECT `cache_logs`.`user_id` AS `userid`,
				`cache_logs`.`id` AS `id`,
				`cache_logs`.`uuid` AS `uuid`,
				`cache_logs`.`date` AS `date`,
				`cache_logs`.`type` AS `type`,
				`cache_logs`.`text` AS `text`,
				`cache_logs`.`text_html` AS `texthtml`,
				`cache_logs`.`picture`,
				`user`.`username` AS `username`,
				IF(ISNULL(`cache_rating`.`cache_id`), 0, 1) AS `recommended`
			FROM `cache_logs`
			INNER JOIN `user` ON `user`.`user_id` = `cache_logs`.`user_id`
			LEFT JOIN `cache_rating` ON `cache_logs`.`cache_id`=`cache_rating`.`cache_id` AND `cache_logs`.`user_id`=`cache_rating`.`user_id`
			WHERE `cache_logs`.`cache_id`='&1'
			ORDER BY `cache_logs`.`date` DESC, `cache_logs`.`Id` DESC LIMIT &2, &3", $cacheid, $start+0, $count+0);

		$logs = array();
		while ($rLog = sql_fetch_assoc($rsLogs))
		{
			$pictures = array();
			$rsPictures = sql("SELECT `url`, `title`, `uuid` FROM `pictures` WHERE `object_id`='&1' AND `object_type`=1", $rLog['id']);
			while ($rPicture = sql_fetch_assoc($rsPictures))
				$pictures[] = $rPicture;
			sql_free_result($rsPictures);
			$rLog['pictures'] = $pictures;

			$logs[] = $rLog;
		}
		sql_free_result($rsLogs);

		return $logs;
	}

	function report($userid, $reportreason, $reportnote)
	{
		sql("INSERT INTO cache_reports (`cacheid`, `userid`, `reason`, `note`)
		     VALUES(&1, &2, &3, '&4')",
		     $this->nCacheId, $userid, $reportreason, $reportnote);

		return true;
	}

	function addAdoption($userid)
	{
		if ($this->allowEdit() == false)
			return false;

		if (sql_value("SELECT COUNT(*) FROM `user` WHERE `user_id`='&1' AND `is_active_flag`=1", 0, $userid) == 0)
			return false;

		// same user?
		if ($this->getUserId() == $userid)
			return false;

		sql("INSERT IGNORE INTO `cache_adoption` (`cache_id`, `user_id`) VALUES ('&1', '&2')", $this->nCacheId, $userid);

		return true;
	}

	function cancelAdoption($userid)
	{
		global $login;

		if ($this->allowEdit() == false && $login->userid != $userid)
			return false;

		sql("DELETE FROM `cache_adoption` WHERE `user_id`='&1' AND `cache_id`='&2'", $userid, $this->nCacheId);

		return true;
	}

	function commitAdoption($userid)
	{
		global $login;

		// cache_adoption exists?
		if (sql_value("SELECT COUNT(*) FROM `cache_adoption` WHERE `cache_id`='&1' AND `user_id`='&2'", 0, $this->nCacheId, $userid) == 0)
			return false;

		// new user active?
		if (sql_value("SELECT `is_active_flag` FROM `user` WHERE `user_id`='&1'", 0, $userid) != 1)
			return false;

		sql("INSERT INTO `logentries` (`module`, `eventid`, `userid`, `objectid1`, `objectid2`, `logtext`)
		                       VALUES ('cache', 5, '&1', '&2', '&3', '&4')",
		                       $login->userid, $this->nCacheId, 0, 
		                       'Cache ' . sql_escape($this->nCacheId) . ' has changed the owner from userid ' . sql_escape($this->getUserId()) . ' to ' . sql_escape($userid) . ' by ' . sql_escape($login->userid));
		sql("UPDATE `caches` SET `user_id`='&1' WHERE `cache_id`='&2'", $userid, $this->nCacheId);
		sql("DELETE FROM `cache_adoption` WHERE `cache_id`='&1'", $this->nCacheId);

		$this->reCache->setValue('user_id', $userid);

		return true;
	}

	// true if anyone can view the cache
	function isPublic()
	{
		return (sql_value("SELECT `allow_user_view` FROM `cache_status` WHERE `id`='&1'", 0, $this->getStatus()) == 1);
	}
	function allowView()
	{
		global $login;

		if ($this->isPublic())
			return true;

		$login->verify();

		if (($login->admin & ADMIN_USER) == ADMIN_USER)
			return true;
		else if ($this->getUserId() == $login->userid)
			return true;

		return false;
	}
	function allowEdit()
	{
		global $login;

		$login->verify();
		if ($this->getUserId() == $login->userid)
			return true;

		return false;
	}
	function allowLog()
	{
		global $login;

		$login->verify();
		if ($this->getUserId() == $login->userid)
			return true;

		return (sql_value("SELECT `allow_user_log` FROM `cache_status` WHERE `id`='&1'", 0, $this->getStatus()) == 1);
	}

	function isRecommendedByUser($nUserId)
	{
		return (sql_value("SELECT COUNT(*) FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'", 0, $this->nCacheId, $nUserId) > 0);
	}
	function addRecommendation($nUserId)
	{
		sql("INSERT IGNORE INTO `cache_rating` (`cache_id`, `user_id`) VALUES ('&1', '&2')", $this->nCacheId, $nUserId);
	}
	function removeRecommendation($nUserId)
	{
		sql("DELETE FROM `cache_rating` WHERE `cache_id`='&1' AND `user_id`='&2'", $this->nCacheId, $nUserId);
	}
}
?>