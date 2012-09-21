<?php

defined('_JEXEC')or die;

jimport('joomla.application.component.modellist');

class FormsModelFields extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'catid', 'a.catid', 'category_title',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'fid', 'a.fid', 'form_name',// The form that this field is connected to
				'state', 'a.state',// Publishing state and archiving possible
				'access', 'a.access',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'scope', 'a.scope',// Used to link fields to some components' record(s)
				'image', 'a.image',// Used to display an icon
				'url', 'a.url',// used to set the url to load an AJAX Content Pane
				'type', 'a.type',// Used to configure a field type
				'required','a.required',				
			);
		}

		parent::__construct($config);
	}

	public function populateState($ordering=null,$direction=null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$params = $app->getParams();

		// Force pagination sets (if user sets a pagebreak)
		$fid = JRequest::getInt('fid');
		$this->setState('filter.form_id', $fid);

		// Force pagination sets (if user sets a pagebreak)
		$limit = $jinput->get('limit', $app->getCfg('list_limit', 0));
		$this->setState('list.limit', $limit);

		// Force pagination groups (if param is set to allow a max # fields per page)
		$links = $jinput->get('links', $app->getCfg('list_links', 0));
		$this->setState('list.links', $links);

		$orderCol	= $jinput->get('filter_order', 'a.ordering');
		// Check to make sure the filter ordering type is in our list of available ordering types
		// TODO: Given the nature of the component, we should restrict filtering to ordering only
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'a.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  $jinput->get('filter_order_Dir', 'ASC');
		// Verify the ordering hasn't been tampered with
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);

		$user		= JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_forms')) &&  (!$user->authorise('core.edit', 'com_forms'))){
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$formId = $this->getUserStateFromRequest($this->context.'.filter.form_id', 'filter_form_id', '');
		$this->setState('filter.form_id', $formId);

		$language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', $app->getLanguageFilter());
		$this->setState('filter.language', $language);

		// process show_noauth parameter
		if (!$params->get('show_noauth')) {
			$this->setState('filter.access', true);
		}
		else {
			$this->setState('filter.access', false);
		}

		// Force the layout based on teh field type requested
		$this->setState('layout', $jinput->get('layout'));

		// Load the parameters.
		$params = JComponentHelper::getParams('com_forms');
		$this->setState('params', $params);

		parent::populateState('a.ordering', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.published');
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.language');
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.category_id');
		$id .= ':'.$this->getState('filter.category_id.include');
		$id .= ':'.$this->getState('filter.field_id');
		$id .= ':'.$this->getState('filter.field_id.include');
		$id .= ':'.$this->getState('filter.form_id');
		$id .= ':'.$this->getState('filter.form_id.include');
		$id .= ':'.$this->getState('filter.date_filtering');
		$id .= ':'.$this->getState('filter.date_field');
		$id .= ':'.$this->getState('filter.start_date_range');
		$id .= ':'.$this->getState('filter.end_date_range');
		$id .= ':'.$this->getState('filter.relative_date');

		return parent::getStoreId($id);
	}

	public function getTable($type = 'Field', $prefix = 'FormsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function getListQuery()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select(
			$this->getState('list.select',
				'a.id,a.name,a.alias,a.catid,a.checked_out,a.checked_out_time,'.
				'a.fid,a.access,a.ordering,a.language,a.attribs,a.custom,'.
				'a.publish_up,a.publish_down,a.scope,a.image,a.url,a.type')
		);

		if($this->getState('filter.published') == 2){
			$query->select($this->getState('list.select','CASE WHEN badcats.id is null THEN a.state ELSE 2 END AS state'));
		}else{
			$query->select($this->getState('list.select','CASE WHEN badcats.id is not null THEN 0 ELSE a.state END AS state'));
		}

		$query->from('#__forms_fields AS a');

		// Join over categories
		$query->select('c.title AS category_title, c.path AS category_route, c.access AS category_access, c.alias AS category_alias');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');
		// Join over the categories to get parent category titles
		$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
		$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');
		// Join to check for category published state in parent categories up the tree
		$query->select('c.published, CASE WHEN badcats.id is null THEN c.published ELSE 0 END AS parents_published');
		$subquery = 'SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ';
		$subquery .= 'ON cat.lft BETWEEN parent.lft AND parent.rgt ';
		$subquery .= 'WHERE parent.extension = ' . $db->quote('com_forms');
		if ($this->getState('filter.published') == 2) {
			// Find any up-path categories that are archived
			// If any up-path categories are archived, include all children in archived layout
			$subquery .= ' AND parent.published = 2 GROUP BY cat.id ';
			// Set effective state to archived if up-path category is archived
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 2 END';
		}
		else {
			// Find any up-path categories that are not published
			// If all categories are published, badcats.id will be null, and we just use the article state
			$subquery .= ' AND parent.published != 1 GROUP BY cat.id ';
			// Select state to unpublished if up-path category is unpublished
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 0 END';
		}
		$query->join('LEFT OUTER', '(' . $subquery . ') AS badcats ON badcats.id = c.id');

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$user	= JFactory::getUser();
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
			$query->where('c.access IN ('.$groups.')');
		}

		// Filter by search in name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} elseif(stripos($search, 'name:') === 0) {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('a.name LIKE '.substr($search, 5));
			}elseif(strpos($search, 'type:') === 0) {
				$search = $db->Quote('%'.$db->escape(substr($search, 5), true).'%');
				$query->where('a.type LIKE '.$search);
			}else{
				$search = $db->quote('%'.$db->escape($search).'%');
				$query->where('a.attribs LIKE '.$search);
			}
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published)) {
			// Use article state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' = ' . (int) $published);
		}
		elseif (is_array($published)) {
			JArrayHelper::toInteger($published);
			$published = implode(',', $published);
			// Use article state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where($publishedWhere . ' IN ('.$published.')');
		}

		// Filter by a single or group of fields.
		$fieldId = $this->getState('filter.field_id');

		if (is_numeric($fieldId)) {
			$type = $this->getState('filter.field_id.include', true) ? '= ' : '<> ';
			$query->where('a.id '.$type.(int) $fieldId);
		}
		elseif (is_array($fieldId)) {
			JArrayHelper::toInteger($fieldId);
			$fieldId = implode(',', $fieldId);
			$type = $this->getState('filter.field_id.include', true) ? 'IN' : 'NOT IN';
			$query->where('a.id '.$type.' ('.$fieldId.')');
		}

		// Filter by a single form.
		$fieldId = $this->getState('filter.form_id');

		if (is_numeric($fieldId)) {
			$type = $this->getState('filter.form_id.include', true) ? '= ' : '<> ';
			$query->where('a.fid '.$type.(int) $fieldId);
		}

		// Filter by a single or group of categories
		$categoryId = $this->getState('filter.category_id');

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
				if ($levels >= 0) {
					$subQuery->where('sub.level <= this.level + '.$levels);
				}

				// Add the subquery to the main query
				$query->where('('.$categoryEquals.' OR a.catid IN ('.$subQuery->__toString().'))');
			}
			else {
				$query->where($categoryEquals);
			}
		}
		elseif (is_array($categoryId) && (count($categoryId) > 0)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			if (!empty($categoryId)) {
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('a.catid '.$type.' ('.$categoryId.')');
			}
		}

		// Filter by start and end dates.
		$nullDate	= $db->Quote($db->getNullDate());
		$nowDate	= $db->Quote(JFactory::getDate()->toSql());

		$query->where('(a.publish_up = '.$nullDate.' OR a.publish_up <= '.$nowDate.')');
		$query->where('(a.publish_down = '.$nullDate.' OR a.publish_down >= '.$nowDate.')');

		// Filter by Date Range or Relative Date
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		$dateField = $this->getState('filter.date_field', 'a.created');

		switch ($dateFiltering)
		{
			case 'range':
				$startDateRange = $db->Quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->Quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField .
					' <= '.$endDateRange.')');
				break;

			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL ' .
					$relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}

		// process the filter for list views with user-entered filters
		$params = $this->getState('params');

		if ((is_object($params)) && ($params->get('filter_field') != 'hide') && ($filter = $this->getState('list.filter'))) {
			// clean filter variable
			$filter = JString::strtolower($filter);
			$formIdFilter = intval($filter);
			$filter = $db->Quote('%'.$db->escape($filter, true).'%', false);

			switch ($params->get('filter_field', 'form'))
			{
				case 'form':
					$query->where('a.fid = '.$formIdFilter.' ');
					break;

				case 'type':
					$query->where('a.type >= '.$filter.' ');
					break;

				// Provide legacy support for common column property title
				case 'title':
				case 'name':
				default: // default to 'name' if parameter is not valid
					$query->where('LOWER( a.name ) LIKE '.$filter);
					break;
			}
		}
		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('a.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}

		// Add the list ordering clause.
		$query->order($this->getState('list.ordering', 'a.ordering').' '.$this->getState('list.direction', 'ASC'));
		$query->group('a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.catid, a.publish_up,a.publish_down, a.access, a.state, badcats.id, c.title, c.path, c.access, c.alias, parent.title, parent.id, parent.path, parent.alias, c.published, c.lft, a.ordering, parent.lft, c.id, a.image, a.url');
		return $query;
	}

	public function getItems()
	{
		$items	= parent::getItems();
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_forms', true);

		// Convert the parameter fields into objects.
		foreach ($items as &$item)
		{
			$fieldParams = new JRegistry;
			$fieldParams->loadString($item->attribs);

			$item->params = clone $this->getState('params');
			$item->params->merge($fieldParams);

			$customOptions = new JRegistry;
			$customOptions->loadString($item->custom);
			$item->custom = $customOptions;

			$item->params = $this->getState('params');

			// get display date
			switch ($item->params->get('list_show_date'))
			{
				case 'published':
					$item->displayDate = ($item->publish_up == 0) ? $item->created : $item->publish_up;
					break;

				default:
				case 'created':
					$item->displayDate = $item->created;
					break;
			}
			if (!$guest) {
				$asset	= 'com_forms.field.'.$item->id;

				// Check general edit permission first.
				if ($user->authorise('core.edit', $asset)) {
					$item->params->set('access-edit', true);
				}
				// Now check if edit.own is available.
				elseif (!empty($userId) && $user->authorise('core.edit.own', $asset)) {
					// Check for a valid user and that they are the owner.
					if ($userId == $item->created_by) {
						$item->params->set('access-edit', true);
					}
				}
			}

			$access = $this->getState('filter.access');

			if ($access) {
				// If the access filter has been set, we already have only the fields this user can view.
				$item->params->set('access-view', true);
			}
			else {
				// If no access filter is set, the layout takes some responsibility for display of limited information.
				if ($item->catid == 0 || $item->category_access === null) {
					$item->params->set('access-view', in_array($item->access, $groups));
				}
				else {
					$item->params->set('access-view', in_array($item->access, $groups) && in_array($item->category_access, $groups));
				}
			}
		}
		return $items;
	}

	public function getStart()
	{
		return $this->getState('list.start');
	}
}