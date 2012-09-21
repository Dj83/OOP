<div class="width100" style="overflow:hidden;">
<p><label>File Upload</label></p>
<?php $r= rand(); ?>
<input multiple="true" type="file" id="hiddenfile_<?php echo $r; ?>" style="width: 50%;visibility:hidden; max-height:22px; margin: 0px; padding:0px; border:0px;position:relative;z-index:-2;" accept="image/*|video/*|audio/*" onChange="dijit.byId('fileupload_<?php echo $r; ?>').set('value',this.value);" readonly="readonly" />
<div style="margin-top: -22px;" class="width100"><input type="text" style="margin-right: 5px;" id="fileupload_<?php echo $r; ?>" disabled="disabled" data-dojo-type="dijit/form/TextBox" /><a class="button-primary" href="javascript:document.getElementById('hiddenfile_<?php echo $r; ?>').click();void(0);">Upload A File</a></div>
</div>