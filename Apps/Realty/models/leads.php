<?php
/**
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of lead records.
 *
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 * @since       2.5
 */
class RealtorModelLeads extends JModelList
{
	/**
	 * For the most part a lead goes into the contact and user tables, however,
	 * we do have a storage method at the d.b. for related records
	 * Technically we should let the plugins handle all the logic for
	 * filter_fields but we can force some defaults here so we can use them
	 * in the view!
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @see     JController
	 * @since   2.5
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				// #__leads
				'id', 'a.id',
				'aid', 'a.aid',
				'cid', 'a.cid',
				'uid', 'a.uid',
				'catid', 'a.catid',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'notes', 'a.notes',
				'featured', 'a.featured',
				'state', 'a.state',
/*
				// #__property_info
				'condition', 'pi.condition',
				'features', 'pi.features',// array of features cat ID's
				'type', 'pi.type',
				'size', 'pi.size',
				'sizeUnit', 'pi.sizeUnit',
				'suburbs', 'pi.suburbs',// array of suburbs cat ID's
				'beds', 'pi.bedrooms',
				'baths', 'pi.bathrooms',
				'parking', 'pi.parking',
				'lastSaleFrom', 'pi.last_sale_from',
				'lastSaleTo', 'pi.last_sale_to',
				'lastSalePrice', 'pi.last_sale_price',
				'onSaleFrom', 'pi.publish_up',
				'onSaleTo', 'pi.publish_down',
				'onSalePrice', 'pi.on_sale_price',
				'modified','pi.modified',
				'modifiedBy','pi.modified_by',
				'files', 'pi.files',

				#__users
				'username', 'u.username',
				'email', 'u.email',
				'block', 'u.block',
				'registerDate', 'u.registerDate',
				'lastvisitDate', 'u.lastvisitDate',
				'activation', 'u.activation',

				#_contact_details
				'fullname', 'cd.name',
				'phoneTel', 'cd.telephone',
				'phoneFax', 'cd.fax',
				'phoneMobile', 'cd.mobile',
				'webPage', 'cd.webpage',				
				'addressStreet', 'cd.address',
				// These are automatically formatted based on category titles
				'addressSuburb', 'cd.suburb',
				'addressState', 'cd.state',
				'addressCountry', 'cd.country',
				'addressPostCode', 'cd.postcode',
*/

			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @note	Calling getState in this method will result in recursion.
	 * @return  void
	 *
	 * @since   2.5
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout', 'default'))
		{
			$this->context .= '.'.$layout;
		}

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$active = $this->getUserStateFromRequest($this->context.'.filter.active', 'filter_active');
		$this->setState('filter.active', $active);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state');
		$this->setState('filter.state', $state);

		$range = $this->getUserStateFromRequest($this->context.'.filter.range', 'filter_range');
		$this->setState('filter.range', $range);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_realtor');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('cd.name', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 * @return  string  A store id.
	 * @since   2.5
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.active');
		$id	.= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.range');

		return parent::getStoreId($id);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	2.5
	 */
	public function getTable($type = 'Lead', $prefix = 'RealtorTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Gets the list of leads and adds extensive joins to the result set.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   2.5
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (empty($this->cache[$store]))
		{
			$items = parent::getItems();
			// Bail out on an error or empty list.
			if (empty($items))
			{
				$this->cache[$store] = $items;

				return $items;
			}
			// Add the items to the internal cache.
			$this->cache[$store] = $items;
		}

		return $this->cache[$store];
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id AS id, a.aid AS agentId, a.cid AS contactId,'.
				'a.uid AS userId, a.checked_out AS checked_out,'.
				'a.checked_out_time AS checked_out_time, a.catid AS catid,' .
				'a.state AS state, a.ordering AS ordering,'.
				'a.language, a.publish_up, a.publish_down'
			)
		);
		$query->from($db->quoteName('#__leads').' AS a');

		// Join over the users for the checked out user.
		$query->select('u.name AS editor, u.email AS email, u.registerDate as registerDate, u.lastvisitDate as lastVisit');
		$query->join('LEFT', '#__users AS u ON u.id=a.uid');

		// Join over the contact details for the leads contact data.
		$query->select('cd.name AS fullname, cd.address as street_address, cd.suburb as suburb_title, cd.state AS state_title, cd.postcode AS postcode');
		$query->join('LEFT', '#__contact_details AS cd ON cd.id = a.cid');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');


		// If the model is set to check item state, add to the query.
		$state = $this->getState('filter.state');
		// If the model is set to check the activated state, add to the query.
		$published = $this->getState('filter.state');
		$active = $this->getState('filter.active');
		$search = $this->getState('filter.search');
		$range = $this->getState('filter.range');
		$ordering = $this->getState('list.ordering', 'l.fullname');
		$direction = $this->getState('list.direction', 'ASC');

		$categoryId = $this->getState('filter.category_id');
		$contactId = $this->getState('filter.contact_id');
		$agentId = $this->getState('filter.agent_id');

		// Filter by published state
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by category.
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $categoryId);
		}

		// Filter by client.
		if (is_numeric($contactId)) {
			$query->where('a.cid = '.(int) $contactId);
		}

		// Filter by agent.
		if (is_numeric($agentId)) {
			$query->where('a.aid = '.(int) $agentId);
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = ' . $db->quote($language));
		}

		if (!empty($search)) {
			// Escape the search string.
			$search	= $db->Quote('%'.$db->escape($search, true).'%');
			$searches	= array();

			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				// Compile the different search clauses.
				$searches[]	= 'cd.name LIKE '.$token;
				$searches[]	= 'u.username LIKE '.$token;
				$searches[]	= 'u.email LIKE '.$token;
			}

			// Add the clauses to the query.
			$query->where('('.implode(' OR ', $searches).')');
		}

		// Add filter for registration ranges select list
		// Apply the range filter.
		if ($range)
		{
			jimport('joomla.utilities.date');

			// Get UTC for now.
			$dNow = new JDate;
			$dStart = clone $dNow;

			switch ($range)
			{
				case 'past_week':
					$dStart->modify('-7 day');
					break;

				case 'past_1month':
					$dStart->modify('-1 month');
					break;

				case 'past_3month':
					$dStart->modify('-3 month');
					break;

				case 'past_6month':
					$dStart->modify('-6 month');
					break;

				case 'post_year':
				case 'past_year':
					$dStart->modify('-1 year');
					break;

				case 'today':
					// Ranges that need to align with local 'days' need special treatment.
					$app	= JFactory::getApplication();
					$offset	= $app->getCfg('offset');

					// Reset the start time to be the beginning of today, local time.
					$dStart	= new JDate('now', $offset);
					$dStart->setTime(0, 0, 0);

					// Now change the timezone back to UTC.
					$dStart->setOffset(0);
					break;
			}

			if ($range == 'post_year')
			{
				$query->where(
					'l.created < '.$db->quote($dStart->format('Y-m-d H:i:s'))
				);
			}
			else
			{
				$query->where(
					'l.created >= '.$db->quote($dStart->format('Y-m-d H:i:s')).
					' AND l.created <='.$db->quote($dNow->format('Y-m-d H:i:s'))
				);
			}
		}


		// Add the list ordering clause.
		if ($ordering == 'ordering' || $ordering == 'fullname') {
			$ordering = 'cd.name '.$direction.', a.ordering';
		}

		$query->order($db->escape($ordering).' '.$db->escape($direction));

		return $query;
	}
}
