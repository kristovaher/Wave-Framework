	
/*
WWW Framework
WWW API connnection wrapper class for JavaScript

This class should be used when you wish to communicate with WWW Framework that is set up in another 
server. This class is independent from WWW Framework itself and can be used by other servers alone. 
Differently from PHP API Wrapper, JavaScript API Wrapper does not support sending and receiving 
encrypted data.

Author and support: Kristo Vaher - kristo@waher.net
*/

function WWW_Wrapper(address){

	// HTTP address of WWW-Framework-based API
	var apiAddress=address;
	
	// API profile information
	var apiSecretKey=false;
	var apiToken=false;
	
	// Information about last error
	var errorMessage=false;
	var errorCode=false;
	
	// Input data
	var inputData=new Object();
	
	// State settings
	var log=new Array();
	var timestampDuration=10;
	var hiddenWindowCounter=0;
	var apiSubmitFormId=false;
		
	// Log entry
	log.push('WWW API Wrapper object created with API address: '+address);
	
	// SETTINGS
		
		// This function simply returns current log
		// * implode - String to implode the log with
		// Function returns current log as an array
		this.returnLog=function(implode){
			log.push('Returning log');
			// Imploding, if requested
			if(implode==null || implode==false){
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
			if(typeof input==='object' && input instanceof Object){
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
					apiAddress=value;
					log.push('API address changed to: '+value);
					break;
				case 'www-secret-key':
					apiSecretKey=value;
					log.push('API secret key set to: '+value);
					break;
				case 'www-token':
					apiToken=value;
					log.push('API session token set to: '+value);
					break;
				case 'www-timestamp-duration':
					timestampDuration=$value;
					log.push('API valid timestamp duration set to: '+value);
					break;
				case 'www-output':
					log.push('Ignoring www-output setting, wrapper always requires output to be set to true');
					break;
				default:
					// Converting numeric flaots and strings to their proper formats
					if(!!(value % 1)){
						value=parseFloat(value);
					} else if((typeof(value) === 'number' || typeof(value) === 'string') && value !== '' && !isNaN(value)){
						value=parseInt(value);
					}
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
		this.setForm=function(formId){
			if(formId!=null){
				// Sets the form handler
				apiSubmitFormId=formId;
				// This forces another content type to stop browsers from pre-formatting the hidden iFrame content
				inputData['www-content-type']='text/html';
				log.push('Form ID set');
			} else {
				alert('Please enter proper form ID');
			}
		}
		
		// This function disables the use of form
		// Simply unsets the form ID
		this.clearForm=function(){
			if(inputData['www-content-type']!=null){
				delete inputData['www-content-type'];
			}
			apiSubmitFormId=false;
		}
		
		// This function simply deletes current input values
		// Returns true
		this.clearInput=function(){
			// Settings
			apiSecretKey=false;
			apiToken=false;
			timestampDuration=10;
			apiSubmitFormId=false;
			// Input data
			inputData=new Object();
			// Log entry
			log.push('Input data, crypted input and file data is unset');
			return true;
		}
		
	// SENDING REQUEST
		
		// This is the main function for making the request
		// This method builds a request string and then makes a request to API and attempts to fetch and parse the returned result
		// * callback - Method to call with the result as input once the request is done
		// * asynchronous - True or false flag about whether the request is asynchronous or not
		// * unserializeResult - Whether the result is automatically unserialized or not
		// Executes callback function with returned result, if no problems are encountered
		this.sendRequest=function(callback,asynchronous,unserializeResult){
		
			// Default value
			if(unserializeResult==null){
				unserializeResult=true;
			}
			// Default value
			if(asynchronous==null){
				asynchronous=false;
			}
			
			// Storing input data
			var thisInputData=clone(inputData);
			
			// Current state settings
			var thisApiSecretKey=apiSecretKey;
			var thisApiToken=apiToken;
			var thisTimestampDuration=timestampDuration;
			var thisApiSubmitFormId=apiSubmitFormId;
					
			// Clearing input data
			this.clearInput();

			// Log entry
			log.push('Starting to build request');
		
			// Correct request requires command to be set
			if(thisInputData['www-command']==null){
				errorCode=201;
				errorMessage='API command is not set, this is required';
				log.push(errorMessage);
				return false;
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
			// If default value is set, then it is removed
			if(thisInputData['www-return-timestamp']!=null && thisInputData['www-return-timestamp']==0){
				log.push('Since www-return-timestamp is set to default value, it is removed from input data');
				delete thisInputData['www-return-timestamp'];
			}
			// If default value is set, then it is removed
			if(thisInputData['www-return-hash']!=null && thisInputData['www-return-hash']==0){
				log.push('Since www-return-hash is set to default value, it is removed from input data');
				delete thisInputData['www-return-hash'];
			}
			// If default value is set, then it is removed
			if(thisInputData['www-output']!=null && thisInputData['www-output']==1){
				log.push('Since www-output is set to default value, it is removed from input data');
				delete thisInputData['www-output'];
			}
			
			// If profile is used, then timestamp will also be sent with the request
			if(thisInputData['www-profile']!=null){
				// Timestamp is required in API requests since it will be used for request validation and replay attack protection
				if(thisInputData['www-timestamp']==null){
					thisInputData['www-timestamp']=Math.floor(new Date().getTime()/1000);
				}
			}
						
			// If API profile and secret key are set, then wrapper assumes that non-public profile is used, thus hash and timestamp have to be included
			if(thisApiSecretKey){
			
				// Log entry
				log.push('API secret key set, hash authentication will be used');
				
				// Token has to be provided for every request that is not a 'www-create-session'
				if(thisApiToken){
					var requestToken=thisApiToken;
				} else {
					var requestToken='';
				}
				
				// Input data has to be sorted based on key
				thisInputData=ksort(thisInputData);
				
				// Validation hash is generated based on current serialization option
				if(thisInputData['www-hash']==null){
					thisInputData['www-hash']=sha1(JSON.stringify(thisInputData)+requestToken+thisApiSecretKey);
				}

				// Log entry
				if(thisApiToken){
					log.push('Validation hash created using JSON encoded input data, API token and secret key');
				} else {
					log.push('Validation hash created using JSON encoded input data and secret key');
				}
				
			} else {
		
				// Token-only validation means that token will be sent to the server, but data itself will not be hashed. This works like a cookie.
				if(thisApiToken){
					// Log entry
					log.push('Using token-only validation');
				} else {
					// Log entry
					log.push('API secret key is not set, hash validation will not be used');
				}
				
			}
			
			// MAKING A REQUEST
			
				// Getting input variables
				var queryString=new Array();
				for(node in thisInputData){
					queryString.push(node+'='+encodeURIComponent(thisInputData[node]));
				}
			
				// Building the request URL
				var requestURL=apiAddress+'?'+(queryString.join('&'));
				
				// Method
				var method='GET';
				
				// POST data
				var postData=null;
				
				// Command is made slightly differently depending on whether files are to be uploaded or not
				if(thisApiSubmitFormId==false){
				
					// POST request is made if the URL is longer than 2048 bytes (2KB).
					// While servers can easily handle 8KB of data, servers are recommended to be vary if the GET request is longer than 2KB
					if(requestURL.length>2048){
						// Inserting all input data to POST variables for submit command
						var postData=new FormData();
						for(node in thisInputData){
							postData.append(node,thisInputData[node]);
						}
						// Resetting the API url to not carry input data
						requestURL=apiAddress;
						method='POST';
					}
				
					// Making the request
					XMLHttp=new XMLHttpRequest();
					if(asynchronous){
						
						// Log entry
						log.push('Making '+method+' request to URL: '+requestURL);
						
						// AJAX states
						XMLHttp.onreadystatechange=function(oEvent){
							if(XMLHttp.readyState===4){
								// Result based on status
								if(XMLHttp.status===200){
									log.push('Result of the request: '+XMLHttp.responseText);
									return parseResult(XMLHttp.responseText,callback,thisApiSecretKey,thisApiToken,thisTimestampDuration,thisInputData,unserializeResult);
								} else if(XMLHttp.status===304){
									errorCode=214;
									errorMessage='Not modified';
									log.push(errorMessage);
									return false;
								} else {
									if(method=='POST'){
										errorCode=205;
										errorMessage='POST request failed: '+XMLHttp.statusText;
									} else {
										errorCode=204;
										errorMessage='GET request failed: '+XMLHttp.statusText;
									}
									log.push(errorMessage);
									return false;
								}  
							}  
						};
						
						XMLHttp.open(method,requestURL,true);
						XMLHttp.send(postData);
						
					} else {
					
						// Log entry
						log.push('Making '+method+' request to URL: '+requestURL);
						
						// XMLHttp request
						XMLHttp.open(method,requestURL,false);
						XMLHttp.send(postData);
						
						// Result based on status
						if(XMLHttp.status===200){  
							log.push('Result of the request: '+XMLHttp.responseText);
							return parseResult(XMLHttp.responseText,callback,thisApiSecretKey,thisApiToken,thisTimestampDuration,thisInputData,unserializeResult);
						} else if(XMLHttp.status===304){
							errorCode=214;
							errorMessage='Not modified';
							log.push(errorMessage);
							return false;
						} else { 
							if(method=='POST'){
								errorCode=205;
								errorMessage='POST request failed: '+XMLHttp.statusText;
							} else {
								errorCode=204;
								errorMessage='GET request failed: '+XMLHttp.statusText;
							}
							log.push(errorMessage);
							return false;
						}
						
					}
					
				} else {
				
					// Getting the hidden form
					var apiSubmitForm=document.getElementById(thisApiSubmitFormId);
					
					if(apiSubmitForm==null){
						errorCode=215;
						errorMessage='Form not found: '.thisApiSubmitFormId;
						log.push(errorMessage);
						return false;
					}
				
					// Hidden iFrame
					hiddenWindowCounter++;
					var hiddenWindow=document.createElement('iframe');
					var hiddenWindowName='WWW_API_Wrapper_Hidden_iFrame_'+hiddenWindowCounter;
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
					apiSubmitForm.enctype='multipart/form-data';
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
							apiSubmitForm.enctype=old_formEnctype;
						} else {
							apiSubmitForm.enctype='';
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
						var result=hiddenWindow.contentWindow.document.body.innerHTML;
						// Log entry
						log.push('Result of the request: '+result);
						result=parseResult(result,callback,thisApiSecretKey,thisApiToken,thisTimestampDuration,thisInputData,unserializeResult);
						// Removing hidden iFrame
						apiSubmitForm.removeChild(hiddenWindow);
						return result;
					}
					
					if(hiddenWindow.attachEvent==null){
						// Hidden iFrame onload function
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
		var parseResult=function(result,callback,thisApiSecretKey,thisApiToken,thisTimestampDuration,thisInputData,unserializeResult){
				
			// PARSING REQUEST RESULT
				
				// If unserialize command was set and the data type was JSON or serialized array, then it is returned as serialized
				if((thisInputData['www-return-type']==null || thisInputData['www-return-type']=='json') && unserializeResult){
					// JSON support is required
					result=JSON.parse(result);
					log.push('Returning JSON object');
				} else if(unserializeResult){
					// Every other unserialization attempt fails
					errorCode=207;
					errorMessage='Cannot unserialize returned data';
					log.push(errorMessage);
					return false;
				} else {
					// Data is simply returned if serialization was not requested
					log.push('Returning result');
				}
				
			// RESULT VALIDATION
			
				// Result validation only applies to non-public profiles
				if(thisInputData['www-profile']!=null){
				
					// Only unserialized data can be validated
					if(unserializeResult){
					
						// If it was requested that validation timestamp is returned
						if(thisInputData['www-return-timestamp']!=null){
							if(result['www-timestamp']!=null){
								// Making sure that the returned result is within accepted time limit
								if(((Math.floor(new Date().getTime()/1000))-thisTimestampDuration)>result['www-timestamp']){
									errorCode=210;
									errorMessage='Validation timestamp is too old';
									log.push(errorMessage);
									return false;
								}
							} else {
								errorCode=209;
								errorMessage='Validation data missing: Timestamp was not returned';
								log.push(errorMessage);
								return false;
							}
						}
						
						// If it was requested that validation hash is returned
						if(thisInputData['www-return-hash']!=null){
							// Hash and timestamp have to be defined in response
							if(result['www-hash']!=null){
								// Assigning returned array to hash validation array
								var validationHash=clone(result);
								// Hash itself is removed from validation
								delete validationHash['www-hash'];
								// Data is sorted
								validationHash=ksort(validationHash);
								// Validation depends on whether session creation or destruction commands were called
								if(thisInputData['www-command']=='www-create-session'){
									var resultHash=sha1(JSON.stringify(validationHash)+thisApiSecretKey);
								} else {
									var resultHash=sha1(JSON.stringify(validationHash)+thisApiToken+thisApiSecretKey);
								}
								// If sent hash is the same as calculated hash
								if(resultHash==result['www-hash']){
									log.push('Hash validation successful');
								} else {
									errorCode=211;
									errorMessage='Hash validation failed';
									log.push(errorMessage);
									return false;
								}
							} else {
								errorCode=209;
								errorMessage='Validation data missing: Hash was not returned';
								log.push(errorMessage);
								return false;
							}
						}
					
					} else {
						errorCode=208;
						errorMessage='Cannot validate hash: Unserialization not possible';
						log.push(errorMessage);
						return false;
					}
				
				}
							
			// Resetting the error variables
			errorCode=false;
			errorMessage=false;
			
			// If callback function is set
			if(callback==null){
				// Returning request result
				return result;
			} else {
				log.push('Sending data to callback '+callback+'(result)');
				eval(callback+'(result);');
			}				

		}
		
	// REQUIRED FUNCTIONS
	
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
		
		// This checks if an object is empty or not
		// * object - Object to be checked
		// Returns true or false
		var empty=function(object){
			if(typeof object=='object'){
				for(key in object){
					return false;
				}
				return true;
			}
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
		 
			word_array.push( msg_len>>>29 );
			word_array.push( (msg_len<<3)&0x0ffffffff );
		 
		 
			for ( blockstart=0; blockstart<word_array.length; blockstart+=16 ) {
		 
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

/*
This Wrapper is not yet implemented in WWW Framework
*/