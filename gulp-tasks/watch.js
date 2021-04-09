const gulp = require( 'gulp' );
const pkg = require( '../package.json' );
const browserSync = require( 'browser-sync' );
const webpack = require( 'webpack' );
const webpackStream = require( 'webpack-stream' );
const merge = require( 'webpack-merge' );

const webpackAdminDevConfig = require( '../webpack/admindev' );
const webpackThemeDevConfig = require( '../webpack/themedev' );
const watchRules = require( '../webpack/rules/watch' );
const watchPlugins = require( '../webpack/plugins/watch' );

const watchConfig = {
	watch: true,
};

webpackAdminDevConfig.module.rules = watchRules;
webpackThemeDevConfig.module.rules = watchRules;
webpackAdminDevConfig.plugins = watchPlugins.admin;
webpackThemeDevConfig.plugins = watchPlugins.theme;

function maybeReloadBrowserSync() {
	const server = browserSync.get( 'Gravityflow Dev' );
	if ( server.active ) {
		server.reload();
	}
}

module.exports = {
	main() {
		// watch main plugin postcss

		gulp.watch( [
			`${ pkg.gravityflow.paths.css_src }admin/**/*.pcss`,
		], gulp.parallel( 'postcss:adminCss' ) );

		gulp.watch( [
			`${ pkg.gravityflow.paths.css_src }theme/**/*.pcss`,
		], gulp.parallel( 'postcss:themeCss' ) );
	},
	watchAdminJS() {
		gulp.src( `${ pkg.gravityflow.paths.js_src }admin/**/*.js` )
			.pipe( webpackStream( merge( webpackAdminDevConfig, watchConfig ), webpack, function( err, stats ) {
				console.log( stats.toString( { colors: true } ) );
				maybeReloadBrowserSync();
			} ) )
			.pipe( gulp.dest( pkg.gravityflow.paths.js_dist ) );
	},
	watchThemeJS() {
		gulp.src( [
			`${ pkg.gravityflow.paths.js_src }theme/**/*.js`,
		] )
			.pipe( webpackStream( merge( webpackThemeDevConfig, watchConfig ), webpack, function( err, stats ) {
				console.log( stats.toString( { colors: true } ) );
				maybeReloadBrowserSync();
			} ) )
			.pipe( gulp.dest( pkg.gravityflow.paths.js_dist ) );
	},
};
