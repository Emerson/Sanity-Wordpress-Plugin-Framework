Wordpress Sanity Plugin Framework
=================================
The goal of this simple framework is to add some sanity and civility to Wordpress plugin development, a task which is currently messy and savage. So, without further ado, I should like to present you with some sanity.


For Developer's Eyes Only
=========================
The Sanity Framework was created for developers, not people who just install plugins. If you don't understand PHP and the principles of object oriented code you should probably just move on. However, if you're a frustrated Wordpress developer who is sick of the terrible coding practices encouraged by Wordpress' plugin API, then please, pull up a chair. We have much to talk about.


The Benefits
============
* A template system that helps to separate your logic from your views.
* An easy way to include JS and CSS within the admin or front-end of your plugin.
* AJAX in the admin made simple, with nonce integration out of the box.
* An object oriented approach to plugin development which makes modifications easy.


Installation
============
Sanity takes a very opinionated approach to plugin development. There are particular conventions laid out by the framework that need to be adhered to.


Templates
=========
Whenever possible, we should separate PHP from HTML. Within Wordpress, you'll see the two mixed together without apprehension. While this is often an "easy way" of doing things, it is almost never the "right way." Instead, we should segregate the two, thus keeping our logic pure and our views dumb. For this purpose we have the $this->render('my-template') method. A few examples:

		// From within a method in our controller
		$this->data['message'] = 'Pass this on to the template please';
		$this->render('my-template');
		
		// Meanwhile, in the /plugin/views/my-template.php file
		<h2>The Separation of Logic and Views</h2>
		<p><?php echo $this->data['message'];?></p>
		

Admin Javascript
================
Sometimes you might like to include some handy javascript or stylesheet files within the admin area of your wordpress installation. Well, Sanity makes this task quite easy! Just populate our $admin_js and $admin_css class variables with an array of scripts you would like to include.

        class YourPlugin extends SanityPluginFramework {
            
            // Loads /plugin/js/example.js
            var $admin_js = array('example');
            
            // Loads /plugin/css/example.css
            var $admin_css = array('example');
        
        }


Admin AJAX
==========
Adding ajax actions within the admin is easy. Within your plugin class you just follow this pattern:

    class YourPlugin extends SanityPluginFramework {


        // Registers our ajax action
        var $ajax_actions = array('admin' => array('my_ajax_action'));


        // Handles the actual ajax request and response.
        // All Wordpress admin ajax requests are sent through http://your-site.com/wp-admin/admin-ajax.php, which
        // is available as a global javascript variable sensibly named 'ajaxurl'
        function my_ajax_action() {
            $result['message'] = 'Your stuff has been saved';
            update_option('my_plugin_option', $_POST['field_1']);
            echo json_encode($result);
            exit();
        }

    }

Custom Post Types
=================
Adding a custom post types is easy, but relies more on WordPress than Sanity. You should start by adding an action to the 'init' phase of your plugin, and then use the standard WordPress post type syntax to set everything up:


    // Within your class...

    function __construct() {
        parent::__construct(__FILE__);
        add_action('init', array($this, 'add_post_type'));
    }

    function add_post_type() {
        // Labels
        $labels = array(
            'name'          => __('Sliders'),
            'singular_name' => __('Slider'),
            'add_new'       => __('Add New'),
            'all_items'     => __('All Sliders'),
            'add_new_item'  => __('Add New Slider'),
            'edit_item'     => __('Edit Slider'),
            'new_item'      => __('New Slider'),
            'view_item'     => __('View Slider'),
            'search_items'  => __('Search Sliders'),
            'not_found'     => __('No Sliders Found')
        );
        // Settings
        $settings = array(
            'labels'               => $labels,
            'public'               => false,
            'publicly_queryable'   => false,
            'show_ui'              => true,
            'register_meta_box_cb' => array($this, 'add_meta_boxes'),
            'menu_icon'            => get_stylesheet_directory_uri().'/assets/images/admin/sliders.png',
            'show_in_menu'         => true, 
            'query_var'            => true,
            'rewrite'              => array('slug' => 'sliders', 'with_front' => false),
            'capability_type'      => 'post',
            'has_archive'          => false, 
            'hierarchical'         => false,
            'menu_position'        => 2,
            'supports'             => array('thumbnail')
        ); 
        // Register the actual type
        register_post_type('slider', $settings);
    }

    funciton add_meta_boxes() {
        // Your metabox code here...
    }


Custom Fields
=============
Sanity takes away much of the pain associated with saving simple custom fields. To get started, all you need to do is declare the custom fields you want to save.

    // Within your class...
    // (the use of underscores is important within WordPress)
    var $custom_fields = array('_color', '_year', '_etc');

    // With your $custom_fields defined, you now need to render a view within your admin.
    // Sanity takes care of populating the $this->data array with your custom_field info.
    // Please note that it's important to have the Sanity Nonce field appear once within
    // your edit screen, otherwise it will not save your custom fields.
    <input type="text" name="_red" value="<?php $this->data['_red']?>" />
    <?php sanity_nonce(); // outputs a hidden nonce field ?>

Thats it! Sanity will automatically (and securly) save the data on submit.