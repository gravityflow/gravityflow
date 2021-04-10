const gulp = require( 'gulp' );
const rename = require( 'gulp-rename' );
const pkg = require( '../package.json' );

module.exports = {
	adminIconsFonts() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/admin/fonts/*`,
			] )
			.pipe( gulp.dest( pkg.gravityflow.paths.fonts ) );
	},
	adminIconsStyles() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/admin/style.css`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/base/` ) );
	},
	adminIconsVariables() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/admin/variables.scss`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/variables/` ) );
	},
	themeIconsFonts() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/theme/fonts/*`,
			] )
			.pipe( gulp.dest( pkg.gravityflow.paths.fonts ) );
	},
	themeIconsStyles() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/theme/style.css`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/base/` ) );
	},
	themeIconsVariables() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/theme/variables.scss`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/variables/` ) );
	},
};
