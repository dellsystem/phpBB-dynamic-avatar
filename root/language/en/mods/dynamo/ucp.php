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
	'DYNAMO_EXPLAIN'		=> "Here you'll be able to edit your avatar. All the possible items that can be worn will appear, and you can choose among them. If Javascript is enabled, you can click on an item to try it on - a preview of your avatar will appear on the left panel. Make sure to hit <strong>submit</strong> in order to save your changes.",
	'RESTORE_ORIGINAL'		=> 'Restore original items',
	'RESTORE_DEFAULT'		=> 'Restore default items',

	'INVALID_ITEM'			=> 'You must select a valid item for layer <strong>%s</strong>.',
	'MANDATORY_LAYER'		=> 'Layer <strong>%s</strong> is mandatory, so you must select an item for it.',
	'NO_ITEM'				=> 'None',
	'MANDATORY'				=> 'Mandatory',
));

?>
