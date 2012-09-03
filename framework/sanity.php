<?php
class SanityPluginFramework {

    // Container variables
    var $view = '';
    var $data = array();
    var $wpdb;
    var $nonce;

    // Assets to load
    var $admin_css = array();
    var $admin_js = array();
    var $plugin_css = array();
    var $plugin_js = array();

    // Paths
    var $css_path = 'css';
    var $js_path = 'js';
    var $plugin_dir = '';
    var $plugin_dir_name = '';

    // Used to define custom fields
    var $custom_fields = array();

    // AJAX actions
    var $ajax_actions = array(
        'admin' => array(),
        'plugin' => array()
    );
    
    function __construct($here = __FILE__) {
        global $wpdb;
        $this->add_ajax_actions();
        $this->wpdb = $wpdb;
        if(empty($this->plugin_dir)) {
            $this->plugin_dir = WP_PLUGIN_DIR.'/'.basename(dirname($here));
        }
        $this->plugin_dir_name = basename(dirname($here));
        $this->css_path = WP_PLUGIN_URL.'/'.$this->plugin_dir_name.'/css/';
        $this->js_path = WP_PLUGIN_URL.'/'.$this->plugin_dir_name.'/js/';
        add_action('wp_loaded', array(&$this, 'create_nonce'));
        if(!empty($this->admin_css) || !empty($this->admin_js) ) {
            add_action('admin_enqueue_scripts', array(&$this, 'load_admin_scripts'));
        }
        if(!empty($this->plugin_css) || !empty($this->plugin_js) ) {
            // TODO: enqueue plugin scripts
        }
        add_action('adminmenu', array($this, 'load_custom_fields'));
        if(!empty($this->custom_fields)) {
            add_action('save_post', array($this, 'save_custom_fields'));
        }
    }
    
    /*
    *       load_admin_scripts()
    *       =====================
    *       Loads admin-facing CSS and JS.
    */
    function load_admin_scripts() {
        foreach($this->admin_css as $css) {
            wp_enqueue_style($css, $this->css_path.$css.'.css');
        }
        foreach($this->admin_js as $js) {
            wp_enqueue_script($js, $this->js_path.$js.'.js');
        }
    }

    /*
    *   load_plugin_scripts()
    *   =====================
    *   Loads front-facing CSS and JS.
    */
    function load_plugin_scripts() {
        foreach($this->plugin_css as $css) {
            wp_enqueue_style($css, $this->css_path.$css.'.css');
        }
        foreach($this->plugin_js as $js) {
            wp_enqueue_script($js, $this->js_path.$js.'.js');
        }
    }

    /*
    *   create_nonce()
    *   ==============
    *   A security feature that Sanity presumes you should use. Please
    *   refer to: http://codex.wordpress.org/WordPress_Nonces
    */
    function create_nonce() {
        $this->nonce = wp_create_nonce('sanity-nonce');
    }

    /*
    *   add_ajax_actions()
    *   ==================
    *   Loops through $this->ajax_actions['admin'] and $this->ajax_actions['plugin'] and
    *   registers ajax actions. This makes the actions available in the client plugin.
    */
    function add_ajax_actions() {
        if(!empty($this->ajax_actions['admin'])) {
            foreach($this->ajax_actions['admin'] as $action) {
                add_action("wp_ajax_$action", array(&$this, $action));
            }
        }
        if(!empty($this->ajax_actions['plugin'])) {
            foreach($this->ajax_actions['plugin'] as $action) {
                add_action("wp_ajax_nopriv_$action", array(&$this, $action));
            }
        }               
    }


    /*
    *   load_custom_fields()
    *   ====================
    *   Loops through any custom fields defined within $this->custom_fields and
    *   prepopulatd $this->data with the associated data
    */
    function load_custom_fields() {
        foreach($this->custom_fields as $custom_field) {
            $this->data[$custom_field] = get_post_meta(get_the_ID(), $custom_field, true);
        }
    }

    /*
    *   save_custom_fields()
    *   ====================
    *   Automatically saves custom fields defined within the $this->custom_fields array.
    */
    function save_custom_fields($post_id) {
        if($this->is_valid_save($post_id)) {
            foreach($this->custom_fields as $custom_field) {
                if(!empty($_POST[$custom_field])) {
                    update_post_meta($post_id, $custom_field, $_POST[$custom_field]);
                }
            }
        }
    }

    /*
    *   is_valid_save($post_id)
    *   =======================
    *   Verifies that a valid user is doing a valid save. Returns true if valid, false otherwise.
    */
    function is_valid_save($post_id) {
        // Ignore the field if it's an autosave
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }
        // If we are missing the security nonce, return false
        if(empty($_POST['sanity-nonce'])) {
            return false;
        }
        // If the nonce does not verify, return false
        if(!wp_verify_nonce($_POST['sanity-nonce'], 'sanity-nonce')) {
            return false;
        }
        // If the current user does not have permission, return false
        if(!current_user_can('edit_post', $post_id)) {
            return false;
        }
        // If we get this far, return true
        return true;
    }

    /*
    *   render($view)
    *   =============
    *   Loads a view from within the /plugin/views folder. Keep in mind
    *   that any data you need should be passed through the $this->data array.
    *   A few examples:
    *
    *       Load /Plugin/views/example.php
    *       $this->render('example');
    *
    *       Load /Plugin/views/subfolder/example.php
    *       $this->render('subfolder/example);
    *
    */
    function render($view) {
        $template_path = $this->plugin_dir.'/views/'.$view.'.php';
        ob_start();
        include($template_path);
        $output = ob_get_clean();
        return $output;
    }

    /*
    *   sanity_nonce()
    *   ==============
    *   Used to render the hidden nonce field required by Sanity to save custom fields
    */
    function sanity_nonce() {
        echo "<input type='hidden' name='sanity-nonce' value='".$this->nonce."' />";
    }

}
?>