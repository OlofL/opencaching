{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/misc/32x32-home.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Userprofile{/t}" />
	{t 1=$username}Userprofile of %1{/t}
</div>

<div style="text-align: right;padding-right: 22px;">
	<a href="mailto.php?userid={$userid}"><img src="resource2/{$opt.template.style}/images/misc/16x16-email.png" width="16" height="16" border="0" alt="{t}Send E-Mail{/t}" align="middle" /></a>&nbsp;
	[<a href="mailto.php?userid={$userid}">{t}Send E-Mail{/t}</a>]
</div>

<div class="buffer" style="width: 500px;">&nbsp;</div>

<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Userprofile{/t}" /> 
		{t}Userprofile{/t}
	</p>
</div>

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>

	{if $showcountry==true}
		<tr>
			<td><b>{t}Country{/t}:</b></td>
			<td>{$country|escape}</td>
		</tr>
	{/if}

	{if $pmr==1}
		<tr>
			<td style="vertical-align:top;"><b>{t}Others{/t}:</b></td>
			<td>
				<ul>
					{if $pmr==1}
						<li>{t}I'm taking an PMR radio on channel 2 with me{/t}</li>
					{/if}
				</ul>
			</td>
		</tr>
	{/if}

	<tr>
		<td><b>{t}Registered since{/t}:</b></td>
		<td>{$registered|date_format:$opt.format.date}</td>
	</tr>
	<tr>
		<td><b>{t}Last login{/t}:</b></td>
		{if $lastlogin==1}
			<td>{t}Within the last month{/t}</td>
		{elseif $lastlogin==2}
			<td>{t}Between 1 and 6 months{/t}</td>
		{elseif $lastlogin==3}
			<td>{t}Between 6 and 12 months{/t}</td>
		{elseif $lastlogin==4}
			<td>{t}More than 12 months ago{/t}</td>
		{elseif $lastlogin==6}
			<td>{t}The user account has been disabled.{/t}</td>
		{else}
			<td>{t}unknown{/t}</td>
		{/if}
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>

	{foreach from=$useroptions item=optionItem}
		<tr>
			<td style="vertical-align:top;"><b>{$optionItem.name|escape}:</b></td>
				{if $optionItem.option_id==3}
					</tr></table>
				{/if}
			{if $siteSettings.logic.enableHTMLInUserDescription == true}
				<td style="vertical-align:top;">{$optionItem.option_value}</td>
			{else}
				<td style="vertical-align:top;">{$optionItem.option_value|escape|nl2br}</td>
			{/if}
				{if $optionItem.option_id==3}
					<table class="table"><tr>
				{/if}
		</tr>
	{/foreach}

	<tr><td class="spacer" colspan="2"></td></tr>
</table>

<div class="content2-container bg-blue02">
	<p class="content-title-noshade-size2">
		<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}User statistics{/t}" /> 
		{t}User statistics{/t}
	</p>
</div>

<table class="table">
	<tr><td class="spacer" colspan="2"></td></tr>

	<tr>
		<td valign="middle" class="header-small" style="padding-top:5px;padding-bottom:5px">
		<img src="resource2/{$opt.template.style}/images/cacheicon/22x22-traditional.gif" width="22" height="22" align="middle" border="0" alt="{t}Hidden caches{/t}" title="{t}Hidden caches{/t}">&nbsp;<b>{t}Hidden caches{/t}:</b></td>
		<td class="header-small">{$hidden}
			[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;ownerid={$userid}&amp;searchbyowner=">{t}Show all{/t}</a>]
		</td>
	</tr>

	{if $show_statistics==true}
		{foreach from=$userstatshidden item=stats}
			<tr>
				<td>{include file="res_cacheicon_15.tpl" cachetype=$stats.id}{$stats.cachetype|escape}:</td>
				<td>{$stats.anzahl}&nbsp;
				  <span style="color:#666666; font-size:10px;">
				  (<a href="search.php?showresult=1&expert=0&output=HTML&sort=byname&ownerid={$userid}&searchbyowner=&f_inactive=0&cachetype={$stats.id}">{t}show{/t}</a>)
				  </span>
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="2">{t}The user has not hidden any caches yet.{/t}</td>
			</tr>
		{/foreach}
		<tr><td class="spacer" colspan="2"></td></tr>
	{/if}

	<tr>
		<td valign="middle" class="header-small" style="padding-top:5px;padding-bottom:5px">
			<img src="resource2/ocstyle/images/logtype/16x16-found.png" alt="{t}Caches found{/t}" title="{t}Caches found{/t}" />
			&nbsp;<b>{t}Caches found{/t}:</b>
		</td>
		<td class="header-small">
			{$founds}
			[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=1,7">{t}Show all{/t}</a>]
		</td>
	</tr>

	{if $show_statistics==true}
		{foreach from=$userstatsfound item=stats}
			<tr>
				<td>{include file="res_cacheicon_15.tpl" cachetype=$stats.id}{$stats.cachetype|escape}:</td>
				<td>{$stats.anzahl}&nbsp;
				  <span style="color:#666666; font-size:10px;">
				  (<a href="search.php?showresult=1&expert=0&output=HTML&sort=byname&finderid={$userid}&searchbyfinder=&f_inactive=0&cachetype={$stats.id}&amp;logtype=1,7">{t}show{/t}</a>)
				  </span>
				</td>
			</tr>
		{foreachelse}
			<tr>
				<td colspan="2">{t}The user has not found any caches yet.{/t}</td>
			</tr>
		{/foreach}
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td valign="middle" class="header-small" style="padding-top:5px;padding-bottom:5px">
			<img src="resource2/ocstyle/images/logtype/16x16-dnf.png" alt="{t}Caches not found{/t}" title="{t}Caches not found{/t}">&nbsp;&nbsp;&nbsp;<b>{t}Not found{/t}:</b></td>
			<td class="header-small" >{$notfound}
				[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=2">{t}Show all{/t}</a>]
			</td>
		</tr>
		<tr>
			<td valign="middle" class="header-small" style="padding-bottom:5px">
			<img src="resource2/ocstyle/images/logtype/16x16-note.png" alt="{t}Notes{/t}" title="{t}Notes{/t}">&nbsp;&nbsp;&nbsp;<b>{t}Notes{/t}:</b></td>
			<td class="header-small" >{$note}
				[<a href="search.php?showresult=1&amp;expert=0&amp;f_inactive=0&amp;output=HTML&amp;sort=byname&amp;finderid={$userid}&amp;searchbyfinder=&amp;logtype=3">{t}Show all{/t}</a>]
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>
	{/if}

	<tr>
		<td class="header-small">
		<img src="resource2/{$opt.template.style}/images/viewcache/cache-rate.png"  align="middle" border="0" alt="{t}Recommendations{/t}" title="{t}Recommendations{/t}">&nbsp;&nbsp;&nbsp;<b>{t}Recommendations{/t}:</b></td>
		<td class="header-small" >{t 1=$recommended 2=$maxrecommended}%1 of %2 possibles{/t} [<a href="usertops.php?userid={$userid}">{t}Show all{/t}</a>]
		</td>
	</tr>
	<tr><td class="spacer" colspan="2"></td></tr>
</table>
