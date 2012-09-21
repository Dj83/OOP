<?php
defined('_JEXEC') or die;

abstract class BulletinsHelper
{
	static function getStateOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option',	'1',	JText::_('COM_BULLETINS_OPTIONS_READ'));
		$options[]	= JHtml::_('select.option',	'0',	JText::_('COM_BULLETINS_OPTIONS_UNREAD'));
		$options[]	= JHtml::_('select.option',	'-2',	JText::_('JTRASHED'));
		return $options;
	}

	static function getPriorityOptions()
	{
		// Build the filter options.
		$options	= array();
		$options[]	= JHtml::_('select.option',	'1',	JText::_('COM_BULLETINS_PRIORITY_LOW'));
		$options[]	= JHtml::_('select.option',	'2',	JText::_('COM_BULLETINS_PRIORITY_HIGH'));
		$options[]	= JHtml::_('select.option',	'0',	JText::_('COM_BULLETINS_PRIORITY_NORMAL'));
		return $options;
	}

	static function getPriorityToggler($priority)
	{
		
		$text = '';
		switch((int)$priority){
			case 1:
			$text = '<span style="color:#729BD9;">'.JText::_('COM_BULLETINS_PRIORITY_LOW').'</span>';
			break;
			case 2:
			$text = '<span style="color:#CC3300;">'.JText::_('COM_BULLETINS_PRIORITY_HIGH').'</span>';
			break;
			case 0:
			default :
			$text = '<span>'.JText::_('COM_BULLETINS_PRIORITY_NORMAL').'</span>';
			break;
		}
		return $text;
	}
}