/**
 * Copy block.json to plugin build output after wp-scripts build.
 */
const fs = require( 'fs' );
const path = require( 'path' );

const src = path.join( __dirname, '..', 'src', 'book-button', 'block.json' );
const dest = path.join(
	__dirname,
	'..',
	'pikit-booking-widget',
	'build',
	'book-button',
	'block.json'
);

fs.copyFileSync( src, dest );

const nestedDir = path.join(
	path.dirname( dest ),
	'book-button'
);
if ( fs.existsSync( nestedDir ) ) {
	fs.rmSync( nestedDir, { recursive: true, force: true } );
}
