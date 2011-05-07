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
	'ACP_DYNAMO'					=> 'Dynamic avatar',
	'ACP_DYNAMO_OVERVIEW'			=> 'Dynamic avatar overview',
	'ACP_DYNAMO_SETTINGS'			=> 'Dynamic avatar settings',
	'ACP_DYNAMO_LAYERS'				=> 'Dynamic avatar layers',
	'ACP_DYNAMO_ITEMS'				=> 'Dynamic avatar items',
	'ACP_DYNAMO_USERS'				=> 'Dynamic avatar users',
));

?>
