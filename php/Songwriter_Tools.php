<?php

namespace Max_Garceau\Songwriter_Tools;

use Max_Garceau\Songwriter_Tools\Includes\Register_Songs_CPT;

/**
 * Load and initialize hooks for the plugin.
 */
class Songwriter_Tools {

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
		$register_songs_cpt = new Register_Songs_CPT();
		add_action( 'init', array( $register_songs_cpt, 'create_song_post_type' ) );
	}
}
