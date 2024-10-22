<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

class Validation {

	/**
	 * Validate the song title
	 * 
	 * Not type hinting the $param variable to avoid throwing fatal error which
	 * would break validation
	 */
	public function title( $param ): bool {
		return is_string( $param ) && ! empty( $param );
	}

	/**
	 * Sending song to the BE with the FormData object
	 * Validating the $_FILES['song_file'] array
	 * 
	 * Criteria
	 * - File is present
	 * - Allowed mime types (MP3 and WAV)
	 * - File size is less than 15MB
	 */
	public function song_file(): bool|\WP_Error {
		// Ensure file is present and uploaded correctly
		if ( empty( $_FILES['song_file'] ) || $_FILES['song_file']['error'] !== UPLOAD_ERR_OK ) {
			return new \WP_Error( 'missing_file', 'File is missing or there was an error during the upload.' );
		}

		$file = $_FILES['song_file'];

		// Define allowed MIME types (e.g., MP3 and WAV)
		$allowed_mime_types = ['audio/mpeg', 'audio/wav'];

		// Validate MIME type
		$finfo = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $file['tmp_name'] );
		finfo_close( $finfo );

		if ( ! in_array( $mime_type, $allowed_mime_types ) ) {
			return new \WP_Error( 'invalid_file_type', 'Invalid file type. Only MP3 and WAV files are allowed.' );
		}

		// Validate file size (limit to 15MB)
		$max_file_size = 15 * 1024 * 1024;  // 15MB
		if ( $file['size'] > $max_file_size ) {
			return new \WP_Error( 'file_too_large', 'File size exceeds the maximum limit of 15MB.' );
		}

		// If validation passes, return true
		return true;
	}
}
