<?php

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'feature.cancel' || document.formvalidator.isValid(document.id('feature-form'))) {
			Joomla.submitform(task, document.getElementById('feature-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_subscriptions&view=feature&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="feature-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_SUBSCRIPTIONS_NEW_FEATURE') : JText::sprintf('COM_SUBSCRIPTIONS_FEATURE_DETAILS', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('label'); ?>
				<?php echo $this->form->getInput('label'); ?></li>

				<li><?php echo $this->form->getLabel('alias'); ?>
				<?php echo $this->form->getInput('alias'); ?></li>

				<li><?php echo $this->form->getLabel('type'); ?>
				<?php echo $this->form->getInput('type'); ?></li>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>

				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>

				<li><?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?></li>

				<li><?php echo $this->form->getLabel('note'); ?>
				<?php echo $this->form->getInput('note'); ?></li>

				<li><?php echo $this->form->getLabel('message'); ?>
				<?php echo $this->form->getInput('message'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
			</ul>
			<div class="clr"> </div>

		</fieldset>
	</div>

<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start', 'feature-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_SUBSCRIPTIONS_GROUP_LABEL_PUBLISHING_DETAILS'), 'publishing-details'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('publish') as $field): ?>
				<li><?php echo $field->label; ?>
					<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>
	<?php echo JHtml::_('sliders.panel', JText::_('COM_SUBSCRIPTIONS_GROUP_LABEL_PARAMS_DETAILS'), 'params-details'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('params') as $field): ?>
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
