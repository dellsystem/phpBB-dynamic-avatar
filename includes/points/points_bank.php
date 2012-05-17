<?php
/**
*
* @package Ultimate Points
* @version $Id: points_bank.php 594 2009-11-18 09:34:38Z femu $
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
class points_bank
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $check_auth;

		// Check if bank is enabled
		if ( 1 > $points_values['bank_pay_period']  )
		{
			$message = $user->lang['BANK_ERROR_PAYOUTTIME_SHORT'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		if (!$points_config['bank_enable']) 
		{
			$message = $user->lang['BANK_DISABLED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		if ( !$auth->acl_get('u_use_bank') )
		{
			$message = $user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		$withdrawtotal_check = '';

		// Add part to bar
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
			'FORUM_NAME'	=> $points_values['bank_name'],
		));

		// Check, if it's time to pay users
		$time = time();

		if ( ($time - $points_values['bank_last_restocked']) > $points_values['bank_pay_period'] )
		{
			set_points_values('bank_last_restocked', $time);

			// Pay the users
			$sql = 'UPDATE ' . POINTS_BANK_TABLE . '
				SET holding = holding + round(((holding / 100) * ' . $points_values['bank_interest'] . '))
				WHERE holding < ' . $points_values['bank_interestcut'] . '
					OR ' . $points_values['bank_interestcut'] . ' = 0';
			$db->sql_query($sql);

			// Mantain the bank costs
			if ( $points_values['bank_cost'] <> '0' )
			{
				$sql = 'UPDATE ' . POINTS_BANK_TABLE . '
					SET holding = holding - ' . $points_values['bank_cost'] . '
					WHERE holding >= ' . $points_values['bank_cost'] . '';
				$db->sql_query($sql);
			}
		}
 
		$sql_array = array(
			'SELECT'    => '*',
			'FROM'      => array(
				POINTS_BANK_TABLE => 'u',
			),
			'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
		);
		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);

		$action = request_var('action', '');
		add_form_key('bank_action');

		// Default bank info page
		if ( empty($action) )
		{
			$template->set_filenames(array(
				'body' => 'points/points_bank.html'
			));

			if ( !isset($row['holding']) && $user->data['user_id'] > 0 && $user->data['username'] != ANONYMOUS )
			{
				$template->assign_block_vars('no_account', array(
					'USER_NO_ACCOUNT'	=> sprintf($user->lang['BANK_USER_NO_ACCOUNT'], $points_values['bank_name']),
					'OPEN_ACCOUNT'		=> sprintf($user->lang['BANK_OPEN_ACCOUNT'], '<a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank&amp;action=createaccount") . '" title="' . $user->lang['BANK_OPEN_ACCOUNT'] . '!">', '</a>')
				));
			}
			else if ( $user->data['user_id'] > 0 && $user->data['username'] != ANONYMOUS )
			{
				$template->assign_block_vars('has_account', array());
			}

			$sql_array = array(
				'SELECT'    => 'SUM(holding) AS total_holding, count(user_id) AS total_users',
				'FROM'      => array(
					POINTS_BANK_TABLE => 'u',
				),
				'WHERE'		=> 'id > 0',
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$b_row = $db->sql_fetchrow($result);

			$bankholdings 	= ( $b_row['total_holding'] ) ? $b_row['total_holding'] : 0;
			$bankusers 		= $b_row['total_users'];

			$withdrawtotal 	= ( $row['fees'] == 'on' ) ? $row['holding'] - (round($row['holding'] / 100 * $points_values['bank_fees'])) : $row['holding'];

			if ( $row['fees'] == 'on' && $user->lang['BANK_WITHDRAW_RATE'] )
			{
				$template->assign_block_vars('switch_withdraw_fees', array());
			}

			if ( $points_values['bank_min_withdraw'] )
			{
				$template->assign_block_vars('switch_min_with', array());
			}

			if ( $points_values['bank_min_deposit'] )
			{
				$template->assign_block_vars('switch_min_depo', array());
			}

			$banklocation = ' -> <a href="' . append_sid("{$phpbb_root_path}points." . $phpEx) . '" class="nav">' . $points_values['bank_name'] . '</a>';

			$title = $points_values['bank_name'] . '; ' . ( (!is_numeric($row['holding']) ) ? $user->lang['BANK_ACCOUNT_OPENING'] : $user->lang['BANK_DEPOSIT_WITHDRAW'] . ' ' . $config['points_name']);

			page_header($points_values['bank_name']);

			$bank_enable = $points_config['bank_enable'];

			$template->assign_vars(array(
				'BANK_NAME'		=> $points_values['bank_name'],
				'BANKLOCATION' 	=> $banklocation,
				'BANK_OPENED' 	=> $user->format_date($bank_enable),
				'BANK_HOLDINGS' => sprintf(number_format_points($bankholdings)),
				'BANK_ACCOUNTS' => $bankusers,
				'BANK_FEES' 	=> $points_values['bank_fees'],
				'BANK_INTEREST' => $points_values['bank_interest'],
				'BANK_MIN_WITH' => sprintf(number_format_points($points_values['bank_min_withdraw'])),
				'BANK_MIN_DEPO' => sprintf(number_format_points($points_values['bank_min_deposit'])),
				'BANK_MAX_HOLD' => sprintf(number_format_points($points_values['bank_interestcut'])),
				'BANK_TITLE' 	=> $title,
				'POINTS_NAME'	=> $config['points_name'],
				'USER_BALANCE' 	=> sprintf(number_format_points($row['holding'])),
				'USER_GOLD' 	=> $user->data['user_points'],
				'USER_WITHDRAW' => sprintf(number_format($withdrawtotal, 2, '.', '')),

				'U_WITHDRAW' 	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank&amp;action=withdraw"),
				'U_DEPOSIT' 	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank&amp;action=deposit")
			));

		}
		// Start page, where an account is created
		else if ( $action == 'createaccount' )
		{
			if ( !$user->data['is_registered'] )
			{
				login_box();
			}

			$template->set_filenames(array(
				'body' => 'points/points_bank.html'
			));

			if ( is_numeric($row['holding']) )
			{
				trigger_error(' ' . $user->lang['YES_ACCOUNT'] . '!<br /><br />' . sprintf($user->lang['BANK_BACK_TO_BANK'], '<a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">', '</a>') . sprintf('<br />' . $user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>'));
			}
			else
			{
				$sql = 'INSERT INTO ' . POINTS_BANK_TABLE . ' ' . $db->sql_build_array('INSERT', array(
					'user_id'			=> (int) $user->data['user_id'],
					'opentime'			=> time(),
					'fees'				=> 'on',
				));
				$db->sql_query($sql);

				trigger_error(' ' . $user->lang['BANK_WELCOME_BANK'] . ' ' . $points_values['bank_name'] . '! <br />' . $user->lang['BANK_START_BALANCE'] . '<br />' . $user->lang['BANK_YOUR_ACCOUNT'] . '!<br /><br />' . sprintf($user->lang['BANK_BACK_TO_BANK'], '<a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">', '</a>') . sprintf('<br />' . $user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>'));
			}
		}
		// Deposit points
		else if ( $action == 'deposit' )
		{
			if (!check_form_key('bank_action'))
			{
				trigger_error('FORM_INVALID');
			}
		
			$deposit = round(request_var('deposit', 0.00),2);

			if ( !$user->data['is_registered'] )
			{
				login_box();
			}

			if ( $deposit < $points_values['bank_min_deposit'] )
			{			
				$message = sprintf($user->lang['BANK_DEPOSIT_SMALL_AMOUNT'], $points_values['bank_min_deposit'], $config['points_name']) . '<br /><br /><a href="' .  append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}
			else if ( $deposit < 1 )
			{
				$message = $user->lang['BANK_ERROR_DEPOSIT'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}
			else if ( $deposit > $user->data['user_points'] )
			{
				$message = sprintf($user->lang['BANK_ERROR_NOT_ENOUGH_DEPOSIT'], $config['points_name']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);			
			}

			substract_points($user->data['user_id'], $deposit);

			$sql_array = array(
				'SELECT'    => 'holding, totaldeposit',
				'FROM'      => array(
					POINTS_BANK_TABLE => 'b',
				),
				'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			
			$user_bank = $db->sql_fetchrow($result);
			$user_holding = $user_bank['holding'];
			$user_totaldeposit = $user_bank['totaldeposit'];
			$db->sql_freeresult($result);

			$data = array(
				'holding'		=> $user_holding + $deposit,
				'totaldeposit'	=> $user_totaldeposit + $deposit,
			);
		
			$sql = 'UPDATE ' . POINTS_BANK_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $data) . '
				WHERE user_id = ' . (int) $user->data['user_id'];
			$db->sql_query($sql);

			trigger_error(' ' . $user->lang['BANK_HAVE_DEPOSIT'] . ' ' . sprintf(number_format_points($deposit)) . ' ' . $config['points_name'] . ' ' . $user->lang['BANK_TO_ACCOUNT'] . '<br />' . $user->lang['BANK_NEW_BALANCE'] . ' ' . sprintf(number_format_points(($row['holding'] + $deposit))) . '.<br />' . $user->lang['BANK_LEAVE_WITH'] . ' ' . (sprintf(number_format_points($user->data['user_points'] - $deposit))) . ' ' . $config['points_name'] . ' ' . $user->lang['BANK_ON_HAND'] . '.<br /><br />' . sprintf($user->lang['BANK_BACK_TO_BANK'], '<a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">', '</a>') . sprintf('<br />' . $user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>'));
		}
		// Withdraw points
		else if ( $action == 'withdraw' )
		{
			if (!check_form_key('bank_action'))
			{
				trigger_error('FORM_INVALID');
			}		
		
			$withdraw = round(request_var('withdraw', 0.00),2);

			if ( !$user->data['is_registered'] )
			{
				login_box();
			}

			if ( $withdraw < $points_values['bank_min_withdraw'] )
			{			
				$message = sprintf($user->lang['BANK_WITHDRAW_SMALL_AMOUNT'], $points_values['bank_min_withdraw'],  $config['points_name']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);		
			}
			else if ( $withdraw < 1 )
			{
				$message = $user->lang['BANK_ERROR_WITHDRAW'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}
	  
			if ( $row['fees'] == 'on' )
			{
				$withdrawtotal_check 	= ( $row['fees'] == 'on' ) ? $row['holding'] - (round($row['holding'] / 100 * $points_values['bank_fees'])) : $row['holding'];
				$fees = round($row['holding'] / 100 * $points_values['bank_fees']);

				if ( $withdraw == $withdrawtotal_check )
				{
					$withdrawtotal = $withdraw + $fees;
				}
				else
				{
					$withdrawtotal = (round((($withdraw / 100) * $points_values['bank_fees']))) + $withdraw;
				}
			}
			else
			{
				$withdrawtotal = 0;
			}

			if ( $row['holding'] < $withdrawtotal )
			{
				$message = sprintf($user->lang['BANK_ERROR_NOT_ENOUGH_WITHDRAW'], $config['points_name']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Transfer points to users cash account
			add_points($user->data['user_id'], $withdraw);	  

			// Update users bank account
			$sql_array = array(
				'SELECT'    => 'holding, totalwithdrew',
				'FROM'      => array(
					POINTS_BANK_TABLE => 'b',
				),
				'WHERE'		=> 'user_id = ' . (int) $user->data['user_id'],
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			
			$user_bank = $db->sql_fetchrow($result);
			$user_holding = $user_bank['holding'];
			$user_totalwithdrew = $user_bank['totalwithdrew'];
			$db->sql_freeresult($result);

			$data = array(
				'holding'		=> $user_holding - $withdrawtotal,
				'totalwithdrew'	=> $user_totalwithdrew + $withdraw,
			);
		
			$sql = 'UPDATE ' . POINTS_BANK_TABLE . '
				SET ' . $db->sql_build_array('UPDATE', $data) . '
				WHERE user_id = ' . (int) $user->data['user_id'];
			$db->sql_query($sql);

			trigger_error(' ' . $user->lang['BANK_HAVE_WITHDRAW'] . ' ' . sprintf(number_format_points($withdraw)) . ' ' . $config['points_name'] . ' ' . $user->lang['BANK_FROM_ACCOUNT'] . '. <br />' . $user->lang['BANK_NEW_BALANCE'] . ' ' . sprintf(number_format_points(($row['holding'] - $withdrawtotal))) . ' ' . $config['points_name'] . '.<br />' . $user->lang['BANK_NOW_HAVE'] . ' ' . (sprintf(number_format_points($user->data['user_points'] + $withdraw))) . ' ' . $config['points_name'] . ' ' . $user->lang['BANK_ON_HAND'] . '.<br /><br />' . sprintf($user->lang['BANK_BACK_TO_BANK'], '<a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank") . '">', '</a>') . sprintf('<br />' . $user->lang['BANK_BACK_TO_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>'));
		}
		else
		{
			redirect("{$phpbb_root_path}points.$phpEx", "mode=bank");
		}

		// Generate most rich banker to show
		$limit = $points_values['number_show_top_points'];
		$sql_array = array(
			'SELECT'	=> 'u.user_id, u.username, u.user_colour, b.*',

			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(POINTS_BANK_TABLE => 'b'),
					'ON'	=> 'u.user_id = b.user_id'
				),
			),

			'WHERE'		=> 'b.holding > 0',
			'ORDER_BY'	=> 'b.holding DESC, u.username ASC',
		);

		$sql = $db->sql_build_query('SELECT', $sql_array);
		$result = $db->sql_query_limit($sql, $limit);

		while ( $row = $db->sql_fetchrow($result) )
		{
			$template->assign_block_vars('bank', array(
				'USERNAME'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'POINT'		=> sprintf(number_format_points($row['holding'])),
			));
		}
		$db->sql_freeresult($result);

		// Generate the time format
		function time_format($secs, $filter = false)
		{
			global $user;

			$output = '';
			$filter = ($filter) ? explode('|', strtolower($filter)) : false;

			$time_array = array(
				'year'		=> 60 * 60 * 24 * 365,
				'month'		=> 60 * 60 * 24 * 30,
				'week'		=> 60 * 60 * 24 * 7,
				'day'		=> 60 * 60 * 24,
				'hour'		=> 60 * 60,
				'minute'	=> 60,
				'second'	=> 0,
			);

			foreach ( $time_array as $key => $value )
			{
				if ( $filter && !in_array($key, $filter) )
				{
					continue;
				}

				$item = ($value) ? intval(intval($secs) / $value) : intval($secs);
				if ( $item > 0 )
				{
					$secs = $secs - ($item * $value);
					$output .= ' ' . $item . ' ' . (($item > 1) ? $user->lang['TIME_' . strtoupper($key) . 'S'] : $user->lang['TIME_' . strtoupper($key)]);
				}
			}

			return $output;
		}

		$template->assign_vars(array(
			'BANK_INTEREST_PERIOD'	=> time_format($points_values['bank_pay_period']),
			'BANK_COST'				=> sprintf(number_format_points($points_values['bank_cost'])),
			'LOTTERY_NAME'			=> $points_values['lottery_name'],
			'BANK_NAME'				=> $points_values['bank_name'],
			'S_DISPLAY_INDEX'		=> ($points_values['number_show_top_points'] > 0) ? true : false,

			'L_BANK_DESCRIPTION'	=> sprintf($user->lang['BANK_DESCRIPTION'], $config['points_name']),

			'U_TRANSFER_USER'		=> append_sid("{$phpbb_root_path}points.$phpEx", 'mode=transfer_user'),
			'U_LOGS'				=> append_sid("{$phpbb_root_path}points.$phpEx", 'mode=logs'),
			'U_LOTTERY'				=> append_sid("{$phpbb_root_path}points.$phpEx", 'mode=lottery'),
			'U_BANK'				=> append_sid("{$phpbb_root_path}points.$phpEx", 'mode=bank'),
			'U_ROBBERY'				=> append_sid("{$phpbb_root_path}points.$phpEx", 'mode=robbery'),
			'U_INFO'				=> append_sid("{$phpbb_root_path}points.$phpEx", 'mode=info'),
			'U_USE_TRANSFER'		=> $auth->acl_get('u_use_transfer'),
			'U_USE_LOGS'			=> $auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY'			=> $auth->acl_get('u_use_lottery'),
			'U_USE_BANK'			=> $auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY'			=> $auth->acl_get('u_use_robbery'),	
		));

		page_footer();
	}
}

?>