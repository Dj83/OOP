<div style="display:none;">
<div data-dojo-type="dijit/Dialog" data-dojo-props="href: '<?php 
	echo JRoute::_('index.php?option=com_forms&view=field&layout=edit&formid=&type=&task=field.add'); 
	?>'" data-dojo-id="formFieldSingleLineText" title="Single Line Text">
    <div class="dijitDialogPaneActionBar">
        <a href="javascript:void(0) return false;" onClick="formFieldSingleLineText.onHide();" class="button-primary">Continue</a>
        <a href="javascript:void(0) return false;" onClick="formFieldSingleLineText.onCancel();" class="button-primary">Cancel</a>
    </div>
</div>
<div data-dojo-type="dijit/Dialog" data-dojo-props="href: '<?php 
	echo JRoute::_('index.php?option=com_forms&view=field&layout=edit&task=field.add&formid=&fieldtype=text'); 
	?>'" data-dojo-id="formFieldParagraphText" title="Single Line Text">
    <div class="dijitDialogPaneActionBar">
        <a href="javascript:void(0) return false;" onClick="formFieldParagraphText.onHide();" class="button-primary">Continue</a>
        <a href="javascript:void(0) return false;" onClick="formFieldParagraphText.onCancel();" class="button-primary">Cancel</a>
    </div>
</div>
</div>
<div class="width100 grid-block">
	<ul class="fields width50 grid-box">
		<li onClick="formFieldSingleLineText.show();">Single Line Text</li>
		<li onClick="formFieldParagraphText.show();">Paragraph Text</li>
		<li>Multiple Choice</li>
		<li>Name</li>
		<li>Time</li>
		<li>Date</li>
		<li>Address</li>
		<li>Price</li>
		<li>Matrix Choice</li>
		<li>Section Break</li>
	</ul>
	<ul class="fields width50 grid-box">
		<li>Signature</li>
		<li>Number</li>
		<li>Checkboxes</li>
		<li>List</li>
		<li>Phone</li>
		<li>Website</li>
		<li>Email</li>
		<li>File Upload</li>
		<li>Page Break</li>
	</ul>
</div>