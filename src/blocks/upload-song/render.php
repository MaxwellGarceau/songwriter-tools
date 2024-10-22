<?php
/**
 * Template for rendering the "Upload Song" block on the front end.
 *
 * Variables available to the file:
 * - $attributes (array): The block attributes.
 * - $content (string): The block content.
 * - $block (WP_Block): The block instance.
 */

use Max_Garceau\Songwriter_Tools\Includes\DI_Container;
use Max_Garceau\Songwriter_Tools\Services\Nonce_Service;
$nonce_service = DI_Container::get_container()->get( Nonce_Service::class );

// Check if the user is logged in, if not return a message.
if ( ! is_user_logged_in() ) {
	echo '<p>' . esc_html__( 'You must be logged in to upload a song.', 'songwriter-tools' ) . '</p>';
	return;
}

// Generate a unique ID for the block.
$unique_id = wp_unique_id( 'song-upload-' );

// TODO: Let's find a place to put a class constant or define for this
$store_namespace = 'upload-block';

// Enqueue global state using the WordPress Interactivity API.
// Add nonce and ajax_url to the global state
wp_interactivity_state( $store_namespace, array(
	'nonce' => $nonce_service->create_nonce(),
));

$context = wp_interactivity_data_wp_context( array(
		'fileSelected' => false,
		'fileBlob'      => '',
		'title'        => '',
	),
	$store_namespace
);

?>

<div 
	<?php echo get_block_wrapper_attributes(); ?> 
	data-wp-interactive="<?php echo $store_namespace; ?>"
	<?php echo $context; ?>
>
	<h3><?php echo esc_html__( 'Upload Your Song', 'songwriter-tools' ); ?></h3>

	<form id="song-upload-form" data-wp-on--submit="actions.uploadSong">
		<input 
			type="file" 
			id="song-file" 
			accept="audio/*" 
			required 
			data-wp-on--change="actions.handleFileSelect" 
		/>
		<input 
			type="text" 
			id="song-title" 
			placeholder="<?php esc_attr_e( 'Song Title', 'songwriter-tools' ); ?>" 
			required 
			data-wp-bind--value="context.title" 
		/>

		<button type="submit" class="button button-primary">
			<?php esc_html_e( 'Upload Song', 'songwriter-tools' ); ?>
		</button>
	</form>

	<p id="song-upload-message" data-wp-bind--text="state.statusMessage"></p>
</div>
