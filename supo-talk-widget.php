<?php
/**
 * Plugin Name: Supo Talk widget
 * Plugin URI: https://wordpress.org/plugins/supo-talk-widget/
 * Description: Easily add supo talk widget to your website.
 * Version: 0.02
 * Author: supo.online
 * Author URI: https://www.supo.online
 * License: GPL2
 *
 * Text Domain: supo-talk-widget
 * Domain Path: /languages
 *
 * WC requires at least: 2.3.0
 * WC tested up to: 5.1.1
 */

add_action( 'plugins_loaded', 'stwwp_load_textdomain' );
/**
 * Load plugin textdomain.
 */
function stwwp_load_textdomain() {

    load_plugin_textdomain( 'supo-talk-widget', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
}

function stwwp_scripts_basic()
{
    $options = get_option( 'stwwp_supo_talk_widget_option_name' );
    // Register the script like this for a plugin:
    wp_register_script( 'stwwp_supo-talk-widget', 'https://www.supochat.com/api/showwindow.js?website='.$options["stwwp_api_key"], array(), null, false );
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'stwwp_supo-talk-widget' );
}
add_action( 'wp_enqueue_scripts', 'stwwp_scripts_basic' );



class stwwp_SupoTalkWidgetPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'stwwp_add_plugin_page'));
        add_action('admin_init', array($this, 'stwwp_page_init'));
    }

    /**
     * Add options page
     */
    public function stwwp_add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Supo Talk Widget Settings Page',
            __('Supo Talk Widget Settings', "supo-talk-widget"),
            'manage_options',
            'stwwp_supo-talk-widget-admin',
            array( $this, 'stwwp_create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function stwwp_create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'stwwp_supo_talk_widget_option_name' );
        ?>
        <div class="wrap">
            <h1><?php echo __('Supo Talk Widget Settings' , "supo-talk-widget"); ?></h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields( 'stwwp_supo_talk_widget_option_group' );
                do_settings_sections( 'stwwp_supo-talk-widget-admin' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function stwwp_page_init()
    {
        register_setting(
            'stwwp_supo_talk_widget_option_group', // Option group
            'stwwp_supo_talk_widget_option_name', // Option name
            array( $this, 'stwwp_sanitize' ) // Sanitize
        );

        add_settings_section(
            'stwwp_setting_section_id', // ID
            __('Supo Talk Settings', "supo-talk-widget"), // Title
            array( $this, 'stwwp_print_section_info' ), // Callback
            'stwwp_supo-talk-widget-admin' // Page
        );

        add_settings_field(
            'stwwp_api_key', // ID
            __('API KEY', "supo-talk-widget"), // Title
            array( $this, 'stwwp_api_key_callback' ), // Callback
            'stwwp_supo-talk-widget-admin', // Page
            'stwwp_setting_section_id' // Section
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function stwwp_sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['stwwp_api_key'] ) )
            $new_input['stwwp_api_key'] = sanitize_text_field( $input['stwwp_api_key'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function stwwp_print_section_info()
    {
        print __('Enter your API KEY below:', "supo-talk-widget");
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function stwwp_api_key_callback()
    {
        printf(
            '<input type="text" id="stwwp_api_key" name="stwwp_supo_talk_widget_option_name[stwwp_api_key]" value="%s" />',
            isset( $this->options['stwwp_api_key'] ) ? esc_attr( $this->options['stwwp_api_key']) : ''
        );
    }
}

if( is_admin() )
    $stwwp_supo_talk_widget_page = new stwwp_SupoTalkWidgetPage();