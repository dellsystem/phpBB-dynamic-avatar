<?php
/**
*
* @package Ultimate Points
* @version $Id: points_info.php 571 2009-10-09 11:37:19Z femu $
* @copyright (c) 2009 wuerzi & femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/


if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package Ultimate Points
*/
class points_info
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $check_auth;

		// Add part to bar
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=info"),
			'FORUM_NAME'	=> sprintf($user->lang['POINTS_INFO'], $config['points_name']),
		));

		// Read out all the need values
		$info_attach 			= ($points_values['points_per_attach'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) :  sprintf(number_format_points($points_values['points_per_attach']) . '&nbsp;' . $config['points_name']);
		$info_addtional_attach	= ($points_values['points_per_attach_file'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_attach_file']) . '&nbsp;' . $config['points_name']);
		$info_poll				= ($points_values['points_per_poll'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_poll']) . '&nbsp;' . $config['points_name']);
		$info_poll_option		= ($points_values['points_per_poll_option'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_poll_option']) . '&nbsp;' . $config['points_name']);
		$info_topic_word		= ($points_values['points_per_topic_word'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_topic_word']) . '&nbsp;' . $config['points_name']);
		$info_topic_character	= ($points_values['points_per_topic_character'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_topic_character']) . '&nbsp;' . $config['points_name']);
		$info_post_word			= ($points_values['points_per_post_word'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_post_word']) . '&nbsp;' . $config['points_name']);
		$info_post_character	= ($points_values['points_per_post_character'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_post_character']) . '&nbsp;' . $config['points_name']);
		$info_cost_dl_attach	= ($points_values['points_dl_cost_per_attach'] == 0) ? sprintf($user->lang['INFO_NO_COST'], $config['points_name']) : sprintf(number_format_points($points_values['points_dl_cost_per_attach']) . '&nbsp;' . $config['points_name']);
		$info_cost_warning		= ($points_values['points_per_warn'] == 0) ? sprintf($user->lang['INFO_NO_COST'], $config['points_name']) : sprintf(number_format_points($points_values['points_per_warn']) . '&nbsp;' . $config['points_name']);
		$info_reg_bonus			= ($points_values['reg_points_bonus'] == 0) ? sprintf($user->lang['INFO_NO_POINTS'], $config['points_name']) : sprintf(number_format_points($points_values['reg_points_bonus']) . '&nbsp;' . $config['points_name']);

		$template->assign_vars(array(
			'USER_POINTS'				=> sprintf(number_format_points($user->data['user_points'])),
			'POINTS_NAME'				=> $config['points_name'],
			'LOTTERY_NAME'				=> $points_values['lottery_name'],
			'BANK_NAME'					=> $points_values['bank_name'],
			'POINTS_INFO_DESCRIPTION'	=> sprintf($user->lang['POINTS_INFO_DESCRIPTION'], $config['points_name']),

			'INFO_ATTACH'				=> $info_attach,
			'INFO_ADD_ATTACH'			=> $info_addtional_attach,
			'INFO_POLL'					=> $info_poll,
			'INFO_POLL_OPTION'			=> $info_poll_option,
			'INFO_TOPIC_WORD'			=> $info_topic_word,
			'INFO_TOPIC_CHARACTER'		=> $info_topic_character,
			'INFO_POST_WORD'			=> $info_post_word,
			'INFO_POST_CHARACTER'		=> $info_post_character,
			'INFO_COST_DL_ATTACH'		=> $info_cost_dl_attach,
			'INFO_COST_WARNING'			=> $info_cost_warning,
			'INFO_REG_BONUS'			=> $info_reg_bonus,

			'U_TRANSFER_USER'			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),
			'U_LOGS'					=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
			'U_LOTTERY'					=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
			'U_BANK'					=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
			'U_ROBBERY'					=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),
			'U_INFO'					=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=info"),
			'U_USE_TRANSFER'			=> $auth->acl_get('u_use_transfer'),
			'U_USE_LOGS'				=> $auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY'				=> $auth->acl_get('u_use_lottery'),
			'U_USE_BANK'				=> $auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY'				=> $auth->acl_get('u_use_robbery'),
		));

		// Generate the page
		page_header($user->lang['POINTS_INFO']);

		// Generate the page template
		$template->set_filenames(array(
			'body'	=> 'points/points_info.html'
		));

		page_footer();
	}
}

?>