<?php
/**
*
* @package Dynamo (Dynamic Avatar MOD for phpBB3)
* @version $Id: info_ucp_dynamo.php ilostwaldo@gmail.com$
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
	'DYNAMO_EXPLAIN'		=> "Here you'll be able to edit your avatar. All the items in your inventory (if the shop is enabled etc) will appear, and you can click them to try them on, and then press submit to save the changes. If the Salvation Army setup is enabled, then all the possible items that can be worn will appear, and you can choose among them. At the moment, only the Salvation Army setup is possible, so just click on an item to try it on.",
	'RESTORE_ORIGINAL'		=> 'Restore original items',
	'RESTORE_DEFAULT'		=> 'Restore default items',
));

?>
