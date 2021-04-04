/**
 * External Dependencies
 */
const webpack = require( 'webpack' );
const merge = require( 'webpack-merge' );

/**
 * Internal Dependencies
 */
const base = require( './base.js' );
const minimizer = require( '../optimization/minimizer' );

module.exports = merge.strategy( {
	plugins: 'append',
} )( base, {
	cache: false,
	mode: 'production',
	devtool: false,
	output: {
		filename: '[name].min.js',
		chunkFilename: '[name].[chunkhash].min.js',
	},
	plugins: [
		new webpack.DefinePlugin( {
			'process.env': { NODE_ENV: JSON.stringify( 'production' ) },
		} ),
		new webpack.LoaderOptionsPlugin( {
			debug: false,
		} ),
	],
	optimization: {
		noEmitOnErrors: true, // NoEmitOnErrorsPlugin
		concatenateModules: true, //ModuleConcatenationPlugin
		minimizer,
	},
} );
