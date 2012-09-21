<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
class JHtmlBulletins extends JController
{
	public static function messagestate($value = 0, $i, $canChange)
	{
		// Array of image, task, title, action.
		$states	= array(
			//-2	=> array('trash.gif',		'messages.trash',	'JTRASHED','COM_MESSAGES_MARK_AS_TRASHED'),
			1	=> array('read.gif',			'messages.unpublish',	'COM_MESSAGES_OPTION_READ','COM_MESSAGES_MARK_AS_UNREAD'),
			0	=> array('unread.gif',		'messages.publish',	'COM_MESSAGES_OPTION_UNREAD','COM_MESSAGES_MARK_AS_READ')
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= JHtml::_('image', 'com_bulletins/messages/'.$state[0], JText::_($state[2]), NULL, true);
		if ($canChange) {
			$html = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					.$html.'</a>';
		}

		return $html;
	}

	public static function priority($value = 0, $i, $canChange)
	{
		// Array of image, task, title, action.
		$states	= array(
			0	=> array('blank.png','&nbsp;'),
			1	=> array('lowimportance.gif',	'COM_BULLETINS_OPTION_LOWPRIORITY'),
			2	=> array('highimportance.gif',	'COM_BULLETINS_OPTION_HIGHPRIORITY')
		);
		$state	= JArrayHelper::getValue($states, (int) $value);
		$html	= JHtml::_('image', 'com_bulletins/messages/'.$state[0], JText::_($state[1]), NULL, true);
		if ($canChange) {
			$html = '<a href="javascript:void(0); return false;" onclick="return false;" title="'.JText::_($state[1]).'">'
					.$html.'</a>';
		}

		return $html;
	}

	public static function messagetrash($i, $canChange)
	{
		$html	= JHtml::_('image', 'com_bulletins/trash.png', JText::_('JTRASHED'), NULL, true);
		if ($canChange) {
			$html = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\'messages.trash\')" title="'.JText::_('COM_MESSAGES_MARK_AS_TRASHED').'">'
					.$html.'</a>';
		}

		return $html;
	}
	public static function attachment($id,$canGrabAttachments)
	{
		$html	= JHtml::_('image', 'com_bulletins/messages/attachment.gif', JText::_('Attachments'), NULL, true);

		if ($canGrabAttachments) {
			$href = JRoute::_('index.php?option=com_bulletins&view=message&layout=attachments&id='.$id.'&tmpl=component');
			$height = '260';
			$width = '340';
			$html = '<a href="#" onclick="return Joomla.popupWindow(\''.$href.'\', \'_blank\',\''.$height.'\',\''.$width.'\', \'no\')" title="'.JText::_('COM_MESSAGES_VIEW_ATTACHMENTS').'">'
					.$html.'</a>';
		}

		return $html;
	}
	public static function statebox($value = 0)
	{
		// Array of image, task, title, action.
		$priorities	= array(
			0	=> array('box-note'),
			1	=> array('')
		);
		$priority	= JArrayHelper::getValue($priorities, (int) $value, $priorities[0]);
		$html	= $priority[0];

		return $html;
	}
	public static function fromthumb($userfrom_id,$userfrom_name='')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.thumb AS thumb');
		$query->from('#__community_users AS a');
		$query->where('a.userid = '.(int) $userfrom_id);
		$db->setQuery($query);
		$user = $db->loadObject();
		if($db->getErrorNum() || !isset($user->thumb)){
			$user->thumb = '';
		}
		$attribs = '';
		$attribs = ' style="border: 3px solid #FFFFFF; display: inline; float: left; height: 40px;margin: 5px 0 0 5px; outline: 1px solid #CCCCCC; width: 40px;"';
		return JHtml::_('image', $user->thumb, $userfrom_name, $attribs, false);
	}


}