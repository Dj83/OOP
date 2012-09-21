<?php
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'bulletin.cancel' || document.formvalidator.isValid(document.id('bulletin-form'))) {
			Joomla.submitform(task, document.getElementById('bulletin-form'));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_bulletins&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="bulletin-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo empty($this->item->id) ? JText::_('COM_BULLETINS_NEW_BULLETIN') : JText::sprintf('COM_BULLETINS_BULLETIN_DETAILS', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li><?php echo $this->form->getLabel('subject'); ?>
				<?php echo $this->form->getInput('subject'); ?></li>

				<li><?php echo $this->form->getLabel('catid'); ?>
				<?php echo $this->form->getInput('catid'); ?></li>

				<li><?php echo $this->form->getLabel('state'); ?>
				<?php echo $this->form->getInput('state'); ?></li>

				<li><?php echo $this->form->getLabel('access'); ?>
				<?php echo $this->form->getInput('access'); ?></li>

				<li><?php echo $this->form->getLabel('usergroups'); ?>
				<?php echo $this->form->getInput('usergroups'); ?></li>

				<li><?php echo $this->form->getLabel('message'); ?>
				<?php echo $this->form->getInput('message'); ?></li>

				<li><?php echo $this->form->getLabel('ordering'); ?>
				<?php echo $this->form->getInput('ordering'); ?></li>

				<li><?php echo $this->form->getLabel('language'); ?>
				<?php echo $this->form->getInput('language'); ?></li>

				<li><?php echo $this->form->getLabel('id'); ?>
				<?php echo $this->form->getInput('id'); ?></li>
			</ul>
			<div class="clr"> </div>

		</fieldset>
	</div>

<div class="width-40 fltrt">
	<?php echo JHtml::_('sliders.start', 'bulletin-sliders-'.$this->item->id, array('useCookie'=>1)); ?>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_BULLETINS_GROUP_LABEL_PUBLISHING_DETAILS'), 'publishing-details'); ?>
		<fieldset class="panelform">
		<ul class="adminformlist">
			<?php foreach($this->form->getFieldset('publish') as $field): ?>
				<li><?php echo $field->label; ?>
					<?php echo $field->input; ?></li>
			<?php endforeach; ?>
			</ul>
		</fieldset>

	<?php echo JHtml::_('sliders.panel', JText::_('COM_BULLETINS_GROUP_LABEL_ATTACHMENT_DETAILS'), 'attachments-details'); ?>
		<fieldset class="panelform">
			<?php echo $this->form->getInput('attachments'); ?>
		</fieldset>

	<?php echo JHtml::_('sliders.end'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</div>

<div class="clr"></div>
</form>