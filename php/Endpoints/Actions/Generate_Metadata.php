<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Actions;

class Generate_Metadata implements Action_Command_Interface {
	public function __construct( private int $attachment_id, private string $file_path ) {}

	public function execute(): void {
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_data = wp_generate_attachment_metadata( $this->attachment_id, $this->file_path );
		wp_update_attachment_metadata( $this->attachment_id, $attachment_data );
	}
}
