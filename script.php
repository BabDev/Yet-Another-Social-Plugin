<?php
/**
* Yet Another Social Plugin
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
*/

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @package		Yet Another Social Plugin
 * @since		1.0
 */
class plgContentYetAnotherSocialInstallerScript {

	/**
	 * Function to act prior to installation process begins
	 *
	 * @param	string	$type	The action being performed
	 * @param	string	$parent	The function calling this method
	 *
	 * @return	void
	 * @since	1.0
	 */
	function preflight($type, $parent) {
		// Requires Joomla! 1.7 or newer
		$jversion = new JVersion();
		if (version_compare($jversion->getShortVersion(), '1.7', 'lt')) {
			JError::raiseWarning(null, JText::_('PLG_CONTENT_YETANOTHERSOCIAL_ERROR_J17'));
			return false;
		}
	}
}
