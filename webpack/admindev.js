/**
 * External Dependencies
 */
const { resolve } = require( 'path' );
const merge = require( 'webpack-merge' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const BundleAnalyzerPlugin = require( 'webpack-bundle-analyzer' ).BundleAnalyzerPlugin;

/**
 * Internal Dependencies
 */
const devBase = require( './configs/dev-base.js' );
const pkg = require( '../package.json' );
const entry = require( './entry/admin' );
const externals = require( './externals/admin' );
const sc = require( './optimization/split-chunks' );
const config = require( './config' );

module.exports = merge.strategy( {
	optimization: 'append',
	plugins: 'append',
} )( devBase, {
	entry,
	externals,
	optimization: {
		splitChunks: sc.scAdmin,
	},
	output: {
		path: resolve( `${ __dirname }/../`, pkg.gravityflow.paths.js_dist ),
		publicPath: `${config.pluginPath}${ pkg.gravityflow.paths.js_dist }`,
	},
	plugins: [
		new MiniCssExtractPlugin( {
			filename: '../../../css/[name].css',
		} ),
		new BundleAnalyzerPlugin( {
			analyzerMode: 'static',
			reportFilename: resolve( `${ __dirname }/../`, 'reports/webpack-admin-bundle.html' ),
			openAnalyzer: false,
		} ),
	],
} );
