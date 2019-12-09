<?php

class UpsEasyTracking
{
    private static $initiated = false;

    public static function init() {
		if ( ! self::$initiated ) {
			self::init_hooks();
		}
    }

    public static function init_hooks() {
        self::$initiated = true;

        //Define shortcode
        add_shortcode( 'wpups', array('UpsEasyTracking','loadShortCode') );

        //Add custom JS lib to footer
        add_action( 'wp_enqueue_scripts', array('UpsEasyTracking','enqueue_scripts') );

    }

    public static function enqueue_scripts(){
        wp_register_script( 'wp-upseasytracking', plugins_url( 'views/js/custom.js', dirname(__FILE__) ), array('jquery'), false, true );
        wp_enqueue_script( 'wp-upseasytracking');
        //wp_enqueue_script( 'wp-upseasytracking', plugins_url( 'views/js/custom.js', dirname(__FILE__) ), array ( 'jquery' ), 1.0, true);

        wp_localize_script( 'wp-upseasytracking','ajax_object',array( 'wpups_ajaxurl' => admin_url( 'admin-ajax.php' ) )  );
    }

    public static function activation(){
        flush_rewrite_rules();
    }
    public static function deactivation(){
        flush_rewrite_rules();
    }
    public static function uninstall(){
        flush_rewrite_rules();

        //Clean database
        delete_option( '_wpups_accesskey' );
        delete_option( '_wpups_username' );
        delete_option( '_wpups_pass' );
        delete_option( '_wpups_mode' );

    }

    public static function loadShortCode(){
        $html = file_get_contents(WPUPS_PLUGIN_DIR.'views/frontend.php');
        return $html;
    }


}