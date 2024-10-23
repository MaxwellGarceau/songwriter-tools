import { store, getContext } from "@wordpress/interactivity";
import configs from './configs';

const { apiPath } = configs;

// TODO: Synchronize this with render.php
const storeNamespace = 'upload-block';

type ServerState = {
	state: {
		nonce: string;
	};
};

type Context = {
	fileSelected: boolean;
	allowedFileTypes: string[];
	maxFileSize: number;
	postId: number;
};

// Define the store
const storeDef = store( storeNamespace, {
	actions: {
		handleFileSelect,
		uploadSong,
	},
   });

// This function will handle file validation and selection
function handleFileSelect(event: Event): void {
	const context = getContext<Context>();

	const fileInput = event.target as HTMLInputElement;
	const file = fileInput?.files?.[0];

	if (!file || !context.allowedFileTypes.includes(file.type)) {
		setStatusMessage(`Allowed file types: ${context.allowedFileTypes.join('|')}`, 'error');
		fileInput.value = '';  // Reset the file input
		return;
	}

	if (getSizeInMb(file.size) > context.maxFileSize) {
		setStatusMessage(`Allowed file size: ${context.maxFileSize}MB`, 'error');
		fileInput.value = '';  // Reset the file input
		return;
	}

	// Update the global state (context)
	context.fileSelected = true;
}

// This function will handle the form submission and song upload
function uploadSong(event: Event): void {
	event.preventDefault();

	const { fileSelected, postId } = getContext<Context>();
	const titleInput = document.getElementById('song-title') as HTMLInputElement;

	if (!fileSelected || !titleInput?.value) {
		setStatusMessage('Please select a file and provide a song title.', 'error');
		return;
	}

	// Create a FormData object to send the actual file
	const formData = new FormData();
	const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement;
	const file = fileInput.files?.[0];

	// Add form fields
	formData.append('title', titleInput.value);  // Song title
	formData.append('song_file', file);          // The actual file
	formData.append('post_id', postId ? postId.toString() : null);  // Default to false, we have fallback in REST endpoint

	// Send a POST request to the REST API to create the new post
	fetch(`${apiPath}/song`, {
		method: 'POST',
		headers: {
			'X-WP-Nonce': state.nonce,
		},
		body: formData,  // Send the FormData object
	})
		// Check if response succeeded
		.then(response => {
			// If response is not OK, handle the error and get the message from the body
			if (!response.ok) {
				// Display BE error to FE
				return response.json().then(({ message = 'An unknown error occurred' }) => {
					// Throw an error with the message
					throw new Error(`Error uploading song: ${message}`);
				});
			}
			// If the response is OK, proceed to parse the JSON
			return response.json();
		})
		.then(({ success, message }) => {
			// Check if song upload was successful
			if (success) {
				setStatusMessage('Song uploaded successfully!', 'success');
				clearForm();
			} else {
				setStatusMessage(message, 'error');
			}
		})
		.catch(({ message }) => {
			// Catch any error thrown (including those from non-OK responses)
			setStatusMessage(message, 'error');
		});
}

function clearForm(): void {
	const titleInput = document.getElementById('song-title') as HTMLInputElement;
	const fileInput = document.querySelector('input[type="file"]') as HTMLInputElement;

	titleInput.value = '';
	fileInput.value = '';
}

// Helper function to show status messages
function setStatusMessage(message: string, status: 'success' | 'error'): void {
	const messageElement = document.getElementById('song-upload-message');
	if (messageElement) {
		messageElement.textContent = message;
		messageElement.style.color = status === 'error' ? 'red' : 'green';
	}
}

function getSizeInMb(sizeInBytes: number): number {
	const sizeInMbFloat = sizeInBytes / (1024 * 1024);
	// Round to 2 decimal places
	return Math.round((sizeInMbFloat) * 100) / 100;
}

type Store = ServerState & typeof storeDef;

const { state } = store<Store>(storeNamespace, storeDef);
