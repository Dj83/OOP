<?php
/**
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
JLoader::register('RealtorHelper', JPATH_COMPONENT.'/helpers/realtor.php');

/**
 * View to edit a lead.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_realtor
 * @since		2.5
 */
class RealtorViewLead extends JView
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
	 * @since	2.5
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= RealtorHelper::getActions($this->item->catid,0);

		JToolBarHelper::title($isNew ? JText::_('COM_REALTOR_MANAGER_LEAD_NEW') : JText::_('COM_REALTOR_MANAGER_LEAD_EDIT'), 'leads.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_realtor', 'core.create')) > 0)) {
			JToolBarHelper::apply('lead.apply');
			JToolBarHelper::save('lead.save');

			if ($canDo->get('core.create')) {
				JToolBarHelper::save2new('lead.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('lead.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('lead.cancel');
		}
		else {
			JToolBarHelper::cancel('lead.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_REALTOR_LEADS_EDIT');
	}
}
