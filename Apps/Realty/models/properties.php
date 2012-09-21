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
 * Methods supporting a list of property records.
 *
 * @package		Realtor.Administrator
 * @subpackage	com_realtor
 * @since		2.5
 */
class RealtorModelProperties extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	2.5
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'route', 'a.route',
				'formatted_address', 'a.formatted_address',
				'intersection', 'a.intersection',
				'political','a.political',
				// Has abbreviation
				'country','a.country',
				'country_short','a.country_short',
				// State
				'states', 'a.administrative_area_level_1',
				'states_short', 'a.administrative_area_level_1_short',
				// County
				'county', 'a.administrative_area_level_2',
				// City,
				'city', 'a.administrative_area_level_3',
				// Additional city name used
				'colloquial_area', 'a.colloquial_area',
				// Town or incorporated city
				'locality','a.locality',
				// Suburb
				'suburb','a.sublocality',
				'neighborhood','a.neighborhood',
				'premise','a.premise',
				'subpremise','a.subpremise',
				'postal_code','a.postal_code',
				'street_number','a.street_number',
				'floor','a.floor',
				'room','a.room',
				'unit','a.unit',
				'state', 'a.state',
			);
		}

		parent::__construct($config);
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
	public function getTable($type = 'Property', $prefix = 'RealtorTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	2.5
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_realtor');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.formatted_address', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
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
				'a.id AS id,'.
				/*'a.formatted AS formatted_address,'.
				'a.route AS route,'.
				'a.intersection AS intersection,'.
				'a.political AS political,'.
				'a.country AS country, a.country_short AS country_abbrev'.
				'a.states AS states, a.states_short AS states_abbrev'.
				'a.county AS county,'.
				'a.city AS city,'.
				'a.colloquial_area AS a.colloquial_area,'.
				'a.locality AS a.locality,'.
				'a.suburb AS a.sublocality,'.
				'a.neighborhood AS a.neighborhood,'.
				'a.premise AS a.premise,'.
				'a.subpremise AS a.subpremise,'.
				'a.postal_code AS a.postal_code,'.
				'a.street_number AS a.street_number,'.
				'a.floor AS a.floor,'.
				'a.room AS a.room,'.
				'a.unit AS a.unit,'.
				'a.checked_out AS checked_out,'.
				'a.checked_out_time AS checked_out_time, ' .*/
				'a.state AS state'
			)
		);

		$query->from($db->quoteName('#__property_information').' AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.street_address LIKE '.$search);
			}
		}
		$ordering = $this->getState('list.ordering', 'ordering');
		// Add the list ordering clause.
		$query->order($db->escape($ordering).' '.$db->escape($this->getState('list.direction', 'ASC')));

		return $query;
	}
}
