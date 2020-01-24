<?php

/*
  Plugin Name: BuddyPress Activity Bump
  Plugin URI: http://wbcomdesigns.com
  Author:     Wbcom Designs
  Description: Bumps an activity record to the top of the stream on activity comment replies and like
  Author URI: http://wbcomdesigns.com
  Version: 1.0.1
 */

/**
 * Function to update activity template on comment posted
 *
 * @global type $bp
 * @global type $wpdb
 * @param type $comment_id
 * @param type $params
 * @return boolean
 */
if ( ! function_exists( 'wb_bp_activity_comment_posted' ) ) {

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
		if ( ! $activity->save() ) {
			return false;
		}

		if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) {
			include locate_template( array( 'activity/entry.php' ), false );
		}
	}

	add_action( 'bp_activity_comment_posted', 'wb_bp_activity_comment_posted', 10, 2 );
}

/**
 * Function to update activity action on comment posted and favorite button click
 *
 * @global type $bp
 * @param string $content
 * @param type $activity
 * @return string
 */
if ( ! function_exists( 'wb_bp_activity_bump_time_since' ) ) {

	function wb_bp_activity_bump_time_since( $content, $activity ) {
		global $bp;
		if ( ! $date = bp_activity_get_meta( $activity->id, 'activity_bump_date' ) ) {
			return $content;
		}

		$content = '<span class="time-since">' . sprintf( __( ' updated %s', 'bp-activity-bump' ), bp_core_time_since( $activity->date_recorded ) ) . '</span>';
		return '<span class="time-since time-created">' . sprintf( __( ' %s', 'buddypress' ), bp_core_time_since( $date ) ) . '</span> &middot; ';
	}

	add_filter( 'bp_activity_time_since', 'wb_bp_activity_bump_time_since', 10, 2 );
}

/**
 * Function to update activity stream on activity like
 *
 * @global type $bp
 * @global type $wpdb
 * @param type $activity_id
 * @param type $user_id
 * @return boolean
 */
if ( ! function_exists( 'wb_add_like_notification' ) ) {

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
		if ( ! $activity->save() ) {
			return false;
		}

		if ( bp_has_activities( bp_ajax_querystring( 'activity' ) ) ) {
			include locate_template( array( 'activity/entry.php' ), false );
		}
	}

	add_action( 'bp_activity_add_user_favorite', 'wb_add_like_notification', 9, 2 );
}

/**
 *  Check if buddypress activate.
 */
if( !function_exists( 'bp_activity_bump_requires_buddypress' ) ){
    add_action( 'admin_init', 'bp_activity_bump_requires_buddypress' );
    function bp_activity_bump_requires_buddypress()
    {

        if ( !class_exists( 'Buddypress' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            //deactivate_plugins('buddypress-polls/buddypress-polls.php');
            add_action( 'admin_notices', 'bp_activity_bump_required_plugin_admin_notice' );
            unset($_GET['activate']);
        }
    }

}

/**
 * Throw an Alert to tell the Admin why it didn't activate.
 *
 * @author wbcomdesigns
 * @since  1.2.0
 */
if( !function_exists( 'bp_activity_bump_required_plugin_admin_notice' ) ){
    function bp_activity_bump_required_plugin_admin_notice()
    {

        $bpquotes_plugin          = esc_html__('BuddyPress Activity Bump', 'bp-activity-bump');
        $bp_plugin                = esc_html__('BuddyPress', 'bp-activity-bump');
        echo '<div class="error"><p>';
        echo sprintf(esc_html__('%1$s is ineffective now as it requires %2$s to be installed and active.', 'bp-activity-bump'), '<strong>' . esc_html($bpquotes_plugin) . '</strong>', '<strong>' . esc_html($bp_plugin) . '</strong>');
        echo '</p></div>';
        if (isset($_GET['activate']) ) {
            unset($_GET['activate']);
        }
    }

}

