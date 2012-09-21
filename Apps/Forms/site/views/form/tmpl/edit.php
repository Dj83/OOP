<?php

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

$id = $this->item->id;

$jinput = JFactory::getApplication()->input;
$tmpl = $jinput->get('tmpl');
if($tmpl != 'component'):?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		Joomla.submitform(task);
	}
</script>
<script type="text/javascript">
<?php if (!$id): ?>var newFormDialogAdvancedOptions = false;<?php endif; ?>
function switchPanes(id)
{
	var activatethisButton = dojo.byId("forms_button_"+id);
	activatethisButton.set('class','active');
	var formsTab = dijit.byId('forms-tab');
	var tabs = dijit.byId("forms-tab").getChildren();
	var currenttabId = null;
	dojo.forEach(tabs, function(tab, index){
		if(tab.selected){
			currenttabId = index;
		}
	});

	var currenttabButton = dojo.byId('forms_button_'+currenttabId);
	currenttabButton.set('class','inactive');
	formsTab.selectChild('forms-tab-'+id);
	return false;
}
require(["dojo/ready","dijit/layout/StackContainer", "dijit/layout/ContentPane"<?php if (!($this->item->id > 0)):?>, "dijit/Dialog","dijit/form/Textarea"<?php endif;?>], 
function(ready, Tab, Pane<?php if (!($this->item->id > 0)):?>,Dialog,Textarea<?php endif;?>){
    ready(function(){
		<?php if (!($this->item->id > 0)):?>
		var prepareNewFormDialog = new Dialog({doLayout:true,style:"width: 400px;",extractContent: true,title:"",refreshOnShow:true, draggable: false, href:"<?php
			echo JRoute::_('index.php?option=com_forms&view=form&layout=edit&tmpl=component&task=form.add');
		?>"},"new-form-dialog");
		prepareNewFormDialog.startup();
		prepareNewFormDialog.titleBar.destroy();
		prepareNewFormDialog.show();
		<?php endif; ?>
		var formFieldsContainerOuter = new Tab({doLayout:false},"fields-container-outer");
        var formFieldsContainerInner = new Pane({
			doLayout: false,
			extractContent: true,
			refreshOnShow: true,
			href: "<?php echo JRoute::_('index.php?option=com_forms&view=fields&fid='.$id. '&layout=default&tmpl=component'); ?>",
			style: "width: 100%; height: 100%; overflow:hidden;"
		},"fields-container-inner");
		formFieldsContainerOuter.addChild(formFieldsContainerInner);

		formFieldsContainerOuter.startup();
		var formTabs = new Tab({
            style: "min-height: 300px; height: 100%; width: 100%;",
			doLayout: false
        }, "forms-tab");

        var cp1 = new Pane({
             title: "Add A Field",
			 extractContent: true,
			 loadingMessage: "<p class=\"box-info\">Loading available form field types. Thank you for waiting.</p>",
			 refreshOnShow: true,
             href: "<?php echo JRoute::_('index.php?option=com_forms&view=fields&fid='.$id. '&layout=fields&tmpl=component'); ?>",
			 id: "forms-tab-0"
        });
        formTabs.addChild(cp1);

        var cp2 = new Pane({
             title: "Field Properties",
             content: "<p class=\"box-warning\">No field properties set</p>",
			 id: "forms-tab-1"
        });
        formTabs.addChild(cp2);

        var cp3 = new Pane({
             title: "Form Properties",
             content: "<p class=\"box-warning\">No form properties set</p>",
			 id: "forms-tab-2"			 
        });
        formTabs.addChild(cp3);

        formTabs.startup();
		dojo.byId("forms_button_0").set('class','active');
    });
});
</script>
<div id="system">
	<form class="box style">
		<div class="width100">
			<div class="width60 grid-h grid-box">
				<div id="fields-container-outer">
					<div id="fields-container-inner">
						<p id="form-hint" class="box-hint active" style="text-align: center;">Click the buttons on the <small>right sidebar</small> or <small>Drag it here</small> to add new field.</p>
					</div>
				</div>
			</div>
			<div class="width40 grid-h grid-box">
				<div class="module mod-line">
					<div class="deepest" style="padding: 8px; min-height: 360px;">
						<div class="clearfix" style="text-align: center;">
							<button class="" id="forms_button_0" onclick="switchPanes(0); return false;">Add A Field</button>
							<button class="" id="forms_button_1" onclick="switchPanes(1); return false;">Field Properties</button>
							<button class="" id="forms_button_2" onclick="switchPanes(2); return false;">Form Properties</button>
						</div>
						<div id="forms-tab"></div>
					</div>
				</div>
			</div>
		</div>
		<?php if(!($this->item->id > 0)):?>
		<div id="new-form-dialog"></div>
		<?php endif; ?>
	</form>
</div>
<?php else: ?>
<form class="box style" action="<?php echo JRoute::_('index.php?option=com_forms&f_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="clearfix width100" style="padding: 10px;">
		<div class="width100">
			<div class="width100 clearfix">
					<div class="width100 clearfix">
						<?php echo $this->form->getLabel('name'); ?>
						<?php echo $this->form->getInput('name'); ?>
					</div>
					<div class="width100 clearfix">
						<?php echo $this->form->getLabel('description'); ?>
						<?php echo $this->form->getInput('description'); ?>
					</div>
					<div class="width50 float-left">
						<div style="margin-right: 5px;"
							<?php echo $this->form->getLabel('catid'); ?>
							<?php echo $this->form->getInput('catid'); ?>
						</div>
					</div>
					<div class="width50 float-left">
						<?php echo $this->form->getLabel('language'); ?>
						<?php echo $this->form->getInput('language'); ?>
					</div>
				</div>
				<div class="width100 clearfix" style="display:none;" id="new-form-dialog-more">
					<fieldset><legend>Additional Options</legend>
						<div class="width33 float-left">
							<div style="margin-right: 5px;"
								<?php echo $this->form->getLabel('state'); ?>
								<?php echo $this->form->getInput('state'); ?>
							</div>
						</div>
						<div class="width33 float-left">
							<div style="margin-right: 5px;"
								<?php echo $this->form->getLabel('access'); ?>
								<?php echo $this->form->getInput('access'); ?>
							</div>
						</div>
						<div class="width33 float-left">
							<?php echo $this->form->getLabel('featured'); ?>
							<?php echo $this->form->getInput('featured'); ?>
						</div>
					</fieldset>
				</div>
			<div class="width100 clearfix button" style="margin: 5px 0px;">
				<span class="width50 float-left"><a href="javascript:dojo.byId('new-form-dialog-more').set('style', (!newFormDialogAdvancedOptions ? 'display: block;' : 'display: none;')); newFormDialogAdvancedOptions = !newFormDialogAdvancedOptions; void(0);">Additional Options <span></span></a></span>
				<button class="float-right button-primary" type="button" onClick="Joomla.submitbutton('form.apply');dijit.byId('new-form-dialog').hide();">Continue</button>
				<button class="float-right button-default" type="button" onClick="Joomla.submitbutton('form.cancel');dijit.byId('new-form-dialog').hide();">Cancel</button>
			</div>

			<input type="hidden" name="task" value="" />
			<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
			<?php echo JHtml::_( 'form.token' ); ?>
		</div>
	</div>
</form>
<?php endif;?>