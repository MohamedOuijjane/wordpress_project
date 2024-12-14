<?php

/**
 * The main plugin class
 */
final class TA_WC_Variation_Swatches {
	/**
	 * The single instance of the class
	 *
	 * @var TA_WC_Variation_Swatches
	 */
	protected static $instance = null;

	/**
	 * Extra attribute types
	 *
	 * @var array
	 */
	public $types = array();

	/**
	 * Main instance
	 *
	 * @return TA_WC_Variation_Swatches
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
		$this->types = array(
			'color' => esc_html__( 'Color', 'wcvs' ),
			'image' => esc_html__( 'Image', 'wcvs' ),
			'label' => esc_html__( 'Label', 'wcvs' ),
		);

		if ( TA_WC_Variation_Swatches::is_pro_addon_active() ) {
			$this->types['radio'] = esc_html__( 'Radio button', 'wcvs' );
		}

		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		$current_dir = dirname( __FILE__ );
		require_once $current_dir . '/class-upgrader.php';
		require_once $current_dir . '/class-frontend.php';

		if ( is_admin() ) {
			require_once $current_dir . '/class-admin.php';
			if ( ! self::is_woo_core_active() ) {
				require_once $current_dir . '/class-addon-page.php';
			}
		}
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		add_filter( 'product_attributes_type_selector', array( $this, 'add_attribute_types' ) );

		if ( is_admin() ) {
			add_action( 'init', array( 'TA_WC_Variation_Swatches_Admin', 'instance' ) );
		}

		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			add_action( 'init', array( 'TA_WC_Variation_Swatches_Frontend', 'instance' ) );
		}
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wcvs', false, dirname( plugin_basename( TAWC_VS_PLUGIN_FILE ) ) . '/languages/' );
	}

	/**
	 * Add extra attribute types
	 * Add color, image and label type
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function add_attribute_types( array $types ) {
		return array_merge( $types, $this->types );
	}

	/**
	 * Get attribute's properties
	 *
	 * @param string $taxonomy
	 *
	 * @return object
	 */
	public function get_tax_attribute( $taxonomy ) {
		global $wpdb;

		if ( strpos( $taxonomy, 'pa_' ) === 0 ) {
			$attr = substr( $taxonomy, 3 );
		} else {
			$attr = $taxonomy;
		}

		$result = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s",
				$attr
			)
		);

		return apply_filters( 'tawcvs_tax_attributes', $result );
	}

	/**
	 * Instance of admin
	 *
	 * @return object
	 */
	public function admin() {
		return TA_WC_Variation_Swatches_Admin::instance();
	}

	/**
	 * Instance of frontend
	 *
	 * @return object
	 */
	public function frontend() {
		return TA_WC_Variation_Swatches_Frontend::instance();
	}

	/**
	 * Function to generate the formatted style for the dual color feature
	 *
	 * @param int $term_id
	 * @param string $main_color
	 *
	 * @return string
	 */
	public static function generate_color_style( $term_id, $main_color ) {

		$color_style = apply_filters( 'tawcvs_color_style', $main_color, $term_id, $main_color );

		return esc_attr( $color_style );
	}

	/**
	 * Include a specified template file
	 *
	 * @param $file_path
	 */
	public static function get_template( $file_path ) {
		$template = WCVS_PLUGIN_DIR . 'templates/' . $file_path;
		if ( file_exists( $template ) ) {
			include $template;
		}
	}

	public static function get_product_attributes_as_checkbox(
		$section_id,
		$tab_id,
		$field_name,
		$show_configure_link = false,
		$type_to_update = ''
	) {
		ob_start();
		$current_options = get_option( 'woosuite_variation_swatches_option' ) ?: array();
		if ( ! empty( $tab_id ) ) {
			$field_name_prefix = $section_id . '[' . $tab_id . ']';
		} else {
			$field_name_prefix = $section_id;
		}
		foreach ( wc_get_attribute_taxonomies() as $att ) {
			if ( ! empty( $tab_id ) ) {
				$field_value = isset( $current_options[ $section_id ][ $tab_id ][ $field_name . '-' . $att->attribute_name ] ) ? $current_options[ $section_id ][ $tab_id ][ $field_name . '-' . $att->attribute_name ] : '';
			} else {
				$field_value = isset( $current_options[ $section_id ][ $field_name . '-' . $att->attribute_name ] ) ? $current_options[ $section_id ][ $field_name . '-' . $att->attribute_name ] : '';
			}
			$field_id = $field_name . '-' . $att->attribute_name;
			$field_name_modified = $field_name_prefix . '[' . $field_name . '-' . $att->attribute_name . ']';

			$tax_slug = esc_attr( wc_attribute_taxonomy_name( $att->attribute_name ) );
			?>
            <label class="variation-checkbox-container" for="<?php echo $field_id; ?>">
				<?php echo $att->attribute_label; ?>
                <input type="hidden" name="<?php echo $field_name_modified; ?>" value="0">
                <input id="<?php echo $field_id; ?>"
                       type="checkbox"
                       name="<?php echo $field_name_modified; ?>"
                       value="1"
                       data-slug="<?php echo $att->attribute_name; ?>"
					<?php echo $type_to_update ? 'data-type="' . esc_attr( $type_to_update ) . '"' : '';; ?>
					<?php checked( '1', $field_value ); ?>/>
                <span class="checkmark"></span>
				<?php if ( $show_configure_link ): ?>
					<?php $configure_link = 'edit-tags.php?taxonomy=' . $tax_slug . '&amp;post_type=product'; ?>
                    <a class="configure-items-link <?php echo 1 == $field_value ? '' : 'hidden'; ?>"
                       href="<?php echo $configure_link; ?>">
						<?php _e( 'Configure items', 'wcvs' ); ?>
                    </a>
				<?php endif; ?>
            </label>
			<?php
		}

		return ob_get_clean();
	}

	public static function get_detailed_product_variations( $product, $attribute_tax_name ) {
		if ( ! $product instanceof WC_Product_Variable ) {
			return array();
		}
		$collected_variations = array();
		$variations = self::get_available_variations( $product );

		if ( ! empty( $variations ) ) {
			foreach ( $variations as $variation ) {
				if ( ! isset( $variation['attributes'][ 'attribute_' . $attribute_tax_name ] ) ) {
					continue;
				}
				$attribute_item_obj_slug = $variation['attributes'][ 'attribute_' . $attribute_tax_name ];

				if ( ! isset( $collected_variations[ $attribute_item_obj_slug ] ) ) {
					$collected_variations[ $attribute_item_obj_slug ] = $variation;
				}
			}
		}

		return $collected_variations;
	}

	/**
	 * Get an array of available variations for the current product.
	 *
	 * @param $product
	 * @param $skip_out_of_stock
	 * @param $get_full_attribute_data
	 *
	 * @return array[]|WC_Product_Variation[]
	 */
	public static function get_available_variations( $product, $skip_out_of_stock = false, $get_full_attribute_data = false ) {
		if ( ! $product instanceof WC_Product_Variable ) {
			return array();
		}
		$variation_ids = $product->get_children();
		if ( empty( $variation_ids ) ) {
			return array();
		}
		$available_variations = array();

		if ( is_callable( '_prime_post_caches' ) ) {
			_prime_post_caches( $variation_ids );
		}

		foreach ( $variation_ids as $variation_id ) {

			$variation = wc_get_product( $variation_id );

			// Hide out of stock variations if 'Hide out of stock items from the catalog' is checked.
			if ( ! $variation || ! $variation->exists() || ( $skip_out_of_stock && ! $variation->is_in_stock() ) ) {
				continue;
			}

			// Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price).
			if ( apply_filters( 'woocommerce_hide_invisible_variations', true, $product->get_id(),
					$variation ) && ! $variation->variation_is_visible() ) {
				continue;
			}
			if ( $get_full_attribute_data ) {
				$available_variations[] = array_merge(
					$product->get_available_variation( $variation_id ),
					array(
						'woosuite_purchasable' => $variation->is_purchasable() && $variation->is_in_stock()
					)
				);
			} else {
				$available_variations[] = array(
					'attributes'           => $variation->get_variation_attributes(),
					'variation_id'         => $variation_id,
					'woosuite_purchasable' => $variation->is_purchasable() && $variation->is_in_stock()
				);
			}
		}

		return array_values( array_filter( $available_variations ) );
	}

	/**
	 * Detect if we have the Woosuite Core plugin activated
	 *
	 * @return bool
	 */
	public static function is_woo_core_active() {
		return class_exists( 'Woosuite_Core' );
	}

	/**
	 * Detect if we have the Woosuite Core plugin activated
	 *
	 * @return bool
	 */
	public static function is_pro_addon_active() {
		return class_exists( 'Woosuite_Variation_Swatches_Pro' );
	}

	public static function is_in_plugin_settings_page() {
		return is_admin() && isset( $_GET['page'] ) && $_GET['page'] === 'variation-swatches-settings';
	}
}

if ( ! function_exists( 'TA_WCVS' ) ) {
	/**
	 * Main instance of plugin
	 *
	 * @return TA_WC_Variation_Swatches
	 */
	function TA_WCVS() {
		return TA_WC_Variation_Swatches::instance();
	}
}