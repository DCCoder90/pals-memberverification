<?php
/**
 * Plugin Name: Member Verification
 * Description: Displays membership status at /verify/{user_id}. Requires Ultimate Member.
 * Version:     1.1.0
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * Requires Plugins: ultimate-member
 */

defined( 'ABSPATH' ) || exit;

// Bail if Ultimate Member is not active.
add_action( 'plugins_loaded', function () {
    if ( ! defined( 'um_path' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>Member Verification</strong> requires the Ultimate Member plugin to be installed and active.</p></div>';
        } );
    }
} );

// Register the rewrite rule: /verify/123
add_action( 'init', function () {
    add_rewrite_rule( '^verify/(\d+)/?$', 'index.php?member_verify_id=$matches[1]', 'top' );
} );

// Expose the custom query var to WordPress.
add_filter( 'query_vars', function ( $vars ) {
    $vars[] = 'member_verify_id';
    return $vars;
} );

// Compute membership status, build a virtual page, and let the block theme render it.
add_action( 'wp', function () {
    $raw = get_query_var( 'member_verify_id', '' );
    if ( $raw === '' ) {
        return;
    }

    $user_id      = absint( $raw );
    $member_roles = [ 'um_member', 'um_board-member', 'administrator' ];
    $is_member    = false;

    if ( $user_id > 0 ) {
        $user = get_userdata( $user_id );
        if ( $user ) {
            $is_member = (bool) array_intersect( $member_roles, (array) $user->roles );
        }
    }

    $message = $is_member ? 'Member in Good Standing' : 'Not a member';

    global $wp_query, $post;

    $post = new WP_Post( (object) [
        'ID'                    => 0,
        'post_title'            => 'Member Verification',
        'post_content'          => $message,
        'post_excerpt'          => '',
        'post_status'           => 'publish',
        'post_type'             => 'page',
        'post_name'             => 'member-verification',
        'post_author'           => 0,
        'post_parent'           => 0,
        'post_date'             => current_time( 'mysql' ),
        'post_date_gmt'         => current_time( 'mysql', 1 ),
        'post_password'         => '',
        'post_content_filtered' => '',
        'guid'                  => home_url( '/verify/' ),
        'menu_order'            => 0,
        'post_mime_type'        => '',
        'comment_status'        => 'closed',
        'ping_status'           => 'closed',
        'comment_count'         => 0,
        'filter'                => 'raw',
    ] );

    $wp_query->posts       = [ $post ];
    $wp_query->post        = $post;
    $wp_query->post_count  = 1;
    $wp_query->found_posts = 1;
    $wp_query->is_page     = true;
    $wp_query->is_singular = true;
    $wp_query->is_404      = false;
    $wp_query->is_home     = false;
    $wp_query->is_archive  = false;
    $wp_query->is_search   = false;

    setup_postdata( $post );
} );

// Flush rewrite rules on activation so /verify/* works immediately.
register_activation_hook( __FILE__, function () {
    add_rewrite_rule( '^verify/(\d+)/?$', 'index.php?member_verify_id=$matches[1]', 'top' );
    flush_rewrite_rules();
} );

// Clean up rewrite rules on deactivation.
register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );
