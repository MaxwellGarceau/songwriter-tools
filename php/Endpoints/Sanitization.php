<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

class Sanitization {
	/**
	 * Sanitize text input using WordPress helper functions.
	 *
	 * @param string $text The raw text.
	 * @return string The sanitized text.
	 */
	function sanitize_input_text( $text ) {
		// Strip HTML tags and encode special characters
		$sanitized_title = wp_strip_all_tags( $text );

		// Remove any unwanted characters, allowing only alphanumeric characters, spaces, and punctuation
		$sanitized_title = remove_accents( $sanitized_title );

		// Strip non-printable control characters
		$sanitized_title = preg_replace( '/[\x00-\x1F\x7F]/', '', $sanitized_title );

		// Allow only alphanumeric characters, spaces, and common punctuation
		$sanitized_title = preg_replace( '/[^a-zA-Z0-9\s\-\'\!\.\,\?]/', '', $sanitized_title );

		// Trim any excess whitespace from the beginning or end of the string.
		return trim( $sanitized_title );
	}
}
