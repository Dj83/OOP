<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
class BulletinsController extends JController
{
	public function display($cachable=false, $urlparams=false)
	{
		$cachable = true;

		JHtml::_('behavior.caption');

		$id		= JRequest::getInt('b_id');
		$vName	= JRequest::getCmd('view', 'bulletins');
		JRequest::setVar('view', $vName);

		$user = JFactory::getUser();

		if ($user->get('id') ||
			($_SERVER['REQUEST_METHOD'] == 'POST' &&
				(($vName == 'bulletins' && JRequest::getCmd('layout') != 'list') || $vName == 'archive' ))) {
			$cachable = false;
		}

		$safeurlparams = array('catid'=>'INT', 'id'=>'INT','limit'=>'UINT', 'limitstart'=>'UINT',
			'showall'=>'INT', 'return'=>'BASE64', 'filter'=>'STRING', 'filter_order'=>'CMD', 
			'filter_order_Dir'=>'CMD', 'filter-search'=>'STRING', 'lang'=>'CMD');

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_bulletins.edit.bulletin', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}