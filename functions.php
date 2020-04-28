<?php

	/* ==================================================
	
	Neighborhood Main Functions
	
	================================================== */
	
	/* VARIABLE DEFINITIONS
	================================================== */ 
	define('NEIGHBORHOOD_TEMPLATE_PATH', get_template_directory());
	define('NEIGHBORHOOD_INCLUDES_PATH', NEIGHBORHOOD_TEMPLATE_PATH . '/includes');
	define('NEIGHBORHOOD_FRAMEWORK_PATH', NEIGHBORHOOD_TEMPLATE_PATH . '/swift-framework');
	define('NEIGHBORHOOD_WIDGETS_PATH', NEIGHBORHOOD_INCLUDES_PATH . '/widgets');
	define('NEIGHBORHOOD_LOCAL_PATH', get_template_directory_uri());


	/* NEIGHBORHOOD FRAMEWORK CHECK
	================================================== */
	if ( ! function_exists( 'neighborhood_swiftframework_check' ) ) {
		function neighborhood_swiftframework_check() {

			if ( defined( 'NEIGHBORHOOD_FRAMEWORK_VERSION' ) || !( current_user_can('editor') || current_user_can('administrator') ) ) {
				return;
			}

			$class = 'notice notice-error';
			$message = __( 'Please install/activate the Neighborhood Framework plugin. If you have not installed the plugin, please go to Appearance > Install Plugins.', 'neighborhood' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		}
		add_action( 'admin_notices', 'neighborhood_swiftframework_check' );
	}
	
	/* META BOX CHECK
	================================================== */
	if ( ! function_exists( 'neighborhood_metabox_check' ) ) {
		function neighborhood_metabox_check() {

			if ( class_exists( 'RW_Meta_Box' ) || !( current_user_can('editor') || current_user_can('administrator') ) ) {
				return;
			}

			$class = 'notice notice-error';
			$message = __( 'Please install/activate the Meta Box plugin. If you have not installed the plugin, please go to Appearance > Install Plugins.', 'atelier' );

			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
		}
		add_action( 'admin_notices', 'neighborhood_metabox_check' );
	}
	
	/* CHECK WOOCOMMERCE IS ACTIVE
	================================================== */ 
	if ( ! function_exists( 'neighborhood_woocommerce_activated' ) ) {
		function neighborhood_woocommerce_activated() {
			if ( class_exists( 'woocommerce' ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/* CHECK WPML IS ACTIVE
	================================================== */ 
	if ( ! function_exists( 'neighborhood_wpml_activated' ) ) {
		function neighborhood_wpml_activated() {
			if ( function_exists('icl_object_id') ) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/* INCLUDES
	================================================== */
	require_once(NEIGHBORHOOD_INCLUDES_PATH . '/plugins/aq_resizer.php');
	require_once(NEIGHBORHOOD_INCLUDES_PATH . '/taxonomy-meta-class/Tax-meta-class.php');
	include(NEIGHBORHOOD_INCLUDES_PATH . '/plugin-includes.php');	
	include(NEIGHBORHOOD_INCLUDES_PATH . '/plugins/love-it-pro/love-it-pro.php');
	
	
	/* THEME OPTIONS FRAMEWORK
	================================================== */ 
	require_once (NEIGHBORHOOD_FRAMEWORK_PATH . '/sf-options.php');

	
	/* THEME FRAMEWORK FUNCTIONS
	================================================== */
	if ( ! function_exists( 'neighborhood_include_framework' ) ) {
		function neighborhood_include_framework() {
			require_once( NEIGHBORHOOD_FRAMEWORK_PATH . '/swift-framework.php' );
		}
		add_action( 'init', 'neighborhood_include_framework', 10 );
	}

	/* THEME SETUP
	================================================== */
	if (!function_exists('neighborhood_theme_setup')) {
		function neighborhood_theme_setup() { 	

			add_theme_support( 'structured-post-formats', array(
				'audio', 'gallery', 'image', 'link', 'video'
			) );
			add_theme_support( 'post-formats', array(
				'aside', 'chat', 'quote', 'status'
			) );

			add_theme_support( 'title-tag' ); 
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'custom-logo' );
			add_theme_support( 'custom-background' );
			add_theme_support( 'woocommerce' );
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
			set_post_thumbnail_size( 220, 150, true);
			add_image_size( 'widget-image', 94, 70, true);
			add_image_size( 'thumb-image', 600, 450, true);
			add_image_size( 'thumb-image-twocol', 900, 675, true);
			add_image_size( 'thumb-image-onecol', 1280, 960, true);
			add_image_size( 'blog-image', 1280, 9999);
			add_image_size( 'full-width-image', 1280, 720, true);
			add_image_size( 'full-width-image-gallery', 1280, 720, true);

			/* CONTENT WIDTH
			================================================== */
			if ( ! isset( $content_width ) ) $content_width = 1170;
			
			/* LOAD THEME LANGUAGE
			================================================== */
			load_theme_textdomain('neighborhood', NEIGHBORHOOD_TEMPLATE_PATH.'/language');


			/* MIGRATION
			================================================== */
			neighborhood_migrate_old_theme_data();
			
		}
		add_action( 'after_setup_theme', 'neighborhood_theme_setup' );
	}

	/* MIGRATION
	================================================== */
	function neighborhood_migrate_old_theme_data() {
		$options = get_option('sf_neighborhood_options');

		// LOGO
		if (isset($options['logo_upload']) && !has_custom_logo()) {
			
			// get logo data
			$logo = $options['logo_upload'];
			$id = attachment_url_to_postid($logo);
			if ( $id != '' ) {
				// save to customizer
				set_theme_mod( 'custom_logo', $id );
			}

		}
	}

	
	/* LOAD STYLES
	================================================== */
	function neighborhood_enqueue_styles() {  
		
		$options = get_option('sf_neighborhood_options');
		$enable_responsive = $options['enable_responsive'];

		$body_font_option = $options['body_font_option'];
		$headings_font_option = $options['headings_font_option'];
		$menu_font_option = $options['menu_font_option'];
		
		if (($body_font_option == "google") || ($headings_font_option == "google") || ($menu_font_option == "google")) {
			wp_enqueue_style( 'neighborhood-google-fonts', neighborhood_google_fonts_url(), array(), null );
		}

		wp_enqueue_style('bootstrap', NEIGHBORHOOD_LOCAL_PATH . '/css/bootstrap.min.css', array(), NULL, 'all');  
		wp_enqueue_style('font-awesome-v5', NEIGHBORHOOD_LOCAL_PATH .'/css/font-awesome.min.css', array(), '5.2.0', 'all');
		wp_enqueue_style('font-awesome-v4shims', NEIGHBORHOOD_LOCAL_PATH .'/css/v4-shims.min.css', array(), NULL, 'all');
		wp_enqueue_style('neighborhood', get_stylesheet_directory_uri() . '/style.css', array(), NULL, 'all');  

		if ($enable_responsive) {
			wp_enqueue_style('bootstrap-responsive', NEIGHBORHOOD_LOCAL_PATH . '/css/bootstrap-responsive.min.css', array(), NULL, 'all');
			wp_enqueue_style('neighborhood-responsive', NEIGHBORHOOD_LOCAL_PATH . '/css/responsive.css', array(), NULL, 'screen'); 
		}
	}
	add_action('wp_enqueue_scripts', 'neighborhood_enqueue_styles');


	/* GOOGLE FONTS URL FUNCTION
	================================================== */
	function neighborhood_google_fonts_url() {
		$fonts_url = '';
		$font_families = array();
		$options = get_option('sf_neighborhood_options');

		$custom_fonts = $google_font_one = $google_font_two = $google_font_three = $google_font_subset = $subset_output = "";
		
		$body_font_option = $options['body_font_option'];
		if (isset($options['google_standard_font'])) {
			$google_font_one = $options['google_standard_font'];
		}
		$headings_font_option = $options['headings_font_option'];
		if (isset($options['google_heading_font'])) {
			$google_font_two = $options['google_heading_font'];
		}
		$menu_font_option = $options['menu_font_option'];
		if (isset($options['google_menu_font'])) {
			$google_font_three = $options['google_menu_font'];
		}
		
		if (isset($options['google_font_subset'])) {
			$google_font_subset = $options['google_font_subset'];
			$s = 0;
			if (is_array($google_font_subset)) {
				foreach ($google_font_subset as $subset) {
					if ($subset == "none") {
						break;
					}
					if ($s > 0) {
						$subset_output .= ','.$subset;
					} else {
						$subset_output = $subset;
					}
					$s++;
				}
			}
		}

		if ($body_font_option == "google" && $google_font_one != "") {
			$font_families[] = str_replace(' ', '+', $google_font_one);
		}
		if ($headings_font_option == "google" && $google_font_two != "") {
			$font_families[] = str_replace(' ', '+', $google_font_two);
		}
		if ($menu_font_option == "google" && $google_font_three != "") {
			$font_families[] = str_replace(' ', '+', $google_font_three);
		}
		
		$query_args = array(
			'family' => implode( '|', $font_families ),
			'subset' => urlencode( $subset_output ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		
		return esc_url_raw( $fonts_url );
	}
	

	/* FRONTEND SCRIPTS
	================================================== */
	function neighborhood_enqueue_scripts() {

		$options = get_option('sf_neighborhood_options');
		$gmaps_api_key = "";
		if (isset($options['gmaps_api_key'])) {
			$gmaps_api_key = $options['gmaps_api_key'];
		}

		// ENQUEUE		
		wp_enqueue_script('bootstrap', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/bootstrap.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('jquery-transit', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery.transit.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('jquery-hoverIntent', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery.hoverIntent.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('jquery-easing', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery.easing.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('jquery-ui', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery-ui-1.10.2.custom.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('flexslider', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery.flexslider-min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('lightslider', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/lightslider.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('stellar', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery.stellar.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('ilightbox', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/ilightbox.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('fitvids', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery.fitvids.js', array('jquery'), NULL , TRUE);

		wp_enqueue_script('imagesloaded', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/imagesloaded.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('isotope', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/jquery.isotope.min.js', array('jquery'), NULL, TRUE);
		wp_enqueue_script('owlcarousel', NEIGHBORHOOD_LOCAL_PATH . '/js/lib/owl.carousel.min.js', array('jquery'), NULL, TRUE);

		if ( $gmaps_api_key != "" ) {
			wp_enqueue_script('google-maps', '//maps.google.com/maps/api/js?key=' . $gmaps_api_key, array('jquery'), NULL, TRUE);
		}

		if (!is_admin()) {
			wp_enqueue_script('neighborhood', NEIGHBORHOOD_LOCAL_PATH . '/js/functions.js', array('jquery'), NULL, TRUE);
		}

		if (is_singular()) {
			wp_enqueue_script('comment-reply');
		}
	}
	add_action('wp_enqueue_scripts', 'neighborhood_enqueue_scripts');
	

	/* ADMIN SCRIPT
	================================================== */
	function neighborhood_admin_scripts() {
		wp_enqueue_script('admin-functions', get_template_directory_uri() . '/js/sf-admin.js', array('jquery'), '1.0', TRUE);
	}
	add_action('admin_init', 'neighborhood_admin_scripts');
	
	
	/* PERFORMANCE FRIENDLY GET META FUNCTION
	================================================== */
	if ( !function_exists( 'neighborhood_get_post_meta' ) ) {
		function neighborhood_get_post_meta( $id, $key = "", $single = false ) {

			$GLOBALS['neighborhood_post_meta'] = isset( $GLOBALS['neighborhood_post_meta'] ) ? $GLOBALS['neighborhood_post_meta'] : array();
			if ( ! isset( $id ) ) {
				return;
			}
			if ( ! is_array( $id ) ) {
				if ( ! isset( $GLOBALS['neighborhood_post_meta'][ $id ] ) ) {
	                //$GLOBALS['neighborhood_post_meta'][ $id ] = array();
					$GLOBALS['neighborhood_post_meta'][ $id ] = get_post_meta( $id );
				}
				if ( ! empty( $key ) && isset( $GLOBALS['neighborhood_post_meta'][ $id ][ $key ] ) && ! empty( $GLOBALS['neighborhood_post_meta'][ $id ][ $key ] ) ) {
					if ( $single ) {
						return maybe_unserialize( $GLOBALS['neighborhood_post_meta'][ $id ][ $key ][0] );
					} else {
						return array_map( 'maybe_unserialize', $GLOBALS['neighborhood_post_meta'][ $id ][ $key ] );
					}
				}

				if ( $single ) {
					return '';
				} else {
					return array();
				}

			}

			return get_post_meta( $id, $key, $single );
		}
	}


	/* MAINTENANCE MODE
	================================================== */
	if ( ! function_exists( 'neighborhood_maintenance_mode' ) ) {
		function neighborhood_maintenance_mode() {
			$options = get_option('sf_neighborhood_options');
			$custom_logo        = "";
			$custom_logo_output = $maintenance_mode = "";
			if ( isset( $options['custom_admin_login_logo'] ) ) {
				$custom_logo = $options['custom_admin_login_logo'];
			}
			if ( isset( $custom_logo ) ) {
				$custom_logo_output = '<img src="' . $custom_logo . '" alt="maintenance" style="margin: 0 auto; display: block;" />';
			} else {
				$custom_logo_output = '<img src="' . get_template_directory_uri() . '/images/custom-login-logo.png" alt="maintenance" style="margin: 0 auto; display: block;" />';
			}

			if ( isset( $options['enable_maintenance'] ) ) {
				$maintenance_mode = $options['enable_maintenance'];
			} else {
				$maintenance_mode = false;
			}

			if ( $maintenance_mode == 2 ) {

				$holding_page     = __( $options['maintenance_mode_page'], 'neighborhood' );
				$current_page_URL = neighborhood_current_page_url();
				$holding_page_URL = get_permalink( $holding_page );

				if ( $current_page_URL != $holding_page_URL ) {
					if ( ! current_user_can( 'edit_themes' ) || ! is_user_logged_in() ) {
						wp_redirect( $holding_page_URL );
						exit;
					}
				}

			} else if ( $maintenance_mode == 1 ) {
				if ( ! current_user_can( 'edit_themes' ) || ! is_user_logged_in() ) {
					wp_die( $custom_logo_output . '<p style="text-align:center">' . __( 'We are currently in maintenance mode, please check back shortly.', 'neighborhood' ) . '</p>', get_bloginfo( 'name' ) );
				}
			}
		}
		add_action( 'get_header', 'neighborhood_maintenance_mode' );
	}
	
	
	/* GET CURRENT PAGE URL
	================================================== */
	function neighborhood_current_page_url() {
		global $wp;
		if ($wp) {
			$current_url = home_url( $wp->request );
			return trailingslashit( $current_url );
		}
	}	
	
	/* REVSLIDER RETURN FUNCTION
	================================================== */
	function return_slider($revslider_shortcode) {
		ob_start();
		putRevSlider($revslider_shortcode);
		return ob_get_clean();
	}


	/* ADMIN CUSTOM POST TYPE ICONS
	================================================== */
	
	add_action( 'admin_head', 'neighborhood_admin_css' );
	function neighborhood_admin_css() {
		?>

		<?php
	 		// Alt Background
		$options = get_option('sf_neighborhood_options');
		$section_divide_color = get_option('section_divide_color', '#e4e4e4');
		$alt_one_bg_color = $options['alt_one_bg_color'];
		$alt_one_text_color = $options['alt_one_text_color'];
		if (isset($options['alt_one_bg_image'])) {
			$alt_one_bg_image = $options['alt_one_bg_image'];
		}
		$alt_one_bg_image_size = $options['alt_one_bg_image_size'];
		$alt_two_bg_color = $options['alt_two_bg_color'];
		$alt_two_text_color = $options['alt_two_text_color'];
		if (isset($options['alt_two_bg_image'])) {
			$alt_two_bg_image = $options['alt_two_bg_image'];
		}
		$alt_two_bg_image_size = $options['alt_two_bg_image_size'];
		$alt_three_bg_color = $options['alt_three_bg_color'];
		$alt_three_text_color = $options['alt_three_text_color'];
		if (isset($options['alt_three_bg_image'])) {
			$alt_three_bg_image = $options['alt_three_bg_image'];
		}
		$alt_three_bg_image_size = $options['alt_three_bg_image_size'];
		$alt_four_bg_color = $options['alt_four_bg_color'];
		$alt_four_text_color = $options['alt_four_text_color'];
		if (isset($options['alt_four_bg_image'])) {
			$alt_four_bg_image = $options['alt_four_bg_image'];
		}
		$alt_four_bg_image_size = $options['alt_four_bg_image_size'];
		$alt_five_bg_color = $options['alt_five_bg_color'];
		$alt_five_text_color = $options['alt_five_text_color'];
		if (isset($options['alt_five_bg_image'])) {
			$alt_five_bg_image = $options['alt_five_bg_image'];
		}
		$alt_five_bg_image_size = $options['alt_five_bg_image_size'];
		$alt_six_bg_color = $options['alt_six_bg_color'];
		$alt_six_text_color = $options['alt_six_text_color'];
		if (isset($options['alt_six_bg_image'])) {
			$alt_six_bg_image = $options['alt_six_bg_image'];
		}
		$alt_six_bg_image_size = $options['alt_six_bg_image_size'];
		$alt_seven_bg_color = $options['alt_seven_bg_color'];
		$alt_seven_text_color = $options['alt_seven_text_color'];
		if (isset($options['alt_seven_bg_image'])) {
			$alt_seven_bg_image = $options['alt_seven_bg_image'];
		}
		$alt_seven_bg_image_size = $options['alt_seven_bg_image_size'];
		$alt_eight_bg_color = $options['alt_eight_bg_color'];
		$alt_eight_text_color = $options['alt_eight_text_color'];
		if (isset($options['alt_eight_bg_image'])) {
			$alt_eight_bg_image = $options['alt_eight_bg_image'];
		}
		$alt_eight_bg_image_size = $options['alt_eight_bg_image_size'];
		$alt_nine_bg_color = $options['alt_nine_bg_color'];
		$alt_nine_text_color = $options['alt_nine_text_color'];
		if (isset($options['alt_nine_bg_image'])) {
			$alt_nine_bg_image = $options['alt_nine_bg_image'];
		}
		$alt_nine_bg_image_size = $options['alt_nine_bg_image_size'];
		$alt_ten_bg_color = $options['alt_ten_bg_color'];
		$alt_ten_text_color = $options['alt_ten_text_color'];
		if (isset($options['alt_ten_bg_image'])) {
			$alt_ten_bg_image = $options['alt_ten_bg_image'];
		}
		$alt_ten_bg_image_size = $options['alt_ten_bg_image_size'];  	
		?>

		<style type="text/css" media="screen">

			#menu-posts-slide .wp-menu-image img {
				width: 16px;
			}
			#toplevel_page_absfa_theme_options .wp-menu-image img {
				width: 11px;
				margin-top: -2px;
				margin-left: 3px;
			}
			.toplevel_page_absfa_theme_options #adminmenu li#toplevel_page_absfa_theme_options.wp-has-current-submenu a.wp-has-current-submenu, .toplevel_page_absfa_theme_options #adminmenu #toplevel_page_absfa_theme_options .wp-menu-arrow div, .toplevel_page_absfa_theme_options #adminmenu #toplevel_page_absfa_theme_options .wp-menu-arrow {
				background: #222;
				border-color: #222;
			}
			#wpbody-content {
				min-height: 815px;
			}
			/* META BOX CUSTOM */
			.rwmb-field {
				margin: 10px 0;
			}
			.rwmb-field > h3 {
				margin: 10px 0;
				border-bottom: 1px solid #e4e4e4;
				padding-bottom: 10px !important;
			}
			.rwmb-label label {
				padding-right: 10px;
				vertical-align: top;
			}
			.rwmb-checkbox-wrapper .description {
				display: block;
				margin: 6px 0 8px;
			}
			.rwmb-input .rwmb-slider {
				background: #f7f7f7;
				border: 1px solid #e3e3e3;
			}
			.meta-box-sortables select, .rwmb-input > input, .rwmb-media-view .rwmb-add-media {
				margin-bottom: 5px;
			}
			.meta-altbg-preview {
				max-width: 200px;
				padding: 10px;
				text-align: center;
				margin-left: 25%;
			}
			.rwmb-input .rwmb-slider {
				background: #f7f7f7;
				border: 1px solid #e3e3e3;
			}

			.rwmb-slider.ui-slider-horizontal .ui-slider-range-min {
				background: #fe504f!important;
			}

			.rwmb-slider-value-label {
				vertical-align: 0;
			}

			.rwmb-images img {
				max-width: 150px;
				max-height: 150px;
				width: auto;
				height: auto;
			}

			h2.meta-box-section {
				border-bottom: 1px solid #e4e4e4;
				padding-bottom: 10px !important;
				margin-top: 20px !important;
				font-size: 18px !important;
				color: #444;
			}

			.rwmb-meta-box div:first-child h2.meta-box-section {
				margin-top: 0 !important;
			}

			/* META BOX TABS */
			.sf-meta-tabs-wrap {
				height: auto;
				overflow: hidden;
			}

			.rwmb-meta-box {
				padding: 20px 10px;
			}

			.sf-meta-tabs-wrap.all-hidden {
				display: none;
			}

			#sf-tabbed-meta-boxes {
				position: relative;
				z-index: 1;
				float: right;
				width: 80%;
				border-left: 1px solid #e3e3e3;
			}

			#sf-tabbed-meta-boxes > div > .hndle, #sf-tabbed-meta-boxes > div > .handlediv {
				display: none !important;
			}

			#sf-tabbed-meta-boxes .inside {
				display: block !important;
			}

			#sf-tabbed-meta-boxes > div {
				border-left: 0;
				border-right: 0;
				border-bottom: 0;
				margin-bottom: 0;
				padding-bottom: 20px;
			}

            /*#sf-tabbed-meta-boxes > div.hide-if-js {
                   display: none!important;
                   }*/
                   #sf-meta-box-tabs {
                   	margin: 0;
                   	width: 20%;
                   	position: relative;
                   	z-index: 2;
                   	float: left;
                   	margin-right: -1px;
                   }

                   #sf-meta-box-tabs li {
                   	margin-bottom: -1px;
                   }

                   #sf-meta-box-tabs li.user-hidden {
                   	display: none !important;
                   }

                   #sf-meta-box-tabs li > a {
                   	display: block;
                   	background: #f7f7f7;
                   	padding: 12px;
                   	line-height: 150%;
                   	border: 1px solid #e5e5e5;
                   	-webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                   	box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
                   	text-decoration: none;
                   }

                   #sf-meta-box-tabs li > a:hover {
                   	color: #222;
                   	background: #fff;
                   }

                   #sf-meta-box-tabs li > a.active {
                   	border-right-color: #fff;
                   	background: #fff;
                   	box-shadow: none;
                   }

                   .closed #sf-meta-box-tabs, .closed #sf-tabbed-meta-boxes {
                   	display: none;
                   }

                   #sf-tabbed-meta-boxes .inside .rwmb-meta-box .rwmb-field:first-of-type > h3 {
                   	margin-top: -10px;
                   }

                   <?php
                   echo "\n".'/*========== Asset Background Styles ==========*/'."\n";
                   echo '.alt-one {background-color: '.$alt_one_bg_color.';}'. "\n";
                   if (isset($options['alt_one_bg_image']) && $alt_one_bg_image != "") {
                   	if ($alt_one_bg_image_size == "cover") {
                   		echo '.alt-one {background-image: url('.$alt_one_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-one {background-image: url('.$alt_one_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-one {color: '.$alt_one_text_color.';}'. "\n";
                   echo '.alt-one.full-width-text:after {border-top-color:'.$alt_one_bg_color.';}'. "\n";
                   echo '.alt-two {background-color: '.$alt_two_bg_color.';}'. "\n";
                   if (isset($options['alt_two_bg_image']) && $alt_two_bg_image != "") {
                   	if ($alt_two_bg_image_size == "cover") {
                   		echo '.alt-two {background-image: url('.$alt_two_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-two {background-image: url('.$alt_two_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-two {color: '.$alt_two_text_color.';}'. "\n";
                   echo '.alt-two.full-width-text:after {border-top-color:'.$alt_two_bg_color.';}'. "\n";	
                   echo '.alt-three {background-color: '.$alt_three_bg_color.';}'. "\n";
                   if (isset($options['alt_three_bg_image']) && $alt_three_bg_image != "") {
                   	if ($alt_three_bg_image_size == "cover") {
                   		echo '.alt-three {background-image: url('.$alt_three_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-three {background-image: url('.$alt_three_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-three {color: '.$alt_three_text_color.';}'. "\n";
                   echo '.alt-three.full-width-text:after {border-top-color:'.$alt_three_bg_color.';}'. "\n";	
                   echo '.alt-four {background-color: '.$alt_four_bg_color.';}'. "\n";
                   if (isset($options['alt_four_bg_image']) && $alt_four_bg_image != "") {
                   	if ($alt_four_bg_image_size == "cover") {
                   		echo '.alt-four {background-image: url('.$alt_four_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-four {background-image: url('.$alt_four_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-four {color: '.$alt_four_text_color.';}'. "\n";
                   echo '.alt-four.full-width-text:after {border-top-color:'.$alt_four_bg_color.';}'. "\n";	
                   echo '.alt-five {background-color: '.$alt_five_bg_color.';}'. "\n";
                   if (isset($options['alt_five_bg_image']) && $alt_five_bg_image != "") {
                   	if ($alt_five_bg_image_size == "cover") {
                   		echo '.alt-five {background-image: url('.$alt_five_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-five {background-image: url('.$alt_five_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-five {color: '.$alt_five_text_color.';}'. "\n";
                   echo '.alt-five.full-width-text:after {border-top-color:'.$alt_five_bg_color.';}'. "\n";			
                   echo '.alt-six {background-color: '.$alt_six_bg_color.';}'. "\n";
                   if (isset($options['alt_six_bg_image']) && $alt_six_bg_image != "") {
                   	if ($alt_six_bg_image_size == "cover") {
                   		echo '.alt-six {background-image: url('.$alt_six_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-six {background-image: url('.$alt_six_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-six {color: '.$alt_six_text_color.';}'. "\n";
                   echo '.alt-six.full-width-text:after {border-top-color:'.$alt_six_bg_color.';}'. "\n";
                   echo '.alt-seven {background-color: '.$alt_seven_bg_color.';}'. "\n";
                   if (isset($options['alt_seven_bg_image']) && $alt_seven_bg_image != "") {
                   	if ($alt_seven_bg_image_size == "cover") {
                   		echo '.alt-seven {background-image: url('.$alt_seven_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-seven {background-image: url('.$alt_seven_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-seven {color: '.$alt_seven_text_color.';}'. "\n";
                   echo '.alt-seven.full-width-text:after {border-top-color:'.$alt_seven_bg_color.';}'. "\n";
                   echo '.alt-eight {background-color: '.$alt_eight_bg_color.';}'. "\n";
                   if (isset($options['alt_eight_bg_image']) && $alt_eight_bg_image != "") {
                   	if ($alt_eight_bg_image_size == "cover") {
                   		echo '.alt-eight {background-image: url('.$alt_eight_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-eight {background-image: url('.$alt_eight_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-eight {color: '.$alt_eight_text_color.';}'. "\n";
                   echo '.alt-eight.full-width-text:after {border-top-color:'.$alt_eight_bg_color.';}'. "\n";
                   echo '.alt-nine {background-color: '.$alt_nine_bg_color.';}'. "\n";
                   if (isset($options['alt_nine_bg_image']) && $alt_nine_bg_image != "") {
                   	if ($alt_nine_bg_image_size == "cover") {
                   		echo '.alt-nine {background-image: url('.$alt_nine_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-nine {background-image: url('.$alt_nine_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-nine {color: '.$alt_nine_text_color.';}'. "\n";
                   echo '.alt-nine.full-width-text:after {border-top-color:'.$alt_nine_bg_color.';}'. "\n";


                   echo '.alt-ten {background-color: '.$alt_ten_bg_color.';}'. "\n";
                   if (isset($options['alt_ten_bg_image']) && $alt_ten_bg_image != "") {
                   	if ($alt_ten_bg_image_size == "cover") {
                   		echo '.alt-ten {background-image: url('.$alt_ten_bg_image.'); background-repeat: no-repeat; background-position: center center; background-size:cover;}'. "\n";
                   	} else {
                   		echo '.alt-ten {background-image: url('.$alt_ten_bg_image.'); background-repeat: repeat; background-position: center center; background-size:auto;}'. "\n";
                   	}	
                   }
                   echo '.alt-ten {color: '.$alt_nine_text_color.';}'. "\n";
                   echo '.alt-ten.full-width-text:after {border-top-color:'.$alt_ten_bg_color.';}'. "\n";
                   ?>
               </style>

           <?php }


	/* PLUGIN OPTION PARAMS
	================================================== */
	if ( ! function_exists( 'neighborhood_option_parameters' ) ) {
		function neighborhood_option_parameters() {
			$options = get_option('sf_neighborhood_options');
			$lightbox_nav = "default";
			$lightbox_thumbs = "true";
			$lightbox_skin = "light";
			$lightbox_sharing = "true";

			if (isset($options['lightbox_nav'])) {
				$lightbox_nav             = $options['lightbox_nav'];
			}
			if (isset($options['lightbox_thumbs'])) {
				$lightbox_thumbs          = $options['lightbox_thumbs'];
			}
			if (isset($options['lightbox_skin'])) {
				$lightbox_skin            = $options['lightbox_skin'];
			}
			if (isset($options['lightbox_sharing'])) {
				$lightbox_sharing         = $options['lightbox_sharing'];
			}
			?>
			<div id="sf-option-params"
			data-lightbox-nav="<?php echo esc_attr($lightbox_nav); ?>"
			data-lightbox-thumbs="<?php echo esc_attr($lightbox_thumbs); ?>"
			data-lightbox-skin="<?php echo esc_attr($lightbox_skin); ?>"
			data-lightbox-sharing="<?php echo esc_attr($lightbox_sharing); ?>"></div>

			<?php
		}

		add_action( 'wp_footer', 'neighborhood_option_parameters' );
	}
	
	
	/* BETTER SEO PAGE TITLE
	================================================== */
	
	add_filter( 'wp_title', 'filter_wp_title' );
	/**
	 * Filters the page title appropriately depending on the current page
	 *
	 * This function is attached to the 'wp_title' fiilter hook.
	 *
	 * @uses	get_bloginfo()
	 * @uses	is_home()
	 * @uses	is_front_page()
	 */
	function filter_wp_title( $title ) {
		global $page, $paged;

		if ( is_feed() )
			return $title;

		$site_description = get_bloginfo( 'description' );

		$filtered_title = $title . get_bloginfo( 'name' );
		$filtered_title .= ( ! empty( $site_description ) && ( is_home() || is_front_page() ) ) ? ' | ' . $site_description: '';
		$filtered_title .= ( 2 <= $paged || 2 <= $page ) ? ' | ' . sprintf( __( 'Page %s', 'neighborhood' ), max( $paged, $page ) ) : '';

		return $filtered_title;
	}
	
	
	/* SET SIDEBAR GLOBAL
	================================================== */

	function neighborhood_set_sidebar_global($sidebar_config) {
		
		global $sidebars, $neighborhood_sidebar_config;
		
		if (($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar")) {
			$sidebars = 'one-sidebar';
			$neighborhood_sidebar_config = 'one-sidebar';
		} else if ($sidebar_config == "both-sidebars") {
			$sidebars = 'both-sidebars';
			$neighborhood_sidebar_config = 'both-sidebars';
		} else {
			$sidebars = 'no-sidebars';
			$neighborhood_sidebar_config = 'no-sidebars';
		}
	}
	
	
	/* WORDPRESS GALLERY MODS
	================================================== */
	
	add_filter( 'wp_get_attachment_link', 'sant_lightboxadd');

	function sant_lightboxadd($content) {
		$content = preg_replace("/<a/","<a class=\"lightbox\" data-rel='ilightbox[gallery]'",$content,1);
		return $content;
	}
	add_filter( 'gallery_style', 'custom_gallery_styling', 99 );
	
	function custom_gallery_styling() {
		return "<div class='gallery'>";
	}
	
	
	/* WORDPRESS TAG CLOUD WIDGET MODS
	================================================== */
	
	add_filter( 'widget_tag_cloud_args', 'neighborhood_tag_cloud_args' );
	
	function neighborhood_tag_cloud_args( $args ) {
		$args['largest'] = 12;
		$args['smallest'] = 12;
		$args['unit'] = 'px';
		$args['format'] = 'list';
		return $args;
	}
	
	
	/* WORDPRESS CATEGORY WIDGET MODS
	================================================== */
	
	add_filter('wp_list_categories', 'neighborhood_category_widget_mod');
	
	function neighborhood_category_widget_mod($output) {
		$output = str_replace('</a> (',' <span>(',$output);
		$output = str_replace(')',')</span></a> ',$output);
		return $output;
	}
	
	
	/* WORDPRESS ARCHIVES WIDGET MODS
	================================================== */
	
	add_filter('wp_get_archives', 'neighborhood_archives_widget_mod');
	
	function neighborhood_archives_widget_mod($output) {
		$output = str_replace('</a> (',' <span>(',$output);
		$output = str_replace(')',')</span></a> ',$output);
		return $output;
	}

	
	/* GET WOOCOMMERCE FILTERS
	================================================== */
	
	function get_woo_product_filters_array() {
		
		global $woocommerce;
		
		$attribute_array = array();
		
		$transient_name = 'wc_attribute_taxonomies';
		
		if ( false === ( $attribute_taxonomies = get_transient( $transient_name ) ) ) {

			global $wpdb;

			$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies" );

			set_transient( $transient_name, $attribute_taxonomies );
		}

		$attribute_taxonomies = apply_filters( 'woocommerce_attribute_taxonomies', $attribute_taxonomies );
		
		$attribute_array['product_cat'] = __('Product Category', 'neighborhood');
		$attribute_array['price'] = __('Price', 'neighborhood');

		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$attribute_array[$tax->attribute_name] = $tax->attribute_name;
			}
		}
		
		return $attribute_array;	
	}

	
	
	/* TWEET FUNCTIONS
	================================================== */
	
	function neighborhood_get_tweets($twitterID, $count) {

		$content = "";

		if (function_exists('getTweets')) {

			$tweets = getTweets($twitterID, $count);

			if(is_array($tweets)){

				foreach($tweets as $tweet){

					$content .= '<li>';

					if(is_array($tweet) && isset($tweet['text']) && $tweet['text']){

						$content .= '<div class="tweet-text">';

						$the_tweet = $tweet['text'];
				        /*
				        Twitter Developer Display Requirements
				        https://dev.twitter.com/terms/display-requirements
				
				        2.b. Tweet Entities within the Tweet text must be properly linked to their appropriate home on Twitter. For example:
				          i. User_mentions must link to the mentioned user's profile.
				         ii. Hashtags must link to a twitter.com search with the hashtag as the query.
				        iii. Links in Tweet text must be displayed using the display_url
				             field in the URL entities API response, and link to the original t.co url field.
				        */

				        // i. User_mentions must link to the mentioned user's profile.
				             if(isset($tweet['entities']['user_mentions']) && is_array($tweet['entities']['user_mentions'])){
				             	foreach($tweet['entities']['user_mentions'] as $key => $user_mention){
				             		$the_tweet = preg_replace(
				             			'/@'.$user_mention['screen_name'].'/i',
				             			'<a href="http://www.twitter.com/'.$user_mention['screen_name'].'" target="_blank">@'.$user_mention['screen_name'].'</a>',
				             			$the_tweet);
				             	}
				             }

				        // ii. Hashtags must link to a twitter.com search with the hashtag as the query.
				             if(isset($tweet['entities']['hashtags']) && is_array($tweet['entities']['hashtags'])){
				             	foreach($tweet['entities']['hashtags'] as $key => $hashtag){
				             		$the_tweet = preg_replace(
				             			'/#'.$hashtag['text'].'/i',
				             			'<a href="https://twitter.com/search?q=%23'.$hashtag['text'].'&amp;src=hash" target="_blank">#'.$hashtag['text'].'</a>',
				             			$the_tweet);
				             	}
				             }

				        // iii. Links in Tweet text must be displayed using the display_url
				        //      field in the URL entities API response, and link to the original t.co url field.
				             if(isset($tweet['entities']['urls']) && is_array($tweet['entities']['urls'])){
				             	foreach($tweet['entities']['urls'] as $key => $link){
				             		$the_tweet = preg_replace(
				             			'`'.$link['url'].'`',
				             			'<a href="'.$link['url'].'" target="_blank">'.$link['url'].'</a>',
				             			$the_tweet);
				             	}
				             }

				        // Custom code to link to media
				             if(isset($tweet['entities']['media']) && is_array($tweet['entities']['media'])){
				             	foreach($tweet['entities']['media'] as $key => $media){
				             		$the_tweet = preg_replace(
				             			'`'.$media['url'].'`',
				             			'<a href="'.$media['url'].'" target="_blank">'.$media['url'].'</a>',
				             			$the_tweet);
				             	}
				             }

				             $content .= $the_tweet;

				             $content .= '</div>';

				        // 3. Tweet Actions
				        //    Reply, Retweet, and Favorite action icons must always be visible for the user to interact with the Tweet. These actions must be implemented using Web Intents or with the authenticated Twitter API.
				        //    No other social or 3rd party actions similar to Follow, Reply, Retweet and Favorite may be attached to a Tweet.
				        // 4. Tweet Timestamp
				        //    The Tweet timestamp must always be visible and include the time and date. e.g., “3:00 PM - 31 May 12”.
				        // 5. Tweet Permalink
				        //    The Tweet timestamp must always be linked to the Tweet permalink.

				             $content .= '<div class="twitter_intents">'. "\n";
				             $content .= '<a class="reply" href="https://twitter.com/intent/tweet?in_reply_to='.$tweet['id_str'].'"><i class="fas fa-reply"></i></a>'. "\n";
				             $content .= '<a class="retweet" href="https://twitter.com/intent/retweet?tweet_id='.$tweet['id_str'].'"><i class="fas fa-retweet"></i></a>'. "\n";
				             $content .= '<a class="favorite" href="https://twitter.com/intent/favorite?tweet_id='.$tweet['id_str'].'"><i class="fas fa-star"></i></a>'. "\n";

				        $date = strtotime($tweet['created_at']); // retrives the tweets date and time in Unix Epoch terms
				        $blogtime = current_time('U'); // retrives the current browser client date and time in Unix Epoch terms
				        $dago = human_time_diff($date, $blogtime) . ' ' . sprintf(__('ago', 'neighborhood')); // calculates and outputs the time past in human readable format
				        $content .= '<a class="timestamp" href="https://twitter.com/'.$twitterID.'/status/'.$tweet['id_str'].'" target="_blank">'.$dago.'</a>'. "\n";
				        $content .= '</div>'. "\n";
				    } else {
				    	$content .= '<a href="http://twitter.com/'.$twitterID.'" target="_blank">@'.$twitterID.'</a>';
				    }
				    $content .= '</li>';
				}
			}
			return $content;
		} else {
			return '<li><div class="tweet-text">Please install the oAuth Twitter Feed Plugin and follow the theme documentation to set it up.</div></li>';
		}	

	}
	
	function neighborhood_hyperlinks($text) {
		$text = preg_replace('/\b([a-zA-Z]+:\/\/[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"$1\" class=\"twitter-link\">$1</a>", $text);
		$text = preg_replace('/\b(?<!:\/\/)(www\.[\w_.\-]+\.[a-zA-Z]{2,6}[\/\w\-~.?=&%#+$*!]*)\b/i',"<a href=\"http://$1\" class=\"twitter-link\">$1</a>", $text);
	    // match name@address
		$text = preg_replace("/\b([a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]*\@[a-zA-Z][a-zA-Z0-9\_\.\-]*[a-zA-Z]{2,6})\b/i","<a href=\"mailto://$1\" class=\"twitter-link\">$1</a>", $text);
	        //mach #trendingtopics. Props to Michael Voigt
		$text = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/#search?q=$2\" class=\"twitter-link\">#$2</a>$3 ", $text);
		return $text;
	}

	function neighborhood_encode_tweet($text) {
		$text = mb_convert_encoding( $text, "HTML-ENTITIES", "UTF-8");
		return $text;
	}
	
	
	/* VIDEO EMBED FUNCTIONS
	================================================== */
	
	function video_embed($url, $width = 640, $height = 480) {
		if (strpos($url,'youtube')){
			return video_youtube($url, $width, $height);
		} else {
			return video_vimeo($url, $width, $height);
		}
	}
	
	function video_youtube($url, $width = 640, $height = 480){

		preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $url, $video_id);
		
		return '<iframe itemprop="video" src="https://www.youtube.com/embed/'. $video_id[1] .'?wmode=transparent" width="'. $width .'" height="'. $height .'" allowfullscreen></iframe>';

	}
	
	function video_vimeo($url, $width = 640, $height = 480){

		preg_match('/http:\/\/vimeo.com\/(\d+)$/', $url, $video_id);		
		
		return '<iframe itemprop="video" src="https://player.vimeo.com/video/'. $video_id[1] .'?title=0&amp;byline=0&amp;portrait=0" width="'. $width .'" height="'. $height .'" allowfullscreen></iframe>';
		
	}
	

	/* FEATURED IMAGE TITLE
	================================================== */
	
	function neighborhood_featured_img_title() {
		global $post;
		$neighborhood_thumbnail_id = get_post_thumbnail_id($post->ID);
		$neighborhood_thumbnail_image = get_posts(array('p' => $neighborhood_thumbnail_id, 'post_type' => 'attachment', 'post_status' => 'any'));
		if ($neighborhood_thumbnail_image && isset($neighborhood_thumbnail_image[0])) {
			return $neighborhood_thumbnail_image[0]->post_title;
		}
	}
	
	
	/* GET ATTACHMENT ID FROM URL
	================================================== */
	
	function neighborhood_get_attachment_id_from_url( $attachment_url = '' ) {

		global $wpdb;
		$attachment_id = false;

		// If there is no url, return.
		if ( '' == $attachment_url )
			return;

		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();

		// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

			// If this is the URL of an auto-generated thumbnail, get the URL of the original image
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

		}

		return $attachment_id;
	}
	
	
	/* ICON LIST
	================================================== */ 
	if ( ! function_exists( 'neighborhood_get_icons_list' ) ) {
		function neighborhood_get_icons_list($type = "") {
			
			// VARIABLES
			$icon_list = $fontawesome_list = "";
			
			// FONT AWESOME
			$fontawesome_list = '<li><i class="fas fa-address-book"></i><span class="icon-name">fas fa-address-book</span></li><li><i class="fas fa-address-card"></i><span class="icon-name">fas fa-address-card</span></li><li><i class="fas fa-adjust"></i><span class="icon-name">fas fa-adjust</span></li><li><i class="fas fa-air-freshener"></i><span class="icon-name">fas fa-air-freshener</span></li><li><i class="fas fa-align-center"></i><span class="icon-name">fas fa-align-center</span></li><li><i class="fas fa-align-justify"></i><span class="icon-name">fas fa-align-justify</span></li><li><i class="fas fa-align-left"></i><span class="icon-name">fas fa-align-left</span></li><li><i class="fas fa-align-right"></i><span class="icon-name">fas fa-align-right</span></li><li><i class="fas fa-allergies"></i><span class="icon-name">fas fa-allergies</span></li><li><i class="fas fa-ambulance"></i><span class="icon-name">fas fa-ambulance</span></li><li><i class="fas fa-american-sign-language-interpreting"></i><span class="icon-name">fas fa-american-sign-language-interpreting</span></li><li><i class="fas fa-anchor"></i><span class="icon-name">fas fa-anchor</span></li><li><i class="fas fa-angle-double-down"></i><span class="icon-name">fas fa-angle-double-down</span></li><li><i class="fas fa-angle-double-left"></i><span class="icon-name">fas fa-angle-double-left</span></li><li><i class="fas fa-angle-double-right"></i><span class="icon-name">fas fa-angle-double-right</span></li><li><i class="fas fa-angle-double-up"></i><span class="icon-name">fas fa-angle-double-up</span></li><li><i class="fas fa-angle-down"></i><span class="icon-name">fas fa-angle-down</span></li><li><i class="fas fa-angle-left"></i><span class="icon-name">fas fa-angle-left</span></li><li><i class="fas fa-angle-right"></i><span class="icon-name">fas fa-angle-right</span></li><li><i class="fas fa-angle-up"></i><span class="icon-name">fas fa-angle-up</span></li><li><i class="fas fa-angry"></i><span class="icon-name">fas fa-angry</span></li><li><i class="fas fa-apple-alt"></i><span class="icon-name">fas fa-apple-alt</span></li><li><i class="fas fa-archive"></i><span class="icon-name">fas fa-archive</span></li><li><i class="fas fa-archway"></i><span class="icon-name">fas fa-archway</span></li><li><i class="fas fa-arrow-alt-circle-down"></i><span class="icon-name">fas fa-arrow-alt-circle-down</span></li><li><i class="fas fa-arrow-alt-circle-left"></i><span class="icon-name">fas fa-arrow-alt-circle-left</span></li><li><i class="fas fa-arrow-alt-circle-right"></i><span class="icon-name">fas fa-arrow-alt-circle-right</span></li><li><i class="fas fa-arrow-alt-circle-up"></i><span class="icon-name">fas fa-arrow-alt-circle-up</span></li><li><i class="fas fa-arrow-circle-down"></i><span class="icon-name">fas fa-arrow-circle-down</span></li><li><i class="fas fa-arrow-circle-left"></i><span class="icon-name">fas fa-arrow-circle-left</span></li><li><i class="fas fa-arrow-circle-right"></i><span class="icon-name">fas fa-arrow-circle-right</span></li><li><i class="fas fa-arrow-circle-up"></i><span class="icon-name">fas fa-arrow-circle-up</span></li><li><i class="fas fa-arrow-down"></i><span class="icon-name">fas fa-arrow-down</span></li><li><i class="fas fa-arrow-left"></i><span class="icon-name">fas fa-arrow-left</span></li><li><i class="fas fa-arrow-right"></i><span class="icon-name">fas fa-arrow-right</span></li><li><i class="fas fa-arrow-up"></i><span class="icon-name">fas fa-arrow-up</span></li><li><i class="fas fa-arrows-alt"></i><span class="icon-name">fas fa-arrows-alt</span></li><li><i class="fas fa-arrows-alt-h"></i><span class="icon-name">fas fa-arrows-alt-h</span></li><li><i class="fas fa-arrows-alt-v"></i><span class="icon-name">fas fa-arrows-alt-v</span></li><li><i class="fas fa-assistive-listening-systems"></i><span class="icon-name">fas fa-assistive-listening-systems</span></li><li><i class="fas fa-asterisk"></i><span class="icon-name">fas fa-asterisk</span></li><li><i class="fas fa-at"></i><span class="icon-name">fas fa-at</span></li><li><i class="fas fa-atlas"></i><span class="icon-name">fas fa-atlas</span></li><li><i class="fas fa-atom"></i><span class="icon-name">fas fa-atom</span></li><li><i class="fas fa-audio-description"></i><span class="icon-name">fas fa-audio-description</span></li><li><i class="fas fa-award"></i><span class="icon-name">fas fa-award</span></li><li><i class="fas fa-backspace"></i><span class="icon-name">fas fa-backspace</span></li><li><i class="fas fa-backward"></i><span class="icon-name">fas fa-backward</span></li><li><i class="fas fa-balance-scale"></i><span class="icon-name">fas fa-balance-scale</span></li><li><i class="fas fa-ban"></i><span class="icon-name">fas fa-ban</span></li><li><i class="fas fa-band-aid"></i><span class="icon-name">fas fa-band-aid</span></li><li><i class="fas fa-barcode"></i><span class="icon-name">fas fa-barcode</span></li><li><i class="fas fa-bars"></i><span class="icon-name">fas fa-bars</span></li><li><i class="fas fa-baseball-ball"></i><span class="icon-name">fas fa-baseball-ball</span></li><li><i class="fas fa-basketball-ball"></i><span class="icon-name">fas fa-basketball-ball</span></li><li><i class="fas fa-bath"></i><span class="icon-name">fas fa-bath</span></li><li><i class="fas fa-battery-empty"></i><span class="icon-name">fas fa-battery-empty</span></li><li><i class="fas fa-battery-full"></i><span class="icon-name">fas fa-battery-full</span></li><li><i class="fas fa-battery-half"></i><span class="icon-name">fas fa-battery-half</span></li><li><i class="fas fa-battery-quarter"></i><span class="icon-name">fas fa-battery-quarter</span></li><li><i class="fas fa-battery-three-quarters"></i><span class="icon-name">fas fa-battery-three-quarters</span></li><li><i class="fas fa-bed"></i><span class="icon-name">fas fa-bed</span></li><li><i class="fas fa-beer"></i><span class="icon-name">fas fa-beer</span></li><li><i class="fas fa-bell"></i><span class="icon-name">fas fa-bell</span></li><li><i class="fas fa-bell-slash"></i><span class="icon-name">fas fa-bell-slash</span></li><li><i class="fas fa-bezier-curve"></i><span class="icon-name">fas fa-bezier-curve</span></li><li><i class="fas fa-bicycle"></i><span class="icon-name">fas fa-bicycle</span></li><li><i class="fas fa-binoculars"></i><span class="icon-name">fas fa-binoculars</span></li><li><i class="fas fa-birthday-cake"></i><span class="icon-name">fas fa-birthday-cake</span></li><li><i class="fas fa-blender"></i><span class="icon-name">fas fa-blender</span></li><li><i class="fas fa-blind"></i><span class="icon-name">fas fa-blind</span></li><li><i class="fas fa-bold"></i><span class="icon-name">fas fa-bold</span></li><li><i class="fas fa-bolt"></i><span class="icon-name">fas fa-bolt</span></li><li><i class="fas fa-bomb"></i><span class="icon-name">fas fa-bomb</span></li><li><i class="fas fa-bone"></i><span class="icon-name">fas fa-bone</span></li><li><i class="fas fa-bong"></i><span class="icon-name">fas fa-bong</span></li><li><i class="fas fa-book"></i><span class="icon-name">fas fa-book</span></li><li><i class="fas fa-book-open"></i><span class="icon-name">fas fa-book-open</span></li><li><i class="fas fa-book-reader"></i><span class="icon-name">fas fa-book-reader</span></li><li><i class="fas fa-bookmark"></i><span class="icon-name">fas fa-bookmark</span></li><li><i class="fas fa-bowling-ball"></i><span class="icon-name">fas fa-bowling-ball</span></li><li><i class="fas fa-box"></i><span class="icon-name">fas fa-box</span></li><li><i class="fas fa-box-open"></i><span class="icon-name">fas fa-box-open</span></li><li><i class="fas fa-boxes"></i><span class="icon-name">fas fa-boxes</span></li><li><i class="fas fa-braille"></i><span class="icon-name">fas fa-braille</span></li><li><i class="fas fa-brain"></i><span class="icon-name">fas fa-brain</span></li><li><i class="fas fa-briefcase"></i><span class="icon-name">fas fa-briefcase</span></li><li><i class="fas fa-briefcase-medical"></i><span class="icon-name">fas fa-briefcase-medical</span></li><li><i class="fas fa-broadcast-tower"></i><span class="icon-name">fas fa-broadcast-tower</span></li><li><i class="fas fa-broom"></i><span class="icon-name">fas fa-broom</span></li><li><i class="fas fa-brush"></i><span class="icon-name">fas fa-brush</span></li><li><i class="fas fa-bug"></i><span class="icon-name">fas fa-bug</span></li><li><i class="fas fa-building"></i><span class="icon-name">fas fa-building</span></li><li><i class="fas fa-bullhorn"></i><span class="icon-name">fas fa-bullhorn</span></li><li><i class="fas fa-bullseye"></i><span class="icon-name">fas fa-bullseye</span></li><li><i class="fas fa-burn"></i><span class="icon-name">fas fa-burn</span></li><li><i class="fas fa-bus"></i><span class="icon-name">fas fa-bus</span></li><li><i class="fas fa-bus-alt"></i><span class="icon-name">fas fa-bus-alt</span></li><li><i class="fas fa-calculator"></i><span class="icon-name">fas fa-calculator</span></li><li><i class="fas fa-calendar"></i><span class="icon-name">fas fa-calendar</span></li><li><i class="fas fa-calendar-alt"></i><span class="icon-name">fas fa-calendar-alt</span></li><li><i class="fas fa-calendar-check"></i><span class="icon-name">fas fa-calendar-check</span></li><li><i class="fas fa-calendar-minus"></i><span class="icon-name">fas fa-calendar-minus</span></li><li><i class="fas fa-calendar-plus"></i><span class="icon-name">fas fa-calendar-plus</span></li><li><i class="fas fa-calendar-times"></i><span class="icon-name">fas fa-calendar-times</span></li><li><i class="fas fa-camera"></i><span class="icon-name">fas fa-camera</span></li><li><i class="fas fa-camera-retro"></i><span class="icon-name">fas fa-camera-retro</span></li><li><i class="fas fa-cannabis"></i><span class="icon-name">fas fa-cannabis</span></li><li><i class="fas fa-capsules"></i><span class="icon-name">fas fa-capsules</span></li><li><i class="fas fa-car"></i><span class="icon-name">fas fa-car</span></li><li><i class="fas fa-car-alt"></i><span class="icon-name">fas fa-car-alt</span></li><li><i class="fas fa-car-battery"></i><span class="icon-name">fas fa-car-battery</span></li><li><i class="fas fa-car-crash"></i><span class="icon-name">fas fa-car-crash</span></li><li><i class="fas fa-car-side"></i><span class="icon-name">fas fa-car-side</span></li><li><i class="fas fa-caret-down"></i><span class="icon-name">fas fa-caret-down</span></li><li><i class="fas fa-caret-left"></i><span class="icon-name">fas fa-caret-left</span></li><li><i class="fas fa-caret-right"></i><span class="icon-name">fas fa-caret-right</span></li><li><i class="fas fa-caret-square-down"></i><span class="icon-name">fas fa-caret-square-down</span></li><li><i class="fas fa-caret-square-left"></i><span class="icon-name">fas fa-caret-square-left</span></li><li><i class="fas fa-caret-square-right"></i><span class="icon-name">fas fa-caret-square-right</span></li><li><i class="fas fa-caret-square-up"></i><span class="icon-name">fas fa-caret-square-up</span></li><li><i class="fas fa-caret-up"></i><span class="icon-name">fas fa-caret-up</span></li><li><i class="fas fa-cart-arrow-down"></i><span class="icon-name">fas fa-cart-arrow-down</span></li><li><i class="fas fa-cart-plus"></i><span class="icon-name">fas fa-cart-plus</span></li><li><i class="fas fa-certificate"></i><span class="icon-name">fas fa-certificate</span></li><li><i class="fas fa-chalkboard"></i><span class="icon-name">fas fa-chalkboard</span></li><li><i class="fas fa-chalkboard-teacher"></i><span class="icon-name">fas fa-chalkboard-teacher</span></li><li><i class="fas fa-charging-station"></i><span class="icon-name">fas fa-charging-station</span></li><li><i class="fas fa-chart-area"></i><span class="icon-name">fas fa-chart-area</span></li><li><i class="fas fa-chart-bar"></i><span class="icon-name">fas fa-chart-bar</span></li><li><i class="fas fa-chart-line"></i><span class="icon-name">fas fa-chart-line</span></li><li><i class="fas fa-chart-pie"></i><span class="icon-name">fas fa-chart-pie</span></li><li><i class="fas fa-check"></i><span class="icon-name">fas fa-check</span></li><li><i class="fas fa-check-circle"></i><span class="icon-name">fas fa-check-circle</span></li><li><i class="fas fa-check-double"></i><span class="icon-name">fas fa-check-double</span></li><li><i class="fas fa-check-square"></i><span class="icon-name">fas fa-check-square</span></li><li><i class="fas fa-chess"></i><span class="icon-name">fas fa-chess</span></li><li><i class="fas fa-chess-bishop"></i><span class="icon-name">fas fa-chess-bishop</span></li><li><i class="fas fa-chess-board"></i><span class="icon-name">fas fa-chess-board</span></li><li><i class="fas fa-chess-king"></i><span class="icon-name">fas fa-chess-king</span></li><li><i class="fas fa-chess-knight"></i><span class="icon-name">fas fa-chess-knight</span></li><li><i class="fas fa-chess-pawn"></i><span class="icon-name">fas fa-chess-pawn</span></li><li><i class="fas fa-chess-queen"></i><span class="icon-name">fas fa-chess-queen</span></li><li><i class="fas fa-chess-rook"></i><span class="icon-name">fas fa-chess-rook</span></li><li><i class="fas fa-chevron-circle-down"></i><span class="icon-name">fas fa-chevron-circle-down</span></li><li><i class="fas fa-chevron-circle-left"></i><span class="icon-name">fas fa-chevron-circle-left</span></li><li><i class="fas fa-chevron-circle-right"></i><span class="icon-name">fas fa-chevron-circle-right</span></li><li><i class="fas fa-chevron-circle-up"></i><span class="icon-name">fas fa-chevron-circle-up</span></li><li><i class="fas fa-chevron-down"></i><span class="icon-name">fas fa-chevron-down</span></li><li><i class="fas fa-chevron-left"></i><span class="icon-name">fas fa-chevron-left</span></li><li><i class="fas fa-chevron-right"></i><span class="icon-name">fas fa-chevron-right</span></li><li><i class="fas fa-chevron-up"></i><span class="icon-name">fas fa-chevron-up</span></li><li><i class="fas fa-child"></i><span class="icon-name">fas fa-child</span></li><li><i class="fas fa-church"></i><span class="icon-name">fas fa-church</span></li><li><i class="fas fa-circle"></i><span class="icon-name">fas fa-circle</span></li><li><i class="fas fa-circle-notch"></i><span class="icon-name">fas fa-circle-notch</span></li><li><i class="fas fa-clipboard"></i><span class="icon-name">fas fa-clipboard</span></li><li><i class="fas fa-clipboard-check"></i><span class="icon-name">fas fa-clipboard-check</span></li><li><i class="fas fa-clipboard-list"></i><span class="icon-name">fas fa-clipboard-list</span></li><li><i class="fas fa-clock"></i><span class="icon-name">fas fa-clock</span></li><li><i class="fas fa-clone"></i><span class="icon-name">fas fa-clone</span></li><li><i class="fas fa-closed-captioning"></i><span class="icon-name">fas fa-closed-captioning</span></li><li><i class="fas fa-cloud"></i><span class="icon-name">fas fa-cloud</span></li><li><i class="fas fa-cloud-download-alt"></i><span class="icon-name">fas fa-cloud-download-alt</span></li><li><i class="fas fa-cloud-upload-alt"></i><span class="icon-name">fas fa-cloud-upload-alt</span></li><li><i class="fas fa-cocktail"></i><span class="icon-name">fas fa-cocktail</span></li><li><i class="fas fa-code"></i><span class="icon-name">fas fa-code</span></li><li><i class="fas fa-code-branch"></i><span class="icon-name">fas fa-code-branch</span></li><li><i class="fas fa-coffee"></i><span class="icon-name">fas fa-coffee</span></li><li><i class="fas fa-cog"></i><span class="icon-name">fas fa-cog</span></li><li><i class="fas fa-cogs"></i><span class="icon-name">fas fa-cogs</span></li><li><i class="fas fa-coins"></i><span class="icon-name">fas fa-coins</span></li><li><i class="fas fa-columns"></i><span class="icon-name">fas fa-columns</span></li><li><i class="fas fa-comment"></i><span class="icon-name">fas fa-comment</span></li><li><i class="fas fa-comment-alt"></i><span class="icon-name">fas fa-comment-alt</span></li><li><i class="fas fa-comment-dots"></i><span class="icon-name">fas fa-comment-dots</span></li><li><i class="fas fa-comment-slash"></i><span class="icon-name">fas fa-comment-slash</span></li><li><i class="fas fa-comments"></i><span class="icon-name">fas fa-comments</span></li><li><i class="fas fa-compact-disc"></i><span class="icon-name">fas fa-compact-disc</span></li><li><i class="fas fa-compass"></i><span class="icon-name">fas fa-compass</span></li><li><i class="fas fa-compress"></i><span class="icon-name">fas fa-compress</span></li><li><i class="fas fa-concierge-bell"></i><span class="icon-name">fas fa-concierge-bell</span></li><li><i class="fas fa-cookie"></i><span class="icon-name">fas fa-cookie</span></li><li><i class="fas fa-cookie-bite"></i><span class="icon-name">fas fa-cookie-bite</span></li><li><i class="fas fa-copy"></i><span class="icon-name">fas fa-copy</span></li><li><i class="fas fa-copyright"></i><span class="icon-name">fas fa-copyright</span></li><li><i class="fas fa-couch"></i><span class="icon-name">fas fa-couch</span></li><li><i class="fas fa-credit-card"></i><span class="icon-name">fas fa-credit-card</span></li><li><i class="fas fa-crop"></i><span class="icon-name">fas fa-crop</span></li><li><i class="fas fa-crop-alt"></i><span class="icon-name">fas fa-crop-alt</span></li><li><i class="fas fa-crosshairs"></i><span class="icon-name">fas fa-crosshairs</span></li><li><i class="fas fa-crow"></i><span class="icon-name">fas fa-crow</span></li><li><i class="fas fa-crown"></i><span class="icon-name">fas fa-crown</span></li><li><i class="fas fa-cube"></i><span class="icon-name">fas fa-cube</span></li><li><i class="fas fa-cubes"></i><span class="icon-name">fas fa-cubes</span></li><li><i class="fas fa-cut"></i><span class="icon-name">fas fa-cut</span></li><li><i class="fas fa-database"></i><span class="icon-name">fas fa-database</span></li><li><i class="fas fa-deaf"></i><span class="icon-name">fas fa-deaf</span></li><li><i class="fas fa-desktop"></i><span class="icon-name">fas fa-desktop</span></li><li><i class="fas fa-diagnoses"></i><span class="icon-name">fas fa-diagnoses</span></li><li><i class="fas fa-dice"></i><span class="icon-name">fas fa-dice</span></li><li><i class="fas fa-dice-five"></i><span class="icon-name">fas fa-dice-five</span></li><li><i class="fas fa-dice-four"></i><span class="icon-name">fas fa-dice-four</span></li><li><i class="fas fa-dice-one"></i><span class="icon-name">fas fa-dice-one</span></li><li><i class="fas fa-dice-six"></i><span class="icon-name">fas fa-dice-six</span></li><li><i class="fas fa-dice-three"></i><span class="icon-name">fas fa-dice-three</span></li><li><i class="fas fa-dice-two"></i><span class="icon-name">fas fa-dice-two</span></li><li><i class="fas fa-digital-tachograph"></i><span class="icon-name">fas fa-digital-tachograph</span></li><li><i class="fas fa-directions"></i><span class="icon-name">fas fa-directions</span></li><li><i class="fas fa-divide"></i><span class="icon-name">fas fa-divide</span></li><li><i class="fas fa-dizzy"></i><span class="icon-name">fas fa-dizzy</span></li><li><i class="fas fa-dna"></i><span class="icon-name">fas fa-dna</span></li><li><i class="fas fa-dollar-sign"></i><span class="icon-name">fas fa-dollar-sign</span></li><li><i class="fas fa-dolly"></i><span class="icon-name">fas fa-dolly</span></li><li><i class="fas fa-dolly-flatbed"></i><span class="icon-name">fas fa-dolly-flatbed</span></li><li><i class="fas fa-donate"></i><span class="icon-name">fas fa-donate</span></li><li><i class="fas fa-door-closed"></i><span class="icon-name">fas fa-door-closed</span></li><li><i class="fas fa-door-open"></i><span class="icon-name">fas fa-door-open</span></li><li><i class="fas fa-dot-circle"></i><span class="icon-name">fas fa-dot-circle</span></li><li><i class="fas fa-dove"></i><span class="icon-name">fas fa-dove</span></li><li><i class="fas fa-download"></i><span class="icon-name">fas fa-download</span></li><li><i class="fas fa-drafting-compass"></i><span class="icon-name">fas fa-drafting-compass</span></li><li><i class="fas fa-draw-polygon"></i><span class="icon-name">fas fa-draw-polygon</span></li><li><i class="fas fa-drum"></i><span class="icon-name">fas fa-drum</span></li><li><i class="fas fa-drum-steelpan"></i><span class="icon-name">fas fa-drum-steelpan</span></li><li><i class="fas fa-dumbbell"></i><span class="icon-name">fas fa-dumbbell</span></li><li><i class="fas fa-edit"></i><span class="icon-name">fas fa-edit</span></li><li><i class="fas fa-eject"></i><span class="icon-name">fas fa-eject</span></li><li><i class="fas fa-ellipsis-h"></i><span class="icon-name">fas fa-ellipsis-h</span></li><li><i class="fas fa-ellipsis-v"></i><span class="icon-name">fas fa-ellipsis-v</span></li><li><i class="fas fa-envelope"></i><span class="icon-name">fas fa-envelope</span></li><li><i class="fas fa-envelope-open"></i><span class="icon-name">fas fa-envelope-open</span></li><li><i class="fas fa-envelope-square"></i><span class="icon-name">fas fa-envelope-square</span></li><li><i class="fas fa-equals"></i><span class="icon-name">fas fa-equals</span></li><li><i class="fas fa-eraser"></i><span class="icon-name">fas fa-eraser</span></li><li><i class="fas fa-euro-sign"></i><span class="icon-name">fas fa-euro-sign</span></li><li><i class="fas fa-exchange-alt"></i><span class="icon-name">fas fa-exchange-alt</span></li><li><i class="fas fa-exclamation"></i><span class="icon-name">fas fa-exclamation</span></li><li><i class="fas fa-exclamation-circle"></i><span class="icon-name">fas fa-exclamation-circle</span></li><li><i class="fas fa-exclamation-triangle"></i><span class="icon-name">fas fa-exclamation-triangle</span></li><li><i class="fas fa-expand"></i><span class="icon-name">fas fa-expand</span></li><li><i class="fas fa-expand-arrows-alt"></i><span class="icon-name">fas fa-expand-arrows-alt</span></li><li><i class="fas fa-external-link-alt"></i><span class="icon-name">fas fa-external-link-alt</span></li><li><i class="fas fa-external-link-square-alt"></i><span class="icon-name">fas fa-external-link-square-alt</span></li><li><i class="fas fa-eye"></i><span class="icon-name">fas fa-eye</span></li><li><i class="fas fa-eye-dropper"></i><span class="icon-name">fas fa-eye-dropper</span></li><li><i class="fas fa-eye-slash"></i><span class="icon-name">fas fa-eye-slash</span></li><li><i class="fas fa-fast-backward"></i><span class="icon-name">fas fa-fast-backward</span></li><li><i class="fas fa-fast-forward"></i><span class="icon-name">fas fa-fast-forward</span></li><li><i class="fas fa-fax"></i><span class="icon-name">fas fa-fax</span></li><li><i class="fas fa-feather"></i><span class="icon-name">fas fa-feather</span></li><li><i class="fas fa-feather-alt"></i><span class="icon-name">fas fa-feather-alt</span></li><li><i class="fas fa-female"></i><span class="icon-name">fas fa-female</span></li><li><i class="fas fa-fighter-jet"></i><span class="icon-name">fas fa-fighter-jet</span></li><li><i class="fas fa-file"></i><span class="icon-name">fas fa-file</span></li><li><i class="fas fa-file-alt"></i><span class="icon-name">fas fa-file-alt</span></li><li><i class="fas fa-file-archive"></i><span class="icon-name">fas fa-file-archive</span></li><li><i class="fas fa-file-audio"></i><span class="icon-name">fas fa-file-audio</span></li><li><i class="fas fa-file-code"></i><span class="icon-name">fas fa-file-code</span></li><li><i class="fas fa-file-contract"></i><span class="icon-name">fas fa-file-contract</span></li><li><i class="fas fa-file-download"></i><span class="icon-name">fas fa-file-download</span></li><li><i class="fas fa-file-excel"></i><span class="icon-name">fas fa-file-excel</span></li><li><i class="fas fa-file-export"></i><span class="icon-name">fas fa-file-export</span></li><li><i class="fas fa-file-image"></i><span class="icon-name">fas fa-file-image</span></li><li><i class="fas fa-file-import"></i><span class="icon-name">fas fa-file-import</span></li><li><i class="fas fa-file-invoice"></i><span class="icon-name">fas fa-file-invoice</span></li><li><i class="fas fa-file-invoice-dollar"></i><span class="icon-name">fas fa-file-invoice-dollar</span></li><li><i class="fas fa-file-medical"></i><span class="icon-name">fas fa-file-medical</span></li><li><i class="fas fa-file-medical-alt"></i><span class="icon-name">fas fa-file-medical-alt</span></li><li><i class="fas fa-file-pdf"></i><span class="icon-name">fas fa-file-pdf</span></li><li><i class="fas fa-file-powerpoint"></i><span class="icon-name">fas fa-file-powerpoint</span></li><li><i class="fas fa-file-prescription"></i><span class="icon-name">fas fa-file-prescription</span></li><li><i class="fas fa-file-signature"></i><span class="icon-name">fas fa-file-signature</span></li><li><i class="fas fa-file-upload"></i><span class="icon-name">fas fa-file-upload</span></li><li><i class="fas fa-file-video"></i><span class="icon-name">fas fa-file-video</span></li><li><i class="fas fa-file-word"></i><span class="icon-name">fas fa-file-word</span></li><li><i class="fas fa-fill"></i><span class="icon-name">fas fa-fill</span></li><li><i class="fas fa-fill-drip"></i><span class="icon-name">fas fa-fill-drip</span></li><li><i class="fas fa-film"></i><span class="icon-name">fas fa-film</span></li><li><i class="fas fa-filter"></i><span class="icon-name">fas fa-filter</span></li><li><i class="fas fa-fingerprint"></i><span class="icon-name">fas fa-fingerprint</span></li><li><i class="fas fa-fire"></i><span class="icon-name">fas fa-fire</span></li><li><i class="fas fa-fire-extinguisher"></i><span class="icon-name">fas fa-fire-extinguisher</span></li><li><i class="fas fa-first-aid"></i><span class="icon-name">fas fa-first-aid</span></li><li><i class="fas fa-fish"></i><span class="icon-name">fas fa-fish</span></li><li><i class="fas fa-flag"></i><span class="icon-name">fas fa-flag</span></li><li><i class="fas fa-flag-checkered"></i><span class="icon-name">fas fa-flag-checkered</span></li><li><i class="fas fa-flask"></i><span class="icon-name">fas fa-flask</span></li><li><i class="fas fa-flushed"></i><span class="icon-name">fas fa-flushed</span></li><li><i class="fas fa-folder"></i><span class="icon-name">fas fa-folder</span></li><li><i class="fas fa-folder-open"></i><span class="icon-name">fas fa-folder-open</span></li><li><i class="fas fa-font"></i><span class="icon-name">fas fa-font</span></li><li><i class="fas fa-font-awesome-logo-full"></i><span class="icon-name">fas fa-font-awesome-logo-full</span></li><li><i class="fas fa-football-ball"></i><span class="icon-name">fas fa-football-ball</span></li><li><i class="fas fa-forward"></i><span class="icon-name">fas fa-forward</span></li><li><i class="fas fa-frog"></i><span class="icon-name">fas fa-frog</span></li><li><i class="fas fa-frown"></i><span class="icon-name">fas fa-frown</span></li><li><i class="fas fa-frown-open"></i><span class="icon-name">fas fa-frown-open</span></li><li><i class="fas fa-futbol"></i><span class="icon-name">fas fa-futbol</span></li><li><i class="fas fa-gamepad"></i><span class="icon-name">fas fa-gamepad</span></li><li><i class="fas fa-gas-pump"></i><span class="icon-name">fas fa-gas-pump</span></li><li><i class="fas fa-gavel"></i><span class="icon-name">fas fa-gavel</span></li><li><i class="fas fa-gem"></i><span class="icon-name">fas fa-gem</span></li><li><i class="fas fa-genderless"></i><span class="icon-name">fas fa-genderless</span></li><li><i class="fas fa-gift"></i><span class="icon-name">fas fa-gift</span></li><li><i class="fas fa-glass-martini"></i><span class="icon-name">fas fa-glass-martini</span></li><li><i class="fas fa-glass-martini-alt"></i><span class="icon-name">fas fa-glass-martini-alt</span></li><li><i class="fas fa-glasses"></i><span class="icon-name">fas fa-glasses</span></li><li><i class="fas fa-globe"></i><span class="icon-name">fas fa-globe</span></li><li><i class="fas fa-globe-africa"></i><span class="icon-name">fas fa-globe-africa</span></li><li><i class="fas fa-globe-americas"></i><span class="icon-name">fas fa-globe-americas</span></li><li><i class="fas fa-globe-asia"></i><span class="icon-name">fas fa-globe-asia</span></li><li><i class="fas fa-golf-ball"></i><span class="icon-name">fas fa-golf-ball</span></li><li><i class="fas fa-graduation-cap"></i><span class="icon-name">fas fa-graduation-cap</span></li><li><i class="fas fa-greater-than"></i><span class="icon-name">fas fa-greater-than</span></li><li><i class="fas fa-greater-than-equal"></i><span class="icon-name">fas fa-greater-than-equal</span></li><li><i class="fas fa-grimace"></i><span class="icon-name">fas fa-grimace</span></li><li><i class="fas fa-grin"></i><span class="icon-name">fas fa-grin</span></li><li><i class="fas fa-grin-alt"></i><span class="icon-name">fas fa-grin-alt</span></li><li><i class="fas fa-grin-beam"></i><span class="icon-name">fas fa-grin-beam</span></li><li><i class="fas fa-grin-beam-sweat"></i><span class="icon-name">fas fa-grin-beam-sweat</span></li><li><i class="fas fa-grin-hearts"></i><span class="icon-name">fas fa-grin-hearts</span></li><li><i class="fas fa-grin-squint"></i><span class="icon-name">fas fa-grin-squint</span></li><li><i class="fas fa-grin-squint-tears"></i><span class="icon-name">fas fa-grin-squint-tears</span></li><li><i class="fas fa-grin-stars"></i><span class="icon-name">fas fa-grin-stars</span></li><li><i class="fas fa-grin-tears"></i><span class="icon-name">fas fa-grin-tears</span></li><li><i class="fas fa-grin-tongue"></i><span class="icon-name">fas fa-grin-tongue</span></li><li><i class="fas fa-grin-tongue-squint"></i><span class="icon-name">fas fa-grin-tongue-squint</span></li><li><i class="fas fa-grin-tongue-wink"></i><span class="icon-name">fas fa-grin-tongue-wink</span></li><li><i class="fas fa-grin-wink"></i><span class="icon-name">fas fa-grin-wink</span></li><li><i class="fas fa-grip-horizontal"></i><span class="icon-name">fas fa-grip-horizontal</span></li><li><i class="fas fa-grip-vertical"></i><span class="icon-name">fas fa-grip-vertical</span></li><li><i class="fas fa-h-square"></i><span class="icon-name">fas fa-h-square</span></li><li><i class="fas fa-hand-holding"></i><span class="icon-name">fas fa-hand-holding</span></li><li><i class="fas fa-hand-holding-heart"></i><span class="icon-name">fas fa-hand-holding-heart</span></li><li><i class="fas fa-hand-holding-usd"></i><span class="icon-name">fas fa-hand-holding-usd</span></li><li><i class="fas fa-hand-lizard"></i><span class="icon-name">fas fa-hand-lizard</span></li><li><i class="fas fa-hand-paper"></i><span class="icon-name">fas fa-hand-paper</span></li><li><i class="fas fa-hand-peace"></i><span class="icon-name">fas fa-hand-peace</span></li><li><i class="fas fa-hand-point-down"></i><span class="icon-name">fas fa-hand-point-down</span></li><li><i class="fas fa-hand-point-left"></i><span class="icon-name">fas fa-hand-point-left</span></li><li><i class="fas fa-hand-point-right"></i><span class="icon-name">fas fa-hand-point-right</span></li><li><i class="fas fa-hand-point-up"></i><span class="icon-name">fas fa-hand-point-up</span></li><li><i class="fas fa-hand-pointer"></i><span class="icon-name">fas fa-hand-pointer</span></li><li><i class="fas fa-hand-rock"></i><span class="icon-name">fas fa-hand-rock</span></li><li><i class="fas fa-hand-scissors"></i><span class="icon-name">fas fa-hand-scissors</span></li><li><i class="fas fa-hand-spock"></i><span class="icon-name">fas fa-hand-spock</span></li><li><i class="fas fa-hands"></i><span class="icon-name">fas fa-hands</span></li><li><i class="fas fa-hands-helping"></i><span class="icon-name">fas fa-hands-helping</span></li><li><i class="fas fa-handshake"></i><span class="icon-name">fas fa-handshake</span></li><li><i class="fas fa-hashtag"></i><span class="icon-name">fas fa-hashtag</span></li><li><i class="fas fa-hdd"></i><span class="icon-name">fas fa-hdd</span></li><li><i class="fas fa-heading"></i><span class="icon-name">fas fa-heading</span></li><li><i class="fas fa-headphones"></i><span class="icon-name">fas fa-headphones</span></li><li><i class="fas fa-headphones-alt"></i><span class="icon-name">fas fa-headphones-alt</span></li><li><i class="fas fa-headset"></i><span class="icon-name">fas fa-headset</span></li><li><i class="fas fa-heart"></i><span class="icon-name">fas fa-heart</span></li><li><i class="fas fa-heartbeat"></i><span class="icon-name">fas fa-heartbeat</span></li><li><i class="fas fa-helicopter"></i><span class="icon-name">fas fa-helicopter</span></li><li><i class="fas fa-highlighter"></i><span class="icon-name">fas fa-highlighter</span></li><li><i class="fas fa-history"></i><span class="icon-name">fas fa-history</span></li><li><i class="fas fa-hockey-puck"></i><span class="icon-name">fas fa-hockey-puck</span></li><li><i class="fas fa-home"></i><span class="icon-name">fas fa-home</span></li><li><i class="fas fa-hospital"></i><span class="icon-name">fas fa-hospital</span></li><li><i class="fas fa-hospital-alt"></i><span class="icon-name">fas fa-hospital-alt</span></li><li><i class="fas fa-hospital-symbol"></i><span class="icon-name">fas fa-hospital-symbol</span></li><li><i class="fas fa-hot-tub"></i><span class="icon-name">fas fa-hot-tub</span></li><li><i class="fas fa-hotel"></i><span class="icon-name">fas fa-hotel</span></li><li><i class="fas fa-hourglass"></i><span class="icon-name">fas fa-hourglass</span></li><li><i class="fas fa-hourglass-end"></i><span class="icon-name">fas fa-hourglass-end</span></li><li><i class="fas fa-hourglass-half"></i><span class="icon-name">fas fa-hourglass-half</span></li><li><i class="fas fa-hourglass-start"></i><span class="icon-name">fas fa-hourglass-start</span></li><li><i class="fas fa-i-cursor"></i><span class="icon-name">fas fa-i-cursor</span></li><li><i class="fas fa-id-badge"></i><span class="icon-name">fas fa-id-badge</span></li><li><i class="fas fa-id-card"></i><span class="icon-name">fas fa-id-card</span></li><li><i class="fas fa-id-card-alt"></i><span class="icon-name">fas fa-id-card-alt</span></li><li><i class="fas fa-image"></i><span class="icon-name">fas fa-image</span></li><li><i class="fas fa-images"></i><span class="icon-name">fas fa-images</span></li><li><i class="fas fa-inbox"></i><span class="icon-name">fas fa-inbox</span></li><li><i class="fas fa-indent"></i><span class="icon-name">fas fa-indent</span></li><li><i class="fas fa-industry"></i><span class="icon-name">fas fa-industry</span></li><li><i class="fas fa-infinity"></i><span class="icon-name">fas fa-infinity</span></li><li><i class="fas fa-info"></i><span class="icon-name">fas fa-info</span></li><li><i class="fas fa-info-circle"></i><span class="icon-name">fas fa-info-circle</span></li><li><i class="fas fa-italic"></i><span class="icon-name">fas fa-italic</span></li><li><i class="fas fa-joint"></i><span class="icon-name">fas fa-joint</span></li><li><i class="fas fa-key"></i><span class="icon-name">fas fa-key</span></li><li><i class="fas fa-keyboard"></i><span class="icon-name">fas fa-keyboard</span></li><li><i class="fas fa-kiss"></i><span class="icon-name">fas fa-kiss</span></li><li><i class="fas fa-kiss-beam"></i><span class="icon-name">fas fa-kiss-beam</span></li><li><i class="fas fa-kiss-wink-heart"></i><span class="icon-name">fas fa-kiss-wink-heart</span></li><li><i class="fas fa-kiwi-bird"></i><span class="icon-name">fas fa-kiwi-bird</span></li><li><i class="fas fa-language"></i><span class="icon-name">fas fa-language</span></li><li><i class="fas fa-laptop"></i><span class="icon-name">fas fa-laptop</span></li><li><i class="fas fa-laptop-code"></i><span class="icon-name">fas fa-laptop-code</span></li><li><i class="fas fa-laugh"></i><span class="icon-name">fas fa-laugh</span></li><li><i class="fas fa-laugh-beam"></i><span class="icon-name">fas fa-laugh-beam</span></li><li><i class="fas fa-laugh-squint"></i><span class="icon-name">fas fa-laugh-squint</span></li><li><i class="fas fa-laugh-wink"></i><span class="icon-name">fas fa-laugh-wink</span></li><li><i class="fas fa-layer-group"></i><span class="icon-name">fas fa-layer-group</span></li><li><i class="fas fa-leaf"></i><span class="icon-name">fas fa-leaf</span></li><li><i class="fas fa-lemon"></i><span class="icon-name">fas fa-lemon</span></li><li><i class="fas fa-less-than"></i><span class="icon-name">fas fa-less-than</span></li><li><i class="fas fa-less-than-equal"></i><span class="icon-name">fas fa-less-than-equal</span></li><li><i class="fas fa-level-down-alt"></i><span class="icon-name">fas fa-level-down-alt</span></li><li><i class="fas fa-level-up-alt"></i><span class="icon-name">fas fa-level-up-alt</span></li><li><i class="fas fa-life-ring"></i><span class="icon-name">fas fa-life-ring</span></li><li><i class="fas fa-lightbulb"></i><span class="icon-name">fas fa-lightbulb</span></li><li><i class="fas fa-link"></i><span class="icon-name">fas fa-link</span></li><li><i class="fas fa-lira-sign"></i><span class="icon-name">fas fa-lira-sign</span></li><li><i class="fas fa-list"></i><span class="icon-name">fas fa-list</span></li><li><i class="fas fa-list-alt"></i><span class="icon-name">fas fa-list-alt</span></li><li><i class="fas fa-list-ol"></i><span class="icon-name">fas fa-list-ol</span></li><li><i class="fas fa-list-ul"></i><span class="icon-name">fas fa-list-ul</span></li><li><i class="fas fa-location-arrow"></i><span class="icon-name">fas fa-location-arrow</span></li><li><i class="fas fa-lock"></i><span class="icon-name">fas fa-lock</span></li><li><i class="fas fa-lock-open"></i><span class="icon-name">fas fa-lock-open</span></li><li><i class="fas fa-long-arrow-alt-down"></i><span class="icon-name">fas fa-long-arrow-alt-down</span></li><li><i class="fas fa-long-arrow-alt-left"></i><span class="icon-name">fas fa-long-arrow-alt-left</span></li><li><i class="fas fa-long-arrow-alt-right"></i><span class="icon-name">fas fa-long-arrow-alt-right</span></li><li><i class="fas fa-long-arrow-alt-up"></i><span class="icon-name">fas fa-long-arrow-alt-up</span></li><li><i class="fas fa-low-vision"></i><span class="icon-name">fas fa-low-vision</span></li><li><i class="fas fa-luggage-cart"></i><span class="icon-name">fas fa-luggage-cart</span></li><li><i class="fas fa-magic"></i><span class="icon-name">fas fa-magic</span></li><li><i class="fas fa-magnet"></i><span class="icon-name">fas fa-magnet</span></li><li><i class="fas fa-male"></i><span class="icon-name">fas fa-male</span></li><li><i class="fas fa-map"></i><span class="icon-name">fas fa-map</span></li><li><i class="fas fa-map-marked"></i><span class="icon-name">fas fa-map-marked</span></li><li><i class="fas fa-map-marked-alt"></i><span class="icon-name">fas fa-map-marked-alt</span></li><li><i class="fas fa-map-marker"></i><span class="icon-name">fas fa-map-marker</span></li><li><i class="fas fa-map-marker-alt"></i><span class="icon-name">fas fa-map-marker-alt</span></li><li><i class="fas fa-map-pin"></i><span class="icon-name">fas fa-map-pin</span></li><li><i class="fas fa-map-signs"></i><span class="icon-name">fas fa-map-signs</span></li><li><i class="fas fa-marker"></i><span class="icon-name">fas fa-marker</span></li><li><i class="fas fa-mars"></i><span class="icon-name">fas fa-mars</span></li><li><i class="fas fa-mars-double"></i><span class="icon-name">fas fa-mars-double</span></li><li><i class="fas fa-mars-stroke"></i><span class="icon-name">fas fa-mars-stroke</span></li><li><i class="fas fa-mars-stroke-h"></i><span class="icon-name">fas fa-mars-stroke-h</span></li><li><i class="fas fa-mars-stroke-v"></i><span class="icon-name">fas fa-mars-stroke-v</span></li><li><i class="fas fa-medal"></i><span class="icon-name">fas fa-medal</span></li><li><i class="fas fa-medkit"></i><span class="icon-name">fas fa-medkit</span></li><li><i class="fas fa-meh"></i><span class="icon-name">fas fa-meh</span></li><li><i class="fas fa-meh-blank"></i><span class="icon-name">fas fa-meh-blank</span></li><li><i class="fas fa-meh-rolling-eyes"></i><span class="icon-name">fas fa-meh-rolling-eyes</span></li><li><i class="fas fa-memory"></i><span class="icon-name">fas fa-memory</span></li><li><i class="fas fa-mercury"></i><span class="icon-name">fas fa-mercury</span></li><li><i class="fas fa-microchip"></i><span class="icon-name">fas fa-microchip</span></li><li><i class="fas fa-microphone"></i><span class="icon-name">fas fa-microphone</span></li><li><i class="fas fa-microphone-alt"></i><span class="icon-name">fas fa-microphone-alt</span></li><li><i class="fas fa-microphone-alt-slash"></i><span class="icon-name">fas fa-microphone-alt-slash</span></li><li><i class="fas fa-microphone-slash"></i><span class="icon-name">fas fa-microphone-slash</span></li><li><i class="fas fa-microscope"></i><span class="icon-name">fas fa-microscope</span></li><li><i class="fas fa-minus"></i><span class="icon-name">fas fa-minus</span></li><li><i class="fas fa-minus-circle"></i><span class="icon-name">fas fa-minus-circle</span></li><li><i class="fas fa-minus-square"></i><span class="icon-name">fas fa-minus-square</span></li><li><i class="fas fa-mobile"></i><span class="icon-name">fas fa-mobile</span></li><li><i class="fas fa-mobile-alt"></i><span class="icon-name">fas fa-mobile-alt</span></li><li><i class="fas fa-money-bill"></i><span class="icon-name">fas fa-money-bill</span></li><li><i class="fas fa-money-bill-alt"></i><span class="icon-name">fas fa-money-bill-alt</span></li><li><i class="fas fa-money-bill-wave"></i><span class="icon-name">fas fa-money-bill-wave</span></li><li><i class="fas fa-money-bill-wave-alt"></i><span class="icon-name">fas fa-money-bill-wave-alt</span></li><li><i class="fas fa-money-check"></i><span class="icon-name">fas fa-money-check</span></li><li><i class="fas fa-money-check-alt"></i><span class="icon-name">fas fa-money-check-alt</span></li><li><i class="fas fa-monument"></i><span class="icon-name">fas fa-monument</span></li><li><i class="fas fa-moon"></i><span class="icon-name">fas fa-moon</span></li><li><i class="fas fa-mortar-pestle"></i><span class="icon-name">fas fa-mortar-pestle</span></li><li><i class="fas fa-motorcycle"></i><span class="icon-name">fas fa-motorcycle</span></li><li><i class="fas fa-mouse-pointer"></i><span class="icon-name">fas fa-mouse-pointer</span></li><li><i class="fas fa-music"></i><span class="icon-name">fas fa-music</span></li><li><i class="fas fa-neuter"></i><span class="icon-name">fas fa-neuter</span></li><li><i class="fas fa-newspaper"></i><span class="icon-name">fas fa-newspaper</span></li><li><i class="fas fa-not-equal"></i><span class="icon-name">fas fa-not-equal</span></li><li><i class="fas fa-notes-medical"></i><span class="icon-name">fas fa-notes-medical</span></li><li><i class="fas fa-object-group"></i><span class="icon-name">fas fa-object-group</span></li><li><i class="fas fa-object-ungroup"></i><span class="icon-name">fas fa-object-ungroup</span></li><li><i class="fas fa-oil-can"></i><span class="icon-name">fas fa-oil-can</span></li><li><i class="fas fa-outdent"></i><span class="icon-name">fas fa-outdent</span></li><li><i class="fas fa-paint-brush"></i><span class="icon-name">fas fa-paint-brush</span></li><li><i class="fas fa-paint-roller"></i><span class="icon-name">fas fa-paint-roller</span></li><li><i class="fas fa-palette"></i><span class="icon-name">fas fa-palette</span></li><li><i class="fas fa-pallet"></i><span class="icon-name">fas fa-pallet</span></li><li><i class="fas fa-paper-plane"></i><span class="icon-name">fas fa-paper-plane</span></li><li><i class="fas fa-paperclip"></i><span class="icon-name">fas fa-paperclip</span></li><li><i class="fas fa-parachute-box"></i><span class="icon-name">fas fa-parachute-box</span></li><li><i class="fas fa-paragraph"></i><span class="icon-name">fas fa-paragraph</span></li><li><i class="fas fa-parking"></i><span class="icon-name">fas fa-parking</span></li><li><i class="fas fa-passport"></i><span class="icon-name">fas fa-passport</span></li><li><i class="fas fa-paste"></i><span class="icon-name">fas fa-paste</span></li><li><i class="fas fa-pause"></i><span class="icon-name">fas fa-pause</span></li><li><i class="fas fa-pause-circle"></i><span class="icon-name">fas fa-pause-circle</span></li><li><i class="fas fa-paw"></i><span class="icon-name">fas fa-paw</span></li><li><i class="fas fa-pen"></i><span class="icon-name">fas fa-pen</span></li><li><i class="fas fa-pen-alt"></i><span class="icon-name">fas fa-pen-alt</span></li><li><i class="fas fa-pen-fancy"></i><span class="icon-name">fas fa-pen-fancy</span></li><li><i class="fas fa-pen-nib"></i><span class="icon-name">fas fa-pen-nib</span></li><li><i class="fas fa-pen-square"></i><span class="icon-name">fas fa-pen-square</span></li><li><i class="fas fa-pencil-alt"></i><span class="icon-name">fas fa-pencil-alt</span></li><li><i class="fas fa-pencil-ruler"></i><span class="icon-name">fas fa-pencil-ruler</span></li><li><i class="fas fa-people-carry"></i><span class="icon-name">fas fa-people-carry</span></li><li><i class="fas fa-percent"></i><span class="icon-name">fas fa-percent</span></li><li><i class="fas fa-percentage"></i><span class="icon-name">fas fa-percentage</span></li><li><i class="fas fa-phone"></i><span class="icon-name">fas fa-phone</span></li><li><i class="fas fa-phone-slash"></i><span class="icon-name">fas fa-phone-slash</span></li><li><i class="fas fa-phone-square"></i><span class="icon-name">fas fa-phone-square</span></li><li><i class="fas fa-phone-volume"></i><span class="icon-name">fas fa-phone-volume</span></li><li><i class="fas fa-piggy-bank"></i><span class="icon-name">fas fa-piggy-bank</span></li><li><i class="fas fa-pills"></i><span class="icon-name">fas fa-pills</span></li><li><i class="fas fa-plane"></i><span class="icon-name">fas fa-plane</span></li><li><i class="fas fa-plane-arrival"></i><span class="icon-name">fas fa-plane-arrival</span></li><li><i class="fas fa-plane-departure"></i><span class="icon-name">fas fa-plane-departure</span></li><li><i class="fas fa-play"></i><span class="icon-name">fas fa-play</span></li><li><i class="fas fa-play-circle"></i><span class="icon-name">fas fa-play-circle</span></li><li><i class="fas fa-plug"></i><span class="icon-name">fas fa-plug</span></li><li><i class="fas fa-plus"></i><span class="icon-name">fas fa-plus</span></li><li><i class="fas fa-plus-circle"></i><span class="icon-name">fas fa-plus-circle</span></li><li><i class="fas fa-plus-square"></i><span class="icon-name">fas fa-plus-square</span></li><li><i class="fas fa-podcast"></i><span class="icon-name">fas fa-podcast</span></li><li><i class="fas fa-poo"></i><span class="icon-name">fas fa-poo</span></li><li><i class="fas fa-poop"></i><span class="icon-name">fas fa-poop</span></li><li><i class="fas fa-portrait"></i><span class="icon-name">fas fa-portrait</span></li><li><i class="fas fa-pound-sign"></i><span class="icon-name">fas fa-pound-sign</span></li><li><i class="fas fa-power-off"></i><span class="icon-name">fas fa-power-off</span></li><li><i class="fas fa-prescription"></i><span class="icon-name">fas fa-prescription</span></li><li><i class="fas fa-prescription-bottle"></i><span class="icon-name">fas fa-prescription-bottle</span></li><li><i class="fas fa-prescription-bottle-alt"></i><span class="icon-name">fas fa-prescription-bottle-alt</span></li><li><i class="fas fa-print"></i><span class="icon-name">fas fa-print</span></li><li><i class="fas fa-procedures"></i><span class="icon-name">fas fa-procedures</span></li><li><i class="fas fa-project-diagram"></i><span class="icon-name">fas fa-project-diagram</span></li><li><i class="fas fa-puzzle-piece"></i><span class="icon-name">fas fa-puzzle-piece</span></li><li><i class="fas fa-qrcode"></i><span class="icon-name">fas fa-qrcode</span></li><li><i class="fas fa-question"></i><span class="icon-name">fas fa-question</span></li><li><i class="fas fa-question-circle"></i><span class="icon-name">fas fa-question-circle</span></li><li><i class="fas fa-quidditch"></i><span class="icon-name">fas fa-quidditch</span></li><li><i class="fas fa-quote-left"></i><span class="icon-name">fas fa-quote-left</span></li><li><i class="fas fa-quote-right"></i><span class="icon-name">fas fa-quote-right</span></li><li><i class="fas fa-random"></i><span class="icon-name">fas fa-random</span></li><li><i class="fas fa-receipt"></i><span class="icon-name">fas fa-receipt</span></li><li><i class="fas fa-recycle"></i><span class="icon-name">fas fa-recycle</span></li><li><i class="fas fa-redo"></i><span class="icon-name">fas fa-redo</span></li><li><i class="fas fa-redo-alt"></i><span class="icon-name">fas fa-redo-alt</span></li><li><i class="fas fa-registered"></i><span class="icon-name">fas fa-registered</span></li><li><i class="fas fa-reply"></i><span class="icon-name">fas fa-reply</span></li><li><i class="fas fa-reply-all"></i><span class="icon-name">fas fa-reply-all</span></li><li><i class="fas fa-retweet"></i><span class="icon-name">fas fa-retweet</span></li><li><i class="fas fa-ribbon"></i><span class="icon-name">fas fa-ribbon</span></li><li><i class="fas fa-road"></i><span class="icon-name">fas fa-road</span></li><li><i class="fas fa-robot"></i><span class="icon-name">fas fa-robot</span></li><li><i class="fas fa-rocket"></i><span class="icon-name">fas fa-rocket</span></li><li><i class="fas fa-route"></i><span class="icon-name">fas fa-route</span></li><li><i class="fas fa-rss"></i><span class="icon-name">fas fa-rss</span></li><li><i class="fas fa-rss-square"></i><span class="icon-name">fas fa-rss-square</span></li><li><i class="fas fa-ruble-sign"></i><span class="icon-name">fas fa-ruble-sign</span></li><li><i class="fas fa-ruler"></i><span class="icon-name">fas fa-ruler</span></li><li><i class="fas fa-ruler-combined"></i><span class="icon-name">fas fa-ruler-combined</span></li><li><i class="fas fa-ruler-horizontal"></i><span class="icon-name">fas fa-ruler-horizontal</span></li><li><i class="fas fa-ruler-vertical"></i><span class="icon-name">fas fa-ruler-vertical</span></li><li><i class="fas fa-rupee-sign"></i><span class="icon-name">fas fa-rupee-sign</span></li><li><i class="fas fa-sad-cry"></i><span class="icon-name">fas fa-sad-cry</span></li><li><i class="fas fa-sad-tear"></i><span class="icon-name">fas fa-sad-tear</span></li><li><i class="fas fa-save"></i><span class="icon-name">fas fa-save</span></li><li><i class="fas fa-school"></i><span class="icon-name">fas fa-school</span></li><li><i class="fas fa-screwdriver"></i><span class="icon-name">fas fa-screwdriver</span></li><li><i class="fas fa-search"></i><span class="icon-name">fas fa-search</span></li><li><i class="fas fa-search-minus"></i><span class="icon-name">fas fa-search-minus</span></li><li><i class="fas fa-search-plus"></i><span class="icon-name">fas fa-search-plus</span></li><li><i class="fas fa-seedling"></i><span class="icon-name">fas fa-seedling</span></li><li><i class="fas fa-server"></i><span class="icon-name">fas fa-server</span></li><li><i class="fas fa-shapes"></i><span class="icon-name">fas fa-shapes</span></li><li><i class="fas fa-share"></i><span class="icon-name">fas fa-share</span></li><li><i class="fas fa-share-alt"></i><span class="icon-name">fas fa-share-alt</span></li><li><i class="fas fa-share-alt-square"></i><span class="icon-name">fas fa-share-alt-square</span></li><li><i class="fas fa-share-square"></i><span class="icon-name">fas fa-share-square</span></li><li><i class="fas fa-shekel-sign"></i><span class="icon-name">fas fa-shekel-sign</span></li><li><i class="fas fa-shield-alt"></i><span class="icon-name">fas fa-shield-alt</span></li><li><i class="fas fa-ship"></i><span class="icon-name">fas fa-ship</span></li><li><i class="fas fa-shipping-fast"></i><span class="icon-name">fas fa-shipping-fast</span></li><li><i class="fas fa-shoe-prints"></i><span class="icon-name">fas fa-shoe-prints</span></li><li><i class="fas fa-shopping-bag"></i><span class="icon-name">fas fa-shopping-bag</span></li><li><i class="fas fa-shopping-basket"></i><span class="icon-name">fas fa-shopping-basket</span></li><li><i class="fas fa-shopping-cart"></i><span class="icon-name">fas fa-shopping-cart</span></li><li><i class="fas fa-shower"></i><span class="icon-name">fas fa-shower</span></li><li><i class="fas fa-shuttle-van"></i><span class="icon-name">fas fa-shuttle-van</span></li><li><i class="fas fa-sign"></i><span class="icon-name">fas fa-sign</span></li><li><i class="fas fa-sign-in-alt"></i><span class="icon-name">fas fa-sign-in-alt</span></li><li><i class="fas fa-sign-language"></i><span class="icon-name">fas fa-sign-language</span></li><li><i class="fas fa-sign-out-alt"></i><span class="icon-name">fas fa-sign-out-alt</span></li><li><i class="fas fa-signal"></i><span class="icon-name">fas fa-signal</span></li><li><i class="fas fa-signature"></i><span class="icon-name">fas fa-signature</span></li><li><i class="fas fa-sitemap"></i><span class="icon-name">fas fa-sitemap</span></li><li><i class="fas fa-skull"></i><span class="icon-name">fas fa-skull</span></li><li><i class="fas fa-sliders-h"></i><span class="icon-name">fas fa-sliders-h</span></li><li><i class="fas fa-smile"></i><span class="icon-name">fas fa-smile</span></li><li><i class="fas fa-smile-beam"></i><span class="icon-name">fas fa-smile-beam</span></li><li><i class="fas fa-smile-wink"></i><span class="icon-name">fas fa-smile-wink</span></li><li><i class="fas fa-smoking"></i><span class="icon-name">fas fa-smoking</span></li><li><i class="fas fa-smoking-ban"></i><span class="icon-name">fas fa-smoking-ban</span></li><li><i class="fas fa-snowflake"></i><span class="icon-name">fas fa-snowflake</span></li><li><i class="fas fa-solar-panel"></i><span class="icon-name">fas fa-solar-panel</span></li><li><i class="fas fa-sort"></i><span class="icon-name">fas fa-sort</span></li><li><i class="fas fa-sort-alpha-down"></i><span class="icon-name">fas fa-sort-alpha-down</span></li><li><i class="fas fa-sort-alpha-up"></i><span class="icon-name">fas fa-sort-alpha-up</span></li><li><i class="fas fa-sort-amount-down"></i><span class="icon-name">fas fa-sort-amount-down</span></li><li><i class="fas fa-sort-amount-up"></i><span class="icon-name">fas fa-sort-amount-up</span></li><li><i class="fas fa-sort-down"></i><span class="icon-name">fas fa-sort-down</span></li><li><i class="fas fa-sort-numeric-down"></i><span class="icon-name">fas fa-sort-numeric-down</span></li><li><i class="fas fa-sort-numeric-up"></i><span class="icon-name">fas fa-sort-numeric-up</span></li><li><i class="fas fa-sort-up"></i><span class="icon-name">fas fa-sort-up</span></li><li><i class="fas fa-spa"></i><span class="icon-name">fas fa-spa</span></li><li><i class="fas fa-space-shuttle"></i><span class="icon-name">fas fa-space-shuttle</span></li><li><i class="fas fa-spinner"></i><span class="icon-name">fas fa-spinner</span></li><li><i class="fas fa-splotch"></i><span class="icon-name">fas fa-splotch</span></li><li><i class="fas fa-spray-can"></i><span class="icon-name">fas fa-spray-can</span></li><li><i class="fas fa-square"></i><span class="icon-name">fas fa-square</span></li><li><i class="fas fa-square-full"></i><span class="icon-name">fas fa-square-full</span></li><li><i class="fas fa-stamp"></i><span class="icon-name">fas fa-stamp</span></li><li><i class="fas fa-star"></i><span class="icon-name">fas fa-star</span></li><li><i class="fas fa-star-half"></i><span class="icon-name">fas fa-star-half</span></li><li><i class="fas fa-star-half-alt"></i><span class="icon-name">fas fa-star-half-alt</span></li><li><i class="fas fa-star-of-life"></i><span class="icon-name">fas fa-star-of-life</span></li><li><i class="fas fa-step-backward"></i><span class="icon-name">fas fa-step-backward</span></li><li><i class="fas fa-step-forward"></i><span class="icon-name">fas fa-step-forward</span></li><li><i class="fas fa-stethoscope"></i><span class="icon-name">fas fa-stethoscope</span></li><li><i class="fas fa-sticky-note"></i><span class="icon-name">fas fa-sticky-note</span></li><li><i class="fas fa-stop"></i><span class="icon-name">fas fa-stop</span></li><li><i class="fas fa-stop-circle"></i><span class="icon-name">fas fa-stop-circle</span></li><li><i class="fas fa-stopwatch"></i><span class="icon-name">fas fa-stopwatch</span></li><li><i class="fas fa-store"></i><span class="icon-name">fas fa-store</span></li><li><i class="fas fa-store-alt"></i><span class="icon-name">fas fa-store-alt</span></li><li><i class="fas fa-stream"></i><span class="icon-name">fas fa-stream</span></li><li><i class="fas fa-street-view"></i><span class="icon-name">fas fa-street-view</span></li><li><i class="fas fa-strikethrough"></i><span class="icon-name">fas fa-strikethrough</span></li><li><i class="fas fa-stroopwafel"></i><span class="icon-name">fas fa-stroopwafel</span></li><li><i class="fas fa-subscript"></i><span class="icon-name">fas fa-subscript</span></li><li><i class="fas fa-subway"></i><span class="icon-name">fas fa-subway</span></li><li><i class="fas fa-suitcase"></i><span class="icon-name">fas fa-suitcase</span></li><li><i class="fas fa-suitcase-rolling"></i><span class="icon-name">fas fa-suitcase-rolling</span></li><li><i class="fas fa-sun"></i><span class="icon-name">fas fa-sun</span></li><li><i class="fas fa-superscript"></i><span class="icon-name">fas fa-superscript</span></li><li><i class="fas fa-surprise"></i><span class="icon-name">fas fa-surprise</span></li><li><i class="fas fa-swatchbook"></i><span class="icon-name">fas fa-swatchbook</span></li><li><i class="fas fa-swimmer"></i><span class="icon-name">fas fa-swimmer</span></li><li><i class="fas fa-swimming-pool"></i><span class="icon-name">fas fa-swimming-pool</span></li><li><i class="fas fa-sync"></i><span class="icon-name">fas fa-sync</span></li><li><i class="fas fa-sync-alt"></i><span class="icon-name">fas fa-sync-alt</span></li><li><i class="fas fa-syringe"></i><span class="icon-name">fas fa-syringe</span></li><li><i class="fas fa-table"></i><span class="icon-name">fas fa-table</span></li><li><i class="fas fa-table-tennis"></i><span class="icon-name">fas fa-table-tennis</span></li><li><i class="fas fa-tablet"></i><span class="icon-name">fas fa-tablet</span></li><li><i class="fas fa-tablet-alt"></i><span class="icon-name">fas fa-tablet-alt</span></li><li><i class="fas fa-tablets"></i><span class="icon-name">fas fa-tablets</span></li><li><i class="fas fa-tachometer-alt"></i><span class="icon-name">fas fa-tachometer-alt</span></li><li><i class="fas fa-tag"></i><span class="icon-name">fas fa-tag</span></li><li><i class="fas fa-tags"></i><span class="icon-name">fas fa-tags</span></li><li><i class="fas fa-tape"></i><span class="icon-name">fas fa-tape</span></li><li><i class="fas fa-tasks"></i><span class="icon-name">fas fa-tasks</span></li><li><i class="fas fa-taxi"></i><span class="icon-name">fas fa-taxi</span></li><li><i class="fas fa-teeth"></i><span class="icon-name">fas fa-teeth</span></li><li><i class="fas fa-teeth-open"></i><span class="icon-name">fas fa-teeth-open</span></li><li><i class="fas fa-terminal"></i><span class="icon-name">fas fa-terminal</span></li><li><i class="fas fa-text-height"></i><span class="icon-name">fas fa-text-height</span></li><li><i class="fas fa-text-width"></i><span class="icon-name">fas fa-text-width</span></li><li><i class="fas fa-th"></i><span class="icon-name">fas fa-th</span></li><li><i class="fas fa-th-large"></i><span class="icon-name">fas fa-th-large</span></li><li><i class="fas fa-th-list"></i><span class="icon-name">fas fa-th-list</span></li><li><i class="fas fa-theater-masks"></i><span class="icon-name">fas fa-theater-masks</span></li><li><i class="fas fa-thermometer"></i><span class="icon-name">fas fa-thermometer</span></li><li><i class="fas fa-thermometer-empty"></i><span class="icon-name">fas fa-thermometer-empty</span></li><li><i class="fas fa-thermometer-full"></i><span class="icon-name">fas fa-thermometer-full</span></li><li><i class="fas fa-thermometer-half"></i><span class="icon-name">fas fa-thermometer-half</span></li><li><i class="fas fa-thermometer-quarter"></i><span class="icon-name">fas fa-thermometer-quarter</span></li><li><i class="fas fa-thermometer-three-quarters"></i><span class="icon-name">fas fa-thermometer-three-quarters</span></li><li><i class="fas fa-thumbs-down"></i><span class="icon-name">fas fa-thumbs-down</span></li><li><i class="fas fa-thumbs-up"></i><span class="icon-name">fas fa-thumbs-up</span></li><li><i class="fas fa-thumbtack"></i><span class="icon-name">fas fa-thumbtack</span></li><li><i class="fas fa-ticket-alt"></i><span class="icon-name">fas fa-ticket-alt</span></li><li><i class="fas fa-times"></i><span class="icon-name">fas fa-times</span></li><li><i class="fas fa-times-circle"></i><span class="icon-name">fas fa-times-circle</span></li><li><i class="fas fa-tint"></i><span class="icon-name">fas fa-tint</span></li><li><i class="fas fa-tint-slash"></i><span class="icon-name">fas fa-tint-slash</span></li><li><i class="fas fa-tired"></i><span class="icon-name">fas fa-tired</span></li><li><i class="fas fa-toggle-off"></i><span class="icon-name">fas fa-toggle-off</span></li><li><i class="fas fa-toggle-on"></i><span class="icon-name">fas fa-toggle-on</span></li><li><i class="fas fa-toolbox"></i><span class="icon-name">fas fa-toolbox</span></li><li><i class="fas fa-tooth"></i><span class="icon-name">fas fa-tooth</span></li><li><i class="fas fa-trademark"></i><span class="icon-name">fas fa-trademark</span></li><li><i class="fas fa-traffic-light"></i><span class="icon-name">fas fa-traffic-light</span></li><li><i class="fas fa-train"></i><span class="icon-name">fas fa-train</span></li><li><i class="fas fa-transgender"></i><span class="icon-name">fas fa-transgender</span></li><li><i class="fas fa-transgender-alt"></i><span class="icon-name">fas fa-transgender-alt</span></li><li><i class="fas fa-trash"></i><span class="icon-name">fas fa-trash</span></li><li><i class="fas fa-trash-alt"></i><span class="icon-name">fas fa-trash-alt</span></li><li><i class="fas fa-tree"></i><span class="icon-name">fas fa-tree</span></li><li><i class="fas fa-trophy"></i><span class="icon-name">fas fa-trophy</span></li><li><i class="fas fa-truck"></i><span class="icon-name">fas fa-truck</span></li><li><i class="fas fa-truck-loading"></i><span class="icon-name">fas fa-truck-loading</span></li><li><i class="fas fa-truck-monster"></i><span class="icon-name">fas fa-truck-monster</span></li><li><i class="fas fa-truck-moving"></i><span class="icon-name">fas fa-truck-moving</span></li><li><i class="fas fa-truck-pickup"></i><span class="icon-name">fas fa-truck-pickup</span></li><li><i class="fas fa-tshirt"></i><span class="icon-name">fas fa-tshirt</span></li><li><i class="fas fa-tty"></i><span class="icon-name">fas fa-tty</span></li><li><i class="fas fa-tv"></i><span class="icon-name">fas fa-tv</span></li><li><i class="fas fa-umbrella"></i><span class="icon-name">fas fa-umbrella</span></li><li><i class="fas fa-umbrella-beach"></i><span class="icon-name">fas fa-umbrella-beach</span></li><li><i class="fas fa-underline"></i><span class="icon-name">fas fa-underline</span></li><li><i class="fas fa-undo"></i><span class="icon-name">fas fa-undo</span></li><li><i class="fas fa-undo-alt"></i><span class="icon-name">fas fa-undo-alt</span></li><li><i class="fas fa-universal-access"></i><span class="icon-name">fas fa-universal-access</span></li><li><i class="fas fa-university"></i><span class="icon-name">fas fa-university</span></li><li><i class="fas fa-unlink"></i><span class="icon-name">fas fa-unlink</span></li><li><i class="fas fa-unlock"></i><span class="icon-name">fas fa-unlock</span></li><li><i class="fas fa-unlock-alt"></i><span class="icon-name">fas fa-unlock-alt</span></li><li><i class="fas fa-upload"></i><span class="icon-name">fas fa-upload</span></li><li><i class="fas fa-user"></i><span class="icon-name">fas fa-user</span></li><li><i class="fas fa-user-alt"></i><span class="icon-name">fas fa-user-alt</span></li><li><i class="fas fa-user-alt-slash"></i><span class="icon-name">fas fa-user-alt-slash</span></li><li><i class="fas fa-user-astronaut"></i><span class="icon-name">fas fa-user-astronaut</span></li><li><i class="fas fa-user-check"></i><span class="icon-name">fas fa-user-check</span></li><li><i class="fas fa-user-circle"></i><span class="icon-name">fas fa-user-circle</span></li><li><i class="fas fa-user-clock"></i><span class="icon-name">fas fa-user-clock</span></li><li><i class="fas fa-user-cog"></i><span class="icon-name">fas fa-user-cog</span></li><li><i class="fas fa-user-edit"></i><span class="icon-name">fas fa-user-edit</span></li><li><i class="fas fa-user-friends"></i><span class="icon-name">fas fa-user-friends</span></li><li><i class="fas fa-user-graduate"></i><span class="icon-name">fas fa-user-graduate</span></li><li><i class="fas fa-user-lock"></i><span class="icon-name">fas fa-user-lock</span></li><li><i class="fas fa-user-md"></i><span class="icon-name">fas fa-user-md</span></li><li><i class="fas fa-user-minus"></i><span class="icon-name">fas fa-user-minus</span></li><li><i class="fas fa-user-ninja"></i><span class="icon-name">fas fa-user-ninja</span></li><li><i class="fas fa-user-plus"></i><span class="icon-name">fas fa-user-plus</span></li><li><i class="fas fa-user-secret"></i><span class="icon-name">fas fa-user-secret</span></li><li><i class="fas fa-user-shield"></i><span class="icon-name">fas fa-user-shield</span></li><li><i class="fas fa-user-slash"></i><span class="icon-name">fas fa-user-slash</span></li><li><i class="fas fa-user-tag"></i><span class="icon-name">fas fa-user-tag</span></li><li><i class="fas fa-user-tie"></i><span class="icon-name">fas fa-user-tie</span></li><li><i class="fas fa-user-times"></i><span class="icon-name">fas fa-user-times</span></li><li><i class="fas fa-users"></i><span class="icon-name">fas fa-users</span></li><li><i class="fas fa-users-cog"></i><span class="icon-name">fas fa-users-cog</span></li><li><i class="fas fa-utensil-spoon"></i><span class="icon-name">fas fa-utensil-spoon</span></li><li><i class="fas fa-utensils"></i><span class="icon-name">fas fa-utensils</span></li><li><i class="fas fa-vector-square"></i><span class="icon-name">fas fa-vector-square</span></li><li><i class="fas fa-venus"></i><span class="icon-name">fas fa-venus</span></li><li><i class="fas fa-venus-double"></i><span class="icon-name">fas fa-venus-double</span></li><li><i class="fas fa-venus-mars"></i><span class="icon-name">fas fa-venus-mars</span></li><li><i class="fas fa-vial"></i><span class="icon-name">fas fa-vial</span></li><li><i class="fas fa-vials"></i><span class="icon-name">fas fa-vials</span></li><li><i class="fas fa-video"></i><span class="icon-name">fas fa-video</span></li><li><i class="fas fa-video-slash"></i><span class="icon-name">fas fa-video-slash</span></li><li><i class="fas fa-volleyball-ball"></i><span class="icon-name">fas fa-volleyball-ball</span></li><li><i class="fas fa-volume-down"></i><span class="icon-name">fas fa-volume-down</span></li><li><i class="fas fa-volume-off"></i><span class="icon-name">fas fa-volume-off</span></li><li><i class="fas fa-volume-up"></i><span class="icon-name">fas fa-volume-up</span></li><li><i class="fas fa-walking"></i><span class="icon-name">fas fa-walking</span></li><li><i class="fas fa-wallet"></i><span class="icon-name">fas fa-wallet</span></li><li><i class="fas fa-warehouse"></i><span class="icon-name">fas fa-warehouse</span></li><li><i class="fas fa-weight"></i><span class="icon-name">fas fa-weight</span></li><li><i class="fas fa-weight-hanging"></i><span class="icon-name">fas fa-weight-hanging</span></li><li><i class="fas fa-wheelchair"></i><span class="icon-name">fas fa-wheelchair</span></li><li><i class="fas fa-wifi"></i><span class="icon-name">fas fa-wifi</span></li><li><i class="fas fa-window-close"></i><span class="icon-name">fas fa-window-close</span></li><li><i class="fas fa-window-maximize"></i><span class="icon-name">fas fa-window-maximize</span></li><li><i class="fas fa-window-minimize"></i><span class="icon-name">fas fa-window-minimize</span></li><li><i class="fas fa-window-restore"></i><span class="icon-name">fas fa-window-restore</span></li><li><i class="fas fa-wine-glass"></i><span class="icon-name">fas fa-wine-glass</span></li><li><i class="fas fa-wine-glass-alt"></i><span class="icon-name">fas fa-wine-glass-alt</span></li><li><i class="fas fa-won-sign"></i><span class="icon-name">fas fa-won-sign</span></li><li><i class="fas fa-wrench"></i><span class="icon-name">fas fa-wrench</span></li><li><i class="fas fa-x-ray"></i><span class="icon-name">fas fa-x-ray</span></li><li><i class="fas fa-yen-sign"></i><span class="icon-name">fas fa-yen-sign</span></li><li><i class="far fa-address-book"></i><span class="icon-name">far fa-address-book</span></li><li><i class="far fa-address-card"></i><span class="icon-name">far fa-address-card</span></li><li><i class="far fa-angry"></i><span class="icon-name">far fa-angry</span></li><li><i class="far fa-arrow-alt-circle-down"></i><span class="icon-name">far fa-arrow-alt-circle-down</span></li><li><i class="far fa-arrow-alt-circle-left"></i><span class="icon-name">far fa-arrow-alt-circle-left</span></li><li><i class="far fa-arrow-alt-circle-right"></i><span class="icon-name">far fa-arrow-alt-circle-right</span></li><li><i class="far fa-arrow-alt-circle-up"></i><span class="icon-name">far fa-arrow-alt-circle-up</span></li><li><i class="far fa-bell"></i><span class="icon-name">far fa-bell</span></li><li><i class="far fa-bell-slash"></i><span class="icon-name">far fa-bell-slash</span></li><li><i class="far fa-bookmark"></i><span class="icon-name">far fa-bookmark</span></li><li><i class="far fa-building"></i><span class="icon-name">far fa-building</span></li><li><i class="far fa-calendar"></i><span class="icon-name">far fa-calendar</span></li><li><i class="far fa-calendar-alt"></i><span class="icon-name">far fa-calendar-alt</span></li><li><i class="far fa-calendar-check"></i><span class="icon-name">far fa-calendar-check</span></li><li><i class="far fa-calendar-minus"></i><span class="icon-name">far fa-calendar-minus</span></li><li><i class="far fa-calendar-plus"></i><span class="icon-name">far fa-calendar-plus</span></li><li><i class="far fa-calendar-times"></i><span class="icon-name">far fa-calendar-times</span></li><li><i class="far fa-caret-square-down"></i><span class="icon-name">far fa-caret-square-down</span></li><li><i class="far fa-caret-square-left"></i><span class="icon-name">far fa-caret-square-left</span></li><li><i class="far fa-caret-square-right"></i><span class="icon-name">far fa-caret-square-right</span></li><li><i class="far fa-caret-square-up"></i><span class="icon-name">far fa-caret-square-up</span></li><li><i class="far fa-chart-bar"></i><span class="icon-name">far fa-chart-bar</span></li><li><i class="far fa-check-circle"></i><span class="icon-name">far fa-check-circle</span></li><li><i class="far fa-check-square"></i><span class="icon-name">far fa-check-square</span></li><li><i class="far fa-circle"></i><span class="icon-name">far fa-circle</span></li><li><i class="far fa-clipboard"></i><span class="icon-name">far fa-clipboard</span></li><li><i class="far fa-clock"></i><span class="icon-name">far fa-clock</span></li><li><i class="far fa-clone"></i><span class="icon-name">far fa-clone</span></li><li><i class="far fa-closed-captioning"></i><span class="icon-name">far fa-closed-captioning</span></li><li><i class="far fa-comment"></i><span class="icon-name">far fa-comment</span></li><li><i class="far fa-comment-alt"></i><span class="icon-name">far fa-comment-alt</span></li><li><i class="far fa-comment-dots"></i><span class="icon-name">far fa-comment-dots</span></li><li><i class="far fa-comments"></i><span class="icon-name">far fa-comments</span></li><li><i class="far fa-compass"></i><span class="icon-name">far fa-compass</span></li><li><i class="far fa-copy"></i><span class="icon-name">far fa-copy</span></li><li><i class="far fa-copyright"></i><span class="icon-name">far fa-copyright</span></li><li><i class="far fa-credit-card"></i><span class="icon-name">far fa-credit-card</span></li><li><i class="far fa-dizzy"></i><span class="icon-name">far fa-dizzy</span></li><li><i class="far fa-dot-circle"></i><span class="icon-name">far fa-dot-circle</span></li><li><i class="far fa-edit"></i><span class="icon-name">far fa-edit</span></li><li><i class="far fa-envelope"></i><span class="icon-name">far fa-envelope</span></li><li><i class="far fa-envelope-open"></i><span class="icon-name">far fa-envelope-open</span></li><li><i class="far fa-eye"></i><span class="icon-name">far fa-eye</span></li><li><i class="far fa-eye-slash"></i><span class="icon-name">far fa-eye-slash</span></li><li><i class="far fa-file"></i><span class="icon-name">far fa-file</span></li><li><i class="far fa-file-alt"></i><span class="icon-name">far fa-file-alt</span></li><li><i class="far fa-file-archive"></i><span class="icon-name">far fa-file-archive</span></li><li><i class="far fa-file-audio"></i><span class="icon-name">far fa-file-audio</span></li><li><i class="far fa-file-code"></i><span class="icon-name">far fa-file-code</span></li><li><i class="far fa-file-excel"></i><span class="icon-name">far fa-file-excel</span></li><li><i class="far fa-file-image"></i><span class="icon-name">far fa-file-image</span></li><li><i class="far fa-file-pdf"></i><span class="icon-name">far fa-file-pdf</span></li><li><i class="far fa-file-powerpoint"></i><span class="icon-name">far fa-file-powerpoint</span></li><li><i class="far fa-file-video"></i><span class="icon-name">far fa-file-video</span></li><li><i class="far fa-file-word"></i><span class="icon-name">far fa-file-word</span></li><li><i class="far fa-flag"></i><span class="icon-name">far fa-flag</span></li><li><i class="far fa-flushed"></i><span class="icon-name">far fa-flushed</span></li><li><i class="far fa-folder"></i><span class="icon-name">far fa-folder</span></li><li><i class="far fa-folder-open"></i><span class="icon-name">far fa-folder-open</span></li><li><i class="far fa-font-awesome-logo-full"></i><span class="icon-name">far fa-font-awesome-logo-full</span></li><li><i class="far fa-frown"></i><span class="icon-name">far fa-frown</span></li><li><i class="far fa-frown-open"></i><span class="icon-name">far fa-frown-open</span></li><li><i class="far fa-futbol"></i><span class="icon-name">far fa-futbol</span></li><li><i class="far fa-gem"></i><span class="icon-name">far fa-gem</span></li><li><i class="far fa-grimace"></i><span class="icon-name">far fa-grimace</span></li><li><i class="far fa-grin"></i><span class="icon-name">far fa-grin</span></li><li><i class="far fa-grin-alt"></i><span class="icon-name">far fa-grin-alt</span></li><li><i class="far fa-grin-beam"></i><span class="icon-name">far fa-grin-beam</span></li><li><i class="far fa-grin-beam-sweat"></i><span class="icon-name">far fa-grin-beam-sweat</span></li><li><i class="far fa-grin-hearts"></i><span class="icon-name">far fa-grin-hearts</span></li><li><i class="far fa-grin-squint"></i><span class="icon-name">far fa-grin-squint</span></li><li><i class="far fa-grin-squint-tears"></i><span class="icon-name">far fa-grin-squint-tears</span></li><li><i class="far fa-grin-stars"></i><span class="icon-name">far fa-grin-stars</span></li><li><i class="far fa-grin-tears"></i><span class="icon-name">far fa-grin-tears</span></li><li><i class="far fa-grin-tongue"></i><span class="icon-name">far fa-grin-tongue</span></li><li><i class="far fa-grin-tongue-squint"></i><span class="icon-name">far fa-grin-tongue-squint</span></li><li><i class="far fa-grin-tongue-wink"></i><span class="icon-name">far fa-grin-tongue-wink</span></li><li><i class="far fa-grin-wink"></i><span class="icon-name">far fa-grin-wink</span></li><li><i class="far fa-hand-lizard"></i><span class="icon-name">far fa-hand-lizard</span></li><li><i class="far fa-hand-paper"></i><span class="icon-name">far fa-hand-paper</span></li><li><i class="far fa-hand-peace"></i><span class="icon-name">far fa-hand-peace</span></li><li><i class="far fa-hand-point-down"></i><span class="icon-name">far fa-hand-point-down</span></li><li><i class="far fa-hand-point-left"></i><span class="icon-name">far fa-hand-point-left</span></li><li><i class="far fa-hand-point-right"></i><span class="icon-name">far fa-hand-point-right</span></li><li><i class="far fa-hand-point-up"></i><span class="icon-name">far fa-hand-point-up</span></li><li><i class="far fa-hand-pointer"></i><span class="icon-name">far fa-hand-pointer</span></li><li><i class="far fa-hand-rock"></i><span class="icon-name">far fa-hand-rock</span></li><li><i class="far fa-hand-scissors"></i><span class="icon-name">far fa-hand-scissors</span></li><li><i class="far fa-hand-spock"></i><span class="icon-name">far fa-hand-spock</span></li><li><i class="far fa-handshake"></i><span class="icon-name">far fa-handshake</span></li><li><i class="far fa-hdd"></i><span class="icon-name">far fa-hdd</span></li><li><i class="far fa-heart"></i><span class="icon-name">far fa-heart</span></li><li><i class="far fa-hospital"></i><span class="icon-name">far fa-hospital</span></li><li><i class="far fa-hourglass"></i><span class="icon-name">far fa-hourglass</span></li><li><i class="far fa-id-badge"></i><span class="icon-name">far fa-id-badge</span></li><li><i class="far fa-id-card"></i><span class="icon-name">far fa-id-card</span></li><li><i class="far fa-image"></i><span class="icon-name">far fa-image</span></li><li><i class="far fa-images"></i><span class="icon-name">far fa-images</span></li><li><i class="far fa-keyboard"></i><span class="icon-name">far fa-keyboard</span></li><li><i class="far fa-kiss"></i><span class="icon-name">far fa-kiss</span></li><li><i class="far fa-kiss-beam"></i><span class="icon-name">far fa-kiss-beam</span></li><li><i class="far fa-kiss-wink-heart"></i><span class="icon-name">far fa-kiss-wink-heart</span></li><li><i class="far fa-laugh"></i><span class="icon-name">far fa-laugh</span></li><li><i class="far fa-laugh-beam"></i><span class="icon-name">far fa-laugh-beam</span></li><li><i class="far fa-laugh-squint"></i><span class="icon-name">far fa-laugh-squint</span></li><li><i class="far fa-laugh-wink"></i><span class="icon-name">far fa-laugh-wink</span></li><li><i class="far fa-lemon"></i><span class="icon-name">far fa-lemon</span></li><li><i class="far fa-life-ring"></i><span class="icon-name">far fa-life-ring</span></li><li><i class="far fa-lightbulb"></i><span class="icon-name">far fa-lightbulb</span></li><li><i class="far fa-list-alt"></i><span class="icon-name">far fa-list-alt</span></li><li><i class="far fa-map"></i><span class="icon-name">far fa-map</span></li><li><i class="far fa-meh"></i><span class="icon-name">far fa-meh</span></li><li><i class="far fa-meh-blank"></i><span class="icon-name">far fa-meh-blank</span></li><li><i class="far fa-meh-rolling-eyes"></i><span class="icon-name">far fa-meh-rolling-eyes</span></li><li><i class="far fa-minus-square"></i><span class="icon-name">far fa-minus-square</span></li><li><i class="far fa-money-bill-alt"></i><span class="icon-name">far fa-money-bill-alt</span></li><li><i class="far fa-moon"></i><span class="icon-name">far fa-moon</span></li><li><i class="far fa-newspaper"></i><span class="icon-name">far fa-newspaper</span></li><li><i class="far fa-object-group"></i><span class="icon-name">far fa-object-group</span></li><li><i class="far fa-object-ungroup"></i><span class="icon-name">far fa-object-ungroup</span></li><li><i class="far fa-paper-plane"></i><span class="icon-name">far fa-paper-plane</span></li><li><i class="far fa-pause-circle"></i><span class="icon-name">far fa-pause-circle</span></li><li><i class="far fa-play-circle"></i><span class="icon-name">far fa-play-circle</span></li><li><i class="far fa-plus-square"></i><span class="icon-name">far fa-plus-square</span></li><li><i class="far fa-question-circle"></i><span class="icon-name">far fa-question-circle</span></li><li><i class="far fa-registered"></i><span class="icon-name">far fa-registered</span></li><li><i class="far fa-sad-cry"></i><span class="icon-name">far fa-sad-cry</span></li><li><i class="far fa-sad-tear"></i><span class="icon-name">far fa-sad-tear</span></li><li><i class="far fa-save"></i><span class="icon-name">far fa-save</span></li><li><i class="far fa-share-square"></i><span class="icon-name">far fa-share-square</span></li><li><i class="far fa-smile"></i><span class="icon-name">far fa-smile</span></li><li><i class="far fa-smile-beam"></i><span class="icon-name">far fa-smile-beam</span></li><li><i class="far fa-smile-wink"></i><span class="icon-name">far fa-smile-wink</span></li><li><i class="far fa-snowflake"></i><span class="icon-name">far fa-snowflake</span></li><li><i class="far fa-square"></i><span class="icon-name">far fa-square</span></li><li><i class="far fa-star"></i><span class="icon-name">far fa-star</span></li><li><i class="far fa-star-half"></i><span class="icon-name">far fa-star-half</span></li><li><i class="far fa-sticky-note"></i><span class="icon-name">far fa-sticky-note</span></li><li><i class="far fa-stop-circle"></i><span class="icon-name">far fa-stop-circle</span></li><li><i class="far fa-sun"></i><span class="icon-name">far fa-sun</span></li><li><i class="far fa-surprise"></i><span class="icon-name">far fa-surprise</span></li><li><i class="far fa-thumbs-down"></i><span class="icon-name">far fa-thumbs-down</span></li><li><i class="far fa-thumbs-up"></i><span class="icon-name">far fa-thumbs-up</span></li><li><i class="far fa-times-circle"></i><span class="icon-name">far fa-times-circle</span></li><li><i class="far fa-tired"></i><span class="icon-name">far fa-tired</span></li><li><i class="far fa-trash-alt"></i><span class="icon-name">far fa-trash-alt</span></li><li><i class="far fa-user"></i><span class="icon-name">far fa-user</span></li><li><i class="far fa-user-circle"></i><span class="icon-name">far fa-user-circle</span></li><li><i class="far fa-window-close"></i><span class="icon-name">far fa-window-close</span></li><li><i class="far fa-window-maximize"></i><span class="icon-name">far fa-window-maximize</span></li><li><i class="far fa-window-minimize"></i><span class="icon-name">far fa-window-minimize</span></li><li><i class="far fa-window-restore"></i><span class="icon-name">far fa-window-restore</span></li><li><i class="fab fa-500px"></i><span class="icon-name">fab fa-500px</span></li><li><i class="fab fa-accessible-icon"></i><span class="icon-name">fab fa-accessible-icon</span></li><li><i class="fab fa-accusoft"></i><span class="icon-name">fab fa-accusoft</span></li><li><i class="fab fa-adn"></i><span class="icon-name">fab fa-adn</span></li><li><i class="fab fa-adversal"></i><span class="icon-name">fab fa-adversal</span></li><li><i class="fab fa-affiliatetheme"></i><span class="icon-name">fab fa-affiliatetheme</span></li><li><i class="fab fa-algolia"></i><span class="icon-name">fab fa-algolia</span></li><li><i class="fab fa-amazon"></i><span class="icon-name">fab fa-amazon</span></li><li><i class="fab fa-amazon-pay"></i><span class="icon-name">fab fa-amazon-pay</span></li><li><i class="fab fa-amilia"></i><span class="icon-name">fab fa-amilia</span></li><li><i class="fab fa-android"></i><span class="icon-name">fab fa-android</span></li><li><i class="fab fa-angellist"></i><span class="icon-name">fab fa-angellist</span></li><li><i class="fab fa-angrycreative"></i><span class="icon-name">fab fa-angrycreative</span></li><li><i class="fab fa-angular"></i><span class="icon-name">fab fa-angular</span></li><li><i class="fab fa-app-store"></i><span class="icon-name">fab fa-app-store</span></li><li><i class="fab fa-app-store-ios"></i><span class="icon-name">fab fa-app-store-ios</span></li><li><i class="fab fa-apper"></i><span class="icon-name">fab fa-apper</span></li><li><i class="fab fa-apple"></i><span class="icon-name">fab fa-apple</span></li><li><i class="fab fa-apple-pay"></i><span class="icon-name">fab fa-apple-pay</span></li><li><i class="fab fa-asymmetrik"></i><span class="icon-name">fab fa-asymmetrik</span></li><li><i class="fab fa-audible"></i><span class="icon-name">fab fa-audible</span></li><li><i class="fab fa-autoprefixer"></i><span class="icon-name">fab fa-autoprefixer</span></li><li><i class="fab fa-avianex"></i><span class="icon-name">fab fa-avianex</span></li><li><i class="fab fa-aviato"></i><span class="icon-name">fab fa-aviato</span></li><li><i class="fab fa-aws"></i><span class="icon-name">fab fa-aws</span></li><li><i class="fab fa-bandcamp"></i><span class="icon-name">fab fa-bandcamp</span></li><li><i class="fab fa-behance"></i><span class="icon-name">fab fa-behance</span></li><li><i class="fab fa-behance-square"></i><span class="icon-name">fab fa-behance-square</span></li><li><i class="fab fa-bimobject"></i><span class="icon-name">fab fa-bimobject</span></li><li><i class="fab fa-bitbucket"></i><span class="icon-name">fab fa-bitbucket</span></li><li><i class="fab fa-bitcoin"></i><span class="icon-name">fab fa-bitcoin</span></li><li><i class="fab fa-bity"></i><span class="icon-name">fab fa-bity</span></li><li><i class="fab fa-black-tie"></i><span class="icon-name">fab fa-black-tie</span></li><li><i class="fab fa-blackberry"></i><span class="icon-name">fab fa-blackberry</span></li><li><i class="fab fa-blogger"></i><span class="icon-name">fab fa-blogger</span></li><li><i class="fab fa-blogger-b"></i><span class="icon-name">fab fa-blogger-b</span></li><li><i class="fab fa-bluetooth"></i><span class="icon-name">fab fa-bluetooth</span></li><li><i class="fab fa-bluetooth-b"></i><span class="icon-name">fab fa-bluetooth-b</span></li><li><i class="fab fa-btc"></i><span class="icon-name">fab fa-btc</span></li><li><i class="fab fa-buromobelexperte"></i><span class="icon-name">fab fa-buromobelexperte</span></li><li><i class="fab fa-buysellads"></i><span class="icon-name">fab fa-buysellads</span></li><li><i class="fab fa-cc-amazon-pay"></i><span class="icon-name">fab fa-cc-amazon-pay</span></li><li><i class="fab fa-cc-amex"></i><span class="icon-name">fab fa-cc-amex</span></li><li><i class="fab fa-cc-apple-pay"></i><span class="icon-name">fab fa-cc-apple-pay</span></li><li><i class="fab fa-cc-diners-club"></i><span class="icon-name">fab fa-cc-diners-club</span></li><li><i class="fab fa-cc-discover"></i><span class="icon-name">fab fa-cc-discover</span></li><li><i class="fab fa-cc-jcb"></i><span class="icon-name">fab fa-cc-jcb</span></li><li><i class="fab fa-cc-mastercard"></i><span class="icon-name">fab fa-cc-mastercard</span></li><li><i class="fab fa-cc-paypal"></i><span class="icon-name">fab fa-cc-paypal</span></li><li><i class="fab fa-cc-stripe"></i><span class="icon-name">fab fa-cc-stripe</span></li><li><i class="fab fa-cc-visa"></i><span class="icon-name">fab fa-cc-visa</span></li><li><i class="fab fa-centercode"></i><span class="icon-name">fab fa-centercode</span></li><li><i class="fab fa-chrome"></i><span class="icon-name">fab fa-chrome</span></li><li><i class="fab fa-cloudscale"></i><span class="icon-name">fab fa-cloudscale</span></li><li><i class="fab fa-cloudsmith"></i><span class="icon-name">fab fa-cloudsmith</span></li><li><i class="fab fa-cloudversify"></i><span class="icon-name">fab fa-cloudversify</span></li><li><i class="fab fa-codepen"></i><span class="icon-name">fab fa-codepen</span></li><li><i class="fab fa-codiepie"></i><span class="icon-name">fab fa-codiepie</span></li><li><i class="fab fa-connectdevelop"></i><span class="icon-name">fab fa-connectdevelop</span></li><li><i class="fab fa-contao"></i><span class="icon-name">fab fa-contao</span></li><li><i class="fab fa-cpanel"></i><span class="icon-name">fab fa-cpanel</span></li><li><i class="fab fa-creative-commons"></i><span class="icon-name">fab fa-creative-commons</span></li><li><i class="fab fa-creative-commons-by"></i><span class="icon-name">fab fa-creative-commons-by</span></li><li><i class="fab fa-creative-commons-nc"></i><span class="icon-name">fab fa-creative-commons-nc</span></li><li><i class="fab fa-creative-commons-nc-eu"></i><span class="icon-name">fab fa-creative-commons-nc-eu</span></li><li><i class="fab fa-creative-commons-nc-jp"></i><span class="icon-name">fab fa-creative-commons-nc-jp</span></li><li><i class="fab fa-creative-commons-nd"></i><span class="icon-name">fab fa-creative-commons-nd</span></li><li><i class="fab fa-creative-commons-pd"></i><span class="icon-name">fab fa-creative-commons-pd</span></li><li><i class="fab fa-creative-commons-pd-alt"></i><span class="icon-name">fab fa-creative-commons-pd-alt</span></li><li><i class="fab fa-creative-commons-remix"></i><span class="icon-name">fab fa-creative-commons-remix</span></li><li><i class="fab fa-creative-commons-sa"></i><span class="icon-name">fab fa-creative-commons-sa</span></li><li><i class="fab fa-creative-commons-sampling"></i><span class="icon-name">fab fa-creative-commons-sampling</span></li><li><i class="fab fa-creative-commons-sampling-plus"></i><span class="icon-name">fab fa-creative-commons-sampling-plus</span></li><li><i class="fab fa-creative-commons-share"></i><span class="icon-name">fab fa-creative-commons-share</span></li><li><i class="fab fa-css3"></i><span class="icon-name">fab fa-css3</span></li><li><i class="fab fa-css3-alt"></i><span class="icon-name">fab fa-css3-alt</span></li><li><i class="fab fa-cuttlefish"></i><span class="icon-name">fab fa-cuttlefish</span></li><li><i class="fab fa-d-and-d"></i><span class="icon-name">fab fa-d-and-d</span></li><li><i class="fab fa-dashcube"></i><span class="icon-name">fab fa-dashcube</span></li><li><i class="fab fa-delicious"></i><span class="icon-name">fab fa-delicious</span></li><li><i class="fab fa-deploydog"></i><span class="icon-name">fab fa-deploydog</span></li><li><i class="fab fa-deskpro"></i><span class="icon-name">fab fa-deskpro</span></li><li><i class="fab fa-deviantart"></i><span class="icon-name">fab fa-deviantart</span></li><li><i class="fab fa-digg"></i><span class="icon-name">fab fa-digg</span></li><li><i class="fab fa-digital-ocean"></i><span class="icon-name">fab fa-digital-ocean</span></li><li><i class="fab fa-discord"></i><span class="icon-name">fab fa-discord</span></li><li><i class="fab fa-discourse"></i><span class="icon-name">fab fa-discourse</span></li><li><i class="fab fa-dochub"></i><span class="icon-name">fab fa-dochub</span></li><li><i class="fab fa-docker"></i><span class="icon-name">fab fa-docker</span></li><li><i class="fab fa-draft2digital"></i><span class="icon-name">fab fa-draft2digital</span></li><li><i class="fab fa-dribbble"></i><span class="icon-name">fab fa-dribbble</span></li><li><i class="fab fa-dribbble-square"></i><span class="icon-name">fab fa-dribbble-square</span></li><li><i class="fab fa-dropbox"></i><span class="icon-name">fab fa-dropbox</span></li><li><i class="fab fa-drupal"></i><span class="icon-name">fab fa-drupal</span></li><li><i class="fab fa-dyalog"></i><span class="icon-name">fab fa-dyalog</span></li><li><i class="fab fa-earlybirds"></i><span class="icon-name">fab fa-earlybirds</span></li><li><i class="fab fa-ebay"></i><span class="icon-name">fab fa-ebay</span></li><li><i class="fab fa-edge"></i><span class="icon-name">fab fa-edge</span></li><li><i class="fab fa-elementor"></i><span class="icon-name">fab fa-elementor</span></li><li><i class="fab fa-ello"></i><span class="icon-name">fab fa-ello</span></li><li><i class="fab fa-ember"></i><span class="icon-name">fab fa-ember</span></li><li><i class="fab fa-empire"></i><span class="icon-name">fab fa-empire</span></li><li><i class="fab fa-envira"></i><span class="icon-name">fab fa-envira</span></li><li><i class="fab fa-erlang"></i><span class="icon-name">fab fa-erlang</span></li><li><i class="fab fa-ethereum"></i><span class="icon-name">fab fa-ethereum</span></li><li><i class="fab fa-etsy"></i><span class="icon-name">fab fa-etsy</span></li><li><i class="fab fa-expeditedssl"></i><span class="icon-name">fab fa-expeditedssl</span></li><li><i class="fab fa-facebook"></i><span class="icon-name">fab fa-facebook</span></li><li><i class="fab fa-facebook-f"></i><span class="icon-name">fab fa-facebook-f</span></li><li><i class="fab fa-facebook-messenger"></i><span class="icon-name">fab fa-facebook-messenger</span></li><li><i class="fab fa-facebook-square"></i><span class="icon-name">fab fa-facebook-square</span></li><li><i class="fab fa-firefox"></i><span class="icon-name">fab fa-firefox</span></li><li><i class="fab fa-first-order"></i><span class="icon-name">fab fa-first-order</span></li><li><i class="fab fa-first-order-alt"></i><span class="icon-name">fab fa-first-order-alt</span></li><li><i class="fab fa-firstdraft"></i><span class="icon-name">fab fa-firstdraft</span></li><li><i class="fab fa-flickr"></i><span class="icon-name">fab fa-flickr</span></li><li><i class="fab fa-flipboard"></i><span class="icon-name">fab fa-flipboard</span></li><li><i class="fab fa-fly"></i><span class="icon-name">fab fa-fly</span></li><li><i class="fab fa-font-awesome"></i><span class="icon-name">fab fa-font-awesome</span></li><li><i class="fab fa-font-awesome-alt"></i><span class="icon-name">fab fa-font-awesome-alt</span></li><li><i class="fab fa-font-awesome-flag"></i><span class="icon-name">fab fa-font-awesome-flag</span></li><li><i class="fab fa-font-awesome-logo-full"></i><span class="icon-name">fab fa-font-awesome-logo-full</span></li><li><i class="fab fa-fonticons"></i><span class="icon-name">fab fa-fonticons</span></li><li><i class="fab fa-fonticons-fi"></i><span class="icon-name">fab fa-fonticons-fi</span></li><li><i class="fab fa-fort-awesome"></i><span class="icon-name">fab fa-fort-awesome</span></li><li><i class="fab fa-fort-awesome-alt"></i><span class="icon-name">fab fa-fort-awesome-alt</span></li><li><i class="fab fa-forumbee"></i><span class="icon-name">fab fa-forumbee</span></li><li><i class="fab fa-foursquare"></i><span class="icon-name">fab fa-foursquare</span></li><li><i class="fab fa-free-code-camp"></i><span class="icon-name">fab fa-free-code-camp</span></li><li><i class="fab fa-freebsd"></i><span class="icon-name">fab fa-freebsd</span></li><li><i class="fab fa-fulcrum"></i><span class="icon-name">fab fa-fulcrum</span></li><li><i class="fab fa-galactic-republic"></i><span class="icon-name">fab fa-galactic-republic</span></li><li><i class="fab fa-galactic-senate"></i><span class="icon-name">fab fa-galactic-senate</span></li><li><i class="fab fa-get-pocket"></i><span class="icon-name">fab fa-get-pocket</span></li><li><i class="fab fa-gg"></i><span class="icon-name">fab fa-gg</span></li><li><i class="fab fa-gg-circle"></i><span class="icon-name">fab fa-gg-circle</span></li><li><i class="fab fa-git"></i><span class="icon-name">fab fa-git</span></li><li><i class="fab fa-git-square"></i><span class="icon-name">fab fa-git-square</span></li><li><i class="fab fa-github"></i><span class="icon-name">fab fa-github</span></li><li><i class="fab fa-github-alt"></i><span class="icon-name">fab fa-github-alt</span></li><li><i class="fab fa-github-square"></i><span class="icon-name">fab fa-github-square</span></li><li><i class="fab fa-gitkraken"></i><span class="icon-name">fab fa-gitkraken</span></li><li><i class="fab fa-gitlab"></i><span class="icon-name">fab fa-gitlab</span></li><li><i class="fab fa-gitter"></i><span class="icon-name">fab fa-gitter</span></li><li><i class="fab fa-glide"></i><span class="icon-name">fab fa-glide</span></li><li><i class="fab fa-glide-g"></i><span class="icon-name">fab fa-glide-g</span></li><li><i class="fab fa-gofore"></i><span class="icon-name">fab fa-gofore</span></li><li><i class="fab fa-goodreads"></i><span class="icon-name">fab fa-goodreads</span></li><li><i class="fab fa-goodreads-g"></i><span class="icon-name">fab fa-goodreads-g</span></li><li><i class="fab fa-google"></i><span class="icon-name">fab fa-google</span></li><li><i class="fab fa-google-drive"></i><span class="icon-name">fab fa-google-drive</span></li><li><i class="fab fa-google-play"></i><span class="icon-name">fab fa-google-play</span></li><li><i class="fab fa-google-plus"></i><span class="icon-name">fab fa-google-plus</span></li><li><i class="fab fa-google-plus-g"></i><span class="icon-name">fab fa-google-plus-g</span></li><li><i class="fab fa-google-plus-square"></i><span class="icon-name">fab fa-google-plus-square</span></li><li><i class="fab fa-google-wallet"></i><span class="icon-name">fab fa-google-wallet</span></li><li><i class="fab fa-gratipay"></i><span class="icon-name">fab fa-gratipay</span></li><li><i class="fab fa-grav"></i><span class="icon-name">fab fa-grav</span></li><li><i class="fab fa-gripfire"></i><span class="icon-name">fab fa-gripfire</span></li><li><i class="fab fa-grunt"></i><span class="icon-name">fab fa-grunt</span></li><li><i class="fab fa-gulp"></i><span class="icon-name">fab fa-gulp</span></li><li><i class="fab fa-hacker-news"></i><span class="icon-name">fab fa-hacker-news</span></li><li><i class="fab fa-hacker-news-square"></i><span class="icon-name">fab fa-hacker-news-square</span></li><li><i class="fab fa-hackerrank"></i><span class="icon-name">fab fa-hackerrank</span></li><li><i class="fab fa-hips"></i><span class="icon-name">fab fa-hips</span></li><li><i class="fab fa-hire-a-helper"></i><span class="icon-name">fab fa-hire-a-helper</span></li><li><i class="fab fa-hooli"></i><span class="icon-name">fab fa-hooli</span></li><li><i class="fab fa-hornbill"></i><span class="icon-name">fab fa-hornbill</span></li><li><i class="fab fa-hotjar"></i><span class="icon-name">fab fa-hotjar</span></li><li><i class="fab fa-houzz"></i><span class="icon-name">fab fa-houzz</span></li><li><i class="fab fa-html5"></i><span class="icon-name">fab fa-html5</span></li><li><i class="fab fa-hubspot"></i><span class="icon-name">fab fa-hubspot</span></li><li><i class="fab fa-imdb"></i><span class="icon-name">fab fa-imdb</span></li><li><i class="fab fa-instagram"></i><span class="icon-name">fab fa-instagram</span></li><li><i class="fab fa-internet-explorer"></i><span class="icon-name">fab fa-internet-explorer</span></li><li><i class="fab fa-ioxhost"></i><span class="icon-name">fab fa-ioxhost</span></li><li><i class="fab fa-itunes"></i><span class="icon-name">fab fa-itunes</span></li><li><i class="fab fa-itunes-note"></i><span class="icon-name">fab fa-itunes-note</span></li><li><i class="fab fa-java"></i><span class="icon-name">fab fa-java</span></li><li><i class="fab fa-jedi-order"></i><span class="icon-name">fab fa-jedi-order</span></li><li><i class="fab fa-jenkins"></i><span class="icon-name">fab fa-jenkins</span></li><li><i class="fab fa-joget"></i><span class="icon-name">fab fa-joget</span></li><li><i class="fab fa-joomla"></i><span class="icon-name">fab fa-joomla</span></li><li><i class="fab fa-js"></i><span class="icon-name">fab fa-js</span></li><li><i class="fab fa-js-square"></i><span class="icon-name">fab fa-js-square</span></li><li><i class="fab fa-jsfiddle"></i><span class="icon-name">fab fa-jsfiddle</span></li><li><i class="fab fa-kaggle"></i><span class="icon-name">fab fa-kaggle</span></li><li><i class="fab fa-keybase"></i><span class="icon-name">fab fa-keybase</span></li><li><i class="fab fa-keycdn"></i><span class="icon-name">fab fa-keycdn</span></li><li><i class="fab fa-kickstarter"></i><span class="icon-name">fab fa-kickstarter</span></li><li><i class="fab fa-kickstarter-k"></i><span class="icon-name">fab fa-kickstarter-k</span></li><li><i class="fab fa-korvue"></i><span class="icon-name">fab fa-korvue</span></li><li><i class="fab fa-laravel"></i><span class="icon-name">fab fa-laravel</span></li><li><i class="fab fa-lastfm"></i><span class="icon-name">fab fa-lastfm</span></li><li><i class="fab fa-lastfm-square"></i><span class="icon-name">fab fa-lastfm-square</span></li><li><i class="fab fa-leanpub"></i><span class="icon-name">fab fa-leanpub</span></li><li><i class="fab fa-less"></i><span class="icon-name">fab fa-less</span></li><li><i class="fab fa-line"></i><span class="icon-name">fab fa-line</span></li><li><i class="fab fa-linkedin"></i><span class="icon-name">fab fa-linkedin</span></li><li><i class="fab fa-linkedin-in"></i><span class="icon-name">fab fa-linkedin-in</span></li><li><i class="fab fa-linode"></i><span class="icon-name">fab fa-linode</span></li><li><i class="fab fa-linux"></i><span class="icon-name">fab fa-linux</span></li><li><i class="fab fa-lyft"></i><span class="icon-name">fab fa-lyft</span></li><li><i class="fab fa-magento"></i><span class="icon-name">fab fa-magento</span></li><li><i class="fab fa-mailchimp"></i><span class="icon-name">fab fa-mailchimp</span></li><li><i class="fab fa-mandalorian"></i><span class="icon-name">fab fa-mandalorian</span></li><li><i class="fab fa-markdown"></i><span class="icon-name">fab fa-markdown</span></li><li><i class="fab fa-mastodon"></i><span class="icon-name">fab fa-mastodon</span></li><li><i class="fab fa-maxcdn"></i><span class="icon-name">fab fa-maxcdn</span></li><li><i class="fab fa-medapps"></i><span class="icon-name">fab fa-medapps</span></li><li><i class="fab fa-medium"></i><span class="icon-name">fab fa-medium</span></li><li><i class="fab fa-medium-m"></i><span class="icon-name">fab fa-medium-m</span></li><li><i class="fab fa-medrt"></i><span class="icon-name">fab fa-medrt</span></li><li><i class="fab fa-meetup"></i><span class="icon-name">fab fa-meetup</span></li><li><i class="fab fa-megaport"></i><span class="icon-name">fab fa-megaport</span></li><li><i class="fab fa-microsoft"></i><span class="icon-name">fab fa-microsoft</span></li><li><i class="fab fa-mix"></i><span class="icon-name">fab fa-mix</span></li><li><i class="fab fa-mixcloud"></i><span class="icon-name">fab fa-mixcloud</span></li><li><i class="fab fa-mizuni"></i><span class="icon-name">fab fa-mizuni</span></li><li><i class="fab fa-modx"></i><span class="icon-name">fab fa-modx</span></li><li><i class="fab fa-monero"></i><span class="icon-name">fab fa-monero</span></li><li><i class="fab fa-napster"></i><span class="icon-name">fab fa-napster</span></li><li><i class="fab fa-neos"></i><span class="icon-name">fab fa-neos</span></li><li><i class="fab fa-nimblr"></i><span class="icon-name">fab fa-nimblr</span></li><li><i class="fab fa-nintendo-switch"></i><span class="icon-name">fab fa-nintendo-switch</span></li><li><i class="fab fa-node"></i><span class="icon-name">fab fa-node</span></li><li><i class="fab fa-node-js"></i><span class="icon-name">fab fa-node-js</span></li><li><i class="fab fa-npm"></i><span class="icon-name">fab fa-npm</span></li><li><i class="fab fa-ns8"></i><span class="icon-name">fab fa-ns8</span></li><li><i class="fab fa-nutritionix"></i><span class="icon-name">fab fa-nutritionix</span></li><li><i class="fab fa-odnoklassniki"></i><span class="icon-name">fab fa-odnoklassniki</span></li><li><i class="fab fa-odnoklassniki-square"></i><span class="icon-name">fab fa-odnoklassniki-square</span></li><li><i class="fab fa-old-republic"></i><span class="icon-name">fab fa-old-republic</span></li><li><i class="fab fa-opencart"></i><span class="icon-name">fab fa-opencart</span></li><li><i class="fab fa-openid"></i><span class="icon-name">fab fa-openid</span></li><li><i class="fab fa-opera"></i><span class="icon-name">fab fa-opera</span></li><li><i class="fab fa-optin-monster"></i><span class="icon-name">fab fa-optin-monster</span></li><li><i class="fab fa-osi"></i><span class="icon-name">fab fa-osi</span></li><li><i class="fab fa-page4"></i><span class="icon-name">fab fa-page4</span></li><li><i class="fab fa-pagelines"></i><span class="icon-name">fab fa-pagelines</span></li><li><i class="fab fa-palfed"></i><span class="icon-name">fab fa-palfed</span></li><li><i class="fab fa-patreon"></i><span class="icon-name">fab fa-patreon</span></li><li><i class="fab fa-paypal"></i><span class="icon-name">fab fa-paypal</span></li><li><i class="fab fa-periscope"></i><span class="icon-name">fab fa-periscope</span></li><li><i class="fab fa-phabricator"></i><span class="icon-name">fab fa-phabricator</span></li><li><i class="fab fa-phoenix-framework"></i><span class="icon-name">fab fa-phoenix-framework</span></li><li><i class="fab fa-phoenix-squadron"></i><span class="icon-name">fab fa-phoenix-squadron</span></li><li><i class="fab fa-php"></i><span class="icon-name">fab fa-php</span></li><li><i class="fab fa-pied-piper"></i><span class="icon-name">fab fa-pied-piper</span></li><li><i class="fab fa-pied-piper-alt"></i><span class="icon-name">fab fa-pied-piper-alt</span></li><li><i class="fab fa-pied-piper-hat"></i><span class="icon-name">fab fa-pied-piper-hat</span></li><li><i class="fab fa-pied-piper-pp"></i><span class="icon-name">fab fa-pied-piper-pp</span></li><li><i class="fab fa-pinterest"></i><span class="icon-name">fab fa-pinterest</span></li><li><i class="fab fa-pinterest-p"></i><span class="icon-name">fab fa-pinterest-p</span></li><li><i class="fab fa-pinterest-square"></i><span class="icon-name">fab fa-pinterest-square</span></li><li><i class="fab fa-playstation"></i><span class="icon-name">fab fa-playstation</span></li><li><i class="fab fa-product-hunt"></i><span class="icon-name">fab fa-product-hunt</span></li><li><i class="fab fa-pushed"></i><span class="icon-name">fab fa-pushed</span></li><li><i class="fab fa-python"></i><span class="icon-name">fab fa-python</span></li><li><i class="fab fa-qq"></i><span class="icon-name">fab fa-qq</span></li><li><i class="fab fa-quinscape"></i><span class="icon-name">fab fa-quinscape</span></li><li><i class="fab fa-quora"></i><span class="icon-name">fab fa-quora</span></li><li><i class="fab fa-r-project"></i><span class="icon-name">fab fa-r-project</span></li><li><i class="fab fa-ravelry"></i><span class="icon-name">fab fa-ravelry</span></li><li><i class="fab fa-react"></i><span class="icon-name">fab fa-react</span></li><li><i class="fab fa-readme"></i><span class="icon-name">fab fa-readme</span></li><li><i class="fab fa-rebel"></i><span class="icon-name">fab fa-rebel</span></li><li><i class="fab fa-red-river"></i><span class="icon-name">fab fa-red-river</span></li><li><i class="fab fa-reddit"></i><span class="icon-name">fab fa-reddit</span></li><li><i class="fab fa-reddit-alien"></i><span class="icon-name">fab fa-reddit-alien</span></li><li><i class="fab fa-reddit-square"></i><span class="icon-name">fab fa-reddit-square</span></li><li><i class="fab fa-rendact"></i><span class="icon-name">fab fa-rendact</span></li><li><i class="fab fa-renren"></i><span class="icon-name">fab fa-renren</span></li><li><i class="fab fa-replyd"></i><span class="icon-name">fab fa-replyd</span></li><li><i class="fab fa-researchgate"></i><span class="icon-name">fab fa-researchgate</span></li><li><i class="fab fa-resolving"></i><span class="icon-name">fab fa-resolving</span></li><li><i class="fab fa-rev"></i><span class="icon-name">fab fa-rev</span></li><li><i class="fab fa-rocketchat"></i><span class="icon-name">fab fa-rocketchat</span></li><li><i class="fab fa-rockrms"></i><span class="icon-name">fab fa-rockrms</span></li><li><i class="fab fa-safari"></i><span class="icon-name">fab fa-safari</span></li><li><i class="fab fa-sass"></i><span class="icon-name">fab fa-sass</span></li><li><i class="fab fa-schlix"></i><span class="icon-name">fab fa-schlix</span></li><li><i class="fab fa-scribd"></i><span class="icon-name">fab fa-scribd</span></li><li><i class="fab fa-searchengin"></i><span class="icon-name">fab fa-searchengin</span></li><li><i class="fab fa-sellcast"></i><span class="icon-name">fab fa-sellcast</span></li><li><i class="fab fa-sellsy"></i><span class="icon-name">fab fa-sellsy</span></li><li><i class="fab fa-servicestack"></i><span class="icon-name">fab fa-servicestack</span></li><li><i class="fab fa-shirtsinbulk"></i><span class="icon-name">fab fa-shirtsinbulk</span></li><li><i class="fab fa-shopware"></i><span class="icon-name">fab fa-shopware</span></li><li><i class="fab fa-simplybuilt"></i><span class="icon-name">fab fa-simplybuilt</span></li><li><i class="fab fa-sistrix"></i><span class="icon-name">fab fa-sistrix</span></li><li><i class="fab fa-sith"></i><span class="icon-name">fab fa-sith</span></li><li><i class="fab fa-skyatlas"></i><span class="icon-name">fab fa-skyatlas</span></li><li><i class="fab fa-skype"></i><span class="icon-name">fab fa-skype</span></li><li><i class="fab fa-slack"></i><span class="icon-name">fab fa-slack</span></li><li><i class="fab fa-slack-hash"></i><span class="icon-name">fab fa-slack-hash</span></li><li><i class="fab fa-slideshare"></i><span class="icon-name">fab fa-slideshare</span></li><li><i class="fab fa-snapchat"></i><span class="icon-name">fab fa-snapchat</span></li><li><i class="fab fa-snapchat-ghost"></i><span class="icon-name">fab fa-snapchat-ghost</span></li><li><i class="fab fa-snapchat-square"></i><span class="icon-name">fab fa-snapchat-square</span></li><li><i class="fab fa-soundcloud"></i><span class="icon-name">fab fa-soundcloud</span></li><li><i class="fab fa-speakap"></i><span class="icon-name">fab fa-speakap</span></li><li><i class="fab fa-spotify"></i><span class="icon-name">fab fa-spotify</span></li><li><i class="fab fa-squarespace"></i><span class="icon-name">fab fa-squarespace</span></li><li><i class="fab fa-stack-exchange"></i><span class="icon-name">fab fa-stack-exchange</span></li><li><i class="fab fa-stack-overflow"></i><span class="icon-name">fab fa-stack-overflow</span></li><li><i class="fab fa-staylinked"></i><span class="icon-name">fab fa-staylinked</span></li><li><i class="fab fa-steam"></i><span class="icon-name">fab fa-steam</span></li><li><i class="fab fa-steam-square"></i><span class="icon-name">fab fa-steam-square</span></li><li><i class="fab fa-steam-symbol"></i><span class="icon-name">fab fa-steam-symbol</span></li><li><i class="fab fa-sticker-mule"></i><span class="icon-name">fab fa-sticker-mule</span></li><li><i class="fab fa-strava"></i><span class="icon-name">fab fa-strava</span></li><li><i class="fab fa-stripe"></i><span class="icon-name">fab fa-stripe</span></li><li><i class="fab fa-stripe-s"></i><span class="icon-name">fab fa-stripe-s</span></li><li><i class="fab fa-studiovinari"></i><span class="icon-name">fab fa-studiovinari</span></li><li><i class="fab fa-stumbleupon"></i><span class="icon-name">fab fa-stumbleupon</span></li><li><i class="fab fa-stumbleupon-circle"></i><span class="icon-name">fab fa-stumbleupon-circle</span></li><li><i class="fab fa-superpowers"></i><span class="icon-name">fab fa-superpowers</span></li><li><i class="fab fa-supple"></i><span class="icon-name">fab fa-supple</span></li><li><i class="fab fa-teamspeak"></i><span class="icon-name">fab fa-teamspeak</span></li><li><i class="fab fa-telegram"></i><span class="icon-name">fab fa-telegram</span></li><li><i class="fab fa-telegram-plane"></i><span class="icon-name">fab fa-telegram-plane</span></li><li><i class="fab fa-tencent-weibo"></i><span class="icon-name">fab fa-tencent-weibo</span></li><li><i class="fab fa-themeco"></i><span class="icon-name">fab fa-themeco</span></li><li><i class="fab fa-themeisle"></i><span class="icon-name">fab fa-themeisle</span></li><li><i class="fab fa-trade-federation"></i><span class="icon-name">fab fa-trade-federation</span></li><li><i class="fab fa-trello"></i><span class="icon-name">fab fa-trello</span></li><li><i class="fab fa-tripadvisor"></i><span class="icon-name">fab fa-tripadvisor</span></li><li><i class="fab fa-tumblr"></i><span class="icon-name">fab fa-tumblr</span></li><li><i class="fab fa-tumblr-square"></i><span class="icon-name">fab fa-tumblr-square</span></li><li><i class="fab fa-twitch"></i><span class="icon-name">fab fa-twitch</span></li><li><i class="fab fa-twitter"></i><span class="icon-name">fab fa-twitter</span></li><li><i class="fab fa-twitter-square"></i><span class="icon-name">fab fa-twitter-square</span></li><li><i class="fab fa-typo3"></i><span class="icon-name">fab fa-typo3</span></li><li><i class="fab fa-uber"></i><span class="icon-name">fab fa-uber</span></li><li><i class="fab fa-uikit"></i><span class="icon-name">fab fa-uikit</span></li><li><i class="fab fa-uniregistry"></i><span class="icon-name">fab fa-uniregistry</span></li><li><i class="fab fa-untappd"></i><span class="icon-name">fab fa-untappd</span></li><li><i class="fab fa-usb"></i><span class="icon-name">fab fa-usb</span></li><li><i class="fab fa-ussunnah"></i><span class="icon-name">fab fa-ussunnah</span></li><li><i class="fab fa-vaadin"></i><span class="icon-name">fab fa-vaadin</span></li><li><i class="fab fa-viacoin"></i><span class="icon-name">fab fa-viacoin</span></li><li><i class="fab fa-viadeo"></i><span class="icon-name">fab fa-viadeo</span></li><li><i class="fab fa-viadeo-square"></i><span class="icon-name">fab fa-viadeo-square</span></li><li><i class="fab fa-viber"></i><span class="icon-name">fab fa-viber</span></li><li><i class="fab fa-vimeo"></i><span class="icon-name">fab fa-vimeo</span></li><li><i class="fab fa-vimeo-square"></i><span class="icon-name">fab fa-vimeo-square</span></li><li><i class="fab fa-vimeo-v"></i><span class="icon-name">fab fa-vimeo-v</span></li><li><i class="fab fa-vine"></i><span class="icon-name">fab fa-vine</span></li><li><i class="fab fa-vk"></i><span class="icon-name">fab fa-vk</span></li><li><i class="fab fa-vnv"></i><span class="icon-name">fab fa-vnv</span></li><li><i class="fab fa-vuejs"></i><span class="icon-name">fab fa-vuejs</span></li><li><i class="fab fa-weebly"></i><span class="icon-name">fab fa-weebly</span></li><li><i class="fab fa-weibo"></i><span class="icon-name">fab fa-weibo</span></li><li><i class="fab fa-weixin"></i><span class="icon-name">fab fa-weixin</span></li><li><i class="fab fa-whatsapp"></i><span class="icon-name">fab fa-whatsapp</span></li><li><i class="fab fa-whatsapp-square"></i><span class="icon-name">fab fa-whatsapp-square</span></li><li><i class="fab fa-whmcs"></i><span class="icon-name">fab fa-whmcs</span></li><li><i class="fab fa-wikipedia-w"></i><span class="icon-name">fab fa-wikipedia-w</span></li><li><i class="fab fa-windows"></i><span class="icon-name">fab fa-windows</span></li><li><i class="fab fa-wix"></i><span class="icon-name">fab fa-wix</span></li><li><i class="fab fa-wolf-pack-battalion"></i><span class="icon-name">fab fa-wolf-pack-battalion</span></li><li><i class="fab fa-wordpress"></i><span class="icon-name">fab fa-wordpress</span></li><li><i class="fab fa-wordpress-simple"></i><span class="icon-name">fab fa-wordpress-simple</span></li><li><i class="fab fa-wpbeginner"></i><span class="icon-name">fab fa-wpbeginner</span></li><li><i class="fab fa-wpexplorer"></i><span class="icon-name">fab fa-wpexplorer</span></li><li><i class="fab fa-wpforms"></i><span class="icon-name">fab fa-wpforms</span></li><li><i class="fab fa-xbox"></i><span class="icon-name">fab fa-xbox</span></li><li><i class="fab fa-xing"></i><span class="icon-name">fab fa-xing</span></li><li><i class="fab fa-xing-square"></i><span class="icon-name">fab fa-xing-square</span></li><li><i class="fab fa-y-combinator"></i><span class="icon-name">fab fa-y-combinator</span></li><li><i class="fab fa-yahoo"></i><span class="icon-name">fab fa-yahoo</span></li><li><i class="fab fa-yandex"></i><span class="icon-name">fab fa-yandex</span></li><li><i class="fab fa-yandex-international"></i><span class="icon-name">fab fa-yandex-international</span></li><li><i class="fab fa-yelp"></i><span class="icon-name">fab fa-yelp</span></li><li><i class="fab fa-yoast"></i><span class="icon-name">fab fa-yoast</span></li><li><i class="fab fa-youtube"></i><span class="icon-name">fab fa-youtube</span></li><li><i class="fab fa-youtube-square"></i><span class="icon-name">fab fa-youtube-square</span></li><li><i class="fab fa-zhihu"></i><span class="icon-name">fab fa-zhihu</span></li>';
			
			// OUTPUT
			if ($type == "font-awesome" || $type == "") {
				$icon_list .= $fontawesome_list;
			}
			
			// APPLY FILTERS
			$icon_list = apply_filters('neighborhood_icons_list', $icon_list);
			
			return $icon_list;
		}
	}
	
	
	/* LANGUAGE FLAGS
	================================================== */
	
	if (! function_exists( 'language_flags' )) {
		function language_flags() {

			$language_output = "";

			if (function_exists('icl_get_languages')) {
				$languages = icl_get_languages('skip_missing=0&orderby=code');
				if(!empty($languages)){
					foreach($languages as $l){
						$language_output .= '<li>';
						if($l['country_flag_url']){
							if(!$l['active']) {
								$language_output .= '<a href="'.$l['url'].'"><img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" /><span class="language name">'.$l['translated_name'].'</span></a>'."\n";
							} else {
								$language_output .= '<div class="current-language"><img src="'.$l['country_flag_url'].'" height="12" alt="'.$l['language_code'].'" width="18" /><span class="language name">'.$l['translated_name'].'</span></div>'."\n";
							}
						}
						$language_output .= '</li>';
					}
				}
			} else {
	    	//echo '<li><div>No languages set.</div></li>';
				$flags_url = get_template_directory_uri() . '/images/flags';
				$language_output .= '<li><a href="#">DEMO - EXAMPLE PURPOSES</a><li><a href="#"><span class="language name">German</span></a></li><li><div class="current-language"><span class="language name">English</span></div></li><li><a href="#"><span class="language name">Spanish</span></a></li><li><a href="#"><span class="language name">French</span></a></li>'."\n";
			}

			return $language_output;
		}
	}
	
	
	/* PAGINATION
	================================================== */
	
	function pagination() {
		global $wp_query;
		
		$big = 999999999; // need an unlikely integer
		
		return paginate_links( array(
			'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
			'format' => '?paged=%#%',
			'current' => max( 1, get_query_var('paged') ),
			'total' => $wp_query->max_num_pages
		) );
	}
	
	
	/* LATEST TWEET FUNCTION
	================================================== */
	
	function latestTweet($count, $twitterID) {

		global $include_twitter;
		$include_twitter = true;
		
		$content = "";
		
		if (function_exists('getTweets')) {

			$tweets = getTweets($twitterID, $count);

			if(is_array($tweets)){

				foreach($tweets as $tweet){

					$content .= '<li>';

					if($tweet['text']){

						$content .= '<div class="tweet-text">';

						$the_tweet = $tweet['text'];
				        /*
				        Twitter Developer Display Requirements
				        https://dev.twitter.com/terms/display-requirements
				
				        2.b. Tweet Entities within the Tweet text must be properly linked to their appropriate home on Twitter. For example:
				          i. User_mentions must link to the mentioned user's profile.
				         ii. Hashtags must link to a twitter.com search with the hashtag as the query.
				        iii. Links in Tweet text must be displayed using the display_url
				             field in the URL entities API response, and link to the original t.co url field.
				        */

				        // i. User_mentions must link to the mentioned user's profile.
				             if(is_array($tweet['entities']['user_mentions'])){
				             	foreach($tweet['entities']['user_mentions'] as $key => $user_mention){
				             		$the_tweet = preg_replace(
				             			'/@'.$user_mention['screen_name'].'/i',
				             			'<a href="http://www.twitter.com/'.$user_mention['screen_name'].'" target="_blank">@'.$user_mention['screen_name'].'</a>',
				             			$the_tweet);
				             	}
				             }

				        // ii. Hashtags must link to a twitter.com search with the hashtag as the query.
				             if(is_array($tweet['entities']['hashtags'])){
				             	foreach($tweet['entities']['hashtags'] as $key => $hashtag){
				             		$the_tweet = preg_replace(
				             			'/#'.$hashtag['text'].'/i',
				             			'<a href="https://twitter.com/search?q=%23'.$hashtag['text'].'&amp;src=hash" target="_blank">#'.$hashtag['text'].'</a>',
				             			$the_tweet);
				             	}
				             }

				        // iii. Links in Tweet text must be displayed using the display_url
				        //      field in the URL entities API response, and link to the original t.co url field.
				             if(is_array($tweet['entities']['urls'])){
				             	foreach($tweet['entities']['urls'] as $key => $link){
				             		$the_tweet = preg_replace(
				             			'`'.$link['url'].'`',
				             			'<a href="'.$link['url'].'" target="_blank">'.$link['url'].'</a>',
				             			$the_tweet);
				             	}
				             }

				        // Custom code to link to media
				             if(isset($tweet['entities']['media']) && is_array($tweet['entities']['media'])){
				             	foreach($tweet['entities']['media'] as $key => $media){
				             		$the_tweet = preg_replace(
				             			'`'.$media['url'].'`',
				             			'<a href="'.$media['url'].'" target="_blank">'.$media['url'].'</a>',
				             			$the_tweet);
				             	}
				             }

				             $content .= $the_tweet;

				             $content .= '</div>';

				        // 3. Tweet Actions
				        //    Reply, Retweet, and Favorite action icons must always be visible for the user to interact with the Tweet. These actions must be implemented using Web Intents or with the authenticated Twitter API.
				        //    No other social or 3rd party actions similar to Follow, Reply, Retweet and Favorite may be attached to a Tweet.
				        // 4. Tweet Timestamp
				        //    The Tweet timestamp must always be visible and include the time and date. e.g., “3:00 PM - 31 May 12”.
				        // 5. Tweet Permalink
				        //    The Tweet timestamp must always be linked to the Tweet permalink.

				             $content .= '<div class="twitter_intents">'. "\n";
				             $content .= '<a class="reply" href="https://twitter.com/intent/tweet?in_reply_to='.$tweet['id_str'].'"><i class="fas fa-reply"></i></a>'. "\n";
				             $content .= '<a class="retweet" href="https://twitter.com/intent/retweet?tweet_id='.$tweet['id_str'].'"><i class="fas fa-retweet"></i></a>'. "\n";
				             $content .= '<a class="favorite" href="https://twitter.com/intent/favorite?tweet_id='.$tweet['id_str'].'"><i class="fas fa-star"></i></a>'. "\n";

				        $date = strtotime($tweet['created_at']); // retrives the tweets date and time in Unix Epoch terms
				        $blogtime = current_time('U'); // retrives the current browser client date and time in Unix Epoch terms
				        $dago = human_time_diff($date, $blogtime) . ' ' . sprintf(__('ago', 'neighborhood')); // calculates and outputs the time past in human readable format
				        $content .= '<a class="timestamp" href="https://twitter.com/'.$twitterID.'/status/'.$tweet['id_str'].'" target="_blank">'.$dago.'</a>'. "\n";
				        $content .= '</div>'. "\n";
				    } else {
				    	$content .= '<a href="http://twitter.com/'.$twitterID.'" target="_blank">@'.$twitterID.'</a>';
				    }
				    $content .= '</li>';
				}
			}
			return $content;
		} else {
			return '<li><div class="tweet-text">Please install the oAuth Twitter Feed Plugin and follow the theme documentation to set it up.</div></li>';
		}	
	}
	

	/* CONTENT RETURN FUNCTIONS
	================================================== */
	
	function get_the_content_with_formatting() {
		$content = get_the_content();
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
	function neighborhood_add_formatting($content) {
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
	
	
	/* SHORTCODE FIX
	================================================== */
	
	function neighborhood_shortcode_fix($content){   
		$array = array (
			'<p>[' => '[', 
			']</p>' => ']', 
			']<br />' => ']'
		);

		$content = strtr($content, $array);
		return $content;
	}
	add_filter('the_content', 'neighborhood_shortcode_fix');
	
	
	/* CATEGORY REL FIX
	================================================== */

	function add_nofollow_cat( $text) {
		$strings = array('rel="category"', 'rel="category tag"', 'rel="whatever may need"');
		$text = str_replace('rel="category tag"', "", $text);
		return $text;
	}
	add_filter( 'the_category', 'add_nofollow_cat' );
	
	
	/* POST DETAIL META
	================================================== */
	if ( ! function_exists( 'neighborhood_post_detail_meta' ) ) {
		function neighborhood_post_detail_meta($return = false) {
			global $post;
			$options = get_option('sf_neighborhood_options');
			$site_name = apply_filters('neighborhood_schema_meta_site_name', get_bloginfo( 'name' ));
			$post_title = get_the_title();
			$post_date = get_the_date('Y-m-d g:i:s');
			$modified_date = get_the_modified_date('Y-m-d g:i:s');
			$permalink = get_permalink();
			$author = get_the_author();

			$post_image = get_post_thumbnail_id();
			$image_meta = array();
			$post_image_url = $post_image_alt = "";
			$post_image_width = $post_image_height = 0;

			if ( $post_image != "" ) {
				$post_image_meta = neighborhood_get_attachment_meta( $post_image );
				if ( isset($post_image_meta) ) {
					$post_image_alt = esc_attr( $post_image_meta['alt'] );
				} 
				$post_thumb_id = get_post_thumbnail_id();
				$post_image_url = wp_get_attachment_url( $post_thumb_id );
				$post_image_meta = wp_get_attachment_metadata( $post_thumb_id );
				$post_image_width = isset($post_image_meta['width']) ? $post_image_meta['width'] : 0;
				$post_image_height = isset($post_image_meta['height']) ? $post_image_meta['height'] : 0;
			}
			$logo = array();
			$logo_width = $logo_height = 0;
			if ( isset($options['logo_upload']) ) {
				$logo = $options['logo_upload'];
				if ( isset($logo['width']) ) {
					$logo_width = $logo['width'];
				}
				if ( isset($logo['height']) ) {
					$logo_height = $logo['height'];
				}
			}
			$updated_time = get_the_modified_time('F jS, Y');

			$meta = '<div class="article-meta hide">';
			$meta .= '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
			if ( !empty($logo) ) {
				$meta .= '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
				$meta .= '<img src="' . $logo. '" alt="' . $site_name. '" />';
				$meta .= '<meta itemprop="url" content="' . $logo. '">';
				$meta .= '<meta itemprop="width" content="' . $logo_width. '">';
				$meta .= '<meta itemprop="height" content="' . $logo_height. '">';
				$meta .= '</div>';
			}
			$meta .= '<meta itemprop="name" content="' . $site_name. '">';
			$meta .= '</div>';
			$meta .= '<meta itemscope itemprop="mainEntityOfPage" itemtype="https://schema.org/WebPage" itemid="' . $permalink. '"/>';
			$meta .= '<div itemprop="headline">' . $post_title. '</div>';
			$meta .= '<meta itemprop="datePublished" content="' . $post_date. '"/>';
			$meta .= '<meta itemprop="dateModified" content="' . $modified_date. '"/>';
			if ( $post_image != "" ) {
				$meta .= '<div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">';
				$meta .= '<meta itemprop="url" content="' . $post_image_url. '">';
				$meta .= '<meta itemprop="width" content="' . $post_image_width. '">';
				$meta .= '<meta itemprop="height" content="' . $post_image_height. '">';
				$meta .= '</div>';
			}
			$meta .= '<h3 itemprop="author" itemscope itemtype="https://schema.org/Person">';
			$meta .= '<span itemprop="name">' . $author. '</span>';
			$meta .= '</h3>';
			$meta .= '<span class="vcard author"><span class="fn">' . $author. '</span></span>';
			$meta .= '<span class="date published">' . get_the_time(). '</span>';
			$meta .= '<span class="updated">' . $updated_time. '</span>';
			$meta .= '</div>';

			$allowed = array(
				'div' => array(
					'class' => array(),
					'itemscope' => array(),
					'itemtype' => array()
				),
				'img' => array(
					'src' => array(),
					'alt' => array(),
				),
				'meta' => array(
					'itemscope' => array(),
					'itemprop' => array(),
					'itemtype' => array(),
					'itemid' => array(),
					'content' => array(),
				),
				'h3' => array(
					'itemscope' => array(),
					'itemprop' => array(),
					'itemtype' => array(),
				),
				'span' => array(
					'class' => array(),
					'itemprop' => array(),
				),
			);

			if ($return) {
				return wp_kses($meta, $allowed);
			} else {
				echo wp_kses($meta, $allowed);
			}
		}
	}
	add_action( 'neighborhood_post_article_start', 'neighborhood_post_detail_meta', 5 );
	
	
	/* CUSTOM MENU SETUP
	================================================== */
	
	add_action( 'after_setup_theme', 'setup_menus' );
	function setup_menus() {
		// This theme uses wp_nav_menu() in four locations.
		register_nav_menus( array(
			'main_navigation' => __( 'Main Menu', 'neighborhood' ),
			'top_bar_menu' => __( 'Top Bar Menu', 'neighborhood' )
		) );
	}
	add_filter('nav_menu_css_class', 'mbudm_add_page_type_to_menu', 10, 2 );
	//If a menu item is a page then add the template name to it as a css class 
	function mbudm_add_page_type_to_menu($classes, $item) {
		if($item->object == 'page'){
			$template_name = neighborhood_get_post_meta( $item->object_id, '_wp_page_template', true );
			$new_class =str_replace(".php","",$template_name);
			array_push($classes, $new_class);
		}   
		return $classes;
	}

	
	/* EXCERPT
	================================================== */
	
	function new_excerpt_length($length) {
		return 60;
	}
	add_filter('excerpt_length', 'new_excerpt_length');
	
	// Blog Widget Excerpt
	function excerpt($limit) {
		$excerpt = explode(' ', get_the_excerpt(), $limit);
		if (count($excerpt)>=$limit) {
			array_pop($excerpt);
			$excerpt = implode(" ",$excerpt).'...';
		} else {
			$excerpt = implode(" ",$excerpt).'';
		} 
		$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
		return '<p>' . $excerpt . '</p>';
	}
	
	function content($limit) {
		$content = explode(' ', get_the_content(), $limit);
		if (count($content)>=$limit) {
			array_pop($content);
			$content = implode(" ",$content).'...';
		} else {
			$content = implode(" ",$content).'';
		} 
		$content = preg_replace('/\[.+\]/','', $content);
		$content = apply_filters('the_content', $content); 
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
	
	function custom_excerpt($custom_content, $limit) {
		$content = explode(' ', $custom_content, $limit);
		if (count($content)>=$limit) {
			array_pop($content);
			$content = implode(" ",$content).'...';
		} else {
			$content = implode(" ",$content).'';
		} 
		$content = preg_replace('/\[.+\]/','', $content);
		$content = apply_filters('the_content', $content); 
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}	
	
	/* REGISTER SIDEBARS
	================================================== */
	if (!function_exists('neighborhood_register_sidebars')) {
		function neighborhood_register_sidebars() {
			if ( function_exists('register_sidebar')) {

				$options = get_option('sf_neighborhood_options');
				if (isset($options['footer_layout'])) {
					$footer_config = $options['footer_layout'];
				} else {
					$footer_config = 'footer-1';
				}
				register_sidebar(array(
					'id' => 'sidebar-1',
					'name' => 'Sidebar One',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-2',
					'name' => 'Sidebar Two',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-3',
					'name' => 'Sidebar Three',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-4',
					'name' => 'Sidebar Four',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-5',
					'name' => 'Sidebar Five',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-6',
					'name' => 'Sidebar Six',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-7',
					'name' => 'Sidebar Seven',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-8',
					'name' => 'Sidebar Eight',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-9',
					'name' => 'Footer Column 1',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-10',
					'name' => 'Footer Column 2',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				register_sidebar(array(
					'id' => 'sidebar-11',
					'name' => 'Footer Column 3',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
				if ($footer_config == "footer-1") {
					register_sidebar(array(
						'id' => 'sidebar-12',
						'name' => 'Footer Column 4',
						'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
						'after_widget' => '</section>',
						'before_title' => '<div class="widget-heading clearfix"><h4><span>',
						'after_title' => '</span></h4></div>',
					));
				}
				register_sidebar(array(
					'id' => 'woocommerce-sidebar',
					'name' => 'WooCommerce Sidebar',
					'before_widget' => '<section id="%1$s" class="widget %2$s clearfix">',
					'after_widget' => '</section>',
					'before_title' => '<div class="widget-heading clearfix"><h4><span>',
					'after_title' => '</span></h4></div>',
				));
			} 
		}
		add_action( 'widgets_init', 'neighborhood_register_sidebars', 10);
	}
	
	function neighborhood_sidebars_array() {
		$sidebars = array();
		global $wp_registered_sidebars;

		foreach ( $wp_registered_sidebars as $key => $value ) {
			$sidebars[ucwords($key)] = $value['name'];
		}

		$sidebars['Sidebar-1'] = 'Sidebar One';
		$sidebars['Sidebar-2'] = 'Sidebar Two';
		$sidebars['Sidebar-3'] = 'Sidebar Three';
		$sidebars['Sidebar-4'] = 'Sidebar Four';
		$sidebars['Sidebar-5'] = 'Sidebar Five';
		$sidebars['Sidebar-6'] = 'Sidebar Six';
		$sidebars['Sidebar-7'] = 'Sidebar Seven';
		$sidebars['Sidebar-8'] = 'Sidebar Eight';
		$sidebars['Sidebar-9'] = 'Footer Column 1';
		$sidebars['Sidebar-10'] = 'Footer Column 2';
		$sidebars['Sidebar-11'] = 'Footer Column 3';
		$sidebars['Sidebar-12'] = 'Footer Column 4';
		$sidebars['Woocommerce-sidebar'] = 'Woocommerce Sidebar';

	 	// foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
	 	// 	$sidebars[ucwords($sidebar['id'])] = $sidebar['name'];
	 	// }
		return $sidebars;
	}
	
	
	/* ADD SHORTCODE FUNCTIONALITY TO WIDGETS
	================================================== */
	add_filter('widget_text', 'do_shortcode');
	
	
	/* NAVIGATION CHECK
	================================================== */
	
	//functions tell whether there are previous or next 'pages' from the current page
	//returns 0 if no 'page' exists, returns a number > 0 if 'page' does exist
	//ob_ functions are used to suppress the previous_posts_link() and next_posts_link() from printing their output to the screen
	
	function has_previous_posts() {
		ob_start();
		previous_posts_link();
		$result = strlen(ob_get_contents());
		ob_end_clean();
		return $result;
	}
	
	function has_next_posts() {
		ob_start();
		next_posts_link();
		$result = strlen(ob_get_contents());
		ob_end_clean();
		return $result;
	}
	
	
	/* REMOVE CERTAIN HEAD TAGS
	================================================== */
	
	add_action('init', 'remheadlink');
	function remheadlink() {
		remove_action('wp_head', 'index_rel_link');
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wlwmanifest_link');
	}
	
	
	/* CUSTOM LOGIN LOGO
	================================================== */
	
	function neighborhood_custom_login_logo() {
		$options = get_option('sf_neighborhood_options');
		$custom_logo = "";
		if (isset($options['custom_admin_login_logo'])) {
			$custom_logo = $options['custom_admin_login_logo'];
		}
		if ($custom_logo) {		
			echo '<style type="text/css">
			.login h1 a { background-image:url('. $custom_logo .') !important; height: 95px!important; width: 100%!important; background-size: auto!important; }
			</style>';
		} else {
			echo '<style type="text/css">
			.login h1 a { background-image:url('. get_template_directory_uri() .'/images/custom-login-logo.png) !important; height: 95px!important; width: 100%!important; background-size: auto!important; }
			</style>';
		}
	}
	
	add_action('login_head', 'neighborhood_custom_login_logo');

	
	/* COMMENTS
	================================================== */
	
	// Custom callback to list comments in the your-theme style
	if ( ! function_exists( 'custom_comments' ) ) {
		function custom_comments($comment, $args, $depth) {
			$GLOBALS['comment'] = $comment;
			$GLOBALS['comment_depth'] = $depth;
			?>
			<li id="comment-<?php comment_ID() ?>" <?php comment_class('clearfix') ?>>
				<div class="comment-wrap clearfix">
					<div class="comment-avatar">
						<?php if(function_exists('get_avatar')) { echo get_avatar($comment, '100'); } ?>
						<?php if ($comment->comment_author_email == get_the_author_meta('email')) { ?>
							<span class="tooltip"><?php _e("Author", 'neighborhood'); ?><span class="arrow"></span></span>
						<?php } ?>
					</div>
					<div class="comment-content">
						<div class="comment-meta">
							<?php
							printf('<span class="comment-author">%1$s</span> <span class="comment-date">%2$s</span>',
								get_comment_author_link(),
								get_comment_date()
							);
							edit_comment_link(__('Edit', 'neighborhood'), '<span class="edit-link">', '</span><span class="meta-sep"> |</span>');
							?>
							<?php if($args['type'] == 'all' || get_comment_type() == 'comment') :
								comment_reply_link(array_merge($args, array(
									'reply_text' => __('Reply','neighborhood'),
									'login_text' => __('Log in to reply.','neighborhood'),
									'depth' => $depth,
									'before' => '<span class="comment-reply">',
									'after' => '</span>'
								)));
							endif; ?>
						</div>
						<?php if ($comment->comment_approved == '0') _e("\t\t\t\t\t<span class='unapproved'>Your comment is awaiting moderation.</span>\n", 'neighborhood') ?>
						<div class="comment-body">
							<?php comment_text() ?>
						</div>
					</div>
				</div>
		<?php } // end custom_comments
	}
	
	// Custom callback to list pings
	if ( ! function_exists( 'custom_pings' ) ) {
		function custom_pings($comment, $args, $depth) {
			$GLOBALS['comment'] = $comment;
			?>
			<li id="comment-<?php comment_ID() ?>" <?php comment_class() ?>>
				<div class="comment-author"><?php printf(__('By %1$s on %2$s at %3$s', 'neighborhood'),
					get_comment_author_link(),
					get_comment_date(),
					get_comment_time() );
					edit_comment_link(__('Edit', 'neighborhood'), ' <span class="meta-sep">|</span> <span class="edit-link">', '</span>'); ?></div>
					<?php if ($comment->comment_approved == '0') _e('\t\t\t\t\t<span class="unapproved">Your trackback is awaiting moderation.</span>\n', 'neighborhood') ?>
					<div class="comment-content">
						<?php comment_text() ?>
					</div>
		<?php } // end custom_pings
	}
	
	
	
	/* PAGINATION
	================================================== */
	

	/* Function that Rounds To The Nearest Value.
	Needed for the pagenavi() function */
	function round_num($num, $to_nearest) {
		/*Round fractions down (http://php.net/manual/en/function.floor.php)*/
		return floor($num/$to_nearest)*$to_nearest;
	}

	/* Function that performs a Boxed Style Numbered Pagination (also called Page Navigation).
	Function is largely based on Version 2.4 of the WP-PageNavi plugin */
	function pagenavi($query, $before = '', $after = '') {

		wp_reset_query();
		global $wpdb, $paged;

		$pagenavi_options = array();
	    //$pagenavi_options['pages_text'] = ('Page %CURRENT_PAGE% of %TOTAL_PAGES%:');
		$pagenavi_options['pages_text'] = ('');
		$pagenavi_options['current_text'] = '%PAGE_NUMBER%';
		$pagenavi_options['page_text'] = '%PAGE_NUMBER%';
		$pagenavi_options['first_text'] = ('First Page');
		$pagenavi_options['last_text'] = ('Last Page');
		$pagenavi_options['next_text'] = __("Next <i class='fa-angle-right'></i>", 'neighborhood');
		$pagenavi_options['prev_text'] = __("<i class='fa-angle-left'></i> Previous", 'neighborhood');
		$pagenavi_options['dotright_text'] = '...';
		$pagenavi_options['dotleft_text'] = '...';
	    $pagenavi_options['num_pages'] = 5; //continuous block of page numbers
	    $pagenavi_options['always_show'] = 0;
	    $pagenavi_options['num_larger_page_numbers'] = 0;
	    $pagenavi_options['larger_page_numbers_multiple'] = 5;

	    $output = "";

	    //If NOT a single Post is being displayed
	    /*http://codex.wordpress.org/Function_Reference/is_single)*/
	    if (!is_single()) {
	    	$request = $query->request;
	        //intval — Get the integer value of a variable
	    	/*http://php.net/manual/en/function.intval.php*/
	    	$posts_per_page = intval(get_query_var('posts_per_page'));
	        //Retrieve variable in the WP_Query class.
	    	/*http://codex.wordpress.org/Function_Reference/get_query_var*/
	    	if ( get_query_var('paged') ) {
	    		$paged = get_query_var('paged');
	    	} elseif ( get_query_var('page') ) {
	    		$paged = get_query_var('page');
	    	} else {
	    		$paged = 1;
	    	}
	    	$numposts = $query->found_posts;
	    	$max_page = $query->max_num_pages;

	        //empty — Determine whether a variable is empty
	    	/*http://php.net/manual/en/function.empty.php*/
	    	if(empty($paged) || $paged == 0) {
	    		$paged = 1;
	    	}

	    	$pages_to_show = intval($pagenavi_options['num_pages']);
	    	$larger_page_to_show = intval($pagenavi_options['num_larger_page_numbers']);
	    	$larger_page_multiple = intval($pagenavi_options['larger_page_numbers_multiple']);
	    	$pages_to_show_minus_1 = $pages_to_show - 1;
	    	$half_page_start = floor($pages_to_show_minus_1/2);
	        //ceil — Round fractions up (http://us2.php.net/manual/en/function.ceil.php)
	    	$half_page_end = ceil($pages_to_show_minus_1/2);
	    	$start_page = $paged - $half_page_start;

	    	if($start_page <= 0) {
	    		$start_page = 1;
	    	}

	    	$end_page = $paged + $half_page_end;
	    	if(($end_page - $start_page) != $pages_to_show_minus_1) {
	    		$end_page = $start_page + $pages_to_show_minus_1;
	    	}
	    	if($end_page > $max_page) {
	    		$start_page = $max_page - $pages_to_show_minus_1;
	    		$end_page = $max_page;
	    	}
	    	if($start_page <= 0) {
	    		$start_page = 1;
	    	}

	    	$larger_per_page = $larger_page_to_show*$larger_page_multiple;
	        //round_num() custom function - Rounds To The Nearest Value.
	    	$larger_start_page_start = (round_num($start_page, 10) + $larger_page_multiple) - $larger_per_page;
	    	$larger_start_page_end = round_num($start_page, 10) + $larger_page_multiple;
	    	$larger_end_page_start = round_num($end_page, 10) + $larger_page_multiple;
	    	$larger_end_page_end = round_num($end_page, 10) + ($larger_per_page);

	    	if($larger_start_page_end - $larger_page_multiple == $start_page) {
	    		$larger_start_page_start = $larger_start_page_start - $larger_page_multiple;
	    		$larger_start_page_end = $larger_start_page_end - $larger_page_multiple;
	    	}
	    	if($larger_start_page_start <= 0) {
	    		$larger_start_page_start = $larger_page_multiple;
	    	}
	    	if($larger_start_page_end > $max_page) {
	    		$larger_start_page_end = $max_page;
	    	}
	    	if($larger_end_page_end > $max_page) {
	    		$larger_end_page_end = $max_page;
	    	}
	    	if($max_page > 1 || intval($pagenavi_options['always_show']) == 1) {
	    		/*http://php.net/manual/en/function.str-replace.php */
	    		/*number_format_i18n(): Converts integer number to format based on locale (wp-includes/functions.php*/
	    			$pages_text = str_replace("%CURRENT_PAGE%", number_format_i18n($paged), $pagenavi_options['pages_text']);
	    			$pages_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pages_text);
	    			$output .= $before.'<ul class="pagenavi">'."\n";

	    			if(!empty($pages_text)) {
	    				$output .= '<li><span class="pages">'.$pages_text.'</span></li>';
	    			}
	            //Displays a link to the previous post which exists in chronological order from the current post.
	    			/*http://codex.wordpress.org/Function_Reference/previous_post_link*/
	    			if ($paged > 1) {
	    				$output .= '<li class="prev">' . get_previous_posts_link($pagenavi_options['prev_text']) . '</li>';
	    			}

	    			if ($start_page >= 2 && $pages_to_show < $max_page) {
	    				$first_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['first_text']);
	                //esc_url(): Encodes < > & " ' (less than, greater than, ampersand, double quote, single quote).
	    				/*http://codex.wordpress.org/Data_Validation*/
	                //get_pagenum_link():(wp-includes/link-template.php)-Retrieve get links for page numbers.
	    				$output .= '<li><a href="'.esc_url(get_pagenum_link()).'" class="first" title="'.$first_page_text.'">1</a></li>';
	    				if(!empty($pagenavi_options['dotleft_text'])) {
	    					$output .= '<li><span class="expand">'.$pagenavi_options['dotleft_text'].'</span></li>';
	    				}
	    			}

	    			if($larger_page_to_show > 0 && $larger_start_page_start > 0 && $larger_start_page_end <= $max_page) {
	    				for($i = $larger_start_page_start; $i < $larger_start_page_end; $i+=$larger_page_multiple) {
	    					$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
	    					$output .= '<li><a href="'.esc_url(get_pagenum_link($i)).'" class="single_page" title="'.$page_text.'">'.$page_text.'</a></li>';
	    				}
	    			}

	    			for($i = $start_page; $i  <= $end_page; $i++) {
	    				if($i == $paged) {
	    					$current_page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['current_text']);
	    					$output .= '<li><span class="current">'.$current_page_text.'</span></li>';
	    				} else {
	    					$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
	    					$output .= '<li><a href="'.esc_url(get_pagenum_link($i)).'" class="single_page" title="'.$page_text.'">'.$page_text.'</a></li>';
	    				}
	    			}

	    			if ($end_page < $max_page) {
	    				if(!empty($pagenavi_options['dotright_text'])) {
	    					$output .= '<li><span class="expand">'.$pagenavi_options['dotright_text'].'</span></li>';
	    				}
	    				$last_page_text = str_replace("%TOTAL_PAGES%", number_format_i18n($max_page), $pagenavi_options['last_text']);
	    				$output .= '<li><a href="'.esc_url(get_pagenum_link($max_page)).'" class="last" title="'.$last_page_text.'">'.$max_page.'</a></li>';
	    			}
	    			$output .= '<li class="next">' . get_next_posts_link($pagenavi_options['next_text'], $max_page) . '</li>';

	    			if($larger_page_to_show > 0 && $larger_end_page_start < $max_page) {
	    				for($i = $larger_end_page_start; $i <= $larger_end_page_end; $i+=$larger_page_multiple) {
	    					$page_text = str_replace("%PAGE_NUMBER%", number_format_i18n($i), $pagenavi_options['page_text']);
	    					$output .= '<li><a href="'.esc_url(get_pagenum_link($i)).'" class="single_page" title="'.$page_text.'">'.$page_text.'</a></li>';
	    				}
	    			}
	    			$output .= '</ul>'.$after."\n";
	    		}
	    	}

	    	return $output;
	    }		


	/* ==================================================
	
	SHORTCODE GENERATOR SETUP
	
	================================================== */
	
	function neighborhood_custom_mce_styles( $args ) {

		$style_formats = array (
			array( 'title' => 'Impact Text', 'selector' => 'p', 'classes' => 'impact-text' ),
		);

		$args['style_formats'] = json_encode( $style_formats );

		return $args;
	}

	add_filter('tiny_mce_before_init', 'neighborhood_custom_mce_styles');

	function neighborhood_mce_add_buttons( $buttons ){
		array_splice( $buttons, 1, 0, 'styleselect' );
		return $buttons;
	}
	add_filter( 'mce_buttons_2', 'neighborhood_mce_add_buttons' );

	function neighborhood_add_editor_styles() {
		add_editor_style( '/css/editor-style.css' );
	}
	add_action( 'init', 'neighborhood_add_editor_styles' );

    /* DEMO CONTENT IMPORTER
    ================================================== */
    function ocdi_import_files() {
    	return array(
    		array(
    			'import_file_name'           => 'Main Demo',
    			'import_file_url'            => 'http://www.swiftideas.com/democontent/neighborhood/main/demo-content.xml',
    			'import_widget_file_url'     => 'http://www.swiftideas.com/democontent/neighborhood/main/widgets.wie',
    			'import_customizer_file_url' => 'http://www.swiftideas.com/democontent/neighborhood/main/customizer.dat',
    			'import_preview_image_url'   => 'http://www.swiftideas.com/democontent/neighborhood/main/image.jpg',
    			'import_notice'              => '',
    			'preview_url'                => 'http://neighborhood.swiftideas.com',
    		),
    	);
    }
    add_filter( 'pt-ocdi/import_files', 'ocdi_import_files' );

    if ( ! function_exists( 'neighborhood_democontent_after_import' ) ) {
    	function neighborhood_democontent_after_import( $selected_import ) {

    		if ( 'Main Demo' === $selected_import['import_file_name'] ) {
		        //Set Menu
    			$alt_menu = get_term_by('name', 'Alt Menu', 'nav_menu');
    			$main_menu = get_term_by('name', 'Main Menu', 'nav_menu');
    			set_theme_mod( 'nav_menu_locations' , array( 
    				'top_bar_menu' => $alt_menu->term_id, 
    				'main_navigation' => $main_menu->term_id 
    			) 
    		);

		       //Set Front page
    			$page = get_page_by_title( 'Home');
    			if ( isset( $page->ID ) ) {
    				update_option( 'page_on_front', $page->ID );
    				update_option( 'show_on_front', 'page' );
    			}

		       //Import Revolution Slider
    			if ( class_exists( 'RevSlider' ) ) {
    				$slider_array = array(
    					get_template_directory()."/includes/demo/homeslider.zip"
    				);

    				$slider = new RevSlider();

    				foreach($slider_array as $filepath){
    					$slider->importSliderFromPost(true,true,$filepath);  
    				}

    				echo ' Slider processed';
    			}
    		}

    	}
    	add_action( 'pt-ocdi/after_import', 'neighborhood_democontent_after_import' );
    }

    add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );


	/* META BOX MISSING FALLBACK
	================================================== */
	if (!function_exists('rwmb_meta')) {
		function rwmb_meta($key, $args, $postID = null) {
			if (!$postID) {
				$postID = get_the_ID();
			}
			return get_post_meta($postID, $key, true);
		}
	}	

	add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
	//add_action( 'woocommerce_before_cart' , 'wc_minimum_order_amount' );
    add_action('woocommerce_before_cart_totals', 'wc_minimum_order_amount');
    //add_action( 'woocommerce_after_cart_table' , 'wc_minimum_order_amount' );

	function wc_minimum_order_amount() {
	    global $difference;
        $subtotal = WC()->cart->get_subtotal_tax() + WC()->cart->get_subtotal();
		$minimum = 25;
		$difference = $minimum - $subtotal;
			if ( $subtotal < $minimum ) { if( is_cart() ) { wc_print_notice( sprintf( 'Der Mindestbestellwert beträgt %s pro Bestellung. Der aktuelle Bestellwert beträgt %s. Die Differenz von %s wird automatisch dazu berechnet!' ,
			wc_price( $minimum ), 
			wc_price( $subtotal),
            wc_price($difference)
		), 'error');
			$result = (string)($difference + WC()->cart->total);
			WC()->cart->set_total($result);
			set_query_var('comments_per_page', 20);
	} else {
//		wc_add_notice(
//			sprintf( 'Der Mindestbestellwert beträgt %s pro Bestellung. Der aktuelle Bestellwert beträgt %s. Die Differenz von %s wird automatisch dazu berechnet!' ,
//			wc_price( $minimum ),
//			wc_price( $subtotal ),
//            wc_price($difference)
//		), 'error');
                
	}
}else{
			    wc_clear_notices();
            }
}
add_filter( 'woocommerce_order_amount_total', 'custom_cart_total', 1 );
function custom_cart_total($order_total) {
    remove_filter( 'woocommerce_order_amount_total', 'custom_cart_total', 1 );
    $minimum = 25;
    $subtotal = 0;
      if( WC()->cart != null) {
    $subtotal = WC()->cart->get_subtotal_tax() + WC()->cart->get_subtotal();
    }
    if($subtotal > 0) {
        if ($minimum - $subtotal > 0) {
            $difference = $minimum - $subtotal;
            $order_total += $difference;
            return (string)$order_total;
        } else {
            return (string)$order_total;
        }
    }else{
        remove_filter( 'woocommerce_order_amount_total', 'custom_cart_total', 1 );

        return $order_total;
    }
}
add_filter('online_designer_add_price_information', 'online_designer_add_price_information', 10, 2);
function online_designer_add_price_information($price_t, $text_information = "")
{
	$price = get_option('nbdesigner_price_' . $price_t);
	$text = "";
	if ($text_information == 'full-information') {
		switch ($price_t) {
			case("add_text"):
			$text = sprintf(__('Preis für Text: +%s CHF', 'neighborhood-child'), $price);
			break;
			case("upload_image"):
			$text = sprintf(__('Preis für Bilder: +%s CHF', 'neighborhood-child'), $price);
			break;
			case("use_cliparts"):
			$text = sprintf(__('Preis für Cliparts: +%s CHF', 'neighborhood-child'), $price);
			break;
		}
		if ($text != "")
			echo '<span style="color: #4F5467; display: block; margin-top: 15px;">' . $text . '</span></div><div style="clear: both;">';
	} else {
		echo '+'.$price.' CHF</span>';
	}
}

add_filter( 'woocommerce_adjust_non_base_location_prices', 'woocommerce_adjust_non_base_location_prices' );
function woocommerce_adjust_non_base_location_prices() {
    return false;
}

add_action('show_product_minimal_price', 'show_product_minimal_price');
function show_product_minimal_price()
{
    global $product, $nbd_fontend_printing_options;
    $product_id = $product->get_id();
    $enable_from = get_post_meta($product_id, '_nbo_enable', true);
    $fin_price = null;
    $content = "";
    if(apply_filters('is_products_category', $product_id)) {
        $option_id = $nbd_fontend_printing_options->get_product_option($product_id);
        if ($option_id) {
            $_options = $nbd_fontend_printing_options->get_option($option_id);
            if ($_options) {
                $options = unserialize($_options['fields']);
                $price = null;
                $product = wc_get_product($product_id);
                if ($options['quantity_discount_type'] == 'f') {
                    $price = $product->price - $options['quantity_breaks'][count($options['quantity_breaks']) - 1]['dis'];
                } elseif ($options['quantity_discount_type'] == 'p') {
                    $price = $product->price - ($options['quantity_breaks'][count($options['quantity_breaks']) - 1]['dis'] * $product->price / 100);
                } else {
                    $price = $product->price;
                }
                $price < 0 ? $price = 0 : "";
                if($price == 0) $price = $product->price;
                $fin_price = wc_price($price);

            }
        } else {
            $fin_price = wc_price($product->price);
        }
    } else {
        $fin_price = wc_price( wc_get_price_including_tax( $product ) );
    }
    if($enable_from) {
        $content =  '<span class="nbo-base-price-html">'.__('From', 'web-to-print-online-designer').'</span> ';
    }
    $content .= $fin_price;
    echo $content;
}

add_filter('is_products_category', 'is_products_category');
function is_products_category($product_id) {
    foreach(wp_get_post_terms( $product_id, 'product_cat') as $category) {
        if($category->parent == 357) return true;
    }
    return false;
}
