<?php

if ( ! function_exists( 'seafarer_enqueue_woocommerce_css_assets' ) ) {
	/**
	 * Function that enqueue 3rd party plugins script
	 */
	function seafarer_enqueue_woocommerce_css_assets() {

		if ( seafarer_is_woo_page( 'single' ) && seafarer_get_post_value_through_levels( 'qodef_woo_single_enable_image_lightbox' ) === 'magnific-popup' ) {
			wp_enqueue_style( 'magnific-popup' );
			wp_enqueue_script( 'jquery-magnific-popup' );
		}
	}

	add_action( 'seafarer_action_before_main_css', 'seafarer_enqueue_woocommerce_css_assets' );
}

if ( ! function_exists( 'seafarer_is_woo_page' ) ) {
	/**
	 * Function that check WooCommerce pages
	 *
	 * @param string $page
	 *
	 * @return bool
	 */
	function seafarer_is_woo_page( $page ) {
		switch ( $page ) {
			case 'shop':
				return function_exists( 'is_shop' ) && is_shop();
			case 'single':
				return is_singular( 'product' );
			case 'cart':
				return function_exists( 'is_cart' ) && is_cart();
			case 'checkout':
				return function_exists( 'is_checkout' ) && is_checkout();
			case 'account':
				return function_exists( 'is_account_page' ) && is_account_page();
			case 'category':
				return function_exists( 'is_product_category' ) && is_product_category();
			case 'tag':
				return function_exists( 'is_product_tag' ) && is_product_tag();
			case 'any':
				return (
					function_exists( 'is_shop' ) && is_shop() ||
					is_singular( 'product' ) ||
					function_exists( 'is_cart' ) && is_cart() ||
					function_exists( 'is_checkout' ) && is_checkout() ||
					function_exists( 'is_account_page' ) && is_account_page() ||
					function_exists( 'is_product_category' ) && is_product_category() ||
					function_exists( 'is_product_tag' ) && is_product_tag()
				);
			case 'archive':
				return ( function_exists( 'is_shop' ) && is_shop() ) || ( function_exists( 'is_product_category' ) && is_product_category() ) || ( function_exists( 'is_product_tag' ) && is_product_tag() );
			default:
				return false;
		}
	}
}

if ( ! function_exists( 'seafarer_get_woo_main_page_classes' ) ) {
	/**
	 * Function that return current WooCommerce page class name
	 *
	 * @return string
	 */
	function seafarer_get_woo_main_page_classes() {
		$classes = array();

		if ( seafarer_is_woo_page( 'shop' ) ) {
			$classes[] = 'qodef--list';
		}

		if ( seafarer_is_woo_page( 'single' ) ) {
			$classes[] = 'qodef--single';

			if ( seafarer_get_post_value_through_levels( 'qodef_woo_single_enable_image_lightbox' ) === 'photo-swipe' ) {
				$classes[] = 'qodef-popup--photo-swipe';
			}

			if ( seafarer_get_post_value_through_levels( 'qodef_woo_single_enable_image_lightbox' ) === 'magnific-popup' ) {
				$classes[] = 'qodef-popup--magnific-popup';
				// add classes to initialize lightbox from theme
				$classes[] = 'qodef-magnific-popup';
				$classes[] = 'qodef-popup-gallery';
			}
		}

		if ( seafarer_is_woo_page( 'cart' ) ) {
			$classes[] = 'qodef--cart';
		}

		if ( seafarer_is_woo_page( 'checkout' ) ) {
			$classes[] = 'qodef--checkout';
		}

		if ( seafarer_is_woo_page( 'account' ) ) {
			$classes[] = 'qodef--account';
		}

		return apply_filters( 'seafarer_filter_main_page_classes', implode( ' ', $classes ) );
	}
}

if ( ! function_exists( 'seafarer_woo_get_global_product' ) ) {
	/**
	 * Function that return global WooCommerce object
	 *
	 * @return object
	 */
	function seafarer_woo_get_global_product() {
		global $product;

		return $product;
	}
}

if ( ! function_exists( 'seafarer_woo_get_main_shop_page_id' ) ) {
	/**
	 * Function that return main shop page ID
	 *
	 * @return int
	 */
	function seafarer_woo_get_main_shop_page_id() {
		// Get page id from options table
		$shop_id = get_option( 'woocommerce_shop_page_id' );

		if ( ! empty( $shop_id ) ) {
			return $shop_id;
		}

		return false;
	}
}

if ( ! function_exists( 'seafarer_woo_set_main_shop_page_id' ) ) {
	/**
	 * Function that set main shop page ID for get_post_meta options
	 *
	 * @param int $post_id
	 *
	 * @return int
	 */
	function seafarer_woo_set_main_shop_page_id( $post_id ) {

		if ( seafarer_is_woo_page( 'archive' ) || seafarer_is_woo_page( 'single' ) ) {
			$shop_id = seafarer_woo_get_main_shop_page_id();

			if ( ! empty( $shop_id ) ) {
				$post_id = $shop_id;
			}
		}

		return $post_id;
	}

	add_filter( 'seafarer_filter_page_id', 'seafarer_woo_set_main_shop_page_id' );
	add_filter( 'qode_framework_filter_page_id', 'seafarer_woo_set_main_shop_page_id' );
}

if ( ! function_exists( 'seafarer_woo_set_page_title_text' ) ) {
	/**
	 * Function that returns current page title text for WooCommerce pages
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	function seafarer_woo_set_page_title_text( $title ) {

		if ( seafarer_is_woo_page( 'shop' ) || seafarer_is_woo_page( 'single' ) ) {
			$shop_id = seafarer_woo_get_main_shop_page_id();

			$title = ! empty( $shop_id ) ? get_the_title( $shop_id ) : esc_html__( 'Shop', 'seafarer' );
		} elseif ( seafarer_is_woo_page( 'category' ) || seafarer_is_woo_page( 'tag' ) ) {
			$taxonomy_slug = seafarer_is_woo_page( 'tag' ) ? 'product_tag' : 'product_cat';
			$taxonomy      = get_term( get_queried_object_id(), $taxonomy_slug );

			if ( ! empty( $taxonomy ) ) {
				$title = esc_html( $taxonomy->name );
			}
		}

		return $title;
	}

	add_filter( 'seafarer_filter_page_title_text', 'seafarer_woo_set_page_title_text' );
}

if ( ! function_exists( 'seafarer_woo_breadcrumbs_title' ) ) {
	/**
	 * Improve main breadcrumbs template with additional cases
	 *
	 * @param string $wrap_child
	 * @param array $settings
	 *
	 * @return string
	 */
	function seafarer_woo_breadcrumbs_title( $wrap_child, $settings ) {

		if ( seafarer_is_woo_page( 'category' ) || seafarer_is_woo_page( 'tag' ) ) {
			$wrap_child    = '';
			$taxonomy_slug = seafarer_is_woo_page( 'tag' ) ? 'product_tag' : 'product_cat';
			$taxonomy      = get_term( get_queried_object_id(), $taxonomy_slug );

			if ( isset( $taxonomy->parent ) && 0 !== $taxonomy->parent ) {
				$parent      = get_term( $taxonomy->parent );
				$wrap_child .= sprintf( $settings['link'], get_term_link( $parent->term_id ), $parent->name ) . $settings['separator'];
			}

			if ( ! empty( $taxonomy ) ) {
				$wrap_child .= sprintf( $settings['current_item'], esc_attr( $taxonomy->name ) );
			}
		} elseif ( seafarer_is_woo_page( 'shop' ) ) {
			$shop_id    = seafarer_woo_get_main_shop_page_id();
			$shop_title = ! empty( $shop_id ) ? get_the_title( $shop_id ) : esc_html__( 'Shop', 'seafarer' );

			$wrap_child .= sprintf( $settings['current_item'], $shop_title );

		} elseif ( seafarer_is_woo_page( 'single' ) ) {
			$wrap_child = '';
			$post_terms = wp_get_post_terms( get_the_ID(), 'product_cat' );

			if ( ! empty( $post_terms ) ) {
				$post_term = $post_terms[0];

				if ( isset( $post_term->parent ) && 0 !== $post_term->parent ) {
					$parent      = get_term( $post_term->parent );
					$wrap_child .= sprintf( $settings['link'], get_term_link( $parent->term_id ), $parent->name ) . $settings['separator'];
				}
				$wrap_child .= sprintf( $settings['link'], get_term_link( $post_term ), $post_term->name ) . $settings['separator'];
			}

			$wrap_child .= sprintf( $settings['current_item'], get_the_title() );
		}

		return $wrap_child;
	}

	add_filter( 'seafarer_core_filter_breadcrumbs_content', 'seafarer_woo_breadcrumbs_title', 10, 2 );
}

if ( ! function_exists( 'seafarer_woo_single_add_theme_supports' ) ) {
	/**
	 * Function that add native WooCommerce supports
	 */
	function seafarer_woo_single_add_theme_supports() {
		// Add featured image zoom functionality on product single page
		$is_zoom_enabled = seafarer_get_post_value_through_levels( 'qodef_woo_single_enable_image_zoom' ) !== 'no';

		if ( $is_zoom_enabled ) {
			add_theme_support( 'wc-product-gallery-zoom' );
		}

		// Add photo swipe lightbox functionality on product single images page
		$is_photo_swipe_enabled = seafarer_get_post_value_through_levels( 'qodef_woo_single_enable_image_lightbox' ) === 'photo-swipe';

		if ( $is_photo_swipe_enabled ) {
			add_theme_support( 'wc-product-gallery-lightbox' );
		}
	}

	add_action( 'wp_loaded', 'seafarer_woo_single_add_theme_supports', 11 ); // permission 11 is set because options are init with permission 10 inside framework plugin
}

if ( ! function_exists( 'seafarer_woo_single_disable_page_title' ) ) {
	/**
	 * Function that disable page title area for single product page
	 *
	 * @param bool $enable_page_title
	 *
	 * @return bool
	 */
	function seafarer_woo_single_disable_page_title( $enable_page_title ) {
		$is_enabled = seafarer_get_post_value_through_levels( 'qodef_woo_single_enable_page_title' ) !== 'no';

		if ( ! $is_enabled && seafarer_is_woo_page( 'single' ) ) {
			$enable_page_title = false;
		}

		return $enable_page_title;
	}

	add_filter( 'seafarer_filter_enable_page_title', 'seafarer_woo_single_disable_page_title' );
}

if ( ! function_exists( 'seafarer_woo_single_thumb_images_position' ) ) {
	/**
	 * Function that changes the layout of thumbnails on single product page
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	function seafarer_woo_single_thumb_images_position( $classes ) {
		$product_thumbnail_position = seafarer_is_installed( 'core' ) ? seafarer_get_post_value_through_levels( 'qodef_woo_single_thumb_images_position' ) : 'below';

		if ( ! empty( $product_thumbnail_position ) ) {
			$classes[] = 'qodef-position--' . $product_thumbnail_position;
		}

		return $classes;
	}

	add_filter( 'woocommerce_single_product_image_gallery_classes', 'seafarer_woo_single_thumb_images_position' );
}

if ( ! function_exists( 'seafarer_set_woo_custom_sidebar_name' ) ) {
	/**
	 * Function that return sidebar name
	 *
	 * @param string $sidebar_name
	 *
	 * @return string
	 */
	function seafarer_set_woo_custom_sidebar_name( $sidebar_name ) {

		if ( seafarer_is_woo_page( 'archive' ) ) {
			$option = seafarer_get_post_value_through_levels( 'qodef_woo_product_list_custom_sidebar' );

			if ( isset( $option ) && ! empty( $option ) ) {
				$sidebar_name = $option;
			}
		}

		return $sidebar_name;
	}

	add_filter( 'seafarer_filter_sidebar_name', 'seafarer_set_woo_custom_sidebar_name' );
}

if ( ! function_exists( 'seafarer_set_woo_sidebar_layout' ) ) {
	/**
	 * Function that return sidebar layout
	 *
	 * @param string $layout
	 *
	 * @return string
	 */
	function seafarer_set_woo_sidebar_layout( $layout ) {

		if ( seafarer_is_woo_page( 'archive' ) ) {
			$option = seafarer_get_post_value_through_levels( 'qodef_woo_product_list_sidebar_layout' );

			if ( isset( $option ) && ! empty( $option ) ) {
				$layout = $option;
			}
		}

		return $layout;
	}

	add_filter( 'seafarer_filter_sidebar_layout', 'seafarer_set_woo_sidebar_layout' );
}

if ( ! function_exists( 'seafarer_set_woo_sidebar_grid_gutter_classes' ) ) {
	/**
	 * Function that returns grid gutter classes
	 *
	 * @param string $classes
	 *
	 * @return string
	 */
	function seafarer_set_woo_sidebar_grid_gutter_classes( $classes ) {

		if ( seafarer_is_woo_page( 'archive' ) ) {
			$option = seafarer_get_post_value_through_levels( 'qodef_woo_product_list_sidebar_grid_gutter' );

			if ( isset( $option ) && ! empty( $option ) ) {
				$classes = 'qodef-gutter--' . esc_attr( $option );
			}
		}

		return $classes;
	}

	add_filter( 'seafarer_filter_grid_gutter_classes', 'seafarer_set_woo_sidebar_grid_gutter_classes' );
}

if ( ! function_exists( 'seafarer_set_woo_review_form_fields' ) ) {
	/**
	 * Function that add woo rating to WordPress comment form fields
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	function seafarer_set_woo_review_form_fields( $args ) {
		$comment_args = seafarer_get_comment_form_args( array( 'comment_placeholder' => esc_attr__( 'Your Review *', 'seafarer' ) ) );

		if ( key_exists( 'comment_field', $comment_args ) ) {

			if ( wc_review_ratings_enabled() ) {
				$ratings_html = '<p class="stars qodef-comment-form-ratings">';
				for ( $i = 1; $i <= 5; $i ++ ) {
					$ratings_html .= '<a class="star-' . intval( $i ) . '" href="#">' . intval( $i ) . seafarer_get_svg_icon( 'star' ) . '</a>';
				}
				$ratings_html .= '</p>';

				// add rating stuff before textarea element
				// copied from wp-content/plugins/woocommerce/templates/single-product-reviews.php
				$comment_args['comment_field'] = '<div class="comment-form-rating">
					<label for="rating">' . esc_html__( 'Your Rating ', 'seafarer' ) . ( wc_review_ratings_required() ? '<span class="required">*</span>' : '' ) . '</label>
					' . $ratings_html . '
					<select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'seafarer' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'seafarer' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'seafarer' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'seafarer' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'seafarer' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'seafarer' ) . '</option>
					</select>
				</div>' . $comment_args['comment_field'];
			}
		}

		// Removed url field from form
		if ( isset( $comment_args['fields']['url'] ) ) {
			unset( $comment_args['fields']['url'] );
		}

		// Override WooCommerce review arguments with ours
		$args = array_merge( $args, $comment_args );

		return $args;
	}

	add_filter( 'woocommerce_product_review_comment_form_args', 'seafarer_set_woo_review_form_fields' );
}
