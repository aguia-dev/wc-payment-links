<?php
/**
 * template: wp-content/plugins/wc-payment-links/app/Views/Pages/checkout/classic.php
*/

if (!defined('ABSPATH') || !function_exists('do_blocks')) {
	require_once __DIR__ . '/classic.php';
	exit;
}
?>

<title><?php the_title(); ?></title>

<?php get_header(); ?>

<div>
	<div class="entry-title" style="margin: 30px 0;">
		<h1><?php the_title(); ?></h1>
	</div>
	<div class="entry-content">
		<?php wc_print_notices(); ?>
		<div class="woocommerce">
			<?php echo do_blocks(get_post_field('post_content', $postId)); ?>
		</div>
	</div>
</div>

<?php get_footer(); ?>
