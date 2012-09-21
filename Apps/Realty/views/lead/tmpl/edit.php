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
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'lead.cancel' || document.formvalidator.isValid(document.id('lead-form'))) {
			Joomla.submitform(task, document.getElementById('lead-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_realtor&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="lead-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_REALTOR_NEW_LEAD') : JText::sprintf('COM_REALTOR_LEAD_DETAILS', isset($this->item->contact->fullname) ? $this->item->contact->fullname : isset($this->item->firstname) ? $this->item->firstname : '' ); ?></legend>
			<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('property') as $field): ?>
                <li><?php echo $field->label; ?>
                    <?php echo $field->input; ?></li><?php endforeach; ?>
                <li><?php echo $this->form->getLabel('type'); ?>
				<?php echo $this->form->getInput('type'); ?></li>

				<li><?php echo $this->form->getLabel('notes'); ?>
				<?php echo $this->form->getInput('notes'); ?></li>
			</ul>
			<div class="clr"> </div>

		</fieldset>
	</div>

<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start', 'realtor-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_CONTACT_DETAILS'), 'contact-details'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('contact') as $field): ?>
				<li><?php echo $field->label; ?>
					<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_PROPERTY_DETAILS'), 'property-details'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('details') as $field): ?>
				<li><?php echo $field->label; ?>
					<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_PROPERTY_FEATURES'), 'property-features'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('features') as $field): ?>
				<li><?php echo $field->label; ?>
					<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_REALTOR_GROUP_LABEL_PUBLISHING_DETAILS'), 'publishing-details'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('publish') as $field): ?>
				<li><?php echo $field->label; ?>
					<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
	<?php echo JHtml::_('sliders.end'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</div>

<div class="clr"></div>
</form>
