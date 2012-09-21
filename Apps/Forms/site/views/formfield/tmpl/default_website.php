<div class="width100">
<p><label>Website</label></p>
<input type="text" style="width: 50%;" onChange="function changeme(e){console.log(e); var i = (e.get('value')+'').indexOf('http://', (0 || 0)); checked = (i === -1) ? 'http://'+e.get('value') : e.get('value'); e.set('value',checked);
}
" data-dojo-props="intermediateChanges: false,regExp:'(https?:\/\/|)([\da-z-]+\.){0,2}?([^\.]+)\.(\w{2}|(com|com.au|com.uk|co|co.au|co.uk|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$'" data-dojo-type="dijit/form/ValidationTextBox" />
</div>