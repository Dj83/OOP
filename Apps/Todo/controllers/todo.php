<?php

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class ToDoControllerToDo extends JControllerForm
{
	protected $view_item = 'form';
	protected $view_list = 'categories';

	public function add()
	{
		if (!parent::add()) {
			$this->setRedirect($this->getReturnPage());
		}
	}

	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('catid'), 'int');
		$allow		= null;

		if ($categoryId) {
			$allow	= $user->authorise('core.create', 'com_todo.category.'.$categoryId);
		}
		if ($allow === null) {
			return parent::allowAdd();
		}
		else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$asset		= 'com_todo.todo.'.$recordId;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			return true;
		}
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', $asset)) {
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_by']) ? $data['created_by'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);
				if (empty($record)) {
					return false;
				}
				$ownerId = $record->created_by;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	public function cancel($key = 't_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	public function edit($key = null, $urlVar = 't_id')
	{
		$result = parent::edit($key, $urlVar);

		return $result;
	}

	public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 't_id')
	{
		// Need to override the parent method completely.
		$tmpl		= JRequest::getCmd('tmpl');
		$layout		= JRequest::getCmd('layout', 'edit');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		$append .= '&layout=edit';

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= JRequest::getInt('Itemid');
		$return	= $this->getReturnPage();
		$catId = JRequest::getInt('catid', null, 'get');

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		if($catId) {
			$append .= '&catid='.$catId;
		}

		if ($return) {
			$append .= '&return='.base64_encode($return);
		}

		return $append;
	}

	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}

	protected function postSaveHook(JModel &$model, $validData)
	{
		$task = $this->getTask();

		if ($task == 'save') {
			// TODO: Remove this because technically the dialog brings up the form for a user, no reason to go to the page directly
			// Unless of course the user has javascript turned off for some reason.
			$this->setRedirect(JRoute::_('index.php?option=com_todo&view=category&id='.$validData['catid'], false));
		}
	}

	public function save($key = null, $urlVar = 't_id')
	{
		//require_once JPATH_ADMINISTRATOR.'/components/com_todo/helpers/todo.php';
		$result = parent::save($key, $urlVar);
		if ($result) {
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
}