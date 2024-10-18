<?php

namespace Max_Garceau\Songwriter_Tools;

use Max_Garceau\Songwriter_Tools\Includes\Register_Songs_CPT;
use Max_Garceau\Songwriter_Tools\Endpoints\Api;

/**
 * Load and initialize hooks for the plugin.
 */
class Songwriter_Tools {

	/**
	 * @var Register_Songs_CPT $register_songs_cpt An instance of the Register_Songs_CPT class.
	 */
	private Register_Songs_CPT $register_songs_cpt;

	/**
	 * @var Api $api An instance of the Api class.
	 */
	private Api $api;

	public function __construct(
		Register_Songs_CPT $register_songs_cpt,
		Api $api
	) {
		$this->register_songs_cpt = $register_songs_cpt;
		$this->api                = $api;
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		$this->register_hooks();
	}

	/**
	 * Register hooks for the plugin.
	 */
	private function register_hooks(): void {
		add_action( 'init', array( $this->register_songs_cpt, 'create_song_post_type' ) );
		add_action( 'rest_api_init', array( $this->api, 'register_routes' ) );
	}
}
