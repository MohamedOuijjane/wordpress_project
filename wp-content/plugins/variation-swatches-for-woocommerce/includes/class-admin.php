<?php

/**
 * Class TA_WC_Variation_Swatches_Admin
 */
class TA_WC_Variation_Swatches_Admin {
	/**
	 * The single instance of the class
	 *
	 * @var TA_WC_Variation_Swatches_Admin
	 */
	protected static $instance = null;

	private $option_name = 'woosuite_variation_swatches_option';


	/**
	 * Main instance
	 *
	 * @return TA_WC_Variation_Swatches_Admin
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'includes' ) );
		add_action( 'admin_init', array( $this, 'init_attribute_hooks' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), PHP_INT_MAX );

		add_action( 'woocommerce_product_data_tabs', array( $this, 'add_custom_swatch_variation_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'product_tab_variation_swatches_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_custom_product_data' ) );

		// Restore attributes
		add_action( 'admin_notices', array( $this, 'restore_attributes_notice' ) );
		add_action( 'admin_init', array( $this, 'restore_attribute_types' ) );

		// Display attribute fields
		add_action( 'tawcvs_product_attribute_field', array( $this, 'attribute_fields' ), 10, 4 );

		add_action( 'wp_ajax_update_product_attr_type', array( $this, 'update_product_attr_type' ) );
		add_action( 'wp_ajax_update_attribute_type_setting', array( $this, 'update_attribute_type_setting' ) );


		add_action( 'woocommerce_attribute_added', array( $this, 'update_plugin_setting_on_added' ), 10, 2 );
		add_action( 'woocommerce_attribute_updated', array( $this, 'update_plugin_setting_on_updated' ), 10, 3 );

		add_filter( 'woosuite_core_module_settings_url', array(
			$this,
			'render_the_setting_url_in_core_plugin'
		), 10, 2 );

		include_once( dirname( __FILE__ ) . '/class-menu-page.php' );
		new VSWC_Settings_Page();
	}

	/**
	 * @param $id
	 * @param $data
	 */
	public function update_plugin_setting_on_added( $id, $data ) {

		$this->sync_attribute_setting_to_plugin_settings( $data );
	}

	/**
	 * @param $id
	 * @param $data
	 * @param $old_slug
	 */
	public function update_plugin_setting_on_updated( $id, $data, $old_slug ) {

		$latest_option = $this->get_latest_plugin_option();

		//Remove the old slug from our plugin setting
		if ( $data['attribute_name'] !== $old_slug && isset( $latest_option['general'] ) ) {
			unset( $latest_option['general'][ 'color-swatches-attribute-' . $old_slug ] );
			unset( $latest_option['general'][ 'image-swatches-attribute-' . $old_slug ] );
		}

		$this->sync_attribute_setting_to_plugin_settings( $data, $latest_option );
	}

	/**
	 * Function to sync the attribute type setting with the plugin setting
	 *
	 * @param $data
	 * @param array|bool $latest_option
	 */
	private function sync_attribute_setting_to_plugin_settings( $data, $latest_option = false ) {
		if ( ! $latest_option ) {
			$latest_option = $this->get_latest_plugin_option();
		}

		$generalSettings = isset( $latest_option['general'] ) ? $latest_option['general'] : array();

		//Set new value
		switch ( $data['attribute_type'] ) {
			case 'image':
				$generalSettings[ 'image-swatches-attribute-' . $data['attribute_name'] ] = '1';
				$generalSettings[ 'color-swatches-attribute-' . $data['attribute_name'] ] = '0';
				$generalSettings[ 'radio-swatches-attribute-' . $data['attribute_name'] ] = '0';
				$generalSettings['enable-image-swatches']                                 = '1';
				break;
			case 'color':
				$generalSettings[ 'image-swatches-attribute-' . $data['attribute_name'] ] = '0';
				$generalSettings[ 'color-swatches-attribute-' . $data['attribute_name'] ] = '1';
				$generalSettings[ 'radio-swatches-attribute-' . $data['attribute_name'] ] = '0';
				$generalSettings['enable-color-swatches']                                 = '1';
				break;
			case 'radio':
				$generalSettings[ 'image-swatches-attribute-' . $data['attribute_name'] ] = '0';
				$generalSettings[ 'color-swatches-attribute-' . $data['attribute_name'] ] = '0';
				$generalSettings[ 'radio-swatches-attribute-' . $data['attribute_name'] ] = '1';
				$generalSettings['enable-radio-swatches']                                 = '1';
				break;
		}

		$latest_option['general'] = $generalSettings;

		update_option( $this->option_name, $latest_option );

		$this->remove_wc_attributes_cache();
	}

	/**
	 * Add the custom product tab for Variation swatches
	 *
	 * @param $product_data_tabs
	 *
	 * @return mixed
	 */
	public function add_custom_swatch_variation_tab( $product_data_tabs ) {
		$product_data_tabs['variation-swatches'] = array(
			'label'    => __( 'Variation Swatches', 'wcvs' ),
			'target'   => 'variation_swatches_options',
			'class'    => array( 'show_if_variable' ),
			'priority' => 61,
		);

		return $product_data_tabs;
	}

	/**
	 * Rendering the product data panel for Swatch Variation settings
	 */
	public function product_tab_variation_swatches_panel() { ?>
        <div id="variation_swatches_options" class="panel woocommerce_options_panel">
            <div class="options_group">
				<?php
				woocommerce_wp_checkbox( array(
					'id'            => 'variation_swatches_disabled',
					'wrapper_class' => 'show_if_variable',
					'label'         => __( 'Disable Swatches', 'wcvs' ),
					'default'       => '0',
					'desc_tip'      => false,
					'description'   => __( 'Show the default dropdown selection instead of swatches settings', 'wcvs' )
				) );

				if ( ! TA_WC_Variation_Swatches::is_pro_addon_active() ) {
					woocommerce_wp_select(
						array(
							'wrapper_class'     => 'wcvs-pro-feature',
							'id'                => 'single_variation_preview',
							'label'             => __( 'Single variation preview attributes', 'wcvs' ),
							'options'           => array(
								'' => __( ' - Choose Attribute - ', 'wcvs' )
							),
							'value'             => '',
							'custom_attributes' => array(
								'disabled' => 'disabled'
							)
						)
					);
					woocommerce_wp_select(
						array(
							'wrapper_class'     => 'wcvs-pro-feature',
							'id'                => 'override_single_attribute',
							'label'             => __( 'Single attribute for using in catalog pages', 'wcvs' ),
							'options'           => array(
								'' => __( ' - Choose Attribute - ', 'wcvs' )
							),
							'value'             => '',
							'custom_attributes' => array(
								'disabled' => 'disabled'
							)
						)
					);
					TA_WC_Variation_Swatches::get_template( 'admin/pro-feature-popup.php' );
				}

				do_action( 'variation_swatches_product_panel' );
				?>
            </div>
        </div>
		<?php
	}

	/**
	 * Saving custom field of product data tab
	 *
	 * @param $post_id
	 */
	public function save_custom_product_data( $post_id ) {
		$variation_swatches_disabled = isset( $_POST['variation_swatches_disabled'] ) ? esc_attr( $_POST['variation_swatches_disabled'] ) : 'no';
		update_post_meta( $post_id, 'variation_swatches_disabled', $variation_swatches_disabled );
	}

	/**
	 * Rendering the setting url for this plugin in the dashboard page of Woosuite Core
	 *
	 * @param $url
	 * @param $module
	 *
	 * @return mixed|string|void
	 */
	function render_the_setting_url_in_core_plugin( $url, $module ) {
		if ( $module === WCVS_PLUGIN_NAME ) {
			$url = admin_url( 'admin.php?page=variation-swatches-settings' );
		}

		return $url;
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( dirname( __FILE__ ) . '/class-admin-product.php' );
		include_once( dirname( __FILE__ ) . '/class-menu-page.php' );
		include_once( dirname( __FILE__ ) . '/class-setting-fields-manager.php' );
		include_once( dirname( __FILE__ ) . '/class-setting-fields-renderer.php' );
		new VSWC_Setting_Fields_Renderer();
	}

	/**
	 * Init hooks for adding fields to attribute screen
	 * Save new term meta
	 * Add thumbnail column for attribute term
	 */
	public function init_attribute_hooks() {
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( empty( $attribute_taxonomies ) ) {
			return;
		}

		foreach ( $attribute_taxonomies as $tax ) {
			add_action( 'pa_' . $tax->attribute_name . '_add_form_fields', array( $this, 'add_attribute_fields' ) );
			add_action( 'pa_' . $tax->attribute_name . '_edit_form_fields', array(
				$this,
				'edit_attribute_fields'
			), 10, 2 );

			add_filter( 'manage_edit-pa_' . $tax->attribute_name . '_columns', array(
				$this,
				'add_attribute_columns'
			) );
			add_filter( 'manage_pa_' . $tax->attribute_name . '_custom_column', array(
				$this,
				'add_attribute_column_content'
			), 10, 3 );
		}

		add_action( 'created_term', array( $this, 'save_term_meta' ), 10, 2 );
		add_action( 'edit_term', array( $this, 'save_term_meta' ), 10, 2 );
	}

	/**
	 * Load stylesheet and scripts in edit product attribute screen
	 */
	public function enqueue_scripts() {
		global $wp_styles;
		$screen   = get_current_screen();
		$dir_name = dirname( __FILE__ );

		if ( strpos( $screen->id, 'variation-swatches-addons' ) !== false ) {
			wp_enqueue_style( 'tawcvs-admin-addons', plugins_url( '/assets/css/admin-addons-page.css', $dir_name ), array() );
		}

		// Don't enqueue unless we are in the edit product attribute or plugin setting page
		if ( strpos( $screen->id, 'edit-pa_' ) === false &&
		     strpos( $screen->id, 'product' ) === false &&
		     TA_WC_Variation_Swatches::is_in_plugin_settings_page() === false ) {
			return;
		}

		if ( TA_WC_Variation_Swatches::is_in_plugin_settings_page() ) {
			wp_dequeue_style( 'woocommerce_admin_styles' );
			wp_deregister_script( 'woocommerce_admin_styles' );
			unset( $wp_styles->registered['woocommerce_admin_styles'] );
		}

		wp_enqueue_media();

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style( 'tawcvs-admin', plugins_url( '/assets/css/admin.css', $dir_name ), array( 'wp-color-picker' ), WCVS_PLUGIN_VERSION );
		wp_enqueue_script( 'tawcvs-admin', plugins_url( '/assets/js/admin.js', $dir_name ), array(
			'jquery',
			'wp-color-picker',
			'wp-util'
		), WCVS_PLUGIN_VERSION, true );

		wp_localize_script(
			'tawcvs-admin',
			'tawcvs',
			array(
				'i18n'        => array(
					'mediaTitle'  => esc_html__( 'Choose an image', 'wcvs' ),
					'mediaButton' => esc_html__( 'Use image', 'wcvs' ),
				),
				'placeholder' => WC()->plugin_url() . '/assets/images/placeholder.png',
				'ajaxUrl' => admin_url( 'admin-ajax.php' )
			)
		);

	}

	/**
	 * Display a notice of restoring attribute types
	 */
	public function restore_attributes_notice() {
		if ( get_transient( 'tawcvs_attribute_taxonomies' ) && ! get_option( 'tawcvs_restore_attributes_time' ) ) {
			?>
            <div class="notice-warning notice is-dismissible">
                <p>
					<?php
					esc_html_e( 'Found a backup of product attributes types. This backup was generated at', 'wcvs' );
					echo ' ' . date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), get_option( 'tawcvs_backup_attributes_time' ) ) . '.';
					?>
                </p>
                <p>
                    <a href="<?php echo esc_url( add_query_arg( array(
						'tawcvs_action' => 'restore_attributes_types',
						'tawcvs_nonce'  => wp_create_nonce( 'restore_attributes_types' )
					) ) ); ?>">
                        <strong><?php esc_html_e( 'Restore product attributes types', 'wcvs' ); ?></strong>
                    </a>
                    |
                    <a href="<?php echo esc_url( add_query_arg( array(
						'tawcvs_action' => 'dismiss_restore_notice',
						'tawcvs_nonce'  => wp_create_nonce( 'dismiss_restore_notice' )
					) ) ); ?>">
                        <strong><?php esc_html_e( 'Dismiss this notice', 'wcvs' ); ?></strong>
                    </a>
                </p>
            </div>
			<?php
		} elseif ( isset( $_GET['tawcvs_message'] ) && 'restored' == $_GET['tawcvs_message'] ) {
			?>
            <div class="notice-warning settings-error notice is-dismissible">
                <p><?php esc_html_e( 'All attributes types have been restored.', 'wcvs' ) ?></p>
            </div>
			<?php
		}
	}

	/**
	 * Restore attribute types
	 */
	public function restore_attribute_types() {
		if ( ! isset( $_GET['tawcvs_action'] ) || ! isset( $_GET['tawcvs_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_GET['tawcvs_nonce'], $_GET['tawcvs_action'] ) ) {
			return;
		}

		if ( 'restore_attributes_types' == $_GET['tawcvs_action'] ) {
			global $wpdb;

			$attribute_taxnomies = get_transient( 'tawcvs_attribute_taxonomies' );

			foreach ( $attribute_taxnomies as $id => $attribute ) {
				$wpdb->update(
					$wpdb->prefix . 'woocommerce_attribute_taxonomies',
					array( 'attribute_type' => $attribute->attribute_type ),
					array( 'attribute_id' => $id ),
					array( '%s' ),
					array( '%d' )
				);
			}

			update_option( 'tawcvs_restore_attributes_time', time() );
			delete_transient( 'tawcvs_attribute_taxonomies' );
			delete_transient( 'wc_attribute_taxonomies' );

			$url = remove_query_arg( array( 'tawcvs_action', 'tawcvs_nonce' ) );
			$url = add_query_arg( array( 'tawcvs_message' => 'restored' ), $url );
		} elseif ( 'dismiss_restore_notice' == $_GET['tawcvs_action'] ) {
			update_option( 'tawcvs_restore_attributes_time', 'ignored' );
			$url = remove_query_arg( array( 'tawcvs_action', 'tawcvs_nonce' ) );
		}

		if ( isset( $url ) ) {
			wp_redirect( $url );
			exit;
		}
	}

	/**
	 * Create hook to add fields to add attribute term screen
	 *
	 * @param string $taxonomy
	 */
	public function add_attribute_fields( $taxonomy ) {
		$attr = TA_WCVS()->get_tax_attribute( $taxonomy );
		do_action( 'tawcvs_product_attribute_field', $attr->attribute_type, false, $taxonomy, 'add' );
	}

	/**
	 * Create hook to fields to edit attribute term screen
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */
	public function edit_attribute_fields( $term, $taxonomy ) {
		$attr = TA_WCVS()->get_tax_attribute( $taxonomy );
		do_action( 'tawcvs_product_attribute_field', $attr->attribute_type, $term, $taxonomy, 'edit' );
	}

	/**
	 * Print HTML of custom fields on attribute term screens
	 *
	 * @param $type
	 * @param $term
	 * @param $taxonomy
	 * @param $form
	 */
	public function attribute_fields( $type, $term, $taxonomy, $form ) {
		// Return if this is a default attribute type
		if ( in_array( $type, array( 'select', 'text' ) ) ) {
			return;
		}
		if ( $term instanceof WP_Term ) {
			$term_id = $term->term_id;
		} else {
			$term_id = false;
		}
		$value = get_term_meta( $term_id, $type, true );
		$html  = '';

		ob_start();
		switch ( $type ) {
			case 'image':
				$image = $value ? wp_get_attachment_image_src( $value ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				?>
                <div class="tawcvs-term-image-thumbnail" style="float:left;margin-right:10px;">
                    <img src="<?php echo esc_url( $image ) ?>" width="60px" height="60px"/>
                </div>
                <div style="line-height:60px;">
                    <input type="hidden" class="tawcvs-term-image" name="image" value="<?php echo esc_attr( $value ) ?>"/>
                    <button type="button" class="tawcvs-upload-image-button button"><?php esc_html_e( 'Upload/Add image', 'wcvs' ); ?></button>
                    <button type="button" class="tawcvs-remove-image-button button <?php echo $value ? '' : 'hidden' ?>"><?php esc_html_e( 'Remove image', 'wcvs' ); ?></button>
                </div>
				<?php
				break;
			case 'radio':
				?>
				<input type="hidden" name="radio" />
				<?php
				break;
			default:
				?>
                <input type="text" id="term-<?php echo esc_attr( $type ) ?>" name="<?php echo esc_attr( $type ) ?>" value="<?php echo esc_attr( $value ) ?>"/>
				<?php
				break;
		}
		$html .= ob_get_clean();
		$html = apply_filters( 'tawcvs_tooltip_attributes', $html, $type, $term, $taxonomy, $form );

		if ( $type != 'radio' ) {
			echo sprintf(
				'<%s class="form-field">%s<label for="term-%s">%s</label>%s',
				'edit' == $form ? 'tr' : 'div',
				'edit' == $form ? '<th>' : '',
				esc_attr( $type ),
				TA_WCVS()->types[ $type ],
				'edit' == $form ? '</th><td>' : ''
			);

			echo $html;

			// Print the close tag of field container
			echo 'edit' == $form ? '</td></tr>' : '</div>';
		} else {
			echo $html;
		}
	}

	/**
	 * Save term meta
	 *
	 * @param int $term_id
	 * @param int $tt_id
	 */
	public function save_term_meta( $term_id, $tt_id ) {
		foreach ( TA_WCVS()->types as $type => $label ) {
			if ( isset( $_POST[ $type ] ) ) {
				update_term_meta( $term_id, $type, sanitize_text_field( $_POST[ $type ] ) );
				do_action( 'tawcvs_after_save_term_meta', $term_id, $type );
			}
		}
	}

	/**
	 * Add thumbnail column to column list
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function add_attribute_columns( array $columns ) {
		$new_columns          = array();
		if ( isset( $columns['cb']) ) {
			$new_columns['cb']    = $columns['cb'];
			$new_columns['thumb'] = '';
			unset( $columns['cb'] );
		}

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Render thumbnail HTML depend on attribute type
	 *
	 * @param $columns
	 * @param $column
	 * @param $term_id
	 *
	 * @return mixed|void
	 */
	public function add_attribute_column_content( $columns, $column, $term_id ) {
		if ( 'thumb' !== $column ) {
			return $columns;
		}

		$attr  = TA_WCVS()->get_tax_attribute( $_REQUEST['taxonomy'] );
		$value = get_term_meta( $term_id, $attr->attribute_type, true );

		switch ( $attr->attribute_type ) {
			case 'color':
				$formatted_color_style = TA_WC_Variation_Swatches::generate_color_style( $term_id, $value );
				printf( '<div class="swatch-preview swatch-color" style="background:%s;"></div>', esc_attr( $formatted_color_style ) );
				break;

			case 'image':
				$image = $value ? wp_get_attachment_image_src( $value ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				printf( '<img class="swatch-preview swatch-image" src="%s" width="44px" height="44px">', esc_url( $image ) );
				break;

			case 'label':
				printf( '<div class="swatch-preview swatch-label">%s</div>', esc_html( $value ) );
				break;
		}
	}

	/**
	 * The Ajax callback to update the plugin Attribute type setting.
     * It also can be called from the Ajax callback to save attribute type
	 */
	public function update_attribute_type_setting() {
		if ( ! wp_verify_nonce( $_POST['__nonce'], 'tawcvs_admin_settings') || ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => 'Failed to update', 'success' => false ), 200 );
		}

        //Get the latest plugin option
		$latest_option = $this->get_latest_plugin_option();

        //Check if we need to return an json response or not
		$send_response = (bool) ( isset( $_POST['sendResponse'] ) ? sanitize_text_field( $_POST['sendResponse'] ) : 0 );

		//Update the plugin settings also
		$main_setting_arr = array_slice( $_POST, 0, 1, true );

        //Update the plugin setting and return the response if needed
		if ( update_option( $this->option_name, array_replace_recursive( $latest_option, $main_setting_arr ) ) && $send_response ) {
			wp_send_json_success( array( 'message' => 'Updated plugin settings', 'success' => true ), 200 );
		} elseif ( $send_response ) {
			wp_send_json_error( array( 'message' => 'Failed to update', 'success' => false ), 200 );
		}
	}

	/**
	 * Ajax callback to update the Product Attribute type
	 */
	public function update_product_attr_type() {
		if ( ! wp_verify_nonce( $_POST['__nonce'], 'tawcvs_admin_settings') || ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => 'Failed to update', 'success' => false ), 200 );
		}

		global $wpdb;

		$attribute_name = isset( $_POST['attribute'] ) ? sanitize_text_field( $_POST['attribute'] ) : '';
		$type_to_update = isset( $_POST['typeToUpdate'] ) ? sanitize_text_field( $_POST['typeToUpdate'] ) : '';

		if ( empty( $attribute_name ) || empty( $type_to_update ) ) {
			wp_send_json_error( array( 'message' => 'Missing data', 'success' => false ), 200 );
		}

		//Setting the Product attribute to specified type
		$result = $wpdb->update(
			$wpdb->prefix . 'woocommerce_attribute_taxonomies',
			array( 'attribute_type' => $type_to_update ),
			array( 'attribute_name' => $attribute_name ),
			array( '%s' ),
			array( '%s' )
		);

		$this->update_attribute_type_setting();

		$this->remove_wc_attributes_cache();

		//Save the setting
		if ( false !== $result ) {
			wp_send_json_success( array( 'message' => 'Done with update', 'success' => true ), 200 );
		}

		wp_send_json_error( array( 'message' => 'Unexpected error', 'success' => false ), 200 );

	}

	/**
	 * Reset the WC cache to get the latest value
	 */
	private function remove_wc_attributes_cache() {
		delete_transient( 'wc_attribute_taxonomies' );
	}

	/**
	 * Get the latest plugin option settings
	 */
	private function get_latest_plugin_option() {
		$option = get_option( $this->option_name, array() );

		return is_array( $option ) ? $option : array();
	}

}
