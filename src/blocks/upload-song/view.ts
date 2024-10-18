import { store, getContext } from "@wordpress/interactivity";

// TODO: Syncronize this with render.php
const storeNamespace = 'upload-block';

type ServerState = {
	state: {
		title: string;
		fileSelected: boolean;
		fileUrl: string;
	};
};

type Context = {
	fileSelected: boolean;
	fileUrl: string;
};


const storeDef = store( storeNamespace, {
	state: {
		title: '',
		fileSelected: false,
		fileUrl: '',
	},
	 actions: {
		handleFileSelect,
		uploadSong,
		test: () => console.log('test')
	 },
	//  callbacks: {
	// 	//
	//  },
   });

// This function will handle file validation and selection.
function handleFileSelect(event: Event): void {
	console.log('handleFileSelect');

	const context = getContext< Context >();

	// TODO: Open WP Media library here

    const fileInput = event.target as HTMLInputElement;
    const allowedTypes = ['audio/mpeg', 'audio/wav'];
    const file = fileInput?.files?.[0];

    if (!file || !allowedTypes.includes(file.type)) {
        setStatusMessage('Only audio files are allowed.', 'error');
		fileInput.value = '';  // Reset the file input
        return;
    }

	console.log('context', context);

    // Update the global state (context)
    const fileUrl = URL.createObjectURL(file);
    context.fileSelected = true;
    context.fileUrl = fileUrl;

}

// This function will handle the form submission and song upload.
function uploadSong(event: Event): void {
	console.log('uploadSong');
    event.preventDefault();

	console.log(getContext< Context >());

	const { fileSelected, fileUrl } = getContext< Context >();

    const titleInput = document.getElementById('song-title') as HTMLInputElement;

    if (!fileSelected || !titleInput?.value) {
        setStatusMessage('Please select a file and provide a song title.', 'error');
        return;
    }

    // Send a POST request to the REST API to create the new post
    fetch('/wp-json/songwriter-tools/v1/song', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': (window as any).wpApiSettings.nonce,
        },
        body: JSON.stringify({
            title: titleInput.value,
            meta: {
                song_file: fileUrl,
            },
            status: 'publish',
        }),
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error uploading song.');
            }
            return response.json();
        })
        .then(() => {
            setStatusMessage('Song uploaded successfully!', 'success');
        })
        .catch(error => {
            setStatusMessage(error.message, 'error');
        });
}

// Helper function to show status messages
function setStatusMessage(message: string, status: 'success' | 'error'): void {
	// TODO: Refactor this to use state
    const messageElement = document.getElementById('song-upload-message');
    if (messageElement) {
        messageElement.textContent = message;
        messageElement.style.color = status === 'error' ? 'red' : 'green';
    }
}

type Store = ServerState & typeof storeDef;

const { state } = store< Store >( storeNamespace, storeDef );
