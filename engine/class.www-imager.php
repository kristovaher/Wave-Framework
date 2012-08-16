<?php

/*
Wave Framework
Image editor class

Imager is a class that acts as a wrapper to PHP GD library and has a number of methods to 
resize images with different resize algorithms as well as apply filtering to images and deal 
with image conversions between different formats. It can load an image resource from server 
or a URL and either store the edited image in a filesystem or push it to output buffer.

Author and support: Kristo Vaher - kristo@waher.net
License: GNU Lesser General Public License Version 3
*/

class WWW_Imager {

	// This variable holds the image resource that Imager class handles during its operation.
	public $resource=false;
	
	// This variable holds currently known image width in pixels.
	public $width=0;
	
	// This variable holds currently known image height in pixels.
	public $height=0;

	// Current image IMAGETYPE_X type value
	public $type=false;
	
	// This method is used to load an image resource for the Imager class. $location should 
	// be a file location in the system a web URL. This method automatically detects the type 
	// of image as well as image resolution, which is stored in $width and $height variables.
	// Method returns true if image was loaded successfully.
	// * location - Source file location in file system
	public function input($location){
	
		// Checking if file actually exists in file system
		if($imageInfo=getimagesize($location)){

			// Assigning image parameters to object
			$this->width=$imageInfo[0];
			$this->height=$imageInfo[1];
			$this->type=$imageInfo[2];

			// Creating image resource object based on file type
			switch($this->type){
				case IMAGETYPE_JPEG:
					// Image is created from assumed JPEG file
					if(!$this->resource=imagecreatefromjpeg($location)){
						return false;
					}
					break;
				case IMAGETYPE_PNG:
					// Image is created from assumed PNG file
					if(!$this->resource=imagecreatefrompng($location)){
						return false;
					}
					// This saves the alpha settings of the image
					imagealphablending($this->resource,false);
					imagesavealpha($this->resource,true);
					break;
				case IMAGETYPE_GIF:
					// Image is created from assumed GIF file
					if(!$this->resource=imagecreatefromgif($location)){
						return false;
					}
					break;
				default:
					trigger_error('File format not supported',E_USER_ERROR);
					break;
			}
		
			// Image has been loaded
			return true;
			
		} else {
			// File was not found
			return false;
		}
		
	}
	
	// This method stores the image in filesystem in $location folder and filename. If $location 
	// is not set, then image is returned to output buffer. $quality is used for the compression 
	// quality (from 0-100) and $format is used to define what file format the picture is 
	// returned. $format can be 'jpg', 'png' or 'gif'.
	// * location - new file location in file system. If not set, then returns file data to output
	// * quality - Quality percentage, higher is better
	// * format - Output file extension or type
	public function output($location=false,$quality=90,$format=false){
	
		// Making sure quality is between acceptable values
		if($quality<0 || $quality>100){ 
			// 90 is a good high quality value for image compression
			$quality=90; 
		}
	
		// If output format is not set, then system uses format based on IMAGETYPE_XXX value
		if(!$format){
			switch($this->type){
				case IMAGETYPE_JPEG:
					$format='jpg';
					break;
				case IMAGETYPE_PNG:
					$format='png';
					break;
				case IMAGETYPE_GIF:
					$format='gif';
					break;
			}
		}
		
		// It output location is set, then file is stored in filesystem. If not set, then output is sent to user agent.
		if($location){
		
			// Different file types have different compression levels for quality
			switch($format){
				case 'jpg':
					if(imagejpeg($this->resource,$location,$quality)){
						return true;
					} else {
						return false;
					}
					break;
				case 'png':
					if(imagepng($this->resource,$location,(9-floor($quality/10)))){
						return true;
					} else {
						return false;
					}
					break;
				case 'gif':
					if(imagegif($this->resource,$location)){
						return true;
					} else {
						return false;
					}
					break;
				default:
					trigger_error('This output format is not supported',E_USER_ERROR);
					break;
			}
			
		} else {
		
			// Different file types have different compression levels for quality
			switch($format){
				case 'jpg':
					// Second parameter of null means that image is pushed to output buffer instead of stored in file
					if(imagejpeg($this->resource,null,$quality)){
						header('Content-Type: image/jpeg');
						return true;
					} else {
						// 500 header is returned if file was not created
						header('HTTP/1.1 500 Internal Server Error');
						return false;
					}
					break;
				case 'png':
					// PNG format has compression from 0-9 with 0 being the best, so quality is updated accordingly
					if(imagepng($this->resource,null,(9-floor($quality/10)))){
						header('Content-Type: image/png');
						return true;
					} else {
						// 500 header is returned if file was not created
						header('HTTP/1.1 500 Internal Server Error');
						return false;
					}
					break;
				case 'gif':
					// Second parameter not used means that image is pushed to output buffer instead of stored in file
					if(imagegif($this->resource)){
						header('Content-Type: image/gif');
						return true;
					} else {
						// 500 header is returned if file was not created
						header('HTTP/1.1 500 Internal Server Error');
						return false;
					}
					break;
				default:
					trigger_error('This output format is not supported',E_USER_ERROR);
					break;
			}
			
		}
		
	}
	
	// This method is a shorthand method to apply series of $commands, similar to Wave Framework 
	// on-demand image loading parameters, to an image in $source folder and stored in $target 
	// folder. If $target is not set, then image is returned to output buffer.
	// * source - Source file location
	// * command - Series of commands that will be applied to the image
	// * target - Target file location
	public function commands($source,$command,$target=false){
		
		// This attempts to load the source image
		if($this->input($source)){
		
			// Exploding the command string
			$parameters=explode('&',$command);
		
			// DEFAULT SETTINGS
			
				// Default settings for dynamically resized image
				// This values will be changed based on if parameters are set
				$width=$this->width;
				$height=$this->height;
				$algorithm='fitcrop';
				$red=0;
				$green=0;
				$blue=0;
				$top='center';
				$left='center';
				$quality=90;
				$filters=array();
				$format=false;
				
			// FINDING SETTINGS FROM SET PARAMETERS
			
				// Looping over the data bits to find additional parameters
				foreach($parameters as $parameter){
					switch($parameter){
						case 'fitcrop':
							// This is a resize algorithm flag
							$algorithm='fitcrop';
							break;
						case 'crop':
							// This is a resize algorithm flag
							$algorithm='crop';
							break;
						case 'fitwithbackground':
							// This is a resize algorithm flag
							$algorithm='fitwithbackground';
							break;
						case 'fitwithoutbackground':
							// This is a resize algorithm flag
							$algorithm='fitwithoutbackground';
							break;
						case 'widthonly':
							// This is a resize algorithm flag
							$algorithm='widthonly';
							break;
						case 'jpg':
							// This is a resize algorithm flag
							$format='jpg';
							break;
						case 'png':
							// This is a resize algorithm flag
							$format='png';
							break;
						case 'heightonly':
							// This is a resize algorithm flag
							$algorithm='heightonly';
							break;
						default:
							// If any of the resize algorithm and cache flags were not hit, the parameter is matched for other conditions
							if(strpos($parameter,'filter(')!==false){
							
								// Background color setting is assumed if rgb is present
								$settings=str_replace(array('filter(',')'),'',$parameter);
								$settings=explode(',',$settings);
								
								// Storing data of new filter
								$newFilter=array();
								// First number is the filter type
								if($settings[0]!=''){
									// Filter type can also have parameters
									$typeSettings=explode('@',$settings[0]);
									// First parameter is the filter type
									$newFilter['type']=$typeSettings[0];
									// It is possible to 'layer' the effect by defining alpha level as the second parameter
									if(isset($typeSettings[1])){
										$newFilter['alpha']=$typeSettings[1];
									} else {
										// Filter effect is 100% if alpha was not defined
										$newFilter['alpha']=100;
									}
								}
								
								// Storing data of new filters settings
								$newFilter['settings']=array();
								// Storing other filter variables
								for($i=1;isset($settings[$i]);$i++){
									$newFilter['settings'][]=$settings[$i];
								}
								
								// Adding filter to list of filters
								$filters[]=$newFilter;
								
							} elseif(strpos($parameter,'rgb(')!==false){
							
								// Background color setting is assumed if rgb is present
								$colors=str_replace(array('rgb(',')'),'',$parameter);
								$colors=explode(',',$colors);
								// First number in parameter is red color amount
								if($colors[0]!=''){
									$red=$colors[0];
								}
								// Second number in parameter is green color amount
								if(isset($colors[1]) && $colors[1]!=''){
									$green=$colors[1];
								}
								// Third number in parameter is blue color amount
								if(isset($colors[2]) && $colors[2]!=''){
									$blue=$colors[2];
								}
								
							} elseif(strpos($parameter,'@')!==false){
							
								// Quality setting is assumed if @ sign is present
								$quality=str_replace('@','',$parameter);
								
							} elseif(strpos($parameter,'-')!==false){
							
								// Position setting is assumed if dash is present
								$positions=explode('-',$parameter);
								// First value is top position
								// This can be 'top', 'center', 'bottom' or a number in pixels
								if($positions[0]!=''){
									$top=$positions[0];
								}
								// Second value is left position
								// This can be 'left', 'center', 'right' or a number in pixels
								if($positions[1]!=''){
									$left=$positions[1];
								}
								
							} elseif(strpos($parameter,'x')!==false){
							
								// It is assumed that the remaining parameter is for image dimensions
								$dimensions=explode('x',$parameter);
								// First number is width
								if($dimensions[0]!=''){
									$width=$dimensions[0];
								}
								// Second number, if defined, is height
								if(isset($dimensions[1]) && $dimensions[1]!=''){
									$height=$dimensions[1];
								} else {
									// If height is not defined then height is considered to be as long as width
									$height=$width;
								}
								// If algorithm is still undefined, it is given a default value
								// This is needed when size is set, but algorithm is not
								if(!$algorithm){
									$algorithm='fitcrop';
								}
								
							} elseif($parameter!='nocache'){
							
								// Adding log entry	
								if(isset($logger)){
									$logger->setCustomLogData(array('response-code'=>404,'category'=>'image'));
									$logger->writeLog();
								}
								// Returning 404 header
								header('HTTP/1.1 404 Not Found');
								die();
					
							}
							break;
					}
				}
				
			// IMAGE EDITING
				
				// If algorithm, quality setting or a filter is set
				if($algorithm || $quality || !empty($filters)){
						
					// IMAGE RESIZES
				
						// Image is filtered through resize algorithm and saved in cache directory
						switch($algorithm){
							case 'fitcrop':
								// Crop algorithm fits the image into set dimensions, cutting the edges that do not fit
								if(!$this->resizeFitCrop($width,$height,$left,$top)){
									trigger_error('Cannot resize image with fit-crop algorithm',E_USER_ERROR);
								}
								break;
							case 'crop':
								// Crop algorithm places image in new dimensions box cutting the edges that do not fit
								if(!$this->resizeCrop($width,$height,$left,$top,$red,$green,$blue)){
									trigger_error('Cannot resize image with crop algorithm',E_USER_ERROR);
								}
								break;
							case 'fitwithbackground':
								// This fits image inside the box and gives it certain color background (if applicable)
								if(!$this->resizeFit($width,$height,$left,$top,$red,$green,$blue)){
									trigger_error('Cannot resize image with fit-with-background algorithm',E_USER_ERROR);
								}
								break;
							case 'fitwithoutbackground':
								// This simply resizes the image to fit specific dimensions
								if(!$this->resizeFitNoBackground($width,$height)){
									trigger_error('Cannot resize image with fit-without-background algorithm',E_USER_ERROR);
								}
								break;
							case 'widthonly':
								// This resizes the image to fixed width
								if(!$this->resizeWidth($width)){
									trigger_error('Cannot resize image with width-only algorithm',E_USER_ERROR);
								}
								break;
							case 'heightonly':
								// This resizes the image to fixed height
								if(!$this->resizeHeight($height)){
									trigger_error('Cannot resize image with height-only algorithm',E_USER_ERROR);
								}
								break;
						}
					
					// IMAGE FILTERS
					
						// As long as there are set filters
						if(!empty($filters)){
							// Each filter is applied, one by one
							foreach($filters as $filter){
								if(!$this->applyFilter($filter['type'],$filter['alpha'],$filter['settings'])){
									trigger_error('Cannot apply filter '.$filter['type'],E_USER_ERROR);
								}
							}
						}
						
					// STORING THE RESULTING IMAGE
				
				}
				
			// IMAGE OUTPUT
				
				// Resulting image is saved to cache
				if(!$this->output($target,$quality,$format)){
					trigger_error('Cannot output image file',E_USER_ERROR);
				}
		
		} else {
			return false;
		}
	}
	
	// This is a resize-algorithm method that resizes the current image resource to $width and 
	// $height. This resize method crops the image by removing the parts of picture that are left 
	// out of $width and $height dimensions. Variables $left and $top can be used to set the 
	// position of the image on the new, resized canvas and accept both numeric (pixel) values 
	// as well as relative ones, such as 'center', 'left', 'right' and 'top, 'bottom'.
	// * width - Width of resulting image
	// * height - Height of resulting image
	// * left - Position from the left edge. Can be 'center', 'left', 'right' or a pixel value.
	// * top - Position from the top edge. Can be 'center', 'top', 'bottom' or a pixel value.
	public function resizeFitCrop($width,$height,$left='center',$top='center'){
	
		// Canceling function if resizing is not needed
		if($this->width==$width && $this->height==$height){
			return true;
		}
	
		// System resizes source image based on which side of the image would be left 'outside' of the frame
		if(($this->height/$height)<($this->width/$width)){
			if(!$this->resizeHeight($height)){
				return false;
			}
		} else {
			if(!$this->resizeWidth($width)){
				return false;
			}
		}
		
		// Left position is calculated, if value is a string instead of a number
		switch($left){
		
			case 'center':
				// Calculating image left position based on positioning difference with new dimensions
				$left=-(round(($this->width-$width)/2));
				break;
			case 'left':
				// Left positioning is always 0
				$left=0;
				break;
			case 'right':
				// Right position is simply the current image width subtracted from new width
				$left=$width-$this->width;
				break;
			default:
				// Numeric positioning is possible, but error is thrown when the left value is not numeric
				if(!is_numeric($left)){
					trigger_error('This left position is not supported',E_USER_ERROR);
				}
				break;
		}
		
		// Top position is calculated, if value is a string instead of a number
		switch($top){
		
			case 'center':
				// Calculating image top position based on positioning difference with new dimensions
				$top=-(round(($this->height-$height)/2));
				break;
			case 'top':
				// Top positioning is always 0
				$top=0;
				break;
			case 'bottom':
				// Top position is simply the current image height subtracted from new height
				$top=$height-$this->height;
				break;
			default:
				// Numeric positioning is possible, but error is thrown when the top value is not numeric
				if(!is_numeric($top)){
					trigger_error('This top position is not supported',E_USER_ERROR);
				}
				break;
		}
		
		// Temporary image is created for the output
		$tmpImage=imagecreatetruecolor($width,$height);
		// This preserves alpha maps, if it exists (such as for PNG)
		imagealphablending($tmpImage,false);
		imagesavealpha($tmpImage,true);
		// Current image resource is placed on temporary resource
		imagecopyresampled($tmpImage,$this->resource,$left,$top,0,0,$this->width,$this->height,$this->width,$this->height);
		
		// New dimensions and temporary image resource is assigned as resource of this object
		$this->width=$width;
		$this->height=$height;
		$this->resource=$tmpImage;
		
		// Image has been resized
		return true;
		
	}
	
	// This is a resize-algorithm method that resizes the current image resource to $width 
	// and $height without resizing the actual image. This resize method crops the image by 
	// removing the parts of picture that are left out of $width and $height dimensions. 
	// Variables $left and $top can be used to set the position of the image on the new, 
	// resized canvas and accept both numeric (pixel) values as well as relative ones, such 
	// as 'center', 'left', 'right' and 'top, 'bottom'. $red, $green and $blue are RGB values 
	// for background color in case background is required (not used for PNG images).
	// * width - Width of resulting image
	// * height - Height of resulting image
	// * left - Position from the left edge. Can be 'center', 'left', 'right' or a pixel value.
	// * top - Position from the top edge. Can be 'center', 'top', 'bottom' or a pixel value.
	// * red - Amount of red color for background, from 0-255
	// * green - Amount of green color for background, from 0-255
	// * blue - Amount of blue color for background, from 0-255
	public function resizeCrop($width,$height,$left='center',$top='center',$red=0,$green=0,$blue=0){
	
		// Canceling function if resizing is not needed
		if($this->width==$width && $this->height==$height){
			return true;
		}
		
		// PNG images do not require a background
		if($this->type!=IMAGETYPE_PNG){
	
			// If red color is out of allowed range it is defaulted to 0
			if($red<0 || $red>255){ 
				$red=0; 
			}
			// If green color is out of allowed range it is defaulted to 0
			if($green<0 || $green>255){ 
				$green=0; 
			}
			// If blue color is out of allowed range it is defaulted to 0
			if($blue<0 || $blue>255){ 
				$blue=0; 
			}
			
		}
	
		// Left position is calculated, if value is a string instead of a number
		switch($left){
			case 'center':
				// Calculating image left position based on positioning difference with new dimensions
				$left=-(round(($this->width-$width)/2));
				break;
			case 'left':
				// Left positioning is always 0
				$left=0;
				break;
			case 'right':
				// Right position is simply the current image width subtracted from new width
				$left=$width-$this->width;
				break;
			default:
				// Numeric positioning is possible, but error is thrown when the left value is not numeric
				if(!is_numeric($left)){
					trigger_error('This left position is not supported',E_USER_ERROR);
				}
				break;
		}
		
		// Top position is calculated, if value is a string instead of a number
		switch($top){
			case 'center':
				// Calculating image top position based on positioning difference with new dimensions
				$top=-(round(($this->height-$height)/2));
				break;
			case 'top':
				// Top positioning is always 0
				$top=0;
				break;
			case 'bottom':
				// Top position is simply the current image height subtracted from new height
				$top=$height-$this->height;
				break;
			default:
				// Numeric positioning is possible, but error is thrown when the top value is not numeric
				if(!is_numeric($top)){
					trigger_error('This top position is not supported',E_USER_ERROR);
				}
				break;
		}
		
		// Temporary image is created for the output		
		$tmpImage=imagecreatetruecolor($width,$height);
		
		// Background color is transparent for PNG images
		if($this->type!=IMAGETYPE_PNG){
			// Since the image might have a background color, the temporary image is filled with background color
			imagefill($tmpImage,0,0,imagecolorallocate($tmpImage,$red,$green,$blue));
		} else {
			// PNG images are filled with alpha background color
			imagefill($tmpImage,0,0,imagecolorallocatealpha($tmpImage,0,0,0,127));
		}
		
		// This preserves alpha maps, if it exists (such as for PNG)
		imagealphablending($tmpImage,false);
		imagesavealpha($tmpImage,true);
		// Current image resource is placed on temporary resource
		imagecopyresampled($tmpImage,$this->resource,$left,$top,0,0,$this->width,$this->height,$this->width,$this->height);
		
		// New dimensions and temporary image resource is assigned as resource of this object
		$this->width=$width;
		$this->height=$height;
		$this->resource=$tmpImage;
		
		// Image has been resized
		return true;
		
	}
	
	// This is a resize-algorithm method that resizes the current image simply by resizing the 
	// image to $width and $height and leaves the remaining space for background color. This 
	// resize method crops the image by removing the parts of picture that are left out of $width 
	// and $height dimensions. Variables $left and $top can be used to set the position of the 
	// image on the new, resized canvas and accept both numeric (pixel) values as well as relative 
	// ones, such as 'center', 'left', 'right' and 'top, 'bottom'. $red, $green and $blue are RGB 
	// values for background color in case background is required (not used for PNG images).
	// * width - Width of resulting image
	// * height - Height of resulting image
	// * left - Position from the left edge. Can be 'center', 'left', 'right' or a pixel value.
	// * top - Position from the top edge. Can be 'center', 'top', 'bottom' or a pixel value.
	// * red - Amount of red color for background, from 0-255
	// * green - Amount of green color for background, from 0-255
	// * blue - Amount of blue color for background, from 0-255
	public function resizeFit($width,$height,$left='center',$top='center',$red=0,$green=0,$blue=0){
	
		// Canceling function if resizing is not needed
		if($this->width==$width && $this->height==$height){
			return true;
		}
	
		// PNG images do not require a background
		if($this->type!=IMAGETYPE_PNG){
		
			// If red color is out of allowed range it is defaulted to 0
			if($red<0 || $red>255){ 
				$red=0; 
			}
			// If green color is out of allowed range it is defaulted to 0
			if($green<0 || $green>255){ 
				$green=0; 
			}
			// If blue color is out of allowed range it is defaulted to 0
			if($blue<0 || $blue>255){ 
				$blue=0; 
			}
			
		}
	
		// System resizes source image based on which side of the image would be left 'outside' of the frame		
		if(($this->height/$height)>($this->width/$width)){
			if(!$this->resizeHeight($height)){
				return false;
			}
		} else {
			if(!$this->resizeWidth($width)){
				return false;
			}
		}
		
		// Left position is calculated, if value is a string instead of a number
		switch($left){
			case 'center':
				// Calculating image left position based on positioning difference with new dimensions
				$left=-(round(($this->width-$width)/2));
				break;
			case 'left':
				// Left positioning is always 0
				$left=0;
				break;
			case 'right':
				// Right position is simply the current image width subtracted from new width
				$left=$width-$this->width;
				break;
			default:
				// Numeric positioning is possible, but error is thrown when the left value is not numeric
				if(!is_numeric($left)){
					trigger_error('This left position is not supported',E_USER_ERROR);
				}
				break;
		}
		
		// Top position is calculated, if value is a string instead of a number
		switch($top){
			case 'center':
				// Calculating image top position based on positioning difference with new dimensions
				$top=-(round(($this->height-$height)/2));
				break;
			case 'top':
				// Top positioning is always 0
				$top=0;
				break;
			case 'bottom':
				// Top position is simply the current image height subtracted from new height
				$top=$height-$this->height;
				break;
			default:
				// Numeric positioning is possible, but error is thrown when the top value is not numeric
				if(!is_numeric($top)){
					trigger_error('This top position is not supported',E_USER_ERROR);
				}
				break;
		}
		
		// Temporary image is created for the output
		$tmpImage=imagecreatetruecolor($width,$height);
		
		// Background color is transparent for PNG images
		if($this->type!=IMAGETYPE_PNG){
			// Since the image might have a background color, the temporary image is filled with background color
			imagefill($tmpImage,0,0,imagecolorallocate($tmpImage,$red,$green,$blue));
		} else {
			// PNG images are filled with alpha background color
			imagefill($tmpImage,0,0,imagecolorallocatealpha($tmpImage,0,0,0,127));
		}
		
		// This preserves alpha maps, if it exists (such as for PNG)
		imagealphablending($tmpImage,false);
		imagesavealpha($tmpImage,true);
		// Current image resource is placed on temporary resource
		imagecopyresampled($tmpImage,$this->resource,$left,$top,0,0,$this->width,$this->height,$this->width,$this->height);
		
		// New dimensions and temporary image resource is assigned as resource of this object
		$this->width=$width;
		$this->height=$height;
		$this->resource=$tmpImage;
		
		// Image has been resized
		return true;
		
	}
	
	// This is a resize-algorithm method that resizes the current image simply by resizing the 
	// image to $width and $height and removing the dimensions that would otherwise be left for 
	// a background. This resize method crops the image by removing the parts of picture that are 
	// left out of $width and $height dimensions. Variables $left and $top can be used to set the 
	// position of the image on the new, resized canvas and accept both numeric (pixel) values as 
	// well as relative ones, such as 'center', 'left', 'right' and 'top, 'bottom'. $red, $green 
	// and $blue are RGB values for background color in case background is required (not used for 
	// PNG images).
	// * width - Width of resulting image
	// * height - Height of resulting image
	public function resizeFitNoBackground($width,$height){
	
		// Canceling function if resizing is not needed
		if($this->width==$width && $this->height==$height){
			return true;
		}
	
		// System resizes source image based on which side of the image would be left 'outside' of the frame
		if(($this->height/$height)>($this->width/$width)){
			if(!$this->resizeHeight($height)){
				return false;
			}
		} else {
			if(!$this->resizeWidth($width)){
				return false;
			}
		}
		
		// New dimensions are assigned for this object
		$this->width=$width;
		$this->height=$height;
		
		// Image has been resized
		return true;
		
	}
	
	// This method simply resizes the image to fixed width set with $width variable. New image 
	// height depends on the result of the resize.
	// * width - Width of resulting image
	public function resizeWidth($width){
	
		// Canceling function if resizing is not needed
		if($this->width==$width){
			return true;
		}
	
		// Ratio is used to calculate the ratio which is used to resize the image
		$ratio=$this->width/$width;
		// New height is calculated according to ratio
		$height=round($this->height/$ratio);
		
		// Temporary image is created for the output
		$tmpImage=imagecreatetruecolor($width,$height);
		// This preserves alpha maps, if it exists (such as for PNG)
		imagealphablending($tmpImage,false);
		imagesavealpha($tmpImage,true);
		
		// Current image resource is placed on temporary resource
		if(!imagecopyresampled($tmpImage,$this->resource,0,0,0,0,$width,$height,$this->width,$this->height)){
			return false;
		}
		
		// New dimensions and temporary image resource is assigned as resource of this object
		$this->width=$width;
		$this->height=$height;
		$this->resource=$tmpImage;
		
		// Image has been resized
		return true;
		
	}
	
	// This method simply resizes the image to fixed height set with $height variable. New image 
	// width depends on the result of the resize.
	// * height - Height of resulting image
	public function resizeHeight($height){
	
		// Canceling function if resizing is not needed
		if($this->height==$height){
			return true;
		}
	
		// Ratio is used to calculate the ratio which is used to resize the image
		$ratio=$this->height/$height;
		// New width is calculated according to ratio
		$width=round($this->width/$ratio);
		
		// Temporary image is created for the output
		$tmpImage=imagecreatetruecolor($width,$height);
		// This preserves alpha maps, if it exists (such as for PNG)
		imagealphablending($tmpImage,false);
		imagesavealpha($tmpImage,true);
		
		// Current image resource is placed on temporary resource
		if(!imagecopyresampled($tmpImage,$this->resource,0,0,0,0,$width,$height,$this->width,$this->height)){
			return false;
		}
		
		// New dimensions and temporary image resource is assigned as resource of this object
		$this->width=$width;
		$this->height=$height;
		$this->resource=$tmpImage;
		
		// Image has been resized
		return true;
		
	}
	
	// This method is a wrapper for imagefilter() and imageconvolution() methods. $type can be 
	// 'negative', 'grayscale', 'brightness', 'contrast', 'colorize', 'alphacolorize', 'edge', 
	// 'emboss', 'blur', 'soften', 'sketch', 'smooth', 'pixelate' and 'convulate'. $alpha is 
	// the percentage that this filter effect will be applied to the original image as a layer. 
	// $settings is an array of variables that are expected to be sent with imagefilter() and 
	// imageconvulation() methods.
	// * type - Filtering type
	// * alpha - Level of alpha layering to use on top of original image
	// * settings - Filter settings is an array that carries up to three variables
	public function applyFilter($type,$alpha=100,$settings=array()){
	
		// If alpha level is outside the permitted values
		if($alpha<0 || $alpha>100){ 
			$alpha=100; 
		}
		
		// Storing original type for reference
		$requestedType=$type;

        // Amount of settings/variables that are required
        $settingsRequired=0;
	
		// Type is basically a shortcut to imagefilter() function
		switch($type){
			case 'negative':
				// Reverses all colors of the image
				$type=IMG_FILTER_NEGATE;
				break;
			case 'grayscale':
				// Converts the image into grayscale
				$type=IMG_FILTER_GRAYSCALE;
				break;
			case 'brightness':
				// Changes the brightness of the image, first setting defines brightness level
				$type=IMG_FILTER_BRIGHTNESS;
				// Defines the amount of settings to use
				$settingsRequired=1;
				break;
			case 'contrast':
				// Changes the contrast of the image, first setting defines contrast strength
				$type=IMG_FILTER_CONTRAST;
				// Defines the amount of settings to use
				$settingsRequired=1;
				break;
			case 'colorize':
				// Like 'grayscale', except you can specify the color. Settings are 'red', 'green' and 'blue' and alpha
				$type=IMG_FILTER_COLORIZE;
				// Defines the amount of settings to use
				$settingsRequired=3;
				break;
			case 'alphacolorize':
				// Like 'grayscale', except you can specify the color. Settings are 'red', 'green' and 'blue' and alpha
				$type=IMG_FILTER_COLORIZE;
				// Defines the amount of settings to use
				$settingsRequired=4;
				break;
			case 'edge':
				// Uses edge detection to highlight the edges in the image.
				$type=IMG_FILTER_EDGEDETECT;
				break;
			case 'emboss':
				// Embosses the image
				$type=IMG_FILTER_EMBOSS;
				break;
			case 'blur':
				// Blurs the image using the Gaussian method
				$type=IMG_FILTER_GAUSSIAN_BLUR;
				break;
			case 'soften':
				// Softens the image
				$type=IMG_FILTER_SELECTIVE_BLUR;
				break;
			case 'sketch':
				// Uses mean removal to achieve a sketch effect
				$type=IMG_FILTER_MEAN_REMOVAL;
				break;
			case 'smooth':
				// Makes the image smoother, first setting defines the level of smoothness
				$type=IMG_FILTER_SMOOTH;
				// Defines the amount of settings to use
				$settingsRequired=1;
				break;
			case 'pixelate':
				// Applies pixelation effect to the image, setting 1 defines block size and setting 2 the effect mode
				$type=IMG_FILTER_PIXELATE;
				// Defines the amount of settings to use
				$settingsRequired=2;
				break;
			case 'convulate':
				// Applies pixelation effect to the image, setting 1 defines block size and setting 2 the effect mode
				$type='convulate';
				// Defines the amount of settings to use
				$settingsRequired=11;
				break;
			default:
				trigger_error($requestedType.' filter is not available',E_USER_ERROR);
				break;
		}
		
		// If incorrect number of settings are used then error is thrown
		if(count($settings)!=$settingsRequired){
			trigger_error('Incorrect amount of filter settings for '.$requestedType.', '.count($settings).' set but '.$settingsRequired.' required',E_USER_ERROR);
		}
		
		// If alpha setting is used, then the resulting image will be 'merged'
		if($alpha!=100){
		
			// Temporary image is created for the output
			$tmpImage=imagecreatetruecolor($this->width,$this->height);
			
			// This preserves alpha maps, if it exists (such as for PNG)
			imagealphablending($tmpImage,false);
			imagesavealpha($tmpImage,true);
			// Current image resource is placed on temporary resource
			if(!imagecopyresampled($tmpImage,$this->resource,0,0,0,0,$this->width,$this->height,$this->width,$this->height)){
				return false;
			}
			
		}
		
		// Convulation is a complicated function
		if($type=='convulate'){
		
			// Convulation matrix is 3x3 array of floats
			$matrix=array(array($settings[0],$settings[1],$settings[2]),array($settings[3],$settings[4],$settings[5]),array($settings[6],$settings[7],$settings[8]));
			
			// Convulation applied
			if(!imageconvolution($this->resource, $matrix, $settings[9], $settings[10])){
				return false;
			}
		
		} else {
			
			// This applies the requested filter
			// imagefilter() expects different amount of parameters, this takes all conditions into account
			switch (count($settings)){
				case 4:
					if(!imagefilter($this->resource,$type,$settings[0],$settings[1],$settings[2],$settings[3])){
						return false;
					}
					break;
				case 3:
					if(!imagefilter($this->resource,$type,$settings[0],$settings[1],$settings[2])){
						return false;
					}
					break;
				case 2:
					if(!imagefilter($this->resource,$type,$settings[0],$settings[1])){
						return false;
					}
					break;
				case 1:
					if(!imagefilter($this->resource,$type,$settings[0])){
						return false;
					}
					break;
				default:
					if(!imagefilter($this->resource,$type)){
						return false;
					}
					break;
			}
		
		}
		
		// Filtered image is layered on top of the original, if alpha is not 100%
		if($alpha!=100){
			// Alpha value in the end does the layering
			if(!imagecopymerge($tmpImage,$this->resource,0,0,0,0,$this->width,$this->height,$alpha)){
				return false;
			}
			// New image is set as the resource
			$this->resource=$tmpImage;
		}
		
		// Processing complete
		return true;
	
	}
  
}

?>