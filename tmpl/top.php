<?php
/**
 * Yet Another Social Plugin
 *
 * @copyright  Copyright (C) 2011-2014 Michael Babker. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

defined('_JEXEC') or die;

/* @type  PlgContentYetAnotherSocial  $this */
?>

<div class="yetanothersocial-container">
<?php // Facebook Like button
if ($displayFacebook): ?>
	<?php if ($this->app->get('yasp.fbloaded', false) == false) : ?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {
				return;
			}
			js = d.createElement(s);
			js.id = id;
			js.src = "//connect.facebook.net/<?php echo $FBlanguage; ?>/all.js#xfbml=1&appId=<?php echo $this->params->get('facebookAppId'); ?>";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));
	</script>
	<?php $this->app->set('yasp.fbloaded', true); ?>
	<?php endif; ?>
	<div class="yetanothersocial-facebook pull-<?php echo $this->params->get('horAlign', 'left'); ?>">
		<fb:like href="<?php echo $siteURL . $itemURL; ?>"
			layout="<?php echo $this->params->get('facebookLayout', 'button_count'); ?>"
			show_faces="<?php echo $this->params->get('facebookFaces', 'true'); ?>"
			action="<?php echo $this->params->get('facebookAction', 'like'); ?>"></fb:like>
	</div>
<?php endif; ?>
<?php // Google +1 button
if ($displayGoogle): ?>
	<div class="yetanothersocial-google pull-<?php echo $this->params->get('horAlign', 'left'); ?>">
		<g:plusone size="<?php echo $this->params->get('googleSize', 'standard'); ?>"
			count="<?php echo $this->params->get('googleCount', 'true'); ?>"
			href="<?php echo $siteURL . $itemURL; ?>"></g:plusone>
	</div>
<?php endif; ?>
<?php // Linkedin Share button
if ($displayLinkedin): ?>
	<div class="yetanothersocial-linkedin pull-<?php echo $this->params->get('horAlign', 'left'); ?>">
		<script type="IN/Share"
			data-url="<?php echo $siteURL . $itemURL; ?>"
			data-counter="<?php echo $this->params->get('linkedinCount', 'right'); ?>"></script>
	</div>
<?php endif; ?>
<?php // Twitter Share button
if ($displayTwitter): ?>
	<div class="yetanothersocial-twitter pull-<?php echo $this->params->get('horAlign', 'left'); ?>">
		<a href="https://twitter.com/share" class="twitter-share-button"
			data-url="<?php echo $siteURL . $itemURL; ?>"
			data-counturl="<?php echo $siteURL . $itemURL; ?>"
			data-count="<?php echo $this->params->get('twitterCount', 'horizontal'); ?>"
			data-via="<?php echo $this->params->get('twitterUser', ''); ?>"
			data-lang="<?php echo $twitterLang?>"
			data-related="<?php echo $this->params->get('twitterRelated', ''); ?>"
			data-text="<?php echo $tweetText; ?>">Tweet</a>
	</div>
<?php endif; ?>
</div>
<div class="clear">&nbsp;</div>

<?php echo $article->text;
