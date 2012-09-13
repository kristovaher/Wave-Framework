<?php

/**
 * Wave Framework <http://www.waveframework.com>
 * Test Suite
 *
 * Wave Framework includes a full test suite for writing tests for API's and running said 
 * tests. Tests themselves are defined in '/tools/tests/' subfolder and they can be run 
 * based on settings from the Test Suite tool. This Test Suite tool then returns the results 
 * from said tests and whether the tests '.$command.' or not.
 *
 * @package    Tools
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/guide_tools.htm
 * @since      1.0.0
 * @version    3.2.2
 */

// This initializes tools and authentication
require('.'.DIRECTORY_SEPARATOR.'tools_autoload.php');

// Log is printed out in plain text format
header('Content-Type: text/html;charset=utf-8');

// Information about tests
$tests=array();

// If any tests are assigned to be run
if(isset($_POST['tests']) && !empty($_POST['tests']) && isset($_POST['count']) && $_POST['count']>0){

	// This holds the last detected error
	$lastError='';
	
	// This is an error handler itself
	function testErrorHandler($type=false,$message=false,$file=false,$line=false){
		global $lastError;
		$lastError.='TYPE: '.$type.'<br/>';
		$lastError.='MESSAGE: '.htmlspecialchars($message).'<br/>';
		$lastError.='FILE: '.$file.'<br/>';
		$lastError.='LINE: '.$line.'<br/>';
	}

	// Setting the error handler, this helps suppress the actual error output
	set_error_handler('testErrorHandler',E_ALL);

	// These arrays store the test results
	$testErrorLog=array();
	$testsRun=array();
	$testsPassed=array();
	$testsFailed=array();
	
	// If default execution time is changed
	if(isset($_POST['time']) && ini_get('max_execution_time')!=$_POST['time']){
		set_time_limit($_POST['time']);
	}
	
	// Loading State
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-state.php');
	$state=new WWW_State($config);
	
	// This holds link to database
	$databaseConnection=false;
	
	// Connecting to database, if configuration is set
	if(isset($config['test-database-name'],$config['test-database-type'],$config['test-database-host'],$config['test-database-username'],$config['test-database-password'])){
		// Including the required class and creating the object
		require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-database.php');
		$databaseConnection=new WWW_Database($config['test-database-type'],$config['test-database-host'],$config['test-database-name'],$config['test-database-username'],$config['test-database-password'],false,false);
		// Passing the database to State object
		$state->databaseConnection=$databaseConnection;
	}
	
	// Loading sessions class
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-sessions.php');
	// Loading sessions class with the session namespace
	$state->sessionHandler=new WWW_Sessions($state->data['session-name'],$state->data['session-lifetime'],$databaseConnection,true);
	
	// This functions file is not required, but can be used for system wide functions
	// If you want to include additional libraries, do so here
	if(file_exists(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'autoload.php')){
		require(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'autoload.php');
	} else {
		require(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'autoload.php');
	}
	
	// API is used to process all requests and it handles caching and API validations
	require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-api.php');
	$api=new WWW_API($state);
	
	// Using error reporting in tests to catch potential errors
	error_reporting(E_ALL);
	
	// Looping over each test
	foreach($_POST['tests'] as $test){
	
		// Finding the test name based on filename
		$filename=explode('.',$test);
		$tmp=array_pop($filename);
		$testName=implode('.',$filename);
		
		// Loading test configuration
		$configuration=parse_ini_file('tests'.DIRECTORY_SEPARATOR.$test,true);
		if($configuration){
		
			// Resetting the variables about the current test
			$command=false;
			$input=false;
			$output=false;
			
			// Looping over configuration and tests
			foreach($configuration as $group=>$settings){
			
				// Checking for INI groups for master/input/output related settings
				if($group!='input' && $group!='output'){
					// The same command can be tested multiple times in a single test script
					$trueCommand=$group;
					$command=explode('#',$trueCommand);
					$command=$command[0];
					// Resetting input values
					$input=false;
					$output=false;
					// Assigning the group settings as input
					$masterInput=$settings;
				} elseif($group=='input'){
					$input=$settings;
				} elseif($group=='output'){
					$output=$settings;
				}
				
				// Running the test if all the settings are ok
				if($command && $output){
				
					// Tests are run multiple times
					for($x=1;$x<=$_POST['count'];$x++){
					
						// Defining test address which can be used later on to link between arrays
						$testAddress=$testName.' '.$trueCommand.' #'.$x;
					
						// This is the input array sent to API
						$thisInput=array();
						
						// Input is not required, but if it exists then generating input variables
						if($input && !empty($input)){
						
							// Input variables can be both dynamic and fixed
							foreach($input as $key=>$value){
								if(preg_match('/^:numeric:/',$value)){
								
									// Numeric input is either a random integer or an integer within range of numbers
									$bits=explode(':',$value);
									if(isset($bits[2]) && $bits[2]!=''){
										$bits=explode('-',$bits[2]);
										$thisInput[$key]=rand($bits[0],$bits[1]);
									} else {
										$thisInput[$key]=rand(0,2147483647);
									}
									
								} elseif(preg_match('/^:alpha:/',$value)){
								
									// Alpha input is random characters and spaces
									$characters="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ";
									$charactersLength=strlen($characters)-1;
									$bits=explode(':',$value);
									if(isset($bits[2]) && $bits[2]!=''){
										$bits=explode('-',$bits[2]);
										$length=rand($bits[0],$bits[1]);
									} else {
										$length=rand(1,32);
									}
									$string='';
									for($i=1;$i<=$length;$i++){
										$string.=$characters[rand(0,$charactersLength)];
									}
									// Assigning as input value
									$thisInput[$key]=$string;
									
								} elseif(preg_match('/^:alphanumeric:/',$value)){
								
									// Alpha input is random characters and spaces
									$characters="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 ";
									$charactersLength=strlen($characters)-1;
									$bits=explode(':',$value);
									if(isset($bits[2]) && $bits[2]!=''){
										$bits=explode('-',$bits[2]);
										$length=rand($bits[0],$bits[1]);
									} else {
										$length=rand(1,32);
									}
									$string='';
									for($i=1;$i<=$length;$i++){
										$string.=$characters[rand(0,$charactersLength)];
									}
									// Assigning as input value
									$thisInput[$key]=$string;
									
								} elseif(preg_match('/^:fixed:/',$value)){
								
									// Fixed input is a random value from a fixed list
									$bits=explode(':',$value);
									if(isset($bits[2]) && $bits[2]!=''){
										$bits=explode(',',$bits[2]);
										$length=count($bits)-1;
										$thisInput[$key]=$bits[rand(0,$length)];
									}		
									
								} else {
								
									// If value was not dynamic, then it is sent as fixed
									$thisInput[$key]=$value;
									
								}
							}
							
						}
						
						// Final input
						$thisInput=$thisInput+$masterInput;
						
						// Logging the tests run
						$testsRun[$testAddress]=$thisInput;
						
						// Some of the values are enforced
						$thisInput['www-command']=$command;
						$thisInput['www-return-type']='php';
						$thisInput['www-headers']=0;
						$thisInput['www-disable-callbacks']=1;
						
						// Making the API request
						$result=$apiResult=$api->command($thisInput,false,false,false);
						
						// It is considered a failed test if the output buffer contains contents
						if(trim($lastError)!=''){
						
							// Error failed since output buffer included contents
							$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - encountered an error:<br/>'.$lastError;
							// Resetting the last error container
							$lastError='';
						
						} else {
						
							// Looping over the expected output variables and seeing if they were actually part of API output
							foreach($output as $key=>$value){
							
								// Testing if the otput key exists
								if(isset($result[$key])){
								
									if(preg_match('/^:numeric:/',$value)){
									
										// Testing if the return value is numeric and within accepted range
										$bits=explode(':',$value);
										if(isset($bits[2]) && $bits[2]!=''){
											$variables=explode('-',$bits[2]);
											if(!preg_match('/^[0-9]*\Z/i',$result[$key]) || (is_numeric($variables[0]) && $result[$key]<$variables[0]) || (is_numeric($variables[1]) && $result[$key]>$variables[1])){
												$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" is not numeric or is not within the expected range "'.$bits[2].'"';
											}
										} else {
											if(!preg_match('/^[0-9]*\Z/i',$result[$key])){
												$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" is not numeric';
											}
										}
										
									} elseif(preg_match('/^:alpha:/',$value)){
									
										// Testing if the response consists of letters
										$bits=explode(':',$value);
										if(isset($bits[2]) && $bits[2]!=''){
											$variables=explode('-',$bits[2]);
											if(!preg_match('/(*UTF8)^[a-zA-Z ]*\Z/i',$result[$key]) || (is_numeric($variables[0]) && strlen($result[$key])<$variables[0]) || (is_numeric($variables[1]) && strlen($result[$key])>$variables[1])){
												$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" does not just have letters or is not within the expected length "'.$bits[2].'"';
											}
										} else {
											if(!preg_match('/(*UTF8)^[a-zA-Z ]*\Z/i',$result[$key])){
												$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" does not just have letters';
											}
										}
										
									} elseif(preg_match('/^:alphanumeric:/',$value)){
									
										// Testing if the response consists of letters and numbers
										$bits=explode(':',$value);
										if(isset($bits[2]) && $bits[2]!=''){
											$variables=explode('-',$bits[2]);
											if(!preg_match('/(*UTF8)^[a-zA-Z0-9 ]*\Z/i',$result[$key]) || (is_numeric($variables[0]) && strlen($result[$key])<$variables[0]) || (is_numeric($variables[1]) && strlen($result[$key])>$variables[1])){
												$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" is not alphanumeric or within the expected length "'.$bits[2].'"';
											}
										} else {
											if(!preg_match('/(*UTF8)^[a-zA-Z0-9 ]*\Z/i',$result[$key])){
												$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" is not alphanumeric';
											}
										}
										
									} elseif(preg_match('/^:fixed:/',$value)){
									
										// Testing if the response is an accepted fixed value
										$bits=explode(':',$value);
										if(isset($bits[2]) && $bits[2]!=''){
											$variables=explode(',',$bits[2]);
											if(!in_array($result[$key],$variables)){
												$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" did not match the expected value "'.$bits[2].'"';
											}
										} else {
											$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" could not be matched against empty fixed response';
										}
										
									} else {
									
										// Testing if the value equals the expected value or not
										if($value && $result[$key]!=$value){
											$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" with the value "'.$result[$key].'" did not match the expected value "'.$value.'"';
										}
										
									}
									
								} else {
								
									// Expected value did not exists, so the test is considered as failed
									$testErrorLog[$testAddress]=$testName.' test '.$trueCommand.' iteration #'.$x.' - output key "'.$key.'" was not returned';
									
								}				
								
							}
							
						}
						
						// Storing the result in counter
						if(isset($testErrorLog[$testAddress])){
							$testsFailed[$testAddress]=true;
						} else {
							$testsPassed[$testAddress]=true;
						}
					
					}
					
				}
				
			}
			
		} else {
		
			// Test configuration failure is considered a '.$command.' test, but is not actual part of 'statistics'
			$testErrorLog[$testName.'#all']=$testName.'#all - '.$command.' - test configuration incorrect';
			
		}
		
		// Reloading state
		$state=new WWW_State($config);
		$state->databaseConnection=$databaseConnection;
		$state->sessionHandler=new WWW_Sessions($state->data['session-name'],$state->data['session-lifetime'],$databaseConnection,true);
		
		// Reloading API
		$api=new WWW_API($state);
		
	}
	
}

// Finding all INI configuration files for tests
$testFiles=scandir('tests'.DIRECTORY_SEPARATOR);

// Looping over each test file to check whether configuration is correct
foreach($testFiles as $test){
	// $state->setState('testing',true);
	// Ignoring the placeholder file
	if($test!='.empty' && is_file('tests'.DIRECTORY_SEPARATOR.$test)){
		// Test configuration should be stored as a grouped INI configuration file
		$configuration=parse_ini_file('tests'.DIRECTORY_SEPARATOR.$test,true);
		if($configuration){
			$tests[$test]=$configuration;
		}
	}
}
	
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Test Suite</title>
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
		echo '<h1>Test Suite</h1>';
		echo '<h4 class="highlight">';
		foreach($softwareVersions as $software=>$version){
			// Adding version numbers
			echo '<b>'.$software.'</b> ('.$version.') ';
		}
		echo '</h4>';
		
		// If error log is defined (if testing happened)
		if(isset($testErrorLog)){
		
			// Printing out test results
			echo '<h2>Test Results</h2>';
			echo '<p class="bold">'.count($testsRun).' tests run, '.count($testsFailed).' failed</p>';
			if(!empty($testErrorLog)){
				echo '<h3>Logged errors</h3>';
				foreach($testErrorLog as $address=>$log){
					echo '<p class="bold red small" style="text-align:left;">';
					echo $log;
					echo '</p>';
					if(isset($testsRun[$address]) && !empty($testsRun[$address])){
						echo '<p class="bold small">Input data</p>';
						echo '<pre style="padding-left:40px;">';
							print_r($testsRun[$address]);
						echo '</pre>';
					}
				}
			}
			
		}
		
		echo '<h2>Available Tests</h2>';
		
		// Generating the form for all available tests
		if(!empty($tests)){
		
			?>
				<form method="post" action="" enctype="multipart/form-data">
					<?php
					foreach($tests as $file=>$test){
						$filename=explode('.',$file);
						$tmp=array_pop($filename);
						$testName=implode('.',$filename);
						?>
						<p class="bold"><input type="checkbox" name="tests[]" value="<?=$file?>" <?=(isset($_POST['tests']) && in_array($file,$_POST['tests']))?'checked':''?>/> <?=$testName?></p>
						<?php
					}
					?>
					<p class="bold">
						Set execution time to <input type="text" style="width:40px;" name="time" value="<?=(isset($_POST['time']))?$_POST['time']:ini_get('max_execution_time')?>"/> seconds and run each test 
						<select name="count">
							<option value="1">1</option>
							<?php for($i=10;$i<=1000;$i=$i+10){?>
								<option value="<?=$i?>" <?=(isset($_POST['count']) && $_POST['count']==$i)?'selected':''?>><?=$i?></option>
							<?php } ?>
						</select> times 
						<input type="submit" value="GO!"/>
					</p>
					<p class="small">Please note that on some systems it is not possible to change the execution time by PHP itself, in which case you should ask for assistance from server administrators, if the default execution time is not enough and the script dies before it has finished.</p>
				</form>
			<?php
		
		} else {
			echo '<p class="bold">There are no tests that can be run from /tools/tests/ subfolder</p>';
		}
		
		// Footer
		echo '<p class="footer small bold">Generated at '.date('d.m.Y h:i').' GMT '.date('P').' for '.$_SERVER['HTTP_HOST'].'</p>';
	
		?>
	</body>
</html>