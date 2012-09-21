<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/forms.php';

class FormsModelField extends JModelAdmin
{
	protected $text_prefix = 'COM_FORMS';
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
			$data = $this->generateNewTitle($categoryId, $table->alias, $table->name);
			$table->name = $data['0'];
			$table->alias = $data['1'];

			// Reset the ID because we are making a copy
			$table->id = 0;

			// New category ID
			$table->catid = $categoryId;

			// TODO: Deal with ordering?
			//$table->ordering	= 1;

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
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	protected function canDelete($record)
	{
		if (!empty($record->id)) {
			if ($record->state != -2) {
				return ;
			}
			$user = JFactory::getUser();
			return $user->authorise('core.delete', 'com_forms.field.'.(int) $record->id);
		}
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		// Check for existing article.
		if (!empty($record->id)) {
			return $user->authorise('core.edit.state', 'ccom_forms.field.'.(int) $record->id);
		}
		// New article, so check against the category.
		elseif (!empty($record->catid)) {
			return $user->authorise('core.edit.state', 'com_forms.category.'.(int) $record->catid);
		}
		// Default to component settings if neither article nor category known.
		else {
			return parent::canEditState('com_forms');
		}
	}

	protected function prepareTable(&$table)
	{
		// Set the publish date to now
		$db = $this->getDbo();
		if($table->state == 1 && intval($table->publish_up) == 0) {
			$table->publish_up = JFactory::getDate()->toSql();
		}

		// Reorder the articles within the category so the new article is first
		if (empty($table->id)) {
			$table->reorder('catid = '.(int) $table->catid.' AND state >= 0');
		}
	}

	public function getTable($type = 'Field', $prefix = 'FormsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->attribs);
			$item->attribs = $registry->toArray();

			// Convert the custom fields to an array.
			$registry = new JRegistry;
			$registry->loadString($item->custom);
			$item->custom = $registry->toArray();
		}

		return $item;
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_forms.field', 'field', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}
		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('fi_id'))
		{
			$id =  $jinput->get('fi_id', 0);
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id =  $jinput->get('id', 0);
		}
		// Determine correct permissions to check.
		if ($this->getState('field.id'))
		{
			$id = $this->getState('field.id');
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit');
			// Existing record. Can only edit own articles in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('catid', 'action', 'core.create');
		}

		$user = JFactory::getUser();

		// Check for existing article.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_forms.field.'.(int) $id))
		|| ($id == 0 && !$user->authorise('core.edit.state', 'com_forms'))
		)
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is an article you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');

		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_forms.edit.field.data', array());

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('field.id') == 0) {
				$app = JFactory::getApplication();
				$data->set('catid', JRequest::getInt('catid', $app->getUserState('com_forms.fields.filter.category_id')));
			}
		}

		return $data;
	}

	public function save($data)
	{
		if (isset($data['custom']) && is_array($data['custom'])) {
			$registry = new JRegistry;
			$registry->loadArray($data['custom']);
			$data['custom'] = (string)$registry;
		
		}

		if (isset($data['attribs']) && is_array($data['attribs'])) {
			$registry = new JRegistry;
			$registry->loadArray($data['attribs']);
			$data['attribs'] = (string)$registry;
		
		}

		// Alter the title for save as copy
		if (JRequest::getVar('task') == 'save2copy') {
			list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['name']);
			$data['name']	= $title;
			$data['alias']	= $alias;
		}

		if (parent::save($data)) {
			return true;
		}

		return false;
	}

	protected function getReorderConditions($table)
	{
		$condition = array();
		$condition[] = 'catid = '.(int) $table->catid;
		return $condition;
	}

	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_forms');
	}
}
