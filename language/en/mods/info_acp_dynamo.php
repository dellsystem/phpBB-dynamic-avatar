<?php
/**
*
* @package Dynamo (Dynamic Avatar MOD for phpBB3)
* @version $Id: info_acp_dynamo.php ilostwaldo@gmail.com$
* @copyright (c) 2011 dellsystem (www.dellsystem.me)
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

// Create the lang array if it does not already exist
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// Merge the following language entries into the lang array
$lang = array_merge($lang, array(
	'ACP_DYNAMO'					=> 'Dynamic avatar',
	'ACP_DYNAMO_OVERVIEW'			=> 'Overview',
	'ACP_DYNAMO_SETTINGS'			=> 'Settings', // is this necessary? already defined elsewhere
	'ACP_DYNAMO_LAYERS'				=> 'Layers',
	'ACP_DYNAMO_ITEMS'				=> 'Items',
	'ACP_DYNAMO_USERS'				=> 'Users',

	// ACP messages when performing actions
	'ACP_DYNAMO_EDITED_LAYER'		=> 'Successfully edited layer.',
	'ACP_DYNAMO_ADDED_LAYER'		=> 'Successfully added layer.',
	'ACP_DYNAMO_DELETED_LAYER'		=> 'Successfully deleted layer.',
	'ACP_DYNAMO_DELETE_LAYER'		=> 'Are you sure you want to delete this layer?',
	'ACP_DYNAMO_ADDED_ITEM'			=> 'Successfully added item.',
	'ACP_DYNAMO_EDITED_ITEM'		=> 'Successfully edited item.',
	'ACP_DYNAMO_DELETED_ITEM'		=> 'Successfully deleted item.',
	'ACP_DYNAMO_DELETE_ITEM'		=> 'Are you sure you want to delete this item?',
	'ACP_DYNAMO_CANNOT_MOVE'		=> "Can't move the image file attached to the item. Please post in the MOD's topic or contact the author directly.",

	// Logging
	'LOG_DYNAMO_SETTINGS'			=> '<strong>Altered dynamic avatar settings</strong>',
	'LOG_DYNAMO_EDIT_LAYER'			=> '<strong>Edited dynamic avatar layer</strong><br />» %s',
	'LOG_DYNAMO_ADD_LAYER'			=> '<strong>Created new dynamic avatar layer</strong><br />» %s',
	'LOG_DYNAMO_DELETE_LAYER'		=> '<strong>Deleted dynamic avatar layer</strong><br />» %s',
	'LOG_DYNAMO_MOVE_LAYER'			=> '<strong>Moved dynamic avatar layer</strong> %1$s <strong>%2$s</strong>', // layer name then "up" or "down"
	'LOG_DYNAMO_ADD_ITEM'			=> '<strong>Created new dynamic avatar item</strong><br />» <strong>%1$s</strong> in layer <strong>%2$s</strong>', // item name then layer name
	'LOG_DYNAMO_EDIT_ITEM'			=> '<strong>Edited dynamic avatar item</strong><br />» %s',
	'LOG_DYNAMO_DELETE_ITEM'		=> '<strong>Deleted dynamic avatar item</strong><br />» %s',
));

?>
