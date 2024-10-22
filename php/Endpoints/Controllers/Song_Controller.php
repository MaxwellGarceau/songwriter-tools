<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Controllers;

use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Get_Title;
use Max_Garceau\Songwriter_Tools\Endpoints\Actions\File_Upload;
use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Create_Attachment;
use Max_Garceau\Songwriter_Tools\Endpoints\Actions\Generate_Metadata;
use Max_Garceau\Songwriter_Tools\Services\Nonce_Service;
use WP_REST_Request;

class Song_Controller implements Storeable {

	public function __construct(
		private readonly Nonce_Service $nonce_service
	) {}

	public function store( WP_REST_Request $request ): \WP_REST_Response|\WP_Error {
		try {
			// Action to get and sanitize the title
			$get_title = new Get_Title($request);
			$get_title->execute();
			$title = $get_title->getTitle();

			// Action to handle file upload
			$file_upload = new File_Upload($_FILES['song_file']);
			$file_upload->execute();
			$uploaded_file = $file_upload->getUploadedFile();

			// Action to create an attachment
			$create_attachment = new Create_Attachment($title, $uploaded_file);
			$create_attachment->execute();
			$attachment_id = $create_attachment->getAttachmentId();

			// Action to generate and update metadata
			$generate_metadata = new Generate_Metadata($attachment_id, $uploaded_file['file']);
			$generate_metadata->execute();

			return new \WP_REST_Response( [
				'message' => 'Song uploaded successfully!',
				'attachment_id' => $attachment_id,
				'file_url' => wp_get_attachment_url($attachment_id)
			], 200 );

		} catch (\Exception $e) {
			return new \WP_Error('error', $e->getMessage(), ['status' => 500]);
		}
	}
}
