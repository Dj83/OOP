<?php
defined('_JEXEC') or die('Restricted Access');

class ToDoHelperRoute
{
	public static function &getItems($items)
	{
		static $items;

		// Get the menu items for this component.
		if (!isset($items)) {
			// Include the site app in case we are loading this from the admin.
			require_once JPATH_SITE.'/includes/application.php';

			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();
			$comp	= JComponentHelper::getComponent('com_todo');
			$items	= $menu->getItems('component_id', $comp->id);

			// If no items found, set to empty array.
			if (!$items) {
				$items = array();
			}
		}

		return $items;
	}
	function getToDoRoute()
	{
		$items	= self::getItems();
		$itemid	= null;

		// Search for a suitable menu id.
		foreach ($items as $item) {
			if (isset($item->query['view']) && $item->query['view'] === 'todo') {
				$itemid = $item->id;
				break;
			}
		}

		return $itemid;
	}
}