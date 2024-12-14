<div class="opt-desh-wrap">
	<?php TA_WC_Variation_Swatches::get_template('admin/partials/dashboard-header.php'); ?>

	<div class="opt-desh-body-wrap">
		<div class="opt-desh-body">
			<div class="opt-desh-container">

				<?php TA_WC_Variation_Swatches::get_template('admin/partials/dashboard-menu.php'); ?>

				<!-- module banner -->
				<div class="opt-module-banner">
					<div class="opt-module-banner-grid">
						<div class="opt-main-content-banner">
							<h3 class="opt-banner-title"><?php _e('All Woosuite Plugins','wcvs');?></h3>
							<p><?php _e('You can view and check more details on our plugins below', 'wcvs');?></p>
						</div>
						<div class="opt-module-banner-img">
							<img src="<?php echo WCVS_PLUGIN_URL;?>assets/images/wc-plugins.png" class="img-100" alt="">
						</div>
					</div>
				</div>
				<!-- module banner -->
				<!-- module wrap -->
				<div class="opt-modules-wrap">
					<div class="opt-moules-head">
						<div class="opt-module-filter" style="display: none;">
							<label class="marketing-input-wrapper">
								<span class="marketing-label marketing-label--in-field"><?php _e('Filter by', 'wcvs')?></span>
								<div class="marketing-select-wrapper">
									<svg class="icon marketing-select-wrapper__icon" aria-hidden="true" focusable="false"> <use xlink:href="#modules-caret-down"></use> </svg>

									<select aria-describedby="MessageId_7e3d" aria-label="Reload content and sort by" id="SortByFilter_input" class="marketing-select marketing-select--in-field" name="sort_by_options[sort_by]">
										<option value="wholesale"><?php _e('wholesale', 'wcvs')?></option>
										<option value="wholesale2"><?php _e('wholesale 2', 'wcvs')?></option>
										<option value="wholesale3"><?php _e('wholesale 3', 'wcvs')?></option>
									</select>
								</div>
							</label>
						</div>
					</div>
					<div class="opt-module-content-wrap">
						<?php
						TA_WC_Variation_Swatches::get_template('admin/partials/modules-grid.php');
						?>
					</div>
				</div>
				<!-- module wrap -->
			</div>
		</div>
	</div>


</div>