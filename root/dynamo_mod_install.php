<?php
/**
*
* @author dellsystem
* @package umil
* @copyright (c) 2008 phpBB Group
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
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'Dynamic Avatar MOD';

/*
* The name of the config variable which will hold the currently installed version
* You do not need to set this yourself, UMIL will handle setting and updating the version itself.
*/
$version_config_name = 'dynamo_version';

/*
* The language file which will be included when installing
* Language entries that should exist in the language file for UMIL (replace $mod_name with the mod's name you set to $mod_name above)
* $mod_name
* 'INSTALL_' . $mod_name
* 'INSTALL_' . $mod_name . '_CONFIRM'
* 'UPDATE_' . $mod_name
* 'UPDATE_' . $mod_name . '_CONFIRM'
* 'UNINSTALL_' . $mod_name
* 'UNINSTALL_' . $mod_name . '_CONFIRM'
*/
//$language_file = 'mods/umil_auto_example';

/*
* Options to display to the user (this is purely optional, if you do not need the options you do not have to set up this variable at all)
* Uses the acp_board style of outputting information, with some extras (such as the 'default' and 'select_user' options)
*/

/*
* Optionally we may specify our own logo image to show in the upper corner instead of the default logo.
* $phpbb_root_path will get prepended to the path specified
* Image height should be 50px to prevent cut-off or stretching.
*/
$logo_img = '../contrib/penguin.png';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/

function fix_table_index($action, $version)
{
   global $umil, $config;
   // If updating from 0.0.1 or 0.0.2
   $current = $config['dynamo_version'];
   if ($action == 'update' && ($current == '0.0.1' || $current == '0.0.2'))
   {
      // Remove the position index (unique key)
      $umil->db->sql_query('ALTER TABLE phpbb_dynamo_layers DROP INDEX position');
   }
}

$versions = array(
	'0.1.0' => array(
		'permission_add' => array(
			'u_dynamo',
			'a_dynamo_overview',
			'a_dynamo_settings',
			'a_dynamo_users',
			'a_dynamo_layers',
			'a_dynamo_items',
		),
		'permission_set' => array(
			array('ROLE_ADMIN_FULL', array('a_dynamo_overview', 'a_dynamo_settings', 'a_dynamo_users', 'a_dynamo_layers', 'a_dynamo_items')),
			array('ROLE_USER_STANDARD', 'u_dynamo'),
			array('ROLE_USER_FULL', 'u_dynamo'),
		),
		'config_add' => array(
			array('dynamo_width', 100),
			array('dynamo_height', 120),
			array('dynamo_image_fp', 'images/dynamo'),
			array('dynamo_avatar_fp', 'images/avatars/dynamo'),
		),
		// Remove the layer table position index because it makes updating positions that much harder
		// Can't use the table_index_remove function because it doesn't do unique (or primary) keys, sadly ... so, custom (MySQL-only function)
		'custom' => array(
			'fix_table_index',
		),
	),
	'0.0.2'	=> array(
		// Adding some new fixtures just for fun
		'table_row_insert' => array(
			array('phpbb_dynamo_items', array(
				array(
					'dynamo_item_id'		=> 21,
					'dynamo_item_layer'		=> 12,
					'dynamo_item_name'		=> 'Blue balloon',
					'dynamo_item_desc'		=> 'Bright and blue',
				),
				array(
					'dynamo_item_id'		=> 22,
					'dynamo_item_layer'		=> 12,
					'dynamo_item_name'		=> 'Red balloon',
					'dynamo_item_desc'		=> 'A brilliantly scarlet balloon',
				),
				array(
					'dynamo_item_id'		=> 23,
					'dynamo_item_layer'		=> 12,
					'dynamo_item_name'		=> 'Green balloon',
					'dynamo_item_desc'		=> 'Shiny',
				),
				array(
					'dynamo_item_id'		=> 24,
					'dynamo_item_layer'		=> 12,
					'dynamo_item_name'		=> 'Yellow balloon',
					'dynamo_item_desc'		=> 'Like a miniature sun',
				),
			)),
			array('phpbb_dynamo_layers', array(
				array(
					'dynamo_layer_id'			=> 12,
					'dynamo_layer_mandatory'	=> 0,
					'dynamo_layer_default'		=> 0,
					'dynamo_layer_name'			=> 'Balloon',
					'dynamo_layer_desc'			=> 'A nice little balloon',
					'dynamo_layer_position'		=> 9,
				),
			)),
		),
	),

	// Version 0.0.1
	'0.0.1'	=> array(
		// Add the configs
		'config_add'	=> array(
			array('dynamo_enabled', 1),
			array('dynamo_use_points', 0),
			array('dynamo_change_base', 0),
			array('dynamo_mandatory', 0),
		),

		'module_add' => array(
			// Add category Dynamic Avatar MOD under the .MODs tab (ACP)
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_DYNAMO'),

			// Add module General Configuration under the Dynamic Avatar MOD category
			array('acp', 'ACP_DYNAMO', array(
					'module_basename'		=> 'dynamo',
					'modes'				=> array('overview', 'settings', 'layers', 'items', 'users'),
				),
			),

			// Add the UCP module (under a new category)
			array('ucp', 0, 'UCP_DYNAMO_MOD'),

			array('ucp', 'UCP_DYNAMO_MOD', array(
					'module_basename'		=> 'dynamo',
					'modes'					=> array('edit'),
				),
			),
		),

		// Create the necessary tables
		'table_add' => array(
			array('phpbb_dynamo_items', array(
				'COLUMNS' => array(
					'dynamo_item_id' => array('UINT', NULL, 'auto_increment'),
					'dynamo_item_layer' => array('UINT', 0),
					'dynamo_item_name' => array('XSTEXT_UNI', ''),
					'dynamo_item_desc' => array('VCHAR', ''),
				),
				'PRIMARY_KEY' => 'dynamo_item_id',
			)),

			array('phpbb_dynamo_layers', array(
				'COLUMNS' => array(
					'dynamo_layer_id' => array('UINT', NULL, 'auto_increment'),
					'dynamo_layer_mandatory' => array('BOOL', 0),
					'dynamo_layer_default' => array('UINT', 0),
					'dynamo_layer_name' => array('XSTEXT_UNI', ''),
					'dynamo_layer_desc' => array('VCHAR', ''),
					'dynamo_layer_position' => array('UINT', 0),
				),
				'PRIMARY_KEY'	=> 'dynamo_layer_id',
				// The `position` index (UNIQUE) was removed in 0.1.0
				// Should be handled when updating from 0.0.1 or 0.0.2
				// in fix_table_index
			)),

			array('phpbb_dynamo_users', array(
				'COLUMNS' => array(
					'dynamo_user_id' => array('UINT', 0),
					'dynamo_user_layer' => array('UINT', 0),
					'dynamo_user_item' => array('UINT', 0),
				),

				'KEYS'		=> array(
					'user_layer_index' => array('UNIQUE', array('dynamo_user_id', 'dynamo_user_layer')),
				),
			)),
		),
		
		// Add stuff to the newly-created tables (populate with initial fixtures etc)
		'table_row_insert'	=> array(
			array('phpbb_dynamo_items', array(
				array(
					'dynamo_item_id'		=> 2,
					'dynamo_item_layer'		=> 5,
					'dynamo_item_name'		=> 'Regular eyes',
					'dynamo_item_desc'		=> 'Staring at you',
				),
				array(
					'dynamo_item_id'		=> 3,
					'dynamo_item_layer'		=> 9,
					'dynamo_item_name'		=> 'Red scarf',
					'dynamo_item_desc'		=> 'A nice red scarf',
				),
				array(
					'dynamo_item_id'		=> 4,
					'dynamo_item_layer'		=> 9,
					'dynamo_item_name'		=> 'Blue scarf',
					'dynamo_item_desc'		=> 'A royal blue scarf',
				),
				array(
					'dynamo_item_id'		=> 5,
					'dynamo_item_layer'		=> 9,
					'dynamo_item_name'		=> 'Yellow scarf',
					'dynamo_item_desc'		=> 'A sunny scarf',
				),
				array(
					'dynamo_item_id'		=> 6,
					'dynamo_item_layer'		=> 9,
					'dynamo_item_name'		=> 'Green scarf',
					'dynamo_item_desc'		=> 'A fresh green scarf',
				),
				array(
					'dynamo_item_id'		=> 7,
					'dynamo_item_layer'		=> 10,
					'dynamo_item_name'		=> 'Red hat',
					'dynamo_item_desc'		=> 'A nice red hat',
				),
				array(
					'dynamo_item_id'		=> 8,
					'dynamo_item_layer'		=> 10,
					'dynamo_item_name'		=> 'Blue hat',
					'dynamo_item_desc'		=> 'A very nice blue hat',
				),
				array(
					'dynamo_item_id'		=> 9,
					'dynamo_item_layer'		=> 10,
					'dynamo_item_name'		=> 'Yellow hat',
					'dynamo_item_desc'		=> 'A sunny hat',
				),
				array(
					'dynamo_item_id'		=> 10,
					'dynamo_item_layer'		=> 10,
					'dynamo_item_name'		=> 'Green hat',
					'dynamo_item_desc'		=> 'A fresh green hat',
				),
				array(
					'dynamo_item_id'		=> 11,
					'dynamo_item_layer'		=> 1,
					'dynamo_item_name'		=> 'Black body',
					'dynamo_item_desc'		=> 'Gives off a lot of radiation',
				),
				array(
					'dynamo_item_id'		=> 12,
					'dynamo_item_layer'		=> 7,
					'dynamo_item_name'		=> 'Plain belly',
					'dynamo_item_desc'		=> 'A regular old belly',
				),
				array(
					'dynamo_item_id'		=> 13,
					'dynamo_item_layer'		=> 6,
					'dynamo_item_name'		=> 'Regular beak',
					'dynamo_item_desc'		=> '',
				),
				array(
					'dynamo_item_id'		=> 14,
					'dynamo_item_layer'		=> 6,
					'dynamo_item_name'		=> 'Tanned beak',
					'dynamo_item_desc'		=> 'Too much time on the ice floes',
				),
				array(
					'dynamo_item_id'		=> 15,
					'dynamo_item_layer'		=> 6,
					'dynamo_item_name'		=> 'Evil beak',
					'dynamo_item_desc'		=> 'Dark and sinister-looking',
				),
				array(
					'dynamo_item_id'		=> 16,
					'dynamo_item_layer'		=> 4,
					'dynamo_item_name'		=> 'Regular feet',
					'dynamo_item_desc'		=> '',
				),
				array(
					'dynamo_item_id'		=> 17,
					'dynamo_item_layer'		=> 4,
					'dynamo_item_name'		=> 'Tanned feet',
					'dynamo_item_desc'		=> '',
				),
				array(
					'dynamo_item_id'		=> 18,
					'dynamo_item_layer'		=> 4,
					'dynamo_item_name'		=> 'Evil feet',
					'dynamo_item_desc'		=> '',
				),
				array(
					'dynamo_item_id'		=> 19,
					'dynamo_item_layer'		=> 10,
					'dynamo_item_name'		=> 'Top hat',
					'dynamo_item_desc'		=> 'Very fancy',
				),
				array(
					'dynamo_item_id'		=> 20,
					'dynamo_item_layer'		=> 11,
					'dynamo_item_name'		=> 'Monocle',
					'dynamo_item_desc'		=> 'The epitome of class',
				),
			)),
			
			array('phpbb_dynamo_layers', array(
				array(
					'dynamo_layer_id'			=> 1,
					'dynamo_layer_mandatory'	=> 1,
					'dynamo_layer_default'		=> 11,
					'dynamo_layer_name'			=> 'Body',
					'dynamo_layer_desc'			=> 'The body of the penguin',
					'dynamo_layer_position'		=> 2,
				),
				array(
					'dynamo_layer_id'			=> 9,
					'dynamo_layer_mandatory'	=> 0,
					'dynamo_layer_default'		=> 0,
					'dynamo_layer_name'			=> 'Scarf',
					'dynamo_layer_desc'			=> 'A warm comfortable scarf',
					'dynamo_layer_position'		=> 6,
				),
				array(
					'dynamo_layer_id'			=> 10,
					'dynamo_layer_mandatory'	=> 0,
					'dynamo_layer_default'		=> 0,
					'dynamo_layer_name'			=> 'Hat',
					'dynamo_layer_desc'			=> 'A nice Waldo hat',
					'dynamo_layer_position'		=> 7,
				),
				array(
					'dynamo_layer_id'			=> 4,
					'dynamo_layer_mandatory'	=> 1,
					'dynamo_layer_default'		=> 16,
					'dynamo_layer_name'			=> 'Feet',
					'dynamo_layer_desc'			=> 'Some nice webbed penguin feet',
					'dynamo_layer_position'		=> 1,
				),
				array(
					'dynamo_layer_id'			=> 5,
					'dynamo_layer_mandatory'	=> 1,
					'dynamo_layer_default'		=> 2,
					'dynamo_layer_name'			=> 'Eyes',
					'dynamo_layer_desc'			=> 'Because everyone needs eyes',
					'dynamo_layer_position'		=> 4,
				),
				array(
					'dynamo_layer_id'			=> 6,
					'dynamo_layer_mandatory'	=> 1,
					'dynamo_layer_default'		=> 13,
					'dynamo_layer_name'			=> 'Beak',
					'dynamo_layer_desc'			=> 'A nice beak for eating and stuff',
					'dynamo_layer_position'		=> 3,
				),
				array(
					'dynamo_layer_id'			=> 7,
					'dynamo_layer_mandatory'	=> 1,
					'dynamo_layer_default'		=> 12,
					'dynamo_layer_name'			=> 'Belly',
					'dynamo_layer_desc'			=> 'A nice white belly',
					'dynamo_layer_position'		=> 5,
				),
				array(
					'dynamo_layer_id'			=> 11,
					'dynamo_layer_mandatory'	=> 0,
					'dynamo_layer_default'		=> 0,
					'dynamo_layer_name'			=> 'Eyewear',
					'dynamo_layer_desc'			=> 'Things to put over your eyes',
					'dynamo_layer_position'		=> 8,
				),
			)),
		),
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

/*
* Here is our custom function that will be called for version 0.9.1.
*
* @param string $action The action (install|update|uninstall) will be sent through this.
* @param string $version The version this is being run for will be sent through this.
*/
function umil_auto_example($action, $version)
{
	global $db, $table_prefix, $umil;

	if ($action == 'uninstall')
	{
		// Run this when uninstalling
		$umil->table_row_remove('phpbb_test', array('test_text' => 'This is a test message. (Edited)'));
		$umil->table_row_remove('phpbb_test', array('test_text' => 'This is another test message.'));
	}

	/**
	* Return a string
	* 	The string will be shown as the action performed (command).  It will show any SQL errors as a failure, otherwise success
	*/
	// return 'EXAMPLE_CUSTOM_FUNCTION';

	/**
	* Return an array
	* 	With the keys command and result to specify the command and the result
	*	Returning a result (other than SUCCESS) assumes a failure
	*/
	/* return array(
		'command'	=> 'EXAMPLE_CUSTOM_FUNCTION',
		'result'	=> 'FAIL',
	);*/

	/**
	* Return an array
	* 	With the keys command and result (same as above) with an array for the command.
	*	With an array for the command it will use sprintf the first item in the array with the following items.
	*	Returning a result (other than SUCCESS) assumes a failure
	*/
	/* return array(
		'command'	=> array(
			'EXAMPLE_CUSTOM_FUNCTION',
			$username,
			$number,
			$etc,
		),
		'result'	=> 'FAIL',
	);*/
}

?>
