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
require($plugin_path.'framework/sanity.php');


/*
*		Define your plugin class which extends the SanityPluginFramework
*		Make sure you skip down to the end of this file, as there are a few
*		lines of code that are very important.
*/ 
class ExamplePlugin extends SanityPluginFramework {
	
	/*
	*		Some required plugin information
	*/
	var $plugin_dir = 'example-plugin';
	var $version = '1.0';
	
	
	/*
	*		Required __construct() function that initalizes the Sanity Framework
	*/
	function __construct() {
      parent::__construct();
  }
	
	
	
}

// Initalize the your plugin
$ExamplePlugin = new ExamplePlugin();

// Add an activation hook
register_activation_hook(__FILE__, array(&$ExamplePlugin, 'activate'));

// Run the plugins initialization method
add_action('init', array(&$ExamplePlugin, 'initialize'));

?>