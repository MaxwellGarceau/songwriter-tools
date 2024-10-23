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
wp_interactivity_state(
	$store_namespace,
	array(
		'nonce' => $nonce_service->create_nonce(),
	)
);

$context = wp_interactivity_data_wp_context(
	array(
		'fileSelected'     => false,
		'allowedFileTypes' => $attributes['allowedMimeTypes'],
		'maxFileSize'      => $attributes['maxFileSize'],
		'postId'           => get_the_ID(),
	),
	$store_namespace
);

$allowed_file_types_labels = array_map( fn( $f ) => strtoupper( ltrim( $f, 'audio/' ) ), $attributes['allowedMimeTypes'] );
$allowed_file_types_string = implode( '|', $allowed_file_types_labels );

?>

<section 
	<?php echo get_block_wrapper_attributes(); ?> 
	data-wp-interactive="<?php echo $store_namespace; ?>"
	<?php echo $context; ?>
>
	<<?php echo $attributes['headingTag']; ?> <?php if ( isset( $attributes['headingFontSize'] ) ) { echo 'style="font-size:' . $attributes['headingFontSize'] . '"'; } ?>><?php echo esc_html__( $attributes['headingContent'], 'songwriter-tools' ); ?></<?php echo $attributes['headingTag']; ?>>

	<form id="song-upload-form" class="song-upload-form wp-block-form" data-wp-on--submit="actions.uploadSong">
		<div class="song-upload-form__column">
			<span class="song-upload-form__label"><span class="song-upload-form__label-content">Allowed file types: <?php echo $allowed_file_types_string; ?> (max <?php echo $attributes['maxFileSize']; ?>MB)</span></span>
			<label for="song-file" class="song-upload-form__input-file-button wp-block-button">
				<span class="button button-primary wp-block-button__link wp-element-button">Select song</span>
			</label>
			<span class="song-upload-form__file-selected">No file chosen</span>
			<input 
				class="song-upload-form__input song-upload-form__input-file wp-block-form-input"
				type="file" 
				id="song-file" 
				accept="audio/*" 
				required 
				data-wp-on--change="actions.handleFileSelect" 
				aria-label="Choose a song file. Only MP3 or WAV files less than 15MB are allowed."
			/>
		</div>
		<div class="song-upload-form__column">
			<label for="song-title" class="song-upload-form__label wp-block-form-input__label"><span class="song-upload-form__label-content wp-block-form-input__label-content"><?php esc_html_e( 'Song Title', 'songwriter-tools' ); ?></span></label>
			<input 
				class="song-upload-form__input song-upload-form__input-text wp-block-form-input"
				type="text" 
				id="song-title" 
				placeholder="<?php esc_attr_e( 'Song Title', 'songwriter-tools' ); ?>" 
				required 
				data-wp-bind--value="context.title" 
			/>
		</div>
		<div class="wp-block-button">
			<button type="submit" class="button button-primary wp-block-button__link wp-element-button">
				<?php esc_html_e( 'Upload Song', 'songwriter-tools' ); ?>
			</button>
		</div>
	</form>

	<p id="song-upload-message" class="wp-block-status-message" data-wp-bind--text="state.statusMessage"></p>
</section>
