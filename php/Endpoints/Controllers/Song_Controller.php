<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Controllers;

use Max_Garceau\Songwriter_Tools\Endpoints\Controllers\Storeable;
use Max_Garceau\Songwriter_Tools\Services\Nonce_Service;

class Song_Controller implements Storeable {

	public function __construct(
		private readonly Nonce_Service $nonce_service
	) {}

	public function store( $request ): \WP_REST_Response|\WP_Error {
		// Get non-file parameters like the song title
		$params = $request->get_params();
		$title = sanitize_text_field( $params['title'] );

		$file = $_FILES['song_file'];

		// Now handle the file upload
		$upload_overrides = [ 'test_form' => false ];

		// Require wp_handle_upload if we don't have it
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$uploaded_file = wp_handle_upload( $file, $upload_overrides );

		if ( isset( $uploaded_file['error'] ) ) {
			return new \WP_Error( 'upload_error', $uploaded_file['error'], [ 'status' => 500 ] );
		}

		// Create an attachment in WordPress media library
		$attachment = [
			'guid'           => $uploaded_file['url'],
			'post_mime_type' => $uploaded_file['type'],
			'post_title'     => $title,
			'post_content'   => '',
			'post_status'    => 'inherit'
		];

		$attachment_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );

		if ( ! is_wp_error( $attachment_id ) ) {

			// Generate metadata and update the database
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $uploaded_file['file'] );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			// TODO: If Song CPT exists then create a new song post

			return new \WP_REST_Response( [
				'message' => 'Song uploaded successfully!',
				'attachment_id' => $attachment_id,
				'file_url' => wp_get_attachment_url( $attachment_id )
			], 200 );
		}

		return new \WP_Error( 'attachment_error', 'Failed to insert attachment into the media library.', [ 'status' => 500 ] );
	}
}
