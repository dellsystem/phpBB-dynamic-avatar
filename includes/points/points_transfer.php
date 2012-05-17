<?php
/**
*
* @package Ultimate Points
* @version $Id: points_transfer.php 594 2009-11-18 09:34:38Z femu $
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
class points_transfer
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $checked_user, $check_auth;

		// Grab the variables
		$message		= request_var('comment', '', true);
		$adm_points		= request_var('adm_points', false);
		$transfer_id	= request_var('i', 0);
		$post_id		= request_var('post_id', 0);
		
		add_form_key('transfer_points');

		// Check, if transferring is allowed
		if ( !$points_config['transfer_enable'] )
		{
			$message = $user->lang['TRANSFER_REASON_TRANSFER'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		// Add part to bar
		$template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user"),
			'FORUM_NAME'	=> sprintf($user->lang['TRANSFER_TITLE'], $config['points_name']),
		));

		if ( isset($_POST['submit']) )
		{
			if (!check_form_key('transfer_points'))
			{
				trigger_error('FORM_INVALID');
			}

			// Get variables for transferring
			$am 		=	round(request_var('amount', 0.00),2);
			$comment	=	request_var('comment', '', true);

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

			// Check, if the user is trying to send to himself
			if ( $user->data['user_id'] == $checked_user['user_id'] )
			{
				$message = sprintf($user->lang['TRANSFER_REASON_YOURSELF'], $config['points_name']) . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=transfer_user") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
				trigger_error($message);
			}

			// Add cash to receiver
			add_points($checked_user['user_id'], $am);

			// Remove cash from sender
			substract_points($user->data['user_id'], $am);

			// Get current time for logs
			$current_time = time();

			// Add transfer information to the log
			$text = utf8_normalize_nfc($message);

			$sql = 'INSERT INTO ' . POINTS_LOG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'point_send'	=> (int) $user->data['user_id'],
				'point_recv'	=> (int) $checked_user['user_id'],
				'point_amount'	=> $am,
				'point_sendold'	=> $user->data['user_points'] ,
				'point_recvold'	=> $checked_user['user_points'],
				'point_comment'	=> $text,
				'point_type'	=> '1',
				'point_date'	=> $current_time,
			));
			$db->sql_query($sql);

			// Send pm to user
			if ( !$points_config['transfer_pm_enable'] == 0 && $checked_user['user_allow_pm'] == 1 )
			{
				// Select the user data for the PM
				$sql_array = array(
					'SELECT'    => '*',
					'FROM'      => array(
						USERS_TABLE => 'u',
					),
					'WHERE'		=> 'user_id = ' . (int) $checked_user['user_id'],
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$user_row = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);
				
				// Select the receiver language
				$user_row['user_lang'] = (file_exists($phpbb_root_path . 'language/' . $user_row['user_lang'] . "/mods/points.$phpEx")) ? $user_row['user_lang'] : $config['default_lang'];

				// load receivers language
				include($phpbb_root_path . 'language/' . basename($user_row['user_lang']) . "/mods/points.$phpEx");

				$points_name 	= $config['points_name'];
				$comment 		= $db->sql_escape($comment);
				$pm_subject		= utf8_normalize_nfc(sprintf($lang['TRANSFER_PM_SUBJECT']));
				$pm_text		= utf8_normalize_nfc(sprintf($lang['TRANSFER_PM_BODY'], $am, $points_name, $text));

				$poll = $uid = $bitfield = $options = '';
				generate_text_for_storage($pm_subject, $uid, $bitfield, $options, false, false, false);
				generate_text_for_storage($pm_text, $uid, $bitfield, $options, true, true, true);

				$pm_data = array(
					'address_list'		=> array ('u' => array($checked_user['user_id'] => 'to')),
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

			$message = sprintf($user->lang['TRANSFER_REASON_TRANSUCC'], number_format_points($am), $config['points_name'], $checked_user['username']) . '<br /><br />' . (($post_id) ? sprintf($user->lang['EDIT_P_RETURN_POST'], '<a href="' . append_sid("{$phpbb_root_path}viewtopic.$phpEx", "p=" . $post_id) . '">', '</a>') : sprintf($user->lang['EDIT_P_RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>'));
			trigger_error($message);
			
			$template->assign_vars(array(
				'U_ACTION'					=> $this->u_action,
			));			
		}

		$username_full = get_username_string('full', $checked_user['user_id'], $checked_user['username'], $checked_user['user_colour']);
		
		$template->assign_vars(array(
			'L_TRANSFER_DESCRIPTION'		=> sprintf($user->lang['TRANSFER_DESCRIPTION'], $config['points_name']),
			'POINTS_NAME'					=> $config['points_name'],
			'POINTS_COMMENTS'				=> ($points_config['comments_enable']) ? true : false,
			'U_TRANSFER_NAME'				=> sprintf($user->lang['TRANSFER_TO_NAME'], $username_full, $config['points_name']),

			'S_ALLOW_SEND_PM'				=> $auth->acl_get('u_sendpm'),
		));

		// Generate the page
		page_header(sprintf($user->lang['TRANSFER_TITLE'], $config['points_name']));

		// Generate the page template
		$template->set_filenames(array(
			'body' => 'points/points_transfer.html',
		));

		page_footer();
	}
}

?>