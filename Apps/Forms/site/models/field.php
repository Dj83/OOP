<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/forms.php';

class FormsModelField extends JModelAdmin
{
	protected function populateState()
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;

		// Load state from the request.
		$pk = $jinput->get('fi_id');
		$this->setState('field.id', $pk);

		$this->setState('field.catid', $jinput->get('catid'));
		$this->setState('field.form_id',JRequest::getVar('fid'));

		$return = $jinput->get('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $jinput->get('layout'));
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

	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('field.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		// Convert attrib field to Registry.
		$value->params = new JRegistry;
		$value->params->loadString($value->attribs);

		// Convert the custom fields to an array.
		$registry = new JRegistry;
		$registry->loadString($value->custom);
		$value->custom = $registry->toArray();

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_forms.field.'.$value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			$value->params->set('access-edit', true);
		}

		// Check edit state permission.
		if ($itemId) {
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else {
			// New item.
			$catId = (int) $this->getState('field.catid');

			if ($catId) {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_forms.category.'.$catId));
				$value->catid = $catId;
			}
			else {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_forms'));
			}
		}

		return $value;
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
			//$form->setFieldAttribute('catid', 'action', 'core.edit');
			// Existing record. Can only edit own articles in selected categories.
			//$form->setFieldAttribute('catid', 'action', 'core.edit.own');
		}
		else
		{
			// New record. Can only create in selected categories.
			//$form->setFieldAttribute('catid', 'action', 'core.create');
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

	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}