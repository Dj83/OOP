<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Feature cbanontroller class.
 *
 * @package     Sold.Administrator
 * @subpackage  com_subscriptions
 * @since       2.5
 */
class SubscriptionsControllerFeature extends JControllerForm
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_SUBSCRIPTIONS_FEATURE';

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 * @return  boolean
	 * @since   2.5
	 */
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId)
		{
			// If the category has been passed in the URL check it.
			$allow	= $user->authorise('core.create', $this->option . '.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absence of better information, revert to the component permissions.
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 * @return  boolean
	 * @since   2.5
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;

		if ($recordId)
		{
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId)
		{
			// The category has been set. Check the category permissions.
			return $user->authorise('core.edit', $this->option . '.category.' . $categoryId);
		}
		else
		{
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   string  $model  The model
	 * @return	boolean  True on success.
	 * @since	2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel('Feature', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_subscriptions&view=features' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

}
