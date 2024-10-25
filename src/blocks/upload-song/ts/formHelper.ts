export default class FormHelper {
	private form: HTMLFormElement;

	constructor( form: HTMLFormElement ) {
		this.form = form;
	}

	// Show status messages on the form
	public setStatusMessage(
		message: string,
		status: 'success' | 'error'
	): void {
		const messageElement = this.form
			.closest( '.wp-block-songwriter-tools-upload-song' )
			?.querySelector( '#song-upload-message' ) as HTMLElement;
		if ( messageElement ) {
			messageElement.textContent = message;
			messageElement.style.color =
				status === 'error'
					? 'var(--songwriter-tools--preset--color--error)'
					: 'var(--songwriter-tools--preset--color--success)';
		}
	}

	// Clear the status message
	public clearStatusMessage(): void {
		this.setStatusMessage( '', 'success' );
	}

	// Clear form fields and reset UI elements
	public clearForm(): void {
		const titleInput = this.form.querySelector(
			'#song-title'
		) as HTMLInputElement;
		const fileInput = this.form.querySelector(
			'.song-upload-form__input-file'
		) as HTMLInputElement;
		const fileInputDisplay = this.form.querySelector(
			'.song-upload-form__file-selected'
		) as HTMLElement;

		titleInput.value = '';
		fileInput.value = '';
		fileInputDisplay.textContent = 'No file chosen';
	}
}
