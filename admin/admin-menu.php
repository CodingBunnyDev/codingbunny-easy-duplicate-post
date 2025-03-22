<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Function to add the plugin's settings page to the WordPress admin menu
function cbedp_add_admin_menu() {
    add_options_page(
        __('Easy Duplicate Post Settings', 'coding-bunny-easy-duplicate-post'),
        __('Easy Duplicate Post', 'coding-bunny-easy-duplicate-post'),
        'manage_options',
        'easy-duplicate-post',
        'cbedp_create_admin_page'
    );
}
add_action('admin_menu', 'cbedp_add_admin_menu');

// Function to create the settings page content
function cbedp_create_admin_page() {
    ?>
    <div class="wrap">
        <h1>
            <?php esc_html_e( 'CodingBunny Easy Duplicate Post', 'coding-bunny-easy-duplicate-post' ); ?> 
            <span>v<?php echo esc_html( CBEDP_VERSION ); ?></span>
        </h1>
        <h2 class="nav-tab-wrapper">
            <a href="#options" class="nav-tab nav-tab-active" id="options-tab"><?php esc_html_e( 'Options', 'coding-bunny-easy-duplicate-post' ); ?></a>
            <a href="#permissions" class="nav-tab" id="permissions-tab"><?php esc_html_e( 'Permissions', 'coding-bunny-easy-duplicate-post' ); ?></a>
            <a href="#duplicate-menu" class="nav-tab" id="duplicate-menu-tab"><?php esc_html_e( 'Duplicate Menu', 'coding-bunny-easy-duplicate-post' ); ?></a>
        </h2>
        <div class="cbedp-section">
            <div id="options" class="cbedp-tab-content">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('cbedp_options_group');
                    do_settings_sections('cbedp-options');
                    submit_button();
                    ?>
                </form>
            </div>
            <div id="permissions" class="cbedp-tab-content" style="display:none;">
                <form method="post" action="options.php">
                    <?php
                    settings_fields('cbedp_permissions_group');
                    do_settings_sections('cbedp-permissions');
                    submit_button();
                    ?>
                </form>
            </div>
            <div id="duplicate-menu" class="cbedp-tab-content" style="display:none;">
                <?php cbedp_create_duplicate_menu_page(); ?>
            </div>
        </div>
        <p>
            &copy; <?php echo esc_html(gmdate('Y')); ?> - 
            <?php esc_html_e('Powered by CodingBunny', 'coding-bunny-easy-duplicate-post'); ?> |
            <a href="https://coding-bunny.com/support/" target="_blank" rel="noopener">
                <?php esc_html_e('Support', 'coding-bunny-easy-duplicate-post'); ?>
            </a> |
            <a href="https://coding-bunny.com/documentation/easy-duplicate-post-doc/" target="_blank" rel="noopener">
                <?php esc_html_e('Documentation', 'coding-bunny-easy-duplicate-post'); ?>
            </a> |
            <a href="https://coding-bunny.com/changelog/" target="_blank" rel="noopener">
                <?php esc_html_e('Changelog', 'coding-bunny-easy-duplicate-post'); ?>
            </a>
        </p>
    </div>
    <?php
}

include_once plugin_dir_path(__FILE__) . 'duplicate-menu.php';