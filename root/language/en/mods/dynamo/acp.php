<?php
/**
*
* @package Dynamo (Dynamic Avatar MOD for phpBB3)
* @version $Id: acp.php ilostwaldo@gmail.com$
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
	// Users page
	'ACP_DYNAMO_USERS_EXPLAIN'		=> 'Here you can view the dynamic avatar for every user who has one.',

	// Overview page
	'ACP_DYNAMO_OVERVIEW_EXPLAIN'	=> 'Just has some information about the dynamic avatar MOD on your board. I\'m not really sure what should go on this page, to be honest. Hit me up if you have suggestions.',
	'ACP_DYNAMO_MOD_STATUS'			=> 'Dynamic avatar MOD status',
	'ACP_DYNAMO_NUM_LAYERS'			=> 'Number of layers available',
	'ACP_DYNAMO_NUM_USERS'			=> 'Number of users with a dynamic avatar',
	'ACP_DYNAMO_NUM_ITEMS'			=> 'Number of items available',

	// Settings page
	'ACP_DYNAMO_SETTINGS_EXPLAIN'	=> 'Here you can modify the settings for the dynamic avatar MOD.',
	'ACP_DYNAMO_ENABLE'				=> 'Enable dynamic avatar MOD',
	'ACP_DYNAMO_USE_POINTS'			=> 'Use a points MOD',
	'ACP_DYNAMO_USE_POINTS_EXPLAIN'	=> 'If there is a points MOD installed and enabled on your board, then you can use the points-related features of this MOD. <strong>Note: not yet functional.</strong>',
	'ACP_DYNAMO_BASE'				=> 'Allow users to change base',
	'ACP_DYNAMO_BASE_EXPLAIN'		=> 'If this is enabled, users will be able to change items in the layers marked as "base".',
	'ACP_DYNAMO_MANDATORY'			=> 'Set dynamic avatar to mandatory',
	'ACP_DYNAMO_MANDATORY_EXPLAIN'	=> 'If this is enabled, then the only type of avatar a user can have is a dynamic avatar. <strong>Note: not yet implemented.</strong>',

	// Items page
	'ACP_DYNAMO_ITEMS_EXPLAIN'		=> 'Here you can manage the items associated with each layer. Items are grouped by layer, which are themselves sorted by their position (top to bottom).',
	'ACP_DYNAMO_CREATE_ITEM'		=> 'Create new item',
	'ACP_DYNAMO_ITEM_SETTINGS'		=> 'Item settings',
	'ACP_DYNAMO_ITEM_NAME'			=> 'Item name',
	'ACP_DYNAMO_ITEM_DESC'			=> 'Item description',
	'ACP_DYNAMO_LAYER'				=> 'Layer',
	'ACP_DYNAMO_LAYER_EXPLAIN'		=> 'Choose the layer this item will belong to. You can choose to set it to no layer if you want to make it unavailable for now.',
	'ACP_DYNAMO_IMAGE'				=> 'Image file',
	'ACP_DYNAMO_IMAGE_EXPLAIN'		=> 'Upload the image file for this item from your computer.',
	'ACP_DYNAMO_ADD_ITEM'			=> 'Add an item',
	'ACP_DYNAMO_ADD_ITEM_EXPLAIN'	=> 'Here you can add a new item using an image file on your computer and assign it to the desired layer.',
	'ACP_DYNAMO_EDIT_ITEM'			=> 'Edit an item',
	'ACP_DYNAMO_EDIT_ITEM_EXPLAIN'	=> 'Here you can edit the details for an item. Note that uploading a new image here to overwrite the old work won\'t work as that feature has not yet been implemented.',
));

?>
