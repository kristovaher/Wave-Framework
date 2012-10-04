
/*
 * MyProjectNameHere <http://www.example.com>
 * Example JavaScript Class
 *
 * It is possible to load JavaScript classes dynamically through JavaScript Factory. To load 
 * this class, you need to create a Factory object and call getObject('example') call.
 *
 * @package    Factory
 * @author     DeveloperNameHere <email@example.com>
 * @copyright  Copyright (c) 2012, ProjectOwnerNameHere
 * @license    Unrestricted
 * @tutorial   /doc/pages/guide_objects.htm
 * @since      1.0.0
 * @version    1.0.0
 */
 
function example(){
	var myMessage='works';
	this.test=function(){
		alert(myMessage);
	}
}