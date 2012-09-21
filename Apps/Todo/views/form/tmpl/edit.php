<?php

// no direct access
defined('_JEXEC') or die;

//JHtml::_('behavior.keepalive');

// Create shortcut to parameters.
$params = $this->state->get('params');
?>

<script type="text/javascript">
	SubmitButton = function(task) {
		//if (task == 'todo.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			<?php //echo $this->form->getField('body')->save(); ?>
			document.getElementById('adminForm').submit();
		//} else {
			//alert('<?php //echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		//}
	}
</script>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
<?php if ($params->get('show_page_heading', 1)) : ?>
<h1>
	<?php echo $this->escape($params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<form action="<?php echo JRoute::_('index.php?option=com_todo&t_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<fieldset>
		<legend><?php echo JText::_('JEDITOR'); ?></legend>

			<div class="formelm">
			<?php echo $this->form->getLabel('subject'); ?>
			<?php echo $this->form->getInput('subject'); ?>
			</div>
			<?php echo $this->form->getInput('body'); ?>

			<div class="formelm-buttons">
			<button type="button" onclick="SubmitButton('todo.save')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="SubmitButton('todo.cancel')">
				<?php echo JText::_('JCANCEL') ?>
			</button>
			</div>

	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_TODO_PUBLISHING'); ?></legend>
		<div class="formelm">
		<?php echo $this->form->getLabel('catid'); ?>
		<span class="category">
			<?php   echo $this->form->getInput('catid'); ?>
		</span>

		</div>
	<?php if ($this->item->params->get('access-change')): ?>
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

	<?php endif; ?>
		<div class="formelm">
		<?php echo $this->form->getLabel('access'); ?>
		<?php echo $this->form->getInput('access'); ?>
		</div>
		<?php if (is_null($this->item->id)):?>
			<div class="form-note">
			<p><?php echo JText::_('COM_TODO_ORDERING'); ?></p>
			</div>
		<?php endif; ?>
	</fieldset>

    <input type="hidden" name="task" value="todo.save" />
    <input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
    <?php if($this->params->get('enable_category', 0) == 1) :?>
    <input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1);?>"/>
    <?php endif;?>
    <?php echo JHtml::_( 'form.token' ); ?>
</form>
</div>