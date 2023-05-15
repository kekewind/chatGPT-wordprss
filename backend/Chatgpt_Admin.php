<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://gptaipower.com
 * @since      1.0.0
 *
 * @package    Wp_Ai_Content_Generator
 * @subpackage Wp_Ai_Content_Generator/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Ai_Content_Generator
 * @subpackage Wp_Ai_Content_Generator/admin
 * @author     Senol Sahin <senols@gmail.com>
 */

class Chatgpt_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        $screen = get_current_screen();
	    if(strpos($screen->id, 'chatgpt_connect') !== false) {
		    wp_enqueue_style(
			    'materialize_css',
			    plugin_dir_url(__FILE__) . 'css/materialize.min.css',
			    array(),
			    $this->version,
			    'all'
		    );
		    wp_enqueue_style(
			    'chatgpt_admin',
			    plugin_dir_url( __FILE__ ) . 'css/chatgpt_admin.css',
			    array(),
			    'all'
		    );
		    wp_enqueue_style(
			    'materialize_icons',
			    'https://fonts.googleapis.com/icon?family=Material+Icons',
			    array(),
			    'all'
		    );
	    }


    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/chatgpt_admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );


        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'jquery-ui-accordion' );

    }



    function wpaicg_load_db_vaule_js()
    {
        global  $post ;
        include CHATGPT_PLUGIN_DIR.'backend/views/scripts.php';
    }

    public function wpaicg_options_page()
    {
        add_menu_page(
            __( 'CHATGPT AI CONNECT', 'chatgpt_connect' ),
            'CHATGPT AI CONNECT',
            'manage_options',
            'chatgpt_connect',
            array( $this, 'chatgpt_api_settings' ),
            'dashicons-megaphone',
            6
        );
    }

    public function wpaicg_help_menu()
    {
        add_submenu_page(
            'chatgpt_connect',
            'Help',
            'Help',
            'manage_options',
            'wpaicg_help',
            array( $this, 'wpaicg_help_page' )
        );
    }

    public function wpaicg_help_page()
    {
        include WPAICG_PLUGIN_DIR.'admin/views/help/index.php';
    }

    public function chatgpt_api_settings()
    {
        include CHATGPT_PLUGIN_DIR.'backend/views/settings/index.php';
    }

    public static function add_wp_ai_metabox()
    {
        $screens = [ 'post', 'page', 'wporg_cpt' ];
        foreach ( $screens as $screen ) {
            add_meta_box(
                'wpaicg_preview',
                __( 'GPT-3 AI Content Writer & Generator', 'wwu-api' ),
                [ self::class, 'html' ],
                $screen,
                'advanced',
                'default'
            );
        }
    }

    public function wpaicg_set_post_content_()
    {
        wp_send_json( 'success' );
        die;
    }

    /**
     * Save the meta box selections.
     *
     * @param int $post_id  The post ID.
     */
    public static function save( int $post_id )
    {
        $wpaicg_keys = array(
            'wpaicg_settings',
            '_wporg_language',
            '_wporg_preview_title',
            '_wporg_number_of_heading',
            '_wporg_heading_tag',
            '_wporg_writing_style',
            '_wporg_writing_tone',
            '_wporg_modify_headings',
            '_wporg_add_img',
            'wpaicg_image_featured',
            '_wporg_add_tagline',
            '_wporg_add_intro',
            '_wporg_add_conclusion',
            '_wporg_anchor_text',
            '_wporg_target_url',
            '_wporg_generated_text',
            '_wporg_cta_pos',
            '_wporg_target_url_cta',
            'wpaicg_toc',
            'wpaicg_toc_title',
            'wpaicg_toc_title_tag',
            'wpaicg_intro_title_tag',
            'wpaicg_conclusion_title_tag'
        );
        foreach($wpaicg_keys as $wpaicg_key){
            if ( array_key_exists( $wpaicg_key, $_POST ) ) {
                update_post_meta($post_id,$wpaicg_key, \WPAICG\wpaicg_util_core()->sanitize_text_or_array_field($_POST[$wpaicg_key]));
            }
            else{
                delete_post_meta($post_id,$wpaicg_key);
            }
        }
    }

    /**
     * Display the meta box HTML to the user.
     *
     * @param WP_Post $post   Post object.
     */
    public static function html( $post )
    {
        include WPAICG_PLUGIN_DIR.'admin/views/metabox.php';
    }

}