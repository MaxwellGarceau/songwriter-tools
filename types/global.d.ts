// Mark the file as a module to avoid being treated as a script
// Required for global augmentation
export {};

declare global {
	interface Window {
		wp: {
			apiFetch: ( options: any ) => Promise< any >;
			interactivity: {
				setContext: ( key: string, value: any ) => void;
				getContext: ( key: string ) => any;
			};
			media: {
				editor: {
					open: ( target: any, options: any ) => void;
					on: ( event: string, callback: () => void ) => void;
					state: () => any;
				};
			};
		};
	}
}
