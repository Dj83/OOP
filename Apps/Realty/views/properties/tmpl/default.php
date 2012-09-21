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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$params		= (isset($this->state->params)) ? $this->state->params : new JObject();
?>

<form action="<?php echo JRoute::_('index.php?option=com_realtor&view=properties'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_REALTOR_SEARCH_IN_ADDRESS'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">

			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
			<select name="filter_beds" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_REALTOR_FILTER_BEDROOMS');?></option>
				<?php echo JHtml::_('select.options', RealtorHelper::getRoomOptions('bedrooms'), 'value', 'text', $this->state->get('filter.bedrooms'), true);?>
			</select>
			<select name="filter_baths" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_REALTOR_FILTER_BATHROOMS');?></option>
				<?php echo JHtml::_('select.options', RealtorHelper::getRoomOptions('bathrooms'), 'value', 'text', $this->state->get('filter.bathrooms'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_REALTOR_HEADING_STREETNUMBERROUTE', 'a.street_number', $listDirn, $listOrder); ?>
				</th>
				<th width="20%" class="nowrap">
					<?php echo JText::_('COM_REALTOR_HEADING_CITY'); ?>
				</th>
				<th width="10%" class="nowrap">
					<?php echo JText::_('COM_REALTOR_HEADING_SUBURB'); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_REALTOR_HEADING_STATES', 'a.states_abbrev', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_REALTOR_HEADING_POSTALCODE', 'a.postal_code', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort',  'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_REALTOR_HEADING_FEATURED', 'a.featured', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'ordering');
			$canCreate	= $user->authorise('core.create',		'com_realtor');
			$canEdit	= $user->authorise('core.edit',			'com_realtor');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out==$user->get('id') || $item->checked_out==0;
			$canChange	= $user->authorise('core.edit.state',	'com_realtor') && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'properties.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_realtor&task=property.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->street_number . ' ' . $item->route); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->street_number . ' ' . $item->route); ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo $item->city;?>
				</td>
				<td class="center">
					<?php echo $item->suburb; ?>
				</td>
				<td class="center">
					<?php echo $item->states_abbrev; ?>
				</td>
				<td class="center">
					<?php echo $item->postal_code; ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'properties.', $canChange);?>
				</td>
				<td class="center">
					<?php echo $item->featured; ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
