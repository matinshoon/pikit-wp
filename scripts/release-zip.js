/**
 * Create a WordPress.org–ready plugin zip (plugin folder only, no dev tooling).
 */
const fs = require( 'fs' );
const path = require( 'path' );
const { execSync } = require( 'child_process' );

const root = path.join( __dirname, '..' );
const pluginDir = path.join( root, 'pikit-booking-widget' );
const outDir = path.join( root, 'dist' );
const zipName = 'pikit-booking-widget.zip';
const zipPath = path.join( outDir, zipName );

const nestedBlockDir = path.join( pluginDir, 'build', 'book-button', 'book-button' );
if ( fs.existsSync( nestedBlockDir ) ) {
	fs.rmSync( nestedBlockDir, { recursive: true, force: true } );
}

const mainFile = path.join( pluginDir, 'pikit-booking-widget.php' );
const readmeFile = path.join( pluginDir, 'readme.txt' );

if ( ! fs.existsSync( pluginDir ) ) {
	console.error( 'Missing pikit-booking-widget/ directory.' );
	process.exit( 1 );
}

const versionMatch = fs
	.readFileSync( mainFile, 'utf8' )
	.match( /\* Version:\s+([0-9.]+)/ );
const stableMatch = fs
	.readFileSync( readmeFile, 'utf8' )
	.match( /^Stable tag:\s+([0-9.]+)/m );

if ( ! versionMatch || ! stableMatch ) {
	console.error( 'Could not read version from plugin header or readme.txt Stable tag.' );
	process.exit( 1 );
}

if ( versionMatch[ 1 ] !== stableMatch[ 1 ] ) {
	console.error(
		`Version mismatch: plugin header ${ versionMatch[ 1 ] } vs readme Stable tag ${ stableMatch[ 1 ] }`
	);
	process.exit( 1 );
}

if ( fs.existsSync( path.join( root, 'node_modules' ) ) ) {
	const nestedInPlugin = path.join( pluginDir, 'node_modules' );
	if ( fs.existsSync( nestedInPlugin ) ) {
		console.error( 'Remove node_modules from pikit-booking-widget/ before zipping.' );
		process.exit( 1 );
	}
}

/** WordPress.org rejects hidden files (names starting with a dot). */
function findHiddenFiles( dir, hidden = [] ) {
	for ( const entry of fs.readdirSync( dir, { withFileTypes: true } ) ) {
		const fullPath = path.join( dir, entry.name );
		if ( entry.name.startsWith( '.' ) ) {
			hidden.push( fullPath );
			continue;
		}
		if ( entry.isDirectory() ) {
			findHiddenFiles( fullPath, hidden );
		}
	}
	return hidden;
}

const hiddenFiles = findHiddenFiles( pluginDir );
if ( hiddenFiles.length ) {
	console.error( 'Hidden files are not allowed in the plugin zip:' );
	hiddenFiles.forEach( ( file ) => console.error( `  ${ path.relative( root, file ) }` ) );
	process.exit( 1 );
}

fs.mkdirSync( outDir, { recursive: true } );

if ( fs.existsSync( zipPath ) ) {
	fs.unlinkSync( zipPath );
}

const isWindows = process.platform === 'win32';

if ( isWindows ) {
	execSync(
		`powershell -NoProfile -Command "Compress-Archive -Path '${pluginDir}' -DestinationPath '${zipPath}' -Force"`,
		{ stdio: 'inherit' }
	);
} else {
	execSync(
		`cd "${ root }" && zip -r "${ zipPath }" pikit-booking-widget -x "*.DS_Store" "*/node_modules/*" "*/.*" "*/.*/**"`,
		{ stdio: 'inherit' }
	);
}

const sizeKb = Math.round( fs.statSync( zipPath ).size / 1024 );
console.log( `Created ${ zipPath } (${ sizeKb } KB, version ${ versionMatch[ 1 ] })` );

if ( sizeKb > 512 ) {
	console.warn(
		'Warning: zip is larger than 512 KB. Remove unused assets before WordPress.org submission.'
	);
}
