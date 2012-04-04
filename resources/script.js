	
/*
WWW - PHP micro-framework
General JavaScript

Javascript front end controller that is loaded by WWW_controller_view and should be used for 
general Javascript functionality. This controller adds some useful functionality for modern 
websites, such as an AJAX controller that connects to API and parses the data from that 
file, as well as a hidden iFrame form submitting function that can be used for situations 
where AJAX cannot be used (such as when submitting files in background). Please note that 
ajax() and form() functions listed in this Javascript require some variables defined in 
View controller class, such as location of API script.

* Callback functions, if www-callback is returned in API
* AJAX API connection
* Hidden iFrame and form based API connection (can be used for uploading files)

Author and support: Kristo Vaher - kristo@waher.net
*/

// This is executed the moment document has finished loading
$(document).ready(function(){
	// This is used as a simple example of how an AJAX call is made
	// var input=new Object(); // input acts as sort-of associative array
	// input['string']='developer';
	// input['www-callback']='example-get';
	// ajax('example-get',input); // AJAX call is executed here
});

// This function acts as a callback for ajax() and form() functions
// If returned JSON has 'www-callback' assigned, then this is used as 'command' in this function
// If ajax() or form() are called with data with key 'www-callback' set, then this command is also executed
// * command - The command sent by returned data or initially submitted data
// * output - Includes all of the returned JSON data
// * input - Data that was sent when the command was originally processed
// Returns data based on command
function callback(command,output,input){
	switch(command){
	
		// Example action for alert should backend decide to call for an alert
		case 'example-get':
			alert(output['name']);
		break;
		
	}
}

// This function calls jQuery ajax() with additional functionality
// Please note that this function uses a __WWW variable defined by WWW_controller_view
// * command - API command that is called
// * data - Javascript object that carries data to be sent with this call
// * method - POST or GET method of the request (default: 'GET')
// * useLock - If lock is used then the command does not execute while another same command is still being processed (default: false)
// * async - Whether jQuery ajax() uses asyncrhonous request or not (default: true)
// Does not return anything
function ajax(command,data,method,useLock,async){
		
	// Whether lock is used that stops multiple same commands to be processed at once
	if(useLock==null){
		useLock=false;
	}
	
	// Lock is an optional flag to prevent simultaneous calls of the same command
	if(!useLock || __WWW['lock_'+command]!=1){
	
		// This locks the current command
		if(useLock){
			__WWW['lock_'+command]=1;
		}
		
		// Data is a Javascript object that virtually works like an associative array
		if(data==null){
			data=new Object();
			data['www-command']=command;
		} else {
			data['www-command']=command;
		}
		
		// Whether ajax() is executed asynchronously or not
		if(async==null){
			async=true;
		}
		
		// By default the call is made with GET method
		if(method==null){
			method='GET';
		}
		
		// jQuery AJAX call
		$.ajax({
			type:method,
			url:__WWW['api-url'],
			dataType:'json',
			cache:true,
			async:async,
			crossDomain:false,
			contentType:'application/x-www-form-urlencoded',
			ifModified:false,
			data:data,
			success:function(output){
				// If returned data or submitted data includes 'www-callback', ajaxReturn() function is called
				if(output['www-callback']!=null){
					callback(output['www-callback'],output,data);
				} else if(data['www-callback']){
					callback(data['www-callback'],output,data);
				}
				// Command is unlocked again and the same command can be sent again
				if(useLock){
					__WWW['lock_'+command]=0;
				}
			},
			error:function(obj,msg,detailedmsg){
				// In case there was any unexpected error in the backend (PHP errors and whatnot), alert everything
				alert('AJAX ERROR: Action ['+command+'] '+msg+' ('+detailedmsg+')');
				if(useLock){
					__WWW['lock_'+command]=0;
				}
			},
			statusCode:{
				// Systen alerts different status codes if something has gone wrong
				501: function(){
					alert('AJAX ERROR: Command not implemented [501]');
				},
				404: function(){
					alert('AJAX ERROR: Response not found [404]');
				},
				403: function(){
					alert('AJAX ERROR: Action requires HTTP authentication [403]');
				},
				401: function(){
					alert('AJAX ERROR: Action unauthorized [401]');
				}
			}
		});
	}
}

// This function creates hidden iFrame and submits contents of a set form to that iFrame, enabling features such as file submitting to server in the background
// Function also makes sure that when a number of submits are executed that they are processed in queue
// Please note that this function uses a __WWW variable defined by WWW_controller_view
// * command - API command that is called
// * formId - ID string of the form that carries the data to be submitted
// * data - Javascript object that carries additional data to be sent with this call
// * method - POST or GET method of the request (default: 'GET')
// * useLock - If lock is used then the command does not execute while another same command is still being processed (default: false)
// Does not return anything
function form(command,formId,data,method,useLock){
	
	// If form() function is already in process, it is queued and executed later
	if(__WWW['form-queue-set']){
	
		// All the data of current command is stored in Javascript object
		// Keys in this object are the parameters of this function
		var commandData=new Object();
		commandData['command']=command;
		commandData['formId']=formId;
		commandData['data']=data;
		commandData['method']=method;
		commandData['useLock']=useLock;
		
		// Queue is stored in this variable
		__WWW['form-queue'].push(commandData);
	
	} else {
			
		// Whether lock is used that stops multiple same commands to be processed at once
		if(useLock==null){
			useLock=false;
		}
		
		// Lock is an optional flag to prevent simultaneous calls of the same command
		if(!useLock || __WWW['lock_'+command]!=1){
		
			// This locks the current command
			if(useLock){
				__WWW['lock_'+command]=1;
			}
		
			// Every other call for form() function is queued until this call has finished
			__WWW['form-queue-set']=true;

			// Data is a Javascript object that virtually works like an associative array
			if(data==null){
				data=new Object();
			}
			
			// By default the call is made with GET method
			if(method==null){
				method='GET';
			}
			
			// If iFrame does not exist, then it is created
			if($('#__WWW_IFRAME').length<1){
			
				// Created iFrame is hidden and appended at the end of the document
				var __WWW_IFRAME=$('<iframe name="__WWW_IFRAME" id="__WWW_IFRAME" style="display:none;"/>');
				$('body').append(__WWW_IFRAME);
				
				// Load function is assigned for this hidden iFrame
				$('#__WWW_IFRAME').load(function(){
				
					// Loaded iFrame's contents are sent to jQuery's JSON interpreter
					var output=$.parseJSON($('#__WWW_IFRAME')[0].contentWindow.document.body.innerHTML);
					
					// If returned data or submitted data includes 'www-callback', ajaxReturn() function is called
					if(output['www-callback']!=null){
						callback(output['www-callback'],output,data);
					} else if(data['www-callback']){
						callback(data['www-callback'],output,data);
					}
					
					// Command is unlocked again and the same command can be sent again
					if(useLock){
						__WWW['lock_'+command]=0;
					}
					
					// If there are any other commands in form() queue, then the next one is executed
					__WWW['form-queue-set']=false;
					if(__WWW['form-queue'].length>=1){
					
						// Command data is loaded form array and submitted to form() function
						var newCommand=__WWW['form-queue'].shift();
						form(newCommand['command'],newCommand['formId'],newCommand['data'],newCommand['useLock'],newCommand['method']);
						
					}
					
				});
			}
			
			// Form data is loaded from form with set formId
			var __WWW_FORM=$('#'+formId);
			
			// Default values for form submit are being set
			__WWW_FORM.attr('action',__WWW['api-url']);
			__WWW_FORM.attr('method',method);
			__WWW_FORM.attr('target','__WWW_IFRAME');
			
			// POST request is sent with proper enctype
			if(method=='POST' || method=='post'){
				__WWW_FORM.attr('enctype','multipart/form-data');
			}
			
			// The data sent to form() function is loaded into hidden input elements
			for(var i in data){
				// If this data was not already provided when form() was called
				if(data[i]==null){
					data[i]=data[i];
				}
				__WWW_FORM.append('<input type="hidden" name="'+i+'" value="'+data[i]+'"/>');
			}
			
			// API specific variables are set
			__WWW_FORM.append('<input type="hidden" name="www-command" value="'+command+'"/>');
			__WWW_FORM.append('<input type="hidden" name="www-content-type" value="text/html"/>');
			
			// Form is submitted
			__WWW_FORM.submit();
			
		}
	}
}

