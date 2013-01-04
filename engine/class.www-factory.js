
/*
 * Wave Framework <http://www.waveframework.com>
 * JavaScript Factory Class
 *
 * JavaScript factory has very minimal use compared to the Wave Framework's own internal PHP 
 * Factory, but this class is still useful for dynamically loading JavaScript objects from 
 * /resources/classes/ folder or libraries from /resources/libraries/ subfolder.
 *
 * @package    Factory
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/factory_js.htm
 * @since      3.3.0
 * @version    3.4.8
 */

/*
 * Factory class takes only one variable 'base' which defines the root folder where the 
 * Factory looks for the script files. Default location is base URI subfolders in 
 * /resources/ folder.
 * 
 * @param string $base Root address for loading files
 * @return object
 */
function WWW_Factory(base){

	/*
	 * These are the is the base URLs where class and library files are loaded from. Factory 
	 * attempts to load classes and libraries from that folder. This base value can be 
	 * overwritten when defining a different URL when initializing Factory class.
	 */
	if(base==null){
		var baseURI='';
		if('baseURI' in document){
			baseURI=document.baseURI;
		} else {
			var baseTags=document.getElementsByTagName('base');
			if(baseTags.length>0) {
				baseURI=baseTags[0].href;
			} else {
				baseURI=window.location.href;
			}
		}
		var classBase=baseURI+'resources/classes/';
		var libraryBase=baseURI+'resources/libraries/';
	} else {
		var classBase=base;
		var libraryBase=base;
	}
	
	/*
	 * This variable simply holds information about which files have been already loaded to 
	 * make sure that the same classes and libraries are not loaded multiple times.
	 */
	window['WWW_Factory_loaded']=new Object();
	
	/*
	 * This method loads and returns a new object for a class 'className'. Factory attempts 
	 * to load this class definition from a file in /resources/classes/ and file name should 
	 * be written in class.[className].js format in that folder. If 'methodName' is set, then 
	 * this method will also automatically call that method and return result of that method 
	 * instead of the view object itself. If 'methodData' is also set, then this is the input 
	 * variable sent to methodName.
	 *
	 * @param string $className name of the class
	 * @param string $methodName name of the method called once the object is loaded
	 * @param string $methodData input variable for the method that is called
	 * @return array or string, depending on implode setting
	 */
	this.getObject=function(className,methodName,methodData){
	
		// Testing if the class does not exist and needs to be loaded
		if(window[className]==null){
			// Loading the class through AJAX
			if(!loadFile(classBase+'class.'+className+'.js')){
				// Throwing an error if failed
				throw 'could not load a file for '+className;
			}
		}
		
		// Making sure that the object name is defined
		if(window[className]!=null){
			// Has to be an object
			if(typeof(window[className])=='function'){
				// Creating a temporary object
				var newObject=new window[className];
				// Calling the object function with parameters, if requested
				if(methodName!=null && methodName!=false && methodData!=null){
					var result=newObject.methodName(methodData);
				} else if(methodName!=null && methodName!=false){
					var result=newObject.methodName;
				} else {
					// Object itself is returned since function call was not requested
					return newObject;
				}
				// Removing the temporary object
				delete newObject;
				// Returning the result
				return result;
			} else {
				// Throwing an error if failed
				throw className+' is not a class or a function';
			}
		} else {
			// Throwing an error if failed
			throw className+' is not a defined';
		}
		
	}
	
	/*
	 * This method is used to dynamically load a library from /resources/libaries/ subfolder. 
	 * If additional parameters are set, then this method can also automatically call one 
	 * of the functions in the library.
	 *
	 * @param string $name library filename (without the extension)
	 * @param string $functionName optional function name to be called
	 * @param mixed $functionData data array to be sent to optional function call
	 * @return array or string, depending on implode setting
	 */
	this.loadLibrary=function(libraryName,functionName,functionData){
	
		// Multiple libraries can also be requested with a single request
		var libraries=libraryName.split(',');
		
		// If more than single library is requested
		if(libraries.length>1){
			for(var i in libraries){
				libraries[i]=libraries[i]+'.js';
			}
			// New request URL
			var requestUrl=libraryBase+(libraries.join('&'));
		} else {
			// Single request URL
			var requestUrl=libraryBase+libraryName+'.js';
		}
	
		// Attempting to load the contents of the file
		if(loadFile(requestUrl)){
			// If custom function is also requested
			if(functionName!=null){
				// Making sure that the functionName parameter is an actual function
				if(window[functionName]!=null && typeof(window[functionName])=='function'){
					// Sending parameters, if they are defined
					if(functionData!=null){
						return window[functionName](functionData);
					} else {
						return window[functionName]();
					}
				} else {
					throw functionName+' is not a defined function';
				}
			} else {
				// Library is loaded
				return true;
			}
		} else {
			throw 'Cannot load library '+libraryName;
		}
		
	}
	
	/*
	 * This private function is used to load contents of a script file and initialize these 
	 * contents in the browser for use as classes, functions or other scripts.
	 *
	 * @param string $address address of the file to load
	 * @return boolean if success or failure
	 */
	var loadFile=function(address){
	
		// File is only loaded if this file has not already been requested
		if(window['WWW_Factory_loaded'][address]==null){
			// Required for AJAX connections
			var XMLHttp=new XMLHttpRequest();
			// Making the non-async request to the file
			XMLHttp.open('GET',address,false);
			XMLHttp.send(null);
			// Result based on response status
			if(XMLHttp.status===200 || XMLHttp.status===304){
				// Getting the contents of the script
				var data=XMLHttp.responseText;
				// Loading the script contents to the browser
				(window.execScript || function(data){
					window['eval'].call(window,data);
				})(data);
				// Deleting the response content
				delete data;
				// Changing the flag to true so this file is not loaded again
				window['WWW_Factory_loaded'][address]=true;
			} else {
				// Request has failed
				window['WWW_Factory_loaded'][address]=false;
				throw 'Cannot load resource '+address;
			}
		}
		
		// Returning whether this file has been successfully loaded or not
		return window['WWW_Factory_loaded'][address];
		
	}

}
