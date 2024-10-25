import { getSizeInMb } from './utils';

export function validateFile(
	file: File | undefined,
	allowedFileTypes: string[],
	maxFileSize: number
): { isValid: boolean; message?: string } {
	if ( ! file || ! allowedFileTypes.includes( file.type ) ) {
		return {
			isValid: false,
			message: `Allowed file types: ${ allowedFileTypes.join( '|' ) }`,
		};
	}

	if ( getSizeInMb( file.size ) > maxFileSize ) {
		return {
			isValid: false,
			message: `Allowed file size: ${ maxFileSize }MB`,
		};
	}

	return { isValid: true };
}
