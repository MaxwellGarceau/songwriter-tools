import { store, getContext } from "@wordpress/interactivity";
import configs from './configs';

const { apiPath } = configs;

// TODO: Synchronize this with render.php
const storeNamespace = 'upload-block';

type ServerState = {
	state: {
		title: string;
		fileSelected: boolean;
		nonce: string;
	};
};

type Context = {
	fileSelected: boolean;
};

// Define the store
const storeDef = store( storeNamespace, {
	state: {
		title: '',
		fileSelected: false,
	},
	actions: {
		handleFileSelect,
		uploadSong,
	},
   });

// This function will handle file validation and selection
function handleFileSelect(event: Event): void {
	// TODO: Open WP Media library here...OR do we want to keep
	// it to a file URL?
	// Pros: We keep the /song endpoint, create attachment post on BE
	// and open the possibility of integration with external services
	const context = getContext<Context>();

	const fileInput = event.target as HTMLInputElement;
	const allowedTypes = ['audio/mpeg', 'audio/wav'];
	const file = fileInput?.files?.[0];

	if (!file || !allowedTypes.includes(file.type)) {
		setStatusMessage('Only audio files are allowed.', 'error');
		fileInput.value = '';  // Reset the file input
		return;
	}

	// Update the global state (context)
	context.fileSelected = true;
	console.log(`File selected: ${file.name}`);
}

// This function will handle the form submission and song upload
function uploadSong(event: Event): void {
	event.preventDefault();

	const { fileSelected } = getContext<Context>();
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
			} else {
				setStatusMessage(message, 'error');
			}
		})
		.catch(({ message }) => {
			// Catch any error thrown (including those from non-OK responses)
			setStatusMessage(message, 'error');
		});
}

// Helper function to show status messages
function setStatusMessage(message: string, status: 'success' | 'error'): void {
	const messageElement = document.getElementById('song-upload-message');
	if (messageElement) {
		messageElement.textContent = message;
		messageElement.style.color = status === 'error' ? 'red' : 'green';
	}
}

type Store = ServerState & typeof storeDef;

const { state } = store<Store>(storeNamespace, storeDef);
