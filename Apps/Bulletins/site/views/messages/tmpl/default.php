<?php
defined('_JEXEC') or die;

// Include the component HTML helpers.
require_once JPATH_COMPONENT.'/helpers/bulletins.php';
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::addIncludePath(JPATH_BASE.'/administrator/components/com_messages/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canCreate	= $user->authorise('core.create','com_messages');
?>

<form action="<?php echo JRoute::_('index.php?option=com_bulletins&view=messages'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="float-left width50">
			<label class="filter-search-lbl" style="min-width:50px; float: left;" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BULLETINS_SEARCH_IN_MESSAGE_SUBJECT'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="float-right width50">
			<select name="filter_state" class="inputbox float-right" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', BulletinsHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>
			<select name="filter_priority" class="inputbox float-right" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_BULLETINS_SELECT_PRIORITY');?></option>
				<?php echo JHtml::_('select.options', BulletinsHelper::getPriorityOptions(), 'value', 'text', $this->state->get('filter.priority'));?>
			</select>
		</div>
	</fieldset>
	<div class="width100 grid-block clearfix">
	<?php if($canCreate):?>
	<a href="#" onclick="return Joomla.submitbutton('message.add')">Add</a> |
	<?php endif; ?>
	<a href="#" onclick="return Joomla.submitbutton('messages.publish')">Read</a> |
	<a href="#" onclick="return Joomla.submitbutton('messages.unpublish')">Unread</a> |
	<a href="#" onclick="return Joomla.submitbutton('messages.trash')">Trash</a>
	</div>
	<div class="clearfix"> </div>
	<ul class="" style="padding:0;margin:0;list-style-type:none;">
		<?php foreach ($this->items as $i => $item) :
			$canChange	= $user->authorise('core.edit.state', 'com_messages');
			$canGrabAttachments	= $user->authorise('core.files.download', 'com_messages');
			$box_class = JHtml::_('bulletins.statebox', $item->state);
			?>
		<li class="width100 clearfix <?php echo $box_class; ?>" style="margin-bottom: 10px; vertical-align: middle; background-image: none; padding-left: 5px;">
			<div class="userInfo" style="vertical-align:middle">
				<span class="float-left" style="display: inline-block; padding-top: 20px;"><?php echo JHtml::_('grid.id', $i, $item->message_id); ?></span>
				<div style="display: inline; float: left;">
					<?php echo JHtml::_('bulletins.fromthumb',$item->user_id_from, $item->user_from); ?>
					<p><?php echo JHtml::_('bulletins.messagestate', $item->state, $i, $canChange); ?><?php echo JHtml::_('bulletins.messagetrash', $i, $canChange); ?><?php echo JHtml::_('bulletins.priority',$item->priority, $i, $canChange); ?><?php echo JHtml::_('bulletins.attachment',$item->message_id, $canGrabAttachments); ?></p>
				</div>
				<div class="userShout" style="margin: 0px; padding: 0px 5px 0px 80px;">
					<p class="discussionTitle" style="margin: 0px; padding: 0px; font-size: 90%; line-height: 17px;"><a href="javascript:void(0) return false;"><?php echo $this->escape($item->subject); ?></a></p>
					<p class="shout" style="display: block; margin: 0px; padding: 0px; line-height: 14px; font-size: 80%;"></p>
					<p style="margin: 0px; padding: 0px; line-height: 16px; font-size: 90%;"><?php echo $this->escape($item->message); ?></p>
					<p class="status" style="margin: 0px; padding: 0px; text-transform: uppercase; font-size: 75%; line-height: 12px;"><?php echo $item->user_from; ?> - <?php echo JHtml::_('date', $item->date_time, JText::_('DATE_FORMAT_LC2')); ?></p>
				</div>
			</div>
		</li>
			<?php endforeach; ?>
	</ul>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>