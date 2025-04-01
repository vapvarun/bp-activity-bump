<?php
/**
 *
 * This file is used for rendering and saving plugin welcome settings.
 *
 * @package bp-activity-bump
 * @subpackage bp-activity-bump\admin\tab-templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}
?>
<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-welcome-head">
			<p class="wbcom-welcome-description"><?php esc_html_e( 'BuddyPress Activity Bump moves an activity to the top of the newsfeed when a user likes or comments on it. BuddyPress displays activities in chronological order, with the most recent activity appearing first. When a member comments on an existing activity or marks it as a favorite, it is bumped to the top of the feed. ', 'bp-activity-bump' ); ?></p>
		</div><!-- .wbcom-welcome-head -->

		<div class="wbcom-welcome-content">
			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help &amp; Support Resources', 'bp-activity-bump' ); ?></h3>
				<p><?php esc_html_e( 'If you need assistance, here are some helpful resources. Our documentation is a great place to start, and our support team is available if you require further help.', 'bp-activity-bump' ); ?></p>
				<div class="wbcom-support-info-wrap">
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'bp-activity-bump' ); ?></h3>
						<p><?php esc_html_e( 'We’ve created a comprehensive guide on BuddyPress Activity Bump to help you understand all aspects of the plugin. Most of your questions can be answered here.', 'bp-activity-bump' ); ?></p>
						<a href="<?php echo esc_url( 'https://docs.wbcomdesigns.com/doc_category/buddypress-activity-bump/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'bp-activity-bump' ); ?></a>
						</div>
					</div>

					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'bp-activity-bump' ); ?></h3>
						<p><?php esc_html_e( 'We are dedicated to providing top-notch customer support. Once your plugin is activated, please feel free to reach out at any time for assistance.', 'bp-activity-bump' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'bp-activity-bump' ); ?></a>
					</div>
					</div>
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Share Your Feedback?', 'bp-activity-bump' ); ?></h3>
						<p><?php esc_html_e( 'We’d love to hear about your experience with the plugin. Your feedback and suggestions help us improve future updates.', 'bp-activity-bump' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/submit-review/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'bp-activity-bump' ); ?></a>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div><!-- .wbcom-welcome-main-wrapper -->
</div><!-- .wbcom-tab-content -->
