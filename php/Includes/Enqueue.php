<?php

declare( strict_types = 1 );

namespace Max_Garceau\Songwriter_Tools\Includes;

use Max_Garceau\Songwriter_Tools\Services\Nonce_Service;

class Enqueue {

	private const JS_SCRIPTS_HANDLE = 'songwriter-tools-scripts';
	private const JS_OBJECT_NAME    = 'songwriterToolsAjax';

	/**
	 * @param Nonce_Service $nonce_service
	 */
	public function __construct( private readonly Nonce_Service $nonce_service ) {}

	public function localize_scripts(): void {
		wp_localize_script(
			self::JS_SCRIPTS_HANDLE,
			self::JS_OBJECT_NAME,
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $this->nonce_service->create_nonce(),
			]
		);
	}
}
