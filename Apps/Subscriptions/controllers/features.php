<?php

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Features list controller class.
 *
 * @package		Sold.Administrator
 * @subpackage	com_subscriptions
 * @since		2.5
 */
class SubscriptionsControllerFeatures extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	2.5
	 */
	protected $text_prefix = 'COM_SUBSCRIPTIONS_FEATURES';

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	2.5
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('unrequired',	'required');
	}

	/**
	 * Method to toggle the featured setting of a list of articles.
	 *
	 * @return	void
	 * @since	1.6
	 */
	function requires()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= JRequest::getVar('cid', array(), '', 'array');
		$values	= array('required' => 1, 'unrequired' => 0);
		$task	= $this->getTask();
		$value	= JArrayHelper::getValue($values, $task, 0, 'int');

		// Access checks.
		foreach ($ids as $i => $id)
		{
			if (!$user->authorise('core.edit.state', 'com_subscriptions.feature.'.(int) $id)) {
				// Prune items that you can't change.
				unset($ids[$i]);
				JError::raiseNotice(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
			}
		}

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->requires($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_subscriptions&view=features');
	}

	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'Feature', $prefix = 'SubscriptionsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
