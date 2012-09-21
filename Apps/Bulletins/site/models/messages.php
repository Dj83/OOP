<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class BulletinsModelMessages extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'message_id', 'a.id',
				'subject', 'a.subject',
				'state', 'a.state',
				'user_id_from', 'a.user_id_from',
				'user_id_to', 'a.user_id_to',
				'date_time', 'a.date_time',
				'priority', 'a.priority',
			);
		}

		parent::__construct($config);
	}

	public function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('site');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		// List state information.
		parent::populateState('a.date_time', 'desc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.state');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*, '.
				'u.name AS user_from'
			)
		);
		$query->from('#__messages AS a');

		// Join over the users for message owner.
		$query->join('INNER', '#__users AS u ON u.id = a.user_id_from');
		$query->where('a.user_id_to = '.(int) $user->get('id'));

		// Filter by published state.
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('a.state = '.(int) $state);
		}
		elseif ($state === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in subject or message.
		$search = $this->getState('filter.search');

		if (!empty($search)) {
			$search = $db->Quote('%'.$db->escape($search, true).'%', false);
			$query->where('a.subject LIKE '.$search.' OR a.message LIKE '.$search);
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.date_time')).' '.$db->escape($this->getState('list.direction', 'DESC')));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}