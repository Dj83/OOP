<?php

// no direct access
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_SUBSCRIPTIONS_FEATURES_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_SUBSCRIPTIONS_FEATURES_BATCH_TIP'); ?></p>
	<?php echo JHtml::_('batch.language');?>

	<?php if ($published >= 0) : ?>
		<?php echo JHtml::_('batch.item', 'com_subscriptions.features');?>
	<?php endif; ?>

	<button type="submit" onclick="Joomla.submitbutton('feature.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-language-id').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
