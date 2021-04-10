const gulp = require( 'gulp' );
const eslint = require( 'gulp-eslint' );
const gulpIf = require( 'gulp-if' );
const pkg = require( '../package.json' );

function isFixed( file ) {
	return file.eslint != null && file.eslint.fixed;
}

function lint( src = [], dest = pkg.gravityflow.paths.js_src ) {
	return gulp.src( src )
		.pipe( eslint( { fix: true } ) )
		.pipe( eslint.format() )
		.pipe( gulpIf( isFixed, gulp.dest( dest ) ) )
		.pipe( eslint.format() )
		.pipe( eslint.failAfterError() );
}

module.exports = {
	theme() {
		return lint( [
			`${ pkg.gravityflow.paths.js_src }theme/**/*`,
		], `${pkg.gravityflow.paths.js_src}theme/` );
	},
	apps() {
		return lint( [
			`${ pkg.gravityflow.paths.js_src }apps/**/*.js`,
		], `${pkg.gravityflow.paths.js_src}apps/` );
	},
	utils() {
		return lint( [
			`${ pkg.gravityflow.paths.js_src }utils/**/*`,
		], `${pkg.gravityflow.paths.js_src}utils/` );
	},
	admin() {
		return lint( [
			`${ pkg.gravityflow.paths.js_src }admin/**/*`,
		], `${pkg.gravityflow.paths.js_src}admin/` );
	},
};
