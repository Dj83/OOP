<?php

defined('_JEXEC') or die;

jimport('paypal.checkout.cart');
jimport('paypal.checkout.item');

class CheckoutControllerCart extends JController
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

	/*
	 * @note	This is a task to add an item to the cart
	 * @note	The url query looks like this: task=cart.add&...[query]...
	 */
	public function add()
	{
		// Find the application for the input object
		$req = JFactory::getApplication();
		// Check the token
		//JRequest::checkToken() or jexit(JText::_('Invalid Request Token or Forgery Attempt Detected'));
		$record = array();
		$record['id'] = $req->input->get('pp_id',0);
		$record['itemId'] = $req->input->get('pp_item_id',0);
		$record['name'] = $req->input->get('pp_item_name','');
		$record['section'] = $req->input->get('pp_item_section','');
		$record['houses'] = $req->input->get('pp_item_houses',0);
		$record['postcode'] = $req->input->get('pp_item_postcode',0000);
		$record['material_price'] = $req->input->get('pp_item_material_price',0.00);
		$record['description'] = $req->input->get('pp_item_description','');
		$record['quantity'] = 1;
		$record['price'] = $req->input->get('pp_item_price', (float) 0.00);

		//if($record->itemId == 0 || $record->name == '' || $record->description = '' || $record->quantity == 0 || $record->price = 0.00){
			//return parent::display();
		//}

		//if($record->name == '' || $record->name == '&nbsp;' || $record->name == ' '){
			
			//return parent::display();
		//}
		// Attempt to add the record to the cart array
		if(!MPayPalCart::add($record)){
			echo 'Error adding to cart.';
			parent::display();
		}
		parent::display();
	}
}