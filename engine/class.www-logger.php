<?php

/*
Wave Framework
Logger class

This is an optional class that is used to log requests on system by Index Gateway. WWW_Logger 
is used to keep track of performance of requests made to Index Gateway files, it keeps track 
of how long the request took, what data it included, how high was the memory use, server load, 
CPU usage and so on. If this logger is used, then every single request is logged as serialized 
arrays in filesystem in /filesystem/log/ subfolders. It is a good idea to clean that folder 
every now and then. There is a separate log-reader.php script for reading those log files 
situated in /tools/ subfolder.

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

class WWW_Logger {

	// This stores microtime of the request at the moment Logger object was created. This 
	// microtime is used by Logger to calculate execution time of the script. If this value 
	// is not defined at the moment Logger object is created, then it is defined automatically.
	private $requestMicrotime=false;
	
	// This is the main address of the folder where log files will be stored. This folder 
	// should be writable by PHP. Logger creates subfolders under this folder and stores log 
	// files in those subfolders.
	private $logDir='./';
	
	// This array variable stores custom data sent to logger. Keys of this array will be stored 
	// as keys in the log entry.
	public $logData=array('response-code'=>200);
	
	// Construction method of Logger requires just one variable: $logDir, which is the folder 
	// where log files will be stored. Second variable, $microTime, is used to calculate the 
	// execution time of the script. This microtime should be the microtime from the very start 
	// of the script, if it is not defined then Logger defines it by itself.
	// * logDir - location of directory to store log files at
	// * microTime - Starting microtime
	public function __construct($logDir='./',$microTime=false){
	
		// Defining IP
		if(!defined('__IP__')){
			define('__IP__',$_SERVER['REMOTE_ADDR']);
		}
	
		// We record the start time of the request
		if(!$microTime){
			$this->requestMicrotime=microtime(true);
		} else {
			$this->requestMicrotime=$microTime;
		}
		// Checking if log directory is valid
		if(is_dir($logDir)){
			// Log directory is assigned
			$this->logDir=$logDir;
		} else {
			// Assigned folder is not detected as being a folder
			trigger_error('Assigned log folder does not exist or is not writable',E_USER_ERROR);
		}
		
	}
	
	// This method is used to add data to objects $logData array. Key will be the same key defined 
	// in the log entry array and value will be the value of this key. It is also possible to send
	// multiple keys and values in the same method, if $key is an array of keys and values, instead 
	// of a string.
	// * setting - single key or array of data
	public function setCustomLogData($key,$value=true){
		if(is_array($key)){
			$this->logData=$key+$this->logData;
		} else {
			$this->logData[$key]=$value;
		}
		return true;
	}
	
	// This is the main method of Logger. This method attempts to gather a lot of data about the 
	// HTTP request and calculate things such as execution time, memory usage and more. It also 
	// creates a logger entry array and combines it with custom log array of $logData. It also 
	// creates subfolder in the log folder directory, if it doesn't exist, and writes serialized 
	// log entry array in that folder.
	// * category - Category under which log is assigned
	public function writeLog(){
	
		// All log data is gathered to this variable
		$logData=array();
		
		// STATIC LOG DATA
		
			// Unique request identifier set by the server.
			if(isset($_SERVER['UNIQUE_ID'])){ $logData['request-id']=$_SERVER['UNIQUE_ID']; } // This is not supported on Windows
			
			// This stores the URI that user agent requested.
			$logData['request']=$_SERVER['REQUEST_URI'];
			
			// Timestamp of the request in milliseconds.
			$logData['microtime']=$this->requestMicrotime;

			// UNIX timestamp of the request time.
			$logData['time']=$_SERVER['REQUEST_TIME'];
			
			// Stores request datetime in format 'Y-m-d H:i:s'.
			$logData['datetime']=date('Y-m-d H:i:s').' GMT '.date('P');
			
			// Stores IP of the user agent that made the request.
			$logData['ip']=__IP__;
			
			// Additional IP's
			if(isset($_SERVER['HTTP_CLIENT_IP'])){
				$logData['forwarded-client-ip']=$_SERVER['HTTP_CLIENT_IP'];
			}
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
				$logData['forwarded-client-ip']=$_SERVER['HTTP_X_FORWARDED_FOR'];
			}
			if(__IP__!=$_SERVER['REMOTE_ADDR']){
				$logData['remote-addr']=$_SERVER['REMOTE_ADDR'];
			}
			
			// This stores user agent string of the user agent.
			$logData['user-agent']=(isset($_SERVER['HTTP_USER_AGENT']))?$_SERVER['HTTP_USER_AGENT']:'Unknown';

			// This stores user agent string of the user agent.
			if(isset($_SERVER['HTTP_REFERER'])){
				$logData['referrer']=$_SERVER['HTTP_REFERER'];
			}
				
			// GET variables submitted by user agent.
			if(!empty($_GET)){ 
				$logData['get']=$_GET;
			}
			
			// POST variables submitted by user agent
			if(!empty($_POST)){ 
				$logData['post']=$_POST;
			}
			
			// FILES variables submitted by user agent
			if(!empty($_FILES)){
				$logData['files']=$_FILES;
			}
			
			// SESSION variables submitted by user agent
			if(!empty($_SESSION)){ 
				$logData['session']=$_SESSION;
			}
			
			// COOKIE variables submitted by user agent
			if(!empty($_COOKIE)){ 
				$logData['cookie']=$_COOKIE;
			}
			
			// This stores how long the request took in seconds.
			$logData['execution-time']=number_format((microtime(true)-$this->requestMicrotime),6);
			
			// This stores the peak usage of memory during the request.
			$logData['memory-peak-usage']=memory_get_peak_usage(); // In bytes, divide by 1048576 to get megabytes

			// CPU load - This is not available on Windows
			if(function_exists('getrusage')){
				$load=getrusage();
				$logData['cpu-user-usage']=number_format($load['ru_utime.tv_sec']+$load['ru_utime.tv_usec']/1000000,6);
				// Disregarding potentially incorrectly reported numbers
				if($logData['cpu-user-usage']>$logData['execution-time']){
					unset($logData['cpu-user-usage']);
				}
				$logData['cpu-system-usage']=number_format($load['ru_stime.tv_sec']+$load['ru_stime.tv_usec']/1000000,6);
				// Disregarding potentially incorrectly reported numbers
				if($logData['cpu-system-usage']>$logData['execution-time']){
					unset($logData['cpu-system-usage']);
				}
			}
			
			// This stores system load number (that is one minute old).
			// This is not supported on Windows
			if(function_exists('sys_getloadavg')){
				$load=sys_getloadavg(); //last 1, 5 and 15 minutes (number of processes in run queue)
				$logData['system-load']=$load[0];
			}
		
		// WRITING LOG
		
			// Log filename is current time-based with present hour in the end
			$logFileName=date('Y-m-d-H');
			
			// Finding the subfolder of the log file, if subfolder does not exist then it is created
			$logSubfolder=substr($logFileName,0,10);
			if(!is_dir($this->logDir.$logSubfolder.DIRECTORY_SEPARATOR)){
				if(!mkdir($this->logDir.$logSubfolder.DIRECTORY_SEPARATOR,0755)){
					trigger_error('Cannot create log folder',E_USER_ERROR);
				}
			}
			
			// Appending the log data at the end of log file or creating it if it does not exist
			if(!file_put_contents($this->logDir.$logSubfolder.DIRECTORY_SEPARATOR.$logFileName.'.log',json_encode($logData)."\n",FILE_APPEND)){
				trigger_error('Cannot write log file',E_USER_ERROR);
			}
		
		// Logging process is finished
		return true;
		
	}
}
	
?>