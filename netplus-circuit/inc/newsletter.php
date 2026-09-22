<?php
/**
 * Lightweight newsletter capture: custom DB table + protected AJAX endpoint
 * + an admin screen to view & export subscribers.
 *
 * @package NetPlus_Circuit
 */

defined( 'ABSPATH' ) || exit;

/**
 * Table name.
 */
function np_newsletter_table() {
	global $wpdb;
	return $wpdb->prefix . 'np_newsletter';
}

/**
 * Create the subscriber table (on theme activation).
 */
function np_newsletter_install() {
	global $wpdb;
	$table   = np_newsletter_table();
	$charset = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS {$table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		email VARCHAR(190) NOT NULL,
		ip VARCHAR(100) DEFAULT '',
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) {$charset};";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}
add_action( 'after_switch_theme', 'np_newsletter_install' );

/**
 * AJAX subscribe endpoint.
 */
function np_ajax_newsletter_subscribe() {
	check_ajax_referer( 'np_circuit_ajax', 'nonce' );

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

	// Honeypot — bots fill the invisible field; pretend success.
	if ( ! empty( $_POST['np_nl_hp'] ) ) {
		wp_send_json_success( array( 'message' => __( 'Subscribed! Watch your inbox for deals.', 'netplus-circuit' ) ) );
	}
	// Timing trap.
	$elapsed = isset( $_POST['np_form_time'] ) ? np_bot_time_seconds( sanitize_text_field( wp_unslash( $_POST['np_form_time'] ) ) ) : PHP_INT_MAX;
	if ( PHP_INT_MAX === $elapsed || $elapsed < 3 ) {
		wp_send_json_success( array( 'message' => __( 'Subscribed! Watch your inbox for deals.', 'netplus-circuit' ) ) );
	}
	// Rate limit per IP.
	$rl_key = 'np_nl_' . md5( np_client_ip() );
	$hits   = (int) get_transient( $rl_key );
	if ( $hits >= 5 ) {
		wp_send_json_error( array( 'message' => __( 'Too many attempts. Please try again later.', 'netplus-circuit' ) ) );
	}
	set_transient( $rl_key, $hits + 1, HOUR_IN_SECONDS );

	if ( ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => __( 'Please enter a valid email address.', 'netplus-circuit' ) ) );
	}

	global $wpdb;
	$table  = np_newsletter_table();
	$exists = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE email = %s", $email ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	if ( $exists ) {
		wp_send_json_success( array( 'message' => __( 'You are already on the list. Thanks!', 'netplus-circuit' ) ) );
	}

	$inserted = $wpdb->insert(
		$table,
		array(
			'email'      => $email,
			'ip'         => np_client_ip(),
			'created_at' => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s' )
	);

	if ( $inserted ) {
		wp_send_json_success( array( 'message' => __( 'Subscribed! Watch your inbox for deals.', 'netplus-circuit' ) ) );
	}
	wp_send_json_error( array( 'message' => __( 'Something went wrong. Please try again.', 'netplus-circuit' ) ) );
}
add_action( 'wp_ajax_np_newsletter_subscribe', 'np_ajax_newsletter_subscribe' );
add_action( 'wp_ajax_nopriv_np_newsletter_subscribe', 'np_ajax_newsletter_subscribe' );

/**
 * Admin menu: Settings -> NetPlus Subscribers.
 */
function np_newsletter_admin_menu() {
	add_options_page(
		__( 'NetPlus Subscribers', 'netplus-circuit' ),
		__( 'NetPlus Subscribers', 'netplus-circuit' ),
		'manage_woocommerce',
		'np-subscribers',
		'np_render_subscribers_page'
	);
}
add_action( 'admin_menu', 'np_newsletter_admin_menu' );

/**
 * Render subscriber admin page (list + CSV export + delete).
 */
function np_render_subscribers_page() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( esc_html__( 'You do not have permission to view this page.', 'netplus-circuit' ) );
	}

	global $wpdb;
	$table = np_newsletter_table();

	// CSV export.
	if ( isset( $_GET['np_export'] ) && check_admin_referer( 'np_export_subs' ) ) {
		$rows = $wpdb->get_results( "SELECT email, created_at FROM {$table} ORDER BY id DESC" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=np-subscribers-' . gmdate( 'Y-m-d' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'Email', 'Subscribed' ) );
		foreach ( (array) $rows as $row ) {
			fputcsv( $out, array( $row->email, $row->created_at ) );
		}
		fclose( $out );
		exit;
	}

	// Delete one.
	if ( isset( $_GET['np_delete'] ) && check_admin_referer( 'np_delete_sub_' . absint( $_GET['np_delete'] ) ) ) {
		$wpdb->delete( $table, array( 'id' => absint( $_GET['np_delete'] ) ), array( '%d' ) );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Subscriber removed.', 'netplus-circuit' ) . '</p></div>';
	}

	$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$paged = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
	$per   = 25;
	$rows  = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} ORDER BY id DESC LIMIT %d OFFSET %d", $per, ( $paged - 1 ) * $per ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

	$export_url = wp_nonce_url( admin_url( 'options-general.php?page=np-subscribers&np_export=1' ), 'np_export_subs' );
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Newsletter Subscribers', 'netplus-circuit' ); ?></h1>
		<a href="<?php echo esc_url( $export_url ); ?>" class="page-title-action"><?php esc_html_e( 'Export CSV', 'netplus-circuit' ); ?></a>
		<p><?php echo esc_html( sprintf( /* translators: %d: total subscribers */ __( 'Total: %d subscribers', 'netplus-circuit' ), $total ) ); ?></p>

		<table class="widefat striped" style="max-width:860px;">
			<thead>
				<tr>
					<th>#</th>
					<th><?php esc_html_e( 'Email', 'netplus-circuit' ); ?></th>
					<th><?php esc_html_e( 'Subscribed', 'netplus-circuit' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( empty( $rows ) ) : ?>
				<tr><td colspan="4"><?php esc_html_e( 'No subscribers yet.', 'netplus-circuit' ); ?></td></tr>
			<?php else : ?>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td><?php echo esc_html( $row->id ); ?></td>
						<td><?php echo esc_html( $row->email ); ?></td>
						<td><?php echo esc_html( $row->created_at ); ?></td>
						<td>
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'options-general.php?page=np-subscribers&np_delete=' . (int) $row->id ), 'np_delete_sub_' . (int) $row->id ) ); ?>"
								onclick="return confirm('<?php echo esc_js( __( 'Delete this subscriber?', 'netplus-circuit' ) ); ?>');"
								style="color:#b32d2e;"><?php esc_html_e( 'Delete', 'netplus-circuit' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
			</tbody>
		</table>

		<?php
		$pages = (int) ceil( $total / $per );
		if ( $pages > 1 ) {
			echo '<p class="tablenav-pages">';
			for ( $i = 1; $i <= $pages; $i++ ) {
				$url = admin_url( 'options-general.php?page=np-subscribers&paged=' . $i );
				echo $i === $paged
					? '<span class="tablenav-paged" style="margin-right:8px;font-weight:700;">' . esc_html( $i ) . '</span>'
					: '<a class="page-numbers" style="margin-right:8px;" href="' . esc_url( $url ) . '">' . esc_html( $i ) . '</a>';
			}
			echo '</p>';
		}
		?>
	</div>
	<?php
}
