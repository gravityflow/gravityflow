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
const prodBase = require( './configs/prod-base.js' );
const entry = require( './entry/theme' );
const pkg = require( '../package.json' );
const externals = require( './externals/theme' );
const sc = require( './optimization/split-chunks' );
const config = require( './config' );

module.exports = merge.strategy( {
	optimization: 'append',
	plugins: 'append',
} )(
	prodBase,
	{
		entry,
		externals,
		optimization: {
			splitChunks: sc.scTheme,
		},
		output: {
			path: resolve( `${ __dirname }/../`, pkg.gravityflow.paths.js_dist ),
			publicPath: `${config.pluginPath}${ pkg.gravityflow.paths.js_dist }`,
		},
		plugins: [
			new MiniCssExtractPlugin( {
				filename: '../../../css/[name].min.css',
			} ),
			new BundleAnalyzerPlugin( {
				analyzerMode: 'static',
				reportFilename: resolve( `${ __dirname }/../`, 'reports/webpack-theme-bundle-prod.html' ),
				openAnalyzer: false,
			} ),
		],
	}
);
