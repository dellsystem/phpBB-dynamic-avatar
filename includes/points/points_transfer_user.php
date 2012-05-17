<?php
/**
*
* @package Ultimate Points
* @version $Id: points_transfer_user.php 594 2009-11-18 09:34:38Z femu $
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
class points_transfer_user
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $checked_user, $check_auth;

		add_form_key('transfer_user');

		// Grab the message variable
		$message = request_var('comment', '', true);

		// Check, if transferring is allowed
		if ( !$points_config['transfer_enable'] ) 
		{
			$message = $user->lang['TRANSFER_REASON_TRANSFER'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Check, if user is allowed to use the transfer module
		if ( !$auth->acl_get('u_use_transfer') )
		{
			$message = $user->lang['NOT_AUTHORISED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),
			'FORUM_NAME'	=>	sprintf($user->lang['TRANSFER_TITLE'], $config['points_name']),
		));

		if ( isset($_POST['submit']) ) 
		{
			if (!check_form_key('transfer_user'))
			{
				trigger_error('FORM_INVALID');
			}		

			// Grab need variables for the transfer
			$am 		= round(request_var('amount', 0.00),2);
			$comment	= request_var('comment', '', true);
			$username1 	= request_var('username', '', true);
			$username 	= strtolower($username1);

			// Select the user data to transfer to
			$sql_array = array(
				'SELECT'    => '*',
				'FROM'      => array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'username_clean = "' . $db->sql_escape(utf8_clean_string($username)) . '"',
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$transfer_user = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			if ( $transfer_user == NULL )
			{
				$message = $user->lang['TRANSFER_NO_USER_RETURN'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Select the old user_points from user_id to transfer to
			$sql_array = array(
				'SELECT'    => 'user_points',
				'FROM'      => array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_id = ' . (int) $transfer_user['user_id'],
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$transfer_user_old_points = (int) $db->sql_fetchfield('user_points');
			$db->sql_freeresult($result);

			// Check, if the sender has enough cash
			if ( $user->data['user_points'] < $am )
			{
				$message = sprintf($user->lang['TRANSFER_REASON_MINPOINTS'], $config['points_name']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if the amount is 0 or below
			if ( $am <= 0 )
			{
				$message = sprintf($user->lang['TRANSFER_REASON_UNDERZERO'], $config['points_name']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Check, if user is trying to send to himself
			if ( $user->data['user_id'] == $transfer_user['user_id'] )
			{
				$message = sprintf($user->lang['TRANSFER_REASON_YOURSELF'], $config['points_name']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Add cash to receiver
			add_points($transfer_user['user_id'], $am);

			// Remove cash from sender
			substract_points($user->data['user_id'], $am);

			// Get current time for log
			$current_time = time();

			// Add transferring information to the log
			$text = utf8_normalize_nfc($message);

			$sql = 'INSERT INTO ' . POINTS_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'point_send'	=> (int) $user->data['user_id'],
				'point_recv'	=> (int) $transfer_user['user_id'],
				'point_amount'	=> $am,
				'point_sendold'	=> $user->data['user_points'] ,
				'point_recvold'	=> $transfer_user_old_points,
				'point_comment'	=> $text,
				'point_type'	=> '1',
				'point_date'	=> $current_time,
			));
			$db->sql_query($sql);

			// Send pm to receiver, if PM is enabled
			if ( !$points_config['transfer_pm_enable'] == 0 && $transfer_user['user_allow_pm'])
			{
				// Select the receiver language
				$transfer_user['user_lang'] = (file_exists($phpbb_root_path . 'language/' . $transfer_user['user_lang'] . "/mods/points.$phpEx")) ? $transfer_user['user_lang'] : $config['default_lang'];

				// load receivers language
				include($phpbb_root_path . 'language/' . basename($transfer_user['user_lang']) . "/mods/points.$phpEx");

				$points_name = $config['points_name'];
				$comment = $db->sql_escape($comment);
				$pm_subject	= utf8_normalize_nfc(sprintf($lang['TRANSFER_PM_SUBJECT']));
				$pm_text	= utf8_normalize_nfc(sprintf($lang['TRANSFER_PM_BODY'], $am, $points_name, $text));

				$poll = $uid = $bitfield = $options = '';
				generate_text_for_storage($pm_subject, $uid, $bitfield, $options, false, false, false);
				generate_text_for_storage($pm_text, $uid, $bitfield, $options, true, true, true);

				$pm_data = array(
					'address_list'		=> array ('u' => array($transfer_user['user_id'] => 'to')),
					'from_user_id'		=> $user->data['user_id'],
					'from_username'		=> $user->data['username'],
					'icon_id'			=> 0,
					'from_user_ip'		=> '',

					'enable_bbcode'		=> true,
					'enable_smilies'	=> true,
					'enable_urls'		=> true,
					'enable_sig'		=> true,

					'message'			=> $pm_text,
					'bbcode_bitfield'	=> $bitfield,
					'bbcode_uid'		=> $uid,
				);

				submit_pm('post', $pm_subject, $pm_data, false);
			}

			// Change $username back to regular username
			$sql_array = array(
				'SELECT'    => 'username',
				'FROM'      => array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_id = ' . (int) $transfer_user['user_id'],
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);

			$result = $db->sql_query($sql);
			$show_user = $db->sql_fetchfield('username');
			$db->sql_freeresult($result);

			// Show the successful transfer message
			$message = sprintf($user->lang['TRANSFER_REASON_TRANSUCC'], number_format_points($am), $config['points_name'], $show_user) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
			
			$template->assign_vars(array(
				'U_ACTION'				=> $this->u_action,
			));				
		}

		$template->assign_vars(array(
			'USER_POINTS'				=> sprintf(number_format_points($checked_user['user_points'])),
			'POINTS_NAME'				=> $config['points_name'],
			'POINTS_COMMENTS'			=> ($points_config['comments_enable']) ? true : false,
			'LOTTERY_NAME'				=> $points_values['lottery_name'],
			'BANK_NAME'					=> $points_values['bank_name'],

			'L_TRANSFER_DESCRIPTION'	=> sprintf($user->lang['TRANSFER_DESCRIPTION'], $config['points_name']),

			'U_TRANSFER_USER'        	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),
			'U_LOGS'        			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=logs"),
			'U_LOTTERY'        			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=lottery"),
			'U_BANK'        			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank"),
			'U_ROBBERY'        			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=robbery"),
			'U_INFO'					=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=info"),
			'U_FIND_USERNAME'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=searchuser&amp;form=post&amp;field=username"),
			'U_USE_TRANSFER'			=> $auth->acl_get('u_use_transfer'),
			'U_USE_LOGS'				=> $auth->acl_get('u_use_logs'),
			'U_USE_LOTTERY'				=> $auth->acl_get('u_use_lottery'),
			'U_USE_BANK'				=> $auth->acl_get('u_use_bank'),
			'U_USE_ROBBERY'				=> $auth->acl_get('u_use_robbery'),	

			'S_ALLOW_SEND_PM'			=> $auth->acl_get('u_sendpm'),
			));

		// Generate the page
		page_header(sprintf($user->lang['TRANSFER_TITLE'], $config['points_name']));

		// Generate the page template
		$template->set_filenames(array(
			'body' => 'points/points_transfer_user.html',
		));

		page_footer();
	}
}

?>