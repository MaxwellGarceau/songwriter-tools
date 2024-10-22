<?php

namespace Max_Garceau\Songwriter_Tools\Includes;

class Register_Songs_CPT {
	public function create_song_post_type(): void {
		register_post_type(
			'song',
			array(
				'labels'       => array(
					'name'          => __( 'Songs' ),
					'singular_name' => __( 'Song' ),
				),
				'public'       => true,
				'supports'     => array( 'title', 'editor', 'custom-fields' ),
				'has_archive'  => true,
				'show_in_rest' => true,  // Enable REST API support
				'menu_icon'    => 'dashicons-format-audio',
			)
		);
	}
}
