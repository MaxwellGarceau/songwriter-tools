import { __ } from '@wordpress/i18n';
import { useBlockProps, MediaUpload, MediaUploadCheck, RichText, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, Notice, SelectControl, RangeControl, TextControl } from '@wordpress/components';
import { useState } from '@wordpress/element';
import * as React from 'react';

interface SongUploadProps {
    allowedTypes: string[];
    attributes: {
        headingTag: string;
        headingContent: string;
        maxFileSize: number;
        allowedMimeTypes: string[];
    };
    setAttributes: Function;
}

interface Media {
    id: number;
    url: string;
    title: {
        rendered: string;
    };
    mime: string;
    size: number;
}

const SongUploadBlock: React.FC<SongUploadProps> = ({ attributes, setAttributes }) => {
    const { headingTag, headingContent, maxFileSize, allowedMimeTypes } = attributes;
    const [file, setFile] = useState<Media | null>(null);
    const [error, setError] = useState<string | null>(null);

    const ALLOWED_MEDIA_TYPES = allowedMimeTypes.length ? allowedMimeTypes : ['audio/mpeg', 'audio/wav'];

    const onFileSelect = (media: Media) => {
        if (!ALLOWED_MEDIA_TYPES.includes(media.mime)) {
            setError(__('Only selected audio file types are allowed!', 'upload-block'));
            setFile(null);
            return;
        }
        if (media.size > maxFileSize * 1024 * 1024) {
            setError(__('File size exceeds the allowed limit', 'upload-block'));
            setFile(null);
            return;
        }
        setError(null);
        setFile(media);
    };

    const handleSubmit = async () => {
        setError(__('File uploads are not possible within the editor.', 'upload-block'));
    };

    const blockProps = useBlockProps({
        className: 'song-upload-block',
    });

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
                        options={[
                            { label: 'MP3', value: 'audio/mpeg' },
                            { label: 'WAV', value: 'audio/wav' },
                        ]}
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

            <MediaUploadCheck>
                <MediaUpload
                    onSelect={onFileSelect}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    render={({ open }) => (
                        <Button
                            onClick={open}
                            className="button button-primary"
                        >
                            {file ? __('Change file', 'upload-block') : __('Select file', 'upload-block')}
                        </Button>
                    )}
                />
            </MediaUploadCheck>

            {file && <p>{__('Selected file: ', 'upload-block') + file.url}</p>}

            <div className="wp-block-button">
                <button
                    type="submit"
                    className="wp-block-button__link"
                    onClick={handleSubmit}
                >
                    {__('Upload Song', 'upload-block')}
                </button>
            </div>
        </div>
    );
};

export default SongUploadBlock;
