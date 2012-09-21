<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class BulletinsViewBulletins extends JView
{
	protected $items;
	protected $state;
	protected $pagination;

	public function display($tpl=null)
	{
		$this->items			= $this->get('Items');
		$this->state			= $this->get('State');
		$this->pagination	= $this->get('Pagination');

		if(count($errors = $this->get('Errors'))){
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);

		$this->addToolbar();
	}

	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = BulletinsHelper::getActions();

		JToolbarHelper::title(JText::_('COM_BULLETINS_MANAGER_BULLETINS'), 'inbox.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_bulletins', 'core.create'))) > 0 ) {
			JToolBarHelper::addNew('bulletin.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('bulletin.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('bulletins.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('bulletins.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::custom('bulletins.featured', 'featured.png', 'featured_f2.png', 'JFEATURED', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('bulletins.archive');
			JToolBarHelper::checkin('bulletins.checkin');
		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'bulletins.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('bulletins.trash');
			JToolBarHelper::divider();
		}

		JToolBarHelper::divider();
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_bulletins');
		}
	}
}