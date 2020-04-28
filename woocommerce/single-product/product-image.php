<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( version_compare( WC_VERSION, '3.3.2', '>=' ) ) {

	global $post, $product, $neighborhood_options;
	$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
	$thumbnail_size    = apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
	$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
	$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
	$product_slider_thumbs_pos = "bottom";
	if ( isset( $neighborhood_options['product_slider_thumbs_pos'] ) ) {
			$product_slider_thumbs_pos = $neighborhood_options['product_slider_thumbs_pos'];
	}
	$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'woocommerce-thumb-nav--'. $product_slider_thumbs_pos,
		'images',
	) );
	$image_caption = "";
	$image_meta = neighborhood_get_attachment_meta( $post_thumbnail_id );
	if ( isset($image_meta) ) {
	    $image_caption      = esc_attr( $image_meta['caption'] );
	}
	?>
	<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">

		<?php
		
			if (is_out_of_stock()) {
					
				echo '<span class="out-of-stock-badge product-image-badge">' . __( 'Out of Stock', 'neighborhood' ) . '</span>';
			
			} else if ($product->is_on_sale()) {
					
				echo apply_filters('woocommerce_sale_flash', '<span class="onsale product-image-badge">'.__( 'Sale!', 'neighborhood' ).'</span>', $post, $product);
					
			} else if (!$product->get_price()) {
				
				echo '<span class="free-badge product-image-badge">' . __( 'Free', 'neighborhood' ) . '</span>';
			
			} else {
			
				$postdate 		= get_the_time( 'Y-m-d' );			// Post date
				$postdatestamp 	= strtotime( $postdate );			// Timestamped post date
				$newness 		= 7; 	// Newness in days

				if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) { // If the product was published within the newness time frame display the new badge
					echo '<span class="wc-new-badge product-image-badge">' . __( 'New', 'neighborhood' ) . '</span>';
				}
				
			}
		?>

		<figure class="woocommerce-product-gallery__wrapper">
			<?php
			$attributes = array(
				'title'                   => get_post_field( 'post_title', $post_thumbnail_id ),
				'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2],
			);
			$full_size         = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
			
			if ( $product->get_image_id() && $image_caption != '' ) {
				$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '">';
				$html .= get_the_post_thumbnail( $post->ID, $full_size, $attributes );
				$html .= '</a>';
				if ( $image_caption != "" ) {
				    $html .= '<div class="img-caption">' . $image_caption . '</div>';
				}
				$html .= '</div>';
			} else if ( $product->get_image_id() ) {
				$html  = wc_get_gallery_image_html( $post_thumbnail_id, true );
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'neighborhood' ) );
				$html .= '</div>';
			}
	
			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped
	
			do_action( 'woocommerce_product_thumbnails' );
			?>
		</figure>
	</div>

<?php } else if ( version_compare( WC_VERSION, '2.7', '>=' ) ) {

	global $post, $product, $neighborhood_options;
	$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
	$thumbnail_size    = apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' );
	$post_thumbnail_id = get_post_thumbnail_id( $post->ID );
	$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, $thumbnail_size );
	$placeholder       = has_post_thumbnail() ? 'with-images' : 'without-images';
	$product_slider_thumbs_pos = "bottom";
	if ( isset( $neighborhood_options['product_slider_thumbs_pos'] ) ) {
			$product_slider_thumbs_pos = $neighborhood_options['product_slider_thumbs_pos'];
	}
	$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . $placeholder,
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'woocommerce-thumb-nav--'. $product_slider_thumbs_pos,
		'images',
	) );
	$image_caption = "";
	$image_meta = neighborhood_get_attachment_meta( $post_thumbnail_id );
	if ( isset($image_meta) ) {
	    $image_caption      = esc_attr( $image_meta['caption'] );
	}
	?>
	<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">

		<?php
		
			if (is_out_of_stock()) {
					
				echo '<span class="out-of-stock-badge product-image-badge">' . __( 'Out of Stock', 'neighborhood' ) . '</span>';
			
			} else if ($product->is_on_sale()) {
					
				echo apply_filters('woocommerce_sale_flash', '<span class="onsale product-image-badge">'.__( 'Sale!', 'neighborhood' ).'</span>', $post, $product);
					
			} else if (!$product->get_price()) {
				
				echo '<span class="free-badge product-image-badge">' . __( 'Free', 'neighborhood' ) . '</span>';
			
			} else {
			
				$postdate 		= get_the_time( 'Y-m-d' );			// Post date
				$postdatestamp 	= strtotime( $postdate );			// Timestamped post date
				$newness 		= 7; 	// Newness in days
	
				if ( ( time() - ( 60 * 60 * 24 * $newness ) ) < $postdatestamp ) { // If the product was published within the newness time frame display the new badge
					echo '<span class="wc-new-badge product-image-badge">' . __( 'New', 'neighborhood' ) . '</span>';
				}
				
			}
		?>

		<figure class="woocommerce-product-gallery__wrapper">
			<?php
			$attributes = array(
				'title'                   => get_post_field( 'post_title', $post_thumbnail_id ),
				'data-caption'            => get_post_field( 'post_excerpt', $post_thumbnail_id ),
				'data-src'                => $full_size_image[0],
				'data-large_image'        => $full_size_image[0],
				'data-large_image_width'  => $full_size_image[1],
				'data-large_image_height' => $full_size_image[2]
			);
	
			if ( has_post_thumbnail() ) {
				$html  = '<div data-thumb="' . get_the_post_thumbnail_url( $post->ID, 'shop_thumbnail' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_size_image[0] ) . '" itemprop="image">';
				$html .= get_the_post_thumbnail( $post->ID, 'shop_single', $attributes );
				$html .= '</a>';
				if ( $image_caption != "" ) {
				    $html .= '<div class="img-caption">' . $image_caption . '</div>';
				}
				$html .= '</div>';
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src() ), esc_html__( 'Awaiting product image', 'neighborhood' ) );
				$html .= '</div>';
			}
	
			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( $post->ID ) );
	
			do_action( 'woocommerce_product_thumbnails' );
			?>
		</figure>
	</div>

<?php } else {

	global $post, $woocommerce, $product;
	
	$product_image_width = apply_filters('neighborhood_product_image_width', 800);
	$attachment_ids = array();
	
	$options = get_option('sf_neighborhood_options');
	$product_image_srcset = false;
	if ( isset($options['product_image_srcset']) ) {
		$product_image_srcset = $options['product_image_srcset'];
	}
	
	?>
	<div class="images">
		
		<?php
		
			if (is_out_of_stock()) {
					
				echo '<span class="out-of-stock-badge">' . __( 'Out of Stock', 'neighborhood' ) . '</span>';
			
			} else if ($product->is_on_sale()) {
					
				echo apply_filters('woocommerce_sale_flash', '<span class="onsale">'.__( 'Sale!', 'neighborhood' ).'</span>', $post, $product);
					
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
		?>
		
		<div id="product-img-slider" class="flexslider">
			<ul class="slides">
				<?php
					$video_url = get_post_meta( $post->ID, '_video_url', true );
					if ( $video_url != "" ) {
						echo '<li><div class="video-wrap">' . apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'neighborhood' ) ), $post->ID ) . '</div></li>';
					} else if ( has_post_thumbnail() ) {
						
						$image_id			= get_post_thumbnail_id();
						$image_object		= get_the_post_thumbnail( $post->ID, 'full' );
						$image_meta 		= neighborhood_get_attachment_meta( $image_id );
	
						$caption_html = $image_caption = $image_alt = $image_title = "";
						if ( isset($image_meta) ) {
							$image_caption 		= esc_attr( $image_meta['caption'] );
							$image_title 		= esc_attr( $image_meta['title'] );
							$image_alt 			= esc_attr( $image_meta['alt'] );
						}
	
						$image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
						
						$thumb_image = wp_get_attachment_url( $image_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
						
						if ( $image_caption != "" ) {
							$caption_html = '<div class="img-caption">' . $image_caption . '</div>';
						}
						
						if ( $product_image_srcset ) {
							$image         = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
											'title'	=> get_the_title( get_post_thumbnail_id() )
										) );
							echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<li itemprop="image" data-thumb="%s">%s%s<a href="%s" itemprop="image" class="woocommerce-main-image zoom lightbox" data-rel="ilightbox[product]" data-caption="%s" title="%s" alt="%s"><i class="fas fa-search-plus"></i></a></li>', $thumb_image, $image_html, $caption_html, $image_link, $image_caption, $image_title, $image_alt ), $post->ID );						
						} else {
							$image = aq_resize( $image_link, 800, NULL, true, false);
							if ($image) {
								$image_html = '<img class="product-slider-image" data-zoom-image="'.$image_link.'" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" alt="'.$image_alt.'" title="'.$image_title.'" />';
								
								echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<li itemprop="image" data-thumb="%s">%s%s<a href="%s" itemprop="image" class="woocommerce-main-image zoom lightbox" data-rel="ilightbox[product]" data-caption="%s" title="%s" alt="%s"><i class="fas fa-search-plus"></i></a></li>', $thumb_image, $image_html, $caption_html, $image_link, $image_caption, $image_title, $image_alt ), $post->ID );	
							}	
						}
					}
										
					$loop = 0;
					$columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
					
					$attachment_ids = $product->get_gallery_attachment_ids();
					
					if ( $attachment_ids ) {
			
						foreach ( $attachment_ids as $attachment_id ) {
				
							$classes = array( 'zoom' );
				
							if ( $loop == 0 || $loop % $columns == 0 )
								$classes[] = 'first';
				
							if ( ( $loop + 1 ) % $columns == 0 )
								$classes[] = 'last';
				
							$image_link = wp_get_attachment_url( $attachment_id );
				
							if ( ! $image_link )
								continue;
							
							$thumb_image = wp_get_attachment_url( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
							
							$image_class = esc_attr( implode( ' ', $classes ) );
							$image_meta  = neighborhood_get_attachment_meta( $attachment_id );			
							$image_caption = $image_alt = $image_title = $caption_html = "";
							if ( isset($image_meta) ) {
								$image_caption 		= esc_attr( $image_meta['caption'] );
								$image_title 		= esc_attr( $image_meta['title'] );
								$image_alt 			= esc_attr( $image_meta['alt'] );
							}
	
							if ( $image_caption != "" ) {
								$caption_html = '<div class="img-caption">' . $image_caption . '</div>';
							}
							
							
							if ( $product_image_srcset ) {
								$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), 0, $attr = array(
												'title'	=> $image_title,
												'alt'	=> $image_title
												) );
								echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li data-thumb="%s">%s%s<a href="%s" class="%s lightbox" data-rel="ilightbox[product]" data-caption="%s" title="%s" alt="%s"><i class="fas fa-search-plus"></i></a></li>', $thumb_image, $image_html, $caption_html, $image_link, $image_class, $image_caption, $image_title, $image_alt ), $attachment_id, $post->ID, $image_class );					
							} else {
								$image = aq_resize( $image_link, $product_image_width, NULL, true, false);
								if ($image) {												
									$image_html = '<img class="product-slider-image" data-zoom-image="'.$image_link.'" src="'.$image[0].'" width="'.$image[1].'" height="'.$image[2].'" alt="'.$image_alt.'" title="'.$image_title.'" />';
			
									echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<li data-thumb="%s">%s%s<a href="%s" class="%s lightbox" data-rel="ilightbox[product]" data-caption="%s" title="%s" alt="%s"><i class="fas fa-search-plus"></i></a></li>', $thumb_image, $image_html, $caption_html, $image_link, $image_class, $image_caption, $image_title, $image_alt ), $attachment_id, $post->ID, $image_class );
								
								}
														
							}
							
							$loop++;
						}
					
					}
				?>
			</ul>
		</div>
	
	</div>
	
<?php } ?>
