<?php


class VSWC_Settings_Page {
	private $option_name = 'woosuite_variation_swatches_option';
	/**
	 * @var VSWC_Upgrader
	 */
	private $upgrader_obj;

	public function __construct() {
		if ( class_exists( 'VSWC_Upgrader' ) ) {
			$this->upgrader_obj = new VSWC_Upgrader();
		}

		add_action( 'admin_menu', array( $this, 'handle_save_actions' ), 5 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_ajax_tawcvs_save_settings', array( $this, 'tawcvs_save_settings' ) );
	}

	public function admin_scripts() {
		if ( TA_WC_Variation_Swatches::is_in_plugin_settings_page() ) {

			do_action( 'woosuite_core_admin_page_scripts' );
		}
	}

	public function admin_menu() {
		if ( TA_WC_Variation_Swatches::is_woo_core_active() ) {
			add_submenu_page(
				'woosuite-core',
				__( 'Variation Swatches', 'wcvs' ),
				__( 'Variation Swatches', 'wcvs' ),
				'manage_options',
				'variation-swatches-settings',
				array( $this, 'render' )
			);
		} else {
			add_menu_page(
				__( 'Variation Swatches', 'wcvs' ),
				__( 'Variation Swatches', 'wcvs' ),
				'manage_options',
				'variation-swatches-settings',
				array( $this, 'render' ),
				'dashicons-ellipsis',
				12 );

			add_submenu_page(
				'variation-swatches-settings',
				__( 'Settings', 'wcvs' ),
				__( 'Settings', 'wcvs' ),
				'manage_options',
				'variation-swatches-settings',
				array( $this, 'render' )
			);
			add_submenu_page(
				'variation-swatches-settings',
				__( 'Woosuite Addons', 'wcvs' ),
				__( 'Addons', 'wcvs' ),
				'manage_options',
				'variation-swatches-addons',
				array( $this, 'render_addons' )
			);
		}

	}

	public function render() {
		TA_WC_Variation_Swatches::get_template( 'admin/setting-panel.php' );
		TA_WC_Variation_Swatches::get_template( 'admin/pro-feature-popup.php' );
		if ( $this->upgrader_obj instanceof VSWC_Upgrader && $this->upgrader_obj->is_welcome_popup_should_be_shown() ) {
			TA_WC_Variation_Swatches::get_template( 'admin/welcome-popup.php' );
		}
	}

	public function render_addons() {
		TA_WC_Variation_Swatches::get_template( 'admin/addons-pages.php' );
	}

	public function tawcvs_save_settings() {
		unset( $_POST['action'] );
		if ( $this->save_post_data_to_db() ) {
			wp_send_json_success( [ 'msg' => 'saved' ], 200 );
		}
	}

	/**
	 * Save form in case the core plugin is activated
	 *
	 * @return void
	 */
	public function handle_save_actions() {
		if ( isset( $_POST['woosuite_saving_variation_settings'] ) ) {
			unset( $_POST['woosuite_saving_variation_settings'] );
			if ( $this->save_post_data_to_db() ) {
				$this->syncing_up_color_image_swatches();
				$_POST['woosuite_saved_variation_settings'] = true;
			}
		}
	}

	/**
	 * Helper function to save _POST data to db
	 */
	private function save_post_data_to_db() {
		if ( wp_verify_nonce( $_POST['__nonce'], 'tawcvs_admin_settings') && current_user_can( 'manage_woocommerce' ) ) {
			unset( $_POST['__nonce'] );
			update_option( $this->option_name, $this->sanitize_post_data( $_POST ) );
			return TRUE;
		}
	}

	/**
	 * Helper function to update the corresponding Color/Image swatches settings
	 */
	private function syncing_up_color_image_swatches() {
		$latest_settings  = get_option( $this->option_name, array() );
		$general_settings = $latest_settings['general'];

		$all_attrs = wc_get_attribute_taxonomies();

		$this->update_attribute_by_type(
			$general_settings,
			$all_attrs,
			'enable-color-swatches',
			'color-swatches-attribute-',
			'color'
		);
		$this->update_attribute_by_type(
			$general_settings,
			$all_attrs,
			'enable-image-swatches',
			'image-swatches-attribute-',
			'image'
		);

		if ( TA_WC_Variation_Swatches::is_pro_addon_active() ) {
			$this->update_attribute_by_type(
				$general_settings,
				$all_attrs,
				'enable-radio-swatches',
				'radio-swatches-attribute-',
				'radio'
			);
		}

	}

	/**
	 *
	 * @param $general_settings
	 * @param $all_attrs
	 * @param $setting_key
	 * @param $attribute_key_prefix
	 * @param $attribute_type_name
	 */
	private function update_attribute_by_type( $general_settings, &$all_attrs, $setting_key, $attribute_key_prefix, $attribute_type_name ) {

		if ( $general_settings[ $setting_key ] === '1' ) {
			foreach ( $all_attrs as $index => $attr ) {
				if ( $general_settings[ $attribute_key_prefix . $attr->attribute_name ] === '1' ) {
					$this->update_product_attribute( $attribute_type_name, $attr->attribute_name );

					//We don't want to check it in other type
					unset( $all_attrs[ $index ] );
				} else {
					$this->update_product_attribute( 'select', $attr->attribute_name );
				}
			}
		} else {
			$this->update_product_attributes( $attribute_type_name, 'select' );
		}
	}

	/**
	 * Update single product attribute type by the attribute name
	 *
	 * @param $attr_type
	 * @param $attr_name
	 *
	 * @return bool|int
	 */
	private function update_product_attribute( $attr_type, $attr_name ) {
		global $wpdb;

		return $wpdb->update(
			$wpdb->prefix . 'woocommerce_attribute_taxonomies',
			array( 'attribute_type' => $attr_type ),
			array( 'attribute_name' => $attr_name ),
			array( '%s' ),
			array( '%s' )
		);
	}

	/**
	 * Update all attributes type to new type
	 *
	 * @param $attr_old_type
	 * @param $attr_new_type
	 *
	 * @return bool|int
	 */
	private function update_product_attributes( $attr_old_type, $attr_new_type ) {
		global $wpdb;

		return $wpdb->update(
			$wpdb->prefix . 'woocommerce_attribute_taxonomies',
			array( 'attribute_type' => $attr_new_type ),
			array( 'attribute_type' => $attr_old_type ),
			array( '%s' ),
			array( '%s' )
		);
	}

	private function sanitize_post_data( $post_data ) {
		foreach ( $post_data as $section_id => $items ) {
			foreach ( $items as $field_name => $field_value ) {
				if ( is_array( $field_value ) ) {
					$post_data[ $section_id ] = $this->sanitize_post_data( $items );
				} else {
					$post_data[ $section_id ][ $field_name ] = sanitize_text_field( $field_value );
				}
			}
		}

		return $post_data;
	}
}


?>