<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

$controller = JController::getInstance('Checkout', array('base_path'=>dirname(__FILE__)));
$controller->execute(JRequest::getCmd('task'));
$controller->redirect(); 