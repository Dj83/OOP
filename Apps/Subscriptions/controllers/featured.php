<?php

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once dirname(__FILE__).'/subscriptions.php';

/**
 * @package		Sold.Administrator
 * @subpackage	com_subscriptions
 */
class SubscriptionsControllerFeatured extends SubscriptionsControllerSubscriptions
{
	/**
	 * Removes an item
	 */
	function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.delete', 'com_subscriptions.subscription.'.(int) $id))
			{
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JERROR_CORE_DELETE_NOT_PERMITTED'));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Remove the items.
			if (!$model->featured($ids, 0)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_subscriptions&view=featured');
	}

	/**
	 * Method to publish a list of subscriptions.
	 *
	 * @return	void
	 * @since	2.5
	 */
	function publish()
	{
		parent::publish();

		$this->setRedirect('index.php?option=com_subscriptions&view=featured');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   2.5
	 */
	public function getModel($name = 'Featuring', $prefix = 'SubscriptionsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
