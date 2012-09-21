<?php

defined('_JEXEC') or die;

/**
 * @param	array
 * @return	array
 */
function CheckoutBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		unset($query['view']);
	}

	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function CheckoutParseRoute($segments)
{
	$vars = array();

	$vars['view'] = 'checkout';

	return $vars;
}
