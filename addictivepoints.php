<?php

/*
    Plugin Name: addictivepoints for WordPress
    Plugin URI: http://addictivepoints.com
    Description: <strong>addictive</strong>points for WordPress allows you to award your visitors <strong>addictive</strong>points for daily visits, Facebook Likes and Twitter follows.
				
				You can get a free package for these three actions or upgrade to
				a flexible package and reward visitors for any action on your
				website, for example:-
				
				* Filling in a survey or a poll..
				* Commenting on a blog.
				* Placing an order on your site.
				* Reviewing a produce.
				* Register with your site.
				* Refer a friend.
				* Invite a friend to join your site.
				* Enter a promo code on your site.
				* Watching a video.
				* Any action you can think of!
				
				To get started with your free package :-
				
				1 : Get your key by registering your site at
				    www.addictivepoints.com/partners
				    and follow the simple four step process making a note of
				    your API key.
				
				2 : Activate the plugin from your Wordpress Plugins menu.
				
				3 : From the Wordpress settings menu enter the API key and
				    choose where you want the tab to appear on your site. Done!
				
    Version: 1.0
    Author: <strong>addictive</strong>points
    Author URI: http://addictivepoints.com
*/

// Let's check to see if we have a suitable version of wordpress; message out if not.
global $wp_version;
if(!version_compare($wp_version, '3.0', '>='))
{
    die("AddictivePoints requires WordPress 3.0 or above. <a href='http://codex.wordpress.org/Upgrading_WordPress'>Please update!</a>");
}


// To work around wordpress bug http://core.trac.wordpress.org/ticket/16953
$addictivepoints_file = __FILE__;

if ( isset( $mu_plugin ) ) { 
    $addictivepoints_file = $mu_plugin; 
}
if ( isset( $network_plugin ) ) {
    $addictivepoints_file = $network_plugin;
}
if ( isset( $plugin ) ) {
    $addictivepoints_file = $plugin;
}

$GLOBALS['addictivepoints_file'] = $addictivepoints_file;

// Make sure the addictivepoints class doesn't already exist.
if(!class_exists('AddictivePoints')) {

    // Addictive Points Plugin class declaration and definition.
    class AddictivePoints
    {
        // Store the plugin id.
        private $plugin_id;
        // Store the option key value.
        private $options;
		// Store any validation errors if the key entered does not pass validation.
		public $validation_errors = array();

        /** Method
        * Use: Constructor definition
        * @param1: string
        * Return: NULL
        *******************************/
        public function __construct($id)
        {
            // Store
            $this->plugin_id = $id;
            // create array of options
            $this->options = array();
            // set default options
            $this->options['key'] = '';
			
            /*
            * Add action and registration Hooks
            */
            // register the script files into the footer section
            add_action('wp_footer', array(&$this, 'addictivepoints_scripts'));
            // plugin initialise and save options
            register_activation_hook(__FILE__, array(&$this, 'install'));
            // this action is activated on plugin initialisation and allows for plugin option updates.
            add_action('admin_init', array(&$this, 'init'));
            // we need to add in a plugin admin menu to allow options to be selected.
            add_action('admin_menu', array(&$this, 'menu'));
        }
		/** Method
        * Use: get the plugin options.
        * @param1: NONE
        * Return: array of options.
        *******************************/
        private function get_options()
        {
            // return saved options
            $options = get_option($this->plugin_id);
            return $options;
        }
		/** Method
        * Use: Allows the key to be updated and the location of the tab.
        * @param1: NONE
        * Return: NONE
        *******************************/
        private function update_options($options=array())
        {
            // update options
            update_option($this->plugin_id, $options);
        }		
		/** Method
        * Use: Code for injecting Addictive Points API code into each loaded page.
        * @param1: NONE
        * Return: NONE
        *******************************/
        public function addictivepoints_scripts()
        {
            if (!is_admin()) {
                $options = $this->get_options();
                $key = trim($options['key']);
				$var_pos = $options['var_pos'];
				
                if ($options['key_pass']) {
                    $this->show_addictivepoints_reward_js($key, $var_pos);
                }
            }
        }
		/** Method
        * Use: Getting the code from the heredoc function, replacing the key and tab position data.
        * @param1: The key code submitted by the user.
        * @param2: The tab position as submitted by the user.
        * Return: NONE
        *******************************/
        public function show_addictivepoints_reward_js($key = "", $var_pos = "")
        {
			// Get the standard javascript api code that we will inject into the wordpress footer.
			$js_code = apoints_js_code();
			
			// Get the left or right position of the tab. Default is top-right.
			if (strpos($var_pos, 'left') !== FALSE)
			{
				$xpos = 'left';
			}
			else
			{
				$xpos = 'right';
			}
			// Get the Top or Bottom position of the tab.
			if (strpos($var_pos, 'bottom') !== FALSE)
			{
				$ypos = 'bottom';
			}
			else
			{
				$ypos = 'top';
			}
			
			// Perform string replacement within the template javascript to inject the users specific key and tab position.
			$existing_string = array( '_APOINTS_KEY_DEF_', '_APOINTS_X_POS_', '_APOINTS_Y_POS_');
			$replace_string = array($key, $xpos, $ypos);
			$apoints_js_code = str_replace( $existing_string, $replace_string, $js_code);
			
			echo $apoints_js_code;
        }
		/** Method
        * Use: General install function
        * @param1: NULL        
        * Return: NONE
        *******************************/
        public function install()
        {
            $this->update_options($this->options);
        }
		
		/** Method
        * Use: Hook for registration.
        * @param1: NULL
        * Return: NONE
        *******************************/
        public function init()
        {
            register_setting($this->plugin_id.'_options', $this->plugin_id);
        }
		
		/** Method
        * Use: Admin page for Addictive Points, including validation checking.
        * @param1: NULL
        * Return: NONE
        *******************************/
        public function options_page()
        {
            if (!current_user_can('manage_options'))
            {
                wp_die( __('You can manage options from the Settings-><strong>addictive<strong>points Options menu.') );
            }

            // Get the users saved options
            $options = $this->get_options();
			
			// Validation section ::-----------------------------------------------------
			$key = $options['key'];
			
			$key_length = strlen($key);
			if ($key_length != 40)
			{
				$this->validation_errors['key_length'] = 'Key needs to be 40 characters long!';
				
			}			
			
			if (preg_match('/[^0-9a-f]/', $key))
			{
				$this->validation_errors['key_chars'] = 'This Key is not correct and includes disallowed characters. <br />Please copy the key from the installation page and paste into the field below.';
				
			}
			
			$options['key_pass'] = FALSE;
			
			if (count($this->validation_errors) < 1) {
				$options['key_pass'] = TRUE;
				$this->update_options($options);
			}
            include('addictivepoints_options_form.php');
        }
		
		/** Method
        * Use: Admin page for Addictive Points, including validation checking.
        * @param1: NULL
        * Return: NONE
        *******************************/
        public function menu()
        {
            add_options_page('<strong>addictive</strong>points Options', '<strong>addictive</strong>points', 'manage_options', $this->plugin_id.'-plugin', array(&$this, 'options_page'));
        }
    }

    // Create a new plugin instantiation.
    $AddictivePoints = new AddictivePoints('addictivepoints');
}

// ========================================= oOo =========================================

function log_me($message) {
    if (WP_DEBUG === true) {
        if (is_array($message) || is_object($message)) {
            error_log(print_r($message, true));
        } else {
            error_log($message);
        }
    }
}

function apoints_js_code(){
	$ap_js_code = <<<APCODE
	
	<script type='text/javascript'>
	
	/* addictivepoints API For Wordpress Installations*/
	
	var _apapi = _apapi || {};
	_apapi.settings = {
			key:'_APOINTS_KEY_DEF_',
			position: {x:'_APOINTS_X_POS_', y:'_APOINTS_Y_POS_'}};
	
	(function(){
			var apoints = document.createElement('script');
			apoints.type = 'text/javascript'; apoints.async = true;
			apoints.src =
			('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.addictivepoints.com/api/js/addictive-points.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(apoints, s);})();
	
	/* End addictivepoints API for Wordpress*/
	
	</script>
APCODE;
	return $ap_js_code;
}

// ========================================= oOo =========================================


?>
