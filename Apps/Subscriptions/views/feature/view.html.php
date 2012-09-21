<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JLoader::register('SubscriptionsHelper', JPATH_COMPONENT.'/helpers/subscriptions.php');

/**
 * View to edit a Subscription Feature.
 *
 * @package		Sold.Administrator
 * @subpackage	com_subscriptions
 * @since		2.5
 */
class SubscriptionsViewFeature extends JView
{
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= SubscriptionsHelper::getActions($this->item->catid,0);

		JToolBarHelper::title($isNew ? JText::_('COM_SUBSCRIPTIONS_MANAGER_FEATURE_NEW') : JText::_('COM_SUBSCRIPTIONS_MANAGER_FEATURE_EDIT'), 'features.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_subscriptions', 'core.create')) > 0)) {
			JToolBarHelper::apply('feature.apply');
			JToolBarHelper::save('feature.save');

			if ($canDo->get('core.create')) {
				JToolBarHelper::save2new('feature.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('feature.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('feature.cancel');
		}
		else {
			JToolBarHelper::cancel('feature.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_SUBSCRIPTIONS_FEATURES_EDIT');
	}
}
