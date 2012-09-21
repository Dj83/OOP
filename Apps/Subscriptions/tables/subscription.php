<?php
/**
 * @package     Sold.Administrator
 * @subpackage  com_subscriptions
 *
 * @copyright   Copyright (C) 2012 Sold CRM. All rights reserved.
 * @license     see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Suybscriptions table class
 *
 * @package     Sold.Administrator
 * @subpackage  com_subscriptions
 * @since       2.5
 */
class SubscriptionsTableSubscription extends JTable
{
	/**
	 * Constructor
	 *
	 * @param  JDatabase  &$db  Database object
	 *
	 * @since  2.5
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__subscriptions', 'id', $db);
	}

	/**
	 * Overloaded store method for the notes table.
	 *
	 * @param   boolean  $updateNulls  Toggle whether null values should be updated.
	 *
	 * @return  boolean  True on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function store($updateNulls = false)
	{
		// Initialise variables.
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;

		// If there are no primary keys set check to see if the instance key is set.
		if (empty($pks))
		{
			if ($this->$k)
			{
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else
			{
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		$query = $this->_db->getQuery(true);
		$query->update($this->_db->quoteName($this->_tbl));
		$query->set($this->_db->quoteName('state') . ' = ' . (int) $state);

		// Build the WHERE clause for the primary keys.
		$query->where($k . '=' . implode(' OR ' . $k . '=', $pks));

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = false;
		}
		else
		{
			$query->where('(checked_out = 0 OR checked_out = ' . (int) $userId . ')');
			$checkin = true;
		}

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// If checkin is supported and all rows were adjusted, check them in.
		if ($checkin && (count($pks) == $this->_db->getAffectedRows()))
		{
			// Checkin the rows.
			foreach($pks as $pk)
			{
				$this->checkin($pk);
			}
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks))
		{
			$this->state = $state;
		}

		$this->setError('');
		return true;
	}
/*	public function bind($array, $ignore = array())
	{
		if (isset($array['features']) && is_array($array['features'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['features']);

			
			$array['features'] = (string)$registry;
		}
		return parent::bind($array, $ignore);
	}
	function check()
	{

		return parent::check();
	}
*/}
