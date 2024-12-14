<?php
if ( ! class_exists( 'VSWC_Setting_Fields_Renderer' ) ) {
	class VSWC_Setting_Fields_Renderer {
		private $current_options;
		private $option_name = 'woosuite_variation_swatches_option';
		private $settings_sections;
		private $settings_tabs;
		private $settings_fields;
		private $field_html_placeholder = '%%WCVS_FIELD%%';


		public function __construct() {
			add_action( 'woosuite_variation_swatches_settings_fields_html', array( $this, 'render_settings_page' ) );

			if ( TA_WC_Variation_Swatches::is_in_plugin_settings_page() ) {
				//Hook for the template parts
				add_action( 'woosuite_child_plugin_header', array( $this, 'render_settings_page_header' ) );
				add_action( 'woosuite_child_plugin_sidebar', array( $this, 'render_settings_page_sidebar' ) );
				add_action( 'woosuite_child_plugin_footer', array( $this, 'render_settings_page_footer' ) );
				add_action( 'woosuite_child_plugin_video_tutorials', array(
					$this,
					'render_settings_page_video_tutorials'
				) );
			}


			$this->settings_sections = VSWC_Setting_Fields_Manager::get_settings_sections();
			$this->settings_tabs     = VSWC_Setting_Fields_Manager::get_settings_tabs();
			$this->settings_fields   = VSWC_Setting_Fields_Manager::get_settings_fields();
		}

		/**
		 * Main function to render the settings page
		 */
		public function render_settings_page() {
			//Get the latest values
			$this->current_options = get_option( $this->option_name ) ?: array();

			$loop_count = 0;

			foreach ( $this->settings_sections as $section ) {
				$index_class     = $loop_count === 0 ? 'first-item' : '';
				$section['icon'] = isset( $section['icon'] ) ? $section['icon'] : 'dashicons-admin-settings';
				?>
                <div class="variation-accordion-item <?php echo $index_class; ?>"
                     id="<?php echo 'wcvs_section_' . $section['id']; ?>">
                    <div class="variation-item-head var-gen-head <?php echo $loop_count === 0 ? 'active-accordion' : ''; ?>">
                        <h3 class="variation-accrodion-title">
                            <span class="dashicons <?php echo $section['icon']; ?>"></span>
							<?php echo $section['title']; ?>
                        </h3>
                        <span class="dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="variation-accordion-content">
                        <div class="variation-switcher-wrap">
                            <div class="woocommerce_options_panel">
								<?php
								$this->render_settings_fields( $section ); ?>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
				$loop_count ++;
			}
		}

		/**
		 * Rendering the fields belong to specified section
		 *
		 * @param $section
		 * @param array $custom_fields
		 */
		public function render_settings_fields( $section, array $custom_fields = array() ) {

			//Rendering the fields belong to the tab if existed
			if ( isset( $section['has_tab'] ) && $section['has_tab'] === true ) {
				$this->render_settings_tabs( $section['id'] );

				return;
			}

			//Using custom fields if it is set
			if ( empty( $custom_fields ) && isset( $this->settings_fields[ $section['id'] ] ) ) {
				$custom_fields = $this->settings_fields[ $section['id'] ];
			}

			foreach ( $custom_fields as $main_field_settings ) {
				$fields_html = '';

				//If pro add-on is activated, so don't render the pro features badge
				if ( isset( $main_field_settings['is_pro_feature'] ) && true === $main_field_settings['is_pro_feature'] && defined( 'WOOSUITE_VARIATION_SWATCHES_PRO_VERSION' ) ) {
					continue;
				}

				if ( isset( $main_field_settings['fields'] ) && ! empty( $main_field_settings['fields'] ) ) {
					foreach ( $main_field_settings['fields'] as $single_field ) {
						$item_field_setting = $this->get_parsed_field_setting( $section['id'], $main_field_settings, $single_field );
						$fields_html        .= call_user_func( $this->get_field_callback( $item_field_setting ), $item_field_setting );
					}
					echo str_replace( $this->field_html_placeholder, $fields_html, $this->render_field_wrapper( $main_field_settings ) );
				} else {
					$this->callback_text( $main_field_settings );
				}
			}

		}

		/**
		 * Get the callback function for each field element
		 *
		 * @param $field_settings
		 *
		 * @return array|mixed
		 */
		private function get_field_callback( $field_settings ) {
			if ( isset( $field_settings['callback'] ) && ! empty( $field_settings['callback'] ) ) {
				return $field_settings['callback'];
			} else {
				return array( $this, 'callback_' . $field_settings['type'] );
			}
		}

		/**
		 * Combine the general field settings with the settings of each element
		 *
		 * @param $section_id
		 * @param $main_settings
		 * @param $item_settings
		 *
		 * @return array|object
		 */
		private function get_parsed_field_setting( $section_id, $main_settings, $item_settings ) {
			$default_field_setting = array(
				'title'             => '',
				'section_id'        => $section_id,
				'tab_id'            => '',
				'id'                => '',
				'type'              => 'text',
				'name'              => '',
				'default_value'     => '',
				'class'             => '',
				'custom_item_class' => '',
				'field_to_check'    => '',
				'placeholder'       => '',
				'html_before'        => '',
				'html_after'        => '',
				'html'              => '',
				'min'               => 0,
				'max'               => 99999,
				'step'              => 1,
				'callback'          => '',
				'options_group'     => array(),
				'show_if_checked'   => false,
			);

			$parsed_field = array_merge( $main_settings, $item_settings );
			unset( $parsed_field['fields'] );

			return wp_parse_args( $parsed_field, $default_field_setting );

		}

		/**
		 * Rendering the tabs belong to the section
		 *
		 * @param $section_id
		 */
		public function render_settings_tabs( $section_id ) {
			if ( ! isset( $this->settings_tabs[ $section_id ] ) || empty( $this->settings_tabs[ $section_id ] ) ) {
				return;
			}
			?>
            <div class="wcvs-accor-tab-wrap">
                <div class="wcvs-accor-tab-btns">
					<?php
					$tab_loop = 0;
					foreach ( $this->settings_tabs[ $section_id ] as $tab_item ) {
						$tab_class = esc_attr( $section_id . '-' . $tab_item['id'] );
						?>
                        <button class="accor-tab-btn <?php echo ( $tab_loop === 0 ? 'active-at-btn ' : ' ' ) . $tab_class; ?>">
							<?php echo $tab_item['title']; ?>
                        </button>
						<?php
						$tab_loop ++;
					} ?>
                </div>
            </div>
            <div class="wcvs-accor-tab-content-wrap">
				<?php
				foreach ( $this->settings_tabs[ $section_id ] as $tab_item ) {
					$tab_class = esc_attr( $section_id . '-' . $tab_item['id'] );
					?>
                    <div class="wcvs-accor-tab-content <?php echo $tab_class; ?>">
						<?php
						if ( isset( $this->settings_fields[ $section_id ][ $tab_item['id'] ] ) ) {
							$fields = array_map( function ( $field ) use ( $section_id, $tab_item ) {
								$field['section_id'] = $section_id;
								$field['tab_id']     = $tab_item['id'];

								return $field;
							}, $this->settings_fields[ $section_id ][ $tab_item['id'] ] );
							$this->render_settings_fields( $tab_item, $fields );
						}
						?>
                    </div>
				<?php } ?>
            </div>
			<?php
		}

		/**
		 * Displays a text field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_text( $args ) {
			?>
            <div class="variation-switcher-item wcvs-text-only">
                <div class="variation-switcher-label">
					<?php
					echo '<h3 class="vs-label-title">' . esc_html( $args['title'] ) . '</h3>';
					echo $this->callback_html( $args ); ?>
                </div>
            </div>
			<?php
		}

		/**
		 * Displays the html for a settings field
		 *
		 * @param array $args settings field args
		 */
		public function callback_html( $args ) {
			ob_start();
			?>
            <div class="variation-html-wrapper">
				<?php echo $args['html']; ?>
            </div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Displays a radio for a settings field
		 *
		 * @param array $args settings field args
		 */
		public function callback_radio( array $args ) {
			$field_value = $this->get_field_value( $args );
			$field_name  = $this->get_field_name( $args );
			ob_start();
			?>
            <div class="variation-radio-wrapper">
				<?php foreach ( $args['options_group'] as $option ) { ?>
                    <label class="radio-<?php echo $option['value'] . ' ' . esc_attr( $args['custom_item_class'] ); ?>">
                        <input type="radio" name="<?php echo $field_name; ?>"
                               class="variation-radio-input"
                               value="<?php echo $option['value']; ?>"
							<?php checked( $option['value'], $field_value ); ?>>
                        <span class="variation-radio-text">
                            <?php echo $option['label']; ?>
                        </span>
                    </label>
				<?php } ?>
            </div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Displays a selectbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		public function callback_select( array $args ) {
			$field_value = $this->get_field_value( $args );
			$field_name  = $this->get_field_name( $args );
			ob_start();
			?>
            <div class="variation-switch-field-grid">
                <select class="<?php echo esc_attr( $args['custom_item_class'] ); ?>"
                        id="<?php echo esc_attr( $args['id'] ); ?>" name="<?php echo $field_name; ?>">
					<?php foreach ( $args['options_group'] as $option ) { ?>
                        <option class="selectbox-<?php echo $option['value']; ?>"
                                value="<?php echo $option['value']; ?>"
							<?php selected( $option['value'], $field_value ); ?>>
							<?php echo $option['label']; ?>
                        </option>
					<?php } ?>
                </select>
            </div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Displays a checkbox for a settings field
		 *
		 * @param array $args settings field args
		 */
		public function callback_checkbox( array $args ) {
			$field_value = $this->get_field_value( $args );
			$field_name  = $this->get_field_name( $args );
			ob_start();
			?>
            <label class="variation-switch">
                <input type="hidden" name="<?php echo $field_name; ?>" value="0">
                <input type="checkbox" name="<?php echo $field_name; ?>" id="<?php echo esc_attr( $args['id'] ); ?>"
                       class="wcvs-accordion-switch" value="1" <?php checked( '1' === $field_value ); ?>/>
                <span class="slider round"></span>
            </label>
			<?php
			return ob_get_clean();
		}

		/**
		 * Displays a color picker field for a settings field
		 *
		 * @param array $args settings field args
		 */
		function callback_color( $args ) {
			$field_value = $this->get_field_value( $args );
			$field_name  = $this->get_field_name( $args );
			ob_start();
			?>
            <input type="text"
                   name="<?php echo $field_name; ?>"
                   class="vs-color-picker"
                   id="<?php echo esc_attr( $args['id'] ); ?>"
                   value="<?php echo $field_value; ?>">
			<?php
			return ob_get_clean();
		}

		/**
		 * Display a number field
		 *
		 * @param $args
		 *
		 * @return false|string
		 */
		function callback_number( $args ) {
			$field_value = $this->get_field_value( $args );
			$field_name  = $this->get_field_name( $args );
			ob_start();
			echo empty( $args['html_after'] ) ? '' : '<div class="field-with-html-after">';
			echo empty( $args['html_before'] ) ? '' : '<div class="field-with-html-before">';

			echo empty( $args['html_before'] ) ? '' : $args['html_before'];
            ?>
            <input type="number"
                   name="<?php echo $field_name; ?>"
                   class="<?php echo esc_attr( $args['custom_item_class'] ); ?>"
                   id="<?php echo esc_attr( $args['id'] ); ?>"
                   min="<?php echo esc_attr( $args['min'] ); ?>"
                   max="<?php echo esc_attr( $args['max'] ); ?>"
                   step="<?php echo esc_attr( $args['step'] ); ?>"
                   placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
                   value="<?php echo $field_value; ?>">
			<?php
			echo empty( $args['html_before'] ) ? '' : '</div>';
			echo empty( $args['html_after'] ) ? '' : $args['html_after'] . '</div>';

			return ob_get_clean();
		}

		/**
		 * Display a textbox field
		 *
		 * @param $args
		 *
		 * @return false|string
		 */
		function callback_textbox( $args ) {
			$field_value = $this->get_field_value( $args );
			$field_name  = $this->get_field_name( $args );
			ob_start();
			echo empty( $args['html_after'] ) ? '' : '<div class="field-with-html-after">';
			echo empty( $args['html_before'] ) ? '' : '<div class="field-with-html-before">';

			echo empty( $args['html_before'] ) ? '' : $args['html_before'];
            ?>
            <input type="text"
                   name="<?php echo $field_name; ?>"
                   class="<?php echo esc_attr( $args['custom_item_class'] ); ?>"
                   id="<?php echo esc_attr( $args['id'] ); ?>"
                   placeholder="<?php echo esc_attr( $args['placeholder'] ); ?>"
                   value="<?php echo $field_value; ?>">
			<?php
			echo empty( $args['html_before'] ) ? '' : '</div>';
			echo empty( $args['html_after'] ) ? '' : $args['html_after'] . '</div>';

			return ob_get_clean();
		}

		/**
		 * Rendering the heading of each field element (heading, desc, tip text)
		 *
		 * @param $field_args
		 *
		 * @return false|string
		 */
		private function render_field_heading( $field_args ) {
			ob_start();
			?>
            <h3 class="vs-label-title"><?php echo esc_html( $field_args['title'] ); ?></h3>
			<?php
			$this->render_field_tip( $field_args );
			$this->render_field_desc( $field_args );

			return ob_get_clean();
		}

		/**
		 * Rendering the layout of each element (wrapper, heading, placeholder to replace with the real field input)
		 *
		 * @param $field_args
		 *
		 * @return false|string
		 */
		private function render_field_wrapper( $field_args ) {
			$field_args['class'] = isset( $field_args['class'] ) ? $field_args['class'] : '';
			$conditional_attr    = $this->get_field_conditional_id( $field_args );

			if ( isset( $field_args['is_pro_feature'] ) && $field_args['is_pro_feature'] === true ) {
				$field_args['class'] .= ' wcvs-pro-item';
			}
			$style_attr = empty( $conditional_attr ) ? '' : 'style="display: none;"';
			ob_start();
			?>
            <div class="variation-switcher-item <?php echo esc_attr( $field_args['class'] ); ?>" <?php echo $conditional_attr . $style_attr; ?>>
                <div class="variation-switcher-label">
					<?php echo $this->render_field_heading( $field_args ); ?>
                </div>
                <div class="variation-switch-field">
                    %%WCVS_FIELD%%
                </div>
            </div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Rendering the Woocommerce help tip
		 *
		 * @param $field_args
		 */
		private function render_field_tip( $field_args ) {
			if ( ! empty( $field_args['desc_tip'] ) ) {
				echo '<br><span class="woocommerce-help-tip" data-tip="' . esc_html( $field_args['desc_tip'] ) . '"></span>';
			}
		}

		/**
		 * Rendering the description of field
		 *
		 * @param $field_args
		 */
		private function render_field_desc( $field_args ) {
			if ( ! empty( $field_args['desc'] ) ) {
				echo '<br><span class="sub">' . $field_args['desc'] . '</span>';
			}
		}

		/**
		 * Getting the name="" attribute for the field element
		 *
		 * @param $field_args
		 *
		 * @return string|void
		 */
		private function get_field_name( $field_args ) {
			if ( isset( $field_args['is_pro_feature'] ) && $field_args['is_pro_feature'] === true ) {
				return '';
			}

			if ( ! empty( $field_args['tab_id'] ) ) {
				$field_name = $field_args['section_id'] . '[' . $field_args['tab_id'] . ']' . '[' . $field_args['name'] . ']';
			} else {
				$field_name = $field_args['section_id'] . '[' . $field_args['name'] . ']';
			}

			return esc_attr( $field_name );
		}

		/**
		 * Get the saved value of field in database
		 *
		 * @param $field_args
		 *
		 * @return false|mixed
		 */
		private function get_field_value( $field_args ) {
			if ( isset( $field_args['is_pro_feature'] ) && true === $field_args['is_pro_feature'] ) {
				return false;
			}

			if ( empty( $this->current_options ) ) {
				return $field_args['default_value'];
			}
			if ( ! empty( $field_args['tab_id'] ) ) {
				$field_value = isset( $this->current_options[ $field_args['section_id'] ][ $field_args['tab_id'] ][ $field_args['name'] ] ) ? $this->current_options[ $field_args['section_id'] ][ $field_args['tab_id'] ][ $field_args['name'] ] : $field_args['default_value'];
			} else {
				$field_value = isset( $this->current_options[ $field_args['section_id'] ][ $field_args['name'] ] ) ? $this->current_options[ $field_args['section_id'] ][ $field_args['name'] ] : $field_args['default_value'];
			}

			return $field_value;
		}

		/**
		 * Get the field conditional ID
		 *
		 * @param $field_args
		 *
		 * @return string
		 */
		public function get_field_conditional_id( $field_args ) {
			$conditional_id = '';
			if ( isset( $field_args['show_if_checked'] )
			     && true === $field_args['show_if_checked']
			     && ! empty( $field_args['field_to_check'] ) ) {
				$conditional_id = 'data-conditional="' . $field_args['field_to_check'] . '"';
			}

			return $conditional_id;
		}

		/**
		 * Get field description for display
		 *
		 * @param array $args settings field args
		 */
		public function get_field_description( $args ) {
			if ( ! empty( $args['desc'] ) ) {
				$desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
			} else {
				$desc = '';
			}

			return $desc;
		}

		/**
		 * Rendering the header of the settings page
		 *
		 * @param $plugin_slug
		 */
		public function render_settings_page_header( $plugin_slug ) {
			$this->render_settings_page_if_woo_core_deactivated( 'admin/partials/panel-header.php' );
		}

		/**
		 * Rendering the sidebar of the settings page
		 *
		 * @param $plugin_slug
		 */
		public function render_settings_page_sidebar( $plugin_slug ) {
			$this->render_settings_page_if_woo_core_deactivated( 'admin/partials/panel-sidebar.php' );
		}

		/**
		 * Rendering the footer of the settings page
		 *
		 * @param $plugin_slug
		 */
		public function render_settings_page_footer( $plugin_slug ) {
			$this->render_settings_page_if_woo_core_deactivated( 'admin/partials/panel-footer.php' );
		}

		/**
		 * Rendering the video tutorial of the settings page
		 *
		 * @param $plugin_slug
		 */
		public function render_settings_page_video_tutorials( $plugin_slug ) {
			$this->render_settings_page_if_woo_core_deactivated( 'admin/partials/panel-video-tutorial.php' );
		}

		private function render_settings_page_if_woo_core_deactivated( $template_path ) {
			if ( ! TA_WC_Variation_Swatches::is_woo_core_active() ) {
				TA_WC_Variation_Swatches::get_template( $template_path );
			}
		}

	}
}