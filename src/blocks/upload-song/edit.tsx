import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { useBlockProps, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, Notice } from '@wordpress/components';
import { MediaItem } from '@wordpress/media-utils';
import * as React from 'react';

interface SongUploadProps {
    allowedTypes: string[];
    attributes: Object;
    setAttributes: Function;
}

interface Media {
    id: number;
    url: string;
    title: {
        rendered: string;
    };
    mime: string;
    isMediaEnabled: boolean;
}

const SongUploadBlock: React.FC<SongUploadProps> = ( { attributes, setAttributes } ) => {
    const [file, setFile] = useState<Media | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [isMediaEnabled] = useState<boolean | null>(false);

    const ALLOWED_MEDIA_TYPES: string[] = ['audio/mpeg', 'audio/wav'];

    const onFileSelect = (media: MediaItem) => {
        if (!ALLOWED_MEDIA_TYPES.includes(media.mime)) {
            setError(__('Only audio files are allowed!', 'upload-block'));
            setFile(null);
            return;
        }
        setError(null);
        setFile(media);
    };

    const handleSubmit = async () => {
        setError(__('No submitting file in the editor', 'upload-block'))
    };

    const blockProps = useBlockProps([
        { className: 'song-upload-block' },
    ]);
    return (
        <div {...blockProps}>
            <h3>{__('Upload Your Song', 'upload-block')}</h3>
            {error && <Notice status="error">{error}</Notice>}
            <MediaUploadCheck>
                <MediaUpload
                    onSelect={onFileSelect}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    render={({ open }) => (
                        <Button onClick={isMediaEnabled ? open : () => setError(__('Media uploading is not enabled on the editor.', 'upload-block'))} className="button button-primary">
                            {file ? __('Change file', 'upload-block') : __('Upload file', 'upload-block')}
                        </Button>
                    )}
                />
            </MediaUploadCheck>
            {file && <p>{__('Selected file: ', 'upload-block') + file.url}</p>}
            <Button isPrimary onClick={handleSubmit}>
                {__('Submit', 'upload-block')}
            </Button>
        </div>
    );
};

export default SongUploadBlock;
