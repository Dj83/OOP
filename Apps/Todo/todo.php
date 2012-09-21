<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
require_once JPATH_COMPONENT.'/helpers/route.php';

$controller = JController::getInstance('ToDo');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();