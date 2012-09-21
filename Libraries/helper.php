<?php

defined('JPATH_PLATFORM') or die;

/**
 * Extension helper class
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       12.1
 */
class ExtensionHelper
{
	/**
	 * The extension list cache
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected static $extensions = array();

	/**
	 * Get the extension information.
	 *
	 * @param   string   $name    The name of the extension.
	 * @param   string   $type    The type of the extension [library,module,plugin,template,component,file].
	 * @param   boolean  $strict  If set and the extension does not exist, the enabled attribute will be set to false.
	 *
	 * @return  object   An object with the information for the extension.
	 *
	 * @since   11.1
	 */
	public static function getExtension($name, $type, $strict = false)
	{
		if (!isset(self::$extensions[$type][$name]))
		{
			if (self::_load($name,$type))
			{
				$result = self::$extensions[$type][$name];
			}
			else
			{
				$result = new stdClass;
				$result->enabled = $strict ? false : true;
				$result->params = new JRegistry;
			}
		}
		else
		{
			$result = self::$extensions[$type][$name];
		}

		return $result;
	}

	/**
	 * Checks if the extension is enabled
	 *
	 * @param   string   $option  The extension option.
	 * @param   boolean  $strict  If set and the extension does not exist, false will be returned.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	public static function isEnabled($name, $strict = false)
	{
		$result = self::getExtension($name, $strict);

		return ($result->enabled | JFactory::getApplication()->isAdmin());
	}

	/**
	 * Gets the parameter object for the extension
	 *
	 * @param   string   $name    The name of the extension.
	 * @param   string   $type    The type of the extension [library,module,plugin,template,component,file].
	 * @param   boolean  $strict  If set and the component does not exist, false will be returned
	 *
	 * @return  JRegistry  A JRegistry object.
	 *
	 * @see     JRegistry
	 * @since   11.1
	 */
	public static function getParams($name, $type, $strict = false)
	{
		$extension = self::getExtension($name, $type, $strict);

		return $extension->params;
	}

	/**
	 * Load the installed extensions into the _extensions property.
	 *
	 * @param   string  $name  The element value for the extension
	 *
	 * @param   string  $type  The element type for the extension
	 *
	 * @return  boolean  True on success
	 *
	 * @since   11.1
	 */
	protected static function _load($name,$type)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id AS "id", element AS "extension", params, enabled');
		$query->from('#__extensions');
		$query->where($query->qn('element') . ' = ' . $db->quote($name));
		$query->where($query->qn('type') 	. ' = ' . $db->quote($type));
		$db->setQuery($query);

		$cache = JFactory::getCache('_system', 'callback');

		self::$extensions[$type][$name] = $cache->get(array($db, 'loadObject'), null, $name, false);

		if ($error = $db->getErrorMsg() || empty(self::$extensions[$type][$name]))
		{
			// Fatal error.
			JError::raiseWarning(500, JText::sprintf('JLIB_APPLICATION_ERROR_EXTENSION_NOT_LOADING', $name, $error));
			return false;
		}

		// Convert the params to an object.
		if (is_string(self::$extensions[$type][$option]->params))
		{
			$temp = new JRegistry;
			$temp->loadString(self::$extensions[$type][$name]->params);
			self::$extensions[$type][$name]->params = $temp;
		}

		return true;
	}
}