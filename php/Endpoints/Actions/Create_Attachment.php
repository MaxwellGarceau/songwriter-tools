<?php

namespace Max_Garceau\Songwriter_Tools\Endpoints\Actions;

class Create_Attachment implements Action_Command_Interface {
	private int $attachment_id;

	public function __construct( private string $title, private array $uploaded_file ) {}

	public function execute(): void {
		$attachment = [
			'guid'           => $this->uploaded_file['url'],
			'post_mime_type' => $this->uploaded_file['type'],
			'post_title'     => $this->title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		];

		$this->attachment_id = wp_insert_attachment( $attachment, $this->uploaded_file['file'] );

		if ( is_wp_error( $this->attachment_id ) ) {
			throw new \Exception( 'Failed to insert attachment into the media library.' );
		}
	}

	public function getAttachmentId(): int {
		return $this->attachment_id;
	}
}
