<?php

// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_subscriptions')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Register helper class
JLoader::register('SubscriptionsHelper', dirname(__FILE__) . '/helpers/subscriptions.php');

// Include dependencies
jimport('joomla.application.component.controller');

$controller = JController::getInstance('Subscriptions');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
