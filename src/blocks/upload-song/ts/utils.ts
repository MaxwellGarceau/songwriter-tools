// // Helper function to show status messages
// export function setStatusMessage(
// 	message: string,
// 	status: 'success' | 'error',
// 	form: HTMLFormElement
// ): void {
// 	const messageElement = form
// 		.closest( '.wp-block-songwriter-tools-upload-song' )
// 		?.querySelector( '#song-upload-message' ) as HTMLElement;
// 	if ( messageElement ) {
// 		messageElement.textContent = message;
// 		messageElement.style.color =
// 			status === 'error'
// 				? 'var(--songwriter-tools--preset--color--error)'
// 				: 'var(--songwriter-tools--preset--color--success)';
// 	}
// }

// /**
//  * Wrap setStatusMessage to avoid duplicating functionality
//  * but create different function for better api
//  */
// export function clearStatusMessage( form: HTMLFormElement ): void {
// 	setStatusMessage( '', 'success', form );
// }

// // Helper function to clear the form
// export function clearForm( form: HTMLFormElement ): void {
// 	const titleInput = form.querySelector( '#song-title' ) as HTMLInputElement;
// 	const fileInput = form.querySelector(
// 		'.song-upload-form__input-file'
// 	) as HTMLInputElement;
// 	const fileInputDisplay = form.querySelector(
// 		'.song-upload-form__file-selected'
// 	) as HTMLElement;

// 	titleInput.value = '';
// 	fileInput.value = '';
// 	fileInputDisplay.textContent = 'No file chosen';
// }

// Helper function to convert bytes to MB and round to 2 decimal places
export function getSizeInMb( sizeInBytes: number ): number {
	const sizeInMbFloat = sizeInBytes / ( 1024 * 1024 );
	return Math.round( sizeInMbFloat * 100 ) / 100;
}
