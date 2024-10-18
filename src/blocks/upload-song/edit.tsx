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
    isMediaEnabled: boolean;
}

const SongUploadBlock: React.FC<SongUploadProps> = () => {
    const [file, setFile] = useState<Media | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [isMediaEnabled] = useState<boolean | null>(false);

    const ALLOWED_MEDIA_TYPES: string[] = ['audio/mpeg', 'audio/wav']; // Accept only MP3 and WAV

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
        if (!file) {
            setError(__('Please select a file before submitting.', 'upload-block'));
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
                throw new Error(__('Error uploading file.', 'upload-block'));
            }
            alert(__('File uploaded successfully!', 'upload-block'));
        } catch (err) {
            setError((err as Error).message);
        }
    };

    // TODO: I don't like this solution, but here for now to prevent users from uploading media in editor
    const stopMediaUpload = (): void => {
        setError(__('Media uploading is not enabled on the editor.', 'upload-block'));
        return;
    }

    return (
        <div className="song-upload-block">
            <h3>{__('Upload Your Song', 'upload-block')}</h3>
            {error && <Notice status="error">{error}</Notice>}
            <MediaUploadCheck>
                <MediaUpload
                    onSelect={onFileSelect}
                    allowedTypes={ALLOWED_MEDIA_TYPES}
                    render={({ open }) => (
                        <Button onClick={isMediaEnabled ? open : stopMediaUpload} className="button button-primary">
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
