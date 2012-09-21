<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');


require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/subscriptions.php';

/**
 * Item Model for a Subscription.
 *
 * @package		Sold.Administrator
 * @subpackage	com_subscription
 * @since		2.5
 */
class SubscriptionsModelSubscription extends JModelAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	2.5
	 */
	protected $text_prefix = 'COM_SUBSCRIPTION';

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_users.subscription', 'subscription', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		if(empty($data)){
			$data = $this->loadFormData();
		}

		$model = JModel::getInstance('Features', 'SubscriptionsModel', array('ignore_request'=>true));
		$features = $model->getItems();
		if(!empty($features)){

			$addform = new JXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'features');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'features');
			$fieldset->addAttribute('description', 'COM_SUBSCRIPTIONS_SUBSCRIPTION_FEATURES_FIELDSET_DESC');
			foreach($features as $feature){
				$field = $fieldset->addChild('field');
				$fieldtype = isset($feature->fieldtype) ? (string) $feature->fieldtype : 'text';
				$name = strtolower(str_replace(" ", "_",trim((string) $feature->label)));
				$field->addAttribute('name',$name);
				$field->addAttribute('label',(string) $feature->label);
				$field->addAttribute('description',(string) $feature->message);
				$field->addAttribute('type', $fieldtype);
				if(isset($data->features)){
					$value = $data->features->get($name,'');
				}else{
					$value = '';
				}
				if($fieldtype == 'radio'){
					$no = $field->addChild('option','JNO');
					$no->addAttribute('value', '0');
					$yes = $field->addChild('option','JYES');
					$yes->addAttribute('value', '1');
				}else{
					$field->addAttribute('value', $value);
				}
				$form->load($addform,false);
				$form->setValue($name, 'features', $value);
			}
			$form->load($addform,false);
		}

		$input = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($input->get('s_id'))
		{
			$id =  $input->get('s_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id =  $input->get('id', 0);
		}
		// Determine correct permissions to check.


		if ($this->getState('subscription.id'))
		{
			$id = $this->getState('subscription.id');
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
			// Existing record. Can only edit own subscritions in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing subscription.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_subscriptions.subscription.'.(int) $id))
		|| ($id == 0 && !$user->authorise('core.edit.state', 'com_subscriptions')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('featured', 'disabled', 'true');
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a subscription the user can edit.
			$form->setFieldAttribute('featured', 'filter', 'unset');
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');

		}

		return $form;
	}
	protected function populateState()
	{
		parent::populateState();

		$id = JFactory::getApplication()->input->get('s_id', 0, 'int');
		if($id){
			$this->setState('subscription.id', $id);
		}
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param	JTable	A JTable object.
	 *
	 * @return	void
	 * @since	2.5
	 */
	protected function prepareTable(&$table)
	{
		// Set the publish date to now
		$db = $this->getDbo();
		if($table->state == 1 && intval($table->publish_up) == 0) {
			$table->publish_up = JFactory::getDate()->toSql();
		}

		// Reorder the subscriptions within the category so the new subscription is first
		if (empty($table->id)) {
			$table->reorder('catid = '.(int) $table->catid.' AND state >= 0');
		}
	}

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 *
	 * @return	JTable	A database object
	*/
	public function getTable($name = 'Subscription', $prefix = 'SubscriptionsTable', $options = array())
	{
		return JTable::getInstance($name, $prefix, $options);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the features field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->features);
			$item->features = $registry->toArray();

			// Convert the metadata field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->metadata);
			$item->metadata = $registry->toArray();
		}
		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	2.5
	 */
	protected function loadFormData()
	{
		// Get the application
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_subscriptions.edit.subscription.data', array());

		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('subscription.id') == 0)
			{
				$data->set('catid', $app->input->get('catid', $app->getUserState('com_users.subscriptions.filter.category_id'), 'int'));
			}

			//$data->id = $app->input->get('s_id', 0, 'int');
			$data->features = new JObject($data->features);
			//var_dump($data->features);

		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param	array	The form data.
	 *
	 * @return	boolean	True on success.
	 * @since	2.5
	 */
	public function save($data)
	{
		// Initialise variables.
		$pk		= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('subscription.id');
		$table	= $this->getTable();
		$isNew	= empty($pk);

		if (!$table->bind($data)) {
			$this->setError($table->getError());

			return false;
		}

		// JTableCategory doesn't bind the features, so we need to do that by hand.
		if (isset($data['features']) && is_array($data['features'])) {
			$registry = new JRegistry();
			$registry->loadArray($data['features']);
			$table->features = $registry->toString();
			// This will give us INI format.
		}
		unset($registry);

		if (!$table->check()) {
			$this->setError($table->getError());

			return false;
		}

		if (!$table->store()) {
			$this->setError($table->getError());

			return false;
		}

		$this->setState('subscription.id', $table->id);
		return true;
	}

	/**
	 * Custom clean the cache of component and modules
	 *
	 * @since	2.5
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_subscriptions');
		parent::cleanCache('mod_subscriptions');
		parent::cleanCache('mod_subscriptions_categories');
		parent::cleanCache('mod_subscriptions_category');
		parent::cleanCache('mod_subscriptions_popular');
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 *
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	2.5
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to delete the record. Defaults to the permission set in the component.
	 * @since	2.5
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return ;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_subscriptions.subscription.'.(int) $record->id);
		}
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param	object	$record	A record object.
	 *
	 * @return	boolean	True if allowed to change the state of the record. Defaults to the permission set in the component.
	 * @since	2.5
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing subscription.
		if (!empty($record->id)) {
			return $user->authorise('core.edit.state', 'com_subscription.subscription.'.(int) $record->id);
		}
		// New subscription lets check against the category.
		elseif (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_subscription.category.'.(int) $record->catid);
		}

		// Default to component settings if neither subscription nor category known.
		else {
			return parent::canEditState('com_subscription');
		}
	}

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $value     The new category.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 * @note		   This method is borrowed from the Platform Suite
	 * @since	11.1
	 */
	protected function batchCopy($value, $pks, $contexts)
	{
		$categoryId = (int) $value;

		$table = $this->getTable();
		$i = 0;

		// Check that the category exists
		if ($categoryId)
		{
			$categoryTable = JTable::getInstance('Category');
			if (!$categoryTable->load($categoryId))
			{
				if ($error = $categoryTable->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
					return false;
				}
			}
		}

		if (empty($categoryId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_MOVE_CATEGORY_NOT_FOUND'));
			return false;
		}

		// Check that the user has create permission for the component
		$extension = JFactory::getApplication()->input->get('option', '');
		$user = JFactory::getUser();
		if (!$user->authorise('core.create', $extension . '.category.' . $categoryId))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_CREATE'));
			return false;
		}

		// Parent exists so we let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$table->reset();

			// Check that the row actually exists
			if (!$table->load($pk))
			{
				if ($error = $table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// Alter the title & alias
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->title);
			$table->title = $data['0'];
			$table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$table->id = 0;

			// New category ID
			$table->catid = $categoryId;

			// TODO: Deal with ordering?
			//$table->ordering	= 1;

			// Get the featured state
			$featured = $table->featured;

			// Check the row.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Store the row.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}

			// Get the new item ID
			$newId = $table->get('id');

			// Add the new ID to the array
			$newIds[$i]	= $newId;
			$i++;

			// Check if the subscription was featured and update the #__subscription_frontpage table
			if ($featured == 1)
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				$query->insert($db->quoteName('#__subscriptions_frontpage'));
				$query->values($newId . ', 0');
				$db->setQuery($query);
				$db->query();
			}
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Method to toggle the featured setting of subscriptions.
	 *
	 * @param	array	The ids of the items to toggle.
	 * @param	int		The value to toggle to.
	 *
	 * @return	boolean	True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		JArrayHelper::toInteger($pks);

		if (empty($pks)) {
			$this->setError(JText::_('COM_SUBSCRIPTION_NO_ITEM_SELECTED'));
			return false;
		}

		//$table = $this->getTable('Featured', 'SubscriptionTable');

		try {
			$db = $this->getDbo();

			$db->setQuery(
				'UPDATE #__subscriptions' .
				' SET featured = '.(int) $value.
				' WHERE id IN ('.implode(',', $pks).')'
			);
			if (!$db->query()) {
				throw new Exception($db->getErrorMsg());
			}

			if ((int)$value == 0) {
				// Adjust the mapping table.
				// Clear the existing features settings.
				$db->setQuery(
					'DELETE FROM #__subscriptions_frontpage' .
					' WHERE subscription_id IN ('.implode(',', $pks).')'
				);
				if (!$db->query()) {
					throw new Exception($db->getErrorMsg());
				}
			} else {
				// first, we find out which of our new featured subscriptions are already featured.
				$query = $db->getQuery(true);
				$query->select('f.subscription_id');
				$query->from('#__subscriptions_frontpage AS f');
				$query->where('subscription_id IN ('.implode(',', $pks).')');
				//echo $query;
				$db->setQuery($query);

				if (!is_array($old_featured = $db->loadColumn())) {
					throw new Exception($db->getErrorMsg());
				}

				// we diff the arrays to get a list of the subscriptions that are newly featured
				$new_featured = array_diff($pks, $old_featured);

				// Featuring.
				$tuples = array();
				foreach ($new_featured as $pk) {
					$tuples[] = '('.$pk.', 0)';
				}
				if (count($tuples)) {
					$db->setQuery(
						'INSERT INTO #__subscriptions_frontpage ('.$db->quoteName('subscription_id').', '.$db->quoteName('ordering').')' .
						' VALUES '.implode(',', $tuples)
					);
					if (!$db->query()) {
						$this->setError($db->getErrorMsg());
						return false;
					}
				}
			}

		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		//$table->reorder();

		$this->cleanCache();

		return true;
	}
}