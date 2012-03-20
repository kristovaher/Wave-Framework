<?php

/*
WWW - PHP micro-framework
MVC Controller class

This class is used by index.php gateway to solve current URL request. It uses the URL string 
of the request to calculate what view must be loaded and it uses an internal URL map that it 
maps itself against, stored in /resources/{language-code}.urlmap.php where language code is 
the language that WWW_controller_url detects is being used. This class also deals with things 
such as slashes at the end of URL's and whether first language of the sytem needs to have a 
URL node in the request string or not. It also redirects the client in case URL is incorrectly 
formatted. This class is optional and only needed if one intends to build a website with 
beautiful URL's with WWW framework.

Author and support: Kristo Vaher - kristo@waher.net
*/

// WWW_Factory is parent class for all MVC classes of WWW
class WWW_controller_url extends WWW_Factory {

	// Variables that are used by both methods, solve() and returnViewData()
	private $enforceLanguageUrl;
	private $systemRoot;
	private $languages;
	private $webRoot;
	private $homeView;

	// This is called by index.php gateway when trying to solve request URL to view
	public function solve($input){
		
		// Custom request URL can be used, this is required
		if(isset($input['www-request'])){
			// Request string is loaded from input
			$request=$input['www-request'];
		} else {
			// Formatting and returning the expected result array
			return $this->returnViewData(array('view'=>'404','subview'=>'','language'=>$this->getState('language'),'unsolved-url'=>array()));
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
		$this->homeView=$this->getState('home-view');
		
		// By default it is assumed that home view is used
		$view=$this->homeView;
		
		// To solve the request GET is separated from URL nodes
		$requestNodesRaw=explode('?',$request,2);
		
		// Finding URL map match and module from the request
		$urlNodes=array();
		
		// If there is no request URL set
		if($requestNodesRaw[0]=='' || $requestNodesRaw[0]=='/'){
		
			// If first language code has to be defined in URL, system redirects to URL that has it, otherwise returns home view data
			if($this->enforceLanguageUrl==true){
				// Client is redirected to URL that has just the language node set
				if(isset($requestNodesRaw[1])){
					header('Location: '.$this->webRoot.$language.'/?'.$requestNodesRaw[1],TRUE,302);
				} else {
					header('Location: '.$this->webRoot.$language.'/',TRUE,302);
				}
				die();
			} else {
				// Formatting and returning the expected result array
				return $this->returnViewData(array('view'=>$view,'subview'=>'','language'=>$language,'unsolved-url'=>array()));
			}
			
		} else {
		
			// Request is formatted to remove all potentially harmful characters
			$requestFormatted=preg_replace('/^[\/]|[^A-Za-z\-\_0-9\/]/i','',strtolower($requestNodesRaw[0]));
			
			// Request is exploded into an array that will be looped to find proper view
			$requestNodes=explode('/',$requestFormatted);
			
			// If slash is enforced at the end of the URL then client is redirected to such an URL
			if($enforceSlash==true && end($requestNodes)!=''){
				// If GET variables were set, system redirects to proper URL that has a slash in the end and appends the GET variables
				if(isset($requestNodesRaw[1])){
					header('Location: '.$this->webRoot.$requestNodesRaw[0].'/?'.$requestNodesRaw[1],TRUE,302);
				} else {
					header('Location: '.$this->webRoot.$requestNodesRaw[0].'/',TRUE,302);
				}
				die();
			}
			
			// Looping through all the URL nodes from the request
			foreach($requestNodes as $nodeKey=>$node){
			
				// As long as the URL node in the request is not empty, it is taken into account for finding the proper view
				if($node!='' || !isset($requestNodes[$nodeKey+1])){
					
					// If this is the first request node and it is found in languages array
					if($nodeKey==0 && in_array($node,$this->languages)){
					
						// Language was found and is defined as the request language
						$language=$node;
						
						// If this is the first language and language node is not required in URL, client is redirected to a URL without it
						if($this->enforceLanguageUrl==false && $language==$this->languages[0]){
						
							// We unset the first node, as it was not required
							unset($requestNodes[$nodeKey]);
							// If GET variables were set, system redirects to URL without the language and appends the GET variables
							if(isset($requestNodesRaw[1])){
								header('Location: '.$this->webRoot.implode('/',$requestNodes).'/?'.$requestNodesRaw[1],TRUE,302);
							} else {
								header('Location: '.$this->webRoot.implode('/',$requestNodes).'/',TRUE,302);
							}
							die();
							
						}
						
					} else {
					
						// If language node is required in URL and the first request node was not a language, it is added and client is redirected
						if($nodeKey==0 && $this->enforceLanguageUrl==true){
						
							// Client is redirected to the same URL as before, but with the default language node added
							if(isset($requestNodesRaw[1])){
								header('Location: '.$this->webRoot.$language.'/'.$requestFormatted.'/?'.$requestNodesRaw[1],TRUE,302);
							} else {
								header('Location: '.$this->webRoot.$language.'/'.$requestFormatted,TRUE,302);
							}
							die();
							
						} else {
						
							// If language node is set in request, but has no second URL node set, it is also assumed that it is default view
							if($nodeKey==1 && (!isset($requestNodes[1]) || $requestNodes[1]=='')){
								// Formatting and returning the expected result array
								return $this->returnViewData(array('view'=>$view,'subview'=>'','language'=>$language,'unsolved-url'=>array()));
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
					return $this->returnViewData(array('view'=>'404','subview'=>'','language'=>$language,'unsolved-url'=>array()));
				}

			}
			
		}
		
		// Subview is a sub-category name from URL-map for a module
		$subView='';
		
		// This flag checks if unsolved URL is allowed or not, only URL's with aterisk parameter from URL map allow unsolved URL's
		$unsolvedUrlAllowed=false;
		
		// All nodes of URL's that were not found as modules are stored here
		$unsolvedUrlNodes=array();
		
		// System root that is used for checking if view file exists
		$this->systemRoot=$this->getState('system-root');
		
		// URL Map is stored in this array
		$urlMap=array();
		
		// Checking for existence of URL Map file, if it does not exist a 404 error is returned
		if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.urlmap.php')){
			// Overrides can be used if they are stored in /overrides/resources/ subfolder
			require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$language.'.urlmap.php');
		} else if(file_exists($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$language.'.urlmap.php')){
			// If there was no override, the URL Map is loaded from /resources/
			require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$language.'.urlmap.php');
		} else {
			// Formatting and returning the expected result array
			return $this->returnViewData(array('view'=>'404','subview'=>'','language'=>$language,'unsolved-url'=>array()));
		}
		
		// System loops through URL nodes and attempts to find a match in URL Map
		while(!empty($urlNodes) && $view==$this->homeView){
		
			// This string is used to find a match
			$search=implode('/',$urlNodes);
			// String is matched against URL Map, if match is found the value from URL Map is assigned as view
			if(isset($urlMap[$search])){
			
				// Match was found from URL Map, so the value in URL map is exploded to only include view name
				$viewBits=explode('/',$urlMap[$search]);
				$view=$viewBits[0];
				
				// If additional value is stored, then it is assigned to subView variable
				if(isset($viewBits[1])){
				
					// If additional parameter is an aterisk, then this means that unsolved URL's are allowed and 404 will not be returned if present
					if($viewBits[1]=='*'){
						$unsolvedUrlAllowed=true;
					} else {
						// Subview is stored as a single parameter
						$subView=$viewBits[1];
					}
					
				}
				
				// URL can be unsolved even in case where subview is defined
				if(isset($viewBits[2])){
					// If additional parameter is an aterisk, then this means that unsolved URL's are allowed and 404 will not be returned if present
					if($viewBits[2]=='*'){
						$unsolvedUrlAllowed=true;
					}
				}
				
			} else {
			
				// Last element from the array is removed and inserted to unsolved nodes array
				$bit=array_pop($urlNodes);
				if($bit!=''){
					$unsolvedUrlNodes[]=$bit;
				}
				
			}
			
		}
		
		// Array of unsolved URL nodes is reversed if it is not empty
		if(!empty($unsolvedUrlNodes)){
		
			// Unsolved URL's are reversed so that they can be used in the order they were defined in URL
			$unsolvedUrlNodes=array_reverse($unsolvedUrlNodes);
			
			// 404 is returned if unsolved URL's were not permitted
			if($unsolvedUrlAllowed==false){
				return $this->returnViewData(array('view'=>'404','subview'=>$subView,'language'=>$language,'unsolved-url'=>$unsolvedUrlNodes));
			}
			
		}
			
		// Formatting and returning the expected result array
		return $this->returnViewData(array('view'=>$view,'subview'=>$subView,'language'=>$language,'unsolved-url'=>$unsolvedUrlNodes));
		
	}
	
	// This function returns view data
	private function returnViewData($data){
		
		// Checking for existence of URL Map file, if it does not exist a 404 error is returned
		if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.urlmap.php')){
			// Overrides can be used if they are stored in /overrides/resources/ subfolder
			require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.urlmap.php');
		} else if(file_exists($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.urlmap.php')){
			// If there was no override, the URL Map is loaded from /resources/
			require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.urlmap.php');
		} else {
			// Formatting and returning the expected result array
			return $this->returnViewData(array('view'=>'404','subview'=>'','language'=>$data['language'],'unsolved-url'=>array()));
		}
		
		// System builds usable URL map for views
		$urlMap=array_flip($urlMap);
		foreach($urlMap as $key=>$node){
		
			// Home views do not need a URL node
			if($node!=$this->homeView){
				$node.='/';
			} else {
				$node='';
			}
			
			// If first language URL is not enforced, then this is taken into account
			if($data['language']==$this->languages[0] && $this->enforceLanguageUrl==false){
				$urlMap[$key]=$this->webRoot.$node;
			} else {
				$urlMap[$key]=$this->webRoot.$data['language'].'/'.$node;
			}
			
		}
		
		// This stores flipped array (for reverse access in views and objects) as a state
		$data['url-map']=$urlMap;
		
		// Web root will also be returned
		$data['web-root']=$this->webRoot;
		
		// Web root will also be returned
		$data['system-root']=$this->systemRoot;
		
		// Translations are stored in an array
		$translations=array();
	
		// If translation file exists it is loaded
		// Translations file is first looked for from /overrides/resources/ folder, then /resources/ folder
		if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.translations.php')){
			require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.translations.php');
		} else if(file_exists($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.translations.php')){
			require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.translations.php');
		}
		
		// If module-specific translation file exists it is loaded
		// Translations file is first looked for from /overrides/resources/ folder, then /resources/ folder
		if(file_exists($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php')){
			require($this->systemRoot.'overrides'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php');
		} else if(file_exists($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php')){
			require($this->systemRoot.'resources'.DIRECTORY_SEPARATOR.$data['language'].'.'.$data['view'].'.translations.php');
		}
		
		// Current translations are set as state data
		$data['translations']=$translations;
		
		// Data about the view is returned
		return $data;
		
	}

}
	
?>