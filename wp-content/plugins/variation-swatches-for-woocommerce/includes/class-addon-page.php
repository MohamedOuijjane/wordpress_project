<?php
if ( ! class_exists( 'VSWC_Addons_Page' ) ) {
	class VSWC_Addons_Page {

		/**
		 * Get all available modules.
		 *
		 * @return array Array of available modules.
		 */
		public function woosuite_core_get_modules() {
			$request = wp_remote_request( $this->woosuite_core_get_api_url( '/plugins/' ) );

			if ( is_wp_error( $request ) ) {
				return array();
			}

			$body = json_decode( wp_remote_retrieve_body( $request ) );

			if ( ! empty( $body->data ) && ! empty( $body->data->status ) && 200 !== $body->data->status ) {
				return array();
			}

			// Exclude core plugin to be displayed here.
			if ( ! is_wp_error( $body ) ) {
				$body = wp_list_filter( $body, array( 'slug' => 'woosuite-core' ), 'NOT' );
			}

			return $body;
		}

		private function woosuite_core_get_api_url( $path = '' ) {
			return apply_filters( 'woosuite_core_api_url', $this->woosuite_core_get_api_site_url() . 'wp-json/woosuite-server/v1' ) . $path;
		}

		private function woosuite_core_get_api_site_url() {
			return apply_filters( 'woosuite_core_get_api_site_url', 'https://dw.woosuite.com/' );
		}
	}
}