<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Controllers;

use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Get_Title;
use Max_Garceau\Songwriter_Tools\Endpoints\Actions\File_Upload;
use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Create_Attachment;
use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Generate_Metadata;
use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Create_Song;
use Max_Garceau\Songwriter_Tools\Endpoints\Validation;
use Monolog\Logger;
use WP_REST_Request;

class Song_Controller implements Storeable {

	public function __construct(
		private readonly Validation $validation,
		private readonly Logger $logger,
	) {}

	public function store( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		try {
			/**
			 * Validate the song_file
			 * Doing this here because WP isn't set up to handle file uploads
			 * in the args parameter of register_rest_route
			 *
			 * Sanitization is being handled in the wp_handle_upload in the File_Upload action
			 * You can see for yourself here:
			 * https://github.com/WordPress/wordpress-develop/blob/b42f5f95417413ee6b05ef389e21b3a0d61d3370/src/wp-admin/includes/file.php#L802-L1075
			 */
			$result = $this->validation->audio_file( $_FILES );
			if ( is_wp_error( $result ) ) {
				return new \WP_REST_Response(
					array(
						'error_code' => $result->get_error_code(),
						'message'    => $result->get_error_message(),
					),
					400
				);
			}

			// Action to get and sanitize the title
			$get_title = new Get_Title( $request );
			$get_title->execute();
			$title = $get_title->getTitle();

			// Action to handle file upload
			$file_upload = new File_Upload( $_FILES['song_file'] );
			$file_upload->execute();
			$uploaded_file = $file_upload->getUploadedFile();

			// Action to create an attachment
			$create_attachment = new Create_Attachment( $title, $uploaded_file );
			$create_attachment->execute();
			$attachment_id = $create_attachment->getAttachmentId();

			// Action to generate and update metadata
			$generate_metadata = new Generate_Metadata( $attachment_id, $uploaded_file['file'] );
			$generate_metadata->execute();

			// Action to create a song custom post type
			$create_song = new Create_Song( $title, $attachment_id );
			$create_song->execute();
			$song_id = $create_song->get_song_id();

			// Yay we made it!
			return new \WP_REST_Response(
				array(
					'message'       => 'Song uploaded successfully!',
					'attachment_id' => $attachment_id,
					'song_id'       => $song_id,
					'file_url'      => wp_get_attachment_url( $attachment_id ),
					'success'       => true,
				),
				200
			);

		} catch ( \Exception $e ) {
			return new \WP_Error( 'error', $e->getMessage(), array( 'status' => 500 ) );
		}
	}
}
