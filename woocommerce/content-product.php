<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
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

global $product, $catalog_mode, $woocommerce_loop;

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}
	
$options = get_option('sf_neighborhood_options');
$product_overlay_transition = $options['product_overlay_transition'];
$overlay_transition_type = "";

if (isset($options['overlay_transition_type'])) {
	$overlay_transition_type = $options['overlay_transition_type'];
}

$enable_product_desc = false;
if ( isset( $options['enable_product_desc'] ) ) {
	$enable_product_desc = $options['enable_product_desc'];
}
$product_description = neighborhood_get_post_meta($post->ID, 'sf_product_short_description', true);
if ($product_description == "") {
	$product_description = $post->post_excerpt;
}

?>

<?php if ( version_compare( WC_VERSION, '3.6', '>=' ) ) { ?>
<li <?php wc_product_class( '', $product ); ?>>
<?php } else if ( version_compare( WC_VERSION, '3.4', '>=' ) ) { ?>
<li <?php wc_product_class(); ?>>
<?php } else { ?>
<li <?php post_class(); ?>>
<?php } ?>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<?php if ($product_overlay_transition) {
		if ($overlay_transition_type == "slideleft") { ?>
		<figure class="product-transition-alt">
	<?php } else if ($overlay_transition_type == "fade") { ?>
		<figure class="product-transition-fade">
	<?php } else { ?>
		<figure class="product-transition">
	<?php }
	?>
	<?php } else { ?>
	<figure class="no-transition">
	<?php } ?>
		<?php

			$image_html = "";

			if (is_out_of_stock()) {

				echo '<span class="out-of-stock-badge">' . __( 'Out of Stock', 'neighborhood' ) . '</span>';

			} else if ($product->is_on_sale()) {

				echo apply_filters('woocommerce_sale_flash', '<span class="onsale">'. __( 'Sale!', 'neighborhood' ).'</span>', $post, $product);

			} else if (!$product->get_price()) {

				echo '<span class="free-badge">' . __( 'Free', 'neighborhood' ) . '</span>';

			} else {

				$postdate 		= get_the_time( 'Y-m-d' );			// Post date
				$postdatestamp 	= strtotime( $postdate );			// Timestamped post date
				$newness 		= 7; 	// Newness in days

				if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) { // If the product was published within the newness time frame display the new badge
					echo '<span class="wc-new-badge">' . __( 'New', 'neighborhood' ) . '</span>';
				}

			}

			if ( has_post_thumbnail() ) {
				$image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'woocommerce_thumbnail' );
			}
		?>

		<div class="product-img-wrap">
			<?php
				$attachment_ids = is_callable( array( $product, 'get_gallery_image_ids' ) ) ? $product->get_gallery_image_ids() : $product->get_gallery_attachment_ids();
	
				$img_count = 0;
	
				if ($attachment_ids) {
	
					echo '<div class="product-image">'.$image_html.'</div>';
	
					foreach ( $attachment_ids as $attachment_id ) {
	
						if ( neighborhood_get_post_meta( $attachment_id, '_woocommerce_exclude_image', true ) )
							continue;
	
						echo '<div class="product-image second-image">'.wp_get_attachment_image( $attachment_id, 'woocommerce_thumbnail' ).'</div>';
	
						$img_count++;
	
						if ($img_count == 1) break;
	
					}
	
				} else {
	
					echo '<div class="product-image">'.$image_html.'</div>';
					echo '<div class="product-image second-image">'.$image_html.'</div>';
	
				}
			?>
		</div>
		
		<?php if (!$catalog_mode) { ?>
		<figcaption>
			<div class="shop-actions clearfix">
			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
			</div>
		</figcaption>
		<?php } ?>
		
		<a class="product-hover-link" href="<?php the_permalink(); ?>"></a>
		
	</figure>

	<div class="product-details">
		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php
			if ( function_exists('wc_get_product_category_list') ) {
				$product_id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
				echo wc_get_product_category_list( $product_id, ', ', '<span class="posted_in">', '</span>' );
			}
		?>

		<?php if ($enable_product_desc) { ?>
			<div class="product-desc">
				<?php echo do_shortcode($product_description); ?>
			</div>
		<?php } ?>

	</div>

	<?php
		/**
		 * woocommerce_after_shop_loop_item_title hook
		 *
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );
	?>

</li>