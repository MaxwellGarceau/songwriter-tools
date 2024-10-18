// This function will handle file validation and selection.
function handleFileSelect(event: Event): void {
    const fileInput = event.target as HTMLInputElement;
    const allowedTypes = ['audio/mpeg', 'audio/wav'];
    const file = fileInput?.files?.[0];

    if (!file || !allowedTypes.includes(file.type)) {
        setStatusMessage('Only audio files are allowed.', 'error');
        return;
    }

    // Update the global state (context)
    const fileUrl = URL.createObjectURL(file);
    window.wp.interactivity.setContext('fileSelected', true);
    window.wp.interactivity.setContext('fileUrl', fileUrl);
}

// This function will handle the form submission and song upload.
function uploadSong(event: Event): void {
    event.preventDefault();

    const fileSelected = window.wp.interactivity.getContext('fileSelected');
    const fileUrl = window.wp.interactivity.getContext('fileUrl');
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
    const messageElement = document.getElementById('song-upload-message');
    if (messageElement) {
        messageElement.textContent = message;
        messageElement.style.color = status === 'error' ? 'red' : 'green';
    }
}

// Declare these methods globally for the interactivity API to access them.
(window as any).actions = {
    handleFileSelect,
    uploadSong,
};
