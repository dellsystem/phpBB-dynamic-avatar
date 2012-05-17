<?php
/**
*
* @package Ultimate Points
* @version $Id: points_main.php 569 2009-10-09 09:35:51Z femu $
* @copyright (c) 2009 wuerzi 6 femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package Ultimate Points
*/
class points_main
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $checked_user, $check_auth;

		// Select user's bank holding
		$sql_array = array(
			'SELECT'    => '*',
			'FROM'      => array(
				POINTS_BANK_TABLE => 'b',
			),
			'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		// Select user's lottery tickets
		$viewer_total_tickets = '';
		if ( $user->data['user_id'] != ANONYMOUS )
		{
			$sql_array = array(
				'SELECT'    => 'COUNT(ticket_id) AS num_tickets',
				'FROM'      => array(
					POINTS_LOTTERY_TICKETS_TABLE => 't',
				),
				'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$viewer_total_tickets = (int) $db->sql_fetchfield('num_tickets');
			$db->sql_freeresult($result);
		}

		// Generate the page header
		page_header(sprintf($user->lang['POINTS_TITLE_MAIN'], $config['points_name']));

		$user_name = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']);

		// Generate some language stuff, dependig on the fact, if user has a bank account or not
		if ( ($row['user_id'] != $user->data['user_id']) || ($row['holding'] < 1) )
		{
			$template->assign_vars(array(
				'L_MAIN_ON_HAND'				=>	sprintf($user->lang['MAIN_ON_HAND'], number_format_points($checked_user['user_points']), $config['points_name']),
				'L_MAIN_HELLO_USERNAME'			=>  sprintf($user->lang['MAIN_HELLO_USERNAME'], $user_name),
				'L_MAIN_LOTTERY_TICKETS'		=>	sprintf($user->lang['MAIN_LOTTERY_TICKETS'], $viewer_total_tickets),
				)
			);
		}
		else
		{
			$template->assign_vars(array(
				'L_MAIN_ON_HAND'				=>	$auth->acl_get('u_use_points') ? sprintf($user->lang['MAIN_ON_HAND'],  number_format_points($checked_user['user_points']), $config['points_name']) : '',
				'L_MAIN_HELLO_USERNAME'			=>  sprintf($user->lang['MAIN_HELLO_USERNAME'], $user_name),
				'L_MAIN_LOTTERY_TICKETS'		=>	$auth->acl_get('u_use_lottery') ? sprintf($user->lang['MAIN_LOTTERY_TICKETS'], $viewer_total_tickets) : '',
				)
			);

			if ( $auth->acl_get('u_use_bank') )
			{
				$template->assign_block_vars('has_bank_account', array(
					'L_MAIN_BANK_HAVE'				=>	sprintf($user->lang['MAIN_BANK_HAVE'], number_format_points($row['holding']), $config['points_name']),
				));
			}
		}

		// Generate most rich users
		$limit = $points_values['number_show_top_points'];
		$sql_array = array(
			'SELECT'    => 'user_id, username, user_colour, user_points',
			'FROM'      => array(
				USERS_TABLE => 'u',
			),
			'WHERE'		=> 'user_points > 0',
			'ORDER_BY'	=> 'user_points DESC, username_clean ASC'
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $limit);

		while ( $row = $db->sql_fetchrow($result) )
		{
			$template->assign_block_vars('points', array(
				'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'POINT'		=> sprintf(number_format_points($row['user_points'])),
			));
		}
		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'LOTTERY_NAME'		=> $points_values['lottery_name'],
			'BANK_NAME'			=> $points_values['bank_name'],
			'S_DISPLAY_INDEX'	=> ($points_values['number_show_top_points'] > 0) ? true : false,

			'U_TRANSFER_USER'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),
			'U_LOGS'			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
			'U_LOTTERY'			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
			'U_BANK'			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
			'U_ROBBERY'			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),
			'U_INFO'			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=info"),
			'U_USE_TRANSFER'	=> $auth->acl_get('u_use_transfer'),
			'U_USE_LOGS'		=> $auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY'		=> $auth->acl_get('u_use_lottery'),
			'U_USE_BANK'		=> $auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY'		=> $auth->acl_get('u_use_robbery'),	
		));

		// Generate the page template
		$template->set_filenames(array(
			'body' => 'points/points_main.html',
		));

		page_footer();
	}
}

?>