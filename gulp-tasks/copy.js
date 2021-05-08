const gulp = require( 'gulp' );
const rename = require( 'gulp-rename' );
const pkg = require( '../package.json' );

module.exports = {
	commonIconsFonts() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/common/fonts/*`,
			] )
			.pipe( gulp.dest( pkg.gravityflow.paths.fonts ) );
	},
	commonIconsStyles() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/common/style.css`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }common/base/` ) );
	},
	commonIconsVariables() {
		return gulp
			.src( [
				`${ pkg.gravityflow.paths.dev }icons/common/variables.scss`,
			] )
			.pipe( rename( '_icons.pcss' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }common/variables/` ) );
	},
};
