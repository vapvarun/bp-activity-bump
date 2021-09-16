<?php
/**
 * BP activity bump admin function class file.
 *
 * @package bp-activity-bump
 * @subpackage bp-activity-bump\admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Add admin page for importing Review(s).
if ( ! class_exists( 'BP_ACTIVITY_BUMP_ADMIN_SETIING' ) ) {
	/**
	 * The admin-facing functionality of the plugin.
	 *
	 * @package bp-activity-bump
	 * @subpackage bp-activity-bump\admin
	 * @author     wbcomdesigns <admin@wbcomdesigns.com>
	 */
	class BP_ACTIVITY_BUMP_ADMIN_SETIING {

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'bpwoo_add_submenu_page_admin_settings' ) );
			add_action( 'admin_init', array( $this, 'bpwoo_plugin_settings' ) );
		}
		/**
		 * Actions performed on loading admin_menu.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bpwoo_add_submenu_page_admin_settings() {
			if ( class_exists( 'BuddyPress' ) ) {
				if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {
					add_menu_page( esc_html__( 'WB Plugins', 'bp-activity-bump' ), esc_html__( 'WB Plugins', 'bp-activity-bump' ), 'manage_options', 'wbcomplugins', array( $this, 'bupr_admin_options_page' ), 'dashicons-lightbulb', 59 );
					add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'bp-activity-bump' ), esc_html__( 'General', 'bp-activity-bump' ), 'manage_options', 'wbcomplugins' );

				}
				add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Activity Bump', 'bp-activity-bump' ), esc_html__( 'BP Activity Bump', 'bp-activity-bump' ), 'manage_options', 'bp-activity-bump-settings', array( $this, 'bupr_admin_options_page' ) );
			}
		}

		/**
		 * Actions performed on loading plugin settings
		 *
		 * @since    1.0.9
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bpwoo_plugin_settings() {
			$this->plugin_settings_tabs['bpwoo-welcome'] = esc_html__( 'Welcome', 'bp-activity-bump' );
			register_setting( 'bpwoo_admin_welcome_options', 'bpwoo_admin_welcome_options' );
			add_settings_section( 'bpwoo-welcome', ' ', array( $this, 'bpwoo_admin_welcome_content' ), 'bpwoo-welcome' );

			$this->plugin_settings_tabs['bpwoo-genral'] = esc_html__( 'General', 'bp-activity-bump' );
			register_setting( 'bp_bump_admin_general_options', 'bp_bump_admin_general_options' );
			add_settings_section( 'bpwoo-genral', ' ', array( $this, 'bpwoo_admin_general_content' ), 'bpwoo-genral' );
			}

		/**
		 * Include buddypress activity bump admin welcome setting tab content file.
		 */
		public function bpwoo_admin_welcome_content() {
			include 'tab-templates/bp-bump-welcome-page.php';
		}

		/**
		 * Include buddypress activity bump admin genral setting tab content file.
		 */
		public function bpwoo_admin_general_content() {
			include 'tab-templates/bp-bump-setting-general-tab.php';
		}

		

		/**
		 * Actions performed to create a submenu page content.
		 *
		 * @since    1.0.0
		 * @access public
		 */
		public function bupr_admin_options_page() {
			global $allowedposttags;
			$tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bpwoo-welcome';
			?>
			<div class="wrap">
							<hr class="wp-header-end">
							<div class="wbcom-wrap">
				<div class="bupr-header">
					<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					<h1 class="wbcom-plugin-heading">
						<?php esc_html_e( 'Buddypress Activity Bump Setting', 'bp-activity-bump' ); ?>
					</h1>
				</div>
				<div class="wbcom-admin-settings-page">
					<?php
					settings_errors();
					$this->bupr_plugin_settings_tabs();
					settings_fields( $tab );
					do_settings_sections( $tab );
					?>
				</div>
							</div>
			</div>
			<?php
		}

		/**
		 * Actions performed to create tabs on the sub menu page.
		 */
		public function bupr_plugin_settings_tabs() {
			$current_tab = filter_input( INPUT_GET, 'tab' ) ? filter_input( INPUT_GET, 'tab' ) : 'bpwoo-welcome';
			// xprofile setup tab.
			echo '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
				$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
				echo '<li><a class="nav-tab ' . esc_attr( $active ) . '" id="' . esc_attr( $tab_key ) . '-tab" href="?page=bp-activity-bump-settings' . '&tab=' . esc_attr( $tab_key ) . '">' . esc_attr( $tab_caption ) . '</a></li>';
			}
			echo '</div></ul></div>';
		}
	}
	new BP_ACTIVITY_BUMP_ADMIN_SETIING();
}
