import { getContext, store } from '@wordpress/interactivity';
import { clearForm, setStatusMessage } from './utils';
import configs from './configs';
import { Context } from './types';
import storeDef from '../view';
import { Store } from './types';

const { apiPath, storeNamespace } = configs;

export function uploadSong( event: Event ): void {
	event.preventDefault();

	const { state } = store< Store >( storeNamespace, storeDef );
	const { fileSelected, postId } = getContext< Context >();

	const form = event.target as HTMLFormElement;
	const titleInput = form.querySelector( '#song-title' ) as HTMLInputElement;

	if ( ! fileSelected || ! titleInput?.value ) {
		setStatusMessage(
			'Please select a file and provide a song title.',
			'error',
			form
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
	fetch( `${ apiPath }/song`, {
		method: 'POST',
		headers: {
			'X-WP-Nonce': state.nonce,
		},
		body: formData, // Send the FormData object
	} )
		.then( ( response ) => {
			if ( ! response.ok ) {
				return response
					.json()
					.then( ( { message = 'An unknown error occurred' } ) => {
						throw new Error( `Error uploading song: ${ message }` );
					} );
			}
			return response.json();
		} )
		.then( ( { success, message } ) => {
			if ( success ) {
				setStatusMessage(
					'Song uploaded successfully!',
					'success',
					form
				);
				clearForm( form );
			} else {
				setStatusMessage( message, 'error', form );
			}
		} )
		.catch( ( { message } ) => {
			setStatusMessage( message, 'error', form );
		} );
}
