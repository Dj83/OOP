<?php
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('user');

class JFormFieldUserBulletins extends JFormFieldUser
{
	public $type = 'UserBulletins';
	protected function getGroups()
	{
		// Compute usergroups
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__usergroups');
		$db->setQuery($query);
		$groups = $db->loadColumn();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseNotice(500, $db->getErrorMsg());
			return null;
		}

		foreach ($groups as $i=>$group)
		{
			if (JAccess::checkGroup($group, 'core.admin')) {
				continue;
			}
			if (!JAccess::checkGroup($group, 'core.manage', 'com_bulletins')) {
				unset($groups[$i]);
				continue;
			}
			if (!JAccess::checkGroup($group, 'core.login.admin')) {
				unset($groups[$i]);
				continue;
			}
		}
		return array_values($groups);
	}
	protected function getExcluded()
	{
		return array(JFactory::getUser()->id);
	}
}