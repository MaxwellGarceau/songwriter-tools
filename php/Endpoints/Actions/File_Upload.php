<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Actions;

class File_Upload implements Action_Command_Interface {
	private array $uploaded_file;
	private array $file;

	public function __construct( array $file ) {
		$this->file = $file;
	}

	public function execute(): void {
		$upload_overrides = [ 'test_form' => false ];

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$this->uploaded_file = wp_handle_upload( $this->file, $upload_overrides );

		if ( isset( $this->uploaded_file['error'] ) ) {
			throw new \Exception( $this->uploaded_file['error'] );
		}
	}

	public function getUploadedFile(): array {
		return $this->uploaded_file;
	}
}
