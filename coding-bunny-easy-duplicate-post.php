<?php

/**
* Plugin Name: CodingBunny Easy Duplicate Post
* Plugin URI:  https://coding-bunny.com/easy-duplicate-post/
* Description: Duplicate posts, pages and custom posts using single click.
* Version:     1.0.0
* Requires at least: 6.0
* Requires PHP: 8.0
* Author:      CodingBunny
* Author URI:  https://coding-bunny.com
* Text Domain: coding-bunny-easy-duplicate-post
* Domain Path: /languages
* License URI:  https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
* License:      GPL v2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CODING_BUNNY_EASY_DUPLICATE_POST_VERSION', '1.0.0' );

require_once plugin_dir_path( __FILE__ ) . 'admin/admin-menu.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/settings.php';
require_once plugin_dir_path( __FILE__ ) . 'admin/enqueue-scripts.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/duplicate-post.php';

class EasyDuplicatePost {

    public function __construct() {
        add_action('admin_menu', 'cbedp_add_admin_menu');
        add_action('admin_init', 'cbedp_register_settings');
        add_action('post_row_actions', 'cbedp_add_duplicate_link', 10, 2);
        add_action('page_row_actions', 'cbedp_add_duplicate_link', 10, 2);
        add_action('admin_action_duplicate_post_as_draft', 'cbedp_duplicate_post_as_draft');
        add_action('admin_enqueue_scripts', 'cbedp_enqueue_styles');
    }
}

// Load plugin textdomain for translations
function cbedp_load_textdomain() {
    load_plugin_textdomain( 'coding-bunny-easy-duplicate-post', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'cbedp_load_textdomain' );

// Add settings link on the plugins page
function cbedp_action_links( $links ) {
    if ( is_array( $links ) ) {
        $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=easy-duplicate-post' ) ) . '">' . esc_html__( 'Settings', 'coding-bunny-easy-duplicate-post' ) . '</a>';
        array_unshift( $links, $settings_link );
    }
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cbedp_action_links' );

new EasyDuplicatePost();