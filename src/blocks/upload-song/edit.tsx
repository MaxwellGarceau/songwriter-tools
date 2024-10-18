import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button, Notice, FC } from '@wordpress/components';
import { MediaItem } from '@wordpress/media-utils';
import * as React from 'react';

interface SongUploadProps {
    allowedTypes: string[];
}

interface Media {
    id: number;
    url: string;
    title: {
        rendered: string;
    };
    mime: string;
}

const SongUploadBlock: React.FC<SongUploadProps> = () => {
    const [file, setFile] = useState<Media | null>(null);
    const [error, setError] = useState<string | null>(null);

    const ALLOWED_MEDIA_TYPES: string[] = ['audio/mpeg', 'audio/wav']; // Accept only MP3 and WAV

    const onFileSelect = (media: MediaItem) => {
        if (!ALLOWED_MEDIA_TYPES.includes(media.mime)) {
            setError(__('Only audio files are allowed!', 'songwriter-tools'));
            setFile(null);
            return;
        }
        setError(null);
        setFile(media);
    };

    const handleSubmit = async () => {
        if (!file) {
            setError(__('Please select a file before submitting.', 'songwriter-tools'));
            return;
        }

        try {
            const response = await fetch('/wp-json/wp/v2/song', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': (window as any).wpApiSettings.nonce, // TypeScript workaround for wpApiSettings
                },
                body: JSON.stringify({
                    title: file.title.rendered,
                    status: 'publish',
                    meta: {
                        song_file: file.url,
                    },
                }),
            });

            if (!response.ok) {
                throw new Error(__('Error uploading file.', 'songwriter-tools'));
            }
            alert(__('File uploaded successfully!', 'songwriter-tools'));
        } catch (err) {
            setError((err as Error).message);
        }
    };

    return (
        <div className="song-upload-block">
            <h3>{__('Upload Your Song', 'songwriter-tools')}</h3>
            {error && <Notice status="error">{error}</Notice>}
            <MediaUploadCheck>
                <MediaUpload
                    onSelect={onFileSelect}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    render={({ open }) => (
                        <Button onClick={open} className="button button-primary">
                            {file ? __('Change file', 'songwriter-tools') : __('Upload file', 'songwriter-tools')}
                        </Button>
                    )}
                />
            </MediaUploadCheck>
            {file && <p>{__('Selected file: ', 'songwriter-tools') + file.url}</p>}
            <Button isPrimary onClick={handleSubmit}>
                {__('Submit', 'songwriter-tools')}
            </Button>
        </div>
    );
};

export default SongUploadBlock;
