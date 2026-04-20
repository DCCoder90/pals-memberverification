<?php
defined( 'ABSPATH' ) || exit;

$raw          = get_query_var( 'member_verify_id', '' );
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

get_header();
?>

<main id="main" class="site-main">
    <article class="page type-page status-publish">
        <header class="entry-header">
            <h1 class="entry-title">Member Verification</h1>
        </header>
        <div class="entry-content">
            <p><?php echo esc_html( $message ); ?></p>
        </div>
    </article>
</main>

<?php
get_footer();
