import { getContext } from '@wordpress/interactivity';
import { setStatusMessage, clearStatusMessage } from './utils';
import { validateFile } from './validation';
import { Context } from './types';

export function handleFileSelect( event: Event ): void {
	const context = getContext< Context >();

	const fileInput = event.target as HTMLInputElement;
	const file = fileInput?.files?.[ 0 ];

	const form = fileInput.closest( '.song-upload-form' ) as HTMLFormElement;
	const fileSelectDisplay = form?.querySelector(
		'.song-upload-form__file-selected'
	) as HTMLFormElement;

	// Validate the file
	const validationResult = validateFile(
		file,
		context.allowedFileTypes,
		context.maxFileSize
	);
	if ( ! validationResult.isValid ) {
		setStatusMessage( validationResult.message, 'error', form );
		fileInput.value = ''; // Reset the file input
		fileSelectDisplay.textContent = 'No file chosen';
		return;
	}

	// Clear any previous status messages
	clearStatusMessage( form );

	// Set the selected file name in the UI
	fileSelectDisplay.textContent = file.name;

	// Update the global state (context)
	context.fileSelected = true;
}
