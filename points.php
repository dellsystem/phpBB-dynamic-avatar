<?php
/**
*
* @package Ultimate Points
* @version $Id: points.php 588 2009-10-14 18:37:29Z Wuerzi $
* @copyright (c) 2009 wuerzi & femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
include($phpbb_root_path . 'includes/functions_module.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_privmsgs.' . $phpEx);

$mode	= request_var('mode', '');

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/points');

// Exclude Bots
if ($user->data['is_bot'])
{
	redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
}

// Disable board if the points_install/ directory is still present
if ( !defined('DEBUG_EXTRA') && file_exists($phpbb_root_path . 'install_ultimate_points')	)
{
	// Adjust the message slightly according to the permissions
	if ( $auth->acl_gets('a_') )
	{
		$message = 'POINTS_REMOVE_INSTALL';
	}
	else
	{
		$message = (!empty($config['board_disable_msg'])) ? $config['board_disable_msg'] : 'POINTS_DISABLE';
	}
	trigger_error($message);
}

//Check if you are locked or not
if ( !$auth->acl_get('u_use_points') )
{
    trigger_error('NOT_AUTHORISED');
}

// Get user's information
$check_user = request_var('i', 0);
$check_user = ($check_user == 0) ? $user->data['user_id'] : $check_user;

$sql_array = array(
	'SELECT'	=> '*',
	'FROM'		=> array(
		USERS_TABLE => 'u',
	),
	'WHERE'		=> 'u.user_id = ' . (int) $check_user,
);
$sql = $db->sql_build_query('SELECT', $sql_array);
$result = $db->sql_query($sql);
$checked_user = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$check_auth = new auth();
$check_auth->acl($checked_user);

if ( !$checked_user )
{
	trigger_error('POINTS_NO_USER');
}

// Ultimate Points Version
$version = $config['ultimate_points_version'];

// Check if points system is enabled
if ( !$config['points_enable'] )
{
	trigger_error($points_config['points_disablemsg']);
}

// Add the base entry into the Nav Bar at top
$template->assign_block_vars('navlinks', array(
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx"),
	'FORUM_NAME'	=> sprintf($user->lang['POINTS_TITLE_MAIN'], $config['points_name']),
));

$template->assign_vars(array_change_key_case($checked_user, CASE_UPPER));

$user_name = get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour'], $user->data['username']);

$template->assign_vars(array_merge(array_change_key_case($points_config, CASE_UPPER), array(
	'USER_POINTS'		=> number_format_points ($user->data['user_points']),
	'U_USE_POINTS'		=> $check_auth->acl_get('u_use_points'),
	'U_CHG_POINTS'		=> $check_auth->acl_get('m_chg_points'),
	'POINT_VERS'		=> $version,
	'U_USE_TRANSFER'	=> $check_auth->acl_get('u_use_transfer'),
	'U_USE_LOGS'		=> $check_auth->acl_get('u_use_logs'),
	'U_USE_LOTTERY'		=> $check_auth->acl_get('u_use_lottery'),
	'U_USE_BANK'		=> $check_auth->acl_get('u_use_bank'),
	'U_USE_ROBBERY'		=> $check_auth->acl_get('u_use_robbery'),	
)));
		
$module = new p_master();

switch( $mode ) 
{
	case 'transfer_user':
		$module->load('points', 'transfer_user');
		$module->display("{L_POINTS_TRANSFER}");
	break;

	case 'logs':
	case 'lottery':
	case 'transfer':
	case 'robbery':
	case 'points_edit':
	case 'bank':
	case 'bank_edit':
	case 'info':
		$module->load('points', $mode);
		$module->display("{L_POINTS_}" . strtoupper($mode));
	break;

	default:
		$module->load('points', 'main');
		$module->display("{L_POINTS_OVERVIEW}");
	break;
}

?>