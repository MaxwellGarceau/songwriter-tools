<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Controllers;

interface Storeable {
	public function store( \WP_REST_Request $request ): \WP_REST_Response;
}
