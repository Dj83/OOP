<?php

jimport('joomla.application.component.view');
class FormsViewCategories extends JView
{
	protected $items;
	protected $state;
	protected $params;
	public function display($tpl=null)
	{
		parent::display($tpl);
	}
	protected function _prepareDocument()
	{
		$this->document->setTitle('Form Builder - Form Categories');
	}
}