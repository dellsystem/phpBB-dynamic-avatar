<?php
/**
*
* @package Ultimate Points
* @version $Id: functions_points.php 609 2009-12-04 13:30:39Z femu $
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

$ultimate_points = new ultimate_points();

class ultimate_points
{
	/**
	* Strip text
	*/
	function strip_text($text)
	{
		//remove quotes
		$new_text = '';
		$text = explode('[quote', $text);
		$new_text .= $text[0]; //1st frame is always valid text
		for($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			if(stristr($text[$i], '[/quote') === false) //checkout if it's a double/triple and so on quote
			{
				continue;
			}
		
			$item = explode('[/quote' , $text[$i]);
			$last_frame = sizeof($item) - 1; //only last frame is valid text
			$new_text .= trim(substr($item[$last_frame], 10)); //remove bbcode uid
		}

		//remove code
		$text = $new_text;
		$new_text = '';
		$text = explode('[code', $text);
		$new_text .= $text[0]; //1st frame is always valid text
		for($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			if(stristr($text[$i], '[/code') === false) //checkout if it's a double/triple and so on code
			{
				continue;
			}
		
			$item = explode('[/code' , $text[$i]);
			$last_frame = sizeof($item) - 1; //only last frame is valid text
			$new_text .= trim(substr($item[$last_frame], 10)); //remove bbcode uid
		}

		//remove urls
		$text = $new_text;
		$new_text = '';
		$text = explode('[url', $text);
		$new_text .= $text[0]; //1st frame is always valid text
		for($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			if(stristr($text[$i], '[/url') === false) //checkout if it's a double/triple and so on url
			{
				continue;
			}
			$item = explode('[/url' , $text[$i]);
			$last_frame = sizeof($item) - 1; //only last frame is valid text
			$new_text .= trim(substr($item[$last_frame], 10)); //remove bbcode uid
		}

		//now remove the rest of bbcode
		$text = $new_text;
		$new_text = '';
		$text = explode('[', $text);
		$new_text .= $text[0]; //1st frame is always valid text   
		for($i = 1, $size = sizeof($text); $i < $size; $i++)
		{
			$item = explode(']' , $text[$i]);       
			if(count($item) > 1) // if any part of text remains :-D
			{
				$new_text .= $item[1];
			}
		}
		$new_text = strip_tags($new_text); //remove smilies and images
		
		//BEGIN to remove extra spaces
		$new_text = explode(' ', $new_text);
		for($i = 0, $size = count($new_text); $i < $size; $i++)
		{
			if(trim($new_text[$i]) == '' || trim($new_text[$i]) == '&nbsp;')
			{
				unset($new_text[$i]);
			}
			else
			{
				$new_text[$i] = trim($new_text[$i]);
			}
		}
		//END to remove extra spaces      
		return trim(join(' ', $new_text));
	}

	/**
	* Calculate and send points for starting a new topic
	*/
	function new_topic_ch($forum_id, $topic_id, $message)
	{
		global $db, $config, $auth, $user, $points_values;

		// Count words and characters
		$topic_words = $topic_characters = 0;

		$topic_words		= $points_values['points_per_topic_word'] * sizeof(explode(' ' , $message));
		$topic_characters	= $points_values['points_per_topic_character'] * utf8_strlen($message);
		$total				= $topic_words + $topic_characters;

		// Update user points
		$sql = 'UPDATE ' . USERS_TABLE . "
		SET user_points = user_points + $total
		WHERE user_id = " . (int) $user->data['user_id'];
		$db->sql_query($sql);

		// Update post points
		$sql = 'UPDATE ' . POSTS_TABLE . "
		SET points_topic_received = points_topic_received + $total
		WHERE topic_id = " . (int) $topic_id;
		$db->sql_query($sql);

		return;
	}

	/**
	* Calculate and send points for posting
	**/
	function new_post_ch($forum_id, $post_id, $message)
	{
		global $db, $config, $auth, $user, $points_values;

		// Count words and characters
		$post_words	= $post_characters = 0;

		$post_words			= $points_values['points_per_post_word'] * sizeof(explode(' ' , $message));
		$post_characters	= $points_values['points_per_post_character'] * utf8_strlen($message);
		$total				= $post_words + $post_characters;

		// Update user points
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_points = user_points + $total
			WHERE user_id = " . (int) $user->data['user_id'];
		$db->sql_query($sql);

		// Update post points
		$sql = 'UPDATE ' . POSTS_TABLE . "
			SET points_post_received = points_post_received + $total
			WHERE post_id = " . (int) $post_id;
		$db->sql_query($sql);

		return;
	}

	/**
	* Receive cash for creating new polls
	*/
	function new_poll($forum_id, $post_id, $options)
	{
		global $db, $config, $auth, $user, $points_values;

		$poll_options	= $poll_points = 0;
		$poll_options	= $points_values['points_per_poll_option'] * $options;
		$poll_points	= $points_values['points_per_poll'];
		$total			= $poll_options + $poll_points;

		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_points = user_points + $total
			WHERE user_id = " . (int) $user->data['user_id'];
		$db->sql_query($sql);

		// Update post points
		$sql = 'UPDATE ' . POSTS_TABLE . "
			SET points_poll_received = points_poll_received + $total
			WHERE post_id = " . (int) $post_id;
		$db->sql_query($sql);

		return $total;
	}

	/**
	* Receive points for attaching files
	*/
	function new_attachment($forum_id, $post_id, $files)
	{
		global $db, $config, $auth, $user, $points_values;

		$attachment_files	= $attachment_points = 0;
		$attachment_files	= $points_values['points_per_attach_file'] * $files;
		$attachment_points	= $points_values['points_per_attach'];
		$total				= $attachment_files + $attachment_points;
		
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_points = user_points + $total
			WHERE user_id = " . (int) $user->data['user_id'];
		$db->sql_query($sql);

		// Update post points
		$sql = 'UPDATE ' . POSTS_TABLE . "
			SET points_attachment_received = points_attachment_received + $total
			WHERE post_id = " . (int) $post_id;
		$db->sql_query($sql);

		return $total;
	}

	/**
	* Calculate and send points for starting a new topic
	*/
	function update_topic_ch($poster_id,$forum_id, $topic_id, $message)
	{
		global $db, $config, $auth, $user, $points_values;

		// Count words and characters
		$topic_words = $topic_characters = 0;

		$topic_words		= $points_values['points_per_topic_word'] * sizeof(explode(' ' , $message));
		$topic_characters	= $points_values['points_per_topic_character'] * utf8_strlen($message);
		$total				= $topic_words + $topic_characters;

		// Update user points
		$sql = 'UPDATE ' . USERS_TABLE . "
		SET user_points = user_points + $total
		WHERE user_id = " . (int) $poster_id;
		$db->sql_query($sql);

		// Update post points
		$sql = 'UPDATE ' . POSTS_TABLE . "
		SET points_topic_received = points_topic_received + $total
		WHERE topic_id = " . (int) $topic_id;
		$db->sql_query($sql);

		return;
	}

	/**
	* Calculate and send points for posting
	**/
	function update_post_ch($poster_id, $forum_id, $post_id, $message)
	{
		global $db, $config, $auth, $user, $points_values;

		// Count words and characters
		$post_words			= $post_characters = 0;

		$post_words			= $points_values['points_per_post_word'] * sizeof(explode(' ' , $message));
		$post_characters	= $points_values['points_per_post_character'] * utf8_strlen($message);
		$total				= $post_words + $post_characters;

		// Update user points
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_points = user_points + $total
			WHERE user_id = " . (int) $poster_id;
		$db->sql_query($sql);

		// Update post points
		$sql = 'UPDATE ' . POSTS_TABLE . "
			SET points_post_received = points_post_received + $total
			WHERE post_id = " . (int) $post_id;
		$db->sql_query($sql);

		return;
	}

	/**
	* Receive cash for creating new polls
	*/
	function update_poll($poster_id, $forum_id, $post_id, $options)
	{
		global $db, $config, $auth, $user, $points_values;

		$poll_options	= $poll_points = 0;
		$poll_options	= $points_values['points_per_poll_option'] * $options;
		$poll_points	= $points_values['points_per_poll'];
		$total			= $poll_options + $poll_points;

		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_points = user_points + $total
			WHERE user_id = " . (int) $poster_id;
		$db->sql_query($sql);

		// Update post points
		$sql = 'UPDATE ' . POSTS_TABLE . "
			SET points_poll_received = points_poll_received + $total
			WHERE post_id = " . (int) $post_id;
		$db->sql_query($sql);

		return $total;
	}
}

/**
* Add points to user
*/
function add_points($user_id, $amount)
{
	global $db, $user;

	// Select users current points
	$sql_array = array(
		'SELECT'    => 'user_points',
		'FROM'      => array(
			USERS_TABLE => 'u',
		),
		'WHERE'		=> 'user_id = ' . (int) $user_id,
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	$user_points = $db->sql_fetchfield('user_points');
	$db->sql_freeresult($result);

	// Add the points
	$data = array(
		'user_points'	=> $user_points + $amount,
	);

	$sql = 'UPDATE ' . USERS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE user_id = ' . (int) $user_id;
	$db->sql_query($sql);

	return;
}

/**
* Substract points from user
*/
function substract_points($user_id, $amount)
{
	global $db, $user;

	// Select users current points
	$sql_array = array(
		'SELECT'    => 'user_points',
		'FROM'      => array(
			USERS_TABLE => 'u',
		),
		'WHERE'		=> 'user_id = ' . (int) $user_id,
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	$user_points = $db->sql_fetchfield('user_points');
	$db->sql_freeresult($result);

	// Update the points
	$data = array(
		'user_points'	=> $user_points - $amount,
	);

	$sql = 'UPDATE ' . USERS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE user_id = ' . (int) $user_id;
	$db->sql_query($sql);

	return;
}

/**
* Set the amount of points to user
*/
function set_points($user_id, $amount)
{
	global $db, $user;

	// Set users new points
	$data = array(
		'user_points'	=> $amount,
	);

	$sql = 'UPDATE ' . USERS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE user_id = ' . (int) $user_id;
	$db->sql_query($sql);

	return;
}

/**
* Set the amount of bank points to user
*/
function set_bank($user_id, $amount)
{
	global $db;

	// Set users new holding
	$data = array(
		'holding'	=> $amount,
	);

	$sql = 'UPDATE ' . POINTS_BANK_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE user_id = ' . (int) $user_id;
	$db->sql_query($sql);

	return;
}

/**
* Preformat numbers
*/
function number_format_points($num)
{
	global $user;

	$decimals = 2;

	return number_format($num, $decimals, $user->lang['POINTS_SEPARATOR_DECIMAL'], $user->lang['POINTS_SEPARATOR_THOUSANDS']);
}

/**
* Run Lottery
*/
function run_lottery()
{
	global $db, $config, $auth, $user, $ultimate_points, $points_config, $points_values, $phpbb_root_path, $phpEx;
	
	$current_time = time();

	// Count number of tickets
	$sql_array = array(
		'SELECT'    => 'COUNT(ticket_id) AS num_tickets',
		'FROM'      => array(
			POINTS_LOTTERY_TICKETS_TABLE => 'l',
		),
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query($sql);
	$total_tickets = (int) $db->sql_fetchfield('num_tickets');
	$db->sql_freeresult($result);

	// Select a random user from tickets table
	switch ($db->sql_layer)
	{
		case 'postgres':
			$order_by = 'RANDOM()';
		break;

		case 'mssql':
		case 'mssql_odbc':
			$order_by = 'NEWID()';
		break;
        
		default:
			$order_by = 'RAND()';
		break;
	}

	$sql_array = array(
		'SELECT'    => '*',
		'FROM'      => array(
			POINTS_LOTTERY_TICKETS_TABLE => 'l',
		),
		'ORDER_BY'	=> $order_by,
	);
	$sql = $db->sql_build_query('SELECT', $sql_array);
	$result = $db->sql_query_limit($sql, 1);
	$random_user_by_tickets = (int) $db->sql_fetchfield('user_id');
	$db->sql_freeresult($result);

	if ( $total_tickets > 0 )
	{
		// Genarate a random number 
		$rand_base 	= $points_values['lottery_chance'];
		$rand_value = rand(0, 100);

		// Decide, if the user really wins
		if ( $rand_value <= $rand_base )
		{
			$winning_number = $random_user_by_tickets;

			// Select a winner from ticket table
			$sql_array = array(
				'SELECT'    => '*',
				'FROM'      => array(
					USERS_TABLE => 'u',
				),
				'WHERE'		=> 'user_id = ' . $winning_number,
			);
			$sql = $db->sql_build_query('SELECT', $sql_array);
			$result = $db->sql_query($sql);
			$winner = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			// Check if lottery is enabled and prepare winner informations
			if ( $points_config['lottery_enable'] != 0 )
			{
				// Select the receiver language
				$winner['user_lang'] = (file_exists($phpbb_root_path . 'language/' . $winner['user_lang'] . "/mods/points.$phpEx")) ? $winner['user_lang'] : $config['default_lang'];

				// load receivers language
				include($phpbb_root_path . 'language/' . basename($winner['user_lang']) . "/mods/points.$phpEx");

				$winnings_update = $winner['user_points'] + $points_values['lottery_jackpot'];
				set_points($winner['user_id'], $winnings_update);

				$winner_notification = sprintf(number_format_points($points_values['lottery_jackpot'])) . ' ' . $config['points_name'] . ' ';
				$winner_deposit = $lang['LOTTERY_PM_CASH_ENABLED'];
				$amount_won = $points_values['lottery_jackpot'];
			}
			else
			{
				$winner_notification = '';
				$winner_deposit = '';
				$amount_won = '';
			}

			// Update previous winner information
			set_points_values('lottery_prev_winner', ("'" . $winner['username'] . "'"));
			set_points_values('lottery_prev_winner_id', $winner['user_id']);

			// Check, if user wants to be informed by PM
			if ( $winner['user_allow_pm'] == 1 )
			{
				$sql_array = array(
					'SELECT'    => '*',
					'FROM'      => array(
						USERS_TABLE => 'u',
					),
					'WHERE'		=> 'user_id = ' . $points_values['lottery_pm_from'],
				);
				$sql = $db->sql_build_query('SELECT', $sql_array);
				$result = $db->sql_query($sql);
				$pm_sender = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				// Notify the lucky winner by PM
				$pm_subject	= utf8_normalize_nfc($lang['LOTTERY_PM_SUBJECT']);
				$pm_text	= utf8_normalize_nfc(sprintf($lang['LOTTERY_PM_BODY'], $winner_notification, $winner_deposit));

				$poll = $uid = $bitfield = $options = '';
				generate_text_for_storage($pm_subject, $uid, $bitfield, $options, false, false, false);
				generate_text_for_storage($pm_text, $uid, $bitfield, $options, true, true, true);

				$pm_data = array(
					'address_list'		=> array ('u' => array($winner['user_id'] => 'to')),
					'from_user_id'		=> ($points_values['lottery_pm_from'] == 0) ? $winner['user_id'] : $pm_sender['user_id'],
					'from_username'		=> ($points_values['lottery_pm_from'] == 0) ? $user->lang['LOTTERY_PM_COMMISION'] : $pm_sender['username'],
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
			
			// Add new winner to lottery history
			$sql = 'INSERT INTO ' . POINTS_LOTTERY_HISTORY_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'user_id'			=> (int) $winner['user_id'] ,
				'user_name'			=> $winner['username'] ,
				'time'				=> $current_time,
				'amount'			=> $points_values['lottery_jackpot'] ,
			));
			$db->sql_query($sql);

			// Update winners total
			set_points_values('lottery_winners_total', $points_values['lottery_winners_total'] + 1);

			// Reset jackpot
			set_points_values('lottery_jackpot', $points_values['lottery_base_amount']);
		}
		else
		{
			set_points_values('lottery_jackpot', $points_values['lottery_jackpot'] + $points_values['lottery_base_amount']);

			$no_winner = 0;
			
			$sql = 'INSERT INTO ' . POINTS_LOTTERY_HISTORY_TABLE . ' ' . $db->sql_build_array('INSERT', array(
				'user_id'			=> 0,
				'user_name'			=> $no_winner,
				'time'				=> $current_time,
				'amount'			=> 0,
			));
			$db->sql_query($sql);

			// Update previous winner information
			set_points_values('lottery_prev_winner', "'" . $no_winner . "'");
			set_points_values('lottery_prev_winner_id', 0);
		}
	}

	// Reset lottery

	// Delete all tickets
	$sql = 'DELETE FROM ' . POINTS_LOTTERY_TICKETS_TABLE;
	$db->sql_query($sql);

	// Reset last draw time
	$check_time = $points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period'];
	$current_time = time();
	if ( $current_time > $check_time)
	{
		while ( $check_time < $current_time )
		{
			$check_time = $check_time + $points_values['lottery_draw_period'];
			$check_time++;
		}

		if ( $check_time > $current_time )
		{
			$check_time = $check_time - $points_values['lottery_draw_period'];
			set_points_values('lottery_last_draw_time', $check_time);
		}
	}
	else
	{
		set_points_values('lottery_last_draw_time', ($points_values['lottery_last_draw_time'] + $points_values['lottery_draw_period']));
	}
}

/**
* Set points config value. Creates missing config entry.
*/
function set_points_config($config_name, $config_value, $is_dynamic = false)
{
	global $db, $cache, $config, $points_config;

	$sql = 'UPDATE ' . POINTS_CONFIG_TABLE . "
		SET config_value = '" . $db->sql_escape($config_value) . "'
		WHERE config_name = '" . $db->sql_escape($config_name) . "'";
	$db->sql_query($sql);

	if (!$db->sql_affectedrows() && !isset($points_config[$config_name]))
	{
		$sql = 'INSERT INTO ' . POINTS_CONFIG_TABLE . ' ' . $db->sql_build_array('INSERT', array(
			'config_name'	=> $config_name,
			'config_value'	=> $config_value,
			'is_dynamic'	=> ($is_dynamic) ? 1 : 0));
		$db->sql_query($sql);
	}

	$points_config[$config_name] = $config_value;

	if (!$is_dynamic)
	{
		$cache->destroy('config');
	}
}

/**
* Set points values
*/
function set_points_values($field, $value)
{
	global $db;

	$sql = "UPDATE " . POINTS_VALUES_TABLE . "
		SET $field = $value";
	$db->sql_query($sql);

	return;
}

/**
* Reset field topic_received
*/
function reset_topic_received($post_id)
{
	global $db, $user;

	// Reset the points
	$data = array(
		'points_topic_received'	=> '0',
	);

	$sql = 'UPDATE ' . POSTS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE post_id = ' . (int) $post_id;
	$db->sql_query($sql);

	return;
}

/**
* Reset field post_received
*/
function reset_post_received($post_id)
{
	global $db, $user;

	// Reset the points
	$data = array(
		'points_post_received'	=> '0',
	);

	$sql = 'UPDATE ' . POSTS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE post_id = ' . (int) $post_id;
	$db->sql_query($sql);

	return;
}

/**
* Reset field poll_received
*/
function reset_poll_received($post_id)
{
	global $db, $user;

	// Reset the points
	$data = array(
		'points_poll_received'	=> '0',
	);

	$sql = 'UPDATE ' . POSTS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE post_id = ' . (int) $post_id;
	$db->sql_query($sql);

	return;
}

/**
* Reset field poll_received with topic
*/
function reset_poll_received_topic($topic_id)
{
	global $db, $user;

	// Reset the points
	$data = array(
		'points_poll_received'	=> '0',
	);

	$sql = 'UPDATE ' . POSTS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE topic_id = ' . (int) $topic_id;
	$db->sql_query($sql);

	return;
}

/**
* Update field points_attachment_received with topic
*/
function update_attachment_field($post_id, $new_value)
{
	global $db, $user;

	// Reset the points
	$data = array(
		'points_attachment_received'	=> $new_value,
	);

	$sql = 'UPDATE ' . POSTS_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $data) . '
		WHERE post_id = ' . (int) $post_id;
	$db->sql_query($sql);

	return;
}

?>