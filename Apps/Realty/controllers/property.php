<?php
/**
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Property controller class.
 *
 * @package		Realtor.Administrator
 * @subpackage	com_realtor
 * @since		2.5
 */
class RealtorControllerProperty extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_REALTOR_PROPERTY';
}
