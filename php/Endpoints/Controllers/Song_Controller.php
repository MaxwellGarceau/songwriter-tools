<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Controllers;

use Max_Garceau\Songwriter_Tools\Endpoints\Controllers\Storeable;

class Song_Controller implements Storeable {
	public function store( \WP_REST_Request $request ): \WP_REST_Response {
		$params = $request->get_params();

		// Ensure we have the necessary data
		if ( ! isset( $params['meta']['song_file'] ) ) {
			return new \WP_REST_Response(
				[
					'success' => false,
					'message' => __( 'Song file URL is missing.', 'songwriter-tools' ),
				],
				400
			);
		}

		// Sanitize input
		// TODO: Move these into Api.php as sanitize_callback arguments
		// TODO: Move the sanitization logic into a new Sanitization class
		// TODO: Sanitize at the start with $request->sanitize_params()
		$post_title    = sanitize_text_field( $params['title'] );
		$song_file_url = esc_url_raw( $params['meta']['song_file'] );

		// Create new post of custom post type "song"
		$post_id = wp_insert_post(
			[
				'post_title'  => $post_title,
				'post_type'   => 'song',
				'post_status' => 'publish',
				'meta_input'  => [
					'song_file' => $song_file_url,
				],
			]
		);

		if ( is_wp_error( $post_id ) ) {
			return new \WP_REST_Response(
				[
					'success' => false,
					'message' => __( 'Error creating post.', 'songwriter-tools' ),
				],
				500
			);
		}

		return new \WP_REST_Response(
			[
				'success' => true,
				'post_id' => $post_id,
			],
			200
		);
	}
}
