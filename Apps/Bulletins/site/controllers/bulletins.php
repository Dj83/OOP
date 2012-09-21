<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class BulletinsControllerBulletins extends JControllerAdmin
{
	protected $text_prefix = 'COM_BULLETINS_BULLETINS';

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'Bulletin', $prefix = 'BulletinsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}