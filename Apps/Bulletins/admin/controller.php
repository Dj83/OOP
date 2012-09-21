<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.controller');
class BulletinsController extends JController
{
	function display($cachable = false, $urlparams = false)
	{
		// Build some menus
		require_once JPATH_COMPONENT.'/helpers/bulletins.php';

		$app = JFactory::getApplication();

		// For legacy support we'll use JRequest instead of JInput
		//$jinput	= $app->input;
		//$layout	= $jinput->get('layout','default');
		//$view		= $jinput->get('view','bulletins');
		//$id			= $jinput->get('id');

		$layout = JRequest::getVar('layout','default');
		$view = JRequest::getCmd('view','bulletins');
		$id = JRequest::getInt('id');

		// Check for the edit form, layout is always edit!
		if($view == 'bulletin' && $layout == 'edit' && !$this->checkEditId('com_bulletins.edit.bulletin', $id))
		{
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_bulletins&view=bulletins'));
			return false;
		}

		parent::display();

		BulletinsHelper::addSubmenu($view);
	}
}