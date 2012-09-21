<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
class JHtmlBulletins extends JController
{
	public static function messagestate($value = 0, $i, $canChange)
	{
		// Array of image, task, title, action.
		$states	= array(
			-2	=> array('trash.png',		'messages.unpublish',	'JTRASHED','COM_MESSAGES_MARK_AS_UNREAD'),
			1	=> array('tick.png',			'messages.unpublish',	'COM_MESSAGES_OPTION_READ','COM_MESSAGES_MARK_AS_UNREAD'),
			0	=> array('publish_x.png','messages.publish',	'COM_MESSAGES_OPTION_UNREAD','COM_MESSAGES_MARK_AS_READ')
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= JHtml::_('image', 'com_bulletins/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					.$html.'</a>';
		}

		return $html;
	}

}