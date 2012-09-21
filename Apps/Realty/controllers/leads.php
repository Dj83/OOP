<?php
/**
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Leads list controller class.
 *
 * @package		realtor.Administrator
 * @subpackage	com_realtor
 * @since		2.5
 */
class RealtorControllerLeads extends JControllerAdmin
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	2.5
	 */
	protected $text_prefix = 'COM_REALTOR_LEADS';

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	2.5
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Proxy for getModel.
	 * @since	2.5
	 */
	public function getModel($name = 'Lead', $prefix = 'RealtorModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
