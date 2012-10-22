<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Limiter Class
 *
 * This is an optional class that is used to limit HTTP requests based on user agent, IP, 
 * server condition and other information. This class is loaded by Index Gateway. WWW_Limiter 
 * can be used to block IP's if they make too many requests per minute, block requests if 
 * server load is detected as too high, block the request if it comes from blacklist provided 
 * by the system, allow only whitelisted IP's to access, ask for HTTP authentication or force 
 * the user agent to use HTTPS. Note that some of this functionality can be achieved by Apache 
 * configuration and modules, but it is provided here for cases where the project developer 
 * might not have control over server configuration.
 *
 * @package    Limiter
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/limiter.htm
 * @since      1.0.0
 * @version    3.2.3
 */

class WWW_Limiter {

	/**
	 * This is the main address of the folder where limiter stores log files for 
	 * request limiter.
	 */
	private $logDir='./';
	
	/**
	 * This holds the WWW_Logger object, if it is used. This makes it possible for Limiter 
	 * to write proper log files through Logger in case requests are blocked.
	 */
	public $logger=false;
	
	/**
	 * Construction method of Logger expects just one variable: $logDir, which is the folder where 
	 * limiter stores files for specific limiter methods. This folder should be writable by PHP.
	 *
	 * @param string $logDir location of directory to store log files at
	 * @return object
	 */
	public function __construct($logDir='./'){
	
		// Defining IP
		if(!defined('__IP__')){
			define('__IP__',$_SERVER['REMOTE_ADDR']);
		}
		
		// Checking if log directory is valid
		if(is_dir($logDir)){
			// Log directory is assigned
			$this->logDir=$logDir;
		} else {
			// Assigned folder is not detected as being a folder
			trigger_error('Assigned limiter folder does not exist',E_USER_ERROR);
		}
		
	}
	
	/**
	 * This method will block requests from the request-making IP address for $duration amount 
	 * of seconds, if the IP address makes more than $limit amount of requests per minute. It 
	 * keeps track of the amount of requests by storing minimal log files in filesystem, in 
	 * $logDir subfolder. Returns true if not limited, throws 403 error if limit exceeded.
	 *
	 * @param integer $limit amount of requests that cannot be exceeded per minute
	 * @param integer $duration duration of how long the IP will be blocked if limit is exceeded
	 * @return boolean or exit if limiter hit
	 */
	public function limitRequestCount($limit=400,$duration=3600){
	
		// Limiter is only used if limit is set higher than 0 and request does not originate from the same server
		if($limit!=0 && __IP__!=$_SERVER['SERVER_ADDR']){
		
			// Log filename is hashed user agents IP
			$logFilename=md5(__IP__);
			// Subfolder name is derived from log filename
			$cacheSubfolder=substr($logFilename,0,2);
			
			// If log directory does not exist, then it is created
			$this->logDir.=$cacheSubfolder.DIRECTORY_SEPARATOR;
			if(!is_dir($this->logDir)){
				// Error is returned if creating the limiter folder with proper permissions does not work
				if(!mkdir($this->logDir,0755)){
					trigger_error('Cannot create limiter folder',E_USER_ERROR);
				}
			}
			
			// If file exists, then the amount of requests are checked, if file does not exist then it is created
			if(file_exists($this->logDir.$logFilename.'.tmp')){
			
				// Loading current contents of the file
				$data=file_get_contents($this->logDir.$logFilename.'.tmp');
				
				// If current file does not say that the IP is blocked, the request frequency is checked
				if($data!='BLOCKED'){
				
					// Limit is checked by counting the most recent requests stored in the file					
					$data=explode("\n",$data);
					if(count($data)>=$limit){
						// Limited amount of rows is taken from the file before data is flipped, minimizing the timestamps for check
						$data=array_slice($data,-$limit); 
						$checkData=array_flip($data);
						// Limit has been reached by all of the requests happening in the same minute
						if(count($checkData)==1){
							// Request is logged and can be used for performance review later
							if($this->logger){
								$this->logger->setCustomLogData(array('response-code'=>429,'category'=>'limiter','reason'=>'Too many requests'));
								$this->logger->writeLog();
							}
							// Block file is created and 403 page thrown to the user agent
							file_put_contents($this->logDir.$logFilename.'.tmp','BLOCKED');
							// Returning proper header
							header('HTTP/1.1 429 Too Many Requests');
							header('Retry-After: '.$duration);
							// Response to be displayed in browser
							echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 429 Too Many Requests</div>';
							echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">YOUR IP HAS MADE TOO MANY REQUESTS TO THIS SERVER, TRY AGAIN IN '.$duration.' SECONDS</div>';
							die();
						}
					}
					
					// When limit was not exceeded, file is stored again with new data
					$limiterData=implode("\n",$data);
					file_put_contents($this->logDir.$logFilename.'.tmp',$limiterData."\n".date('Y-m-d H:i',$_SERVER['REQUEST_TIME']));
					
				} else {
				
					// The time when lock was created
					$blockDuration=filemtime($this->logDir.$logFilename.'.tmp');
					// If the file that has blocked the requests is older than the limit duration, then block is deleted, otherwise 403 page is shown
					if(time()-$blockDuration>=$duration){
						// Block file is removed
						unlink($this->logDir.$logFilename.'.tmp');
					} else {
						// Request is logged and can be used for performance review later
						if($this->logger){
							$this->logger->setCustomLogData(array('response-code'=>429,'category'=>'limiter','reason'=>'Too many requests'));
							$this->logger->writeLog();
						}
						// Returning 403 header
						$retryAfter=($duration-(time()-$blockDuration));
						header('HTTP/1.1 429 Too Many Requests');
						header('Retry-After: '.$retryAfter);
						// Response to be displayed in browser
						echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 429 Too Many Requests</div>';
						echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">YOUR IP HAS MADE TOO MANY REQUESTS TO THIS SERVER, TRY AGAIN IN '.$retryAfter.' SECONDS</div>';
						die();
					}
					
				}
				
			} else {
			
				// Current date, hour and minute are stored in the file
				file_put_contents($this->logDir.$logFilename.'.tmp',date('Y-m-d H:i',$_SERVER['REQUEST_TIME']));
				
			}
		}
		
		// Request limiter processed
		return true;
		
	}
	
	/**
	 * This method will block HTTP requests if server load is more than $limit. It throws 
	 * 503 Service Unavailable message should that happen.
	 *
	 * @param integer $limit server load that, if exceeded, causes the user agents request to be blocked
	 * @return boolean or exit if limiter hit
	 */
	public function limitServerLoad($limit=80){
	
		// System load is checked only if limit is not set
		if($limit!=0){
			// This function does not return on Windows servers
			if(function_exists('sys_getloadavg')){
				// Returns system load in the last 1, 5 and 15 minutes.
				$load=sys_getloadavg();
				// 503 page is returned if load is above limit
				if($load[0]>$limit){
					// Request is logged and can be used for performance review later
					if($this->logger){
						$this->logger->setCustomLogData(array('response-code'=>503,'category'=>'limiter','reason'=>'Server load exceeded, current load is '.$load[0].', limit is '.$limit));
						$this->logger->writeLog();
					}
					// Returning 503 header
					header('HTTP/1.1 503 Service Unavailable');
					// Response to be displayed in browser
					echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 503 Service Unavailable</div>';
					echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">SERVER IS UNDER A LOT OF STRESS, YOUR REQUEST IS CURRENTLY BLOCKED, PLEASE TRY AGAIN LATER</div>';
					die();
				}
			} else {
				return true;
			}
		}
		
		// Server load limiter processed
		return true;
	
	}
	
	/**
	 * This method only allows HTTP requests from a comma-separated list of IP addresses 
	 * sent with $whitelist. For every other IP address it throws a 403 Forbidden error.
	 *
	 * @param string $whiteList comma-separated list of whitelisted IP addresses
	 * @return boolean or exit if limiter hit
	 */
	public function limitWhitelisted($whiteList=''){
	
		// This value should be a comma-separated string of blacklisted IP's
		if($whiteList!=''){
			// Exploding string of IP's into an array
			$whiteList=explode(',',$whiteList);
			// Checking if the user agent IP is set in blacklist array
			if(empty($whiteList) || !in_array(__IP__,$whiteList)){
				// Request is logged and can be used for performance review later
				if($this->logger){
					$this->logger->setCustomLogData(array('response-code'=>403,'category'=>'limiter','reason'=>'Not whitelisted'));
					$this->logger->writeLog();
				}
				// Returning 403 data
				header('HTTP/1.1 403 Forbidden');
				// Response to be displayed in browser
				echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 403 Forbidden</div>';
				echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">YOUR IP IS NOT ALLOWED TO USE THIS SERVICE</div>';
				die();
			}
		}
		
		// Blacklist processed
		return true;
		
	}
	
	/**
	 * This method blocks IP addresses sent with $blackList as a comma-separated list. If HTTP 
	 * request has an IP defined in that list, then Limiter throws a 403 Forbidden error.
	 *
	 * @param string $blackList comma-separated list of blacklisted IP addresses
	 * @return boolean or exit if limiter hit
	 */
	public function limitBlacklisted($blackList=''){
	
		// This value should be a comma-separated string of blacklisted IP's
		if($blackList!=''){
			// Exploding string of IP's into an array
			$blackList=explode(',',$blackList);
			// Checking if the user agent IP is set in blacklist array
			if(!empty($blackList) && in_array(__IP__,$blackList)){
				// Request is logged and can be used for performance review later
				if($this->logger){
					$this->logger->setCustomLogData(array('response-code'=>403,'category'=>'limiter','reason'=>'Blacklisted'));
					$this->logger->writeLog();
				}
				// Returning 403 data
				header('HTTP/1.1 403 Forbidden');
				// Response to be displayed in browser
				echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 403 Forbidden</div>';
				echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">YOUR IP IS NOT ALLOWED TO USE THIS SERVICE</div>';
				die();
			}
		}
		
		// Blacklist processed
		return true;
		
	}
	
	/**
	 * This method asks for basic HTTP authentication $username and $password and throws a 
	 * 403 Forbidden error if provided credentials are incorrect or missing. It is also 
	 * possible to provide a comma-separated list of IP addresses in $ip that allow this 
	 * type of authentication for additional security.
	 *
	 * @param string $username correct username for the request
	 * @param string $password correct password for the request
	 * @param string $ip comma separated list of allowed IP addresses
	 * @return boolean or exit if limiter hit
	 */
	public function limitUnauthorized($username,$password,$ip='*'){
	
		// If all IP's are not allowed
		if($ip!='*'){
			$ip=explode(',',$ip);
		}
	
		// If provided username and password are not correct, then 401 page is displayed to the user agent
		if(is_array($ip) && !in_array(__IP__,$ip)){
			header('HTTP/1.1 401 Unauthorized');
			// Response to be displayed in browser
			echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 401 Unauthorized</div>';
			echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">YOUR IP IS NOT ALLOWED TO USE THIS SERVICE</div>';
			die();
		} elseif(!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER']!=$username || !isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW']!=$password){
			// Request is logged and can be used for performance review later
			if($this->logger){
				$this->logger->setCustomLogData(array('response-code'=>401,'category'=>'limiter','reason'=>'Authorization required'));
				$this->logger->writeLog();
			}
			// Returning 401 headers
			header('WWW-Authenticate: Basic realm="'.$_SERVER['HTTP_HOST'].'"');
			header('HTTP/1.1 401 Unauthorized');
			// Response to be displayed in browser
			echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 401 Unauthorized</div>';
			echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">AUTHORIZATION DETAILS ARE REQUIRED</div>';
			die();
		}
		
		// HTTP authorization processed
		return true;
		
	}
	
	/**
	 * This method either throws a 403 Forbidden error if non-HTTPS connection is used to make 
	 * a request, or redirects the request to HTTPS. If $autoRedirect is set to true, then HTTP 
	 * requests are automatically redirected.
	 *
	 * @param boolean $autoRedirect if this is set to true, then system redirects user agent to HTTPS
	 * @return boolean or exit if limiter hit
	 */
	public function limitNonSecureRequests($autoRedirect=true){
	
		// HTTPS is detected from $_SERVER variables
		if(!isset($_SERVER['HTTPS']) || ($_SERVER['HTTPS']!=1 && $_SERVER['HTTPS']!='on')){
			// If auto redirect is on, user agent is forwarded by replacing the http:// protocol with https://
			if($autoRedirect){
				// Redirecting to HTTPS address
				header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			} else {
				// Request is logged and can be used for performance review later
				if($this->logger){
					$this->logger->setCustomLogData(array('response-code'=>401,'category'=>'limiter','reason'=>'HTTPS required'));
					$this->logger->writeLog();
				}
				// Returning 401 header
				header('HTTP/1.1 403 Forbidden');
				// Response to be displayed in browser
				echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 403 Forbidden</div>';
				echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">HTTPS CONNECTION IS REQUIRED</div>';
			}
			// Script is halted
			die();
		} else {
			// This tells the browser to remember to use HTTPS when accessing the site.
			header('Strict-Transport-Security: max-age=2147483647 [; includeSubdomains]');
		}
		
		// HTTPS check processed
		return true;
		
	}
	
	/**
	 * This method either throws a 403 Forbidden error if a referrer is used that is not accepted.
	 *
	 * @param string $allowed This is comma-separated list of domains that are allowed or not to be the referrer
	 * @return boolean or exit if limiter hit
	 */
	public function limitReferrer($allowed='*'){
	
		if(isset($_SERVER['HTTP_REFERER'])){
			// Allowed setting can be a comma-separated string
			$allowed=explode(',',$allowed);
			// Parsing the referrer URL
			$referrer=parse_url($_SERVER['HTTP_REFERER']);
			// Checking for domain name existence and returning true, if accepted
			if(in_array('*',$allowed) && !in_array('!'.$referrer['host'],$allowed)){
				return true;
			} elseif(in_array($referrer['host'],$allowed)){
				return true;
			}
			// Request is logged and can be used for performance review later
			if($this->logger){
				$this->logger->setCustomLogData(array('response-code'=>401,'category'=>'limiter','reason'=>'Incorrect referrer'));
				$this->logger->writeLog();
			}
			// Returning 401 header
			header('HTTP/1.1 403 Forbidden');
			// Response to be displayed in browser
			echo '<div style="font:18px Tahoma; text-align:center;padding:100px 50px 10px 50px;">HTTP/1.1 403 Forbidden</div>';
			echo '<div style="font:14px Tahoma; text-align:center;padding:10px 50px 100px 50px;">THIS REFERRER IS NOT ALLOWED</div>';
			// Script is halted
			die();
		}
		
		// Referrer check processed
		return true;
		
	}

}
	
?>