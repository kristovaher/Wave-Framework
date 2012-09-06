
/*
 * Wave Framework <http://www.waveframework.com>
 * JavaScript API Wrapper Class
 *
 * Main purpose of an API Wrapper is to make it easier to make API requests over HTTP to a system 
 * built on Wave Framework. API Wrapper class does everything for the developer without requiring 
 * the developer to learn the ins and outs of technical details about how to build an API request. 
 * Wave Framework comes with two separate API authentication methods, one more secure than the 
 * other, both which are handled by this Wrapper class. JavaScript API Wrapper does not support 
 * sending data in encrypted form or decrypting encrypted data from a response.
 *
 * @package    API
 * @author     Kristo Vaher <kristo@waher.net>
 * @copyright  Copyright (c) 2012, Kristo Vaher
 * @license    GNU Lesser General Public License Version 3
 * @tutorial   /doc/pages/wrapper_js.htm
 * @since      2.0.1
 * @version    3.2.0
 */

/*
 * Wrapper object creation requires an 'address', which is the address that Wrapper will make 
 * API requests to. If this is not defined, then 'address' assumes that the system it makes 
 * requests to is the same where the API is loaded from. 'language' is a language keyword from 
 * the system that API makes a connection with and is used whenever language-specific results 
 * are returned from API.
 * 
 * @param string $address API address, default value is current domain presumed API address
 * @param string $language language keyword, default value is current document language
 * @return object
 */
function WWW_Wrapper(address,language){

	// Finding a default, if not set
	if(address==null){
		address=document.baseURI+'json.api';
	}
	
	// Finding a default, if not set
	if(language==null && document.documentElement.lang!=null){
		language=document.documentElement.lang;
	}

	/* 
	 * This is the address and URL of the API that the Wrapper will connect to. The API address 
	 * must be for Wave Framework API. This value is set either in object creation or when 
	 * setting 'www-address' input variable.
	 */
	var apiAddress=address;
	
	/*
	 * This holds the current language of the API, it can be useful if the API commands return 
	 * language-specific responses and translations from the API. This variable is set by sending 
	 * 'www-language' input variable.
	 */
	var apiLanguage=language;
	
	/*
	 * This holds information about current API state, such as profile name, secret key and 
	 * various API-related flags for callbacks, asyncrhonous status and more. This variable is 
	 * passed around per each API call.
	 */
	var apiState={
		apiProfile:false,
		apiSecretKey:false,
		apiToken:false,
		apiPublicToken:false,
		apiHashValidation:true,
		returnHash:false,
		returnTimestamp:false,
		trueCallback:false,
		falseCallback:false,
		errorCallback:false,
		timestampDuration:60,
		hiddenWindowCounter:0,
		apiSubmitFormId:false,
		asynchronous:false,
		unserialize:true
	}
	
	/*
	 * This variable holds the last known error message returned from the API.
	 */
	var errorMessage=false;
	
	/*
	 * This variable holds the last known response code returned from the API.
	 */
	var responseCode=false;
	
	/*
	 * Input data is a variable that stores all the plain-text input sent with the API request, 
	 * it's a key-value pair of variables and their values for the API.
	 */
	var inputData=new Object();
	
	/*
	 * This is an array that gathers log information about the requests made through the API 
	 * that can be used for debugging purposes should something go wrong.
	 */
	var log=new Array();
	
	/*
	 * This is the user-agent string of the API Wrapper. At the moment it is not possible to 
	 * set custom headers with AJAX requests, so this variable is unused in the class and only 
	 * defined for future purpose.
	 */
	var userAgent='WWWFramework/3.2.0 (JavaScript)';
	
	/*
	 * This is the GET string maximum length. Most servers should easily be able to deal with 
	 * 2048 bytes of request string length, but this value can be changed by submitting a 
	 * different length with 'www-get-length' input value.
	 */
	var getLimit=2048;
	
	/*
	 * If this value is set, then API log will be reset after each API request. This value can 
	 * be sent with 'www-reset-log' keyword sent to Wrapper.
	 */
	var resetLog=true;
		
	// Log entry
	log.push('Wave API Wrapper object created with API address: '+address);
	
	// SETTINGS
		
		/*
		 * This method returns current log of the API wrapper. If 'implode' is set, then the 
		 * value of 'implode' is used as a character to implode the log with. Otherwise the log 
		 * is returned as an array.
		 *
		 * @param string $implode String to implode the log entries with
		 * @return array or string, depending on implode setting
		 */
		this.returnLog=function(implode){
			log.push('Returning log');
			// Imploding, if requested
			if(implode==null){
				return log;
			} else {
				return log.join(implode);
			}
		}
		
		/*
		 * This method clears the API log. This method can be called manually or is called 
		 * automatically if log is assigned to be reset with each new API request made by the 
		 * object.
		 * 
		 * @return boolean
		 */
		this.clearLog=function(){
			log=new Array();
			return true;
		}
		
		/*
		 * This method returns currently used token, if one exists. This can be stored for 
		 * subsequent requests with Wrapper (or manually over HTTP).
		 *
		 * @return string or false if token does not exist
		 */
		this.getToken=function(){
			// Returning from the state
			return apiState.apiToken;
		}
		
	// INPUT
		
		/*
		 * This method is used to set an input value in the API Wrapper. 'input' is the key 
		 * to set and 'value' is the value of the input key. 'input' can also be an array, 
		 * in which case multiple input values will be set in the same call. This method calls 
		 * private inputSetter() function that checks the input value for any internal flags 
		 * that might not actually be sent as an input to the API.
		 *
		 * @param string/object $input input data keyword or an object of input data
		 * @param string $value input value
		 * @return boolean
		 */
		this.setInput=function(input,value){
			//Default value
			if(value==null || value==false){
				value=0;
			}
			// If this is an array then it populates input array recursively
			if(typeof(input)==='object'){
				for(var node in input){
					inputSetter(node,input[node]);
				}
			} else {
				inputSetter(input,value);
			}
			return true;
		}
		
		/*
		 * This is a helper function that setInput() method uses to actually assign 'value' 
		 * to the 'input' keyword. A lot of the keywords set carry additional functionality 
		 * that may entirely be API Wrapper specific. This method also creates a log entry 
		 * for any value that is changed or set.
		 * 
		 * @param string $input input data key
		 * @param string $value value to be set
		 * @return boolean
		 */
		var inputSetter=function(input,value){
			switch(input){
				case 'www-api':
					apiState.apiAddress=value;
					log.push('API address changed to: '+value);
					break;
				case 'www-hash-validation':
					apiState.apiHashValidation=value;
					if(value){
						log.push('API hash validation is used');
					} else {
						log.push('API hash validation is not used');
					}
					break;
				case 'www-secret-key':
					apiState.apiSecretKey=value;
					log.push('API secret key set to: '+value);
					break;
				case 'www-token':
					apiState.apiToken=value;
					log.push('API session token set to: '+value);
					break;
				case 'www-profile':
					apiState.apiProfile=value;
					log.push('API profile set to: '+value);
					break;
				case 'www-state':
					apiState.apiStateKey=value;
					log.push('API state check key set to: '+value);
					break;
				case 'www-unserialize':
					if(value){
						apiState.unserialize=true;
						log.push('Returned result will be automatically unserialized');
					} else {
						apiState.unserialize=false;
						log.push('Returned result will not be automatically unserialized');
					}
					break;
				case 'www-asynchronous':
					if(value){
						apiState.asynchronous=true;
						log.push('Request will be made asynchronously');
					} else {
						apiState.asynchronous=false;
						log.push('Request will not be made asynchronously');
					}
					break;
				case 'www-return-hash':
					apiState.returnHash=value;
					if(value){
						log.push('API request will require hash validation');
					} else {
						log.push('API request will not require hash validation');
					}
					break;
				case 'www-return-timestamp':
					apiState.returnTimestamp=value;
					if(value){
						log.push('API request will require timestamp validation');
					} else {
						log.push('API request will not require timestamp validation');
					}
					break;
				case 'www-public-token':
					apiState.apiPublicToken=value;
					if(value){
						log.push('API public token set to: '+value);
					} else {
						log.push('API public token unset');
					}
					break;
				case 'www-return-type':
					inputData[input]=value;
					log.push('Input value of "'+input+'" set to: '+value);
					if(value!='json'){
						apiState.unserialize=false;
						log.push('API result cannot be unserialized, setting unserialize flag to false');
					}
					break;
				case 'www-language':
					apiLanguage=value;
					if(value){
						log.push('API result language set to: '+value);
					} else {
						log.push('API result language uninitialized');
					}
					break;
				case 'www-true-callback':
					apiState.trueCallback=value;
					if(value){
						if(typeof(value)!=='function'){
							log.push('API return true/success callback set to: '+value+'()');
						} else {
							log.push('API return true/success callback uses an anonymous function');
						}
					}
					break;
				case 'www-false-callback':
					apiState.falseCallback=value;
					if(value){
						if(typeof(value)!=='function'){
							log.push('API return false/failure callback set to: '+value+'()');
						} else {
							log.push('API return false/failure callback uses an anonymous function');
						}
					}
					break;
				case 'www-error-callback':
					apiState.errorCallback=value;
					if(value){
						if(typeof(value)!=='function'){
							log.push('API return error callback set to: '+value+'()');
						} else {
							log.push('API return error uses an anonymous function');
						}
					}
					break;
				case 'www-get-limit':
					getLimit=value;
					log.push('Maximum GET string length is set to: '+value);
					break;
				case 'www-reset-log':
					resetLog=value;
					if(value){
						log.push('Log is reset after each new request');
					} else {
						log.push('Log is kept for multiple requests');
					}
					break;
				case 'www-timestamp-duration':
					apiState.timestampDuration=value;
					log.push('API valid timestamp duration set to: '+value);
					break;
				case 'www-output':
					log.push('Ignoring www-output setting, wrapper always requires output to be set to true');
					break;
				default:
					if(value==true){
						value=1;
					} else if(value==false){
						value=0;
					}
					inputData[input]=value;
					log.push('Input value of "'+input+'" set to: '+value);
					break;				
			}
			return true;
		}
		
		/*
		 * This method sets the form ID that is used to fetch input data from. This form can 
		 * be used for uploading files with JavaScript API Wrapper or making it easy to send 
		 * large form-based requests to API over AJAX.
		 * 
		 * @param string $formId form ID value
		 * @return boolean
		 */
		this.setForm=function(formId){
			// Sets the form handler
			apiState.apiSubmitFormId=formId;
			// This forces another content type to stop browsers from pre-formatting the hidden iFrame content
			inputData['www-content-type']='text/html';
			log.push('Form ID set: '+formId);
			return true;
		}
		
		/*
		 * This method unsets the attached form from the API request.
		 *
		 * @return boolean
		 */
		this.clearForm=function(){
			if(inputData['www-content-type']!=null){
				delete inputData['www-content-type'];
			}
			apiState.apiSubmitFormId=false;
			return true;
		}
		
		/*
		 * This function simply deletes current input values
		 *
		 * @param boolean $clearAuth whether to also reset authentication and state data
		 * @return boolean
		 */
		this.clearInput=function(clearAuth){
			if(clearAuth!=null && clearAuth==true){
				// Settings
				apiState.apiProfile=false;
				apiState.apiSecretKey=false;
				apiState.apiToken=false;
				apiState.apiPublicToken=false;
				apiState.apiHashValidation=true;
				apiState.returnHash=false;
				apiState.returnTimestamp=false;
				apiState.timestampDuration=60;
			}
			// Resetting the API state test key
			apiState.apiStateKey=false;
			// Neutralizing state settings
			apiState.unserialize=true;
			apiState.asynchronous=false;
			// Neutralizing callbacks and submit form
			apiState.trueCallback=false;
			apiState.falseCallback=false;
			apiState.errorCallback=false;
			apiState.apiSubmitFormId=false;
			// Input data
			inputData=new Object();
			// Log entry
			log.push('Input data, crypted input and file data is unset');
			return true;
		}
		
	// SENDING REQUEST		
		
		/*
		 * This method executes the API request by building the request based on set input data 
		 * and set forms and sending it to API using XmlHttpRequest() or through hidden iFrame 
		 * forms. It also builds all validations as well as validates the returned response 
		 * from the server and calls callback functions, if they are set. It is possible to 
		 * send input variables directly with a single call by supplying the 'variable' array. 
		 * Form ID can also be sent with the request directly.
		 *
		 * @param object $variables object of keys and values to use as input data
		 * @param string $formId form ID value
		 * @return object/void returns object on only non-async requests
		 */
		this.sendRequest=function(variables,formId){
		
			// If log is assigned to be reset with each new API request
			if(resetLog){
				this.clearLog();
			}
		
			// In case variables have been sent with a single request
			if(variables!=null && typeof(variables)=='object'){
				for(var key in variables){
					// Setting variable through input setter
					this.setInput(key,variables[key]);
				}
			}
			if(formId!=null){
				this.setForm(formId);
			}
			
			// Storing input data
			var thisInputData=clone(inputData);
			
			// Current state settings
			var thisApiState=clone(apiState);
			
			// Assigning authentication options that are sent with the request
			if(thisApiState.apiProfile!=false){
				thisInputData['www-profile']=thisApiState.apiProfile;
			}
			// Assigning the state check key
			if(thisApiState.apiStateKey!=false){
				thisInputData['www-state']=thisApiState.apiStateKey;
			}
			// Assigning return-timestamp flag to request
			if(thisApiState.returnTimestamp==true || thisApiState.returnTimestamp==1){
				thisInputData['www-return-timestamp']=1;
			}
			// Assigning return-hash flag to request
			if(thisApiState.returnHash==true || thisApiState.returnHash==1){
				thisInputData['www-return-hash']=1;
			}
			// Assigning public API token as part of the request
			if(thisApiState.apiPublicToken){
				thisInputData['www-public-token']=thisApiState.apiPublicToken;
			}

			// If language is set
			if(apiLanguage!=null && apiLanguage!=false){
				thisInputData['www-language']=apiLanguage;
			}

			// Clearing input data
			this.clearInput(false);

			// Log entry
			log.push('Starting to build request');
		
			// Correct request requires command to be set
			if(thisInputData['www-command']==null){
				return errorHandler(thisInputData,201,'API command is not set, this is required',thisApiState.errorCallback);
			}
		
			// If default value is set, then it is removed
			if(thisInputData['www-return-type']!=null && thisInputData['www-return-type']=='json'){
				log.push('Since www-return-type is set to default value, it is removed from input data');
				delete thisInputData['www-return-type'];
			}
			
			// If default value is set, then it is removed
			if(thisInputData['www-cache-timeout']!=null && thisInputData['www-cache-timeout']==0){
				log.push('Since www-cache-timeout is set to default value, it is removed from input data');
				delete thisInputData['www-cache-timeout'];
			}
			// If default value is set, then it is removed
			if(thisInputData['www-minify']!=null && thisInputData['www-minify']==0){
				log.push('Since www-minify is set to default value, it is removed from input data');
				delete thisInputData['www-minify'];
			}
			
			// If profile is used, then timestamp will also be sent with the request
			if(thisApiState.apiProfile){
				// Timestamp is required in API requests since it will be used for request validation and replay attack protection
				if(thisInputData['www-timestamp']==null){
					thisInputData['www-timestamp']=Math.floor(new Date().getTime()/1000);
				}
			}
						
			// If API profile and secret key are set, then wrapper assumes that non-public profile is used, thus hash and timestamp have to be included
			if(thisApiState.apiSecretKey){
			
				// Log entry
				log.push('API secret key set, hash authentication will be used');
				
				// If API hash validation is used
				if(thisApiState.apiHashValidation){
				
					// Validation hash is generated based on current serialization option
					if(thisInputData['www-hash']==null){
					
						// Validation requires a different hash
						var validationData=clone(thisInputData);
						// Calculating validation hash
						if(thisApiState.apiToken && thisInputData['www-command']!='www-create-session'){
							thisInputData['www-hash']=validationHash(validationData,thisApiState.apiToken+thisApiState.apiSecretKey);
						} else {
							thisInputData['www-hash']=validationHash(validationData,thisApiState.apiSecretKey);
						}
						
					}

					// Log entry
					if(thisApiState.apiToken){
						log.push('Validation hash created using JSON encoded input data, API token and secret key');
					} else {
						log.push('Validation hash created using JSON encoded input data and secret key');
					}
					
				} else {
				
					// Attaching secret key or token to the request
					if(thisInputData['www-command']=='www-create-session' && thisApiState.apiSecretKey){
						thisInputData['www-secret-key']=thisApiState.apiSecretKey;
						log.push('Validation will be secret key based');
					} else if(thisApiState.apiToken){
						thisInputData['www-token']=thisApiState.apiToken;
						log.push('Validation will be session token based');
					}
					
				}
				
			} else {
		
				// Token-only validation means that token will be sent to the server, but data itself will not be hashed. This works like a cookie.
				if(thisApiState.apiToken){
					// Log entry
					log.push('Using token-only validation');
				} else {
					// Log entry
					log.push('API secret key is not set, hash validation will not be used');
				}
				
			}
			
			// MAKING A REQUEST
				
				// Command is made slightly differently depending on whether files are to be uploaded or not
				if(thisApiState.apiSubmitFormId==false){
				
					// Default method
					var method='GET';
				
					// Getting input variables
					var requestData=buildRequestData(thisInputData);
				
					// Creating request handler
					var XMLHttp=new XMLHttpRequest();
				
					// POST request is made if the URL is longer than 2048 bytes (2KB).
					// While servers can easily handle 8KB of data, servers are recommended to be vary if the GET request is longer than 2KB
					if((apiAddress+'?'+requestData)>getLimit){
						// Log entries
						log.push('More than '+getLimit+' bytes would be sent, POST request will be used');
						// Request header and method for POST
						XMLHttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
						method='POST';
					}
				
					// Separate functionality for synchronous and asynchronous requests
					if(thisApiState.asynchronous){
						
						// Log entry
						log.push('Making '+method+' request to URL: '+apiAddress);
						
						// AJAX states
						XMLHttp.onreadystatechange=function(){
							if(XMLHttp.readyState===4){
								// Result based on status
								if(XMLHttp.status===200 || XMLHttp.status===304){
									log.push('Result of the request: '+XMLHttp.responseText);
									return parseResult(XMLHttp.responseText,thisInputData,thisApiState);
								} else {
									if(method=='POST'){
										return errorHandler(thisInputData,205,'POST request failed: '+XMLHttp.statusText,thisApiState.errorCallback);
									} else {
										return errorHandler(thisInputData,204,'GET request failed: '+XMLHttp.statusText,thisApiState.errorCallback);
									}
								}  
							}  
						};
						
						// Sending the request
						if(method=='POST'){
							XMLHttp.open(method,apiAddress,true);
							XMLHttp.send(requestData);
						} else {
							XMLHttp.open(method,apiAddress+'?'+requestData,true);
							XMLHttp.send(null);
						}
						
					} else {
					
						// Log entry
						log.push('Making '+method+' request to URL: '+apiAddress);
						
						// Sending the request
						if(method=='POST'){
							XMLHttp.open(method,apiAddress,false);
							XMLHttp.send(requestData);
						} else {
							XMLHttp.open(method,apiAddress+'?'+requestData,false);
							XMLHttp.send(null);
						}
						
						// Result based on status
						if(XMLHttp.status===200 || XMLHttp.status===304){  
							log.push('Result of the request: '+XMLHttp.responseText);
							return parseResult(XMLHttp.responseText,thisInputData,thisApiState);
						} else {
							if(method=='POST'){
								return errorHandler(thisInputData,205,'POST request failed: '+XMLHttp.statusText,thisApiState.errorCallback);
							} else {
								return errorHandler(thisInputData,204,'GET request failed: '+XMLHttp.statusText,thisApiState.errorCallback);
							}
						}
						
					}
					
				} else {
				
					// Getting the hidden form
					var apiSubmitForm=document.getElementById(thisApiState.apiSubmitFormId);
					
					if(apiSubmitForm==null){
						return errorHandler(thisInputData,215,'Form not found: '+thisApiState.apiSubmitFormId,thisApiState.errorCallback);
					}
				
					// Hidden iFrame
					thisApiState.hiddenWindowCounter++;
					var hiddenWindow=document.createElement('iframe');
					var hiddenWindowName='WWW_API_Wrapper_Hidden_iFrame_'+thisApiState.hiddenWindowCounter;
					hiddenWindow.id=hiddenWindowName;
					hiddenWindow.name=hiddenWindowName;
					hiddenWindow.style.display='none';
					apiSubmitForm.appendChild(hiddenWindow);
					
					//Old parameters
                    var old_formAction=apiSubmitForm.action;
                    var old_formTarget=apiSubmitForm.target;
                    var old_formMethod=apiSubmitForm.method;
					var old_formEnctype=apiSubmitForm.enctype;
					
					// Preparing form submission
					apiSubmitForm.method='POST';
					apiSubmitForm.action=apiAddress;
					apiSubmitForm.setAttribute('enctype','multipart/form-data'); // Done differently because of IE8
					apiSubmitForm.setAttribute('encoding','multipart/form-data'); // IE6 wants this
					apiSubmitForm.target=hiddenWindowName;
					
					// Input data
					var counter=0;
					var hiddenFields=new Object();
					for(var node in thisInputData){
						counter++;
						hiddenFields[counter]=document.createElement('input');
						hiddenFields[counter].id=('www_hidden_form_data_'+counter);
						hiddenFields[counter].name=node;
						hiddenFields[counter].value=thisInputData[node];
						hiddenFields[counter].type='hidden';
						apiSubmitForm.appendChild(hiddenFields[counter]);
						// Log entry
						log.push('Attaching variable to form request: '+node+'='+thisInputData[node]);
					}
					
					// This is on-load function for iFrame
					var onLoad=function(){
						// Resetting the form data
						if(old_formMethod!=null){
							apiSubmitForm.method=old_formMethod;
						} else {
							apiSubmitForm.method='';
						}
						if(old_formAction!=null){
							apiSubmitForm.action=old_formAction;
						} else {
							apiSubmitForm.action='';
						}
						if(old_formEnctype!=null){
							apiSubmitForm.setAttribute('enctype',old_formEnctype);
							apiSubmitForm.setAttribute('encoding',old_formEnctype);
						} else {
							apiSubmitForm.setAttribute('enctype','');
							apiSubmitForm.setAttribute('encoding','');
						}
						if(old_formTarget!=null){
							apiSubmitForm.target=old_formTarget;
						} else {
							apiSubmitForm.target='';
						}
						// Removing created elements
						for(var i=1;i<=counter;i++){
							if(hiddenFields[i]!=null){
								hiddenFields[i].parentNode.removeChild(hiddenFields[i]);
							}
						}
						// Parsing the result
						var resultData=hiddenWindow.contentWindow.document.body.innerHTML;
						// Log entry
						log.push('Result of the request: '+resultData);
						resultData=parseResult(resultData,thisInputData,thisApiState);
						// Removing hidden iFrame
						setTimeout(function(){apiSubmitForm.removeChild(hiddenWindow);},100);
						return resultData;
					}
					
					// Hidden iFrame onload function
					// Two versions, one IE compatible, another one not
					if(hiddenWindow.attachEvent==null){
						hiddenWindow.onload=onLoad;
					} else {
						hiddenWindow.attachEvent('onload',onLoad);
					}

					// Log entry
					log.push('Making POST request to URL: '+apiAddress);
					
					// Submitting form
					apiSubmitForm.submit();	
					
				}
			
		}
				
		/*
		 * JavaScript API Wrapper handles asynchronous requests and all of request callbacks, 
		 * which allows to make multiple API requests at the same time or in sequence. This 
		 * method validates the response data, if validation is requested and executes set 
		 * callbacks with the results. 'resultData' is the response from the API call, 
		 * 'thisInputData' is the original input sent to the request and 'thisApiState' is 
		 * the API Wrapper state at the time of the request.
		 *
		 * @param string $resultData result string from response
		 * @param object $thisInputData data that was sent as input
		 * @param object $thisApiState api state for this request
		 * @return object/string response data from request depending on settings
		 */
		var parseResult=function(resultData,thisInputData,thisApiState){
		
			// Returning the result directly if the result is not intended to be unserialized
			if(!thisApiState.unserialize){
			
				// Log entry for returning data
				log.push('Returning result without unserializing');
				// Data is simply returned if serialization was not requested
				return resultData;
				
			} else {
				
				// PARSING REQUEST RESULT
			
					// If unserialize command was set and the data type was JSON or serialized array, then it is returned as serialized
					if(thisInputData['www-return-type']==null || thisInputData['www-return-type']=='json'){
						// JSON support is required
						if(typeof(JSON)!=='undefined'){
							resultData=JSON.parse(resultData);
						} else if(typeof(jQuery)!=='undefined'){
							// Attempting to unserialize with jQuery
							resultData=jQuery.parseJSON(resultData);
						} else {
							// Could not unserialize
							return errorHandler(thisInputData,207,'Cannot unserialize returned data',thisApiState.errorCallback);
						}
						log.push('Returning JSON object');
					} else if(thisApiState.unserialize){
						// Every other unserialization attempt fails
						return errorHandler(thisInputData,207,'Cannot unserialize returned data',thisApiState.errorCallback);
					}
					
					// If error was detected
					if(resultData['www-response-code']!=null && resultData['www-response-code']<400){
						if(resultData['www-message']!=null){
							return errorHandler(thisInputData,resultData['www-response-code'],resultData['www-message'],thisApiState.errorCallback);
						} else {
							return errorHandler(thisInputData,resultData['www-response-code'],'',thisApiState.errorCallback);
						}
					}
				
				// RESULT VALIDATION
				
					// Result validation only applies to non-public profiles
					if(thisApiState.apiProfile && (thisApiState.returnTimestamp || thisApiState.returnHash)){
						
						// If it was requested that validation timestamp is returned
						if(thisApiState.returnTimestamp){
							if(resultData['www-timestamp']!=null){
								// Making sure that the returned result is within accepted time limit
								if(((Math.floor(new Date().getTime()/1000))-thisApiState.timestampDuration)>resultData['www-timestamp']){
									return errorHandler(thisInputData,209,'Validation timestamp is too old',thisApiState.errorCallback);
								}
							} else {
								return errorHandler(thisInputData,208,'Validation data missing: Timestamp was not returned',thisApiState.errorCallback);
							}
						}
						
						// If it was requested that validation timestamp is returned
						if(thisApiState.apiStateKey){
							if(resultData['www-state']==null || resultData['www-state']!=thisApiState.apiStateKey){
								return errorHandler(thisInputData,210,'Validation state keys do not match',thisApiState.errorCallback);
							}
						}
						
						// If it was requested that validation hash is returned
						if(thisApiState.returnHash){
							// Hash and timestamp have to be defined in response
							if(resultData['www-hash']!=null){
							
								// Assigning returned array to hash validation array
								var validationData=clone(resultData);
								// Hash itself is removed from validation
								delete validationData['www-hash'];
								
								// Validation depends on whether session creation or destruction commands were called
								if(thisInputData['www-command']=='www-create-session'){
									var resultHash=validationHash(validationData,thisApiState.apiSecretKey);
								} else {
									var resultHash=validationHash(validationData,thisApiState.apiToken+thisApiState.apiSecretKey);
								}
								
								// If sent hash is the same as calculated hash
								if(resultHash==resultData['www-hash']){
									log.push('Hash validation successful');
								} else {
									return errorHandler(thisInputData,210,'Hash validation failed',thisApiState.errorCallback);
								}
								
							} else {
								return errorHandler(thisInputData,208,'Validation data missing: Hash was not returned',thisApiState.errorCallback);
							}
						}
					
					}
								
				// Resetting the error variables
				responseCode=false;
				errorMessage=false;
				
				// If this command was to create a token
				if(thisInputData['www-command']=='www-create-session' && resultData['www-token']!=null){
					apiState.apiToken=resultData['www-token'];
					log.push('Session token was found in reply, API session token set to: '+resultData['www-token']);
				}					
				
				// If callback function is set
				if(thisApiState.trueCallback && resultData['www-response-code']!=null && resultData['www-response-code']>=500){
					// If the callback is a function name and not a function itself
					if(typeof(thisApiState.trueCallback)!=='function'){
						// Calling user function
						var thisCallback=this.window[thisApiState.trueCallback];
						if(typeof(thisCallback)==='function'){
							log.push('Sending failure data to callback: '+thisApiState.trueCallback+'()');
							// Callback execution
							return thisCallback.call(this,resultData);
						} else {
							return errorHandler(thisInputData,216,'Callback method not found',thisApiState.errorCallback);
						}
					} else {
						// Returning data from callback
						thisApiState.trueCallback(resultData);
					}
				} else if(thisApiState.falseCallback && resultData['www-response-code']!=null && resultData['www-response-code']<500){
					// If the callback is a function name and not a function itself
					if(typeof(thisApiState.falseCallback)!=='function'){
						// Calling user function
						var thisCallback=this.window[thisApiState.falseCallback];
						if(typeof(thisCallback)==='function'){
							log.push('Sending failure data to callback: '+thisApiState.falseCallback+'()');
							// Callback execution
							return thisCallback.call(this,resultData);
						} else {
							return errorHandler(thisInputData,216,'Callback method not found',thisApiState.errorCallback);
						}
					} else {
						// Returning data from callback
						thisApiState.falseCallback(resultData);
					}
				} else {
					// Returning request result
					return resultData;
				}	
			
			}			

		}
		
	// REQUIRED FUNCTIONS
	
		/*
		 * This method is simply meant for returning a result if there was an error in the sent request
		 *
		 * @param object $thisInputData input data sent to the request
		 * @param string $thisResponseCode response code value
		 * @param string $thisErrorMessage returned error message text
		 * @param string/function $thisErrorCallback anonymous function or function name to be called
		 * @return boolean/mixed depending on whether callback function is called or not
		 */
		var errorHandler=function(thisInputData,thisResponseCode,thisErrorMessage,thisErrorCallback){
			// Assigning error details to object state
			responseCode=thisResponseCode;
			errorMessage=thisErrorMessage;
			log.push(errorMessage);
			// If failure callback has been defined
			if(thisErrorCallback){
				// If the callback is a function name and not a function itself
				if(typeof(thisErrorCallback)!=='function'){
					// Looking for function of that name
					var thisCallback=this.window[thisErrorCallback];
					if(typeof(thisCallback)==='function'){
						log.push('Sending failure data to callback: '+thisErrorCallback+'()');
						// Callback execution
						return thisCallback.call(this,{'www-input':thisInputData,'www-response-code':responseCode,'www-message':errorMessage});
					} else {
						responseCode=217;
						errorMessage='Callback method not found: '+thisErrorCallback+'()';
						log.push(errorMessage);
						return false;
					}
				} else {
					// Returning data from callback
					thisErrorCallback(thisInputData);
				}
			} else {
				return false;
			}
		}
		
		/*
		 * This helper method is used to clone one JavaScript object to another 'object' is 
		 * the JavaScript object to be converted.
		 *
		 * @param object $object object to be cloned
		 * @return object
		 */
		var clone=function(object){
			if(object==null || typeof(object)!=='object'){
				return object;
			}
			var tmp=object.constructor();
			for(var key in object){
				tmp[key]=clone(object[key]);
			}
			return tmp;
		}
		
		/*
		 * This method is used to build an input data validation hash string for authenticating 
		 * API requests. The entire input array of 'validationData' is serialized and hashed 
		 * with SHA-1 and a salt string set in 'postFix'. This is used for all API requests 
		 * where input has to be validated.
		 * 
		 * @param object $validationData data to be used for hash generation
		 * @param string $postFix will be appended prior to hash being generated
		 * @return string
		 */
		var validationHash=function(validationData,postFix){
			// Sorting and encoding the output data
			validationData=ksortArray(validationData);
			// Returning validation hash		
			return sha1(buildRequestData(validationData)+postFix);
		}
		
		/*
		 * This is a helper function used by validationHash() function to serialize an array 
		 * recursively. It applies ksort() to main method as well as to all sub-arrays. 'data' 
		 * is the array or object to be sorted.
		 *
		 * @param object/mixed $data variable to be sorted
		 * @return mixed
		 */
		var ksortArray=function(data){
			// Method is based on the current data type
			if(typeof(data)==='array' || typeof(data)==='object'){
				// Sorting the current array
				data=ksort(data);
				// Sorting every sub-array, if it is one
				for(var i in data){
					data[i]=ksortArray(data[i]);
				}
			}
			return data;
		}
		
		/*
		 * This is a method that is similar to PHP http_build_query() function. It builds a 
		 * GET request string of input variables set in 'data'.
		 *
		 * @param object $data object to build request data string from
		 * @return string
		 */
		var buildRequestData=function(data){
			var variables=new Array();
			for(var i in data){
				// Using the helper function
				var query=subRequestData(i,data[i]);
				if(query!=''){
					variables.push(query);
				}
			}
			return variables.join('&');
		}
		
		/* 
		 * This is a helper function for buildRequestData() method, it converts between 
		 * different ways data is represented in a GET request string.
		 * 
		 * @param string $key key value
		 * @param mixed $value variable value
		 * @return string 
		 */
		var subRequestData=function(key,value){
			var variables=new Array();
			if(value!=null){
				// Converting true/false to numeric
				if(value===true){
					value='1';
				} else if(value===false){
					value='0';
				}
				// Object will be parsed through subRequestData recursively
				if(typeof(value)==='object'){
					for(var i in value){
						if(value[i]!=null){
							variables.push(subRequestData(key+'['+i+']',value[i]));
						}
					}
					return variables.join('&');
				} else {
					return encodeValue(key)+'='+encodeValue(value);
				}
			} else {
				return '';
			}
		};
		
		/*
		 * This helper method converts certain characters into their suitable form that would 
		 * be accepted and same as in PHP. This is a modified version of encodeURIComponent() 
		 * function. 'data' is the string to be converted.
		 * 
		 * @param string $data string to encode
		 * @return string
		 */
		var encodeValue=function(data){
			data=encodeURIComponent(data);
			data=data.replace('\'','%27');
			data=data.replace('!','%21');
			data=data.replace('(','%28');
			data=data.replace(')','%29');
			data=data.replace('*','%2A');
			data=data.replace('%20','+');
			return data;
		}
	
		/*
		 * This is a JavaScript method that works similarly to PHP's ksort() function and 
		 * applies to JavaScript objects. 'object' is the object to be sorted.
		 * 
		 * @param object $object object to sort by keys
		 * @return object
		 */
		var ksort=function(object){
			// Result will be gathered here
			var keys=new Array();
			var sorted=new Object();
			// Sorted keys gathered in sorting array
			for(var i in object){
				keys.push(i);
			}
			// Sorting the keys
			keys.sort();
			// Building a new object based on sorted keys
			for(var i in keys){
				sorted[keys[i]]=object[keys[i]];
			}
			return sorted;
		}
	
		/*
		 * This is a JavaScript equivalent of PHP's sha1() function. It calculates a hash 
		 * string from 'msg' string.
		 * 
		 * @author http://www.webtoolkit.info/javascript-sha1.html
		 * @param string $msg string to hash
		 * @return string
		 */
		var sha1=function(msg){
			function rotate_left(n,s) {
				var t4 = ( n<<s ) | (n>>>(32-s));
				return t4;
			}
			function lsb_hex(val) {
				var str="";
				var i;
				var vh;
				var vl;
				for( i=0; i<=6; i+=2 ) {
					vh = (val>>>(i*4+4))&0x0f;
					vl = (val>>>(i*4))&0x0f;
					str += vh.toString(16) + vl.toString(16);
				}
				return str;
			}
			function cvt_hex(val) {
				var str="";
				var i;
				var v;
				for( i=7; i>=0; i-- ) {
					v = (val>>>(i*4))&0x0f;
					str += v.toString(16);
				}
				return str;
			}
			function Utf8Encode(string) {
				string = string.replace(/\r\n/g,"\n");
				var utftext = "";
				for (var n = 0; n < string.length; n++) {
					var c = string.charCodeAt(n);
					if (c < 128) {
						utftext += String.fromCharCode(c);
					}
					else if((c > 127) && (c < 2048)) {
						utftext += String.fromCharCode((c >> 6) | 192);
						utftext += String.fromCharCode((c & 63) | 128);
					}
					else {
						utftext += String.fromCharCode((c >> 12) | 224);
						utftext += String.fromCharCode(((c >> 6) & 63) | 128);
						utftext += String.fromCharCode((c & 63) | 128);
					}
				}
				return utftext;
			}
			var blockstart;
			var i, j;
			var W = new Array(80);
			var H0 = 0x67452301;
			var H1 = 0xEFCDAB89;
			var H2 = 0x98BADCFE;
			var H3 = 0x10325476;
			var H4 = 0xC3D2E1F0;
			var A, B, C, D, E;
			var temp;
			msg = Utf8Encode(msg);
			var msg_len = msg.length;
			var word_array = new Array();
			for( i=0; i<msg_len-3; i+=4 ) {
				j = msg.charCodeAt(i)<<24 | msg.charCodeAt(i+1)<<16 |
				msg.charCodeAt(i+2)<<8 | msg.charCodeAt(i+3);
				word_array.push( j );
			}
			switch( msg_len % 4 ) {
				case 0:
					i = 0x080000000;
				break;
				case 1:
					i = msg.charCodeAt(msg_len-1)<<24 | 0x0800000;
				break;
				case 2:
					i = msg.charCodeAt(msg_len-2)<<24 | msg.charCodeAt(msg_len-1)<<16 | 0x08000;
				break;
				case 3:
					i = msg.charCodeAt(msg_len-3)<<24 | msg.charCodeAt(msg_len-2)<<16 | msg.charCodeAt(msg_len-1)<<8	| 0x80;
				break;
			}
			word_array.push( i );
			while( (word_array.length % 16) != 14 ) word_array.push( 0 );
			word_array.push(msg_len>>>29);
			word_array.push((msg_len<<3)&0x0ffffffff);
			for ( blockstart=0; blockstart<word_array.length; blockstart+=16 ){
				for( i=0; i<16; i++ ) W[i] = word_array[blockstart+i];
				for( i=16; i<=79; i++ ) W[i] = rotate_left(W[i-3] ^ W[i-8] ^ W[i-14] ^ W[i-16], 1);
				A = H0;
				B = H1;
				C = H2;
				D = H3;
				E = H4;
				for( i= 0; i<=19; i++ ) {
					temp = (rotate_left(A,5) + ((B&C) | (~B&D)) + E + W[i] + 0x5A827999) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B,30);
					B = A;
					A = temp;
				}
				for( i=20; i<=39; i++ ) {
					temp = (rotate_left(A,5) + (B ^ C ^ D) + E + W[i] + 0x6ED9EBA1) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B,30);
					B = A;
					A = temp;
				}
				for( i=40; i<=59; i++ ) {
					temp = (rotate_left(A,5) + ((B&C) | (B&D) | (C&D)) + E + W[i] + 0x8F1BBCDC) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B,30);
					B = A;
					A = temp;
				}
				for( i=60; i<=79; i++ ) {
					temp = (rotate_left(A,5) + (B ^ C ^ D) + E + W[i] + 0xCA62C1D6) & 0x0ffffffff;
					E = D;
					D = C;
					C = rotate_left(B,30);
					B = A;
					A = temp;
				}
				H0 = (H0 + A) & 0x0ffffffff;
				H1 = (H1 + B) & 0x0ffffffff;
				H2 = (H2 + C) & 0x0ffffffff;
				H3 = (H3 + D) & 0x0ffffffff;
				H4 = (H4 + E) & 0x0ffffffff;
			}
			var temp = cvt_hex(H0) + cvt_hex(H1) + cvt_hex(H2) + cvt_hex(H3) + cvt_hex(H4);
			return temp.toLowerCase();
		}
		
}
