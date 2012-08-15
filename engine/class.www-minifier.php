<?php

/*
Wave Framework
Minifier class

Minifier is used to minify text data without breaking functionality of that text. This is 
useful to be applied to JavaScript, CSS Stylesheets, HTML and XML formats for purposes of 
making the file size smaller, thus increasing the performance when file is transferred over 
HTTP. You should be careful when using minifier however, since it might break functionality 
under some instances, so it is always good to test before deploying minified resources to 
live systems.

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

class WWW_Minifier {
	
	// This method removes comments, tabs, spaces, new-lines and various other spaces from 
	// text. It assumes that text is in a CSS-like format.
	// * data - Data string to be minified
	public static function minifyCSS($data){
	
		// Removing comments
		$data=preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$data);
		// Removing tabs, spaces and newlines
		$data=str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), '', $data);
		// Removing other spaces before and after
		$data=preg_replace(array('(( )+{)','({( )+)'),'{',$data);
		$data=preg_replace(array('(( )+})','(}( )+)','(;( )*})'),'}',$data);
		$data=preg_replace(array('(;( )+)','(( )+;)'),';',$data);
		//Returning minified string
		return $data;
		
	}
	
	// This method removes comments, tabs, spaces, new-lines and various other spaces from 
	// text. It assumes that text is in a JavaScript-like format.
	// * data - Data string to be minified
	public static function minifyJS($data){
	
		// Removing comments
		$data=preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/","",$data);
		// Removing tabs, spaces and newlines
		$data=str_replace(array("\r\n","\r","\t","\n",'  ','    ','     '),'',$data);
		// Removing other spaces before and after
		$data=preg_replace(array('(( )+\))','(\)( )+)'), ')',$data);
		//Returning minified string
		return $data;
		
	}
	
	// This method removes comments, tabs, spaces, new-lines and various other spaces from 
	// text. It assumes that text is in a HTML-like format.
	// * data - Data string to be minified
	public static function minifyHTML($data){
	
		// Remove newlines and tabs
		$data=preg_replace('/[\r\n\t]/i','',$data);
		// Remove comments
		$data=preg_replace('/<!--.*?-->/i','',$data);
		//Returning minified string
		return $data;
		
	}
	
	// This method removes comments, tabs, spaces, new-lines and various other spaces from 
	// text. It assumes that text is in a XML-like format.
	// * data - Data string to be minified
	public static function minifyXML($data){
	
		// Remove newlines and tabs
		$data=preg_replace('/[\r\n\t]/i','',$data);
		// Remove comments
		$data=preg_replace('/<!--.*?-->/i','',$data);
		//Returning minified string
		return $data;
		
	}
	
}
	
?>