<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

require_once dirname(__FILE__).'/subscription.php';

class SubscriptionsModelFeaturing extends SubscriptionsModelSubscription
{
	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	*/
	public function getTable($type = 'Featured', $prefix = 'SubscriptionsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	2.5
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		return $condition;
	}
}
