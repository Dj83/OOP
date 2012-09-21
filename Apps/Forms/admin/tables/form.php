<?php

defined('_JEXEC') or die;

class FormsTableForm extends JTable
{
	public function __construct(& $db)
	{
		parent::__construct('#__forms_details', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		if (isset($array['attribs']) && is_array($array['attribs'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string) $registry;
		}

		if (isset($array['metadata']) && is_array($array['metadata'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['metadata']);
			$array['metadata'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		// Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}

		$date	= JFactory::getDate();
		$user	= JFactory::getUser();
		if ($this->id) {
			// Existing item
			$this->modified		= $date->toSql();
			$this->modified_by	= $user->get('id');
		} else {
			// New newsfeed. A feed created and created_by field can be set by the user,
			// so we don't touch either of these if they are set.
			if (!intval($this->created)) {
				$this->created = $date->toSql();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
		}
		// Verify that the alias is unique
		$table = JTable::getInstance('Form', 'FormsTable');
		if ($table->load(array('alias'=>$this->alias, 'catid'=>$this->catid)) && ($table->id != $this->id || $this->id==0)) {
			$this->setError(JText::_('COM_CONTACT_ERROR_UNIQUE_ALIAS'));
			return false;
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	function check()
	{
		if (JFilterInput::checkAttribute(array ('href', $this->webpage))) {
			$this->setError(JText::_('COM_FORMS_WARNING_PROVIDE_VALID_URL'));
			return false;
		}

		/** check for valid name */
		if (trim($this->name) == '') {
			$this->setError(JText::_('COM_FORMS_WARNING_PROVIDE_VALID_NAME'));
			return false;
		}
				/** check for existing name */
		$query = 'SELECT id FROM #__contact_details WHERE name = '.$this->_db->Quote($this->name).' AND catid = '.(int) $this->catid;
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->setError(JText::_('COM_FORMS_WARNING_SAME_NAME'));
			return false;
		}

		if (empty($this->alias)) {
			$this->alias = $this->name;
		}
		$this->alias = JApplication::stringURLSafe($this->alias);
		if (trim(str_replace('-', '', $this->alias)) == '') {
			$this->alias = JFactory::getUser()->alias . JFactory::getDate()->format("Y-m-d-H-i-s");
		}
		/** check for valid category */
		if (trim($this->catid) == '') {
			$this->setError(JText::_('COM_FORMS_WARNING_CATEGORY'));
			return false;
		}

		// Check the publish down date is not earlier than publish up.
		if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
			$this->setError(JText::_('JGLOBAL_START_PUBLISH_AFTER_FINISH'));
			return false;
		}

		// clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey)) {
			// only process if not empty
			$bad_characters = array("\n", "\r", "\"", "<", ">"); // array of characters to remove
			$after_clean = JString::str_ireplace($bad_characters, "", $this->metakey); // remove bad characters
			$keys = explode(',', $after_clean); // create array using commas as delimiter
			$clean_keys = array();
			foreach($keys as $key) {
				if (trim($key)) {  // ignore blank keywords
					$clean_keys[] = trim($key);
				}
			}
			$this->metakey = implode(", ", $clean_keys); // put array back together delimited by ", "
		}

		// clean up description -- eliminate quotes and <> brackets
		if (!empty($this->metadesc)) {
			// only process if not empty
			$bad_characters = array("\"", "<", ">");
			$this->metadesc = JString::str_ireplace($bad_characters, "", $this->metadesc);
		}
		return true;
	}
}