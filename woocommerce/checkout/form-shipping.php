<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
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
 * @global WC_Checkout $checkout
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php if ( WC()->cart->needs_shipping_address() === true ) : ?>

	<?php
		if ( empty( $_POST ) ) {

			$ship_to_different_address = get_option( 'woocommerce_ship_to_billing' ) == 'no' ? 1 : 0;
			$ship_to_different_address = apply_filters( 'woocommerce_ship_to_different_address_checked', $ship_to_different_address );

		} else {

			$ship_to_different_address = $checkout->get_value( 'ship_to_different_address' );

		}
	?>

	<p id="ship-to-different-address">
		<input id="ship-to-different-address-checkbox" class="input-checkbox" <?php checked( $ship_to_different_address, 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
		<label for="ship-to-different-address-checkbox" class="checkbox"><?php esc_html_e( 'Ship to a different address?', 'neighborhood' ); ?></label>
	</p>

	<div class="shipping_address">

		<h4 class="lined-heading"><span><?php _e( 'Shipping', 'neighborhood' ); ?></span></h4>

		<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>

			<?php
					$fields = $checkout->get_checkout_fields( 'shipping' );

					foreach ( $fields as $key => $field ) {
						if (version_compare( WC_VERSION, '3.6', '<')) {
							if ( isset( $field['country_field'], $fields[ $field['country_field'] ] ) ) {
								$field['country'] = $checkout->get_value( $field['country_field'] );
							}
						}
						woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
					}
				?>

		<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>

	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>

	<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

		<h3><?php esc_html_e( 'Additional Information', 'neighborhood' ); ?></h3>

	<?php endif; ?>

	<?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>