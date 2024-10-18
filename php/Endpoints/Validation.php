<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

class Validation {

	public function validate_title( $param, \WP_REST_Request $request, $key ): bool {
		return is_string( $param ) && ! empty( $param );
	}

	public function validate_meta( $param, \WP_REST_Request $request, $key ): bool {
		return isset( $param['song_file'] ) && filter_var( $param['song_file'], FILTER_VALIDATE_URL );
	}
}
