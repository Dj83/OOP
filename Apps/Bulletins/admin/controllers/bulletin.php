<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class BulletinsControllerBulletin extends JControllerForm
{
	protected $text_prefix = 'COM_BULLETINS_BULLETIN';

	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId)
		{
			// If the category has been passed in the URL check it.
			$allow	= $user->authorise('core.create', $this->option . '.category.' . $categoryId);
		}

		if ($allow === null)
		{
			// In the absence of better information, revert to the component permissions.
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;

		if ($recordId)
		{
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId)
		{
			// The category has been set. Check the category permissions.
			return $user->authorise('core.edit', $this->option . '.category.' . $categoryId);
		}
		else
		{
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}
	}
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel('Bulletin', 'BulletinsModel', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_bulletins&view=bulletins' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
	public function postSaveHook(&$model,$validData)
	{
		//if($validData['id'] == 0){
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_messages/tables');
	
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
				$message->priority = isset($validData['priority']) ? intval($validData['subject']) : 0;
				$message->subject = isset($validData['subject']) ? (string) $validData['subject'] : '';
				$message->message = isset($validData['message']) ? JFilterInput::getInstance()->clean($validData['message'], 'string') : '';
	
				if ($message->check()) {
					$status = $message->store();
				}
		
				if (!(isset($status)) || !$status) {
					$this->setError(42, JText::sprintf('%s, %s', $message->getError(),$receiver_id));
				}
			}
				//exit(0);
		//}
	}
}