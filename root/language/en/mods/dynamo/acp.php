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
	'ACP_DYNAMO_USERS_EXPLAIN'		=> 'Here you can view the dynamic avatar for every user who has one.',
	'ACP_DYNAMO_OVERVIEW_EXPLAIN'	=> 'Just has some information about the dynamic avatar MOD on your board. I\'m not really sure what should go on this page, to be honest. Hit me up if you have suggestions.',
	'ACP_DYNAMO_MOD_STATUS'			=> 'Dynamic avatar MOD status',
	'ACP_DYNAMO_NUM_LAYERS'			=> 'Number of layers available',
	'ACP_DYNAMO_NUM_USERS'			=> 'Number of users with a dynamic avatar',
	'ACP_DYNAMO_NUM_ITEMS'			=> 'Number of items available',
));

?>
