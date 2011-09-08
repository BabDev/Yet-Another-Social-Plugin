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
?>

<div class="yetanothersocial-container">
<?php // Facebook Like button
if ($displayFacebook): ?>
	<div class="yetanothersocial-facebook" style="float:<?php echo $this->params->get('horAlign', 'left'); ?>;">
		<fb:like href="<?php echo $siteURL.$itemURL; ?>"
			send="<?php echo $this->params->get('facebookSend', 'true'); ?>"
			layout="<?php echo $this->params->get('facebookLayout', 'button_count'); ?>"
			show_faces="<?php echo $this->params->get('facebookFaces', 'true'); ?>"
			action="<?php echo $this->params->get('facebookAction', 'like'); ?>"
			font="<?php echo $this->params->get('facebookFont', 'arial'); ?>"
			colorscheme="<?php echo $this->params->get('facebookColor', 'light'); ?>" />
	</div>
<?php endif; ?>
<?php // Google +1 button
if ($displayGoogle): ?>
	<div class="yetanothersocial-google" style="float:<?php echo $this->params->get('horAlign', 'left'); ?>;">
		<g:plusone size="<?php echo $this->params->get('googleSize', 'standard'); ?>"
			count="<?php echo $this->params->get('googleCount', 'true'); ?>"
			href="<?php echo $siteURL.$itemURL; ?>"></g:plusone>
	</div>
<?php endif; ?>
<?php // Linkedin Share button
if ($displayLinkedin): ?>
	<div class="yetanothersocial-linkedin" style="float:<?php echo $this->params->get('horAlign', 'left'); ?>;">
		<script type="IN/Share"
			data-url="<?php echo $siteURL.$itemURL; ?>"
			data-counter="<?php echo $this->params->get('linkedinCount', 'right'); ?>"></script>
	</div>
<?php endif; ?>
<?php // Twitter Share button
if ($displayTwitter): ?>
	<div class="yetanothersocial-twitter" style="float:<?php echo $this->params->get('horAlign', 'left'); ?>;">
		<a href="http://twitter.com/share" class="twitter-share-button"
			data-url="<?php echo $siteURL.$itemURL; ?>"
			data-counturl="<?php echo $siteURL.$itemURL; ?>"
			data-count="<?php echo $this->params->get('twitterCount', 'horizontal'); ?>"
			data-via="<?php echo $this->params->get('twitterUser', ''); ?>"
			data-related="<?php echo $this->params->get('twitterRelated', ''); ?>"
			data-text="<?php echo $this->params->get('twitterText', ''); ?>">Tweet</a>
	</div>
<?php endif; ?>
</div>
<div class="clear">&nbsp;</div>

<?php echo $article->text; ?>
