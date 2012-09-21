<?php
$fieldtypes = array('singlelinetext','paragraphtext','multiplechoice',
	'name','time','date','address','price','matrixchoice',/*'signature',*/
	'number','checkboxes','list','phone','website','email','fileupload',
	'pagebreak');
	
?>
<div class="width100 grid-block">
	<ul class="fields width100 grid-box">
<?php foreach($fieldtypes as $type):
	$onclick =str_replace(" ", "", JText::_('COM_FORMS_FIELDTYPE_'.$type));
$routeDisplay = 'index.php?option=com_forms&tmpl=component&view=formfield&fieldtype='.$type.'&fid='.$this->form_id.'&id=0';
// TODO: Build the form route at the formfield display
$routeForm = "index.php?option=com_forms&tmpl=component&view=field&layout=edit&task=field.add&fid=".$this->form_id."&fieldtype=".$type;
?>
<?php
$onclick = array();
$onclick[] = "var newPane = new dijit.layout.ContentPane({";
$onclick[] = "onClick:function(e){switchPanes(1);dijit.byId('forms-tab-1').set('href','".$routeForm."');},";
$onclick[] = "onMouseLeave: function(e){newPane.set('class','');},";
$onclick[] = "onMouseOver: function(e){newPane.set('class','box-content'); newPane.set('style','cursor:pointer');},";
$onclick[] = "style:'padding-right:5px;margin:15px;',extractContent:true,refreshOnShow:true";
$onclick[] = "});";
$onclick[]= "newPane.set('href','".$routeDisplay."');newPane.placeAt('fields-container-inner');newPane.startup();newPane.refresh();";
?>
		<li class="width50" onClick="<?php echo implode("\n", $onclick); ?>">
			<?php echo JText::_('COM_FORMS_FIELDTYPE_'.strtoupper($type)); ?>
		</li>
<?php endforeach; ?>
	</ul>
</div>