<?php

// No direct access.
defined('_JEXEC') or die;

/**
 * Subscriptions Feature table
 *
 * @package		Sold.Administrator
 * @subpackage	com_subscriptions
 * @since		2.5
 */
class SubscriptionsTableFeature extends JTable
{
	/**
	 * Constructor
	 *
	 * @since	2.5
	 */
	function __construct(&$_db)
	{
		parent::__construct('#__subscriptions_features', 'id', $_db);
		$date = JFactory::getDate();
		$this->created = $date->toSql();
	}

	/**
	 * Overloaded check function
	 *
	 * @return	boolean
	 * @see		JTable::check
	 * @since	2.5
	 */
	function check()
	{
		// Set label
		$this->label = htmlspecialchars_decode($this->label, ENT_QUOTES);

		// Set Note
		$this->note = strip_tags(htmlspecialchars_decode($this->note, ENT_QUOTES));

		// Set label
		$this->message = strip_tags(htmlspecialchars_decode($this->message, ENT_QUOTES));

		// Set alias
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (empty($this->alias)) {
			$this->alias = JApplication::stringURLSafe($this->label);
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up) {
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		// Set ordering
		if ($this->state < 0) {
			// Set ordering to 0 if state is archived or trashed
			$this->ordering = 0;
		} elseif (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder($this->_db->quoteName('catid').'=' . $this->_db->Quote($this->catid).' AND state>=0');
		}

		return true;
	}

	/**
	 * Overloaded bind function
	 *
	 * @param	array		$hash named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 2.5
	 */
	public function bind($array, $ignore = array())
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);

			
			$array['params'] = (string)$registry;
		}
		return parent::bind($array, $ignore);
	}
	/**
	 * method to store a row
	 *
	 * @param boolean $updateNulls True to update fields even if they are null.
	 */
	function store($updateNulls = false)
	{
		if (empty($this->id))
		{
			$type = $this->type;

			switch($type)
			{
				case 1:
					$this->reset=$this->_db->getNullDate();
					break;
				case 2:
					$date = JFactory::getDate('+1 year '.date('Y-m-d', strtotime('now')));
					$reset = $this->_db->Quote($date->toSql());
					break;
				case 3:
					$date = JFactory::getDate('+1 month '.date('Y-m-d', strtotime('now')));
					$reset = $this->_db->Quote($date->toSql());
					break;
				case 4:
					$date = JFactory::getDate('+7 day '.date('Y-m-d', strtotime('now')));
					$reset = $this->_db->Quote($date->toSql());
					break;
				case 5:
					$date = JFactory::getDate('+1 day '.date('Y-m-d', strtotime('now')));
					$reset = $this->_db->Quote($date->toSql());
					break;
			}
			// Store the row
			parent::store($updateNulls);
		}
		else
		{
			// Get the old row
			$oldrow = JTable::getInstance('Feature', 'SubscriptionsTable');
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}

			// Verify that the alias is unique
			$table = JTable::getInstance('Feature', 'SubscriptionsTable');
			if ($table->load(array('alias'=>$this->alias, 'catid'=>$this->catid)) && ($table->id != $this->id || $this->id==0)) {
				$this->setError(JText::_('COM_SUBSCRIPTIONS_ERROR_UNIQUE_ALIAS'));
				return false;
			}

			// Store the new row
			parent::store($updateNulls);

			// Need to reorder ?
			if ($oldrow->state>=0 && ($this->state < 0 || $oldrow->catid != $this->catid))
			{
				// Reorder the oldrow
				$this->reorder($this->_db->quoteName('catid').'=' . $this->_db->Quote($oldrow->catid).' AND state>=0');
			}
		}
		return count($this->getErrors())==0;
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param	mixed	An optional array of primary key values to update.  If not
	 *					set the instance property value is used.
	 * @param	integer The publishing state. eg. [0 = unpublished, 1 = published, 2=archived, -2=trashed]
	 * @param	integer The user id of the user performing the operation.
	 * @return	boolean	True on success.
	 * @since	1.6
	 */
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
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Get an instance of the table
		$table = JTable::getInstance('Feature', 'SubscriptionsTable');

		// For all keys
		foreach ($pks as $pk)
		{
			// Load the banner
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}

			// Verify checkout
			if($table->checked_out==0 || $table->checked_out==$userId)
			{
				// Change the state
				$table->state = $state;
				$table->checked_out=0;
				$table->checked_out_time=$this->_db->getNullDate();

				// Check the row
				$table->check();

				// Store the row
				if (!$table->store())
				{
					$this->setError($table->getError());
				}
			}
		}
		return count($this->getErrors())==0;
	}
}