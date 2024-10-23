import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, Notice, SelectControl, RangeControl, TextControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import * as React from 'react';

interface SongUploadProps {
    allowedTypes: string[];
    attributes: {
        headingTag: string;
        headingContent: string;
        maxFileSize: number;
        allowedMimeTypes: string[];
        songTitle: string;
    };
    setAttributes: Function;
}

const SongUploadBlock: React.FC<SongUploadProps> = ({ attributes, setAttributes }) => {
    const { headingTag, headingContent, maxFileSize, allowedMimeTypes, songTitle } = attributes;
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

    const blockProps = useBlockProps({
        className: 'song-upload-block',
    });

    const maxFileString = `(max ${maxFileSize}MB)`;

    const fileTypeLabel = fileTypeOptions.filter((option) => allowedMimeTypes.includes(option.value)).map((option) => option.label);
    const allowedMimeTypesLabel = fileTypeLabel.join('|');

    const maxFileSizeLabel = `${__('Allowed file types: ', 'upload-block')} ${allowedMimeTypesLabel} ${maxFileString}`;
    return (
        <div {...blockProps}>
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

            <RichText
                tagName={headingTag}
                value={headingContent}
                onChange={(value) => setAttributes({ headingContent: value })}
                placeholder={__('Enter heading...', 'upload-block')}
            />

            {error && <Notice status="error" onRemove={() => setError(null)}>{error}</Notice>}

            <div className="wp-block-form">
                <label htmlFor="song-title" className="wp-block-form-input__label">
                    {__('Song Title', 'upload-block')}
                </label>
                <TextControl
                    id="song-title"
                    value={songTitle}
                    onChange={(value) => setAttributes({ songTitle: value })}
                    placeholder={__('Enter song title (this is only editable on the front of the website)', 'upload-block')}
                    disabled={true}
                />

                <label htmlFor="song-file" className="wp-block-form-input__label">
                    {maxFileSizeLabel}
                </label>
                <input
                    className="wp-block-form-input"
                    type="file"
                    id="song-file"
                    accept={ALLOWED_MEDIA_TYPES.join(',')}
                    onClick={handleError}
                />

                <div className="wp-block-button">
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
    );
};

export default SongUploadBlock;
