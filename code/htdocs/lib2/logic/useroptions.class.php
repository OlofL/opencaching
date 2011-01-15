<?php
/***************************************************************************
 *  You can find the license in the docs directory
 *
 *  Unicode Reminder メモ
 *
 *   set has to be commited with save
 *
 ***************************************************************************/
global $opt;

require_once($opt['rootpath'] . 'lib2/logic/class.inputfilter.php');

class useroptions
{

	var $nUserId = 0;
	var $nOptions;

	function __construct($nUserId=ID_NEW)
	{
		$this->nUserId = $nUserId+0;

		if ($nUserId == ID_NEW)
		{
			$rs = sqll('SELECT `id`, `name`, `default_value`, `check_regex`, `option_order`, 0 AS `option_visible`, `internal_use`, `default_value` AS `option_value` 
			             FROM `profile_options`');
		}
		else
		{
			$rs = sqll("SELECT `p`.`id`, `p`.`name`, `p`.`default_value`, `p`.`check_regex`, `p`.`option_order`, IFNULL(`u`.`option_visible`, 0) AS `option_visible`, `p`.`internal_use`, IFNULL(`u`.`option_value`, `p`.`default_value`) AS `option_value`
				           FROM `profile_options` AS `p`
				      LEFT JOIN `user_options` AS `u` ON `p`.`id`=`u`.`option_id` AND (`u`.`user_id` IS NULL OR `u`.`user_id`='&1')", 
				                $this->nUserId);
		}

		while($record = sql_fetch_array($rs))
		{
			$this->nOptions[$record['id']] = $record;
		}

		sql_free_result($rs);
	}

	function getUserId()
	{
		return $this->nUserId;
	}
	function getOptName($pId)
	{
		return $this->nOptions[$pId]['name'];
	}
	function getOptDefault($pId)
	{
		return $this->nOptions[$pId]['default_value'];
	}
	function getOptRegex($pId)
	{
		return $this->nOptions[$pId]['option_regex'];
	}
	function getOptOrder($pId)
	{
		return $this->nOptions[$pId]['option_order'];
	}
	function getOptVisible($pId)
	{
		return $this->nOptions[$pId]['option_visible'];
	}
	function getOptInternal($pId)
	{
		return $this->nOptions[$pId]['internal_use'];
	}
	function getOptValue($pId)
	{
		return $this->nOptions[$pId]['option_value'];
	}

	function setOptVisible($pId, $pValue)
	{
		$pId += 0;
		$pValue += 0;

		if ($pValue != 1 || $this->nOptions[$pId]['internal_use'] == 1)
		{
			$pValue = 0;
		}		

		$this->nOptions[$pId]['option_visible'] = $pValue;
		return true;
	}

	function setOptValue($pId, $pValue)
	{
		$pId += 0;
		if ($this->nOptions[$pId]['check_regex'] == '')
		{
			$this->nOptions[$pId]['option_value'] = $pValue;
			return true;
		}
		else if (ereg($this->nOptions[$pId]['check_regex'], $pValue) || strlen($pValue) == 0)
		{
			$this->nOptions[$pId]['option_value'] = $pValue;
			return true;
		}

		return false;
	}

	// return if successfull (with insert)
	function save()
	{
		foreach($this->nOptions as $record)
		{
			sqll("INSERT INTO `user_options` (`user_id`, `option_id`, `option_visible`, `option_value`) 
			      VALUES ('&1', '&2', '&3', '&4') ON DUPLICATE KEY UPDATE `option_visible`='&3', `option_value`='&4'",
			      $this->nUserId, $record['id'], $record['option_visible'], $this->tidy_html_description($record['option_value']));
	  }
		return true;
	}

  function tidy_html_description($text)
  {
    $options = array("input-encoding" => "utf8", "output-encoding" => "utf8", "output-xhtml" => true, "doctype" => "omit", "show-body-only" => true, "char-encoding" => "utf8", "quote-ampersand" => true, "quote-nbsp" => true, "wrap" => 0);
    $myFilter = new InputFilter(InputFilter::$allowedtags, InputFilter::$allowedattr, 0, 0, 1);
    $the_html = $myFilter->process($text);
    return $the_html;
  }
}
?>
