<?php


// get item id
$itemid = intval($params->get('set_itemid', 0));

?>

<form id="searchbox" action="<?php echo JRoute::_('index.php'); ?>" method="post" role="search">
	<input type="text" value="" name="searchword" placeholder="<?php echo JText::_('Search...'); ?>" />
	<button type="reset" value="Reset"></button>
	<input type="hidden" name="task"   value="search" />
	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="Itemid" value="<?php echo $itemid > 0 ? $itemid : JRequest::getInt('Itemid'); ?>" />	
</form>