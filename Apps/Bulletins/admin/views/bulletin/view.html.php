<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');
JLoader::register('BulletinsHelper', JPATH_COMPONENT.'/helpers/bulletins.php');

class BulletinsViewBulletin extends JView
{
	protected $item;
	protected $form;
	protected $state;
	public function display($tpl=null)
	{
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		$this->state = $this->get('State');
		
		if(count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		parent::display($tpl);
		$this->addToolbar();
	}
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		// Since we don't track these assets at the item level, use the category id.
		$canDo		= BulletinsHelper::getActions($this->item->catid,0);

		JToolBarHelper::title($isNew ? JText::_('COM_BULLETINS_MANAGER_BULLETIN_NEW') : JText::_('COM_BULLETINS_MANAGER_BULLETIN_EDIT'), 'bulletins.png');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_bulletins', 'core.create')) > 0) {
			JToolBarHelper::apply('bulletin.apply');
			JToolBarHelper::save('bulletin.save');

			if ($canDo->get('core.create')) {
				JToolBarHelper::save2new('bulletin.save2new');
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::save2copy('bulletin.save2copy');
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('bulletin.cancel');
		}
		else {
			JToolBarHelper::cancel('bulletin.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}