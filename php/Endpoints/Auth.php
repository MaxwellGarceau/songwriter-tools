<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

use Max_Garceau\Songwriter_Tools\Services\Nonce_Service;
use Max_Garceau\Songwriter_Tools\Services\Nonce_Status;
use Monolog\Logger;

class Auth {

	/**
	 * @param Nonce_Service $nonce_service
	 * @param Logger $logger
	 */
	public function __construct(
		private readonly Nonce_Service $nonce_service,
		private readonly Logger $logger
	) {}

	public function permission_check( \WP_REST_Request $request ): bool {
		$nonce = $request->get_header( 'X-WP-Nonce' );

		/**
		 * Verify nonce
		 *
		 * To be honest, this isn't actually needed.
		 * The request will succeed as long as the user has the 'wp_rest' nonce
		 * in their X-WP-Nonce header.
		 */
		if ( $this->nonce_service->verify_nonce( $nonce ) === Nonce_Status::INVALID ) {
			$this->logger->error( 'Invalid or expired nonce.' );
			return new \WP_REST_Response(
				array(
					'message' => 'Invalid or expired nonce.',
				),
				403
			);
		}

		return is_user_logged_in();
	}
}
