<?php/** * Wave Framework <http://www.waveframework.com> * Image Handler * * Image Handler is used by Index Gateway to return all image files to the user agent HTTP  * requests. Handler adds proper cache headers as well as supports on-demand image-editing,  * where it is possible to load an image file with specific resize algorithms and even image  * filtering. It also checks for files from overrides folder, which can be returned instead  * of the actual file. * * @package    Index Gateway * @author     Kristo Vaher <kristo@waher.net> * @copyright  Copyright (c) 2012, Kristo Vaher * @license    GNU Lesser General Public License Version 3 * @tutorial   /doc/pages/handler_image.htm * @since      1.5.0 * @version    3.5.0 */// INITIALIZATION	// Stopping all requests that did not come from Index Gateway	if(!isset($resourceAddress)){		header('HTTP/1.1 403 Forbidden');		die();	}		// If access control header is set in configuration	if(isset($config['access-control'])){		header('Access-Control-Allow-Origin: '.$config['access-control']);	}	// Web root is the subfolder on public site	$webRoot=str_replace('index.php','',$_SERVER['SCRIPT_NAME']);		// Web root is the subfolder on public site	$systemRoot=str_replace('index.php','',$_SERVER['SCRIPT_FILENAME']);		// Dynamic resource loading can be turned off in configuration	if(!isset($config['dynamic-image-loading']) || $config['dynamic-image-loading']==true){		// If filename includes & symbol, then system assumes it should be dynamically generated		$parameters=array_unique(explode('&',$resourceFile));	} else {		$parameters=array();		$parameters[0]=$resourceFile;	}	// True filename is the last string in the string separated by & character	$resourceFile=array_pop($parameters);	// Current true file position	$resource=$resourceFolder.$resourceFile;	// Files from /resources/ folder can be overwritten if file with the same name is placed to /overrides/resources/	if(preg_match('/^'.str_replace('/','\/',$webRoot).'resources/',$resourceRequest)){		//Checking if file of the same name exists in overrides folder		$overrideFolder=str_replace($webRoot.'resources'.DIRECTORY_SEPARATOR,$webRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR,$resourceFolder);		if(file_exists($overrideFolder.$resourceFile)){			// System will use an override as a resource, since it exists			$resource=$overrideFolder.$resourceFile;		}	}	// RESOURCE EXISTENCE CHECK	// If file does not exist then 404 is thrown	if(!file_exists($resource) && (!isset($config['404-image-placeholder']) || $config['404-image-placeholder']==true) && (file_exists(__ROOT__.'resources'.DIRECTORY_SEPARATOR.'placeholder.jpg') || file_exists(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'placeholder.jpg'))){		// It's possible to overwrite the default image used for 404 placeholder		if(file_exists(__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'placeholder.jpg')){			$resource=__ROOT__.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'placeholder.jpg';		} else {			$resource=__ROOT__.'resources'.DIRECTORY_SEPARATOR.'placeholder.jpg';		}		// 404 header		header('HTTP/1.1 404 Not Found');		// Notifying Logger of 404 response code		if(isset($logger)){			$logger->setCustomLogData(array('response-code'=>404));		}		// This variable is used by cache to calculate cache filename, but since system is returning a placeholder instead, it is overwritten		// This allows system to keep all 404 placeholder image cache in the same cache file		$tmp=explode('/',str_replace($resourceFile,'placeholder.jpg',$resourceRequest));		$resourceRequest=array_pop($tmp);			} elseif(!file_exists($resource)){		// Adding log entry			if(isset($logger)){			$logger->setCustomLogData(array('response-code'=>404,'category'=>'image'));			$logger->writeLog();		}		// Returning 404 header		header('HTTP/1.1 404 Not Found');		die();	}	// CACHE SETTINGS	// Default cache timeout of one month, unless timeout is set	if(!isset($config['resource-cache-timeout'])){		$config['resource-cache-timeout']=31536000; // A year	}	// Last-modified time of the original resource	$lastModified=filemtime($resource);	// This flag stores whether cache was used	$cacheUsed=false;	// No cache flag	if(in_array('nocache',$parameters)){		$noCache=true;	} else {		$noCache=false;	}// GENERATION BASED ON PARAMETERS	// No cache flag	if(in_array('base64',$parameters)){		$base64=true;	} else {		$base64=false;	}		// If file seems to carry additional configuration options, then it is generated or loaded from cache	if(empty($parameters)){				// Pure image file request is considered 'cache used' due to it not needing any processing		$cacheUsed=true;				// IF NOT MODIFIED			// If the request timestamp is exactly the same, then we let the browser know of this			if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified){				// Adding log entry					if(isset($logger)){					$logger->setCustomLogData(array('response-code'=>304,'category'=>'image','cache-used'=>true));					$logger->writeLog();				}				// Cache headers (Last modified is never sent with 304 header, since it is often ignored)				header('Cache-Control: public,max-age='.$config['resource-cache-timeout']);				header('Expires: '.gmdate('D, d M Y H:i:s',($_SERVER['REQUEST_TIME']+$config['resource-cache-timeout'])).' GMT');				// Returning 304 header				header('HTTP/1.1 304 Not Modified');				die();			}	} else {				// Solving cache folders and directory		$cacheFilename=md5($lastModified.$config['version'].$resourceRequest).'.tmp';		$cacheDirectory=__ROOT__.'filesystem'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.substr($cacheFilename,0,2).DIRECTORY_SEPARATOR;				// IF NOT MODIFIED					// If cache file exists then cache modified is considered that time			if(!$noCache && file_exists($cacheDirectory.$cacheFilename)){				// Getting last modified time from cache file				$lastModified=filemtime($cacheDirectory.$cacheFilename);				// If the request timestamp is exactly the same, then we let the browser know of this				if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified){					// Adding log entry						if(isset($logger)){						$logger->setCustomLogData(array('response-code'=>304,'category'=>'image','cache-used'=>true));						$logger->writeLog();					}					// Cache headers (Last modified is never sent with 304 header)					header('Cache-Control: public,max-age='.$config['resource-cache-timeout']);					header('Expires: '.gmdate('D, d M Y H:i:s',($_SERVER['REQUEST_TIME']+$config['resource-cache-timeout'])).' GMT');					// Returning 304 header					header('HTTP/1.1 304 Not Modified');					die();				}			} else {				// Otherwise it is server request time				$lastModified=$_SERVER['REQUEST_TIME'];			}					// GENERATING RESOURCE						// If resource cannot be found from cache, it is generated			if($noCache || ($lastModified==$_SERVER['REQUEST_TIME'] || $lastModified<($_SERVER['REQUEST_TIME']-$config['resource-cache-timeout']))){							// DEFAULT SETTINGS								// Get existing image size					$resolution=getimagesize($resource);									// Default settings for dynamically resized image					// This values will be changed based on if parameters are set					$width=$resolution[0];					$height=$resolution[1];					$algorithm='fitcrop';					$red=0;					$green=0;					$blue=0;					$top='center';					$left='center';					$quality=90;					$filters=array();					$filterSettings=array();					$format=false;									// FINDING SETTINGS FROM SET PARAMETERS									// Looping over the data bits to find additional parameters					foreach($parameters as $parameter){						switch($parameter){							case 'fitcrop':								// This is a resize algorithm flag								$algorithm='fitcrop';								break;							case 'crop':								// This is a resize algorithm flag								$algorithm='crop';								break;							case 'fitwithbackground':								// This is a resize algorithm flag								$algorithm='fitwithbackground';								break;							case 'fitwithoutbackground':								// This is a resize algorithm flag								$algorithm='fitwithoutbackground';								break;							case 'widthonly':								// This is a resize algorithm flag								$algorithm='widthonly';								break;							case 'jpg':								// This is a resize algorithm flag								$format='jpg';								break;							case 'png':								// This is a resize algorithm flag								$format='png';								break;							case 'heightonly':								// This is a resize algorithm flag								$algorithm='heightonly';								break;							default:								// If any of the resize algorithm and cache flags were not hit, the parameter is matched for other conditions								if(strpos($parameter,'filter(')!==false){																	// Background color setting is assumed if rgb is present									$settings=str_replace(array('filter(',')'),'',$parameter);									$settings=explode(',',$settings);																		// Storing data of new filter									$newFilter=array();									// First number is the filter type									if($settings[0]!=''){										// Filter type can also have parameters										$typeSettings=explode('@',$settings[0]);										// First parameter is the filter type										$newFilter['type']=$typeSettings[0];										// It is possible to 'layer' the effect by defining alpha level as the second parameter										if(isset($typeSettings[1])){											$newFilter['alpha']=$typeSettings[1];										} else {											// Filter effect is 100% if alpha was not defined											$newFilter['alpha']=100;										}									}																		// Storing data of new filters settings									$newFilter['settings']=array();									// Storing other filter variables									for($i=1;isset($settings[$i]);$i++){										$newFilter['settings'][]=$settings[$i];									}																		// Adding filter to list of filters									$filters[]=$newFilter;																	} elseif(strpos($parameter,'rgb(')!==false){																	// Background color setting is assumed if rgb is present									$colors=str_replace(array('rgb(',')'),'',$parameter);									$colors=explode(',',$colors);									// First number in parameter is red color amount									if($colors[0]!=''){										$red=$colors[0];									}									// Second number in parameter is green color amount									if(isset($colors[1]) && $colors[1]!=''){										$green=$colors[1];									}									// Third number in parameter is blue color amount									if(isset($colors[2]) && $colors[2]!=''){										$blue=$colors[2];									}																	} elseif(strpos($parameter,'@')!==false){																	// Quality setting is assumed if @ sign is present									$quality=str_replace('@','',$parameter);																	} elseif(strpos($parameter,'-')!==false){																	// Position setting is assumed if dash is present									$positions=explode('-',$parameter);									// First value is top position									// This can be 'top', 'center', 'bottom' or a number in pixels									if($positions[0]!=''){										$top=$positions[0];									}									// Second value is left position									// This can be 'left', 'center', 'right' or a number in pixels									if($positions[1]!=''){										$left=$positions[1];									}																	} elseif(strpos($parameter,'x')!==false){																	// It is assumed that the remaining parameter is for image dimensions									$dimensions=explode('x',$parameter);									// First number is width									if($dimensions[0]!=''){										$width=$dimensions[0];									}									// Second number, if defined, is height									if(isset($dimensions[1]) && $dimensions[1]!=''){										$height=$dimensions[1];									} else {										// If height is not defined then height is considered to be as long as width										$height=$width;									}									// If algorithm is still undefined, it is given a default value									// This is needed when size is set, but algorithm is not									if(!$algorithm){										$algorithm='fitcrop';									}																	} elseif($parameter!='nocache' && $parameter!='base64'){																	// Adding log entry										if(isset($logger)){										$logger->setCustomLogData(array('response-code'=>404,'category'=>'image'));										$logger->writeLog();									}									// Returning 404 header									header('HTTP/1.1 404 Not Found');									die();														}								break;						}					}									// IMAGE SETTING VALIDATION									// System checks for legality of the entered values							// Whitelists allow to protect the server better from possible abuse and denial of service attacks					$allowed=true;										// If configuration file has not been set for dynamic max size, then it is defaulted to 1000x1000 maximum					if(!isset($config['dynamic-max-size'])){						// Default maximum image dimension height/width						$config['dynamic-max-size']=4096;					}										// Checking if image is within allowed parameters					if($width>$config['dynamic-max-size'] || $height>$config['dynamic-max-size'] || $height==0 || $width==0){						// If image dimensions are beyond allowed values						$allowed=false;					} elseif(isset($config['dynamic-size-whitelist']) && $config['dynamic-size-whitelist']!='' && ($width!=$resolution[0] || $height!=$resolution[1]) && !in_array($width.'x'.$height,explode(' ',$config['dynamic-size-whitelist']))){						// For size whitelist check						// If resolution has been changed and this resolution is not found in whitelist						$allowed=false;					} elseif(isset($config['dynamic-color-whitelist']) && $config['dynamic-color-whitelist']!='' && ($red || $green || $blue) && !in_array($red.','.$green.','.$blue,explode(' ',$config['dynamic-color-whitelist']))){						// For RGB whitelist check						// If RGB values are not defaults and this setting is not found in color whitelist						$allowed=false;					} elseif(isset($config['dynamic-quality-whitelist']) && $config['dynamic-quality-whitelist']!='' && $quality && !in_array('@'.$quality,explode(' ',$config['dynamic-quality-whitelist']))){						// For quality whitelist check						// If quality values are not defaults and this setting is not found in quality whitelist						$allowed=false;					} elseif(isset($config['dynamic-position-whitelist']) && $config['dynamic-position-whitelist']!='' && ($top || $left) && !in_array($top.'-'.$left,explode(' ',$config['dynamic-position-whitelist']))){						// For position whitelist check						// If position values are not defaults and this setting is not found in position whitelist						$allowed=false;					} elseif($allowed && isset($config['dynamic-filter-whitelist']) && $config['dynamic-filter-whitelist']!='' && !empty($filters)){						// For filter whitelist check						foreach($filters as $filter){							// If filters are not in filter whitelist then processing is canceled							if($allowed && !in_array($filter['type'].'@'.$filter['alpha'].','.implode(',',$filter['settings']),explode(' ',$config['dynamic-filter-whitelist']))){								$allowed=false;							}						}					}									// IMAGE GENERATION BASED ON SETTINGS									// If whitelist checks did not fail and image dimensions are good					if($allowed){												// If algorithm, quality setting or a filter is set						if($algorithm || $quality || !empty($filters)){													// If cache folder does not exist, it is created							if(!is_dir($cacheDirectory)){								if(!mkdir($cacheDirectory,0755)){									trigger_error('Cannot create cache folder',E_USER_ERROR);								}							}													// This functionality only works if GD library is loaded							if(extension_loaded('gd')){																	// IMAGE EDITING																// Requiring WWW_Imager class that is used to do basic image manipulation									require(__ROOT__.'engine'.DIRECTORY_SEPARATOR.'class.www-imager.php');									// New imager object, this is a wrapper around GD or ImageMagick library									$picture=new WWW_Imager();									// Current image file is loaded into Imager									if(!$picture->input($resource)){										trigger_error('Cannot load image',E_USER_ERROR);									}																// IMAGE RESIZES																// Image is filtered through resize algorithm and saved in cache directory									switch($algorithm){										case 'fitcrop':											// Crop algorithm fits the image into set dimensions, cutting the edges that do not fit											if(!$picture->resizeFitCrop($width,$height,$left,$top)){												trigger_error('Cannot resize image with fit-crop algorithm',E_USER_ERROR);											}											break;										case 'crop':											// Crop algorithm places image in new dimensions box cutting the edges that do not fit											if(!$picture->resizeCrop($width,$height,$left,$top,$red,$green,$blue)){												trigger_error('Cannot resize image with crop algorithm',E_USER_ERROR);											}											break;										case 'fitwithbackground':											// This fits image inside the box and gives it certain color background (if applicable)											if(!$picture->resizeFit($width,$height,$left,$top,$red,$green,$blue)){												trigger_error('Cannot resize image with fit-with-background algorithm',E_USER_ERROR);											}											break;										case 'fitwithoutbackground':											// This simply resizes the image to fit specific dimensions											if(!$picture->resizeFitNoBackground($width,$height)){												trigger_error('Cannot resize image with fit-without-background algorithm',E_USER_ERROR);											}											break;										case 'widthonly':											// This resizes the image to fixed width											if(!$picture->resizeWidth($width)){												trigger_error('Cannot resize image with width-only algorithm',E_USER_ERROR);											}											break;										case 'heightonly':											// This resizes the image to fixed height											if(!$picture->resizeHeight($height)){												trigger_error('Cannot resize image with height-only algorithm',E_USER_ERROR);											}											break;									}																// IMAGE FILTERS																	// If filtering is also requested and system does not have it turned off									if((!isset($config['dynamic-image-filters']) || $config['dynamic-image-filters']==true)){																			// As long as there are set filters										if(!empty($filters)){											// Each filter is applied, one by one											foreach($filters as $filter){												if(!$picture->applyFilter($filter['type'],$filter['alpha'],$filter['settings'])){													trigger_error('Cannot apply filter '.$filter['type'],E_USER_ERROR);												}											}										}																		} else {																			// Adding log entry											if(isset($logger)){														$logger->setCustomLogData(array('response-code'=>404,'category'=>'image'));											$logger->writeLog();										}										// Returning 404 header										header('HTTP/1.1 404 Not Found');										die();																			}																	// STORING THE RESULTING IMAGE																	// Resulting image is saved to cache									if(!$picture->output($cacheDirectory.$cacheFilename,$quality,$format)){										trigger_error('Cannot output image file',E_USER_ERROR);									}															} else {								// Without GD library the file is simply stored instead								if(!file_put_contents($cacheDirectory.$cacheFilename,file_get_contents($resource))){									trigger_error('Cannot create resource cache',E_USER_ERROR);								}							}												} else {							// Without needing to process the image the file contents are stimply stored in cache							if(!file_put_contents($cacheDirectory.$cacheFilename,file_get_contents($resource))){								trigger_error('Cannot create resource cache',E_USER_ERROR);							}						}										} else {											// Adding log entry							if(isset($logger)){							$logger->setCustomLogData(array('response-code'=>404,'category'=>'image'));							$logger->writeLog();						}						// Returning 404 header						header('HTTP/1.1 404 Not Found');						die();											}							} else {							// To notify logger that cache was used				$cacheUsed=true;							}				// File URL is set to cache address		$resource=$cacheDirectory.$cacheFilename;			}	// HEADERS	// Serving up the correct content type header	if($base64){		// BASE64 text string		header('Content-Type: application/octet-stream');		header('Content-Transfer-Encoding: base64');	} else {		// Proper content-type is set based on file extension		if(isset($resourceExtension)){			switch($resourceExtension){				case 'jpg':					header('Content-Type: image/jpeg;');					break;				case 'jpeg':					header('Content-Type: image/jpeg;');					break;				case 'png':					header('Content-Type: image/png;');					break;			}		}	}	// If cache is used, then proper headers will be sent	if($noCache){		// User agent is told to cache these results for set duration		header('Cache-Control: no-cache,no-store');		header('Expires: '.gmdate('D, d M Y H:i:s',$_SERVER['REQUEST_TIME']).' GMT');		header('Last-Modified: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT');	} else {		// User agent is told to cache these results for set duration		header('Cache-Control: public,max-age='.$config['resource-cache-timeout']);		header('Expires: '.gmdate('D, d M Y H:i:s',($_SERVER['REQUEST_TIME']+$config['resource-cache-timeout'])).' GMT');		header('Last-Modified: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT');	}	// Robots header	if(isset($config['image-robots'])){		// If image-specific robots setting is defined		header('Robots-Tag: '.$config['image-robots'],true);	} elseif(isset($config['robots'])){		// This sets general robots setting, if it is defined in configuration file		header('Robots-Tag: '.$config['robots'],true);	} else {		// If robots setting is not configured, system tells user agent not to cache the file		header('Robots-Tag: noindex,nocache,nofollow,noarchive,noimageindex,nosnippet',true);	}	// OUTPUT	// Returning image resource	if(!$base64){		// Getting current output length		$contentLength=filesize($resource);		// Content length is defined that can speed up website requests, letting user agent to determine file size		header('Content-Length: '.$contentLength);		// Returning the file to user agent		readfile($resource);	} else {		// Getting file contents and converting it to BASE64 string		$contents=base64_encode(file_get_contents($resource));		// Getting current output length		$contentLength=strlen($contents);		// Content length is defined that can speed up website requests, letting user agent to determine file size		header('Content-Length: '.$contentLength);		// Returning data to user agent		echo $contents;	}		// Cache resource is deleted if cache was requested to be off	if($noCache){		unlink($resource);	}	// WRITING TO LOG	// If Logger is defined then request is logged and can be used for performance review later	if(isset($logger)){		// Assigning custom log data to logger		$logger->setCustomLogData(array('cache-used'=>$cacheUsed,'content-length'=>$contentLength,'category'=>'image'));		// Writing log entry		$logger->writeLog();	}?>