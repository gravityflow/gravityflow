const gulp = require( 'gulp' );
const header = require( 'gulp-header' );
const pkg = require( '../package.json' );

module.exports = {
	commonIconsStyle() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }common/base/_icons.pcss` )
			.pipe( header( `/* stylelint-disable */
/* -----------------------------------------------------------------------------
 *
 * Admin Font Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

` ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }common/base/` ) );
	},
	commonIconsVariables() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }common/variables/_icons.pcss` )
			.pipe( header( `/* stylelint-disable */
/* -----------------------------------------------------------------------------
 *
 * Variables: Admin Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

:root {` ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }common/variables/` ) );
	},
};
