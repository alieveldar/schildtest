<?php
/**
 * Wishlist page template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 1.0.6
 */
 
?>

<div class="container">

<?php
 
global $wpdb, $yith_wcwl, $woocommerce, $catalog_mode;

$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
$myaccount_page_url = $shop_url = "";
if ( $myaccount_page_id ) {
  $myaccount_page_url = get_permalink( $myaccount_page_id );
}

$shop_page_url = "";
if ( version_compare( WOOCOMMERCE_VERSION, "2.1.0" ) >= 0 ) {
	$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
} else {
	$shop_page_url = get_permalink( wc_get_page_id( 'shop' ) );
}
wc_print_notices();
?>
	<div id="yith-wcwl-messages"></div>
	
	<?php neighborhood_woo_help_bar(); ?>
	
	<div class="my-account-left">
	
		<h4 class="lined-heading"><span><?php esc_html_e("My Account", 'neighborhood'); ?></span></h4>
		<ul class="nav my-account-nav">
			<?php if( is_user_logged_in() ) { ?>
			<li><a href="<?php echo esc_url($myaccount_page_url); ?>"><?php esc_html_e("Back to my account", 'neighborhood'); ?></a></li>	
			<?php } else { ?>
			<li><a href="<?php echo esc_url($shop_page_url); ?>"><?php esc_html_e("Shop", 'neighborhood'); ?></a></li>		
			<li><a href="<?php echo esc_url($myaccount_page_url); ?>"><?php esc_html_e("Create Account / Login", 'neighborhood'); ?></a></li>	
			<?php } ?>
		</ul>
	
	</div>
	
	<div class="my-account-right tab-content">
	
		<?php if ( function_exists( 'YITH_WCWL' ) ) { ?>

			<?php do_action( 'yith_wcwl_before_wishlist_form', $wishlist_meta ); ?>

			<form id="yith-wcwl-form" action="<?php echo esc_attr($form_action); ?>" method="post" class="woocommerce">

			    <?php wp_nonce_field( 'yith-wcwl-form', 'yith_wcwl_form_nonce' ) ?>

			    <?php do_action( 'yith_wcwl_before_wishlist', $wishlist_meta ); ?>

			    <!-- WISHLIST TABLE -->
				<table class="shop_table cart wishlist_table" data-pagination="<?php echo esc_attr( $pagination )?>" data-per-page="<?php echo esc_attr( $per_page )?>" data-page="<?php echo esc_attr( $current_page )?>" data-id="<?php echo esc_attr($wishlist_id); ?>" data-token="<?php echo esc_attr($wishlist_token); ?>">

				    <?php $column_count = 2; ?>

			        <thead>
			        <tr>
				        <?php if( $show_cb ) : ?>

					        <th class="product-checkbox">
						        <input type="checkbox" value="" name="" id="bulk_add_to_cart"/>
					        </th>

				        <?php
					        $column_count ++;
			            endif;
				        ?>

				        <?php if( $is_user_owner ): ?>
					        <th class="product-remove"></th>
				        <?php
				            $column_count ++;
				        endif;
				        ?>

			            <th class="product-thumbnail"></th>

			            <th class="product-name">
			                <span class="nobr"><?php echo apply_filters( 'yith_wcwl_wishlist_view_name_heading', __( 'Product Name', 'neighborhood' ) ) ?></span>
			            </th>

			            <?php if( $show_price ) : ?>

			                <th class="product-price">
			                    <span class="nobr">
			                        <?php echo apply_filters( 'yith_wcwl_wishlist_view_price_heading', __( 'Unit Price', 'neighborhood' ) ) ?>
			                    </span>
			                </th>

			            <?php
				            $column_count ++;
			            endif;
			            ?>

			            <?php if( $show_stock_status ) : ?>

			                <th class="product-stock-status">
			                    <span class="nobr">
			                        <?php echo apply_filters( 'yith_wcwl_wishlist_view_stock_heading', __( 'Stock Status', 'neighborhood' ) ) ?>
			                    </span>
			                </th>

			            <?php
				            $column_count ++;
			            endif;
			            ?>

			            <?php if( $show_last_column ) : ?>

			                <th class="product-add-to-cart"></th>

			            <?php
				            $column_count ++;
			            endif;
			            ?>
			        </tr>
			        </thead>

			        <tbody>
			        <?php
			        if( !empty( $wishlist_items ) ) :
				        $added_items = array();
			            foreach( $wishlist_items as $item ) :
			                global $product, $woocommerce;

				            $item['prod_id'] = yit_wpml_object_id ( $item['prod_id'], 'product', true );

				            if( in_array( $item['prod_id'], $added_items ) ){
					            continue;
				            }

				            $added_items[] = $item['prod_id'];
				            $product = new WC_Product( $item['prod_id'] );
				            $availability = $product->get_availability();
				            $stock_status = isset( $availability['class'] ) ? $availability['class'] : false;
				      //       $image_output_args = array(
					    	// 	'img' => array()
					    	// );
			                if( $product && $product->exists() ) :
				                ?>
			                    <tr id="yith-wcwl-row-<?php echo esc_attr($item['prod_id']); ?>" data-row-id="<?php echo esc_attr($item['prod_id']); ?>">
				                    <?php if( $show_cb ) : ?>
					                    <td class="product-checkbox">
						                    <input type="checkbox" value="<?php echo esc_attr( $item['prod_id'] ) ?>" name="add_to_cart[]" <?php echo ( ! $product->is_type( 'simple' ) ) ? 'disabled="disabled"' : '' ?>/>
					                    </td>
				                    <?php endif ?>

			                        <?php if( $is_user_owner ): ?>
			                        <td class="product-remove"><div> <a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id']) ) ?>" class="remove remove_from_wishlist" title="<?php _e( 'Remove this product', 'neighborhood' ) ?>"><i class="fas fa-times"></i></a></div></td>
			                        <?php endif; ?>

			                        <td class="product-thumbnail">
			                            <a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>">
			                                <?php echo wp_kses_post($product->get_image()); ?>
			                            </a>
			                        </td>

			                        <td class="product-name">
			                            <a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ?></a>
			                            <?php do_action( 'yith_wcwl_table_after_product_name', $item ); ?>
			                        </td>

			                        <?php if( $show_price ) : ?>
			                            <td class="product-price">
			                                <?php
			                                $base_product = $product->is_type( 'variable' ) ? $product->get_variation_regular_price( 'max' ) : $product->get_price();
			                                $price_output_args = array(
									    		'span' => array(),
									    		'del' => array(),
									    		'ins' => array(),
									    		'p' => array()
									    	);
			                                if ( $base_product ) {
			                                	echo wp_kses($product->get_price_html(), $price_output_args);
			                                } else {
			                                	echo esc_html(apply_filters( 'yith_free_text', __( 'Free!', 'neighborhood' ) ));
			                                }
			                                ?>
			                            </td>
			                        <?php endif ?>

			                        <?php if( $show_stock_status ) : ?>
			                            <td class="product-stock-status">
			                                <?php if ($stock_status == 'out-of-stock') {
			                                	echo '<span class="wishlist-out-of-stock">' . __( 'Out of Stock', 'neighborhood' ) . '</span>';
			                                } else {
			                                	echo '<span class="wishlist-in-stock">' . __( 'In Stock', 'neighborhood' ) . '</span>';
			                                }
			                               	?>
			                            </td>
			                        <?php endif ?>

				                    <?php if( $show_last_column ): ?>
			                        <td class="product-add-to-cart">
				                        <!-- Date added -->
				                        <?php
				                        if( $show_dateadded && isset( $item['dateadded'] ) ):
											echo '<span class="dateadded">' . sprintf( __( 'Added on : %s', 'neighborhood' ), date_i18n( get_option( 'date_format' ), strtotime( $item['dateadded'] ) ) ) . '</span>';
				                        endif;
				                        ?>

			                            <!-- Add to cart button -->
			                            <?php if( $show_add_to_cart && isset( $stock_status ) && $stock_status != 'out-of-stock' ): ?>
			                                <?php woocommerce_template_loop_add_to_cart(); ?>
			                            <?php endif ?>

				                        <!-- Change wishlist -->
										<?php if( $available_multi_wishlist && is_user_logged_in() && count( $users_wishlists ) > 1 && $move_to_another_wishlist && $is_user_owner ): ?>
				                        <select class="change-wishlist selectBox">
					                        <option value=""><?php _e( 'Move', 'neighborhood' ) ?></option>
					                        <?php
					                        foreach( $users_wishlists as $wl ):
						                        if( $wl['wishlist_token'] == $wishlist_meta['wishlist_token'] ){
							                        continue;
						                        }

					                        ?>
						                        <option value="<?php echo esc_attr( $wl['wishlist_token'] ) ?>">
							                        <?php
							                        $wl_title = ! empty( $wl['wishlist_name'] ) ? esc_html( $wl['wishlist_name'] ) : esc_html( $default_wishlsit_title );
							                        if( $wl['wishlist_privacy'] == 1 ){
								                        $wl_privacy = __( 'Shared', 'neighborhood' );
							                        }
							                        elseif( $wl['wishlist_privacy'] == 2 ){
								                        $wl_privacy = __( 'Private', 'neighborhood' );
							                        }
							                        else{
								                        $wl_privacy = __( 'Public', 'neighborhood' );
							                        }

							                        echo sprintf( '%s - %s', $wl_title, $wl_privacy );
							                        ?>
						                        </option>
					                        <?php
					                        endforeach;
					                        ?>
				                        </select>
				                        <?php endif; ?>

				                        <!-- Remove from wishlist -->
				                        <?php if( $is_user_owner && $repeat_remove_button ): ?>
			                                <div> <a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id']) ) ?>" class="remove remove_from_wishlist" title="<?php _e( 'Remove this product', 'neighborhood' ) ?>"><i class="fas fa-times"></i></a></div>
			                            <?php endif; ?>
			                        </td>
				                <?php endif; ?>
			                    </tr>
			                <?php
			                endif;
			            endforeach;
			        else: ?>
			            <tr>
			                <td colspan="<?php echo esc_attr( $column_count ) ?>" class="wishlist-empty"><?php echo apply_filters( 'yith_wcwl_no_product_to_remove_message', __( 'No products were added to the wishlist', 'neighborhood' ) ) ?></td>
			            </tr>
			        <?php
			        endif;

			        if( ! empty( $page_links ) ) : ?>
			            <tr class="pagination-row">
			                <td colspan="<?php echo esc_attr( $column_count ) ?>"><?php echo esc_html($page_links) ?></td>
			            </tr>
			        <?php endif ?>
			        </tbody>

			        <tfoot>
			        <tr>
				        <td colspan="<?php echo esc_attr( $column_count ) ?>">
				            <?php if( $show_cb ) : ?>
					            <div class="custom-add-to-cart-button-cotaniner">
					                <a href="<?php echo esc_url( add_query_arg( array( 'wishlist_products_to_add_to_cart' => '', 'wishlist_token' => $wishlist_token ) ) ) ?>" class="button alt" id="custom_add_to_cart"><?php echo apply_filters( 'yith_wcwl_custom_add_to_cart_text', __( 'Add the selected products to the cart', 'neighborhood' ) ) ?></a>
					            </div>
				            <?php endif; ?>

				            <?php if ( $is_user_owner && $show_ask_estimate_button && $count > 0 ): ?>
					            <div class="ask-an-estimate-button-container">
				                    <a href="<?php echo ( $additional_info || ! is_user_logged_in() ) ? '#ask_an_estimate_popup' : $ask_estimate_url ?>" class="btn button ask-an-estimate-button" <?php echo ( $additional_info ) ? 'data-rel="prettyPhoto[ask_an_estimate]"' : '' ?> >
				                    <?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
				                    <?php echo apply_filters( 'yith_wcwl_ask_an_estimate_text', __( 'Ask for an estimate', 'neighborhood' ) ) ?>
				                </a>
					            </div>
				            <?php endif; ?>

					        <?php
					        do_action( 'yith_wcwl_before_wishlist_share', $wishlist_meta );

					        if ( is_user_logged_in() && $is_user_owner && ! $is_private && $share_enabled ){
						        yith_wcwl_get_template( 'share.php', $share_atts );
					        }

					        do_action( 'yith_wcwl_after_wishlist_share', $wishlist_meta );
					        ?>
				        </td>
			        </tr>
			        </tfoot>

			    </table>

			    <?php wp_nonce_field( 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ); ?>

			    <?php if( ! $is_default ): ?>
			        <input type="hidden" value="<?php echo esc_attr($wishlist_token); ?>" name="wishlist_id" id="wishlist_id">
			    <?php endif; ?>

			    <?php do_action( 'yith_wcwl_after_wishlist', $wishlist_meta ); ?>

			</form>

			<?php do_action( 'yith_wcwl_after_wishlist_form', $wishlist_meta ); ?>

			<?php if( $show_ask_estimate_button && ( ! is_user_logged_in() || $additional_info ) ): ?>
				<div id="ask_an_estimate_popup">
					<form action="<?php echo esc_attr($ask_estimate_url); ?>" method="post" class="wishlist-ask-an-estimate-popup">
						<?php if( ! is_user_logged_in() ): ?>
							<label for="reply_email"><?php echo apply_filters( 'yith_wcwl_ask_estimate_reply_mail_label', __( 'Your email', 'neighborhood' ) ) ?></label>
							<input type="email" value="" name="reply_email" id="reply_email">
						<?php endif; ?>
						<?php if( ! empty( $additional_info_label ) ):?>
							<label for="additional_notes"><?php echo esc_html( $additional_info_label ) ?></label>
						<?php endif; ?>
						<textarea id="additional_notes" name="additional_notes"></textarea>

						<button class="btn button ask-an-estimate-button ask-an-estimate-button-popup" >
							<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
							<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_text', __( 'Ask for an estimate', 'neighborhood' ) ) ?>
						</button>
					</form>
				</div>
			<?php endif; ?>

			<a class="continue-shopping accent" href="<?php echo apply_filters( 'woocommerce_continue_shopping_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php _e('Continue shopping', 'neighborhood'); ?></a>

		<?php } ?>
	</div>

</div>
