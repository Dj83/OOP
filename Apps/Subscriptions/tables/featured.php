<?php

// no direct access
defined('_JEXEC') or die;

class SubscriptionsTableFeatured extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__subscriptions_frontpage', 'subscription_id', $db);
	}
}
