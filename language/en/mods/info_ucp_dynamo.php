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
	'UCP_DYNAMO_UPDATED'		=> 'Successfully updated dynamic avatar.',
	'UCP_DYNAMO_MOD'			=> 'Dynamic avatar',
	'PURCHASE'					=> 'Purchase',
	'CURRENCY'					=> 'points',
	'STOP_TRYING_ON'			=> 'Remove items from preview',
	'NOT_ENOUGH_POINTS'			=> 'You don\'t have enough points to purchase this item.',
	'SUCCESSFUL_PURCHASE'		=> 'You have successfully purchased <strong>%s</strong>.',
));

?>
