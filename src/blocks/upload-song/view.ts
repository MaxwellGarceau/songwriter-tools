import { store } from '@wordpress/interactivity';
import { uploadSong } from './ts/uploadSong';
import { handleFileSelect } from './ts/fileSelect';
import configs from './ts/configs';

// TODO: Syncronize this with render.php
const { storeNamespace } = configs;

/**
 * Define and export the store
 *
 * NOTE: Defining the store here because it seems like the
 * WP Interactivity API requires it
 *
 * TODO: Break out the second argument into a separate config
 * file once we have more actions
 */
const storeDef = store( storeNamespace, {
	actions: {
		handleFileSelect,
		uploadSong,
	},
} );

export default storeDef;
