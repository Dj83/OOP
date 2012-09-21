<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
$controller = JController::getInstance('Bulletins');
$controller->execute(JRequest::getVar('task', 'display'));
$controller->redirect();