<?php
$this->document->addScriptDeclaration('https://www.paypalobjects.com/js/external/dg.js');
$this->document->addScript("\n".'var paypalDialog = new PAYPAL.apps.DGFlow({ "paypal" });'."\n");

?>
<a href="https://www.sandbox.paypal.com/incontext?token=" id="paypal">
    <img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px; cursor: pointer;" />
</a>