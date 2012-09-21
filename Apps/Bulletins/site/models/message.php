<?php
defined('_JEXEC') or die;

// Base this model on the backend version.
jimport('joomla.application.component.modeladmin');

class BulletinsModelMessage extends JModelAdmin
{
	protected $item;
	public function getTable($type = 'Message', $prefix = 'BulletinsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	protected function populateState()
	{
		parent::populateState();

		$user = JFactory::getUser();
		$this->setState('user.id', $user->get('id'));

		$messageId = (int) JRequest::getInt('id');
		$this->setState('message.id', $messageId);

		$replyId = (int) JRequest::getInt('reply_id');
		$this->setState('reply.id', $replyId);
	}
	public function getItem($pk = null)
	{
		if (!isset($this->item))
		{
			if ($this->item = parent::getItem($pk)) {
				// Prime required properties.
				if (empty($this->item->message_id))
				{
					// Prepare data for a new record.
					if ($replyId = $this->getState('reply.id'))
					{
						// If replying to a message, preload some data.
						$db		= $this->getDbo();
						$query	= $db->getQuery(true);

						$query->select('subject, user_id_from');
						$query->from('#__messages');
						$query->where('message_id = '.(int) $replyId);
						$message = $db->setQuery($query)->loadObject();

						if ($error = $db->getErrorMsg())
						{
							$this->setError($error);
							return false;
						}


						$this->item->set('user_id_to', $message->user_id_from);
						$re = JText::_('COM_BULLETINS_RE');
						if (stripos($message->subject, $re) !== 0) {
							$this->item->set('subject', $re.$message->subject);
						}
					}
				}
				elseif ($this->item->user_id_to != JFactory::getUser()->id)
				{
					$this->setError(JText::_('JERROR_ALERTNOAUTHOR'));
					return false;
				}
				else {
					// Mark message read
					$db		= $this->getDbo();
					$query	= $db->getQuery(true);
					$query->update('#__messages');
					$query->set('state = 1');
					$query->where('message_id = '.$this->item->message_id);
					$db->setQuery($query)->query();
				}
			}

			// Get the user name for an existing messae.
			if ($this->item->user_id_from && $fromUser = new JUser($this->item->user_id_from)) {
				$this->item->set('from_user_name', $fromUser->name);
			}

			if(isset($this->item->xreference) && $this->item->xreference > 0){
				// Bind the attachments
				$db		= $this->getDbo();
				$query	= $db->getQuery(true);
	
				$query->select('attachments AS attachments');
				$query->from('#__bulletins');
				$query->where('id = '.(int) $this->item->xreference);
				$db->setQuery($query);
				$result = $db->loadObject();
	
				if ($error = $db->getErrorMsg())
				{
					$this->setError($error);
					return false;
				}
				if(isset($result->attachments)){
					$attachment = new JRegistry;
					$attachment->loadString($result->attachments);
					$this->item->set('attachments', $attachment->toArray());
				}
				
			}
		}
		return $this->item;
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_bulletins.message', 'message', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_bulletins.edit.message.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		$table = $this->getTable();

		// Bind the data.
		if (!$table->bind($data)) {
			$this->setError($table->getError());
			return false;
		}

		// Assign empty values.
		if (empty($table->user_id_from)) {
			$table->user_id_from = JFactory::getUser()->get('id');
		}
		if (intval($table->date_time) == 0) {
			$table->date_time = JFactory::getDate()->toSql();
		}

		// Check the data.
		if (!$table->check()) {
			$this->setError($table->getError());
			return false;
		}

		// Load the recipient user configuration.
		JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_messages/models');
		$model = JModel::getInstance('Config', 'MessagesModel', array('ignore_request' => true));
		$model->setState('user.id', $table->user_id_to);
		$config = $model->getItem();
		if (empty($config)) {
			$this->setError($model->getError());
			return false;
		}

		if ($config->get('locked', false)) {
			$this->setError(JText::_('COM_BULLETINS_ERR_SEND_FAILED'));
			return false;
		}

		// Store the data.
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;
		}

/*		if ($config->get('mail_on_new', true)) {
			// Load the user details (already valid from table check).
			$fromUser = JUser::getInstance($table->user_id_from);
			$toUser = JUser::getInstance($table->user_id_to);
			$debug = JFactory::getConfig()->get('debug_lang');
			$default_language = JComponentHelper::getParams('com_languages')->get('administrator');
			$lang = JLanguage::getInstance($toUser->getParam('admin_language', $default_language), $debug);
			$lang->load('com_messages', JPATH_ADMINISTRATOR);

			$siteURL	= JURI::root() . 'administrator/index.php?option=com_messages&view=message&message_id='.$table->message_id;
			$sitename	= JFactory::getApplication()->getCfg('sitename');

			$subject	= sprintf ($lang->_('COM_MESSAGES_NEW_MESSAGE_ARRIVED'), $sitename);
			$msg		= sprintf ($lang->_('COM_MESSAGES_PLEASE_LOGIN'), $siteURL);
			JFactory::getMailer()->sendMail($fromUser->email, $fromUser->name, $toUser->email, $subject, $msg);
		}
*/
		return true;
	}
}