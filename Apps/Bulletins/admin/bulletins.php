<?php
defined('_JEXEC') or die;

if(!JFactory::getUser()->authorise('core.manage','com_bulletins'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

jimport('joomla.application.component.controller');
$controller	= JController::getInstance('Bulletins');

// For legacy support we're using JRequest as apposed to JInput
//$jinput	= JFactory::getApplication()->input;
//$controller->execute(jinput->get('task'));

$controller->execute(JRequest::getVar('task'));
$controller->redirect();