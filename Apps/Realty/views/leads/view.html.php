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
 * View class for a list of leads.
 *
 * @package		Realtor.Administrator
 * @subpackage	com_realtor
 * @since		2.5
 */
class RealtorViewLeads extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

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
		$canDo	= RealtorHelper::getActions();

		JToolBarHelper::title(JText::_('COM_REALTOR_VIEW_LEADS_TITLE'), 'lead');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('lead.add');
		}
		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('lead.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('leads.active', 'COM_REALTOR_TOOLBAR_ACTIVATE', true);
			JToolBarHelper::unpublish('leads.assign', 'COM_REALTOR_TOOLBAR_ASSIGN', true);
			JToolBarHelper::custom('leads.unassign', 'unassign.png', 'unassign_f2.png', 'COM_REALTOR_TOOLBAR_UNASSIGN', true);
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'leads.delete');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_realtor');
			JToolBarHelper::divider();
		}

		JToolBarHelper::help('JHELP_REALTOR_LEAD_MANAGER');
	}
}
