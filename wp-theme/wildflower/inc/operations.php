<?php
/**
 * Lead delivery (Telegram + purpose-routed email), operations settings, and
 * Wildflower order numbers.
 *
 * @package Wildflower
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const WILDFLOWER_OPERATIONS_OPTION = 'wildflower_operations_settings';

/**
 * Return default operations settings.
 *
 * @return array<string, string>
 */
function wildflower_operations_defaults() {
	return array(
		'telegram_bot_token'           => '',
		'telegram_recipient_ids'       => '',
		'telegram_group_chat_id'       => '',
		'telegram_topic_orders'        => '',
		'telegram_topic_leads'         => '',
		'telegram_topic_subscriptions' => '',
	);
}

/**
 * Return normalized operations settings.
 *
 * @return array<string, string>
 */
function wildflower_operations_settings() {
	$settings = get_option( WILDFLOWER_OPERATIONS_OPTION, array() );
	if ( ! is_array( $settings ) ) {
		$settings = array();
	}

	return wp_parse_args( $settings, wildflower_operations_defaults() );
}

/**
 * Return one purpose-routed studio mailbox, falling back to the public address.
 *
 * @param string $purpose One of `studio`, `orders`, `subscriptions`.
 * @return string Email address, or an empty string when none is configured.
 */
function wildflower_studio_email( $purpose ) {
	$brand  = wildflower_brand();
	$emails = isset( $brand['emails'] ) && is_array( $brand['emails'] ) ? $brand['emails'] : array();

	if ( isset( $emails[ $purpose ] ) && is_email( $emails[ $purpose ] ) ) {
		return $emails[ $purpose ];
	}

	return isset( $brand['email'] ) && is_email( $brand['email'] ) ? $brand['email'] : '';
}

/**
 * Contact form topics, keyed by the stable slug that is submitted and routed on.
 *
 * @return array<string, string>
 */
function wildflower_contact_topics() {
	return array(
		'order'        => __( 'An order', 'wildflower' ),
		'subscription' => __( 'Flower subscription', 'wildflower' ),
		'custom'       => __( 'Custom / bespoke arrangement', 'wildflower' ),
		'weddings'     => __( 'Weddings & events', 'wildflower' ),
		'corporate'    => __( 'Corporate gifting', 'wildflower' ),
		'other'        => __( 'Something else', 'wildflower' ),
	);
}

/**
 * Decide which mailbox a lead belongs to.
 *
 * Subscription interest goes to the subscriptions mailbox; everything else is
 * order traffic.
 *
 * @param string                $source Lead source.
 * @param array<string, string> $fields Validated fields.
 * @return string Mailbox purpose.
 */
function wildflower_lead_mailbox( $source, $fields ) {
	if ( 'newsletter' === $source ) {
		return 'subscriptions';
	}

	if ( 'contact' === $source && isset( $fields['topic'] ) && 'subscription' === $fields['topic'] ) {
		return 'subscriptions';
	}

	return 'orders';
}

/**
 * Telegram destinations, keyed by the route slug stored in the settings.
 *
 * @return array<string, string>
 */
function wildflower_telegram_routes() {
	return array(
		'orders'        => __( 'Orders', 'wildflower' ),
		'leads'         => __( 'Leads', 'wildflower' ),
		'subscriptions' => __( 'Subscriptions', 'wildflower' ),
	);
}

/**
 * Decide which Telegram topic a lead belongs to.
 *
 * Subscription interest is its own topic; every other form is a lead. Paid
 * WooCommerce orders are the only thing that goes to the orders topic.
 *
 * @param string                $source Lead source.
 * @param array<string, string> $fields Validated fields.
 * @return string Route slug.
 */
function wildflower_lead_telegram_route( $source, $fields ) {
	return 'subscriptions' === wildflower_lead_mailbox( $source, $fields ) ? 'subscriptions' : 'leads';
}

/**
 * Resolve the forum topic ID for one route.
 *
 * An unset subscriptions topic falls back to the leads topic, and an unset
 * topic altogether posts to the group's General topic.
 *
 * @param string $route Route slug.
 * @return string Topic ID, or an empty string.
 */
function wildflower_telegram_thread_id( $route ) {
	$settings = wildflower_operations_settings();
	$key      = 'telegram_topic_' . $route;
	$thread   = isset( $settings[ $key ] ) ? (string) $settings[ $key ] : '';

	if ( '' === $thread && 'subscriptions' === $route ) {
		$thread = (string) $settings['telegram_topic_leads'];
	}

	return $thread;
}

/**
 * Resolve where one route is delivered.
 *
 * A configured group is the single destination and carries the topic; the
 * per-person recipient IDs are the fallback used only while no group is set.
 *
 * @param string $route Route slug.
 * @return array<int, array<string, string>> Chat ID / thread ID pairs.
 */
function wildflower_telegram_targets( $route ) {
	$settings = wildflower_operations_settings();
	$group    = trim( (string) $settings['telegram_group_chat_id'] );

	if ( '' !== $group ) {
		return array(
			array(
				'chat_id'   => $group,
				'thread_id' => wildflower_telegram_thread_id( $route ),
			),
		);
	}

	$targets = array();
	foreach ( wildflower_parse_telegram_recipient_ids( $settings['telegram_recipient_ids'] ) as $recipient ) {
		$targets[] = array(
			'chat_id'   => $recipient,
			'thread_id' => '',
		);
	}

	return $targets;
}

/**
 * Parse and deduplicate comma-separated Telegram user or group IDs.
 *
 * @param string $raw_ids Comma-separated IDs.
 * @return array<int, string>
 */
function wildflower_parse_telegram_recipient_ids( $raw_ids ) {
	$parts = preg_split( '/\s*,\s*/', (string) $raw_ids, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! is_array( $parts ) ) {
		return array();
	}

	$valid_ids = array();
	foreach ( $parts as $part ) {
		$id = trim( $part );
		if ( preg_match( '/^-?[1-9][0-9]*$/', $id ) ) {
			$valid_ids[ $id ] = $id;
		}
	}

	return array_values( $valid_ids );
}

/**
 * Sanitize operations settings without printing the stored bot token.
 *
 * An empty token field preserves the existing token. The explicit checkbox is
 * required to remove it.
 *
 * @param mixed $input Submitted settings.
 * @return array<string, string>
 */
function wildflower_sanitize_operations_settings( $input ) {
	$current = wildflower_operations_settings();
	$input   = is_array( $input ) ? $input : array();
	$clean   = $current;

	if ( ! empty( $input['clear_bot_token'] ) ) {
		$clean['telegram_bot_token'] = '';
	} elseif ( isset( $input['telegram_bot_token'] ) && is_scalar( $input['telegram_bot_token'] ) ) {
		$token = trim( sanitize_text_field( wp_unslash( $input['telegram_bot_token'] ) ) );
		if ( '' !== $token ) {
			if ( preg_match( '/^[0-9]{5,}:[A-Za-z0-9_-]{20,}$/', $token ) ) {
				$clean['telegram_bot_token'] = $token;
			} else {
				add_settings_error(
					WILDFLOWER_OPERATIONS_OPTION,
					'wildflower_invalid_bot_token',
					__( 'The Telegram Bot Token format is invalid. The previously saved token was kept.', 'wildflower' ),
					'error'
				);
			}
		}
	}

	$raw_ids = isset( $input['telegram_recipient_ids'] ) && is_scalar( $input['telegram_recipient_ids'] ) ? sanitize_text_field( wp_unslash( $input['telegram_recipient_ids'] ) ) : '';
	$ids     = wildflower_parse_telegram_recipient_ids( $raw_ids );
	$parts   = preg_split( '/\s*,\s*/', $raw_ids, -1, PREG_SPLIT_NO_EMPTY );
	$parts   = is_array( $parts ) ? array_map( 'trim', $parts ) : array();
	$invalid = array_filter(
		$parts,
		static function ( $part ) {
			return ! preg_match( '/^-?[1-9][0-9]*$/', $part );
		}
	);

	if ( $invalid ) {
		add_settings_error(
			WILDFLOWER_OPERATIONS_OPTION,
			'wildflower_invalid_recipient_ids',
			__( 'Only valid Telegram IDs were saved. Use integers separated by commas; group IDs may begin with a minus sign.', 'wildflower' ),
			'warning'
		);
	}

	$clean['telegram_recipient_ids'] = implode( ', ', $ids );

	$raw_group = isset( $input['telegram_group_chat_id'] ) && is_scalar( $input['telegram_group_chat_id'] ) ? trim( sanitize_text_field( wp_unslash( $input['telegram_group_chat_id'] ) ) ) : '';
	if ( '' === $raw_group || preg_match( '/^-?[1-9][0-9]*$/', $raw_group ) ) {
		$clean['telegram_group_chat_id'] = $raw_group;
	} else {
		add_settings_error(
			WILDFLOWER_OPERATIONS_OPTION,
			'wildflower_invalid_group_chat_id',
			__( 'The Group Chat ID must be a whole number. A supergroup ID normally begins with -100. The previous value was kept.', 'wildflower' ),
			'error'
		);
	}

	foreach ( array_keys( wildflower_telegram_routes() ) as $route ) {
		$key       = 'telegram_topic_' . $route;
		$raw_topic = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? trim( sanitize_text_field( wp_unslash( $input[ $key ] ) ) ) : '';

		if ( '' === $raw_topic || preg_match( '/^[1-9][0-9]*$/', $raw_topic ) ) {
			$clean[ $key ] = $raw_topic;
		} else {
			add_settings_error(
				WILDFLOWER_OPERATIONS_OPTION,
				'wildflower_invalid_topic_' . $route,
				__( 'Topic IDs must be positive whole numbers. The previous value was kept.', 'wildflower' ),
				'error'
			);
		}
	}

	unset( $clean['clear_bot_token'] );

	return $clean;
}

/**
 * Register operations settings.
 */
function wildflower_register_operations_settings() {
	register_setting(
		'wildflower_operations',
		WILDFLOWER_OPERATIONS_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'wildflower_sanitize_operations_settings',
			'default'           => wildflower_operations_defaults(),
			'show_in_rest'      => false,
		)
	);
}
add_action( 'admin_init', 'wildflower_register_operations_settings' );

/**
 * Add the native WordPress operations settings page.
 */
function wildflower_add_operations_page() {
	add_options_page(
		__( 'Wildflower Operations', 'wildflower' ),
		__( 'Wildflower Operations', 'wildflower' ),
		'manage_options',
		'wildflower-operations',
		'wildflower_render_operations_page'
	);
}
add_action( 'admin_menu', 'wildflower_add_operations_page' );

/**
 * Render the native WordPress operations settings page.
 */
function wildflower_render_operations_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings   = wildflower_operations_settings();
	$recipients = wildflower_parse_telegram_recipient_ids( $settings['telegram_recipient_ids'] );
	$configured = '' !== $settings['telegram_bot_token'];
	$test_state = isset( $_GET['wf_telegram_test'] ) ? sanitize_key( wp_unslash( $_GET['wf_telegram_test'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$test_sent  = isset( $_GET['wf_test_sent'] ) ? absint( $_GET['wf_test_sent'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$test_fail  = isset( $_GET['wf_test_failed'] ) ? absint( $_GET['wf_test_failed'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$group      = trim( (string) $settings['telegram_group_chat_id'] );
	$test_attrs = $configured && ( '' !== $group || $recipients ) ? array() : array( 'disabled' => 'disabled' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Wildflower Operations', 'wildflower' ); ?></h1>
		<p><?php esc_html_e( 'Requests are delivered by email and, optionally, Telegram. A request is accepted as long as one of the two channels succeeds. The bot token is stored only in WordPress and is never committed to GitHub.', 'wildflower' ); ?></p>

		<h2><?php esc_html_e( 'Email Routing', 'wildflower' ); ?></h2>
		<table class="widefat striped" style="max-width:46rem;">
			<tbody>
				<tr>
					<td><?php esc_html_e( 'Contact requests and Custom Order requests', 'wildflower' ); ?></td>
					<td><code><?php echo esc_html( wildflower_studio_email( 'orders' ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Subscription requests and newsletter sign-ups', 'wildflower' ); ?></td>
					<td><code><?php echo esc_html( wildflower_studio_email( 'subscriptions' ) ); ?></code></td>
				</tr>
				<tr>
					<td><?php esc_html_e( 'Public studio address shown on the site', 'wildflower' ); ?></td>
					<td><code><?php echo esc_html( wildflower_studio_email( 'studio' ) ); ?></code></td>
				</tr>
			</tbody>
		</table>
		<p class="description"><?php esc_html_e( 'These addresses live in the theme so a deployment cannot lose them. WooCommerce customer notifications are sent separately from the WooCommerce email settings.', 'wildflower' ); ?></p>

		<hr>
		<h2><?php esc_html_e( 'Telegram', 'wildflower' ); ?></h2>

		<?php settings_errors( WILDFLOWER_OPERATIONS_OPTION ); ?>
		<?php if ( 'success' === $test_state ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php printf( esc_html__( 'Telegram test delivered to %1$d recipient(s). Failed: %2$d.', 'wildflower' ), esc_html( $test_sent ), esc_html( $test_fail ) ); ?></p></div>
		<?php elseif ( 'error' === $test_state ) : ?>
			<div class="notice notice-error is-dismissible"><p><?php printf( esc_html__( 'Telegram test failed. Delivered: %1$d. Failed: %2$d. Check the token, recipient IDs, and that each recipient has started the bot.', 'wildflower' ), esc_html( $test_sent ), esc_html( $test_fail ) ); ?></p></div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:46rem;">
			<tbody>
				<?php foreach ( wildflower_telegram_routes() as $route_slug => $route_label ) : ?>
					<?php
					$route_targets = wildflower_telegram_targets( $route_slug );
					if ( ! $configured || empty( $route_targets ) ) {
						$route_where = __( 'not configured', 'wildflower' );
					} elseif ( '' !== $group ) {
						$route_thread = wildflower_telegram_thread_id( $route_slug );
						$route_where  = '' !== $route_thread
							? sprintf( /* translators: 1: group chat ID, 2: topic ID. */ __( 'group %1$s, topic %2$s', 'wildflower' ), $group, $route_thread )
							: sprintf( /* translators: %s: group chat ID. */ __( 'group %s, General topic', 'wildflower' ), $group );
					} else {
						$route_where = sprintf( /* translators: %d: number of recipients. */ _n( '%d direct recipient', '%d direct recipients', count( $route_targets ), 'wildflower' ), count( $route_targets ) );
					}
					?>
					<tr>
						<td><?php echo esc_html( $route_label ); ?></td>
						<td><code><?php echo esc_html( $route_where ); ?></code></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description"><?php esc_html_e( 'Both storefronts post into the same group, each using its own bot. The topic IDs above decide where Wildflower traffic lands, so point them at whichever topics you keep — one per site, or one per kind of traffic. Add every bot to the group before saving.', 'wildflower' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'wildflower_operations' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="wildflower-telegram-token"><?php esc_html_e( 'Telegram Bot Token', 'wildflower' ); ?></label></th>
					<td>
						<input id="wildflower-telegram-token" class="regular-text" type="password" name="<?php echo esc_attr( WILDFLOWER_OPERATIONS_OPTION ); ?>[telegram_bot_token]" value="" autocomplete="new-password" spellcheck="false">
						<p class="description"><?php echo $configured ? esc_html__( 'A token is configured. Leave this field empty to keep it, or enter a new token to replace it.', 'wildflower' ) : esc_html__( 'Paste the BotFather token. It will not be shown again after saving.', 'wildflower' ); ?></p>
						<?php if ( $configured ) : ?>
							<label><input type="checkbox" name="<?php echo esc_attr( WILDFLOWER_OPERATIONS_OPTION ); ?>[clear_bot_token]" value="1"> <?php esc_html_e( 'Remove the saved bot token', 'wildflower' ); ?></label>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wildflower-telegram-group"><?php esc_html_e( 'Group Chat ID', 'wildflower' ); ?></label></th>
					<td>
						<input id="wildflower-telegram-group" class="regular-text" type="text" name="<?php echo esc_attr( WILDFLOWER_OPERATIONS_OPTION ); ?>[telegram_group_chat_id]" value="<?php echo esc_attr( $settings['telegram_group_chat_id'] ); ?>" spellcheck="false" placeholder="-1001234567890">
						<p class="description"><?php esc_html_e( 'The shared group both storefront bots post into. A supergroup ID begins with -100. Leave empty to fall back to the per-person recipient IDs below.', 'wildflower' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wildflower-topic-orders"><?php esc_html_e( 'Topic ID — Orders', 'wildflower' ); ?></label></th>
					<td>
						<input id="wildflower-topic-orders" class="small-text" type="text" name="<?php echo esc_attr( WILDFLOWER_OPERATIONS_OPTION ); ?>[telegram_topic_orders]" value="<?php echo esc_attr( $settings['telegram_topic_orders'] ); ?>" spellcheck="false" placeholder="2">
						<p class="description"><?php esc_html_e( 'Paid WooCommerce orders (WF-…).', 'wildflower' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wildflower-topic-leads"><?php esc_html_e( 'Topic ID — Leads', 'wildflower' ); ?></label></th>
					<td>
						<input id="wildflower-topic-leads" class="small-text" type="text" name="<?php echo esc_attr( WILDFLOWER_OPERATIONS_OPTION ); ?>[telegram_topic_leads]" value="<?php echo esc_attr( $settings['telegram_topic_leads'] ); ?>" spellcheck="false" placeholder="3">
						<p class="description"><?php esc_html_e( 'Contact form and Custom Order form.', 'wildflower' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wildflower-topic-subscriptions"><?php esc_html_e( 'Topic ID — Subscriptions', 'wildflower' ); ?></label></th>
					<td>
						<input id="wildflower-topic-subscriptions" class="small-text" type="text" name="<?php echo esc_attr( WILDFLOWER_OPERATIONS_OPTION ); ?>[telegram_topic_subscriptions]" value="<?php echo esc_attr( $settings['telegram_topic_subscriptions'] ); ?>" spellcheck="false" placeholder="4">
						<p class="description"><?php esc_html_e( 'Newsletter sign-ups and subscription enquiries. Optional — leave empty to send these to the Leads topic.', 'wildflower' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wildflower-telegram-recipients"><?php esc_html_e( 'Recipient Telegram IDs', 'wildflower' ); ?></label></th>
					<td>
						<input id="wildflower-telegram-recipients" class="regular-text" type="text" name="<?php echo esc_attr( WILDFLOWER_OPERATIONS_OPTION ); ?>[telegram_recipient_ids]" value="<?php echo esc_attr( $settings['telegram_recipient_ids'] ); ?>" spellcheck="false" placeholder="123456789, 987654321">
						<p class="description"><?php esc_html_e( 'Fallback only: used while no Group Chat ID is set, so nothing is delivered twice. Separate IDs with commas; every recipient must start the bot first.', 'wildflower' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save Telegram Settings', 'wildflower' ) ); ?>
		</form>

		<hr>
		<h2><?php esc_html_e( 'Connection Test', 'wildflower' ); ?></h2>
		<p><?php esc_html_e( 'Save the settings first. With a group configured the test posts one message into each topic, so you can confirm every topic ID landed where you meant it to.', 'wildflower' ); ?></p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="wildflower_test_telegram">
			<?php wp_nonce_field( 'wildflower_test_telegram' ); ?>
			<?php submit_button( __( 'Test Telegram Connection', 'wildflower' ), 'secondary', 'submit', false, $test_attrs ); ?>
		</form>
	</div>
	<?php
}

/**
 * Send one plain-text message to one Telegram recipient.
 *
 * @param string $token     Telegram bot token.
 * @param string $chat_id   Telegram recipient ID.
 * @param string $text      Message text.
 * @param string $thread_id Forum topic ID, or an empty string for none.
 * @return true|WP_Error
 */
function wildflower_send_telegram_to_recipient( $token, $chat_id, $text, $thread_id = '' ) {
	$body = array(
		'chat_id'                  => $chat_id,
		'text'                     => $text,
		'disable_web_page_preview' => 'true',
	);

	// Only forum groups accept a thread; sending one to a private chat errors out.
	if ( '' !== (string) $thread_id ) {
		$body['message_thread_id'] = (string) $thread_id;
	}

	$response = wp_remote_post(
		'https://api.telegram.org/bot' . $token . '/sendMessage',
		array(
			'timeout' => 12,
			'body'    => $body,
		)
	);

	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'wildflower_telegram_network', __( 'Telegram could not be reached.', 'wildflower' ) );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( 200 !== wp_remote_retrieve_response_code( $response ) || ! is_array( $body ) || empty( $body['ok'] ) ) {
		return new WP_Error( 'wildflower_telegram_api', __( 'Telegram rejected the message.', 'wildflower' ) );
	}

	return true;
}

/**
 * Send one message to every destination configured for a route.
 *
 * @param string $text  Message text.
 * @param string $route Route slug; see wildflower_telegram_routes().
 * @return array<string, int>|WP_Error
 */
function wildflower_send_telegram_message( $text, $route = 'leads' ) {
	$settings = wildflower_operations_settings();
	$targets  = wildflower_telegram_targets( $route );

	if ( '' === $settings['telegram_bot_token'] || empty( $targets ) ) {
		return new WP_Error( 'wildflower_telegram_not_configured', __( 'Telegram is not configured yet.', 'wildflower' ) );
	}

	$result = array(
		'sent'   => 0,
		'failed' => 0,
	);

	foreach ( $targets as $target ) {
		$sent = wildflower_send_telegram_to_recipient( $settings['telegram_bot_token'], $target['chat_id'], $text, $target['thread_id'] );
		if ( is_wp_error( $sent ) ) {
			++$result['failed'];
		} else {
			++$result['sent'];
		}
	}

	return $result;
}

/**
 * Handle the native WordPress admin Telegram connection test.
 */
function wildflower_handle_telegram_test() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to test these settings.', 'wildflower' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'wildflower_test_telegram' );

	$settings = wildflower_operations_settings();
	$routes   = '' !== trim( (string) $settings['telegram_group_chat_id'] ) ? array_keys( wildflower_telegram_routes() ) : array( 'leads' );
	$sent     = 0;
	$failed   = 0;
	$seen     = array();

	foreach ( $routes as $route ) {
		// Two routes pointed at one topic would otherwise be tested twice.
		$fingerprint = wp_json_encode( wildflower_telegram_targets( $route ) );
		if ( isset( $seen[ $fingerprint ] ) ) {
			continue;
		}
		$seen[ $fingerprint ] = true;

		$labels       = wildflower_telegram_routes();
		$test_message = sprintf(
			/* translators: 1: route label, 2: current site URL. */
			__( "Wildflower Telegram connection test\nTopic: %1\$s\nSite: %2\$s\nStatus: connected", 'wildflower' ),
			isset( $labels[ $route ] ) ? $labels[ $route ] : $route,
			home_url( '/' )
		);

		$result = wildflower_send_telegram_message( $test_message, $route );
		if ( is_wp_error( $result ) ) {
			++$failed;
			continue;
		}

		$sent   += $result['sent'];
		$failed += $result['failed'];
	}

	$state = $sent > 0 && 0 === $failed ? 'success' : 'error';
	$redirect     = add_query_arg(
		array(
			'page'             => 'wildflower-operations',
			'wf_telegram_test' => $state,
			'wf_test_sent'     => $sent,
			'wf_test_failed'   => $failed,
		),
		admin_url( 'options-general.php' )
	);

	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_wildflower_test_telegram', 'wildflower_handle_telegram_test' );

/**
 * Return a privacy-preserving rate-limit key for the visitor.
 *
 * @return string
 */
function wildflower_lead_rate_limit_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	return 'wf_lead_' . hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) );
}

/**
 * Increment the lead counter and report whether the request is blocked.
 *
 * @return bool
 */
function wildflower_lead_is_rate_limited() {
	$key   = wildflower_lead_rate_limit_key();
	$count = absint( get_transient( $key ) );

	if ( $count >= 5 ) {
		return true;
	}

	set_transient( $key, $count + 1, 15 * MINUTE_IN_SECONDS );
	return false;
}

/**
 * Read and sanitize one submitted lead field.
 *
 * @param string $name      Field name.
 * @param int    $max_chars Maximum characters.
 * @param bool   $multiline Preserve line breaks.
 * @return string
 */
function wildflower_lead_field( $name, $max_chars = 200, $multiline = false ) {
	if ( ! isset( $_POST[ $name ] ) || ! is_scalar( $_POST[ $name ] ) ) {
		return '';
	}

	$value = wp_unslash( $_POST[ $name ] );
	$value = $multiline ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	return function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $max_chars ) : substr( $value, 0, $max_chars );
}

/**
 * Validate a Contact form submission.
 *
 * @return array<string, string>|WP_Error
 */
function wildflower_validate_contact_lead() {
	$fields = array(
		'first_name' => wildflower_lead_field( 'first_name', 80 ),
		'last_name'  => wildflower_lead_field( 'last_name', 80 ),
		'email'      => sanitize_email( wildflower_lead_field( 'email', 190 ) ),
		'phone'      => wildflower_lead_field( 'phone', 40 ),
		'topic'      => wildflower_lead_field( 'topic', 40 ),
		'message'    => wildflower_lead_field( 'message', 1500, true ),
	);
	$errors = array();

	if ( ! array_key_exists( $fields['topic'], wildflower_contact_topics() ) ) {
		$fields['topic'] = 'order';
	}

	if ( '' === $fields['first_name'] ) {
		$errors[] = 'first_name';
	}
	if ( ! is_email( $fields['email'] ) ) {
		$errors[] = 'email';
	}
	if ( '' === $fields['message'] ) {
		$errors[] = 'message';
	}

	if ( $errors ) {
		return new WP_Error( 'wildflower_invalid_lead', __( 'Please complete the required fields and use a valid email address.', 'wildflower' ), array( 'fields' => $errors ) );
	}

	return $fields;
}

/**
 * Validate a Custom Order form submission.
 *
 * @return array<string, string>|WP_Error
 */
function wildflower_validate_custom_order_lead() {
	$fields = array(
		'name'     => wildflower_lead_field( 'name', 160 ),
		'email'    => sanitize_email( wildflower_lead_field( 'email', 190 ) ),
		'phone'    => wildflower_lead_field( 'phone', 40 ),
		'occasion' => wildflower_lead_field( 'occasion', 100 ),
		'date'     => wildflower_lead_field( 'date', 20 ),
		'budget'   => wildflower_lead_field( 'budget', 80 ),
		'palette'  => wildflower_lead_field( 'palette', 160 ),
		'location' => wildflower_lead_field( 'location', 160 ),
		'details'  => wildflower_lead_field( 'details', 1500, true ),
	);
	$errors = array();

	if ( '' === $fields['name'] ) {
		$errors[] = 'name';
	}
	if ( ! is_email( $fields['email'] ) ) {
		$errors[] = 'email';
	}
	if ( '' === $fields['details'] ) {
		$errors[] = 'details';
	}

	if ( $errors ) {
		return new WP_Error( 'wildflower_invalid_lead', __( 'Please complete the required fields and use a valid email address.', 'wildflower' ), array( 'fields' => $errors ) );
	}

	return $fields;
}

/**
 * Validate a footer newsletter submission.
 *
 * @return array<string, string>|WP_Error
 */
function wildflower_validate_newsletter_lead() {
	$fields = array(
		'email' => sanitize_email( wildflower_lead_field( 'email', 190 ) ),
	);

	if ( ! is_email( $fields['email'] ) ) {
		return new WP_Error( 'wildflower_invalid_lead', __( 'Please enter a valid email address.', 'wildflower' ), array( 'fields' => array( 'email' ) ) );
	}

	return $fields;
}

/**
 * Human-readable title for one lead source.
 *
 * @param string $source Lead source.
 * @return string
 */
function wildflower_lead_title( $source ) {
	if ( 'custom_order' === $source ) {
		return __( 'CUSTOM ORDER REQUEST', 'wildflower' );
	}

	if ( 'newsletter' === $source ) {
		return __( 'NEWSLETTER SIGN-UP', 'wildflower' );
	}

	return __( 'CONTACT REQUEST', 'wildflower' );
}

/**
 * Format a validated lead as plain text, used for both Telegram and email.
 *
 * @param string                $source Lead source.
 * @param array<string, string> $fields Validated fields.
 * @return string
 */
function wildflower_format_lead_message( $source, $fields ) {
	$labels = array(
		'first_name' => __( 'First name', 'wildflower' ),
		'last_name'  => __( 'Last name', 'wildflower' ),
		'name'       => __( 'Name', 'wildflower' ),
		'email'      => __( 'Email', 'wildflower' ),
		'phone'      => __( 'Phone', 'wildflower' ),
		'topic'      => __( 'Topic', 'wildflower' ),
		'occasion'   => __( 'Occasion', 'wildflower' ),
		'date'       => __( 'Date needed', 'wildflower' ),
		'budget'     => __( 'Budget', 'wildflower' ),
		'palette'    => __( 'Colors / palette', 'wildflower' ),
		'location'   => __( 'Delivery city / ZIP', 'wildflower' ),
		'message'    => __( 'Message', 'wildflower' ),
		'details'    => __( 'Vision', 'wildflower' ),
	);
	$topics = wildflower_contact_topics();
	$lines  = array(
		'[WILDFLOWER] ' . wildflower_lead_title( $source ),
		__( 'Store: Wildflower', 'wildflower' ),
	);

	foreach ( $labels as $key => $label ) {
		if ( empty( $fields[ $key ] ) ) {
			continue;
		}

		$value = 'topic' === $key && isset( $topics[ $fields[ $key ] ] ) ? $topics[ $fields[ $key ] ] : $fields[ $key ];
		$lines[] = $label . ': ' . $value;
	}

	$lines[] = __( 'Submitted:', 'wildflower' ) . ' ' . wp_date( 'M j, Y g:i A T' );
	$referer = wp_get_referer();
	if ( $referer ) {
		$lines[] = __( 'Page:', 'wildflower' ) . ' ' . esc_url_raw( $referer );
	}

	return implode( "\n", $lines );
}

/**
 * Deliver one validated lead to its routed studio mailbox.
 *
 * The mailbox itself is the sender so the envelope stays aligned with the site
 * domain for SPF/DMARC; the visitor's address goes in Reply-To.
 *
 * @param string                $source Lead source.
 * @param array<string, string> $fields Validated fields.
 * @param string                $body   Plain-text message body.
 * @return true|WP_Error
 */
function wildflower_send_lead_email( $source, $fields, $body ) {
	$to = wildflower_studio_email( wildflower_lead_mailbox( $source, $fields ) );

	if ( '' === $to ) {
		return new WP_Error( 'wildflower_email_not_configured', __( 'No studio mailbox is configured.', 'wildflower' ) );
	}

	$brand   = wildflower_brand();
	$name    = sanitize_text_field( ! empty( $brand['name'] ) ? $brand['name'] : 'Wildflower' );
	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		sprintf( 'From: %s <%s>', $name, $to ),
	);

	if ( ! empty( $fields['email'] ) && is_email( $fields['email'] ) ) {
		$headers[] = 'Reply-To: ' . $fields['email'];
	}

	$subject = sprintf( '[Wildflower] %s', wildflower_lead_title( $source ) );
	$sent    = wp_mail( $to, $subject, $body, $headers );

	return $sent ? true : new WP_Error( 'wildflower_email_failed', __( 'The studio mailbox could not be reached.', 'wildflower' ) );
}

/**
 * Handle public Contact, Custom Order, and newsletter AJAX submissions.
 */
function wildflower_handle_lead_submission() {
	$method = isset( $_SERVER['REQUEST_METHOD'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) : '';
	if ( 'POST' !== strtoupper( $method ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid request method.', 'wildflower' ) ), 405 );
	}

	if ( ! check_ajax_referer( 'wildflower_submit_lead', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'This form expired. Refresh the page and try again.', 'wildflower' ) ), 403 );
	}

	if ( '' !== wildflower_lead_field( 'website', 200 ) ) {
		wp_send_json_success( array( 'message' => __( 'Thank you. Your request has been sent.', 'wildflower' ) ) );
	}

	if ( wildflower_lead_is_rate_limited() ) {
		wp_send_json_error( array( 'message' => __( 'Too many requests were sent. Please wait 15 minutes or contact us by phone or WhatsApp.', 'wildflower' ) ), 429 );
	}

	$source = wildflower_lead_field( 'source', 30 );
	if ( 'contact' === $source ) {
		$fields = wildflower_validate_contact_lead();
	} elseif ( 'custom_order' === $source ) {
		$fields = wildflower_validate_custom_order_lead();
	} elseif ( 'newsletter' === $source ) {
		$fields = wildflower_validate_newsletter_lead();
	} else {
		wp_send_json_error( array( 'message' => __( 'Unknown form source.', 'wildflower' ) ), 400 );
	}

	if ( is_wp_error( $fields ) ) {
		$error_data   = $fields->get_error_data();
		$error_fields = is_array( $error_data ) && isset( $error_data['fields'] ) && is_array( $error_data['fields'] ) ? $error_data['fields'] : array();
		wp_send_json_error(
			array(
				'message' => $fields->get_error_message(),
				'fields'  => $error_fields,
			),
			422
		);
	}

	/*
	 * Two independent channels. The lead is accepted when either one lands, so a
	 * site with no Telegram token still delivers by email and vice versa.
	 */
	$body     = wildflower_format_lead_message( $source, $fields );
	$telegram = wildflower_send_telegram_message( $body, wildflower_lead_telegram_route( $source, $fields ) );
	$email    = wildflower_send_lead_email( $source, $fields, $body );

	$telegram_ok = ! is_wp_error( $telegram ) && ! empty( $telegram['sent'] );
	$email_ok    = ! is_wp_error( $email );

	if ( ! $telegram_ok && ! $email_ok ) {
		wp_send_json_error( array( 'message' => __( 'Messaging is temporarily unavailable. Please call or contact us on WhatsApp.', 'wildflower' ) ), 503 );
	}

	if ( ! $email_ok ) {
		error_log( sprintf( 'Wildflower lead email failed: %s', $email->get_error_message() ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	if ( $telegram_ok && $telegram['failed'] > 0 ) {
		error_log( sprintf( 'Wildflower Telegram lead delivered partially: %d recipient(s) failed.', absint( $telegram['failed'] ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}

	do_action( 'wildflower_lead_sent', $source, $fields, array( 'telegram' => $telegram, 'email' => $email ) );

	$confirmation = 'newsletter' === $source
		? __( 'Thank you. You are on the list.', 'wildflower' )
		: __( 'Thank you. Your request has been sent to our studio team.', 'wildflower' );

	wp_send_json_success( array( 'message' => $confirmation ) );
}
add_action( 'wp_ajax_wildflower_submit_lead', 'wildflower_handle_lead_submission' );
add_action( 'wp_ajax_nopriv_wildflower_submit_lead', 'wildflower_handle_lead_submission' );

/**
 * Prefix displayed WooCommerce order numbers without altering database IDs.
 *
 * @param string|int $order_number Existing order number.
 * @param WC_Order   $order        Order object.
 * @return string
 */
function wildflower_order_number( $order_number, $order ) {
	if ( ! is_object( $order ) || ! method_exists( $order, 'get_id' ) ) {
		return (string) $order_number;
	}

	return sprintf( 'WF-%06d', $order->get_id() );
}
add_filter( 'woocommerce_order_number', 'wildflower_order_number', 10, 2 );

/**
 * Format one WooCommerce order for the Telegram orders topic.
 *
 * @param WC_Order $order Order object.
 * @return string
 */
function wildflower_format_order_message( $order ) {
	$lines = array(
		'[WILDFLOWER] NEW ORDER',
		__( 'Store: Wildflower', 'wildflower' ),
		__( 'Order:', 'wildflower' ) . ' ' . $order->get_order_number(),
		__( 'Total:', 'wildflower' ) . ' ' . html_entity_decode( wp_strip_all_tags( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ), ENT_QUOTES, 'UTF-8' ),
		__( 'Status:', 'wildflower' ) . ' ' . wc_get_order_status_name( $order->get_status() ),
		__( 'Payment:', 'wildflower' ) . ' ' . ( $order->get_payment_method_title() ? $order->get_payment_method_title() : $order->get_payment_method() ),
	);

	$customer = trim( $order->get_formatted_billing_full_name() );
	if ( '' !== $customer ) {
		$lines[] = __( 'Customer:', 'wildflower' ) . ' ' . $customer;
	}

	if ( $order->get_billing_email() ) {
		$lines[] = __( 'Email:', 'wildflower' ) . ' ' . $order->get_billing_email();
	}

	if ( $order->get_billing_phone() ) {
		$lines[] = __( 'Phone:', 'wildflower' ) . ' ' . $order->get_billing_phone();
	}

	$destination = trim( wp_strip_all_tags( $order->get_formatted_shipping_address() ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address() ) );
	if ( '' !== $destination ) {
		$lines[] = __( 'Deliver to:', 'wildflower' ) . ' ' . preg_replace( '/\s*\n\s*/', ', ', $destination );
	}

	$items = array();
	foreach ( $order->get_items() as $item ) {
		$items[] = $item->get_quantity() . ' x ' . $item->get_name();
	}

	if ( $items ) {
		$lines[] = __( 'Items:', 'wildflower' ) . ' ' . implode( '; ', $items );
	}

	if ( $order->get_customer_note() ) {
		$lines[] = __( 'Note:', 'wildflower' ) . ' ' . $order->get_customer_note();
	}

	$lines[] = __( 'Admin:', 'wildflower' ) . ' ' . $order->get_edit_order_url();

	return implode( "\n", $lines );
}

/**
 * Post a newly placed order into the Telegram orders topic.
 *
 * Guarded by order meta because the classic and block checkouts each fire their
 * own hook, and a resumed payment can replay the same order.
 *
 * @param int $order_id Order ID.
 */
function wildflower_notify_new_order( $order_id ) {
	if ( ! function_exists( 'wc_get_order' ) ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order || 'yes' === $order->get_meta( '_wildflower_telegram_notified', true ) ) {
		return;
	}

	$result = wildflower_send_telegram_message( wildflower_format_order_message( $order ), 'orders' );
	if ( is_wp_error( $result ) || empty( $result['sent'] ) ) {
		if ( is_wp_error( $result ) && 'wildflower_telegram_not_configured' !== $result->get_error_code() ) {
			error_log( sprintf( 'Wildflower order Telegram notice failed for #%d: %s', absint( $order_id ), $result->get_error_message() ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
		return;
	}

	$order->update_meta_data( '_wildflower_telegram_notified', 'yes' );
	$order->save();
}
add_action( 'woocommerce_checkout_order_processed', 'wildflower_notify_new_order', 20 );
add_action( 'woocommerce_store_api_checkout_order_processed', 'wildflower_notify_new_order_object', 20 );

/**
 * Block checkout hands over the order object rather than its ID.
 *
 * @param WC_Order $order Order object.
 */
function wildflower_notify_new_order_object( $order ) {
	if ( is_object( $order ) && method_exists( $order, 'get_id' ) ) {
		wildflower_notify_new_order( $order->get_id() );
	}
}
