<?php

jimport('joomla.application.component.view');

class FormsViewFormfield extends JView
{
	protected $fieldtype;
	protected $fieldid;
	protected $formid;

	public function display($tpl=null)
	{
		// Basically we're just loading a quick display and the container has 
		// a link that opens a tab on the right side of the display to load a form
		$this->fieldtype = JRequest::getVar('fieldtype');
		$this->form_id = JRequest::getVar('fid');
		$this->id = JRequest::getVar('id');
		parent::display($this->fieldtype);
	}
}