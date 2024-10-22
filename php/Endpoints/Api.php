<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

use Max_Garceau\Songwriter_Tools\Endpoints\Auth;
use Max_Garceau\Songwriter_Tools\Endpoints\Validation;
use Max_Garceau\Songwriter_Tools\Endpoints\Controllers\Song_Controller;

class Api {

	/**
	 * @var Auth $auth An instance of the Auth class.
	 */
	private Auth $auth;

	/**
	 * @var Validation $validation An instance of the Validation class.
	 */
	private Validation $validation;

	/**
	 * @var Song_Controller $song_controller An instance of the Song_Controller class.
	 */
	private Song_Controller $song_controller;

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
	 */
	public function __construct(
		Auth $auth,
		Validation $validation,
		Song_Controller $song_controller
	) {
		$this->auth            = $auth;
		$this->validation      = $validation;
		$this->song_controller = $song_controller;
	}

	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			'/song',
			[
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => [ $this->song_controller, 'store' ],
				'permission_callback' => [ $this->auth, 'permission_check' ],
				'args'                => [
					'title' => [
						'required'          => true,
						'validate_callback' => [ $this->validation, 'validate_title' ],
					],
					'meta'  => [
						'required'          => true,
						'validate_callback' => [ $this->validation, 'validate_meta' ],
					],
				],
			]
		);

		register_rest_route(
			self::NAMESPACE,
			'/health-check',
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => fn() => 'Health check passed!',
				'permission_callback' => [ $this->auth, 'permission_check' ],
			]
		);
	}
}
