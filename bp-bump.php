<?php
/**
 * Plugin Name: BuddyPress Activity Bump
 * Plugin URI: https://wbcomdesigns.com/
 * Description: Bumps an activity record to the top of the stream on activity comment replies and like
 * Author:     Wbcom Designs
 * Author URI: https://wbcomdesigns.com/
 * Version: 1.2.5
 * Requires PHP: 8.1
 * Text Domain:   bp-activity-bump
 *
 * @link              https://wbcomdesigns.com/
 * @since             1.0.0
 * @package           bp-activity-bump
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'BP_ACTIVITY_BUMP_VERSION' ) ) {
	define( 'BP_ACTIVITY_BUMP_VERSION', '1.2.5' );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_DIR' ) ) {
	define( 'BP_ACTIVITY_BUMP_DIR', trailingslashit( __DIR__ ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_PATH' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_URL' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_BASENAME' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'BP_ACTIVITY_BUMP_PLUGIN_FILE' ) ) {
	define( 'BP_ACTIVITY_BUMP_PLUGIN_FILE', __FILE__ );
}

/**
 * Including file for wbcom admin setting.
 */
require plugin_dir_path( __FILE__ ) . 'admin/wbcom/wbcom-admin-settings.php';

/**
 * Including file for admin setting.
 */
require_once plugin_dir_path( __FILE__ ) . 'admin/bp-bump-admin.php';

/**
 * Which actions bump an activity: 'favorite-activity', 'commented-activity' or 'both-activity'.
 *
 * @since 1.2.5
 * @return string
 */
function wb_bp_activity_bump_mode() {
	$options = get_option( 'bp_bump_admin_general_options' );
	$mode    = is_array( $options ) && isset( $options['bp_bump_activity_option'] ) ? $options['bp_bump_activity_option'] : '';
	return in_array( $mode, array( 'favorite-activity', 'commented-activity', 'both-activity' ), true ) ? $mode : 'both-activity';
}

/**
 * Register the bump hooks once BuddyPress components are known, only when the Activity component is on.
 *
 * @since 1.2.5
 */
function wb_bp_activity_bump_register_hooks() {
	if ( ! function_exists( 'bp_is_active' ) || ! bp_is_active( 'activity' ) ) {
		return;
	}

	$mode = wb_bp_activity_bump_mode();
	if ( 'commented-activity' === $mode || 'both-activity' === $mode ) {
		add_action( 'bp_activity_comment_posted', 'wb_bp_activity_comment_posted', 10, 3 );
	}
	if ( 'favorite-activity' === $mode || 'both-activity' === $mode ) {
		add_action( 'bp_activity_add_user_favorite', 'wb_add_like_notification', 9 );
		// BuddyBoss reactions replace favorites on BuddyBoss sites.
		add_action( 'bb_reaction_after_add_user_item_reaction', 'wb_bp_activity_bump_on_reaction', 10, 2 );
	}

	add_filter( 'bp_activity_time_since', 'wb_bp_activity_bump_time_since', 10, 2 );
	add_filter( 'bp_rest_activity_prepare_value', 'bp_activity_bump_data_embed_rest_api', 10, 3 );
}
add_action( 'bp_init', 'wb_bp_activity_bump_register_hooks' );

/**
 * Move a top-level activity to the top of the stream.
 *
 * The first bump stores the original date in the `activity_bump_date` meta so it can still be shown.
 *
 * @since 1.2.5
 * @param int $activity_id Activity ID.
 * @return bool True when the activity was bumped.
 */
function wb_bp_activity_bump( $activity_id ) {
	global $wpdb;

	$activity_id = (int) $activity_id;
	if ( $activity_id < 1 ) {
		return false;
	}

	$activity = new BP_Activity_Activity( $activity_id );

	// Comments keep their own date: only top-level items move in the stream.
	if ( empty( $activity->id ) || 'activity_comment' === $activity->type ) {
		return false;
	}

	if ( ! bp_activity_get_meta( $activity_id, 'activity_bump_date' ) ) {
		bp_activity_update_meta( $activity_id, 'activity_bump_date', $activity->date_recorded );
	}

	$table   = buddypress()->activity->table_name;
	$updated = $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET date_recorded = %s WHERE id = %d", gmdate( 'Y-m-d H:i:s' ), $activity_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery

	if ( ! $updated ) {
		return false;
	}

	// Raw SQL skips BuddyPress' own save hooks: drop the cached row and the cached stream queries.
	wp_cache_delete( $activity_id, 'bp_activity' );
	wp_cache_delete( 'bp_activity_sitewide_front', 'bp' );
	bp_activity_reset_cache_incrementor();

	return true;
}

/**
 * Bump the activity a comment or reply was posted on.
 *
 * @since 1.0.0
 * @param int                       $comment_id Comment ID.
 * @param array                     $params     Comment arguments.
 * @param BP_Activity_Activity|null $activity   Activity commented on.
 */
function wb_bp_activity_comment_posted( $comment_id, $params, $activity = null ) {
	$activity_id = is_object( $activity ) && ! empty( $activity->id ) ? $activity->id : ( $params['activity_id'] ?? 0 );
	wb_bp_activity_bump( $activity_id );
}

/**
 * Bump an activity when a member likes (favorites) it.
 *
 * @since 1.0.0
 * @param int $activity_id Activity ID.
 */
function wb_add_like_notification( $activity_id ) {
	wb_bp_activity_bump( $activity_id );
}

/**
 * Bump an activity when a member reacts to it on BuddyBoss.
 *
 * @since 1.2.5
 * @param int   $user_reaction_id Reaction ID.
 * @param array $args             Reaction arguments.
 */
function wb_bp_activity_bump_on_reaction( $user_reaction_id, $args ) {
	if ( isset( $args['item_type'], $args['item_id'] ) && 'activity' === $args['item_type'] ) {
		wb_bp_activity_bump( $args['item_id'] );
	}
}

/**
 * Show the original post date on a bumped activity instead of the bump time.
 *
 * @since 1.0.0
 * @param string               $content  Time since markup.
 * @param BP_Activity_Activity $activity Activity.
 * @return string
 */
function wb_bp_activity_bump_time_since( $content, $activity ) {
	$date = '' === $content ? '' : bp_activity_get_meta( $activity->id, 'activity_bump_date' );
	if ( ! $date ) {
		return $content;
	}

	// Same markup as core (bp_insert_activity_meta), so BuddyPress's live "x ago" refresh keeps the original time.
	return sprintf(
		'<span class="time-since time-created" data-livestamp="%1$s">%2$s</span> ',
		esc_attr( bp_core_get_iso8601_date( $date ) ),
		esc_html( bp_core_time_since( $date ) )
	);
}

/**
 * Redirect to plugin settings page after activated.
 */
if ( class_exists( 'BuddyPress' ) ) {
	add_action( 'activated_plugin', 'bp_bump_activation_redirect_settings' );
}
/**
 * Bp_bump_activation_redirect_settings
 *
 * @param  mixed $plugin plugin.
 * @return void
 */
function bp_bump_activation_redirect_settings( $plugin ) {

	if ( plugin_basename( __FILE__ ) === $plugin ) {
		if ( isset( $_REQUEST['action'] ) && 'activate' === $_REQUEST['action'] && isset( $_REQUEST['plugin'] ) && $_REQUEST['plugin'] === $plugin ) { //phpcs:ignore
			wp_safe_redirect( admin_url( 'admin.php?page=bp-activity-bump-settings&tab=bp-bump-genral' ) );
			exit;
		}
	}
}

add_action( 'admin_notices', 'bp_bump_plugin_admin_notice' );
/**
 * Tell admins that this plugin needs BuddyPress. The plugin stays active so settings survive a BuddyPress update.
 *
 * @since 1.0.0
 */
function bp_bump_plugin_admin_notice() {
	if ( class_exists( 'BuddyPress' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	echo '<div class="error"><p>'
	/* translators: 1: plugin name, 2: BuddyPress. */
	. sprintf( esc_html__( '%1$s is ineffective as it requires %2$s to be installed and active.', 'bp-activity-bump' ), '<strong>BuddyPress Activity Bump</strong>', '<strong>BuddyPress</strong>' )
	. '</p></div>';
}

/**
 * Embed bp activity bump data in rest api activity endpoint.
 *
 * @param  WP_REST_Response $response get response data.
 * @param  WP_REST_Request  $request get request data.
 * @param  object           $activity get activity data.
 * @return WP_REST_Response
 */
function bp_activity_bump_data_embed_rest_api( $response, $request, $activity ) {
	$response->data['bp_activity_bump'] = array(
		'activity_bump_date' => bp_activity_get_meta( $activity->id, 'activity_bump_date', true ),
		'favorite_count'     => bp_activity_get_meta( $activity->id, 'favorite_count', true ),
	);
	return $response;
}
