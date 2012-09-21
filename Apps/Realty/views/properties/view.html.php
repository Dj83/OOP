<?php
/**
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for a list of properties.
 *
 * @package		Realtor.Administrator
 * @subpackage	com_realtor
 * @since		2.5
 */
class RealtorViewProperties extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

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
		require_once JPATH_COMPONENT.'/helpers/realtor.php';

		$canDo	= RealtorHelper::getActions();

		JToolBarHelper::title(JText::_('COM_REALTOR_MANAGER_PROPERTIES'), 'realtor-properies.png');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('property.add');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('property.edit');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('properties.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('properties.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('properties.archive');
			JToolBarHelper::checkin('properties.checkin');
		}
		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'proprties.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('properties.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_realtor');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_COMPONENTS_REALTOR_PROPERTIES');
	}
}
