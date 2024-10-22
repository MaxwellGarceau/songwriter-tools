<?php

namespace Max_Garceau\Songwriter_Tools;

use Max_Garceau\Songwriter_Tools\Includes\Hook_Manager;
use Max_Garceau\Songwriter_Tools\Includes\Register_Songs_CPT;
use Max_Garceau\Songwriter_Tools\Endpoints\Api;
use Max_Garceau\Songwriter_Tools\Includes\Enqueue;

/**
 * Load and initialize hooks for the plugin.
 */
class Songwriter_Tools {

	/**
	 * @var Hook_Manager $hook_manager An instance of the Hook_Manager class.
	 * @var Enqueue $enqueue An instance of the Enqueue class.
	 * @var Register_Songs_CPT $register_songs_cpt An instance of the Register_Songs_CPT class.
	 * @var Api $api An instance of the Api class.
	 */
	public function __construct(
		public readonly Hook_Manager $hook_manager,
		public readonly Enqueue $enqueue,
		public readonly Register_Songs_CPT $register_songs_cpt,
		public readonly Api $api
	) {}

	/**
	 * Initializes the plugin
	 */
	public function init() {
		// Init WP Hooks
		$this->hook_manager->add_actions( $this );
	}
}
