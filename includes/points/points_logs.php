<?php
/**
*
* @package Ultimate Points
* @version $Id: points_logs.php 569 2009-10-09 09:35:51Z femu $
* @copyright (c) 2009 wuerzi & femu
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
class points_logs
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $checked_user, $check_auth;

		// Check if user is allowed to use the logs
		if ( !$auth->acl_get('u_use_logs') )
		{
			$message = $user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
			'FORUM_NAME'	=> sprintf($user->lang['LOGS_TITLE'], $config['points_name']),
		));

		// Preparing the sort order
		$start 			= request_var('start', 0);
		$number 		= $points_values['number_show_per_page'];

		$sort_days		= request_var('st', 0);
		$sort_key		= request_var('sk', 'date');
		$sort_dir		= request_var('sd', 'd');
		$limit_days 	= array(0 => $user->lang['ALL_POSTS'], 1 => $user->lang['1_DAY'], 7 => $user->lang['7_DAYS'], 14 => $user->lang['2_WEEKS'], 30 => $user->lang['1_MONTH'], 90 => $user->lang['3_MONTHS'], 180 => $user->lang['6_MONTHS'], 365 => $user->lang['1_YEAR']);

		$sort_by_text	= array('date' => $user->lang['LOGS_SORT_DATE'], 'to' => $user->lang['LOGS_SORT_TONAME'], 'from' => $user->lang['LOGS_SORT_FROMNAME'], 'comment' => $user->lang['LOGS_SORT_COMMENT']);
		$sort_by_sql 	= array('date' => 'point_date', 'to' => 'point_recv', 'from' => 'point_send', 'comment' => 'point_comment');

		$s_limit_days = $s_sort_key = $s_sort_dir = $u_sort_param = '';
		gen_sort_selects($limit_days, $sort_by_text, $sort_days, $sort_key, $sort_dir, $s_limit_days, $s_sort_key, $s_sort_dir, $u_sort_param);
		$sql_sort_order = $sort_by_sql[$sort_key] . ' ' . (($sort_dir == 'd') ? 'DESC' : 'ASC');

		// The different log types
		$types	=	array(
			0			=>	'--',
			1			=>	$user->lang['LOGS_RECV'],
			2			=>	$user->lang['LOGS_SENT'],
		);

		// Grab the total amount of logs for this user
		$sql_array = array(
			'SELECT'    => 'COUNT(*) AS total',
			'FROM'      => array(
				POINTS_LOG_TABLE => 'l',
			),
			'WHERE'		=> 'point_send = ' . (int) $user->data['user_id'] . '
				OR point_recv = ' . (int) $user->data['user_id'],
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$max = (int) $db->sql_fetchfield('total');

		// Grab the actual logs based on all account movements
		$sql_array = array(
			'SELECT'    => '*',
			'FROM'      => array(
				POINTS_LOG_TABLE => 'l',
			),
			'WHERE'		=> 'point_send = ' . (int) $user->data['user_id'] . '
				OR point_recv = ' . (int) $user->data['user_id'],
			'ORDER_BY'	=> $sql_sort_order,
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $number, $start);

		// Start looping all the logs
		while ( $row = $db->sql_fetchrow($result) )
		{
			switch ( $row['point_type'] )
			{
				case 1: //Transfer
					$transfer_user = ($row['point_send'] == $checked_user['user_id']) ? $row['point_recv'] : $row['point_send'];
					$sql_array = array(
						'SELECT'    => '*',
						'FROM'      => array(
							USERS_TABLE => 'u',
						),
						'WHERE'		=> 'user_id = ' . (int) $transfer_user,
					);
					$sql = $db->sql_build_query('SELECT', $sql_array);
					$result1 = $db->sql_query($sql);
					$opponent = $db->sql_fetchrow($result1);
					$db->sql_freeresult($result1);

					if ( $row['point_send'] == $checked_user['user_id'])
					{
						$who = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']) . "<br />(" . number_format_points($row['point_sendold']) . "->" . number_format_points($row['point_sendold'] - $row['point_amount']) . ")";
						$to = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']) . "<br />(" . number_format_points($row['point_recvold']) . "->" . number_format_points($row['point_recvold'] + $row['point_amount']) . ")";
						$rows = 2;
					}
					else
					{
						$to = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']) . "<br />(" . number_format_points($row['point_recvold']) . "->" . number_format_points($row['point_recvold'] + $row['point_amount']) . ")";
						$who = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']) . "<br />(" . number_format_points($row['point_sendold']) . "->" . 	number_format_points($row['point_sendold'] - $row['point_amount']) . ")";
						$rows = 1;
					}
					$who .= " (-" . number_format_points($row['point_amount']) . ")";
					$to .= " (+" . number_format_points($row['point_amount']) . ")";
				break;

				case 2: //Locked
					$who = get_username_string('full', $opponent['user_id'], $opponent['username'], $opponent['user_colour']);
					$to = "--";
				break;
			}

			// Add the items to the template
			$template->assign_block_vars('logs', array(
				'DATE'		=>	$user->format_date($row['point_date']),
				'COMMENT'	=>	nl2br($row['point_comment']),
				'TYPE'		=>	$types[$rows],
				'ROW'		=>	$rows,
				'WHO'		=>	$who,
				'TO'		=>	$to,
			));
		}
		$db->sql_freeresult($result);

		// Generate the page template
		$template->assign_vars(array(
			'PAGINATION'		=> generate_pagination(append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs&amp;sk=$sort_key&amp;sd=$sort_dir"), $max, $number, $start),
			'PAGE_NUMBER' 		=> on_page($max, $number, $start),
			'LOTTERY_NAME'		=> $points_values['lottery_name'],
			'BANK_NAME'			=> $points_values['bank_name'],

			'L_TOTAL_ENTRIES'	=> ($max == 1) ? $user->lang['POINTS_LOG_SINGLE'] : sprintf($user->lang['POINTS_LOG_MULTI'], $max),

			'S_LOGS_ACTION' 	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
			'S_SELECT_SORT_DIR'	=> $s_sort_dir,
			'S_SELECT_SORT_KEY'	=> $s_sort_key,

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

		// Generate the page header
		page_header(sprintf($user->lang['LOGS_TITLE'], $checked_user['username']));

		// Generate the page template
		$template->set_filenames(array(
			'body' => 'points/points_logs.html',
		));

		page_footer();
	}
}

?>