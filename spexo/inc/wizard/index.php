<?php

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

get_template_part('inc/wizard/wizard-functions');
get_template_part('inc/wizard/wizard-ajax-api');

add_action('admin_enqueue_scripts', 'tmpcoder_theme_admin_enqueue_script_custom');
function tmpcoder_theme_admin_enqueue_script_custom(){
    $current_screen = get_current_screen();
    if ( isset($current_screen->base) && ($current_screen->base == 'admin_page_tmpcoder-theme-wizard' || $current_screen->base == 'toplevel_page_tmpcoder-theme-wizard' || $current_screen->base == 'appearance_page_tmpcoder-theme-wizard') ){
        $theme_version = wp_get_theme()->get( 'Version' );
        if ( ! is_string( $theme_version ) || '' === $theme_version ) {
            $theme_version = '1.0.0';
        }

        wp_enqueue_style( 'spexo-wizard-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap', array(), $theme_version );
        // Always load source assets for wizard because flow was refactored to one-step.
        wp_enqueue_style( 'tmpcoder-theme-wizard-admin-style', get_template_directory_uri() . '/inc/wizard/css/wizard-style.css', array( 'spexo-wizard-fonts' ), $theme_version );
        wp_enqueue_script( 'tmpcoder-theme-wizard-admin-script', get_template_directory_uri() . '/inc/wizard/js/wizard-script.js', array( 'jquery' ), $theme_version, false );
        
        // Define an array of data to pass to JavaScript
        $my_data = array(
            'tmpcoder_admin_url' => admin_url(),
            'wizard_one_step_flow' => tmpcoder_theme_wizard_is_one_step_flow_enabled() ? '1' : '0',
            'wizard_one_step_action' => 'tmpcoder_theme_wizard_one_step_setup',
            'wizard_one_step_nonce' => esc_js( wp_create_nonce( 'tmpcoder_theme_wizard_one_step_setup' ) ),
            'wizard_setup_processing' => esc_html__( 'Installing and activating required plugins...', 'spexo' ),
            'wizard_setup_failed' => esc_html__( 'Setup failed. Please try again.', 'spexo' ),
            'wizard_button_loading_text' => esc_html__( 'Activating...', 'spexo' ),
            'wizard_theme_version' => esc_js( $theme_version ),
        );

        // Pass the data to the JavaScript file
        wp_localize_script( 'tmpcoder-theme-wizard-admin-script', 'tmpcoderMessages', $my_data );
    }
}


class Tmpcoder_Theme_Setup_Wizard {

    /**
     * @var Tmpcoder_Theme_Setup_Wizard
     */
    private static $_instance;

    /**
     * @return Tmpcoder_Theme_Setup_Wizard
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    function __construct(){

        $wizard_run = get_option( TMPCODER_THEME_SLUG . '_wizard_done', 0 );
        if ( ! $this->is_spexo_addon_plugin_active() ) {
            add_action( 'admin_menu', array($this, 'register_newpage') );
        }

        add_action( 'admin_notices', array($this, 'wizard_admin_notice_success') );
        add_filter( 'admin_body_class', array( $this, 'admin_body_class_wizard_fullscreen' ) );
        add_action( 'admin_head', array( $this, 'wizard_admin_head_fullscreen' ), 1 );
        add_action( 'admin_head', array( $this, 'wizard_admin_menu_icon_css' ), 2 );
    }

    /**
     * Check whether Spexo Addon plugin is active.
     *
     * @return bool
     */
    private function is_spexo_addon_plugin_active() {
        $plugin_basename = 'sastra-essential-addons-for-elementor/sastra-essential-addons-for-elementor.php';

        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( function_exists( 'is_plugin_active' ) && is_plugin_active( $plugin_basename ) ) {
            return true;
        }

        return function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( $plugin_basename );
    }

    /**
     * Full-screen wizard: remove WP admin chrome spacing on html.
     */
    public function wizard_admin_head_fullscreen() {
        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->base, array( 'appearance_page_tmpcoder-theme-wizard', 'admin_page_tmpcoder-theme-wizard', 'toplevel_page_tmpcoder-theme-wizard' ), true ) ) {
            return;
        }
        echo '<style id="spexo-wizard-fullscreen-html">html.wp-toolbar{margin-top:0!important;padding-top:0!important;}</style>';
    }

    /**
     * Keep custom top-level menu icon sized like default WP icons.
     */
    public function wizard_admin_menu_icon_css() {
        echo '<style id="spexo-wizard-menu-icon-css">#adminmenu .toplevel_page_tmpcoder-theme-wizard .wp-menu-image img{width:20px!important;height:20px!important;object-fit:contain;padding:7px 0 0;}</style>';
    }

    /**
     * Body class for full-screen wizard layout.
     *
     * @param string $classes Space-separated body classes.
     * @return string
     */
    public function admin_body_class_wizard_fullscreen( $classes ) {
        $screen = get_current_screen();
        if ( ! $screen || ! in_array( $screen->base, array( 'appearance_page_tmpcoder-theme-wizard', 'admin_page_tmpcoder-theme-wizard', 'toplevel_page_tmpcoder-theme-wizard' ), true ) ) {
            return $classes;
        }
        return $classes . ' spexo-theme-wizard-fullscreen';
    }

    public static function script_suffix() {
        // $dir = is_rtl() ? '-rtl' : '';
        return defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    }

    function wizard_admin_notice_success() {
        if ( isset($_GET['saved']) && $_GET['saved'] == "wizard" ){ // phpcs:ignore WordPress.Security.NonceVerification.Recommended    
            delete_option(TMPCODER_THEME_SLUG.'_wizard_step');
            update_option(TMPCODER_THEME_SLUG.'_wizard_done', 1);
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e( 'Congrats, The Setup Wizard has successfully set up your website.', 'spexo' ); ?></p>
            </div>
            <?php
        }
    }

    function register_newpage(){
        add_menu_page(
            'Wizard',                      // Page title
            'Spexo Wizard',                // Menu title
            'manage_options',              // Capability
            'tmpcoder-theme-wizard',       // Menu slug
            array($this, 'tmpcoder_theme_func'), // Function to display the page
            get_theme_file_uri( '/assets/images/logo-icon.png' ), // Menu icon
            '2'   // Menu position
        );        
    }
    
    function tmpcoder_theme_func(){
        $landing_active = ' spexo-wizard-landing-active';
        ?>
        <div class="wrap tmpcoder-container tmpcoder-theme-wizard spexo-wizard-shell<?php echo esc_attr( $landing_active ); ?>">

            <?php get_template_part( 'inc/wizard/partials/brand-header' ); ?>

            <div class="spexo-wizard-body">
            <div class="theme-wizard-main">
                <ul class="nav-tab-wrapper theme-wizard-nav wp-clearfix">
                    <li class="nav-tab theme-welcome nav-tab-active" data-tab="theme-welcome">
                        <span class="step-number">1</span><?php echo esc_html( sprintf(
                            /* translators: %s is Theme Name */
                          __( 'Welcome %s Theme', 'spexo' ) , ucfirst( TMPCODER_THEME_NAME )) ); ?>
                    </li>
                </ul>
                <div id="theme-welcome" class="tab-content tab-content-theme-welcome active">
                    <?php get_template_part( 'inc/wizard/partials/landing-step' ); ?>
                </div>
                <div class="process-loader hide">
                    <span class="loader-image"></span>
                    <span class="loader-text"></span>
                </div>
            </div>
            </div>
        </div>

        <div class="tmpcoder-skip-theme-wizard-popup-wrap tmpcoder-admin-popup-wrap">
            <div class="tmpcoder-skip-theme-wizard-popup tmpcoder-admin-popup">
                <div id="tmpcoder-skip-theme-wizard-confirm-popup" class="mfp-hide">
                    <h2 class="popup-heading"> <?php esc_html_e('Skip the Setup Wizard?','spexo') ?> </h2>
                    <div class="popup-content">
                         <p class="popup-message"><?php esc_html_e('Heads up! Would you like to continue without completing the setup wizard?', 'spexo') ?></p>
                        <p class="popup-message"><?php echo wp_kses_post(__('You’ll still be able to access the setup wizard later from the <strong>Appearance → Spexo Wizard</strong> menu.', 'spexo')); ?></p>
                        <a class="button button-primary popup-close"><?php esc_html_e('Continue Setup', 'spexo') ?></a>
                        <a class="button button-secondary tmpcoder-skip-theme-wizard-confirm-button"><?php esc_html_e('Yes, Skip', 'spexo') ?></a>
                    </div>
                </div>
            </div>
        </div>

        <?php 
    }    
}

new Tmpcoder_Theme_Setup_Wizard();
