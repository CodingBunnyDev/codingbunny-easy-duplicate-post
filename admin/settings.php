<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Register settings for the plugin
function cbedp_register_settings() {
    register_setting('easy_duplicate_post_options_group', 'easy_duplicate_post_options');

    add_settings_section(
        'easy_duplicate_post_main_section',
        null,
        null,
        'easy-duplicate-post-options'
    );

    add_settings_field(
        'elements_to_copy',
        __('Post/page elements', 'coding-bunny-easy-duplicate-post'),
        'cbedp_settings_field_post_page_elements',
        'easy-duplicate-post-options',
        'easy_duplicate_post_main_section'
    );

    add_settings_field(
        'prefix_title',
        __('Title prefix', 'coding-bunny-easy-duplicate-post'),
        'cbedp_settings_field_prefix_title',
        'easy-duplicate-post-options',
        'easy_duplicate_post_main_section'
    );

    add_settings_field(
        'suffix_title',
        __('Title suffix', 'coding-bunny-easy-duplicate-post'),
        'cbedp_settings_field_suffix_title',
        'easy-duplicate-post-options',
        'easy_duplicate_post_main_section'
    );

    add_settings_field(
        'post_status',
        __('Post/page status', 'coding-bunny-easy-duplicate-post'),
        'cbedp_settings_field_post_status',
        'easy-duplicate-post-options',
        'easy_duplicate_post_main_section'
    );

    add_settings_field(
        'redirect_option',
        __('Redirect option', 'coding-bunny-easy-duplicate-post'),
        'cbedp_settings_field_redirect_option',
        'easy-duplicate-post-options',
        'easy_duplicate_post_main_section'
    );

    register_setting('easy_duplicate_post_permissions_group', 'easy_duplicate_post_permissions');

    add_settings_section(
        'easy_duplicate_post_permissions_section',
        null,
        null,
        'easy-duplicate-post-permissions'
    );

    add_settings_field(
        'allowed_roles',
        __('Allowed user roles', 'coding-bunny-easy-duplicate-post'),
        'cbedp_settings_field_allowed_roles',
        'easy-duplicate-post-permissions',
        'easy_duplicate_post_permissions_section'
    );

    add_settings_field(
        'post_types',
        __('Allowed post types', 'coding-bunny-easy-duplicate-post'),
        'cbedp_settings_field_post_types',
        'easy-duplicate-post-permissions',
        'easy_duplicate_post_permissions_section'
    );
}

// Display allowed user roles setting field
function cbedp_settings_field_allowed_roles() {
    $options = get_option('easy_duplicate_post_permissions', [
        'allowed_roles' => ['administrator', 'editor'],
    ]);
    if ( ! is_array($options) ) {
        $options = [];
    }

    $roles = wp_roles()->roles;
    foreach ( $roles as $role_key => $role ) :
        if (in_array($role_key, ['customer', 'subscriber'])) {
            continue; // Skip customer and subscriber roles
        }
    ?>
        <label>
            <input type="checkbox" name="easy_duplicate_post_permissions[allowed_roles][]" value="<?php echo esc_attr($role_key); ?>" <?php echo in_array($role_key, $options['allowed_roles'] ?? []) ? 'checked' : ''; ?> />
            <?php echo esc_html($role['name']); ?>
        </label><br>
    <?php endforeach; ?>
    <p class="cbedp-warning">
        <?php esc_html_e('Warning: users will have the ability to copy, rewrite, and repost all messages, including those of other users.', 'coding-bunny-easy-duplicate-post'); ?>
    </p>
    <p class="cbedp-warning">
        <?php esc_html_e('Passwords and password-protected content may become visible to unauthorized users and visitors.', 'coding-bunny-easy-duplicate-post'); ?>
    </p>
<?php
}

// Display allowed post types setting field
function cbedp_settings_field_post_types() {
    $options = get_option('easy_duplicate_post_permissions', [
        'post_types' => ['post', 'page'],
    ]);
    if ( ! is_array($options) ) {
        $options = [];
    }

    $post_types = get_post_types(['show_ui' => true], 'objects');
    unset($post_types['attachment']); // Exclude attachment post type
    foreach ( $post_types as $post_type ) : ?>
        <label>
            <input type="checkbox" name="easy_duplicate_post_permissions[post_types][]" value="<?php echo esc_attr($post_type->name); ?>" <?php echo in_array($post_type->name, $options['post_types'] ?? []) ? 'checked' : ''; ?> />
            <?php echo esc_html($post_type->label); ?>
        </label><br>
    <?php endforeach; ?>
    <p class="cbedp-description">
        <?php esc_html_e('Select the post types for which you want to activate the plugin.', 'coding-bunny-easy-duplicate-post'); ?>
    </p>
    <p class="cbedp-description">
        <?php esc_html_e('The display of links for custom post types registered by themes or plugins depends on the use of standard WordPress UI elements.', 'coding-bunny-easy-duplicate-post'); ?>
    </p>
<?php
}

// Display post/page status setting field
function cbedp_settings_field_post_status() {
    $options = get_option('easy_duplicate_post_options', [
        'post_status' => 'draft',
    ]);
    if ( ! is_array($options) ) {
        $options = [];
    }

    $statuses = ['draft' => __('Draft', 'coding-bunny-easy-duplicate-post'), 'publish' => __('Publish', 'coding-bunny-easy-duplicate-post'), 'pending' => __('Pending', 'coding-bunny-easy-duplicate-post')];
    ?>
    <select name="easy_duplicate_post_options[post_status]">
        <?php foreach ( $statuses as $value => $label ) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($options['post_status'] ?? 'draft', $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="cbedp-description"><?php esc_html_e('Choose the status for duplicated posts: draft, publish, or pending', 'coding-bunny-easy-duplicate-post'); ?></p>
<?php
}

// Display redirect option setting field
function cbedp_settings_field_redirect_option() {
    $options = get_option('easy_duplicate_post_options', [
        'redirect_option' => 'edit',
    ]);
    if ( ! is_array($options) ) {
        $options = [];
    }

    $redirects = ['edit' => __('Edit Page', 'coding-bunny-easy-duplicate-post'), 'list' => __('List Page', 'coding-bunny-easy-duplicate-post')];
    ?>
    <select name="easy_duplicate_post_options[redirect_option]">
        <?php foreach ( $redirects as $value => $label ) : ?>
            <option value="<?php echo esc_attr($value); ?>" <?php selected($options['redirect_option'] ?? 'edit', $value); ?>>
                <?php echo esc_html($label); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="cbedp-description"><?php esc_html_e('Choose where to redirect after duplicating a post: to the edit page or the list page', 'coding-bunny-easy-duplicate-post'); ?></p>
<?php
}

// Display title prefix setting field
function cbedp_settings_field_prefix_title() {
    $options = get_option('easy_duplicate_post_options', [
        'prefix_title' => 'Copy of ',
    ]);
    if ( ! is_array($options) ) {
        $options = [];
    }
    ?>
    <input type="text" name="easy_duplicate_post_options[prefix_title]" value="<?php echo esc_attr($options['prefix_title'] ?? ''); ?>" />
    <p class="cbedp-description"><?php esc_html_e('Prefix to be added before the title, e.g. "Copy of" (blank for no prefix)', 'coding-bunny-easy-duplicate-post'); ?></p>
<?php
}

// Display title suffix setting field
function cbedp_settings_field_suffix_title() {
    $options = get_option('easy_duplicate_post_options', [
        'suffix_title' => '',
    ]);
    if ( ! is_array($options) ) {
        $options = [];
    }
    ?>
    <input type="text" name="easy_duplicate_post_options[suffix_title]" value="<?php echo esc_attr($options['suffix_title'] ?? ''); ?>" />
    <p class="cbedp-description"><?php esc_html_e('Suffix to be added after the title, e.g. "(dup)" (blank for no suffix)', 'coding-bunny-easy-duplicate-post'); ?></p>
<?php
}

// Display post/page elements setting field
function cbedp_settings_field_post_page_elements() {
    $options = get_option('easy_duplicate_post_options', [
        'post_page_elements' => ['title', 'excerpt', 'content', 'featured_image', 'template', 'post_format', 'menu_order'],
    ]);
    if ( ! is_array($options) ) {
        $options = [];
    }

    $elements = [
        'title' => __('Title', 'coding-bunny-easy-duplicate-post'),
        'date' => __('Date', 'coding-bunny-easy-duplicate-post'),
        'status' => __('Status', 'coding-bunny-easy-duplicate-post'),
        'slug' => __('Slug', 'coding-bunny-easy-duplicate-post'),
        'excerpt' => __('Excerpt', 'coding-bunny-easy-duplicate-post'),
        'content' => __('Content', 'coding-bunny-easy-duplicate-post'),
        'featured_image' => __('Featured Image', 'coding-bunny-easy-duplicate-post'),
        'categories' => __('Categories', 'coding-bunny-easy-duplicate-post'),
        'tags' => __('Tags', 'coding-bunny-easy-duplicate-post'),
        'template' => __('Template', 'coding-bunny-easy-duplicate-post'),
        'post_format' => __('Post format', 'coding-bunny-easy-duplicate-post'),
        'author' => __('Author', 'coding-bunny-easy-duplicate-post'),
        'password' => __('Password', 'coding-bunny-easy-duplicate-post'),
        'attachments' => __('Attachments', 'coding-bunny-easy-duplicate-post'),
        'children' => __('Children', 'coding-bunny-easy-duplicate-post'),
        'comments' => __('Comments', 'coding-bunny-easy-duplicate-post'),
        'menu_order' => __('Menu order', 'coding-bunny-easy-duplicate-post'),
    ];
    foreach ( $elements as $element_key => $element_label ) : ?>
        <label>
            <input type="checkbox" name="easy_duplicate_post_options[post_page_elements][]" value="<?php echo esc_attr($element_key); ?>" <?php echo in_array($element_key, $options['post_page_elements'] ?? []) ? 'checked' : ''; ?> />
            <?php echo esc_html($element_label); ?>
        </label><br>
    <?php endforeach;
}