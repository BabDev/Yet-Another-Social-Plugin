<?php
/**
 * Yet Another Social Plugin
 *
 * @copyright  Copyright (C) 2011-2015 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

/**
 * Yet Another Social Plugin Content Plugin
 *
 * @since  1.0
 */
class PlgContentYetAnotherSocial extends JPlugin
{
	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  2.0
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  2.0
	 */
	protected $db;

	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 * @since  2.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Plugin to add the social buttons
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   object   &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onContentPrepare($context, &$article, &$params, $page)
	{
		// Set the parameters
		$document           = JFactory::getDocument();
		$displayFacebook    = $this->params->get('displayFacebook', '1');
		$displayGoogle      = $this->params->get('displayGoogle', '1');
		$displayTwitter     = $this->params->get('displayTwitter', '1');
		$displayLinkedin    = $this->params->get('displayLinkedin', '1');
		$selectedCategories = $this->params->def('displayCategories', '');
		$position           = $this->params->def('displayPosition', 'top');
		$view               = $this->app->input->get('view', '', 'cmd');

		// Check if the plugin is enabled
		if (JPluginHelper::isEnabled('content', 'yetanothersocial') == false)
		{
			return;
		}

		// Make sure the document is an HTML document
		if ($document->getType() != 'html')
		{
			return;
		}

		// Check whether we're displaying the plugin in the current view
		if ($this->params->get('view' . ucfirst($view), '1') == '0')
		{
			return;
		}

		// Check that we're actually displaying a button
		if ($displayFacebook == '0' && $displayGoogle == '0' && $displayTwitter == '0' && $displayLinkedin == '0')
		{
			return;
		}

		// If we're not in the article view, we have to get the full $article object ourselves
		if ($view == 'featured' || $view == 'category')
		{
			/*
			 * We only want to handle com_content items; if this function returns null, there's no DB item
			 * Also, make sure the object isn't already loaded and undo previous plugin processing
			 */
			if ((!is_null($this->loadArticle($article))) && (!isset($article->catid)))
			{
				$article = $this->loadArticle($article);
			}
		}

		// Make sure we have a category ID, otherwise, end processing
		$properties = get_object_vars($article);

		if (!array_key_exists('catid', $properties))
		{
			return;
		}

		// Make sure the article language is set
		if (!isset($article->language))
		{
			$article->language = $this->loadArticleLanguage($article->id);
		}

		// Get the current category
		if (is_null($article->catid))
		{
			$currentCategory = 0;
		}
		else
		{
			$currentCategory = $article->catid;
		}

		// Define category restrictions
		if (is_array($selectedCategories))
		{
			$categories = $selectedCategories;
		}
		elseif ($selectedCategories == '')
		{
			$categories = [$currentCategory];
		}
		else
		{
			$categories = [$selectedCategories];
		}

		// If we aren't in a defined category, exit
		if (!in_array($currentCategory, $categories))
		{
			// If we made it this far, we probably deleted the text object; reset it
			if (!isset($article->text))
			{
				$article->text = $article->introtext;
			}

			return;
		}

		// Create the article slug
		$article->slug = $article->alias ? ($article->id . ':' . $article->alias) : $article->id;

		// Build the URL for the plugins to use
		$siteURL = substr(JUri::root(), 0, -1);
		$itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid));

		// Declare the stylesheet
		JHtml::_('stylesheet', 'yetanothersocial/default.css', array(), true);

		// Get the article's language
		$artLang = $article->language;

		// Get the site language
		$lang     = JFactory::getLanguage();
		$locale   = $lang->getLocale();
		$langCode = $lang->getTag();

		// Facebook Language
		$FBlanguage = $this->getFacebookLanguage($artLang, $locale);

		// Google+ Language
		$Glang = $this->getGoogleLanguage($artLang, $langCode);

		// Twitter Language
		$twitterLang = $this->getTwitterLanguage($artLang, $locale);

		/*
		 * Check that the scripts aren't already loaded and load if needed
		 */

		// Google +1
		if ($displayGoogle && !in_array('<script type="text/javascript" src="https://apis.google.com/js/plusone.js">' . $Glang . '</script>', $document->_custom))
		{
			$document->addCustomTag('<script type="text/javascript" src="https://apis.google.com/js/plusone.js">' . $Glang . '</script>');
		}

		// Twitter Tweet
		if ($displayTwitter && !in_array('<script type="text/javascript" src="https://platform.twitter.com/widgets.js" async></script>', $document->_custom))
		{
			$document->addCustomTag('<script type="text/javascript" src="https://platform.twitter.com/widgets.js" async></script>');
		}

		// LinkedIn Share
		if ($displayLinkedin && !in_array('<script src="https://platform.linkedin.com/in.js" type="text/javascript"></script>', $document->_custom))
		{
			$document->addCustomTag('<script src="https://platform.linkedin.com/in.js" type="text/javascript"></script>');
		}

		// Get the content and merge in the template; first see if $article->text is defined
		if (!isset($article->text))
		{
			$article->text = $article->introtext;
		}

		// Set the tweet text for the Twitter button
		if (strlen($this->params->get('twitterText', '')) >= 3)
		{
			$tweetText = $this->params->get('twitterText', '');
		}
		else
		{
			$tweetText = $article->title;
		}

		ob_start();
		$template = JPluginHelper::getLayoutPath('content', 'yetanothersocial', $position);
		include $template;
		$output = ob_get_clean();

		// Final output
		$article->text = $output;

		return;
	}

	/**
	 * Function to set the language for the Facebook Like button
	 *
	 * @param   string  $artLang  The language of the article
	 * @param   string  $locale   The site locale
	 *
	 * @return  string  The language to use for Facebook's Like button
	 *
	 * @since   1.1
	 */
	private function getFacebookLanguage($artLang, $locale)
	{
		if ($artLang != '*')
		{
			// Using article language
			return str_replace('-', '_', $artLang);
		}

		// Using site language
		return $locale['2'];
	}

	/**
	 * Function to set the language for the Google +1 button
	 *
	 * @param   string  $artLang   The language of the article
	 * @param   string  $langCode  The site language code
	 *
	 * @return  string  The language to use for Google's +1 button
	 *
	 * @since   1.1
	 */
	private function getGoogleLanguage($artLang, $langCode)
	{
		$GlanguageShort = [
			'af', 'am', 'ar', 'eu', 'bn', 'bg', 'ca', 'hr', 'cs', 'da', 'nl', 'et', 'fil', 'fi', 'fr', 'gl', 'de', 'el', 'gu', 'iw', 'hi', 'hu', 'is',
			'id', 'it', 'ja', 'kn', 'ko', 'lv', 'lt', 'ms', 'ml', 'mr', 'no', 'fa', 'pl', 'ro', 'ru', 'sr', 'sk', 'sl', 'es', 'sw', 'sv', 'ta', 'te',
			'th', 'tr', 'uk', 'ur', 'vi', 'zu'
		];
		$GlanguageLong	= ['zh-HK', 'zh-CN', 'zh-TW', 'en-GB', 'en-US', 'fr-CA', 'pt-BR', 'pt-PT', 'es-419'];

		// Check if the article's language is *; use site language if so
		if ($artLang != '*')
		{
			// Check the short language code based on the article's language
			if (in_array(substr($artLang, 0, 2), $GlanguageShort))
			{
				return 'window.___gcfg = {lang: "' . substr($artLang, 0, 2) . '"};';
			}

			// Check the long language code based on the article's language
			if (in_array($artLang, $GlanguageLong))
			{
				return 'window.___gcfg = {lang: "' . $artLang . '"};';
			}

			// None of the above are matched, define no language
			return '';
		}

		// Check the short language code based on the site's language
		if (in_array(substr($langCode, 0, 2), $GlanguageShort))
		{
			return 'window.___gcfg = {lang: "' . substr($langCode, 0, 2) . '"};';
		}

		// Check the long language code based on the site's language
		if (in_array($langCode, $GlanguageLong))
		{
			return 'window.___gcfg = {lang: "' . $langCode . '"};';
		}

		// None of the above are matched, define no language
		return '';
	}

	/**
	 * Function to set the language for the Twitter Tweet button
	 *
	 * @param   string  $artLang  The language of the article
	 * @param   string  $locale   The site locale
	 *
	 * @return  string  The language to use for Twitter's Tweet button
	 *
	 * @since   1.1
	 */
	private function getTwitterLanguage($artLang, $locale)
	{
		// Authorized languages
		$tweetShort = ['pt', 'id', 'it', 'es', 'tr', 'en', 'ko', 'fr', 'nl', 'ru', 'de', 'ja', 'hi', 'pl', 'no', 'da', 'fi', 'sv', 'fil', 'msa'];
		$tweetFull = ['zh-cn', 'zh-tw'];

		// Check if the article's language is *; use site language if so
		if ($artLang != '*')
		{
			// Check the short language code based on the article's language
			if (in_array(substr($artLang, 0, 2), $tweetShort))
			{
				return substr($artLang, 0, 2);
			}

			// Check the long language code based on the article's language
			if (in_array(substr($artLang, 0, 3), $tweetShort))
			{
				return substr($artLang, 0, 3);
			}

			// Check the full language code based on the article's language
			if (in_array($artLang, $tweetFull))
			{
				return $artLang;
			}

			// Not in array, default to English
			return 'en';
		}

		// Check the language code based on the site's locale
		if (in_array(substr($locale['2'], 0, 2), $tweetShort))
		{
			return substr($locale['2'], 0, 2);
		}

		// Check the full language code based on the site's locale
		if (in_array(substr($locale['2'], 0, 2), substr($tweetFull, 0, 2)))
		{
			return substr($locale['2'], 0, 2);
		}

		// Not in array, default to English
		return 'en';
	}

	/**
	 * Function to retrieve the full article object
	 *
	 * @param   object  $article  The content object
	 *
	 * @return  object  The full content object
	 *
	 * @since   1.0
	 */
	private function loadArticle($article)
	{
		// Query the database for the article text
		$query = $this->db->getQuery(true)
			->select('*')
			->from($this->db->quoteName('#__content'))
			->where($this->db->quoteName('introtext') . ' = ' . $this->db->quote($article->text));
		$this->db->setQuery($query);

		return $this->db->loadObject();
	}

	/**
	 * Function to retrieve the article language
	 *
	 * @param   string  $id  Article ID
	 *
	 * @return  string  Article language
	 *
	 * @since   2.0
	 */
	private function loadArticleLanguage($id)
	{
		// Query the database for the article text
		$query = $this->db->getQuery(true)
			->select('language')
			->from($this->db->quoteName('#__content'))
			->where($this->db->quoteName('id') . ' = ' . (int) $id);
		$this->db->setQuery($query);

		return $this->db->loadResult();
	}
}
