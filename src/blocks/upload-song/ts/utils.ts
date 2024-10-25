// Helper function to convert bytes to MB and round to 2 decimal places
export function getSizeInMb( sizeInBytes: number ): number {
	const sizeInMbFloat = sizeInBytes / ( 1024 * 1024 );
	return Math.round( sizeInMbFloat * 100 ) / 100;
}
