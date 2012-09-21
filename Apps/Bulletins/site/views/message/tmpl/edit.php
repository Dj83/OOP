<?php
defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'message.cancel' || document.formvalidator.isValid(document.id('message-form'))) {
			Joomla.submitform(task, document.getElementById('message-form'));
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_bulletins&message_id='.$this->item->id); ?>" method="post" name="adminForm" id="message-form" class="form-validate">
	<div class="width-100">
		<fieldset class="adminform">
		<ul class="adminformlist">
			<li><?php echo $this->form->getLabel('user_id_to'); ?>
			<?php echo $this->form->getInput('user_id_to'); ?></li>

			<li><?php echo $this->form->getLabel('subject'); ?>
			<?php echo $this->form->getInput('subject'); ?></li>

			<li><?php echo $this->form->getLabel('message'); ?>
			<?php echo $this->form->getInput('message'); ?></li>
		</ul>
		</fieldset>
			<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('message.save')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('message.cancel')">
				<?php echo JText::_('JCANCEL') ?>
			</button>
			</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
