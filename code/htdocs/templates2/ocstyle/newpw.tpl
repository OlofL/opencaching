{***************************************************************************
*  You can find the license in the docs directory
*
*  Unicode Reminder メモ
***************************************************************************}
{* OCSTYLE *}
<div class="content2-pagetitle">
	<img src="resource2/{$opt.template.style}/images/profile/32x32-profile.png" style="align: left; margin-right: 10px;" width="32" height="32" alt="{t}Change password{/t}" />
	{t}Change password{/t}
</div>

<form action="newpw.php" method="post" style="display: inline;">
	<table class="table">
		<tr>
			<td><b>{t}Step 1{/t}</b></td>
		</tr>
		<tr>
			<td class="help" colspan="2">
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
				{t}To change your password, you have to request a security code which will be sent to you via E-Mail.{/t}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		<tr>
			<td>{t}E-Mail-Address:{/t}</td>
			<td>
				<input name="email" type="text" value="{$emailrq|escape}" maglength="60" class="input200" />
			</td>
		</tr>
		<tr>
			<td width="150px">&nbsp;</td>
			<td>
        <input type="submit" name="rqcode" value="{t}Request code{/t}" class="formbuttons" />
			</td>
		</tr>
		{if $emailErrorNotFound==true}
			<tr><td width="150px">&nbsp;</td><td><span class="errormsg">{t}This E-Mail-Address is not registered with a valid username.{/t}</span></td></tr>
		{elseif $emailRequested==true}
			<tr><td width="150px">&nbsp;</td><td><span class="successmsg">{t}An E-Mail was sent to you with the security code.{/t}</span></td></tr>
		{elseif $emailErrorUnknown==true}
			<tr><td width="150px">&nbsp;</td><td><span class="errormsg">{t}An unknown error occured. The security code could not be sent to you.{/t}</span></td></tr>
		{/if}
		<tr><td class="spacer" colspan="2"></td></tr>
	</table>
</form>

<form action="newpw.php" method="post" style="display: inline;">
	<table class="table">
		<colgroup>
			<col width="150">
			<col>
		</colgroup>
		<tr>
			<td><b>{t}Step 2{/t}</b></td>
		</tr>
		<tr>
			<td class="help" colspan="2">
				<img src="resource2/{$opt.template.style}/images/misc/hint.gif" border="0" width="15" height="11" align="middle" />
				{t}Please enter your E-Mail-Address, the security code you received and your new password. The security code is only 3 days valid. You have to request a new one after that time.{/t}
			</td>
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>

		{if $errorUnknown==true}
			<tr><td width="150px">&nbsp;</td><td><span class="errormsg">{t}An unkown error occured.{/t}</span></td></tr>
		{/if}
		<tr>
			<td>{t}E-Mail-Address:{/t}</td>
			<td>
				<input name="email" type="text" value="{$emailch|escape}" maglength="60" class="input200" />
			</td>
		</tr>
		{if $emailRqErrorNotFound==true}
			<tr><td width="150px">&nbsp;</td><td><span class="errormsg">{t}This E-Mail-Address is not registered with a valid username.{/t}</span></td></tr>
		{elseif $notActiveError==true}
			<tr><td width="150px">&nbsp;</td><td><span class="errormsg">{t}The user account is not activated.{/t}</span></td></tr>
		{/if}
		<tr>
			<td>{t}Security code:{/t}</td>
			<td>
      	<input name="code" type="text" value="{$code|escape}" maglength="60" class="input100" />
			</td>
		</tr>
		{if $codeErrorDate==true}
			<tr><td width="150px">&nbsp;</td><td><span class="errormsg">{t}The security code is expired. Request a new one.{/t}</span></td></tr>
		{elseif $codeError==true}
			<tr><td width="150px">&nbsp;</td><td><span class="errormsg">{t}The security code does not match.{/t}</span></td></tr>
		{/if}
		<tr>
			<td>{t}New password{/t}</td>
			<td>
				<input name="password1" type="password" value="" maxlength="60" class="input120" />
				{if $passwordNotMatch==true}
					<span class="errormsg">{t}The passwords did not match.{/t}</span>
				{elseif $passwordError==true}
					<span class="errormsg">{t}The password contains invalid chars.{/t}</span>
				{/if}
			</td>
		</tr>
		<tr>
			<td>{t}Please repeat:{/t}</td>
			<td>
				<input name="password2" type="password" value="" maxlength="60" class="input120" />
			</td>	
		</tr>
		<tr><td class="spacer" colspan="2"></td></tr>
		{if $passwordChanged==true}
			<tr><td width="150px">&nbsp;</td><td><span class="successmsg">{t}The password has been changed, you can now <a href="login.php">login</a> with the new password.{/t}</span></td></tr>
		{/if}
		<tr><td class="spacer" colspan="2"></td></tr>
		<tr>
			<td class="header-small" colspan="2">
				<input type="reset" name="cancel" value="{t}Reset{/t}" class="formbuttons"  />&nbsp;&nbsp;
				<input type="submit" name="changepw" value="{t}Change{/t}" class="formbuttons" />
			</td>
		</tr>
	</table>
</form>