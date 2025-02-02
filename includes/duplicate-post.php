<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Function to add a duplicate link to post actions
function cbedp_add_duplicate_link($actions, $post) {
    $options = get_option('cbedp_permissions');
    if ( ! is_array($options) ) {
        $options = [
            'allowed_roles' => [],
            'post_types' => []
        ];
    }

    $user = wp_get_current_user();
    $allowed_roles = $options['allowed_roles'] ?? [];
    $has_allowed_role = false;

    foreach ($allowed_roles as $role) {
        if (in_array($role, (array) $user->roles)) {
            $has_allowed_role = true;
            break;
        }
    }

    $allowed_post_types = $options['post_types'] ?? [];
    $is_allowed_post_type = in_array($post->post_type, $allowed_post_types);

    if ($has_allowed_role && $is_allowed_post_type) {
        $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_post_as_draft&post=' . $post->ID, 'duplicate_post_' . $post->ID) . '">' . __('Clone', 'coding-bunny-easy-duplicate-post') . '</a>';
    }

    return $actions;
}
add_filter('post_row_actions', 'cbedp_add_duplicate_link', 10, 2);

// Function to duplicate a post as draft
function cbedp_duplicate_post_as_draft() {
    if ( ! isset($_GET['_wpnonce']) || ! isset($_GET['post']) ) {
        wp_die('Missing required parameters');
    }

    $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));
    $post_id = absint($_GET['post']);

    if ( ! wp_verify_nonce($nonce, 'duplicate_post_' . $post_id) ) {
        wp_die('Nonce verification failed');
    }

    $post = get_post($post_id);
    if ( ! $post ) {
        wp_die('Post not found');
    }

    $options = get_option('cbedp_options');
    if ( ! is_array($options) ) {
        $options = [
            'prefix_title' => '',
            'suffix_title' => '',
            'post_status' => 'draft',
            'redirect_option' => 'edit',
            'post_page_elements' => []
        ];
    }

    $prefix = $options['prefix_title'] ?? '';
    $suffix = $options['suffix_title'] ?? '';
    $post_status = $options['post_status'] ?? 'draft';
    $elements = $options['post_page_elements'] ?? [];
    
    $new_post = [
        'post_title'   => in_array('title', $elements) ? $prefix . $post->post_title . $suffix : 'Untitled',
        'post_content' => in_array('content', $elements) ? $post->post_content : 'No content',
        'post_status'  => $post_status,
        'post_type'    => $post->post_type,
        'post_author'  => in_array('author', $elements) ? $post->post_author : get_current_user_id(),
    ];

    if (in_array('date', $elements)) {
        $new_post['post_date'] = $post->post_date;
        $new_post['post_date_gmt'] = $post->post_date_gmt;
    }

    if (in_array('slug', $elements)) {
        $new_post['post_name'] = $post->post_name;
    }

    if (in_array('excerpt', $elements)) {
        $new_post['post_excerpt'] = $post->post_excerpt;
    }

    if (in_array('featured_image', $elements)) {
        $new_post['post_thumbnail'] = get_post_thumbnail_id($post->ID);
    }

    $new_post_id = wp_insert_post($new_post);

    if (is_wp_error($new_post_id)) {
    wp_die(esc_html('Failed to duplicate post: ' . $new_post_id->get_error_message()));
}

    if ($new_post_id === 0) {
        wp_die('Failed to duplicate post');
    }

    if (in_array('featured_image', $elements)) {
        set_post_thumbnail($new_post_id, $new_post['post_thumbnail']);
    }

    if (in_array('template', $elements)) {
        update_post_meta($new_post_id, '_wp_page_template', get_post_meta($post->ID, '_wp_page_template', true));
    }

    if (in_array('post_format', $elements)) {
        set_post_format($new_post_id, get_post_format($post));
    }

    if (in_array('password', $elements)) {
        $new_post['post_password'] = $post->post_password;
    }

    if (in_array('attachments', $elements)) {
        $attachments = get_children([
            'post_parent' => $post_id,
            'post_type' => 'attachment',
        ]);
        foreach ($attachments as $attachment) {
            $new_attachment = [
                'post_title'     => $attachment->post_title,
                'post_content'   => $attachment->post_content,
                'post_excerpt'   => $attachment->post_excerpt,
                'post_status'    => 'inherit',
                'post_type'      => 'attachment',
                'post_parent'    => $new_post_id,
                'guid'           => $attachment->guid,
                'post_mime_type' => $attachment->post_mime_type,
            ];
            $new_attachment_id = wp_insert_post($new_attachment);
            $attachment_meta = wp_get_attachment_metadata($attachment->ID);
            wp_update_attachment_metadata($new_attachment_id, $attachment_meta);
        }
    }

    if (in_array('children', $elements)) {
        $children = get_children([
            'post_parent' => $post_id,
            'post_type' => $post->post_type,
        ]);
        foreach ($children as $child) {
            $new_child = [
                'post_title'   => $child->post_title,
                'post_content' => $child->post_content,
                'post_status'  => 'draft',
                'post_type'    => $child->post_type,
                'post_author'  => $child->post_author,
                'post_parent'  => $new_post_id,
            ];
            wp_insert_post($new_child);
        }
    }

    if (in_array('comments', $elements)) {
        $comments = get_comments([
            'post_id' => $post_id,
        ]);
        foreach ($comments as $comment) {
            $new_comment = [
                'comment_post_ID' => $new_post_id,
                'comment_author' => $comment->comment_author,
                'comment_content' => $comment->comment_content,
                'comment_author_email' => $comment->comment_author_email,
                'comment_author_url' => $comment->comment_author_url,
                'comment_date' => $comment->comment_date,
                'comment_approved' => $comment->comment_approved,
            ];
            wp_insert_comment($new_comment);
        }
    }

    if (in_array('menu_order', $elements)) {
        $new_post['menu_order'] = $post->menu_order;
    }

    if (in_array('categories', $elements)) {
        $categories = wp_get_post_terms($post_id, 'category', ['fields' => 'ids']);
        wp_set_post_terms($new_post_id, $categories, 'category');
    }

    if (in_array('tags', $elements)) {
        $tags = wp_get_post_terms($post_id, 'post_tag', ['fields' => 'names']);
        wp_set_post_terms($new_post_id, $tags, 'post_tag');
    }

    $redirect_option = $options['redirect_option'] ?? 'edit';

    if ( $redirect_option === 'list' ) {
        wp_redirect(admin_url('edit.php?post_type=' . $post->post_type));
    } else {
        wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
    }
    exit;
}
add_action('admin_action_duplicate_post_as_draft', 'cbedp_duplicate_post_as_draft');