<?php if (isset($this->item->attachments)):?>
<?php foreach ($this->item->attachments as $key => $attachment):?>
<?php
$filename = explode('/', $attachment);
?>
<a href="<?php echo JURI::root(). $attachment; ?>" title=""><?php echo $filename[(count($filename)-1)]; ?></a>
<?php endforeach; ?>
<?php else: ?>
<p class="box-download">There are no attachments</p>
<?php endif; ?>