<?php
/* 
	come out of web root and into private
 */
define('JPATH_BASE', dirname(__FILE__));
define('JPATH_SITE', JPATH_BASE);
define('JPATH_LIBRARIES', dirname(dirname(__FILE__)).'/private/libraries');
define('JPATH_PAYPAL', JPATH_LIBRARIES.'/paypal');
define('JPATH_THEMES', JPATH_BASE.'/themes');
define('JPATH_APPS', JPATH_BASE.'/checkout');


require dirname(dirname(dirname(dirname(__FILE__)))).'/private/libraries/import.php';
