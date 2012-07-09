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
	// acp_dynamo_overview.html
	'DYNAMO_OVERVIEW'			=> 'Overview',
	'DYNAMO_OVERVIEW_EXPLAIN'	=> 'Just has some information about the dynamic avatar MOD on your board. I\'m not really sure what should go on this page, to be honest. Hit me up if you have suggestions.',
	'MOD_STATUS'				=> 'Dynamic avatar MOD status',
	'POINTS_SUPPORT'			=> 'Integration with points MOD',
	'NUM_LAYERS'				=> 'Number of layers available',
	'NUM_USERS'					=> 'Number of users with a dynamic avatar',
	'NUM_ITEMS'					=> 'Number of items available',

	// acp_dynamo_settings.html
	'DYNAMO_SETTINGS_EXPLAIN'	=> 'Here you can modify the settings of your dynamic avatar MOD.',
	'POINTS_MOD'				=> 'Use points MOD',
	'MOD_ENABLE'				=> 'Enable dynamic avatar',
	'POINTS_MOD_DESC'			=> 'If this is set to yes, you can use the points-related features of this MOD - namely, users will be able to purchase inventory items. This option is only available if the ultimate points system MOD (or another compatible MOD) is installed on your board.',
	'CHANGE_BASE'				=> 'Allow users to change base',
	'CHANGE_BASE_DESC'			=> 'If this is enabled, users will be able to change items in the layers marked as "base".',
	'MANDATORY'					=> 'Set dynamic avatar to mandatory',
	'MANDATORY_DESC'			=> 'If this is enabled, then the only type of avatar a user can have is a dynamic avatar. <strong>Note: not yet implemented.</strong>',
	'DYNAMO_WIDTH'				=> 'Dynamic avatar width',
	'DYNAMO_WIDTH_DESC'			=> 'Set this to the width, in pixels, of the images you upload. If you upload an image of a different width, you will be able to crop or enlarge it as necessary. <strong>Note: Not yet implemented.</strong>',
	'DYNAMO_HEIGHT'				=> 'Dynamic avatar height',
	'DYNAMO_HEIGHT_DESC'		=> 'See above, for height.',
	'DYNAMO_IMAGE_FP'			=> 'Uploaded image filepath',
	'DYNAMO_IMAGE_FP_DESC'		=> 'Path under your phpBB root directory to which item images will be uploaded. By default, this is <samp>images/dynamo</samp>. Please ensure that this directory is writeable by the user your webserver runs as.',
	'DYNAMO_AVATAR_FP'			=> 'Dynamic avatar filepath',
	'DYNAMO_AVATAR_FP_DESC'		=> 'Path under your phpBB root directory to which your users\' dynamic avatars will be saved. By default, this is <samp>images/avatars/dynamo</samp>. Please ensure that this directory is writeable by the user your webserver runs as.',

	// acp_dynamo_items.html
	'DYNAMO_ITEMS'				=> 'Items',
	'DYNAMO_ITEMS_EXPLAIN'		=> 'Here you can manage the items associated with each layer. Items are grouped by layer, which are themselves sorted by their position (top to bottom).',
	'CREATE_NEW_ITEM'			=> 'Create new item',

	// acp_dynamo_items_edit.html
	'ADDING_ITEM'				=> 'Add a new item',
	'ADDING_ITEM_EXPLAIN'		=> 'Here you can add a new item using an image file on your computer and assign it to the desired layer.',
	'EDITING_ITEM'				=> 'Editing item %s',
	'EDITING_ITEM_EXPLAIN'		=> 'Here you can edit the details for an item. Note that uploading a new image here to overwrite the old work won\'t work as that feature has not yet been implemented.',
	'ITEM_LAYER_DESC'			=> 'Choose the layer this item will belong to. You can choose to set it to no layer if you want to make it unavailable for now.',
	'ITEM_PREVIEW'				=> 'Item preview',
	'ITEM_SETTINGS'				=> 'Item settings',
	'ITEM_NAME'					=> 'Item name',
	'ITEM_DESCRIPTION'			=> 'Item description',
	'LAYER'						=> 'Layer',
	'IMAGE_FILE'				=> 'Image file',
	'IMAGE_FILE_DESC'			=> 'Upload the image file for this item from your computer.',
	'UNCATEGORISED'				=> 'Uncategorised',
	'ITEM_PRICE'				=> 'Item price',
	'ITEM_PRICE_EXPLAIN'		=> 'Cost of the item (using the points MOD)',

	// acp_dynamo_users.html
	'DYNAMO_USERS'				=> 'Users',
	'DYNAMO_USERS_EXPLAIN'		=> 'Here you can view the dynamic avatar for every user who has one.',

	// acp_dynamo_layers.html
	'DYNAMO_LAYERS'				=> 'Layers',
	'DYNAMO_LAYERS_EXPLAIN'		=> 'Here you can view all the layers you have created and edit them.',
	'LAYER_ID'					=> 'Layer ID',
	'LAYER_NAME'				=> 'Layer name',
	'LAYER_DESCRIPTION'			=> 'Layer description',
	'LAYER_POSITION'			=> 'Layer position',
	'LAYER_MANDATORY'			=> 'Mandatory layer?',
	'DEFAULT_ITEM'				=> 'Default item',
	'DEFAULT_PRICE'				=> 'Default price',
	'CREATE_NEW_LAYER'			=> 'Create new layer',

	// acp_layers_edit.html
	'EDITING_LAYER'				=> 'Editing layer %s',
	'EDITING_LAYER_EXPLAIN'		=> 'Edit the properties of a layer.',
	'LAYER_SETTINGS'			=> 'Layer settings',
	'LAYER_POSITION_EXPLAIN'	=> 'Choose the position of the layer.',
	'LAYER_MANDATORY_EXPLAIN'	=> 'If this is enabled, then users must select an item for this layer',
	'DEFAULT_ITEM_EXPLAIN'		=> 'If desired, select the default item. If there is only one item associated with this layer and the layer is mandatory, you have no choice; otherwise, you can choose among the items or select "No default item".',
	'DEFAULT_ITEM_PRICE'		=> 'Note that you cannot set a price for the default item of a mandatory layer.',
	'NO_LAYER_ITEMS'			=> 'No items for this layer',
	'ADD_LAYER'					=> 'Add a layer',
	'ADD_LAYER_EXPLAIN'			=> 'Create a new layer.',
	'ADD_ITEMS_AFTER'			=> 'You can set a default item after creating the layer and adding images.',
	'LAYER_AT_BOTTOM'			=> 'At the very bottom',
	'IMMEDIATELY_BELOW'			=> 'Immediately below %s',
	'NO_DEFAULT_ITEM'			=> 'No default item',
	'LAYER_AT_TOP'				=> 'At the very top',
	'LAYER_CURRENT_POSITION'	=> '(keep it where it is)',
	'DEFAULT_PRICE_EXPLAIN'		=> 'The default price for each item in this layer, used when creating that layer. This value is overriden by the price set for each individual item. Set to 0 to make it free.',
));

?>
