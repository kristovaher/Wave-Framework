<?php

/*
WWW - PHP micro-framework
WWW Framework Updater script

This is an update script template, if file with this name is added to root archive of update zip 
files that is given to /tools/updater.php, then this script is also executed (and then removed). 
This script is useful to store in other update-related commands, such as file rights, database 
changes and so on.

Author and support: Kristo Vaher - kristo@waher.net
*/

// It is always recommended to have this file return a plain-text log where each log entry is on a new line
header('Content-Type: text/plain;charset=utf-8');

// Error reporting is turned off in this script
error_reporting(0);

// Updater script calls this function always with specific version numbers that can be used within the script
if(isset($_GET['www-version']) && isset($_GET['system-version'])){

	// add here various functionality that is required for this version
	echo 'Doing version update on WWW Framework version '.$_GET['www-version'].' and system version '.$_GET['system-version'];
	
} else {

	// Version numbers are required
	echo 'Cannot run updater without version numbers';

}

?>