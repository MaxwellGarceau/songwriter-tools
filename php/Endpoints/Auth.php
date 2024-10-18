<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints;

class Auth {
	public function permission_check( \WP_REST_Request $request ): bool {
		return is_user_logged_in();
	}
}
