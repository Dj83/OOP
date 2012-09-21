<?php
defined('JPATH_PLATFORM') or die;

class JFormFieldUsergroups extends JFormField
{
	protected $type = 'Usergroups';
	protected function getInput()
	{
		// Initialize variables.
		$options = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		$excluded = $this->element['excluded'] ? json_decode($this->element['excluded']) : array();

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Iterate through the children and build an array of options.
		foreach ($this->element->children() as $option)
		{

			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}

			// Create a new option object based on the <option /> element.
			$tmp = JHtml::_(
				'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
				((string) $option['disabled'] == 'true')
			);

			// Set some option attributes.
			$tmp->class = (string) $option['class'];

			// Set some JavaScript option attributes.
			$tmp->onclick = (string) $option['onclick'];

			// Add the option object to the result set.
			$options[] = $tmp;
		}

		return $this->accessUserGroups($this->name, $this->value, $excluded, true);
	}
	private static function accessUserGroups($name, $selected, $excluded = array(), $checkSuperAdmin = false)
	{
		static $count;

		$count++;

		$isSuperAdmin = JFactory::getUser()->authorise('core.admin');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, COUNT(DISTINCT b.id) AS level');
		$query->from($db->quoteName('#__usergroups') . ' AS a');
		$query->join('LEFT', $db->quoteName('#__usergroups') . ' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
		$query->group('a.id, a.title, a.lft, a.rgt, a.parent_id');
		$query->order('a.lft ASC');
		$db->setQuery($query);
		$groups = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum())
		{
			JError::raiseNotice(500, $db->getErrorMsg());
			return null;
		}

		$html = array();

		$total = count($groups);
		
		$html[] = '<ul class="checklist usergroups width100 clearfix float-left">';
		for ($i = 0, $n = $total; $i < $n; $i++)
		{
			$item = &$groups[$i];
			$inhere = in_array($item->id, $excluded);

			// If checkSuperAdmin is true, only add item if the user is superadmin or the group is not super admin
			// or if there are element attributes set from the model/form to exclude specific groups
			if ((!$checkSuperAdmin) 
			|| $isSuperAdmin 
			|| (!JAccess::checkGroup($item->id, 'core.admin'))
			)
			
			{
				if( !$inhere ){
					// Setup  the variable attributes.
					$eid = $count . 'group_' . $item->id;
					// Don't call in_array unless something is selected
					$checked = '';
					if ($selected)
					{
						$checked = in_array($item->id, $selected) ? ' checked="checked"' : '';
					}
					$rel = ($item->parent_id > 0) ? ' rel="' . $count . 'group_' . $item->parent_id . '"' : '';
	
					// Build the HTML for the item.
					$html[] = '	<li class="float-left width33">';
					$html[] = '		<input type="checkbox" name="' . $name . '[]" value="' . $item->id . '" id="' . $eid . '"';
					$html[] = '				' . $checked . $rel . ' />';
					$html[] = '		<label for="' . $eid . '">';
					$html[] = '		' . $item->title;
					$html[] = '		</label>';
					$html[] = '	</li>';
				}
			}
		}
		$html[] = '</ul>';

		return implode("\n", $html);
	}
}
