<?php

declare( strict_types = 1 );

namespace Max_Garceau\Songwriter_Tools\Includes;

use Max_Garceau\Songwriter_Tools\Songwriter_Tools;

class Hook_Manager {
	public function add_actions( Songwriter_Tools $main ): void {
		add_action( 'init', array( $main->register_songs_cpt, 'create_song_post_type' ) );
		add_action( 'rest_api_init', array( $main->api, 'register_routes' ) );
	}
}
