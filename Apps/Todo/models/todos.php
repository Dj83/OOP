<?php 
defined('_JEXEC') or die('Restricted Access');

jimport('joomla.application.component.modellist');
jimport('joomla.application.component.helper');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

class ToDoModelToDos extends JModelList
{	//protected $cache = array();
	//protected $context = null;
	//protected $filter_fields = array();
	//protected $query = array();

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'subject', 'a.subject',
				'item_id', 'a.item_id',
				'item_alias', 'a.item_alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		$value = JRequest::getUInt('limit', $app->getCfg('list_limit', 0));
		$this->setState('list.limit', $value);

		$value = JRequest::getUInt('limitstart', 0);
		$this->setState('list.start', $value);

		$orderCol	= JRequest::getCmd('filter_order', 'a.ordering');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		$user		= JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_todo')) &&  (!$user->authorise('core.edit', 'com_todo'))){
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language', $app->getLanguageFilter());

		// process show_noauth parameter
		if (!$params->get('show_noauth')) {
			$this->setState('filter.access', true);
		}
		else {
			$this->setState('filter.access', false);
		}

		$this->setState('layout', JRequest::getCmd('layout'));
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.published');
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.todo_id');
		$id .= ':'.$this->getState('filter.todo_id.include');
		$id .= ':'.$this->getState('filter.category_id');
		$id .= ':'.$this->getState('filter.category_id.include');
		$id .= ':'.$this->getState('filter.date_filtering');
		$id .= ':'.$this->getState('filter.date_field');
		$id .= ':'.$this->getState('filter.start_date_range');
		$id .= ':'.$this->getState('filter.end_date_range');
		$id .= ':'.$this->getState('filter.relative_date');

		return parent::getStoreId($id);
	}
	public function getStart()
	{
		return $this->getState('list.start');
	}

	/**
	 * Get a list of todos.
	 *
	 * @return	array
	 * @since	2.4
	 */
	public function getItems()
	{
		if (!isset($this->cache['items'])) {
			$this->cache['items'] = parent::getItems();

			foreach ($this->cache['items'] as &$item)
			{
				$parameters = new JRegistry;
				// TODO:  I have to add the params column still in the database as I forgot about it lol
				//$parameters->loadString($item->params);
				$item->params = $parameters;
			}
		}
		return $this->cache['items'];
	}

	/**
	 * Gets a list of tasks
	 *
	 * @return	array	An array of todo objects.
	 * @since	2.5
	 */
	protected function getListQuery()
	{
		$db			= $this->getDbo();
		$query		= $db->getQuery(true);
		$ordering	= $this->getState('filter.ordering');
		$search		= $this->getState('filter.search');
		$categoryId = $this->getState('filter.category_id');
		$nullDate	= $db->quote($db->getNullDate());

		$query->select($this->getState(
				'list.select',
				'a.id, a.item_id, a.item_alias, a.subject, a.body, ' .
				'a.checked_out, a.checked_out_time, ' .
				'a.catid, a.created, a.created_by, ' .
				// use created if modified is 0
				'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
					'a.modified_by, uam.name as modified_by_name,' .
				// use created if publish_up is 0
				'CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END as publish_up,' .
					'a.publish_down, a.access'
			)
		);
		$query->from('#__todos as a');
		// Filter on users
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');
		$query->join('LEFT', '#__users AS uam ON uam.id = a.modified_by');

		$query->where('a.state=1');
		$query->where('('.$query->currentTimestamp().' >= a.publish_up OR a.publish_up = '.$nullDate.')');
		$query->where('('.$query->currentTimestamp().' <= a.publish_down OR a.publish_down = '.$nullDate.')');

		// Filter by a single or group of categories
		$categoryId = $this->getState('filter.category_id');
		$catid		= $this->getState('filter.category_id', array());

		if (is_numeric($categoryId)) {
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'a.catid '.$type.(int) $categoryId;

			if ($includeSubcategories) {
				$levels = (int) $this->getState('filter.max_category_levels', '1');
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = '.(int) $categoryId);
				$subQuery->where('sub.level <= this.level + '.$levels);

				// Add the subquery to the main query
				$query->where('('.$categoryEquals.' OR a.catid IN ('.$subQuery->__toString().'))');
			}
			else {
				$query->where($categoryEquals);
			}
		}
		elseif ((is_array($categoryId)) && (count($categoryId) > 0)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			if($categoryId != '0') {
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('a.catid '.$type.' ('.$categoryId.')');
			}
		}

		// TODO: Filter by some more stuff:
		//if ($this->getState('filter.XXX')) {
		//	$query->where('a.XXX in (' . $db->Quote('XXX') . ',' . $db->Quote('*') . ')');
		//}

		$query->order('a.ordering');
		return $query;
	}
}