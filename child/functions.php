<?php

	/*
	*
	*	Neighborhood Functions - Child Theme
	*	------------------------------------------------
	*	These functions will override the parent theme
	*	functions. We have provided some examples below.
	*
	*
	*/

	/* LOAD PARENT THEME STYLES
	================================================== */
	function neighborhood_child_enqueue_styles() {
	    wp_enqueue_style( 'neighborhood-parent-style', get_template_directory_uri() . '/style.css' );

	}
	add_action( 'wp_enqueue_scripts', 'neighborhood_child_enqueue_styles' );


	/* NEW THEME OPTIONS SECTION
	================================================== */
	// function new_section($sections) {
	//     //$sections = array();
	//     $sections[] = array(
	//         'title' => __('A Section added by hook', 'swift-framework-admin'),
	//         'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'swift-framework-admin'),
	//         // Redux ships with the glyphicons free icon pack, included in the options folder.
	//         // Feel free to use them, add your own icons, or leave this blank for the default.
	//         'icon' => trailingslashit(get_template_directory_uri()) . 'options/img/icons/glyphicons_062_attach.png',
	//         // Leave this as a blank section, no options just some intro text set above.
	//         'fields' => array()
	//     );

	//     return $sections;
	// }
	// add_filter('redux-opts-sections-sf_neighborhood_options', 'new_section');

//is_page('admin.php?page=loco')
if (is_admin()) {
    load_plugin_textdomain( 'web-to-print-online-designer', false, '/wp-content/languages/plugins' ) ;
}

function displayAdditionalPriceOption(){
	global $product_object;
	$val=get_post_meta($product_object->get_id(),'_price_display',true);
	woocommerce_wp_text_input(
		array(
			'id'        => '_price_display',
			'value'     => $val,
			'label'     => __( 'Display price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
			'data_type' => 'price',
		)
	);
}
add_action('woocommerce_product_options_pricing','displayAdditionalPriceOption');

function saveDisplayPrice($pid){	
	update_post_meta($pid,'_price_display',$_POST['_price_display']);
}
add_action('save_post_product', 'saveDisplayPrice');

if(!is_admin()){
	function invoicePaymentisAvailable($gateways){
		$user=wp_get_current_user();
		$sign=!is_user_logged_in() || get_user_meta($user->ID,'_user_invoice_available',true)!=1;
		if($sign){
			foreach($gateways as $k=>$gw){
				if($gw=='IGFW\Models\Gateways\IGFW_Invoice_Gateway') unset($gateways[$k]);
			}
		}
	 
		return $gateways;
	}
	 
	add_filter('woocommerce_payment_gateways','invoicePaymentisAvailable',10,1);
}

add_action( 'show_user_profile', 'addInvoicePaymentMethodForUser',5);
add_action( 'edit_user_profile', 'addInvoicePaymentMethodForUser',5);

function addInvoicePaymentMethodForUser( $user ) { ?>
    <h3><?php _e("Add invoice payments method", "neighborhood-child"); ?></h3>
	<?php $payment=get_user_meta($user->ID,'_user_invoice_available',true); ?>
    <table class="form-table">
		<tr>
			<th colspan="2"><input type="checkbox" name="invoice_payment" value="1" id="invoice_payment" <?=($payment==1 ? 'checked="checked"' : '')?>> <label for="invoice_payment"><?php _e("Invoice payment available","neighborhood-child"); ?></label></th>
		</tr>
    </table>
	<?php 
}

add_action( 'personal_options_update', 'saveUserInvoicePayment');
add_action( 'edit_user_profile_update', 'saveUserInvoicePayment');

function saveUserInvoicePayment( $user_id ) {
    if(!current_user_can('edit_user',$user_id)){ return false; }
    update_user_meta($user_id, '_user_invoice_available', ($_POST['invoice_payment']==1 ? 1 : ''));
}

add_filter('woocommerce_order_subtotal_to_display','fixWCSubtotal',10,3);
function fixWCSubtotal($subtotal, $compound, $wc) {
	$tax_display = $tax_display ? $tax_display : get_option( 'woocommerce_tax_display_cart' );
	$subtotal    = 0;

	if ( ! $compound ) {
		foreach ( $wc->get_items() as $item ) {
			$subtotal += $item->get_subtotal();

			if ( 'incl' === $tax_display ) {
				$subtotal += $item->get_subtotal_tax();
			}
		}
		$subtotal=round($subtotal);
		$subtotal = wc_price( $subtotal, array( 'currency' => $wc->get_currency() ) );

		if ( 'excl' === $tax_display && $wc->get_prices_include_tax() && wc_tax_enabled() ) {
			$subtotal .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
		}
	} else {
		if ( 'incl' === $tax_display ) {
			return '';
		}

		foreach ( $wc->get_items() as $item ) {
			$subtotal += $item->get_subtotal();
		}

		// Add Shipping Costs.
		$subtotal += $wc->get_shipping_total();

		// Remove non-compound taxes.
		foreach ( $wc->get_taxes() as $tax ) {
			if ( $tax->is_compound() ) {
				continue;
			}
			$subtotal = $subtotal + $tax->get_tax_total() + $tax->get_shipping_tax_total();
		}

		// Remove discounts.
		$subtotal = $subtotal - $wc->get_total_discount();
		$subtotal = wc_price( $subtotal, array( 'currency' => $wc->get_currency() ) );
	}

	return $subtotal;
}
add_action( 'woocommerce_admin_order_totals_after_tax', 'actuation_total_in_admin', 200 );
function actuation_total_in_admin($order_id){
$order = wc_get_order();
$order->get_total();
}

function general_admin_notice(){
    global $pagenow;
    if ( $pagenow == 'update-core.php' || $pagenow == 'index.php' ) {
         echo '<div class="notice notice-success" style="display:flex;"><img src="'.get_stylesheet_directory_uri().'/images/ivato.png" style="width: 30%; max-width: 140px; height: 50px; margin: 5px 10px 0 0;"><p>Bei Fragen oder Problemen helfen wir Ihnen im Rahmen unseres Wordpress Lifecycle-Management gern weiter.<br>Wenden Sie sich dazu bitte an unseren Support unter <a href="mailto:support@ivato.de">support@ivato.de</a>.<br>Wir werden uns schnellstmöglich mit Ihnen in Verbindung setzen.</p></div>';
    }
}
add_action('admin_notices', 'general_admin_notice');
?>