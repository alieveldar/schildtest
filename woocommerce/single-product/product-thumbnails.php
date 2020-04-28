<?php
/**
 * Single Product Thumbnails
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-thumbnails.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( version_compare( WC_VERSION, '3.3.3', '>=' ) ) {

	global $product;

	$attachment_ids = $product->get_gallery_image_ids();

	if ( $attachment_ids && $product->get_image_id() ) {
		foreach ( $attachment_ids as $attachment_id ) {
			$image_caption = "";
			$image_meta = neighborhood_get_attachment_meta( $attachment_id );
			if ( isset($image_meta) ) {
			    $image_caption      = esc_attr( $image_meta['caption'] );
			}
			if ( $image_caption != "" ) {

				$main_image = false;
				$flexslider        = (bool) apply_filters( 'woocommerce_single_product_flexslider_enabled', get_theme_support( 'wc-product-gallery-slider' ) );
				$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
				$thumbnail_size    = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
				$image_size        = apply_filters( 'woocommerce_gallery_image_size', $flexslider || $main_image ? 'woocommerce_single' : $thumbnail_size );
				$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
				$thumbnail_src     = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
				$full_src          = wp_get_attachment_image_src( $attachment_id, $full_size );
				$image             = wp_get_attachment_image( $attachment_id, $image_size, false, array(
					'title'                   => get_post_field( 'post_title', $attachment_id ),
					'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
					'data-src'                => $full_src[0],
					'data-large_image'        => $full_src[0],
					'data-large_image_width'  => $full_src[1],
					'data-large_image_height' => $full_src[2],
					'class'                   => $main_image ? 'wp-post-image' : '',
				) );

				$html = '<div data-thumb="' . esc_url( $thumbnail_src[0] ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_src[0] ) . '">' . $image . '</a>';
				if ( $image_caption != "" ) {
		 		    $html .= '<div class="img-caption">' . $image_caption . '</div>';
		 		}
		 		$html .= '</div>';

				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
			} else {
				echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', wc_get_gallery_image_html( $attachment_id  ), $attachment_id );
			}
		}
	}

} else if ( version_compare( WC_VERSION, '2.7', '>=' ) ) { 
	
	global $post, $product;
	
	$attachment_ids = $product->get_gallery_image_ids();
	
	if ( $attachment_ids && has_post_thumbnail() ) {
		foreach ( $attachment_ids as $attachment_id ) {
			$full_size_image  = wp_get_attachment_image_src( $attachment_id, 'full' );
			$thumbnail        = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
			$image_caption = "";
			$image_meta = neighborhood_get_attachment_meta( $attachment_id );
			if ( isset($image_meta) ) {
			    $image_caption      = esc_attr( $image_meta['caption'] );
			}
			
			$attributes = array(
				'title'                   => get_post_field( 'post_title', $attachment_id ),
				'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);
	
			$html  = '<div data-thumb="' . esc_url( $thumbnail[0] ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '" itemprop="image">';
			$html .= wp_get_attachment_image( $attachment_id, 'woocommerce_gallery_thumbnail', false, $attributes );
	 		$html .= '</a>';
 			if ( $image_caption != "" ) {
 			    $html .= '<div class="img-caption">' . $image_caption . '</div>';
 			}
 			$html .= '</div>';
	
			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $attachment_id );
		}
	}
		
} else {
		
	global $post, $product, $woocommerce;
	
	$attachment_ids = array();
	
	if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
	
	$attachment_ids = $product->get_gallery_attachment_ids();
	
	} else {
	
	$attachment_ids = get_posts( array(
		'post_type' 	=> 'attachment',
		'numberposts' 	=> -1,
		'post_status' 	=> null,
		'post_parent' 	=> $post->ID,
		'post_mime_type'=> 'image',
		'orderby'		=> 'menu_order',
		'order'			=> 'ASC'
	) );
	
	}
	
	if ( $attachment_ids ) {
		?>
		<?php
	
			$loop = 0;
			$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
	
			foreach ( $attachment_ids as $attachment_id ) {
	
				$classes = array( 'zoom' );
	
				if ( $loop == 0 || $loop % $columns == 0 )
					$classes[] = 'first';
	
				if ( ( $loop + 1 ) % $columns == 0 )
					$classes[] = 'last';
	
				$image_link = wp_get_attachment_url( $attachment_id );
	
				if ( ! $image_link )
					continue;
					
				$image = wp_get_attachment_image_src( $attachment_id, 'woocommerce_gallery_thumbnail' );
				
				$image_class = esc_attr( implode( ' ', $classes ) );
				$image_title = esc_attr( get_the_title( $attachment_id ) );
				$image_alt = esc_attr( neighborhood_get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true) );
				
				if ($image) {
									
					$image_html = '<img src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" alt="'.$image_alt.'" title="'.$image_title.'" />';
	
					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li>%s</li>', $image_html ), $attachment_id, $post->ID, $image_class );
					
				}
					
				$loop++;
			}
	
		?>
		<?php
	}
	
}