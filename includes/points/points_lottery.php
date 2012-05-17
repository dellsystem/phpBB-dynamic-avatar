<?php
/**
*
* @package Ultimate Points
* @version $Id: points_lottery.php 580 2009-10-10 12:05:18Z Wuerzi $
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
* @ Ultimate Points
*/
class points_lottery
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $checked_user, $check_auth;

		// Set some variables
		$start	= request_var('start', 0);
		$number	= $points_values['number_show_per_page'];
		add_form_key('lottery_tickets');

		// Check, if lottery is enabled
		if ( !$points_config['lottery_enable'] )
		{
			$message = $user->lang['LOTTERY_DISABLED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Check, if user is allowed to use the lottery
		if ( !$auth->acl_get('u_use_lottery') )
		{
			$message = $user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
			'FORUM_NAME'	=> $points_values['lottery_name'],
		));

		// Add lottery base amount in description
		$template->assign_vars(array(				
			'L_LOTTERY_BASE_AMOUNT'	=> sprintf($user->lang['LOTTERY_DESCRIPTION'], sprintf(number_format_points($points_values['lottery_base_amount'])), $config['points_name']),
		));

		// Recheck, if lottery was run, for those boards only having one user per day and which don't call the index page first
		if ( $points_values['lottery_draw_period'] != 0 && time() > $points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'] )
		{
			if (!function_exists('run_lottery'))
			{
				include($phpbb_root_path . 'includes/points/functions_points.' . $phpEx);
			}
			run_lottery();
		}

		// Check, if user has purchased tickets
		if ( request_var('purchase_ticket', false) && $user->data['user_id'] != ANONYMOUS )
		{
			if (!check_form_key('lottery_tickets'))
			{
				trigger_error('FORM_INVALID');
			}
		
			// How many tickets have been bought?
			$total_tickets_bought = request_var('total_tickets', 0);

			// Check, if user already bought tickets
			$sql_array = array(
				'SELECT'    => 'COUNT(ticket_id) AS number_of_tickets',
				'FROM'      => array(
					POINTS_LOTTERY_TICKETS_TABLE => 't',
				),
				'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$number_tickets = $db->sql_fetchfield('number_of_tickets');
			$db->sql_freeresult($result);

			// Check, if the user tries to buy more tickets than allowed
			if ( $total_tickets_bought > $points_values['lottery_max_tickets'] )
			{
				$message = sprintf($user->lang['LOTTERY_MAX_TICKETS_REACH'], $points_values['lottery_max_tickets']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check in user try to buy negative tickets
			if ( $total_tickets_bought <= 0)
			{
				$message = $user->lang['LOTTERY_NEGATIVE_TICKETS'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if the already bought tickets and the new request are higher than the max set number of tickets
			if ( ($number_tickets + $total_tickets_bought) > $points_values['lottery_max_tickets'] )
			{
				$message = sprintf($user->lang['LOTTERY_MAX_TICKETS_LEFT'], ($points_values['lottery_max_tickets'] - $number_tickets)) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if the user sent an empty value
			if ( !$total_tickets_bought )
			{
				$message = $user->lang['LOTTERY_INVALID_INPUT'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check. if lottery is enabled
			if ( $points_config['lottery_enable'] != 0 && $points_values['lottery_ticket_cost'] != 0 )
			{
				// Grab users total cash
				$sql_array = array(
					'SELECT'    => '*',
					'FROM'      => array(
						USERS_TABLE => 'u',
					),
					'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$purchaser = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				// Check, if the user has enough cash to buy tickets
				if ( $points_values['lottery_ticket_cost'] * $total_tickets_bought > $purchaser['user_points'] )
				{
					$message = $user->lang['LOTTERY_LACK_FUNDS'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}
			}

			// Loop through total purchased tickets and create insert array
			for ( $i = 0, $total_tickets_bought; $i < $total_tickets_bought; $i++ )
			{
				$sql_insert_ary[] = array(
					'user_id'	=> $user->data['user_id'],
				);
			}
			$db->sql_multi_insert(POINTS_LOTTERY_TICKETS_TABLE, $sql_insert_ary);

			// Check again, if lottery is enablled
			if ( $points_config['lottery_enable'] != 0 )
			{
				// Deduct cost
				$viewer_cash = $purchaser['user_points'] - ($points_values['lottery_ticket_cost'] * $total_tickets_bought);
				set_points($user->data['user_id'], $viewer_cash);

				// Update jackpot
				set_points_values('lottery_jackpot', $points_values['lottery_jackpot'] + ($points_values['lottery_ticket_cost'] * $total_tickets_bought));
			}

			$message = $user->lang['LOTTERY_TICKET_PURCHASED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
			
			$template->assign_vars(array(
				'U_ACTION'		=> $this->u_action,
			));			
        }

		// Display main page
		$history_mode = request_var('history', '');

		if ( $history_mode )
		{
			// If no one has ever won, why bother doing anything else?
			if ( $points_values['points_winners_total'] = 0 )
			{
				$message = $user->lang['LOTTERY_NO_WINNERS'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			$total_wins = $points_values['points_winners_total'];

			// Check, if no entries returned, only self search would turn up empty at this point
			if ( $history_mode == 'ego' )
			{
				$sql_array = array(
					'SELECT'    => 'COUNT(id) AS viewer_history',
					'FROM'      => array(
						POINTS_LOTTERY_HISTORY_TABLE => 'h',
					),
					'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$total_wins =  (int) $db->sql_fetchfield('viewer_history');
				$db->sql_freeresult($result);

				if ( $total_wins == 0 )
				{
					$message = $user->lang['LOTTERY_NEVER_WON'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.php", "mode=lottery") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}

				$template->assign_vars(array(
					'U_VIEW_HISTORY'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery&amp;history=all"),
					'U_TRANSFER_USER'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),  
					'U_LOGS'    		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
					'U_LOTTERY'    		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
					'U_BANK'    		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
					'U_ROBBERY'    		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),				
				));
			}

			// Check, if user is viewing all or own entries
			if ( $history_mode == 'all' )
			{
				$sql_array = array(
					'SELECT'    => 'COUNT(id) AS total_entries',
					'FROM'      => array(
						POINTS_LOTTERY_HISTORY_TABLE => 'h',
					),
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$total_entries = (int) $db->sql_fetchfield('total_entries');
				$db->sql_freeresult($result);

				$sql_array = array(
					'SELECT'	=> 'h.*, u.*',
					'FROM'		=> array(
						POINTS_LOTTERY_HISTORY_TABLE	=> 'h',
					),
					'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(USERS_TABLE => 'u'),
							'ON'	=> 'h.user_id = u.user_id'
						),
					),
					'ORDER_BY'	=> 'time DESC',
				);
			}
			else
			{
				$sql_array = array(
					'SELECT'    => 'COUNT(id) AS total_entries',
					'FROM'      => array(
						POINTS_LOTTERY_HISTORY_TABLE => 'h',
					),
					'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$total_entries =  (int) $db->sql_fetchfield('total_entries');
				$db->sql_freeresult($result);

				$sql_array = array(
					'SELECT'	=> 'h.*, u.*',
					'FROM'		=> array(
						POINTS_LOTTERY_HISTORY_TABLE	=> 'h',
					),
					'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(USERS_TABLE => 'u'),
							'ON'	=> 'h.user_id = u.user_id'
						),
					),
					'WHERE'		=> 'h.user_id = ' . (int) $user->data['user_id'],
					'ORDER_BY'	=> 'time DESC',
				);
			}

			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query_limit($sql, $number, $start);
			$row_color = $start;

			while ( $row = $db->sql_fetchrow($result) )
			{
				$row_color++;

				// Check, if winner is user
				if ( $row['user_id'] != 0 )
				{
					$history_member = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
				}
				else
				{
					$history_member = $user->lang['LOTTERY_NO_WINNER'];
				}

				$template->assign_block_vars('history_row',array(
					'NUMBER' 			=> $row_color,
					'U_WINNER_PROFILE'	=> $history_member,
					'WINNER_PROFILE'	=> $history_member,
					'USERNAME' 			=> $row['username'],
					'WINNINGS' 			=> sprintf(number_format_points($row['amount'])),
					'DATE' 				=> $user->format_date($row['time']),
					'ROW_COLOR' 		=> $row_color,
				));

				$template->assign_vars(array(
					'U_VIEW_HISTORY'		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery&amp;history=all"),
					'U_VIEW_SELF_HISTORY'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery&amp;history=ego"),
					'U_INFO'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=info"),
					'U_TRANSFER_USER'  		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),  
					'U_LOGS'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
					'U_LOTTERY'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
					'U_BANK'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
					'U_ROBBERY'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),				
				));
			}

			// Viewing a history page
			$template->assign_vars(array(
				'CASH_NAME'				=> $config['points_name'],
				'PAGINATION' 			=> generate_pagination(append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery&amp;history=$history_mode"), $total_entries, $number, $start),
				'PAGE_NUMBER'			=> on_page($total_entries, $number, $start),
				'LOTTERY_NAME'			=> $points_values['lottery_name'],
				'BANK_NAME'				=> $points_values['bank_name'],
				'L_TOTAL_ENTRIES'		=> ($total_entries == 1) ? $user->lang['POINTS_LOG_SINGLE'] : sprintf($user->lang['POINTS_LOG_MULTI'], $total_entries),

				'S_VIEW_HISTORY'		=> true,

				'U_BACK_TO_LOTTERY'		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
				'U_VIEW_SELF_HISTORY'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery&amp;history=ego"),
				'U_TRANSFER_USER'  		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),  
				'U_LOGS'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
				'U_LOTTERY'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
				'U_BANK'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
				'U_ROBBERY'    			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),
			));

		}
		else
		{
			// Show main lottery page
			$viewer_total_tickets = '';
			if ( $user->data['user_id'] != ANONYMOUS )
			{
				//Select total tickets viewer owns
				$sql_array = array(
					'SELECT'    => 'COUNT(ticket_id) AS num_tickets',
					'FROM'      => array(
						POINTS_LOTTERY_TICKETS_TABLE => 'h',
					),
					'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$viewer_total_tickets = (int) $db->sql_fetchfield('num_tickets');
				$db->sql_freeresult($result);
			}

			// User color selection
			$sql_array = array(
				'SELECT'    => 'user_id, username, user_colour',
				'FROM'      => array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_id = ' . (int) $points_values['lottery_prev_winner_id'],
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);

			if ( $row == NULL )
			{
				$username_colored = $user->lang['LOTTERY_NO_WINNER'];
			}
			else
			{
				$username_colored = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']);
			}

			// Check, if previous winner is a user
			if ( $points_values['lottery_prev_winner_id'] != 0 )
			{
				$link_member = append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $points_values['lottery_prev_winner_id']);
			}
			else
			{
				$link_member = '';
			}

			// Select the total number of tickets
			$sql_array = array(
				'SELECT'    => 'COUNT(ticket_id) AS no_of_tickets',
				'FROM'      => array(
					POINTS_LOTTERY_TICKETS_TABLE => 't',
				),
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$no_of_tickets = $row['no_of_tickets'];
			$db->sql_freeresult($result);

			// Select the total number of players
			$sql_array = array(
				'SELECT'    => 'user_id',
				'FROM'      => array(
					POINTS_LOTTERY_TICKETS_TABLE => 't',
				),
			);
			$sql = $db->sql_build_query('SELECT_DISTINCT', $sql_array);
			$result = $db->sql_query($sql);
			$no_of_players = 0;

			while ($row = $db->sql_fetchrow($result))
			{
			   $no_of_players += 1;
			}
			$db->sql_freeresult($result);
 
			$template->assign_vars(array( 
				'JACKPOT'			 	=> sprintf(number_format_points($points_values['lottery_jackpot']), $config['points_name']),
				'POINTS_NAME'			=> $config['points_name'],
				'TICKET_COST'		 	=> sprintf(number_format_points($points_values['lottery_ticket_cost'])),
				'PREVIOUS_WINNER'	 	=> $username_colored,
				'NEXT_DRAWING'          => $user->format_date($points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'], false, true),
				'LOTTERY_NAME'			=> $points_values['lottery_name'],
				'BANK_NAME'				=> $points_values['bank_name'],
				'VIEWER_TICKETS_TOTAL'  => $viewer_total_tickets,
				'LOTTERY_TICKETS'		=> $no_of_tickets,
				'LOTTERY_PLAYERS'		=> $no_of_players,
				'MAX_TICKETS'			=> $points_values['lottery_max_tickets'],

				'S_PURCHASE_SINGLE'		=> (($viewer_total_tickets == 0) && ($points_config['lottery_multi_ticket_enable'] == 0) && ($points_config['lottery_enable'] == 1)) ? true : false,
				'S_PURCHASE_MULTI'		=> (($viewer_total_tickets < $points_values['lottery_max_tickets']) && ($points_config['lottery_multi_ticket_enable'] == 1) && ($points_config['lottery_enable'] == 1)) ? true : false,

				'S_MULTI_TICKETS'		=> ($points_config['lottery_multi_ticket_enable'] == 1) ? true : false,
				'S_LOTTERY_ENABLE'		=> ($points_config['lottery_enable'] == 1) ? true : false,
				'S_DRAWING_ENABLED'		=> ($points_values['lottery_draw_period']) ? true : false,

				'U_PREVIOUS_WINNER'		=> $link_member,
				'U_VIEW_HISTORY'		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery&amp;history=all"),
				'U_VIEW_SELF_HISTORY'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery&amp;history=ego"),
				'U_TRANSFER_USER'		=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),
				'U_LOGS'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
				'U_LOTTERY'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
				'U_BANK'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
				'U_ROBBERY'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),
				'U_INFO'				=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=info"),
				'U_USE_TRANSFER'		=> $auth->acl_get('u_use_transfer'),
				'U_USE_LOGS'			=> $auth->acl_get('u_use_logs'),
				'U_USE_LOTTERY'			=> $auth->acl_get('u_use_lottery'),
				'U_USE_BANK'			=> $auth->acl_get('u_use_bank'),
				'U_USE_ROBBERY'			=> $auth->acl_get('u_use_robbery'),	
				'USER_POINTS'			=> sprintf(number_format_points($checked_user['user_points'])),
			));
		}

		// Generate the page header
		page_header($points_values['lottery_name']);

		// Generate the page template
		$template->set_filenames(array(
			'body' => 'points/points_lottery.html',
		));

		page_footer();
	}
}

?>