<?php
/**
*
* @package Dynamo (Dynamic Avatar MOD for phpBB3)
* @version $Id: functions_dynamo.php ilostwaldo@gmail.com$
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

/**
* get_item_image_url()
*/

/**
* Get the path for an item's image from its layer ID and item ID
* Returns a path relative to the board root (e.g. `image/dynamo/
* Depends on $config['dynamo_image_fp'];
* Modes: entire path, just the image filename, just the dirs
*/
function get_item_image_path($mode = 'entire', $layer_id = 0, $item_id = 0)
{
	global $config, $phpbb_root_path;
	$filename = $layer_id . '-' . $item_id . '.png';
	$dirs = $phpbb_root_path . $config['dynamo_image_fp'];

	switch ($mode)
	{
		case 'entire':
			return $dirs . '/' . $filename;
		case 'filename':
			return $filename;
		case 'dirs':
			return $dirs;
	}
}
?>
