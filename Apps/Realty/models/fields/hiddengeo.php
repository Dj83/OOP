<?php

defined('JPATH_PLATFORM') or die;

class JFormFieldHiddenGeo extends JFormField
{
	protected $type = 'HiddenGeo';

	protected function getInput()
	{
		// Initialize some field attributes.
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$short = str_replace('[]','[short]', $this->name);
		$long = str_replace('[]','[long]', $this->name);
		$shortv = '';
		$longv = '';
		if(is_array($this->value)){
			$shortv = isset($this->value['short']) ? $this->value['short'] : '';
			$longv = isset($this->value['long']) ? $this->value['long'] : '';
		}

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		$return = array();
		$return[] = '<input type="hidden" name="' . $short . '" id="' . $this->id . '_short"' . ' value="'
			. htmlspecialchars($shortv, ENT_COMPAT, 'UTF-8') . '"' . $class . $disabled . $onchange . ' />';
		$return[] = '<input type="hidden" name="' . $long . '" id="' . $this->id . '_long"' . ' value="'
			. htmlspecialchars($longv, ENT_COMPAT, 'UTF-8') . '"' . $class . $disabled . $onchange . ' />';
		return implode($return);
	}
	protected function getLabel(){}
}
