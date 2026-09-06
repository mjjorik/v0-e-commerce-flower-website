<?php
/**
 * Wildflower checkout details and delivery scheduling.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the current time in the studio's business timezone.
 *
 * The site historically had no WordPress timezone configured, so checkout
 * cutoffs must not inherit UTC from that setting.
 *
 * @return DateTimeImmutable
 */
function wildflower_checkout_now() {
	$timezone = new DateTimeZone( 'America/New_York' );
	$now      = new DateTimeImmutable( 'now', $timezone );
	$filtered = apply_filters( 'wildflower_checkout_now', $now );

	if ( $filtered instanceof DateTimeInterface ) {
		return DateTimeImmutable::createFromInterface( $filtered )->setTimezone( $timezone );
	}

	return $now;
}

/**
 * Same-day cutoff hour in Boston (24-hour clock).
 *
 * @return int
 */
function wildflower_checkout_same_day_cutoff_hour() {
	return min( 23, max( 0, (int) apply_filters( 'wildflower_checkout_same_day_cutoff_hour', 13 ) ) );
}

/**
 * Whether the studio's same-day cutoff has passed.
 *
 * @return bool
 */
function wildflower_checkout_same_day_cutoff_passed() {
	return (int) wildflower_checkout_now()->format( 'G' ) >= wildflower_checkout_same_day_cutoff_hour();
}

/**
 * Add-ons travel with the bouquet and must not invalidate its availability.
 *
 * @param WC_Product $product Product in the cart.
 * @return bool
 */
function wildflower_checkout_product_is_add_on( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return false;
	}

	$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	return has_term( 'add-ons', 'product_cat', $product_id );
}

/**
 * Whether a product is marked as available for same-day delivery.
 *
 * @param WC_Product $product Product in the cart.
 * @return bool
 */
function wildflower_checkout_product_allows_same_day( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return false;
	}

	$product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
	return has_term( 'same-day', 'pa_availability', $product_id );
}

/**
 * A cart qualifies when it contains an eligible bouquet, no scheduled-only
 * product, and the 1 PM ET cutoff has not passed. Gift add-ons are neutral.
 *
 * @return bool
 */
function wildflower_checkout_cart_allows_same_day() {
	if ( wildflower_checkout_same_day_cutoff_passed() || ! function_exists( 'WC' ) || ! WC()->cart ) {
		return false;
	}

	$has_eligible_product = false;

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
		if ( ! $product instanceof WC_Product ) {
			return false;
		}

		if ( wildflower_checkout_product_is_add_on( $product ) ) {
			continue;
		}

		if ( ! wildflower_checkout_product_allows_same_day( $product ) ) {
			return false;
		}

		$has_eligible_product = true;
	}

	return $has_eligible_product;
}

/**
 * Minimum notice in calendar days for the current cart.
 *
 * @return int
 */
function wildflower_checkout_minimum_notice_days() {
	return wildflower_checkout_cart_allows_same_day() ? 0 : 1;
}

/**
 * Earliest date accepted by both the calendar and server validation.
 *
 * @return string YYYY-MM-DD.
 */
function wildflower_checkout_earliest_date() {
	return wildflower_checkout_now()
		->modify( '+' . wildflower_checkout_minimum_notice_days() . ' day' )
		->format( 'Y-m-d' );
}

/**
 * Boston Flowers' operational rule: after noon, morning is unavailable for
 * the earliest bookable date. Same-day orders never offer a morning window.
 *
 * @param string $delivery_date YYYY-MM-DD.
 * @return array<int,string>
 */
function wildflower_checkout_allowed_windows( $delivery_date ) {
	$windows       = array( 'morning', 'afternoon', 'evening' );
	$earliest_date = wildflower_checkout_earliest_date();

	if ( wildflower_checkout_cart_allows_same_day() && $delivery_date === $earliest_date ) {
		return array( 'afternoon', 'evening' );
	}

	if ( $delivery_date === $earliest_date && (int) wildflower_checkout_now()->format( 'G' ) >= 12 ) {
		return array( 'afternoon', 'evening' );
	}

	return $windows;
}

/**
 * Detect a local-pickup method if one is introduced later.
 *
 * @return bool
 */
function wildflower_checkout_is_pickup() {
	if ( ! function_exists( 'WC' ) || ! WC()->session ) {
		return false;
	}

	foreach ( (array) WC()->session->get( 'chosen_shipping_methods', array() ) as $method ) {
		if ( false !== strpos( (string) $method, 'local_pickup' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Require the customer identity and contact details used by the studio.
 *
 * @param array $fields Checkout fields.
 * @return array
 */
function wildflower_checkout_required_customer_fields( $fields ) {
	foreach ( array( 'billing_first_name', 'billing_last_name', 'billing_phone', 'billing_email' ) as $key ) {
		if ( isset( $fields['billing'][ $key ] ) ) {
			$fields['billing'][ $key ]['required'] = true;
		}
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'wildflower_checkout_required_customer_fields', 20 );

/**
 * Keep the phone requirement intact when country locale rules refresh fields.
 *
 * @param array $locale Default country locale.
 * @return array
 */
function wildflower_checkout_required_phone_locale( $locale ) {
	if ( isset( $locale['phone'] ) ) {
		$locale['phone']['required'] = true;
	}

	return $locale;
}
add_filter( 'woocommerce_get_country_locale_default', 'wildflower_checkout_required_phone_locale', 20 );

/**
 * Let WooCommerce update the correct phone field when country rules change.
 *
 * @param array $selectors Locale field selectors.
 * @return array
 */
function wildflower_checkout_phone_field_selector( $selectors ) {
	$selectors['phone'] = '#billing_phone_field';
	return $selectors;
}
add_filter( 'woocommerce_country_locale_field_selectors', 'wildflower_checkout_phone_field_selector' );

/**
 * Enqueue checkout-only behavior and the core datepicker.
 */
function wildflower_checkout_assets() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_wc_endpoint_url( 'order-received' ) ) {
		return;
	}

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script(
		'wildflower-checkout',
		get_template_directory_uri() . '/assets/js/checkout.js',
		array( 'jquery', 'jquery-ui-datepicker', 'wc-checkout' ),
		wildflower_ver( '/assets/js/checkout.js' ),
		true
	);

	wp_localize_script(
		'wildflower-checkout',
		'wildflowerCheckout',
		array(
			'ajaxUrl'          => WC_AJAX::get_endpoint( 'wildflower_sync_express_checkout' ),
			'nonce'            => wp_create_nonce( 'wildflower_sync_express_checkout' ),
			'earliestDate'     => wildflower_checkout_earliest_date(),
			'minNoticeDays'    => wildflower_checkout_minimum_notice_days(),
			'sameDayAllowed'   => wildflower_checkout_cart_allows_same_day(),
			'afterNoon'        => (int) wildflower_checkout_now()->format( 'G' ) >= 12,
			'messages'         => array(
				'dateRequired'   => __( 'Delivery date is required.', 'wildflower' ),
				'windowRequired' => __( 'Please select a preferred delivery window.', 'wildflower' ),
				'windowCutoff'   => __( 'Morning delivery is unavailable for this date. Please choose afternoon or evening.', 'wildflower' ),
				'expressReady'   => __( 'Complete the required checkout fields above to use express checkout.', 'wildflower' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'wildflower_checkout_assets', 20 );

/**
 * Render delivery scheduling immediately after billing details.
 *
 * @param WC_Checkout $checkout Checkout instance.
 */
function wildflower_checkout_render_delivery_fields( $checkout ) {
	$same_day_allowed = wildflower_checkout_cart_allows_same_day();

	if ( $same_day_allowed ) {
		$notice = __( 'Same-day delivery is available until 1 PM ET for these flowers. Same-day orders arrive in the afternoon or evening.', 'wildflower' );
	} elseif ( wildflower_checkout_same_day_cutoff_passed() ) {
		$notice = __( "Today's 1 PM ET same-day cutoff has passed. Please choose tomorrow or a later delivery date.", 'wildflower' );
	} else {
		$notice = __( 'Same-day delivery is available for eligible bouquets. For this order, please choose tomorrow or a later date.', 'wildflower' );
	}
	?>
	<section id="wf_delivery_fields" class="wf-delivery-fields" aria-labelledby="wf-delivery-title">
		<div class="wf-delivery-fields__heading">
			<p class="wf-delivery-fields__eyebrow"><?php esc_html_e( 'Hand-delivered in Greater Boston', 'wildflower' ); ?></p>
			<h3 id="wf-delivery-title"><?php esc_html_e( 'Delivery details', 'wildflower' ); ?></h3>
		</div>
		<?php
		woocommerce_form_field(
			'wildflower_delivery_date',
			array(
				'type'              => 'text',
				'class'             => array( 'wf-delivery-date-field', 'form-row-wide' ),
				'label'             => __( 'Delivery date', 'wildflower' ),
				'placeholder'       => __( 'Select a date', 'wildflower' ),
				'required'          => true,
				'custom_attributes' => array(
					'min'          => wildflower_checkout_earliest_date(),
					'autocomplete' => 'off',
					'inputmode'    => 'none',
					'readonly'     => 'readonly',
				),
			),
			$checkout->get_value( 'wildflower_delivery_date' )
		);

		woocommerce_form_field(
			'wildflower_delivery_window',
			array(
				'type'     => 'select',
				'class'    => array( 'wf-delivery-window-field', 'form-row-wide' ),
				'label'    => __( 'Preferred delivery window', 'wildflower' ),
				'required' => true,
				'options'  => array(
					''          => __( 'Select a delivery window', 'wildflower' ),
					'morning'   => __( 'Morning (8 AM–12 PM)', 'wildflower' ),
					'afternoon' => __( 'Afternoon (12–4 PM)', 'wildflower' ),
					'evening'   => __( 'Evening (4–8 PM)', 'wildflower' ),
				),
			),
			$checkout->get_value( 'wildflower_delivery_window' )
		);
		?>
		<p class="wf-delivery-notice" data-wf-delivery-notice><?php echo esc_html( $notice ); ?></p>
	</section>
	<?php
}
add_action( 'woocommerce_after_checkout_billing_form', 'wildflower_checkout_render_delivery_fields' );

/**
 * Normalize a posted date and ensure it is a real calendar date.
 *
 * @param mixed $value Posted value.
 * @return string Empty when invalid.
 */
function wildflower_checkout_clean_date( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$value = sanitize_text_field( wp_unslash( (string) $value ) );
	$date  = DateTimeImmutable::createFromFormat( '!Y-m-d', $value, new DateTimeZone( 'America/New_York' ) );

	return $date && $date->format( 'Y-m-d' ) === $value ? $value : '';
}

/**
 * Normalize a delivery-window key from an untrusted request.
 *
 * @param mixed $value Posted value.
 * @return string
 */
function wildflower_checkout_clean_window( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	return sanitize_key( wp_unslash( (string) $value ) );
}

/**
 * Validate delivery fields during classic checkout.
 */
function wildflower_checkout_validate_delivery_fields() {
	if ( wildflower_checkout_is_pickup() ) {
		return;
	}

	$date_raw   = isset( $_POST['wildflower_delivery_date'] ) ? $_POST['wildflower_delivery_date'] : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$window_raw = isset( $_POST['wildflower_delivery_window'] ) ? $_POST['wildflower_delivery_window'] : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$date       = wildflower_checkout_clean_date( $date_raw );
	$window     = wildflower_checkout_clean_window( $window_raw );

	if ( ! is_scalar( $date_raw ) || '' === trim( (string) $date_raw ) ) {
		wc_add_notice( __( 'Delivery date is a required field.', 'wildflower' ), 'error' );
		return;
	}

	if ( '' === $date ) {
		wc_add_notice( __( 'Please select a valid delivery date.', 'wildflower' ), 'error' );
		return;
	}

	if ( $date < wildflower_checkout_earliest_date() ) {
		wc_add_notice( __( 'That delivery date is no longer available. Please select the next available date.', 'wildflower' ), 'error' );
	}

	if ( '' === $window ) {
		wc_add_notice( __( 'Please select a preferred delivery window.', 'wildflower' ), 'error' );
		return;
	}

	if ( ! in_array( $window, wildflower_checkout_allowed_windows( $date ), true ) ) {
		wc_add_notice( __( 'That delivery window is unavailable for the selected date. Please choose afternoon or evening.', 'wildflower' ), 'error' );
	}
}
add_action( 'woocommerce_checkout_process', 'wildflower_checkout_validate_delivery_fields' );

/**
 * Save delivery fields on a classic checkout order.
 *
 * @param WC_Order $order Order being created.
 */
function wildflower_checkout_save_delivery_fields( $order ) {
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$date_raw   = isset( $_POST['wildflower_delivery_date'] ) ? $_POST['wildflower_delivery_date'] : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$window_raw = isset( $_POST['wildflower_delivery_window'] ) ? $_POST['wildflower_delivery_window'] : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$date       = wildflower_checkout_clean_date( $date_raw );
	$window     = wildflower_checkout_clean_window( $window_raw );

	if ( $date ) {
		$order->update_meta_data( '_wildflower_delivery_date', $date );
	}
	if ( in_array( $window, array( 'morning', 'afternoon', 'evening' ), true ) ) {
		$order->update_meta_data( '_wildflower_delivery_window', $window );
	}
}
add_action( 'woocommerce_checkout_create_order', 'wildflower_checkout_save_delivery_fields', 20 );

/**
 * Human-readable delivery-window label.
 *
 * @param string $window Window key.
 * @return string
 */
function wildflower_checkout_window_label( $window ) {
	$labels = array(
		'morning'   => __( 'Morning (8 AM–12 PM)', 'wildflower' ),
		'afternoon' => __( 'Afternoon (12–4 PM)', 'wildflower' ),
		'evening'   => __( 'Evening (4–8 PM)', 'wildflower' ),
	);

	return isset( $labels[ $window ] ) ? $labels[ $window ] : '';
}

/**
 * Show delivery details to staff in the order admin.
 *
 * @param WC_Order $order Order being displayed.
 */
function wildflower_checkout_admin_delivery_details( $order ) {
	$date   = $order->get_meta( '_wildflower_delivery_date' );
	$window = wildflower_checkout_window_label( $order->get_meta( '_wildflower_delivery_window' ) );

	if ( $date ) {
		echo '<p><strong>' . esc_html__( 'Delivery date:', 'wildflower' ) . '</strong> ' . esc_html( $date ) . '</p>';
	}
	if ( $window ) {
		echo '<p><strong>' . esc_html__( 'Preferred window:', 'wildflower' ) . '</strong> ' . esc_html( $window ) . '</p>';
	}
}
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'wildflower_checkout_admin_delivery_details' );

/**
 * Add delivery details to customer and studio emails.
 *
 * @param array    $fields Email order meta fields.
 * @param bool     $sent_to_admin Whether this is an admin email.
 * @param WC_Order $order Order being sent.
 * @return array
 */
function wildflower_checkout_email_delivery_details( $fields, $sent_to_admin, $order ) {
	$date   = $order->get_meta( '_wildflower_delivery_date' );
	$window = wildflower_checkout_window_label( $order->get_meta( '_wildflower_delivery_window' ) );

	if ( $date ) {
		$fields['wildflower_delivery_date'] = array(
			'label' => __( 'Delivery date', 'wildflower' ),
			'value' => $date,
		);
	}
	if ( $window ) {
		$fields['wildflower_delivery_window'] = array(
			'label' => __( 'Preferred delivery window', 'wildflower' ),
			'value' => $window,
		);
	}

	return $fields;
}
add_filter( 'woocommerce_email_order_meta_fields', 'wildflower_checkout_email_delivery_details', 20, 3 );

/**
 * Trim textarea values stored through express checkout.
 *
 * @param mixed $value Posted value.
 * @return string
 */
function wildflower_checkout_clean_message( $value ) {
	if ( ! is_scalar( $value ) ) {
		return '';
	}

	$value = sanitize_textarea_field( wp_unslash( (string) $value ) );
	if ( function_exists( 'mb_substr' ) ) {
		return mb_substr( $value, 0, 1000 );
	}

	return substr( $value, 0, 1000 );
}

/**
 * Validate the completed classic form before exposing express checkout.
 *
 * @param array     $posted Posted checkout values.
 * @param bool|null $pickup_override Explicit pickup state during Store API payment.
 * @return array
 */
function wildflower_checkout_validate_express_data( $posted, $pickup_override = null ) {
	$checkout                  = WC()->checkout();
	$ship_to_different_address = ! empty( $posted['ship_to_different_address'] );
	$groups                    = array( 'billing' );
	$fields                    = array();
	$errors                    = array();

	if ( $ship_to_different_address ) {
		$groups[] = 'shipping';
	}

	foreach ( $groups as $group ) {
		foreach ( $checkout->get_checkout_fields( $group ) as $key => $field ) {
			$value          = isset( $posted[ $key ] ) ? wc_clean( wp_unslash( $posted[ $key ] ) ) : '';
			$value          = is_scalar( $value ) ? (string) $value : '';
			$fields[ $key ] = $value;

			if ( ! empty( $field['required'] ) && '' === trim( (string) $value ) ) {
				$label          = isset( $field['label'] ) ? wp_strip_all_tags( $field['label'] ) : $key;
				$errors[ $key ] = sprintf( __( '%s is a required field.', 'wildflower' ), $label );
			}
		}
	}

	if ( ! empty( $fields['billing_email'] ) && ! is_email( $fields['billing_email'] ) ) {
		$errors['billing_email'] = __( 'Please enter a valid email address.', 'wildflower' );
	}
	if ( ! empty( $fields['billing_phone'] ) && function_exists( 'wc_is_phone_number' ) && ! wc_is_phone_number( $fields['billing_phone'] ) ) {
		$errors['billing_phone'] = __( 'Please enter a valid phone number.', 'wildflower' );
	}

	$countries = WC()->countries->get_countries();
	foreach ( $groups as $group ) {
		$country_key  = $group . '_country';
		$state_key    = $group . '_state';
		$postcode_key = $group . '_postcode';
		$country      = isset( $fields[ $country_key ] ) ? $fields[ $country_key ] : '';

		if ( $country && ! isset( $countries[ $country ] ) ) {
			$errors[ $country_key ] = __( 'Please select a valid country.', 'wildflower' );
			continue;
		}
		if ( $country && ! empty( $fields[ $postcode_key ] ) && ! WC_Validation::is_postcode( $fields[ $postcode_key ], $country ) ) {
			$errors[ $postcode_key ] = __( 'Please enter a valid ZIP code.', 'wildflower' );
		}

		$states = $country ? WC()->countries->get_states( $country ) : array();
		if ( is_array( $states ) && $states && ! empty( $fields[ $state_key ] ) && ! isset( $states[ $fields[ $state_key ] ] ) ) {
			$errors[ $state_key ] = __( 'Please select a valid state.', 'wildflower' );
		}
	}

	$is_pickup = is_bool( $pickup_override ) ? $pickup_override : wildflower_checkout_is_pickup();
	$date      = isset( $posted['wildflower_delivery_date'] ) ? wildflower_checkout_clean_date( $posted['wildflower_delivery_date'] ) : '';
	$window    = isset( $posted['wildflower_delivery_window'] ) ? wildflower_checkout_clean_window( $posted['wildflower_delivery_window'] ) : '';

	if ( ! $is_pickup && ! $date ) {
		$errors['wildflower_delivery_date'] = __( 'Please select a valid delivery date.', 'wildflower' );
	} elseif ( ! $is_pickup && $date < wildflower_checkout_earliest_date() ) {
		$errors['wildflower_delivery_date'] = __( 'Please select an available delivery date.', 'wildflower' );
	}
	if ( ! $is_pickup && ! $window ) {
		$errors['wildflower_delivery_window'] = __( 'Please select a preferred delivery window.', 'wildflower' );
	} elseif ( ! $is_pickup && $date && ! in_array( $window, wildflower_checkout_allowed_windows( $date ), true ) ) {
		$errors['wildflower_delivery_window'] = __( 'Please select an available delivery window.', 'wildflower' );
	}

	$terms_accepted = ! empty( $posted['terms'] );
	if ( function_exists( 'wc_terms_and_conditions_checkbox_enabled' ) && wc_terms_and_conditions_checkbox_enabled() && ! $terms_accepted ) {
		$errors['terms'] = __( 'Please read and accept the terms and conditions to proceed with your order.', 'woocommerce' );
	}

	return array(
		'errors'                    => $errors,
		'fields'                    => $fields,
		'ship_to_different_address' => $ship_to_different_address,
		'is_pickup'                 => $is_pickup,
		'delivery_date'             => $date,
		'delivery_window'           => $window,
		'order_comments'            => wildflower_checkout_clean_message( isset( $posted['order_comments'] ) ? $posted['order_comments'] : '' ),
		'card_message'              => wildflower_checkout_clean_message( isset( $posted['wildflower_card_message'] ) ? $posted['wildflower_card_message'] : '' ),
		'terms_accepted'            => $terms_accepted,
	);
}

/**
 * Store a short-lived, server-validated snapshot for WooPay/express checkout.
 */
function wildflower_checkout_sync_express_data() {
	check_ajax_referer( 'wildflower_sync_express_checkout', 'security' );

	if ( ! WC()->session || ! WC()->cart ) {
		wp_send_json_error( array( 'message' => __( 'Checkout session is unavailable. Please refresh the page.', 'wildflower' ) ), 400 );
	}

	$form_data = isset( $_POST['form_data'] ) && is_string( $_POST['form_data'] ) ? wp_unslash( $_POST['form_data'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$posted    = array();
	parse_str( $form_data, $posted );
	$posted['ship_to_different_address'] = ! empty( $_POST['shipping_address_active'] ) && 'yes' === wp_unslash( $_POST['shipping_address_active'] ) ? '1' : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

	$validation = wildflower_checkout_validate_express_data( $posted );
	if ( $validation['errors'] ) {
		WC()->session->__unset( 'wildflower_express_checkout_data' );
		wp_send_json_error(
			array(
				'message' => implode( ' ', $validation['errors'] ),
				'fields'  => array_keys( $validation['errors'] ),
			),
			400
		);
	}

	$validation['cart_hash']    = WC()->cart->get_cart_hash();
	$validation['validated_at'] = time();
	unset( $validation['errors'] );
	WC()->session->set( 'wildflower_express_checkout_data', $validation );

	wp_send_json_success( array( 'ready' => true ) );
}
add_action( 'wc_ajax_wildflower_sync_express_checkout', 'wildflower_checkout_sync_express_data' );

/**
 * Revalidate and hydrate an express-checkout order before Store API payment.
 *
 * @param WC_Order $order Order created by Store API.
 * @param WP_Error $validation_errors Store API validation errors.
 */
function wildflower_checkout_validate_order_before_payment( $order, $validation_errors ) {
	if ( ! $order instanceof WC_Order || ! $validation_errors instanceof WP_Error ) {
		return;
	}

	$snapshot = WC()->session ? WC()->session->get( 'wildflower_express_checkout_data' ) : null;
	if ( ! is_array( $snapshot ) || empty( $snapshot['fields'] ) || empty( $snapshot['validated_at'] ) ) {
		$validation_errors->add( 'wildflower_express_not_validated', __( 'Complete all required checkout fields before using express checkout.', 'wildflower' ) );
		return;
	}
	if ( time() - absint( $snapshot['validated_at'] ) > 30 * MINUTE_IN_SECONDS ) {
		$validation_errors->add( 'wildflower_express_expired', __( 'Your checkout details have expired. Please review the required fields and try again.', 'wildflower' ) );
		return;
	}

	$order_cart_hash = (string) $order->get_cart_hash();
	if ( $order_cart_hash && ! hash_equals( $order_cart_hash, (string) $snapshot['cart_hash'] ) ) {
		$validation_errors->add( 'wildflower_express_cart_changed', __( 'Your cart changed. Please review the required checkout fields and try again.', 'wildflower' ) );
		return;
	}

	$posted                               = (array) $snapshot['fields'];
	$posted['ship_to_different_address'] = ! empty( $snapshot['ship_to_different_address'] ) ? '1' : '';
	$posted['wildflower_delivery_date']   = (string) $snapshot['delivery_date'];
	$posted['wildflower_delivery_window'] = (string) $snapshot['delivery_window'];
	$posted['terms']                     = ! empty( $snapshot['terms_accepted'] ) ? 'on' : '';
	$is_pickup                           = false;

	foreach ( $order->get_shipping_methods() as $shipping_item ) {
		if ( false !== strpos( (string) $shipping_item->get_method_id(), 'local_pickup' ) ) {
			$is_pickup = true;
			break;
		}
	}

	$validation = wildflower_checkout_validate_express_data( $posted, $is_pickup );
	foreach ( $validation['errors'] as $code => $message ) {
		$validation_errors->add( 'wildflower_' . sanitize_key( $code ), $message );
	}
	if ( $validation_errors->has_errors() ) {
		return;
	}

	foreach ( $snapshot['fields'] as $key => $value ) {
		$setter = 'set_' . $key;
		if ( is_callable( array( $order, $setter ) ) ) {
			$order->{$setter}( $value );
		}
	}

	if ( empty( $snapshot['ship_to_different_address'] ) ) {
		foreach ( array( 'first_name', 'last_name', 'company', 'country', 'address_1', 'address_2', 'city', 'state', 'postcode' ) as $field ) {
			$getter = 'get_billing_' . $field;
			$setter = 'set_shipping_' . $field;
			if ( is_callable( array( $order, $getter ) ) && is_callable( array( $order, $setter ) ) ) {
				$order->{$setter}( $order->{$getter}() );
			}
		}
	}

	if ( is_callable( array( $order, 'set_shipping_phone' ) ) && ! $order->get_shipping_phone() ) {
		$order->set_shipping_phone( $order->get_billing_phone() );
	}
	if ( ! $is_pickup ) {
		$order->update_meta_data( '_wildflower_delivery_date', $snapshot['delivery_date'] );
		$order->update_meta_data( '_wildflower_delivery_window', $snapshot['delivery_window'] );
	}
	if ( ! empty( $snapshot['order_comments'] ) ) {
		$order->set_customer_note( $snapshot['order_comments'] );
	}
	if ( ! empty( $snapshot['card_message'] ) ) {
		$order->update_meta_data( '_wildflower_card_message', $snapshot['card_message'] );
	}

	$order->save();
}
add_action( 'woocommerce_checkout_validate_order_before_payment', 'wildflower_checkout_validate_order_before_payment', 10, 2 );
