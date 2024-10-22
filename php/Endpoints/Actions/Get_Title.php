<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Actions;

use WP_REST_Request;

class Get_Title implements Action_Command_Interface {
	private string $title;

	public function __construct( private WP_REST_Request $request ) {}

	public function execute(): void {
		$params      = $this->request->get_params();
		$this->title = sanitize_text_field( $params['title'] );
	}

	public function getTitle(): string {
		return $this->title;
	}
}
