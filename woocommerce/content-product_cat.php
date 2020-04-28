<?php
	/**
	 * The template for displaying product category thumbnails within loops.
	 *
	 * Override this template by copying it to yourtheme/woocommerce/content-product_cat.php
	 *
	 * @author 		WooThemes
	 * @package 	WooCommerce/Templates
	 * @version     2.6.1
	 */
	
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	global $woocommerce_loop, $sidebars;
	
	// Store loop count we're currently on
	if ( empty( $woocommerce_loop['loop'] ) ) {
		$woocommerce_loop['loop'] = 0;
	}
	
    // Classes
    $classes = array();
    
?>
<li <?php wc_product_cat_class( $classes, $category ); ?>>

	<?php do_action( 'woocommerce_before_subcategory', $category ); ?>

	<a href="<?php echo get_term_link( $category->slug, 'product_cat' ); ?>">

		<?php
			/**
			 * woocommerce_before_subcategory_title hook
			 *
			 * @hooked woocommerce_subcategory_thumbnail - 10
			 */
			do_action( 'woocommerce_before_subcategory_title', $category );
		?>
		
		<div class="product-cat-info">
			
			<h3><?php echo esc_html($category->name); ?></h3>
	
			<?php if ( $category->count > 0 ) {
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <span class="count">' . $category->count . ' '. __("items", 'neighborhood') . '</span>', $category );
				}
			?>
			
		</div>

		<?php
			/**
			 * woocommerce_after_subcategory_title hook
			 */
			do_action( 'woocommerce_after_subcategory_title', $category );
		?>

	</a>

	<?php do_action( 'woocommerce_after_subcategory', $category ); ?>

</li>