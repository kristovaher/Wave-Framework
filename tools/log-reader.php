<?php

/*
Wave Framework
Log reader for reading performance logs

This is a simple script that is used to read log files stored by WWW_Logger class. 
It loads a log file defined by specific timestamp formatted as Y-m-d-H. By default 
it displays information about all the requests that have happened in the current 
hour, but if GET variable 'log' is supplied in same timestamp format, then that 
log file data is returned instead.

* It is recommended to remove all files from /tools/ subfolder prior to deploying project in live

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

// This initializes tools and authentication
require('.'.DIRECTORY_SEPARATOR.'tools_autoload.php');

// Log is printed out in plain text format
header('Content-Type: text/html;charset=utf-8');

// Checking if logger attempts to read internal log
if(isset($_GET['internal'])){

	// Actual log address
	$logAddress='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'internal.log';
	
	// If file is set for deletion
	if(isset($_GET['delete']) && file_exists($logAddress)){
		unlink($logAddress);
		// Redirecting to link without delete flag set
		header('Location: log-reader.php?internal');
		die();
	}
	
} elseif(isset($_GET['api'])){

	// Actual log address
	$logAddress='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'api.log';
	
	// If file is set for deletion
	if(isset($_GET['delete']) && file_exists($logAddress)){
		unlink($logAddress);
		// Redirecting to link without delete flag set
		header('Location: log-reader.php?api');
		die();
	}

} else {

	// Log reader can access any log file created by the system
	if(isset($_GET['log'])){
		// User agent requested input URL is validated against hostile characters
		$logFileName=preg_replace('/[^A-Za-z\-\_0-9\/]/i','',$_GET['log']);
	} else {
		// By default the results are returned from current hour
		header('Location: log-reader.php?log='.date('Y-m-d-H'));
		die();
	}
		
	// This stores the array types to print out
	$types=array();

	// You can print out only some log information
	if(isset($_GET['types'])){
		$rawTypes=explode(',',$_GET['types']);
		foreach($rawTypes as $t){
			$bits=explode('[',$t);
			if(isset($bits[1])){
				$types[$t]=str_replace(']','',$bits[1]);
			} else {
				$types[$t]=true;
			}
		}
	} else {
		$types['all']=true;
	}

	// Every day the logs are stored under different log subfolder
	$logSubfolder=substr($logFileName,0,10);
	
	// Actual log address
	$logAddress='..'.DIRECTORY_SEPARATOR.'filesystem'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.$logSubfolder.DIRECTORY_SEPARATOR.$logFileName.'.log';
	
	// If file is set for deletion
	if(isset($_GET['delete']) && file_exists($logAddress)){
		unlink($logAddress);
		unset($_GET['delete']);
		// Redirecting to link without delete flag set
		if(!empty($_GET)){
			header('Location: log-reader.php?'.http_build_query($_GET));
		} else {
			header('Location: log-reader.php');
		}
		die();
	}
	
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?=(isset($_GET['internal']))?'Internal Log':(isset($_GET['api']))?'API Log':'Log Reader'?></title>
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
		
		// Header
		if(isset($_GET['internal'])){
			echo '<h1>Internal Log</h1>';
		} elseif(isset($_GET['api'])){
			echo '<h1>API Log</h1>';
		} else {
			echo '<h1>HTTP Request Log</h1>';
		}
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		echo '<h2>Log</h2>';

			// All logs are stored in /log/ folder, if a folder does not exist
			if(file_exists($logAddress)){
			
				// File delete link
				echo '<h3 onclick="if(confirm(\'Are you sure?\')){ document.location.href=document.location.href+\'&delete\'; }" class="red bold" style="cursor:pointer;">Click to delete this log</h3>';

				// Log files are stored as JSON serialized arrays, separated with line-breaks
				$log=explode("\n",file_get_contents($logAddress));
				
				// Output buffer allows to increase peformance due to multiple echo's
				ob_start();
					
				// This variable holds various summary statistics
				$summary=array();
				
				// Printing out every line from the log file
				foreach($log as $l){
					
					if(isset($_GET['api']) && $l!=''){
						$logDetails=explode("\t",$l);
						echo '<div class="block">';
							// Printing out log data
							echo '<b>'.date('d.m.Y H:i:s',$logDetails[0]).'</b> '.$logDetails[1].' -&gt; '.$logDetails[2].'<br/>';
						echo '</div>';
						if($logDetails[0]>=($_SERVER['REQUEST_TIME']-2592000)){
							$summary['30days'][$logDetails[1]][$logDetails[2]]++;
						}
						$summary['totals'][$logDetails[1]][$logDetails[2]]++;
					} else {
						// Log data is deencoded from JSON string
						$l=json_decode($l,true);
						// Log entry should be an array once decoded
						if(is_array($l)){
							$accepted=true;
							// Breaking out of the loop if the assigned key value is not the one that is required
                            if(isset($types)){
                                foreach($types as $key=>$t){
                                    if($key!='all' && $t!==true){
                                        if(!isset($l[str_replace('['.$t.']','',$key)]) || $l[str_replace('['.$t.']','',$key)]!=$t){
                                            $accepted=false;
                                        }
                                    }
                                }
                            }
							if($accepted){
								echo '<div class="border block">';
								// Printing out log data
								foreach($l as $key=>$entry){
									if(isset($_GET['internal']) || isset($types['all']) || isset($types[$key])){
										if(!is_array($entry)){
											echo '<b>'.$key.':</b> '.$entry.'<br/>';
										} else {
											echo '<b>'.$key.':</b>';
											echo '<pre class="small box disabled">';
											print_r($entry);
											echo '</pre>';
										}
									}
								}
								echo '</div>';
							}
						}
					}
					
				}
				
				// Getting the content from output buffer
				$logContent=ob_get_clean();
				
				// Output buffer allows to increase peformance due to multiple echo's
				ob_start();
					
				// If summary data is set
				if(!empty($summary)){
				
					echo '<h2>Summary</h2>';
				
					if(isset($_GET['api'])){
						
						// Summary for 30 Days
						echo '<h3>Lat 30 Days</h3>';
						foreach($summary['30days'] as $profile=>$data){
							echo '<h4><b>'.$profile.'</b></h4>';
							echo '<ul>';
							foreach($data as $command=>$d){
								echo '<li><b>'.$command.'</b> '.$d.' calls</li>';
							}
							echo '</ul>';
						}
						
						// Printing out summary for totals
						echo '<h3>Totals</h3>';
						foreach($summary['totals'] as $profile=>$data){
							echo '<h4><b>'.$profile.'</b></h4>';
							echo '<ul>';
							foreach($data as $command=>$d){
								echo '<li><b>'.$command.'</b> '.$d.' calls</li>';
							}
							echo '</ul>';
						}
						
					}
					
					echo '<h2>Entries</h2>';
				}
				
				// Getting the content from output buffer
				$summaryContent=ob_get_clean();
				
				// Printing out summary and log
				echo $summaryContent;
				echo $logContent;
				
			} else {
				// Log information not found
				echo '<p class="red bold">Cannot find log information</p>';
			}
			
		echo '<h2>Modes</h2>';
		echo '<p><a href="log-reader.php">HTTP Request Log</a> - Log information about HTTP requests</p>';
		echo '<p><a href="log-reader.php?internal">Internal Log</a> - Internal log entries</p>';
		echo '<p><a href="log-reader.php?api">API Log</a> - API profile call log</p>';
		
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' GMT '.date('P').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>