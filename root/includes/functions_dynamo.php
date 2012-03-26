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

/**
* Get the path for a user's dynamic avatar
* Where it should be saved etc
*/
function get_avatar_image_path($user_id)
{
	global $config, $phpbb_root_path;
	return $phpbb_root_path . $config['dynamo_avatar_fp'] . '/' . $user_id . '_' . time() . '.png';
}

/**
* From the php.net documentation: http://www.php.net/manual/en/function.imagecopymerge.php
* Main script by aiden dot mail at freemail dot hu
* Transformed to imagecopymerge_alpha() by rodrigo dot polo at gmail dot com
*/
function merge_images($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
	if (!isset($pct))
	{
		return false;
	}

	$pct /= 100;
	// Get image width and height
	$w = imagesx($src_im);
	$h = imagesy($src_im);
	// Turn alpha blending off
	imagealphablending($src_im, false);

	// Find the most opaque pixel in the image (the one with the smallest alpha value)
	$minalpha = 127;
	for ($x = 0; $x < $w; $x++)
	{
		for ($y = 0; $y < $h; $y++)
		{
			$alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
			if ($alpha < $minalpha)
			{
				$minalpha = $alpha;
			}
		}
	}

	// Loop through image pixels and modify alpha for each
	for($x = 0; $x < $w; $x++)
	{
		for ($y = 0; $y < $h; $y++)
		{
			// Get current alpha value (represents the transparency)
			$colorxy = imagecolorat($src_im, $x, $y);
			$alpha = ($colorxy >> 24) & 0xFF;

			// Calculate new alpha
			if ($minalpha !== 127)
			{
				$alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
			}
			else
			{
				$alpha += 127 * $pct;
			}

			// Get the color index with new alpha
			$alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);

			// Set pixel with the new color + opacity
			if (!imagesetpixel($src_im, $x, $y, $alphacolorxy))
			{
				return false;
			}
		}
	}

	imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
}
?>
