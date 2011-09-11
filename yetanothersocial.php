<?php
/**
* Yet Another Social Plugin
*
* @package    YetAnotherSocialPlugin
*
* @copyright  Copyright (C) 2011 Michael Babker. All rights reserved.
* @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
require_once JPATH_SITE.'/components/com_content/helpers/route.php';

/**
 * Yet Another Social Plugin Content Plugin
 *
 * @package  YetAnotherSocialPlugin
 * @since    1.0
 */
class plgContentYetAnotherSocial extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  &$subject  The object to observe
	 * @param   array   $config    An array that holds the plugin configuration
	 *
	 * @return	plgContentYetAnotherSocial
	 *
	 * @since	1.0
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

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
		$document			= JFactory::getDocument();
		$displayFacebook	= $this->params->get('displayFacebook', '1');
		$displayGoogle		= $this->params->get('displayGoogle', '1');
		$displayTwitter		= $this->params->get('displayTwitter', '1');
		$displayLinkedin	= $this->params->get('displayLinkedin', '1');
		$displayBuzz		= $this->params->get('displayBuzz', '1');
		$selectedCategories	= $this->params->def('displayCategories', '');
		$position			= $this->params->def('displayPosition', 'top');
		$view				= JRequest::getCmd('view');

		// Check if the plugin is enabled
		if (JPluginHelper::isEnabled('content', 'yetanothersocial') == false)
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
			// We only want to handle com_content items; if this function returns null, there's no DB item
			// Also, make sure the object isn't already loaded and undo previous plugin processing
			if ((!is_null($this->_loadArticle($article))) && (!isset($article->catid)))
			{
				$article = $this->_loadArticle($article);
			}
		}

		// Make sure we have a category ID, otherwise, end processing
		$properties = get_object_vars($article);
		if (!(array_key_exists('catid', $properties)))
		{
			return;
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
		else if ($selectedCategories == '')
		{
			$categories[] = $currentCategory;
		}
		else
		{
			$categories[] = $selectedCategories;
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
		$siteURL	= substr(JURI::root(), 0, -1);
		$itemURL	= JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid));

		// Declare the stylesheet
		$css = $this->_getCssPath('default.css');
		JHtml::stylesheet($css, false, false, false);

		// Get the article's language
		$artLang	= $article->language;

		// Get the site language
		$lang		= JFactory::getLanguage();
		$locale		= $lang->getLocale();
		$langCode	= $lang->getTag();

		// Facebook Language
		$FBlanguage = $this->_getFBLanguage($artLang, $locale);

		// Google+ Language
		$Glang = $this->_getGoogleLanguage($artLang, $langCode);

		// Google Buzz Language
		$buzzLang = $this->_getBuzzLanguage($artLang, $langCode);

		// Twitter Language
		$twitterLang = $this->_getTwitterLanguage($artLang, $locale);

		// Check the scripts aren't already loaded and load if needed
		if ($displayFacebook && !in_array('<script src="http://connect.facebook.net/'.$FBlanguage.'/all.js#xfbml=1"></script>', $document->_custom))
		{
			$document->addCustomTag('<script src="http://connect.facebook.net/'.$FBlanguage.'/all.js#xfbml=1"></script>');
		}
		if ($displayGoogle && !in_array('<script type="text/javascript" src="https://apis.google.com/js/plusone.js">'.$Glang.'</script>', $document->_custom))
		{
			$document->addCustomTag('<script type="text/javascript" src="https://apis.google.com/js/plusone.js">'.$Glang.'</script>');
		}
		if ($displayBuzz && !in_array('<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>', $document->_custom))
		{
			$document->addCustomTag('<script type="text/javascript" src="http://www.google.com/buzz/api/button.js"></script>');
		}
		if ($displayTwitter && !in_array('<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>', $document->_custom))
		{
			$document->addCustomTag('<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>');
		}
		if ($displayLinkedin && !in_array('<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>', $document->_custom))
		{
			$document->addCustomTag('<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>');
		}

		// Get the content and merge in the template
		// First, see if $article->text is defined
		if (!isset($article->text))
		{
			$article->text = $article->introtext;
		}
		ob_start();
		$template = $this->_getTemplatePath($position.'.php');
		include $template;
		$output = ob_get_contents();
		ob_end_clean();

		// Final output
		$article->text = $output;
		return;
	}

	/**
	 * Function to set the language for the Google Buzz button
	 *
	 * @param   string  $artLang   The language of the article
	 * @param   string  $langCode  The site language code
	 *
	 * @return  string  The language to use for Google's Buzz button
	 *
	 * @since   1.1
	 */
	private function _getBuzzLanguage($artLang, $langCode)
	{
		$GlanguageShort = array(
						'ar', 'bg', 'ca', 'hr', 'cs', 'da', 'nl', 'et', 'fil', 'fi',
						'fr', 'de', 'el', 'iw', 'hi', 'hu', 'id', 'it', 'ja', 'ko',
						'lv', 'lt', 'ms', 'no', 'fa', 'pl', 'ro', 'ru', 'sr', 'sk',
						'sl', 'es', 'sv', 'th', 'tr', 'uk', 'vi');
		$GlanguageLong	= array('zh-CN', 'zh-TW', 'en-GB', 'en-US', 'pt-BR', 'pt-PT', 'es-419');

		// Check if the article's language is *; use site language if so
		if ($artLang != '*')
		{
			// Using article language
			if (in_array(substr($artLang, 0, 2), $GlanguageShort))
			{
				$buzzLang	= 'data-locale="'.substr($artLang, 0, 2).'"';
			}
			else if (in_array($artLang, $GlanguageLong))
			{
				$buzzLang	= 'data-locale="'.$artLang.'"';
			}
			// None of the above are matched, set no language
			// The Buzz API sets language based on browser config if one isn't set
			else
			{
				$buzzLang	= '';
			}
		}
		else
		{
			// Using site language
			if (in_array(substr($langCode, 0, 2), $GlanguageShort))
			{
				$buzzLang	= 'data-locale="'.substr($langCode, 0, 2).'"';
			}
			else if (in_array($langCode, $GlanguageLong))
			{
				$buzzLang	= 'data-locale="'.$langCode.'"';
			}
			// None of the above are matched, set no language
			// The Buzz API sets language based on browser config if one isn't set
			else
			{
				$buzzLang	= '';
			}
		}
		return $buzzLang;
	}

	/**
	 * Function to determine the CSS file path
	 *
	 * @param   string  $file  The file name of the CSS file
	 *
	 * @return  string  The path to the CSS file
	 *
	 * @since   1.0
	 */
	private function _getCssPath($file)
	{
		$app	= JFactory::getApplication();
		if (file_exists(JPATH_SITE.'/templates/'.$app->getTemplate().'/html/yetanothersocial/'.$file))
		{
			$path = 'templates/'.$app->getTemplate().'/html/yetanothersocial/'.$file;
		}
		else
		{
			$path = 'plugins/content/yetanothersocial/media/css/'.$file;
		}
		return $path;
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
	private function _getFBLanguage($artLang, $locale)
	{
		if ($artLang != '*')
		{
			// Using article language
			$FBlanguage = substr($artLang, 0, 2);
		}
		else
		{
			// Using site language
			$FBlanguage	= $locale['2'];
		}
		return $FBlanguage;
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
	private function _getGoogleLanguage($artLang, $langCode)
	{
		$GlanguageShort = array(
						'ar', 'bg', 'ca', 'hr', 'cs', 'da', 'nl', 'et', 'fil', 'fi',
						'fr', 'de', 'el', 'iw', 'hi', 'hu', 'id', 'it', 'ja', 'ko',
						'lv', 'lt', 'ms', 'no', 'fa', 'pl', 'ro', 'ru', 'sr', 'sk',
						'sl', 'es', 'sv', 'th', 'tr', 'uk', 'vi');
		$GlanguageLong	= array('zh-CN', 'zh-TW', 'en-GB', 'en-US', 'pt-BR', 'pt-PT', 'es-419');

		// Check if the article's language is *; use site language if so
		if ($artLang != '*')
		{
			// Using article language
			if (in_array(substr($artLang, 0, 2), $GlanguageShort))
			{
				$Glang	= 'window.___gcfg = {lang: "'.substr($artLang, 0, 2).'"};';
			}
			else if (in_array($artLang, $GlanguageLong))
			{
				$Glang	= 'window.___gcfg = {lang: "'.$artLang.'"};';
			}
			// None of the above are matched, define no language
			else
			{
				$Glang	= '';
			}
		}
		else
		{
			// Using site language
			if (in_array(substr($langCode, 0, 2), $GlanguageShort))
			{
				$Glang	= 'window.___gcfg = {lang: "'.substr($langCode, 0, 2).'"};';
			}
			else if (in_array($langCode, $GlanguageLong))
			{
				$Glang	= 'window.___gcfg = {lang: "'.$langCode.'"};';
			}
			// None of the above are matched, define no language
			else
			{
				$Glang	= '';
			}
		}
		return $Glang;
	}

	/**
	 * Function to determine the template file path
	 *
	 * @param   string  $file  The file name of the template
	 *
	 * @return  string  The paths to the template
	 *
	 * @since   1.0
	 */
	private function _getTemplatePath($file)
	{
		$app	= JFactory::getApplication();
		if (file_exists(JPATH_SITE.'/templates/'.$app->getTemplate().'/html/yetanothersocial/'.$file))
		{
			$path = JPATH_SITE.'/templates/'.$app->getTemplate().'/html/yetanothersocial/'.$file;
		}
		else
		{
			$path = JPATH_SITE.'/plugins/content/yetanothersocial/tmpl/'.$file;
		}
		return $path;
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
	private function _getTwitterLanguage($artLang, $locale)
	{
		// Authorized languages
		$tweetShort = array('pt', 'id', 'it', 'es', 'tr', 'en', 'ko', 'fr', 'nl', 'ru', 'de', 'ja');

		// Check if the article's language is *; use site language if so
		if ($artLang != '*')
		{
			// Using article language
			if (in_array(substr($artLang, 0, 2), $tweetShort))
			{
				$twitterLang	= substr($artLang, 0, 2);
			}
			// Not in array, default to English
			else
			{
				$twitterLang	= 'en';
			}
		}
		else
		{
			// Using site language
			if (in_array($locale['2'], $tweetShort))
			{
				$twitterLang	= $locale['2'];
			}
			// Not in array, default to English
			else
			{
				$twitterLang	= 'en';
			}
		}
		return $twitterLang;
	}

	/**
	 * Function to retreive the full article object
	 *
	 * @param   object  $article  The content object
	 *
	 * @return  object  The full content object
	 *
	 * @since	1.0
	 */
	private function _loadArticle($article)
	{
		// Query the database for the article text
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__content'));
		$query->where($db->quoteName('introtext').' = '.$db->quote($article->text));
		$db->setQuery($query);
		$article = $db->loadObject();

		return $article;
	}
}
