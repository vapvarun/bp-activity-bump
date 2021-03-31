<?php
/**
 * Plugin Name: BuddyPress Activity Bump
 * Plugin URI: http://wbcomdesigns.com
 * Author:     Wbcom Designs
 * Description: Bumps an activity record to the top of the stream on activity comment replies and like
 * Author URI: http://wbcomdesigns.com
 * Version: 1.1.0
 *
 * @package bp-activity-bump
 */

if ( ! function_exists( 'wb_bp_activity_comment_posted' ) ) {
	/**
	 * Function to update activity template on comment posted
	 *
	 * @global type $bp
	 * @global type $wpdb
	 * @param type $comment_id Get a comment id.
	 * @param type $params Parameter Array.
	 * @return boolean
	 */
	function wb_bp_activity_comment_posted( $comment_id, $params ) {
		global $bp, $wpdb;
		extract( $params, EXTR_SKIP );
		$activity_parent = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );

		if ( ! $activity_parent = $activity_parent['activities'][0] ) {
			return;
		}

		if ( ! bp_activity_get_meta( $activity_id, 'activity_bump_date' ) ) {
			bp_activity_update_meta( $activity_id, 'activity_bump_date', $activity_parent->date_recorded );
		}

		$activity                = new BP_Activity_Activity( $activity_id );
		$activity->date_recorded = gmdate( 'Y-m-d H:i:s' );
		$update                  = $wpdb->get_results( $wpdb->prepare( "UPDATE  {$wpdb->prefix}bp_activity SET date_recorded = '" . gmdate( 'Y-m-d H:i:s' ) . "'  WHERE  id=%s", $activity_id ) );
		if ( ! $update ) {
			return false;
		}

		if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) {
			include locate_template( array( 'activity/entry.php' ), false );
		}
	}

	add_action( 'bp_activity_comment_posted', 'wb_bp_activity_comment_posted', 10, 2 );
}

if ( ! function_exists( 'wb_bp_activity_bump_time_since' ) ) {

	/**
	 * Function to update activity action on comment posted and favorite button click
	 *
	 * @global type $bp
	 * @param string $content Return the content.
	 * @param type   $activity BP Profile Activity.
	 * @return string
	 */
	function wb_bp_activity_bump_time_since( $content, $activity ) {
		global $bp;
		$date = bp_activity_get_meta( $activity->id, 'activity_bump_date' );
		if ( ! $date ) {
			return $content;
		}
		/* Translators: %s: Activity date */
		$content = '<span class="time-since">' . sprintf( __( ' updated %s', 'bp-activity-bump' ), bp_core_time_since( $activity->date_recorded ) ) . '</span>';
		/* Translators: %s: Activity date */
		return '<span class="time-since time-created">' . sprintf( __( '%s', 'bp-activity-bump' ), bp_core_time_since( $date ) ) . '</span> &middot; ';
	}

	add_filter( 'bp_activity_time_since', 'wb_bp_activity_bump_time_since', 10, 2 );
}


if ( ! function_exists( 'wb_add_like_notification' ) ) {
	/**
	 * Function to update activity stream on activity like
	 *
	 * @global type $bp
	 * @global type $wpdb
	 * @param type $activity_id BP Activity id.
	 * @param type $user_id BP User id.
	 * @return boolean
	 */
	function wb_add_like_notification( $activity_id, $user_id ) {
		global $bp, $wpdb;

		$activity_parent = bp_activity_get_specific( array( 'activity_ids' => $activity_id ) );

		if ( ! $activity_parent = $activity_parent['activities'][0] ) {
			return;
		}

		if ( ! bp_activity_get_meta( $activity_id, 'activity_bump_date' ) ) {
			bp_activity_update_meta( $activity_id, 'activity_bump_date', $activity_parent->date_recorded );
		}

		$activity                = new BP_Activity_Activity( $activity_id );
		$activity->date_recorded = gmdate( 'Y-m-d H:i:s' );
		$update                  = $wpdb->get_results( $wpdb->prepare( "UPDATE  {$wpdb->prefix}bp_activity SET date_recorded = '" . gmdate( 'Y-m-d H:i:s' ) . "'  WHERE  id=%s", $activity_id ) );
		if ( ! $update ) {
			return false;
		}

		if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) {
			include locate_template( array( 'activity/entry.php' ), false );
		}
	}

	add_action( 'bp_activity_add_user_favorite', 'wb_add_like_notification', 9, 2 );
}
