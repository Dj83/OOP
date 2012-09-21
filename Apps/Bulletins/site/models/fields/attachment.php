<?php
defined('_JEXEC') or die;

class JFormFieldAttachment extends JFormField
{
	protected $type = 'Attachment';
	protected static $initialised = false;
	protected function getInput()
	{
		$maxFiles = $this->element['maxfiles'] ? (int) $this->element['maxfiles'] : 5;
		$assetField = $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';
		$authorField = $this->element['created_by_field'] ? (string) $this->element['created_by_field'] : 'created_by';
		$asset = $this->form->getValue($assetField) ? $this->form->getValue($assetField) : (string) $this->element['asset_id'];
		if ($asset == '')
		{
			$asset = JRequest::getCmd('option');
		}

		$link = (string) $this->element['link'];
		if (!self::$initialised)
		{

			// Load the modal behavior script.
			JHtml::_('behavior.modal');

			// Build the script.
			$script = array();
			$script[] = '	function jInsertFieldValue(value, id) {';
			$script[] = '		var old_value = document.id(id).value;';
			$script[] = '		if (old_value != value) {';
			$script[] = '			var elem = document.id(id);';
			$script[] = '			elem.value = value;';
			$script[] = '			elem.fireEvent("change");';
			$script[] = '			if (typeof(elem.onchange) === "function") {';
			$script[] = '				elem.onchange();';
			$script[] = '			}';
			$script[] = '			jMediaRefreshPreview(id);';
			$script[] = '		}';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreview(id) {';
			$script[] = '		var value = document.id(id).value;';
			$script[] = '		var img = document.id(id + "_preview");';
			$script[] = '		if (img) {';
			$script[] = '			if (value) {';
			$script[] = '				img.src = "' . JURI::root() . '" + value;';
			$script[] = '				document.id(id + "_preview_empty").setStyle("display", "none");';
			$script[] = '				document.id(id + "_preview_img").setStyle("display", "");';
			$script[] = '			} else { ';
			$script[] = '				img.src = ""';
			$script[] = '				document.id(id + "_preview_empty").setStyle("display", "");';
			$script[] = '				document.id(id + "_preview_img").setStyle("display", "none");';
			$script[] = '			} ';
			$script[] = '		} ';
			$script[] = '	}';

			$script[] = '	function jMediaRefreshPreviewTip(tip)';
			$script[] = '	{';
			$script[] = '		tip.setStyle("display", "block");';
			$script[] = '		var img = tip.getElement("img.media-preview");';
			$script[] = '		var id = img.getProperty("id");';
			$script[] = '		id = id.substring(0, id.length - "_preview".length);';
			$script[] = '		jMediaRefreshPreview(id);';
			$script[] = '	}';

			// Add the script to the document head.
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

			self::$initialised = true;
		}

		// Initialize variables.
		$html = array();
		$html[] = '<div class="clearfix"></div>';
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';

		// Initialize JavaScript field attributes.

		// Add an extra field
		$this->value[] = '';

		for($i = 0; $i < $maxFiles; $i++){
			$onchange = ' onchange="return document.getElementById(\'' . $this->id . '_'. $i .'_placeholder\').innerHTML = this.value"';
			$value = isset($this->value[$i]) ?  htmlspecialchars($this->value[$i], ENT_COMPAT, 'UTF-8') : '';
			// The text field.
			$html[] = '<div class="float-left width20">';
			$html[] = '	<input type="text" name="' . $this->name . '" id="' . $this->id . '_'. $i .'"' . ' value="'
				. $value . '"' . ' hidden="true"' . $attr . $onchange. ' />';
			$html[] = '<span>Attachment #'.($i+1).': <em id="' . $this->id . '_'. $i .'_placeholder">'.$value.'</em></span>';
	
			$directory = (string) $this->element['directory'];
			if ($value && file_exists(JPATH_ROOT . '/' . $value))
			{
				$folder = explode('/', $value);
				array_shift($folder);
				array_pop($folder);
				$folder = implode('/', $folder);
			}
			elseif (file_exists(JPATH_ROOT . '/' . JComponentHelper::getParams('com_media')->get('image_path', 'images') . '/' . $directory))
			{
				$folder = $directory;
			}
			else
			{
				$folder = '';
			}
			// The button.
			$html[] = '<div class="clearfix"></div>';
			$html[] = '	<div class="blank float-left">';
			$html[] = '		<a class="modal" title="' . JText::_('JLIB_FORM_BUTTON_SELECT') . '"' . ' href="'
				. ($this->element['readonly'] ? ''
				: ($link ? $link
					: 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=' . $asset . '&amp;author='
					. $this->form->getValue($authorField)) . '&amp;fieldid=' . $this->id . '_'. $i .'&amp;folder=' . $folder) . '"'
				. ' rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = JText::_('JLIB_FORM_BUTTON_SELECT') . '</a>';
			$html[] = '</div>';
	
			$html[] = '	<div class="blank float-left">';
			$html[] = '		<a title="' . JText::_('JLIB_FORM_BUTTON_CLEAR') . '"' . ' href="#" onclick="';
			$html[] = 'jInsertFieldValue(\'\', \'' . $this->id . '_'. $i .'\');';
			$html[] = 'return false;';
			$html[] = '">';
			$html[] = JText::_('JLIB_FORM_BUTTON_CLEAR') . '</a>';
			$html[] = '</div>';
	
			// The Preview.
			$preview = (string) $this->element['preview'];
			$showPreview = true;
			$showAsTooltip = false;
			switch ($preview)
			{
				case 'false':
				case 'none':
					$showPreview = false;
					break;
				case 'true':
				case 'show':
					break;
				case 'tooltip':
				default:
					$showAsTooltip = true;
					$options = array(
						'onShow' => 'jMediaRefreshPreviewTip',
					);
					JHtml::_('behavior.tooltip', '.hasTipPreview', $options);
					break;
			}
	
			if ($showPreview)
			{
				if ($value && file_exists(JPATH_ROOT . '/' . $value))
				{
					$src = JURI::root() . $value;
				}
				else
				{
					$src = '';
				}
	
				$attr = array(
					'id' => $this->id . '_'. $i .'_preview',
					'class' => 'media-preview',
					'style' => 'max-width:160px; max-height:100px;'
				);
				$img = JHtml::image($src, JText::_('JLIB_FORM_MEDIA_PREVIEW_ALT'), $attr);
				$previewImg = '<div id="' . $this->id . '_'. $i .'_preview_img"' . ($src ? '' : ' style="display:none"') . '>' . $img . '</div>';
				$previewImgEmpty = '<div id="' . $this->id . '_'. $i .'_preview_empty"' . ($src ? ' style="display:none"' : '') . '>'
					. JText::_('JLIB_FORM_MEDIA_PREVIEW_EMPTY') . '</div>';
	
				$html[] = '<div class="media-preview fltlft">';
				if ($showAsTooltip)
				{
					$tooltip = $previewImgEmpty . $previewImg;
					$options = array(
						'title' => JText::_('JLIB_FORM_MEDIA_PREVIEW_SELECTED_IMAGE'),
						'text' => JText::_('JLIB_FORM_MEDIA_PREVIEW_TIP_TITLE'),
						'class' => 'hasTipPreview'
					);
					$html[] = JHtml::tooltip($tooltip, $options);
				}
				else
				{
					$html[] = ' ' . $previewImgEmpty;
					$html[] = ' ' . $previewImg;
				}
				$html[] = '</div>';
			}
			$html[] = '</div>';
		}

		return implode("\n", $html);
	}
}