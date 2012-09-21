<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class BulletinsViewModal extends JView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Include the component HTML helpers.
		JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
		$this->_prepareDocument();
		parent::display($tpl);
	}
	protected function _prepareDocument()
	{
		$this->document->addStylesheet('administrator/templates/bluestork/css/template.css');
	}
}
