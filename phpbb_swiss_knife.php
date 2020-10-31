<?php
/***
* Tasks: 
* disable phpBB extensions (that maybe are causing errors)
* create Super User account with a random password
* update existent username with a new random password
* reset cookie_domain and cookie_secure setting
*
* Remove this file when finished: leaving this file in place, expose your phpBB board to high security risks!
*
* Usage: Download and unzip the file phpbb_swiss_knife.php
* May rename the file into something else (not strictly required) (i.e.: mySecretFile.php)
* upload it to your Board's root (i.e.: www.mydomain.com/phpBB3/)
* Point your browser to i.e.: www.mydomain.com/phpBB3/phpbb_swiss_knife.php or to /mySecretFile.php (or whatever you renamed it) and follow instructions.
* Remove this file when finished: leaving this file in place, expose your phpBB board to high security risks!
*
* phpbb_swiss_knife Version 1.0.0 - axe70 2020
* Version 1.0.0 - david63 2017
* Based on modisson.php - Oyabun1 2015
* https://www.phpbb.com/support/docs/en/3.3/kb/article/disabling-all-extensions-at-once/
*
* This script is free software. It comes without any warranty.
* license http://opensource.org/licenses/GPL-2.0 GNU General Public License v2.
*
* Ensure that you have a backup of your Database before to run this tool
*
*/

define('IN_PHPBB', true);

$phpbb_root_path	= (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx 				= substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
include_once($phpbb_root_path . 'phpbb/extension/manager.' . $phpEx);

// Let's see how many extension we can disable
$orig_ext_count = get_active_ext();

// Create a HTML5 page to add some form elements and display stuff
echo '<!DOCTYPE html>';
echo '<html>';
echo '<head>';
echo '<meta http-equiv="content-type" content="text/html; charset=UTF-8" />';
echo '<title>' . basename(__FILE__) . '</title>';

echo '<style type="text/css">
	body {
		font-size: 1em;
		background-color: #C0C0C0 ;
		width: 600px;
		margin: 2em auto 0;
	}
	form {
		text-align: center;
		line-height: 230%;
	}
	fieldset {
		-moz-border-radius:7px;
		border-radius: 7px;
		-webkit-border-radius: 7px;
	}
	
	h1, h2{
	color:#BF0040;
	}
	h3 {
		text-align: center;
	}
	label {
		cursor: pointer;
		background-color: #FFD700;
		border-style: outset;
		border-width: 1px;
		border-radius: 7px;
		border-color: #808080;
		font-size: 1.1em;
		padding: 2px;
		margin: 2px;
	}
	input[type="checkbox"]:disabled {
    	opacity:0;
	}
	input[type="checkbox"] {
		cursor: pointer;
	}
	img.mid {
		display: block;
		margin-top: 1em;
		margin-left: auto;
		margin-right: auto
	}
	/* Buttons based on Pressable CSS Buttons by Joshua Hibbert */
	.button {
		background-image: -webkit-linear-gradient(hsla(0,0%,100%,.05), hsla(0,0%,0%,.1));
		background-image:    -moz-linear-gradient(hsla(0,0%,100%,.05), hsla(0,0%,0%,.1));
		background-image:     -ms-linear-gradient(hsla(0,0%,100%,.05), hsla(0,0%,0%,.1));
		background-image:      -o-linear-gradient(hsla(0,0%,100%,.05), hsla(0,0%,0%,.1));
		background-image:         linear-gradient(hsla(0,0%,100%,.05), hsla(0,0%,0%,.1));
		border: none;
		border-radius: 1.25em;
		box-shadow: inset 0 0 0 1px hsla(0,0%,0%,.25),
					inset 0 2px 0 hsla(0,0%,100%,.1),
					inset 0 1.2em 0 hsla(0,0%,100%,.05),
					inset 0 -.2em 0 hsla(0,0%,100%,.1),
					inset 0 -.25em 0 hsla(0,0%,0%,.5),
					0 .25em .25em hsla(0,0%,0%,.1);
		color: #fff;
		text-shadow: 0 -1px 1px hsla(0,0%,0%,.25);
		cursor: pointer;
		display: inline-block;
		font-family: sans-serif;
		font-size: 1.1em;
		font-weight: bold;
		line-height: 150%;
		margin: 0 .5em;
		padding: .25em .75em .5em;
		position: relative;
		text-decoration: none;
		vertical-align: middle;
	}
	.button:hover {
		outline: none;
	}
	.button:hover, .button:focus {
		box-shadow: inset 0 0 0 1px hsla(0,0%,0%,.25),
					inset 0 2px 0 hsla(0,0%,100%,.1),
					inset 0 1.2em 0 hsla(0,0%,100%,.05),
					inset 0 -.2em 0 hsla(0,0%,100%,.1),
					inset 0 -.25em 0 hsla(0,0%,0%,.5),
					inset 0 0 0 3em hsla(0,0%,100%,.2),
					0 .25em .25em hsla(0,0%,0%,.1);
	}
	.button:active {
		box-shadow: inset 0 0 0 1px hsla(0,0%,0%,.25),
					inset 0 2px 0 hsla(0,0%,100%,.1),
					inset 0 1.2em 0 hsla(0,0%,100%,.05),
					inset 0 0 0 3em hsla(0,0%,100%,.2),
					inset 0 .25em .5em hsla(0,0%,0%,.05),
					0 -1px 1px hsla(0,0%,0%,.1),
					0 1px 1px hsla(0,0%,100%,.25);
		margin-top: .25em;
		outline: none;
		padding-bottom: .5em;
	}
	.green {
		background-color: #228B22;
	}
</style></head><body>';

echo'<h2 style="text-align:center">phpBB swiss knife</h2><h2 style="text-align:center">Results will display at the bottom of the page when the task has been completed</h2><h3>You can execute only one task per time</h3><br /><br />';

// Create a form with a checkbox
echo '<h3>Disable all extensions</h3>';
echo '<strong>Check the selection box and click the Run button.';
echo '<form action="' . basename(__FILE__) . '" method="post" onsubmit="return confirm(\'You are about to disable ' . $orig_ext_count . ' extensions. \n Make sure you first have a database backup. \n If you click OK there is no going back.\')">';
echo '<input type="checkbox" name="chkExt"
	value="Yes" />Disable '. $orig_ext_count . ' extensions&nbsp;';
echo '<p><button type="submit" class="button green";>Run</button></p>';
echo '</form>';
echo '<br /><br />';
		
// Create a form with a checkbox
echo '<h3>Create a new founder Super Admin</h3>';
echo '<strong>Check the selection box and click the Run button.</strong>';
echo '<form action="' . basename(__FILE__) . '" method="post" onsubmit="return confirm(\'You are about to Create a new founder Super Admin. \n Username and password will shown down here \n.\')">';
echo '<input type="checkbox" name="chkSuperAdmin"
	value="Yes" />Create a new founder SuperAdmin&nbsp;&nbsp;';
echo '<p><button type="submit" class="button green";>Run</button></p>';
echo '</form>';
echo '<br /><br />';


// Create a form with a checkbox
echo '<h3>Change the Password of an existent username</h3>';
echo '<strong>Write/paste the username which you want to change password of, then click the Run button.</strong>';
echo '<form action="' . basename(__FILE__) . '" method="post" onsubmit="return confirm(\'You are about to Change password for the provided username. \n Username and password will shown down here \n.\')">';
echo '<input type="text" size="20" name="chkChangeUserPass"
	value="" /> Change existent username password&nbsp;&nbsp;';
echo '<p><button type="submit" class="button green";>Run</button></p>';
echo '</form>';
echo '<br />';

// Create a form with a checkbox for reset cookie domain and ssl forcing
echo '<h3>Reset cookie domain value and disable cookie secure</h3>';
echo '<strong>Write down the cookie domain which you want to change into.<br />For example, it normally should be set as <i>.yourdomain.com</i> or <i>.your.subdomain.com</i></strong><br />Note: to setup as empty the phpBB cookie setting, write down <br /><span style="font-weight:200;color:#BF0040">empty-cookie-setting</span> as value';
echo '<form action="' . basename(__FILE__) . '" method="post" onsubmit="return confirm(\'You are about to Change phpBB cookie domain setting and to disable cookie secure. \n.\')">';
echo '<input type="text" size="20" name="chkPhpbbCookieDomainSetting"
	value="" /> Change cookie domain and disable cookie secure&nbsp;&nbsp;';
echo '<p><button type="submit" class="button green";>Run</button></p>';
echo '</form>';
echo '<br />';

// Use request_var() to get the returned value of the selection
$chk_ext = (request_var('chkExt', ''));
$chk_create_SuperAdmin = (request_var('chkSuperAdmin', ''));
$chk_change_user_pass = (request_var('chkChangeUserPass', ''));
$chkPhpbbCookieDomainSetting = (request_var('chkPhpbbCookieDomainSetting', ''));

// Get the current version from 'includes/constants.php'
$version = PHPBB_VERSION;

// Let's make sure that we are running phpBB > 3.1
if (phpbb_version_compare($version, '3.2.0', '>='))
{
	// Disable extensions
	if($chk_ext == 'Yes')
	{
		// Get the enabled extensions
		$sql = 'SELECT ext_name
			FROM ' . EXT_TABLE . '
			WHERE ext_active = 1';

		$result = $db->sql_query($sql);

		// Now we can try to disable the extensions
		if (!empty($result))
		{
			while ($ext_name = $db->sql_fetchrow($result))
			{
				while ($phpbb_extension_manager->disable_step($ext_name['ext_name']));
			}

			$db->sql_freeresult($result);
		}
		else
		{
			echo '<h3>No active extensions found </h3><h3><span style="color:#BF0040">Delete this file now!</span></h3>';
		}

		// Get count of extensions disabled
		$disabled_ext = $orig_ext_count - get_active_ext();

		// Add disable action to the admin log
		add_log('admin', $disabled_ext . ' extensions disabled');

		echo '<h3>'.$disabled_ext . '  extensions have been disabled
    <h3><span style="color:#BF0040">Delete this file now!</span></h3>';
    
	} elseif ($chk_create_SuperAdmin == 'Yes'){ // Create SuperAdmin
		
		 $rand_pass = str_shuffle('A)B?C!D=EFGHI' . md5(time()) . '(LMN)OPQRSTUVZWY=JK?X');
		 $rand_pass = substr($rand_pass, 5, rand(8,15));
   	//$rand_pass = 'phpbbsuperuser007'; // not rand
		// phpBB 3.3.0 add/support PASSWORD_ARGON2I / and PASSWORD_ARGON2ID if php 7.2.0>
    //$rand_pass = password_hash($rand_pass, PASSWORD_ARGON2I);
		//$rand_suser = str_shuffle('ABCDEFGHILMNOPQRSTUVZabcdefghilmnopqrstuvz');
		//$rand_suser = substr($rand_user, rand(2,5,),rand(3,6));
		//$rand_suser_nice = strtolower($rand_suser);

		$rand_suser = $rand_suser_nice = 'phpbbsuperuser007'; // not rand
		$rand_pass_hash = password_hash($rand_pass, PASSWORD_BCRYPT,['cost' => 12]);

		$sql = "INSERT INTO ". USERS_TABLE ." (user_id, user_type, group_id, username, username_clean, user_regdate, user_password, user_email, user_lang, user_style, user_rank, user_colour, user_posts, user_permissions, user_ip, user_birthday, user_lastpage, user_last_confirm_key, user_post_sortby_type, user_post_sortby_dir, user_topic_sortby_type, user_topic_sortby_dir, user_avatar, user_sig, user_sig_bbcode_uid, user_jabber, user_actkey, user_newpasswd) 
			VALUES ('0', '3', '5', '".$rand_suser."', '".$rand_suser_nice."', 0, '".$rand_pass_hash."', 'rand0-admin0@rand0.example0.you', 'en', 1, 1, 'AA0000', 1, '', '', '', '', '', 't', 'a', 't', 'd', '', '', '', '', '', '') ON DUPLICATE KEY UPDATE user_password = '$rand_pass_hash'";
    $db->sql_query($sql);
	
		echo '<h3><span style="color:#BF0040">Super Admin username:</span> ' . $rand_suser . '<br /><span style="color:#BF0040">Password:</span> '.$rand_pass.'
		<h4><span style="color:#BF0040">Delete this file now!</span>. Then login phpBB ACP with provided credentials, then execute tasks into ACP. May remove this same user through another Super Admin user, when you have finished with your tasks.</h4>
	  <h4>Note: each time page refresh, the password will change.</h4>';
	
	} elseif (!empty($chk_change_user_pass)){ // UPDATE username password
		
		 $rand_pass = str_shuffle('A)B?C!D=EFGHI' . md5(time()) . '(LMN)OPQRSTUVZWY=JK?X');
		 $rand_pass = substr($rand_pass, 5, rand(8,15));
     $chk_change_user_pass = trim($chk_change_user_pass);
		// phpBB 3.3.0 add/support PASSWORD_ARGON2I / and PASSWORD_ARGON2ID if php 7.2.0>
    //$rand_pass = password_hash($rand_pass, PASSWORD_ARGON2I);
		//$rand_suser = str_shuffle('ABCDEFGHILMNOPQRSTUVZabcdefghilmnopqrstuvz');
		//$rand_suser = substr($rand_user, rand(2,5,),rand(3,6));
		//$rand_suser_nice = strtolower($rand_suser);

		$rand_pass_hash = password_hash($rand_pass, PASSWORD_BCRYPT,['cost' => 12]);
		
   	$sql = "UPDATE ". USERS_TABLE. " 
		SET user_password = '" . $db->sql_escape($rand_pass_hash) . "' WHERE username = '" . $db->sql_escape($chk_change_user_pass) . "'";
		$result = $db->sql_query($sql);
		$result = (int) $db->sql_affectedrows();  

	if($result > 0){
		echo '<h3><span style="color:#BF0040">Username: </span> ' . $chk_change_user_pass . '<br /><span style="color:#BF0040">New Password:</span> '.$rand_pass.'</h3>
		<h3>Delete this file now! Then login phpBB ACP with provided username/password.</h3>
	  <h3>Note: each time page refresh, the password will change.</h3>';
	 } else {
		echo '<h3>Username <span style="color:#BF0040">' . $chk_change_user_pass . '</span> do not exists</h3>';
	}
	
	
	} elseif (!empty($chkPhpbbCookieDomainSetting)){ // UPDATE cookie domain setting
		
		$chkPhpbbCookieDomainSetting = $chkPhpbbCookieDomainSetting == 'empty-cookie-setting' ? '' : $chkPhpbbCookieDomainSetting;
   	$sql = "UPDATE ". CONFIG_TABLE. " 
		SET config_value = '" . $db->sql_escape($chkPhpbbCookieDomainSetting) . "' WHERE config_name = 'cookie_domain'";
		$result = $db->sql_query($sql);
		$result0 = (int) $db->sql_affectedrows();
		$sql = "UPDATE ". CONFIG_TABLE. " 
		SET config_value = '0' WHERE config_name = 'cookie_secure'";
		$result1 = $db->sql_query($sql);
		//$result = (int) $db->sql_affectedrows(); 

	if($result0 > 0){
		echo '<h3><span style="color:#BF0040">The phpBB cookie_domain setting has been updated. Delete cookies on your browser (may by clicking into "Delete cookies" link on bottom of your board) and then login</h3>
		<h3>Delete this file now!</h3>';
	 } else {
		echo '<h3>phpBB cookie_domain Update failed</h3>';
	}
	
	
	}
	
	
	
} // END - if (phpbb_version_compare($version, '3.2.0', '>='))

	
	
echo '<br /><br /><br /></body></html>';


// Get count of active extensions
function get_active_ext()
{
	global $db;

	$sql = 'SELECT COUNT(ext_active) AS active_ext
		FROM ' . EXT_TABLE . '
		WHERE ext_active = 1';

	$result		= $db->sql_query($sql);
	$ext_count	= (int)$db->sql_fetchfield('active_ext');

	$db->sql_freeresult($result);

	return $ext_count;
}

