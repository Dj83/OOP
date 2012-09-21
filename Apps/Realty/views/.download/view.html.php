<?php
/**
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View class for download a list of tracks.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class BannersViewDownload extends JView
{
	protected $form;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->form	= $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}
}
