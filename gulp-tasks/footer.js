const gulp = require( 'gulp' );
const footer = require( 'gulp-footer' );
const pkg = require( '../package.json' );

module.exports = {
	commonIconsVariables() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }common/variables/_icons.pcss` )
			.pipe( footer( '}\n' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }common/variables/` ) );
	},
};
