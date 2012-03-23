<?php

/*
WWW - PHP micro-framework
Logger class

This is an optional class that is used to log requests on system by Index gateway. 
WWW_Logger is used to keep track of performance of requests made to Index gateway files, 
it keeps track of how long the request took, what data it included, how high was the 
memory use, server load and so on. If this logger is used, every single request is logged 
as serialized arrays in filesystem in /filesystem/log/ subfolders. It is a good idea to 
clean that folder every now and then. There is a separate script for reading those log 
files situated in /tools/read-log.php.

* Logger expects /filesystem/log/ to be writeable

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_Logger {

	// Request execution time
	private $requestMicrotime=false;
	
	// Directory of log files
	private $logDir;
	
	// Data types setting, comma separated list of keywords of data to be logged
	private $loggedData='all';
	
	// Database connection for database performance logging
	public $databaseConnection=false;
	
	// If cache was used
	public $cacheUsed=false;
	
	// If cache was used
	public $profile='public';
	
	// Default values are assigned when Logger is constructed
	// It is good to initiate Logger as early as possible
	// * loggerDir - location of Logger script relative to the script that loads Logger
	// * logDir - location of directory to store log files at
	public function __construct($loggedData='all',$logDir){
	
		// Comma separated list of keys that log should store
		$this->loggedData=$loggedData;
		
		// We record the start time of the request
		$this->requestMicrotime=microtime(true);
		
		// Checking if log directory is valid
		if(is_dir($logDir)){
			// Log directory is assigned
			$this->logDir=$logDir;
		} else {
			// Assigned folder is not detected as being a folder
			trigger_error('Assigned log folder does not exist',E_USER_ERROR);
		}
		
	}
	
	// Writes log entry to file system
	// * profile - API profile used with this request, useful for tracking which API profile is causing problems
	// * cacheUsed - Used by Index gateway to set if cache was used during request, good for grading performance
	// Returns true if entry written to log, triggers an error if file cannot be written
	public function writeLog($category='index'){
	
		// If logged data setting is not set to 'all', then it loads the setting by exploding the setting string
		if($this->loggedData!='all'){
			$this->loggedData=explode(',',$this->loggedData);
		}
	
		// All log data is gathered to this variable
		$logData=array();
		
		// For performance reasons it is possible to only store specific type of logged data
		// By default all type of data is returned
		
		// Profile stores API profile that is used for this request
		// Public profiles are always stored as 'public' and don't require API validations
		if($this->loggedData=='all' || in_array('api-profile',$this->loggedData)){ 
			$logData['api-profile']=$this->profile; 
		}
		
		//  This is for categorizing various calls, used mainly by Index gateway to separate requests of different type.
		if($this->loggedData=='all' || in_array('category',$this->loggedData)){ 
			$logData['category']=$category;
		}
		
		// Unique request identifier set by the server.
		if($this->loggedData=='all' || in_array('request-id',$this->loggedData)){ 
			if(isset($_SERVER['UNIQUE_ID'])){ $logData['request-id']=$_SERVER['UNIQUE_ID']; } // This is not supported on Windows
		}
		
		// This stores the URI that client requested.
		if($this->loggedData=='all' || in_array('request',$this->loggedData)){ 
			$logData['request']=$_SERVER['REQUEST_URI'];
		}
		
		// Timestamp of the request in milliseconds.
		if($this->loggedData=='all' || in_array('microtime',$this->loggedData)){ 
			$logData['microtime']=$this->requestMicrotime;
		}
		
		// UNIX timestamp of the request time.
		if($this->loggedData=='all' || in_array('time',$this->loggedData)){ 
			$logData['time']=$_SERVER['REQUEST_TIME'];
		}
		
		// Stores request datetime in format 'Y-m-d H:i:s'.
		if($this->loggedData=='all' || in_array('datetime',$this->loggedData)){ 
			$logData['datetime']=date('Y-m-d H:i:s');
		}
		
		// Stores IP of the client that made the request.
		if($this->loggedData=='all' || in_array('ip',$this->loggedData)){ 
			$logData['ip']=$_SERVER['REMOTE_ADDR'];
		}
		
		// This stores user agent string of the client.
		if($this->loggedData=='all' || in_array('user-agent',$this->loggedData)){ 
			$logData['user-agent']=(isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:'Unknown';
		}
		
		// GET variables submitted by client.
		if($this->loggedData=='all' || in_array('get',$this->loggedData)){ 
			if(isset($_GET) && !empty($_GET)){ $logData['get']=$_GET; }
		}
		
		// POST variables submitted by client
		if($this->loggedData=='all' || in_array('post',$this->loggedData)){ 
			if(isset($_POST) && !empty($_POST)){ $logData['post']=$_POST; }
		}
		
		// FILES variables submitted by client
		if($this->loggedData=='all' || in_array('files',$this->loggedData)){ 
			if(isset($_FILES) && !empty($_FILES)){ $logData['files']=$_FILES; }
		}
		
		// SESSION variables submitted by client
		if($this->loggedData=='all' || in_array('session',$this->loggedData)){ 
			if(isset($_SESSION) && !empty($_SESSION)){ $logData['session']=$_SESSION; }
		}
		
		// COOKIE variables submitted by client
		if($this->loggedData=='all' || in_array('cookie',$this->loggedData)){ 
			if(isset($_COOKIE) && !empty($_COOKIE)){ $logData['cookie']=$_COOKIE; }
		}
		
		// Flag that defines whether cache has been used with this request.
		// This is not detected by Logger itself and is instead sent to Logger by other parts of the system
		if($this->loggedData=='all' || in_array('cache-used',$this->loggedData)){ 
			$logData['cache-used']=($this->cacheUsed==true)?1:0;
		}
		
		// This stores how long the request took in seconds.
		if($this->loggedData=='all' || in_array('execution-time',$this->loggedData)){ 
			$logData['execution-time']=(microtime(true)-$this->requestMicrotime);
		}
		
		// This stores the peak usage of memory during the request.
		if($this->loggedData=='all' || in_array('memory-peak-usage',$this->loggedData)){ 
			$logData['memory-peak-usage']=memory_get_peak_usage(); // In bytes, divide by 1048576 to get megabytes
		}
		
		// If set, this stores how many queries were sent to database.
		// This information is tracked by Database class itself
		if($this->loggedData=='all' || in_array('database-query-count',$this->loggedData)){ 
			if($this->databaseConnection!=false){
				$logData['database-query-count']=$this->databaseConnection->queryCounter;
			}
		}
		
		// This stores system load number (that is one minute old).
		// This is not supported on Windows
		if($this->loggedData=='all' || in_array('system-load',$this->loggedData)){ 
			if(function_exists('sys_getloadavg')){
				$load=sys_getloadavg(); //last 1, 5 and 15 minutes (number of processes in run queue)
				$logData['system-load']=$load[0];
			}
		}
		
		// Log filename is current time-based with present hour in the end
		$logFileName=date('Y-m-d-H');
		
		// Finding the subfolder of the log file, if subfolder does not exist then it is created
		$logSubfolder=substr($logFileName,0,10);
		if(!is_dir($this->logDir.$logSubfolder.DIRECTORY_SEPARATOR)){
			if(!mkdir($this->logDir.$logSubfolder.DIRECTORY_SEPARATOR,0777)){
				trigger_error('Cannot create log folder',E_USER_ERROR);
			}
		}
		
		// Appending the log data at the end of log file or creating it if it does not exist
		if(!file_put_contents($this->logDir.$logSubfolder.DIRECTORY_SEPARATOR.$logFileName.'.tmp',json_encode($logData)."\n",FILE_APPEND)){
			trigger_error('Cannot write log file',E_USER_ERROR);
		}
		
		// Logging process is finished
		return true;
		
	}
}
	
?>