<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JController::getInstance('Forms');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();