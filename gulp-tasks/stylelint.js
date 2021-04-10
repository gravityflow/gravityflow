const gulp = require( 'gulp' );
const stylelint = require( 'gulp-stylelint' );
const pkg = require( '../package.json' );

module.exports = {
	admin() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }admin/**/*.pcss`,
		] )
			.pipe( stylelint( {
				fix: true,
				reporters: [
					{ formatter: 'string', console: true },
				],
			} ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/` ) );
	},
	theme() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }theme/**/*.pcss`,
		] )
			.pipe( stylelint( {
				fix: true,
				reporters: [
					{ formatter: 'string', console: true },
				],
			} ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/` ) );
	},
	apps() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.js_src }apps/**/*.pcss`,
		] )
			.pipe( stylelint( {
				fix: true,
				reporters: [
					{ formatter: 'string', console: true },
				],
			} ) )
			.pipe( gulp.dest( `${pkg.gravityflow.paths.js_src}apps/` ) );
	},
};
