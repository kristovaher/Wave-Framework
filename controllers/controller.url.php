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
License: This file can be copied, changed and re-published under another license without any restrictions
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_controller_url extends WWW_Factory {

	// This method is called by index.php gateway when trying to solve request URL to view
	// * www-request - This is the request URL that will be parsed
	// Returns modified data about the view from sitemap file
	public function solve($input){
		
		// Default view is loaded from State (this is loaded when no URL is defined)
		$view404=$this->getState('404-view');
		
		// Custom request URL can be used, this is required
		if(isset($input['www-request'])){
			// Request string is loaded from input
			$request=$input['www-request'];
		} else {
			// Formatting and returning the expected result array
			return $this->returnViewData(array('view'=>$view404,'header'=>'HTTP/1.1 404 Not Found'));
		}
		
		// Web root is the base directory of the website
		$webRoot=$this->getState('web-root');
		
		// This setting will force that even the first language (first in languages array) has to be represented in URL
		$enforceSlash=$this->getState('enforce-url-end-slash');
		// This setting means that URL has to end with a slash
		$enforceLanguageUrl=$this->getState('enforce-first-language-url');
		
		// Default language is loaded from State
		$language=$this->getState('language');
		// List of defined languages is loaded from State
		$languages=$this->getState('languages');
		
		// Default view is loaded from State (this is loaded when no URL is defined)
		$viewHome=$this->getState('home-view');
		// By default it is assumed that home view is used
		$view=$viewHome;
		
		// To solve the request GET is separated from URL nodes
		$requestNodesRaw=explode('?',$request,2);
		// This array stores all the URL nodes that will be matched against sitemap
		$urlNodes=array();
		
		// This is used for testing if the returned URL should be home or not
		$returnHome=false;
		
		// If there is no request URL set
		if($requestNodesRaw[0]=='' || $requestNodesRaw[0]=='/'){
		
			// If first language code has to be defined in URL, system redirects to URL that has it, otherwise returns home view data
			if($enforceLanguageUrl==true){
				// User agent is redirected to URL that has just the language node set
				if(isset($requestNodesRaw[1])){
					return array('www-permanent-redirect'=>$webRoot.$language.'/?'.$requestNodesRaw[1]);
				} else {
					return array('www-permanent-redirect'=>$webRoot.$language.'/');
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
					return array('www-permanent-redirect'=>$webRoot.$requestNodesRaw[0].'/?'.$requestNodesRaw[1]);
				} else {
					return array('www-permanent-redirect'=>$webRoot.$requestNodesRaw[0].'/');
				}
			}
			
			// Looping through all the URL nodes from the request
			foreach($requestNodes as $nodeKey=>$node){
			
				// As long as the URL node in the request is not empty, it is taken into account for finding the proper view
				if($node!='' || !isset($requestNodes[$nodeKey+1])){
					
					// If this is the first request node and it is found in languages array
					if($nodeKey==0 && in_array($node,$languages)){
					
						// Language was found and is defined as the request language
						$language=$node;
						// If this is the first language and language node is not required in URL, user agent is redirected to a URL without it
						if($enforceLanguageUrl==false && $language==$languages[0]){
							// We unset the first node, as it was not required
							unset($requestNodes[$nodeKey]);
							// If GET variables were set, system redirects to URL without the language and appends the GET variables
							if(isset($requestNodesRaw[1])){ 
								return array('www-permanent-redirect'=>$webRoot.implode('/',$requestNodes).'?'.$requestNodesRaw[1]);
							} else {
								return array('www-permanent-redirect'=>$webRoot.implode('/',$requestNodes));
							}
						}
						
					} else {
					
						// If language node is required in URL and the first request node was not a language, it is added and user agent is redirected
						if($nodeKey==0 && $enforceLanguageUrl==true){
							// User agent is redirected to the same URL as before, but with the default language node added
							if(isset($requestNodesRaw[1])){
								return array('www-permanent-redirect'=>$webRoot.$language.'/'.$requestFormatted.'?'.$requestNodesRaw[1]);
							} else {
								return array('www-permanent-redirect'=>$webRoot.$language.'/'.$requestFormatted);
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
					return $this->returnViewData(array('view'=>$view404,'header'=>'HTTP/1.1 404 Not Found'));
				}

			}
			
		}
		
		// Notifying State of current language
		$this->setState('language',$language);
		
		// All nodes of URL's that were not found as modules are stored here
		$unsolvedUrl=array();
		
		// URL Map is stored in this array
		$siteMap=$this->getSitemapRaw($language);
		if(!$siteMap){
			// Formatting and returning the expected result array
			return $this->returnViewData(array('view'=>$view404,'header'=>'HTTP/1.1 404 Not Found'));
		}
		
		// Array that stores information from sitemap file
		$siteMapInfo=$siteMap[$viewHome];
		
		// If home is not expected to be returned
		if(!$returnHome){
		
			// System loops through URL nodes and attempts to find a match in URL Map
			while(!empty($urlNodes)){
				// This string is used to find a match
				$search=implode('/',$urlNodes);
				// String is matched against URL Map, if match is found the value from URL Map is assigned as view
				if(isset($siteMap[$search])){
					// Setting current page information
					$siteMapInfo=$siteMap[$search];
					// Match was found from URL Map, so view is defined
					$view=$siteMap[$search]['view'];
					// Page has been found
					break;
				} else {
					// Last element from the array is removed and inserted to unsolved nodes array
					$bit=array_pop($urlNodes);
					if($bit!=''){
						$unsolvedUrl[]=$bit;
					}
				}
			}
			
			// If the found view is home view, then we simply redirect to home view without the long url
			if(empty($unsolvedUrl) && $view==$viewHome){
			
				// If first language is used and it is not needed to use language URL in first language
				if($enforceLanguageUrl==false && $language==$languages[0]){
					// If request nodes are set in the URL
					if(isset($requestNodesRaw[1])){
						return array('www-permanent-redirect'=>$webRoot.'?'.$requestNodesRaw[1]);
					} else {
						return array('www-permanent-redirect'=>$webRoot);
					}
				} else {
					// If request nodes are set in the URL
					if(isset($requestNodesRaw[1])){
						return array('www-permanent-redirect'=>$webRoot.$language.'/?'.$requestNodesRaw[1]);
					} else {
						return array('www-permanent-redirect'=>$webRoot.$language.'/');
					}
				}
			
			}
		
		}
		
		// It is possible to assign temporary or permanent redirection in Sitemap, causing 302 or 301 redirect
		if(isset($siteMapInfo['temporary-redirect']) && $siteMapInfo['temporary-redirect']!=''){
			// Query string is also sent, if it has been defined
			if(isset($requestNodesRaw[1]) && strpos($siteMapInfo['temporary-redirect'],'?')===false){
				return array('www-temporary-redirect'=>$siteMapInfo['temporary-redirect'].'?'.$requestNodesRaw[1]);
			} else {
				return array('www-temporary-redirect'=>$siteMapInfo['temporary-redirect']);
			}
		} elseif(isset($siteMapInfo['permanent-redirect']) && $siteMapInfo['permanent-redirect']!=''){
			// Query string is also sent, if it has been defined
			if(isset($requestNodesRaw[1]) && strpos($siteMapInfo['permanent-redirect'],'?')===false){
				return array('www-permanent-redirect'=>$siteMapInfo['permanent-redirect'].'?'.$requestNodesRaw[1]);
			} else {
				return array('www-permanent-redirect'=>$siteMapInfo['permanent-redirect']);
			}
		}
		
		// Populating sitemap info with additional details
		$siteMapInfo['request-url']='/'.$requestNodesRaw[0];
		if(isset($requestNodesRaw[1])){
			$siteMapInfo['request-parameters']=$requestNodesRaw[1];
		} else {
			$siteMapInfo['request-parameters']='';
		}
		$siteMapInfo['language']=$language;
		$siteMapInfo['web-root']=$webRoot;
		
		// Array of unsolved URL nodes is reversed if it is not empty
		if(!empty($unsolvedUrl)){
			// Unsolved URL's are reversed so that they can be used in the order they were defined in URL
			$unsolvedUrl=array_reverse($unsolvedUrl);
			// 404 is returned if unsolved URL's were not permitted
			if(!isset($siteMapInfo['unsolved-url']) || $siteMapInfo['unsolved-url']==false){
				// Populating sitemap info with additional details
				$siteMapInfo['unsolved-url']=$unsolvedUrl;
				return $this->returnViewData(array('view'=>$view404,'cache-timeout'=>0,'header'=>'HTTP/1.1 404 Not Found')+$siteMapInfo);
			}
		} else {
			// It is possible to assign temporary or permanent redirection in Sitemap, causing 302 or 301 redirect
			if(isset($siteMapInfo['temporary-redirect']) && $siteMapInfo['temporary-redirect']!=''){
				// Query string is also sent, if it has been defined
				if(isset($requestNodesRaw[1]) && strpos($siteMapInfo['temporary-redirect'],'?')===false){
					return array('www-temporary-redirect'=>$siteMapInfo['temporary-redirect'].'?'.$requestNodesRaw[1]);
				} else {
					return array('www-temporary-redirect'=>$siteMapInfo['temporary-redirect']);
				}
			} elseif(isset($siteMapInfo['permanent-redirect']) && $siteMapInfo['permanent-redirect']!=''){
				// Query string is also sent, if it has been defined
				if(isset($requestNodesRaw[1]) && strpos($siteMapInfo['permanent-redirect'],'?')===false){
					return array('www-permanent-redirect'=>$siteMapInfo['permanent-redirect'].'?'.$requestNodesRaw[1]);
				} else {
					return array('www-permanent-redirect'=>$siteMapInfo['permanent-redirect']);
				}
			}
		}
		
		// Populating sitemap info with additional details
		$siteMapInfo['unsolved-url']=$unsolvedUrl;
			
		// Formatting and returning the expected result array
		return $this->returnViewData($siteMapInfo);
		
	}
	
	// This function returns view data
	private function returnViewData($data){
	
		// DEFAULTS FOR VIEW DATA
		
			// If view controller has not been defined in sitemap configuration
			if(!isset($data['controller'])){
				$data['controller']='view';
			}
			
			// If view subview has not been defined in sitemap configuration
			if(!isset($data['subview'])){
				$data['subview']='';
			}
			
			// If view hidden state has not been defined in sitemap configuration
			if(!isset($data['hidden'])){
				$data['hidden']=0;
			}
			
			// If project title is not set by Sitemap, system defines the State project title as the value
			if(!isset($data['project-title'])){
				$data['project-title']=$this->getState('project-title');
			}
			
			// Robots data is also returned to views
			if(!isset($data['robots'])){
				$data['robots']=$this->getState('robots');
			}
			
			// Notifying State of view data
			$this->setState('view',$data);
			
		// HEADERS
		
			// These headers will be set by API
			$data['www-set-header']=array();
			
			if(isset($data['header'])){
				$data['www-set-header'][]=$data['header'];
			}
			
			// Writing robots data to header
			if($data['robots']!=''){
				// This header is not 'official HTTP header', but is widely supported by Google and others
				$data['www-set-header'][]='X-Robots-Tag: '.$data['robots'];
			}
			
		// HANDLING AND CHECKS
		
			// This is the best place to build your authentication module for web views
			// But the commands that it uses are not shown here, so it is commented out
			// This is essentially the boilerplate startpoint for you to implement authentication, as the actual login redirection is turned off
			// Attempting to get user session
			if(isset($data['rights'])){
				$data['rights']=explode(',',$data['rights']);
				// if($data['view']!='login' && !$this->checkRights($data['rights'])){
					// $siteMapReference=$this->getSitemap();
					// return $this->errorArray('Authentication required',300,array('www-temporary-redirect'=>$siteMapReference['login']['url']));
				// }
			}
		
		// Data about the view is returned
		return $data;
		
	}

}
	
?>