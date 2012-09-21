<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * User subscription model.
 *
 * @package     Sold.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class UsersModelSubscription extends JModelAdmin
{
	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   2.5
	 */
	public function getForm($data = array(), $loadData = true)
	{
		//var_dump($data);
		$form = $this->loadForm('com_users.subscription', 'subscription', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		if(empty($data)){
			$data = $this->loadFormData();
		}

		$model = JModel::getInstance('Features', 'UsersModel', array('ignore_request'=>true));
		$features = $model->getItems();
		if(!empty($features)){

			$addform = new JXMLElement('<form />');
			$fields = $addform->addChild('fields');
			$fields->addAttribute('name', 'features');
			$fieldset = $fields->addChild('fieldset');
			$fieldset->addAttribute('name', 'features');
			$fieldset->addAttribute('description', 'COM_USERS_SUBSCRIPTION_FEATURES_FIELDSET_DESC');
			foreach($features as $feature){
				$field = $fieldset->addChild('field');
				$fieldtype = isset($feature->fieldtype) ? (string) $feature->fieldtype : 'text';
				$name = strtolower(str_replace(" ", "_",trim((string) $feature->title)));
				$value = $data->features->get($name);
				$field->addAttribute('name',$name);
				$field->addAttribute('label',(string) $feature->title);
				$field->addAttribute('description',(string) $feature->message);
				$field->addAttribute('type', $fieldtype);
				if($fieldtype == 'radio'){
					$no = $field->addChild('option','JNO');
					$no->addAttribute('value', '0');
					$yes = $field->addChild('option','JYES');
					$yes->addAttribute('value', '1');
				}else{
					$field->addAttribute('value', '');
				}
			}
			$form->load($addform,false);
			foreach($features as $feature){
				$name = strtolower(str_replace(" ", "_",trim((string) $feature->title)));
				$form->setValue($name,'features', $data->features->get($name));
			}
		}
		//var_dump($form->getFormData());
		
		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			// Convert the params field to an array.
			$registry = new JRegistry;
			$registry->loadString($item->features);
			$item->features = $registry->toArray();
		}
		return $item;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  JTable  The table object
	 *
	 * @since   2.5
	 */
	public function getTable($name = 'Subscription', $prefix = 'UsersTable', $options = array())
	{
		return JTable::getInstance($name, $prefix, $options);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData()
	{
		// Get the application
		$app = JFactory::getApplication();

		// Check the session for previously entered form data.
		$data = $app->getUserState('com_users.edit.subscription.data', array());

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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 *
	 * @since   2.5
	 */
	protected function populateState()
	{
		parent::populateState();

		$id = JFactory::getApplication()->input->get('s_id', 0, 'int');
		if($id){
			$this->setState('subscription.id', $id);
		}
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   2.5
	 */
	public function save($data)
	{
		// Initialise variables.
		$pk		= (!empty($data['id'])) ? $data['id'] : (int) $this->getState('note.id');
		$table	= $this->getTable();
		$isNew	= empty($pk);

		if (!$table->bind($data)) {
			$this->setError($table->getError());

			return false;
		}

		// JTableCategory doesn't bind the params, so we need to do that by hand.
		if (isset($data['params']) && is_array($data['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($data['params']);
			$table->params = $registry->toString();
			// This will give us INI format.
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
}
