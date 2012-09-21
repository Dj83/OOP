<?php
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if (task == 'bulletin.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task);
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($params->get('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_bulletins&b_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate box style">
	<fieldset>
		<legend><?php echo JText::_('JEDITOR'); ?></legend>

			<div class="formelm">
			<?php echo $this->form->getLabel('subject'); ?>
			<?php echo $this->form->getInput('subject'); ?>
			</div>

			<div class="formelm">
			<?php echo $this->form->getLabel('message'); ?>
			<?php echo $this->form->getInput('message'); ?>
			</div>

	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_BULLETINS_BASICS');?></legend>
			<div class="formelm">
			<?php echo $this->form->getLabel('usergroups'); ?>
			<?php echo $this->form->getInput('usergroups'); ?>
			</div>

	</fieldset>

	<fieldset>
		<legend><?php echo JText::_('COM_BULLETINS_FILES'); ?></legend>
			<div class="formelm">
			<?php echo $this->form->getLabel('attachments'); ?>
			<?php echo $this->form->getInput('attachments'); ?>
			</div>
	</fieldset>

	<?php if ($this->item->params->get('access-change')): ?>
	<fieldset>
		<legend><?php echo JText::_('COM_BULLETINS_PUBLISHING'); ?></legend>
		<div class="formelm">
		<?php echo $this->form->getLabel('priority'); ?>
		<?php echo $this->form->getInput('priority'); ?>
		</div>

		<div class="formelm">
		<?php echo $this->form->getLabel('state'); ?>
		<?php echo $this->form->getInput('state'); ?>
		</div>

		<div class="formelm">
		<?php echo $this->form->getLabel('publish_up'); ?>
		<?php echo $this->form->getInput('publish_up'); ?>
		</div>
		<div class="formelm">
		<?php echo $this->form->getLabel('publish_down'); ?>
		<?php echo $this->form->getInput('publish_down'); ?>
		</div>
	</fieldset>
	<?php endif; ?>
	<div class="formelm-buttons">
	<button type="button" class="button-primary" onclick="Joomla.submitbutton('bulletin.save')">
		<?php echo JText::_('JSAVE') ?>
	</button>
	<button type="button" class="button-default" onclick="Joomla.submitbutton('bulletin.cancel')">
		<?php echo JText::_('JCANCEL') ?>
	</button>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="jform[sendmessage]" value="1"/>
	<?php if($this->params->get('enable_category', 0) == 1) :?>
	<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
	<?php endif;?>
	<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>