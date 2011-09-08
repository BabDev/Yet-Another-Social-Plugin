<?php
/**
* Yet Another Social Plugin
*
* @copyright  Copyright (C) 2011 Michael Babker. All rights reserved.
* @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
* @author     Michael Babker (Owner)
* @author     Olaf Rietzschel (Contributor)
*/

// Restricted access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');
require_once(JPATH_SITE.'/components/com_content/helpers/route.php');

class plgContentYetAnotherSocial extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An array that holds the plugin configuration
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
	public function onContentPrepare($context, &$article, &$params, $limitstart)
	{
		// Set the parameters
		$document			= JFactory::getDocument();
		$displayFacebook	= $this->params->get('displayFacebook', '1');
		$displayGoogle		= $this->params->get('displayGoogle', '1');
		$displayTwitter		= $this->params->get('displayTwitter', '1');
		$displayLinkedin	= $this->params->get('displayLinkedin', '1');
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
			if ((!is_null($this->loadArticle($article))) && (!isset($article->catid)))
			{
				$article = $this->loadArticle($article);
			}
		}

		// Make sure we have a category ID, otherwise, end processing
		$properties = get_object_vars($article);
		if (!(array_key_exists ('catid', $properties)))
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
		$css = $this->getCssPath('default.css');
		JHtml::stylesheet($css, false, false, false);

		// Get the site language
		$lang		= JFactory::getLanguage();
		$langCode	= substr($lang->getTag(), 0, 2);

		// @TODO: Add arrays for all legal languages for each plugin

		// Declare Google & Facebook Language text
		$languageSet = $this->params->get('languageDecl', 'en');

		// Check the scripts aren't already loaded and load if needed
		// @TODO: Handle multi-language situations as able
		// #TODO: Check and set the language dynamically
		if ($displayFacebook && $languageSet == 'en' && !in_array('<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>', $document->_custom))
		{
			$document->addCustomTag('<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>');
		}
		else if ($displayFacebook && $languageSet == 'nl' && !in_array('<script src="http://connect.facebook.net/nl_NL/all.js#xfbml=1"></script>', $document->_custom))
		{
			$document->addCustomTag('<script src="http://connect.facebook.net/nl_NL/all.js#xfbml=1"></script>');
		}
		else if ($displayFacebook && $languageSet == 'es' && !in_array('<script src="http://connect.facebook.net/es_ES/all.js#xfbml=1"></script>', $document->_custom))
		{
			$document->addCustomTag('<script src="http://connect.facebook.net/es_ES/all.js#xfbml=1"></script>');
		}
		else if ($displayFacebook && $languageSet == 'pt' && !in_array('<script src="http://connect.facebook.net/pt_BR/all.js#xfbml=1"></script>', $document->_custom)) {
			$document->addCustomTag('<script src="http://connect.facebook.net/pt_BR/all.js#xfbml=1"></script>');
		}
		else if ($displayFacebook && $languageSet == 'sv' && !in_array('<script src="http://connect.facebook.net/sv_SE/all.js#xfbml=1"></script>', $document->_custom)) {
			$document->addCustomTag('<script src="http://connect.facebook.net/sv_SE/all.js#xfbml=1"></script>');
		}
		if ($displayGoogle && !in_array('<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: "'.$languageSet.'"}</script>', $document->_custom))
		{
			$document->addCustomTag('<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: "'.$languageSet.'"}</script>');
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
		$template = $this->getTemplatePath($position.'.php');
		include($template);
		$output = ob_get_contents();
		ob_end_clean();

		// Final output
		$article->text = $output;
		return;
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
	private function getCssPath($file)
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
	 * Function to determine the template file path
	 *
	 * @param   string  $file  The file name of the template
	 *
	 * @return  string  The paths to the template
	 *
	 * @since   1.0
	 */
	private function getTemplatePath($file)
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
	 * Function to retreive the full article object
	 *
	 * @param   object  $article  The content object
	 *
	 * @return  object  The full content object
	 *
	 * @since	1.0
	 */
	private function loadArticle($article)
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
