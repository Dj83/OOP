<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class BulletinsControllerBulletin extends JControllerForm
{
	protected $view_item = 'form';
	protected $view_list = 'bulletins';

	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
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
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_bulletins.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
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
		$asset		= 'com_bulletins.bulletin.'.$recordId;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			return true;
		}

		// Fallback on edit.own.
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
		return parent::allowEdit($data, $key);
	}

	public function cancel($key = 'b_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	public function edit($key = null, $urlVar = 'b_id')
	{
		$result = parent::edit($key, $urlVar);

		return $result;
	}

	public function getModel($name = 'form', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'b_id')
	{
		// Need to override the parent method completely.
		$tmpl		= JRequest::getCmd('tmpl');
		$layout		= JRequest::getCmd('layout', 'edit');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		// TODO This is a bandaid, not a long term solution.
//		if ($layout) {
//			$append .= '&layout='.$layout;
//		}
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

	protected function postSaveHook(JModel &$model, $validData=array())
	{
		$db= JFactory::getDbo();
		$db->setQuery('ALTER TABLE '. $db->quote('#__messages'). ' ADD '. $db->quote('xreferences'). ' INT( 11 ) UNSIGNED NOT NULL DEFAULT \'0\' AFTER '. $db->quote('message'));

		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_messages/tables');
		if((int)$validData['id'] == 0){

			$acl = JFactory::getACL();
			$groups = isset($validData['usergroups']) ? $validData['usergroups'] : array();

			$recipients = array();
			foreach($groups as $group){
				$users = $acl->getUsersByGroup($group,false);

				foreach($users as $user){
					$recipients[$user] = true;
				}	
			}
			foreach($recipients as $receiver_id => $bool){
	
				$message = JTable::getInstance('Message', 'MessagesTable');
				if(!$message){
					$this->setError('COM_BULLETINGS_ERROR_MSGS');
					return false;
				}
				// Typecast all fields
				$message->user_id_from = isset($validData['modified_by']) ? 
					intval($validData['modified_by'])	: isset($validData['created_by']) ?
					intval($validData['created_by'])	: JFactory::getUser()->id;
				$message->user_id_to = (int)$receiver_id;
				$message->folder_id = '';

				$message->date_time = isset($validData['created']) ? $validData['created'] : new JDate('now');

				$message->state = '0';
				$message->priority = isset($validData['priority']) ? intval($validData['priority']) : 0;
				$message->subject = isset($validData['subject']) ? (string) $validData['subject'] : '';
				$message->message = isset($validData['message']) ? JFilterInput::getInstance()->clean($validData['message'], 'string') : '';
				//$message->xreference = isset($validData['id']) ? intval($validData['id']) : $model->getState('form.id');
	
				if ($message->check()) {
					$status = $message->store();
				}
				if (!(isset($status)) || !$status) {

					$this->setError(42, JText::sprintf('%s, %s', $message->getError(),$receiver_id));
				}
			}
		}
		$task = $this->getTask();

		if ($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_bulletins&view=bulletins', false));
		}
	}
	public function save($key = null, $urlVar = 'b_id')
	{
		// Load the backend helper for filtering.
		require_once JPATH_ADMINISTRATOR.'/components/com_bulletins/helpers/bulletins.php';

		$result = parent::save($key, $urlVar);

		// If ok, redirect to the return page.
		if ($result) {
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
}