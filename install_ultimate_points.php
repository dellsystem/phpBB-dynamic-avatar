<?php
/** 
*
* @package Ultimate Points
* @version $Id: install_ultimate_points.php 594 2009-11-18 09:34:38Z femu $
* @copyright (c) 2009 wuerzi & femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'UP_ULTIMATE_POINTS_NAME';

/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/

$version_config_name = 'ultimate_points_version';

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
*/
$language_file = 'mods/points';

/*
* Optionally we may specify our own logo image to show in the upper corner instead of the default logo.
* $phpbb_root_path will get prepended to the path specified
* Image height should be 50px to prevent cut-off or stretching.
*/

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/

/**
* Define the basic structure
* The format:
*		array('{TABLE_NAME}' => {TABLE_DATA})
*		{TABLE_DATA}:
*			COLUMNS = array({column_name} = array({column_type}, {default}, {auto_increment}))
*			PRIMARY_KEY = {column_name(s)}
*			KEYS = array({key_name} = array({key_type}, {column_name(s)})),
*
*	Column Types:
*	INT:x		=> SIGNED int(x)
*	BINT		=> BIGINT
*	UINT		=> mediumint(8) UNSIGNED
*	UINT:x		=> int(x) UNSIGNED
*	TINT:x		=> tinyint(x)
*	USINT		=> smallint(4) UNSIGNED (for _order columns)
*	BOOL		=> tinyint(1) UNSIGNED
*	VCHAR		=> varchar(255)
*	CHAR:x		=> char(x)
*	XSTEXT_UNI	=> text for storing 100 characters (topic_title for example)
*	STEXT_UNI	=> text for storing 255 characters (normal input field with a max of 255 single-byte chars) - same as VCHAR_UNI
*	TEXT_UNI	=> text for storing 3000 characters (short text, descriptions, comments, etc.)
*	MTEXT_UNI	=> mediumtext (post text, large text)
*	VCHAR:x		=> varchar(x)
*	TIMESTAMP	=> int(11) UNSIGNED
*	DECIMAL		=> decimal number (5,2)
*	DECIMAL:		=> decimal number (x,2)
*	PDECIMAL		=> precision decimal number (6,3)
*	PDECIMAL:	=> precision decimal number (x,3)
*	VCHAR_UNI	=> varchar(255) BINARY
*	VCHAR_CI		=> varchar_ci for postgresql, others VCHAR
*/

$versions = array(
	// Version 1.0.0 - this is the first version using UMIL
	'1.0.0'	=> array(
		// Add fields in the forums and users table
		'table_column_add' => array(
			array($table_prefix . 'users', 'user_points', array('DECIMAL:20', 0.00)),
			array($table_prefix . 'forums', 'forum_perpost', array('DECIMAL:10', 5.00)),
			array($table_prefix . 'forums', 'forum_peredit', array('DECIMAL:10', 0.05)),
			array($table_prefix . 'forums', 'forum_pertopic', array('DECIMAL:10', 15.00)),
		),

		// Now to add some permission settings
		'permission_add' => array(
			array('u_use_points', true),
			array('u_use_bank', true),
			array('u_use_logs', true), 
			array('u_use_robbery', true),
			array('u_use_lottery', true),
			array('u_use_transfer', true),
			array('m_chg_points', true),
			array('m_chg_bank', true),
			array('a_points', true),
		),
  
		// How about we give some default permissions then as well?
		'permission_set' => array(
			array('REGISTERED', 'u_use_points', 'group'),
			array('REGISTERED', 'u_use_bank', 'group'),
			array('REGISTERED', 'u_use_logs', 'group'),
			array('REGISTERED', 'u_use_robbery', 'group'),
			array('REGISTERED', 'u_use_lottery', 'group'),
			array('REGISTERED', 'u_use_transfer', 'group'),
			array('ROLE_ADMIN_STANDARD', 'a_points', 'role'),
			array('ROLE_MOD_STANDARD', 'm_chg_points', 'role'),
			array('ROLE_MOD_STANDARD', 'm_chg_bank', 'role'),
		), 
      

		// Now to add the tables (this uses the layout from develop/create_schema_files.php and from phpbb_db_tools)
		'table_add' => array(

			array($table_prefix . 'points_bank', array(
					'COLUMNS'		=> array(
						'id'			=> array('UINT:10', NULL, 'auto_increment'),
						'user_id'		=> array('UINT:10', 0),
						'holding'		=> array('DECIMAL:20', 0.00),
						'totalwithdrew'	=> array('DECIMAL:20', 0.00),
						'totaldeposit'	=> array('DECIMAL:20', 0.00),
						'opentime'		=> array('UINT:10', 0),
						'fees'			=> array('CHAR:5', 'on'),
					),
					'PRIMARY_KEY'	=> 'id',
				),
			),

			array($table_prefix . 'points_config', array(
					'COLUMNS'		=> array(
						'config_name'		=> array('VCHAR', ''),
						'config_value'		=> array('VCHAR_UNI', ''),
					),
					'PRIMARY_KEY'	=> 'config_name',
				),
			),

			array($table_prefix . 'points_log', array(
					'COLUMNS'		=> array(
						'id'			=> array('UINT:11', NULL, 'auto_increment'),
						'point_send'	=> array('UINT:11', NULL, ''),
						'point_recv'	=> array('UINT:11', NULL, ''),
						'point_amount'	=> array('DECIMAL:20', 0.00),
						'point_sendold'	=> array('DECIMAL:20', 0.00),
						'point_recvold'	=> array('DECIMAL:20', 0.00),
						'point_comment'	=> array('MTEXT_UNI', ''),
						'point_type'	=> array('UINT:11', NULL, ''),
						'point_date'	=> array('UINT:11', NULL, ''),
					),
					'PRIMARY_KEY'	=> 'id',
				),
			),

			array($table_prefix . 'points_lottery_history', array(
					'COLUMNS'		=> array(
						'id'		=> array('UINT:11', NULL, 'auto_increment'),
						'user_id'	=> array('UINT', 0),
						'user_name'	=> array('VCHAR', ''),
						'time'		=> array('UINT:11', 0),
						'amount'	=> array('DECIMAL:20', 0.00),
					),
					'PRIMARY_KEY'	=> 'id',
				),
			),

			array($table_prefix . 'points_lottery_tickets', array(
					'COLUMNS'		=> array(
						'ticket_id'	=> array('UINT:11', NULL, 'auto_increment'),
						'user_id'	=> array('UINT:11', 0),
					),
					'PRIMARY_KEY'	=> 'ticket_id',
				),
			),

			array($table_prefix . 'points_values', array(
					'COLUMNS'		=> array(
						'bank_cost'						=> array('DECIMAL:10', 0.00),
						'bank_fees'						=> array('DECIMAL:10', 0.00),
						'bank_interest'					=> array('DECIMAL:10', 0.00),
						'bank_interestcut'				=> array('DECIMAL:20', 0.00),
						'bank_last_restocked'			=> array('UINT:11', NULL),
						'bank_min_deposit'				=> array('DECIMAL:10', 0.00),
						'bank_min_withdraw'				=> array('DECIMAL:10', 0.00),
						'bank_name'						=> array('VCHAR:100', NULL),
						'bank_pay_period'				=> array('UINT:10', 2592000),
						'lottery_base_amount'			=> array('DECIMAL:10', 0.00),
						'lottery_chance'				=> array('DECIMAL', 50.00),
						'lottery_draw_period'			=> array('UINT:10', 3600),
						'lottery_jackpot'				=> array('DECIMAL:20', 50.00),
						'lottery_last_draw_time'		=> array('UINT:11', NULL),
						'lottery_max_tickets'			=> array('UINT:10', 10),
						'lottery_name'					=> array('VCHAR:100', ''),
						'lottery_prev_winner'			=> array('VCHAR', ''),
						'lottery_prev_winner_id'		=> array('UINT:10', 0),
						'lottery_ticket_cost'			=> array('DECIMAL:10', 0.00),
						'lottery_winners_total'			=> array('UINT', 0),
						'number_show_per_page'			=> array('UINT:10', 0),
						'number_show_top_points'		=> array('UINT', 0),
						'points_dl_cost_per_attach'		=> array('DECIMAL:10', 0.00),
						'points_per_attach'				=> array('DECIMAL:10', 0.00),
						'points_per_attach_file'		=> array('DECIMAL:10', 0.00),
						'points_per_poll'				=> array('DECIMAL:10', 0.00),
						'points_per_poll_option'		=> array('DECIMAL:10', 0.00),
						'points_per_post_character'		=> array('DECIMAL:10', 0.00),
						'points_per_post_word'			=> array('DECIMAL:10', 0.00),
						'points_per_topic_character'	=> array('DECIMAL:10', 0.00),
						'points_per_topic_word'			=> array('DECIMAL:10', 0.00),
						'points_per_warn'				=> array('DECIMAL:10', 0.00),
						'reg_points_bonus'				=> array('DECIMAL:10', 0.00),
						'robbery_chance'				=> array('DECIMAL:5', 0.00),
						'robbery_loose'					=> array('DECIMAL:5', 0.00),
					),          
				),
			),
		),

		// Clear the general cache as well as the templates, imagesets and themes cache
		'cache_purge' => array(
			array(),
			array('imageset'),
			array('template'),
			array('theme'),
		),

		// Alright, now lets add some modules to the ACP
		'module_add' => array(
			// First, lets add a new category named ACP_POINTS to ACP_CAT_DOT_MODS
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_POINTS'),

			// Now we will add the settings mode to the ACP_POINTS category
			array('acp', 'ACP_POINTS', array(
					'module_basename'	=> 'points',
					'module_langname'	=> 'ACP_POINTS_INDEX_TITLE',
					'module_mode'		=> 'points',
					'module_auth'		=> 'acl_a_points',
				),
			),

			// Now we will add the bank modul to the ACP_POINTS category
			array('acp', 'ACP_POINTS', array(
					'module_basename'	=> 'points',
					'module_langname'	=> 'ACP_POINTS_BANK_TITLE',
					'module_mode'		=> 'bank',
					'module_auth'		=> 'acl_a_points',
				),
			),

			// Now we will add the lottery modul to the ACP_POINTS category
			array('acp', 'ACP_POINTS', array(
					'module_basename'	=> 'points',
					'module_langname'	=> 'ACP_POINTS_LOTTERY_TITLE',
					'module_mode'		=> 'lottery',
					'module_auth'		=> 'acl_a_points',
				),
			),

			// Now we will add the robbery modul to the ACP_POINTS category
			array('acp', 'ACP_POINTS', array(
					'module_basename'	=> 'points',
					'module_langname'	=> 'ACP_POINTS_ROBBERY_TITLE',
					'module_mode'		=> 'robbery',
					'module_auth'		=> 'acl_a_points',
				),
			),
		),

		/*
		* Now we need to insert some data.  The easiest way to do that is through a custom function
		* Enter 'custom' for the array key and the name of the function for the value.
		*/
		'custom'	=> 'first_fill_1_0_0',
	),
	
	// Version 1.0.1 only update Version
	'1.0.1'		=> array(),
	
	// Version 1.0.2
	'1.0.2' => array(
		// Version 1.0.2 add robbery max rob and lottery pm id
		'custom'	=> 'fill_1_0_2',
	),
	
	// Version 1.0.3 only update Version
	'1.0.3'		=> array(),
	
	// Version 1.0.4
	'1.0.4' => array(
		'module_add' => array(
			// Now we will add the forum points modlue to the ACP_POINTS category
			array('acp', 'ACP_POINTS', array(
					'module_basename'	=> 'points',
					'module_langname'	=> 'ACP_POINTS_FORUM_TITLE',
					'module_mode'		=> 'forumpoints',
					'module_auth'		=> 'acl_a_points',
				),
			),

			// Now we will add the userguide modul to the ACP_POINTS category
			array('acp', 'ACP_POINTS', array(
					'module_basename'	=> 'points',
					'module_langname'	=> 'ACP_POINTS_USERGUIDE_TITLE',
					'module_mode'		=> 'userguide',
					'module_auth'		=> 'acl_a_points',
				),
			),
		),
		// Version 1.0.4 add userguide in ACP
		'custom'	=> 'fill_1_0_4',
	),
	
	// Version 1.0.5 only update Version
	'1.0.5'		=> array(),	
	
	// Version 1.0.6 only update Version
	'1.0.6'		=> array(),	
	
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

/*
* Here is our custom function that will be called for version 1.0.0
*
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function first_fill_1_0_0($action, $version)
{
	global $db, $table_prefix, $umil;

	switch ($action)
	{
		case 'install' :    
			// Run this when installing the first time
			if ($umil->table_exists($table_prefix . 'config'))
			{
				$sql_ary = array();

				$sql_ary[] = array('config_name' => 'points_enable',			'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'points_name',				'config_value' => 'Points',);
				$sql_ary[] = array('config_name' => 'ultimate_points_version',	'config_value' => '1.0.0',);
				$db->sql_multi_insert($table_prefix . 'config ', $sql_ary);
			}

			if ($umil->table_exists($table_prefix . 'points_config'))
			{
				// before we fill anything in this table, we truncate it. Maybe someone missed an old installation.
				$db->sql_query('TRUNCATE TABLE ' . $table_prefix . 'points_config');

				$sql_ary = array();

				$sql_ary[] = array('config_name' => 'transfer_enable',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'transfer_pm_enable',			'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'comments_enable',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'pertopic_enable',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'perpost_enable',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'peredit_enable',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'logs_enable',					'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'images_topic_enable',			'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'images_memberlist_enable',		'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'lottery_enable',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'bank_enable',					'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'robbery_enable',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'points_disablemsg',			'config_value' => 'Ultimate Points is currently disabled!',);
				$sql_ary[] = array('config_name' => 'stats_enable',					'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'lottery_multi_ticket_enable',	'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'robbery_sendpm',				'config_value' => '1',);
				$sql_ary[] = array('config_name' => 'display_lottery_stats',		'config_value' => '1',);

				$db->sql_multi_insert($table_prefix . 'points_config ', $sql_ary);
			}

			if ($umil->table_exists($table_prefix . 'points_values'))
			{
				// before we fill anything in this table, we truncate it. Maybe someone missed an old installation.
				$db->sql_query('TRUNCATE TABLE ' . $table_prefix . 'points_values');

				$sql_ary = array();

				$sql_ary[] = array(
					'number_show_per_page' => '15', 
					'number_show_top_points' => '10', 
					'reg_points_bonus' => '50', 
					'lottery_jackpot' => '50', 
					'lottery_winners_total' => '0', 
					'lottery_prev_winner' => '0', 
					'lottery_prev_winner_id' => '0', 
					'lottery_last_draw_time' => '0', 
					'bank_last_restocked' => '0', 
					'lottery_base_amount' => '50', 
					'lottery_draw_period' => '3600', 
					'lottery_ticket_cost' => '10', 
					'bank_fees' => '0', 
					'bank_interest' => '0', 
					'bank_pay_period' => '2592000', 
					'bank_min_withdraw' => '0', 
					'bank_min_deposit' => '0', 
					'bank_interestcut' => '0', 
					'points_per_poll_option' => '0', 
					'points_per_poll' => '0', 
					'points_per_attach_file' => '0', 
					'points_per_attach' => '0', 
					'points_per_post_word' => '0', 
					'points_per_post_character' => '0', 
					'points_per_topic_word' => '0', 
					'points_per_topic_character' => '0', 
					'points_dl_cost_per_attach' => '0', 
					'points_per_warn' => '0', 
					'robbery_chance' => '50', 
					'robbery_loose' => '50', 
					'bank_cost' => '0', 
					'bank_name' => 'BANK NAME', 
					'lottery_name' => 'LOTTERY NAME',
				);

				$db->sql_multi_insert($table_prefix . 'points_values ', $sql_ary);
			}      

			// Send the message, that the command was successful
			return 'UP_INSERT_FIRST_FILL';
		break;

		case 'update' :
		break;

 		case 'uninstall' :
			// Run this additionally when uninstalling
			if ($umil->table_exists($table_prefix . 'config'))
			{
				$sql = 'DELETE FROM ' . $table_prefix . "config
					WHERE config_name = 'points_enable'";
				$db->sql_query($sql);
				$sql = 'DELETE FROM ' . $table_prefix . "config
					WHERE config_name = 'points_name'";
				$db->sql_query($sql);
			}

			// Send the message, that the command was successful
			return 'UP_REMOVE_FORUM_ENTRIES';
		break;
	}
}


/*
* Here is our custom function that will be called for version 1.0.2
*
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function fill_1_0_2($action, $version)
{
	global $db, $table_prefix, $umil;

	switch ($action)
	{
		case 'install' :
		case 'update' :
			// Run this when installing/updating
			if ($umil->table_exists($table_prefix . 'points_values'))
			{
				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD robbery_max_rob decimal(5,2) NOT NULL DEFAULT '10.00'";
				$db->sql_query($sql);
				
				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD lottery_pm_from INT( 10 ) UNSIGNED NOT NULL DEFAULT '0'";
				$db->sql_query($sql);
				
				$sql = "ALTER TABLE " . $table_prefix . "posts
					ADD points_received DECIMAL( 20, 2 ) NOT NULL default '0.00'";
				$db->sql_query($sql);
			}
			
			// Method 1 of displaying the command (and Success for the result)
			return 'UP_UPDATE_SUCCESFUL';
		break;

		case 'uninstall' :
		
			if ($umil->table_exists($table_prefix . 'posts'))
			{
				$sql = 'ALTER TABLE ' . $table_prefix . 'posts
					DROP points_received';
				$db->sql_query($sql);
			}
			
			// Send the message, that the command was successful
			return 'UP_REMOVE_FORUM_ENTRIES';
		break;
	}
}

function fill_1_0_4($action, $version)
{
	global $db, $table_prefix, $umil;

	switch ($action)
	{
		case 'install' :
		case 'update' :
			// Run this when installing/updating
			if ($umil->table_exists($table_prefix . 'points_values'))
			{
				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD forum_topic decimal(10,2) NOT NULL DEFAULT '0.00'";
				$db->sql_query($sql);
				
				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD forum_post decimal(10,2) NOT NULL DEFAULT '0.00'";
				$db->sql_query($sql);
				
				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD forum_edit decimal(10,2) NOT NULL DEFAULT '0.00'";
				$db->sql_query($sql);
				
				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD gallery_upload decimal(10,2) NOT NULL DEFAULT '0.00'";
				$db->sql_query($sql);
				
				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD gallery_remove decimal(10,2) NOT NULL DEFAULT '0.00'";
				$db->sql_query($sql);

				$sql = "ALTER TABLE " . $table_prefix . "points_values
					ADD gallery_view decimal(10,2) NOT NULL DEFAULT '0.00'";
				$db->sql_query($sql);
			}
			
			if ($umil->table_exists($table_prefix . 'posts'))
			{
				$sql = "ALTER TABLE " . $table_prefix . "posts
					ADD points_poll_received DECIMAL( 20, 2 ) NOT NULL default '0.00'";
				$db->sql_query($sql);
				
				$sql = "ALTER TABLE " . $table_prefix . "posts
					ADD points_attachment_received DECIMAL( 20, 2 ) NOT NULL default '0.00'";
				$db->sql_query($sql);

				$sql = "ALTER TABLE " . $table_prefix . "posts
					ADD points_topic_received DECIMAL( 20, 2 ) NOT NULL default '0.00'";
				$db->sql_query($sql);

				$sql = "ALTER TABLE " . $table_prefix . "posts
					ADD points_post_received DECIMAL( 20, 2 ) NOT NULL default '0.00'";
				$db->sql_query($sql);
			}
			
			if ($umil->table_exists($table_prefix . 'points_config'))
			{
				$sql = "INSERT INTO " . $table_prefix . "points_config
					(`config_name`, `config_value`) VALUES ('gallery_deny_view', '0')";
				$db->sql_query($sql);
			}
			
			// Method 1 of displaying the command (and Success for the result)
			return 'UP_UPDATE_SUCCESFUL';
		break;

		case 'uninstall' :
		
			if ($umil->table_exists($table_prefix . 'posts'))
			{
				$sql = 'ALTER TABLE ' . $table_prefix . 'posts
					DROP points_poll_received';
				$db->sql_query($sql);
				
				$sql = 'ALTER TABLE ' . $table_prefix . 'posts
					DROP points_attachment_received';
				$db->sql_query($sql);

				$sql = 'ALTER TABLE ' . $table_prefix . 'posts
					DROP points_topic_received';
				$db->sql_query($sql);

				$sql = 'ALTER TABLE ' . $table_prefix . 'posts
					DROP points_post_received';
				$db->sql_query($sql);				
			}
			
			// Send the message, that the command was successful
			return 'UP_REMOVE_FORUM_ENTRIES';
		break;
	}
}

?>