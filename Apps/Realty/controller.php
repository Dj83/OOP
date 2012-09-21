<?php
/**
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Component Controller
 *
 * @package		Realtor.Administrator
 * @subpackage	com_realtor
 */
class RealtorController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	2.5
	 */
	protected $default_view = 'leads';

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	2.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/realtor.php';

		// Load the submenu.
		RealtorHelper::addSubmenu(JRequest::getCmd('view', 'leads'));

		$view	= JRequest::getCmd('view', 'leads');
		$layout = JRequest::getCmd('layout', 'default');
		$id		= JRequest::getInt('id');

		// Check for edit form.
		if ($view == 'lead' && $layout == 'edit' && !$this->checkEditId('com_realtor.edit.lead', $id)) {

			// we don't allow direct linking to the form from the admin
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_realtor&view=leads', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
