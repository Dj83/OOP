<?php

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Subscriptions Controller
 *
 * @package		Sold.Administrator
 * @subpackage	com_subscriptions
 */
class SubscriptionsController extends JController
{
	/**
	 * @var		string	The default view.
	 * @since	2.5
	 */
	protected $default_view = 'subscriptions';

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 * @return	JController		This object to support chaining.
	 * @since	2.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		// Load the submenu.
		SubscriptionsHelper::addSubmenu(JRequest::getCmd('view', 'subscriptions'));

		$view		= JRequest::getCmd('view', 'subscriptions');
		$layout 	= JRequest::getCmd('layout', 'subscriptions');
		$id			= JRequest::getInt('id');

		// Check for edit form.
		if ($view == 'subscription' && $layout == 'edit' && !$this->checkEditId('com_subscriptions.edit.subscription', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_subscription&view=subscriptions', false));

			return false;
		}else if ($view == 'feature' && $layout == 'edit' && !$this->checkEditId('com_subscriptions.edit.feature', $id)) {
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_subscription&view=features', false));

			return false;
		}

		parent::display();

		return $this;
	}
}
