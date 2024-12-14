<?php
/**
 * For use with the API debugging support page
 *
 * @package instagram-feed
 */

use InstagramFeed\Admin\SBI_Support_Tool;

require_once trailingslashit( SBI_PLUGIN_DIR ) . 'inc/class-sb-instagram-data-encryption.php';

if ( ! defined( 'ABSPATH' ) ) {
	return;
}
$role_id = SBI_Support_Tool::$plugin . SBI_Support_Tool::$role;
$cap     = $role_id;

if ( ! current_user_can( $cap ) ) {
	return;
}

$ids_with_accounts      = get_option( 'sbi_hashtag_ids_with_connected_accounts', array() );
$encryption             = new SB_Instagram_Data_Encryption();
$all_connected_accounts = SB_Instagram_Connected_Account::get_all_connected_accounts();

$data = array();
if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'sbi-api-check' ) ) {
	$data    = $this->validate_and_sanitize_support_settings( $_POST );
	$results = wp_remote_get( $this->create_api_url( $data['endpoint'], $data ) );

	if ( ! is_wp_error( $results ) ) {
		$sanitized_results               = sanitize_text_field( wp_unslash( wp_remote_retrieve_body( $results ) ) );
		$sanitized_results_token_removed = str_replace( $data['access_token'], '{access_token}', $sanitized_results );
		echo '<h3>Results</h3>';

		echo '<pre>';
		var_dump( $sanitized_results_token_removed, json_decode( $sanitized_results_token_removed, true ) );
		echo '</pre>';
		echo '<hr>';

	} elseif ( ! empty( $results->errors ) ) {
			echo '<h3>HTTP Error</h3>';
		foreach ( $results->errors as $key => $single_error ) {
			echo '<p>' . esc_html( $key ) . ' - ' . esc_html( $single_error[0] ) . '</p>';
		}
			echo '<hr>';
	}
}


?>
<div class="sbi_support_tools_wrap">
	<form method="post" action="">
		<?php wp_nonce_field( 'sbi-api-check' ); ?>

		<div class="sbi_support_tools_field_group">
			<label for="sb_instagram_support_connected_account"><?php esc_html_e( 'Connected Account', 'instagram-feed' ); ?></label>
			<select id="sb_instagram_support_connected_account" name="sb_instagram_support_connected_account">
				<option value="">Please Select</option>
				<?php
				foreach ( $all_connected_accounts as $connected_account ) :
					?>
					<option value="<?php echo esc_attr( $connected_account['id'] ); ?>"><?php echo esc_html( $connected_account['username'] ); ?> (<?php echo esc_html( $connected_account['account_type'] ); ?>)</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
		$ids_with_accounts_option = get_option( 'sbi_hashtag_ids_with_connected_accounts', array() );
		$json                     = $encryption->maybe_decrypt( $ids_with_accounts_option );
		if ( $json ) {
			$ids_with_accounts = json_decode( $json, true );
		}
		?>
		<div class="sbi_support_tools_field_group">
			<label for="sb_instagram_support_hashtag"><?php esc_html_e( 'Hashtag (if applicable)', 'instagram-feed' ); ?></label>
			<select id="sb_instagram_support_hashtag" name="sb_instagram_support_hashtag">
				<option value="">Please Select</option>
				<?php
				foreach ( $ids_with_accounts as $key => $hashtag ) :
					?>
					<option value="<?php echo esc_attr( $hashtag['id'] ); ?>"><?php echo esc_html( $key ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="sbi_support_tools_field_group">
			<label for="sb_instagram_support_endpoint"><?php esc_html_e( 'Endpoint', 'instagram-feed' ); ?></label>
			<select id="sb_instagram_support_endpoint" name="sb_instagram_support_endpoint">
				<option value="">Please Select</option>
				<?php
				foreach ( $this->available_endpoints() as $key => $endpoint ) :
					?>
					<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $endpoint ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="sbi_support_tools_field_group">
			<label for="sb_instagram_support_fields"><?php esc_html_e( 'Fields', 'instagram-feed' ); ?></label>
			<input id="sb_instagram_support_fields" name="sb_instagram_support_fields" type="text">
			<p>
				Examples: <br>
				biography,id,username,website,followers_count,media_count,profile_picture_url,name<br>
				media_url,media_product_type,thumbnail_url,caption,id,media_type,timestamp,username,comments_count,like_count,permalink
			</p>
		</div>
		<div class="sbi_support_tools_field_group">
			<label for="sb_instagram_support_children_fields"><?php esc_html_e( 'Children Fields', 'instagram-feed' ); ?></label>
			<input id="sb_instagram_support_children_fields" name="sb_instagram_support_children_fields" type="text">
			<p>
				Examples: <br>
				media_url,id,media_type,timestamp,permalink,thumbnail_url
			</p>
		</div>
		<div class="sbi_support_tools_field_group">
			<label for="sb_instagram_support_limit"><?php esc_html_e( 'Limit', 'instagram-feed' ); ?></label>
			<input id="sb_instagram_support_limit" name="sb_instagram_support_limit" type="number">
		</div>

		<button class="button button-primary" type="submit">Submit</button>
	</form>
</div>

<style>
	.sbi_support_tools_wrap {
		padding: 20px;
	}
	.sbi_support_tools_field_group {
		margin-bottom: 20px;
	}
	.sbi_support_tools_field_group label {
		display: block;
		font-weight: bold;
	}
</style>
