<?php

defined('_JEXEC') or die;

class FormsController extends JController
{
	function __construct($config = array())
	{
		$jinput = JFactory::getApplication()->input;
		// Form frontpage Editor form proxying:
		if($jinput->get('view') === 'forms' && JRequest::getCmd('layout') === 'modal') {
			JHtml::_('stylesheet', 'system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
		parent::__construct($config);
	}
	public function display($cachable = false, $urlparams = false)
	{
		$jinput = JFactory::getApplication()->input;
		$cachable = true;

		JHtml::_('behavior.caption');

		// Set the default view name and format from the Request.
		// Note we are using f_id to avoid collisions with the router and the return page.
		// Frontend is a bit messier than the backend.
		$id		= JRequest::getInt('f_id');
		$vName	= $jinput->get('view', 'categories');
		JRequest::setVar('view', $vName);

		$user = JFactory::getUser();

		if ($user->get('id') ||
			($_SERVER['REQUEST_METHOD'] == 'POST' &&
				(($vName == 'category' && $jinput->get('layout') != 'list') || $vName == 'archive' ))) {
			$cachable = false;
		}

		$safeurlparams = array(
			'catid'=>'INT',
			'id'=>'INT',
			'limit'=>'UINT',
			'limitstart'=>'UINT',
			'showall'=>'INT',
			'return'=>'BASE64',
			'filter'=>'STRING',
			'filter_order'=>'CMD',
			'filter_order_Dir'=>'CMD',
			'filter-search'=>'STRING',
			'print'=>'BOOLEAN',
			'lang'=>'CMD'
		);

		// Check for edit form.
		if ($vName == 'form' && !$this->checkEditId('com_forms.edit.form', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			return JError::raiseError(403, JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
		}

		parent::display($cachable, $safeurlparams);

		return $this;
	}
}