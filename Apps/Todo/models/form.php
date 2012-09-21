<?php

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_todo/models/todo.php';

class ToDoModelForm extends ToDoModelToDo
{
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = JRequest::getInt('t_id');
		$this->setState('todo.id', $pk);

		$this->setState('todo.catid', JRequest::getInt('catid'));

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params	= $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', JRequest::getCmd('layout'));
	}

	public function getItem($itemId = null)
	{
		// Initialise variables.
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('todo.id');
		$table = $this->getTable();
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError()) {
			$this->setError($table->getError());
			return false;
		}

		$properties = $table->getProperties(1);
		$value = JArrayHelper::toObject($properties, 'JObject');

		// TODO: Create attrib column at database then convert attrib field to Registry.
		$value->params = new JRegistry;
		//$value->params->loadString($value->attribs);

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_todo.todo.'.$value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			$value->params->def('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_by) {
				$value->params->def('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId) {
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else {
			// New item.
			$catId = (int) $this->getState('todo.catid');

			if ($catId) {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_todo.category.'.$catId));
				$value->catid = $catId;
			}
			else {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_todo'));
			}
		}

		return $value;
	}

	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}