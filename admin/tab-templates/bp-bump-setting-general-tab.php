<?php
/**
 * Buddypress activity bump general tab content.
 *
 * @package bp-activity-bump
 * @subpackage bp-activity-bump\admin\tab-templates
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/* admin setting on dashboard */
$bp_bump_genral_setting = get_option( 'bp_bump_admin_general_options' );
?>
<div class="wbcom-tab-content">
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bp_bump_admin_general_options' );
		do_settings_sections( 'bp_bump_admin_general_options' );
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="bpwoo-shop-tab">
							<?php esc_html_e( 'Bump on Like/Favorite', 'bp-activity-bump' ); ?>
						</label>
					</th>
					<td>
						<label class="bpwoo-switch">
							<input name="bp_bump_admin_general_options[bp_bump_activity_option]" type="radio" id="bp-bump-display-liked-activity"  value="favorite-activity"<?php ( isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ) ? checked( $bp_bump_genral_setting['bp_bump_activity_option'], 'favorite-activity' ) : ''; ?>>
							<div class="bpwoo-slider bupr-round"></div>
						</label>
						<p class="description"><?php esc_html_e( 'Enable this option, if you want to move activity up after new likes.', 'bp-activity-bump' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="bpwoo-order-tab">
							<?php esc_html_e( 'Bump on new comments', 'bp-activity-bump' ); ?>
						</label>
					</th>
					<td>
						<label class="bpwoo-switch">
							<input name="bp_bump_admin_general_options[bp_bump_activity_option]" type="radio" id="bp-bump-display-commented-activity"  value="commented-activity"<?php ( isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ) ? checked( $bp_bump_genral_setting['bp_bump_activity_option'], 'commented-activity' ) : ''; ?>>
							<div class="bpwoo-slider bupr-round"></div>
						</label>
						<p class="description"><?php esc_html_e( 'Enable this option, if you want to move activity up after new comment', 'bp-activity-bump' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="bpwoo-order-tracking-tab">
							<?php esc_html_e( 'Bump on both', 'bp-activity-bump' ); ?>
						</label>
					</th>
					<td>
						<label class="bpwoo-switch">
							<input name="bp_bump_admin_general_options[bp_bump_activity_option]" type="radio" id="bp-bump-display-both-activities"  value="both-activity"<?php ( isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ) ? checked( $bp_bump_genral_setting['bp_bump_activity_option'], 'both-activity' ) : ''; ?>>
							<div class="bpwoo-slider bupr-round"></div>
						</label>
						<p class="description"><?php esc_html_e( 'Enable this option, if you want to move activity up after new like or comment', 'bp-activity-bump' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
