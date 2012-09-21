<?php
/**
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.database.tableasset');
jimport('joomla.database.table');

/**
 * Leads table
 *
 * @package     Realtor.Library
 * @subpackage  Table
 * @since       12.1
 */
class RealtorTableLead extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   JDatabase  &$db  A database connector object
	 *
	 * @since   12.1
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__leads', 'id', $db);
		$date = JFactory::getDate();
		$this->created = $date->toSql();
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_realtor.lead.' . (int) $this->$k;
	}

	/**
	 * Method to return the full name to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   12.1
	 */
	protected function _getAssetTitle()
	{
		return $this->fullname;
	}

	/**
	 * Method to get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object (optional) for the asset parent
	 * @param   integer  $id     The id (optional) of the lead.
	 * @return  integer
	 * @since   12.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;

		// This is a lead under a category.
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

	/**
	 * Overloaded bind function
	 *
	 * @param   array  $array   Named array
	 * @param   mixed  $ignore  An optional array or space separated list of properties
	 * to ignore while binding.
	 *
	 * @return  mixed  Null if operation was satisfactory, otherwise returns an error string
	 *
	 * @see     JTable::bind
	 * @since   11.1
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['attribs']) && is_array($array['attribs']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check function
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @see     JTable::check
	 * @since   11.1
	 */
	public function check()
	{
		// Check the publish down date is not earlier than publish up.
		// These are used to determine when a lead is an active lead or if 
		// He is just an entry in the system
		if ($this->publish_down > $this->_db->getNullDate() && $this->publish_down < $this->publish_up)
		{
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}

		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->notes))
		{
			// Only process if not empty
			$bad_characters = array("\n", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->notes); // remove bad characters
			$keys = explode("\r", $after_clean); // create array using commas as delimiter
			$clean_keys = array();

			foreach ($keys as $key)
			{
				if (trim($key))
				{
					// Ignore blank notes
					$clean_keys[] = trim($key);
				}
			}
			$this->note = implode("\r", $clean_keys); // put array back together delimited by ", "
		}

		// Set ordering
		if ($this->state < 0) {
			// Set ordering to 0 if state is inactive, archived, or trashed
			$this->ordering = 0;
		} elseif (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder($this->_db->quoteName('catid').'=' . $this->_db->Quote($this->catid).' AND state>=0');
		}


		return true;
	}

	/**
	 * Overrides JTable::store to set modified data and user id if exists.
	 * Before the model allows a store it will first attempt to register and activate
	 * a new user, so we can check for user id to add modifications
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.1
	 */
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
			// New lead. A lead created and created_by field can be set by a user,
			// so we don't touch either of these if they are set.
			if (!intval($this->created))
			{
				$this->created = $date->toSql();
			}

			if (empty($this->created_by))
			{
				$this->created_by = $user->get('id');
			}
		}
		if(!parent::store($updateNulls)){
			// Some error happened just return false
			return false;
		}

		// Lets build user info, property info, and contact details to do a store
		// import the necessary tables... 
		// @Note: we already have some set (#__leads, #__property_info, #__users)
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_contact/tables');

		$stores = array(
			'user'=> JTable::getInstance('User','JTable'),
			'contact' => JTable::getInstance('Contact','ContactTable'),
			//'property' => JTable::getInstance('Property','RealtorTable')
		);
		$results = new JObject();
		foreach($stores as $key => &$store){
			// Prime some default values
			switch($key){
				case 'contact':
					$store->created_by = '';
					$store->params = array();
				case 'user':
					$store->username = '';
				default:
					$store->name = '';
					$store->email = '';
					break;
			}
			if($store->check()){
				$results->def($key, $store->store());
			}
			// NOTE: We dont have to translate text unless its global since the language files wont be loaded
			if($results->get($key, null) === null){
				$error = $store->getError();
				switch($error){
					//User table stuff
					case JText::_('JLIB_DATABASE_ERROR_PLEASE_ENTER_YOUR_NAME'):
					case JText::_('JLIB_DATABASE_ERROR_PLEASE_ENTER_A_USER_NAME'):
					case JText::_('JLIB_DATABASE_ERROR_VALID_AZ09'):
					case JText::_('JLIB_DATABASE_ERROR_USERNAME_INUSE'):
					case JText::_('JLIB_DATABASE_ERROR_USERNAME_CANNOT_CHANGE'):
					// Contact table stuff
					case 'COM_CONTACT_WARNING_PROVIDE_VALID_NAME';
					case 'COM_CONTACT_WARNING_SAME_NAME';
					$store->name = '';// TODO: auto-generate username
					$store->username = '';// TODO: auto-generate username
					break;

					// Contact details stuff
					case 'COM_CONTACT_WARNING_PROVIDE_VALID_URL';
					return false;
					break;
					case 'COM_CONTACT_WARNING_CATEGORY';
					// TODO: find default category for a lead! (@params)				
					break;

					/* user table stuff */
					case JText::_('JLIB_DATABASE_ERROR_VALID_MAIL'):
					case JText::_('JLIB_DATABASE_ERROR_EMAIL_INUSE'):
					// Email address has an issue... it is either in the system 
					// or simply invalid
					return false;
					default:
					break;
				}
				// Try to store it one more time since we've changed some values around
				if($store->check()){
					$results->def($key, $store->store());
				}else{
					return false;
				}
			}
		}
		
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table. The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * The method is named publish for compatibility but it should be named assign
	 * since technically we're changing the status of whether a lead has been assigned 
	 * to an agent or not.
	 *
	 * At this point the lead should be in a "featured" state where it is currently available for grabs
	 * - eg. column "featured" [0 = unfeatured, 1 = featured]
	 * - eg. column "state" [0 = assigned (a.k.a. unpublished), 1 = unassigned (a.k.a. published)]
	 * @param   mixed    $pks     An optional array of primary key values to update.  If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.1
	 */
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

	/**
	 * Converts record to CSV
	 *
	 * @param   boolean  $mapKeysToText  Map foreign keys to text values
	 * @return  string  Record in Excel format
	 * @since   12.1
	 */
	public function toExcel($mapKeysToText = false)
	{
		if ($mapKeysToText)
		{
			// Get the JDatabaseQuery object
			$query = $this->_db->getQuery(true);

			$query->select($this->_db->quoteName('name'));
			$query->from($this->_db->quoteName('#__categories'));
			$query->where($this->_db->quoteName('id') . ' = ' . (int) $this->catid);
			$this->_db->setQuery($query);
			$this->catid = $this->_db->loadResult();

			$query->clear();
			$query->select($this->_db->quoteName('name'));
			$query->from($this->_db->quoteName('#__users'));
			$query->where($this->_db->quoteName('id') . ' = ' . (int) $this->created_by);
			$this->_db->setQuery($query);
			$this->created_by = $this->_db->loadResult();
		}
		// TODO: Import the excel library and prepare for output

	}
}
