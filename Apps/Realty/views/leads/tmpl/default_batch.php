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

// Create the copy/move options.
$options = array(
	JHtml::_('select.option', 'add', JText::_('COM_REALTOR_BATCH_ADD')),
	JHtml::_('select.option', 'del', JText::_('COM_REALTOR_BATCH_DELETE')),
	JHtml::_('select.option', 'set', JText::_('COM_REALTOR_BATCH_SET'))
);

?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_REALTOR_BATCH_OPTIONS');?></legend>
	<label id="batch-choose-action-lbl" for="batch-choose-action"><?php echo JText::_('COM_REALTOR_BATCH_GROUP') ?></label>

	<button type="submit" onclick="Joomla.submitbutton('user.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-group-id').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>