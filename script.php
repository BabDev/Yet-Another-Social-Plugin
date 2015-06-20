<?php
/**
 * Yet Another Social Plugin
 *
 * @copyright  Copyright (C) 2011-2015 Michael Babker. All rights reserved.
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
	 * Minimum supported Joomla! version
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $minimumJoomlaVersion = '3.4.1';

	/**
	 * Minimum supported PHP version
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $minimumPHPVersion = '5.4';

	/**
	 * Function to act prior to installation process begins
	 *
	 * @param   string                   $type    The action being performed
	 * @param   JInstallerAdapterPlugin  $parent  The function calling this method
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function preflight($type, $parent)
	{
		// PHP Version Check
		if (version_compare(PHP_VERSION, $this->minimumPHPVersion, 'lt'))
		{
			JError::raiseNotice(
				null, JText::sprintf('PLG_CONTENT_YETANOTHERSOCIAL_ERROR_INSTALL_PHPVERSION', $this->minimumPHPVersion)
			);

			return false;
		}

		// Joomla! Version Check
		if (version_compare(JVERSION, $this->minimumJoomlaVersion, 'lt'))
		{
			JError::raiseNotice(
				null, JText::sprintf('PLG_CONTENT_YETANOTHERSOCIAL_ERROR_INSTALL_JVERSION', $this->minimumJoomlaVersion)
			);

			return false;
		}

		return true;
	}
}
