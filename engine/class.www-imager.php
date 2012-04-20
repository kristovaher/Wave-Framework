<?php

/*
WWW Framework
Image editor class

This class is used to handle basic image editing, mostly for file resizes and cropping. This 
is used by default by Index gateway when a PNG or JPEG file is requested with specific parameters, 
but can also be used for other image editing in the system since it can be loaded independently.

* Image resizing
* Image positioning
* Image filtering
* Basic layering of filters

Author and support: Kristo Vaher - kristo@waher.net
*/

class WWW_Imager {

	// This stores image resource from imagecreatefromjpeg() of the currently handled file
	public $resource=false;
	
	// Current image dimensions
	public $width=0;
	public $height=0;

	// Current image IMAGETYPE_XXX type value
	public $type=false;
	
	// Loads image from filesystem to object
	// * location - Source file location in file system
	// Returns true if successful, false if failed
	public function input($location){
	
		// Checking if file actually exists in file system
		if(file_exists($location)){

			// Getting image information and assigning it to object parameters
			$imageInfo=getimagesize($location);
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
					throw new Exception('File format not supported');
					break;
			}
		
			// Image has been loaded
			return true;
			
		} else {
			// File was not found
			return false;
		}
		
	}
	
	// Outputs the image to filesystem or to output
	// * location - new file location in file system. If not set, then returns file data to output
	// * quality - Quality percentage, higher is better
	// * extension - Output file extension or type
	// Returns true if successful
	public function output($location=false,$quality=90,$extension=false){
	
		// Making sure quality is between acceptable values
		if($quality<0 || $quality>100){ 
			// 90 is a good high quality value for image compression
			$quality=90; 
		}
	
		// If output extension is not set, then system uses extension based on IMAGETYPE_XXX value
		if(!$extension){
			switch($this->type){
				case IMAGETYPE_JPEG:
					$extension='jpg';
					break;
				case IMAGETYPE_PNG:
					$extension='png';
					break;
				case IMAGETYPE_GIF:
					$extension='gif';
					break;
			}
		}
		
		// It output location is set, then file is stored in filesystem. If not set, then output is sent to user agent.
		if($location){
		
			// Different file types have different compression levels for quality
			switch($extension){
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
					throw new Exception('This output extension is not supported');
					break;
			}
			
		} else {
		
			// Different file types have different compression levels for quality
			switch($extension){
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
					if(imagepng($this->resource,null,(10-round($quality/10)))){
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
					throw new Exception('This output extension is not supported');
					break;
			}
			
		}
		
	}
	
	// This resizes and fits image into dimensions set with width and height and removing parts of image left outside the frame
	// * width - Width of resulting image
	// * height - Height of resulting image
	// * left - Position from the left edge. Can be 'center', 'left', 'right' or a pixel value.
	// * top - Position from the top edge. Can be 'center', 'top', 'bottom' or a pixel value.
	// Returns true if image resize was a success or did not need resizing
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
					throw new Exception('This left position is not supported');
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
					throw new Exception('This top position is not supported');
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
	
	// This simply places image into dimensions set with width and height and removing parts of image left outside the frame
	// * width - Width of resulting image
	// * height - Height of resulting image
	// * left - Position from the left edge. Can be 'center', 'left', 'right' or a pixel value.
	// * top - Position from the top edge. Can be 'center', 'top', 'bottom' or a pixel value.
	// * red - Amount of red color for background, from 0-255
	// * green - Amount of green color for background, from 0-255
	// * blue - Amount of blue color for background, from 0-255
	// Returns true if image resize was a success or did not need resizing
	public function resizeCrop($width,$height,$left='center',$top='center',$red,$green,$blue){
	
		// Canceling function if resizing is not needed
		if($this->width==$width && $this->height==$height){
			return true;
		}
	
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
					throw new Exception('This left position is not supported');
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
					throw new Exception('This top position is not supported');
				}
				break;
		}
		
		// Temporary image is created for the output
		$tmpImage=imagecreatetruecolor($width,$height);
		// Since the image might have a background color, the temporary image is filled with background color
		imagefill($tmpImage,0,0,imagecolorallocate($tmpImage,$red,$green,$blue));
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
	
	// This simply places the image into new dimensions, filling the rest of the areas with background color
	// * width - Width of resulting image
	// * height - Height of resulting image
	// * left - Position from the left edge. Can be 'center', 'left', 'right' or a pixel value.
	// * top - Position from the top edge. Can be 'center', 'top', 'bottom' or a pixel value.
	// * red - Amount of red color for background, from 0-255
	// * green - Amount of green color for background, from 0-255
	// * blue - Amount of blue color for background, from 0-255
	// Returns true if image resize was successful
	public function resizeFit($width,$height,$left='center',$top='center',$red,$green,$blue){
	
		// Canceling function if resizing is not needed
		if($this->width==$width && $this->height==$height){
			return true;
		}
	
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
					throw new Exception('This left position is not supported');
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
					throw new Exception('This top position is not supported');
				}
				break;
		}
		
		// Temporary image is created for the output
		$tmpImage=imagecreatetruecolor($width,$height);
		// Since the image might have a background color, the temporary image is filled with background color
		imagefill($tmpImage,0,0,imagecolorallocate($tmpImage,$red,$green,$blue));
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
	
	// This simply places the image into new dimensions, areas left empty won't be filled after resize
	// * width - Width of resulting image
	// * height - Height of resulting image
	// Returns true if image resize was successful
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
	
	// This simply resizes current resource to new width
	// * width - Width of resulting image
	// * height - Height of resulting image
	// Returns true if image resize was successful
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
	
	// This simply resizes current resource to new height
	// * width - Width of resulting image
	// * height - Height of resulting image
	// Returns true if image resize was successful
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
	
	// This function applies filtering to the image, this is basically a wrapper to GD library imagefilter() function
	// * type - Filtering type
	// * alpha - Level of alpha layering to use on top of original image
	// * settings - Filter settings is an array that carries up to three variables
	// Returns true if filtering was successful
	public function applyFilter($type,$alpha=100,$settings){
	
		// If alpha level is outside the permitted values
		if($alpha<0 || $alpha>100){ 
			$red=100; 
		}
		
		// Storing original type for reference
		$requestedType=$type;
	
		// Type is basically a shortcut to imagefilter() function
		switch($type){
			case 'negative':
				// Reverses all colors of the image
				$type=IMG_FILTER_NEGATE;
				// Defines the amount of settings to use
				$settingsRequired=0;
				break;
			case 'grayscale':
				// Converts the image into grayscale
				$type=IMG_FILTER_GRAYSCALE;
				// Defines the amount of settings to use
				$settingsRequired=0;
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
				// Defines the amount of settings to use
				$settingsRequired=0;
				break;
			case 'emboss':
				// Embosses the image
				$type=IMG_FILTER_EMBOSS;
				// Defines the amount of settings to use
				$settingsRequired=0;
				break;
			case 'blur':
				// Blurs the image using the Gaussian method
				$type=IMG_FILTER_GAUSSIAN_BLUR;
				// Defines the amount of settings to use
				$settingsRequired=0;
				break;
			case 'soften':
				// Softens the image
				$type=IMG_FILTER_SELECTIVE_BLUR;
				// Defines the amount of settings to use
				$settingsRequired=0;
				break;
			case 'sketch':
				// Uses mean removal to achieve a sketch effect
				$type=IMG_FILTER_MEAN_REMOVAL;
				// Defines the amount of settings to use
				$settingsRequired=0;
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
				throw new Exception($requestedType.' filter is not available');
				break;
		}
		
		// If incorrect number of settings are used then error is thrown
		if(count($settings)!=$settingsRequired){
			throw new Exception('Incorrect amount of filter settings for '.$requestedType.', '.count($settings).' set but '.$settingsRequired.' required');
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