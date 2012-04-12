
/*
WWW Framework
WWW API connection wrapper class for Java

This class should be used when you wish to communicate with WWW Framework that is set up in another 
server. This class is independent from WWW Framework itself and can be used by other servers alone. 
This class can be used for mobile development for Android, for example.

Author and support: Kristo Vaher - kristo@waher.net
*/

// Required classes
import java.util.List;
import java.util.ArrayList;

public final class WWW_Wrapper {
	
	// HTTP address of WWW-Framework-based API
	private String apiAddress;
	
	// API profile information
	private String apiSecretKey="";
	private String apiToken="";
	
	// Information about last error
	public String errorMessage="";
	public Integer errorCode=0;
	
	// Input data
	private String[] inputData;
	private String[] cryptedData;
	private String[] inputFiles;
	
	// State settings
	private List<String> log=new ArrayList<String>();
	private Integer requestTimeout=10;
	private Integer timestampDuration=10;
	
	// API Address and custom user agent are assigned during object creation
	// * apiAddress - Full URL is required, like http://www.example.com/www.api
	// Object is created
	public WWW_Wrapper(String apiAddress){
		// This should be URL to API of WWW Framework
		this.apiAddress=apiAddress;
		// Log entry
		this.log.add("WWW API Wrapper object created with API address: "+apiAddress);
	}
	
	// SETTINGS
	
		// This function simply returns current log
		// Function returns current log as an array
		public List<String> returnLog(){
			// Log entry
			this.log.add("Returning log");
			// Returning the log
			return this.log;
		}
		
		// This function simply clears current log
		// Function returns true
		public Boolean clearLog(){
			this.log=new ArrayList<String>();
			return true;
		}
		
	// INPUT
		
		

}
