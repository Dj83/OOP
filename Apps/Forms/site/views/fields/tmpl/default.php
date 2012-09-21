<?php if(!$this->form_id): ?>
<p class="box-download">Field<strong>s</strong> view loaded!</p>
TODO: Load some fields in here, adding an event listener on each container
to be able to change the backgorund class and also load its form!
<?php else: ?>
<?php echo $this->form_id; ?>
<?php endif; ?>