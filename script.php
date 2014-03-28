<?php
/**
 * Yet Another Social Plugin
 *
 * @copyright  Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @since  1.0
 */
class plgContentYetAnotherSocialInstallerScript
{
	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string                   $type    The action being performed
	 * @param   JInstallerAdapterPlugin  $parent  The function calling this method
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function preflight($type, $parent)
	{
		// Requires Joomla! 3.2 or newer
		if (version_compare(JVERSION, '3.2', 'lt'))
		{
			JError::raiseWarning(null, JText::_('PLG_CONTENT_YETANOTHERSOCIAL_ERROR_VERSION'));

			return false;
		}
	}
}
