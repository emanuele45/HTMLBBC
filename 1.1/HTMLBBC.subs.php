<?php
/**
 * @name	  ElkArte Forum
 * @copyright ElkArte Forum contributors
 * @license   BSD http://opensource.org/licenses/BSD-3-Clause
 *
 * This software is a derived product, based on:
 *
 * Simple Machines Forum (SMF)
 * copyright:	2011 Simple Machines (http://www.simplemachines.org)
 * license:  	BSD, See included LICENSE.TXT for terms and conditions.
 *
 * @version 1.1
 *
 */

class HTMLBBC
{
	public static function bbc_codes(&$bbcodes)
	{
		$bbcodes[] = array(
			BBC\Codes::ATTR_TAG => 'html',
			BBC\Codes::ATTR_TYPE => BBC\Codes::TYPE_UNPARSED_CONTENT,
			BBC\Codes::ATTR_CONTENT => '$1',
			BBC\Codes::ATTR_BLOCK_LEVEL => true,
			BBC\Codes::ATTR_DISABLED_CONTENT => '$1',
			BBC\Codes::ATTR_LENGTH => 4,
		);
	}

	public static function preparse_tokenized_code(&$message, $previewing, $parts)
	{
		if (!$previewing && strpos($message, '[html]') !== false) {
			if (allowedTo('admin_forum')) {
				$message = preg_replace_callback('~\[html\](.+?)\[/html\]~is', 'preparsecode_html_callback', $message);
			}
			// We should edit them out, or else if an admin edits the message they will get shown...
			else {
				while (strpos($message, '[html]') !== false) {
					$message = preg_replace('~\[[/]?html\]~i', '', $message);
				}
			}
		}
	}

	public static function unpreparse_code(&$message, &$parts, $i)
	{
		$message = preg_replace_callback('~\[html\](.+?)\[/html\]~i', 'preparsecode_unhtml_callback', $message);
	}
}

/**
 * Prepares text inside of html tags to make them safe for display and prevent bbc rendering
 *
 * @package Posts
 * @param string[] $matches
 */
function preparsecode_html_callback($matches)
{
	return '[html]' . strtr(un_htmlspecialchars($matches[1]), array("\n" => '&#13;', '  ' => ' &#32;', '[' => '&#91;', ']' => '&#93;')) . '[/html]';
}

/**
 * Reverses what was done by preparsecode to html tags
 *
 * @package Posts
 * @param string[] $matches
 */
function preparsecode_unhtml_callback($matches)
{
	return '[html]' . strtr(htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8'), array(
			'\\&quot;' => '&quot;', '&amp;#13;' => '<br />',
			'&amp;#32;' => ' ', '&amp;#91;' => '[', '&amp;#93;' => ']')) . '[/html]';
}
