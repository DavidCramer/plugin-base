const gulp = require( 'gulp' );
const zipModule = require( 'gulp-zip' );
const zip =
	typeof zipModule === 'function'
		? zipModule
		: ( zipModule && zipModule.default ) || zipModule;

const newer = require( 'gulp-newer' );
const clean = require( 'gulp-clean' );
const path = require( 'path' );
const fs = require( 'fs' );
const pkg = ( () => {
	try {
		return require( './package.json' );
	} catch {
		return { version: 'latest' };
	}
} )();
const packageTag = 'runtime' === process.env.RELEASE ? '-runtime' : '';
const pluginName = __dirname.split('/').pop()
// Build paths configuration
const buildPaths = {
	src: [ './**/{**,.**}' ],
	base: './',
	dist: 'release/', // Destination folder
	exclude: [
		'!./**/src/**',
		'!./**/*-src/**',
		'!./**/release/**',
		'!./**/vendor/**',
		'!./**/node_modules/**',
		'!./**/tests/**',
		'!./**/tools/**',
		'!./**/.*',
		'!./**/composer.*',
		'!./**/package.*',
		'!./**/package-lock.*',
		'!./**/gulpfile.*',
		'!./**/webpack.*',
		'!./**/phpcs.*',
		'!./**/phpunit.*',
		'!./**/*.config.js',
		'!./**/tailwind/**',
		'!./**/tailwind.css',
		'!./**/tailwind/**',
		'!./**/vite.*',
		'!./**/vite-plugins/**',
		'!./**/json-src/**',
		'!./**/admin-src/**',
		'!./**/dev/**',
		'!./**/*.md',
		'!./*.json',
		'!./**/*.map',
		'!./**/*.mjs',
	],
};

// Generate paths to clean in dist based on specific wp-content folders
function getCleanPaths( paths ) {
	return paths.src.map( ( srcPath ) => {
		const baseFolder = srcPath.split( '/**/{**,.**}' )[ 0 ]; // Extract base folder from glob
		return path.join(
			paths.dist,
			pluginName,
			path.relative( '.', baseFolder )
		); // Match dist folder structure
	} );
}

// Clean only corresponding folders in dist
function cleanDist() {
	const cleanPaths = getCleanPaths( buildPaths );
	return gulp
		.src( cleanPaths, { allowEmpty: true, read: false } )
		.pipe( clean() );
}

// Copy files to dist while excluding patterns and preserving structure
function copyFiles( paths ) {
	// Combine sources and exclusions into a single array
	const sources = [ ...paths.src, ...paths.exclude ];

	return gulp
		.src( sources, { base: paths.base } ) // Use paths.base as the base directory to preserve structure
		.pipe( newer( paths.dist ) ) // Only copy newer files
		.pipe(
			gulp.dest( path.join( paths.dist, pluginName, paths.base ) )
		); // Replicate wp-content structure in dist
}

function copyBuildFiles() {
	return copyFiles( buildPaths );
}

function packageDist() {
	const zipName = `${pluginName}${packageTag}-${ pkg.version || 'latest' }.zip`;
	// zip everything under the dist folder and place the zip next to the dist folder (in release/)
	return gulp
		.src( path.join( buildPaths.dist, `/${pluginName}/**/*` ), {
			base: buildPaths.dist,
		} )
		.pipe( zip( zipName ) )
		.pipe( gulp.dest( path.join( buildPaths.dist, '.' ) ) );
}


// Define tasks
const build = gulp.series( cleanDist, copyBuildFiles );
// Create a new pipeline that runs the existing build then packaging
const distWithPackage = gulp.series( build, packageDist, cleanDist );

// Export tasks
exports.clean = cleanDist;
exports.copy = copyFiles;
exports.package = packageDist;
exports.dist = distWithPackage;
exports.default = distWithPackage;
