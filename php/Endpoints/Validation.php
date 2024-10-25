<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

class Validation {

	// Multiple variants supported for cross browser compatibility
	const FILE_MIME_TYPE_SAFETY_DEFAULTS = array(
		// MP3 - https://mimetype.io/audio/mpeg
		'audio/mpeg',
		'audio/mp3',
		'audio/mpeg3',
		'audio/x-mpeg-3',

		// WAV - https://mimetype.io/audio/wav
		'audio/wav',
		'audio/x-wav',
		'audio/wave',
		'audio/vnd.wav',
		'audio/vnd.wave',
		'audio/x-pn-wav',
	);
	const MAX_FILE_SIZE_MB               = 50;  // 50MB

	// TODO: This doesn't belong here. Leaving for now, but let's revisit this.
	const UPLOAD_BLOCK_NAME = 'songwriter-tools/upload-song';

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
	 * Validating the $_FILES array
	 *
	 * Criteria
	 * - File is present
	 * - MIME type starts with "audio/"
	 * - Allowed mime types (MP3 and WAV) and their variants
	 * - File size is less than 50MB
	 *
	 * UPDATE TO VALIDATION PHILOSOPHY
	 *
	 * Previously we were accepting a post ID from the FE and
	 * matching that to get the block settings here in the BE.
	 *
	 * BE Validation logic has been changed to focus on security instead
	 * of block settings.
	 *
	 * Benefits
	 * - Validation is not duplicated in the BE and FE
	 * - BE validation can focus on what it's supposed to do, keep the
	 * site safe and avoid data integrity errors
	 * - Less potential for attackers to manipulate the system by sending
	 * a post ID to a block with looser settings
	 * - Less potential for edge case errors such as multiple upload blocks
	 * on a page
	 *
	 * @param array $file The $_FILES array
	 */
	public function audio_file( array $files ): bool|\WP_Error {
		/**
		 * TODO: Add antivirus scan here
		 *
		 * Too much lift for a small project but this would
		 * be the best way to ensure the file is safe
		 */

		// Ensure file is present and uploaded correctly
		if ( ! isset( $files['song_file'] ) || empty( $files['song_file'] ) || $files['song_file']['error'] !== UPLOAD_ERR_OK ) {
			return new \WP_Error( 'missing_file', 'File is missing or there was an error during the upload.' );
		}

		$file = $files['song_file'];

		// Validate MIME type
		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $file['tmp_name'] );
		finfo_close( $finfo );

		// Validate MIME type starts with "audio/"
		if ( strpos( $mime_type, 'audio/' ) !== 0 ) {
			return new \WP_Error( 'invalid_file_type', 'Invalid file type. Only audio files are allowed.' );
		}

		// MIME type must be in Validation whitelist
		if ( ! in_array( $mime_type, self::FILE_MIME_TYPE_SAFETY_DEFAULTS ) ) {
			return new \WP_Error( 'invalid_file_type', 'Invalid file type. Only MP3 and WAV files are allowed. Your MP3 or WAV file may not have a valid extension or may be an unrecognized variant.' );
		}

		// Validate file size (max limit 50MB)
		// Convert the max file size from MB to bytes
		$max_file_size_mb = self::MAX_FILE_SIZE_MB;
		if ( $file['size'] > $this->convert_megabytes_to_bytes( $max_file_size_mb ) ) {
			return new \WP_Error( 'file_too_large', "File size exceeds the maximum server limit of {$max_file_size_mb}MB." );
		}

		return true;
	}

	private function convert_megabytes_to_bytes( $megabytes ): float {
		return round( $megabytes * 1024 * 1024 );
	}
}
