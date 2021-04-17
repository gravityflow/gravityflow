/**
 * External Dependencies
 */
const webpack = require( 'webpack' );
const merge = require( 'webpack-merge' );

/**
 * Internal Dependencies
 */
const base = require( './base.js' );

module.exports = merge.strategy( {
	plugins: 'append',
} )( base, {
	cache: true,
	mode: 'development',
	output: {
		filename: '[name].js',
		chunkFilename: '[name].[chunkhash].js',
	},
	devtool: 'eval-source-map',
	plugins: [
		new webpack.LoaderOptionsPlugin( {
			debug: true,
		} ),
	],
	optimization: {
		noEmitOnErrors: true, // NoEmitOnErrorsPlugin
		concatenateModules: true, //ModuleConcatenationPlugin
	},
} );
