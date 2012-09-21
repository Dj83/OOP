<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
class UsersModelSubscriptions extends JModelList
{
	/**
	 * Class constructor.
	 *
	 * @param  array  $config  An optional associative array of configuration settings.
	 *
	 * @since  2.5
	 */
	public function __construct($config = array())
	{
		// Set the list ordering fields.
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id','a.id,',
				'title','a.title,',
				'alias','a.alias,',
				'description','a.description,',
				'featured','a.featured,',
				'price','a.price,',
				'period','a.period,',
				'starting_price','a.starting_price,',
				'starting_period','a.starting_period,',
				'badge','a.badge,',
				'accesslevel','a.accesslevel,',
				'features','a.features,',
				'catid','a.catid,',
				'state','a.state,',
				'ordering','a.ordering,',
				'language','a.language,',
				'access','a.access,',
				'params','a.params,',
				'checked_out','a.checked_out,',
				'checked_out_time','a.checked_out_time,',
				'modified','a.modified,',
				'modified_by','a.modified_by,',
				'created','a.created,',
				'created_by','a.created_by,',
				'publish_up','a.publish_up,',
				'publish_down','a.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery  A JDatabaseQuery object to retrieve the data set.
	 *
	 * @since   2.5
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$section = $this->getState('filter.category_id');

		// Select the required fields from the table.
		$query->select(
			$this->getState('list.select',
				'a.id,' .
				'a.title,' .
				'a.alias,' .
				'a.description,' .
				'a.featured,'.
				'a.price,' .
				'a.period,' .
				'a.starting_price,' .
				'a.starting_period,' .
				'a.badge,' .
				'a.accesslevel,' .
				'a.features,' .
				'a.catid,' .
				'a.state,' .
				'a.ordering,' .
				'a.language,' .
				'a.access,' .
				'a.params,' .
				'a.checked_out,' .
				'a.checked_out_time,' .
				'a.modified_by,' .
				'a.created,' .
				'a.created_by,' .
				'a.publish_up,' .
				'a.publish_down'
			)
		);
		$query->from('#__user_subscriptions AS a');
		return $query;

		// Join over the category
		$query->select('c.title AS category_title, c.params AS category_params');
		$query->leftJoin('#__categories AS c ON c.id = a.catid');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->leftJoin('#__users AS uc ON uc.id = a.checked_out');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'price:') === 0)
			{
				$query->where('a.price = ' . (int) substr($search, 6));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('(a.title LIKE ' . $search . ')', 'OR');
				$query->where('(a.description LIKE ' . $search . ')', 'OR');
				$query->where('(a.features LIKE ' . $search . ')', 'OR');
			}
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// TODO: Filter by price

		// Filter by a single or group of categories.
		$categoryId = (int) $this->getState('filter.category_id');
		if ($categoryId)
		{
			if (is_scalar($section))
			{
				$query->where('a.catid = ' . $categoryId);
			}
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   2.5
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.price');
		$id .= ':' . $this->getState('filter.period');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$input = $app->input;

		// Adjust the context to support modal layouts.
		if ($layout = $input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$value = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $value);

		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		$published = $this->getUserStateFromRequest($this->context.'.filter.price', 'filter_price', '', 'string');
		$this->setState('filter.price', $published);

		$published = $this->getUserStateFromRequest($this->context.'.filter.period', 'filter_period', '', 'string');
		$this->setState('filter.period', $published);

		$section = $app->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $section);

		parent::populateState('a.ordering', 'DESC');
	}
}
