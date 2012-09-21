<?php
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_bulletins/models/bulletin.php';

class BulletinsModelForm extends BulletinsModelBulletin
{
	protected function populateState()
	{
		$app = JFactory::getApplication();

		// Load state from the request.
		$pk = JRequest::getInt('b_id');
		$this->setState('bulletin.id', $pk);

		$this->setState('bulletin.catid', JRequest::getInt('catid'));

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
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('bulletin.id');

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
		$value->params = new JRegistry;

		$value->usergroups = explode(",", $value->usergroups);

		$registry = new JRegistry;
		$registry->loadString($value->attachments);
		$value->attachments = $registry->toArray();

		// Compute selected asset permissions.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$asset	= 'com_bulletins.bulletin.'.$value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset)) {
			$value->params->set('access-edit', true);
		}
		// Now check if edit.own is available.
		elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
			// Check for a valid user and that they are the owner.
			if ($userId == $value->created_by) {
				$value->params->set('access-edit', true);
			}
		}

		// Check edit state permission.
		if ($itemId) {
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else {
			// New item.
			$catId = (int) $this->getState('bulletin.catid');

			if ($catId) {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_bulletin.category.'.$catId));
				$value->catid = $catId;
			}
			else {
				$value->params->set('access-change', $user->authorise('core.edit.state', 'com_bulletin'));
			}
		}

		return $value;
	}
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
}