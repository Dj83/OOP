<form id="searchbox" action="<?php echo JRoute::_($route); ?>" method="get" role="search">
	<input type="text" value="" name="q" placeholder="<?php echo JText::_('TPL_METRO_SEARCH'); ?>" autocomplete="off" />
	<button type="reset" value="Reset"></button>
	<?php echo modFinderHelper::getGetFields($route); ?>
</form>