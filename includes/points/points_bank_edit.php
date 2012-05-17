<?php
/** 
*
* @package Ultimate Points
* @version $Id: points_bank_edit.php 594 2009-11-18 09:34:38Z femu $
* @copyright (c) 2009 wuerz & femu
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
class points_bank_edit
{
	var $u_action;

	function main($id, $mode)
	{
		global $template, $user, $db, $config, $phpEx, $phpbb_root_path, $ultimate_points, $points_config, $points_values, $auth, $check_auth;

		// Only registered users can go beyond this point
		if ( !$user->data['is_registered'] )
		{
			if ( $user->data['is_bot'] )
			{
				redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
			}
			login_box('', $user->lang['LOGIN_INFO']);
		}

		$adm_points	= request_var('adm_points', false);
		$u_id 		= request_var('user_id', 0);
		$post_id	= request_var('post_id', 0);

		if ( empty($u_id) )
		{
			$message = $user->lang['EDIT_NO_ID_SPECIFIED'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank_edit") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
			trigger_error($message);
		}

		$user_id = $u_id;
		add_form_key('bank_edit');

		if ( $adm_points != false && ($auth->acl_get('a_') || $auth->acl_get('m_chg_bank')) )
		{
			$template->assign_block_vars('administer_bank', array());
			
			if ( isset($_POST['submit']) )
			{
				if (!check_form_key('bank_edit'))
				{
					trigger_error('FORM_INVALID');
				}

				$new_points = round(request_var('points', 0.00),2);
				
				set_bank($u_id, $new_points);

				$sql_array = array(
					'SELECT'    => 'user_id, username, user_points, user_colour',
					'FROM'      => array(
						USERS_TABLE => 'u',
					),
					'WHERE'		=> 'user_id = ' . (int) $u_id,
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$points_user = $db->sql_fetchrow($result);

				add_log('admin', 'LOG_MOD_BANK', $points_user['username']);
				$message = ($post_id) ? sprintf($user->lang['EDIT_P_RETURN_POST'], '<a href="'. append_sid("{$phpbb_root_path}viewtopic.$phpEx", "p=" . $post_id) . '">', '</a>') : sprintf($user->lang['EDIT_P_RETURN_INDEX'], '<a href="' . append_sid("{$phpbb_root_path}index.$phpEx") . '">', '</a>');
				trigger_error((sprintf($user->lang['EDIT_POINTS_SET'], $config['points_name'])) . $message);
			}
			else
			{
				$sql_array = array(
					'SELECT'	=> 'u.user_id, u.username, u.user_points, u.user_colour, b.holding',

					'FROM'		=> array(
						USERS_TABLE	=> 'u',
					),

					'LEFT_JOIN'	=> array(
						array(
							'FROM'	=> array(POINTS_BANK_TABLE => 'b'),
							'ON'	=> 'u.user_id = b.user_id'
						),
					),

					'WHERE'		=> 'u.user_id = ' . (int) $u_id,
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);

				if ( empty($u_id) )
				{
					$message = $user->lang['EDIT_USER_NOT_EXIST'] . '<br /><br /><a href="' . append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank_edit") . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
					trigger_error($message);
				}

				$hidden_fields = build_hidden_fields(array(
					'user_id'	=> $u_id,
					'post_id'	=> $post_id,
				));

				$template->assign_vars(array(
					'USER_NAME'			=> get_username_string('full', $u_id, $row['username'], $row['user_colour']),
					'BANK_POINTS'		=> sprintf(number_format_points($row['holding'])),
					'POINTS_NAME'		=> $config['points_name'],
					'CURRENT_VALUE'		=> $row['holding'],

					'L_POINTS_MODIFY'	=> sprintf($user->lang['EDIT_BANK_MODIFY'], $config['points_name']),
					'L_P_BANK_TITLE'	=> sprintf($user->lang['EDIT_P_BANK_TITLE'], $config['points_name']),
					'L_USERNAME'		=> $user->lang['USERNAME'],
					'L_SET_AMOUNT'		=> $user->lang['EDIT_SET_AMOUNT'],

					'U_USER_LINK'		=> append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=viewprofile&amp;u=" . $u_id),

					'S_ACTION'			=> append_sid("{$phpbb_root_path}points.$phpEx", "mode=bank_edit&amp;adm_points=1"),
					'S_HIDDEN_FIELDS'	=> $hidden_fields,
				));
			}
		}
		// Generate the page
		page_header($user->lang['EDIT_POINTS_ADMIN']);

		// Generate the page template
		$template->set_filenames(array(
			'body'	=> 'points/points_bank_edit.html'
		));

		page_footer();
	}
}

?>