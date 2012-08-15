<?php

/*
Wave Framework
Filesystem cleaner

This script is used to clear Wave Framework system generated filesystem. It should be used 
for debugging purposes during development or for maintenance once system is deployed. This 
script can be configured by running it with a GET variable. Possible values are 'maintenance' 
(clears only cache and temporary files), 'all' (clears everything), 'output' (clears output 
cache), 'images' (clears images cache), 'resources' (clears cache of JavaScript and CSS), 
'custom' (clears custom cache), 'tags' (clears cache tags), 'messenger' (clears State Messenger 
data), 'errors' (clears debugging errors), 'limiter' (clears request data of user agent IP's), 
'logs' (clears system logs), 'tokens' (clears API session tokens), 'tmp' (clears folder from 
everything that might be stored here), 'data' (clears the folder intended for database 
storage), 'backups' and 'updates' (clears folders that store backup and update archives) 
and 'userdata' (entirely custom storage folder). Please make sure to use 'all' carefully, 
since it might remove sensitive data. The script runs in 'maintenance' mode by default.

* It is recommended to remove all files from /tools/ subfolder prior to deploying project in live

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

// This initializes tools and authentication
require('.'.DIRECTORY_SEPARATOR.'tools_autoload.php');

// Log is printed out in plain text format
header('Content-Type: text/html;charset=utf-8');

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Cleaner</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width"/> 
		<link type="text/css" href="style.css" rel="stylesheet" media="all"/>
		<link rel="icon" href="../favicon.ico" type="image/x-icon"/>
		<link rel="icon" href="../favicon.ico" type="image/vnd.microsoft.icon"/>
		<meta content="noindex,nocache,nofollow,noarchive,noimageindex,nosnippet" name="robots"/>
		<meta http-equiv="cache-control" content="no-cache"/>
		<meta http-equiv="pragma" content="no-cache"/>
		<meta http-equiv="expires" content="0"/>
	</head>
	<body>
		<?php
		
		if(isset($_GET['cutoff'])){
			$cutoff=$_GET['cutoff'];
		} else {
			$cutoff=time();
		}

		// Header
		echo '<h1>Filesystem cleaner</h1>';
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		echo '<h2>Log</h2>';

		// Log will be stored in this array
		$log=array();

		// Clears /filesystem/cache/output/
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['output'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'output'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears images cache
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['images'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['resources'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['custom'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'custom'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache tags
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['tags'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'tags'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears cache of JavaScript and CSS
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['messenger'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'messenger'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears request data of user agent IP's
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['errors'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'errors'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears system log
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['logs'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears folder from everything that might be stored here
		if(isset($_GET['all']) || isset($_GET['maintenance']) || isset($_GET['tmp'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears request data of user agent IP's
		if(isset($_GET['all']) || isset($_GET['limiter'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'limiter'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears API session tokens
		if(isset($_GET['all']) || isset($_GET['tokens'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'tokens'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears backups
		if(isset($_GET['all']) || isset($_GET['backups'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'backups'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears update archive
		if(isset($_GET['all']) || isset($_GET['updates'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'updates'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears database folder
		if(isset($_GET['all']) || isset($_GET['data'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears custom user data folder
		if(isset($_GET['all']) || isset($_GET['userdata'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'userdata'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears certificate and key folder
		if(isset($_GET['all']) || isset($_GET['keys'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Clears static files from filesystem
		if(isset($_GET['all']) || isset($_GET['static'])){
			$directory='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'static'.DIRECTORY_SEPARATOR;
			$log=array_merge($log,dirCleaner($directory,$cutoff));
		}

		// Printing out log, if it is not empty
		if(!empty($log)){
			// Log is returned in plain text format, every log entry in a separate line
			echo '<p>';
			echo implode('</p><p>',$log);
			echo '</p>';
		} else {
			echo '<p class="box bold">Did not find files to clean</p>';
		}
		
		echo '<h2>Modes</h2>';
		echo '<p><a href="cleaner.php?maintenance&cutoff='.$cutoff.'">Maintenance</a> - Removes all cache and temporary files</p>';
		echo '<p><a href="cleaner.php?output&cutoff='.$cutoff.'">Output</a> - API and web cache</p>';
		echo '<p><a href="cleaner.php?images&cutoff='.$cutoff.'">Images</a> - On-demand images cache</p>';
		echo '<p><a href="cleaner.php?resources&cutoff='.$cutoff.'">Resources</a> - Static resources cache</p>';
		echo '<p><a href="cleaner.php?custom&cutoff='.$cutoff.'">Custom</a> - API created cache</p>';
		echo '<p><a href="cleaner.php?tags&cutoff='.$cutoff.'">Cache Tags</a> - Cache tag indexes</p>';
		echo '<p><a href="cleaner.php?messenger&cutoff='.$cutoff.'">State Messenger</a> - State Messenger database</p>';
		echo '<p><a href="cleaner.php?errors&cutoff='.$cutoff.'">Errors</a> - Debugging errors</p>';
		echo '<p><a href="cleaner.php?logs&cutoff='.$cutoff.'">Logs</a> - Request logs</p>';
		echo '<p><a href="cleaner.php?tmp&cutoff='.$cutoff.'">Temporary Files</a> - Temporary files only</p>';
		echo '<p><a href="cleaner.php?limiter&cutoff='.$cutoff.'">Limiter</a> - Request limiter database</p>';
		echo '<p><a href="cleaner.php?tokens&cutoff='.$cutoff.'">Tokens</a> - API session tokens</p>';
		echo '<p><a href="cleaner.php?backups&cutoff='.$cutoff.'">Backups</a> - Backup archive files</p>';
		echo '<p><a href="cleaner.php?updates&cutoff='.$cutoff.'">Updates</a> - Update archive files</p>';
		echo '<p><a href="cleaner.php?data&cutoff='.$cutoff.'">Data</a> - Database files, such as for SQLite</p>';
		echo '<p><a href="cleaner.php?userdata&cutoff='.$cutoff.'">User Data</a> - Custom files folder for project use</p>';
		echo '<p><a href="cleaner.php?keys&cutoff='.$cutoff.'">Keys</a> - Various cryptography keys</p>';
		echo '<p><a href="cleaner.php?static&cutoff='.$cutoff.'">Static</a> - Custom static files</p>';
		echo '<p><a href="cleaner.php?all&cutoff='.$cutoff.'">All</a> - <b>Caution!</b> Removes all created files, including potentially sensitive files</p>';
		
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' GMT '.date('P').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>