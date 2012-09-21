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
		$app = JFactory::getApplication();
		$params = $app->getParams();
		$items = $this->get('Items');
		$state = $this->get('State');
		$pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('items', $items);
		$this->assignRef('state',$state);
		$this->assignRef('params', $params);
		$this->assignRef('pagination', $pagination);
		$this->_prepareDocument();
		parent::display($tpl);
	}
	
	protected function _prepareDocument()
	{
		//JHtml::_('script', 'com_bulletins/bulletins.js', true, true);
		JHtml::_('stylesheet', 'com_bulletins/system.css', array(), true);
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_BULLETINS_DEFAULT_PAGE_TITLE'));
		}
		$id = (int) @$menu->query['id'];

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