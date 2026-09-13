<?php
/**
 * Telegram order notices for Wildflower — the same message Boston Flowers sends.
 *
 * Both storefronts belong to one company and land in one Telegram group, split
 * only by topic. Until now the two sides wrote in different languages: Boston
 * Flowers posted a full HTML card with the listing photo, alerts, the requested
 * delivery window, the card message, both phone numbers, per-item SKU and price
 * and buttons to open the order, while Wildflower posted nine plain lines. A
 * manager reading the group had to switch habits depending on which brand the
 * order came from, and the photo — the fastest way to recognise a bouquet —
 * was missing on one side entirely.
 *
 * This file is the Boston Flowers notifier ported to Wildflower: same sections,
 * same order of fields, same buttons. Only three things differ, because the two
 * sites store them differently:
 *   - delivery date and window come from _wildflower_delivery_* meta;
 *   - the card message comes from _wildflower_card_message;
 *   - settings and destinations come from the Wildflower Operations page
 *     rather than the Boston Flowers secrets file.
 *
 * One message per order per destination, edited in place as the order is paid,
 * cancelled or refunded — so the group holds one live card per order instead of
 * a pile of updates.
 *
 * @package Wildflower
 */

defined( 'ABSPATH' ) || exit;

const WILDFLOWER_TG_LEGACY_NOTIFIED_META = '_wildflower_telegram_notified';
const WILDFLOWER_TG_MESSAGE_IDS_META     = '_wildflower_tg_message_ids';
const WILDFLOWER_TG_LAST_EVENT_META      = '_wildflower_tg_last_event';

/**
 * Escape a value for Telegram's HTML parse mode.
 *
 * @param mixed $value Raw value.
 * @return string
 */
function wildflower_tg_html( $value ) {
	return htmlspecialchars( wp_strip_all_tags( (string) $value ), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
}

/**
 * Collapse whitespace and cut a value to a readable length.
 *
 * @param mixed $value Raw value.
 * @param int   $limit Maximum characters.
 * @return string
 */
function wildflower_tg_plain( $value, $limit = 220 ) {
	$value = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( (string) $value ) ) );

	if ( function_exists( 'mb_strlen' ) && mb_strlen( $value ) > $limit ) {
		return mb_substr( $value, 0, max( 0, $limit - 3 ) ) . '...';
	}

	if ( strlen( $value ) > $limit ) {
		return substr( $value, 0, max( 0, $limit - 3 ) ) . '...';
	}

	return $value;
}

/**
 * Format money in the order's own currency.
 *
 * @param WC_Order $order  Order.
 * @param mixed    $amount Amount.
 * @return string
 */
function wildflower_tg_money( WC_Order $order, $amount ) {
	return html_entity_decode( wp_strip_all_tags( wc_price( (float) $amount, array( 'currency' => $order->get_currency() ) ) ), ENT_QUOTES, 'UTF-8' );
}

/**
 * Tip on the order, if the storefront ever collects one.
 *
 * Wildflower has no tip step today; the line is kept so the two brands' cards
 * stay field-for-field identical, and a fee named TIP is already understood.
 *
 * @param WC_Order $order Order.
 * @return float
 */
function wildflower_tg_tip_amount( WC_Order $order ) {
	$amount = (float) $order->get_meta( '_wildflower_tip_amount' );
	if ( $amount > 0 ) {
		return (float) wc_format_decimal( $amount, 2 );
	}

	foreach ( $order->get_fees() as $fee ) {
		if ( 'TIP' === strtoupper( $fee->get_name() ) ) {
			return (float) wc_format_decimal( $fee->get_total(), 2 );
		}
	}

	return 0.0;
}

/**
 * Admin link for the order.
 *
 * @param WC_Order $order Order.
 * @return string
 */
function wildflower_tg_admin_url( WC_Order $order ) {
	if ( method_exists( $order, 'get_edit_order_url' ) ) {
		return $order->get_edit_order_url();
	}

	return admin_url( 'post.php?post=' . absint( $order->get_id() ) . '&action=edit' );
}

/**
 * One-line address.
 *
 * @param WC_Order $order Order.
 * @param string   $type  billing|shipping.
 * @return string
 */
function wildflower_tg_address_lines( WC_Order $order, $type ) {
	$address = 'shipping' === $type ? $order->get_address( 'shipping' ) : $order->get_address( 'billing' );
	$parts   = array_filter(
		array(
			trim( ( $address['first_name'] ?? '' ) . ' ' . ( $address['last_name'] ?? '' ) ),
			$address['company'] ?? '',
			$address['address_1'] ?? '',
			$address['address_2'] ?? '',
			trim( ( $address['city'] ?? '' ) . ', ' . ( $address['state'] ?? '' ) . ' ' . ( $address['postcode'] ?? '' ) ),
			$address['country'] ?? '',
		)
	);

	return $parts ? implode( ', ', $parts ) : '';
}

/**
 * Who opens the door, when the buyer gave a second number.
 *
 * @param WC_Order $order Order.
 * @return string
 */
function wildflower_tg_recipient_phone( WC_Order $order ) {
	$phone = method_exists( $order, 'get_shipping_phone' ) ? trim( (string) $order->get_shipping_phone() ) : '';

	return '' !== $phone ? $phone : 'None';
}

/**
 * First line item with its listing photo — the picture at the top of the card.
 *
 * A variation carries its own image only sometimes; when it does not, the
 * parent product's image is the listing photo the customer actually saw.
 *
 * @param WC_Order $order Order.
 * @return array<string, string>
 */
function wildflower_tg_first_product( WC_Order $order ) {
	foreach ( $order->get_items( 'line_item' ) as $item ) {
		$product = $item->get_product();
		if ( ! $product ) {
			continue;
		}

		$product_id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
		$image_id   = $product->get_image_id();

		if ( ! $image_id && $product->is_type( 'variation' ) ) {
			$parent   = wc_get_product( $product->get_parent_id() );
			$image_id = $parent ? $parent->get_image_id() : 0;
		}

		return array(
			'name'      => $item->get_name(),
			'url'       => $product_id ? get_permalink( $product_id ) : '',
			'image_url' => $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : '',
		);
	}

	return array(
		'name'      => '',
		'url'       => '',
		'image_url' => '',
	);
}

/**
 * Variation and item meta as one line.
 *
 * @param WC_Order_Item_Product $item Line item.
 * @return string
 */
function wildflower_tg_variation_text( WC_Order_Item_Product $item ) {
	$parts = array();

	foreach ( $item->get_formatted_meta_data( '' ) as $meta ) {
		$key = wp_strip_all_tags( $meta->display_key );
		$val = wp_strip_all_tags( $meta->display_value );
		if ( '' !== $key && '' !== $val ) {
			$parts[] = $key . ': ' . $val;
		}
	}

	return implode( '; ', $parts );
}

/**
 * Headline per event.
 *
 * @param string $event Event slug.
 * @return string
 */
function wildflower_tg_event_title( $event ) {
	$titles = array(
		'created'   => 'New Wildflower Order / Новый заказ Wildflower',
		'paid'      => 'Payment Received / Оплата прошла',
		'cancelled' => 'Order Cancelled / Заказ отменен',
		'refunded'  => 'Refund Created / Возврат создан',
	);

	return isset( $titles[ $event ] ) ? $titles[ $event ] : 'Wildflower Order Update';
}

/**
 * Meta key that marks an event as already announced.
 *
 * @param string $event     Event slug.
 * @param int    $refund_id Refund ID, for refunds.
 * @return string
 */
function wildflower_tg_event_flag( $event, $refund_id = 0 ) {
	$suffix = 'refunded' === $event && $refund_id ? 'refunded_' . absint( $refund_id ) : sanitize_key( $event );

	return '_wildflower_tg_event_' . $suffix;
}

/**
 * Does this order read as a same-day request?
 *
 * @param WC_Order $order Order.
 * @return bool
 */
function wildflower_tg_detect_same_day( WC_Order $order ) {
	$requested = trim( (string) $order->get_meta( '_wildflower_delivery_date' ) );
	if ( '' !== $requested && $requested === current_time( 'Y-m-d' ) ) {
		return true;
	}

	$needle = strtolower(
		implode(
			' ',
			array(
				$order->get_customer_note(),
				$order->get_shipping_method(),
				$order->get_billing_address_1(),
				$order->get_shipping_address_1(),
			)
		)
	);

	return (bool) preg_match( '/same[- ]?day|today|asap|urgent|сегодня|срочно/', $needle );
}

/**
 * Is this delivery outside the usual zone?
 *
 * @param WC_Order $order Order.
 * @return bool
 */
function wildflower_tg_detect_extended_delivery( WC_Order $order ) {
	$text = strtolower(
		implode(
			' ',
			array(
				$order->get_shipping_state(),
				$order->get_billing_state(),
				$order->get_shipping_city(),
				$order->get_billing_city(),
				wildflower_tg_address_lines( $order, 'shipping' ),
				$order->get_shipping_method(),
			)
		)
	);

	if ( preg_match( '/\bri\b|rhode island|long distance|extended|out of zone|outside boston/', $text ) ) {
		return true;
	}

	return (float) $order->get_shipping_total() >= 100;
}

/**
 * The lines that go above everything else, because they change what you do next.
 *
 * @param WC_Order $order Order.
 * @return string[]
 */
function wildflower_tg_alerts( WC_Order $order ) {
	$alerts = array();
	$total  = (float) $order->get_total();

	if ( $total >= 1000 ) {
		$alerts[] = 'HIGH VALUE ORDER: $1000+';
	} elseif ( $total >= 500 ) {
		$alerts[] = 'HIGH VALUE ORDER: $500+';
	} elseif ( $total >= 300 ) {
		$alerts[] = 'HIGH VALUE ORDER: $300+';
	}

	if ( wildflower_tg_detect_extended_delivery( $order ) ) {
		$alerts[] = 'EXTENDED DELIVERY - confirm manually';
	}

	if ( wildflower_tg_detect_same_day( $order ) ) {
		$alerts[] = 'SAME-DAY REQUEST - confirm availability';
	}

	if ( $order->get_customer_note() ) {
		$alerts[] = 'CUSTOMER NOTE: ' . wildflower_tg_plain( $order->get_customer_note(), 260 );
	}

	return $alerts;
}

/**
 * Build the order card.
 *
 * @param WC_Order $order     Order.
 * @param string   $event     created|paid|cancelled|refunded.
 * @param int      $refund_id Refund ID, for refunds.
 * @return string
 */
function wildflower_tg_message( WC_Order $order, $event = 'created', $refund_id = 0 ) {
	$billing_address  = wildflower_tg_address_lines( $order, 'billing' );
	$shipping_address = wildflower_tg_address_lines( $order, 'shipping' );
	$first_product    = wildflower_tg_first_product( $order );
	$shipping_methods = array();

	foreach ( $order->get_items( 'shipping' ) as $shipping_item ) {
		$shipping_methods[] = $shipping_item->get_name();
	}

	$date    = $order->get_date_created() ? $order->get_date_created()->date_i18n( 'Y-m-d H:i:s T' ) : current_time( 'mysql' );
	$coupons = $order->get_coupon_codes();
	$alerts  = wildflower_tg_alerts( $order );
	$tip     = wildflower_tg_tip_amount( $order );

	$lines = array(
		'<b>' . wildflower_tg_html( wildflower_tg_event_title( $event ) ) . '</b>',
	);

	if ( $alerts ) {
		$lines[] = '';
		$lines[] = '<b>Alerts</b>';
		foreach ( $alerts as $alert ) {
			$lines[] = '- <b>' . wildflower_tg_html( $alert ) . '</b>';
		}
	}

	// The bare URL is deliberate: Telegram renders it as the preview at the top
	// of the message, which is how the bouquet gets recognised at a glance.
	if ( ! empty( $first_product['image_url'] ) ) {
		$lines[] = '';
		$lines[] = '<b>Product image</b>';
		$lines[] = esc_url_raw( $first_product['image_url'] );
	}

	$lines = array_merge(
		$lines,
		array(
			'',
			'<b>Order</b>',
			'ID: #' . wildflower_tg_html( $order->get_order_number() ),
			'Store: Wildflower',
			'Event: ' . wildflower_tg_html( strtoupper( $event ) ),
			'Status: ' . wildflower_tg_html( wc_get_order_status_name( $order->get_status() ) ),
			'Payment: ' . wildflower_tg_html( $order->get_payment_method_title() ? $order->get_payment_method_title() : $order->get_payment_method() ),
			'Date/time: ' . wildflower_tg_html( $date ),
			'Total: <b>' . wildflower_tg_html( wildflower_tg_money( $order, $order->get_total() ) ) . '</b>',
			'Subtotal: ' . wildflower_tg_html( wildflower_tg_money( $order, $order->get_subtotal() ) ),
			'Shipping: ' . wildflower_tg_html( wildflower_tg_money( $order, $order->get_shipping_total() ) ),
			'Tip: ' . wildflower_tg_html( $tip > 0 ? wildflower_tg_money( $order, $tip ) : 'None' ),
			'Tax: ' . wildflower_tg_html( wildflower_tg_money( $order, $order->get_total_tax() ) ),
			'Discount: ' . wildflower_tg_html( wildflower_tg_money( $order, $order->get_discount_total() ) ),
			'Coupons: ' . wildflower_tg_html( $coupons ? implode( ', ', $coupons ) : 'None' ),
			'Currency: ' . wildflower_tg_html( $order->get_currency() ),
		)
	);

	if ( 'refunded' === $event && $refund_id ) {
		$refund = wc_get_order( $refund_id );
		if ( $refund instanceof WC_Order_Refund ) {
			$lines[] = 'Refund: #' . wildflower_tg_html( $refund_id ) . ' | ' . wildflower_tg_html( wildflower_tg_money( $order, abs( $refund->get_amount() ) ) );
			if ( $refund->get_reason() ) {
				$lines[] = 'Refund reason: ' . wildflower_tg_html( wildflower_tg_plain( $refund->get_reason(), 180 ) );
			}
		}
	}

	$requested_date   = trim( (string) $order->get_meta( '_wildflower_delivery_date' ) );
	$requested_window = (string) $order->get_meta( '_wildflower_delivery_window' );
	$window_label     = function_exists( 'wildflower_checkout_window_label' ) ? wildflower_checkout_window_label( $requested_window ) : '';
	if ( '' === $window_label ) {
		$window_label = 'Any time (8 AM - 8 PM)';
	}

	if ( '' !== $requested_date ) {
		$lines[] = '';
		$lines[] = '<b>📅 REQUESTED DELIVERY</b>';
		$lines[] = 'Date: <b>' . wildflower_tg_html( $requested_date ) . '</b>';
		$lines[] = 'Time: ' . wildflower_tg_html( $window_label );
	}

	$card_message = trim( (string) $order->get_meta( '_wildflower_card_message' ) );
	if ( '' !== $card_message ) {
		$lines[] = '';
		$lines[] = '<b>💌 CARD MESSAGE</b>';
		$lines[] = wildflower_tg_html( wildflower_tg_plain( $card_message, 600 ) );
	}

	$lines = array_merge(
		$lines,
		array(
			'',
			'<b>Customer</b>',
			'Name: ' . wildflower_tg_html( trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) ),
			'Phone: ' . wildflower_tg_html( $order->get_billing_phone() ? $order->get_billing_phone() : 'None' ),
			'Email: ' . wildflower_tg_html( $order->get_billing_email() ? $order->get_billing_email() : 'None' ),
			'Billing: ' . wildflower_tg_html( wildflower_tg_plain( $billing_address ? $billing_address : 'Not provided', 220 ) ),
			'Shipping: ' . wildflower_tg_html( wildflower_tg_plain( $shipping_address ? $shipping_address : ( $billing_address ? $billing_address : 'Not provided' ), 220 ) ),
			'',
			'<b>Delivery</b>',
			'Shipping method: ' . wildflower_tg_html( $shipping_methods ? implode( ', ', $shipping_methods ) : 'Not selected' ),
			'Delivery address: ' . wildflower_tg_html( wildflower_tg_plain( $shipping_address ? $shipping_address : ( $billing_address ? $billing_address : 'Not provided' ), 220 ) ),
			// Both numbers, named: who pays and who opens the door are often two
			// different people. 'None' is written out on purpose — a missing line
			// reads as 'forgot', 'None' reads as 'asked, not given'.
			'Sender phone: ' . wildflower_tg_html( $order->get_billing_phone() ? $order->get_billing_phone() : 'None' ),
			'Recipient phone: ' . wildflower_tg_html( wildflower_tg_recipient_phone( $order ) ),
			'',
			'<b>Products</b>',
		)
	);

	$item_count = 0;
	foreach ( $order->get_items( 'line_item' ) as $item ) {
		++$item_count;
		if ( $item_count > 6 ) {
			$lines[] = '- More items in admin order';
			break;
		}

		$product    = $item->get_product();
		$name       = $item->get_name();
		$sku        = $product ? $product->get_sku() : '';
		$product_id = $product ? ( $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id() ) : 0;
		$url        = $product_id ? get_permalink( $product_id ) : '';
		$variation  = $item instanceof WC_Order_Item_Product ? wildflower_tg_variation_text( $item ) : '';
		$line_total = (float) $item->get_total() + (float) $item->get_total_tax();

		$lines[] = '- <b>' . wildflower_tg_html( wildflower_tg_plain( $name, 120 ) ) . '</b>';
		$lines[] = '  Qty: ' . wildflower_tg_html( $item->get_quantity() ) . ' | Price: ' . wildflower_tg_html( wildflower_tg_money( $order, $line_total ) );
		if ( $sku ) {
			$lines[] = '  SKU: ' . wildflower_tg_html( $sku );
		}
		if ( $variation ) {
			$lines[] = '  Variation: ' . wildflower_tg_html( wildflower_tg_plain( $variation, 160 ) );
		}
		if ( $url ) {
			$lines[] = '  Product: ' . esc_url_raw( $url );
		}
	}

	$lines[] = '';
	$lines[] = '<b>Admin</b>';
	$lines[] = 'Order: ' . esc_url_raw( wildflower_tg_admin_url( $order ) );

	$message = implode( "\n", $lines );

	if ( strlen( $message ) > 3900 ) {
		$message = substr( $message, 0, 3850 ) . "\n\n[Message shortened. Open order for full details.]";
	}

	return $message;
}

/**
 * Buttons under the card.
 *
 * @param WC_Order $order Order.
 * @return array<string, mixed>
 */
function wildflower_tg_keyboard( WC_Order $order ) {
	$first_product = wildflower_tg_first_product( $order );
	$rows          = array(
		array(
			array(
				'text' => 'Open Order',
				'url'  => wildflower_tg_admin_url( $order ),
			),
		),
	);

	if ( ! empty( $first_product['url'] ) ) {
		$rows[0][] = array(
			'text' => 'Open Product',
			'url'  => $first_product['url'],
		);
	}

	$contact_row = array();
	$phone       = preg_replace( '/\D+/', '', (string) $order->get_billing_phone() );
	if ( $phone ) {
		if ( 10 === strlen( $phone ) ) {
			$phone = '1' . $phone;
		}
		$contact_row[] = array(
			'text' => 'Call Customer',
			'url'  => 'https://wa.me/' . $phone,
		);
	}

	if ( $order->get_billing_email() ) {
		$contact_row[] = array(
			'text' => 'Email Customer',
			'url'  => 'https://mail.google.com/mail/?view=cm&fs=1&to=' . rawurlencode( $order->get_billing_email() ),
		);
	}

	if ( $contact_row ) {
		$rows[] = $contact_row;
	}

	return array( 'inline_keyboard' => $rows );
}

/**
 * One call to the Telegram API.
 *
 * @param string               $token  Bot token.
 * @param string               $method API method.
 * @param array<string, mixed> $body   Request body.
 * @return array<string, mixed>|WP_Error
 */
function wildflower_tg_api_post( $token, $method, array $body ) {
	$response = wp_remote_post(
		'https://api.telegram.org/bot' . $token . '/' . $method,
		array(
			'timeout' => 15,
			'body'    => $body,
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$decoded = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $decoded['ok'] ) ) {
		return new WP_Error(
			'wildflower_tg_api',
			! empty( $decoded['description'] ) ? sanitize_text_field( $decoded['description'] ) : 'Telegram API returned an error.'
		);
	}

	return $decoded;
}

/**
 * Send the card, or edit the one already standing for this order.
 *
 * @param string               $token      Bot token.
 * @param string               $chat_id    Destination chat.
 * @param string               $message    Message HTML.
 * @param array<string, mixed> $keyboard   Inline keyboard.
 * @param int                  $message_id Existing message to edit, 0 to send.
 * @param string               $thread_id  Forum topic, empty for a plain chat.
 * @return int Message ID that now stands, or 0.
 */
function wildflower_tg_send_or_edit( $token, $chat_id, $message, array $keyboard, $message_id = 0, $thread_id = '' ) {
	$base_body = array(
		'chat_id'                  => (string) $chat_id,
		'parse_mode'               => 'HTML',
		// False on purpose: the preview IS the listing photo.
		'disable_web_page_preview' => false,
		'reply_markup'             => wp_json_encode( $keyboard ),
	);

	if ( $message_id ) {
		$result = wildflower_tg_api_post(
			$token,
			'editMessageText',
			array_merge(
				$base_body,
				array(
					'message_id' => absint( $message_id ),
					'text'       => $message,
				)
			)
		);

		if ( ! is_wp_error( $result ) ) {
			return absint( $message_id );
		}

		wildflower_tg_log( 'Telegram edit failed for chat ' . $chat_id . ': ' . $result->get_error_message() );
		return 0;
	}

	// editMessageText has no thread parameter: a message already lives in its topic.
	if ( '' !== (string) $thread_id ) {
		$base_body['message_thread_id'] = (string) $thread_id;
	}

	$result = wildflower_tg_api_post( $token, 'sendMessage', array_merge( $base_body, array( 'text' => $message ) ) );

	if ( is_wp_error( $result ) ) {
		wildflower_tg_log( 'Telegram send with keyboard failed for chat ' . $chat_id . ': ' . $result->get_error_message() );
		$fallback_body = $base_body;
		unset( $fallback_body['reply_markup'] );
		$result = wildflower_tg_api_post( $token, 'sendMessage', array_merge( $fallback_body, array( 'text' => $message ) ) );
	}

	if ( is_wp_error( $result ) ) {
		wildflower_tg_log( 'Telegram send failed for chat ' . $chat_id . ': ' . $result->get_error_message() );
		return 0;
	}

	return ! empty( $result['result']['message_id'] ) ? absint( $result['result']['message_id'] ) : 0;
}

/**
 * Log to the WooCommerce log when it exists, otherwise to PHP's.
 *
 * @param string $message Message.
 * @param string $level   Log level.
 */
function wildflower_tg_log( $message, $level = 'error' ) {
	if ( function_exists( 'wc_get_logger' ) ) {
		wc_get_logger()->log( $level, $message, array( 'source' => 'wildflower-telegram-orders' ) );
		return;
	}

	error_log( '[wildflower-telegram-orders] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}

/**
 * Announce an order event, or update the card already posted for it.
 *
 * @param int    $order_id  Order ID.
 * @param string $event     created|paid|cancelled|refunded.
 * @param int    $refund_id Refund ID, for refunds.
 */
function wildflower_tg_notify_event( $order_id, $event = 'created', $refund_id = 0 ) {
	if ( ! function_exists( 'wc_get_order' ) || ! function_exists( 'wildflower_telegram_targets' ) ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order instanceof WC_Order ) {
		return;
	}

	$event     = sanitize_key( $event );
	$flag_meta = wildflower_tg_event_flag( $event, $refund_id );

	if ( 'yes' === $order->get_meta( $flag_meta, true ) ) {
		return;
	}

	// Orders announced by the previous notifier keep their old card: there is no
	// message ID to edit, and re-sending would post the same order twice.
	if ( 'created' === $event
		&& 'yes' === $order->get_meta( WILDFLOWER_TG_LEGACY_NOTIFIED_META, true )
		&& ! $order->get_meta( WILDFLOWER_TG_MESSAGE_IDS_META, true ) ) {
		return;
	}

	$settings = wildflower_operations_settings();
	$token    = isset( $settings['telegram_bot_token'] ) ? (string) $settings['telegram_bot_token'] : '';
	$targets  = wildflower_telegram_targets( 'orders' );

	if ( '' === $token || empty( $targets ) ) {
		wildflower_tg_log( 'Telegram token or destination is not configured for order #' . $order->get_id() );
		return;
	}

	$message     = wildflower_tg_message( $order, $event, $refund_id );
	$keyboard    = wildflower_tg_keyboard( $order );
	$message_ids = $order->get_meta( WILDFLOWER_TG_MESSAGE_IDS_META, true );
	if ( ! is_array( $message_ids ) ) {
		$message_ids = array();
	}

	$changed = false;
	foreach ( $targets as $target ) {
		$chat_id   = (string) $target['chat_id'];
		$thread_id = (string) $target['thread_id'];
		// Keyed per destination so a later status edit finds its own message; the
		// plain chat ID stays the key without a thread, keeping older orders editable.
		$chat_key   = '' !== $thread_id ? $chat_id . ':' . $thread_id : $chat_id;
		$message_id = ! empty( $message_ids[ $chat_key ] ) ? absint( $message_ids[ $chat_key ] ) : 0;
		$new_msg_id = wildflower_tg_send_or_edit( $token, $chat_id, $message, $keyboard, $message_id, $thread_id );

		if ( $new_msg_id ) {
			$message_ids[ $chat_key ] = $new_msg_id;
			$changed                  = true;
		}
	}

	if ( $changed ) {
		$order->update_meta_data( $flag_meta, 'yes' );
		$order->update_meta_data( WILDFLOWER_TG_LAST_EVENT_META, $event );
		$order->update_meta_data( WILDFLOWER_TG_MESSAGE_IDS_META, $message_ids );
		if ( 'created' === $event ) {
			$order->update_meta_data( WILDFLOWER_TG_LEGACY_NOTIFIED_META, 'yes' );
		}
		$order->save();
	}
}

/**
 * Classic checkout hands over the ID.
 *
 * @param int $order_id Order ID.
 */
function wildflower_tg_notify_created( $order_id ) {
	wildflower_tg_notify_event( $order_id, 'created' );
}

/**
 * Block checkout hands over the order object.
 *
 * @param WC_Order $order Order.
 */
function wildflower_tg_notify_created_object( $order ) {
	if ( is_object( $order ) && method_exists( $order, 'get_id' ) ) {
		wildflower_tg_notify_event( $order->get_id(), 'created' );
	}
}

add_action( 'woocommerce_checkout_order_processed', 'wildflower_tg_notify_created', 20 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'wildflower_tg_notify_created_object', 20 );

add_action(
	'woocommerce_payment_complete',
	function ( $order_id ) {
		wildflower_tg_notify_event( $order_id, 'paid' );
	},
	20
);
add_action(
	'woocommerce_order_status_processing',
	function ( $order_id ) {
		wildflower_tg_notify_event( $order_id, 'paid' );
	},
	20
);
add_action(
	'woocommerce_order_status_completed',
	function ( $order_id ) {
		wildflower_tg_notify_event( $order_id, 'paid' );
	},
	20
);
add_action(
	'woocommerce_order_status_cancelled',
	function ( $order_id ) {
		wildflower_tg_notify_event( $order_id, 'cancelled' );
	},
	20
);
add_action(
	'woocommerce_order_refunded',
	function ( $order_id, $refund_id ) {
		wildflower_tg_notify_event( $order_id, 'refunded', $refund_id );
	},
	20,
	2
);
