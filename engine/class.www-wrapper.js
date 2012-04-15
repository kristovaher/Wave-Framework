	
/*
WWW Framework
WWW API connection wrapper class for JavaScript

This class should be used when you wish to communicate with WWW Framework that is set up in another 
server. This class is independent from WWW Framework itself and can be used by other servers alone. 
Differently from PHP API Wrapper, JavaScript API Wrapper does not support sending and receiving 
encrypted data.

Author and support: Kristo Vaher - kristo@waher.net
*/

function WWW_Wrapper(address){

	// HTTP address of WWW-Framework-based API
	var apiAddress=address;
	
	// This is current API state
	var apiState={
		apiProfile:false,
		apiSecretKey:false,
		apiToken:false,
		returnHash:false,
		returnTimestamp:false,
		successCallback:false,
		failureCallback:false,
		timestampDuration:60,
		hiddenWindowCounter:0,
		apiSubmitFormId:false,
		asynchronous:true,
		unserialize:true
	}
	
	// Information about last error
	var errorMessage=false;
	var errorCode=false;
	
	// Input data
	var inputData=new Object();
	
	// Log
	var log=new Array();
	
	// User agent
	var userAgent='WWWFramework/2.0.0 (JavaScript)';
		
	// Log entry
	log.push('WWW API Wrapper object created with API address: '+address);
	
	// SETTINGS
		
		// This function simply returns current log
		// * implode - String to implode the log with
		// Function returns current log as an array
		this.returnLog=function(implode){
			log.push('Returning log');
			// Imploding, if requested
			if(implode==null){
				return log;
			} else {
				return log.join(implode);
			}
		}
		
		// This function simply clears current log
		// Function returns true
		this.clearLog=function(){
			log=new Array();
			return true;
		}
		
	// INPUT
	
		// This populates input array either with an array of input or a key and a value
		// * input - Can be an array or a key value of input
		// * value - If input value is not an array, then this is what the input key will get as a value
		// Sets the input value
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
		
		// This private function is used to seek out various private www-* settings that are not actually sent in request to API
		// * input - Input data key
		// * value - Input data value
		// Returns true after setting the data
		var inputSetter=function(input,value){
			switch(input){
				case 'www-api':
					apiState.apiAddress=value;
					log.push('API address changed to: '+value);
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
					if(value){
						apiState.returnHash=value;
						log.push('API request will require hash validation');
					}
					break;
				case 'www-return-timestamp':
					if(value){
						apiState.returnTimestamp=value;
						log.push('API request will require timestamp validation');
					}
					break;
				case 'www-success-callback':
					if(value){
						apiState.successCallback=value;
						log.push('API return success callback set to '+value+'()');
					}
					break;
				case 'www-failure-callback':
					if(value){
						apiState.failureCallback=value;
						log.push('API return failure callback set to '+value+'()');
					}
					break;
				case 'www-timestamp-duration':
					apiState.timestampDuration=$value;
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
		
		// This sets file names and locations to be uploaded with the cURL request
		// * form - ID of the form to be used
		// * location - If input value is not an array, then this is what the input file address is
		// Returns true
		this.setForm=function(formId){
			// Sets the form handler
			apiState.apiSubmitFormId=formId;
			// This forces another content type to stop browsers from pre-formatting the hidden iFrame content
			inputData['www-content-type']='text/html';
			log.push('Form ID set: '+formId);
			return true;
		}
		
		// This function disables the use of form
		// Simply unsets the form ID
		this.clearForm=function(){
			if(inputData['www-content-type']!=null){
				delete inputData['www-content-type'];
			}
			apiState.apiSubmitFormId=false;
		}
		
		// This function simply deletes current input values
		// * clearAuth - True or false flag whether to also reset authentication and state data
		// Returns true
		this.clearInput=function(clearAuth){
			if(clearAuth!=null && clearAuth==true){
				// Settings
				apiState.apiProfile=false;
				apiState.apiSecretKey=false;
				apiState.apiToken=false;
				apiState.returnHash=false;
				apiState.returnTimestamp=false;
				apiState.timestampDuration=10;
			}
			// Neutralizing state settings
			apiState.unserialize=true;
			apiState.asynchronous=true;
			// Neutralizing callbacks and submit form
			apiState.successCallback=false;
			apiState.failureCallback=false;
			apiState.apiSubmitFormId=false;
			// Input data
			inputData=new Object();
			// Log entry
			log.push('Input data, crypted input and file data is unset');
			return true;
		}
		
	// SENDING REQUEST
		
		// This is the main function for making the request
		// This method builds a request string and then makes a request to API and attempts to fetch and parse the returned result
		// Executes callback function with returned result, if no problems are encountered
		this.sendRequest=function(){
			
			// Storing input data
			var thisInputData=clone(inputData);
			
			// Current state settings
			var thisApiState=clone(apiState);
			
			// Assigning authentication options that are sent with the request
			if(thisApiState.apiProfile!=false){
				thisInputData['www-profile']=thisApiState.apiProfile;
			}
			// Assigning return-timestamp flag to request
			if(thisApiState.returnTimestamp==true || thisApiState.returnTimestamp==1){
				thisInputData['www-return-timestamp']=1;
			}
			// Assigning return-timestamp flag to request
			if(thisApiState.returnHash==true || thisApiState.returnHash==1){
				thisInputData['www-return-hash']=1;
			}
					
			// Clearing input data
			this.clearInput();

			// Log entry
			log.push('Starting to build request');
		
			// Correct request requires command to be set
			if(thisInputData['www-command']==null){
				return failureHandler(thisInputData,201,'API command is not set, this is required',thisApiState.failureCallback);
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
			
				// Building the request URL
				var requestURL=apiAddress;
				
				// Command is made slightly differently depending on whether files are to be uploaded or not
				if(thisApiState.apiSubmitFormId==false){
				
					// Default method
					var method='GET';
				
					// Getting input variables
					var requestData=buildRequestData(thisInputData);
				
					// Creating request handler
					XMLHttp=new XMLHttpRequest();
					
					// Request data information
					log.push('Data sent with request: '+requestData);
				
					// POST request is made if the URL is longer than 2048 bytes (2KB).
					// While servers can easily handle 8KB of data, servers are recommended to be vary if the GET request is longer than 2KB
					if((requestURL+'?'+requestData)>2048){
						// Log entries
						log.push('More than 2048 bytes sent, POST request will be used');
						// Request header and method for POST
						XMLHttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
						method='POST';
					}
				
					// Separate functionality for synchronous and asynchronous requests
					if(thisApiState.asynchronous){
						
						// Log entry
						log.push('Making '+method+' request to URL: '+requestURL);
						
						// AJAX states
						XMLHttp.onreadystatechange=function(oEvent){
							if(XMLHttp.readyState===4){
								// Result based on status
								if(XMLHttp.status===200){
									log.push('Result of the request: '+XMLHttp.responseText);
									return parseResult(XMLHttp.responseText,thisInputData,thisApiState);
								} else if(XMLHttp.status===304){
									return failureHandler(thisInputData,214,'Not modified',thisApiState.failureCallback);
								} else {
									if(method=='POST'){
										return failureHandler(thisInputData,205,'POST request failed: '+XMLHttp.statusText,thisApiState.failureCallback);
									} else {
										return failureHandler(thisInputData,204,'GET request failed: '+XMLHttp.statusText,thisApiState.failureCallback);
									}
								}  
							}  
						};
						
						// Sending the request
						if(method=='POST'){
							XMLHttp.open(method,requestURL,true);
							XMLHttp.send(requestData);
						} else {
							XMLHttp.open(method,requestURL+'?'+requestData,true);
							XMLHttp.send(null);
						}
						
					} else {
					
						// Log entry
						log.push('Making '+method+' request to URL: '+requestURL);
						
						// Sending the request
						if(method=='POST'){
							XMLHttp.open(method,requestURL,false);
							XMLHttp.send(requestData);
						} else {
							XMLHttp.open(method,requestURL+'?'+requestData,false);
							XMLHttp.send(null);
						}
						
						// Result based on status
						if(XMLHttp.status===200){  
							log.push('Result of the request: '+XMLHttp.responseText);
							return parseResult(XMLHttp.responseText,thisInputData,thisApiState);
						} else if(XMLHttp.status===304){
							return failureHandler(thisInputData,214,'Not modified',thisApiState.failureCallback);
						} else {
							if(method=='POST'){
								return failureHandler(thisInputData,205,'POST request failed: '+XMLHttp.statusText,thisApiState.failureCallback);
							} else {
								return failureHandler(thisInputData,204,'GET request failed: '+XMLHttp.statusText,thisApiState.failureCallback);
							}
						}
						
					}
					
				} else {
				
					// Getting the hidden form
					var apiSubmitForm=document.getElementById(thisApiState.apiSubmitFormId);
					
					if(apiSubmitForm==null){
						return failureHandler(thisInputData,215,'Form not found: '.thisApiState.apiSubmitFormId,thisApiState.failureCallback);
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
					old_formAction=apiSubmitForm.action;
					old_formTarget=apiSubmitForm.target;
					old_formMethod=apiSubmitForm.method;
					old_formEnctype=apiSubmitForm.enctype;
					
					// Preparing form submission
					apiSubmitForm.method='POST';
					apiSubmitForm.action=apiAddress;
					apiSubmitForm.setAttribute('enctype','multipart/form-data'); // Done differently because of IE8
					apiSubmitForm.setAttribute('encoding','multipart/form-data'); // IE6 wants this
					apiSubmitForm.target=hiddenWindowName;
					
					// Input data
					var counter=0;
					var hiddenFields=new Object();
					for(node in thisInputData){
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
						for(i=1;i<=counter;i++){
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
		
		// The return parsing method has to be separate, since JavaScript allows to make calls to API asynchronously
		// * result - Result from the request
		// * thisInputData - Input data sent to request
		// * thisApiState - API state at the time of the request
		// Returns result
		var parseResult=function(resultData,thisInputData,thisApiState){
				
			// PARSING REQUEST RESULT
				
				// If unserialize command was set and the data type was JSON or serialized array, then it is returned as serialized
				if((thisInputData['www-return-type']==null || thisInputData['www-return-type']=='json') && thisApiState.unserialize){
					// JSON support is required
					resultData=JSON.parse(resultData);
					log.push('Returning JSON object');
				} else if(thisApiState.unserialize){
					// Every other unserialization attempt fails
					return failureHandler(thisInputData,207,'Cannot unserialize returned data',thisApiState.failureCallback);
				} else {
					// Data is simply returned if serialization was not requested
					log.push('Returning result');
				}
				
				// Return specific actions
				if(thisApiState.unserialize){
					// If error was detected
					if(resultData['www-error']!=null){
						return failureHandler(thisInputData,resultData['www-error-code'],resultData['www-error'],thisApiState['failureCallback']);
					}
				}
				
			// RESULT VALIDATION
			
				// Result validation only applies to non-public profiles
				if(thisApiState.apiProfile && (thisApiState.returnTimestamp || thisApiState.returnHash)){
				
					// Only unserialized data can be validated
					if(thisApiState.unserialize){
					
						// If it was requested that validation timestamp is returned
						if(thisApiState.returnTimestamp){
							if(resultData['www-timestamp']!=null){
								// Making sure that the returned result is within accepted time limit
								if(((Math.floor(new Date().getTime()/1000))-thisApiState.timestampDuration)>resultData['www-timestamp']){
									return failureHandler(thisInputData,210,'Validation timestamp is too old',thisApiState.failureCallback);
								}
							} else {
								return failureHandler(thisInputData,209,'Validation data missing: Timestamp was not returned',thisApiState.failureCallback);
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
									return failureHandler(thisInputData,211,'Hash validation failed',thisApiState.failureCallback);
								}
								
							} else {
								return failureHandler(thisInputData,209,'Validation data missing: Hash was not returned',thisApiState.failureCallback);
							}
						}
					
					} else {
						return failureHandler(thisInputData,208,'Cannot validate hash: Unserialization not possible',thisApiState.failureCallback);
					}
				
				}
							
			// Resetting the error variables
			errorCode=false;
			errorMessage=false;
			
			// Return specific actions
			if(thisApiState.unserialize){
				// If this command was to create a token
				if(thisInputData['www-command']=='www-create-session' && resultData['www-token']!=null){
					apiState.apiToken=resultData['www-token'];
					log.push('Session token was found in reply, API session token set to: '+resultData['www-token']);
				}				
			}
			
			// If callback function is set
			if(thisApiState.successCallback){
				// Calling user function
				thisCallback=this.window[thisApiState.successCallback];
				if(typeof(thisCallback)==='function'){
					log.push('Sending failure data to callback: '+thisApiState['failureCallback']+'()');
					// Callback execution
					return thisCallback.call(this,resultData);
				} else {
					return failureHandler(thisInputData,216,'Callback method not found',thisApiState['failureCallback']);
				}
			} else {
				// Returning request result
				return resultData;
			}				

		}
		
	// REQUIRED FUNCTIONS
	
		// This method is simply meant for returning a result if there was an error in the sent request
		// * thisInputData - Data sent to request
		// * thisErrorCode - Code number to be set as an error
		// * thisErrorMessage - Clear text error message
		// * thisFailureCallback - Callback function to call with the error message
		// Returns either false or the result of callback function
		var failureHandler=function(thisInputData,thisErrorCode,thisErrorMessage,thisFailureCallback){
			// Assigning error details to object state
			errorCode=thisErrorCode;
			errorMessage=thisErrorMessage;
			log.push(errorMessage);
			// If failure callback has been defined
			if(thisFailureCallback){
				// Looking for function of that name
				thisCallback=this.window[thisFailureCallback];
				if(typeof(thisCallback)==='function'){
					log.push('Sending failure data to callback: '+thisFailureCallback+'()');
					// Callback execution
					var result={'www-input':thisInputData,'www-error-code':errorCode,'www-error':errorMessage};
					return thisCallback.call(this,result);
				} else {
					errorCode=216;
					errorMessage='Callback method not found: '+thisFailureCallback+'()';
					log.push(errorMessage);
					return false;
				}
			} else {
				return false;
			}
		}
	
		// This function clones one JavaScript object to another
		// * object - Object to be cloned
		// Returns cloned object
		var clone=function(object){
			if(object==null || typeof(object)!='object'){
				return object;
			}
			var tmp=object.constructor();
			for(var key in object){
				tmp[key]=clone(object[key]);
			}
			return tmp;
		}
		
		// Calculates validation hash
		// * apiResult - Data array to calculate validation hash from
		// * postFix - Part of the validation that is only known to sender and recipient
		// Returns the hash
		var validationHash=function(validationData,postFix){
			// Sorting and encoding the output data
			validationData=ksortArray(validationData);
			// Returning validation hash		
			return sha1(buildRequestData(validationData)+postFix);
		}
		
		// This function applies key-based sorting recursively to an array of arrays
		// * array - Array to be sorted
		// Returns sorted array
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
		
		// This function builds a request data string from input data
		// * value - input data object to be converted
		// Returns the data string
		var buildRequestData=function(value){
			var variables=new Array();
			for(var i in value){
				// Using the helper function
				var query=subRequestData(value[i],i);
				if(query!=''){
					variables.push(query);
				}
			}
			return variables.join('&');
		}
		
		// This is a helper function for request builder
		// * value - Value
		// * key - Key
		// Returns snippet for request string
		var subRequestData=function(value,key){
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
							variables.push(subRequestData(value[i],key+'['+i+']'));
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
		
		// This function is a modified version of encodeURIComponent
		// It adds certain conversions for PHP-sake
		// * data - String to encode
		// Returns encoded string
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
	
		// This function sorts object based on its keys
		// * object - Object for input
		// Returns sorted object
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
		
		// This function encodes string in base64, this is used for hash validations
		// * value - value to convert
		var base64_encode=function(data){
			var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
			var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
				ac = 0,
				enc = "",
				tmp_arr = [];

			if (!data) {
				return data;
			}

			do { // pack three octets into four hexets
				o1 = data.charCodeAt(i++);
				o2 = data.charCodeAt(i++);
				o3 = data.charCodeAt(i++);

				bits = o1 << 16 | o2 << 8 | o3;

				h1 = bits >> 18 & 0x3f;
				h2 = bits >> 12 & 0x3f;
				h3 = bits >> 6 & 0x3f;
				h4 = bits & 0x3f;

				// use hexets to index into b64, and append result to encoded string
				tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
			} while (i < data.length);

			enc = tmp_arr.join('');
			
			var r = data.length % 3;
			
			return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
		}
	
		// This calculates sha1 hash from string
		// Source: http://www.webtoolkit.info/javascript-sha1.html
		// * msg - String to convert
		// Returns hashed string
		var sha1=function(msg){
			function rotate_left(n,s) {
				var t4 = ( n<<s ) | (n>>>(32-s));
				return t4;
			};
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
			};
			function cvt_hex(val) {
				var str="";
				var i;
				var v;
				for( i=7; i>=0; i-- ) {
					v = (val>>>(i*4))&0x0f;
					str += v.toString(16);
				}
				return str;
			};
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
			};
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
