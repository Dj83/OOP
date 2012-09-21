<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class FormsViewField extends JView
{
	// HTML View class to create a new Field record
	protected $item;
	protected $form;
	protected $state;
	protected $params;
	protected $form_id;
	public function display($tpl=null)
	{
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$state = $this->get('State');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$this->params = $state->get('params');
		$this->form_id = JRequest::getVar('fid');
		parent::display($tpl);
		//echo 'adaASAsaSasASasd   -  '. JRequest::getVar('formid') . ' -   '. JRequest::getVar('fieldtype');
	}
	public function _prepareDocument()
	{
	}
}