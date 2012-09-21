<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * @package		Mercury.Crm
 */
class CheckoutViewCart extends JView
{
	//protected $items;

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$params = JFactory::getConfig();

		$model = $this->getModel();
		$items = $this->get('Items');
		$token = null;

		if($items){
			$token = $this->get('Token');
		}
		if ($this->getLayout() == '') {
		}
		$this->pageclass_sfx = htmlspecialchars($params->get('pageclass_sfx',''));
		$this->assignRef('items',$items);
		$this->assignRef('params',$params);
		$this->assignRef('token',$token);
		self::_prepare($app,$params);
		parent::display($tpl);
	}

	protected function _prepare($app,$params)
	{
		// because the application sets a default page title, we need to get it
		// right from the menu item itself
		$title = $params->get('page_title', 'Shopping Cart');
		$this->document->setTitle($title);

		if ($params->get('menu-meta_description'))
		{
			$this->document->setDescription($params->get('menu-meta_description'));
		}

		if ($params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		}

		if ($params->get('robots'))
		{
			$this->document->setMetadata('robots', $params->get('robots'));
		}
	}
}
