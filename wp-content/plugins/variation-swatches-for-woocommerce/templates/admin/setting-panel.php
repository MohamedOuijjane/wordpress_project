<!-- render webpage -->

<div class="variation-wrap">

	<?php do_action( 'woosuite_child_plugin_header', WCVS_PLUGIN_NAME ); ?>

    <div class="thd-theme-dashboard-wrap">
        <!-- variation body -->
        <div class="thd-wrap thd-theme-dashboard">
            <div class="wrap" id="tawcvs-settings-wrap">
                <div class="thd-main">
                    <h2 style="margin: 0;padding:0;"></h2>
                    <!-- variation main content -->
                    <div class="thd-main-content">
						<?php
						if ( isset( $_POST['woosuite_saved_variation_settings'] ) ) {
							$show_success_message = 'is-dismissible';
						} else {
							$show_success_message = 'hidden';
						}
						?>
                        <div class="wcvs-notice wcvs-saving-notice notice notice-info hidden">
                            <p><?php _e( 'Saving...', 'wcvs' ); ?></p>
                        </div>
                        <div class="wcvs-notice wcvs-saved-notice notice notice-success <?php echo $show_success_message; ?>">
                            <p><?php _e( 'Settings saved successfully.', 'wcvs' ); ?></p>
                        </div>
                        <div class="wcvs-notice wcvs-failed-notice notice notice-error hidden">
                            <p><?php _e( 'Settings saved un-successfully.', 'wcvs' ); ?></p>
                        </div>
                        <div class="variation-accordion-outer">
                            <div class="clear"></div>
                            <div class="variation-main-content-head">
                                <p class="vmch-text">
									<?php if ( TA_WC_Variation_Swatches::is_woo_core_active() ): ?>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=woosuite-core' ) ); ?>">
                                            <span class="dashicons dashicons-arrow-left-alt"></span>
                                        </a>
									<?php else: ?>
                                        <span class="dashicons dashicons-admin-tools"></span>
									<?php endif; ?>
									<?php _e( 'Variations Swatches for woocommerce by Woosuite', 'wcvs' ); ?>
                                </p>
                            </div>
                            <!-- variation accordio -->
                            <div class="variation-accordion-wrap woosuite-master-form">
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="woosuite_saving_variation_settings" value="ok">
                                    <input type="hidden" name="__nonce" value="<?php echo wp_create_nonce( 'tawcvs_admin_settings' ); ?>" />
									<?php do_action( 'woosuite_variation_swatches_settings_fields_html' ); ?>
                                </form>
                            </div>
                            <!-- variation accordio -->
                        </div>
                        <div class="clear"></div>

                    </div>
                    <!-- variation main content -->
					<?php do_action( 'woosuite_child_plugin_sidebar', WCVS_PLUGIN_NAME ); ?>
                </div>
                <div class="clear"></div>
				<?php
				do_action( 'woosuite_child_plugin_footer', WCVS_PLUGIN_NAME );
				do_action( 'woosuite_child_plugin_video_tutorials', WCVS_PLUGIN_NAME );
				?>
            </div>
        </div>
        <!-- variation body -->
    </div>
</div>