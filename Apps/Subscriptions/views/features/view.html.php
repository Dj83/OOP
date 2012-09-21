<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of Subscription Features.
 *
 * @package     Sold.Administrator
 * @subpackage  com_subscriptions
 * @since       2.5
 */
class SubscriptionsViewFeatures extends JView
{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Method to display the view.
	 *
	 * @param   string  $tpl  A template file to load. [optional]
	 * @return  mixed  A string if successful, otherwise a JError object.
	 * @since   2.5
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->categories	= $this->get('CategoryOrders');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   2.5
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/subscriptions.php';

		$canDo = SubscriptionsHelper::getActions($this->state->get('filter.category_id'));
		$user = JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_SUBSCRIPTIONS_MANAGER_FEATURES'), 'features.png');
		if (count($user->getAuthorisedCategories('com_subscriptions.features', 'core.create')) > 0)
		{
			JToolBarHelper::addNew('feature.add');
		}

		if (($canDo->get('core.edit')))
		{
			JToolBarHelper::editList('feature.edit');
		}

		if ($canDo->get('core.edit.state'))
		{
			if ($this->state->get('filter.state') != 2)
			{
				JToolBarHelper::divider();
				JToolBarHelper::publish('features.publish', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::unpublish('features.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			}

			if ($this->state->get('filter.state') != -1)
			{
				JToolBarHelper::divider();
				if ($this->state->get('filter.state') != 2)
				{
					JToolBarHelper::archiveList('features.archive');
				}
				elseif ($this->state->get('filter.state') == 2)
				{
					JToolBarHelper::unarchiveList('features.publish');
				}
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::checkin('features.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
		{
			JToolBarHelper::deleteList('', 'features.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state'))
		{
			JToolBarHelper::trash('features.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_subscriptions');
			JToolBarHelper::divider();
		}
		JToolBarHelper::help('JHELP_COMPONENTS_SUBSCRIPTIONS_FEATURES');
	}
}
