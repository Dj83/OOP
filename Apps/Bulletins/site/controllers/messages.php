<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class BulletinsControllerMessages extends JControllerAdmin
{
	public function getModel($name = 'Message', $prefix = 'BulletinsModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}