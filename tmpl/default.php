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
if ($displayFacebook): ?>
	<div class="yetanothersocial-facebook active-<?php echo $count; ?>">
	</div>
<?php endif; ?>
<?php // Google +1 button
if ($displayGoogle): ?>
	<div class="yetanothersocial-google active-<?php echo $count; ?>">
	</div>
<?php endif; ?>
<?php // Twitter Share button
if ($displayTwitter): ?>
	<div class="yetanothersocial-twitter active-<?php echo $count; ?>">
	</div>
<?php endif; ?>
</div>

<?php // Check if we're in the full article view and render the correct element
if ($context == 'com_content.article') {
	echo $article->text;
} else {
	echo $article->introtext;
}?>
