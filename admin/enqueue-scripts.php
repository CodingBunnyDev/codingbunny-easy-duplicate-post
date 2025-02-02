<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Function to enqueue admin styles and scripts for the plugin
function cbedp_enqueue_styles($hook_suffix) {
    if ( isset( $_GET['page'] ) && $_GET['page'] === 'easy-duplicate-post' ) {
        $css_file = plugin_dir_path( __FILE__ ) . '../assets/css/styles.css';
        if ( file_exists( $css_file ) ) {
            $version = '1.0.0';
            wp_enqueue_style( 'easy-duplicate-post-admin-styles', plugin_dir_url( __FILE__ ) . '../assets/css/styles.css', [], $version );
        }
    }

    $js_file = plugin_dir_path( __FILE__ ) . '../assets/js/admin.js';
    if ( file_exists( $js_file ) ) {
        $version = '1.0.0';
        wp_enqueue_script( 'easy-duplicate-post-admin-js', plugin_dir_url( __FILE__ ) . '../assets/js/admin.js', [], $version, true );
    }
}

add_action('admin_enqueue_scripts', 'cbedp_enqueue_styles');