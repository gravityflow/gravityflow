/**
 * External Dependencies
 */
const { resolve } = require( 'path' );

/**
 * Internal Dependencies
 */
const appBase = require( './configs/app-base.js' );
const pkg = require( '../package.json' );

module.exports = {
	mode: 'development',
	entry: [ `./${ pkg.gravityflow.paths.js_src }apps/Example/index.js` ],
	output: {
		filename: 'app.js',
		path: resolve( `${ __dirname }/../`, 'public/js/' ),
		publicPath: 'https://localhost:3000/',
	},
	...appBase,
};
