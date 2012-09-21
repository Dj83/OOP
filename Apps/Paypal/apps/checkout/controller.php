<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class CheckoutController extends JController
{
	function __construct($config = array())
	{
		$config['default_view'] = 'cart';
		parent::__construct($config);
	}
	public function getModel($name = 'Cart', $prefix = 'CheckoutModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function display($cachable = false, $urlparams = false)
	{
		return parent::display(false);


jimport('joomla.utilities.utility');
$user = JFactory::getUser();// Set the default view name and format from the Request.
$vName		= JRequest::getCmd('view', 'cart');
JRequest::setVar('view', $vName);

$model = $this->getModel();

return parent::display($cachable, array('Itemid'=>'INT'));

	}
}
