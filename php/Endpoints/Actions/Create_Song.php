<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Actions;

use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Action_Command_Interface;

class Create_Song implements Action_Command_Interface {
	private int $song_id;

	public function __construct( private string $title, private int $audio_id ) {}

	/**
	 * Create a custom post type for the song.
	 *
	 * @param string $title The title of the song.
	 * @return int The ID of the song custom post type.
	 */
	private function create_cpt( string $title ): int {
		// Create the song custom post type programmatically
		$new_song = array(
			'post_title'  => $title,
			'post_type'   => 'song', // Custom post type slug for song
			'post_status' => 'publish', // You can change this if needed
			'post_author' => get_current_user_id(), // Assign to current user
		);

		// Insert the song post into the database
		$this->song_id = wp_insert_post( $new_song );
		if ( ! $this->song_id ) {
			throw new \Exception( 'Failed to create song custom post type.' );
		}
		return $this->song_id;
	}

	private function attach_audio() {
		$result = update_post_meta( $this->song_id, 'audio', $this->audio_id );
		if ( ! $result ) {
			throw new \Exception( 'Failed to attach audio to song.' );
		}
	}

	public function execute(): void {
		$this->create_cpt( $this->title );
		$this->attach_audio();
	}
}
