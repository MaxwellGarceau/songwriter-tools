<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

use Max_Garceau\Songwriter_Tools\Endpoints\Auth;
use Max_Garceau\Songwriter_Tools\Endpoints\Validation;
use Max_Garceau\Songwriter_Tools\Endpoints\Controllers\Song_Controller;
use Max_Garceau\Songwriter_Tools\Endpoints\Sanitization;

class Api {

	const NAMESPACE = 'songwriter-tools/v1';

	/**
	 * TODO: It's important that we don't unnecessarily couple the blocks
	 * to a single backend.
	 *
	 * The song route is okay here because that route is specific to the theme.
	 *
	 * The upload-song button can be moved to another project with minimal tweaks
	 * and be refactored to more generically accept user uploads
	 * (e.g. images, videos, audio, etc).
	 *
	 * However, making a note here to check in on future routes added here for
	 * long term app health.
	 *
	 * @param Auth $auth
	 * @param Validation $validation
	 * @param Sanitization $sanitization
	 * @param Song_Controller $song_controller
	 */
	public function __construct(
		private Auth $auth,
		private Validation $validation,
		private Sanitization $sanitization,
		private Song_Controller $song_controller
	) {
		$this->auth            = $auth;
		$this->validation      = $validation;
		$this->song_controller = $song_controller;
		$this->sanitization    = $sanitization;
	}

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/song',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this->song_controller, 'store' ),
				'permission_callback' => array( $this->auth, 'permission_check' ),
				'args'                => array(
					'title' => array(
						'required'          => true,
						'validate_callback' => array( $this->validation, 'title' ),
						'sanitize_callback' => array( $this->sanitization, 'sanitize_input_text' ),
					),
				),
			)
		);

		register_rest_route(
			self::NAMESPACE,
			'/health-check',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => fn() => 'Health check passed!',
				'permission_callback' => array( $this->auth, 'permission_check' ),
			)
		);
	}
}
