<?php
if ( ! class_exists( 'VSWC_Upgrader' ) ) {
	class VSWC_Upgrader {
		/**
		 * The single instance of the class
		 *
		 * @var VSWC_Upgrader
		 */
		protected static $instance = null;

		private $previous_version_need_to_compare = '1.0.11';
		private $saved_plugin_version;

		/**
		 * Main instance
		 *
		 * @return VSWC_Upgrader
		 */
		public static function instance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function __construct() {
			add_action( 'admin_notices', array( $this, 'show_warning_message_after_upgrading' ) );
			add_action( 'admin_init', array( $this, 'set_current_plugin_version' ) );
		}

		function set_current_plugin_version() {
			if ( ! get_option( 'wcvs_current_version', false ) ) {
				update_option( 'wcvs_current_version', WCVS_PLUGIN_VERSION, true );
			}
		}

		/**
		 * Showing the warning message after upgrading plugin
		 */
		public function show_warning_message_after_upgrading() {

			if ( $this->is_admin_notice_should_be_shown()
			     && ! TA_WC_Variation_Swatches::is_in_plugin_settings_page() ) {
				$url = admin_url( 'admin.php?page=variation-swatches-settings' );
				?>
                <div class="notice-warning notice is-dismissible">
                    <p><?php printf( __( 'You need to update new settings for the Variation Swatches plugin <a href="%s">here</a>.', 'wcvs' ), $url ); ?></p>
                </div>
				<?php
			}
		}

		/**
		 * Check if the welcome popup should be shown in the settings page
		 *
		 * @return bool
		 */
		public function is_welcome_popup_should_be_shown() {
			$this->saved_plugin_version = $this->get_current_plugin_version();

			if ( ! $this->is_welcome_page_is_already_show() ) {
				$this->mark_welcome_page_shown_for_new_version();

				return $this->is_notice_allowed_to_show();
			}

			return false;
		}

		/**
		 * Check if the admin notice should be show
		 *
		 * @return bool
		 */
		public function is_admin_notice_should_be_shown() {
			$this->saved_plugin_version = $this->get_current_plugin_version();

			return $this->is_notice_allowed_to_show() && ! $this->is_met_admin_notice_requirements();
		}

		/**
		 * @return bool
		 */
		private function is_notice_allowed_to_show() {
			if ( WCVS_NEED_TO_SHOW_NOTICE_AFTER_UPGRADED ) {
				return ! empty( $this->saved_plugin_version ) && $this->is_current_version_newer_than_compared_version();
			}

			return false;
		}

		/**
		 * @return bool|int
		 */
		private function is_current_version_newer_than_compared_version() {
			return $this->compare_plugin_version( $this->saved_plugin_version, $this->previous_version_need_to_compare, '>' );
		}

		/**
		 * @return false|mixed|void
		 */
		private function get_current_plugin_version() {
			return get_option( 'wcvs_current_version', false );
		}

		/**
		 * Mark the welcome page is already shown to hide it next time
		 */
		private function mark_welcome_page_shown_for_new_version() {
			update_option( 'wcvs_welcome_page_shown_v_' . $this->get_slugify_string_of_current_version(), 1 );
		}

		/**
		 * @return false|mixed|void
		 */
		private function is_welcome_page_is_already_show() {
			return get_option( 'wcvs_welcome_page_shown_v_' . $this->get_slugify_string_of_current_version(), false );
		}

		/**
		 * Checking if the new settings has been update after upgrading
		 *
		 * @return bool
		 */
		private function is_met_admin_notice_requirements() {
			$new_settings_value = get_option( 'woosuite_variation_swatches_option', false );
			if ( empty( $new_settings_value ) || ! isset( $new_settings_value['general'] ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Change the version number to slug.
		 * For example: 2.0.1 -> 2_0_1
		 *
		 * @return array|string|string[]
		 */
		private function get_slugify_string_of_current_version() {
			return str_replace( array( '.', '-' ), '_', WCVS_PLUGIN_VERSION );
		}

		/**
		 *
		 * @param $old_version
		 * @param $new_version
		 * @param $operator
		 *
		 * @return bool|int
		 */
		private function compare_plugin_version( $old_version, $new_version, $operator ) {
			$p    = '#(\.0+)+($|-)#';
			$ver1 = preg_replace( $p, '', $old_version );
			$ver2 = preg_replace( $p, '', $new_version );

			return isset( $operator ) ?
				version_compare( $ver1, $ver2, $operator ) :
				version_compare( $ver1, $ver2 );
		}

	}
}