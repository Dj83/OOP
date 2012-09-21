<?php

function com_install()
{
	// We could add resources to the main index file here if we wanted to!
	// We could even add libraries!
	$html = array();
	$html[] = '<h1>The installation has completed!</h1>';
	$html[] = '<p>Get started by creating your <a href="index.php?option=com_forms&view=subscription&task=form.add" title="Click here to begin creating your first form!">first form</a></p>';
	echo implode("\n", $html);
}