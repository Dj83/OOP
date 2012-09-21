<?php
defined('_JEXEC') or die;

class BulletinsHelper extends JController
{
	static public function addSubmenu($vName)
	{
		JSubmenuHelper::addEntry(
			JText::_('COM_BULLETINS_SUBMENU_ADD'),
			'index.php?option=com_bulletins&view=bulletin&layout=edit',
			$vName == 'bulletin'
		);

		JSubmenuHelper::addEntry(
			JText::_('COM_BULLETINS_SUBMENU_READ'),
			'index.php?option=com_bulletins',
			$vName == 'bulletins'
		);

		JSubmenuHelper::addEntry(
			JText::_('COM_BULLETINS_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_bulletins',
			$vName == 'categories'
		);
	}

	static public function getActions()
	{
		$user = JFactory::getUser();

		$result = new JObject;
		$actions = JAccess::getActions('com_bulletins');
		foreach($actions as $action){
			$result->set($action->name, $user->authorise($action->name, 'com_bulletins'));
		}

		return $result;
	}

	static public function getStateOptions()
	{
		$options = array();
		$options[] = JHtml::_('select.option', 1, JText::_('COM_BULLETINS_OPTIONS_READ'));
		$options[] = JHtml::_('select.option', 0, JText::_('COM_BULLETINS_OPTIONS_UNREAD'));
		$options[] = JHtml::_('select.option',-2, JText::_('COM_BULLETINS_OPTIONS_TRASHED'));
		return $options;
	}

	static public function getPriorityOptions()
	{
		$options = array();
		$options[] = JHtml::_('select.option', 1, JText::_('COM_BULLETINS_OPTIONS_FEATURED'));
		$options[] = JHtml::_('select.option', 0, JText::_('COM_BULLETINS_OPTIONS_IMPORTANT'));
		$options[] = JHtml::_('select.option', 2, JText::_('COM_BULLETINS_OPTIONS_FAVORITE'));
		return $options;
	}
}