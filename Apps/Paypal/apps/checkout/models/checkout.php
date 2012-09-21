<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_COMPONENT . '/tables');

class CheckoutModelCheckout extends JModelList
{
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('cart.id');

		return parent::getStoreId($id);
	}

	/**
	 * Gets a list of cart items
	 *
	 * @return	array	An array of carts' product objects.
	 * @since	2.5
	 */
	protected function getListQuery()
	{
		$user = JFactory::getUser();
		$db			= $this->getDbo();
		$query		= $db->getQuery(true);
		$ordering	= $this->getState('filter.ordering');
		$unique		= $this->getState('cart.id');
		$nullDate	= $db->quote($db->getNullDate());

		$query->select(
			'a.id as id,'.
			'a.qty as quantity'
			);
			$query->from('#__checkout as a');
			$query->where('a.state=1');
			$query->where('('.$query->currentTimestamp().' >= a.publish_up OR a.publish_up = '.$nullDate.')');
			$query->where('('.$query->currentTimestamp().' <= a.publish_down OR a.publish_down = '.$nullDate.')');
			$query->where('(a.imptotal = 0 OR a.impmade <= a.imptotal)');



		if ($unique) {
			$query->where('a.unique_id = ' . (int) $uniqueid);
			$query->join('LEFT', '#__content AS p ON p.id = a.item_id');
			$query->select('p.title as title, p.introtext as description, p.attribs as params');
			$query->where('p.state = 1');
		}else{
			// Must be a users cart
			$qyery->where('a.author = ' .(int) $user->get('id'));
		}


		$query->order('a.ordering');
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
		$items = parent::getItems();

		foreach ($items as &$item)
		{
			$parameters = new JRegistry;
			$parameters->loadString($item->params);
			$item->params = $parameters;
			$item->price = float(( 
					$item->params->get('price') +
					$this->params->get('attributes_'.$this->params->get('attribute'),0.00)
				)*
				$this->qty
			);
		}
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
		
		$items	= $this->getItems();
		// TODO: send items to the query for checkout details at PayPal
		// ...

		$db	= $this->getDbo();
		$query	= $db->getQuery(true);

		foreach ($items as $item)
		{
			// Increment impression made
			$id = $item->id;
			$query->clear();
			$query->delete('#__checkout');
			$query->where('id = '.(int)$id);
			$db->setQuery((string)$query);

			if (!$db->query()) {
				JError::raiseError(500, $db->getErrorMsg());
			}
		}
		$state->set('cart.id',null);
		$state->set('cart.apitoken',null);
	}
}
