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
JLoader::register('RealtorHelper', JPATH_COMPONENT.'/helpers/realtor.php');

/**
 * View to edit a property.
 *
 * @package		Realtor.Administrator
 * @subpackage	com_realtor
 * @since		2.5
 */
class RealtorViewProperty extends JView
{
	protected $form;
	protected $item;
	protected $state;
	protected $canDo;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->form	= $this->get('Form');
		$this->item	= $this->get('Item');
		$this->state= $this->get('State');
		$this->canDo= RealtorHelper::getActions();

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
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= &$this->canDo;

		JToolBarHelper::title($isNew ? JText::_('COM_REALTOR_MANAGER_PROPERTY_NEW') : JText::_('COM_REALTOR_MANAGER_PROPERTY_EDIT'), 'realtor-properties.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit')||$canDo->get('core.create'))) {
			JToolBarHelper::apply('property.apply');
			JToolBarHelper::save('property.save');
		}
		if (!$checkedOut && $canDo->get('core.create')) {

			JToolBarHelper::save2new('property.save2new');
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('property.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('property.cancel');
		} else {
			JToolBarHelper::cancel('property.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
		JToolBarHelper::help('JHELP_COMPONENTS_REALTOR_PROPERTIES_EDIT');
	}
}
