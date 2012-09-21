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

/**
 * Realtor component helper.
 *
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 * @since       2.5
 * @help		Used for adding submenus to a component on 
 *				the administrattion views
 * @help		Also used throughout the component for 
 * 				various static tasks and also filtering options
 */
class RealtorHelper
{
	/**
	 * @var    JObject  A cache for the available actions.
	 * @since  2.5
	 */
	protected static $actions;

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public static function addSubmenu($vName)
	{

		// Groups and Levels are restricted to core.admin
		$canDo = self::getActions();

		if ($canDo->get('core.admin'))
		{
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_LEADS'),
				'index.php?option=com_realtor&view=leads',
				$vName == 'leads'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_PROPERTIES'),
				'index.php?option=com_realtor&view=properties',
				$vName == 'properties'
			);

			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_REPORTS'),
				'index.php?option=com_realtor&view=reports',
				$vName == 'reports'
			);

			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_STORE'),
				'index.php?option=com_realtor&view=store',
				$vName == 'store'
			);

			$extension = JRequest::getString('extension');
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_SUPPLIERS_CATEGORIES'),
				'index.php?option=com_categories&extension=com_realtor.suppliers',
				$vName == 'categories' || $extension == 'com_realtor.suppliers'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_DISTRIBUTORS_CATEGORIES'),
				'index.php?option=com_categories&extension=com_realtor.distributors',
				$vName == 'categories' || $extension == 'com_realtor.distributors'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_MARKETS_CATEGORIES'),
				'index.php?option=com_categories&extension=com_realtor.markets',
				$vName == 'categories' || $extension == 'com_realtor.markets'
			);
			// Property Types
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_PROPERTIES_CATEGORIES'),
				'index.php?option=com_categories&extension=com_realtor.properties',
				$vName == 'categories' || $extension == 'com_realtor.properties'
			);
			// Property Features
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_FEATURES_CATEGORIES'),
				'index.php?option=com_categories&extension=com_realtor.features',
				$vName == 'categories' || $extension == 'com_realtor.features'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_CATALOG_CATEGORIES'),
				'index.php?option=com_categories&extension=com_realtor.catalog',
				$vName == 'categories' || $extension == 'com_realtor.catalog'
			);
			JSubMenuHelper::addEntry(
				JText::_('COM_REALTOR_SUBMENU_SUBSCRIPTIONS_CATEGORIES'),
				'index.php?option=com_categories&extension=com_realtor.subscriptions',
				$vName == 'categories' || $extension == 'com_realtor.subscriptions'
			);
		}
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 *
	 * @since   2.5
	 */
	public static function getActions()
	{
		if (empty(self::$actions))
		{
			$user = JFactory::getUser();
			self::$actions = new JObject;

			$actions = array(
				'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
			);

			foreach ($actions as $action)
			{
				self::$actions->set($action, $user->authorise($action, 'com_realtor'));
			}
		}

		return self::$actions;
	}

	/**
	 * Get a list of filter options for the various rooms of a property.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @since   2.5
	 */
	static function getRoomOptions($room='bedrooms')
	{
		// Build the filter options.
		$options = array(
			JHtml::_('select.option', '1', JText::sprintf('COM_REALTOR_OPTION_RANGE_ONE', $room)),
			JHtml::_('select.option', '1.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_ONEANDHALF', $room)),
			JHtml::_('select.option', '2', JText::sprintf('COM_REALTOR_OPTION_RANGE_TWO', $room)),
			JHtml::_('select.option', '2.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_TWOANDHALF', $room)),
			JHtml::_('select.option', '3', JText::sprintf('COM_REALTOR_OPTION_RANGE_THREE', $room)),
			JHtml::_('select.option', '3.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_THREEANDHALF', $room)),
			JHtml::_('select.option', '4', JText::sprintf('COM_REALTOR_OPTION_RANGE_FOUR', $room)),
			JHtml::_('select.option', '4.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_FOURANDHALF', $room)),
			JHtml::_('select.option', '5', JText::sprintf('COM_REALTOR_OPTION_RANGE_FIVE', $room)),
			JHtml::_('select.option', '5.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_FIVEANDHALF', $room)),
			JHtml::_('select.option', '6', JText::sprintf('COM_REALTOR_OPTION_RANGE_SIX', $room)),
			JHtml::_('select.option', '6.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_SIXANDHALF', $room)),
			JHtml::_('select.option', '7', JText::sprintf('COM_REALTOR_OPTION_RANGE_SEVEN', $room)),
			JHtml::_('select.option', '7.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_SEVENANDHALF', $room)),
			JHtml::_('select.option', '8', JText::sprintf('COM_REALTOR_OPTION_RANGE_EIGHT', $room)),
			JHtml::_('select.option', '8.5', JText::sprintf('COM_REALTOR_OPTION_RANGE_EIGHTANDHALF', $room)),
			JHtml::_('select.option', '9.0', JText::sprintf('COM_REALTOR_OPTION_RANGE_NINE', $room)),
		);
		return $options;
	}

	/**
	 * Get a list of filter options for the blocked state of an agent.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @since   2.5
	 */
	static function getStateOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_REALTOR_ACTIVE'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_REALTOR_INACTIVE'));

		return $options;
	}

	/**
	 * Get a list of filter options for the state of a lead.
	 *
	 * @return  array  An array of JHtmlOption elements.
	 *
	 * @since   2.5
	 */
	static function getLeadOptions()
	{
		// Build the filter options.
		$options = array();
		$options[] = JHtml::_('select.option', '0', JText::_('COM_REALTOR_ASSIGNED'));
		$options[] = JHtml::_('select.option', '1', JText::_('COM_REALTOR_UNASSIGNED'));
		$options[] = JHtml::_('select.option', '2', JText::_('COM_REALTOR_INACTIVE'));
		$options[] = JHtml::_('select.option', '3', JText::_('COM_REALTOR_CANCELLED'));

		return $options;
	}

	/**
	 * Creates a list of range options used in filter select list
	 * used in com_realtor on leads view
	 *
	 * @return  array
	 *
	 * @since   2.5
	 */
	public static function getRangeOptions()
	{
		$options = array(
			JHtml::_('select.option', 'today', JText::_('COM_REALTOR_OPTION_RANGE_TODAY')),
			JHtml::_('select.option', 'past_week', JText::_('COM_REALTOR_OPTION_RANGE_PAST_WEEK')),
			JHtml::_('select.option', 'past_1month', JText::_('COM_REALTOR_OPTION_RANGE_PAST_1MONTH')),
			JHtml::_('select.option', 'past_3month', JText::_('COM_REALTOR_OPTION_RANGE_PAST_3MONTH')),
			JHtml::_('select.option', 'past_6month', JText::_('COM_REALTOR_OPTION_RANGE_PAST_6MONTH')),
			JHtml::_('select.option', 'past_year', JText::_('COM_REALTOR_OPTION_RANGE_PAST_YEAR')),
			JHtml::_('select.option', 'post_year', JText::_('COM_REALTOR_OPTION_RANGE_POST_YEAR')),
		);
		return $options;
	}
}