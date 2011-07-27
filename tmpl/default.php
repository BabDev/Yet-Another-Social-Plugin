<?php
/**
* Yet Another Social Plugin
*
* @copyright	Copyright (C) 2011 Michael Babker. All rights reserved.
* @license		GNU/GPL - http://www.gnu.org/copyleft/gpl.html
*
*/

// Restricted access
defined('_JEXEC') or die;
?>

<div class="yetanothersocial-container">
<?php // Facebook Like button
// @TODO: Param for count and size
if ($displayFacebook): ?>
	<div class="yetanothersocial-facebook active-<?php echo $count; ?>">
		<fb:like href="<?php echo $siteURL.$itemURL; ?>" send="false" layout="button_count" show_faces="false" font="lucida grande" />
	</div>
<?php endif; ?>
<?php // Google +1 button
// @TODO: Param for count and size
if ($displayGoogle): ?>
	<div class="yetanothersocial-google active-<?php echo $count; ?>">
		<g:plusone size="standard" count="true" href="<?php echo $siteURL.$itemURL; ?>"></g:plusone>
	</div>
<?php endif; ?>
<?php // Twitter Share button
// @TODO: Param for Twitter account, text, count
if ($displayTwitter): ?>
	<div class="yetanothersocial-twitter active-<?php echo $count; ?>">
		<a href="http://twitter.com/share" class="twitter-share-button"
		data-url="<?php echo $siteURL.$itemURL; ?>"
		data-count="horizontal"
		data-via="mbabker"
		data-text="Check this out -">Tweet</a>
	</div>
<?php endif; ?>
</div>
<div class="clear">&nbsp;</div>

<?php echo $article->text; ?>
