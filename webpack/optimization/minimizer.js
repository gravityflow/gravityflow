const TerserPlugin = require( 'terser-webpack-plugin' );
const OptimizeCSSAssetsPlugin = require( 'optimize-css-assets-webpack-plugin' );

module.exports = [
	new TerserPlugin( {
		cache: true,
		parallel: true,
		sourceMap: false, // Must be set to true if using source-maps in production
		extractComments: false,
		terserOptions: {
			compress: {
				warnings: false,
				drop_console: true,
			},
			output: {
				comments: false,
			},
		},
	} ),
	new OptimizeCSSAssetsPlugin( {
		cssProcessorOptions: {
			zindex: false,
		},
	} ),
];
