<?php

defined('_JEXEC') or die;
?>
<ul class="adminformlist">
	<li><?php echo $this->form->getLabel('metadesc'); ?>
	<?php echo $this->form->getInput('metadesc'); ?></li>

	<li><?php echo $this->form->getLabel('metakey'); ?>
	<?php echo $this->form->getInput('metakey'); ?></li>


<?php foreach($this->form->getGroup('metadata') as $field): ?>
	<li>
		<?php if (!$field->hidden): ?>
			<?php echo $field->label; ?>
		<?php endif; ?>
		<?php echo $field->input; ?>
	</li>
<?php endforeach; ?>
</ul>