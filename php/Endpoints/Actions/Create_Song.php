<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Actions;

use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Action_Command_Interface;

class Create_Song implements Action_Command_Interface {
	private int $song_id;
	private string $type = 'song';

	public function __construct( private string $title, private int $audio_id ) {}

	/**
	 * Create a custom post type for the song.
	 *
	 * @param string $title The title of the song.
	 * @return int The ID of the song custom post type.
	 * @throws \Exception
	 */
	private function create_cpt( string $title ): int {
		// Create the song custom post type programmatically
		$new_song = array(
			'post_title'   => $title,
			'post_content' => '', // TODO: Add lyrics here
			'post_type'    => $this->type,
			'post_status'  => 'publish',

			// Assign to current user - would need to handle this if API opens up
			'post_author'  => get_current_user_id(),
		);

		// Insert the song post into the database
		$this->song_id = wp_insert_post( $new_song );
		if ( ! $this->song_id ) {
			throw new \Exception( "Failed to create {$this->type} post type." );
		}
		return $this->song_id;
	}

	/**
	 * Attach the audio file to the song custom post type.
	 *
	 * This humble method is more significant than meets the eye. We are making
	 * a very important decision about how our CPT will associate with audio
	 * related functionality.
	 *
	 * Primary association: Associate the CPT with the audio attachment ID
	 * - Integration with WP media management system. Audio
	 *   attachments can be extended, customized, and used
	 *   independently of how they are used in the CPT.
	 * - Encapsulates audio logic in the audio attachment post.
	 *   Better for modifications and future proofing.
	 *
	 * Secondary association: audio_url field fallback for loading audio
	 * - Allows the possibility of receiving audio from external data sources
	 *
	 * PLAN:
	 * 1. Prioritize the audio_url post_meta field for loading audio
	 * 2. If audio_url is not set, query the url by the audio attachment ID
	 *
	 * @return void
	 * @throws \Exception
	 */
	private function attach_audio() {
		$result = update_post_meta( $this->song_id, 'audio', $this->audio_id );
		if ( ! $result ) {
			throw new \Exception( "Failed to attach audio to {$this->type}." );
		}
	}

	public function execute(): void {
		$this->create_cpt( $this->title );
		$this->attach_audio();
	}

	public function get_song_id(): int {
		return $this->song_id;
	}
}
