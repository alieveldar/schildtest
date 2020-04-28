<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $post, $product, $catalog_mode;

$options = get_option('sf_neighborhood_options');
if (isset($options['enable_pb_product_pages'])) {
	$enable_pb_product_pages = $options['enable_pb_product_pages'];
} else {
	$enable_pb_product_pages = false;
}

$product_short_description = neighborhood_get_post_meta($post->ID, 'sf_product_short_description', true);

if ($catalog_mode) {
	if (!$product->is_type( 'variable' ) ) {
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}
}
?>

<div class="container">
<?php
	/**
 	 * Hook Woocommerce_before_single_product.
	 *
	 * @hooked woocommerce_show_messages - 10
	 */
	 do_action( 'woocommerce_before_single_product' );
?>
</div>

<?php if ( version_compare( WC_VERSION, '3.6', '>=' ) ) { ?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
<?php } else if ( version_compare( WC_VERSION, '3.4', '>=' ) ) { ?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class(); ?>>
<?php } else { ?>
<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php } ?>

	<div class="entry-title" itemprop="name"><?php the_title(); ?></div>

	<div class="container">
		
		<?php
			/**
			 * woocommerce_show_product_images hook
			 *
			 * @hooked woocommerce_show_product_sale_flash - 10
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="summary entry-summary">
	
			<div class="summary-top clearfix">
	
				<?php wc_get_template( 'single-product/price.php' ); ?>
	
				<?php
					if ( comments_open() ) {
	
						$count = $wpdb->get_var("
						    SELECT COUNT(meta_value) FROM $wpdb->commentmeta
						    LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
						    WHERE meta_key = 'rating'
						    AND comment_post_ID = $post->ID
						    AND comment_approved = '1'
						    AND meta_value > 0
						");
	
						$rating = $wpdb->get_var("
					        SELECT SUM(meta_value) FROM $wpdb->commentmeta
					        LEFT JOIN $wpdb->comments ON $wpdb->commentmeta.comment_id = $wpdb->comments.comment_ID
					        WHERE meta_key = 'rating'
					        AND comment_post_ID = $post->ID
					        AND comment_approved = '1'
					    ");
	
					    if ( $count > 0 ) {
	
					        $average = number_format($rating / $count, 2);
	
							$reviews_text = sprintf(_n('%d Review', '%d Reviews', $count, 'neighborhood'), $count);
	
					        echo '<div class="review-summary"><div class="star-rating" title="'.sprintf(__('Rated %s out of 5', 'neighborhood'), $average).'"><span style="width:'.($average*16).'px"><span class="rating">'.$average.'</span> '.__('out of 5', 'neighborhood').'</span></div><div class="reviews-text">'.$reviews_text.'</div></div>';
	
					    }
					}
				?>
				<?php
					$has_cat = get_the_terms( $post->ID, 'product_cat' );
				?>
				<?php if ($has_cat != 0) { ?>
				<div class="product-navigation">
					<div class="nav-previous"><?php previous_post_link( '%link', '<i class="fas fa-angle-right"></i>', true, '', 'product_cat' ); ?></div>
					<div class="nav-next"><?php next_post_link( '%link', '<i class="fas fa-angle-left"></i>', true, '', 'product_cat' ); ?></div>
				</div>
				<?php } ?>
	
			</div>
	
			<?php if ($product_short_description != "") { ?>
				<div itemprop="description" class="product-short">
					<?php echo do_shortcode($product_short_description); ?>
				</div>
			<?php } else { ?>
				<div class="product-short">
					<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
				</div>
			<?php } ?>
	
			<?php
				/**
				* woocommerce_single_product_summary hook
				*
				* @hooked woocommerce_template_single_title - 5
				* @hooked woocommerce_template_single_price - 10
				* @hooked woocommerce_template_single_excerpt - 20
				* @hooked woocommerce_template_single_add_to_cart - 30
				* @hooked woocommerce_template_single_meta - 40
				* @hooked woocommerce_template_single_sharing - 50
				*/
	
				do_action( 'woocommerce_single_product_summary' );
			?>
	
		</div><!-- .summary -->
	
	</div><!-- .container -->
	
	<?php if ($enable_pb_product_pages) {
		
		// Page container handling
		$pb_fw_mode = false;
		$extra_class = "";
		if ($post) {
			$sidebar_config = neighborhood_get_post_meta($post->ID, 'sf_sidebar_config', true);
			if (isset($_GET['sidebar'])) {
				$sidebar_config = $_GET['sidebar'];
			}
			$pb_active = neighborhood_get_post_meta($post->ID, '_spb_js_status', true);
			if ($sidebar_config != "no-sidebars" || post_password_required() ) {
				$pb_fw_mode = false;
			} else if ($pb_active == "true") {
				$pb_fw_mode = true;
			}
			if ($pb_fw_mode == false) {
				$extra_class = "container";
			}
		}
	
	?>

	<div id="product-display-area" class="<?php echo esc_attr($extra_class); ?> clearfix">

		<?php the_content(); ?>

	</div>

	<?php } ?>
	
	<div class="container product-after-wrap">
	
	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>
	
	</div>

</div><!-- #product-<?php the_ID(); ?> -->

<div class="container">
	<?php do_action( 'woocommerce_after_single_product' ); ?>
</div>