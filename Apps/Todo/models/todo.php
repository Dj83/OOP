<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

class ToDoModelToDo extends JModelItem
{
	protected $_context = 'com_todo.todo';

	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('todo.id', $pk);

		$offset = JRequest::getUInt('limitstart');
		$this->setState('list.offset', $offset);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		// TODO: Tune these values based on other permissions.
		$user		= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_todo')) &&  (!$user->authorise('core.edit.own', 'com_todo'))){
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

	public function &getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('todo.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {

			try {
				$db = $this->getDbo();
				$query = $db->getQuery(true);

				$query->select($this->getState(
					'item.select', 'a.id, a.subject, a.body, ' .

					'CASE WHEN badcats.id is null THEN a.state ELSE 0 END AS state, ' .
					'a.catid, a.created, a.created_by, ' .

					'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
					'a.modified_by, a.checked_out, a.checked_out_time, a.publish_up, a.publish_down, ' .
					'a.ordering, a.access'
					)
				);
				$query->from('#__todos AS a');

				// Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access');
				$query->join('LEFT', '#__categories AS c on c.id = a.catid');

				// Join on user table.
				$query->select('u.name AS author');
				$query->join('LEFT', '#__users AS u on u.id = a.created_by');
		
				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
				$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

				$query->where('a.id = ' . (int) $pk);

				// Filter by start and end dates.
				$nullDate = $db->Quote($db->getNullDate());
				$date = JFactory::getDate();

				$nowDate = $db->Quote($date->toSql());

				$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
				$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');

				$subquery = ' (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
				$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
				$subquery .= 'WHERE parent.extension = ' . $db->quote('com_todo');
				$subquery .= ' AND parent.published <= 0 GROUP BY cat.id)';
				$query->join('LEFT OUTER', $subquery . ' AS badcats ON badcats.id = c.id');

				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');

				if (is_numeric($published)) {
					$query->where('(a.state = ' . (int) $published . ' OR a.state =' . (int) $archived . ')');
				}

				$db->setQuery($query);

				$data = $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new Exception($error);
				}

				if (empty($data)) {
					return JError::raiseError(404, JText::_('COM_TODO_ERROR_TODOTASK_NOT_FOUND'));
				}

				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->state != $published) && ($data->state != $archived))) {
					return JError::raiseError(404, JText::_('COM_TODO_ERROR_TODOTASK_NOT_FOUND'));
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry;
				$registry->loadString($data->attribs);

				//$data->params = clone $this->getState('params');
				//$data->params->merge($registry);

				// Compute selected asset permissions.
				$user	= JFactory::getUser();

				if (!$user->get('guest')) {
					$userId	= $user->get('id');
					$asset	= 'com_todo.todo.'.$data->id;

					// Check general edit permission first.
					if ($user->authorise('core.edit', $asset)) {
						$data->params->set('access-edit', true);
					}
					// Now check if edit.own is available.
					elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
						// Check for a valid user and that they are the owner.
						if ($userId == $data->created_by) {
							$data->params->set('access-edit', true);
						}
					}
				}

				// Compute view access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					if ($data->catid == 0 || $data->category_access === null) {
						$data->params->set('access-view', in_array($data->access, $groups));
					}
					else {
						$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (JException $e)
			{
				if ($e->getCode() == 404) {
					// Need to go thru the error handler to allow Redirect to work.
					JError::raiseError(404, $e->getMessage());
				}
				else {
					$this->setError($e);
					$this->_item[$pk] = false;
				}
			}
		}

		return $this->_item[$pk];
	}
}