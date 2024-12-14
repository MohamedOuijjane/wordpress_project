<?php
if ( ! class_exists( 'VSWC_Setting_Fields_Manager' ) ) {
	class VSWC_Setting_Fields_Manager {


		/**
		 * Get the array of settings sections
		 *
		 * @return mixed|void
		 */
		public static function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'general',
					'title' => __( 'General Settings', 'wcvs' ),
					'icon'  => 'dashicons-admin-settings'
				),
				array(
					'id'      => 'design',
					'title'   => __( 'Design', 'wcvs' ),
					'icon'    => 'dashicons-art',
					'has_tab' => true
				),
				array(
					'id'    => 'archive',
					'title' => __( 'Archive / Shop', 'wcvs' ),
					'icon'  => 'dashicons-store'
				)
			);

			return apply_filters( 'wcvs_settings_sections', $sections );
		}

		/**
		 * Get the array of settings sections
		 *
		 * @return mixed|void
		 */
		public static function get_settings_tabs() {

			$tabs = array(
				'design' => array(
					array(
						'id'    => 'productDesign',
						'title' => __( 'Product page', 'wcvs' )
					),
					array(
						'id'    => 'shopDesign',
						'title' => __( 'Shop Archive', 'wcvs' )
					),
					array(
						'id'    => 'toolTipDesign',
						'title' => __( 'Tooltip', 'wcvs' )
					),
				)
			);

			return apply_filters( 'wcvs_settings_tabs', $tabs );
		}

		/**
		 * Get the array of settings fields
		 *
		 * @return mixed|void
		 */
		public static function get_settings_fields() {

			$settings_fields = array(
				'general' => array(
					array(
						'title'    => __( 'Enable Color Swatches', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-enable-color-swatches',
								'type' => 'checkbox',
								'name' => 'enable-color-swatches',
							)
						),
						'class'=>'main-ajax-trigger',
						'desc'     => __( 'Enable the Color type for Product Attributes', 'wcvs' ),
						'priority' => 1
					),
					array(
						'title'           => __( 'Select Attributes', 'wcvs' ),
						'fields'          => array(
							array(
								'id'   => 'wcvs-color-swatches-attributes',
								'type' => 'html',
								'html' => TA_WC_Variation_Swatches::get_product_attributes_as_checkbox(
									'general',
									'',
									'color-swatches-attribute',
									true,
									'color' )
							)
						),
						'desc'            => __( 'Which attributes you want to enable the Color type?', 'wcvs' ),
						'class'           => 'indent ajax-to-update',
						'field_to_check'  => 'wcvs-enable-color-swatches',
						'show_if_checked' => true,
						'priority'        => 1.1
					),
					array(
						'title'    => __( 'Show label inside swatches', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-show-label-on-color-swatches',
								'type' => 'checkbox',
								'name' => 'show-label-on-color-swatches',
							)
						),
						'class'          => 'indent',
						'field_to_check' => 'wcvs-enable-color-swatches',
						'desc'           => __( 'Show the label for color swatches', 'wcvs' ),
						'show_if_checked' => true,
						'priority'       => 1.2,
						'is_pro_feature' => true,
					),
					array(
						'title'    => __( 'Enable Image Swatches', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-enable-image-swatches',
								'type' => 'checkbox',
								'name' => 'enable-image-swatches',
							)
						),
						'class'=>'main-ajax-trigger',
						'desc'     => __( 'Enable the Image type for Product Attributes', 'wcvs' ),
						'priority' => 1.3
					),
					array(
						'title'           => __( 'Select Attributes', 'wcvs' ),
						'fields'          => array(
							array(
								'id'   => 'wcvs-image-swatches-attributes',
								'type' => 'html',
								'html' => TA_WC_Variation_Swatches::get_product_attributes_as_checkbox(
									'general',
									'',
									'image-swatches-attribute',
									true,
									'image' )
							)
						),
						'desc'            => __( 'Which attributes you want to enable the Image type?', 'wcvs' ),
						'class'           => 'indent ajax-to-update',
						'field_to_check'  => 'wcvs-enable-image-swatches',
						'show_if_checked' => true,
						'priority'        => 1.4
					),
					array(
						'title'    => __( 'Enable Radio Button Swatches', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-enable-radio-swatches',
								'type' => 'checkbox',
								'name' => 'enable-radio-swatches',
							)
						),
						'class'          => 'main-ajax-trigger',
						'desc'           => __( 'Enable the Radio button type for Product Attributes', 'wcvs' ),
						'priority'       => 1.5,
						'is_pro_feature' => true,
					),
					array(
						'title'    => __( 'Auto Convert Dropdowns To Label', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-dropdown-to-label',
								'type' => 'checkbox',
								'name' => 'dropdown-to-label',
							)
						),
						'desc'     => __( 'Automatically covert dropdowns to &#34;Label Swatch&#34; by default', 'wcvs' ),
						'priority' => 1.8
					),
					array(
						'title'           => __( 'Select Attributes', 'wcvs' ),
						'fields'          => array(
							array(
								'id'   => 'wcvs-selectbox-to-label',
								'type' => 'html',
								'html' => TA_WC_Variation_Swatches::get_product_attributes_as_checkbox( 'general', '', 'dropdown-to-label-attribute' )
							)
						),
						'class'           => 'indent',
						'field_to_check'  => 'wcvs-dropdown-to-label',
						'show_if_checked' => true,
						'priority'        => 2
					),
					array(
						'title'          => __( 'Auto Convert Dropdowns To Image', 'wcvs' ),
						'fields'         => array(
							array(
								'id'   => 'wcvs-dropdown-to-image',
								'type' => 'checkbox',
								'name' => 'dropdown-to-image',
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Automatically covert dropdowns to &#34;Image Swatch&#34; if variation has an image.', 'wcvs' ),
						'priority'       => 3
					),
					array(
						'title'          => __( 'Disable checking the variation product availability', 'wcvs' ),
						'fields'         => array(
							array(
								'id'   => 'wcvs-disable-checking-availability',
								'type' => 'checkbox',
								'name' => 'disable-checking-availability',
							)
						),
						'desc'           => __( 'All variations will be shown as available to select.', 'wcvs' ),
						'priority'       => 4.1
					),
					array(
						'title'          => __( 'Still showing the invalid attribute\'s as selectable', 'wcvs' ),
						'fields'         => array(
							array(
								'id'   => 'wcvs-keep-showing-attribute-state',
								'type' => 'checkbox',
								'name' => 'keep-showing-attribute-state',
							)
						),
						'priority'       => 4.1,
						'class'           => 'indent',
						'field_to_check'  => 'wcvs-disable-checking-availability',
						'show_if_checked' => true,
					),
					array(
						'title'    => __( 'Single variation image preview', 'wcvs' ),
						'fields'   => array(
							array(
								'type'          => 'select',
								'options_group'	=> array(
									array(
										'value' => 0,
										'label' => __( '-- Select attribute --', 'wcvs' )
									)
								),
								'class'         => 'br-type',
								'name'          => 'single-variation-preview',
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Automatically change product image based on this attribute', 'wcvs' ),
						'priority'       => 4.2
					),
					array(
						'title'    => __( 'Choose your swatch shape', 'wcvs' ),
						'fields'   => array(
							array(
								'type'              => 'radio',
								'name'              => 'swatch-shape',
								'options_group'     => array(
									array(
										'value' => 'circle',
										'label' => __( 'S', 'wcvs' ),
									),
									array(
										'value' => 'edge',
										'label' => __( 'S', 'wcvs' ),
									),
									array(
										'value' => 'rounded',
										'label' => __( 'S', 'wcvs' ),
									)
								),
								'default_value'     => 'circle',
								'custom_item_class' => 'swatch-shape-radio',
							)
						),
						'class'    => 'swatch-shape-wrapper',
						'desc'     => __( 'Select option below', 'wcvs' ),
						'priority' => 5
					),
					array(
						'title'    => __( 'Choose your swatch image ratio', 'wcvs' ),
						'fields'   => array(
							array(
								'type'              => 'radio',
								'name'              => 'swatch-ratio',
								'options_group'     => array(
									array(
										'value' => 'disabled',
										'label' => __( 'None', 'wcvs' ),
									),
									array(
										'value' => '11',
										'label' => __( '1:1', 'wcvs' ),
									),
									array(
										'value' => '34',
										'label' => __( '3:4', 'wcvs' ),
									),
									array(
										'value' => '43',
										'label' => __( '4:3', 'wcvs' ),
									),
									array(
										'value' => '32',
										'label' => __( '3:2', 'wcvs' ),
									),
									array(
										'value' => '23',
										'label' => __( '2:3', 'wcvs' ),
									),
									array(
										'value' => '169',
										'label' => __( '16:9', 'wcvs' ),
									),
									array(
										'value' => '916',
										'label' => __( '9:16', 'wcvs' ),
									),
									array(
										'value' => '54',
										'label' => __( '5:4', 'wcvs' ),
									),
									array(
										'value' => '45',
										'label' => __( '4:5', 'wcvs' ),
									)
								),
								'default_value'     => 'disabled',
								'custom_item_class' => 'swatch-ratio-radio',
							)
						),
						'is_pro_feature' => true,
						'class'    => 'swatch-image-ratio-wrapper',
						'desc'     => __( 'It will be applied to the swatch image only.<br><i><u>Note:</u></i> <b>Swatch Height</b> in <b>Design</b> tab will be ignored.', 'wcvs' ),
						'priority' => 5.1
					),
					array(
						'title'    => __( 'Image position', 'wcvs' ),
						'fields'   => array(
							array(
								'id'            => 'wcvs-image-position-pro',
								'type'          => 'select',
								'options_group' => array(
									array(
										'value' => 'default',
										'label' => __( 'Default', 'wcvs' ),
									)
								),
								'class'         => 'br-type',
								'name'          => 'image-position',
							)
						),
						'desc'     => __( 'Select option', 'wcvs' ),
						'priority' => 5.2,
						'is_pro_feature' => true,
					),
					array(
						'title'    => __( 'Disable Default Plugin Stylesheet', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-disable-plugin-stylesheet',
								'type' => 'checkbox',
								'name' => 'disable-plugin-stylesheet',
							)
						),
						'desc'     => __( 'Option to enable/disable default plugin stylesheet for theme developer', 'wcvs' ),
						'priority' => 6
					),
					array(
						'title'    => __( 'Tooltip', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-enable-tooltip',
								'type' => 'checkbox',
								'name' => 'enable-tooltip',
							)
						),
						'desc'     => __( 'Enable or disable tooltip', 'wcvs' ),
						'priority' => 7
					),
					array(
						'title'          => __( 'Enable Dual Color', 'wcvs' ),
						'fields'         => array(
							array(
								'id'   => 'wcvs-enable-dual-color',
								'type' => 'checkbox',
								'name' => 'enable-dual-color',
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Enable or disable when the attribute type is set to color', 'wcvs' ),
						'priority'       => 8
					),
					array(
						'title'          => __( 'Out of Stock Behaviour', 'wcvs' ),
						'fields'         => array(
							array(
								'id'            => 'wcvs-enable-dual-color',
								'type'          => 'select',
								'options_group' => array(
									array(
										'value' => 'blur-with-cross',
										'label' => __( 'Blur With Cross', 'wcvs' ),
									)
								),
								'class'         => 'br-type',
								'name'          => 'oos',
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Select option', 'wcvs' ),
						'priority'       => 8
					),
					array(
						'title'    => __( 'Enable Attribute Name', 'woosuite-variation-swatches-pro' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-enable-attribute-name',
								'type' => 'checkbox',
								'name' => 'enable-attribute-name',
							)
						),
						'is_pro_feature' => true,
						'desc'     => __( 'It will show the attribute name when choosing a desired attribute value', 'woosuite-variation-swatches-pro' ),
						'priority' => 9
					),
					array(
						'title'    => __( 'Enable Attribute Description', 'woosuite-variation-swatches-pro' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-enable-attribute-desc',
								'type' => 'checkbox',
								'name' => 'enable-attribute-desc',
							)
						),
						'is_pro_feature' => true,
						'desc'     => __( 'It will show the attribute description under the attribute block', 'woosuite-variation-swatches-pro' ),
						'priority' => 10
					),
					array(
						'title'          => __( 'Swatch limit on Singe product', 'wcvs' ),
						'fields'         => array(
							array(
								'id'            => 'wcvs-swatch-limit-single-page',
								'type'          => 'number',
								'name'          => 'swatch-limit-single-page',
								'default_value' => '0'
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Set a max amount of swatches to show.<br>0 means show all swatches', 'wcvs' ),
						'priority'       => 11
					),
				),
				'design'  => array(
					'productDesign' => array(
						array(
							'title'          => __( 'Item styling', 'wcvs' ),
							'fields'         => array(
								array(
									'id'   => 'wcvs-product-item-styling',
									'type' => 'checkbox',
									'name' => 'item-styling',
								)
							),
							'is_pro_feature' => true,
							'desc'           => __( 'Edit the default state of your swatches', 'wcvs' ),
							'priority'       => 1
						),
						array(
							'title'          => __( 'Item hover styling', 'wcvs' ),
							'fields'         => array(
								array(
									'id'   => 'wcvs-product-item-hover',
									'type' => 'checkbox',
									'name' => 'item-hover',
								)
							),
							'is_pro_feature' => true,
							'desc'           => __( 'Edit the hover state of your swatches', 'wcvs' ),
							'priority'       => 5
						),
						array(
							'title'          => __( 'Item Selected Styling', 'wcvs' ),
							'fields'         => array(
								array(
									'id'   => 'wcvs-product-item-selected',
									'type' => 'checkbox',
									'name' => 'item-selected',
								)
							),
							'is_pro_feature' => true,
							'desc'           => __( 'Edit the selected state of your swatches', 'wcvs' ),
							'priority'       => 9
						),
						array(
							'title'    => __( 'Swatch Text font size', 'wcvs' ),
							'fields'   => array(
								array(
									'id'   => 'wcvs-product-item-font',
									'type' => 'checkbox',
									'name' => 'item-font',
								)
							),
							'desc'     => __( 'The default font size that will be used for your swatches text', 'wcvs' ),
							'priority' => 13
						),
						array(
							'title'           => __( 'Text font size', 'wcvs' ),
							'fields'          => array(
								array(
									'id'            => 'wcvs-text-font-size',
									'type'          => 'number',
									'name'          => 'text-font-size',
									'default_value' => '12'
								),
								array(
									'id'            => 'wcvs-product-item-font-size-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										)
									),
									'name'          => 'item-font-size-type',
									'default_value' => 'px'
								)
							),
							'class'           => 'indent',
							'field_to_check'  => 'wcvs-product-item-font',
							'show_if_checked' => true,
							'priority'        => 14
						),
						array(
							'title'    => __( 'Margin and Padding', 'wcvs' ),
							'type'     => 'text',
							'html'     => __( 'Swatch Item -> represents each individual swatch item.<br>Swatches Wrapper -> represents the container for the group of swatches.', 'wcvs' ),
							'priority' => 15
						),
						array(
							'title'    => __( 'Swatch Item Margin', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-mar-top',
									'type'          => 'number',
									'name'          => 'mar-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-right',
									'type'          => 'number',
									'name'          => 'mar-right',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-bottom',
									'type'          => 'number',
									'name'          => 'mar-bottom',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-left',
									'type'          => 'number',
									'name'          => 'mar-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'mar-type',
									'default_value' => 'px'
								)
							),
							'priority' => 16
						),
						array(
							'title'    => __( 'Swatch Item Padding', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-pad-top',
									'type'          => 'number',
									'name'          => 'pad-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-right',
									'type'          => 'number',
									'name'          => 'pad-right',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-bottom',
									'type'          => 'number',
									'name'          => 'pad-bottom',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-left',
									'type'          => 'number',
									'name'          => 'pad-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'pad-type',
									'default_value' => 'px'
								)
							),
							'priority' => 17
						),
						array(
							'title'    => __( 'Swatch Wrapper Margin', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-wrm-top',
									'type'          => 'number',
									'name'          => 'wrm-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-right',
									'type'          => 'number',
									'name'          => 'wrm-right',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-bottom',
									'type'          => 'number',
									'name'          => 'wrm-bottom',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-left',
									'type'          => 'number',
									'name'          => 'wrm-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'wrm-type',
									'default_value' => 'px'
								)
							),
							'priority' => 18
						),
						array(
							'title'    => __( 'Swatch Wrapper Padding', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-wrp-top',
									'type'          => 'number',
									'name'          => 'wrp-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-right',
									'type'          => 'number',
									'name'          => 'wrp-right',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-bottom',
									'type'          => 'number',
									'name'          => 'wrp-bottom',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-left',
									'type'          => 'number',
									'name'          => 'wrp-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'wrp-type',
									'default_value' => 'px'
								)
							),
							'priority' => 19
						),
						array(
							'title'          => __( 'Swatch Width', 'wcvs' ),
							'fields'         => array(
								array(
									'id'            => 'wcvs-swatch-width',
									'type'          => 'number',
									'name'          => 'swatch-width',
									'default_value' => '',
									'placeholder'   => 'auto',
									'html_after'    => '<span class="sw-input-type-text">' . __( 'px', 'wcvs' ) . '</span>'
								),
							),
							'is_pro_feature' => true,
							'priority'       => 20
						),
						array(
							'title'          => __( 'Swatch Height', 'wcvs' ),
							'fields'         => array(
								array(
									'id'            => 'wcvs-swatch-height',
									'type'          => 'number',
									'name'          => 'swatch-height',
									'default_value' => '',
									'placeholder'   => 'auto',
									'html_after'    => '<span class="sw-input-type-text">' . __( 'px', 'wcvs' ) . '</span>'
								),
							),
							'is_pro_feature' => true,
							'priority'       => 21
						),
					),
					'shopDesign'    => array(
						array(
							'title'          => __( 'Item styling', 'wcvs' ),
							'fields'         => array(
								array(
									'id'   => 'wcvs-shop-item-styling',
									'type' => 'checkbox',
									'name' => 'item-styling',
								)
							),
							'is_pro_feature' => true,
							'desc'           => __( 'Edit the default state of your swatches', 'wcvs' ),
							'priority'       => 1
						),
						array(
							'title'          => __( 'Item hover styling', 'wcvs' ),
							'fields'         => array(
								array(
									'id'   => 'wcvs-shop-item-hover',
									'type' => 'checkbox',
									'name' => 'item-hover',
								)
							),
							'is_pro_feature' => true,
							'desc'           => __( 'Edit the hover state of your swatches', 'wcvs' ),
							'priority'       => 5
						),
						array(
							'title'          => __( 'Item Selected Styling', 'wcvs' ),
							'fields'         => array(
								array(
									'id'   => 'wcvs-shop-item-selected',
									'type' => 'checkbox',
									'name' => 'item-selected',
								)
							),
							'is_pro_feature' => true,
							'desc'           => __( 'Edit the selected state of your swatches', 'wcvs' ),
							'priority'       => 9
						),
						array(
							'title'    => __( 'Swatch Text font size', 'wcvs' ),
							'fields'   => array(
								array(
									'id'   => 'wcvs-shop-item-font',
									'type' => 'checkbox',
									'name' => 'item-font',
								)
							),
							'desc'     => __( 'The default font size that will be used for your swatches text', 'wcvs' ),
							'priority' => 13
						),
						array(
							'title'           => __( 'Text font size', 'wcvs' ),
							'fields'          => array(
								array(
									'id'            => 'wcvs-text-font-size',
									'type'          => 'number',
									'name'          => 'text-font-size',
									'default_value' => '12'
								),
								array(
									'id'            => 'wcvs-shop-item-font-size-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										)
									),
									'name'          => 'item-font-size-type',
									'default_value' => 'px'
								)
							),
							'class'           => 'indent',
							'field_to_check'  => 'wcvs-shop-item-font',
							'show_if_checked' => true,
							'priority'        => 14
						),
						array(
							'title'    => __( 'Margin and Padding', 'wcvs' ),
							'type'     => 'text',
							'html'     => __( 'Swatch Item -> represents each individual swatch item.<br>Swatches Wrapper -> represents the container for the group of swatches.', 'wcvs' ),
							'priority' => 15
						),
						array(
							'title'    => __( 'Swatch Item Margin', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-mar-top',
									'type'          => 'number',
									'name'          => 'mar-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-right',
									'type'          => 'number',
									'name'          => 'mar-right',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-bottom',
									'type'          => 'number',
									'name'          => 'mar-bottom',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-left',
									'type'          => 'number',
									'name'          => 'mar-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-mar-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'mar-type',
									'default_value' => 'px'
								)
							),
							'priority' => 16
						),
						array(
							'title'    => __( 'Swatch Item Padding', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-pad-top',
									'type'          => 'number',
									'name'          => 'pad-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-right',
									'type'          => 'number',
									'name'          => 'pad-right',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-bottom',
									'type'          => 'number',
									'name'          => 'pad-bottom',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-left',
									'type'          => 'number',
									'name'          => 'pad-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-pad-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'pad-type',
									'default_value' => 'px'
								)
							),
							'priority' => 17
						),
						array(
							'title'    => __( 'Swatch Wrapper Margin', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-wrm-top',
									'type'          => 'number',
									'name'          => 'wrm-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-right',
									'type'          => 'number',
									'name'          => 'wrm-right',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-bottom',
									'type'          => 'number',
									'name'          => 'wrm-bottom',
									'default_value' => '15',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-left',
									'type'          => 'number',
									'name'          => 'wrm-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-wrm-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'wrm-type',
									'default_value' => 'px'
								)
							),
							'priority' => 18
						),
						array(
							'title'    => __( 'Swatch Wrapper Padding', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-wrp-top',
									'type'          => 'number',
									'name'          => 'wrp-top',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&uarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-right',
									'type'          => 'number',
									'name'          => 'wrp-right',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&rarr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-bottom',
									'type'          => 'number',
									'name'          => 'wrp-bottom',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&darr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-left',
									'type'          => 'number',
									'name'          => 'wrp-left',
									'default_value' => '0',
									'html_before'    => '<span class="sw-input-type-icon">&larr;</span>'
								),
								array(
									'id'            => 'wcvs-wrp-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										),
										array(
											'value' => 'em',
											'label' => __( 'em', 'wcvs' ),
										)
									),
									'name'          => 'wrp-type',
									'default_value' => 'px'
								)
							),
							'priority' => 19
						),
						array(
							'title'          => __( 'Swatch Width', 'wcvs' ),
							'fields'         => array(
								array(
									'id'            => 'wcvs-swatch-archive-width',
									'type'          => 'number',
									'name'          => 'swatch-archive-width',
									'default_value' => '',
									'placeholder'   => 'auto',
									'html_after'    => '<span class="sw-input-type-text">' . __( 'px', 'wcvs' ) . '</span>'
								),
							),
							'is_pro_feature' => true,
							'priority'       => 20
						),
						array(
							'title'          => __( 'Swatch Height', 'wcvs' ),
							'fields'         => array(
								array(
									'id'            => 'wcvs-swatch-archive-height',
									'type'          => 'number',
									'name'          => 'swatch-archive-height',
									'default_value' => '',
									'placeholder'   => 'auto',
									'html_after'    => '<span class="sw-input-type-text">' . __( 'px', 'wcvs' ) . '</span>'
								),
							),
							'is_pro_feature' => true,
							'priority'       => 21
						),
					),
					'toolTipDesign' => array(
						array(
							'title'          => __( 'Tooltip styling', 'wcvs' ),
							'fields'         => array(
								array(
									'id'   => 'wcvs-item-tooltip',
									'type' => 'checkbox',
									'name' => 'item-tooltip',
								)
							),
							'is_pro_feature' => true,
							'priority'       => 1
						),
						array(
							'title'    => __( 'Tooltip Text font size', 'wcvs' ),
							'fields'   => array(
								array(
									'id'   => 'wcvs-tooltip-item-font',
									'type' => 'checkbox',
									'name' => 'item-font',
								)
							),
							'desc'     => __( 'The default font size that will be used for your swatches text', 'wcvs' ),
							'priority' => 5
						),
						array(
							'title'           => __( 'Text font size', 'wcvs' ),
							'fields'          => array(
								array(
									'id'            => 'wcvs-tooltip-text-font-size',
									'type'          => 'number',
									'name'          => 'text-font-size',
									'default_value' => '12'
								),
								array(
									'id'            => 'wcvs-tooltip-item-font-size-type',
									'type'          => 'select',
									'options_group' => array(
										array(
											'value' => 'px',
											'label' => __( 'px', 'wcvs' ),
										),
										array(
											'value' => 'rem',
											'label' => __( 'rem', 'wcvs' ),
										),
										array(
											'value' => 'pt',
											'label' => __( 'pt', 'wcvs' ),
										)
									),
									'name'          => 'item-font-size-type',
									'default_value' => 'px'
								)
							),
							'class'           => 'indent',
							'field_to_check'  => 'wcvs-tooltip-item-font',
							'show_if_checked' => true,
							'priority'        => 6
						),

						array(
							'title'    => __( 'Tooltip Width', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-tooltip-width',
									'type'          => 'number',
									'name'          => 'width',
									'default_value' => '',
									'placeholder'   => 'auto',
									'html_after'    => '<span class="sw-input-type-text">' . __( 'px', 'wcvs' ) . '</span>'
								),
							),
							'priority' => 7
						),
						array(
							'title'    => __( 'Tooltip Maximum Width', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-tooltip-max-width',
									'type'          => 'number',
									'name'          => 'max-width',
									'default_value' => '',
									'placeholder'   => 'auto',
									'html_after'    => '<span class="sw-input-type-text">' . __( 'px', 'wcvs' ) . '</span>'
								),
							),
							'priority' => 8
						),
						array(
							'title'    => __( 'Tooltip Line Height', 'wcvs' ),
							'fields'   => array(
								array(
									'id'            => 'wcvs-tooltip-line-height',
									'type'          => 'number',
									'name'          => 'line-height',
									'default_value' => '',
									'placeholder'   => 'auto',
									'html_after'    => '<span class="sw-input-type-text">' . __( 'px', 'wcvs' ) . '</span>'
								),
							),
							'priority' => 9
						),
					)
				),
				'archive' => array(
					array(
						'title'    => __( 'Show Swatches Label', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-show-swatch',
								'type' => 'checkbox',
								'name' => 'show-swatch',
							)
						),
						'desc'     => __( 'This will show your swatches when users are browsing your main store page', 'wcvs' ),
						'priority' => 1
					),
					array(
						'title'    => __( 'Show clear link', 'wcvs' ),
						'fields'   => array(
							array(
								'id'   => 'wcvs-show-clear-link',
								'type' => 'checkbox',
								'name' => 'show-clear-link',
							)
						),
						'desc'     => __( 'This allows users to clean section', 'wcvs' ),
						'priority' => 2
					),
					array(
						'title'    => __( 'Swatch alignment', 'wcvs' ),
						'fields'   => array(
							array(
								'id'            => 'wcvs-swatch-alignment',
								'type'          => 'select',
								'options_group' => array(
									array(
										'value' => 'left',
										'label' => __( 'Left', 'wcvs' ),
									),
									array(
										'value' => 'center',
										'label' => __( 'Center', 'wcvs' ),
									),
									array(
										'value' => 'right',
										'label' => __( 'Right', 'wcvs' ),
									),
								),
								'name'          => 'swatch-alignment',
							)
						),
						'desc'     => __( 'Chose how to swatches are displayed', 'wcvs' ),
						'priority' => 3
					),
					array(
						'title'          => __( 'Swatch position', 'wcvs' ),
						'fields'         => array(
							array(
								'id'            => 'wcvs-swatch-position',
								'type'          => 'select',
								'options_group' => array(
									array(
										'value' => 'before-title',
										'label' => __( 'Before Title', 'wcvs' ),
									),
									array(
										'value' => 'after-title',
										'label' => __( 'After Title', 'wcvs' ),
									),
									array(
										'value' => 'before-add-to-cart',
										'label' => __( 'Before Add to Cart', 'wcvs' ),
									),
									array(
										'value' => 'after-add-to-cart',
										'label' => __( 'After Add to Cart', 'wcvs' ),
									),
								),
								'name'          => 'swatch-position',
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Choose where to insert swatches', 'wcvs' ),
						'priority'       => 4
					),
					array(
						'title'          => __( 'Enable Tooltip', 'wcvs' ),
						'fields'         => array(
							array(
								'id'   => 'wcvs-tooltip-enable',
								'type' => 'checkbox',
								'name' => 'tooltip-enable',
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Enhance the shopping experience for your users', 'wcvs' ),
						'priority'       => 5
					),
					array(
						'title'          => __( 'Swatch limit', 'wcvs' ),
						'fields'         => array(
							array(
								'id'            => 'wcvs-swatch-limit',
								'type'          => 'number',
								'name'          => 'swatch-limit',
								'default_value' => '0'
							)
						),
						'is_pro_feature' => true,
						'desc'           => __( 'Set a max amount of swatches to show.<br>0 means show all swatches', 'wcvs' ),
						'priority'       => 6
					),
				),
			);

			$settings_fields = apply_filters( 'wcvs_settings_fields', $settings_fields );

			foreach ( $settings_fields as $section_id => $items ) {
				foreach ( $items as $field_name => $field_value ) {
					if ( ! isset( $field_value['priority'] ) ) {
						$settings_fields[ $section_id ][ $field_name ] = wp_list_sort( $field_value, 'priority', 'ASC', true );
					} else {
						$settings_fields[ $section_id ] = wp_list_sort( $items, 'priority', 'ASC', true );
					}
				}
			}

			return $settings_fields;
		}
	}
}