<?php
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/bulletins.php';
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::addIncludePath(JPATH_BASE.'/administrator/components/com_messages/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_bulletins.category');
$saveOrder	= $listOrder=='ordering';
$params		= (isset($this->state->params)) ? $this->state->params : new JObject();
$canCreate	= $user->authorise('core.create','com_bulletins');
?>
<form action="<?php echo JRoute::_('index.php?option=com_bulletins'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="float-left">
			<label class="filter-search-lbl" style="min-width:50px; float: left;" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_BULLETINS_SEARCH_IN_BULLETIN_SUBJECT'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="width50 float-right">

			<select name="filter_category_id" class="inputbox float-right" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_bulletins'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
			<select name="filter_priority" class="inputbox float-right" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_BULLETINS_SELECT_PRIORITY');?></option>
				<?php echo JHtml::_('select.options', BulletinsHelper::getPriorityOptions(), 'value', 'text', $this->state->get('filter.priority'));?>
			</select>
		</div>
	</fieldset>
	<div class="width100 grid-block clearfix">
	<a href="#" onclick="return Joomla.submitbutton('bulletins.publish')">Publish</a> |
	<a href="#" onclick="return Joomla.submitbutton('bulletins.unpublish')">Unpublish</a> |
	<a href="#" onclick="return Joomla.submitbutton('bulletins.trash')">Trash</a>
	</div>
	<div class="clearfix"> </div>

	<table class="zebra">
		<caption><?php echo JText::_('COM_BULLETINS_BULLETINS_LIST_CAPTION'); ?></caption>
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th></th>
				<th width="20%">
					<?php echo JHtml::_('grid.sort',  'COM_BULLETINS_HEADING_SUBJECT', 'a.subject', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JText::_('COM_BULLETINS_HEADING_MESSAGE'); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" class="center">
					<?php echo JHtml::_('grid.sort', 'COM_BULLETINS_HEADING_PRIORITY', 'a.priority', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder): ?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'bulletins.saveorder'); ?>
					<?php endif;?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'ordering');
			//$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_bulletins&task=edit&type=other&cid[]='. $item->catid);
			$canCreate	= $user->authorise('core.create',		'com_bulletins.category.'.$item->catid);
			$canEdit	= $user->authorise('core.edit.own',		'com_bulletins.category.'.$item->catid);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_bulletins.category.'.$item->catid) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if($item->checked_out): ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'bulletins.', $canCheckin); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($item->checked_out || !$canEdit) : ?>
							<?php echo $this->escape($item->subject); ?>
					<?php else: ?>
						<a href="<?php echo JRoute::_('index.php?option=com_bulletins&task=bulletin.edit&b_id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->subject); ?></a>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $this->escape($item->message); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'bulletins.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<td class="center">
					<?php echo BulletinsHelper::getPriorityToggler($item->priority); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) : ?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->catid == $item->catid), 'bulletins.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->catid == $item->catid), 'bulletins.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->catid == $item->catid), 'bulletins.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->catid == $item->catid), 'bulletins.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled; ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php //Load the batch processing form. ?>
	<?php //echo $this->loadTemplate('batch'); ?>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>