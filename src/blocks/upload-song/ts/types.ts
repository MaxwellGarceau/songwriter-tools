/**
 * Types local to Upload Song block
 *
 * TODO: Should we combine these with the global types
 * in /types/global.d.ts?
 */

// Type for server-side state
export type ServerState = {
	state: {
		nonce: string;
	};
};

// Type for context used in the store
export type Context = {
	fileSelected: boolean;
	allowedFileTypes: string[];
	maxFileSize: number;
	postId: number;
	nonce: string;
};

// Type that combines server state and store definition
export type Store = ServerState & {
	actions: {
		handleFileSelect: ( event: Event ) => void;
		uploadSong: ( event: Event ) => void;
	};
};
