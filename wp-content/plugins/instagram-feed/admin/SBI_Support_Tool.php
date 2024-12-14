<?php
/**
 * SBI_Support_Tool.
 *
 * @since 6.4
 *
 * @package instagram-feed-pro
 */

namespace InstagramFeed\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a temporary page and user that support can use to troubleshoot feed issues
 */
class SBI_Support_Tool {

	/**
	 * Plugin name for identifying which plugin this is for
	 *
	 * @var string
	 */
	public static $plugin_name = 'SmashBalloon Instagram';

	/**
	 * Slug for identifying which plugin this is for
	 *
	 * @var string
	 */
	public static $plugin = 'smash_sbi';

	/**
	 * Temp User Name
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $name = 'SmashBalloon';

	/**
	 * Temp Last Name
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $last_name = 'Support';


	/**
	 * Temp Login UserName
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $username = 'SmashBalloon_SBISupport';

	/**
	 * Cron Job Name
	 *
	 * @access public
	 *
	 * @var string
	 */
	public static $cron_event_name = 'smash_sbi_delete_expired_user';

	/**
	 * Temp User Role
	 *
	 * @access private
	 *
	 * @var string
	 */
	public static $role = '_support_role';

	/**
	 * Class constructor
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * SBI_Support_Tool initializer.
	 *
	 * @since 6.3
	 */
	public function init() {
		$this->init_temp_login();

		if ( ! is_admin() ) {
			return;
		}

		$this->ini_ajax_calls();
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_footer', array( '\InstagramFeed\Admin\SBI_Support_Tool', 'delete_expired_users' ) );
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 *
	 * @return void
	 */
	public function ini_ajax_calls() {
		add_action( 'wp_ajax_sbi_create_temp_user', array( $this, 'create_temp_user_ajax_call' ) );
		add_action( 'wp_ajax_sbi_delete_temp_user', array( $this, 'delete_temp_user_ajax_call' ) );
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 */
	public function delete_temp_user_ajax_call() {
		check_ajax_referer( 'sbi-admin', 'nonce' );
		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error();
		}

		if ( ! isset( $_POST['userId'] ) ) {
			wp_send_json_error();
		}

		$user_id = absint( $_POST['userId'] );
		$return  = self::delete_temporary_user( $user_id );
		echo wp_json_encode( $return );
		wp_die();
	}

	/**
	 * Create New User Ajax Call
	 *
	 * @since 6.3
	 */
	public function create_temp_user_ajax_call() {
		check_ajax_referer( 'sbi-admin', 'nonce' );
		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error();
		}
		$return = self::create_temporary_user();
		echo wp_json_encode( $return );
		wp_die();
	}

	/**
	 * Init Login
	 *
	 * @since 6.3
	 */
	public function init_temp_login() {

		$attr = self::$plugin . '_token';
		if ( empty( $_GET[ $attr ] ) ) { // phpcs:ignore
			return;
		}

		$token     = sanitize_key( $_GET[ $attr ] );  // phpcs:ignore
		$temp_user = self::get_temporary_user_by_token( $token );
		if ( ! $temp_user ) {
			wp_die( esc_attr__( 'You cannot connect this user', 'instagram-feed' ) );
		}

		$user_id      = $temp_user->ID;
		$should_login = ( is_user_logged_in() && $user_id !== get_current_user_id() ) || ! is_user_logged_in();

		if ( $should_login ) {
			if ( $user_id !== get_current_user_id() ) {
				wp_logout();
			}

			$user_login = $temp_user->user_login;

			wp_set_current_user( $user_id, $user_login );
			wp_set_auth_cookie( $user_id );
			do_action( 'wp_login', $user_login, $temp_user );
			$redirect_page = 'admin.php?page=' . self::$plugin . '_tool';
			wp_safe_redirect( admin_url( $redirect_page ) );
			exit();
		}
	}

	/**
	 * Create New User.
	 *
	 * @return array
	 *
	 * @since 6.3
	 */
	public static function create_temporary_user() {
		if ( ! current_user_can( 'create_users' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You don\'t have enough permission to create users' ),
			);
		}
		$domain = str_replace(
			array(
				'http://',
				'https://',
				'http://www.',
				'https://www.',
				'www.',
			),
			'',
			site_url()
		);

		$email          = self::$username . '@' . $domain;
		$temp_user_args = array(
			'user_email' => $email,
			'user_pass'  => self::generate_temp_password(),
			'first_name' => self::$name,
			'last_name'  => self::$last_name,
			'user_login' => self::$username,
			'role'       => self::$plugin . self::$role,
		);

		$temp_user_id = \wp_insert_user( $temp_user_args );
		if ( is_wp_error( $temp_user_id ) ) {
			$result = array(
				'success' => false,
				'message' => __( 'Cannot create user' ),
			);
		} else {
			$creation_time = \time();
			$expires       = strtotime( '+15 days', $creation_time );
			$token         = str_replace( array( '=', '&', '"', "'" ), '', \sbi_encrypt_decrypt( 'encrypt', self::generate_temp_password( 35 ) ) );

			update_user_meta( $temp_user_id, self::$plugin . '_user', $temp_user_id );
			update_user_meta( $temp_user_id, self::$plugin . '_token', $token );
			update_user_meta( $temp_user_id, self::$plugin . '_create_time', $creation_time );
			update_user_meta( $temp_user_id, self::$plugin . '_expires', $expires );

			$result = array(
				'success' => true,
				'message' => __( 'Temporary user created successfully' ),
				'user'    => self::get_user_meta_data( $temp_user_id ),
			);
		}
		return $result;
	}

	/**
	 * Delete Temp User.
	 *
	 * @param int $user_id User ID to delete.
	 *
	 * @return array
	 *
	 * @since 6.3
	 */
	public static function delete_temporary_user( $user_id ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';

		if ( ! current_user_can( 'delete_users' ) ) {
			return array(
				'success' => false,
				'message' => __( 'You don\'t have enough permission to delete users' ),
			);
		}
		if ( ! wp_delete_user( $user_id ) ) {
			return array(
				'success' => false,
				'message' => __( 'Cannot delete this user' ),
			);
		}

		return array(
			'success' => true,
			'message' => __( 'User Deleted' ),
		);
	}

	/**
	 * Get User Meta
	 *
	 * @param int $user_id User ID to retrieve metadata for.
	 *
	 * @return array|bool
	 *
	 * @since 6.3
	 */
	public static function get_user_meta_data( $user_id ) {
		$user = get_user_meta( $user_id, self::$plugin . '_user' );
		if ( ! $user ) {
			return false;
		}
		$token         = get_user_meta( $user_id, self::$plugin . '_token' );
		$creation_time = get_user_meta( $user_id, self::$plugin . '_create_time' );
		$expires       = get_user_meta( $user_id, self::$plugin . '_expires' );

		$url = self::$plugin . '_token=' . $token[0];
		return array(
			'id'            => $user_id,
			'token'         => $token[0],
			'creation_time' => $creation_time[0],
			'expires'       => $expires[0],
			'expires_date'  => self::get_expires_days( $expires[0] ),
			'url'           => admin_url( '/?' . $url ),
		);
	}

	/**
	 * Get UDays before Expiring Token
	 *
	 * @param string $expires Unix timestamp of when the token expires.
	 *
	 * @since 6.3
	 */
	public static function get_expires_days( $expires ) {
		return ceil( ( $expires - time() ) / 60 / 60 / 24 );
	}

	/**
	 * Get User By Token.
	 *
	 * @param string $token Token to connect with.
	 *
	 * @since 6.3
	 */
	public static function get_temporary_user_by_token( $token = '' ) {
		if ( empty( $token ) ) {
			return false;
		}

		$args = array(
			'fields'     => 'all',
			'meta_query' => array(
				array(
					'key'     => self::$plugin . '_token',
					'value'   => sanitize_text_field( $token ),
					'compare' => '=',
				),
			),
		);

		$users        = new \WP_User_Query( $args );
		$users_result = $users->get_results();

		if ( empty( $users_result ) ) {
			return false;
		}

		return $users_result[0];
	}

	/**
	 * Check Temporary User Created
	 *
	 * @since 6.3
	 */
	public static function check_temporary_user_exists() {
		$args         = array(
			'fields'     => 'all',
			'meta_query' => array(
				array(
					'key'     => self::$plugin . '_token',
					'value'   => null,
					'compare' => '!=',
				),
			),
		);
		$users        = new \WP_User_Query( $args );
		$users_result = $users->get_results();
		if ( empty( $users_result ) ) {
			return null;
		}
		return self::get_user_meta_data( $users_result[0]->ID );
	}

	/**
	 * Check & Delete Expired Users
	 *
	 * @since 6.3
	 */
	public static function delete_expired_users() {
		$existing_user = self::check_temporary_user_exists();
		if ( $existing_user === null ) {
			return false;
		}
		$is_expired = intval( $existing_user['expires'] ) - \time() <= 0;
		if ( ! $is_expired ) {
			return false;
		}
		require_once ABSPATH . 'wp-admin/includes/user.php';
		\wp_delete_user( $existing_user['id'] );
	}

	/**
	 * Delete Temp User
	 *
	 * @since 6.3
	 */
	public static function delete_temp_user() {
		$existing_user = self::check_temporary_user_exists();
		if ( $existing_user === null ) {
			return false;
		}
		require_once ABSPATH . 'wp-admin/includes/user.php';
		\wp_delete_user( $existing_user['id'] );
	}


	/**
	 * Register Menu.
	 *
	 * @since 6.0
	 */
	public function register_menu() {
		$role_id = self::$plugin . self::$role;
		$cap     = $role_id;
		$cap     = apply_filters( 'sbi_settings_pages_capability', $cap );

		add_submenu_page(
			'sb-instagram-feed',
			__( 'Support API tool', 'instagram-feed' ),
			__( 'Support API tool', 'instagram-feed' ),
			$cap,
			self::$plugin . '_tool',
			array( $this, 'render' ),
			5
		);
	}


	/**
	 * Generate Temp User Password
	 *
	 * @param int $length Length of password.
	 *
	 * @since 6.3
	 *
	 * @return string
	 */
	public static function generate_temp_password( $length = 20 ) {
		return wp_generate_password( $length, true, true );
	}


	/**
	 * Render the Api Tools Page
	 *
	 * @since 6.3
	 */
	public function render() {
		include_once SBI_PLUGIN_DIR . 'admin/views/support/support-tools.php';
	}

	/**
	 * Available Endpoints
	 *
	 * @since 6.3
	 *
	 * @return array
	 */
	public function available_endpoints() {
		return array(
			'https://graph.instagram.com/me',
			'https://graph.instagram.com/{user_id}/media?',
			'https://graph.facebook.com/{user_id}/stories?',
			'https://graph.facebook.com/{hashtag_id}/top_media?',
			'https://graph.facebook.com/{hashtag_id}/recent_media?',
			'https://graph.facebook.com/{user_id}/recently_searched_hashtags?',
			'https://graph.facebook.com/{user_id}/tags?',
			'https://graph.facebook.com/ig_hashtag_search?',
			'https://graph.facebook.com/{user_id}/media?',
		);
	}

	/**
	 * Validates the fields being retrieved in the API call.
	 *
	 * @param string $raw_fields Instagram API fields.
	 *
	 * @return array
	 */
	public function validate_fields( $raw_fields ) {
		$fields_array = explode( ',', $raw_fields );

		$acceptable_fields = array(
			'biography',
			'id',
			'username',
			'website',
			'followers_count',
			'media_count',
			'profile_picture_url',
			'name',
			'limit',
			'media_url',
			'media_product_type',
			'thumbnail_url',
			'caption',
			'id',
			'media_type',
			'timestamp',
			'username',
			'comments_count',
			'like_count',
			'permalink',
			'media_url',
			'id',
			'media_type',
			'timestamp',
			'permalink',
			'thumbnail_url',
		);

		$valid = array();

		foreach ( $fields_array as $field ) {
			if ( in_array( $field, $acceptable_fields, true ) ) {
				$valid[] = $field;
			}
		}

		return $valid;
	}

	/**
	 * Validates and sanitizes input from the support tool.
	 *
	 * @param array $raw_post Raw $_POST data.
	 *
	 * @return array
	 */
	public function validate_and_sanitize_support_settings( $raw_post ) {

		if ( empty( $raw_post['sb_instagram_support_connected_account'] ) ) {
			return array();
		}

		$return = array(
			'sb_instagram_support_connected_account' => sanitize_key( $raw_post['sb_instagram_support_connected_account'] ),
			'sb_instagram_support_hashtag'           => sanitize_key( $raw_post['sb_instagram_support_hashtag'] ),
			'sb_instagram_support_endpoint'          => absint( $raw_post['sb_instagram_support_endpoint'] ),
			'sb_instagram_support_fields'            => sanitize_text_field( wp_unslash( $raw_post['sb_instagram_support_fields'] ) ),
			'sb_instagram_support_children_fields'   => sanitize_text_field( wp_unslash( $raw_post['sb_instagram_support_children_fields'] ) ),
			'sb_instagram_support_limit'             => absint( $raw_post['sb_instagram_support_limit'] ),

		);

		$connected_accounts = \SB_Instagram_Connected_Account::get_all_connected_accounts();

		foreach ( $connected_accounts as $connected_account ) {
			if ( (string) $connected_account['id'] === $return['sb_instagram_support_connected_account'] ) {
				$return['access_token'] = $connected_account['access_token'];
				$return['user_id']      = (string) $connected_account['id'];
			}
		}

		$endpoints            = $this->available_endpoints();
		$return['endpoint']   = $endpoints[ $return['sb_instagram_support_endpoint'] ];
		$return['hashtag_id'] = $return['sb_instagram_support_hashtag'];

		$return['fields']          = $this->validate_fields( str_replace( ' ', '', $raw_post['sb_instagram_support_fields'] ) );
		$return['children_fields'] = $this->validate_fields( str_replace( ' ', '', $raw_post['sb_instagram_support_children_fields'] ) );

		$return['limit'] = $return['sb_instagram_support_limit'];

		return $return;
	}

	/**
	 * Create a URL to make the API request with.
	 *
	 * @param string $url API URL with placeholders that need to be replaced.
	 * @param array  $settings Settings from support tool inputs.
	 *
	 * @return string
	 */
	public function create_api_url( $url, $settings ) {
		if ( ! empty( $settings['user_id'] ) ) {
			$url = str_replace( '{user_id}', $settings['user_id'], $url );
		}
		if ( ! empty( $settings['user_id'] ) ) {
			$url = str_replace( '{hashtag_id}', $settings['hashtag_id'], $url );
		}

		$params = array();

		if ( ! empty( $settings['limit'] ) ) {
			$params['limit'] = absint( $settings['limit'] );
		}

		if ( ! empty( $settings['access_token'] ) ) {
			$params['access_token'] = sanitize_key( $settings['access_token'] );
		}

		if ( ! empty( $settings['access_token'] ) ) {
			$params['access_token'] = $settings['access_token'];
		}

		if ( ! empty( $settings['fields'] ) ) {
			$params['fields'] = implode( ',', $settings['fields'] );
		}

		if ( ! empty( $settings['children_fields'] ) ) {
			$params['fields'] .= ',children%7B' . implode( ',', $settings['children_fields'] ) . '%7D';
		}
		return add_query_arg( $params, $url );
	}
}
