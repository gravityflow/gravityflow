const gulp = require( 'gulp' );
const footer = require( 'gulp-footer' );
const pkg = require( '../package.json' );

module.exports = {
	adminIconsVariables() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }admin/variables/_icons.pcss` )
			.pipe( footer( '}\n' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/variables/` ) );
	},
	themeIconsVariables() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }theme/variables/_icons.pcss` )
			.pipe( footer( '}\n' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/variables/` ) );
	},
};
