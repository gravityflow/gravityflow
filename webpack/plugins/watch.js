const path = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const WatchExternalFilesPlugin = require( 'webpack-watch-files-plugin' ).default;

module.exports = {
	theme: [
		new MiniCssExtractPlugin( {
			filename: '../../../css/[name].css',
		} ),
		new WatchExternalFilesPlugin( {
			files: [
				path.resolve( `${ __dirname }/../../`, 'src/js/common/**/*.js' ),
				path.resolve( `${ __dirname }/../../`, 'src/js/utils/**/*.js' ),
			]
		} ),

	],
	admin: [
		new MiniCssExtractPlugin( {
			filename: '../../../css/[name].css',
		} ),
		new WatchExternalFilesPlugin( {
			files: [
				path.resolve( `${ __dirname }/../../`, 'src/js/common/**/*.js' ),
				path.resolve( `${ __dirname }/../../`, 'src/js/utils/**/*.js' ),
			]
		} ),
	],
};
