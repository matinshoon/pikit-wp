/**
 * Resize generated marketing images into WordPress.org asset sizes.
 * Requires: npm install --no-save sharp (dev only, not in plugin zip)
 */
const fs = require( 'fs' );
const path = require( 'path' );

async function main() {
	let sharp;
	try {
		sharp = require( 'sharp' );
	} catch ( e ) {
		console.error( 'Install sharp first: npm install --no-save sharp' );
		process.exit( 1 );
	}

	const root = path.join( __dirname, '..' );
	const outDir = path.join( root, 'wordpress-org', 'assets' );
	fs.mkdirSync( outDir, { recursive: true } );

	const iconSrc = process.argv[ 2 ];
	const bannerSrc = process.argv[ 3 ];

	if ( ! iconSrc || ! bannerSrc ) {
		console.error( 'Usage: node scripts/prepare-wp-assets.js <icon.png> <banner.png>' );
		process.exit( 1 );
	}

	await sharp( iconSrc ).resize( 256, 256, { fit: 'cover' } ).png().toFile( path.join( outDir, 'icon-256x256.png' ) );
	await sharp( iconSrc ).resize( 128, 128, { fit: 'cover' } ).png().toFile( path.join( outDir, 'icon-128x128.png' ) );

	await sharp( bannerSrc )
		.resize( 1544, 500, { fit: 'cover', position: 'centre' } )
		.png()
		.toFile( path.join( outDir, 'banner-1544x500.png' ) );

	await sharp( bannerSrc )
		.resize( 772, 250, { fit: 'cover', position: 'centre' } )
		.png()
		.toFile( path.join( outDir, 'banner-772x250.png' ) );

	console.log( 'Wrote WordPress.org assets to wordpress-org/assets/' );
	console.log( 'Still needed: screenshot-1.png … screenshot-4.png (from real plugin UI)' );
}

main().catch( ( err ) => {
	console.error( err );
	process.exit( 1 );
} );
