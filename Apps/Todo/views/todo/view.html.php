<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class TodoViewTodo extends JView
{
	protected $item;
	protected $params;
	protected $print;
	protected $state;
	protected $user;

	function display($tpl = null)
	{
		// Initialise variables.
		$app		= JFactory::getApplication();
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$dispatcher	= JDispatcher::getInstance();

		$this->item		= $this->get('Item');

		$this->print	= JRequest::getBool('print');
		$this->state	= $this->get('State');
		$this->user		= $user;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		// Create a shortcut for $item.
		$item = &$this->item;

		// Add router helpers.
		$item->slug			= $item->id;
		$item->catslug		= $item->category_alias ? ($item->catid.':'.$item->category_alias) : $item->catid;

		// TODO: Change based on shownoauth
		$item->readmore_link = JRoute::_(TodoHelperRoute::getTaskRoute($item->slug, $item->catslug));

		$this->params	= $this->state->get('params');
		$active	= $app->getMenu()->getActive();
		$temp	= clone ($this->params);

		// Check to see which parameters should take priority
		if ($active) {
			$currentLink = $active->link;
			if (strpos($currentLink, 'view=todo') && (strpos($currentLink, '&id='.(string) $item->id))) {
				$item->params->merge($temp);
				if (isset($active->query['layout'])) {
					$this->setLayout($active->query['layout']);
				}
			}
			else {
				$temp->merge($item->params);
				$item->params = $temp;
				if ($layout = $item->params->get('todo_layout')) {
					$this->setLayout($layout);
				}
			}
		}
		else {
			// Merge so that todo task params take priority
			$temp->merge($item->params);
			$item->params = $temp;
			// Check for alternative layouts (since we are not in a single todo task menu item)
			if ($layout = $item->params->get('todo_layout')) {
				$this->setLayout($layout);
			}
		}

		$offset = $this->state->get('list.offset');

		// Check the view access to the todo task (the model has already computed the values).
		if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true &&  $user->get('guest') ))) {
			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

		//$this->_prepareDocument();

		parent::display($tpl);
	}

	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$pathway = $app->getPathway();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_TODO_PAGE_HEADING_TODOS'));
		}

		$title = $this->params->get('page_title', '');

		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this task
		if ($menu && ($menu->query['option'] != 'com_todo' || $menu->query['view'] != 'todo' || $id != $this->item->id))
		{
			// If this is not a single task menu item, set the page title to the task title
			if ($this->item->subject) {
				$title = $this->item->subject;
			}
			$path = array(array('title' => $this->item->subject, 'link' => ''));
			$category = JCategories::getInstance('Todo')->get($this->item->catid);
			while ($category && ($menu->query['option'] != 'com_todo' || $menu->query['view'] == 'todo' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => ToDoHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}
			$path = array_reverse($path);
			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		// Check for empty title and add site name if param is set
		if (empty($title)) {
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		if (empty($title)) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->getCfg('MetaAuthor') == '1')
		{
			$this->document->setMetaData('author', $this->item->author);
		}

		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}