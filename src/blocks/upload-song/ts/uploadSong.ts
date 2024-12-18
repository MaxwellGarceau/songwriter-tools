import { getContext, store } from '@wordpress/interactivity';
import FormHelper from './formHelper';
import configs from './configs';
import { Context } from './types';
import storeDef from '../view';
import { Store } from './types';

const { storeNamespace, apiNamespacePath } = configs;

export function uploadSong( event: Event ): void {
	event.preventDefault();

	const { state } = store< Store >( storeNamespace, storeDef );
	const { fileSelected, postId } = getContext< Context >();

	const form = event.target as HTMLFormElement;

	const formHelper = new FormHelper( form );

	const titleInput = form.querySelector( '#song-title' ) as HTMLInputElement;

	if ( ! fileSelected || ! titleInput?.value ) {
		formHelper.setStatusMessage(
			'Please select a file and provide a song title.',
			'error'
		);
		return;
	}

	// Create a FormData object to send the actual file
	const formData = new FormData();
	const fileInput = form.querySelector(
		'.song-upload-form__input-file'
	) as HTMLInputElement;
	const file = fileInput.files?.[ 0 ];

	// Add form fields
	formData.append( 'title', titleInput.value ); // Song title
	formData.append( 'song_file', file ); // The actual file
	formData.append( 'post_id', postId ? postId.toString() : null ); // Default to false, we have fallback in REST endpoint

	// Send a POST request to the REST API to create the new post
	window.wp
		.apiFetch( {
			path: `${ apiNamespacePath }/song`,
			method: 'POST',
			headers: {
				'X-WP-Nonce': state.nonce,
			},
			body: formData, // Send the FormData object
		} )
		.then( ( { success, message } ) => {
			// Request succeeded - handle outcome
			if ( success ) {
				formHelper.setStatusMessage(
					'Song uploaded successfully!',
					'success'
				);
				formHelper.clearForm();
			} else {
				formHelper.setStatusMessage( message, 'error' );
			}
		} )
		.catch( ( { message } ) => {
			// Server request failed
			formHelper.setStatusMessage( message, 'error' );
		} );
}
