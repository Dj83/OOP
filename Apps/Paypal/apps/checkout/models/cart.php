<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');
jimport('paypal.checkout.express');
//JTable::addIncludePath(JPATH_COMPONENT . '/tables');

class CheckoutModelCart extends JModelList
{
	protected $context;
	public function __construct($config=array())
	{
		$config['dbo'] = null;
		$config['table_path'] = null;
		$this->context = 'mercury.crm.checkout';
 		$this->option = 'checkout';
		parent::__construct($config);
	}
	/**
	 * Gets a list of cart items
	 *
	 * @return	array	An array of carts' product objects.
	 * @since	2.5
	 */
	protected function getListQuery()
	{
		$db			= $this->getDbo();
		$query		= $db->getQuery(true);
		return $query;
	}

	/**
	 * Get a list of Cart items
	 *
	 * @return	array
	 * @since	2.5
	 */
	public function getItems()
	{
		$items = JFactory::getSession()->get('mercury.cart.pp.subscriptions', array());
		return $items;
	}

	/**
	 * Checks out a list of items after it gets paypal checkout details
	 *
	 * @return	void
	 * @since	2.5
	 */
	public function complete()
	{
		$state= $this->getState();
		$state->set('mercury.cart.pp.subscriptions',null);
		$state->set('mercury.cart.pp.apitoken',null);
		$this->display();
	}
	public function getToken()
	{
		//$model = $this->getModel();

		//$params = JComponentHelper::getParams('com_checkout');
		//$model->setState('params',$params);

		//$user = JFactory::getUser();// Set the default view name and format from the Request.

		$tmpIds = array();
		$tmps = array();
		
return '';
		/*$items = $model->getItems();
		$grandTotal = (float)0.00;
		$checkoutItems = array();

		if(!empty($items)){
			foreach($items as &$item){
				// Typecast values
				$price = isset($item['price']) ? round((float)$item['price'],2) : 0.00;
				$qty =	isset($item['quantity']) ? (int) $item['quantity'] : 1;
				$grandTotal = round($grandTotal + ($price * $qty),2);
			}
		}

		$properties= array();
		
		$properties['amt'] = $grandTotal;
		$properties['currencycode'] = $params->get('default_currency','AUD');
		$properties['paymentaction'] = 'Sale';

		$pp = new MPayPalCheckout($properties,$grandTotal);
		$token = $pp->get('return');
		
		//$model->setState('apitoken', $token);
		return $token;*/
	}
}
