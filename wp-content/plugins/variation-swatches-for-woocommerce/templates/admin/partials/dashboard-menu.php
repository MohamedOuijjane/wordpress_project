<div class="opt-desh-menu-wrap">
    <ul class="opt-desh-menu">
        <li class="active">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=variation-swatches-addons' ) ); ?>"
               class="<?php echo isset( $_GET['page'] ) && $_GET['page'] == 'woosuite-core-addons' ? 'active-desh-menu' : ''; ?>">
				<?php _e( 'Addons', 'wcvs' ); ?>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://aovup.com/docs?utm_source=user-dashboard&utm_medium=header" target="_blank">
				<?php _e( 'Docs', 'wcvs' ); ?>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://aovup.com/support?utm_source=user-dashboard&utm_medium=header" target="_blank">
				<?php _e( 'Support', 'wcvs' ); ?>
            </a>
        </li>
        <li>
            <a target="_blank" href="https://aovup.com/my-account?utm_source=user-dashboard&utm_medium=header" target="_blank">
				<?php _e( 'My Account ', 'wcvs' ); ?>
            </a>
        </li>
    </ul>
</div>