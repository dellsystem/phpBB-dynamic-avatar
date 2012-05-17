<?php
/**
*
* help_points [English]
*
* @package language
* @version $Id: help_points.php 604 2009-11-30 21:04:37Z wuerzi $
* copyright (c) 2009 wuerzi & femu
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if ( empty($lang) || !is_array($lang) )
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » „ “ — …
//

$help = array(
	array(
		0 => '--',
		1 => 'General'
	),
	array(
		0 => 'Edit posts',
		1 => 'If a user edit his post, the points for the post are re-calcualted and he only receives the points after the edits. But the post will only be re-calculated, if you set points for posting in this forum ( > 0 ) in the Forum ACP and the switch for posting is set to on.'
	),
	array(
		0 => 'Edit topics',
		1 => 'If a user edit his topic (so first post in a topic), he will only receive the points for generating a new topic. So in fact the topic will be completely re-calculated.
But the topic will only be re-calculated, if you set points for new topics for this forum ( > 0 ) in the Forum ACP and the switch for new topics is set to on.'
	),
	array(
		0 => 'Edit attachments',
		1 => 'If a user add an attachment to his post or he deletes an attachment, only the points for the attachments are calculated which are finally within the post. 
If he deletes all attachments, the general points for attachemnts will be substracted too. Attachemnts are always calculated and they have nothing to do to with new topics or posts!'
	),
	array(
		0 => 'Edit polls',
		1 => 'If a user edit his poll, the poll will be completly re-calculated and he finally receives the points for those parts which are left. If you delete the poll, all poll points will be substracted and the user will receive the points as it was a new topic.
But the topic will only be re-calculated, if you set points for topics for in the advanced points settings ( > 0 ) in the Forum ACP and the switch for new topics is set to on. This is due the fact, that polls are always new topics!'
	),
	array(
		0 => 'Delete posts',
		1 => 'If a user deletes a post, the received points are substracted from his points account.
If a moderator deletes a post, the points for the user will remain on his account. Also if you have automatic pruning active, the points for the user will remain.'
	),
	array(
		0 => 'bbCodes',
		1 => 'All charcters within a bbCode will be counted. The bbCode itself will not be counted.'
	),
	array(
		0 => 'Code blocks',
		1 => 'Everything within code blocks ( [code] [/code] ) will not be counted!'
	),
	array(
		0 => 'Smilies',
		1 => 'Smiles are not counted. But be aware, that between two smilies you have blanks, which are counted as a character!'
	),
	array(
		0 => 'Charcaters an special characters',
		1 => 'Every single character will be counted, if you have enabled the counting of characters. Characters are all letters, numbers, special characters and even empty spaces!'
	),
	array(
		0 => 'Quotes',
		1 => 'Everything between the quote bbCodes ( [quote] / [/quote] ) will not been counted! Only the post itself and the text outside the quote is counted.'
	),
	array(
		0 => 'Important note!',
		1 => 'Only text within bbCodes will be counted, but not the bbCode itself. The code will not be read out from the database!! So the starting [xxx] and the ending [/xxx] are the important codes. So if you don\'t end a bbCode, only the text until the opening tag will be counted.
<br /><br />
If a user edit a post, for which he didn\'t get points earlier (ie. before installing the mod or cause you set it inactive for a while), the user won\'t get points for editing!
<br /><br />
If a user edit a post or topic, where he didn\'t get points from the advanced points part earlier, this post or topic won\'t be recalculated!'
	),
	array(
		0 => '--',
		1 => 'Points settings'
	),
	array(
		0 => 'General',
		1 => 'Here you can enter a different name for your points, enable/disable the Ultimate Points System and enter a disable message.
Additionally you can enable/disable the different parts from the Ultimate Points System and some more stuff, which should be self explaning.'
	),
	array(
		0 => 'Group transfer',
		1 => 'With this option in the main points settings you have the possibility to transfer a certain amount of points to a group, remove or set all to the same value. If you fill the subject and the comment field, you can send a personal message to all members of the group. You can use of course all bbCodes, but you will only the the most common in the mini box.'
	),
	array(
		0 => 'Reset user points',
		1 => 'With this option in the main points settings, you can reset all user points to zero. But be careful! This action cannot be undone!'
	),
	array(
		0 => 'Reset user logs',
		1 => 'With this option in the main points settings, you can reset all user logs. But be careful! This action cannot be undone!'
	),
	array(
		0 => '--',
		1 => 'Advanced points settings'
	),
	array(
		0 => 'Attachments',
		1 => 'You can give points for topics and posts with an attachment. The main points are given once and the additional points are given for each attachment.
You <strong>CANNOT</strong> disable attachments on per forum basis!'
	),
	array(
		0 => 'Polls',
		1 => 'You can give points for the poll itself (these are give once) and points for each poll option.
As polls are only possible in new topics, points are only given, if you set points ( > 0 ) for new topics in the forum settings and enabled the switch for new topics!'
	),
	array(
		0 => 'New topics',
		1 => 'Additionally to the main per topic points from the forum settings, you can give points for each word and/or points for each character.
If you set 0 in the forum or disabled the points for new topics, the additional points are not counted!'
	),
	array(
		0 => 'New posts/replies',
		1 => 'Additionally to the main per post points from the forum settings, you can give points for each word and/or points for each character.
If you set 0 in the forum or disabled the points for new posts, the additional points are not counted!'
	),
	array(
		0 => 'Download an attachment',
		1 => 'You can set costs per download. If you set 0 here, the download is for free. If the user does not have enough points, he cannot download the attachment!

Important note! As ie attached pictures are shown directly within a post and they are normally visible, the points for the picture attachments are directly substracted. If a user does not have enough points, he won\'t see the pictures!'
	),
	array(
		0 => 'Points per warning',
		1 => 'If a user gets warned, you have the possibility to substract points from his account. If the user hasn\'t enough points, the value will be substracted anyway. He then will have negative points!'
	),
	array(
		0 => 'Points for registering',
		1 => 'Here you can set, how much points a user will receive on registering on your board. This way he will have a seed capital. These points are given at once. So not after releasing his account!'
	),
	array(
		0 => 'Entries per page',
		1 => 'Here you can set how much entries are shown per page in the Logs and the Lottery history. Minumum value is 5.'
	),
	array(
		0 => 'Number of most rich users',
		1 => 'Here you can set, how much of the most rich users are shown. You will see this number at multiple places: On the index, at the bank and in the overview.
Set 0 to deactivate this feature. On the index page the part will not be visible any more and in the bank and the overview, users will see a corresponding message.'
	),
	array(
		0 => '--',
		1 => 'Forum points settings'
	),
	array(
		0 => 'General',
		1 => 'The forum points are mostly independant from the other points settings and will be counted additionally. You can set the points per forum. This way you can set the points, users will receive, completely  individually. You will find these settings under ACP - Forums - Manage Forums - Forum you like to edit.'
	),
	array(
		0 => 'The switches',
		1 => 'With the forum points switches, enable/disable the forum points globally. If you disable Points for topics, posts or edit, points are NOT counted in all forums. Additionally the advanced points are not counted, until you enable the switches again.'
	),
	array(
		0 => 'Global forum points settings',
		1 => 'You can set the points globally here for all forums at once. These settings will overwrite any previously individual points settings! So if you use this feature, you have to rework all forums, where you set different points!'
	),
	array(
		0 => 'New Topic',
		1 => 'Here you can set, how much points a user will receive for creating a new topic. You can set it globally or individually via the ACP -> Forums.
If you set 0, also the advanced points settings (words, characters) are NOT counted.'
	),
	array(
		0 => 'New post',
		1 => 'Here you can set, how much points a user will receive for creating a new post or reply. You can set it globally or individually via the ACP -> Forums.
If you set 0, also the advanced points settings (words, characters) are NOT counted.'
	),
	array(
		0 => 'Edit topic/post',
		1 => 'Here you can set, if a user will gain points for editing a topic or a post.'
	),
	array(
		0 => '--',
		1 => 'Bank'
	),
	array(
		0 => 'General',
		1 => 'If the bank is activated, the users will see an additional tab in Ultimate Points main menu. Additionally you will find infos in the profile view and in the viewtopic part, where admin/moderators have the possibility to change the users amounts of the points and the bank, if allowed.'
	),
	array(
		0 => 'Interest rate',
		1 => 'Here you can set an interest rate between 0 and 100 percent per pay period. The pay period is set as an "in days" period. After this period the users will get their interest rate payed out automatically. You can also define, at what amount these payments will stop. So as soon as an user has more on his bank account, as you defined, he won\'t get any additional payments.'
	),
	array(
		0 => 'Bank costs',
		1 => 'Here you can set the cost for withdrawing money from the bank account. You can set any value between 0 to 100 percent. Additionally you can set a fixed cost value for maintaining the bank account per period. This one will have the same period as the the interest rate.'
	),
	array(
		0 => '--',
		1 => 'Lottery'
	),
	array(
		0 => 'General',
		1 => 'If the Lottery is enabled, the users will have access to the Lottery module.
If you disable the module, the Lottery is still running in the background, but the users won\'t have access. The Lottery will run at the pre-defined period via the lottery page or the index page.'
	),
	array(
		0 => 'How the Lottery works',
		1 => 'With a random calculation, one ticket out of all purchased tickets, is selcted as a possible winner ticket. Afterwards another random calculation defines - using the the value of the cahnce to win - if the selected ticket really wins or not. If it does not win, the value goes to the Jackpot until one ticket wins.'
	),
	array(
		0 => 'Jackpot',
		1 => 'The Lottery works with a Jackpot system. So the value from all bought tickets will go into the Jackpot. Additionally you can define a starting value for the Jackpot, which will be payed out additionally. If noone wins, the Jackpot will remain and grows with the next playing round.'
	),
	array(
		0 => 'Chance to win',
		1 => 'Here you can set the chance to win. The users will not see this value. The higher you set this value, the bigger is the chance to win.
0 means noone will win, 100 means the Jackpot will be payed out to one of the players.'
	),
	array(
		0 => 'Paying period',
		1 => 'You can set the paying period in hours. This does have an effect immediately!
If you set the paying period to 0, the payout will stop.
The users cannot pay any tickets and the Jackpot will remain with it\'s current value.
You can use this feature to force a payout. As soon as you set a new value, the payout will start at the next call of the page.'
	),
	array(
		0 => 'Sender ID',
		1 => 'Here you can set the ID of the user, who will send the lucky user the winning message via a personal message. If you don\'t like to use a different sender, set 0 here. The user will then received the message from himself.'
	),
	array(
		0 => '--',
		1 => 'Robbery'
	),
	array(
		0 => 'General',
		1 => 'With the Robbery module the user may rob points from other users point account (not the bank!). You can enable/disable the modul. If it is disabled, the users won\'t see the modul.'
	),
	array(
		0 => 'Personal message settings',
		1 => 'Here you can set, if the users are informed about the robbery tries. If the user set in his peronal settings, that he won\'t receive personal messages from other users, he won\'t get messages from the Robebry module.
If the one who tries to rob another user is blocked from sending PMs, the robbed user still gets a message. This messages are pre-defined and the user, who tried to rob, does not have any influence on this message..'
	),
	array(
		0 => 'Chance for successful robbery',
		1 => 'Here you can set the chance to make a successful robbery in percent. So you can set any value between 0 and 100 percent.'
	),
	array(
		0 => 'Penalty for a failed robbery',
		1 => 'Here you can set the penalty a user has to pay, if his robbery fails.
The thief will have to pay the set percantage of the value he tried to rob. What a user has to pay, if he fails, is shown on the Robbery page. You can set any value between 0 and 100 percent.'
	),
	array(
		0 => 'Maximum value, which can be robbed at once',
		1 => 'Here you can set the maximum percentage of the ponts the robbed user owns, which can be robbed at once. This value is shown on the Robebry page. You can use any value between 0 and 100 percent.'
	),
	array(
		0 => '--',
		1 => 'Transfer / Donate'
	),
	array(
		0 => 'General',
		1 => 'If the users have the permission, the users will have the possibility to transfer points from their own account to another users point account. This can be done from the Transfer page, from the viewtopic view or from the profile view.'
	),
	array(
		0 => 'Personal message with transfers',
		1 => 'You can enable/disable this feature within the main points setting page in the ACP. If user is blocked from sending PMs, he cannot add a comment to his transfer.'
	),
	array(
		0 => 'Logs',
		1 => 'All transfers incl. all the needed informations are shown on the logs page. This feature can be enabled/disabled on the main point settings page. You also have the possibility to reset ALL users logs. But be aware, this cannot be undone!'
	),
	array(
		0 => '--',
		1 => 'Permissions'
	),
	array(
		0 => 'Administrator permissions',
		1 => 'You can give the administrator the right to manage the Ultimate Points System. This can be done in ACP -> Permissions -> Administrator permissions -> Advanced permissions.'
	),
	array(
		0 => 'Global moderators permissions',
		1 => 'Within the module ACP -> Permissions -> Global moderators you can set, if they will have the permission to change the points and the bank accounts from other users.'
	),
	array(
		0 => 'User and group permissions',
		1 => 'Within the module ACP -> Permissions -> User/Group permissions, you can set different things conerning the Ultimate Points System. See below:
<ul>
	<li>Can use the Ultimate Points</li> 			
	<li>Can use the Bank Module</li>  	 	 	
	<li>Can use the Log Module</li> 			
	<li>Can use the Lottery Module</li> 			
	<li>Can use the Robbery Module</li> 			
	<li>Can use the Transfer Module</li>
</ul>'
	),
	array(
		0 => '--',
		1 => 'AddOns and compatibility with other modifications'
	),
	array(
		0 => 'General',
		1 => 'The Ultimate Points System is currently supported by some other modifications.'
	),
	array(
		0 => 'phpBB Arcade',
		1 => 'The phpBB Arcade from Jeff ( <a href="http://www.jeffrusso.net/">JeffRusso.net</a> ) does support the Ultimate Points System. The Arcade will self-detect, if the UPS is installed. You can the set the cost per game and a Jackpot.'
	),
	array(
		0 => 'phpbb Gallery',
		1 => 'The phpBB Gallery from nickvergessen ( <a href="http://www.flying-bits.org/">Flying-bits.org</a> ) supports Ultimate Points.<br />
As soon as you have installed the Gallery, you will see additional fields in the main points settings.<br /><br />You will need to copy the included hookup addon, which can be found in the UPS package in contrib/AddOns/Gallery_Integration/root/gallery/includes/hookup_gallery.php to it\'s correct location in the gallery folder!<br /><br /><strong>Important hint!</strong> If you are using one of the image view boxes (Highslide, Lightbox, Shadowbox.), the points are substracted twice here due to a technical issue. So if you like to substract 2 points for viewing images, you have to enter 1 point here!<br />For the Highslide Box you can find a fix for this issue <a href="http://highslide.com/forum/viewtopic.php?p=18498#p18498">here</a><br /><br />Additionally you can enable/disbale, if user with a negative or zero points account will still be able to view images or not.'
	),
	array(
		0 => 'Medal System MOD',
		1 => 'The Medal System from Gremlinn ( <a href="http://test.dupra.net/">Gremlinn\'s Mod Support Site</a> ) does support the UPS.
Within the Medal Mod ACP, you will find a field, where you can set, how much points a user will receive addtionally to the given medal.'
	),
	array(
		0 => 'Sudoku',
		1 => 'The Sudoku MOD from el_teniente ( <a href="http://vfalcone.ru/">vfalcone.ru</a> ) does support the UPS.
Although it still have an internal points system, you can set the points, which users will receive within the rewards system.'
	),
	array(
		0 => 'F1 Webtipp',
		1 => 'The Formula 1 Webtipp from Dr.Death ( <a href="http://www.lpi-clan.de/">LPI-Clan</a> ) does support the UPS.
You can set who much points users will receive with their tipps.'
	),
	array(
		0 => 'DM Video',
		1 => 'The DM Video MOD from femu ( <a href="http://area53.die-muellers.org/">femu\'s Mod Support Site</a> ) does support the UPS.
You can set there, how much points users will receive, when they add a video. This amount will be substracted of course, when the user deletes a video.'
	),
	array(
		0 => 'Shop Mod',
		1 => 'The Shop Mod v1.0.4 Beta from Adrian does support the UPS. 
For more details, go to Adrians website at <a href="http://phpbbgods.org/community/index.php]phpbbgods.org">phpbbgods.org</a>.<br />Also use this side to ask for support on handling the Shop Mod!'
	),
	array(
		0 => 'User Blog Mod',
		1 => 'The User Blog Mod from EXreaction ( <a href="http://www.lithiumstudios.org/">Lithiumstudios.org</a> ) does support the UPS.

In order to use the User Blog Mod with the UPS, you need to installe the plugin, which you can find in the folder  contrib/AddOns/User_Blog_Mod_Plugin.
After the installtion you will find additional settings in the Blog Mod settings.'
	),
	array(
		0 => 'Board3 Portal',
		1 => 'You will find in the contrib/AddOns/Board3_Portal_AddOns folder an addon, which will show the lottery on the Board3 Portal page ( <a href="http://www.board3.de/">Board3 Portal</a> ).'
	),
	array(
		0 => 'DM Easy Download System',
		1 => 'The DM EDS from femu ( <a href="http://area53.die-muellers.org/">femu\'s Mod Support Site</a> ) supports the Ultimate Points.<br />The DM EDS is a very easy Downloadsystem, where you can set costs for each download seperately. So if the users don\'t have enough points, they can\'t download the files.'
	),
	array(
		0 => 'DM Quotes Collection',
		1 => 'DM Quotes Collection is a simple tool from femu ( <a href="http://area53.die-muellers.org/">femu\'s Mod Support Site</a> ), where you have the possibility to start creating a quote collection, which will be shown in a random order on the index page. All quotes are managed via the ACP. As soon as a quote is released, the user will receive the points, which you set in the ACP.'
	),
	array(
		0 => 'Knuffel (Dice role game)',
		1 => 'Ultimate Points is supported by Knuffel (a Kniffel clone game) from Wuerzi ( <a href="http://www.spieleresidenz.de/">Spieleresidenz</a> ). Within this game you need to role different sets of dice figures to get the maximum of points. In the ACP you can set the costs per game and also set a Jackpot.'
	),
	array(
		0 => 'phpBB Ajax Partners',
		1 => 'The phpbb Ajax Partners from djchrisnet ( <a href="http://djchrisnet.de/?page=partners">djchrisnet Webdesign</a> ) will support the Ultimate Points.<br />With the Ajax Partners Mod, you can add a partners area, where you can receive points for comments, ratings, etc.<br />
<strong>Still in development!</strong>'
	),	
	array(
		0 => 'Invite A Friend',
		1 => 'The Invite A Friend Mod from Bycoja ( <a href="http://bycoja.by.funpic.de/">Bycoja\'s Bugs</a> ) as of version 0.5.3 does support the Ultimate Points.
. Invite A Friend is an addition to phpBB3, that enables your users to advise their friends of your board.'
	),	
);

?>