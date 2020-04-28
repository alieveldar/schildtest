<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce_loop, $neighborhood_carouselID;

//$woocommerce_loop['columns'] 	= $columns;
$woocommerce_loop['columns'] = 4;

if ($neighborhood_carouselID == "") {
$neighborhood_carouselID = 1;
} else {
$neighborhood_carouselID++;
}

if ( version_compare( WC_VERSION, '2.7', '>=' ) ) {

	if ( $related_products ) : ?>
	
		<div class="product-carousel spb_content_element related">
		
			<h4 class="lined-heading"><span><?php _e( 'Related Products', 'neighborhood' ); ?></span></h4>
	
			<div class="carousel-wrap">
			
				<ul class="related products carousel-items" id="carousel-<?php echo esc_attr($neighborhood_carouselID); ?>" data-columns="<?php echo esc_attr($woocommerce_loop['columns']); ?>>">
	
					<?php foreach ( $related_products as $related_product ) : ?>
		
						<?php
						 	$post_object = get_post( $related_product->get_id() );
		
							setup_postdata( $GLOBALS['post'] =& $post_object );
		
							wc_get_template_part( 'content', 'product' ); ?>
		
					<?php endforeach; ?>
		
				</ul>
				
				<a href="#" class="carousel-prev"><i class="fas fa-chevron-left"></i></a><a href="#" class="carousel-next"><i class="fas fa-chevron-right"></i></a>
					
			</div>
		
		</div>
	
	<?php endif;
	
} else {

	global $product;
	
	$related = $product->get_related(12);
	
	if ( sizeof( $related ) == 0 ) return;
	
	$args = apply_filters('woocommerce_related_products_args', array(
		'post_type'				=> 'product',
		'ignore_sticky_posts'	=> 1,
		'no_found_rows' 		=> 1,
		'posts_per_page' 		=> 12,
		'orderby' 				=> $orderby,
		'post__in' 				=> $related,
		'post__not_in'			=> array($product->get_id())
	) );
	
	$products = new WP_Query( $args );
	
	if ( $products->have_posts() ) : ?>
	
		<div class="product-carousel spb_content_element">
	
			<h4 class="lined-heading"><span><?php _e( 'Related Products', 'neighborhood' ); ?></span></h4>
			
			<div class="carousel-wrap">
			
				<ul class="related products carousel-items" id="carousel-<?php echo esc_attr($neighborhood_carouselID); ?>" data-columns="<?php echo esc_attr($woocommerce_loop['columns']); ?>>">
			
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
		
						<?php wc_get_template_part( 'content', 'product' ); ?>
		
					<?php endwhile; // end of the loop. ?>
			
				</ul>
		
				<a href="#" class="carousel-prev"><i class="fas fa-chevron-left"></i></a><a href="#" class="carousel-next"><i class="fas fa-chevron-right"></i></a>
				
			</div>
	
		</div>
	
	<?php endif;
	
	global $include_carousel, $include_isotope;
	$include_carousel = true;
	$include_isotope = true;
	
	wp_reset_postdata();

}
