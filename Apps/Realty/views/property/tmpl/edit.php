<?php
/**
 * @package     Realtor.Administrator
 * @subpackage  com_realtor
 *
 * @copyright   Copyright (C) 2012 TODO:. All rights reserved.
 * @license     see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$canDo	= &$this->canDo;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		//if (task == 'property.cancel' || document.formvalidator.isValid(document.id('propertyForm'))) {
			<?php echo $this->form->getField('note')->save(); ?>
			Joomla.submitform(task, document.getElementById('propertyForm'));
		//}
	}
var locationToggler;
/*require(["dojo/ready", "dojo/parser", "dijit/registry", "dojo/dom-style", "dijit/form/Button", "dojo/fx"], function(ready, parser, registry, domStyle, Button, MapFx){
	ready(function(){
		new Button({class: 'locationToggleButton'},'showLocationMap');
		new Button({class: 'locationToggleButton'},'hideLocationMap');
		locationToggler = new MapFx.Toggler({
			node: dojo.byId("locationmapcontainer"),
			showDuration: 400,
			hideDuration: 1200,
			showFunc: MapFx.wipeIn,
			hideFunc: MapFx.wipeOut
		});
		dojo.connect(dijit.byId("showLocationMap"), "onClick", locationToggler, "show");
		dojo.connect(dijit.byId("hideLocationMap"), "onClick", locationToggler, "hide");
    });
});
*/</script>
<style>
.claro .dijitButton .dijitButtonNode, .claro .locationToggleButton .dijitButtonNode, .claro .dijitButtonNode{
	background-color: #FFFFFF;
    background-image: none;
    background-position: center top;
    background-repeat: repeat-x;
    border: none;
    border-radius: 0px;
    box-shadow: none;
    color: #000000;
    padding: 0px;
    -moz-transition-duration: 0.3s;
    -moz-transition-property: background-color;
    cursor: pointer;
    line-height: normal;
}
</style>
<form action="<?php echo JRoute::_('index.php?option=com_realtor&view=property&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="propertyForm" class="form-validate">

<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo empty($this->item->id) ? JText::_('COM_REALTOR_NEW_PROPERTY') : JText::sprintf('COM_REALTOR_EDIT_PROPERTY', $this->item->formatted_address); ?></legend>
		<ul class="adminformlist">
				<li>
					<?php echo $this->form->getLabel('formatted_address'); ?>
					<?php echo $this->form->getInput('formatted_address'); ?>
                    <span style="width: 100%; overflow: hidden; float: right;">
                    	<a href="javascript:void() return true;" id="showLocationMap">Show Map</a>
                        <a href="javascript:void() return true;" id="hideLocationMap">Hide Map</a></span>
                </li>

			<?php foreach($this->form->getFieldset('location') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>

				<?php echo $this->form->getLabel('note'); ?>
                <div class="clr"></div>
                <?php echo $this->form->getInput('note'); ?>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>

				<li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>

				<?php if ($this->canDo->get('core.admin')): ?>
					<li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
						<div class="button2-left"><div class="blank">
							<button type="button" onclick="document.location.href='#access-rules';">
								<?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
							</button>
						</div></div>
					</li>
				<?php endif; ?>

				<li><?php echo $this->form->getLabel('featured'); ?>
				<?php echo $this->form->getInput('featured'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
		</ul>
	</fieldset>
</div>

<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start', 'realtor-property-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_FEATURES_OPTIONS'), 'features'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('features') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_PROPERTY_OPTIONS'), 'property'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('property') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_SELL_OPTIONS'), 'sell'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('sell') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_FILES_OPTIONS'), 'files'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('files') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_PUBLISHING_OPTIONS'), 'publish'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('publish') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'metadata'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('metadata') as $field): ?>
				<li>
					<?php if (!$field->hidden): ?>
						<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
	<?php echo JHtml::_('sliders.end'); ?>
</div>
<div class="clr"></div>
<?php if ($this->canDo->get('core.admin')): ?>
    <div class="width-100 fltlft">
        <?php echo JHtml::_('sliders.start', 'permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

            <?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_PROPERTY_RULES'), 'access-rules'); ?>
            <fieldset class="panelform">
                <?php echo $this->form->getLabel('rules'); ?>
                <?php echo $this->form->getInput('rules'); ?>
            </fieldset>

        <?php echo JHtml::_('sliders.end'); ?>
    </div>
<?php endif; ?>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
<div class="clr"></div>
</form>
