<?php

declare( strict_types = 1 );

namespace Max_Garceau\Songwriter_Tools\Includes;

use Max_Garceau\Songwriter_Tools\Songwriter_Tools;

class Hook_Manager {
	public function add_actions( Songwriter_Tools $main ): void {
		add_action( 'init', array( $main->register_songs_cpt, 'create_song_post_type' ) );
		add_action( 'rest_api_init', array( $main->api, 'register_routes' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_wp_fetch' ) );
	}

	/**
	 * Enqueuing the wp-api-fetch script because modules and scripts
	 * were not compatible at the time of development.
	 *
	 * Workaround as described below.
	 * https://make.wordpress.org/core/2024/03/04/script-modules-in-6-5/
	 *
	 * TODO: When wp-api-fetch is compatible with the module system then
	 * import in upload-song/ts/uploadSong.ts.
	 *
	 * Putting here directly in hook manager because this is a temporary fix.
	 */
	public function enqueue_wp_fetch(): void {
		wp_enqueue_script( 'wp-api-fetch' );
	}
}
