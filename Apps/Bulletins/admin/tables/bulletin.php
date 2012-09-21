<?php
defined('_JEXEC') or die;

jimport('joomla.database.table');

class BulletinsTableBulletin extends JTable
{
	function __construct(& $db)
	{
		parent::__construct('#__bulletins', 'id', $db);
	}
	protected function _getAssetTitle()
	{
		return $this->subject;
	}
	public function bind($array, $ignore = '')
	{
		if (isset($array['attachments']) && is_array($array['attachments']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['attachments']);
			$array['attachments'] = (string) $registry;
		}

		if (isset($array['usergroups']) && is_array($array['usergroups']))
		{
			$array['usergroups'] = (string) implode(",", $array['usergroups']);
		}

		return parent::bind($array, $ignore);
	}

	function check()
	{
		if(empty($this->subject) || trim($this->subject) == '') {
			$this->setError(JText::_('COM_BULLETINS_ERROR_INVALID_SUBJECT'));
			return false;
		}

		if(empty($this->message) || trim($this->message) == '') {
			$this->setError(JText::_('COM_BULLETINS_ERROR_INVALID_MESSAGE'));
			return false;
		}

		// Check the publish down date is not earlier than publish up.
		if($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		return true;
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
			if ($this->$k) {
				$pks = array($this->$k);
			}
			// Nothing to set publishing state on, return false.
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}

		// Build the WHERE clause for the primary keys.
		$where = $k.' IN ('.implode(',', $pks).')';

		// Update the publishing state for rows with the given primary keys.
		$this->_db->setQuery(
			'UPDATE '.$this->_db->quoteName($this->_tbl).
			' SET '.$this->_db->quoteName('state').' = '.(int) $state .
			' WHERE ('.$where.')'
		);
		$this->_db->query();

		// Check for a database error.
		if ($this->_db->getErrorNum()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		// If the JTable instance value is in the list of primary keys that were set, set the instance.
		if (in_array($this->$k, $pks)) {
			$this->state = $state;
		}

		return true;
	}

	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		if ($this->id)
		{
			// Existing item
			$this->modified = $date->toSql();
			$this->modified_by = $user->get('id');
		}
		else
		{
			// New bulletin. A bulletin created field can be set by the user,
			// so we don't touch that if it are set.
			if (!intval($this->created))
			{
				$this->created = $date->toSql();
			}
			if (!intval($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}
		// Verify that the alias is unique
		$table = JTable::getInstance('Bulletin', 'BulletinsTable');
		if ($table->load(array('subject' => $this->subject, 'catid' => $this->catid)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('COM_BULLETINS_ERROR_BULLETIN_UNIQUE_SUBJECT'));
			return false;
		}
		return parent::store($updateNulls);
	}
}