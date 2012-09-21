<?php

jimport('joomla.application.component.view');

class FormsViewFields extends JView
{
	protected $categories;
	protected $items;
	protected $state;
	protected $form_id;

	public function display($tpl=null)
	{
		$user = JFactory::getUser(); 
		$jinput = JFactory::getApplication()->input;
		$items = $this->get('Items');
		$state = $this->get('State');
		$this->form_id = JRequest::getVar('fid');
		$this->params = &$state->params;

		if(JRequest::getVar('layout', 'default') === 'fields'){
			$this->_prepareDocument();
			parent::display();
			return $this;
		}
		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		foreach ($items as $i => & $item)
		{
			$item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
			$item->catslug = ($item->category_alias) ? ($item->catid . ':' . $item->category_alias) : $item->catid;
			$item->parent_slug = ($item->parent_alias) ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;
			// No link for ROOT category
			if ($item->parent_alias == 'root') {
				$item->parent_slug = null;
			}
		}
		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->assignRef('params', $this->params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('user', $user);

		$this->_prepareDocument();

		parent::display($tpl);
	}
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$title 		= null;

		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_FORMS_FIELDS'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}