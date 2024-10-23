import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, Notice, SelectControl, RangeControl, TextControl, FontSizePicker } from '@wordpress/components';
import { useState } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import * as React from 'react';

interface SongUploadProps {
	allowedTypes: string[];
	attributes: {
		headingTag: string;
		headingContent: string;
		maxFileSize: number;
		allowedMimeTypes: string[];
		songTitle: string;
		headingFontSize: number;
	};
	setAttributes: Function;
}

const SongUploadBlock: React.FC<SongUploadProps> = ({ attributes, setAttributes }) => {
	const { headingTag, headingContent, maxFileSize, allowedMimeTypes, songTitle, headingFontSize } = attributes;
	const [error, setError] = useState<string | null>(null);

	const fileTypeOptions = [
		{ label: 'MPEG', value: 'audio/mpeg' },
		{ label: 'WAV', value: 'audio/wav' },
	];

	const ALLOWED_MEDIA_TYPES = allowedMimeTypes.length ? allowedMimeTypes : ['audio/mpeg', 'audio/wav'];

	const handleError = async (e) => {
		e.preventDefault();
		setError(__('This feature is only usable on the front end of the website.', 'upload-block'));
	};

	// Access the predefined font sizes from theme.json
	const themeFontSizes = useSelect((select) => {
		return select('core/block-editor').getSettings().fontSizes || [];
	}, []);

	const blockProps = useBlockProps({
		className: 'song-upload-block',
	});

	const maxFileString = `(max ${maxFileSize}MB)`;

	const fileTypeLabel = fileTypeOptions.filter((option) => allowedMimeTypes.includes(option.value)).map((option) => option.label);
	const allowedMimeTypesLabel = fileTypeLabel.join('|');

	const maxFileSizeLabel = `${__('Allowed file types: ', 'upload-block')} ${allowedMimeTypesLabel} ${maxFileString}`;
	return (
		<section {...blockProps}>
			<div className="song-upload-max-width-container">
				<InspectorControls>
					<PanelBody title={__('Heading Settings', 'upload-block')}>
						<SelectControl
							label={__('Heading Tag', 'upload-block')}
							value={headingTag}
							options={[
								{ label: 'H1', value: 'h1' },
								{ label: 'H2', value: 'h2' },
								{ label: 'H3', value: 'h3' },
								{ label: 'H4', value: 'h4' },
								{ label: 'H5', value: 'h5' },
								{ label: 'H6', value: 'h6' },
							]}
							onChange={(value) => setAttributes({ headingTag: value })}
						/>
						<FontSizePicker
							value={headingFontSize}
							onChange={(newSize) => setAttributes({ headingFontSize: newSize })}
							fontSizes={themeFontSizes} // default is empty array
							disableCustomFontSizes={true}
						/>
						<TextControl
							label={__('Heading Content', 'upload-block')}
							value={headingContent}
							onChange={(value) => setAttributes({ headingContent: value })}
						/>
					</PanelBody>
					<PanelBody title={__('File Restrictions', 'upload-block')}>
						<SelectControl
							multiple
							label={__('Allowed File Types', 'upload-block')}
							value={allowedMimeTypes}
							options={fileTypeOptions}
							onChange={(value: string[]) => setAttributes({ allowedMimeTypes: value })}
						/>
						<RangeControl
							label={__('Max File Size (MB)', 'upload-block')}
							value={maxFileSize}
							min={1}
							max={50}
							onChange={(value) => setAttributes({ maxFileSize: value })}
						/>
					</PanelBody>
				</InspectorControls>

				{/** Heading */}
				<RichText
					tagName={headingTag}
					value={headingContent}
					style={{ fontSize: headingFontSize ? `${headingFontSize}` : undefined }}
					onChange={(value) => setAttributes({ headingContent: value })}
					placeholder={__('Enter heading...', 'upload-block')}
				/>

				{error && <Notice status="error" onRemove={() => setError(null)}>{error}</Notice>}

				<div className="song-upload-form wp-block-form">
					<div className="song-upload-form__column">
					<span className="song-upload-form__label"><span className="song-upload-form__label-content wp-block-form-input__label-content">{maxFileSizeLabel}</span></span>
						<label htmlFor="song-file" className="song-upload-form__input-file-button wp-block-button">
							<span className="button button-primary wp-block-button__link wp-element-button">Select song</span>
						</label>
						<span className="song-upload-form__file-selected">No file chosen</span>
						<input
							className="song-upload-form__input song-upload-form__input-file"
							type="file"
							id="song-file"
							accept={ALLOWED_MEDIA_TYPES.join(',')}
							onClick={handleError}
						/>
					</div>

					<div className="song-upload-form__column">
						<label htmlFor="song-title" className="song-upload-form-input__label">
							<span className="song-upload-form__label-content wp-block-form-input__label-content">{__('Song Title', 'upload-block')}</span>
						</label>
						<TextControl
							id="song-title"
							value={songTitle}
							onChange={(value) => setAttributes({ songTitle: value })}
							placeholder={__('Song Title (Input on front end only)', 'upload-block')}
							disabled={true}
							className="song-upload-form__input song-upload-form__input-text"
						/>
					</div>

					{/* Submit Button */}
					<div className="song-upload-form__submit-button-container wp-block-button">
						<button
							type="submit"
							className="wp-block-button__link"
							onClick={handleError}
						>
							{__('Upload Song', 'upload-block')}
						</button>
					</div>
				</div>
			</div>
		</section>
	);
};

export default SongUploadBlock;
