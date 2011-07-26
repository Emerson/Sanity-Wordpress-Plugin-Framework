<?php
/*
Plugin Name: Your Plugin Name
Plugin URI: http://www.your-plugin.com
Description: Your well written plugin description.
Author: Your Name
Version: 1.0
Author URI: http://www.your-github-account.com/
*/


// Derive the current path and load up Sanity
$plugin_path = dirname(__FILE__).'/';
if(class_exists('SanityPluginFramework') != true)
    require_once($plugin_path.'framework/sanity.php');


/*
*		Define your plugin class which extends the SanityPluginFramework
*		Make sure you skip down to the end of this file, as there are a few
*		lines of code that are very important.
*/ 
class ExamplePlugin extends SanityPluginFramework {
	
	/*
	*	Some required plugin information
	*/
	var $version = '1.0';
	var $admin_js = array('hello');
	
	/*
	*		Required __construct() function that initalizes the Sanity Framework
	*/
	function __construct() {
      parent::__construct();
  }

	/*
	*		Run during the activation of the plugin
	*/
	function activate() {
		
	}
	
	/*
	*		Run during the initialization of Wordpress
	*/
	function initialize() {
	
	}
	
}

// Initalize the your plugin
$ExamplePlugin = new ExamplePlugin();

// Add an activation hook
register_activation_hook(__FILE__, array(&$ExamplePlugin, 'activate'));

// Run the plugins initialization method
add_action('init', array(&$ExamplePlugin, 'initialize'));

?>