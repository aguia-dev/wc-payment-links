<?php
/**
 * template: wp-content/plugins/wc-payment-links/app/Views/Pages/checkout/classic.php
*/

if (!defined('ABSPATH')) exit;

get_header();

if (!function_exists('do_blocks')) {
	require_once __DIR__ . '/classic.php';
	exit;
}

echo do_blocks(get_post_field('post_content', $postId));
?>

<?php get_footer(); ?>
