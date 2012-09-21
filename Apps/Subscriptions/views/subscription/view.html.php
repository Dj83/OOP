<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a subscription.
 *
 * @package		Sold.Administrator
 * @subpackage	com_subscriptions
 * @since		2.5
 */
class SubscriptionsViewSubscription extends JView
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
		$this->canDo	= SubscriptionsHelper::getActions($this->state->get('filter.category_id'));

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
	 * @since	2.5
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= SubscriptionsHelper::getActions($this->state->get('filter.category_id'), $this->item->id);
		JToolBarHelper::title(JText::_('COM_SUBSCRIPTIONS_PAGE_'.($checkedOut ? 'VIEW_SUBSCRIPTION' : ($isNew ? 'ADD_SUBSCRIPTION' : 'EDIT_SUBSCRIPTION'))), 'subscription-add.png');

		// Built the actions for new and existing records.

		// For new records, check the create permission.
		if ($isNew && (count($user->getAuthorisedCategories('com_subscriptions', 'core.create')) > 0)) {
			JToolBarHelper::apply('subscription.apply');
			JToolBarHelper::save('subscription.save');
			JToolBarHelper::save2new('subscription.save2new');
			JToolBarHelper::cancel('subscription.cancel');
		}
		else {
			// Can't save the record if it's checked out.
			if (!$checkedOut) {
				// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
				if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
					JToolBarHelper::apply('subscription.apply');
					JToolBarHelper::save('subscription.save');

					// We can save this record, but check the create permission to see if we can return to make a new one.
					if ($canDo->get('core.create')) {
						JToolBarHelper::save2new('subscription.save2new');
					}
				}
			}

			// If checked out, we can still save
			if ($canDo->get('core.create')) {
				JToolBarHelper::save2copy('subscription.save2copy');
			}

			JToolBarHelper::cancel('subscription.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_SUBSCRIPTIONS_SUBSCRIPTION_MANAGER_EDIT');
	}
}
