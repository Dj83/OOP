<?php
/**
 * @package     Realtor.Platform
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * Supports a suggestion box text field using Google Places API.
 *
 * @package     Realtor.Platform
 * @subpackage  com_realtor
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldAutoComplete extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'AutoComplete';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$readonly = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$placeholder = $this->element['placeholder'] ? 
			' placeholder="'. JText::_($this->element['placeholder']) .'"' :
			' placeholder="'. JText::_('COM_REALTOR_FIELD_AUTOCOMPLETE_PLACEHOLDER') .'"';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';
		//$this->getJavaScript();
		$return = '<input id="searchTextField" size="50" style="width: 100%;" type="text"><div class="clr"></div>
		<div id="map_canvas" style="height: 280px;"></div>';
//return $return;
		return '<input type="text" style="width: 100%;" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $placeholder . $readonly . $onchange . $maxLength . '/><div class="clr"></div><div id="locationmapcontainer"><div id="map_canvas" style="height: 280px;"></div></div>';
	}
	protected function getJavaScript()
	{
		$doc = JFactory::getDocument();
		$doc->addScript('http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
		$doc->addScriptDeclaration('var locationArray = \'\';');
		$doc->addScriptDeclaration("
			function strpos (h, n, o) {
				var i = (h + '').indexOf(n, (o || 0));
				return i === -1 ? false : i;
			}
			function initialize() {
			var mapOptions = {
				center: new google.maps.LatLng(-27.0000, 133.0000),
				zoom: 3,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			};
			var map = new google.maps.Map(document.getElementById('map_canvas'),mapOptions);
			var input = document.getElementById('". $this->id ."');
			var autocomplete = new google.maps.places.Autocomplete(input);
			autocomplete.bindTo('bounds', map);
			var infowindow = new google.maps.InfoWindow();
			var marker = new google.maps.Marker(
				{
					map: map
				}
			);
			google.maps.event.addListener(autocomplete, 'place_changed', function()
			{
				infowindow.close();
				var place = autocomplete.getPlace();
				place.geometry = place.geometry || null;
				if(!place.geometry){
					alert('".JText::_('COM_REALTOR_FIELD')."');
					return true;
				}
				if (place.geometry && place.geometry.viewport) {
					map.fitBounds(place.geometry.viewport);
				} else {
					map.setCenter(place.geometry.location);
					map.setZoom(16); // Because it looks better!
				}
				var image = new google.maps.MarkerImage(
					place.icon,
					new google.maps.Size(32, 32),
					new google.maps.Point(0, 0),
					new google.maps.Point(8, 16),
					new google.maps.Size(32, 32)
				);
				marker.setIcon(image);
				marker.setPosition(place.geometry.location);
				var addressBox = '';
				var inputs = {};
				inputs.streetNumber = dojo.byId('jform_address_components_street_number');
				inputs.route = dojo.byId('jform_address_components_route');
				inputs.locality = dojo.byId('jform_address_components_locality');
				inputs.sublocality = dojo.byId('jform_address_components_sublocality');
				inputs.state = {};
				inputs.state.short = dojo.byId('jform_address_components_state_short');
				inputs.state.long = dojo.byId('jform_address_components_state_long');
				inputs.county = dojo.byId('jform_address_components_county');
				inputs.city = dojo.byId('jform_address_components_city');
				inputs.country = {};
				inputs.country.long = dojo.byId('jform_address_components_country_long');
				inputs.country.short = dojo.byId('jform_address_components_country_short');
				inputs.zipCode = dojo.byId('jform_address_components_postal_code');
				inputs.unitnum = dojo.byId('jform_address_components_unit');
				inputs.floornum = dojo.byId('jform_address_components_floor');

				inputs.roomnum = dojo.byId('jform_address_components_room');
				inputs.geolat = dojo.byId('jform_geometry_lat');
				inputs.geolng = dojo.byId('jform_geometry_lng');
				inputs.startOver = function(){
					inputs.streetNumber.set('value',null);
					inputs.route.set('value',null);
					inputs.locality.set('value',null);
					inputs.sublocality.set('value',null);
					inputs.state.short.set('value',null);
					inputs.state.long.set('value',null);
					inputs.county.set('value',null);
					inputs.city.set('value',null);
					inputs.country.long.set('value',null);
					inputs.country.short.set('value',null);
					inputs.zipCode.set('value',null);
					inputs.unitnum.set('value',null);
					inputs.floornum.set('value',null);
					inputs.roomnum.set('value',null);
					inputs.geolat.set('value',null);
					inputs.geolng.set('value',null);
				}

				if (place.address_components) {	
					// reset the values
					inputs.startOver();

					addressBox = [
						(place.address_components[0] && place.address_components[0].short_name || ''),
						(place.address_components[1] && place.address_components[1].short_name || ''),
						(place.address_components[2] && place.address_components[2].short_name || '')
					].join(' ');
					addressComponents = place.address_components;
					inputPieces = input.value.split(' ');

					if (place.address_components[0].types[0] != 'street_address'){
						// Build the street address
						inputs.streetNumber.set('value', inputPieces[0]);
					}
					dojo.forEach(addressComponents,function(item,i){
						// For all the pieces do an indexOf search for apt, unit, etc
						// If the street_address doesnt exist then write the street number
						type = item.types[0] + '';
						switch(type){
							case 'street_number':
							inputs.streetNumber.set('value', item.long_name);
							break;

							case 'route':
							inputs.route.set('value', item.long_name);
							break;

							case 'locality':
							inputs.locality.set('value', item.long_name);
							inputs.city.set('value', item.long_name);
							case 'administrative_area_level_3':
							inputs.city.set('value', item.long_name);
							break;

							case 'sublocality':
							inputs.sublocality.set('value', item.long_name);
							break;

							case 'administrative_area_level_1':
							inputs.state.long.set('value', item.long_name);
							inputs.state.short.set('value', item.short_name);
							break;

							case 'administrative_area_level_2':
							inputs.county.set('value', item.long_name);
							break;

							case 'country':
							inputs.country.long.set('value', item.long_name);
							inputs.country.short.set('value', item.short_name);
							break;

							case 'postal_code':
							inputs.zipCode.set('value', item.long_name);
							break;

							default:
							break;
						}
						console.log(type);
						//console.log(type);
						//console.log(item.long_name);
					});
				}
				if(!inputs.city.get('value',null)){
					inputs.city.set('value', inputs.locality.get('value'));
				}
				infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + addressBox);
				infowindow.open(map, marker);
			});
		}
		google.maps.event.addDomListener(window, 'load', initialize);
		");
		$doc->addScriptDeclaration('
			require(["dojo/_base/array"], function(array){
			  array.forEach(locationArray, function(item, i){
				console.log(item, "at index", i);
			  });
			});
		');
	}
}
