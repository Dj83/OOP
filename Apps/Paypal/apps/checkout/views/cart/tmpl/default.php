<?php

// no direct access
defined('_JEXEC') or die;

//$this->document->addScriptDeclaration('https://www.paypalobjects.com/js/external/dg.js');
//$this->document->addScript("\n".'var paypalDialog = new PAYPAL.apps.DGFlow({ "paypal-checkout" });'."\n");

?>
<?php
if(empty($this->items)): ?>
	<p>Your cart is empty'</p>
<?php else: ?>
<table class="zebra" style="overflow:hidden; min-height: 200px;">
	<thead>
    	<tr>
        	<th width="50%">Name</th>
        	<th width="10%">Section</th>
        	<th width="10%">Post Code</th>
        	<th width="10%">Houses</th>
        	<th width="20%">Price</th>
        </tr>
    </thead>
    <tfoot colspan="5">
    	<tr>
        	<td></td>
        	<td></td>
        	<td></td>
        	<td></td>
        	<td></td>
		</tr>
    </tfoot>
    <tbody>
<?php 
$total = 0.00;
foreach($this->items as $i=> &$item): ?>
    	<tr class="row<?php echo $i % 2;?>">
        	<td class="center"><?php echo $item['name']; ?></td>
        	<td class="center"><?php echo $item['section']; ?></td>
        	<td class="center"><?php echo $item['postcode']; ?></td>
        	<td class="center"><?php echo $item['houses']; ?></td>
        	<td class="center"><?php echo $item['price']; ?></td>
        </tr>
<?php
$total = $total + $item['price'];
endforeach; ?>
		<tr class="center">
        	<td class="center"></td>
        	<td class="center"></td>
        	<td class="center" style="text-align: right; visibility; hidden; display: none;">Total: </td>
        	<td class="center" id="MCartTotal" style="text-align: right; visibility: hidden; display: none;">$ <?php echo $total; ?></td>
        </tr>
    </tbody>
</table>
<?php endif; ?>
