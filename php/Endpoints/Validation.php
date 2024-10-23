<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

class Validation {

	const FILE_RESTRICTION_DEFAULTS = array(
		'allowed_mime_types' => array( 'audio/mpeg', 'audio/wav' ),
		'max_file_size_mb'   => 15,  // 15MB
	);

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
	 * - Allowed mime types (MP3 and WAV)
	 * - File size is less than 15MB
	 *
	 * @param array $file The $_FILES array
	 */
	public function audio_file( array $files ): bool|\WP_Error {
		// Ensure file is present and uploaded correctly
		if ( ! isset( $files['song_file'] ) || empty( $files['song_file'] ) || $files['song_file']['error'] !== UPLOAD_ERR_OK ) {
			return new \WP_Error( 'missing_file', 'File is missing or there was an error during the upload.' );
		}

		$file = $files['song_file'];

		// Get file restrictions from the Song Upload block via the
		// post/page the request was submitted from
		$file_restrictions = $this->get_file_restrictions( $file );

		// Validate MIME type
		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $file['tmp_name'] );
		finfo_close( $finfo );

		if ( ! in_array( $mime_type, $file_restrictions['allowed_mime_types'] ) ) {
			return new \WP_Error( 'invalid_file_type', 'Invalid file type. Only MP3 and WAV files are allowed.' );
		}

		// Validate file size (limit to 15MB)
		// Convert the max file size from MB to bytes
		$max_file_size_mb = $file_restrictions['max_file_size_mb'];
		if ( $file['size'] > $this->convert_megabytes_to_bytes( $max_file_size_mb ) ) {
			return new \WP_Error( 'file_too_large', "File size exceeds the maximum limit of {$max_file_size_mb}MB." );
		}

		return true;
	}

	/**
	 * Get file restrictions from the Song Upload block in whatever post/page
	 * this request was sent from
	 *
	 * Fallback to default restrictions if block or attributes aren't found
	 * Example: The rest request comes from another music service
	 *
	 * On a sidenote, what a mouthful this method is
	 * Good thing it's in here and not in our controller or audio_file validation method =)
	 *
	 * @param array $file The $_FILES array
	 * @return array The file restrictions
	 */
	private function get_file_restrictions( array $file ): array {
		$file_restrictions = self::FILE_RESTRICTION_DEFAULTS;

		// Sanitize post ID if available
		$post_id = isset( $file['post_id'] ) ? absint( $file['post_id'] ) : null;
		if ( ! $post_id ) {
			return $file_restrictions;
		}

		// Retrieve post object and validate it
		$post = get_post( $post_id );
		if ( ! is_a( $post, \WP_Post::class ) ) {
			return $file_restrictions;
		}

		// Parse blocks in post content
		$blocks = parse_blocks( $post->post_content );
		foreach ( $blocks as $block ) {
			if ( $block['blockName'] === self::UPLOAD_BLOCK_NAME ) {
				$attrs = $block['attrs'] ?? array();

				// Get file restrictions from the block, with fallback to defaults
				return array(
					'allowed_mime_types' => $attrs['allowedMimeTypes'] ?? self::FILE_RESTRICTION_DEFAULTS['allowed_mime_types'],
					'max_file_size_mb'   => $attrs['maxFileSize'] ?? self::FILE_RESTRICTION_DEFAULTS['max_file_size_mb'],
				);
			}
		}

		// Return defaults if block or attributes aren't found
		return $file_restrictions;
	}

	private function convert_megabytes_to_bytes( $megabytes ): float {
		return round( $megabytes * 1024 * 1024 );
	}
}
