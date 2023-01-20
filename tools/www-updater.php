<?php

/**
 * Wave Framework <http://github.com/kristovaher/Wave-Framework>
 * Self-Executed Updater Script
 *
 * This is an update script template, if file with this name is added to root archive of update zip 
 * files that is given to /tools/updater.php, then this script is also executed (and then removed). 
 * This script is useful to store in other update-related commands, such as file permissions, database 
 * changes and so on.
 *
 * @package    Tools
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/guide_tools.htm
 * @since      1.0.0
 * @version    3.7.1
 */

// It is always recommended to have this file return a plain-text log where each log entry is on a new line
header('Content-Type: text/plain;charset=utf-8');

// Error reporting is turned off in this script
error_reporting(0);

// Updater script calls this function always with specific version numbers that can be used within the script
if(isset($_GET['www-version'],$_GET['api-version'])){
	// Add here various functionality that is required for this version based on version numbers
	echo 'Applying version update on Wave Framework version '.$_GET['www-version'].' and API version '.$_GET['api-version'];
} else {
	// Version numbers are required
	echo 'Unable to run updater without assigned version numbers';
}

?>