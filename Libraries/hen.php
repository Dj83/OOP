<?php

defined('JPATH_PLATFORM') or die('restricted access');

abstract class HenFactory extends JObject
{
	protected static $template;
/*
 we also over-ride the renderers as to not set a buffer, instead we load this object with the data that would go into a buffer
*/
	public function __construct($properties){
		parent::__construct($properties);
		$this->template = new HenTemplate();
	}
	// This is called by modules and components, it places a SimpleXMLElement node into the appropriate place
	// in order to add a child on it
	public function getInstance($grid,$position=''/*  name of the position to put this wrapper into */)
	{
		static $instance;
		if(!$instance){
			$instance = new HenFactory();
		}
		$grid = $instance->template->get((string)$grid);
		return $grid->get($position);
	}
	/**
	 *	This class is first constructed when the module or component renderer loads
	 *  afterwards, the method below is called (from the index.php in a template extension folder)
	 **/
	public static function renderTemplate()
	{
		return $this->template->asFormattedHTML();
	}
}