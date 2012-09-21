<?php

defined('JPATH_PLATFORM') or die;

jimport('joomla.database.tableasset');
jimport('joomla.database.table');

class FormsTableField extends JTable
{
	public function __construct(&$db)
	{
		parent::__construct('#__forms_fields', 'id', $db);
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_forms.field.' . (int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->name;
	}

	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;

		// This is a article under a category.
		if ($this->catid)
		{
			// Build the query to get the asset id for the parent category.
			$query = $this->_db->getQuery(true);
			$query->select($this->_db->quoteName('asset_id'));
			$query->from($this->_db->quoteName('#__categories'));
			$query->where($this->_db->quoteName('id') . ' = ' . (int) $this->catid);

			// Get the asset id from the database.
			$this->_db->setQuery($query);
			if ($result = $this->_db->loadResult())
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	public function bind($array, $ignore = '')
	{
		if (isset($array['attribs']) && is_array($array['attribs']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string) $registry;
		}

		if (isset($array['custom']) && is_array($array['custom']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['custom']);
			$array['custom'] = (string) $registry;
		}

		// Bind the rules.
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError(JText::_('COM_FORMS_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}

		if (trim($this->alias) == '')
		{
			$this->alias = $this->name;
		}

		$this->alias = JApplication::stringURLSafe($this->alias);

		if (trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getUser()->alias . JFactory::getDate()->format('-m-d-Y-H-i-s');
		}

		if (trim(str_replace('&nbsp;', '', $this->name)) == '')
		{
			$this->name = '';
		}

		// Check the publish down date is not earlier than publish up.
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		return true;
	}

	public function store($updateNulls = false)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();

		// Verify that the alias is unique
		$table = JTable::getInstance('Field', 'FormsTable');
		if ($table->load(array('alias' => $this->alias, 'catid' => $this->fid)) && ($table->id != $this->id || $this->id == 0))
		{
			$this->setError(JText::_('JLIB_DATABASE_ERROR_FIELD_UNIQUE_ALIAS'));
			return false;
		}
		return parent::store($updateNulls);
	}

	public function publish($pks = null, $state = 1, $userId = 0)
	{
		// Initialise variables.
		$k = $this->_tbl_key;

		// Sanitize input.
		JArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state = (int) $state;

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

		// Build the WHERE clause for the primary keys.
		$where = $k . '=' . implode(' OR ' . $k . '=', $pks);

		// Determine if there is checkin support for the table.
		if (!property_exists($this, 'checked_out') || !property_exists($this, 'checked_out_time'))
		{
			$checkin = '';
		}
		else
		{
			$checkin = ' AND (checked_out = 0 OR checked_out = ' . (int) $userId . ')';
		}

		// Get the JDatabaseQuery object
		$query = $this->_db->getQuery(true);

		// Update the publishing state for rows with the given primary keys.
		$query->update($this->_db->quoteName($this->_tbl));
		$query->set($this->_db->quoteName('state') . ' = ' . (int) $state);
		$query->where('(' . $where . ')' . $checkin);
		$this->_db->setQuery($query);
		$this->_db->execute();

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
			foreach ($pks as $pk)
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
}