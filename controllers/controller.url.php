<?php

/*
WWW Framework
MVC Controller class

This class is used by index.php gateway to solve current URL request. It uses the URL string 
of the request to calculate what view must be loaded and it uses an internal sitemap that it 
maps itself against, stored in /resources/{language-code}.sitemap.php where language code is 
the language that WWW_controller_url detects is being used. This class also deals with things 
such as slashes at the end of URL's and whether first language of the sytem needs to have a 
URL node in the request string or not. It also redirects the user agent in case URL is 
incorrectly formatted. This class is optional and only needed if one intends to build a 
website with beautiful URL's with WWW framework.

* Returns view based data, such as translations, sitemap and view settings, to view through data handler

Author and support: Kristo Vaher - kristo@waher.net
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_controller_url extends WWW_Factory {

	// Variables that are used by both methods, solve() and returnViewData()
	private $enforceLanguageUrl;
	// This stores system work directory
	private $systemRoot;
	// This stores web relative directory
	private $webRoot;
	// This stores defined system languages
	private $languages;
	// This stores default home view
	private $viewHome;
	// This stores default 404 view
	private $view404;
	// This stores current language Sitemap
	private $siteMap;
	// This stores sitemap information of detected URL from Sitemap file
	private $siteMapInfo=array();
	// Stores current robots string
	private $robots;

	// This is called by index.php gateway when trying to solve request URL to view
	public function solve($input){
		
		// Default view is loaded from State (this is loaded when no URL is defined)
		$this->view404=$this->getState('404-view');
		// System root is the base directory of files on web server
		$this->robots=$this->getState('robots');
		
		// Custom request URL can be used, this is required
		if(isset($input['www-request'])){
			// Request string is loaded from input
			$request=$input['www-request'];
		} else {
			// Formatting and returning the expected result array
			return $this->returnViewData(array('view'=>$this->view404,'subview'=>'','language'=>$this->getState('language'),'unsolved-url'=>array()));
		}
		
		// Web root is the base directory of the website
		$this->webRoot=$this->getState('web-root');
		// System root is the base directory of files on web server
		$this->systemRoot=$this->getState('system-root');
		
		// This setting will force that even the first language (first in languages array) has to be represented in URL
		$enforceSlash=$this->getState('enforce-url-end-slash');
		// This setting means that URL has to end with a slash
		$this->enforceLanguageUrl=$this->getState('enforce-first-language-url');
		
		// Default language is loaded from State
		$language=$this->getState('language');
		// List of defined languages is loaded from State
		$this->languages=$this->getState('languages');
		
		// Default view is loaded from State (this is loaded when no URL is defined)
		$this->viewHome=$this->getState('home-view');
		// By default it is assumed that home view is used
		$view=$this->viewHome;
		
		// To solve the request GET is separated from URL nodes
		$requestNodesRaw=explode('?',$request,2);
		// Finding URL map match and module from the request
		$urlNodes=array();
		
		// This is used for testing if the returned URL should be home or not
		$returnHome=false;
		
		// If there is no request URL set
		if($requestNodesRaw[0]=='' || $requestNodesRaw[0]=='/'){
		
			// If first language code has to be defined in URL, system redirects to URL that has it, otherwise returns home view data
			if($this->enforceLanguageUrl==true){
				// User agent is redirected to URL that has just the language node set
				if(isset($requestNodesRaw[1])){
					return array('www-permanent-redirect'=>$this->webRoot.$language.'/?'.$requestNodesRaw[1]);
				} else {
					return array('www-permanent-redirect'=>$this->webRoot.$language.'/');
				}
			} else {
				// Expecting to return Home view
				$returnHome=true;
			}
			
		} else {
		
			// Request is formatted to remove all potentially harmful characters
			$requestFormatted=preg_replace('/^[\/]|[^A-Za-z\-\_0-9\/]/i','',strtolower($requestNodesRaw[0]));
			// Request is exploded into an array that will be looped to find proper view
			$requestNodes=explode('/',$requestFormatted);
			// If slash is enforced at the end of the URL then user agent is redirected to such an URL
			if($enforceSlash==true && end($requestNodes)!=''){
				// If GET variables were set, system redirects to proper URL that has a slash in the end and appends the GET variables
				if(isset($requestNodesRaw[1])){
					return array('www-permanent-redirect'=>$this->webRoot.$requestNodesRaw[0].'/?'.$requestNodesRaw[1]);
				} else {
					return array('www-permanent-redirect'=>$this->webRoot.$requestNodesRaw[0].'/');
				}
			}
			
			// Looping through all the URL nodes from the request
			foreach($requestNodes as $nodeKey=>$node){
			
				// As long as the URL node in the request is not empty, it is taken into account for finding the proper view
				if($node!='' || !isset($requestNodes[$nodeKey+1])){
					
					// If this is the first request node and it is found in languages array
					if($nodeKey==0 && in_array($node,$this->languages)){
					
						// Language was found and is defined as the request language
						$language=$node;
						// If this is the first language and language node is not required in URL, user agent is redirected to a URL without it
						if($this->enforceLanguageUrl==false && $language==$this->languages[0]){
							// We unset the first node, as it was not required
							unset($requestNodes[$nodeKey]);
							// If GET variables were set, system redirects to URL without the language and appends the GET variables
							if(isset($requestNodesRaw[1])){ 
								return array('www-permanent-redirect'=>$this->webRoot.implode('/',$requestNodes).'?'.$requestNodesRaw[1]);
							} else {
								return array('www-permanent-redirect'=>$this->webRoot.implode('/',$requestNodes));
							}
						}
						
					} else {
					
						// If language node is required in URL and the first request node was not a language, it is added and user agent is redirected
						if($nodeKey==0 && $this->enforceLanguageUrl==true){
							// User agent is redirected to the same URL as before, but with the default language node added
							if(isset($requestNodesRaw[1])){
								return array('www-permanent-redirect'=>$this->webRoot.$language.'/'.$requestFormatted.'?'.$requestNodesRaw[1]);
							} else {
								return array('www-permanent-redirect'=>$this->webRoot.$language.'/'.$requestFormatted);
							}
						} else {
							// If language node is set in request, but has no second URL node set, it is also assumed that it is default view
							if($requestNodes[0]==$language && $nodeKey==1 && (!isset($requestNodes[1]) || $requestNodes[1]=='')){
								// Expecting to return Home view
								$returnHome=true;
							} else {
								// Every other request node is added to the array that looks for matching URL from URL Map
								if($node!=''){
									$urlNodes[]=$node;
								}
							}
						}
						
					}
					
				} else {
					// Formatting and returning the expected result array
					return $this->returnViewData(array('view'=>$this->view404,'subview'=>'','language'=>$language,'unsolved-url'=>array()));
				}

			}
			
		}
		
		// This flag checks if unsolved URL is allowed or not, only URL's with aterisk parameter from URL map allow unsolved URL's
		$unsolvedUrlAllowed=false;
		// All nodes of URL's that were not found as modules are stored here
		$unsolvedUrlNodes=array();
		
		// System root that is used for checking if view file exists
		$this->systemRoot=$this->getState('system-root');
		
		// URL Map is stored in this array
		$siteMap=array();
		
		// Checking for existence of URL Map file, if it does not exist a 404 error is returned
		if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.php')){
			// Overrides can be used if they are stored in /overrides/resources/ subfolder
			require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.php');
		} elseif(file_exists($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.php')){
			// If there was no override, the URL Map is loaded from /resources/
			require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$language.'.sitemap.php');
		} else {
			// Formatting and returning the expected result array
			return $this->returnViewData(array('view'=>$this->view404,'subview'=>'','language'=>$language,'unsolved-url'=>array()));
		}
		
		// Storing sitemap for later use
		$this->siteMap=$siteMap;
		
		// If home is not expected to be returned
		if(!$returnHome){
		
			// System loops through URL nodes and attempts to find a match in URL Map
			while(!empty($urlNodes)){
			
				// This string is used to find a match
				$search=implode('/',$urlNodes);
				// String is matched against URL Map, if match is found the value from URL Map is assigned as view
				if(isset($this->siteMap[$search])){
					// Setting current page information
					$this->siteMapInfo=$this->siteMap[$search];
					// Match was found from URL Map, so view is defined
					$view=$this->siteMap[$search]['view'];
					// Page has been found
					break;
				} else {
					// Last element from the array is removed and inserted to unsolved nodes array
					$bit=array_pop($urlNodes);
					if($bit!=''){
						$unsolvedUrlNodes[]=$bit;
					}
				}
				
			}
			
			// If the found view is home view, then we simply redirect to home view without the long url
			if(empty($unsolvedUrlNodes) && $view==$this->viewHome){
			
				// If first language is used and it is not needed to use language URL in first language
				if($this->enforceLanguageUrl==false && $language==$this->languages[0]){
					// If request nodes are set in the URL
					if(isset($requestNodesRaw[1])){
						return array('www-permanent-redirect'=>$this->webRoot.'?'.$requestNodesRaw[1]);
					} else {
						return array('www-permanent-redirect'=>$this->webRoot);
					}
				} else {
					// If request nodes are set in the URL
					if(isset($requestNodesRaw[1])){
						return array('www-permanent-redirect'=>$this->webRoot.$language.'/?'.$requestNodesRaw[1]);
					} else {
						return array('www-permanent-redirect'=>$this->webRoot.$language.'/');
					}
				}
			
			}
		
		} else {
			// Setting current page information when returning Home view
			$this->siteMapInfo=$this->siteMap[$this->viewHome];
		}
		
		// If unsolved URL's are allowed
		if(isset($this->siteMapInfo['unsolved-url-nodes']) && $this->siteMapInfo['unsolved-url-nodes']==true){
			$unsolvedUrlAllowed=true;
		}
		
		// It is possible to assign temporary or permanent redirection in Sitemap, causing 302 or 301 redirect
		if(isset($this->siteMapInfo['temporary-redirect']) && $this->siteMapInfo['temporary-redirect']!=''){
			// Query string is also sent, if it has been defined
			if(isset($requestNodesRaw[1]) && strpos($this->siteMapInfo['temporary-redirect'],'?')===false){
				return array('www-temporary-redirect'=>$this->siteMapInfo['temporary-redirect'].'?'.$requestNodesRaw[1]);
			} else {
				return array('www-temporary-redirect'=>$this->siteMapInfo['temporary-redirect']);
			}
		} elseif(isset($this->siteMapInfo['permanent-redirect']) && $this->siteMapInfo['permanent-redirect']!=''){
			// Query string is also sent, if it has been defined
			if(isset($requestNodesRaw[1]) && strpos($this->siteMapInfo['permanent-redirect'],'?')===false){
				return array('www-permanent-redirect'=>$this->siteMapInfo['permanent-redirect'].'?'.$requestNodesRaw[1]);
			} else {
				return array('www-permanent-redirect'=>$this->siteMapInfo['permanent-redirect']);
			}
		}
		
		// It is possible to overwrite the default robots setting
		if(isset($this->siteMapInfo['robots'])){
			$this->robots=$this->siteMapInfo['robots'];
		}
		
		// Array of unsolved URL nodes is reversed if it is not empty
		if(!empty($unsolvedUrlNodes)){
			// Unsolved URL's are reversed so that they can be used in the order they were defined in URL
			$unsolvedUrlNodes=array_reverse($unsolvedUrlNodes);
			// 404 is returned if unsolved URL's were not permitted
			if($unsolvedUrlAllowed==false){
				return $this->returnViewData(array('view'=>$this->view404,'language'=>$language,'unsolved-url'=>$unsolvedUrlNodes));
			}
		}
			
		// Formatting and returning the expected result array
		return $this->returnViewData(array('view'=>$view,'language'=>$language,'unsolved-url'=>$unsolvedUrlNodes));
		
	}
	
	// This function returns view data
	private function returnViewData($data){
		
		// Appending the data from Sitemap file
		$data+=$this->siteMapInfo;
		
		// If view controller has not been defined in sitemap configuration
		if(!isset($data['view-controller'])){
			$data['view-controller']='view';
		}
		
		// This will be returned to view and can be used there for building links
		$siteMapReference=array();
		
		// If sitemap has not been defined then it has to be loaded
		if(empty($this->siteMap)){
			// Checking for existence of URL Map file, if it does not exist a 404 error is returned
			if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.sitemap.php')){
				// Overrides can be used if they are stored in /overrides/resources/ subfolder
				require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.sitemap.php');
			} elseif(file_exists($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.sitemap.php')){
				// If there was no override, the URL Map is loaded from /resources/
				require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.sitemap.php');
			} else {
				// Formatting and returning the expected result array
				return $this->returnViewData(array('view'=>$this->view404,'language'=>$data['language'],'unsolved-url'=>array()));
			}
			$this->siteMap=$siteMap;
		}
		
		// System builds usable URL map for views
		foreach($this->siteMap as $key=>$node){
			// Only sitemap nodes with set view will be assigned to reference
			if(isset($node['view'])){
				// Since the same view can be referenced in multiple locations
				if(isset($node['subview'])){
					$node['view']=$node['view'].'/'.$node['subview'];
				}
				// This is used only if view has not yet been defined
				if(!isset($siteMapReference[$node['view']])){
					$siteMapReference[$node['view']]=$key;
				}
				// Home views do not need a URL node
				if($node['view']!=$this->viewHome){
					$url=$key.'/';
				} else {
					$url='';
				}
				// Storing data from Sitemap file
				$siteMapReference[$node['view']]=$this->siteMap[$key];
				// If first language URL is not enforced, then this is taken into account
				if($data['language']==$this->languages[0] && $this->enforceLanguageUrl==false){
					$siteMapReference[$node['view']]['url']=$this->webRoot.$url;
				} else {
					$siteMapReference[$node['view']]['url']=$this->webRoot.$data['language'].'/'.$url;
				}
			}
		}
		
		// This is the best place to build your authentication module for web views
		// But the commands that it uses are not shown here, so it is commented out
		// This is essentially the boilerplate startpoint for you to implement authentication
		// Attempting to get user session
		// if(isset($data['rights'])){
			// $data['user']=$this->api('user-getUserSession',array('rights'=>$data['rights']));
			// if(!$data['user'] && $data['view']!='login'){
				// return $this->errorArray('Authentication required',300,array('www-temporary-redirect'=>$siteMapReference['login']['url']));
			// }
		// }
		
		// This stores flipped array (for reverse access in views and objects) as a state
		$data['sitemap']=$siteMapReference;
		// Web root will also be returned
		$data['web-root']=$this->webRoot;
		// Web root will also be returned
		$data['system-root']=$this->systemRoot;
		
		// These headers will be set by API
		$data['www-set-header']=array();
		
		// If view file is found as non-existent, a proper header is added
		if($data['view']==$this->view404){
			$data['www-set-header'][]='HTTP/1.1 404 Not Found';
		}
		
		// If project title is not set by Sitemap, system defines the State project title as the value
		if(!isset($data['project-title'])){
			$data['project-title']=$this->getState('project-title');
		}
		
		// Writing robots data to header
		if(isset($data['robots']) && $data['robots']!=''){
			// This header is not 'official HTTP header', but is widely supported by Google and others
			$data['www-set-header'][]='X-Robots-Tag: '.$data['robots'];
		} elseif($this->robots!=''){
			// This will be set from State default value, if Sitemap did not define robots
			$data['www-set-header'][]='X-Robots-Tag: '.$this->robots;
			// Robots data is also returned to views
			$data['robots']=$this->robots;
		}
		
		// Translations are stored in an array
		$translations=array();
	
		// If translation file exists it is loaded
		// Translations file is first looked for from /overrides/resources/ folder, then /resources/ folder
		if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.translations.php')){
			require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.translations.php');
		} else {
			require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.translations.php');
		}
		
		// If module-specific translation file exists it is loaded
		// Translations file is first looked for from /overrides/resources/ folder, then /resources/ folder
		if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php')){
			require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php');
		} elseif(file_exists($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php')){
			require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php');
		}
		
		// Current translations are set as state data
		$data['translations']=$translations;
		
		// Data about the view is returned
		return $data;
		
	}

}
	
?>